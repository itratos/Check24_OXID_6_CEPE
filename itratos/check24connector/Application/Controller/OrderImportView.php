<?php

namespace Itratos\Check24Connector\Application\Controller;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\DatabaseProvider;
use Itratos\Check24Connector\Application\Model\Logger;
use Itratos\Check24Connector\Application\Model\OpentransDocumentReaderOrder;
use Itratos\Check24Connector\Application\Model\OpentransDocumentHeaderOrderinfo;
use Itratos\Check24Connector\Application\Model\OpentransDocumentIdref;
use Itratos\Check24Connector\Application\Model\OpentransDocumentWriterOrderchange;
use Itratos\Check24Connector\Application\Model\OpentransDocumentWriterOrderresponse;
use Itratos\Check24Connector\Application\Model\Process;
use Itratos\Check24Connector\Application\Model\OpentransDocumentParty;
use Itratos\Check24Connector\Application\Model\OpentransException;
use Itratos\Check24Connector\Application\Model\Config as OpentransConfig;

/**
 * Admin view controller for order and orderchange import from CHECK24
 */
class OrderImportView extends \OxidEsales\Eshop\Application\Controller\FrontendController
{

    /**
     * Default VAT value
     */
    const RS_VAT = 19;

    /**
     * Disable direct access to controller, and avoid smarty kicking in.
     *
     * @return void|null
     */
    public function render()
    {
        die('done');
    }

    /**
     * Frontend controller. Main entry point to initiate order import from CHECK24.
     *
     * @return void
     */
    public function import()
    {
        $config = OpentransConfig::getConfig();

        echo '<pre>starting<br>';
        echo '<a href="javascript:history.back()">zur&uuml;ck / back</a><br>';

        if (1 != $config['testsieger_active']) {
            die('OrderImport inactive. / OrderImport inaktiv');
        }

        if (!$config['testsieger_ftpuser'] || !isset($_REQUEST['key']) || $_REQUEST['key'] != $config['testsieger_ftpuser']) {
            die('Wrong Username. / Falscher Benutzername');
        }

        if (empty($config['testsieger_paymenttype_fallback'])) {
            die('No standard payment type defined. Please choose a payment type in your settings. / Keine Standard-Zahlungsart definiert. Bitte w&auml;hlen Sie eine Zahlungsart bei den Einstellungen aus.');
        }

        $this->import_orders($config);
        die('<span style="color:#090"><b><u>OK! Exit now. </u></b></span>');
    }

