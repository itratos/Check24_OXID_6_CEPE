<?php

$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id'           => 'check24connector',
    'title'        => 'Testsieger Check24 connector',
    'description'  => 'Erlmoeglicht, Testsieger-Bestellungen zu importieren. Die Einstellungen finden Sie auf der linken Seite unter "Testsieger".',
    'extend'       => [
        OxidEsales\Eshop\Application\Model\Maintenance::class => TestSieger\Check24Connector\Application\Model\Maintenance::class,
        OxidEsales\Eshop\Application\Model\Order::class => TestSieger\Check24Connector\Application\Model\Order::class
    ],
    'controllers'        => [
        'testsieger_orderimport' => TestSieger\Check24Connector\Application\Controller\OrderImport::class,
        'testsieger_orderchangeimportview' => TestSieger\Check24Connector\Application\Controller\OrderChangeImportView::class,
        'testsieger_orderimportview' => TestSieger\Check24Connector\Application\Controller\OrderImportView::class,
    ],
    'events' => [
        'onActivate' => 'TestSieger\Check24Connector\Core\Events::onActivate',
        'onDeactivate' => 'TestSieger\Check24Connector\Core\Events::onDeactivate'
    ],
    'templates' => [
        'testsieger_orderimport.tpl' => 'testsieger/check24connector/Application/views/admin/tpl/testsieger_orderimport.tpl'
    ],
    'settings' => [
        [
            'group' => 'opentrans_address',
            'name'  => 'sOpentransAddressName',
            'type'  => 'str',
            'value' => 'OXID Testshop OpenTrans 2.1'
        ],
        [
            'group' => 'opentrans_address',
            'name'  => 'sOpentransAddressStreet',
            'type'  => 'str',
            'value' => 'Wallstraße 9-13'
        ],
        [
            'group' => 'opentrans_address',
            'name'  => 'sOpentransAddressZip',
            'type'  => 'str',
            'value' => '10179'
        ],
        [
            'group' => 'opentrans_address',
            'name'  => 'sOpentransAddressCity',
            'type'  => 'str',
            'value' => 'Berlin'
        ],
        [
            'group' => 'opentrans_address',
            'name'  => 'sOpentransAddressCountryCode',
            'type'  => 'str',
            'value' => 'DE'
        ]
    ]
];