[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]
[{assign var="where" value=$oView->getListFilter()}]

<link rel="stylesheet" href="[{$oViewConf->getModuleUrl('check24connector','out/src/css/check24connector.css')}]" type="text/css" />
<script src="[{$oViewConf->getBaseDir()}]/out/admin/src/js/libs/jquery.min.js"></script>
<script src="[{$oViewConf->getBaseDir()}]/out/admin/src/js/libs/jquery-ui.min.js"></script>
<script src="[{$oViewConf->getModuleUrl("check24connector", "out/src/js/widgets/itrCheck24RejectRequest.js")}]"></script>
<script src="[{$oViewConf->getModuleUrl("check24connector", "out/src/js/widgets/oxmodalpopup.js")}]"></script>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
    <!--
    function rejectRequest( sID)
    {
        blCheck = confirm("[{oxmultilang ident="CHECK24CONNECTOR_REQUEST_LIST_YOUWANTTOREJECT"}]");
        if( blCheck == true)
        {
            var oSearch = document.getElementById("search");
            oSearch.oxid.value=sID;
            oSearch.fnc.value='reject';

            var oTransfer = parent.edit.document.getElementById("transfer");
            oTransfer.oxid.value=sID;
            oTransfer.cl.value='[{$default_edit}]';

            //forcing edit frame to reload after submit
            top.forceReloadingEditFrame();

            oSearch.submit();
        }
    }
    function confirmRequest( sID)
    {
        blCheck = confirm("[{oxmultilang ident="CHECK24CONNECTOR_REQUEST_LIST_YOUWANTTOCONFIRM"}]");
        if( blCheck == true)
        {
            var oSearch = document.getElementById("search");
            oSearch.oxid.value=sID;
            oSearch.fnc.value='confirm';

            var oTransfer = parent.edit.document.getElementById("transfer");
            oTransfer.oxid.value=sID;
            oTransfer.cl.value='[{$default_edit}]';

            //forcing edit frame to reload after submit
            top.forceReloadingEditFrame();

            oSearch.submit();
        }
    }
    window.onload = function ()
    {
        top.reloadEditFrame();
        [{if $updatelist == 1}]
        top.oxid.admin.updateList('[{$oxid}]');
        [{/if}]

        $('.button-reject').itrCheck24RejectRequest();
    }
    //-->
</script>

