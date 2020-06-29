<?php

add_action('userpro_inside_form_submit','userpro_social_connect');
function userpro_social_connect($array){
	global $userpro;

	// only in login/register
	if ($array['template'] == 'login' || $array['template'] == 'register' ) {
		if( $array['facebook_redirect'] == "profile" && userpro_get_option('after_login') == 'no_redirect' ){
			$array['facebook_redirect'] = "";
		}
		echo '<div class="userpro-social-connect">';

		if (userpro_get_option('facebook_app_id') != '' && userpro_get_option('facebook_connect') == 1) {
			?>

            <div id="fb-root" class="userpro-column"></div>
            <script>

                window.fbAsyncInit = function() {

                    FB.init({
                        appId      : "<?php echo userpro_get_option('facebook_app_id'); ?>", // Set YOUR APP ID
                        status     : true, // check login status
                        cookie     : true, // enable cookies to allow the server to access the session
                        xfbml      : true,  // parse XFBML
                        version    : 'v2.6'
                    });

                    FB.Event.subscribe('auth.authResponseChange', function(response)
                    {
                        if (response.status === 'connected')
                        {
                            //SUCCESS
                        }
                        else if (response.status === 'not_authorized')
                        {
                            //FAILED

                        } else
                        {
                            //UNKNOWN ERROR
                        }
                    });

                };

                // Login user
                function Login(element){

                    var form = jQuery(element).parents('.userpro').find('form');
                    userpro_init_load( form );

                    if ( element.data('redirect')) {
                        var redirect = element.data('redirect');
                    } else {
                        var redirect = '';
                    }

                    FB.login(function(response) {
                        if (response.authResponse){
                            profilepicture = '';
                            // post to wall
							<?php $scope = 'email'; ?> // end post to wall ?>

                            // connect via facebook
                            FB.api('/me?fields=name,email,first_name,last_name,gender,picture.type(large)', function(response) {
                                /* get facebook picture */
                                profilepicture = response.picture.data.url;
                                var client_id = "<?php echo userpro_get_option('facebook_app_id'); ?>";
                                client_id = client_id.substring(0,8);
                                var ciph = des(client_id, response.id, 1, 0);
                                ciph = stringToHex( ciph );
                                if(typeof(response.email) =="undefined"){
                                    alert("Cannot Sign in! Looks like some error with Facebook email id");}
                                jQuery.ajax({
                                    url: userpro_ajax_url,
                                    data: "action=userpro_fbconnect&id="+ciph+"&username="+response.username+"&first_name="+response.first_name+"&last_name="+response.last_name+"&gender="+response.gender+"&email="+response.email+"&name="+response.name+"&link="+response.link+"&profilepicture="+encodeURIComponent(profilepicture)+"&redirect="+redirect,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    success:function(data){
                                        if(data.error_msg){
                                            alert(data.error_msg);
                                        }
                                        if(typeof(data.paypal_form)!=null)
                                        {
                                            jQuery('body').append(data.paypal_form);
                                            jQuery('#paypalform').submit();
                                        }
                                        userpro_end_load( form );

                                        /* custom message */
                                        if (data.custom_message){
                                            form.parents('.userpro').find('.userpro-body').prepend( data.custom_message );
                                        }

                                        /* redirect after form */
                                        if (data.redirect_uri){
                                            if (data.redirect_uri =='refresh' && data.paypal_form==null) {
                                                var redirect = jQuery(location).attr('href');
                                                document.location.href=redirect;
                                            } else {
                                                document.location.href=data.redirect_uri;
                                            }
                                        }

                                    },
                                    error: function(){
                                        alert('Something wrong happened.');
                                    }
                                });

                            });

                            // cancelled
                        } else {
                            alert( 'Unauthorized or cancelled' );
                            userpro_end_load( form );
                        }
                    },{scope: '<?php echo $scope; ?>', return_scopes: true});

                }

                // Logout
                function Logout(){
                    FB.logout(function(){document.location.reload();});
                }

                // Load the SDK asynchronously
                (function(d, s, id){
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) {return;}
                    js = d.createElement(s); js.id = id;
                    js.src = "//connect.facebook.net/en_US/sdk.js";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
            </script>

            <a href="#" class="userpro-social-facebook userpro-tip" data-redirect="<?php echo $array['facebook_redirect']; ?>" title="<?php _e('Login with Facebook','userpro'); ?>"></a>
			<?php
		}

		/* TWITTER */
		if ( userpro_get_option('twitter_connect') == 1 && userpro_get_option('twitter_consumer_key') && userpro_get_option('twitter_consumer_secret') ) {
			$url = $userpro->gettwitterAuthUrl();
			?>

            <a href="<?php echo $url; ?>" class="userpro-social-twitter userpro-tip" title="<?php _e('Login with Twitter','userpro'); ?>"></a>

			<?php
		}

		/* GOOGLE */
		if ( userpro_get_option('google_connect') == 1 && userpro_get_option('google_client_id') && userpro_get_option('google_client_secret') && userpro_get_option('google_redirect_uri') ) {
			$url = $userpro->getGoogleAuthUrl();
			?>

            <a href="<?php echo $url; ?>" class="userpro-social-google userpro-tip" data-redirect="<?php echo $array['facebook_redirect']; ?>" title="<?php _e('Login with Google+','userpro'); ?>"></a>

			<?php
		}
		if ( userpro_get_option('linkedin_connect') == 1 && userpro_get_option('linkedin_app_key') && userpro_get_option('linkedin_Secret_Key') ) {

		    $url = $userpro->getLinkedinAuthUrl();

			?><a href="<?php echo $url ?>" class="userpro-social-linkedin userpro-tip wplLiLoginBtn" title="<?php _e('Login with Linkedin','userpro'); ?>"></a>

			<?php
		}

		/* INSTAGRAM */
		if ( userpro_get_option('instagram_connect') == 1 && userpro_get_option('instagram_app_key') && userpro_get_option('instagram_Secret_Key') ) {

			$url = $userpro->getInstagramAuthUrl();

			?>
            <a href="<?php echo $url ?>"  class="userpro-social-instagram userpro-tip wpInLoginBtn" title="<?php _e('Login with Instagram','userpro'); ?>"></a>

			<?php
		}
		/* MORE NETWORKS SHOULD BE ADDED BELOW */
		do_action('userpro_social_connect_buttons');

		echo '</div><div class="userpro-clear"></div>';

	}
}
