<?php

namespace TestSieger\Check24Connector\Application\Model;

use \TestSieger\Check24Connector\Application\Model\Config as OpentransConfig;
use OxidEsales\Eshop\Core\DatabaseProvider as DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use \TestSieger\Check24Connector\Application\Model\Logger;
use \TestSieger\Check24Connector\Application\Model\Opentrans\rs_opentrans_exception;

/**
 * Class to handle outbound and inbound XML files
 */
class Process
{
    /**
     * Exit text for "no files"
     */
    const RS_OPENTRANS_EXIT_NONEWFILES = 'Keine neuen Bestelldateien.';

    /**
     * Exit text for success
     */
    const RS_OPENTRANS_EXIT_OK = 'Erfolgreich abgeschlossen.';

    /**
     * Exit text for error
     */
    const RS_OPENTRANS_EXIT_ERROR = 'Fehler.';

    /**
     * Locking time
     */
    const LOCK_TIME = 600;

    /**
     * @var null
     */
    protected $_filetype = null;

    /**
     * Class constructor, sets filetype and ftp stream
     *
     * @param $filetype
     */
    public function __construct($filetype = null)
    {
        $this->_filetype = $filetype;
    }

    /**
     * Downloads new xml files type ORDER, ORDERCHNAGE
     *
     * @return bool
     */
    public function get_remote_xmls()
    {

        $remote_filelist = Ftps::ftps_getlist('/outbound');
        if (!$remote_filelist || !is_array($remote_filelist)) {
            throw new \Exception('Could not get remote filelist after successfull login. Check firewall.');
        } else {
            Logger::msglog("Got filelist");
        }

        $found_new = false;
        foreach ($remote_filelist as $remotefile) {
            if (false === stripos($remotefile, '-' . $this->_filetype . '.xml')) {
                continue;
            }
            // Check for duplicate
            if (in_array(basename($remotefile), $this->get_order_filenames(true))) {
                Logger::msglog("Skipping download of already downloaded $remotefile");
                continue;
            }

            if (in_array(basename($remotefile) . '.xml', $this->get_archived_filenames(true))
                || in_array(basename($remotefile) . '.XML', $this->get_archived_filenames(true))
            ) {
                Logger::msglog("Skipping download of already archived $remotefile");
                continue;
            }

            //download
            $remote_filepath = '/outbound/' . $remotefile;
            $local_filepath = $this->get_xml_inbound_path() . basename($remotefile);
            Logger::msglog("Saving to local file $local_filepath");
            $success = Ftps::ftps_get($remote_filepath, $local_filepath);

            if ($success) {
                Logger::msglog("Got new xml $remote_filepath", 2);
                $found_new = true;
            } else {
                Logger::msglog("Failed to download new xml $remote_filepath", 2);
            }
        }
        return $found_new;
    }

    /**
     * @returns string Path of xml inbound folder
     */
    public function get_xml_inbound_path()
    {
        return $this->get_xmlpath() . 'inbound/' . Registry::getConfig()->getShopId() . '/';
    }

    /**
     * @returns string Path of xml outbound folder
     */
    public function get_xml_outbound_path()
    {
        return $this->get_xmlpath() . 'outbound/' . Registry::getConfig()->getShopId() . '/';
    }

    /**
     * @returns string Path of xml tmp folder
     */
    public function get_xml_tmp_path()
    {
        return $this->get_xmlpath() . 'tmp/';
    }

    /**
     * @returns string Path of xml remote inbound folder
     */
    public function get_xml_remote_inbound_path()
    {
        return 'inbound/';
    }

    /**
     * @returns string Path of xml remote outbound folder
     */
    public function get_xml_remote_outbound_path()
    {
        return $this->get_xmlpath() . 'outbound/' . Registry::getConfig()->getShopId() . '/';
    }

    /**
     * @returns string Path of xml folder
     */
    protected function get_xmlpath()
    {
        return $this->get_datapath() . 'xml/';
    }

