<?php

/**
 * Following variables are passed into the template:
 *   $key_valid
 *   $connection_error
 *   $urls (urls of all dashboard menu items)
 *
 **/

/** @var WPMUDEV_Dashboard_Sui_Page_Urls $urls */
/** @var bool $connection_error */
/** @var bool $key_valid */

$register_url      = 'https://premium.wpmudev.org/#trial';
$reset_url         = 'https://premium.wpmudev.org/wp-login.php?action=lostpassword';
$account_url       = 'https://premium.wpmudev.org/hub/account/';
$hosting_url       = 'https://premium.wpmudev.org/hub/hosting/';
$trial_info_url    = 'https://premium.wpmudev.org/manuals/how-free-trials-work/';
$websites_url      = 'https://premium.wpmudev.org/hub/my-websites/';
$security_info_url = 'https://premium.wpmudev.org/manuals/hub-security/';
$support_url       = 'https://premium.wpmudev.org/hub/support/';
$support_modal_url = 'https://premium.wpmudev.org/hub/support/#get-support';

$login_url = $urls->dashboard_url;
if ( ! empty( $_GET['pid'] ) ) { // wpcs csrf ok.
	$login_url = add_query_arg( 'pid', (int) $_GET['pid'], $login_url ); // wpcs csrf ok.
}

$last_user = WPMUDEV_Dashboard::$site->get_option( 'auth_user' );

$login_errors = array();
if ( isset( $_GET['api_error'] ) ) { // wpcs csrf ok.

	if ( 1 === (int) $_GET['api_error'] || 'auth' === $_GET['api_error'] ) { //invalid creds // wpcs csrf ok.

		$login_errors[] = sprintf(
			'%s<br><a href="%s" target="_blank">%s</a>',
			esc_html__( 'Your login details were incorrect. Please make sure you\'re using your WPMU DEV email and password and try again.', 'wpmudev' ),
			$reset_url,
			esc_html__( 'Forgot your password?', 'wpmudev' )
		);

	} elseif ( 'in_trial' === $_GET['api_error'] ) { //trial members can only login to first time domains // wpcs csrf ok.

		if ( WPMUDEV_Dashboard::$site->is_localhost() ) {
			$login_errors[] = sprintf(
				'%s<br><a href="%s" target="_blank">%s</a>',
				sprintf(
					__(
						'This local development site URL has previously been registered with us by the user %1$s. To use WPMU DEV with this site URL, log in with the original user (you can <a target="_blank" href="%2$s">reset your password</a>) or <a target="_blank" href="%3$s">upgrade your trial</a> to a full membership. Alternatively, try a more uniquely named development site URL. Trial accounts can\'t use previously registered domains - <a target="_blank" href="%4$s">here\'s why</a>.',
						'wpmudev'
					),
					'<strong style="word-break: break-all;">' . esc_html( $_GET['display_name'] ) . '</strong>', // wpcs csrf ok.
					$reset_url,
					$account_url,
					$trial_info_url
				),
				$support_url,
				__( 'Contact support if you need further assistance &raquo;', 'wpmudev' )
			);
		} else {
			$login_errors[] = sprintf(
				'%s<br><a href="%s" target="_blank">%s</a>',
				sprintf(
					__(
						'This domain has previously been registered with us by the user %1$s. To use WPMU DEV on this domain, you can either log in with the original account (you can <a target="_blank" href="%2$s">reset your password</a>) or <a target="_blank" href="%3$s">upgrade your trial</a> to a full membership. Trial accounts can\'t use previously registered domains - <a target="_blank" href="%4$s">here\'s why</a>.',
						'wpmudev'
					),
					'<strong style="word-break: break-all;">' . esc_html( $_GET['display_name'] ) . '</strong>', // wpcs csrf ok.
					$reset_url,
					$account_url,
					$trial_info_url
				),
				$support_url,
				__( 'Contact support if you need further assistance &raquo;', 'wpmudev' )
			);
		}

	} elseif ( 'already_registered' === $_GET['api_error'] ) { //IMPORTANT for security we make sure this site has been logged out of before another user can take it over // wpcs csrf ok.

		if ( WPMUDEV_Dashboard::$site->is_localhost() ) {
			$login_errors[] = sprintf(
				'%s<br><a href="%s" target="_blank">%s</a>',
				sprintf(
					__(
						'This local development site URL is currently registered to %1$s. For <a target="_blank" href="%2$s">security reasons</a> they will need to go to the <a target="_blank" href="%3$s">WPMU DEV Hub</a> and remove this domain before you can log in. If that account is not yours, then make your local development site URL more unique.',
						'wpmudev'
					),
					'<strong style="word-break: break-all;">' . esc_html( $_GET['display_name'] ) . '</strong>', // wpcs csrf ok.
					$security_info_url,
					$websites_url
				),
				$support_url,
				__( 'Contact support if you need further assistance &raquo;', 'wpmudev' )
			);
		} else {
			$login_errors[] = sprintf(
				__(
					'This site is currently registered to %1$s. For <a target="_blank" href="%2$s">security reasons</a> they will need to go to the <a target="_blank" href="%3$s">WPMU DEV Hub</a> and remove this domain before you can log in. If you do not have access to that account, and have no way of contacting that user, please <a target="_blank" href="%4$s">contact support for assistance</a>.',
					'wpmudev'
				),
				'<strong style="word-break: break-all;">' . esc_html( $_GET['display_name'] ) . '</strong>', // wpcs csrf ok.
				$security_info_url,
				$websites_url,
				$support_url
			);
		}

	} else { //this in case we add new error types in the future
		$login_errors[] = __( 'Unknown error. Please update the WPMU DEV Dashboard plugin and try again.', 'wpmudev' );

	}
} elseif ( $connection_error ) {
	// Variable `$connection_error` is set by the UI function `render_dashboard`.
	$login_errors[] = sprintf(
		'%s<br>%s<br><em>%s</em>',
		sprintf(
			__( 'Your server had a problem connecting to WPMU DEV: "%s". Please try again.', 'wpmudev' ),
			WPMUDEV_Dashboard::$api->api_error
		),
		__( 'If this problem continues, please contact your host with this error message and ask:', 'wpmudev' ),
		sprintf(
			__( '"Is php on my server properly configured to be able to contact %s with a POST HTTP request via fsockopen or CURL?"', 'wpmudev' ),
			WPMUDEV_Dashboard::$api->rest_url( '' )
		)
	);
} elseif ( ! $key_valid ) {
	// Variable `$key_valod` is set by the UI function `render_dashboard`.
	$login_errors[] = __( 'Your API Key was invalid. Please try again.', 'wpmudev' );
} elseif ( $site_limit_exceeded ) {
	// Variable `$site_limit_exceeded` is set by the UI function `render_dashboard`.
	$error_msg = sprintf( __( 'You have already reached your plans limit of %1$d site, not hosted with us, connected to The Hub. <a target="_blank" href="%2$s">Upgrade your membership</a> or <a target="_blank" href="%3$s">remove a site</a> before adding another. <a target="_blank" href="%4$s">Contact support</a> for assistance.', 'wpmudev' ), $site_limit_num, $account_url, $websites_url, $support_modal_url );

	if( $available_hosting_sites ){
		$error_msg .= sprintf( __( '</br><strong>Note:</strong> You still have %1$d site <a target="_blank" href="%2$s">hosted with us</a> available.', 'wpmudev' ), $available_hosting_sites, $hosting_url );
	}

	$login_errors[] = $error_msg;
}

