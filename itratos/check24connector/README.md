1. Please add this code to source/Application/views/admin/tpl/order_list.tpl

[{if $oViewConf->isModuleActive('check24connector')}]
[{include file="check24connector_pagetabsnippet.tpl"}]
[{else}]
[{include file="pagetabsnippet.tpl"}]
[{/if}]

in place of 

[{include file="pagetabsnippet.tpl"}]


2. Please add
"itratos/check24connector": "^1.0"

to "require" block in your project composer.json

and repositories block to composer.json:

"repositories": {
    "itratos": {
        "type": "path",
        "url": "source/modules/itratos/check24connector"
    }
}


3. The module is installed by command:

composer require itratos/check24connector


4. Cron scripts

I. /bin/processallcheck24events.php

processes:

1. Orders ships in Shop - sends DISPATCHNOTIFICATION document to Check24
2. Orders cancelled in Shop - sends cancellation ORDERCHANGE document to Check24
3. Processes all outbound documents from Check24

II. Order import

https://your_project_domain/index.php?&cl=check24connector_orderimportview&fnc=import&key=your_check24_user_name

III. Orderchange import

https://your_project_domain/index.php?cl=check24connector_orderchangeimportview&fnc=import&key=your_check24_user_name