<?php

namespace TestSieger\Check24Connector\Application\Model;

use OxidEsales\Eshop\Core\Registry as Registry;

/**
 * Extension to create CHECK24 documents from order
 */
class Order extends Order_parent
{
    /**
     * Unit code for CHECK24
     */
    const ORDER_UNIT_PIECE = 'C62';

    /**
     * Default cancellation reasonkey for CHECK24
     */
    const ORDER_ITEM_CANCELLATION_DEFAULT_REASONKEY = 'other';

    /**
     * Cancellation reasonkey for CHECK24: order invalid
     */
    const ORDER_ITEM_CANCELLATION_REASONKEY_ORDER_INVALID = 'order_invalid';

    /**
     * Default cancellation reasonkey for CHECK24
     */
    const ORDER_ITEM_CANCELLATION_DEFAULT_REASONDESC = 'Default cancellation reason description';

    /**
     * Cancellation reason description for CHECK24: order invalid
     */
    const ORDER_ITEM_CANCELLATION_REASONDESC_ORDER_INVALID = 'Order invalid';

    /**
     * Cancellation reasonkey for CHECK24: order cancelled in shop
     */
    const ORDER_ITEM_CANCELLATION_REASONKEY_ORDER_CANCELLED_IN_SHOP = 'order_cancelled_in_shop';

    /**
     * Cancellation reasondescription for CHECK24: order cancelled in shop
     */
    const ORDER_ITEM_CANCELLATION_REASONDESC_ORDER_CANCELLED_IN_SHOP = 'Order cancelled in Oxid shop';

    /**
     * Convert order data to openTrans-Document Object
     *
     * @return OpentransDocumentDispatchNotification
     * @throws OpentransException
     */
    public function create_document_dispatchnotification()
    {
        $opentrans_dispatchnotification = new OpentransDocumentDispatchNotification();

        $opentrans_dispatchnotification_header = $opentrans_dispatchnotification->create_header();

        $opentrans_dispatchnotification_header->create_controlinfo(
            null, 'CHECK24', date('Y-m-d\TH:i:s')
        );

        //unique ID of this document
        //Date when goods were dispatched
        $opentrans_dispatchnotification_info = $opentrans_dispatchnotification_header->create_dispatchnotificationinfo(
            (string)Registry::getUtilsObject()->generateUID(),
            (string)date('Y-m-d\TH:i:s', strtotime($this->oxorder__oxsenddate->value)), $this->oxorder__oxtransid->value
        );

        if($sShipmentId = $this->oxorder__oxtrackcode->value) {
            $opentrans_dispatchnotification_info->set_shipment_id($sShipmentId);
        }

        if($sTrackingUrl = $this->getShipmentTrackingUrl()) {
            $opentrans_dispatchnotification_info->set_tracking_url($sTrackingUrl);
        }

        $opentrans_dispatchnotification_info->set_shipment_id($this->oxorder__oxtrackcode->value);

        //add carrier name as remark
        $oDelSet = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\DeliverySet::class);
        if($this->oxorder__oxdeltype->value && $oDelSet->load($this->oxorder__oxdeltype->value)) {
            $opentrans_dispatchnotification_info->add_remark('carrier_name', strtolower($oDelSet->oxdeliveryset__oxtitle->value));
        }

        //parties
        $aIdRefs = json_decode(htmlspecialchars_decode($this->oxorder__itrcheck24_idrefs->value), true);

        //delivery
        $sDeliveryPartyId = $aIdRefs[OpentransDocumentParty::ROLE_DELIVERY];
        if (!$sDeliveryPartyId) {
            $sDeliveryPartyId = $this->getDefaultPartyId(OpentransDocumentParty::ROLE_DELIVERY);
        }
        $opentrans_party = new OpentransDocumentParty(
            $sDeliveryPartyId,
            OpentransDocumentPartyid::TYPE_CHECK24,
            OpentransDocumentParty::ROLE_DELIVERY
        );
        $opentrans_address = new OpentransDocumentAddress();
        if ($this->oxorder__oxdelcompany->value) {
            $opentrans_address->set_name($this->oxorder__oxdelcompany->value);
        }
        $opentrans_address->set_name2($this->oxorder__oxdelfname->value);
        $opentrans_address->set_name3($this->oxorder__oxdellname->value);
        $opentrans_address->set_street($this->oxorder__oxdelstreet->value . ' ' . $this->oxorder__oxdelstreetnr->value);
        $opentrans_address->set_zip($this->oxorder__oxdelzip->value);
        $opentrans_address->set_city($this->oxorder__oxdelcity->value);

