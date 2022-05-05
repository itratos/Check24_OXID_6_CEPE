<?php

/**
 * @copyright (C) 2013 Testsieger Portal AG
 *
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

namespace TestSieger\Check24Connector\Application\Controller;

use OxidEsales\Eshop\Core\Registry as Registry;
use OxidEsales\Eshop\Core\DatabaseProvider;
use TestSieger\Check24Connector\Application\Model\Config as OpentransConfig;
use TestSieger\Check24Connector\Application\Model\OpentransException;

/**
 * Class for order import from CHECK24
 */
class OrderImport extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{

    /**
     * Executes parent method parent::render() and gets data to be displayed in template
     *
     * @return string Filename of template to be displayed.
     */
    public function render()
    {

        parent::render();

        $oConf = $this->getConfig();
        $shopId = $oConf->getShopId();
        foreach ($this->get_fields_to_save() as $fieldname) {
            $this->_aViewData[$fieldname] = $oConf->getShopConfVar($fieldname, NULL, 'testsieger_orderimport');
        }

        if (!$this->_aViewData['testsieger_ftphost']) {
            $this->_aViewData['testsieger_ftphost'] = 'partnerftp.testsieger.de';
        }

        if (!$this->_aViewData['testsieger_ftpport']) {
            $this->_aViewData['testsieger_ftpport'] = '44021';
        }

        $this->_aViewData['ts_logo'] = $oConf->getImageUrl(true) . 'testsieger-200x59.png';

        $this->_aViewData['ts_logs'] = @file_get_contents(getShopBasePath() . OpentransConfig::getLogPath());

        $this->_aViewData['iframeurl'] = Registry::get("oxUtilsUrl")->appendUrl(
            $oConf->getShopHomeUrl(NULL, false),
            [
                'shp' => $shopId,
                'cl' => 'testsieger_orderimportview',
                'fnc' => 'import',
                'key' => $this->_aViewData['testsieger_ftpuser'],
            ]
        );

        $this->_aViewData['iframeurl_changeorder'] = Registry::get("oxUtilsUrl")->appendUrl(
            $oConf->getShopHomeUrl(NULL, false),
            [
                'shp' => $shopId,
                'cl' => 'testsieger_orderchangeimportview',
                'fnc' => 'import',
                'key' => $this->_aViewData['testsieger_ftpuser']
            ]
        );

        $aPaymentList = DatabaseProvider::getDb()->getAll('SELECT oxid, oxdesc FROM oxpayments  ORDER BY oxsort');

        $this->_aViewData['paymentlist'] = $aPaymentList;

        return 'testsieger_orderimport.tpl';
    }

    /**
     * Custom save function for module configuration
     *
     * @return void
     */
    public function savesettings()
    {
        $oConf = $this->getConfig();
        $params = Registry::getConfig()->getRequestParameter('editval');

        if (!isset($params['testsieger_shippingtype'])) {
            $params['testsieger_shippingtype'] = '';
        }

        foreach ($this->get_fields_to_save() as $fieldname) {
            if (isset($params[$fieldname])) {
                $oConf->saveShopConfVar('string', $fieldname, $params[$fieldname], NULL, 'testsieger_orderimport');
            }
        }
    }

    /**
     * Get array of declared config fields
     *
     * @return string[] array of fields
     */
    protected function get_fields_to_save()
    {
        return [
            'testsieger_active',
            'testsieger_ftpuser',
            'testsieger_ftppass',
            'testsieger_ftphost',
            'testsieger_ftpport',
            'testsieger_shippingtype',
            'testsieger_sendorderconf',
            'testsieger_reducestock',
            'testsieger_paymenttype_fallback',
            'testsieger_paymenttype_ts'
        ];
    }

    /**
     * Called from the admin mask, will delete (rotate) logfile
     *
     * @return void
     */
    public function deletelog()
    {
        if (file_exists(getShopBasePath() . OpentransConfig::getLogPath())) {
            unlink(getShopBasePath() . OpentransConfig::getLogPath());
        }
    }

    /**
     * Template getter for admin view: list of imported cancellation requests
     * from Check24 awaiting shop's approval or reject
     *
     * @return mixed
     */
    public function getPendingCancellationRequests()
    {
        $oPendingCancellationRequestList = Registry::get(\TestSieger\Check24Connector\Application\Model\ItrCheck24OrderChangeRequestList::class);
        $sPendingCancellationRequestQuery =
            "SELECT r.orderid, o.oxordernr, r.action, r.oxtimestamp FROM itrcheck24_orderchangerequest r
                   LEFT JOIN oxorder o ON o.oxtransid = r.orderid
                   WHERE r.action IN ('request', 'cancellationrequest') AND r.response = 0 ORDER BY r.oxtimestamp DESC";

        $oPendingCancellationRequestList->selectString($sPendingCancellationRequestQuery);
        return $oPendingCancellationRequestList->getArray();
    }

    /**
     * Function to confirm cancellation request from Check24
     * cancelling the order in shop
     *
     * @return void
     */
    public function confirmCancellation()
    {
        $sOrderId = Registry::getConfig()->getRequestParameter('cancellationid');
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

        //send orderchange document with "cancellationconfirmation" action
        $oProcess = new \TestSieger\Check24Connector\Application\Model\Process('ORDERCHANGE');
        $src = $oShopOrder->create_document_orderchange(
            null, null, \TestSieger\Check24Connector\Application\Model\OpentransDocumentOrderchange::ACTION_TYPE_CANCELLATION_CONFIRMATION
        );
        $sFileName = 'ORDER-' . $oShopOrder->oxorder__oxtransid->value . '-CANCELLATION-CONFIRM-ORDERCHANGE.xml';
        $orderChangeWriter = new \TestSieger\Check24Connector\Application\Model\OpentransDocumentWriterOrderchange(
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
    public function denyCancellation()
    {
        $sOrderId = Registry::getConfig()->getRequestParameter('cancellationid');
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

        $aDenyReasons = Registry::getConfig()->getRequestParameter('deny_reason');
        $sDenyReason = $aDenyReasons[$sOrderId] ?: '';
        DatabaseProvider::getDb()->execute("update itrcheck24_orderchangerequest set response = 2, reason = '" . $sDenyReason . "' where orderid ='" . $sOrderId . "'");

        //send orderchange document with "cancellationreject" action and reject reason
        $oProcess = new \TestSieger\Check24Connector\Application\Model\Process('ORDERCHANGE');
        $src = $oShopOrder->create_document_orderchange(
            'denied_by_shop', $sDenyReason, \TestSieger\Check24Connector\Application\Model\OpentransDocumentOrderchange::ACTION_TYPE_CANCELLATION_REJECT
        );
        $sFileName = 'ORDER-' . $oShopOrder->oxorder__oxtransid->value . '-REJECT-ORDERCHANGE.xml';
        $orderChangeWriter = new \TestSieger\Check24Connector\Application\Model\OpentransDocumentWriterOrderchange(
            [$src],
            $oProcess->get_xml_outbound_path() . $sFileName,
            $oProcess->get_xml_remote_inbound_path() . $sFileName
        );
        $orderChangeWriter->run();
    }
}