    /**
     * @returns string Path of Data folder
     */
    protected function get_datapath()
    {
        return getShopBasePath() . 'modules/testsieger/check24connector/data/';
    }

    /**
     * Searches xml folder for files to process.
     * @returns array of xml filenames to be processed.
     */
    public function get_order_filenames($basename_only = false)
    {
        $filelist = glob($this->get_xml_inbound_path() . '*-' . $this->_filetype . '.[xX][mM][lL]');

        if (!is_array($filelist)) $filelist = array();
        if ($basename_only && count($filelist) > 0) {
            foreach ($filelist as $k => $v) {
                $filelist[$k] = basename($v);
            }
        }
        return $filelist;
    }

    /**
     * Searches xml archive folder for already processed files.
     * @returns array of archived xml filenames.
     */
    protected function get_archived_filenames($basename_only)
    {
        $filelist = glob($this->get_xml_inbound_path() . 'archive/*-ORDER.[xX][mM][lL]');
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
    protected function concurrency_lock_check()
    {
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
            die('Exiting due to concurrency lock, beeing ' . (time() - $timestamp) . ' seconds old.  Lock will be deleted after ' . self::LOCK_TIME . ' seconds. / Beende auf Grund der ' . (time() - $timestamp) . ' alten Konkurenzsperre. Sperre wird nach ' . self::LOCK_TIME . ' Sekunden gel&ouml;scht.');
        }

        // Lockfile is outdated.
        Logger::msglog('Removing outdated lockfile.', 3);
        $this->concurrency_lock_release();
        return 'outdated';
    }

    /**
     * Set lock to prevent concurrent execution.
     */
    public function concurrency_lock_set()
    {
        $fh = $this->concurrency_lock_get_filehandle('w+');

        if (!$fh) {
            Logger::msglog('Unable to establish concurrency lock file.');
            throw new rs_opentrans_exception('Unable to establish concurrency lock file.');
        }

        Logger::msglog('Locked', 0);
        fwrite($fh, time());
        fclose($fh);
        return true;
    }

    /**
     * Release concurrency lock
     */
    public function concurrency_lock_release()
    {
        Logger::msglog('Unlocked', 0);
        @unlink($this->concurrency_lock_get_filename());
    }

    /**
     * @returns string Filepath and -name of Lockfile
     */
    protected function concurrency_lock_get_filename()
    {
        return $this->get_datapath() . '/testsieger_lockfile.txt';
    }

    /**
     * Get handle of Lockfile
     *
     * @param string $mode of fopen like 'w+' or 'r'
     * @return resource Filehandle
     */
    protected function concurrency_lock_get_filehandle($mode)
    {
        return fopen($this->concurrency_lock_get_filename(), $mode);
    }

    /**
     * Moves xml file to archive folders.
     *
     * @param stream $ftpstream
     * @param string $filename
     */
    public function archive_xml_filename($filename)
    {
        $filename = basename($filename);
        $this->archive_xml_filename_remotely($filename);
        $this->archive_xml_filename_locally($filename);

    }

    /**
     * Moves xml file remotly from /outbound to /backup
     *
     * @param string $filename
     */
    public function archive_xml_filename_remotely($filename)
    {
        $success = Ftps::ftps_rename("/outbound/$filename", "/backup/$filename", $this->get_xml_tmp_path());
        if ($success) {
            Logger::msglog("Remotely archived $filename");
        } else {
            Logger::msglog("Could not remotely archive $filename", 3);
        }
    }

    /**
     * Moves xml file locally to archive folder
     *
     * @param string $filename
     */
    public function archive_xml_filename_locally($filename)
    {
        $success = copy($this->get_xml_inbound_path() . $filename, $this->get_xml_inbound_path() . 'archive/' . $filename);
        if ($success) {
            $success = unlink($this->get_xml_inbound_path() . $filename);
        }
        if ($success) {
            Logger::msglog("Locally archived $filename");
        } else {
            Logger::msglog("Could not locally archive $filename", 3);
        }
    }
}