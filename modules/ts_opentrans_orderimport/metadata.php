<?php

$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'ts_opentrans_orderimport',
    'title'        => 'TestsiegerOrderImport',
    'description'  => 'Erlmoeglicht, Testsieger-Bestellungen zu importieren. Die Einstellungen finden Sie auf der linken Seite unter "Testsieger".',
    'extend'       => array(),
    'files'        => array(
        'testsieger_orderimport' => 'ts_opentrans_orderimport/controllers/testsieger_orderimport.php',
        'testsieger_opentrans_orderimport' => 'ts_opentrans_orderimport/views/testsieger_opentrans_orderimport.php'
     )
);
