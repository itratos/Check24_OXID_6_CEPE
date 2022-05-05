<?php

namespace TestSieger\Check24Connector\Application\Model;

use TestSieger\Check24Connector\Application\Model\Config as OpentransConfig;

/**
 * Module logger class
 */
class Logger
{
    /**
     * Function to save a log entry
     *
     * @param $msg
     * @param $lvl 0: Minor Notice. 1: Notice. 2:Logged notice. 3: Logged error.
     * @return void
     */
    public static function msglog($msg, $lvl = 0)
    {
        if (is_array($msg)) {
            $msg = implode('<br>' . PHP_EOL, $msg);
        }
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
            file_put_contents(
                getShopBasePath() . OpentransConfig::getLogPath(),
                date('Y.m.d H:i:s: ') . $msg . '<br>',
                FILE_APPEND | LOCK_EX
            );
        }
    }
}