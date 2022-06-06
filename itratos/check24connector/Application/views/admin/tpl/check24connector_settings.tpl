[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

        <table cellspacing="0" cellpadding="0" border="0" style="width:98%;">
            <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post" style="padding: 0px;margin: 0px;height:0px;">
                [{$oViewConf->getHiddenSid()}]
                <input type="hidden" name="cl" value="check24connector_settings">
                <input type="hidden" name="fnc" value="">
                <input type="hidden" name="cancellationid" value="">
                <tr><td colspan="2"> <img src="[{$ts_logo}]" style="margin-left: 8px"></td></tr>
                <tr>
                  <td valign="top" class="edittext" style="padding-top:10px;padding-left:10px;">

                    <fieldset>
                    <legend>[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_HEADER_TEXT"}]</legend>
                    <table cellspacing="0" cellpadding="0" border="0">
                        <tr>
                          <td class="edittext">
                            [{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_MODULE_ACTIVE"}]&nbsp;
                            <span class="infoNotice" title="[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_MODULE_ACTIVE_INFO"}]"></span>
                          </td>
                          <td class="edittext">
                            <select class="editinput" name="editval[testsieger_active]">
                                <option value="1" [{if 1==$testsieger_active}]selected="selected[{/if}]">[{oxmultilang ident="GENERAL_YES"}]</option>
                                <option value="0" [{if 0==$testsieger_active}]selected="selected[{/if}]">[{oxmultilang ident="GENERAL_NO"}]</option>
                            </select>
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                              [{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_FTP_USER"}]&nbsp;
                            <span title="[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_FTP_USER_INFO"}]" class="infoNotice"></span>
                          </td>
                          <td class="edittext">
                            <input type="text" class="editinput" size="32" maxlength="32" id="oLockTarget" name="editval[testsieger_ftpuser]" value="[{$testsieger_ftpuser}]">
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                              [{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_FTP_PASSWORD"}]&nbsp;
                            <span title="[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_FTP_PASSWORD_INFO"}]" class="infoNotice"></span>
                          </td>
                          <td class="edittext">
                            <input type="text" class="editinput" size="32" maxlength="32" id="oLockTarget" name="editval[testsieger_ftppass]" value="[{$testsieger_ftppass}]">
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                              [{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_FTP_HOST"}]&nbsp&nbsp;
                            <span class="infoNotice" title="[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_FTP_HOST_INFO"}]"></span>
                          </td>
                          <td class="edittext">
                            <input type="text" class="editinput" size="32" maxlength="64" id="oLockTarget" name="editval[testsieger_ftphost]" value="[{$testsieger_ftphost}]">
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                              [{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_FTP_PORT"}]&nbsp;
                            <span class="infoNotice" title="[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_FTP_PORT_INFO"}]"></span>
                          </td>
                          <td class="edittext">
                            <input type="text" class="editinput" size="32" maxlength="32" id="oLockTarget" name="editval[testsieger_ftpport]" value="[{$testsieger_ftpport}]">
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                              [{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_DELIVERY_METHOD"}]&nbsp;
                            <span class="infoNotice" title="[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_DELIVERY_METHOD_INFO"}]"></span>
                          </td>
                          <td class="edittext">
                            <input type="text" class="editinput" size="32" maxlength="32" id="oLockTarget" name="editval[testsieger_shippingtype]" value="[{$testsieger_shippingtype}]">
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                              [{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_PAYMENT_METHOD_STANDARD"}]&nbsp;
                            <span class="infoNotice" title="[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_PAYMENT_METHOD_STANDARD_INFO"}]"></span>
                          </td>
                          <td class="edittext">
                              <select class="editinput" name="editval[testsieger_paymenttype_fallback]">
                                  [{if '' == $testsieger_paymenttype_fallback}]
                                  <option value="">[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_PLEASE_SELECT"}]</option>
                                  [{/if}]
                                  [{foreach from=$paymentlist item=payment key=paymentid}]
                                   <option value="[{$payment[0]}]" [{if $testsieger_paymenttype_fallback == $payment[0]}] selected="selected"[{/if}]>[{$payment[1]}]</option>
                                  [{/foreach}]
                              </select>
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                              [{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_PAYMENT_METHOD_CHECK24"}]&nbsp;
                            <span class="infoNotice" title="[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_PAYMENT_METHOD_CHECK24_INFO"}]"></span>
                          </td>
                          <td class="edittext">
                              <select class="editinput" name="editval[testsieger_paymenttype_ts]">
                                  [{if '' == $testsieger_paymenttype_ts}]
                                  <option value="">[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_PLEASE_SELECT"}]</option>
                                  [{/if}]
                                  [{foreach from=$paymentlist item=payment key=paymentid}]
                                   <option value="[{$payment[0]}]" [{if $testsieger_paymenttype_ts == $payment[0]}] selected="selected"[{/if}]>[{$payment[1]}]</option>
                                  [{/foreach}]
                              </select>
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                              [{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_ORDER_CONFIRMATION_BY_EMAIL"}]&nbsp;
                            <span class="infoNotice" title="[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_ORDER_CONFIRMATION_BY_EMAIL_INFO"}]"></span>
                          </td>
                          <td class="edittext">
                            <select class="editinput" name="editval[testsieger_sendorderconf]" onchange="alert('[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_ORDER_CONFIRMATION_ALERT"}]');">
                                <option value="0" [{if 1!=$testsieger_sendorderconf}]selected="selected[{/if}]">[{oxmultilang ident="GENERAL_NO"}]</option>
                                <option value="1" [{if 1==$testsieger_sendorderconf}]selected="selected[{/if}]">[{oxmultilang ident="GENERAL_YES"}]</option>
                            </select>
                          </td>
                        </tr>


                        <tr>
                          <td class="edittext">
                             [{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_REDUCE_INVENTORY"}]&nbsp;
                            <span class="infoNotice" title="[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_REDUCE_INVENTORY_INFO"}]"></span>

                          </td>
                          <td class="edittext">
                            <select class="editinput" name="editval[testsieger_reducestock]" >
                                <option value="0" [{if 1!=$testsieger_reducestock}]selected="selected[{/if}]">[{oxmultilang ident="GENERAL_NO"}]</option>
                                <option value="1" [{if 1==$testsieger_reducestock}]selected="selected[{/if}]">[{oxmultilang ident="GENERAL_YES"}]</option>
                            </select>
                          </td>
                        </tr>

                      <tr>
                        <td class="edittext" colspan="2"><br><br>
                        <input type="submit" class="edittext" id="saveButton" value="[{ oxmultilang ident="ARTICLE_MAIN_SAVE" }]" onclick="Javascript:document.myedit.fnc.value='savesettings'">
                        </td>
                      </tr>
                    </table>
                  </fieldset>
<br><br>

                </td>

            <!-- Anfang rechte Seite -->
                  <td valign="top" class="edittext" align="left" style="width:100%;height:99%;padding-left:25px;padding-bottom:30px;padding-top:10px;">
                        <h2>[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_LOGDATA"}]</h2>
                        <input type="submit" class="edittext" id="deleteLogs" value="[{oxmultilang ident="ITRCHECK24CONNECTOR_SETTINGS_LOGDATA_DELETE"}]" onclick="Javascript:document.myedit.fnc.value='deletelog'"><br>
                        <br>
                        <div style="width:100%; height: 320px; overflow: auto">[{$ts_logs}]</div>
                  </td>
            <!-- Ende rechte Seite -->
                </tr>
            </form>
        </table>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