// Get the login URL.
$form_action = WPMUDEV_Dashboard::$api->rest_url( 'authenticate' );

// Nonce to store sso setting.
$sso_nonce 		= wp_create_nonce( 'sso-status' );
//check if SSO and status was set previously and show the checkbox accordingly.
$enable_sso     = WPMUDEV_Dashboard::$site->get_option( 'enable_sso', true, 1 );

// Detect Free Plugins
$installed_free_projects        = WPMUDEV_Dashboard::$site->get_installed_free_projects();

// build plugin names
$installed_free_projects_names = wp_list_pluck( $installed_free_projects, 'name' );
$installed_free_projects_names_concat = '';
$installed_free_projects_names_concat = array_pop( $installed_free_projects_names );
if ( $installed_free_projects_names ) {
	$installed_free_projects_names_concat = implode( ', ', $installed_free_projects_names ) . ' ' . '&amp;' . ' ' . $installed_free_projects_names_concat;
}

?>

<div class="dashui-onboarding">

	<div class="dashui-onboarding-body dashui-onboarding-content-center">

		<div class="dashui-login-form">

			<?php if ( ! empty( $installed_free_projects_names_concat ) ) : ?>
				<h2><?php esc_html_e( "Let’s unlock pro features", 'wpmudev' ); ?></h2>
			<?php else : ?>
				<h2><?php esc_html_e( "Let’s connect your site", 'wpmudev' ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $installed_free_projects_names_concat ) ) : ?>
				<span class="sui-description">
					<?php
					echo esc_html( sprintf( __( 'To unlock pro features for %s, log in using your WPMU DEV account email and password.', 'wpmudev' ), $installed_free_projects_names_concat ) )
					?>
				</span>
			<?php else : ?>
				<span class="sui-description"><?php esc_html_e( 'To unlock pro plugins and the Hub, log in using your WPMU DEV account email and password.', 'wpmudev' ); ?></span>
			<?php endif; ?>


			<form action="<?php echo esc_url( $form_action ); ?>" method="post" class="js-wpmudev-login-form">

				<div class="sui-form-field">

					<label for="dashboard-email" class="sui-screen-reader-text"><?php esc_html_e( 'Email', 'wpmudev' ); ?></label>

					<input type="email"
					       placeholder="<?php esc_html_e( 'Email', 'wpmudev' ); ?>"
					       id="dashboard-email"
					       name="username"
					       value="<?php echo esc_attr( $last_user ); ?>"
					       required="required"
					       class="sui-form-control"/>
					<span class="sui-error-message sui-hidden js-required-message"><?php esc_html_e( 'Email is required.' ); ?></span>
					<span class="sui-error-message sui-hidden js-valid-email-message"><?php esc_html_e( 'Email is not valid.' ); ?></span>
				</div>

				<div class="sui-form-field">

					<label for="dashboard-password" class="sui-screen-reader-text"><?php esc_html_e( 'Password', 'wpmudev' ); ?></label>

					<div class="sui-with-button sui-with-button-icon">

						<input type="password"
						       placeholder="<?php esc_html_e( 'Password', 'wpmudev' ); ?>"
						       id="dashboard-password"
						       autocomplete="off"
						       name="password"
						       required="required"
						       class="sui-form-control"/>

						<button class="sui-button-icon" type="button">
							<i class="sui-icon-eye" aria-hidden="true"></i>
							<span class="sui-password-text sui-screen-reader-text"><?php esc_html_e( 'Show Password', 'wpmudev' ); ?></span>
							<span class="sui-password-text sui-screen-reader-text sui-hidden"><?php esc_html_e( 'Hide Password', 'wpmudev' ); ?></span>
						</button>
						<span class="sui-error-message sui-hidden js-required-message"><?php esc_html_e( 'Password is required.' ); ?></span>

					</div>
				</div>

				<?php foreach ( $login_errors as $login_error ) : ?>
					<div class="sui-notice sui-notice-error">
						<p><?php echo $login_error; // wpcs xss ok. ?></p>
					</div>
				<?php endforeach; ?>
				<div clas="dashui-login-button-wrap">
					<div class="dashui-sso-checkbox">
						<label for="enable-sso" class="sui-checkbox">
							<input
							type="checkbox"
							id="enable-sso"
							name="enable-sso"
							data-nonce="<?php echo esc_attr( $sso_nonce ); ?>"
							data-userid="<?php echo absint( get_current_user_id() ); ?>"
							<?php checked( $enable_sso ); ?>
							value="1">
							<span aria-hidden="true"></span>
							<span class="enable-sso-label" ><?php esc_html_e( 'Enable SSO', 'wpmudev' ); ?></span>
							<button class="sui-button-icon sui-tooltip sui-tooltip-top sui-tooltip-constrained" data-tooltip="<?php esc_html_e( 'We will automatically log you in when you visit this site from The Hub.', 'wpmudev' ); ?>">
								<i class="sui-icon-info" aria-hidden="true"></i>
							</button>
						</label>
					</div>
					<div class="dashui-login-button">

						<button class="sui-button sui-button-blue js-login-form-submit-button" type="submit">
							<span class="sui-loading-text"><?php esc_html_e( 'Connect', 'wpmudev' ); ?>&nbsp;&nbsp;<i class="sui-icon-arrow-right" aria-hidden="true"></i></span>
							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
						</button>

					</div>
				</div>
				<input type="hidden" name="redirect_url" value="<?php echo esc_url( $login_url ); ?>">
				<input type="hidden" name="domain" value="<?php echo esc_url( WPMUDEV_Dashboard::$api->network_site_url() ); ?>">
			</form>

		</div>

	</div>
	<div class="dashui-onboarding-footer">
		<span class="sui-description">
			<?php printf(
				esc_html__( "Don't have an account? %1\$sSign up%2\$s today!", 'wpmudev' ),
				'<a href="' . $register_url . '" target="_blank">',
				'</a>'
			); // wpcs xss ok.?>
		</span>
		<span class="sui-description">
			<?php printf(
				esc_html__( "%1\$sSystem Information%2\$s", 'wpmudev' ),
				'<a href="' . esc_url( add_query_arg( 'view', 'system', $urls->dashboard_url ) ) . '">',
				'</a>'
			); // wpcs xss ok.?>
		</span>

	</div>
</div>
