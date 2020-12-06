(function($) {
  'use strict';

  $(function() {

    // Ajax Login
	 $('.loginpress-login-widget form').submit( function(e) {

     e.preventDefault();
     $( '.loginpress_widget_error' ).remove();

     var el       = $(this);
     var user_log = el.find('input[name="log"]').val();
	   var user_pwd = el.find('input[name="pwd"]').val();
     var remember = '';

     if ( ! user_log ) {

	    	el.prepend('<p class="loginpress_widget_error">' + loginpress_widget_params.empty_username + '</p>');
	    	return false;
	    }
	    if ( ! user_pwd ) {

			   el.prepend('<p class="loginpress_widget_error">' + loginpress_widget_params.empty_password + '</p>');
	    	return false;
	    }

      // Check for SSL/FORCE SSL LOGIN
  		if ( loginpress_widget_params.force_ssl_admin == 1 && loginpress_widget_params.is_ssl == 0 ) {
        return true;
      }

      if ( el.find( 'input[name="rememberme"]:checked' ).length > 0 ) {
	    	remember = el.find( 'input[name="rememberme"]:checked' ).val();
	    }

  		$.ajax({
  			url: loginpress_widget_params.ajaxurl,
  			data: {
    			action: 		   'loginpress_widget_login_process',
    			user_login: 	  user_log,
    			user_password: 	user_pwd,
    			remember: 		  remember,
    			redirect_to:	  el.find( 'input[name="redirect_to"]' ).val()
    		},
  			type: 'POST',
  			success: function( response ) {

          var result = jQuery.parseJSON( response );          

          if ( result.success == 1 ) {

  					window.location = result.redirect;
  				} else {

            if( result.invalid_username ) {

              el.prepend('<p class="loginpress_widget_error">' + loginpress_widget_params.invalid_username + '</p>');
            } else if ( result.incorrect_password ) {

              el.prepend('<p class="loginpress_widget_error">' + loginpress_widget_params.invalid_password + '</p>');
            } else if ( result.loginpress_use_email ) {

              el.prepend('<p class="loginpress_widget_error">' + loginpress_widget_params.invalid_email + '</p>');
            } else {
							//LLLA Error show on wrong Email or Password
							el.prepend('<p class="loginpress_widget_error">' + result.llla_error + '</p>');				
            }            
            // //Redirect when locked out
						if (result.llla_error.toLowerCase().includes('locked')) {
							window.location.assign(window.location.href);
						}
  				}
  			}
  		});
    //  console.log(remember);
	 });
  });
})(jQuery); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