<div id="liste">
    [{include file="check24connector_request_reject_popup.tpl"}]

    <form name="search" id="search" action="[{$oViewConf->getSelfLink()}]" method="post">
        [{include file="_formparams.tpl" cl="check24connector_requestlist" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
        <table cellspacing="0" cellpadding="0" border="0" width="100%">
            <colgroup>
                [{block name="admin_order_list_colgroup"}]
            <col width="20%">
            <col width="20%">
            <col width="20%">
            <col width="20%">
            <col width="18%">
            <col width="2%">
                [{/block}]
            </colgroup>
            <tr class="listitem">
                [{block name="check24connector_requestlist_filter"}]
                <td valign="top" class="listfilter first" height="20">
                    <div class="r1"><div class="b1">
                            <input class="listedit" type="text" size="15" maxlength="128" name="where[itrcheck24_orderchangerequest][oxtimestamp]" value="[{$where.itrcheck24_orderchangerequest.oxtimestamp|oxformdate}]" [{include file="help.tpl" helpid=order_date}]>
                    </div></div>
                </td>
                <td valign="top" class="listfilter" height="20">
                    <div class="r1"><div class="b1">
                            <input class="listedit" type="text" size="7" maxlength="128" name="where[itrcheck24_orderchangerequest][orderid]" value="[{$where.itrcheck24_orderchangerequest.orderid}]">
                    </div></div>
                </td>
                <td valign="top" class="listfilter" height="20">
                    <div class="r1"><div class="b1">
                            <select name="where[itrcheck24_orderchangerequest][action]" class="folderselect" onChange="document.search.submit();">
                                <option value="" style="color: #000000;">[{oxmultilang ident="ORDER_LIST_FOLDER_ALL"}]</option>
                                <option value="request" [{if $where.itrcheck24_orderchangerequest.action == "request"}]SELECTED[{/if}]>[{oxmultilang|oxtruncate:20:"..":true ident="CHECK24_ACTION_REQUEST_TITLE"}]</option>
                                <option value="confirmation" [{if $where.itrcheck24_orderchangerequest.action == "confirmation"}]SELECTED[{/if}]>[{oxmultilang|oxtruncate:20:"..":true ident="CHECK24_ACTION_CONFIRMATION_TITLE"}]</option>
                            </select>
                    </div></div>
                </td>
                 <td valign="top" class="listfilter" height="20">
                    <div class="r1"><div class="b1">
                            <select name="where[itrcheck24_orderchangerequest][response]" class="folderselect" onChange="document.search.submit();" >
                                <option value="" style="color: #000000;">[{oxmultilang ident="ORDER_LIST_FOLDER_ALL"}]</option>
                                <option value="0" [{if $where.itrcheck24_orderchangerequest.response == "0"}]SELECTED[{/if}]>[{oxmultilang|oxtruncate:20:"..":true ident="CHECK24_NO_RESPONSE_TITLE"}]</option>
                                <option value="1" [{if $where.itrcheck24_orderchangerequest.response == "1"}]SELECTED[{/if}]>[{oxmultilang|oxtruncate:20:"..":true ident="CHECK24_RESPONSE_CONFIRM_TITLE"}]</option>
                                <option value="2" [{if $where.itrcheck24_orderchangerequest.response == "2"}]SELECTED[{/if}]>[{oxmultilang|oxtruncate:20:"..":true ident="CHECK24_RESPONSE_REJECT_TITLE"}]</option>
                            </select>
                    </div></div>
                </td>
                <td valign="top" class="listfilter" height="20" colspan="2" nowrap>
                    <div class="r1"><div class="b1">
                            <div class="find"><input class="listedit" type="submit" name="submitit" value="[{oxmultilang ident="GENERAL_SEARCH"}]"></div>
                            <input class="listedit" type="text" size="50" maxlength="128" name="where[itrcheck24_orderchangerequest][reason]" value="[{$where.itrcheck24_orderchangerequest.reason}]">
                        </div></div>
                </td>
                [{/block}]
            </tr>
            <tr>
                [{block name="admin_order_list_sorting"}]
                <td class="listheader first" height="15">&nbsp;<a href="Javascript:top.oxid.admin.setSorting( document.search, 'itrcheck24_orderchangerequest', 'oxtimestamp', 'desc');document.search.submit();" class="listheader">[{oxmultilang ident="CHECK24_REQUEST_LIST_TIME"}]</a></td>
                <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'itrcheck24_orderchangerequest', 'orderid', 'asc');document.search.submit();" class="listheader">[{oxmultilang ident="CHECK24_REQUEST_LIST_ORDERID"}]</a></td>
                <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'itrcheck24_orderchangerequest', 'action', 'asc');document.search.submit();" class="listheader">[{oxmultilang ident="CHECK24_REQUEST_LIST_ACTION"}]</a></td>
                <td class="listheader" height="15"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'itrcheck24_orderchangerequest', 'response', 'asc');document.search.submit();" class="listheader">[{oxmultilang ident="CHECK24_REQUEST_LIST_RESPONSE"}]</a></td>
                <td class="listheader" height="15" colspan="2"><a href="Javascript:top.oxid.admin.setSorting( document.search, 'itrcheck24_orderchangerequest', 'reason', 'asc');document.search.submit();" class="listheader">[{oxmultilang ident="CHECK24_REQUEST_LIST_REASON"}]</a></td>
                [{/block}]
            </tr>

            [{assign var="blWhite" value=""}]
            [{assign var="_cnt" value=0}]
            [{foreach from=$mylist item=listitem}]
            [{assign var="_cnt" value=$_cnt+1}]
            <tr id="row.[{$_cnt}]">

                [{block name="admin_order_list_item"}]
                [{if $listitem->oxorder__oxstorno->value == 1}]
                [{assign var="listclass" value=listitem3}]
                [{else}]
                [{if $listitem->blacklist == 1}]
                [{assign var="listclass" value=listitem3}]
                [{else}]
                [{assign var="listclass" value=listitem$blWhite}]
                [{/if}]
                [{/if}]
                [{if $listitem->getId() == $oxid}]
                [{assign var="listclass" value=listitem4}]
                [{/if}]
                <td valign="top" class="[{$listclass}] order_time" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{$listitem->itrcheck24_orderchangerequest__oxid->value}]');" class="[{$listclass}]">[{$listitem->itrcheck24_orderchangerequest__oxtimestamp->value|oxformdate:'datetime':true}]</a></div></td>
                <td valign="top" class="[{$listclass}] payment_date" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->itrcheck24_orderchangerequest__oxid->value}]');" class="[{$listclass}]">[{$listitem->itrcheck24_orderchangerequest__orderid->value}]</a></div></td>
                <td valign="top" class="[{$listclass}] order_no" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->itrcheck24_orderchangerequest__oxid->value}]');" class="[{$listclass}]">[{$listitem->itrcheck24_orderchangerequest__action->value}]</a></div></td>
                <td valign="top" class="[{$listclass}] first_name" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->itrcheck24_orderchangerequest__oxid->value}]');" class="[{$listclass}]">[{$listitem->itrcheck24_orderchangerequest__response->value}]</a></div></td>
                <td valign="top" class="[{$listclass}] last_name" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->itrcheck24_orderchangerequest__oxid->value}]');" class="[{$listclass}]">[{$listitem->itrcheck24_orderchangerequest__reason->value}]</a></div></td>
                <td class="[{$listclass}]">
                    [{if !$readonly}]
                    <a href="Javascript:confirmRequest('[{$listitem->itrcheck24_orderchangerequest__oxid->value}]');" class="confirm" id="req_confirm.[{$_cnt}]" [{include file="help.tpl" helpid=item_confirmrequest}]></a>
                    <a data-id="[{$listitem->itrcheck24_orderchangerequest__oxid->value}]" class="delete button-reject" id="req_reject.[{$_cnt}]" [{include file="help.tpl" helpid=item_rejectrequest}]></a>
                    [{/if}]
                </td>
                [{/block}]
            </tr>
            [{if $blWhite == "2"}]
            [{assign var="blWhite" value=""}]
            [{else}]
            [{assign var="blWhite" value="2"}]
            [{/if}]
            [{/foreach}]
            [{include file="pagenavisnippet.tpl" colspan="6"}]
        </table>
    </form>
