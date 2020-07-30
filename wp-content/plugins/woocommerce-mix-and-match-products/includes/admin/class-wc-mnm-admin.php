<?php
/**
 * Admin Class
 *
 * @author   Kathy Darling
 * @category Admin
 * @package  WooCommerce Mix and Match Products/Admin
 * @since    1.0.0
 * @version  1.3.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Mix_and_Match_Admin Class.
 *
 * Loads admin tabs, scripts and adds related hooks / filters.
 */
class WC_Mix_and_Match_Admin {

	/**
	 * Bootstraps the class and hooks required.
	 */
	public static function init() {

		add_action( 'admin_init', array( __CLASS__, 'includes' ) );

		// Add a message in the WP Privacy Policy Guide page.
		add_action( 'admin_init', array( __CLASS__, 'add_privacy_policy_guide_content' ) );

		// Admin jquery.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );

		// Template override scan path.
		add_filter( 'woocommerce_template_overrides_scan_paths', array( __CLASS__, 'template_scan_path' ) );

		// Show outdated templates in the system status.
		add_action( 'woocommerce_system_status_report', array( __CLASS__ , 'render_system_status_items' ) );

		// Add links.
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );

		// Upgrade Warning.
		add_action( 'in_plugin_update_message-woocommerce-mix-and-match-products/woocommerce-mix-and-match-products.php', array( __CLASS__, 'update_notice' ), 10, 2 );

	}

	/**
	 * Message to add in the WP Privacy Policy Guide page.
	 *
	 * @since  1.3.3
	 *
	 * @return string
	 */
	protected static function get_privacy_policy_guide_message() {

		$content = '
			<div contenteditable="false">' .
				'<p class="wp-policy-help">' .
					__( 'Mix and Match Products does not collect, store or share any personal data.', 'woocommerce-mix-and-match-products' ) .
				'</p>' .
			'</div>';

		return $content;
	}

	/**
	 * Admin init.
	 */
	public static function includes() {

		// Product Import/Export.
		if ( WC_MNM_Core_Compatibility::is_wc_version_gte( '3.1' ) ) {
			require_once( 'export/class-wc-mnm-product-export.php' );
			require_once( 'import/class-wc-mnm-product-import.php' );
		}

		// Metaboxes.
		require_once( 'meta-boxes/class-wc-mnm-meta-box-product-data.php' );

		// Admin AJAX.
		require_once( 'class-wc-mnm-admin-ajax.php' );

		// Admin edit-order screen.
		if ( WC_MNM_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {
			require_once( 'meta-boxes/class-wc-mnm-meta-box-order.php' );
		}

	}

	/**
	 * Add a message in the WP Privacy Policy Guide page.
	 *
	 * @since  1.3.3
	 */
	public static function add_privacy_policy_guide_content() {
		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			wp_add_privacy_policy_content( __( 'WooCommerce Mix and Match Products', 'woocommerce-mix-and-match-products' ), self::get_privacy_policy_guide_message() );
		}
	}

	/**
	 * Load the product metabox script.
	 */
	public static function admin_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'wc-mnm-admin-product-panel', WC_Mix_and_Match()->plugin_url() . '/assets/js/admin/meta-boxes-product' . $suffix . '.js', array( 'wc-admin-product-meta-boxes' ), WC_Mix_and_Match()->version );
		wp_register_script( 'wc-mnm-admin-order-panel', WC_Mix_and_Match()->plugin_url() . '/assets/js/admin/meta-boxes-order' . $suffix . '.js', array( 'wc-admin-order-meta-boxes' ), WC_Mix_and_Match()->version );

		wp_register_style( 'wc-mnm-admin', WC_Mix_and_Match()->plugin_url() . '/assets/css/admin/mnm-admin.css', array(), WC_Mix_and_Match()->version );
		wp_style_add_data( 'wc-mnm-admin', 'rtl', 'replace' );

		wp_enqueue_style( 'wc-mnm-admin-order-style', WC_Mix_and_Match()->plugin_url() . '/assets/css/admin/mnm-edit-order.css', array( 'woocommerce_admin_styles' ), WC_Mix_and_Match()->version );
		wp_style_add_data( 'wc-mnm-admin-order-style', 'rtl', 'replace' );

		// General admin styles.
		wp_enqueue_style( 'wc-mnm-admin' );

		// Get admin screen id.
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		/*
		 * Enqueue styles.
		 */
		if ( in_array ( $screen_id, array( 'edit-shop_order', 'shop_order' ) ) ) {
			wp_enqueue_style( 'wc-mnm-admin-order-style' );
		}

		/*
		 * Enqueue scripts.
		 */
		if ( 'product' === $screen_id ) {

			wp_enqueue_script( 'wc-mnm-admin-product-panel' );

		} elseif ( in_array( $screen_id, array( 'shop_order' ) ) ) {

			if( WC_MNM_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {

				wp_enqueue_script( 'wc-mnm-admin-order-panel' );

				$params = array(
					'edit_container_nonce'     => wp_create_nonce( 'wc_edit_container' ),
					'i18n_configure'        => __( 'Configure', 'woocommerce-mix-and-match-products' ),
					'i18n_edit'             => __( 'Edit', 'woocommerce-mix-and-match-products' ),
					'i18n_form_error'       => __( 'Failed to initialize form. If this issue persists, please reload the page and try again.', 'woocommerce-mix-and-match-products' ),
					'i18n_validation_error' => __( 'Failed to validate configuration. If this issue persists, please reload the page and try again.', 'woocommerce-mix-and-match-products' )
				);

				wp_localize_script( 'wc-mnm-admin-order-panel', 'wc_mnm_admin_order_params', $params );
			}
		}
	}

	/**
	 * Add an icon to MNM product data tab
	 *
	 * @deprecated 1.7.0 - Rule is in mnm-admin.css.
	 */
	public static function admin_header() { ?>
		<style>
			#woocommerce-product-data ul.wc-tabs li.mnm_product_options a:before { content: "\f538"; font-family: "Dashicons"; }
	    </style>
	    <?php
	}

	/**
	 * Add template overrides for MNM to WooCommerce Tracker.
	 *
	 * @param  array  $paths
	 * @return array
	 */
	public static function template_scan_path( $paths ) {
		$paths[ 'WooCommerce Mix and Match Products' ] = WC_Mix_and_Match()->plugin_path() . '/templates/';
		return $paths;
	}

	/**
	 * Renders the MNM information in the WC status page
	 * @props to ProsPress
	 */
	public static function render_system_status_items() {
		$debug_data = array(
			'version'    => array(
				'name'      => _x( 'Version', 'label for the system staus page', 'woocommerce-mix-and-match-products' ),
				'note'      => get_option( 'wc_mix_and_match_version', null ),
			),
			'db_version' => array(
				'name'      => _x( 'Database Version', 'label for the system staus page', 'woocommerce-mix-and-match-products' ),
				'note'      => get_option( 'wc_mix_and_match_db_version', null ),
			),
		);

		$theme_overrides = self::get_theme_overrides();
		$debug_data['mnm_theme_overrides'] = array(
			'name'      => _x( 'Template Overrides', 'label for the system status page', 'woocommerce-mix-and-match-products' ),
			'mark'      => '',
			'mark_icon' => $theme_overrides['has_outdated_templates'] ? 'warning' : 'yes',
			'data'      => $theme_overrides,
		);

		if( $theme_overrides['has_outdated_templates'] ) {
			$debug_data['mnm_outdated_templates'] = array(
				'name'      => _x( 'Outdated Templates', 'label for the system status page', 'woocommerce-mix-and-match-products' ),
				'mark'      => 'error',
				'mark_icon' => 'warning',
				'note'    => '<a href="https://docs.woocommerce.com/document/fix-outdated-templates-woocommerce/" target="_blank">' . __( 'Learn how to update', 'woocommerce-mix-and-match-products' ) . '</a>'
			);
		}

		$debug_data = apply_filters( 'woocommerce_mnm_system_status', $debug_data );

		include( 'views/status.php' );
	}

	/**
	 * Determine which of our files have been overridden by the theme.
	 *
	 * @author Jeremy Pry
	 * @return array Theme override data.
	 */
	private static function get_theme_overrides() {
		$mnm_template_dir = WC_Mix_and_Match()->plugin_path() . '/templates/';
		$wc_template_path = trailingslashit( wc()->template_path() );
		$theme_root       = trailingslashit( get_theme_root() );
		$overridden       = array();
		$outdated         = false;
		$templates        = WC_Admin_Status::scan_template_files( $mnm_template_dir );

		foreach ( $templates as $file ) {
			$theme_file = $is_outdated = false;
			$locations  = array(
				get_stylesheet_directory() . "/{$file}",
				get_stylesheet_directory() . "/{$wc_template_path}{$file}",
				get_template_directory() . "/{$file}",
				get_template_directory() . "/{$wc_template_path}{$file}",
			);

			foreach ( $locations as $location ) {
				if ( is_readable( $location ) ) {
					$theme_file = $location;
					break;
				}
			}

			if ( ! empty( $theme_file ) ) {
				$core_version  = WC_Admin_Status::get_file_version( $mnm_template_dir . $file );
				$theme_version = WC_Admin_Status::get_file_version( $theme_file );
				if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
					$outdated = $is_outdated = true;
				}
				$overridden[] = array(
					'file'         => str_replace( $theme_root, '', $theme_file ),
					'version'      => $theme_version,
					'core_version' => $core_version,
					'is_outdated'  => $is_outdated,
				);
			}
		}

		return array(
			'has_outdated_templates' => $outdated,
			'overridden_templates'   => $overridden,
		);
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed  $links
	 * @param	mixed  $file
	 * @return	array
	 */
	public static function plugin_row_meta( $links, $file ) {

		if ( $file == WC_Mix_and_Match()->plugin_basename() ) {
			$row_meta = array(
				'docs'    => '<a href="https://docs.woocommerce.com/document/woocommerce-mix-and-match-products/">' . __( 'Documentation', 'woocommerce-mix-and-match-products' ) . '</a>',
				'support' => '<a href="' . esc_url( WC_MNM_SUPPORT_URL )  . '">' . __( 'Support', 'woocommerce-mix-and-match-products' ) . '</a>',
			);

			$links = array_merge( $links, $row_meta );
		}

		return $links;

	}

	/**
	 * Include the upgrade notice that will fire when 1.3.0 is released.
	 *
	 * @param array $plugin_data information about the plugin
	 * @param array $r response from the server about the new version
	 */
	public static function update_notice( $plugin_data, $r ) {

		// Bail if the update notice is not relevant (new version is not yet 1.3 or we're already on 1.3)
		if ( version_compare( '1.3.0', $plugin_data['new_version'], '>' ) || version_compare( '1.3.0', $plugin_data['Version'], '<=' ) ) {
			return;
		}

		$update_notice = '</p><div class="wc_plugin_upgrade_notice">';
		// translators: placeholders are opening and closing tags. Leads to docs on version 1.3.0
		$update_notice .= sprintf( __( 'Warning! Version 1.3.0 is a major update to the template system of the WooCommerce Mix and Match Products extension. Before updating, please test and update your custom templates with version 1.3.0 on a staging site. %1$sLearn more about the changes in version 1.3.0 &raquo;%2$s', 'woocommerce-mix-and-match-products' ), '<a href="https://docs.woocommerce.com/document/woocommerce-mix-and-match-products/version-1-3/">', '</a>' );
		$update_notice .= '</div><p class="dummy" style="display:none">';

		echo wp_kses_post( $update_notice );

	}

}
// launch the admin class
WC_Mix_and_Match_Admin::init();
