<?php
/**
 * openTrans Dispatchnotification Document Reader standard 2.1
 *
 * Extense the standard openTrans Document Reader for standard 2.1 document
 *
 * @copyright Testsieger Portal AG
 * @license GPL 3:
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Testsieger.de OpenTrans Connector
 */

namespace TestSieger\Check24Connector\Application\Model;

class OpentransDocumentReaderDispatchNotification extends OpentransDocumentReader
{

    /**
     * Convert an XML in openTrans 2.1 Standard to openTrans-Document Object
     *
     * @param string $src
     * @return object
     */
    public function get_document_data_dispatchnotification($src_file)
    {

        if (!is_string($src_file)) {
            throw new OpentransException('Sourcefile must be a string.');
        } elseif (!file_exists($src_file)) {
            throw new OpentransException('Sourcefile (' . $src_file . ') not found.');
        }

        $xml_dispatchnotification = @simplexml_load_file($src_file, OpentransSimplexml::class, LIBXML_ERR_NONE);

        if (!$xml_dispatchnotification) {
            throw new OpentransException('Sourcefile could not be loaded as XML-object.');
        }

        $xml_dispatchnotification_header = $xml_dispatchnotification->get_field('DISPATCHNOTIFICATION_HEADER');

        $xml_dispatchnotification_item_list = $xml_dispatchnotification->get_field('DISPATCHNOTIFICATION_ITEM_LIST');

        $xml_dispatchnotification_summary = $xml_dispatchnotification->get_field('DISPATCHNOTIFICATION_SUMMARY');

        $xml_dispatchnotification_attributes = (array)$xml_dispatchnotification->attributes();

        if (!isset($xml_dispatchnotification_attributes['@attributes']['version'])) {
            throw new OpentransException('DISPATCHNOTIFICATION version not set in "' . $src_file . '"');
        } else {
            $version = $xml_dispatchnotification_attributes['@attributes']['version'];
            unset($xml_dispatchnotification['@attributes']);
        }

        if (count($xml_dispatchnotification) != 3) {
            throw new OpentransException('"' . $src_file . '" is not a valid openTrans standard "' . $version . '" dispatchnotification');
        }

        if (count($xml_dispatchnotification_item_list->get_field('DISPATCHNOTIFICATION_ITEM')) != $xml_dispatchnotification_summary->as_int('TOTAL_ITEM_NUM')) {
            throw new OpentransException('DISPATCHNOTIFICATION_SUMMARY TOTAL_ITEM_NUM (' . $xml_dispatchnotification_summary->as_int('TOTAL_ITEM_NUM') . ') and count of DISPATCHNOTIFICATION_ITEM_LIST ITEMS (' . count($xml_dispatchnotification_item_list->get_field('DISPATCHNOTIFICATION_ITEM')) . ') differs');
        }

        // Starting with header

        $opentrans_dispatchnotification = new OpentransDocumentDispatchNotification();

        $opentrans_dispatchnotification_header = $opentrans_dispatchnotification->create_header();

        $xml_dispatchnotification_header_controlinfo = $xml_dispatchnotification_header->get_field('CONTROL_INFO');

        $opentrans_dispatchnotification_header->create_controlinfo($xml_dispatchnotification_header_controlinfo->as_string('STOP_AUTOMATIC_PROCESSING', false),
            $xml_dispatchnotification_header_controlinfo->as_string('GENERATOR_INFO'),
            $xml_dispatchnotification_header_controlinfo->as_int('GENERATION_DATE'));

        $xml_dispatchnotification_header_dispatchnotificationinfo = $xml_dispatchnotification_header->get_field('DISPATCHNOTIFICATION_INFO');

        $opentrans_dispatchnotification_info = $opentrans_dispatchnotification_header->create_dispatchnotificationinfo($xml_dispatchnotification_header_dispatchnotificationinfo->as_string('DISPATCHNOTIFICATION_ID'),
            $xml_dispatchnotification_header_dispatchnotificationinfo->as_string('DISPATCHNOTIFICATION_DATE'));

        // Adding Remarks

        if ($xml_dispatchnotification_header_dispatchnotificationinfo->get_field('REMARKS', false) != NULL) {

            $xml_remarks = $xml_dispatchnotification_header_dispatchnotificationinfo->get_field('REMARKS');

            if (is_array($xml_remarks) || is_object($xml_remarks)) {

                for ($i = 0; is_object($xml_remarks->get_field($i, false)); $i++) {

                    // Validation version orderapi: throw exception if difference in major.

                    if ('version_orderapi' == $xml_remarks->get_field_attribute($i, 'type') && !$this->is_version_valid($xml_remarks->as_string($i))) {
                        throw new OpentransException('Version orderapi "' . $xml_remarks->as_string($i) . '" is uncompatible to actual version orderapi ' . OpentransDocument::VERSION_ORDERAPI);
                    }

                    $opentrans_dispatchnotification_info->add_remark($xml_remarks->get_field_attribute($i, 'type'), $xml_remarks->as_string($i));
                }

            }

        }

        // Adding Partys

        if ($xml_dispatchnotification_header_dispatchnotificationinfo->get_field('PARTIES') != NULL && $xml_dispatchnotification_header_dispatchnotificationinfo->get_field('PARTIES')->get_field('PARTY') != NULL) {

            $xml_parties = $xml_dispatchnotification_header_dispatchnotificationinfo->get_field('PARTIES')->get_field('PARTY');

            foreach ($xml_parties as $num => $xml_party) {

                switch ($xml_party->as_string('PARTY_ROLE')) {

                    case 'deliverer':

                        $opentrans_party = new OpentransDocumentParty($xml_party->as_string('PARTY_ID'), $xml_party->get_field_attribute('PARTY_ID', 'type'), $xml_party->as_string('PARTY_ROLE'));

                        $opentrans_party->add_remark($xml_party->get_field_attribute('REMARKS', 'type'), $xml_party->as_string('REMARKS'));

                        $opentrans_dispatchnotification_info->add_party($opentrans_party);

                        break;

                    default:

                        if (!$this->is_testsieger_party_id($xml_party->as_string('PARTY_ID'))) {
                            throw new OpentransException('PARTY_ID "' . $xml_party->as_string('PARTY_ID') . '" is not Testsieger-formated');
                        }

                        if ($xml_party->as_string('PARTY_ROLE') !== 'delivery' && $xml_party->as_string('PARTY_ROLE') !== 'invoice_issuer') {
                            continue;
                        }

                        $xml_address = $xml_party->get_field('ADDRESS');

                        $opentrans_party = new OpentransDocumentParty($xml_party->as_string('PARTY_ID'), $xml_party->get_field_attribute('PARTY_ID', 'type'), $xml_party->as_string('PARTY_ROLE'));
                        $opentrans_address = new OpentransDocumentAddress();

                        $opentrans_address->set_name($xml_address->as_string('NAME', false));
                        $opentrans_address->set_name2($xml_address->as_string('NAME2', false));
                        $opentrans_address->set_name3($xml_address->as_string('NAME3', false));
                        $opentrans_address->set_street($xml_address->as_string('STREET', false));
                        $opentrans_address->set_zip($xml_address->as_string('ZIP', false));
                        $opentrans_address->set_city($xml_address->as_string('CITY', false));

                        $opentrans_party->set_address($opentrans_address);

                        $opentrans_dispatchnotification_info->add_party($opentrans_party);

                        break;

                }

            }

        }

        // Adding cart items

        $xml_items = $xml_dispatchnotification_item_list->get_field('DISPATCHNOTIFICATION_ITEM');

        for ($i = 0; is_object($xml_items->get_field($i, false)); $i++) {

            // Missing validation by:
            // - LINE_ITEM_ID = $i
            // - ORDER_ID = this-order_id
            // - Delivery_idref is in Parties

            $xml_item = $xml_items->get_field($i);

            $opentrans_item = new OpentransDocumentItem();

            $opentrans_item->set_product_id(new OpentransDocumentItemProductid(array($xml_item->get_field('PRODUCT_ID')->as_string('SUPPLIER_PID'))));
            $opentrans_item->set_quantity($xml_item->as_string('QUANTITY'));
            $opentrans_item->set_order_unit($xml_item->as_string('ORDER_UNIT'));
            $opentrans_item->set_price_line_amount($xml_item->as_float('PRICE_LINE_AMOUNT', false));

            $opentrans_dispatchnotification->add_item($opentrans_item);

        }

        return $opentrans_dispatchnotification;
    }
}