        $oCountry = oxNew(\OxidEsales\Eshop\Application\Model\Country::class);
        $sCountryId = $this->oxorder__oxdelcountryid->value;
        if (!$sCountryId) {
            $sCountryId = $this->oxorder__oxbillcountryid->value;
        }
        if ($oCountry->load($sCountryId)) {
            $opentrans_address->set_country($oCountry->oxcountry__oxtitle->value);
            $opentrans_address->set_country_coded($oCountry->oxcountry__oxisoalpha2->value);
        }

        if ($this->oxorder__oxdelfon->value) {
            $opentrans_address->set_phone($this->oxorder__oxdelfon->value);
        }
        if ($this->oxorder__oxdelfax->value) {
            $opentrans_address->set_fax($this->oxorder__oxdelfax->value);
        }
        if ($this->oxorder__oxbillemail->value) {
            $opentrans_address->add_email($this->oxorder__oxbillemail->value);
        }

        $opentrans_party->set_address($opentrans_address);

        //add delivery, payment data and carrier name as remarks
        $oDeliverySet = oxNew(\OxidEsales\Eshop\Application\Model\DeliverySet::class);
        if ($oDeliverySet->load($this->oxorder__oxdeltype->value)) {
            $opentrans_party->add_remark('delivery_method', $oDeliverySet->oxdeliveryset__oxtitle->value);
        }
        $opentrans_party->add_remark('payment_date', $this->oxorder__oxpaid->value);
        $opentrans_party->add_remark('shipping_fee', $this->oxorder__oxdelcost->value);
        $opentrans_party->add_remark('additional_costs', '0.00');
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        if ($oPayment->load($this->oxorder__oxpaymenttype->value)) {
            $opentrans_party->add_remark('payment_type', $oPayment->oxpayments__oxdesc->value);
        }
        $opentrans_party->add_remark('version_orderapi', OpentransDocument::VERSION_ORDERAPI);

        $opentrans_dispatchnotification_info->add_party($opentrans_party);


        //supplier
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oUser->load($this->oxorder__oxuserid->value);
        $sSupplierPartyId = $aIdRefs[OpentransDocumentParty::ROLE_SUPPLIER];
        if (!$sSupplierPartyId) {
            $sSupplierPartyId = $this->getDefaultPartyId(OpentransDocumentParty::ROLE_SUPPLIER);
        }
        $opentrans_party = new OpentransDocumentParty(
            $sSupplierPartyId,
            OpentransDocumentPartyid::TYPE_CHECK24,
            OpentransDocumentParty::ROLE_SUPPLIER
        );
        $opentrans_address = new OpentransDocumentAddress();

        $opentrans_address->set_name(Registry::getConfig()->getConfigParam('sOpentransAddressName'));
        $opentrans_address->set_street(Registry::getConfig()->getConfigParam('sOpentransAddressStreet'));
        $opentrans_address->set_zip(Registry::getConfig()->getConfigParam('sOpentransAddressZip'));
        $opentrans_address->set_city(Registry::getConfig()->getConfigParam('sOpentransAddressCity'));
        $opentrans_address->set_country_coded(Registry::getConfig()->getConfigParam('sOpentransAddressCountryCode'));
        $opentrans_party->set_address($opentrans_address);
        $opentrans_dispatchnotification_info->add_party($opentrans_party);

        //items
        $aOrderArticles = $this->getOrderArticles();
        foreach ($aOrderArticles as $oOrderArticle) {
            $opentrans_item = new OpentransDocumentItem();

            $opentrans_item->set_product_id(new OpentransDocumentItemProductid(
                [$oOrderArticle->oxorderarticles__oxartnum->value], null, null, null, null, [], [], $oOrderArticle->oxorderarticles__oxtitle->value
            ));
            $opentrans_item->set_quantity($oOrderArticle->oxorderarticles__oxamount->value);
            $opentrans_item->set_order_unit(self::ORDER_UNIT_PIECE); //todo: get order unit
            $opentrans_item->set_price_line_amount((float)$oOrderArticle->oxorderarticles__oxbrutprice->value); //todo: get price line amount
            $opentrans_dispatchnotification->add_item($opentrans_item, $oOrderArticle->oxorderarticles__itrcheck24_lineitemid->value);
        }

