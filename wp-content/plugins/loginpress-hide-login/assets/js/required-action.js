jQuery(document).ready(function($) {


  $("#loginpress_reset_login_slug").on( 'click', function() {

    // e.preventDefault();

    var _nonce = $('.loginpress_reset_login_slug_nonce').val();

    $.ajax({
      url  : ajaxurl,
      type : 'post',
      data :{
        action  : 'reset_login_slug',
        security: _nonce,
      },
      success : function( res ) {

        location.reload();
      },
      error: function(xhr, textStatus, errorThrown) {
        console.log('Ajax Not Working');
      }
    }); // !Ajax.
  });


  /**
  * [loginpress_hidelogin_link]
  * @return {[string]}
  * @since 1.0.0
  * @version 1.1.4
  */
  function loginpress_hidelogin_link() {

    var hideLoginString = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    var result = "";
    while ( result.length < 20 ) {
      result += hideLoginString.charAt( Math.floor( Math.random() * hideLoginString.length ) );
    }

    return result;
  }

  // Change slug on click Random button.
  $("#loginpress_create_new_hidelogin_slug").on( "click", function(event) {
    event.preventDefault();
    var slug = loginpress_hidelogin_link();
    $("#loginpress_hidelogin\\[rename_login_slug\\]").val(slug);

  });

  $("#wpb-loginpress_hidelogin\\[is_rename_send_email\\]").on('click', function(){
    if ($('#wpb-loginpress_hidelogin\\[is_rename_send_email\\]').is(":checked")) {
      $('tr.rename_email_send_to').show();
    } else {
      $('tr.rename_email_send_to').hide();
    }
  });

  $(window).on('load', function() {
    if ($('#wpb-loginpress_hidelogin\\[is_rename_send_email\\]').is(":checked")) {
      $('tr.rename_email_send_to').show();
    }
  });

});
