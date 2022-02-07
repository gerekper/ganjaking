<?php
/**
 * Allow access file.
 *
 * @package allow access
 * */

$pathallow = preg_replace( '/wp-content(?!.*wp-content).*/', '', __DIR__ );
include( $pathallow . 'wp-load.php' );
$redirect_url = @get_bloginfo( 'url' ) . '/wp-admin/admin.php?page=wc-settings&tab=integration&section=help-scout';
$code = isset( $_REQUEST['code'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['code'] ) ) : '';

if ( isset( $code ) && ! empty( $code ) ) {
	$help_scout_settings = get_option( 'woocommerce_help-scout_settings' );

	update_option( 'wc_helpscout_code', $code );

	// define params for constants to use for api post.
	define( 'WC_HELPSCOUT_CLIENT_APP_KEY', $help_scout_settings['app_key'] );
	define( 'WC_HELPSCOUT_CLIENT_API_SECRET', $help_scout_settings['app_secret'] );
	define( 'WC_HELPSCOUT_ACCESS_CODE', $code );
	define( 'WC_HELPSCOUT_TOKEN_URI', 'https://api.helpscout.net/v2/oauth2/token' );

	if ( $code ) {
		// build up the params for generating token.
		$params = array(
			'client_id'             => WC_HELPSCOUT_CLIENT_APP_KEY,
			'client_secret'         => WC_HELPSCOUT_CLIENT_API_SECRET,
			'grant_type'            => 'authorization_code',
			'code'                  => WC_HELPSCOUT_ACCESS_CODE,
		);

		$params = http_build_query( $params );

		// generate token post through api.
		$response  = wp_remote_post(
			WC_HELPSCOUT_TOKEN_URI,
			array(
				'body' => $params,
			)
		);
		if ( is_array( $response ) ) {

			if ( '200' == $response['response']['code'] ) {
				$token_data = json_decode( $response['body'] );
				$expire_timestamp = time() + $token_data->expires_in;
				// update token related data in option table.
				// update_option('helpscout_app_invalide',0);.
				update_option( 'helpscout_access_token', $token_data->access_token );
				update_option( 'helpscout_access_refresh_token', $token_data->refresh_token );
				update_option( 'helpscout_access_token_type', $token_data->token_type );
				update_option( 'helpscout_expires_in', $expire_timestamp );
				echo '</br>----------------------------------------</br>';
				echo '<b>HelpScout has successfully been integrated to your Woocommerce website.</b>';
				echo '</br>----------------------------------------</br>';
				?>
							<script>
								setTimeout(function(){
									window.location = "<?php echo esc_url_raw( $redirect_url ); ?>";
								}, 2500);
							</script> 
						<?php
			} else {
					// update_option('helpscout_app_invalide',1);.
					echo '</br>----------------------------------------</br>';
					echo '<b>Invalid APP Key or APP Secret Key. Please recheck and submit again.</b>';
					echo '</br>----------------------------------------</br>';

				?>
						<script>
							setTimeout(function(){
								window.location = "<?php echo esc_url_raw( $redirect_url ); ?>";
							}, 2500);
						</script> 
					<?php
			}
		} else {
			echo '</br>----------------------------------------</br>';
			echo 'Request timed out, please try again later';
			echo '</br>----------------------------------------</br>';
			?>
					<script>
					setTimeout(function(){
							window.location = "<?php echo esc_url_raw( $redirect_url ); ?>";
					}, 2500);
					</script> 
				<?php
		}
	}
} else {
	// define params for constants to use for api post.
	define( 'WC_HELPSCOUT_CLIENT_APP_KEY', $help_scout_settings['app_key'] );
	define( 'WC_HELPSCOUT_CLIENT_API_SECRET', $help_scout_settings['app_secret'] );
	define( 'WC_HELPSCOUT_ACCESS_CODE', get_option( 'wc_helpscout_code' ) );
	define( 'WC_HELPSCOUT_TOKEN_URI', 'https://api.helpscout.net/v2/oauth2/token' );

	if ( get_option( 'helpscout_access_refresh_token' ) ) {

		// build up the params for regenerating token.
		$params = array(
			'client_id'             => WC_HELPSCOUT_CLIENT_APP_KEY,
			'client_secret'         => WC_HELPSCOUT_CLIENT_API_SECRET,
			'grant_type'            => 'refresh_token',
			'refresh_token'         => WC_HELPSCOUT_ACCESS_CODE,
		);
		$params = http_build_query( $params );
		// generate token post through api.
		$response = wp_remote_post(
			WC_HELPSCOUT_TOKEN_URI,
			array(
				'body' => $params,
			)
		);
		if ( '200' == $response['response']['code'] ) {
			$token_data = json_decode( $response['body'] );
			$expire_timestamp = time() + $token_data->expires_in;
			// update token related data in option table.
			update_option( 'helpscout_access_token', $token_data->access_token );
			update_option( 'helpscout_access_refresh_token', $token_data->refresh_token );
			update_option( 'helpscout_access_token_type', $token_data->token_type );
			update_option( 'helpscout_expires_in', $expire_timestamp );
			?>

					<script>
						window.location = "<?php echo esc_url_raw( $redirect_url ); ?>";
					</script>

				<?php } else {

						echo '</br>----------------------------------------</br>';
						echo '<b>Invalid APP Key or APP Secret. Please recheck and submit again.</b>';
						echo '</br>----------------------------------------</br>';

				}
	}
}
?>
