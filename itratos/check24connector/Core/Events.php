<?php

namespace Itratos\Check24Connector\Core;

use OxidEsales\Eshop\Core\{DatabaseProvider, DbMetaDataHandler};

class Events
{
    /**
     * Execute action on activate event
     * create log table to save all baskets on calculation
     */
    public static function onActivate()
    {
        $oDbMetaDataHandler = oxNew(DbMetaDataHandler::class);

        if (!$oDbMetaDataHandler->fieldExists('itrcheck24_dispatchnotification_sent', 'oxorder')) {
            DatabaseProvider::getDb()->execute("ALTER TABLE oxorder ADD COLUMN itrcheck24_dispatchnotification_sent TINYINT(1) NOT NULL DEFAULT 0");
        }

        if (!$oDbMetaDataHandler->fieldExists('itrcheck24_processed', 'oxorder')) {
            DatabaseProvider::getDb()->execute("ALTER TABLE oxorder ADD COLUMN itrcheck24_processed TINYINT(1) NOT NULL DEFAULT 0");
        }

        if (!$oDbMetaDataHandler->fieldExists('itrcheck24_idrefs', 'oxorder')) {
            DatabaseProvider::getDb()->execute("ALTER TABLE oxorder ADD COLUMN itrcheck24_idrefs text COLLATE utf8_general_ci NOT NULL default '' AFTER `itrcheck24_processed`");
        }

        if (!$oDbMetaDataHandler->fieldExists('itrcheck24_lineitemid', 'oxorderarticles')) {
            DatabaseProvider::getDb()->execute("ALTER TABLE oxorderarticles ADD COLUMN itrcheck24_lineitemid varchar(255) COLLATE utf8_general_ci NOT NULL default ''");
        }

        if (!$oDbMetaDataHandler->fieldExists('itrcheck24_cancellation_reasonkey', 'oxorderarticles')) {
            DatabaseProvider::getDb()->execute("ALTER TABLE oxorderarticles ADD COLUMN itrcheck24_cancellation_reasonkey varchar(255) COLLATE utf8_general_ci NOT NULL default ''");
        }

        if (!$oDbMetaDataHandler->fieldExists('itrcheck24_cancellation_reasondescription', 'oxorderarticles')) {
            DatabaseProvider::getDb()->execute("ALTER TABLE oxorderarticles ADD COLUMN itrcheck24_cancellation_reasondescription varchar(255) COLLATE utf8_general_ci NOT NULL default ''");
        }

        DatabaseProvider::getDb()->execute(
          "CREATE TABLE IF NOT EXISTS `itrcheck24_orderchangerequest` (
                    `OXID` char(32) NOT NULL,
                    `ORDERID` varchar(255) NOT NULL,
                    `ACTION` varchar(255) NOT NULL,
                    `SEQUENCEID` INT(11) NOT NULL,
                    `RESPONSE` int(11) NOT NULL COMMENT '1 - confirm cancellation, 2 - deny cancellation',
                    `REASON` varchar(255) NOT NULL,
                    `OXTIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                     KEY `ORDERID` (`ORDERID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );
    }

    public static function onDeactivate()
    {
    }
}