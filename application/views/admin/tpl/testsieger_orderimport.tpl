[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]


[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]


        <table cellspacing="0" cellpadding="0" border="0" style="width:98%;">
            <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post" style="padding: 0px;margin: 0px;height:0px;">
                [{$oViewConf->getHiddenSid()}]
                <input type="hidden" name="cl" value="testsieger_orderimport">
                <input type="hidden" name="fnc" value="">
                <tr><td colspan="2"> <img src="[{$ts_logo}]" style="margin-left: 8px"></td></tr>
                <tr>
                  <td valign="top" class="edittext" style="padding-top:10px;padding-left:10px;">

                    <fieldset>
                    <legend>Einstellungen</legend>
                    <table cellspacing="0" cellpadding="0" border="0">
                        <tr>
                          <td class="edittext">
                            Modul aktiv&nbsp;
                            <span title="Durch das aktivieren des Moduls werden noch keine Importe ausgel&ouml;st. Lösen sie die Importe gem&auml;&szlig; Anleitung aus, z.B. zeitgesteuert.">(info)</span>

                          </td>
                          <td class="edittext">
                            <select class="editinput" name="editval[testsieger_active]">
                                <option value="1" [{if 1==$testsieger_active}]selected="selected[{/if}]">Ja</option>
                                <option value="0" [{if 0==$testsieger_active}]selected="selected[{/if}]">Nein</option>
                            </select>
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                            FTP User&nbsp;
                            <span title="Bsp.: partner12345">(info)</span>
                          </td>
                          <td class="edittext">
                            <input type="text" class="editinput" size="32" maxlength="32" id="oLockTarget" name="editval[testsieger_ftpuser]" value="[{$testsieger_ftpuser}]">
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                            FTP Pass&nbsp;
                            <span title="Bsp.: ab12xy">(info)</span>
                          </td>
                          <td class="edittext">
                            <input type="text" class="editinput" size="32" maxlength="32" id="oLockTarget" name="editval[testsieger_ftppass]" value="[{$testsieger_ftppass}]">
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                            FTP Host&nbsp;
                            <span title="Bsp.: partnerftp.testsieger.de">(info)</span>
                          </td>
                          <td class="edittext">
                            <input type="text" class="editinput" size="32" maxlength="32" id="oLockTarget" name="editval[testsieger_ftphost]" value="[{$testsieger_ftphost}]">
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                            FTP Port&nbsp;
                            <span title="Bsp.: 44021">(info)</span>
                          </td>
                          <td class="edittext">
                            <input type="text" class="editinput" size="32" maxlength="32" id="oLockTarget" name="editval[testsieger_ftpport]" value="[{$testsieger_ftpport}]">
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                            Versandart&nbsp;
                            <span title="&Uuml;berschreibt die importierte Versandart. Leer lassen, um Versandart aus der Importdatei zu &uuml;bernehmen. Bsp.: oxidstandard">(info)</span>
                          </td>
                          <td class="edittext">
                            <input type="text" class="editinput" size="32" maxlength="32" id="oLockTarget" name="editval[testsieger_shippingtype]" value="[{$testsieger_shippingtype}]">
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                            Standard Zahlungsart&nbsp;
                            <span title="Die Zahlungsart, welche benutzt wird wenn die Zahlungsart von CHECK24 keiner Zahlungsart von Oxid zugewiesen werden kann.">(info)</span>
                          </td>
                          <td class="edittext">
                              <select class="editinput" name="editval[testsieger_paymenttype_fallback]">
                                  [{if '' == $testsieger_paymenttype_fallback}]
                                  <option value="">Bitte ausw&auml;hlen!</option>
                                  [{/if}]
                                  [{foreach from=$paymentlist item=payment key=paymentid}]
                                   <option value="[{$payment[0]}]" [{if $testsieger_paymenttype_fallback == $payment[0]}] selected="selected"[{/if}]>[{$payment[1]}]</option>
                                  [{/foreach}]
                              </select>
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                            CHECK24 Zahlungsart&nbsp;
                            <span title="Wird die Zahlung über CHECK24.de abgewickelt, soll sie mit dieser Bezahlart im Oxid-System erscheinen.
