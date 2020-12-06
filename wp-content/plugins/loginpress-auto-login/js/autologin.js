(function($) {

  'use strict';

  /**
  * [loginpress_create_new_link]
  * @return {[string]}
  * @since 1.0.0
  */
  function loginpress_create_new_link() {
    var autoLoginString = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    var result = "";
    while ( result.length < 30 ) {
      result += autoLoginString.charAt( Math.floor( Math.random() * autoLoginString.length ) );
    }

    return result;
  }

  $(function() {

    // Apply ajax on click new button.
    $(document).on( "click", ".loginpress-new-link", function(event) {
      event.preventDefault();

      var code   = loginpress_create_new_link();
      var el     = $(this);
      var tr     = el.closest('tr');
      var id     = tr.attr("data-autologin");
      var _nonce = tr.find('.loginpress__user-autologin_nonce').val();

      $.ajax({

        url : ajaxurl,
        type: 'POST',
        data: 'code=' + code + '&id=' + id + '&action=loginpress_autologin' + '&security=' + _nonce,
        beforeSend: function() {
          el.closest('tr').find( '.loginpress_autologin_code p' ).html('');
          el.closest('tr').find( '.autologin-sniper' ).show();
          el.closest('tr').find( '.loginpress-new-link' ).attr( "disabled", "disabled" );
        },
        success: function( response ) {
          el.closest('tr').find( '.autologin-sniper' ).hide();
          el.closest('tr').find( '.loginpress-new-link' ).removeAttr( "disabled" );
          el.closest('tr').find( '.loginpress_autologin_code p' ).html(response);
        }
      }); // !Ajax.
    }); // !click .loginpress-new-link.

    // Apply ajax on click delete button.
    $(document).on( "click", ".loginpress-del-link", function(event) {

      event.preventDefault();

      var el     = $(this);
      var tr     = el.closest('tr');
      var id     = tr.attr("data-autologin");
      var _nonce = tr.find('.loginpress__user-autologin_nonce').val();

      $.ajax({

        url : ajaxurl,
        type: 'POST',
        data: 'id=' + id + '&action=loginpress_autologin_delete' + '&security=' + _nonce,
        beforeSend: function() {
          tr.find( '.loginpress_autologin_code p' ).html('');
          tr.find( '.autologin-sniper' ).show();
          tr.find( '.loginpress-new-link' ).attr( "disabled", "disabled" );
          tr.find( '.loginpress-del-link' ).attr( "disabled", "disabled" );
        },
        success: function( response ) {
          $( '#loginpress_user_id_' + id ).remove();
        }
      }); // !Ajax.
    }); // !click .loginpress-del-link.

  });
})(jQuery); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
