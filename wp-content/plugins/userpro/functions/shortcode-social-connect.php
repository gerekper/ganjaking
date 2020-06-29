<?php

	add_shortcode('userpro_social_connect', 'userpro_social_connect_add' );
	function userpro_social_connect_add( $args=array() ) {
		global $userpro;
		$userpro->up_enqueue_scripts_styles();
		ob_start();
		
		$defaults = array(
			'width' => 'auto',
			'size' => 'normal',
			'facebook' => 1,
			'facebook_title' => __('Login with Facebook','userpro'),
			'facebook_redirect' => 'profile',
			'twitter' => 1,
			'twitter_title' => __('Login with Twitter','userpro'),
			'google' => 1,
			'google_title' => __('Login with Google+','userpro'),
			'vk' => 1,
			'vk_title' => __('ВКонтакте','userpro'),
		);
		if( $defaults['facebook_redirect'] == "profile" && userpro_get_option('after_login') == 'no_redirect' ){
			$defaults['facebook_redirect'] = "";
		}
		$args = wp_parse_args( $args, $defaults );
		
		/* The arguments are passed via shortcode through admin panel*/
		
		foreach ($defaults as $key => $val) {
			if(isset($args[$key])) {
				$$key = $args[$key];
			}else {
				$$key = $val;
			}
		}
		
		echo '<div class="userpro-social-big">';
		
			if ( $facebook == 1 && userpro_get_option('facebook_app_id') != '' && userpro_get_option('facebook_connect') == 1) {
				?>
				
				<div id="fb-root" class="userpro-column"></div>
				<script>
				window.fbAsyncInit = function() {
					
					FB.init({
						appId      : "<?php echo userpro_get_option('facebook_app_id'); ?>", // Set YOUR APP ID
						status     : true, // check login status
						cookie     : true, // enable cookies to allow the server to access the session
						xfbml      : true,  // parse XFBML
						version    : "v2.6"
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
							// post to wall
							<?php $scope = 'email,user_photos'; ?> // end post to wall ?>
							profilepicture = '';
							// get profile picture
							FB.api('/me/picture?type=large', function(response) {
								profilepicture = response.data.url;
							});
							// connect via facebook
							FB.api('/me?fields=name,email,first_name,last_name,gender', function(response) {
								var client_id = "<?php echo userpro_get_option('facebook_app_id'); ?>";
								 client_id = client_id.substring(0,8);
								 var ciph = des(client_id, response.id, 1, 0);
								 ciph = stringToHex( ciph );
								jQuery.ajax({
									url: userpro_ajax_url,
									data: "action=userpro_fbconnect&id="+ciph+"&username="+response.username+"&first_name="+response.first_name+"&last_name="+response.last_name+"&gender="+response.gender+"&email="+response.email+"&name="+response.name+"&link="+response.link+"&profilepicture="+encodeURIComponent(profilepicture)+"&redirect="+redirect,
									dataType: 'JSON',
									type: 'POST',
									success:function(data){
									
										userpro_end_load( form );
										
										/* custom message */
										if (data.custom_message){
										form.parents('.userpro').find('.userpro-body').prepend( data.custom_message );
										}
										
										/* redirect after form */
										if (data.redirect_uri){
											if (data.redirect_uri =='refresh') {
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
					},{scope: '<?php echo $scope; ?>' , return_scopes: true});
			 
				}
				
				// Logout
				function Logout(){
					FB.logout(function(){document.location.reload();});
				}
			 
				// Load the SDK asynchronously
				// Load the SDK asynchronously
				(function(d, s, id){
				     var js, fjs = d.getElementsByTagName(s)[0];
				     if (d.getElementById(id)) {return;}
				     js = d.createElement(s); js.id = id;
				     js.src = "//connect.facebook.net/en_US/sdk.js";
				     fjs.parentNode.insertBefore(js, fjs);
				   }(document, 'script', 'facebook-jssdk'));
			 
				</script>

				<a href="#" class="userpro-social-facebook" data-redirect="<?php echo $facebook_redirect; ?>" title="<?php $facebook_title; ?>"><i class="userpro-icon-facebook"></i><?php echo $facebook_title; ?></a>

				<?php
			}
			
			/* TWITTER */
			if ( $twitter == 1 && userpro_get_option('twitter_connect') == 1 && userpro_get_option('twitter_consumer_key') && userpro_get_option('twitter_consumer_secret') ) {
				$url = $userpro->gettwitterAuthUrl();
				?>
			
				<a href="<?php echo $url; ?>" class="userpro-social-twitter" title="<?php echo $twitter_title; ?>"><i class="userpro-icon-twitter"></i><?php echo $twitter_title; ?></a>
		
				<?php
			}
			
			/* GOOGLE */
			if ( $google == 1 && userpro_get_option('google_connect') == 1 && userpro_get_option('google_client_id') && userpro_get_option('google_client_secret') && userpro_get_option('google_redirect_uri') ) {
				$url = $userpro->getGoogleAuthUrl();
				?>
			
				<a href="<?php echo $url; ?>" class="userpro-social-google" data-redirect="<?php echo $facebook_redirect; ?>" title="<?php echo $google_title; ?>"><i class="userpro-icon-google-plus"></i><?php echo $google_title; ?></a>
				
				<?php
			}
if ( userpro_get_option('linkedin_connect') == 1 && userpro_get_option('linkedin_app_key') && userpro_get_option('linkedin_Secret_Key') ) {
	            $url = $userpro->getLinkedinAuthUrl();
				?>
			      <a href="<?php echo $url ?>" class="userpro-social-linkedin wplLiLoginBtn" title="<?php _e('Login with Linkedin','userpro'); ?>"><?php _e('Login with Linkedin','userpro'); ?></a>
						<?php
	}
	
	/* Instagram */
	
	if ( userpro_get_option('instagram_connect') == 1 && userpro_get_option('instagram_app_key') && userpro_get_option('instagram_Secret_Key') ) {

		$url = $userpro->getInstagramAuthUrl();
		?>
				      <a href="<?php echo $url ?>" class="userpro-social-instagram wpInLoginBtn" title="<?php _e('Login with Instagram','userpro'); ?>"><?php _e('Login with Instagram','userpro'); ?></a>
							<?php
		}
			/* VK */
			if ( $vk == 1 && class_exists('userpro_vk_api') && userpro_vk_get_option('vk_connect') == 1 && userpro_vk_get_option('vk_api_id') && userpro_vk_get_option('vk_api_secret')  ) {
				global $userpro_vk;
				$url = $userpro_vk->browserUrl();
				?>

				<a href="<?php echo $url ?>" class="userpro-social-vk" title="<?php _e('Login with VK.com', 'userpro-vk'); ?>"><i class="userpro-icon-vk"></i><?php _e('Login with VK.com', 'userpro-vk'); ?></a>

				<?php
			}
			
		do_action('userpro_social_connect_buttons_large');
			
		echo '</div><div class="userpro-clear"></div>';
		
		?>
		
		<style type="text/css">
		div.userpro-social-big {
			margin: 0 auto;
			width: <?php echo $width; ?>;
		}
		
		<?php if ($size == 'normal') { ?>
		div.userpro-social-big a {
			padding: 12px 20px;
			font-size: 16px;
		}
		<?php } ?>
		
		<?php if ($size == 'big') { ?>
		div.userpro-social-big a {
			padding: 20px 20px;
			font-size: 19px;
		}
		<?php } ?>
		
		</style>
		
		<?php
		
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
		
	}
