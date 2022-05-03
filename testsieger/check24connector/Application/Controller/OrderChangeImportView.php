<?php

namespace TestSieger\Check24Connector\Application\Controller;

use TestSieger\Check24Connector\Application\Model\Config as OpentransConfig;
use OxidEsales\Eshop\Core\{DatabaseProvider, Registry};

/**
 * Class for orderchange document import from CHECK24
 */
class OrderChangeImportView extends \OxidEsales\Eshop\Application\Controller\FrontendController
{

    /**
     * Frontend controller. Main entry point to initiate Orderchange import from CHECK24
     *
     * @return void
     */
    public function import()
    {
        $config = OpentransConfig::getConfig();

        echo '<pre>starting<br>';
        echo '<a href="javascript:history.back()">zur&uuml;ck / back</a><br>';

        if (1 != $config['testsieger_active']) {
            die('Import inactive. / Import inaktiv');
        }

        if (!$config['testsieger_ftpuser'] || !isset($_REQUEST['key']) || $_REQUEST['key'] != $config['testsieger_ftpuser']) {
            die('Wrong Username. / Falscher Benutzername');
        }

        $oMaintenance = oxNew(\TestSieger\Check24Connector\Application\Model\Maintenance::class);

        $oMaintenance->processOutboundDocuments();

        die('<span style="color:#090"><b><u>OK! Exit now. </u></b></span>');
    }
}
