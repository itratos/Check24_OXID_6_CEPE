[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<link rel="stylesheet" href="[{$oViewConf->getModuleUrl('check24connector','out/src/css/check24connector.css')}]" type="text/css" />

<!-- Buttons to manually run order and orderchange import -->
<table cellspacing="0" cellpadding="0" border="0" style="width:98%;" class="check24-orderimport">
<tr>
    <td valign="top" class="edittext" align="left">
        <h2>[{oxmultilang ident="CHECK24CONNECTOR_ORDERCHANGE_IMPORT_TITLE"}]</h2>

    <input type="submit" class="edittext" id="importNowButton" value="[{oxmultilang ident="ITRCHECK24CONNECTOR_IMPORT_ORDERS_NOW"}]"
           onclick="window.open('[{$iframeurl}]', '[{oxmultilang ident="ITRCHECK24CONNECTOR_ORDERIMPORT"}]');">
    <input type="submit" class="edittext" id="importOrderChangeButton" value="[{oxmultilang ident="ITRCHECK24CONNECTOR_IMPORT_ORDERCHANGE_NOW"}]"
           onclick="window.open('[{$iframeurl_changeorder}]', '[{oxmultilang ident="ITRCHECK24CONNECTOR_ORDERCHANGEIMPORT"}]');">

    </td>
</tr>
</table>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
