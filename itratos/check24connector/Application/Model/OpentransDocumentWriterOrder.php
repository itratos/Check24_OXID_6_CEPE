<?php


namespace Itratos\Check24Connector\Application\Model;

class OpentransDocumentWriterOrder extends OpentransDocumentWriter
{

    /**
     * Convert the Object from opentransdocument to XML for openTrans-
     * Standard 2.1 of ORDER-Files
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

        $xml = new \SimpleXMLElement('<ORDER></ORDER>');

        // Document

        $xml->addAttribute('version', '2.1');

        // Header

        $header = $xml->addChild('ORDER_HEADER');

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
        $oinfo->addChild('ORDER_ID', OpentransHelper::formatString($src->get_header()->get_orderinfo()->get_order_id(), 7));
        $oinfo->addChild('ORDER_DATE', $src->get_header()->get_orderinfo()->get_order_date());

        // Order Parties

        $parties = $oinfo->addChild('PARTIES');
        $src_parties = $src->get_header()->get_orderinfo()->get_parties();

        for ($i = 0, $i_max = count($src_parties); $i < $i_max; ++$i) {

            $party = $parties->addChild('PARTY');

            $party_id = $party->addChild('PARTY_ID', OpentransHelper::formatString($src_parties[$i]->get_id()->get_id(), 46));
            $party_id->addAttribute('type', $src_parties[$i]->get_id()->get_type());

            $party->addChild('PARTY_ROLE', $src_parties[$i]->get_role());

            $src_address = $src_parties[$i]->get_address();

            $address = $party->addChild('ADDRESS');

            $address->addChild('NAME', str_replace('&', '&amp;', str_replace('&amp;', '&', $src_address->get_name())));
            $address->addChild('NAME2', str_replace('&', '&amp;', str_replace('&amp;', '&', $src_address->get_name2())));
            $address->addChild('NAME3', str_replace('&', '&amp;', str_replace('&amp;', '&', $src_address->get_name3())));

            $src_contact_details = $src_address->get_contact_details();

            if (!empty($src_contact_details)) {

                $contact_details = $address->addChild('CONTACT_DETAILS');

                $contact_details->addChild('CONTACT_ID', $src_contact_details->get_contact_id());
                $contact_details->addChild('CONTACT_NAME', $src_contact_details->get_contact_name());
                $contact_details->addChild('FIRST_NAME', $src_contact_details->get_first_name());
                $childTitle = $contact_details->addChild('TITLE');
                $childTitle[0] = '';
                $contact_details->addChild('ACADEMIC_TITLE', $src_contact_details->get_academic_title());
                $contact_details->addChild('CONTACT_DESCR', $src_contact_details->get_contact_descr());
                $contact_details->addChild('URL', $src_contact_details->get_url());
                $contact_details->addChild('EMAILS', $src_contact_details->get_emails());
                $contact_details->addChild('AUTHENTIFICATION', $src_contact_details->get_authentification());

            }

            $address->addChild('STREET', $src_address->get_street());
            $address->addChild('ZIP', $src_address->get_zip());
            $address->addChild('CITY', $src_address->get_city());
            $address->addChild('COUNTRY', $src_address->get_country());
            $address->addChild('COUNTRY_CODED', $src_address->get_country_coded());

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

            $src_address_remarks = $src_address->get_address_remarks();

            if (count($src_address_remarks) > 0) {

                foreach ($src_address_remarks as $address_remarks_delivery_type => $address_remarks_packstation_postnumber) {
                    $address_remarks_packstation_postnumber = str_replace('&', '&amp;', str_replace('&amp;', '&', $address_remarks_packstation_postnumber));
                    $address->addChild('ADDRESS_REMARKS', (string)$address_remarks_packstation_postnumber)->addAttribute('type', $address_remarks_delivery_type);
                }

            }

        }

        // creating IDREFs for Parties

        $parties_reference = $oinfo->addChild('ORDER_PARTIES_REFERENCE');
        $src_parties_reference_buyer_idref = OpentransHelper::formatString($src->get_header()->get_orderinfo()->get_idref(OpentransDocumentIdref::TYPE_DELIVERY_IDREF), 46);
        $src_parties_reference_supplier_idref = $src->get_header()->get_orderinfo()->get_idref(OpentransDocumentIdref::TYPE_SUPPLIER_IDREF);
        $src_parties_reference_invoice_recipient_idref = OpentransHelper::formatString($src->get_header()->get_orderinfo()->get_idref(OpentransDocumentIdref::TYPE_INVOICE_RECIPIENT_IDREF), 46);

        $parties_reference_buyer_idref = $parties_reference->addChild('BUYER_IDREF', $src_parties_reference_buyer_idref);
        $parties_reference_buyer_idref->addAttribute('type', OpentransDocumentPartyid::TYPE_CHECK24);

        $parties_reference_supplier_idref = $parties_reference->addChild('SUPPLIER_IDREF', $src_parties_reference_supplier_idref);
        $parties_reference_supplier_idref->addAttribute('type', OpentransDocumentPartyid::TYPE_CHECK24);

        $parties_reference_invoice_recipient_idref = $parties_reference->addChild('INVOICE_RECIPIENT_IDREF', $src_parties_reference_invoice_recipient_idref);
        $parties_reference_invoice_recipient_idref->addAttribute('type', OpentransDocumentPartyid::TYPE_CHECK24);

        // Payment

        $oinfo->addChild('CURRENCY', 'EUR');

        $src_payment = $src->get_header()->get_orderinfo()->get_payment();

        if ($src_payment) {

            $payment = $oinfo->addChild('PAYMENT');

            if ($src_payment->get_type() != OpentransDocumentPayment::TYPE_OTHER && $src_payment->get_type() != OpentransDocumentPayment::TYPE_DEBIT) {
                $payment_type = $payment->addChild(strtoupper($src_payment->get_type()));
            }

            switch ($src_payment->get_type()) {

                case OpentransDocumentPayment::TYPE_CARD:

                    $payment_type->addChild('CARD_NUM', $src_payment->get_card_num());
                    $payment_type->addChild('CARD_EXPIRATION_DATE', $src_payment->get_card_expiration_date());
                    $payment_type->addAttribute('type', $src_payment->get_card_type());
                    $payment_type->addChild('CARD_HOLDER_NAME', $src_payment->get_card_holder_name());

                    $payment_card_term = $payment_type->addChild('PAYMENT_TERM', $src_payment->get_term_value());
                    $payment_card_term->addAttribute('type', $src_payment->get_term_type());

                    break;

                case OpentransDocumentPayment::TYPE_ACCOUNT:

                    $payment_type->addChild('HOLDER', $src_payment->get_holder());

                    $account = $payment_type->addChild('BANK_ACCOUNT', $src_payment->get_bank_account());

                    if ($src_payment->get_bank_account_type() != OpentransDocumentPaymentAccount::BANK_ACCOUNT_TYPE_STANDARD) {
                        $account->addAttribute('type', $src_payment->get_bank_account_type());
                    }

                    $code = $payment_type->addChild('BANK_CODE', $src_payment->get_bank_code());

                    if ($src_payment->get_bank_code_type() != OpentransDocumentPaymentAccount::BANK_CODE_TYPE_STANDARD) {
                        $account->addAttribute('type', $src_payment->get_bank_code_type());
                    }

                    $payment_type->addChild('BANK_NAME', $src_payment->get_bank_name());
                    $payment_type->addChild('BANK_COUNTRY', $src_payment->get_bank_country());
                    $payment_type->addChild('PAYMENT_TERM', $src_payment->get_term_value())->addAttribute('type', $src_payment->get_term_type());

                    break;

                default:

                    if ($src_payment->get_term_type() !== NULL) {

                        $payment_cash_term = $payment->addChild('CASH')->addChild('PAYMENT_TERM', $src_payment->get_term_value());
                        $payment_cash_term->addAttribute('type', $src_payment->get_term_type());

                    }

                    break;

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

        // Items

        $items = $xml->addChild('ORDER_ITEM_LIST');

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

            $price = $item->addChild('PRODUCT_PRICE_FIX');
            $price->addChild('PRICE_AMOUNT', $src_items[$i]->get_product_price_fix()->get_price_amount());

            $item->addChild('PRICE_LINE_AMOUNT', $src_items[$i]->get_price_line_amount());

            $src_tax = $src_items[$i]->get_tax_details_fix();

            if ($src_tax !== NULL) {

                $tax = $price->addChild('TAX_DETAILS_FIX');
                $tax->addChild('TAX_CATEGORY', $src_tax->get_tax_category());
                $tax->addChild('TAX', $src_tax->get_tax());

            }

            // Remarks from items

            $src_items_remarks = $src_items[$i]->get_remarks();

            if (count($src_items_remarks) > 0) {

                $src_items_remarks_sum = 0;

                foreach ($src_items_remarks as $type => $value) {

                    $item->addChild('REMARKS', str_replace('&', '&amp;', str_replace('&amp;', '&', $value)))->addAttribute('type', $type);

                    if ($type == 'recycling' || $type == 'installation') {
                        $src_items_remarks_sum += $value;
                    }

                }

            }

        }

        // Order Summary

        $summary = $xml->addChild('ORDER_SUMMARY');
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