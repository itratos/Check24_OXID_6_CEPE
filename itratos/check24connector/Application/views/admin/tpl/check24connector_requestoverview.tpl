[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
    [{else}]
    [{assign var="readonly" value=""}]
    [{/if}]

<form name="transfer" id="transfer" data-tmp="1" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="check24connector_requestoverview">
</form>

[{if $edit}]
<table cellspacing="0" cellpadding="0" border="0" width="98%">
    <form name="search" id="search" action="[{$oViewConf->getSelfLink()}]" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cur" value="[{$oCurr->id}]">
        <input type="hidden" name="cl" value="order_article">
        <input type="hidden" name="oxid" value="[{$oxid}]">
        <input type="hidden" name="fnc" value="updateOrder">
        <tr>
            [{block name="admin_order_article_header"}]
            <td class="listheader first">[{oxmultilang ident="GENERAL_SUM"}]</td>
            <td class="listheader" height="15">&nbsp;&nbsp;&nbsp;[{oxmultilang ident="GENERAL_ITEMNR"}]</td>
            <td class="listheader">&nbsp;&nbsp;&nbsp;[{oxmultilang ident="GENERAL_TITLE"}]</td>
            <td class="listheader">&nbsp;&nbsp;&nbsp;[{oxmultilang ident="GENERAL_TYPE"}]</td>
            <td class="listheader">&nbsp;&nbsp;&nbsp;[{oxmultilang ident="ORDER_ARTICLE_PARAMS"}]</td>
            <td class="listheader">&nbsp;&nbsp;&nbsp;[{oxmultilang ident="GENERAL_SHORTDESC"}]</td>
            [{if $edit->isNettoMode()}]
            <td class="listheader">[{oxmultilang ident="ORDER_ARTICLE_ENETTO"}]</td>
            [{else}]
            <td class="listheader">[{oxmultilang ident="ORDER_ARTICLE_EBRUTTO"}]</td>
            [{/if}]
            <td class="listheader">[{oxmultilang ident="GENERAL_ATALL"}]</td>
            <td class="listheader" colspan="3">[{oxmultilang ident="ORDER_ARTICLE_MWST"}]</td>
            [{/block}]
        </tr>
        [{assign var="blWhite" value=""}]
        [{foreach from=$edit->getOrderArticles() item=listitem name=orderArticles}]
        <tr id="art.[{$smarty.foreach.orderArticles.iteration}]">
            [{block name="admin_order_article_listitem"}]
            [{if $listitem->oxorderarticles__oxstorno->value == 1}]
            [{assign var="listclass" value=listitem3}]
            [{else}]
            [{assign var="listclass" value=listitem$blWhite}]
            [{/if}]
            <td valign="top" class="[{$listclass}]">[{if $listitem->oxorderarticles__oxstorno->value != 1 && !$listitem->isBundle()}]<input type="text" name="aOrderArticles[[{$listitem->getId()}]][oxamount]" value="[{$listitem->oxorderarticles__oxamount->value}]" class="listedit">[{else}][{$listitem->oxorderarticles__oxamount->value}][{/if}]</td>
            <td valign="top" class="[{$listclass}]" height="15">[{if $listitem->oxarticles__oxid->value}]<a href="Javascript:editThis('[{$listitem->oxarticles__oxid->value}]');" class="[{$listclass}]">[{/if}][{$listitem->oxorderarticles__oxartnum->value}]</a></td>
            <td valign="top" class="[{$listclass}]">[{if $listitem->oxarticles__oxid->value}]<a href="Javascript:editThis('[{$listitem->oxarticles__oxid->value}]');" class="[{$listclass}]">[{/if}][{$listitem->oxorderarticles__oxtitle->value|oxtruncate:20:""|strip_tags}]</a></td>
            <td valign="top" class="[{$listclass}]">[{$listitem->oxorderarticles__oxselvariant->value}]</td>
            <td valign="top" class="[{$listclass}]">
                [{if $listitem->getPersParams()}]
                [{block name="admin_order_article_persparams"}]
                [{include file="include/persparams.tpl" persParams=$listitem->getPersParams()}]
                [{/block}]
                [{/if}]
            </td>
            <td valign="top" class="[{$listclass}]">[{$listitem->oxorderarticles__oxshortdesc->value|oxtruncate:20:""|strip_tags}]</td>
            [{if $edit->isNettoMode()}]
            <td valign="top" class="[{$listclass}]">[{$listitem->getNetPriceFormated()}] <small>[{$edit->oxorder__oxcurrency->value}]</small></td>
            <td valign="top" class="[{$listclass}]">[{$listitem->getTotalNetPriceFormated()}] <small>[{$edit->oxorder__oxcurrency->value}]</small></td>
            [{else}]
            <td valign="top" class="[{$listclass}]">[{$listitem->getBrutPriceFormated()}] <small>[{$edit->oxorder__oxcurrency->value}]</small></td>
            <td valign="top" class="[{$listclass}]">[{$listitem->getTotalBrutPriceFormated()}] <small>[{$edit->oxorder__oxcurrency->value}]</small></td>
            [{/if}]
            [{/block}]
        </tr>
        [{if $blWhite == "2"}]
        [{assign var="blWhite" value=""}]
        [{else}]
        [{assign var="blWhite" value="2"}]
        [{/if}]
        [{/foreach}]
    </form>
</table>
[{/if}]


