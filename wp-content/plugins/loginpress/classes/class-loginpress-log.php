<?php

/**
 * Log file to know more about users website environment.
 * helps in debugging and providing support.
 *
 * @package    LoginPress
 * @since      1.0.19
 */

class LoginPress_Log_Info {

	/**
	 * Returns the plugin & system information.
	 * @access public
	 * @return string
	 */
	public static function get_sysinfo() {

		global $wpdb;
		$loginpress_setting  = get_option( 'loginpress_setting' );
		$loginpress_config 	 = get_option( 'loginpress_customization' );
		$session_expiration  = ( isset( $loginpress_setting['session_expiration'] ) && '0' != $loginpress_setting['session_expiration'] ) ? $loginpress_setting['session_expiration'] . ' Minute' : 'Not Set';
		$login_order 	       = isset( $loginpress_setting['login_order'] ) ? $loginpress_setting['login_order'] : 'Default';
		$customization 			 = isset( $loginpress_config ) ? print_r( $loginpress_config, true ) : 'No customization yet';
		$lostpassword_url 	 = isset( $loginpress_setting['lostpassword_url'] ) ? $loginpress_setting['lostpassword_url'] : 'off';
		$_loginpassword_url  = ( $lostpassword_url == 'on' ) ? 'WordPress Default' : "WooCommerce Custom URL";
		$loginpress_uninstall= isset( $loginpress_setting['loginpress_uninstall'] ) ? $loginpress_setting['loginpress_uninstall'] : 'off';

		$html = '### Begin System Info ###' . "\n\n";

		// Basic site info
		$html .= '-- WordPress Configuration --' . "\n\n";
		$html .= 'Site URL:                 ' . site_url() . "\n";
		$html .= 'Home URL:                 ' . home_url() . "\n";
		$html .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";
		$html .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
		$html .= 'Language:                 ' . get_locale() . "\n";
		$html .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . "\n";
		$html .= 'WP_DEBUG:                 ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
		$html .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";

		// Plugin Configuration
		$html .= "\n" . '-- LoginPress Configuration --' . "\n\n";
		$html .= 'Plugin Version:           ' . LOGINPRESS_VERSION . "\n";
		$html .= 'Expiration:           	  ' . $session_expiration . "\n";
		$html .= 'Login Order:              ' . ucfirst( $login_order ) . "\n";
		if ( class_exists( 'WooCommerce' ) ) {
		$html .= 'Lost Password URL:        ' . $_loginpassword_url . "\n";
		}
		$html .= 'Uninstallation:       	  ' . $loginpress_uninstall . "\n";
		$html .= 'Total Customized Fields:  ' . count( $loginpress_config ) . "\n";
		$html .= 'Customization Detail:     ' . $customization . "\n";

		// Pro Plugin Configuration
		if ( class_exists( 'LoginPress_Pro' ) ) {

			$enable_repatcha     = ( isset( $loginpress_setting['enable_repatcha'] ) ) ? $loginpress_setting['enable_repatcha'] : 'Off';
			$enable_force 			 = ( isset( $loginpress_setting['force_login'] ) ) ? $loginpress_setting['force_login'] : 'Off';
			$loginpress_preset	 = get_option( 'customize_presets_settings', 'default1' );
			$license_key         = LoginPress_Pro::get_registered_license_status();

			$html .= "\n" . '-- LoginPress Pro Configuration --' . "\n\n";
			$html .= 'Plugin Version:           ' . LOGINPRESS_PRO_VERSION . "\n";
			$html .= 'LoginPress Template:      ' . $loginpress_preset . "\n";
			$html .= 'License Status:           ' . $license_key . "\n";
			$html .= 'Force Login:              ' . $enable_force . "\n";
			$html .= 'Google Repatcha Status:   ' . $enable_repatcha . "\n";

			if ( 'on' == $enable_repatcha ) {
				$site_key          = ( isset( $loginpress_setting['site_key'] ) ) ? $loginpress_setting['site_key'] : 'Not Set';
				$secret_key        = ( isset( $loginpress_setting['secret_key'] ) ) ? $loginpress_setting['secret_key'] : 'Not Set';
				$captcha_theme     = ( isset( $loginpress_setting['captcha_theme'] ) ) ? $loginpress_setting['captcha_theme'] : 'Light';
				$captcha_language  = ( isset( $loginpress_setting['captcha_language'] ) ) ? $loginpress_setting['captcha_language'] : 'English (US)';
				$captcha_enable_on = ( isset( $loginpress_setting['captcha_enable'] ) ) ? $loginpress_setting['captcha_enable'] : 'Not Set';

				$html .= 'Repatcha Site Key:        ' . LoginPress_Pro::mask_license( $site_key ) . "\n";
				$html .= 'Repatcha Secret Key:      ' . LoginPress_Pro::mask_license( $secret_key ) . "\n";
				$html .= 'Repatcha Theme Used:      ' . $captcha_theme . "\n";
				$html .= 'Repatcha Language Used:   ' . $captcha_language . "\n";
				if ( is_array( $captcha_enable_on ) ) {
					foreach ( $captcha_enable_on as $key ) {
						$html .= 'Repatcha Enable On:       ' . ucfirst( str_replace( "_", " ", $key ) )  . "\n";
					}
				}
			}
		}
		// Server Configuration
		$html .= "\n" . '-- Server Configuration --' . "\n\n";
		$html .= 'Operating System:         ' . php_uname( 's' ) . "\n";
		$html .= 'PHP Version:              ' . PHP_VERSION . "\n";
		$html .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";

		$html .= 'Server Software:          ' . $_SERVER['SERVER_SOFTWARE'] . "\n";

		// PHP configs... now we're getting to the important stuff
		$html .= "\n" . '-- PHP Configuration --' . "\n\n";
		// $html .= 'Safe Mode:                ' . ( ini_get( 'safe_mode' ) ? 'Enabled' : 'Disabled' . "\n" );
		$html .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
		$html .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
		$html .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
		$html .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . "\n";
		$html .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
		$html .= 'Display Errors:           ' . ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ) . "\n";

