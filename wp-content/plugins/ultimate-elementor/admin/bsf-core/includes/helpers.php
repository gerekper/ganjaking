<?php
/**
 * Helper functions for BSF Core.
 *
 * @author Brainstorm Force
 * @package bsf-core
 */

/**
 * BSF get API site.
 *
 * @param bool $prefer_unsecure Prefer unsecure.
 * @param bool $is_rest_api use rest api base URL.
 * @return $bsf_api_site.
 */
function bsf_get_api_site( $prefer_unsecure = false, $is_rest_api = false ) {
	$rest_api_endoint = ( true === $is_rest_api ) ? 'wp-json/bsf-products/v1/' : '';

	if ( defined( 'BSF_API_URL' ) ) {
		$bsf_api_site = BSF_API_URL . $rest_api_endoint;
	} else {
		$bsf_api_site = 'http://support.brainstormforce.com/' . $rest_api_endoint;

		if ( false === $prefer_unsecure && wp_http_supports( array( 'ssl' ) ) ) {
			$bsf_api_site = set_url_scheme( $bsf_api_site, 'https' );
		}
	}

	return $bsf_api_site;
}

/**
 * BSF get API URL.
 *
 * @param bool $prefer_unsecure Prefer unsecure.
 * @return $url.
 */
function bsf_get_api_url( $prefer_unsecure = false ) {
	$url = bsf_get_api_site( $prefer_unsecure ) . 'wp-admin/admin-ajax.php';

	return $url;
}

/**
 * BSF time since last version check.
 *
 * @param int    $hours_completed Hours completed.
 * @param string $option Option.
 * @return $url.
 */
function bsf_time_since_last_versioncheck( $hours_completed, $option ) {

	$seconds = $hours_completed * HOUR_IN_SECONDS;
	$status  = false;

	$last_update_timestamp = (int) get_option( $option, false );

	if ( false !== $last_update_timestamp ) {

		// Find seconds passed since the last timestamp update (i.e. last request made).
		$elapsed_seconds = (int) current_time( 'timestamp' ) - $last_update_timestamp;

		// IF time is more than the required seconds allow a new HTTP request.
		if ( $elapsed_seconds > $seconds ) {
			$status = true;
		}
	} else {

		// If timestamp is not yet set - allow the HTTP request.
		$status = true;
	}

	return $status;
}

if ( ! function_exists( 'bsf_convert_core_path_to_relative' ) ) {

	/**
	 * Depracate bsf_convert_core_path_to_relative() to in favour of bsf_core_url()
	 *
	 * @param  $path $path deprecated.
	 * @return String       URL of bsf-core directory.
	 */
	function bsf_convert_core_path_to_relative( $path ) {
		_deprecated_function( __FUNCTION__, '1.22.46', 'bsf_core_url' );

		return bsf_core_url( '' );
	}
}

if ( ! function_exists( 'bsf_core_url' ) ) {

	/**
	 * BSF Core URL
	 *
	 * @param  string $append Append.
	 * @return String URL of bsf-core directory.
	 */
	function bsf_core_url( $append = '' ) {
		$path       = wp_normalize_path( BSF_UPDATER_PATH );
		$theme_dir  = wp_normalize_path( get_template_directory() );
		$plugin_dir = wp_normalize_path( WP_PLUGIN_DIR );

		if ( strpos( $path, $theme_dir ) !== false ) {
			return rtrim( get_template_directory_uri() . '/admin/bsf-core/', '/' ) . $append;
		} elseif ( strpos( $path, $plugin_dir ) !== false ) {
			return rtrim( plugin_dir_url( BSF_UPDATER_FILE ), '/' ) . $append;
		} elseif ( strpos( $path, dirname( plugin_basename( BSF_UPDATER_FILE ) ) ) !== false ) {
			return rtrim( plugin_dir_url( BSF_UPDATER_FILE ), '/' ) . $append;
		}

		return false;
	}
}

if ( ! function_exists( 'get_brainstorm_product' ) ) {

	/**
	 * Get BSF product.
	 *
	 * @param  string $product_id Product ID.
	 * @return array Product.
	 */
	function get_brainstorm_product( $product_id = '' ) {
		$all_products = brainstorm_get_all_products();

		foreach ( $all_products as $key => $product ) {
			$product_id_bsf = isset( $product['id'] ) ? ( is_numeric( $product['id'] ) ? (int) $product['id'] : $product['id'] ) : '';
			if ( $product_id === $product_id_bsf ) {
				return $product;
			}
		}

		return array();
	}
}

