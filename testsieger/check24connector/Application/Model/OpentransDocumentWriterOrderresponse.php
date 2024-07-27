<?php
/**
 * Class added by Itratos
 */

namespace TestSieger\Check24Connector\Application\Model;

use TestSieger\Check24Connector\Application\Model\Config as OpentransConfig;

/**
 * Class to create Orderresponse document for CHECK24
 */
class OpentransDocumentWriterOrderresponse extends OpentransDocumentWriter
{

    /**
     * Creates and saves xml file from orderresponse document
     *
     * @param $src
     * @return false|string
     * @throws OpentransException
     */
    public function get_document_data_orderresponse($src)
    {

        if (!$src instanceof OpentransDocumentOrderResponse) {
            throw new OpentransException('$src must be type of rs_opentrans_document_orderresponse.');
        }

        // start with order list, which could contain more then one order
        $xml = new \SimpleXMLElement('<ORDERRESPONSE xsi:schemaLocation="http://www.opentrans.org/XMLSchema/2.1 https://merchantcenter.check24.de/sdk/opentrans/schema-definitions/opentrans_2_1.xsd http://www.bmecat.org/bmecat/2005 https://merchantcenter.check24.de/sdk/opentrans/schema-definitions/bmecat_2005.xsd http://www.w3.org/2005/05/xmlmime https://merchantcenter.check24.de/sdk/opentrans/schema-definitions/xmlmime.xsd http://www.w3.org/2000/09/xmldsig# https://merchantcenter.check24.de/sdk/opentrans/schema-definitions/xmldsig-core-schema.xsd" xmlns="http://www.opentrans.org/XMLSchema/2.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"  xmlns:bmecat="https://www.bme.de/initiativen/bmecat/bmecat-2005/" xmlns:xmime="http://www.w3.org/2005/05/xmlmime"></ORDERRESPONSE>');

        //var_dump('<ORDERRESPONSE xmlns="http://www.opentrans.org/XMLSchema/2.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentrans.org/XMLSchema/2.1 https://merchantcenter.check24.de/sdk/opentrans/schema-definitions/opentrans_2_1.xsd http://www.bmecat.org/bmecat/2005 https://merchantcenter.check24.de/sdk/opentrans/schema-definitions/bmecat_2005.xsd http://www.w3.org/2005/05/xmlmime https://merchantcenter.check24.de/sdk/opentrans/schema-definitions/xmlmime.xsd http://www.w3.org/2000/09/xmldsig# https://merchantcenter.check24.de/sdk/opentrans/schema-definitions/xmldsig-core-schema.xsd" xmlns:bmecat="https://www.bme.de/initiativen/bmecat/bmecat-2005/" xmlns:xmime="http://www.w3.org/2005/05/xmlmime"></ORDERRESPONSE>');


        // Document
        $xml->addAttribute('version', '2.1');

        // Header
        $header = $xml->addChild('ORDERRESPONSE_HEADER');

        $NAMESPACE_BMECAT = OpentransConfig::NAMESPACE_BMECAT_URL;

        // Control info
        $info = $header->addChild('CONTROL_INFO');

        // Generator info
        if (($name = $src->get_header()->get_controlinfo()->get_generator_name()) !== NULL) {
            $info->addChild('GENERATOR_INFO', $name);
        }
        if (($date = $src->get_header()->get_controlinfo()->get_generation_date()) !== NULL) {
            $info->addChild('GENERATION_DATE', $date);
        }

        // Order info
        $oinfo = $header->addChild('ORDERRESPONSE_INFO');
        $oinfo->addChild('ORDER_ID', OpentransHelper::formatString($src->get_header()->get_orderresponseinfo()->get_order_id(), 7));
        $oinfo->addChild('ORDERRESPONSE_DATE', $src->get_header()->get_orderresponseinfo()->get_orderresponse_date());

        if ($sOrderDate = $src->get_header()->get_orderresponseinfo()->get_order_date()) {
            $oinfo->addChild('ORDER_DATE', $sOrderDate);
        }

        if ($sDeliveryStartDate = $src->get_header()->get_orderresponseinfo()->get_delivery_startdate()) {
            $oinfo->addChild('DELIVERY_DATE', $sDeliveryStartDate());
        }

        if ($sSupplierOrderId = $src->get_header()->get_orderresponseinfo()->get_supplier_orderid()) {
            $oinfo->addChild('SUPPLIER_ORDER_ID', $sSupplierOrderId);
        }


        // Order Parties
        $parties = $oinfo->addChild('PARTIES');
        $src_parties = $src->get_header()->get_orderresponseinfo()->get_parties();

        for ($i = 0, $i_max = count($src_parties); $i < $i_max; ++$i) {
            $party = $parties->addChild('PARTY');
            $party_id = $party->addChild('PARTY_ID', OpentransHelper::formatString($src_parties[$i]->get_id()->get_id(), 46), $NAMESPACE_BMECAT);
            $party_id->addAttribute('type', $src_parties[$i]->get_id()->get_type());
            if ($sPartyRole = $src_parties[$i]->get_role()) {
                $party->addChild('PARTY_ROLE', $sPartyRole);
            }

            $src_address = $src_parties[$i]->get_address();

            $address = $party->addChild('ADDRESS');

            if ($sAddressName = $src_address->get_name()) {
                $address->addChild('NAME', str_replace('&', '&amp;', str_replace('&amp;', '&', $sAddressName)), $NAMESPACE_BMECAT);
            }
            if ($sAddressName2 = $src_address->get_name2()) {
                $address->addChild('NAME2', str_replace('&', '&amp;', str_replace('&amp;', '&', $sAddressName2)), $NAMESPACE_BMECAT);
            }
            if ($sAddressName3 = $src_address->get_name3()) {
                $address->addChild('NAME3', str_replace('&', '&amp;', str_replace('&amp;', '&', $sAddressName3)), $NAMESPACE_BMECAT);
            }
            if ($sAddressStreet = $src_address->get_street()) {
                $address->addChild('STREET', $sAddressStreet, $NAMESPACE_BMECAT);
            }
            if ($sAddressZip = $src_address->get_zip()) {
                $address->addChild('ZIP', $sAddressZip, $NAMESPACE_BMECAT);
            }
            if ($sAddressCity = $src_address->get_city()) {
                $address->addChild('CITY', $sAddressCity, $NAMESPACE_BMECAT);
            }
            if ($sAddressCountry = $src_address->get_country()) {
                $address->addChild('COUNTRY', $sAddressCountry, $NAMESPACE_BMECAT);
            }
            if ($sAddressCountryCoded = $src_address->get_country_coded()) {
                $address->addChild('COUNTRY_CODED', $sAddressCountryCoded, $NAMESPACE_BMECAT);
            }

            $src_phone = $src_address->get_phone();
            if (count($src_phone) > 0) {
                foreach ($src_phone as $phone_type => $phone_number) {
                    $phone = $address->addChild('PHONE', $phone_number, $NAMESPACE_BMECAT);
                    if ($phone_type) {
                        $phone->addAttribute('type', $phone_type);
                    }
                }
            }

            $src_fax = $src_address->get_fax();
            if (count($src_fax) > 0) {
                foreach ($src_fax as $fax_type => $fax_number) {
                    $address->addChild('FAX', $fax_number, $NAMESPACE_BMECAT)->addAttribute('type', $fax_type);
                }
            }

            $src_email = $src_address->get_emails();
            if (count($src_email) > 0) {
                $address->addChild('EMAIL', $src_email[0], $NAMESPACE_BMECAT);
            }


            $src_address_remarks = $src_address->get_address_remarks();
            if (count($src_address_remarks) > 0) {
                foreach ($src_address_remarks as $address_remarks_delivery_type => $address_remarks_packstation_postnumber) {
                    $address_remarks_packstation_postnumber = str_replace('&', '&amp;', str_replace('&amp;', '&', $address_remarks_packstation_postnumber));
                    $address->addChild('ADDRESS_REMARKS', $address_remarks_packstation_postnumber, $NAMESPACE_BMECAT)->addAttribute('type', $address_remarks_delivery_type);
                }
            }
        }

        // creating IDREFs for Parties
        $parties_reference = $oinfo->addChild('ORDER_PARTIES_REFERENCE');

        $src_parties_reference_buyer_idref = OpentransHelper::formatString(
            $src->get_header()->get_orderresponseinfo()->get_idref(OpentransDocumentIdref::TYPE_DELIVERY_IDREF), 46);
        $src_parties_reference_supplier_idref = $src->get_header()->get_orderresponseinfo()->get_idref(OpentransDocumentIdref::TYPE_SUPPLIER_IDREF);
        //$src_parties_reference_invoice_recipient_idref = $src->get_header()->get_orderresponseinfo()->get_idref(OpentransDocumentIdref::TYPE_INVOICE_RECIPIENT_IDREF);

        $parties_reference_buyer_idref = $parties_reference->addChild('BUYER_IDREF', $src_parties_reference_buyer_idref, $NAMESPACE_BMECAT);
        $parties_reference_buyer_idref->addAttribute('type', OpentransDocumentPartyid::TYPE_CHECK24);

        if ($src_parties_reference_supplier_idref) {
            $parties_reference_supplier_idref = $parties_reference->addChild('SUPPLIER_IDREF', $src_parties_reference_supplier_idref, $NAMESPACE_BMECAT);
            $parties_reference_supplier_idref->addAttribute('type', OpentransDocumentPartyid::TYPE_CHECK24);
        }

        /* if($src_parties_reference_invoice_recipient_idref) {
            $parties_reference_invoice_recipient_idref = $parties_reference->addChild('INVOICE_RECIPIENT_IDREF', $src_parties_reference_invoice_recipient_idref, $NAMESPACE_BMECAT);
            $parties_reference_invoice_recipient_idref->addAttribute('type', OpentransDocumentPartyid::TYPE_CHECK24);
        } */

        if ($src_parties_reference_buyer_idref) {
            $parties_reference_delivery_idref = $parties_reference->addChild('SHIPMENT_PARTIES_REFERENCE')->addChild('DELIVERY_IDREF', $src_parties_reference_buyer_idref);
            $parties_reference_delivery_idref->addAttribute('type', OpentransDocumentPartyid::TYPE_CHECK24);
        }
        // Remarks
        $src_remarks = $src->get_header()->get_orderresponseinfo()->get_remarks();
        if (count($src_remarks) > 0) {
            foreach ($src_remarks as $type => $value) {
                $oinfo->addChild('REMARKS', $value)->addAttribute('type', $type);
            }
        }

        // Add version orderapi as remark
        $oinfo->addChild('REMARKS', OpentransDocument::VERSION_ORDERAPI)->addAttribute('type', 'versionorderapi');

        // Items

        $items = $xml->addChild('ORDERRESPONSE_ITEM_LIST');

        $src_items = $src->get_item_list();

        for ($i = 0, $i_max = count($src_items); $i < $i_max; ++$i) {

            $item = $items->addChild('ORDERRESPONSE_ITEM');

            $item->addChild('LINE_ITEM_ID', ($src_items[$i]->get_line_item_id() !== NULL ? $src_items[$i]->get_line_item_id() : $i));

            $product_id = $item->addChild('PRODUCT_ID');

            if ($sSupplierPID = $src_items[$i]->get_product_id()->get_supplier_pid()) {
                $product_id->addChild('SUPPLIER_PID', $sSupplierPID, $NAMESPACE_BMECAT);
            }

            if ($sDescriptionShort = $src_items[$i]->get_product_id()->get_description_short()) {
                $product_id->addChild('DESCRIPTION_SHORT', $sDescriptionShort, $NAMESPACE_BMECAT);
            }

            if ($sDescriptionLong = $src_items[$i]->get_product_id()->get_description_long() != '') {
                $product_id->addChild('DESCRIPTION_LONG', $sDescriptionLong);
            }

            $item->addChild('QUANTITY', $src_items[$i]->get_quantity());
            $item->addChild('ORDER_UNIT', $src_items[$i]->get_order_unit(), $NAMESPACE_BMECAT);

            if ($fPriceLineAmount = $src_items[$i]->get_price_line_amount()) {
                $item->addChild('PRICE_LINE_AMOUNT', $fPriceLineAmount);
            }

            if ($src_parties_reference_buyer_idref) {
                $parties_reference_delivery_idref = $item->addChild('SHIPMENT_PARTIES_REFERENCE')->addChild('DELIVERY_IDREF', $src_parties_reference_buyer_idref);
                $parties_reference_delivery_idref->addAttribute('type', OpentransDocumentPartyid::TYPE_CHECK24);
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
        $summary = $xml->addChild('ORDERRESPONSE_SUMMARY');
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
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $domnode = dom_import_simplexml($xml);
        $domnode = $dom->importNode($domnode, true);
        $dom->appendChild($domnode);
        return $dom->saveXML();
    }
}