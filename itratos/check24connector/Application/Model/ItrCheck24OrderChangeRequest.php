<?php

namespace Itratos\Check24Connector\Application\Model;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry as Registry;

/**
 * Orderchange request manager.
 *
 */
class ItrCheck24OrderChangeRequest extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'ItrCheck24OrderChangeRequest';


    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('itrcheck24_orderchangerequest');
    }


    /**
     * Function to confirm cancellation request from Check24
     * cancelling the order in shop
     *
     * @return void
     */
    public function confirmRequest()
    {
        $sOrderId = $this->itrcheck24_orderchangerequest__orderid->value;
        if (!$sOrderId) {
            Logger::msglog('Failed to confirm older cancellation, orderid is missing');
            return;
        }

        $oShopOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        $oDb = DatabaseProvider::getDb();
        $sOrderOxid = $oDb->getOne("select oxid from oxorder where oxtransid = ?", [$sOrderId]);

        if (!$oShopOrder->load($sOrderOxid)) {
            Logger::msglog('Order not found by oxtransid: '. $sOrderId, 3);
            return;
        }

        $sAction = $oDb->getOne("select `action` from itrcheck24_orderchangerequest where orderid = ? order by oxtimestamp desc limit 1", [$sOrderId]);

        $sResponseAction = $sAction == OpentransDocumentOrderchange::ACTION_TYPE_RETURN_REQUEST ?
            OpentransDocumentOrderchange::ACTION_TYPE_RETURN_CONFIRMATION : OpentransDocumentOrderchange::ACTION_TYPE_CANCELLATION_CONFIRMATION;

        //send orderchange document with "cancellationconfirmation" action
        $oProcess = new \Itratos\Check24Connector\Application\Model\Process('ORDERCHANGE');
        $src = $oShopOrder->create_document_orderchange(null, null, $sResponseAction);

        $sActionStr = $sAction == OpentransDocumentOrderchange::ACTION_TYPE_RETURN_REQUEST ?
            'RETURN-CONFIRMATION' : 'CANCELLATION-CONFIRMATION';

        $sFileName = 'ORDER-' . $oShopOrder->oxorder__oxtransid->value . '-' . $sActionStr . '-ORDERCHANGE.xml';
        $orderChangeWriter = new \Itratos\Check24Connector\Application\Model\OpentransDocumentWriterOrderchange(
            [$src],
            $oProcess->get_xml_outbound_path() . $sFileName,
            $oProcess->get_xml_remote_inbound_path() . $sFileName
        );
        $orderChangeWriter->run();


        DatabaseProvider::getDb()->execute("update itrcheck24_orderchangerequest set response = 1 where orderid ='" . $sOrderId . "'");
        $oShopOrder->cancelOrder();
    }

    /**
     * Function to deny cancellation request from Check24
     *
     * @return void
     * @throws OpentransException
     */
    public function rejectRequest()
    {
        $sOrderId = $this->itrcheck24_orderchangerequest__orderid->value;
        if (!$sOrderId) {
            return;
        }
        $oShopOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        $oDb = DatabaseProvider::getDb();
        $sOrderOxid = $oDb->getOne("select oxid from oxorder where oxtransid = ?", [$sOrderId]);

        if (!$oShopOrder->load($sOrderOxid)) {
            Logger::msglog('Order not found by oxtransid: '. $sOrderId, 3);
            return;
        }

        $sDenyReason = Registry::getConfig()->getRequestParameter('reject_reason');

        DatabaseProvider::getDb()->execute("update itrcheck24_orderchangerequest set response = 2, reason = '" . $sDenyReason . "' where orderid ='" . $sOrderId . "'");

        //send orderchange document with "cancellationreject" action and reject reason
        $oProcess = new \Itratos\Check24Connector\Application\Model\Process('ORDERCHANGE');
        $src = $oShopOrder->create_document_orderchange(
            'denied_by_shop', $sDenyReason, \Itratos\Check24Connector\Application\Model\OpentransDocumentOrderchange::ACTION_TYPE_CANCELLATION_REJECT
        );
        $sFileName = 'ORDER-' . $oShopOrder->oxorder__oxtransid->value . '-REJECT-ORDERCHANGE.xml';
        $orderChangeWriter = new \Itratos\Check24Connector\Application\Model\OpentransDocumentWriterOrderchange(
            [$src],
            $oProcess->get_xml_outbound_path() . $sFileName,
            $oProcess->get_xml_remote_inbound_path() . $sFileName
        );
        $orderChangeWriter->run();
    }


}