if ( ! function_exists( 'brainstorm_get_all_products' ) ) {

	/**
	 * Get BSF all products.
	 *
	 * @param  bool $skip_plugins Skip plugins.
	 * @param  bool $skip_themes Skip themes.
	 * @param  bool $skip_bundled Skip bundled.
	 *
	 * @return array All Products.
	 */
	function brainstorm_get_all_products( $skip_plugins = false, $skip_themes = false, $skip_bundled = false ) {
		$all_products                = array();
		$brainstrom_products         = get_option( 'brainstrom_products', array() );
		$brainstrom_bundled_products = get_option( 'brainstrom_bundled_products', array() );
		$brainstorm_plugins          = isset( $brainstrom_products['plugins'] ) ? $brainstrom_products['plugins'] : array();
		$brainstorm_themes           = isset( $brainstrom_products['themes'] ) ? $brainstrom_products['themes'] : array();

		if ( true === $skip_plugins ) {
			$all_products = $brainstorm_themes;
		} elseif ( true === $skip_themes ) {
			$all_products = $brainstorm_plugins;
		} else {
			$all_products = $brainstorm_plugins + $brainstorm_themes;
		}

		if ( false === $skip_bundled ) {

			foreach ( $brainstrom_bundled_products as $parent_id => $parent ) {

				foreach ( $parent as $key => $product ) {

					if ( isset( $all_products[ $product->id ] ) ) {
						$all_products[ $product->id ] = array_merge( $all_products[ $product->id ], (array) $product );
					} else {
						$all_products[ $product->id ] = (array) $product;
					}
				}
			}
		}

		return $all_products;
	}
}
if ( ! function_exists( 'bsf_extension_nag' ) ) {


	/**
	 * Generate's markup to generate notice to ask users to install required extensions.
	 *
	 * @since Graupi 1.9
	 *
	 * @param string $product_id (string) Product ID of the brainstorm product.
	 * @param bool   $mu_updater (bool) If True - give nag to separately install brainstorm updater multisite plugin.
	 */
	function bsf_extension_nag( $product_id = '', $mu_updater = false ) {

		$display_nag = get_user_meta( get_current_user_id(), $product_id . '-bsf_nag_dismiss', true );

		if ( true === $mu_updater ) {
			bsf_nag_brainstorm_updater_multisite();
		}

		if ( '1' === $display_nag ||
			! user_can( get_current_user_id(), 'activate_plugins' ) ||
			! user_can( get_current_user_id(), 'install_plugins' ) ) {
			return;
		}

		$bsf_installed_plugins     = '';
		$bsf_not_installed_plugins = '';
		$bsf_not_activated_plugins = '';
		$installer                 = '';
		$bsf_install               = false;
		$bsf_activate              = false;
		$bsf_bundled_products      = bsf_bundled_plugins( $product_id );
		$bsf_product_name          = brainstrom_product_name( $product_id );

		foreach ( $bsf_bundled_products as $key => $plugin ) {

			if ( ! isset( $plugin->id ) || '' === $plugin->id || ! isset( $plugin->must_have_extension ) || 'false' === $plugin->must_have_extension ) {
				continue;
			}

			$plugin_abs_path = WP_PLUGIN_DIR . '/' . $plugin->init;
			if ( is_file( $plugin_abs_path ) ) {

				if ( ! is_plugin_active( $plugin->init ) ) {
					$bsf_not_activated_plugins .= $bsf_bundled_products[ $key ]->name . ', ';
				}
			} else {
				$bsf_not_installed_plugins .= $bsf_bundled_products[ $key ]->name . ', ';
			}
		}

		$bsf_not_activated_plugins = rtrim( $bsf_not_activated_plugins, ', ' );
		$bsf_not_installed_plugins = rtrim( $bsf_not_installed_plugins, ', ' );

		if ( '' !== $bsf_not_activated_plugins || '' !== $bsf_not_installed_plugins ) {
			echo '<div class="updated notice is-dismissible"><p></p>';
			if ( '' !== $bsf_not_activated_plugins ) {
				echo '<p>';
				echo esc_html( $bsf_product_name ) . esc_html__( ' requires following plugins to be active : ', 'bsf' );
				echo '<strong><em>';
				echo esc_html( $bsf_not_activated_plugins );
				echo '</strong></em>';
				echo '</p>';
				$bsf_activate = true;
			}

			if ( '' !== $bsf_not_installed_plugins ) {
				echo '<p>';
				echo esc_html( $bsf_product_name ) . esc_html__( ' requires following plugins to be installed and activated : ', 'bsf' );
				echo '<strong><em>';
				echo esc_html( $bsf_not_installed_plugins );
				echo '</strong></em>';
				echo '</p>';
				$bsf_install = true;
			}

			if ( true === $bsf_activate ) {
				$installer .= '<a href="' . get_admin_url() . 'plugins.php?plugin_status=inactive">' . __( 'Begin activating plugins', 'bsf' ) . '</a> | ';
			}

			if ( true === $bsf_install ) {
				$installer .= '<a href="' . bsf_exension_installer_url( $product_id ) . '">' . __( 'Begin installing plugins', 'bsf' ) . '</a> | ';
			}

			$installer .= '<a href="' . esc_url( add_query_arg( 'bsf-dismiss-notice', $product_id ) ) . '">' . __( 'Dismiss This Notice', 'bsf' ) . '</a>';

			$installer = ltrim( $installer, '| ' );

			wp_nonce_field( 'bsf-extension-nag', 'bsf-extension-nag-nonce', true, 1 );
			echo '<p><strong>';
			echo esc_html( rtrim( $installer, ' |' ) );
			echo '</p></strong>';

			echo '<p></p></div>';
		}
	}
}

