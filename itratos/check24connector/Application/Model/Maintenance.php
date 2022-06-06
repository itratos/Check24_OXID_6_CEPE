<?php

namespace Itratos\Check24Connector\Application\Model;

use Itratos\Check24Connector\Application\Model\Config as OpentransConfig;
use OxidEsales\Eshop\Core\DatabaseProvider as DatabaseProvider;
use OxidEsales\Eshop\Core\{Field, Registry};

/**
 * Class for main import and cron functions
 */
class Maintenance extends Maintenance_parent
{

    /**
     * Cron script to process Oxid events and CHECK24 outbound documents
     *
     * @return void
     */
    public function processAllCheck24Events()
    {
        $this->processShippedOrders();
        $this->processCancelledOrders();
        $this->processOutboundDocuments();
    }

    /**
     * Sends Dispatchnotification document to CHECK24 for Oxid sent orders
     *
     * @return void
     * @throws OpentransException
     */
    protected function processShippedOrders()
    {
        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);

        $aShipOrders = $oDb->getAll(
            "select oxid from oxorder where oxsenddate != '0000-00-00 00:00:00' and itrcheck24_dispatchnotification_sent = 0"
        );

        $oProcess = new Process('DISPATCHNOTIFICATION');
        foreach ($aShipOrders as $row) {
            $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            if ($oOrder->load($row['oxid'])) {
                $src = $oOrder->create_document_dispatchnotification();
                $sFileName = 'ORDER-' . $oOrder->oxorder__oxtransid->value . '-DISPATCHNOTIFICATION.xml';
                $dispatchnotificationWriter = new OpentransDocumentWriterDispatchNotification (
                    [$src],
                    $oProcess->get_xml_outbound_path() . $sFileName,
                    $oProcess->get_xml_remote_inbound_path() . $sFileName
                );
                $dispatchnotificationWriter->run();

                //save flag about dispatchnotification sent
                $oOrder->oxorder__itrcheck24_dispatchnotification_sent = new Field(1);
                $oOrder->save();
            }
        }
    }

    /**
     * Sends Orderchange document to CHECK24 for Oxid cancelled orders (oxstorno = 1)
     *
     * @param $sOrderNr
     * @return void
     * @throws OpentransException
     */
    public function processCancelledOrders($sOrderNr = null)
    {
        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);

        if ($sOrderNr) {
            $sOrderNrCond = " and oxordernr = " . $sOrderNr;
        }
        $aCancelledOrders = $oDb->getAll(
            "select oxid from oxorder where oxstorno = 1 and itrcheck24_processed = 0" . $sOrderNrCond
        );

        $oProcess = new Process('ORDERCHANGE');
        foreach ($aCancelledOrders as $row) {
            $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            if ($oOrder->load($row['oxid'])) {
                $sAction = $oOrder->oxorder__itrcheck24_dispatchnotification_sent->value ?
                    OpentransDocumentOrderchange::ACTION_TYPE_RETURN_REQUEST :
                    OpentransDocumentOrderchange::ACTION_TYPE_CANCELLATION_REQUEST;
                $src = $oOrder->create_document_orderchange(
                    Order::ORDER_ITEM_CANCELLATION_REASONKEY_ORDER_CANCELLED_IN_SHOP,
                    Order::ORDER_ITEM_CANCELLATION_REASONDESC_ORDER_CANCELLED_IN_SHOP,
                    $sAction
                );
                $sFileName = 'ORDER-' . $oOrder->oxorder__oxtransid->value . '-CANCELLED-IN_SHOP-ORDERCHANGE.xml';
                $orderChangeWriter = new OpentransDocumentWriterOrderchange(
                    [$src],
                    $oProcess->get_xml_outbound_path() . $sFileName,
                    $oProcess->get_xml_remote_inbound_path() . $sFileName
                );
                $orderChangeWriter->run();

                //save flag about dispatchnotification sent
                $oOrder->oxorder__itrcheck24_processed = new Field(1);
                $oOrder->save();
            }
        }
    }

    /**
     * Processes CHECK24 outbound documents
     *
     * @return string
     */
    public function processOutboundDocuments()
    {
        // Check for concurrency.
        // $this->concurrency_lock_check();
        // $this->concurrency_lock_set();
        try {
            $oProcess = new Process('ORDERCHANGE');
            $oProcess->get_remote_xmls();
            $new_files = $oProcess->get_order_filenames();

            if (!count($new_files)) {
                Logger::msglog(Process::RS_OPENTRANS_EXIT_NONEWFILES, 2);
                $oProcess->concurrency_lock_release();
                return Process::RS_OPENTRANS_EXIT_NONEWFILES;
            }

            Logger::msglog('Process orderchange following files: ' . implode(', ', $new_files));
            foreach ($new_files as $filename) {
                try {
                    // Delegate to actual import:
                    $this->processOrderChangeXml($filename, OpentransConfig::getConfig());
                    $oProcess->archive_xml_filename($filename);
                } catch (OpentransException $e) {
                    Logger::msglog($e->getMessage(), 3);
                } catch (Exception $e) {
                    Logger::msglog('Exception in file ' . $e->getFile() . '@' . $e->getLine() . ': ' . PHP_EOL . $e->getMessage(), 3);
                    var_dump($e->getTraceAsString());
                }
            }
        } catch (Exception $e) {
            Logger::msglog($e, 3);
            $oProcess->concurrency_lock_release();
            Logger::msglog(Process::RS_OPENTRANS_EXIT_ERROR, 3);
            return Process::RS_OPENTRANS_EXIT_ERROR;
        }
        $oProcess->concurrency_lock_release();
        Logger::msglog(Process::RS_OPENTRANS_EXIT_OK, 2);
        return Process::RS_OPENTRANS_EXIT_OK;
    }

    /**
     * Processes Orderchange document from CHECK24
     *
     * @param $filename
     * @param $config
     * @return void
     * @throws OpentransException
     */
    protected function processOrderChangeXml($filename, $config)
    {
        Logger::msglog('processing ' . basename($filename), 1);

        // Create opentrans object from xml
        $opentrans_orderchange_reader = new OpentransDocumentReaderOrderchange('xml', $filename);

        $opentrans_orderchange = $opentrans_orderchange_reader->get_document_data_orderchange($filename);

        // Check opentrans object creation
        if (!($opentrans_orderchange instanceof OpentransDocumentOrderchange)) {
            throw new OpentransException('failed to load OpentransDocumentOrderchange');
        }

        // frequently used vars from xml structure:
        // $opentrans_order
        $itemlist = $opentrans_orderchange->get_item_list();
        $summary = $opentrans_orderchange->get_summary();
        $header = $opentrans_orderchange->get_header();

        $orderinfo = $header->get_orderchangeinfo();
        $orderId = $orderinfo->get_document_id();

        $oShopOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);

        $oDb = DatabaseProvider::getDb();

        $sOrderOxid = $oDb->getOne("select oxid from oxorder where oxtransid = ?", [$orderId]);

        if (!$oShopOrder->load($sOrderOxid)) {
            throw new OpentransException('Order id ' . $orderId . ' in orderchange document is not found in shop');
        }

        //quantity validation
        $aShopOrderQuantities = $oShopOrder->getOrderQuantities();

        foreach ($itemlist as $item) {
            $sOrderArtNum = $item->get_product_id()->get_supplier_pid();
            if ($item->get_quantity() != $aShopOrderQuantities[$sOrderArtNum] * -1) {
                throw new OpentransException('Order item (' . $sOrderArtNum . ') quantity is incorrect for orderchange document');
            }
        }

        //process action
        $sAction = null;
        $aRemarks = $orderinfo->get_remarks();
        if ($aRemarks) {
            foreach ($aRemarks as $type => $value) {
                if($type == 'action') {
                    $sAction = $value;
                    break;
                }
            }
        }

        if(in_array($sAction,
            [
                OpentransDocumentOrderchange::ACTION_TYPE_CANCELLATION_CONFIRMATION,
                OpentransDocumentOrderchange::ACTION_TYPE_RETURN_CONFIRMATION
            ]
        )) {
            //cancel or return the order right now
            //TODO: find out if we need to do anything special on order return or just cancel the order
            $oShopOrder->cancelOrder();
        }
        else {
            //save orderchange entry
            $oItrCheck24OrderChangeRequest = oxNew(\Itratos\Check24Connector\Application\Model\ItrCheck24OrderChangeRequest::class);
            $oItrCheck24OrderChangeRequest->itrcheck24_orderchangerequest__orderid = new Field($orderId);
            $oItrCheck24OrderChangeRequest->itrcheck24_orderchangerequest__action = new Field($sAction);
            $oItrCheck24OrderChangeRequest->itrcheck24_orderchangerequest__sequenceid = new Field(
                $orderinfo->get_orderchange_sequence_id()
            );
            $oItrCheck24OrderChangeRequest->save();
        }
    }
}