Sofern Sie keine Zahlungen über CHECK24.de abwickeln lassen, wählen Sie eine beliebige Zahlungsart.">(info)</span>
                          </td>
                          <td class="edittext">
                              <select class="editinput" name="editval[testsieger_paymenttype_ts]">
                                  [{if '' == $testsieger_paymenttype_ts}]
                                  <option value="">Bitte ausw&auml;hlen!</option>
                                  [{/if}]
                                  [{foreach from=$paymentlist item=payment key=paymentid}]
                                   <option value="[{$payment[0]}]" [{if $testsieger_paymenttype_ts == $payment[0]}] selected="selected"[{/if}]>[{$payment[1]}]</option>
                                  [{/foreach}]
                              </select>
                          </td>
                        </tr>

                        <tr>
                          <td class="edittext">
                            Bestellbest&auml;tigung<br>per E-Mail&nbsp;
                            <span title="Achtung!
Durch das Aktivieren erhält der Kunde eine automatische Bestellbest&auml;tigung bei Bestelleingang. Diese ist NICHT IDENTISCH mit der Oxid-eigenen Bestellbest&auml;tigungsmail, sondern kann in der Datei testsieger_resend_confirm.tpl angepasst werden. Vor der Verwendung pr&uuml;fen!">(info)</span>

                          </td>
                          <td class="edittext">
                            <select class="editinput" name="editval[testsieger_sendorderconf]" onchange="alert('Durch das aktivieren erhält der Kunde eine automatische Bestellbestaetigung. \nDiese ist NICHT IDENTISCH mit der Oxid-eigenen Bestellbestaetigungsmail, sondern kann in der Datei testsieger_resend_confirm.tpl angepasst werden. \nVor der Verwendung pruefen!');">
                                <option value="0" [{if 1!=$testsieger_sendorderconf}]selected="selected[{/if}]">Nein</option>
                                <option value="1" [{if 1==$testsieger_sendorderconf}]selected="selected[{/if}]">Ja</option>
                            </select>
                          </td>
                        </tr>


                        <tr>
                          <td class="edittext">
                            Lagerbestand<br>reduzieren&nbsp;
                            <span title=
"Wenn aktiviert, wird der Lagerbestand der bestellten Artikel um die bestellte Menge reduziert. Die Bestellung wird auch dann importiert, wenn dies zu negativem Lagerbestand führt.">(info)</span>

                          </td>
                          <td class="edittext">
                            <select class="editinput" name="editval[testsieger_reducestock]" >
                                <option value="0" [{if 1!=$testsieger_reducestock}]selected="selected[{/if}]">Nein</option>
                                <option value="1" [{if 1==$testsieger_reducestock}]selected="selected[{/if}]">Ja</option>
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
                <fieldset>
                <legend>Aktionen</legend>
                    <input type="submit" class="edittext" id="importNowButton" value="Bestellungen jetzt importieren"
                    onclick="window.open('[{$iframeurl}]', 'Bestell-Import');">
                </fieldset>




                  </td>
            <!-- Anfang rechte Seite -->
                  <td valign="top" class="edittext" align="left" style="width:100%;height:99%;padding-left:25px;padding-bottom:30px;padding-top:10px;">
                        <h2>Log-Datei</h2>
                        <input type="submit" class="edittext" id="deleteLogs" value="Logdatei l&ouml;schen" onclick="Javascript:document.myedit.fnc.value='deletelog'"><br>
                        <br>
                        <div style="width:100%; height: 320px; overflow: auto">[{$ts_logs}]</div>
                  </td>
            <!-- Ende rechte Seite -->
                </tr>
            </form>
        </table>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
