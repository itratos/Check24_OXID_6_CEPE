<?php

namespace Itratos\Check24Connector\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\DatabaseProvider;
use Itratos\Check24Connector\Application\Model\Config as OpentransConfig;

/**
 * Admin view controller for order and orderchange import from CHECK24
 */
class Settings extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
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

        $aPaymentList = DatabaseProvider::getDb()->getAll('SELECT oxid, oxdesc FROM oxpayments  ORDER BY oxsort');

        $this->_aViewData['paymentlist'] = $aPaymentList;

        return 'check24connector_settings.tpl';
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

}
