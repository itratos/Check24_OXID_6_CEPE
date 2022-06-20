<?php

namespace Itratos\Check24Connector\Application\Model;

use Itratos\Check24Connector\Application\Model\Config as OpentransConfig;

/**
 * Class to handle file transfers via ftps protocol using curl
 */
class Ftps
{

    public static function ftps_put($filesource, $filetarget)
    {
        $ch = curl_init();
        $fp = fopen($filesource, 'r');
        $config = OpentransConfig::getConfig();

        curl_setopt($ch, CURLOPT_URL,  $config['testsieger_ftphost'] . '/' . $filetarget);
        curl_setopt($ch, CURLOPT_PORT, $config['testsieger_ftpport']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $config['testsieger_ftpuser'] . ":" . $config['testsieger_ftppass']);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($filesource));

        $result = curl_exec($ch);
        $error_no = curl_errno($ch);
        curl_close($ch);
        return $error_no == 0;
    }

    public static function ftps_get($filesource, $filetarget)
    {
        $fp = fopen($filetarget, 'w');
        $config = OpentransConfig::getConfig();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['testsieger_ftphost'] . $filesource);
        curl_setopt($ch, CURLOPT_PORT, $config['testsieger_ftpport']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $config['testsieger_ftpuser'] . ":" . $config['testsieger_ftppass']);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $content = curl_exec($ch);
        $error_no = curl_errno($ch);
        curl_close($ch);

        // save xml content on file
        return fwrite($fp, $content);
    }

    public static function ftps_getlist($dir)
    {
        $config = OpentransConfig::getConfig();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['testsieger_ftphost'] . $dir . '/');
        curl_setopt($ch, CURLOPT_PORT, $config['testsieger_ftpport']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $config['testsieger_ftpuser'] . ":" . $config['testsieger_ftppass']);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_DIRLISTONLY, true);

        $files_list = curl_exec($ch);
        curl_close($ch);
        $file_names_array = explode("\n", $files_list);
        return $file_names_array;
    }

    public static function ftps_rename($filepath_from, $filepath_to, $tmpdirpath) {
        //download source file to local tmp
        $tmp_filepath = $tmpdirpath . basename($filepath_from);

        if(!self::ftps_get($filepath_from, $tmp_filepath)) {
            return false;
        }

        //upload source file from local tmp to remote
        if(self::ftps_put($tmp_filepath, $filepath_to)) {
            $result = self::ftps_delete($filepath_from);
            //delete file from local tmp
            unlink($tmp_filepath);
            return $result;
        }
        return false;
    }

    public static function ftps_delete($filepath)
    {
        $config = OpentransConfig::getConfig();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['testsieger_ftphost'] );
        curl_setopt($ch, CURLOPT_PORT, $config['testsieger_ftpport']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $config['testsieger_ftpuser'] . ":" . $config['testsieger_ftppass']);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_QUOTE, array('DELE /' . $filepath));
        $result = curl_exec($ch);
        $error_no = curl_errno($ch);
        curl_close($ch);
        return $error_no == 0;
    }
}