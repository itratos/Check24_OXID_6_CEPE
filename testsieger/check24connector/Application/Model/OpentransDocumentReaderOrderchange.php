<?php
/**
 * openTrans Orderchange Document Reader standard 2.1
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

class OpentransDocumentReaderOrderchange extends OpentransDocumentReader
{

    /**
     * Convert an XML in openTrans 2.1 Standard to openTrans-Document Object
     *
     * @param string $src
     * @return object
     */
    public function get_document_data_orderchange($src_file)
    {

        if (!is_string($src_file)) {
            throw new OpentransException('Sourcefile must be a string.');
        } elseif (!file_exists($src_file)) {
            throw new OpentransException('Sourcefile (' . $src_file . ') not found.');
        }

        $xml_orderchange = @simplexml_load_file($src_file, OpentransSimplexml::class, LIBXML_ERR_NONE);

        if (!$xml_orderchange) {
            throw new OpentransException('Sourcefile could not be loaded as XML-object.');
        }

        $xml_orderchange_header = $xml_orderchange->get_field('ORDERCHANGE_HEADER');

        $xml_orderchange_item_list = $xml_orderchange->get_field('ORDERCHANGE_ITEM_LIST');

        $xml_orderchange_summary = $xml_orderchange->get_field('ORDERCHANGE_SUMMARY');

        $xml_orderchange_attributes = (array)$xml_orderchange->attributes();

        if (!isset($xml_orderchange_attributes['@attributes']['version'])) {
            throw new OpentransException('ORDERCHANGE version not set in "' . $src_file . '"');
        } else {
            $version = $xml_orderchange_attributes['@attributes']['version'];
            unset($xml_orderchange['@attributes']);
        }

        if (count($xml_orderchange) != 3) {
            throw new OpentransException('"' . $src_file . '" is not a valid openTrans standard "' . $version . '" orderchange');
        }

        if (count($xml_orderchange_item_list->get_field('ORDER_ITEM')) != abs($xml_orderchange_summary->as_int('TOTAL_ITEM_NUM'))) {
            throw new OpentransException('ORDERCHANGE_SUMMARY TOTAL_ITEM_NUM (' . abs($xml_orderchange_summary->as_int('TOTAL_ITEM_NUM')) . ') and count of ORDERCHANGE_ITEM_LIST ITEMS (' . count($xml_orderchange_item_list->get_field('ORDER_ITEM')) . ') differs');
        }

        // Starting with header

        $opentrans_orderchange = new OpentransDocumentOrderchange();

        $opentrans_orderchange_header = $opentrans_orderchange->create_header();

        $xml_orderchange_header_controlinfo = $xml_orderchange_header->get_field('CONTROL_INFO');

        $opentrans_orderchange_header->create_controlinfo($xml_orderchange_header_controlinfo->as_string('STOP_AUTOMATIC_PROCESSING', false), $xml_orderchange_header_controlinfo->as_string('GENERATOR_INFO'), $xml_orderchange_header_controlinfo->as_int('GENERATION_DATE'));

        $xml_orderchange_header_orderchangeinfo = $xml_orderchange_header->get_field('ORDERCHANGE_INFO');

        $opentrans_orderchange_info = $opentrans_orderchange_header->create_orderchangeinfo($xml_orderchange_header_orderchangeinfo->as_string('ORDER_ID'), $xml_orderchange_header_orderchangeinfo->as_string('ORDER_DATE'));

        $opentrans_orderchange_info->set_orderchange_date($xml_orderchange_header_orderchangeinfo->as_string('ORDERCHANGE_DATE'));

        $opentrans_orderchange_info->set_orderchange_sequence_id($xml_orderchange_header_orderchangeinfo->as_string('ORDERCHANGE_SEQUENCE_ID'));

        // Adding Remarks

        if ($xml_orderchange_header_orderchangeinfo->get_field('REMARKS', false) != NULL) {

            $xml_remarks = $xml_orderchange_header_orderchangeinfo->get_field('REMARKS');

            if (is_array($xml_remarks) || is_object($xml_remarks)) {

                for ($i = 0; is_object($xml_remarks->get_field($i, false)); $i++) {

                    // Validation version orderapi: throw exception if difference in major.

                    if ('version_orderapi' == $xml_remarks->get_field_attribute($i, 'type') && !$this->is_version_valid($xml_remarks->as_string($i))) {
                        throw new OpentransException('Version orderapi "' . $xml_remarks->as_string($i) . '" is incompatible to actual version orderapi ' . OpentransDocument::VERSION_ORDERAPI);
                    }

                    $opentrans_orderchange_info->add_remark($xml_remarks->get_field_attribute($i, 'type'), $xml_remarks->as_string($i));
                }

            }

        }

        // Adding Partys
        if ($xml_orderchange_header_orderchangeinfo->get_field('PARTIES') != NULL && $xml_orderchange_header_orderchangeinfo->get_field('PARTIES')->get_field('PARTY') != NULL) {

            $xml_parties = $xml_orderchange_header_orderchangeinfo->get_field('PARTIES')->get_field('PARTY');


            for ($i = 0; is_object($xml_parties->get_field($i, false)); $i++) {

                $xml_party = $xml_parties->get_field($i);

                $xml_party_bmecat = $xml_party->children('bmecat', true);
                $sPartyId = $xml_party_bmecat->as_string('PARTY_ID');

                if (!$this->is_testsieger_party_id($sPartyId)) {
                    throw new OpentransException('PARTY_ID "' . $sPartyId . '" is not Testsieger-formated');
                }

                $xml_address = $xml_party->get_field('ADDRESS');
                $xml_address = $xml_address->children('bmecat', true);

                $opentrans_party = new OpentransDocumentParty($sPartyId, $xml_party_bmecat->get_field_attribute('PARTY_ID', 'type'), $xml_party->as_string('PARTY_ROLE'));
                $opentrans_address = new OpentransDocumentAddress();

                $opentrans_address->set_name($xml_address->as_string('NAME', false));
                $opentrans_address->set_name2($xml_address->as_string('NAME2', false));
                $opentrans_address->set_name3($xml_address->as_string('NAME3', false));
                $opentrans_address->set_street($xml_address->as_string('STREET', false));
                $opentrans_address->set_zip($xml_address->as_string('ZIP', false));
                $opentrans_address->set_city($xml_address->as_string('CITY', false));

                $opentrans_party->set_address($opentrans_address);

                $opentrans_orderchange_info->add_party($opentrans_party);

            }

        }

        // Adding cart items

        $xml_items = $xml_orderchange_item_list->get_field('ORDER_ITEM');

        for ($i = 0; is_object($xml_items->get_field($i, false)); $i++) {

            $xml_item = $xml_items->get_field($i);

            $opentrans_item = new OpentransDocumentItem();

            $oProductId = $xml_item->get_field('PRODUCT_ID');
            $oProductId = $oProductId->children('bmecat', true);

            $opentrans_item->set_product_id(new OpentransDocumentItemProductid(array($oProductId->as_string('SUPPLIER_PID'))));
            $opentrans_item->set_quantity($xml_item->as_string('QUANTITY'));

            $xml_item_bmecat = $xml_item->children('bmecat', true);
            $opentrans_item->set_order_unit($xml_item_bmecat->as_string('ORDER_UNIT'));

            $opentrans_item->set_price_line_amount($xml_item->as_float('PRICE_LINE_AMOUNT'));

            $opentrans_orderchange->add_item($opentrans_item);

        }

        return $opentrans_orderchange;
    }

}