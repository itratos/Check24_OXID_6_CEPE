<?php
/**
 * openTrans Document Writer lexware 1.1
 *
 * Extense the standart openTrans Document Writer for Lexware
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

class OpentransDocumentWriterOrderLexware extends OpentransDocumentWriter
{

    /**
     * Convert the Object from opentransdocument to XML for Lexware-Standard
     * 1.1 of ORDER-Files
     *
     * @param object $src
     * @return string
     */
    public function get_document_data_order($src)
    {

        if (!$src instanceof OpentransDocumentOrder) {
            throw new OpentransException('$src must be type of OpentransDocumentOrder.');
        }

        // start with order list, which could contain more then one order

        $xml = new \SimpleXMLElement('<ORDER_LIST></ORDER_LIST>');

        // Document

        $doc = $xml->addChild('ORDER');
        $doc->addAttribute('xmlns', 'http://www.opentrans.org/XMLSchema/1.0');
        $doc->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $doc->addAttribute('version', '1.0');
        $doc->addAttribute('type', 'standard');

        // Header

        $header = $doc->addChild('ORDER_HEADER');

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

        $oinfo = $header->addChild('ORDER_INFO');
        $oinfo->addChild('ORDER_ID',
            OpentransHelper::formatString($src->get_header()->get_orderinfo()->get_order_id(), 7)
        );
        $oinfo->addChild('ORDER_DATE', $src->get_header()->get_orderinfo()->get_order_date());

        // Order Parties

        $parties = $oinfo->addChild('ORDER_PARTIES');
        $src_parties = $src->get_header()->get_orderinfo()->get_parties();

        $party_mapping = array(
            'invoice_recipient_party' => 'INVOICE_PARTY'
        );

        for ($i = 0, $i_max = count($src_parties); $i < $i_max; ++$i) {

            if (($role_key = $src_parties[$i]->get_role()) == OpentransDocumentParty::ROLE_INVOICE_RECIPIENT) {
                $role_key = 'invoice';
            }

            if ($role_key == OpentransDocumentParty::ROLE_DELIVERY) {
                $party = $parties->addChild('SHIPMENT_PARTIES')->addChild(strtoupper($role_key) . '_PARTY')->addChild('PARTY');
            } else {
                $party = $parties->addChild(strtoupper($role_key) . '_PARTY')->addChild('PARTY');
            }

            $party_id = $party->addChild('PARTY_ID',
                OpentransHelper::formatString($src_parties[$i]->get_id()->get_id(), 46)
            );
            $party_id->addAttribute('type', $src_parties[$i]->get_id()->get_type());

            $src_address = $src_parties[$i]->get_address();

            $address = $party->addChild('ADDRESS');

            $address->addChild('NAME', str_replace('&', '&amp;', str_replace('&amp;', '&', $src_address->get_name())));
            $address->addChild('NAME2', str_replace('&', '&amp;', str_replace('&amp;', '&', $src_address->get_name2())));
            $address->addChild('NAME3', str_replace('&', '&amp;', str_replace('&amp;', '&', $src_address->get_name3())));
            $address->addChild('STREET', $src_address->get_street());
            $address->addChild('ZIP', $src_address->get_zip());
            $address->addChild('CITY', $src_address->get_city());
            $address->addChild('COUNTRY', $src_address->get_country_coded());

            $src_phone = $src_address->get_phone();

            if (count($src_phone) > 0) {

                foreach ($src_phone as $phone_type => $phone_number) {
                    $address->addChild('PHONE', $phone_number)->addAttribute('type', $phone_type);
                }

            }

            $src_fax = $src_address->get_fax();

            if (count($src_fax) > 0) {

                foreach ($src_fax as $fax_type => $fax_number) {
                    $address->addChild('FAX', $fax_number)->addAttribute('type', $fax_type);
                }

            }

            $src_email = $src_address->get_emails();

            if (count($src_email) > 0) {
                $address->addChild('EMAIL', $src_email[0]);
            }

        }

        // Remarks

        $src_remarks = $src->get_header()->get_orderinfo()->get_remarks();

        if (count($src_remarks) > 0) {

            foreach ($src_remarks as $type => $value) {
                $oinfo->addChild('REMARKS', $value)->addAttribute('type', $type);
            }

        }

        // Add version orderapi as remark

        $oinfo->addChild('REMARKS', OpentransDocument::VERSION_ORDERAPI)->addAttribute('type', 'version_orderapi');

        // Payment

        $oinfo->addChild('PRICE_CURRENCY', 'EUR');

        $src_payment = $src->get_header()->get_orderinfo()->get_payment();

        if ($src_payment) {

            if ($src_payment->get_type() != OpentransDocumentPayment::TYPE_OTHER) {
                $payment = $oinfo->addChild('PAYMENT')->addChild(strtoupper($src_payment->get_type()));
            }

            switch ($src_payment->get_type()) {

                case OpentransDocumentPayment::TYPE_CARD:

                    $payment->addChild('CARD_NUM', $src_payment->get_card_num());
                    $payment->addChild('CARD_EXPIRATION_DATE', $src_payment->get_card_expiration_date());
                    $payment->addAttribute('type', $src_payment->get_card_type());
                    $payment->addChild('CARD_HOLDER_NAME', $src_payment->get_card_holder_name());

                    $payment_card_term = $payment->addChild('PAYMENT_TERM', $src_payment->get_term_value());
                    $payment_card_term->addAttribute('type', $src_payment->get_term_type());

                    break;

                case OpentransDocumentPayment::TYPE_ACCOUNT:

                    $payment->addChild('HOLDER', $src_payment->get_holder());

                    $account = $payment->addChild('BANK_ACCOUNT', $src_payment->get_bank_account());

                    if ($src_payment->get_bank_account_type() != OpentransDocumentPaymentAccount::BANK_ACCOUNT_TYPE_STANDARD) {
                        $account->addAttribute('type', $src_payment->get_bank_account_type());
                    }

                    $code = $payment->addChild('BANK_CODE', $src_payment->get_bank_code());

                    if ($src_payment->get_bank_code_type() != OpentransDocumentPaymentAccount::BANK_CODE_TYPE_STANDARD) {
                        $account->addAttribute('type', $src_payment->get_bank_code_type());
                    }

                    $payment->addChild('BANK_NAME', $src_payment->get_bank_name());
                    $payment->addChild('BANK_COUNTRY', $src_payment->get_bank_country());
                    $payment->addChild('PAYMENT_TERM', $src_payment->get_term_value())->addAttribute('type', $src_payment->get_term_type());

                    break;

                default:

                    if ($src_payment->get_term_type() !== NULL) {

                        $payment_cash_term = $oinfo->addChild('PAYMENT')->addChild('CASH')->addChild('PAYMENT_TERM', $src_payment->get_term_value());
                        $payment_cash_term->addAttribute('type', $src_payment->get_term_type());

                    }

                    break;

            }

        }

        // Items

        $items = $doc->addChild('ORDER_ITEM_LIST');

        $src_items = $src->get_item_list();

        for ($i = 0, $i_max = count($src_items); $i < $i_max; ++$i) {

            $item = $items->addChild('ORDER_ITEM');
            $item->addChild('LINE_ITEM_ID', ($src_items[$i]->get_line_item_id() !== NULL ? $src_items[$i]->get_line_item_id() : $i));

            $product_id = $item->addChild('ARTICLE_ID');
            $product_id->addChild('SUPPLIER_AID', $src_items[$i]->get_product_id()->get_supplier_pid());

            if ($src_items[$i]->get_product_id()->get_description_short() != '') {
                $product_id->addChild('DESCRIPTION_SHORT', $src_items[$i]->get_product_id()->get_description_short());
            }

            if ($src_items[$i]->get_product_id()->get_description_long() != '') {
                $product_id->addChild('DESCRIPTION_LONG', $src_items[$i]->get_product_id()->get_description_long());
            }

            $item->addChild('QUANTITY', $src_items[$i]->get_quantity());

            $price = $item->addChild('ARTICLE_PRICE');
            $price->addAttribute('type', 'gros_list');
            $price->addChild('PRICE_AMOUNT', $src_items[$i]->get_product_price_fix()->get_price_amount());
            $price->addChild('PRICE_LINE_AMOUNT', $src_items[$i]->get_price_line_amount());

            // Remarks from items

            $src_items_remarks = $src_items[$i]->get_remarks();

            if (count($src_items_remarks) > 0) {

                $src_items_remarks_sum = 0;

                foreach ($src_items_remarks as $type => $value) {

                    $item->addChild('REMARKS', $value)->addAttribute('type', $type);

                    if ($type == 'recycling' || $type == 'installation') {
                        $src_items_remarks_sum += $value;
                    }

                }

            }

            $tax = $src_items[$i]->get_tax_details_fix();

            if ($tax !== NULL) {
                $price->addChild('TAX', $tax->get_tax());
            }

        }

        // Order Summary

        $summary = $doc->addChild('ORDER_SUMMARY');
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

        $dom = new \DOMDocument('1.0', 'ISO-8859-15');
        $dom->formatOutput = true;

        $domnode = dom_import_simplexml($xml);
        $domnode = $dom->importNode($domnode, true);
        $domnode = $dom->appendChild($domnode);

        return $dom->saveXML();

    }

}