( function ( $ ) {
    var itrCheck24RejectRequest = {
        _create: function() {
            var self = this,
                options = self.options,
                el      = self.element;

            $(el).click(function() {
                var id = $(this).data('id');
                var target = $('#reject-confirmation');
                $(target).find('input[name=oxid]').val(id);

                var clone = $(target).clone();
                clone.oxModalPopup({
                    target: target,
                    openDialog: true,
                    position: 'center',
                    width: 400
                });
                return false;
            });
        }
    };
    $.widget("ui.itrCheck24RejectRequest", itrCheck24RejectRequest );
})( jQuery );