        return $opentrans_dispatchnotification;
    }

    /**
     * Convert order data to openTrans-Document Object
     *
     * @return OpentransDocumentOrderchange
     * @throws OpentransException
     */
    public function create_document_orderchange($sCancellationReasonKey = null, $sCancellationReasonDesc = null, $sAction = null)
    {
        $opentrans_orderchange = new OpentransDocumentOrderchange();

        $opentrans_orderchange_header = $opentrans_orderchange->create_header();

        $opentrans_orderchange_header->create_controlinfo(
            null, 'generated by TestsiegerOrderImport module for Oxid', date('Y-m-d\TH:i:s')
        );

        //unique ID of this document
        //Date when goods were dispatched
        $opentrans_orderchange_info = $opentrans_orderchange_header->create_orderchangeinfo(
            (string)$this->oxorder__oxtransid->value,
            date('Y-m-d\TH:i:s', strtotime($this->oxorder__oxorderdate->value))
        );
        $opentrans_orderchange_info->set_orderchange_date(date('Y-m-d\TH:i:s'));

        if($sAction) {
            $opentrans_orderchange_info->add_remark('action', $sAction);
        }

        //parties
        $aIdRefs = json_decode(htmlspecialchars_decode($this->oxorder__itrcheck24_idrefs->value), true);

        //delivery
        $sDeliveryPartyId = $aIdRefs[OpentransDocumentParty::ROLE_DELIVERY];
        if (!$sDeliveryPartyId) {
            $sDeliveryPartyId = $this->getDefaultPartyId(OpentransDocumentParty::ROLE_DELIVERY);
        }
        $opentrans_party = new OpentransDocumentParty(
            $sDeliveryPartyId,
            OpentransDocumentPartyid::TYPE_CHECK24,
            OpentransDocumentParty::ROLE_DELIVERY
        );
        $opentrans_address = new OpentransDocumentAddress();
        if ($this->oxorder__oxdelcompany->value) {
            $opentrans_address->set_name($this->oxorder__oxdelcompany->value);
        }
        $opentrans_address->set_name2($this->oxorder__oxdelfname->value);
        $opentrans_address->set_name3($this->oxorder__oxdellname->value);
        $opentrans_address->set_street($this->oxorder__oxdelstreet->value . ' ' . $this->oxorder__oxdelstreetnr->value);
        $opentrans_address->set_zip($this->oxorder__oxdelzip->value);
        $opentrans_address->set_city($this->oxorder__oxdelcity->value);

        $oCountry = oxNew(\OxidEsales\Eshop\Application\Model\Country::class);
        $sCountryId = $this->oxorder__oxdelcountryid->value;
        if (!$sCountryId) {
            $sCountryId = $this->oxorder__oxbillcountryid->value;
        }
        if ($oCountry->load($sCountryId)) {
            $opentrans_address->set_country($oCountry->oxcountry__oxtitle->value);
            $opentrans_address->set_country_coded($oCountry->oxcountry__oxisoalpha2->value);
        }

        if ($this->oxorder__oxdelfon->value) {
            $opentrans_address->set_phone($this->oxorder__oxdelfon->value);
        }
        if ($this->oxorder__oxdelfax->value) {
            $opentrans_address->set_fax($this->oxorder__oxdelfax->value);
        }
        if ($this->oxorder__oxbillemail->value) {
            $opentrans_address->add_email($this->oxorder__oxbillemail->value);
        }

        $opentrans_party->set_address($opentrans_address);
        //$opentrans_party->add_remark('remark_type', 'remark');
        $opentrans_orderchange_info->add_party($opentrans_party);

        //invoice issuer
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oUser->load($this->oxorder__oxuserid->value);
        $sInvoiceIssuerPartyId = $aIdRefs[OpentransDocumentParty::ROLE_INVOICE_ISSUER];
        if (!$sInvoiceIssuerPartyId) {
            $sInvoiceIssuerPartyId = $this->getDefaultPartyId(OpentransDocumentParty::ROLE_INVOICE_ISSUER);
        }
        $opentrans_party = new OpentransDocumentParty(
            $sInvoiceIssuerPartyId,
            OpentransDocumentPartyid::TYPE_CHECK24,
            OpentransDocumentParty::ROLE_INVOICE_ISSUER
        );
        $opentrans_address = new OpentransDocumentAddress();
        $opentrans_address->set_name($this->oxorder__oxbillcompany->value);
        $opentrans_address->set_name2($this->oxorder__oxbillfname->value);
        $opentrans_address->set_name3($this->oxorder__oxbilllname->value);
        $opentrans_address->set_street($this->oxorder__oxbillstreet->value . ' ' . $this->oxorder__oxbillstreetnr->value);
        $opentrans_address->set_zip($this->oxorder__oxbillzip->value);
        $opentrans_address->set_city($this->oxorder__oxbillcity->value);

        $oCountry = oxNew(\OxidEsales\Eshop\Application\Model\Country::class);
        if ($oCountry->load($this->oxorder__oxbillcountryid->value)) {
            $opentrans_address->set_country($oCountry->oxcountry__oxtitle->value);
            $opentrans_address->set_country_coded($oCountry->oxcountry__oxisoalpha2->value);
        }

        if ($this->oxorder__oxbillfon->value) {
            $opentrans_address->set_phone($this->oxorder__oxbillfon->value);
        }
        if ($this->oxorder__oxbillfax->value) {
            $opentrans_address->set_fax($this->oxorder__oxbillfax->value);
        }
        if ($this->oxorder__oxbillemail->value) {
            $opentrans_address->add_email($this->oxorder__oxbillemail->value);
        }

        $opentrans_party->set_address($opentrans_address);
        $opentrans_orderchange_info->add_party($opentrans_party);

        //supplier
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oUser->load($this->oxorder__oxuserid->value);
        $sSupplierPartyId = $aIdRefs[OpentransDocumentParty::ROLE_SUPPLIER];
        if (!$sSupplierPartyId) {
            $sSupplierPartyId = $this->getDefaultPartyId(OpentransDocumentParty::ROLE_SUPPLIER);
        }
        $opentrans_party = new OpentransDocumentParty(
            $sSupplierPartyId,
            OpentransDocumentPartyid::TYPE_CHECK24,
            OpentransDocumentParty::ROLE_SUPPLIER
        );
        $opentrans_address = new OpentransDocumentAddress();

        $opentrans_address->set_name(Registry::getConfig()->getConfigParam('sOpentransAddressName'));
        $opentrans_address->set_street(Registry::getConfig()->getConfigParam('sOpentransAddressStreet'));
        $opentrans_address->set_zip(Registry::getConfig()->getConfigParam('sOpentransAddressZip'));
        $opentrans_address->set_city(Registry::getConfig()->getConfigParam('sOpentransAddressCity'));
        $opentrans_address->set_country_coded(Registry::getConfig()->getConfigParam('sOpentransAddressCountryCode'));
        $opentrans_party->set_address($opentrans_address);
        $opentrans_orderchange_info->add_party($opentrans_party);

        //items
        $aOrderArticles = $this->getOrderArticles();
        foreach ($aOrderArticles as $oOrderArticle) {
            $opentrans_item = new OpentransDocumentItem();
            $opentrans_item->set_product_id(new OpentransDocumentItemProductid(
                [$oOrderArticle->oxorderarticles__oxartnum->value], null, null, null, null, [], [], $oOrderArticle->oxorderarticles__oxtitle->value
            ));
            $opentrans_item->set_quantity($oOrderArticle->oxorderarticles__oxamount->value * -1);
            $opentrans_item->set_order_unit(self::ORDER_UNIT_PIECE); //todo: get order unit
            $opentrans_item->set_price_line_amount((float)$oOrderArticle->oxorderarticles__oxbrutprice->value); //todo: get price line amount

            $sReasonKey = $sCancellationReasonKey ?: $oOrderArticle->oxorderarticles__itrcheck24_cancellation_reasonkey->value;
            if (!$sReasonKey) {
                $sReasonKey = self::ORDER_ITEM_CANCELLATION_DEFAULT_REASONKEY;
            }
            $opentrans_item->add_remark('reasonkey', $sReasonKey);
            $sRemarkReasonDesc = $sCancellationReasonDesc ?: $oOrderArticle->oxorderarticles__itrcheck24_cancellation_reasondescription->value;
            if (!$sRemarkReasonDesc) {
                $sRemarkReasonDesc = self::ORDER_ITEM_CANCELLATION_DEFAULT_REASONDESC;
            }
            $opentrans_item->add_remark('reasondescription', $sRemarkReasonDesc);
            $opentrans_orderchange->add_item($opentrans_item, $oOrderArticle->oxorderarticles__itrcheck24_lineitemid->value);
        }

        //summary
        $opentrans_summary = new OpentransDocumentSummaryOrder(
            count($aOrderArticles)
        );
        $opentrans_summary->set_total_amount((float)$this->getTotalOrderSum());
        $opentrans_orderchange->set_summary($opentrans_summary);

        return $opentrans_orderchange;
    }

    /**
     * Creates orderresponse document for CHECK24 if CHECH24 order is processed
     *
     * @return OpentransDocumentOrderResponse
     * @throws OpentransException
     */
    public function create_document_orderresponse()
    {
        $opentrans_orderresponse = new OpentransDocumentOrderResponse();

        $opentrans_orderresponse_header = $opentrans_orderresponse->create_header();

        $opentrans_orderresponse_header->create_controlinfo(
            null, 'My shop generator v2.0', date('Y-m-d\TH:i:s')
        );

        //unique ID of this document
        //Date when goods were dispatched
        $opentrans_orderresponse_info = $opentrans_orderresponse_header->create_orderresponseinfo(
            (string)$this->oxorder__oxtransid->value,
            date('Y-m-d\TH:i:s', strtotime($this->oxorder__oxorderdate->value))
        );

        //TODO: set date of order reception (orderdate)
        $opentrans_orderresponse_info->set_orderresponse_date(date('Y-m-d\TH:i:s'));

        //TODO: set these values here
        //$opentrans_orderresponse_info->set_supplier_orderid('supplier order id');
        //$opentrans_orderresponse_info->set_delivery_startdate(date('Y-m-d\TH:i:s'));
        //$opentrans_orderresponse_info->set_delivery_enddate(date('Y-m-d\TH:i:s'));

        //parties
        $aIdRefs = json_decode(htmlspecialchars_decode($this->oxorder__itrcheck24_idrefs->value), true);

        //delivery
        $sDeliveryPartyId = $aIdRefs[OpentransDocumentParty::ROLE_DELIVERY];
        if (!$sDeliveryPartyId) {
            $sDeliveryPartyId = $this->getDefaultPartyId(OpentransDocumentParty::ROLE_DELIVERY);
        }
        $opentrans_party = new OpentransDocumentParty(
            $sDeliveryPartyId,
            OpentransDocumentPartyid::TYPE_CHECK24,
            OpentransDocumentParty::ROLE_DELIVERY
        );
        $opentrans_address = new OpentransDocumentAddress();
        if ($this->oxorder__oxdelcompany->value) {
            $opentrans_address->set_name($this->oxorder__oxdelcompany->value);
        }
        $opentrans_address->set_name2($this->oxorder__oxdelfname->value);
        $opentrans_address->set_name3($this->oxorder__oxdellname->value);
        $opentrans_address->set_street($this->oxorder__oxdelstreet->value . ' ' . $this->oxorder__oxdelstreetnr->value);
        $opentrans_address->set_zip($this->oxorder__oxdelzip->value);
        $opentrans_address->set_city($this->oxorder__oxdelcity->value);

        $oCountry = oxNew(\OxidEsales\Eshop\Application\Model\Country::class);
        $sCountryId = $this->oxorder__oxdelcountryid->value;
        if (!$sCountryId) {
            $sCountryId = $this->oxorder__oxbillcountryid->value;
        }
        if ($oCountry->load($sCountryId)) {
            $opentrans_address->set_country($oCountry->oxcountry__oxtitle->value);
            $opentrans_address->set_country_coded($oCountry->oxcountry__oxisoalpha2->value);
        }

        if ($this->oxorder__oxdelfon->value) {
            $opentrans_address->set_phone($this->oxorder__oxdelfon->value);
        }
        if ($this->oxorder__oxdelfax->value) {
            $opentrans_address->set_fax($this->oxorder__oxdelfax->value);
        }
        if ($this->oxorder__oxbillemail->value) {
            $opentrans_address->add_email($this->oxorder__oxbillemail->value);
        }

        $opentrans_party->set_address($opentrans_address);
        //$opentrans_party->add_remark('remark_type', 'remark');
        $opentrans_orderresponse_info->add_party($opentrans_party);

        //supplier
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oUser->load($this->oxorder__oxuserid->value);
        $sSupplierPartyId = $aIdRefs[OpentransDocumentParty::ROLE_SUPPLIER];
        if (!$sSupplierPartyId) {
            $sSupplierPartyId = $this->getDefaultPartyId(OpentransDocumentParty::ROLE_SUPPLIER);
        }
        $opentrans_party = new OpentransDocumentParty(
            $sSupplierPartyId,
            OpentransDocumentPartyid::TYPE_CHECK24,
            OpentransDocumentParty::ROLE_SUPPLIER
        );
        $opentrans_address = new OpentransDocumentAddress();

        $opentrans_address->set_name(Registry::getConfig()->getConfigParam('sOpentransAddressName'));
        $opentrans_address->set_street(Registry::getConfig()->getConfigParam('sOpentransAddressStreet'));
        $opentrans_address->set_zip(Registry::getConfig()->getConfigParam('sOpentransAddressZip'));
        $opentrans_address->set_city(Registry::getConfig()->getConfigParam('sOpentransAddressCity'));
        $opentrans_address->set_country_coded(Registry::getConfig()->getConfigParam('sOpentransAddressCountryCode'));
        $opentrans_party->set_address($opentrans_address);
        $opentrans_orderresponse_info->add_party($opentrans_party);

        //items
        $aOrderArticles = $this->getOrderArticles();
        foreach ($aOrderArticles as $oOrderArticle) {
            $opentrans_item = new OpentransDocumentItem();
            $opentrans_item->set_product_id(new OpentransDocumentItemProductid(
                [$oOrderArticle->oxorderarticles__oxartnum->value], null, null, null, null, [], [], $oOrderArticle->oxorderarticles__oxtitle->value
            ));
            $opentrans_item->set_quantity($oOrderArticle->oxorderarticles__oxamount->value);
            $opentrans_item->set_order_unit(self::ORDER_UNIT_PIECE); //todo: get order unit
            $opentrans_item->set_price_line_amount((float)$oOrderArticle->oxorderarticles__oxbrutprice->value); //todo: get price line amount

            $opentrans_orderresponse->add_item($opentrans_item, $oOrderArticle->oxorderarticles__itrcheck24_lineitemid->value);
        }

        //summary
        $opentrans_summary = new OpentransDocumentSummaryOrder(
            count($aOrderArticles)
        );
        $opentrans_summary->set_total_amount((float)$this->getTotalOrderSum());
        $opentrans_orderresponse->set_summary($opentrans_summary);

        return $opentrans_orderresponse;
    }

    /**
     * Returns orderarticle quantities from order
     *
     * @return array
     */
    public function getOrderQuantities()
    {
        $aOrderArticle = $this->getOrderArticles();
        $aRet = [];
        foreach ($aOrderArticle as $oOrderArticle) {
            $aRet[$oOrderArticle->oxorderarticles__oxartnum->value] =
                $oOrderArticle->oxorderarticles__oxamount->value;
        }
        return $aRet;
    }

    /**
     * Temporary function, returns default party id by role name
     *
     * @param $sRole
     * @return mixed
     */
    protected function getDefaultPartyId($sRole)
    {
        return $sRole;
    }

    /**
     * Validates order imported from Check24
     * if imported order is valid, we send orderresponse document to check24,
     * otherwise we cancel the order and send orderchange document.
     * We use the same Oxid validation as in checkout last step (order finalization)
     *
     * @return mixed
     */
    public function validateImportedCheck24Order()
    {
        $oBasket = $this->_getOrderBasket();

        //$oBasket->enableSaveToDataBase(true);
        //$oBasket->calculateBasket();
        $oOrderArticles = $this->getOrderArticles(true);

        try {
            $this->_addArticlesToBasket($oBasket, $oOrderArticles->getArray());
        } catch (\OxidEsales\Eshop\Core\Exception\ArticleInputException $e) {
            echo 'article input exception';
            return false;
        } catch (\OxidEsales\Eshop\Core\Exception\NoArticleException $e) {
            echo 'no article exception';
            return false;
        } catch (\OxidEsales\Eshop\Core\Exception\ArticleException $e) {
            echo 'article exception';
            //TODO: correct
            return true;
            return false;
        }

        $oBasket->calculateBasket(true);
        $oUser = $this->getOrderUser();
        return $this->validateOrder($oBasket, $oUser);
    }
}