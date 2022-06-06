<div id="reject-confirmation" class="popupBox corners FXgradGreyLight glowShadow">
    <div class="modal-header">
        <div class="header-text">
            [{oxmultilang ident="CHECK24CONNECTOR_REQUEST_LIST_YOUWANTTOREJECT"}]
            </br></br>
            [{oxmultilang ident="TSCHECK24CONNECTOR_ORDERCHANGE_REQUEST_DENY_REASON" suffix="COLON"}]
        </div>
        <button type="button" class="closePop" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form name="request_reject" id="request_reject" action="[{$oViewConf->getSelfLink()}]" method="post">
        <textarea class="reject-reason" name="reject_reason" placeholder="[{oxmultilang ident="ITRCHECK24CONNECTOR_REJECT_REASON_PLACEHOLDER"}]"></textarea>
        <input type="hidden" name="cl" value="check24connector_requestlist">
        <input type="hidden" name="fnc" value="reject">
        <input type="hidden" name="oxid" value="">
        <div class="action">
            <button class="closePop" type="button">[{oxmultilang ident="ITRCHECK24CONNECTOR_REJECT_CANCEL"}]</button>
            <button class="btn" type="submit">[{oxmultilang ident="ITRCHECK24CONNECTOR_REJECT_SUBMIT"}]</button>
        </div>
    </form>
</div>