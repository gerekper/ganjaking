<?php

/**
 * Ajax handler for grabbing the upgrade url.
 */
function seedprod_pro_upgrade_license() {
	check_ajax_referer( 'seedprod_pro_upgrade_license' );

	// Check for permissions.
	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'You are not allowed to install plugins.', 'seedprod-pro' ) ) );
	}

	// Check for local dev sites
	// if (seedprod_pro_is_dev_url(home_url())) {
	//     wp_send_json_success(array(
	//         'url' => 'https://www.seedprod.com/docs/go-lite-pro/#manual-upgrade',
	//     ));
	// }

	// Check for a license key.
	$license_key = seedprod_pro_get_api_key();
	if ( empty( $license_key ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'You are not licensed.', 'seedprod-pro' ) ) );
	}

	$url = esc_url_raw(
		add_query_arg(
			array(
				'page' => 'seedprod_lite',
			),
			admin_url( 'admin.php' )
		)
	);

	// Verify pro version is not installed.
	$active = activate_plugin( 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php', false, false, true );
	if ( ! is_wp_error( $active ) ) {
		// Deactivate plugin.
		//deactivate_plugins(plugin_basename('seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php'));
		wp_send_json_error(
			array(
				'message' => esc_html__( 'Pro version is already installed.', 'seedprod-pro' ),
				'reload'  => true,
			)
		);
	}

	// Verifiy licnese key
	$license = seedprod_pro_save_api_key( $license_key );

	// Redirect.
	$oth = hash( 'sha512', wp_rand() );
	update_option( 'seedprod_one_click_upgrade', $oth );
	$version  = SEEDPROD_PRO_VERSION;
	$file     = $license['body']->download_link;
	$siteurl  = admin_url();
	$endpoint = admin_url( 'admin-ajax.php' );
	$redirect = admin_url( 'admin.php?page=seedprod_lite#/settings' );

	$url = add_query_arg(
		array(
			'api_token'   => get_option( 'seedprod_api_token' ),
			'license_key' => $license_key,
			'oth'         => $oth,
			'endpoint'    => $endpoint,
			'version'     => $version,
			'siteurl'     => $siteurl,
			'redirect'    => rawurldecode( base64_encode( $redirect ) ),
			'file'        => rawurldecode( base64_encode( $file ) ),
		),
		SEEDPROD_PRO_WEB_API_URL . 'upgrade-free-to-pro'
	);

	wp_send_json_success(
		array(
			'url' => $url,
		)
	);
}

add_action( 'wp_ajax_seedprod_upgrade_license', 'seedprod_upgrade_license' );

/**
 * Endpoint for one-click upgrade.
 */
function seedprod_pro_run_one_click_upgrade() {
	 $error = esc_html__( 'Could not install upgrade. Please download from seedprod.com and install manually.', 'seedprod-pro' );

	// verify params present (oth & download link).
	$post_oth = ! empty( $_REQUEST['oth'] ) ? sanitize_text_field( $_REQUEST['oth'] ) : '';
	$post_url = ! empty( $_REQUEST['file'] ) ? $_REQUEST['file'] : '';
	if ( empty( $post_oth ) || empty( $post_url ) ) {
		wp_send_json_error( $error );
	}
	// Verify oth.
	$oth = get_option( 'seedprod_one_click_upgrade' );
	if ( empty( $oth ) ) {
		wp_send_json_error( $error );
	}
	if ( ! hash_equals( $oth, $post_oth ) ) {
		wp_send_json_error( $error );
	}
	// Delete so cannot replay.
	delete_option( 'seedprod_one_click_upgrade' );
	// Set the current screen to avoid undefined notices.
	set_current_screen( 'insights_page_seedprod_settings' );
	// Prepare variables.
	$url = esc_url_raw(
		add_query_arg(
			array(
				'page' => 'seedprod-settings',
			),
			admin_url( 'admin.php' )
		)
	);
	// Verify pro not activated.
	if ( is_plugin_active( 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php' ) ) {
		deactivate_plugins( plugin_basename( 'coming-soon/coming-soon.php' ) );
		wp_send_json_success( esc_html__( 'Plugin installed & activated.', 'seedprod-pro' ) );
	}
	// Verify pro not installed.
	$active = activate_plugin( 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php', $url, false, true );
	if ( ! is_wp_error( $active ) ) {
		deactivate_plugins( plugin_basename( 'coming-soon/coming-soon.php' ) );
		wp_send_json_success( esc_html__( 'Plugin installed & activated.', 'seedprod-pro' ) );
	}

	$creds = request_filesystem_credentials( $url, '', false, false, null );
	// Check for file system permissions.
	if ( false === $creds ) {
		wp_send_json_error( $error );
	}
	if ( ! WP_Filesystem( $creds ) ) {
		wp_send_json_error( $error );
	}
	// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	if ( version_compare( $wp_version, '5.3.0' ) >= 0 ) {
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/includes/skin53.php';
	} else {
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/includes/skin.php';
	}
	// Do not allow WordPress to search/download translations, as this will break JS output.
	remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );
	// Create the plugin upgrader with our custom skin.
	$installer = new Plugin_Upgrader( $skin = new SeedProd_Skin() );
	// Error check.
	if ( ! method_exists( $installer, 'install' ) ) {
		wp_send_json_error( $error );
	}

	// Check license key.
	$license_key = seedprod_pro_get_api_key();
	if ( empty( $license_key ) ) {
		wp_send_json_error( new WP_Error( '403', esc_html__( 'You are not licensed.', 'seedprod-pro' ) ) );
	}

	$license = seedprod_pro_save_api_key( $license_key );
	if ( empty( $license['body']->download_link ) ) {
		wp_send_json_error();
	}

    $installer->install($license['body']->download_link); // phpcs:ignore
	// Flush the cache and return the newly installed plugin basename.
	wp_cache_flush();
	if ( $installer->plugin_info() ) {
		$plugin_basename = $installer->plugin_info();

		// Deactivate the lite version first.
		deactivate_plugins( plugin_basename( 'coming-soon/coming-soon.php' ) );

		// Activate the plugin silently.
		$activated = activate_plugin( $plugin_basename, '', false, true );
		if ( ! is_wp_error( $activated ) ) {
			wp_send_json_success( esc_html__( 'Plugin installed & activated.', 'seedprod-pro' ) );
		} else {
			// Reactivate the lite plugin if pro activation failed.
			activate_plugin( plugin_basename( 'coming-soon/coming-soon.php' ), '', false, true );
			wp_send_json_error( esc_html__( 'Pro version installed but needs to be activated from the Plugins page inside your WordPress admin.', 'seedprod-pro' ) );
		}
	}
	wp_send_json_error( $error );
}