    /**
     * Main entry point for non shop-specific processing of xml files
     *
     * @param array $config
     * @return string
     */
    public function import_orders(array $config)
    {
        // Check for concurrency.
        // $this->concurrency_lock_check();
        // $this->concurrency_lock_set();

        // Whatever exception occures after this try,
        // will release concurrency lock.
        try {
            $oProcess = new Process('ORDER');
            $oProcess->get_remote_xmls();
            $new_files = $oProcess->get_order_filenames();

            if (!count($new_files)) {
                Logger::msglog(Process::RS_OPENTRANS_EXIT_NONEWFILES, 2);
                $oProcess->concurrency_lock_release();
                return Process::RS_OPENTRANS_EXIT_NONEWFILES;
            }

            Logger::msglog('Will import following files: ' . implode(', ', $new_files));
            foreach ($new_files as $filename) {
                try {
                    // Delegate to actual import:
                    $this->process_xml_file($filename, OpentransConfig::getConfig());
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
     * Main entry point for non shop-specific processing of xml files
     *
     * @param $filename
     * @param $config
     * @return void
     * @throws OpentransException
     */
    protected function process_xml_file($filename, $config)
    {
        Logger::msglog('processing ' . basename($filename), 1);

        // Create opentrans object from xml

        $opentrans_order_reader = new OpentransDocumentReaderOrder('xml', $filename);

        $opentrans_order = $opentrans_order_reader->get_document_data_order($filename);

        // Check opentrans object creation
        if (!($opentrans_order instanceof \Itratos\Check24Connector\Application\Model\OpentransDocumentOrder)) {
            throw new OpentransException('failed to load OpentransDocumentOrder');
        }

        // frequently used vars from xml structure:
        // $opentrans_order
        $itemlist = $opentrans_order->get_item_list();
        $summary = $opentrans_order->get_summary();
        $header = $opentrans_order->get_header();
        $orderinfo = $header->get_orderinfo();
        $parties = $orderinfo->get_parties();
        $orderinfo_remarks = $orderinfo->get_remarks();
        $orderdatetime = $orderinfo->get_order_date();

        $this->check_ts_orderid_for_conflict($orderinfo->get_order_id(), $filename, $config);

        $sql = [];

        // Items
        $sql['oxorderarticles'] = $this->get_oxorderarticles($itemlist);

        // Customer
        $user = $this->get_user($parties);
        $sql['oxuser'] = $this->get_oxuser($user[0]['billingaddress']);

        $shopid_parent = 0;
        $tmp_val = trim(DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getOne('SHOW COLUMNS FROM `oxshops` LIKE \'oxparentid\''));
        if ('' != $tmp_val) {
            $shopid_parent = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getOne("SELECT oxparentid from oxshops WHERE oxid=" . $this->getViewConfig()->getActiveShopId());
        }
        if ($shopid_parent != 0) {
            $real_shopID = $shopid_parent;
        } else {
            $real_shopID = $this->getViewConfig()->getActiveShopId();
        }

        $sql['oxobject2group'] = [
            ['OXID' => md5(serialize($sql['oxuser']) . rand()), 'OXSHOPID' => $real_shopID, 'OXOBJECTID' => $sql['oxuser'][0]['OXID'], 'OXGROUPSID' => 'oxidnewcustomer'],
            ['OXID' => md5(serialize($sql['oxuser']) . rand()), 'OXSHOPID' => $real_shopID, 'OXOBJECTID' => $sql['oxuser'][0]['OXID'], 'OXGROUPSID' => 'oxidcustomer']
        ];

        // Currency
        $currency = $orderinfo->get_currency();

        // Payment
        $sql['oxuserpayments'] = $this->get_oxuserpayments($orderinfo, $sql['oxuser'][0]['OXID'], $config);

        // Installation fees

        $installation_fees = $this->get_installation_fees($itemlist);

        $params = [
            'sql' => $sql,
            'currency' => $currency,
            'shipping_fee' => $orderinfo_remarks['shipping_fee'],
            'config' => $config,
            'total' => $summary->get_total_amount(),
            'orderinfo_remarks' => $orderinfo_remarks,
            'user' => $user,
            'orderdatetime' => $orderdatetime,
            'ts_orderid' => $orderinfo->get_order_id(),
            'summary' => $summary,
            'installation_fees' => $installation_fees,
        ];

        $sql['oxorder'] = $this->create_order($params);

        $params['sql']['oxorder'] = $sql['oxorder'];

        $order_oxid = $sql['oxorder'][0]['OXID'];
        $params['order_oxid'] = $order_oxid;

        // Inject order-id into orderarticles

        foreach ($sql['oxorderarticles'] as &$position) {
            $position['OXORDERID'] = $order_oxid;
        }
        unset($position); // Get rid of reference

        // SAVE
        try {
            $this->save_sql($sql);
            //$this->set_order_no($order_oxid);
            if ($config['testsieger_reducestock']) {
                $this->reduce_stock($sql['oxorderarticles']);
            }

        } catch (Exception $e) {
            Logger::msglog($e->getMessage(), 3);
            //TODO: we use transaction so we don't need the revert function. Remove later?
            $this->revert_sql_save($sql);
            return;
        }

        //save order idrefs
        $this->saveOrderIdRefs($orderinfo, $order_oxid);

        //send orderresponse
        //TODO: question: if the imported order is not validated (eg no stock), do we save it to Oxid db or not?
        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        if ($oOrder->load($order_oxid)) {
            if ($oOrder->validateImportedCheck24Order()) {
                $oProcess = new Process('ORDERRESPONSE');
                $src = $oOrder->create_document_orderresponse();
                $sFileName = 'ORDER-' . $oOrder->oxorder__oxtransid->value . '-ORDERRESPONSE.xml';
                $orderResponseWriter = new OpentransDocumentWriterOrderresponse(
                    [$src],
                    $oProcess->get_xml_outbound_path() . $sFileName,
                    $oProcess->get_xml_remote_inbound_path() . $sFileName
                );
                $orderResponseWriter->run();
                Logger::msglog('Orderresponse, transid=' . $oOrder->oxorder__oxtransid->value . ' has been sent');
            } else {
                //if imported order is not valid, we send orderchange document
                $oProcess = new Process('ORDERCHANGE');
                $src = $oOrder->create_document_orderchange(
                    \Itratos\Check24Connector\Application\Model\Order::ORDER_ITEM_CANCELLATION_REASONKEY_ORDER_INVALID,
                    \Itratos\Check24Connector\Application\Model\Order::ORDER_ITEM_CANCELLATION_REASONDESC_ORDER_INVALID,
                    \Itratos\Check24Connector\Application\Model\OpentransDocumentOrderchange::ORDERCHANGE_TYPE_ORDER_INVALID
                );
                $sFileName = 'ORDER-' . $oOrder->oxorder__oxtransid->value . '-ORDERCHANGE.xml';
                $orderChangeWriter = new OpentransDocumentWriterOrderchange(
                    [$src],
                    $oProcess->get_xml_outbound_path() . $sFileName,
                    $oProcess->get_xml_remote_inbound_path() . $sFileName
                );
                $orderChangeWriter->run();
                Logger::msglog('Order transid=' . $oOrder->oxorder__oxtransid->value . ' is not valid', 3);
                //TODO: check if we need to cancel order or not
                $oOrder->cancelOrder();
            }
        }
        $this->send_order_confirmation_mail($params);
    }

    /**
     * Reduces article stock by orderarticle amount
     *
     * @param array $oxorderarticles
     * @return void
     */
    protected function reduce_stock(array $oxorderarticles)
    {
        foreach ($oxorderarticles as $oxorderarticle) {
            $sql = 'UPDATE oxarticles
                        SET OXSTOCK = OXSTOCK - ' . (int)$oxorderarticle['OXAMOUNT'] . '
                        WHERE OXID = "' . $oxorderarticle['OXARTID'] . '"';
            DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->Execute($sql);
        }
    }

    /**
     * Collects installation fees from articles, if any
     *
     * @param array $itemlist
     * @returns double $installation_fees
     */
    protected function get_installation_fees($itemlist)
    {
        $installation_fees = 0;
        foreach ($itemlist as $item) {
            $remarks = $item->get_remarks();
            if (isset($remarks['installation'])) {
                $installation_fees += $remarks['installation'];
            }
        }
        return $installation_fees;
    }

    /**
     * Send custom order confirmation mail, if activated in config.
     *
     * @param $params
     * @return void
     */
    protected function send_order_confirmation_mail($params)
    {
        if (1 != $params['config']['testsieger_sendorderconf']) {
            Logger::msglog('EMail confirmation disabled.');
            return;
        }

        $ordernr = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getOne('SELECT OXORDERNR FROM oxorder WHERE OXID = "' . $params['order_oxid'] . '"');
        $oShop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
        $oShop->load(Registry::getConfig()->getShopId());

        $oxEmail = oxNew(\OxidEsales\Eshop\Core\Email::class);
        $oxEmail->setConfig(Registry::getConfig());
        $oxEmail->setFrom($oShop->oxshops__oxowneremail->value, $oShop->oxshops__oxname->getRawValue());

        //$oxEmail->setRecipient($params['sql']['oxuser'][0]['OXUSERNAME']);
        $oxEmail->setRecipient('sasha205140245@gmail.com');

        $smarty = Registry::get("oxUtilsView")->getSmarty();
        $smarty->assign("oEmailView", $oxEmail);
        $smarty->assign("oxorderid", $ordernr);
        $smarty->assign("orderarticles", $params['sql']['oxorderarticles']);
        $smarty->assign("oxorder", $params['sql']['oxorder'][0]);
        $smarty->assign("params", $params);

        $oxOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        $oxOrder->load($params['order_oxid']);
        $smarty->assign("order", $oxOrder);

        $sSubject = $oShop->oxshops__oxordersubject->getRawValue() . '( Nr.' . $ordernr . ')';
        $oxEmail->setSubject($sSubject);
        $sBody = $smarty->fetch("check24connector_confirmation.tpl");
        $oxEmail->setBody($sBody);
        Logger::msglog($oxEmail->send() ? 'Mail sent.' : 'Mail could not be sent.');
    }


    /**
     * Once imported, set order number to first availible no
     *
     * @return false|int|string
     */
    protected function set_order_no()
    {
        $shopID = $this->getConfig()->getShopId();
        $buffer = "";

        if (1 !== $shopID) {
            $buffer = "_" . $shopID;
        }

        $res = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->execute('START TRANSACTION');
        if ($res === false) {
            Logger::msglog("SQL Error: start transaction error");
        }
        $sql = "SELECT MAX(OXCOUNT)+1 FROM oxcounters where oxident = 'oxOrder" . $buffer . "';";

        Logger::msglog("SQL for getting new Ordernumber: " . $sql);

        $sNewOrderNumber = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getOne($sql);
        if ($sNewOrderNumber) {
            Logger::msglog("New Ordernumber is: " . $sNewOrderNumber);
        } else {
            $sNewOrderNumber = 1;
            Logger::msglog("Get new Ordernumber failed, setting to 1");
        }

        $sql = "UPDATE oxcounters
                    SET OXCOUNT = '$sNewOrderNumber'
                    WHERE OXIDENT = 'oxOrder" . $buffer . "'";

        Logger::msglog("SQL for Update Ordernumber: " . $sql);

        $res = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->Execute($sql);
        if ($res === false) {
            Logger::msglog("SQL Error in execution of query: " . $sql);
        }

        $res = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->Execute('COMMIT');
        if ($res === false) {
            Logger::msglog("SQL Error: transaction commit error");
        }

        // load order for later recalculation
        // $oOrder = oxnew('oxorder');
        // $oOrder->load($order_oxid);

        // recalculate now
        Logger::msglog("Order nr before recalculation: " . $sNewOrderNumber . '  ' . $this->getConfig()->getShopId());// $oOrder->oxorder__oxordernr->value);
        //$oOrder->recalculateOrder();
        //Logger::msglog("Order nr after recalculation: " . $oOrder->oxorder__oxordernr->value);

        return $sNewOrderNumber;
    }

    /**
     * Iterate through sql-structure, save all rows in corresponding tables.
     *
     * @param array $sql
     * @throws Exception Unable to save into table
     */
    public function save_sql($sql)
    {
        $db = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        $db->startTransaction();

        try {
            foreach ($sql as $table => $rows) {
                foreach ($rows as $row) {
                    $query = ('oxuser' === $table ? 'REPLACE ' : 'INSERT ') . 'INTO ' . $table . ' (' . join(',', array_keys($row)) . ') VALUES '
                        . '(';
                    for ($i = 0, $max = count($row); $i < $max; $i++) {
                        $query .= '?, ';
                    }

                    $query = rtrim($query, ', ');
                    $query .= ')';

                    if (!$this->getConfig()->isUtf()) {
                        Logger::msglog('Shop is not utf-8. Converting data to ISO 8859-15');
                        foreach ($row as &$value) {
                            $value = iconv("UTF-8", "ISO-8859-15//TRANSLIT", $value);
                        }
                    }

                    $res = $db->Execute($query, array_values($row));
                    if (false === $res) {
                        Logger::msglog('Error saving into table ' . $table . '. Query was ' . $query . ' - ' . $res, 3);
                        throw new Exception('Error saving into table ' . $table . '. Query was ' . $query);
                    }
                }
            }
            $db->commitTransaction();
        } catch (Exception $exception) {
            $db->rollbackTransaction();
            throw $exception;
        }
    }

    /**
     * Delete all rows that were recently inserted.
     * Usefull after insertion failure (transaction-replacement)
     *
     * @param array $sql
     * @return void
     */
    public function revert_sql_save(array $sql)
    {
        $db = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        foreach ($sql as $table => $rows) {
            foreach ($rows as $row) {
                if (isset($row['OXID'])) {
                    $db->Execute("DELETE FROM " . $table . " WHERE OXID = " . $db->quote($row['OXID']));
                    Logger::msglog('Reverted OxId ' . $row['OXID'] . ' from table ' . $table);
                }
            }
        }
    }


    /**
     * Get Payment Info.
     *
     * @param OpentransDocumentHeaderOrderinfo $orderinfo
     * @param $oxuserid
     * @param array $config
     * @return array[]
     * @throws OpentransException
     */
    protected function get_oxuserpayments($orderinfo, $oxuserid, array $config)
    {
        // Check parameter type (no typehint used - that shopsystem can run un REALLY old server.)
        if (!is_a($orderinfo, OpentransDocumentHeaderOrderinfo::class)) {
            throw new OpentransException('$orderinfo must be type OpentransDocumentHeaderOrderinfo');
        }

        // Choose payment type to use
        $remarks = $orderinfo->get_remarks();

        if (isset($remarks['payment_type'])) {
            // New style payment type getter. Handles paypal, requieres custom remark.
            $payment_type = $remarks['payment_type'];
        } else {
            // Old school payment type getter. Fails with ew payment types like paypal.
            $payment_type = $orderinfo->get_payment()->get_type();
        }

        $payment_testsieger = $config['testsieger_paymenttype_ts'];
        $payment_fallback = $config['testsieger_paymenttype_fallback'];

        // Translate opentrans-style payment type into shop-style.
        // Make aliases for most common types.

        switch ($payment_type) {
            case 'cashondelivery':
            case 'cash':
            case 'cod':
                $oxpaymentsid = 'oxidcashondel';
                break;

            case 'cc':
            case 'card':
            case 'creditcard':
            case 'creditcard_testsieger':
                $oxpaymentsid = 'oxidcreditcard';
                break;

            case 'paypal':
                $oxpaymentsid = 'oxidpaypal';
                break;

            case 'testsieger':
                if (empty($payment_testsieger)) {
                    throw new OpentransException('no testsieger.de payment type defined');
                }

                $oxpaymentsid = $payment_testsieger;
                break;

            case 'ueberweisung':
            default:
                $oxpaymentsid = $payment_fallback;
                break;

        }

        $oxuserpayments = [
            'OXID' => md5(rand() . microtime()),
            'OXUSERID' => $oxuserid,
            'OXPAYMENTSID' => $oxpaymentsid
        ];

        return [$oxuserpayments];

    }

    #########################################################################################
    #########
    #########    Helper Functions: Logging, Concurrency locking, arthimetrics, FTP
    #########
    #########################################################################################

    /**
     * Checks if given TS order id has already been enetered into order mapping table.
     * If so, it @throws OpentransException
     *
     * @param $ts_orderid
     * @param $filename
     * @param $config
     * @return void
     */
    protected function check_ts_orderid_for_conflict($ts_orderid, $filename, $config)
    {
        $db = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        $res = $db->Execute('SELECT count(*) AS cnt FROM oxorder WHERE oxtransid = ? AND oxshopid = ?', [$ts_orderid, $this->getConfig()->getShopId()]);

        if ($res->fields['cnt'] > 0) {
            $oProcess = new Process('ORDER');
            Logger::msglog("Order with testsieger-order-id {$ts_orderid} found in mapping table", 3);

            $inbound_file = $filename;
            $archive_file = $oProcess->get_xml_inbound_path() . 'archive/' . basename($filename);

            // Order exists in archive
            if (file_exists($archive_file)) {
                // Same file in inbound folder as in archive folder
                if (md5_file($archive_file) == md5_file($inbound_file)) {
                    // Delete inbound order, move remote order, order has already been imported
                    unlink($inbound_file);
                    $oProcess->archive_xml_filename_remotely(basename($filename));
                    throw new OpentransException('Duplicate order with testsieger-order-id "' . $ts_orderid . '". Order "' . basename($filename) . '" deleted automatically');
                } else {
                    throw new OpentransException('Different orders with the same testsieger-order-id (' . $ts_orderid . ') - please check the order "' . basename($filename) . '" manually');
                }
            } else {
                // Order has been imported but its not in the archive folder, so we move it there
                $oProcess->archive_xml_filename_locally(basename($filename));
                throw new OpentransException('Moved order "' . basename($filename) . '" to the archive.');
            }
        }
    }


    /**
     * Iterate through itemlist and create array of order objects.
     *
     * @param $itemlist
     * @return array
     */
    protected function get_oxorderarticles($itemlist)
    {

        $oxorderarticles = [];

        Logger::msglog("Starte Auswertung der Artikelliste");

        foreach ($itemlist as $key => $item) {

            $remarks = $item->get_remarks();

            $orderposition = [];

            $bruttoprice = $item->get_product_price_fix()->get_price_amount();
            $nettoprice = $bruttoprice / (1 + $item->get_tax_details_fix()->get_tax());
            $amount = $item->get_quantity();

            $orderposition['OXARTNUM'] = $item->get_product_id()->get_supplier_pid();

            $articles_inherited = (boolean)DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getOne("SELECT oxvarvalue FROM oxconfig WHERE oxvarname LIKE 'blMallInherit_oxarticles'");
            if ($articles_inherited != 0) {
                $tmp_val = trim(DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getOne('SHOW COLUMNS FROM `oxshops` LIKE \'oxparentid\''));
                if ('' != $tmp_val) {
                    $art_shopID = (integer)DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getOne("SELECT oxparentid FROM oxshops WHERE OXID=" . $this->getViewConfig()->getActiveShopId());
                } else {
                    $art_shopID = $this->getViewConfig()->getActiveShopId();
                }
            } else {
                $art_shopID = $this->getViewConfig()->getActiveShopId();
            }

            $orderposition['OXARTID'] = (string)DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getOne(
                'SELECT OXID FROM oxarticles WHERE OXARTNUM="' . $orderposition['OXARTNUM'] . '" AND OXSHOPID = "' . $art_shopID . '"'
            );
            $orderposition['OXTITLE'] = $remarks['product_name'];

            if (isset($remarks['installation'])) {
                $orderposition['OXTITLE'] .= ' (zzgl.Installation)';
            }
            $orderposition['OXPRICE'] = $bruttoprice; // b1
            $orderposition['OXBRUTPRICE'] = $bruttoprice * $amount; // b*
            $orderposition['OXVATPRICE'] = ($bruttoprice - $nettoprice) * $amount; // v*
            $orderposition['OXNETPRICE'] = $orderposition['OXBRUTPRICE'] - $orderposition['OXVATPRICE']; // n*
            $orderposition['OXVAT'] = 100 * $item->get_tax_details_fix()->get_tax(); // %
            $orderposition['OXNPRICE'] = $nettoprice * $amount; // n*
            $orderposition['OXBPRICE'] = $bruttoprice; // b*
            $orderposition['OXSUBCLASS'] = 'oxarticle'; // n*
            $orderposition['OXORDERSHOPID'] = $this->getViewConfig()->getActiveShopId(); // n*

            $orderposition['OXAMOUNT'] = $amount;
            $orderposition['itrcheck24_lineitemid'] = $item->get_line_item_id();
            $orderposition['OXID'] = md5(json_encode($orderposition) . rand() . microtime());
            $oxorderarticles[] = $orderposition;
        }

        return $oxorderarticles;
    }

    /**
     * Build an array with all user data to be user for SQL build.
     *
     * @param $parties
     * @return array[]
     * @throws OpentransException
     */
    public function get_user($parties)
    {

        // Type check

        if (!is_array($parties)) {
            throw new OpentransException('$parties must be array');
        }

        $db = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);

        // Rename party keys into their specific function

        foreach ($parties as $key => $party) {

            if (!is_a($party, OpentransDocumentParty::class)) {
                throw new OpentransException('$parties must be type OpentransDocumentParty');
            }

            $parties[$party->get_role()] = $party;
            unset($parties[$key]);

        }

        $user = [];

        // Iterate shipping and billing address

        foreach (
            [OpentransDocumentParty::ROLE_INVOICE_ISSUER => 'billingaddress', OpentransDocumentParty::ROLE_DELIVERY => 'shippingaddress']
                 as $partyname => $addresstype) {

            $current_address = $parties[$partyname]->get_address();

            // Get country code

            $res = $db->Execute('SELECT OXID FROM oxcountry WHERE OXISOALPHA2 = ? ',
                [$current_address->get_country_coded()]
            );
            if (!$res) {
                Logger::msglog('Could not find country for code ' . $current_address->get_country_coded(), 3);
                throw new Exception('Could not find country for code ' . $current_address->get_country_coded());
            }

            $address['countryid'] = $res->fields['OXID'];

            // Get Address

            $address['fname'] = $current_address->get_name2();
            $address['lname'] = $current_address->get_name3();
            $address['street'] = $current_address->get_street();

            // Try to split street no. from street name.
            // Count spaces and hypons to street no, e.g. "street 11 - 12"
            // Also accept a single letter, e.g. "Street 10 - 11b"

            $matches = [];
            if (preg_match('~.*\s([\s\-0-9]+[a-zA-Z]?)$~', $address['street'], $matches)) {
                $address['street'] = trim(substr($matches[0], 0, -1 * strlen($matches[1])));
                $address['streetnr'] = trim(substr($matches[0], -1 * strlen($matches[1])));
            }

            $address['city'] = $current_address->get_city();
            $address['zip'] = $current_address->get_zip();
            $address['company'] = $current_address->get_name();

            // Get first found adress remark
            $address['addinfo'] = '';
            foreach ($current_address->get_address_remarks() as $address_remark) {
                if ($address_remark) {
                    $address['addinfo'] = $address_remark;
                    break;
                }
            }

            // Get first found phone number
            $address['fon'] = ''; // Set below
            foreach ($current_address->get_phone() as $number) {
                if ($number) {
                    $address['fon'] = $number;
                    break;
                }
            }

            // Get first found mail address
            $address['email'] = '';
            foreach ($current_address->get_emails() as $mail) {
                if ($mail) {
                    $address['email'] = $mail;
                    break;
                }
            }

            $user[$addresstype] = $address;

        }
        return [$user];
    }

    /**
     * Iterate throu parties, build customer data,
     * i.e. email, billing address and delivery address.
     *
     * @param array $billaddress
     * @return array[]
     */
    protected function get_oxuser(array $billaddress)
    {
        $oxuser = [];
        $oxuser['OXACTIVE'] = 1;
        $oxuser['OXRIGHTS'] = 'user';
        $oxuser['OXSHOPID'] = $this->getConfig()->getShopId();

        $now = new \DateTime();
        $oxuser['OXCREATE'] = $now->format('Y-m-d H:i:s');

        $oxuser['OXFNAME'] = $billaddress['fname'];
        $oxuser['OXLNAME'] = $billaddress['lname'];
        $oxuser['OXSTREET'] = $billaddress['street'];
        $oxuser['OXSTREETNR'] = $billaddress['streetnr'];
        $oxuser['OXCOUNTRYID'] = $billaddress['countryid'];
        $oxuser['OXADDINFO'] = $billaddress['addinfo'];

        $oxuser['OXCITY'] = $billaddress['city'];
        $oxuser['OXZIP'] = $billaddress['zip'];
        $oxuser['OXFON'] = $billaddress['fon'];
        $oxuser['OXCOMPANY'] = $billaddress['company'];

        $oxuser['OXUSERNAME'] = $billaddress['email'];

        $oxuser['OXID'] = md5($billaddress['email'] . rand());

        if (!$oxuser['OXCOUNTRYID'] || $oxuser['OXCOUNTRYID'] == "DE") {
            $oxuser['OXCOUNTRYID'] = 'a7c40f631fc920687.20179984';
        }
        return [$oxuser];
    }

    /**
     * Takes the results of all procesisng (items, address etc.)
     * and finally creates + saves order.
     *
     * @param array $params Associative Array of all parameters
     * @return array[]
     */
    protected function create_order(array $params)
    {
        extract($params);

        $oxorder = [];
        $oxorder['OXID'] = md5(rand() . microtime());
        $oxorder['OXSHOPID'] = $this->getViewConfig()->getActiveShopId();
        $oxorder['OXUSERID'] = $sql['oxuser'][0]['OXID'];
        $oxorder['OXORDERDATE'] = $orderdatetime;
        $oxorder['OXORDERNR'] = $this->set_order_no();

        // Address handling
        $billfields = [
            'OXBILLCOMPANY',
            'OXBILLEMAIL',
            'OXBILLFNAME',
            'OXBILLLNAME',
            'OXBILLSTREET',
            'OXBILLSTREETNR',
            'OXBILLCITY',
            'OXBILLCOUNTRYID',
            'OXBILLZIP',
            'OXBILLFON',
            'OXBILLADDINFO'
        ];

        foreach ($billfields as $fieldname) {
            $oxorder[$fieldname] = $user[0]['billingaddress'][strtolower(substr($fieldname, strlen('OXBILL')))];
        }
        if (!$oxorder['OXBILLCOUNTRYID'] || $oxorder['OXBILLCOUNTRYID'] == "DE") {
            $oxorder['OXBILLCOUNTRYID'] = 'a7c40f631fc920687.20179984';
        }

        $delfields = [
            'OXDELCOMPANY',
            'OXDELFNAME',
            'OXDELLNAME',
            'OXDELSTREET',
            'OXDELSTREETNR',
            'OXDELCITY',
            'OXDELCOUNTRYID',
            'OXDELZIP',
            'OXDELFON',
            'OXDELADDINFO'
        ];
        //var_dump($user[0]['billingaddress'],$user[0]['shippingaddress']);
        foreach ($delfields as $fieldname) {
            $oxorder[$fieldname] = $user[0]['shippingaddress'][strtolower(substr($fieldname, strlen('OXDEL')))];
        }
        if (!$oxorder['OXDELCOUNTRYID'] || $oxorder['OXDELCOUNTRYID'] == "DE") {
            $oxorder['OXDELCOUNTRYID'] = 'a7c40f631fc920687.20179984';
        }

        $oxorder['OXPAYMENTID'] = $sql['oxuserpayments'][0]['OXID'];
        $oxorder['OXPAYMENTTYPE'] = $sql['oxuserpayments'][0]['OXPAYMENTSID'];

        // Collect article brutto sum
        $brutsum = 0;
        foreach ($sql['oxorderarticles'] as $orderposition) {
            $brutsum += $orderposition['OXBRUTPRICE'];
        }

        $oxorder['OXTOTALBRUTSUM'] = $brutsum;
        $oxorder['OXTOTALNETSUM'] = round($oxorder['OXTOTALBRUTSUM'] * (100 / (100 + self::RS_VAT)), 2);
        $oxorder['OXTOTALORDERSUM'] = $summary->get_total_amount();
        $oxorder['OXARTVAT1'] = self::RS_VAT;
        $oxorder['OXARTVATPRICE1'] = $oxorder['OXTOTALBRUTSUM'] - $oxorder['OXTOTALNETSUM'];

        // Combine shipping and installation fee
        $oxorder['OXDELCOST'] = (isset($orderinfo_remarks['services_1_man'])
                ? (float)$orderinfo_remarks['services_1_man']
                : 0)
            + (isset($orderinfo_remarks['services_2_man'])
                ? (float)$orderinfo_remarks['services_2_man']
                : 0)
            + (float)$shipping_fee;

        $oxorder['OXDELVAT'] = self::RS_VAT;
        $oxorder['OXPAYCOST'] = (isset($orderinfo_remarks['additional_costs'])
            ? (float)$orderinfo_remarks['additional_costs'] : 0);
        $oxorder['OXWRAPCOST'] = $installation_fees;
        $oxorder['OXWRAPVAT'] = $installation_fees * self::RS_VAT * 0.01;
        $oxorder['OXTRANSID'] = $ts_orderid;
        $oxorder['OXCURRENCY'] = $currency;
        $oxorder['OXCURRATE'] = 1;
        $oxorder['OXFOLDER'] = 'ORDERFOLDER_NEW';
        $oxorder['OXTRANSSTATUS'] = 'OK';
        $oxorder['OXDELTYPE'] = $config['testsieger_shippingtype'] ? $config['testsieger_shippingtype'] : $orderinfo_remarks['delivery_method'];
        $oxorder['OXPAID'] = date("Y-m-d H:i:s");
        return [$oxorder];
    }

    /*
    protected function custom_oxdeltype_bigpackage($sql_ororderarticles) {
        // example of custom shipping handling. Will return true if more than 1 item is sold, or the single item is heavier that 5g.
        $this->msglog('Using custom shipping handling.');
        // More than one position?
        if(1 < count($sql_ororderarticles)) {return 1;}
        $db = DatabaseProvider::getDb( DatabaseProvider::FETCH_MODE_ASSOC ) ;
        foreach ($sql_ororderarticles AS $orderposition) {
            // More than one item in that position
            if (1 < $orderposition['OXAMOUNT']) {return 2;}
            // Heavy position?
            $res = $db->Execute('SELECT OXWEIGHT FROM oxarticles WHERE OXARTNUM = "' . mysql_real_escape_string($orderposition['OXARTNUM']) . '"');
            if (!$res) {return 3;}
            if ($res->fields['OXWEIGHT'] > 0.005) {return 4;}
        }
        return false;
    }
    /**/

    /**
     * 2015-01-08, Michael Gerhardt: function to test import
     */
    public function testimport()
    {
        $aConfig = [
            "testsieger_paymenttype_fallback" => "tsinv",
            "testsieger_shippingtype" => "209e2257a0175dcabcdbec468a624668"
        ];
        $sTestFile = getShopBasePath() . "tmp/2015-04-06-09-28-06_TS-2015-538651-1-8337-ORDER.xml";
        $sTestFileALT = getShopBasePath() . "tmp/2015-04-06-09-28-06_TS-2015-538651-1-8337-ORDER.XML";

        $oProcess = new Process('ORDER');
        if (file_exists($sTestFile) || file_exists($sTestFileALT)) {
            copy($sTestFile, $oProcess->get_xml_inbound_path() . basename($sTestFile));
            $this->process_xml_file($oProcess->get_xml_inbound_path() . basename($sTestFile), $aConfig);
        }
    }

    /**
     * Saves order idrefs data (BUYER_IDREF and SUPPLIER_IDREF)
     *
     * @param $orderinfo
     * @param $sOxid
     * @return void
     */
    protected function saveOrderIdRefs($orderinfo, $sOxid)
    {
        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        if ($oOrder->load($sOxid)) {
            $aIdRefs = [];
            if ($sSupplierIdref = $orderinfo->get_idref(OpentransDocumentIdref::TYPE_SUPPLIER_IDREF)) {
                $aIdRefs[OpentransDocumentParty::ROLE_SUPPLIER] = $sSupplierIdref;
            }
            if ($sDeliveryIdref = $orderinfo->get_idref(OpentransDocumentIdref::TYPE_DELIVERY_IDREF)) {
                $aIdRefs[OpentransDocumentParty::ROLE_DELIVERY] = $sDeliveryIdref;
            }
            if ($sInvoiceIssuerIdref = $orderinfo->get_idref(OpentransDocumentIdref::TYPE_INVOICE_ISSUER_IDREF)) {
                $aIdRefs[OpentransDocumentParty::ROLE_INVOICE_ISSUER] = $sInvoiceIssuerIdref;
            }
            $oOrder->oxorder__itrcheck24_idrefs = new Field(json_encode($aIdRefs));
            $oOrder->save();
        }
    }
}
