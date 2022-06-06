<?php


namespace Itratos\Check24Connector\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry as Registry;
use OxidEsales\Eshop\Core\DatabaseProvider;
use Itratos\Check24Connector\Application\Model\OpentransDocumentOrderchange;
use Itratos\Check24Connector\Application\Model\OpentransException;

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

        $key = $oConf->getShopConfVar('testsieger_ftpuser', null, 'testsieger_orderimport');

        $this->_aViewData['iframeurl'] = Registry::get("oxUtilsUrl")->appendUrl(
            $oConf->getShopHomeUrl(NULL, false),
            [
                'shp' => $shopId,
                'cl' => 'check24connector_orderimportview',
                'fnc' => 'import',
                'key' => $key,
            ]
        );

        $this->_aViewData['iframeurl_changeorder'] = Registry::get("oxUtilsUrl")->appendUrl(
            $oConf->getShopHomeUrl(NULL, false),
            [
                'shp' => $shopId,
                'cl' => 'check24connector_orderchangeimportview',
                'fnc' => 'import',
                'key' => $key
            ]
        );

        return 'check24connector_orderimport.tpl';
    }

}
