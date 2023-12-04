<?php
/**
 * General Setting Form
 *
 * @package UAEL
 */

use UltimateElementor\Classes\UAEL_Helper;
use UltimateElementor\Classes\UAEL_Maxmind_Database;

$settings = UAEL_Helper::get_integrations_options();

$languages = UAEL_Helper::get_google_map_languages();

if ( isset( $_REQUEST['uael_admin_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_REQUEST['uael_admin_nonce'] ), 'uael_admin_nonce' ) ) {
	$is_saved = ( isset( $_REQUEST['message'] ) && 'saved' === $_REQUEST['message'] ) ? true : false;
}

$google_status = '';
$yelp_status   = '';

// Action when settings saved.
if ( $is_saved ) {
	UAEL_Helper::get_api_authentication();
}

if ( isset( $settings['google_places_api'] ) && ! empty( $settings['google_places_api'] ) ) {
	$google_status = get_option( 'uael_google_api_status' );
}
if ( isset( $settings['yelp_api'] ) && ! empty( $settings['yelp_api'] ) ) {
	$yelp_status = get_option( 'uael_yelp_api_status' );
}
?>
<div class="uael-container uael-integration-wrapper">
	<form method="post" class="wrap clear" action="" >
		<div class="wrap uael-addon-wrap clear">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Integrations', 'uael' ); ?></h1>
			<div id="poststuff">
				<div id="post-body" class="columns-1">
					<div id="post-body-content">
						<div class="uael-integration-form-wrap">
							<div class="widgets postbox">
								<div class="inside">
									<div class="form-wrap">
										<div class="form-field">
											<label for="uael-integration-google-api-key" class="uael-integration-heading"><?php esc_html_e( 'Google Map API Key', 'uael' ); ?></label>
											<p class="install-help uael-p"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>
											<?php
											esc_attr_e( 'This setting is required if you wish to use Google Map in your website.', 'uael' );

											if ( UAEL_Helper::is_internal_links() ) {

												$a_tag_open  = '<a target="_blank" rel="noopener" href="' . esc_url( UAEL_DOMAIN . 'docs/create-google-map-api-key/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin' ) . '">';
												$a_tag_close = '</a>';

												printf(
													/* translators: %1$s: a tag open. */
													esc_attr__( ' Need help to get Google map API key? Read %1$s this article %2$s.', 'uael' ),
													wp_kses_post( $a_tag_open ),
													wp_kses_post( $a_tag_close )
												);
											}
											?>
											</p>
											<input type="text" name="uael_integration[google_api]" id="uael-integration-google-api-key" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['google_api'] ); ?>">
										</div>
									</div>
								</div>
							</div>
							<div class="widgets postbox">
								<div class="inside">
									<div class="form-wrap">
										<div class="form-field">
											<label for="uael-integration-google-language" class="uael-integration-heading"><?php esc_html_e( 'Google Map Localization Language', 'uael' ); ?></label>
											<p class="install-help uael-p"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>  <?php esc_html_e( 'This setting sets localization language to google map. The language affects the names of controls, copyright notices, driving directions, and control labels.', 'uael' ); ?></p>
											<p class="uael-p">
											<?php
											if ( UAEL_Helper::is_internal_links() ) {

												$a_tag_open  = '<a href="' . esc_url( UAEL_DOMAIN . 'docs/how-to-display-uaels-google-maps-widget-in-your-local-language/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin' ) . '" target="_blank" rel="noopener">';
												$a_tag_close = '</a>';

												printf(
													/* translators: %1$s: a tag open. */
													esc_attr__( 'Need help to understand this feature? Read %1$s this article %2$s.', 'uael' ),
													wp_kses_post( $a_tag_open ),
													wp_kses_post( $a_tag_close )
												);
											}
											?>
											</p>
											<select name="uael_integration[language]" id="uael-integration-google-language" class="placeholder placeholder-active">
												<option value=""><?php esc_html_e( 'Default', 'uael' ); ?></option>
											<?php foreach ( $languages as $key => $value ) { ?>
												<?php
												$selected = '';
												if ( $key === $settings['language'] ) {
													$selected = 'selected="selected" ';
												}
												?>
												<option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $value ); ?></option>
											<?php } ?>
											</select>
										</div>
									</div>
								</div>
							</div>

							<div class="widgets postbox">
								<div class="inside">
									<div class="form-wrap">
										<div class="form-field">
											<label for="uael-integration-google-places-key" class="uael-integration-heading"><?php esc_html_e( 'Business Reviews - Google Places API Key', 'uael' ); ?></label>
											<p class="install-help uael-p"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>
											<?php
											esc_attr_e( 'This setting is required if you wish to use Google Places Reviews in your website.', 'uael' );

											if ( UAEL_Helper::is_internal_links() ) {

												$a_tag_open  = '<a target="_blank" rel="noopener" href="' . esc_url( UAEL_DOMAIN . 'docs/get-google-places-api-key/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin' ) . '">';
												$a_tag_close = '</a>';

												printf(
													/* translators: %1$s: a tag open. */
													esc_attr__( ' Need help to get Google Places API key? Read %1$s this article %2$s.', 'uael' ),
													wp_kses_post( $a_tag_open ),
													wp_kses_post( $a_tag_close )
												);
											}
											?>
											</p>
											<p class="uael-billing-acc-warning">
												<span class="dashicons dashicons-warning"></span>
												<span class="uael-google-api-billing"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>
												<?php
													$a_tag_open  = '<a target="_blank" rel="noopener" href="' . esc_url( 'https://console.cloud.google.com/projectselector2/billing/enable' ) . '">';
													$a_tag_close = '</a>';

													printf(
														/* translators: %1$s: a tag open. */
														esc_attr__( 'Google now requires an active billing account associated with your API Key. Click %1$s here %2$s to enable billing.', 'uael' ),
														wp_kses_post( $a_tag_open ),
														wp_kses_post( $a_tag_close )
													);
													?>
												</span>
											</p>
											<input type="text" name="uael_integration[google_places_api]" id="uael-integration-google-places-key" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['google_places_api'] ); ?>">
											<?php if ( 'yes' === $google_status && $is_saved ) { ?>
												<span class="uael-response-success"><?php esc_html_e( 'Your API key authenticated successfully!', 'uael' ); ?></span>
											<?php } elseif ( 'no' === $google_status ) { ?>
													<span class="uael-response-warning"><?php esc_html_e( 'Entered API key is invalid', 'uael' ); ?></span>
											<?php } elseif ( 'exceeded' === $google_status && $is_saved ) { ?>
													<span class="uael-google-error-response">
														<span class="uael-response-warning">
														<?php
														printf(
															/* translators: 1: <b> 2: </b> */
															esc_html__( '%1$sGoogle Error Message:%2$s', 'uael' ),
															'<b>',
															'</b>'
														);
														?>
														</span>
														<?php
														$a_tag_open  = '<a href="http://g.co/dev/maps-no-account" target="_blank" rel="noopener">';
														$a_tag_close = '</a>';

															printf(
																/* translators: %1$s command. */
																esc_attr__( 'You have exceeded your daily request quota for this API. If you did not set a custom daily request quota, verify your project has an active billing account.</br>Click %1$s here %2$s to enable billing.', 'uael' ),
																wp_kses_post( $a_tag_open ),
																wp_kses_post( $a_tag_close )
															);
														?>
													</span>
											<?php } ?>

										</div>
									</div>
								</div>
							</div>

							<div class="widgets postbox">
								<div class="inside">
									<div class="form-wrap">
										<div class="form-field">
											<label for="uael-integration-yelp-api-key" class="uael-integration-heading"><?php esc_html_e( 'Business Reviews - Yelp API Key', 'uael' ); ?></label>
											<p class="install-help uael-p"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>
											<?php
											esc_attr_e( 'This setting is required if you wish to use Yelp Reviews in your website.', 'uael' );

											if ( UAEL_Helper::is_internal_links() ) {

												$a_tag_open  = '<a target="_blank" rel="noopener" href="' . esc_url( UAEL_DOMAIN . 'docs/get-yelp-api-key/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin' ) . '">';
												$a_tag_close = '</a>';

												printf(
													/* translators: %1$s: a tag open. */
													esc_attr__( ' Need help to get Yelp API key? Read %1$s this article %2$s.', 'uael' ),
													wp_kses_post( $a_tag_open ),
													wp_kses_post( $a_tag_close )
												);
											}
											?>
											</p>
											<input type="text" name="uael_integration[yelp_api]" id="uael-integration-yelp-api-key" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['yelp_api'] ); ?>">
											<?php if ( 'yes' === $yelp_status && $is_saved ) { ?>
												<div class="uael-response-success"><?php esc_html_e( 'Your API key authenticated successfully!', 'uael' ); ?></div>
											<?php } elseif ( 'no' === $yelp_status ) { ?>
													<div class="uael-response-warning"><?php esc_html_e( 'Entered API key is invalid', 'uael' ); ?></div>
											<?php } ?>

										</div>
									</div>
								</div>
							</div>

							<div class="widgets postbox">
								<div class="inside">
									<div class="form-wrap">
										<div class="form-field">
											<label class="uael-integration-heading"><?php esc_html_e( 'Setup reCAPTCHA v3', 'uael' ); ?></label>
											<p class="install-help uael-p"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>
											<?php
												$a_tag_open  = '<a target="_blank" rel="noopener" href="' . esc_url( 'https://www.google.com/recaptcha/intro/v3.html' ) . '">';
												$a_tag_close = '</a>';

												printf(
													/* translators: %1$s: a tag open. */
													esc_attr__( '%1$s reCAPTCHA v3 %2$s is a free service by Google that protects your website from spam and abuse. It does this while letting your valid users pass through with ease.', 'uael' ),
													wp_kses_post( $a_tag_open ),
													wp_kses_post( $a_tag_close )
												);
												?>
											</p>
											<p class="install-help uael-p">
											<?php
											if ( UAEL_Helper::is_internal_links() ) {

												$a_tag_open  = '<a target="_blank" rel="noopener" href="' . esc_url( UAEL_DOMAIN . 'docs/user-registration-form-with-recaptcha/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin' ) . '">';
												$a_tag_close = '</a>';

												printf(
													/* translators: %1$s: a tag open. */
													esc_attr__( 'Read %1$s this article %2$s to learn more.', 'uael' ),
													wp_kses_post( $a_tag_open ),
													wp_kses_post( $a_tag_close )
												);
											}
											?>
											</p>
											<label for="uael-recaptcha-v3-key" class="uael-integration-heading"><?php esc_html_e( 'Site key', 'uael' ); ?></label>
											<input type="text" name="uael_integration[recaptcha_v3_key]" id="uael-recaptcha-v3-key" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['recaptcha_v3_key'] ); ?>">
											<br/>
											<br/>
											<label for="uael-recaptcha-v3-secretkey" class="uael-integration-heading"><?php esc_html_e( 'Secret key', 'uael' ); ?></label>
											<input type="text" name="uael_integration[recaptcha_v3_secretkey]" id="uael-recaptcha-v3-secretkey" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['recaptcha_v3_secretkey'] ); ?>">
											<br/>
											<br/>
											<label for="uael-recaptcha-v3-score" class="uael-integration-heading"><?php esc_html_e( 'Score Threshold', 'uael' ); ?></label>
											<input type="text" name="uael_integration[recaptcha_v3_score]" id="uael-recaptcha-v3-score" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['recaptcha_v3_score'] ); ?>">
											<?php
												echo esc_attr_e( 'Score threshold should be a value between 0 and 1, default: 0.5', 'uael' );
											?>
										</div>
									</div>
								</div>
							</div>

							<div class="widgets postbox">
								<div class="inside">
									<div class="form-wrap">
										<div class="form-field">
											<label class="uael-integration-heading"><?php esc_html_e( 'Login Form - Google Client ID', 'uael' ); ?></label>
											<p class="install-help uael-p"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>
											<?php
											esc_attr_e( 'This setting is required if you wish to use Login with Google in your website.', 'uael' );

											if ( UAEL_Helper::is_internal_links() ) {

												$a_tag_open  = '<a target="_blank" rel="noopener" href="' . esc_url( UAEL_DOMAIN . 'docs/create-google-client-id-for-login-form-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin' ) . '">';
												$a_tag_close = '</a>';

												printf(
													/* translators: %1$s: a tag open. */
													esc_attr__( ' Read %1$s this article %2$s.', 'uael' ),
													wp_kses_post( $a_tag_open ),
													wp_kses_post( $a_tag_close )
												);
											}
											?>
											</p>
											<label for="uael-google-client-id" class="uael-integration-heading"><?php esc_html_e( 'Google Client ID', 'uael' ); ?></label>
											<input type="text" name="uael_integration[google_client_id]" id="uael-google-client-id" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['google_client_id'] ); ?>">
										</div>
									</div>
								</div>
							</div>

							<div class="widgets postbox">
								<div class="inside">
									<div class="form-wrap">
										<div class="form-field">
											<label class="uael-integration-heading"><?php esc_html_e( 'Login Form - Facebook App Details', 'uael' ); ?></label>
											<p class="install-help uael-p"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>
											<?php
											esc_attr_e( 'This setting is required if you wish to use Login with Facebook in your website.', 'uael' );

											if ( UAEL_Helper::is_internal_links() ) {

												$a_tag_open  = '<a target="_blank" rel="noopener" href="' . esc_url( UAEL_DOMAIN . 'docs/create-facebook-app-id-for-login-form-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin' ) . '">';
												$a_tag_close = '</a>';

												printf(
													/* translators: %1$s: a tag open. */
													esc_attr__( ' Need help to get Facebook App Details? Read %1$s this article %2$s.', 'uael' ),
													wp_kses_post( $a_tag_open ),
													wp_kses_post( $a_tag_close )
												);
											}
											?>
											</p>
											<label for="uael-facebook-app-id" class="uael-integration-heading"><?php esc_html_e( 'Facebook App ID', 'uael' ); ?></label>
											<input type="text" name="uael_integration[facebook_app_id]" id="uael-facebook-app-id" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['facebook_app_id'] ); ?>">
											<br/>
											<br/>
											<label for="uael-facebook-secret-key" class="uael-integration-heading"><?php esc_html_e( 'Facebook App Secret', 'uael' ); ?></label>
											<input type="text" name="uael_integration[facebook_app_secret]" id="uael-facebook-secret-key" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['facebook_app_secret'] ); ?>">
										</div>
									</div>
								</div>
							</div>
							<div class="widgets postbox">
								<div class="inside">
									<div class="form-wrap">
										<div class="form-field">
											<label class="uael-integration-heading"><?php esc_html_e( 'Social Share - Facebook Access Token', 'uael' ); ?></label>
											<p class="install-help uael-p"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>
											<?php
												$a_tag_open  = '<a target="_blank" rel="noopener" href="' . esc_url( UAEL_DOMAIN . 'docs/display-share-count-for-facebook/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin' ) . '">';
												$a_tag_close = '</a>';

												printf(
													/* translators: %1$s: a tag open. */
													esc_attr__( 'This setting is required if you wish to fetch share count of your post/page from Facebook. Need help? Read %1$s this article %2$s.', 'uael' ),
													wp_kses_post( $a_tag_open ),
													wp_kses_post( $a_tag_close )
												);
												?>
											</p>
											<label for="uael-share-button" class="uael-integration-heading"><?php esc_html_e( 'Facebook Access Token', 'uael' ); ?></label>
											<input type="text" name="uael_integration[uael_share_button]" id="uael-share-button-id" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['uael_share_button'] ); ?>">
											<?php
											$response = UAEL_Helper::facebook_token_authentication();
											if ( 200 === $response && $is_saved && isset( $response ) ) {
												?>
												<span class="uael-response-success"><?php esc_html_e( 'Access Token authenticated successfully!', 'uael' ); ?></span>
											<?php } elseif ( $is_saved && '' !== $settings['uael_share_button'] ) { ?>
													<span class="uael-response-warning"><?php esc_html_e( 'Invalid Access Token', 'uael' ); ?></span>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>

							<div class="widgets postbox">
								<div class="inside">
									<div class="form-wrap">
										<div class="form-field">
											<label class="uael-integration-heading"><?php esc_html_e( 'MaxMind Geolocation', 'uael' ); ?></label>
											<p class="install-help uael-p"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>
												<?php
												esc_attr_e( 'An integration for utilizing MaxMind to do Geolocation lookups. Please note that this integration will only do Country lookups.', 'uael' );

												if ( UAEL_Helper::is_internal_links() ) {

													$a_tag_open  = '<a target="_blank" rel="noopener" href="' . esc_url( UAEL_DOMAIN . 'docs/display-conditions-geolocation/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin' ) . '">';
													$a_tag_close = '</a>';

													printf(
													/* translators: %1$s: a tag open. */
														esc_attr__( ' Need help? Read %1$s this article %2$s.', 'uael' ),
														wp_kses_post( $a_tag_open ),
														wp_kses_post( $a_tag_close )
													);
												}
												?>
											</p>
											<label for="uael_maxmind_geolocation_license_key" class="uael-integration-heading"><?php esc_html_e( 'MaxMind License Key', 'uael' ); ?></label>
											<input type="text" name="uael_integration[uael_maxmind_geolocation_license_key]" id="uael_maxmind_geolocation_license_key" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['uael_maxmind_geolocation_license_key'] ); ?>">
											<br/>
											<br/>
											<label for="uael_maxmind_geolocation_db_path" class="uael-integration-heading"><?php esc_html_e( 'Database File Path', 'uael' ); ?></label>
											<?php $geolite_db = new UAEL_Maxmind_Database(); ?>
											<input type="text" name="uael_integration[uael_maxmind_geolocation_db_path]" id="uael_maxmind_geolocation_db_path" class="placeholder placeholder-active" value="<?php echo esc_attr( $geolite_db->get_uael_database_path() ); ?>" disabled>
										</div>
									</div>
								</div>
							</div>

							<div class="widgets postbox">
								<div class="inside">
									<div class="form-wrap">
										<div class="form-field">
											<label class="uael-integration-heading"><?php esc_html_e( 'Instagram Feed - Instagram Basic Display API Details', 'uael' ); ?></label>
											<p class="install-help uael-p"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>
												<?php
												esc_attr_e( 'This setting is required if you wish to use Instagram Feed in your website.', 'uael' );

												if ( UAEL_Helper::is_internal_links() ) {

													$a_tag_open  = '<a target="_blank" rel="noopener" href="' . esc_url( UAEL_DOMAIN . 'docs/instagram-feed-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin' ) . '">';
													$a_tag_close = '</a>';

													printf(
													/* translators: %1$s: a tag open. */
														esc_attr__( ' Need help to get Instagram App Details? Read %1$s this article %2$s.', 'uael' ),
														wp_kses_post( $a_tag_open ),
														wp_kses_post( $a_tag_close )
													);
												}
												?>
											</p>
											<label for="uael-instagram-app-id" class="uael-integration-heading"><?php esc_html_e( 'Instagram App ID', 'uael' ); ?></label>
											<input type="text" name="uael_integration[instagram_app_id]" id="uael-instagram-app-id" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['instagram_app_id'] ); ?>">
											<br/>
											<br/>
											<label for="uael-instagram-secret-key" class="uael-integration-heading"><?php esc_html_e( 'Instagram App Secret', 'uael' ); ?></label>
											<input type="text" name="uael_integration[instagram_app_secret]" id="uael-instagram-secret-key" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['instagram_app_secret'] ); ?>">
											<br/>
											<br/>
											<label for="uael-instagram-access-token" class="uael-integration-heading"><?php esc_html_e( 'Instagram Access Token', 'uael' ); ?></label>
											<input type="text" name="uael_integration[instagram_app_token]" id="uael-instagram-access-token" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['instagram_app_token'] ); ?>">
											<br/>
											<br/>
											<button type="button" class="uael-instagram-access-token-generator"><?php esc_html_e( 'Refresh Access Token', 'uael' ); ?></button>
											<br/>
											<span class="uael-insta-response-msg uael-response-success uael-insta-hide-response-msg"><?php esc_html_e( 'Your access token refreshed successfully.', 'uael' ); ?></span>
											<span class="uael-insta-response-msg uael-response-warning uael-insta-hide-response-msg"><?php esc_html_e( 'Error while refreshing token from instagram or may be the existing token in not valid.', 'uael' ); ?></span>
										</div>
									</div>
								</div>
							</div>

						</div>
						<div class="widgets postbox">
							<div class="inside">
								<div class="form-wrap">
									<div class="form-field">
										<label class="uael-integration-heading"><?php esc_html_e( 'Twitter Feed', 'uael' ); ?></label>
										<p class="install-help uael-p"><strong><?php esc_html_e( 'Note:', 'uael' ); ?></strong>
											<?php
											esc_attr_e( 'To display your Twitter Feed on your website,', 'uael' );

											$a_tag_open_1  = '<a target="_blank" rel="noopener" href="https://developer.twitter.com/en/portal/products/basic">';
											$a_tag_close_1 = '</a>';

											printf(
											/* translators: %1$s: a tag open. */
												esc_attr__( ' you\'ll need to acquire %1$s Twitter\'s basic plan %2$s and obtain your Twitter Consumer Key and Consumer Secret Key.', 'uael' ),
												wp_kses_post( $a_tag_open_1 ),
												wp_kses_post( $a_tag_close_1 )
											);

											if ( UAEL_Helper::is_internal_links() ) {

												$a_tag_open_2  = '<a target="_blank" rel="noopener" href="' . esc_url( UAEL_DOMAIN . 'docs/twitter-feed-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin' ) . '">';
												$a_tag_close_2 = '</a>';

												printf(
												/* translators: %1$s: a tag open. */
													esc_attr__( ' For guidance on obtaining these keys, please refer to this %1$s article %2$s.', 'uael' ),
													wp_kses_post( $a_tag_open_2 ),
													wp_kses_post( $a_tag_close_2 )
												);
											}
											?>
										</p>
										<label for="uael_twitter_feed_consumer_key" class="uael-integration-heading"><?php esc_html_e( 'Consumer Key', 'uael' ); ?></label>
										<input type="text" name="uael_integration[uael_twitter_feed_consumer_key]" id="uael_twitter_feed_consumer_key" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['uael_twitter_feed_consumer_key'] ); ?>">
										<br/>
										<br/>
										<label for="uael_twitter_feed_consumer_secret" class="uael-integration-heading"><?php esc_html_e( 'Consumer Secret', 'uael' ); ?></label>
										<input type="text" name="uael_integration[uael_twitter_feed_consumer_secret]" id="uael_twitter_feed_consumer_secret" class="placeholder placeholder-active" value="<?php echo esc_attr( $settings['uael_twitter_feed_consumer_secret'] ); ?>">
									</div>
								</div>
							</div>
						</div>
						<?php submit_button( __( 'Save Changes', 'uael' ), 'uael-save-integration-options button-primary button button-hero' ); ?>
						<?php wp_nonce_field( 'uael-integration', 'uael-integration-nonce' ); ?>
					</div>
				</div>
				<!-- /post-body -->
				<br class="clear">
			</div>
		</div>
	</form>
</div>
