(function ($) {
    $('a[rel="abuseFormPrettyPhoto[report_vendor_abuse]"]').off().prettyPhoto({
        hook                 : 'rel',
        social_tools         : false,
        theme                : 'pp_woocommerce',
        horizontal_padding   : 20,
        opacity              : 0.8,
        deeplinking          : false,
        keyboard_shortcuts   : true,
        changepicturecallback: function () {
            $('.report-abuse-form').on('submit', function (e) {
                e.preventDefault();
                var t        = $(this),
                    antispam = t.find('.report_abuse_anti_spam').val() == '' ? true : false;

                if (antispam) {
                    $.ajax({
                        url    : report_abuse.ajaxurl,
                        data   : t.serialize(),
                        beforeSend: function(){
                            $( '.report-abuse-message').remove();

                            $('.pp_content_container').block({
                                message: null,
                                overlayCSS: {
                                    opacity: 0.6
                                }
                            });
                        },
                        success: function (data) {
                            var container = '<div class="report-abuse-message %class%">%message%</div>';
                            $('.pp_content_container').unblock();


                            if( data == 'empty_value' ){
                                data = container.replace( '%class%', report_abuse.classes.empty ).replace( '%message%', report_abuse.messages.empty );
                            }

                            if( data == false || data == 'spam' ){
                                data = container.replace( '%class%', report_abuse.classes.failed ).replace( '%message%', report_abuse.messages.failed );
                            }

                            if (data == true) {
                                data = container.replace('%class%', report_abuse.classes.success).replace('%message%', report_abuse.messages.success);
                                t.fadeOut('slow');
                                t.get(0).reset();
                                setTimeout(function () {
                                    $.prettyPhoto.close();
                                }, 1800);
                            }

                            $(data).insertBefore(t);
                        }
                    });
                }
            });
        }
    });
})(jQuery);