		// WordPress active themes
		$html .= "\n" . '-- WordPress Active Theme --' . "\n\n";
		$my_theme = wp_get_theme();
		$html .= 'Name:                     ' . $my_theme->get( 'Name' ) . "\n";
		$html .= 'URI:                      ' . $my_theme->get( 'ThemeURI' ) . "\n";
		$html .= 'Author:                   ' . $my_theme->get( 'Author' ) . "\n";
		$html .= 'Version:                  ' . $my_theme->get( 'Version' ) . "\n";

		// WordPress active plugins
		$html .= "\n" . '-- WordPress Active Plugins --' . "\n\n";
		$plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );
		foreach( $plugins as $plugin_path => $plugin ) {
			if( !in_array( $plugin_path, $active_plugins ) )
				continue;
			$html .= $plugin['Name'] . ': v(' . $plugin['Version'] . ")\n";
		}

		// WordPress inactive plugins
		$html .= "\n" . '-- WordPress Inactive Plugins --' . "\n\n";
		foreach( $plugins as $plugin_path => $plugin ) {
			if( in_array( $plugin_path, $active_plugins ) )
				continue;
			$html .= $plugin['Name'] . ': v(' . $plugin['Version'] . ")\n";
		}

		if( is_multisite() ) {
			// WordPress Multisite active plugins
			$html .= "\n" . '-- Network Active Plugins --' . "\n\n";
			$plugins = wp_get_active_network_plugins();
			$active_plugins = get_site_option( 'active_sitewide_plugins', array() );
			foreach( $plugins as $plugin_path ) {
				$plugin_base = plugin_basename( $plugin_path );
				if( ! array_key_exists( $plugin_base, $active_plugins ) )
					continue;
				$plugin  = get_plugin_data( $plugin_path );
				$html .= $plugin['Name'] . ': v(' . $plugin['Version'] . ")\n";
			}
		}

		$html .= "\n" . '### End System Info ###';
		return $html;
	}
} // End of Class.