if ( ! function_exists( 'bsf_nag_brainstorm_updater_multisite' ) ) {
	/**
	 * BSF Updater multisite.
	 */
	function bsf_nag_brainstorm_updater_multisite() {

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		if ( ! is_multisite() || is_plugin_active_for_network( 'brainstorm-updater/index.php' ) ) {
			return;
		}

		echo '<div class="notice notice-error uct-notice is-dismissible"><p>';

		/* translators: %1$s: strong tag %2%s: strong tag  %3%s: anchor tag %4%s: closing anchor tag */
		sprintf( __( 'Looks like you are on a WordPress Multisite, you will need to install and network activate %1$s Brainstorm Updater for Multisite %2$s plugin. Download it from %3$s here %4$s', 'bsf' ), '<strong><em>', '<strong><em>', '<a href="http://bsf.io/bsf-updater-mu" target="_blank">', '</a>' );

		echo '</p>';
		echo '</div>';
	}
}

/**
 * Get product name from BSF core is loaded.
 */
function bsf_get_loaded_bsf_core_name() {

	$path         = wp_normalize_path( BSF_UPDATER_PATH );
	$theme_dir    = wp_normalize_path( WP_CONTENT_DIR . '/themes/' );
	$plugin_dir   = wp_normalize_path( WP_PLUGIN_DIR );
	$product_name = '';

	if ( false !== strpos( $path, $theme_dir ) ) {
		// This is a theme path.
		$product_slug = str_replace( array( $theme_dir, '/admin/bsf-core' ), '', $path );
	} elseif ( false !== strpos( $path, $plugin_dir ) ) {
		// This is plugin path.
		$product_slug = str_replace( array( $plugin_dir . '/', '/admin/bsf-core' ), '', $path );
	}

	$brainstrom_products = get_option( 'brainstrom_products', array() );
	foreach ( $brainstrom_products as $type => $products ) {
		foreach ( $products as $product ) {
			if ( $product['slug'] === $product_slug ) {
				$product_name = $product['name'];
			}
		}
	}

	return $product_name;
}

/**
 * Clear versions form trasinent.
 *
 * @param string $product_id Product ID.
 */
function bsf_clear_versions_cache( $product_id ) {
	if ( false !== get_transient( 'bsf-product-versions-' . $product_id ) ) {
		delete_transient( 'bsf-product-versions-' . $product_id );
	}
}
/**
 * Get white labled for product name.
 *
 * @param string $product_id Product ID.
 * @param string $product_name Product Name.
 *
 * @return string Product name.
 */
function bsf_get_white_lable_product_name( $product_id, $product_name ) {
	$white_label_name = apply_filters( "bsf_product_name_{$product_id}", $product_name );
	return ! empty( $white_label_name ) ? $white_label_name : $product_name;
}

/**
 * Get installed version of the product.
 *
 * @param string $type plugins/themes.
 * @param string $product_id Product ID.
 */
function bsf_get_product_current_version( $type, $product_id ) {
	$brainstrom_products = get_option( 'brainstrom_products' );
	return isset( $brainstrom_products[ $type ][ $product_id ]['version'] ) ? $brainstrom_products[ $type ][ $product_id ]['version'] : '';
}

/**
 * Check is user has permisson to view the product rollback version form.
 *
 * @param string $product_id Product ID.
 *
 * @return bool
 */
function bsf_display_rollback_version_form( $product_id ) {
	if ( ! BSF_License_Manager::bsf_is_active_license( $product_id ) ) {
		return false;
	}

	if ( is_multisite() && ! current_user_can( 'manage_network_plugins' ) ) {
		return false;
	}

	if ( ! current_user_can( 'update_plugins' ) ) {
		return false;
	}

	return true;
}

/**
 * Get installed PHP version.
 *
 * @return float|false PHP version.
 * @since 1.0.0
 */
function bsf_get_php_version() {
	if ( defined( 'PHP_MAJOR_VERSION' ) && defined( 'PHP_MINOR_VERSION' ) && defined( 'PHP_RELEASE_VERSION' ) ) { // phpcs:ignore
		return PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION;
	}

	return phpversion();
}
