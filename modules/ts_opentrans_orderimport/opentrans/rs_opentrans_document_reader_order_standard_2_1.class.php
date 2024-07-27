<?php
    /**
     * openTrans Order Document Reader standard 2.1
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
    class rs_opentrans_document_reader_order_standard_2_1 extends rs_opentrans_document_reader {

        /**
         * Convert an XML in openTrans 2.1 Standard to openTrans-Document Object
         *
         * @param string $src
         * @return rs_opentrans_document_order
         */
        public function get_document_data_order($src_file) {

            if (!is_string($src_file)) {
                throw new rs_opentrans_exception('Sourcefile must be a string.');
            } elseif (!file_exists($src_file)) {
                throw new rs_opentrans_exception('Sourcefile (' . $src_file . ') not found.');
            }

            $xml_order = @simplexml_load_file($src_file, 'rs_opentrans_simplexml', LIBXML_ERR_NONE);

            if (!$xml_order) {
                throw new rs_opentrans_exception('Sourcefile could not be loaded as XML-object.');
            }

            $xml_order_header = $xml_order->get_field('ORDER_HEADER');

            $xml_order_item_list = $xml_order->get_field('ORDER_ITEM_LIST');

            $xml_order_summary = $xml_order->get_field('ORDER_SUMMARY');

            $xml_order_attributes = (array)$xml_order->attributes();

            if (!isset($xml_order_attributes['@attributes']['version'])) {
                throw new rs_opentrans_exception('ORDER version not set in "' . $src_file . '"');
            } else {
                $version = $xml_order_attributes['@attributes']['version'];
                unset($xml_order['@attributes']);
            }

            if (count($xml_order) != 3) {
                throw new rs_opentrans_exception('"' . $src_file . '" is not a valid openTrans standard "' . $version . '" order');
            }

            if (count($xml_order_item_list->get_field('ORDER_ITEM')) != abs($xml_order_summary->as_int('TOTAL_ITEM_NUM'))) {
                throw new rs_opentrans_exception('ORDER_SUMMARY TOTAL_ITEM_NUM (' . abs($xml_order_summary->as_int('TOTAL_ITEM_NUM')) . ') and count of ORDER_ITEM_LIST ITEMS (' . count($xml_order_item_list->get_field('ORDER_ITEM')) . ') differs');
            }

            // Starting with header

            $opentrans_order = new rs_opentrans_document_order(rs_opentrans_document_order::TYPE_STANDARD);

            $opentrans_order_header = $opentrans_order->create_header();

            $xml_order_header_controlinfo = $xml_order_header->get_field('CONTROL_INFO');

            $opentrans_order_header->create_controlinfo($xml_order_header_controlinfo->as_string('STOP_AUTOMATIC_PROCESSING', false), $xml_order_header_controlinfo->as_string('GENERATOR_INFO'), $xml_order_header_controlinfo->as_int('GENERATION_DATE'));

            $xml_order_header_orderinfo = $xml_order_header->get_field('ORDER_INFO');

            $opentrans_order_info = $opentrans_order_header->create_orderinfo($xml_order_header_orderinfo->as_string('ORDER_ID'), $xml_order_header_orderinfo->as_string('ORDER_DATE'));

            $opentrans_order_info->set_order_date($xml_order_header_orderinfo->as_string('ORDER_DATE'));

            $payment = $xml_order_header_orderinfo->get_field('PAYMENT');

            $payment_term_type = NULL;

            if ($payment->field_exists('CASH')) {
                $payment_type = rs_opentrans_document_payment::TYPE_CASH;
                $payment_term_type = $payment->get_field('CASH')->as_string('PAYMENT_TERM');
            } else if ($payment->field_exists('CARD')) {
                $payment_type = rs_opentrans_document_payment::TYPE_CARD;
            } else if ($payment->field_exists('ACCOUNT')) {
                $payment_type = rs_opentrans_document_payment::TYPE_ACCOUNT;
            } else if ($payment->field_exists('DEBIT')) {
                $payment_type = rs_opentrans_document_payment::TYPE_DEBIT;
            } else if ($payment->field_exists('CHECK')) {
                $payment_type = rs_opentrans_document_payment::TYPE_CHECK;
            } else {
                $payment_type = rs_opentrans_document_payment::TYPE_OTHER;
            }

            $opentrans_order_info->set_payment(new rs_opentrans_document_payment($payment_type, $payment_term_type));

            // Add Credit card data
            if (rs_opentrans_document_payment::TYPE_CARD == $payment_type) {

                $opentrans_order_info->get_payment()->set_payment_specific_data_element('CARD_NUM', $payment->get_field('CARD', true)->as_string('CARD_NUM', false));
                $opentrans_order_info->get_payment()->set_payment_specific_data_element('CARD_EXPIRATION_DATE', $payment->get_field('CARD', true)->as_string('CARD_EXPIRATION_DATE', false));
                $opentrans_order_info->get_payment()->set_payment_specific_data_element('CARD_HOLDER_NAME', $payment->get_field('CARD', true)->as_string('CARD_HOLDER_NAME', false));
                $cc_attributes = $payment->get_field('CARD', true)->attributes();
                $opentrans_order_info->get_payment()->set_payment_specific_data_element('CARD_TYPE', (string)$cc_attributes['type']);

            }

            // Adding currency

            $currency = $xml_order_header_orderinfo->as_string('CURRENCY', false) ? $xml_order_header_orderinfo->as_string('CURRENCY', false) : 'EUR';
            $opentrans_order_info->set_currency($currency);

            // Adding Remarks

            if ($xml_order_header_orderinfo->get_field('REMARK', false) != NULL) {

                $xml_remarks = $xml_order_header_orderinfo->get_field('REMARK');

                if (is_array($xml_remarks) || is_object($xml_remarks)) {

                    for ($i = 0; is_object($xml_remarks->get_field($i, false)); $i++) {

                        // Validation version orderapi: throw exception if difference in major.

                        if ('version_orderapi' == $xml_remarks->get_field_attribute($i, 'type') && !$this->is_version_valid($xml_remarks->as_string($i))) {
                            throw new rs_opentrans_exception('Version orderapi "' . $xml_remarks->as_string($i) . '" is uncompatible to actual version orderapi ' . rs_opentrans_document::VERSION_ORDERAPI);
                        }

                        $opentrans_order_info->add_remark($xml_remarks->get_field_attribute($i, 'type'), $xml_remarks->as_string($i));

                    }

                }

            }

            // Adding Partys

            if ($xml_order_header_orderinfo->get_field('PARTIES') != NULL && $xml_order_header_orderinfo->get_field('PARTIES')->get_field('PARTY') != NULL) {

                $xml_parties = $xml_order_header_orderinfo->get_field('PARTIES')->get_field('PARTY');

                for ($i = 0; is_object($xml_parties->get_field($i, false)); $i++) {

                    $xml_party = $xml_parties->get_field($i);

                    if (!$this->is_testsieger_party_id($xml_party->as_string('PARTY_ID'))) {
                        throw new rs_opentrans_exception('PARTY_ID "' . $xml_party->as_string('PARTY_ID') . '" is not Testsieger-formated');
                    }

                    $xml_address = $xml_party->get_field('ADDRESS');

                    $opentrans_party = new rs_opentrans_document_party($xml_party->as_string('PARTY_ID'), $xml_party->get_field_attribute('PARTY_ID', 'type'), $xml_party->as_string('PARTY_ROLE'));
                    $opentrans_address = new rs_opentrans_document_address();

                    $opentrans_address->set_name($xml_address->as_string('NAME', false));
                    $opentrans_address->set_name2($xml_address->as_string('NAME2', false));
                    $opentrans_address->set_name3($xml_address->as_string('NAME3', false));
                    $opentrans_address->set_street($xml_address->as_string('STREET', false));
                    $opentrans_address->set_zip($xml_address->as_string('ZIP', false));
                    $opentrans_address->set_city($xml_address->as_string('CITY', false));
                    $opentrans_address->set_country($xml_address->as_string('COUNTRY', false));
                    $opentrans_address->set_country_coded($xml_address->as_string('COUNTRY_CODED', false));
                    $opentrans_address->set_address_remarks($xml_address->as_string('ADDRESS_REMARKS', false), $xml_address->get_field_attribute('ADDRESS_REMARKS', 'type'));

                    if ($xml_address->field_exists('EMAIL')) {
                        $opentrans_address->add_email($xml_address->as_string('EMAIL', false));
                    }

                    if ($xml_address->field_exists('PHONE')) {
                        $opentrans_address->set_phone($xml_address->as_string('PHONE', false));
                    }

                    if ($xml_address->field_exists('CONTACT_DETAILS')) {

                        $xml_address_contact_details = $xml_address->get_field('CONTACT_DETAILS');

                        if (is_array($xml_address_contact_details) || is_object($xml_address_contact_details)) {

                            $opentrans_address_contact_details = new rs_opentrans_document_address_contactdetails();

                            $opentrans_address_contact_details->set_contact_id($xml_address_contact_details->as_string('CONTACT_ID', false));
                            $opentrans_address_contact_details->set_contact_name($xml_address_contact_details->as_string('CONTACT_NAME', false));
                            $opentrans_address_contact_details->set_first_name($xml_address_contact_details->as_string('FIRST_NAME', false));
                            $opentrans_address_contact_details->set_title($xml_address_contact_details->as_string('TITLE', false));
                            $opentrans_address_contact_details->set_academic_title($xml_address_contact_details->as_string('ACADEMIC_TITLE', false));
                            $opentrans_address_contact_details->set_contact_descr($xml_address_contact_details->as_string('CONTACT_DESCR', false));
                            $opentrans_address_contact_details->set_url($xml_address_contact_details->as_string('URL', false));
                            $opentrans_address_contact_details->set_emails($xml_address_contact_details->as_string('EMAILS', false));
                            $opentrans_address_contact_details->set_authentification($xml_address_contact_details->as_string('AUTHENTIFICATION', false));

                            $opentrans_address->set_contact_details($opentrans_address_contact_details);

                        }

                    }

                    $opentrans_party->set_address($opentrans_address);

                    $opentrans_order_info->add_party($opentrans_party);

                }

            }

            // Adding cart items

            $xml_items = $xml_order_item_list->get_field('ORDER_ITEM');

            for ($i = 0; is_object($xml_items->get_field($i, false)); $i++) {

                $xml_item = $xml_items->get_field($i);

                $opentrans_item = new rs_opentrans_document_item();

                $opentrans_item->set_product_id(new rs_opentrans_document_item_productid(array($xml_item->get_field('PRODUCT_ID')->as_string('SUPPLIER_PID'))));
                $opentrans_item->set_quantity($xml_item->as_string('QUANTITY'));
                $opentrans_item->set_order_unit($xml_item->as_string('ORDER_UNIT'));
                $opentrans_item->set_product_price_fix(new rs_opentrans_document_item_productpricefix($xml_item->get_field('PRODUCT_PRICE_FIX')->as_string('PRICE_AMOUNT')));

                $opentrans_item->set_tax_details_fix(
                    new rs_opentrans_document_item_taxdetailsfix(
                        NULL,
                        $xml_item->get_field('PRODUCT_PRICE_FIX')->get_field('TAX_DETAILS_FIX')->as_string('TAX_CATEGORY'),
                        array(),
                        $xml_item->get_field('PRODUCT_PRICE_FIX')->get_field('TAX_DETAILS_FIX')->as_string('TAX')
                    )
                );

                $opentrans_item->set_price_line_amount($xml_item->as_float('PRICE_LINE_AMOUNT'));

                // Adding Remarks

                if ($xml_item->get_field('REMARK', false) != NULL) {

                    $xml_remarks = $xml_item->get_field('REMARK');

                    if (is_array($xml_remarks) || is_object($xml_remarks)) {

                        for ($k = 0; is_object($xml_remarks->get_field($k, false)); $k++) {
                            $opentrans_item->add_remark($xml_remarks->get_field_attribute($k, 'type'), $xml_remarks->as_string($k));
                        }

                    }

                }

                $opentrans_order->add_item($opentrans_item);

            }

            // Set total amount (Sum of all costs) to value as found in XML.
            $opentrans_order->get_summary()->set_total_amount(
                $xml_order_summary->as_float('TOTAL_AMOUNT', true)
            );

            return $opentrans_order;

        }

        /**
         * proves if the party id is in testieger format
         *
         * @param string value
         * @return boolean
         */
        public function is_testsieger_party_id($value) {

            if (!is_string($value)) {
                throw new rs_opentrans_exception('$value must be a string.');
            }

            if (preg_match('/TS-.*/', $value)) {
                return true;
            }

            return false;

        }

    }