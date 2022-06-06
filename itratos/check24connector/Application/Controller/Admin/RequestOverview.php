<?php

namespace Itratos\Check24Connector\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;

class RequestOverview extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{

    protected $_oOrder = null;


    public function render()
    {
        parent::render();

        if ($oOrder = $this->getCheck24Order()) {

            $this->_aViewData["edit"] = $oOrder;
        }

        return "check24connector_requestoverview.tpl";
    }

    /**
     * Returns editable order object
     *
     * @return oxorder
     */
    public function getCheck24Order()
    {
        $soxId = $this->getEditObjectId();

        if ($this->_oOrder === null && isset($soxId) && $soxId != "-1") {
            $oRequest = oxNew(\Itratos\Check24Connector\Application\Model\ItrCheck24OrderChangeRequest::class);
            $oRequest->load($soxId);

            $oShopOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            $oDb = DatabaseProvider::getDb();
            $sOrderOxid = $oDb->getOne(
                "select oxid from oxorder where oxtransid = ?",
                [$oRequest->itrcheck24_orderchangerequest__orderid->value]);

            if($oShopOrder->load($sOrderOxid)) {
                $this->_oOrder = $oShopOrder;
            }
        }

        return $this->_oOrder;
    }

}