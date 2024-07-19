<?php
    require_once(getShopBasePath().'modules/ts_opentrans_orderimport/opentrans/opentrans_helper.php');
    /**
     * openTrans Orderchange Document Writer standard 2.1
     *
     * Extense the standard openTrans Document Writer for standard 2.1 order
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
    class rs_opentrans_document_writer_orderchange_standard_2_1 extends rs_opentrans_document_writer {

        /**
         * Convert the Object from OPENTRANSDOCUMENT to XML for openTrans -
         * Standard 2.1 of orderchange-Files
         *
         * @param object $src
         * @return object simpleXMLElement
         */
        public function get_document_data_orderchange($src) {

            if (!$src instanceof rs_opentrans_document_orderchange) {
                throw new rs_opentrans_exception('$src must be type of rs_opentrans_document_orderchange.');
            }

            // start with order list, which could contain more then one order

            $xml = new SimpleXMLElement('<ORDERCHANGE></ORDERCHANGE>');

            // Document

            $xml->addAttribute('version', '2.1');
            $xml->addAttribute('type', 'standard');

            // Header

            $header = $xml->addChild('ORDERCHANGE_HEADER');

            // Control info

            $info = $header->addChild('CONTROL_INFO');

            if (($stop_automatic_processing = $src->get_header()->get_controlinfo()->get_stop_automatic_processing()) !== NULL) {
                $info->addChild('STOP_AUTOMATIC_PROCESSING', $stop_automatic_processing);
            }

            // Generator info

            if (($name = $src->get_header()->get_controlinfo()->get_generator_name()) !== NULL) {
                $info->addChild('GENERATOR_INFO', $name);
            }

            if (($date = $src->get_header()->get_controlinfo()->get_generation_date()) !== NULL) {
                $info->addChild('GENERATION_DATE', $date);
            }

            // Order info

            $oinfo = $header->addChild('ORDERCHANGE_INFO');
            $oinfo->addChild('ORDER_ID', opentrans_helper::formatString($src->get_header()->get_orderchangeinfo()->get_order_id(), 7));
            $oinfo->addChild('ORDERCHANGE_DATE', $src->get_header()->get_orderchangeinfo()->get_orderchange_date());
            $oinfo->addChild('ORDER_DATE', $src->get_header()->get_orderchangeinfo()->get_order_date());
            $oinfo->addChild('ORDERCHANGE_SEQUENCE_ID', $src->get_header()->get_orderchangeinfo()->get_orderchange_sequence_id());

            // Order Parties

            $parties = $oinfo->addChild('PARTIES');
            $src_parties = $src->get_header()->get_orderchangeinfo()->get_parties();

            for ($i = 0, $i_max = count($src_parties); $i < $i_max; ++$i) {

                $party = $parties->addChild('PARTY');

                $party->addChild('PARTY_ROLE', $src_parties[$i]->get_role());

                $party_id = $party->addChild('PARTY_ID', opentrans_helper::formatString($src_parties[$i]->get_id()->get_id(), 46));
                $party_id->addAttribute('type', $src_parties[$i]->get_id()->get_type());

                $src_address = $src_parties[$i]->get_address();

                $address = $party->addChild('ADDRESS');

                $address->addChild('NAME', str_replace('&', '&amp;', str_replace('&amp;', '&', $src_address->get_name())));
                $address->addChild('NAME2', str_replace('&', '&amp;', str_replace('&amp;', '&', $src_address->get_name2())));
                $address->addChild('NAME3', str_replace('&', '&amp;', str_replace('&amp;', '&', $src_address->get_name3())));
                $address->addChild('STREET', $src_address->get_street());
                $address->addChild('ZIP', $src_address->get_zip());
                $address->addChild('CITY', $src_address->get_city());
                $address->addChild('COUNTRY', $src_address->get_country());
                $address->addChild('COUNTRY_CODED', $src_address->get_country_coded());

                $src_phone = $src_address->get_phone();

                if (count($src_phone) > 0) {

                    foreach ($src_phone AS $phone_type => $phone_number) {
                        $address->addChild('PHONE', $phone_number)->addAttribute('type', $phone_type);
                    }

                }

                $src_fax = $src_address->get_fax();

                if (count($src_fax) > 0) {

                    foreach ($src_fax AS $fax_type => $fax_number) {
                        $address->addChild('FAX', $fax_number)->addAttribute('type', $fax_type);
                    }

                }

                $src_email = $src_address->get_emails();

                if (count($src_email) > 0) {
                    $address->addChild('EMAIL', $src_email[0]);
                }

                $src_address_remarks = $src_address->get_address_remarks();

                if (count($src_address_remarks) > 0) {

                    foreach ($src_address_remarks AS $address_remarks_delivery_type => $address_remarks_packstation_postnumber) {
                        $address_remarks_packstation_postnumber = str_replace('&', '&amp;', str_replace('&amp;', '&', $address_remarks_packstation_postnumber));
                        $address->addChild('ADDRESS_REMARKS', $address_remarks_packstation_postnumber)->addAttribute('type', $address_remarks_delivery_type);
                    }

                }

            }

            // creating IDREFs for Parties

            $parties_reference = $oinfo->addChild('ORDER_PARTIES_REFERENCE');

            $src_parties_reference_buyer_idref = opentrans_helper::formatString($src->get_header()->get_orderchangeinfo()->get_idref(rs_opentrans_document_idref::TYPE_DELIVERY_IDREF), 46);
            $src_parties_reference_supplier_idref = $src->get_header()->get_orderchangeinfo()->get_idref(rs_opentrans_document_idref::TYPE_SUPPLIER_IDREF);
            $src_parties_reference_invoice_recipient_idref = opentrans_helper::formatString($src->get_header()->get_orderchangeinfo()->get_idref(rs_opentrans_document_idref::TYPE_INVOICE_RECIPIENT_IDREF), 46);

            $parties_reference_buyer_idref = $parties_reference->addChild('BUYER_IDREF', $src_parties_reference_buyer_idref);
            $parties_reference_buyer_idref->addAttribute('type', 'testsieger');

            $parties_reference_supplier_idref = $parties_reference->addChild('SUPPLIER_IDREF', $src_parties_reference_supplier_idref);
            $parties_reference_supplier_idref->addAttribute('type', 'testsieger');

            $parties_reference_invoice_recipient_idref = $parties_reference->addChild('INVOICE_RECIPIENT_IDREF', $src_parties_reference_invoice_recipient_idref);
            $parties_reference_invoice_recipient_idref->addAttribute('type', 'testsieger');

            $parties_reference_delivery_idref = $parties_reference->addChild('SHIPMENT_PARTIES_REFERENCE')->addChild('DELIVERY_IDREF', $src_parties_reference_buyer_idref);
            $parties_reference_delivery_idref->addAttribute('type', 'testsieger');

            // Remarks

            $src_remarks = $src->get_header()->get_orderchangeinfo()->get_remarks();

            if (count($src_remarks) > 0) {

                foreach ($src_remarks AS $type => $value) {
                    $oinfo->addChild('REMARK', $value)->addAttribute('type', $type);
                }

            }

            // Add version orderapi as remark

            $oinfo->addChild('REMARK', rs_opentrans_document::VERSION_ORDERAPI)->addAttribute('type', 'version_orderapi');

            // Items

            $items = $xml->addChild('ORDERCHANGE_ITEM_LIST');

            $src_items = $src->get_item_list();

            for ($i = 0, $i_max = count($src_items); $i < $i_max; ++$i) {

                $item = $items->addChild('ORDER_ITEM');

                $item->addChild('LINE_ITEM_ID', ($src_items[$i]->get_line_item_id() !== NULL ? $src_items[$i]->get_line_item_id() : $i));

                $product_id = $item->addChild('PRODUCT_ID');

                $product_id->addChild('SUPPLIER_PID', $src_items[$i]->get_product_id()->get_supplier_pid());

                if ($src_items[$i]->get_product_id()->get_description_short() != '') {
                    $product_id->addChild('DESCRIPTION_SHORT', $src_items[$i]->get_product_id()->get_description_short());
                }

                if ($src_items[$i]->get_product_id()->get_description_long() != '') {
                    $product_id->addChild('DESCRIPTION_LONG', $src_items[$i]->get_product_id()->get_description_long());
                }

                $item->addChild('QUANTITY', $src_items[$i]->get_quantity());
                $item->addChild('ORDER_UNIT', $src_items[$i]->get_order_unit());
                $item->addChild('PRICE_LINE_AMOUNT', $src_items[$i]->get_price_line_amount());

                // Remarks from items

                $src_items_remarks = $src_items[$i]->get_remarks();

                if (count($src_items_remarks) > 0) {

                    $src_items_remarks_sum = 0;

                    foreach ($src_items_remarks AS $type => $value) {

                        $item->addChild('REMARK', str_replace('&', '&amp;', str_replace('&amp;', '&', $value)))->addAttribute('type', $type);

                        if ($type == 'recycling' || $type == 'installation') {
                            $src_items_remarks_sum += $value;
                        }

                    }

                }

                $order_reference = $item->addChild('CUSTOMER_ORDER_REFERENCE');

                $order_reference->addChild('ORDER_ID', opentrans_helper::formatString($src->get_header()->get_orderchangeinfo()->get_order_id(), 7));
                $order_reference->addChild('LINE_ITEM_ID', ($src_items[$i]->get_line_item_id() !== NULL ? $src_items[$i]->get_line_item_id() : $i));

                $parties_reference_delivery_idref = $item->addChild('SHIPMENT_PARTIES_REFERENCE')->addChild('DELIVERY_IDREF', $src_parties_reference_buyer_idref);

                $parties_reference_delivery_idref->addAttribute('type', 'testsieger');

            }

            // Order Summary

            $summary = $xml->addChild('ORDERCHANGE_SUMMARY');

            $summary->addChild('TOTAL_ITEM_NUM', $src->get_summary()->get_total_item_num());

            // Order amount total is the sum of shipping costs, additional_costs, addons and orderpositions amount

            $order_amount_total = $src->get_summary()->get_total_amount();

            if (isset($src_remarks['shipping_fee'])) {
                $order_amount_total += $src_remarks['shipping_fee'];
            }

            if (isset($src_remarks['additional_costs'])) {
                $order_amount_total += $src_remarks['additional_costs'];
            }

            if (isset($src_remarks['services_1_man'])) {
                $order_amount_total += $src_remarks['services_1_man'];
            }

            if (isset($src_remarks['services_2_man'])) {
                $order_amount_total += $src_remarks['services_2_man'];
            }

            if (isset($src_items_remarks_sum)) {
                $order_amount_total += $src_items_remarks_sum;
            }

            $summary->addChild('TOTAL_AMOUNT', $order_amount_total);

            // Output

            $dom = new DOMDocument('1.0', 'ISO-8859-15');

            $dom->formatOutput = true;

            $domnode = dom_import_simplexml($xml);

            $domnode = $dom->importNode($domnode, true);
            $domnode = $dom->appendChild($domnode);

            return $dom->saveXML();

        }

    }