( function( $ ) {

     oxModalPopup = {
            options: {
                width        : 687,
                height       : 'auto',
                modal        : true,
                resizable    : true,
                zIndex       : 10000,
                position     : 'center',
                draggable    : true,
                target       : '#popup',
                openDialog   : false,
                loadUrl      : false,
                loadCallback : false,
                beforeLoadCallback : false,
                closeCallback : false,
                closeButton  : "img.closePop, button.closePop"
            },

            _create: function() {

                var self = this,
                options = self.options,
                el      = self.element;

                if (options.openDialog) {

                    if (options.loadUrl){
                        $(options.target).load(options.loadUrl, options.loadCallback);
                    }

                    self.openDialog(options.target, options);

                } else {

                    el.click(function(){

                        if (options.loadUrl){
                            if(options.beforeLoadCallback) {
                                options.beforeLoadCallback();
                            }
                            $(options.target).load(options.loadUrl, options.loadCallback);
                        }

                        self.openDialog(options.target, options);

                        return false;
                    });
                }

                $(self.options.closeButton, $( options.target ) ).click(function(){
                    $( options.target ).dialog("close");
                    return false;
                });
            },

            openDialog: function (target, options) {
                $(target).dialog({
                    width     : options.width,
                    height    : options.height,
                    modal     : options.modal,
                    resizable : options.resizable,
                    zIndex    : options.zIndex,
                    position  : options.position,
                    draggable : options.draggable,

                    open: function(event, ui) {
                        $('div.ui-dialog-titlebar').remove();
                    },
                    close      : options.closeCallback
                });
            }
    };

    $.widget("ui.oxModalPopup", oxModalPopup );

} )( jQuery );