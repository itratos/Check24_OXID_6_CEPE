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
class testsieger_opentrans_orderimport extends oxUBase {

    const RS_OPENTRANS_EXIT_NONEWFILES = 'Keine neuen Bestelldateien.';
    const RS_OPENTRANS_EXIT_OK = 'Erfolgreich abgeschlossen.';
    const RS_OPENTRANS_EXIT_ERROR = 'Fehler.';
    const RS_VAT = 19;
    const LOCK_TIME = 600;


    /**
     * Disable direct access to controller, and avoid smarty kicking in.
     */
    public function render() {
        die('done');
    }

    /**
     * FRONTEND CONTROLLER: MAIN ENTRY POINT TO INITIATE IMPORT.
     */
    public function import() {


        // require_once($this->getViewConfig()->getModulePath('ts_opentrans_orderimport') . '/opentrans/opentrans.php');
        //require_once($this->getViewConfig()->getBaseDir() . 'modules/ts_opentrans_orderimport/opentrans/opentrans.php');

        require_once(  getShopBasePath().'modules/ts_opentrans_orderimport/opentrans/opentrans.php');

        $oConf = $this->getConfig();

        $config = array('testsieger_ftpuser' => $oConf->getShopConfVar('testsieger_ftpuser', NULL, 'testsieger_orderimport'),
            'testsieger_ftppass' => $oConf->getShopConfVar('testsieger_ftppass', NULL, 'testsieger_orderimport'),
            'testsieger_ftphost' => $oConf->getShopConfVar('testsieger_ftphost', NULL, 'testsieger_orderimport'),
            'testsieger_ftpport' => $oConf->getShopConfVar('testsieger_ftpport', NULL, 'testsieger_orderimport'),
            'testsieger_active'  => $oConf->getShopConfVar('testsieger_active', NULL, 'testsieger_orderimport'),
            'testsieger_shippingtype'  => $oConf->getShopConfVar('testsieger_shippingtype', NULL, 'testsieger_orderimport'),
            'testsieger_sendorderconf'  => $oConf->getShopConfVar('testsieger_sendorderconf', NULL, 'testsieger_orderimport'),
            'testsieger_reducestock'  => $oConf->getShopConfVar('testsieger_reducestock', NULL, 'testsieger_orderimport'),
            'testsieger_paymenttype_fallback'  => $oConf->getShopConfVar('testsieger_paymenttype_fallback', NULL, 'testsieger_orderimport'),
            'testsieger_paymenttype_ts'  => $oConf->getShopConfVar('testsieger_paymenttype_ts', NULL, 'testsieger_orderimport'),
        );

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
     * MAIN ENTRY POINT FOR NON SHOP-SPECIFIC PROCESSING OF XML-FILES
     */
    public function import_orders(array $config) {
        // Check for concurrency.
        // $this->concurrency_lock_check();
        // $this->concurrency_lock_set();

        // Whatever exception occures after this try,
        // will release concurrency lock.
        try {

            $this->get_remote_xmls();

            // Check for new files
            $new_files = $this->get_order_filenames();

            if (!count($new_files)) {

                $this->msglog(self::RS_OPENTRANS_EXIT_ERROR
                    . 'Could not get list of files to import. This can be an error, '
                    . 'but could also mean that they are simply no new orders and your server misinterprets that as an error.'
                    , 1);
                $this->concurrency_lock_release();
                return self::RS_OPENTRANS_EXIT_ERROR;

            }

            if (!count($new_files)) {

                $this->msglog(self::RS_OPENTRANS_EXIT_NONEWFILES, 2);
                $this->concurrency_lock_release();
                return self::RS_OPENTRANS_EXIT_NONEWFILES;

            }

            $this->msglog('Will import following files: ' . implode(', ', $new_files));

            // Iterate through new files
            foreach ($new_files AS $filename) {

                try {

                    // Delegate to actual import:

                    $this->process_xml_file($filename, $config);
                    $this->archive_xml_filename($filename);

                } catch (rs_opentrans_exception $e) {
                    $this->msglog($e->getMessage(), 3);
                } catch (Exception $e) {
                    $this->msglog('Exception in file ' . $e->getFile() . '@' . $e->getLine() . ': ' . PHP_EOL . $e->getMessage(), 3);
                    var_dump($e->getTraceAsString());
                }

            }

        } catch (Exception $e) {

            $this->msglog($e, 3);
            $this->concurrency_lock_release();
            $this->msglog(self::RS_OPENTRANS_EXIT_ERROR, 3);
            return self::RS_OPENTRANS_EXIT_ERROR;

        }

        $this->concurrency_lock_release();
        $this->msglog(self::RS_OPENTRANS_EXIT_OK, 2);
        return self::RS_OPENTRANS_EXIT_OK;

    }

    /**
     * MAIN ENTRY POINT FOR SHOP-SPECIFIC PROCESSING OF XML-FILES
     *
     * @param string $filename
     */
    protected function process_xml_file($filename, $config) {

        $this->msglog('processing ' . basename($filename), 1);

        // Create opentrans object from xml

        $opentrans_order_reader = new rs_opentrans_document_reader_order_standard_2_1('xml', $filename);

        $opentrans_order = $opentrans_order_reader->get_document_data_order($filename);

        // Check opentrans object creation
        if (!($opentrans_order instanceof rs_opentrans_document_order)) {
            throw new rs_opentrans_exception('failed to load rs_opentrans_document_order');
        }

        // frequently used vars from xml structure:
        // $opentrans_order
        $itemlist = $opentrans_order->get_item_list();
        $summary = $opentrans_order->get_summary();
        $header = $opentrans_order->get_header();
        $sourcinginfo = $header->get_sourcinginfo();
        $controlinfo = $header->get_controlinfo();
        $orderinfo = $header->get_orderinfo();
        $parties = $orderinfo->get_parties();
        $orderinfo_remarks = $orderinfo->get_remarks();
        $orderdatetime = $orderinfo->get_order_date();

        $this->check_ts_orderid_for_conflict($orderinfo->get_order_id(), $filename, $config);

        $sql = array();

        // Items
        $sql['oxorderarticles'] = $this->get_oxorderarticles($itemlist);

        // Customer
        $user = $this->get_user($parties);
        $sql['oxuser'] = $this->get_oxuser($user[0]['billingaddress']);

        $shopid_parent = 0;
        $tmp_val = trim(oxDb::getDb( oxDB::FETCH_MODE_ASSOC )->getOne('SHOW COLUMNS FROM `oxshops` LIKE \'oxparentid\''));
        if ('' != $tmp_val) {
            $shopid_parent = oxDb::getDb( oxDB::FETCH_MODE_ASSOC )->getOne("SELECT oxparentid from oxshops WHERE oxid=" . $this->getViewConfig()->getActiveShopId());
        }
        if ($shopid_parent != 0) {
            $real_shopID = $shopid_parent;
        } else {
            $real_shopID = $this->getViewConfig()->getActiveShopId();
        }

        $sql['oxobject2group'] = array(
            array('OXID' => md5(serialize($sql['oxuser']) . rand()), 'OXSHOPID' => $real_shopID, 'OXOBJECTID' => $sql['oxuser'][0]['OXID'], 'OXGROUPSID' => 'oxidnewcustomer'),
            array('OXID' => md5(serialize($sql['oxuser']) . rand()), 'OXSHOPID' => $real_shopID, 'OXOBJECTID' => $sql['oxuser'][0]['OXID'], 'OXGROUPSID' => 'oxidcustomer')
        );

        // Currency
        $currency = $orderinfo->get_currency();

        // Payment
        $sql['oxuserpayments'] = $this->get_oxuserpayments($orderinfo, $sql['oxuser'][0]['OXID'], $config);

        // Installation fees

        $installation_fees = $this->get_installation_fees($itemlist);

        $params = array('sql' => $sql,
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
        );

        $sql['oxorder'] = $this->create_order($params);

        $params['sql']['oxorder'] = $sql['oxorder'];

        $order_oxid = $sql['oxorder'][0]['OXID'];
        $params['order_oxid'] = $order_oxid;

        // Inject order-id into orderarticles

        foreach ($sql['oxorderarticles'] AS &$position ) {
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

        } catch(Exception $e) {
            $this->msglog($e->getMessage(), 3);
            $this->revert_sql_save($sql);
            return;
        }

        $this->send_order_confirmation_mail($params);

    }

    protected function reduce_stock(array $oxorderarticles) {

        foreach ($oxorderarticles AS $oxorderarticle) {

            $sql = 'UPDATE oxarticles
                        SET OXSTOCK = OXSTOCK - ' . (int)$oxorderarticle['OXAMOUNT'] . '
                        WHERE OXID = "' . $oxorderarticle['OXARTID'] . '"';

            oxDb::getDb( oxDB::FETCH_MODE_ASSOC ) ->Execute($sql);

        }

    }

    /**
     * Collects installation fees from articles, if any
     *
     * @param array $itemlist
     * @returns double $installation_fees
     */

    protected function get_installation_fees($itemlist) {

        $installation_fees = 0;

        foreach ($itemlist AS $item) {
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
     * @param array $params
     */
    protected function send_order_confirmation_mail($params) {

        if (1 != $params['config']['testsieger_sendorderconf']) {
            $this->msglog('EMail confirmation disabled.');
            return;
        }

        $ordernr = oxDb::getDb( oxDB::FETCH_MODE_ASSOC ) ->getOne('SELECT OXORDERNR FROM oxorder WHERE OXID = "' . $params['order_oxid'] . '"');
        $oShop = oxNew( "oxshop" );
        $oShop->load(oxRegistry::getConfig()->getShopId());

        $oxEmail = oxNew('oxemail');
        $oxEmail->setConfig(oxRegistry::getConfig());
        $oxEmail->setFrom( $oShop->oxshops__oxowneremail->value, $oShop->oxshops__oxname->getRawValue() );
        $oxEmail->setRecipient($params['sql']['oxuser'][0]['OXUSERNAME']);

        $smarty = oxRegistry::get( "oxUtilsView" )->getSmarty();
        $smarty->assign( "oEmailView", $oxEmail );
        $smarty->assign( "oxorderid", $ordernr);
        $smarty->assign( "orderarticles", $params['sql']['oxorderarticles']);
        $smarty->assign( "oxorder", $params['sql']['oxorder'][0]);
        $smarty->assign( "params", $params);

        $oxOrder = oxNew('oxorder');
        $oxOrder->load($params['order_oxid']);
        $smarty->assign( "order", $oxOrder);


        $sSubject = $oShop->oxshops__oxordersubject->getRawValue() . '( Nr.' . $ordernr . ')';

        $oxEmail->setSubject($sSubject);

        $sBody = $smarty->fetch("email/html/testsieger_resend_confirm.tpl");

        $oxEmail->setBody($sBody);

        $this->msglog($oxEmail->send() ? 'Mail sent.' : 'Mail could not be sent.');

    }


    /**
     * once imported, set order number to first availible no
     *
     * @param array $sql
     */
    protected function set_order_no() {

        $shopID = $this->getConfig()->getShopId();
        $buffer = "";

			if (1 !== $shopID) {
            $buffer = "_" . $shopID;

        }


        $res = oxDb::getDb( oxDB::FETCH_MODE_ASSOC ) ->execute( 'START TRANSACTION' );
            if ($res === false){
                $this->msglog("SQL Error: start transaction error");
        }

            $oCounter = oxNew(OxidEsales\EshopCommunity\Core\Counter::class);
            $sNewOrderNumber = $oCounter->getNext('oxOrder' . $buffer);

            $this->msglog("New ordernumber from counter getNext: " . $sNewOrderNumber);

            $res = oxDb::getDb( oxDB::FETCH_MODE_ASSOC )->execute('COMMIT');
            if ($res === false){
                $this->msglog("SQL Error: transaction commit error");
        }
        // recalculate now
            //$this->msglog("Order nr before recalculation: " . $sNewOrderNumber .'  '. $this->getConfig()->getShopId());// $oOrder->oxorder__oxordernr->value);
        //$oOrder->recalculateOrder();
        //$this->msglog("Order nr after recalculation: " . $oOrder->oxorder__oxordernr->value);

			return $sNewOrderNumber;
    }

    /**
     * Iterate through sql-structure, save all rows in corresponding tables.
     *
     * @param array $sql
     * @throws Exception Unable to save into table
     */
    public function save_sql($sql) {

        $db = oxDb::getDb( oxDB::FETCH_MODE_ASSOC ) ;

        foreach ($sql AS $table => $rows) {

            foreach ($rows AS $row) {

                $query =  ('oxuser' === $table ? 'REPLACE ' : 'INSERT ') .  'INTO ' . $table . ' (' . join(',',array_keys($row)) . ') VALUES '
                    . '(' ;
                for($i=0, $max = count($row); $i<$max; $i++) {
                    $query .= '?, ';
                }

                $query = rtrim($query, ', ');

                $query .= ')';

                if (!$this->getConfig()->isUtf()) {

                    $this->msglog('Shop is not utf-8. Converting data to ISO 8859-15');

                    foreach ($row AS &$value) {
                        $value = iconv("UTF-8", "ISO-8859-15//TRANSLIT", $value);
                    }

                }

                    $res = $db->Execute($query, array_values($row));

                if (false === $res) {
                    $this->msglog('Error saving into table ' . $table . '. Query was ' . $query . ' - ' . $res, 3);
                        /*$this->msglog($db->ErrorMsg(), 3);*/
                    throw new Exception('Error saving into table ' . $table . '. Query was ' . $query);
                }

            }

        }

    }

    /**
     * Delete all rows that were recently inserted.
     * Usefull after insertion failure (transaction-replacement)
     *
     * @param array $sql
     */
    public function revert_sql_save(array $sql) {

        $db = oxDb::getDb( oxDB::FETCH_MODE_ASSOC ) ;

            foreach ($sql AS $table => $rows) {

                foreach ($rows AS $row) {

                if (isset($row['OXID'])) {
                        $db->Execute("DELETE FROM ".$table." WHERE OXID = " . $db->quote($row['OXID']));
                    $this->msglog('Reverted OxId ' . $row['OXID'] . ' from table ' . $table);
                }

            }

        }

    }


    /**
     * Get Payment Info.
     *
     * @param rs_opentrans_document_header_orderinfo $orderinfo
     * @param string $oxuserid
     */
    protected function get_oxuserpayments($orderinfo, $oxuserid, array $config) {

        // Check parameter type (no typehint used - that shopsystem can run un REALLY old server.)
        if (!is_a($orderinfo, 'rs_opentrans_document_header_orderinfo')) {
            throw new rs_opentrans_exception('$orderinfo must be type rs_opentrans_document_header_orderinfo');
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

        switch($payment_type) {

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
                    throw new rs_opentrans_exception('no testsieger.de payment type defined');
                }

                $oxpaymentsid = $payment_testsieger;
                break;

            case 'ueberweisung':
            default:
                $oxpaymentsid = $payment_fallback;
                break;

        }

        $oxuserpayments =  array('OXID' => md5(rand() . microtime()),
            'OXUSERID' => $oxuserid,
            'OXPAYMENTSID' => $oxpaymentsid
        );

        return array($oxuserpayments);

    }

    /**
     * Downloads new xml files
     *
     * @returns bool found_new
     */
    protected function get_remote_xmls() {

        $server_path = '/outbound';

        $remote_filelist = ftps::ftps_getlist( $server_path );

        if ($remote_filelist === false || !is_array($remote_filelist)) {
            throw new Exception('Could not get remote filelist after successfull login. Check firewall.');
        } else {
            $this->msglog("Got filelist");
        }

        $found_new = false;

        foreach($remote_filelist AS $filename){

            $filename_with_path = '/outbound/' . $filename;

            //echo "scanning remote file $filename_with_path\n\n";
            if (false ===strpos($filename_with_path,'-ORDER.xml')) {
                // $this->msglog("Skipping $filename_with_path");
                continue;
            }

            // Check for duplicate
            if (in_array(basename($filename_with_path), $this->get_order_filenames(true))) {
                $this->msglog("Skipping download of already downloaded $filename_with_path");
                continue;
            }

            if (in_array(basename($filename_with_path).'.xml', $this->get_archived_filenames(true))) {
                $this->msglog("Skipping download of already archived $filename_with_path");
                continue;
            }

            //download
            $local_file = $this->get_xml_inbound_path() . basename($filename_with_path);
            $this->msglog("Saving to local file $local_file");
            $success = ftps::ftps_get($filename_with_path, $local_file);

            if ($success) {
                $this->msglog("Got new xml $filename_with_path",2);
                $found_new = true;
            } else {
                $this->msglog("Failed to download new xml $filename_with_path",2);
            }
        }

        return $found_new;

    }

    /**
     * @returns string Path of xml inbound folder
     */
    protected function get_xml_inbound_path() {
        return $this->get_xmlpath().'inbound/'.$this->getConfig()->getShopId().'/';
    }

    /**
     * @returns string Path of xml folder
     */
    protected function get_xmlpath() {
        return $this->get_datapath().'xml/';
    }

    /**
     * @returns string Path of Data folder
     */
    protected function get_datapath() {
        //return $this->getViewConfig()->getModulePath('ts_opentrans_orderimport') .'data/';
        return  getShopBasePath().'modules/ts_opentrans_orderimport/data/';

    }

    /**
     * @returns string Path of xml tmp folder
     */
    protected function get_xml_tmp_path()
    {
        return $this->get_xmlpath() . 'tmp/';
    }

    #########################################################################################
    #########
    #########    Helper Functions: Logging, Concurrency locking, arthimetrics, FTP
    #########
    #########################################################################################

    /**
     * Checks if given TS order id has already been enetered into order mapping table.
     * If so, it @throws rs_opentrans_exception
     *
     * @param string $ts_orderid
     */
    protected function check_ts_orderid_for_conflict($ts_orderid, $filename, $config) {

        $db = oxDb::getDb( oxDB::FETCH_MODE_ASSOC ) ;
        $res = $db->Execute('SELECT count(*) AS cnt FROM oxorder WHERE oxtransid = ? AND oxshopid = ?', array($ts_orderid, $this->getConfig()->getShopId()));

        if ($res->fields['cnt'] > 0) {
            $this->msglog("Order with testsieger-order-id {$ts_orderid} found in mapping table", 3);

            $inbound_file = $filename;
            $archive_file = $this->get_xml_inbound_path() . 'archive/' . basename($filename);

            // Order exists in archive
            if (file_exists($archive_file)) {

                // Same file in inbound folder as in archive folder
                if (md5_file($archive_file) == md5_file($inbound_file)) {

                    // Delete inbound order, move remote order, order has already been imported
                    unlink($inbound_file);
                    $this->archive_xml_filename_remotly($this->get_ftp_stream($config), basename($filename));
                    throw new rs_opentrans_exception('Duplicate order with testsieger-order-id "' . $ts_orderid . '". Order "' . basename($filename) . '" deleted automatically');

                } else {
                    throw new rs_opentrans_exception('Different orders with the same testsieger-order-id (' . $ts_orderid . ') - please check the order "' . basename($filename) . '" manually');
                }

            } else {

                // Order has been imported but its not in the archive folder, so we move it there
                $this->archive_xml_filename_locally(basename($filename));
                throw new rs_opentrans_exception('Moved order "' . basename($filename) . '" to the archive.');

            }

        }

    }

    /**
     * Searches xml folder for files to process.
     * @returns array of xml filenames to be processed.
     */
    protected function get_order_filenames($basename_only = false) {

        $filelist = GLOB( $this->get_xml_inbound_path() . '*-ORDER.xml' );

        if (!is_array($filelist)) $filelist = array();

        if ($basename_only && count($filelist) > 0) {

            foreach ($filelist AS $k => $v) {
                $filelist[$k] = basename($v);
            }

        }

        return $filelist;

    }

    /**
     * Searches xml archive folder for already processed files.
     * @returns array of archived xml filenames.
     */
    protected function get_archived_filenames($basename_only) {

        $filelist = GLOB( $this->get_xml_inbound_path() . 'archive/*-ORDER.xml' );

        if (!is_array($filelist)) $filelist = array();

        if ($basename_only && count($filelist) > 0) {

            foreach ($filelist as $k => $v) {
                $filelist[$k] = basename($v);
            }

        }

        return $filelist;
    }

    /**
     * Check if we have a concurrent lock.
     * Ignore Locks older than an hour.
     * (We also have orderwise monitoring in place)
     *
     * @throws rs_opentrans_exception('Exiting due to concurrency lock [...]');
     */
    protected function concurrency_lock_check() {

        // No lockfile - no lock
        if (!file_exists($this->concurrency_lock_get_filename())) {
            return 'no_lock';
        }

        // We got lockfile. Open and check if it might be outdated
        // due to failure to remove it.
        $fh = $this->concurrency_lock_get_filehandle('r');
        $timestamp = 0;

        if ($fh) {
            $timestamp = fread($fh, 128);
        }

        // Current time is 600 seconds (before) lock+1 hour
        if (($timestamp + self::LOCK_TIME) > time()) {
            die('Exiting due to concurrency lock, beeing ' . (time()-$timestamp) . ' seconds old.  Lock will be deleted after ' . self::LOCK_TIME . ' seconds. / Beende auf Grund der ' . (time()-$timestamp) . ' alten Konkurenzsperre. Sperre wird nach ' . self::LOCK_TIME . ' Sekunden gel&ouml;scht.');
        }

        // Lockfile is outdated.
        $this->msglog('Removing outdated lockfile.',3);
        $this->concurrency_lock_release();
        return 'outdated';

    }

    /**
     * Set lock to prevent concurrent execution.
     *
     * @throws rs_opentrans_exception('Unable to establish concurrency lock file.');
     */
    protected function concurrency_lock_set() {

        $fh = $this->concurrency_lock_get_filehandle('w+');

        if (!$fh) {
            $this->msglog('Unable to establish concurrency lock file.');
            throw new rs_opentrans_exception('Unable to establish concurrency lock file.');
        }

        $this->msglog('Locked', 0);

        fwrite($fh,time());
        fclose($fh);
        return true;

    }

    /**
     * Release concurrency lock
     */
    protected function concurrency_lock_release() {
        $this->msglog('Unlocked', 0);
        @unlink($this->concurrency_lock_get_filename());
    }

    /**
     * @returns string Filepath and -name of Lockfile
     */
    protected function concurrency_lock_get_filename() {
        return $this->get_datapath() . '/testsieger_lockfile.txt';
    }

    /**
     * Get handle of Lockfile
     *
     * @param string $mode of fopen like 'w+' or 'r'
     * @return resource Filehandle
     */
    protected function concurrency_lock_get_filehandle($mode) {
        return fopen($this->concurrency_lock_get_filename(), $mode);
    }

    /**
     * Moves xml file to archive folders.
     *
     * @param string $filename
     */
    public function archive_xml_filename($filename) {

        $filename = basename($filename);

        $this->archive_xml_filename_remotly($filename);
        $this->archive_xml_filename_locally($filename);

    }

    /**
     * Moves xml file remotly from /outbound to /backup
     *
     * @param string $filename
     */
    protected function archive_xml_filename_remotly($filename) {
        //remote archive
        $tmpdir = $this->get_xml_tmp_path();
        $success = ftps::ftps_rename( "/outbound/$filename","/backup/$filename", $tmpdir);
        if ($success) {
            $this->msglog("Remotely archived $filename");
        } else {
            $this->msglog("Could not remotely archive $filename",3);
        }
    }

    /**
     * Moves xml file locally to archive folder
     *
     * @param string $filename
     */
    protected function  archive_xml_filename_locally($filename) {

        $success = copy( $this->get_xml_inbound_path() . $filename,
            $this->get_xml_inbound_path() . 'archive/' . $filename);

        if ($success) {
            $success = unlink($this->get_xml_inbound_path() . $filename);
        }

        if ($success) {
            $this->msglog("Locally archived $filename");
        } else {
            $this->msglog("Could not locally archive $filename",3);
        }

    }

    /**
     * Logger.
     *
     * @param mixed $msg
     * @param mixed $lvl 0: Minor Notice. 1: Notice. 2:Logged notice. 3: Logged error.
     */
    public function msglog($msg, $lvl = 0) {

        if (is_array($msg)) {$msg = implode('<br>' . PHP_EOL, $msg);}

        $msg = htmlspecialchars($msg);

        $out = date('Y.m.d H:i:s: ') . $msg;
        $log_to_file = true; // Set to false to log only major records.

        if (0 == $lvl) {
            //minor notice
            $out = "$msg<br>";
        } else if (1 == $lvl) {
            //notice
            $out = "<b>$msg</b><br>";
        } else if (2 == $lvl) {

            //logged notice
            $out = "<b><u>$msg</u></b><br>";
            $log_to_file = true;

        } else if (3 == $lvl) {

            //logged error
            $out = "<span style='color:#F00'><b><u>$msg</u></b></span><br>";
            $log_to_file = true;
        }

        echo $out;

        if ($log_to_file) {
            file_put_contents($this->get_datapath() .'/testsieger_logfile.html', date('Y.m.d H:i:s: ') . $msg . '<br>',  FILE_APPEND | LOCK_EX);
        }

    }

    /*
    #########################################################################################
    #########################################################################################
    #########################################################################################
    #########################################################################################
    #########################################################################################
    #########################################################################################
    #########################################################################################
    #########################################################################################
    */

    /**
     * Iterate throu itemlist and create array of order objects.
     *
     * @param mixed $itemlist
     * @return array Offer item object list.
     */
    protected function get_oxorderarticles($itemlist) {

        $oxorderarticles = array();

        $this->msglog("Starte Auswertung der Artikelliste");

        foreach ($itemlist AS $key => $item) {

            $remarks = $item->get_remarks();

            $orderposition = array();

            $bruttoprice = $item->get_product_price_fix()->get_price_amount();
            $nettoprice = $bruttoprice / (1 + $item->get_tax_details_fix()->get_tax());
            $amount = $item->get_quantity();

            $orderposition['OXARTNUM'] = $item->get_product_id()->get_supplier_pid();

            $articles_inherited = (boolean) oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne("SELECT oxvarvalue FROM oxconfig WHERE oxvarname LIKE 'blMallInherit_oxarticles'");
            if ($articles_inherited != 0) {
                $tmp_val = trim(oxDb::getDb( oxDB::FETCH_MODE_ASSOC )->getOne('SHOW COLUMNS FROM `oxshops` LIKE \'oxparentid\''));
                if ('' != $tmp_val) {
                    $art_shopID = (integer) oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne("SELECT oxparentid FROM oxshops WHERE OXID=" . $this->getViewConfig()->getActiveShopId());
                } else {
                    $art_shopID = $this->getViewConfig()->getActiveShopId();
                }
            } else {
                $art_shopID = $this->getViewConfig()->getActiveShopId();
            }

            $orderposition['OXARTID'] = (string) oxDb::getDb( oxDB::FETCH_MODE_ASSOC ) ->getOne(
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

            $orderposition['OXID'] = md5(json_encode($orderposition) . rand() . microtime());

            $oxorderarticles[] = $orderposition;

        }

        return $oxorderarticles;

    }

    /**
     * Build an array with all user data to be user for SQL buildt.
     *
     * @param mixed $parties
     * @returns array User
     */
    public function get_user($parties) {

        // Type check

        if (!is_array($parties)) {
            throw new rs_opentrans_exception('$parties must be array');
        }

        $db = oxDb::getDb( oxDB::FETCH_MODE_ASSOC ) ;

        // Rename party keys into their specific function

        foreach ($parties AS $key => $party) {

            if (!is_a($party, 'rs_opentrans_document_party')) {
                throw new rs_opentrans_exception('$parties must be type rs_opentrans_document_party');
            }

            $parties[$party->get_role()] = $party;
            unset($parties[$key]);

        }

        $user = array();

        // Iterate shipping and billing address

        foreach ( array('invoice' => 'billingaddress',
                      'delivery' => 'shippingaddress')
                  AS $partyname => $addresstype) {

            $current_address = $parties[$partyname]->get_address();

            // Get country code

            $res = $db->Execute('SELECT OXID FROM oxcountry WHERE OXISOALPHA2 = ? ',
                array($current_address->get_country_coded())
            );
            if (!$res) {
                $this->msglog('Could not find country for code ' . $current_address->get_country_coded(), 3);
                throw new Exception('Could not find country for code ' . $current_address->get_country_coded());
            }

            $address['countryid'] = $res->fields['OXID'];

            // Get Address

            $address['fname'] = $current_address->get_name2();
            $address['lname'] =  $current_address->get_name3();
            $address['street'] =  $current_address->get_street();

            // Try to split street no. from street name.
            // Count spaces and hypons to street no, e.g. "street 11 - 12"
            // Also accept a single letter, e.g. "Street 10 - 11b"

            $matches = array();
            if (preg_match('~.*\s([\s\-0-9]+[a-zA-Z]?)$~', $address['street'], $matches)) {
                $address['street'] =  trim(substr($matches[0], 0, -1 * strlen($matches[1])));
                $address['streetnr'] =  trim(substr($matches[0], -1 * strlen($matches[1])));
            }

            $address['city'] = $current_address->get_city();
            $address['zip'] = $current_address->get_zip();
            $address['company'] = $current_address->get_name();

            // Get first found adress remark
            $address['addinfo'] =  '';
            foreach ($current_address->get_address_remarks() AS $address_remark) {
                if ($address_remark){
                    $address['addinfo'] =  $address_remark;
                    break;
                }
            }

            // Get first found phone number
            $address['fon'] = ''; // Set below
            foreach ($current_address->get_phone() AS $number) {
                if ($number) {
                    $address['fon'] = $number;
                    break;
                }
            }

            // Get first found mail address
            $address['email'] =  '';
            foreach ($current_address->get_emails() AS $mail) {
                if ($mail) {
                    $address['email'] = $mail;
                    break;
                }
            }

            $user[$addresstype] = $address;

        }

        return array($user);

    }

    /**
     * Iterate throu parties, build customer data,
     * i.e. email, billing address and delivery address.
     *
     * @param array $parties
     * @param array $config
     * @returns array $oxuser
     */
    protected function get_oxuser(array $billaddress) {

        $oxuser = array();

        $oxuser['OXACTIVE'] = 1;
        $oxuser['OXRIGHTS'] = 'user';
        $oxuser['OXSHOPID'] = $this->getConfig()->getShopId();

        $now = new DateTime();
        $oxuser['OXCREATE'] = $now->format('Y-m-d H:i:s');

        $oxuser['OXFNAME'] = $billaddress['fname'];
        $oxuser['OXLNAME'] =  $billaddress['lname'];
        $oxuser['OXSTREET'] =  $billaddress['street'];
        $oxuser['OXSTREETNR'] =  $billaddress['streetnr'];
        $oxuser['OXCOUNTRYID'] =  $billaddress['countryid'];
        $oxuser['OXADDINFO'] =  $billaddress['addinfo'];

        $oxuser['OXCITY'] = $billaddress['city'];
        $oxuser['OXZIP'] = $billaddress['zip'];
        $oxuser['OXFON'] = $billaddress['fon'];
        $oxuser['OXCOMPANY'] = $billaddress['company'];

        $oxuser['OXUSERNAME'] = $billaddress['email'];

        $oxuser['OXID'] = md5($billaddress['email'] . rand());

        if (!$oxuser['OXCOUNTRYID'] || $oxuser['OXCOUNTRYID'] == "DE"){
            $oxuser['OXCOUNTRYID'] =  'a7c40f631fc920687.20179984';
        }
        return array($oxuser);

    }

    /**
     * Takes the results of all procesisng (items, address etc.)
     * and finally creates + saves order.
     *
     * @param array $params Associative Array of all parameters
     */
    protected function create_order(array $params) {

        extract($params);

        $oxorder = array();

        $oxorder['OXID'] = md5(rand() . microtime());
        $oxorder['OXSHOPID'] = $this->getViewConfig()->getActiveShopId();
        $oxorder['OXUSERID'] = $sql['oxuser'][0]['OXID'];
        $oxorder['OXORDERDATE'] = $orderdatetime;
        $oxorder['OXORDERNR'] = $this->set_order_no();

        // Address handling
        $billfields = array('OXBILLCOMPANY',
            'OXBILLEMAIL',
            'OXBILLFNAME',
            'OXBILLLNAME',
            'OXBILLSTREET',
            'OXBILLSTREETNR',
            'OXBILLCITY',
            'OXBILLCOUNTRYID',
            'OXBILLZIP',
            'OXBILLFON',
            'OXBILLADDINFO',
        );

        foreach ($billfields AS $fieldname) {
            $oxorder[$fieldname] = $user[0]['billingaddress'][strtolower(substr($fieldname, strlen('OXBILL')))];
        }
        if (!$oxorder['OXBILLCOUNTRYID'] || $oxorder['OXBILLCOUNTRYID'] == "DE"){
            $oxorder['OXBILLCOUNTRYID'] =  'a7c40f631fc920687.20179984';
        }

        $delfields = array('OXDELCOMPANY',
            'OXDELFNAME',
            'OXDELLNAME',
            'OXDELSTREET',
            'OXDELSTREETNR',
            'OXDELCITY',
            'OXDELCOUNTRYID',
            'OXDELZIP',
            'OXDELFON',
            'OXDELADDINFO',
        );
        //var_dump($user[0]['billingaddress'],$user[0]['shippingaddress']);
        foreach ($delfields AS $fieldname) {
            $oxorder[$fieldname] = $user[0]['shippingaddress'][strtolower(substr($fieldname, strlen('OXDEL')))];
        }
        if (!$oxorder['OXDELCOUNTRYID'] || $oxorder['OXDELCOUNTRYID'] == "DE"){
            $oxorder['OXDELCOUNTRYID'] =  'a7c40f631fc920687.20179984';
        }

        $oxorder['OXPAYMENTID'] = $sql['oxuserpayments'][0]['OXID'];
        $oxorder['OXPAYMENTTYPE'] = $sql['oxuserpayments'][0]['OXPAYMENTSID'];

        // Collect article brutto sum
        $brutsum = 0;
        foreach ($sql['oxorderarticles'] AS $orderposition) {
            $brutsum += $orderposition['OXBRUTPRICE'];
        }

        $oxorder['OXTOTALBRUTSUM'] = $brutsum;

        $oxorder['OXTOTALNETSUM'] = round($oxorder['OXTOTALBRUTSUM'] * (100/(100+self::RS_VAT)), 2);

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
            ? (float)$orderinfo_remarks['additional_costs']
            : 0
        );

        $oxorder['OXWRAPCOST'] = $installation_fees;
        $oxorder['OXWRAPVAT'] = $installation_fees * self::RS_VAT * 0.01;

        $oxorder['OXTRANSID'] = $ts_orderid;

        $oxorder['OXCURRENCY'] = $currency;
        $oxorder['OXCURRATE'] = 1;
        $oxorder['OXFOLDER'] = 'ORDERFOLDER_NEW';
        $oxorder['OXTRANSSTATUS'] = 'OK';
        $oxorder['OXDELTYPE'] = $config['testsieger_shippingtype'] ? $config['testsieger_shippingtype'] : $orderinfo_remarks['delivery_method'];
        //$oxorder['OXDELTYPE'] = $this->custom_oxdeltype_bigpackage($sql['oxorderarticles']) ? 'oxidstandard' : '4mb28dcacca7756a466f95f5fcf6c349';


        $oxorder['OXPAID'] = date("Y-m-d H:i:s");

        return array($oxorder);

    }

    /*
    protected function custom_oxdeltype_bigpackage($sql_ororderarticles) {

        // example of custom shipping handling. Will return true if more than 1 item is sold, or the single item is heavier that 5g.

        $this->msglog('Using custom shipping handling.');

        // More than one position?
        if(1 < count($sql_ororderarticles)) {return 1;}

        $db = oxDb::getDb( oxDB::FETCH_MODE_ASSOC ) ;

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
    public function testimport() {
        require_once(  getShopBasePath().'modules/ts_opentrans_orderimport/opentrans/opentrans.php');
        $aConfig = array(
            "testsieger_paymenttype_fallback" => "tsinv",
            "testsieger_shippingtype" => "209e2257a0175dcabcdbec468a624668"
        );
        $sTestFile = getShopBasePath() . "tmp/2015-04-06-09-28-06_TS-2015-538651-1-8337-ORDER.xml";
        if (file_exists($sTestFile)) {
            copy($sTestFile, $this->get_xml_inbound_path() . basename($sTestFile));
            $this->process_xml_file($this->get_xml_inbound_path() . basename($sTestFile), $aConfig);
        }
    }

        /**
         * 2019-11-17, Llama: function to test import from inbound folder
         */
        public function testimport2() {
            require_once(  getShopBasePath().'modules/ts_opentrans_orderimport/opentrans/opentrans.php');
            $aConfig = array(
                "testsieger_paymenttype_fallback" => "tsinv",
                "testsieger_shippingtype" => "209e2257a0175dcabcdbec468a624668"
            );
            foreach (glob($this->get_xml_inbound_path() ."*.xml") as $filename) {
                echo "$filename размер " . filesize($filename) . "\n";
                $this->process_xml_file($this->get_xml_inbound_path() . basename($filename), $aConfig);
            }
        }

}
