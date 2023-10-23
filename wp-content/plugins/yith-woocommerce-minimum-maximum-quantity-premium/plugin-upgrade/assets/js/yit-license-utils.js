(function($){
    var license_notice  = $( '#yith-license-notice' );

        $('body').on( 'click', '#yith-license-notice button.notice-dismiss', function(){
            $.ajax({
                type: 'POST',
                url: typeof ajaxurl != 'undefined' ? ajaxurl : yith_license_utils.ajax_url,
                data: {
                    action:     'yith_license_banner_dismiss',
                    _wpnonce:   license_notice.data( 'nonce' )
                }
            });
        });

        $(document).on( 'click', '#yith-license-where-find-these', function(ev){
          ev.preventDefault();
          yith.ui.modal(
            {
              title  : yith_license_utils.modal.title,
              content: yith_license_utils.modal.content,
              footer: yith_license_utils.modal.footer,
              width: 960,
              allowWpMenu: false,
              closeWhenClickingOnOverlay: true,
              classes: {
                title: 'yith-license-modal-title',
                content: 'yith-license-modal-content',
                footer: 'yith-license-modal-footer'
              }
            }
          );
        } );
})(jQuery);
