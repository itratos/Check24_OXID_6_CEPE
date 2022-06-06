<?php

$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id'           => 'check24connector',
    'title'       => '<img src="../modules/itratos/check24connector/itratos-small.png" height="12" style="margin-right: 5px;" />Check24 connector',
    'description'  => [
        'de' => 'Das Modul ermöglicht den Import von Check24 Bestellungen und den Abgleich von Statusmeldungen. Die Einstellungen finden Sie auf der linken Seite unter “Check24” und in den Bestellungen im TAB Check24.',
        'en' => 'The module enables the import of Check24 orders and the comparison of status messages. The settings can be found on the left under "Check24" and in the orders in TAB Check24.'
    ],
    'thumbnail' => 'itratos.png',
    'version' => '1.0.0',
    'author' => 'Itratos Ltd & Co. KG',
    'url' => 'https://www.itratos.de',
    'email' => 'support@itratos.de',
    'extend'       => [
        OxidEsales\Eshop\Application\Model\Maintenance::class => Itratos\Check24Connector\Application\Model\Maintenance::class,
        OxidEsales\Eshop\Application\Model\Order::class => Itratos\Check24Connector\Application\Model\Order::class
    ],
    'controllers'        => [
        'check24connector_orderimport' => Itratos\Check24Connector\Application\Controller\Admin\OrderImport::class,
        'check24connector_orderchangeimportview' => Itratos\Check24Connector\Application\Controller\OrderChangeImportView::class,
        'check24connector_orderimportview' => Itratos\Check24Connector\Application\Controller\OrderImportView::class,
        'check24connector_settings' => Itratos\Check24Connector\Application\Controller\Admin\Settings::class,
        'check24connector_requestlist' => Itratos\Check24Connector\Application\Controller\Admin\RequestList::class,
        'check24connector_requestoverview' => Itratos\Check24Connector\Application\Controller\Admin\RequestOverview::class
    ],
    'events' => [
        'onActivate' => 'Itratos\Check24Connector\Core\Events::onActivate',
        'onDeactivate' => 'Itratos\Check24Connector\Core\Events::onDeactivate'
    ],
    'templates' => [
        'check24connector_settings.tpl' => 'itratos/check24connector/Application/views/admin/tpl/check24connector_settings.tpl',
        'check24connector_orderimport.tpl' => 'itratos/check24connector/Application/views/admin/tpl/check24connector_orderimport.tpl',
        'check24connector_requestlist.tpl' => 'itratos/check24connector/Application/views/admin/tpl/check24connector_requestlist.tpl',
        'check24connector_pagetabsnippet.tpl' => 'itratos/check24connector/Application/views/admin/tpl/check24connector_pagetabsnippet.tpl',
        'check24connector_requestoverview.tpl' => 'itratos/check24connector/Application/views/admin/tpl/check24connector_requestoverview.tpl',
        'check24connector_request_reject_popup.tpl' => 'itratos/check24connector/Application/views/admin/tpl/check24connector_request_reject_popup.tpl',
        'check24connector_confirmation.tpl' => 'itratos/check24connector/Application/views/email/html/check24connector_confirmation.tpl'
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