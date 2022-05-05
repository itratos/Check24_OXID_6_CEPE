<?php

use OxidEsales\Eshop\Core\DatabaseProvider as DatabaseProvider;
use OxidEsales\Eshop\Core\Registry as Registry;

ini_set('display_errors', 0);
set_time_limit(0);


require_once dirname(__FILE__) . '/../../../../bootstrap.php';

$oMaintenance = oxNew(\OxidEsales\Eshop\Application\Model\Maintenance::class);

$oMaintenance->processAllCheck24Events();