</div>

[{include file="check24connector_pagetabsnippet.tpl"}]

<script type="text/javascript">
    if (parent.parent)
    {   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
        parent.parent.sMenuItem    = "[{oxmultilang ident="ORDER_LIST_MENUITEM"}]";
        parent.parent.sMenuSubItem = "[{oxmultilang ident="ORDER_LIST_MENUSUBITEM"}]";
        parent.parent.sWorkArea    = "[{$_act}]";
        parent.parent.setTitle();
    }
</script>
<style>
    a.confirm {width: 15px;height: 15px;display:block;float:right;background:transparent url(../modules/itratos/check24connector/out/src/img/tick_round.png) 0 0 no-repeat;margin:0 1px;}
    a.delete {background: transparent url(../out/admin/src/bg/ico_delete.gif) 0 center no-repeat !important;}
</style>
<span class="popUpStyle" id="item_confirmrequest" style="position: absolute;visibility: hidden;top:0;left:0;">[{oxmultilang ident="ITRCHECK24_TOOLTIPS_ITEM_CONFIRM_REQUEST"}]</span>
<span class="popUpStyle" id="item_rejectrequest" style="position: absolute;visibility: hidden;top:0;left:0;">[{oxmultilang ident="ITRCHECK24_TOOLTIPS_ITEM_REJECT_REQUEST"}]</span>



