(function ($) {
  $(document).ready(function() {
    function wafp_get_post_args() {
      var listname = $('#wafpaweber_opt_in').attr('data-listname');
      var args = { listname: listname,
                   redirect: 'http://www.aweber.com/thankyou-coi.htm?m=text',
                   meta_adtracking: 'affiliate-royale',
                   meta_message: 1,
                   meta_forward_vars: 1,
                   email: $('#user_email').val()
                 };
      var name = '';
      if( $('#user_first_name').val() != '' ) {
        name = $('#user_first_name').val();

        if( $('#user_last_name').val() != '' ) {
          name = name + ' ' + $('#user_last_name').val();
        }

        args['name'] = name;
      }

      return args;
    }

    $('#wafp_registerform input').keypress( function(e) {
      if(e.which==13) {
        e.preventDefault();

        // We're bypassing the aweber api due to its complexity and
        // opting for a straight js post from the client side now
        if( $('#wafpaweber_opt_in').is(':checked') && $('#user_email').val()!='' ) {
          args = wafp_get_post_args();
          $.post( "https://www.aweber.com/scripts/addlead.pl", args ).complete( function() {
            $('#wafp_registerform').submit();
          });
        }
      }
    });

    $('#wafp_registerform input[type=submit]').click( function(e) {
      e.preventDefault();

      // We're bypassing the aweber api due to its complexity and
      // opting for a straight js post from the client side now
      if( $('#wafpaweber_opt_in').is(':checked') && $('#user_email').val()!='' ) {
        args = wafp_get_post_args();
        $.post( "https://www.aweber.com/scripts/addlead.pl", args ).complete( function() {
          $('#wafp_registerform').submit();
        });
      } else {
        $('#wafp_registerform').submit(); //We still need to submit the form yolo!
      }
    });
  });
})(jQuery);
