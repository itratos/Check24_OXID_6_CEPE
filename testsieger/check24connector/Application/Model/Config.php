<?php

namespace TestSieger\Check24Connector\Application\Model;

use OxidEsales\Eshop\Core\Registry as Registry;

/**
 * Module config data class
 */
class Config
{
    /**
     * Namespace url for CHECK24 xml files
     */
    const NAMESPACE_BMECAT_URL = 'http://www.bmecat.org/bmecat/2005';

    /**
     * Returns module config data
     *
     * @return array
     */
    public static function getConfig()
    {
        $oConf = Registry::getConfig();
        return [
            'testsieger_ftpuser' => $oConf->getShopConfVar('testsieger_ftpuser', NULL, 'testsieger_orderimport'),
            'testsieger_ftppass' => $oConf->getShopConfVar('testsieger_ftppass', NULL, 'testsieger_orderimport'),
            'testsieger_ftphost' => $oConf->getShopConfVar('testsieger_ftphost', NULL, 'testsieger_orderimport'),
            'testsieger_ftpport' => $oConf->getShopConfVar('testsieger_ftpport', NULL, 'testsieger_orderimport'),
            'testsieger_active' => $oConf->getShopConfVar('testsieger_active', NULL, 'testsieger_orderimport'),
            'testsieger_shippingtype' => $oConf->getShopConfVar('testsieger_shippingtype', NULL, 'testsieger_orderimport'),
            'testsieger_sendorderconf' => $oConf->getShopConfVar('testsieger_sendorderconf', NULL, 'testsieger_orderimport'),
            'testsieger_reducestock' => $oConf->getShopConfVar('testsieger_reducestock', NULL, 'testsieger_orderimport'),
            'testsieger_paymenttype_fallback' => $oConf->getShopConfVar('testsieger_paymenttype_fallback', NULL, 'testsieger_orderimport'),
            'testsieger_paymenttype_ts' => $oConf->getShopConfVar('testsieger_paymenttype_ts', NULL, 'testsieger_orderimport')
        ];
    }

    /**
     * Returns path to module log
     *
     * @return string
     */
    public static function getLogPath()
    {
        return 'modules/testsieger/check24connector/data/testsieger_logfile.html';
    }
}