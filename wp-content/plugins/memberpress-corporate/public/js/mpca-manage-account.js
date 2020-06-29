// IIFE
(function($, window, document) {

  // $ is now locally scoped and available

  $(function() {

    // DOM is now ready

    'use strict';

    //Init all the suggest autocomplete fields
    $(document).ready(function() {
      mpca_setup_clipboardjs();

      $('.mepr_suggest_user').suggest(
        mpcaAjax.ajaxurl + '?action=mepr_user_search', {
          delay: 500,
          minchars: 2
        }
      );
    });

    $('#mpca_import_sub_accounts form').on('submit', function(e) {
      $('.mpca-loading-gif').show();
      $('#mpca_import_sub_accounts form input[type=submit]').attr('disabled',true);
    });

    $('#mpca_sub_account_search').on('keyup',function(e) {
      if(e.which == 13) {
        e.preventDefault();
        var href = window.location.href.replace(/([\?&])search=[^\?&]*/gi,'');
        href = href.replace(/([\?&])currpage=[^\?&]*/gi,'');
        var delim = href.match(/\?/) ? '&' : '?';

        if($(this).val()) {
          window.location.href = href + delim + 'search=' + $(this).val();
        }
        else {
          window.location.href = href;
        }
      }
    });

    $('.mpca-remove-sub-account').on('click', function(e) {
      e.preventDefault();

      if(confirm(mpcaAjax.confirmMsg)) {
        var args = {
          'action': 'mpca_remove_sub_account',
          'ca': $(this).data('ca'),
          'sa': $(this).data('sa')
        };

        $.post(mpcaAjax.ajaxurl,args,function() {
          //$('#mpca-sub-accounts-row-'+args.sa).remove();
          // Fixes reload problem with Chrome
          window.location.replace(location.href);
        });
      }
    });

    $('#mpca-add-sub-user-btn').click(function() {
      jQuery('#mpca-add-sub-user-form').slideToggle();
    });

  });
}(jQuery, window, document));
