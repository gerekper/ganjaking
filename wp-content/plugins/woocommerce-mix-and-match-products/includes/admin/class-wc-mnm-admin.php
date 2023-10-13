<?php
/**
 * Admin Class
 *
 * @package  WooCommerce Mix and Match Products/Admin
 * @since    1.0.0
 * @version  2.4.10
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

		// Admin includes.
		self::includes();

		// Add a message in the WP Privacy Policy Guide page.
		add_action( 'admin_init', array( __CLASS__, 'add_privacy_policy_guide_content' ) );

		// Admin jquery.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );

		// Template override scan path.
		add_filter( 'woocommerce_template_overrides_scan_paths', array( __CLASS__, 'template_scan_path' ) );

		// Show outdated templates in the system status.
		add_action( 'woocommerce_system_status_report', array( __CLASS__, 'render_system_status_items' ) );

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

		// Admin notices handling.
		include_once __DIR__ . '/class-wc-mnm-admin-notices.php';

		// Admin functions.
		include_once __DIR__ . '/wc-mnm-admin-functions.php';

		// Product Import/Export.
		if ( WC_MNM_Core_Compatibility::is_wc_version_gte( '3.1' ) ) {
			include_once __DIR__ . '/export/class-wc-mnm-product-export.php';
			include_once __DIR__ . '/import/class-wc-mnm-product-import.php';
		}

		// Metaboxes.
		include_once __DIR__ . '/meta-boxes/class-wc-mnm-meta-box-product-data.php';

		// Admin AJAX.
		include_once __DIR__ . '/class-wc-mnm-admin-ajax.php';

		// Admin edit-order screen.
		if ( WC_MNM_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {
			include_once __DIR__ . '/meta-boxes/class-wc-mnm-meta-box-order.php';
		}

		// Admin Notes.
		include_once WC_Mix_and_Match()->plugin_path() . '/includes/admin/class-wc-mnm-admin-notes.php';
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

		$script_path = 'assets/js/admin/meta-boxes-product' . $suffix . '.js';
		wp_register_script( 'wc-mnm-admin-product-panel', WC_Mix_and_Match()->plugin_url() . '/' . $script_path, array( 'wc-admin-product-meta-boxes', 'wc-enhanced-select' ), WC_Mix_and_Match()->get_file_version( WC_MNM_ABSPATH . $script_path ), true );

		$script_path = 'assets/js/admin/meta-boxes-order' . $suffix . '.js';
		wp_register_script( 'wc-mnm-admin-order-panel', WC_Mix_and_Match()->plugin_url() . '/' . $script_path, array( 'wc-admin-order-meta-boxes', 'wc-add-to-cart-mnm' ), WC_Mix_and_Match()->get_file_version( WC_MNM_ABSPATH . $script_path ), true );

		$style_path = 'assets/css/admin/mnm-edit-product' . $suffix . '.css';
		wp_register_style( 'wc-mnm-admin-product-panel', WC_Mix_and_Match()->plugin_url() . '/assets/css/admin/mnm-edit-product.css', array( 'woocommerce_admin_styles' ), WC_Mix_and_Match()->get_file_version( WC_MNM_ABSPATH . $style_path ) );
		wp_style_add_data( 'wc-mnm-admin-product-panel', 'rtl', 'replace' );

		$style_path = 'assets/css/admin/mnm-edit-order' . $suffix . '.css';
		wp_register_style( 'wc-mnm-admin-order-style', WC_Mix_and_Match()->plugin_url() . '/' . $style_path, array( 'woocommerce_admin_styles' ), WC_Mix_and_Match()->get_file_version( WC_MNM_ABSPATH . $style_path ) );
		wp_style_add_data( 'wc-mnm-admin-order-style', 'rtl', 'replace' );

		// RTL minified stylesheet fix.
		if ( $suffix ) {
			wp_style_add_data( 'wc-mnm-admin-product-panel', 'suffix', '.min' );
			wp_style_add_data( 'wc-mnm-admin-order-style', 'suffix', '.min' );
		}

		// Get admin screen id.
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		/*
		 * Enqueue script and styles.
		 */
		if ( in_array( $screen_id, array( 'edit-product', 'product' ) ) ) {

			wp_enqueue_style( 'wc-mnm-admin-product-panel' );
			wp_enqueue_script( 'wc-mnm-admin-product-panel' );

		} elseif ( in_array( $screen_id, array( 'shop_order', 'shop_subscription', 'woocommerce_page_wc-orders', 'woocommerce_page_wc-orders--shop_subscription' ) , true ) ) {

			if ( WC_MNM_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {

				// Load edit validation scripts.
				WC_MNM_Ajax::load_edit_scripts();

				wp_enqueue_style( 'wc-mnm-admin-order-style' );
				wp_enqueue_script( 'wc-mnm-admin-order-panel' );

				$params = array(
					'wc_ajax_url'           => WC_AJAX::get_endpoint( '%%endpoint%%' ),
					'edit_container_nonce'  => wp_create_nonce( 'wc_mnm_edit_container' ),
					'i18n_configure'        => __( 'Configure', 'woocommerce-mix-and-match-products' ),
					'i18n_edit'             => __( 'Edit', 'woocommerce-mix-and-match-products' ),
					'i18n_form_error'       => __( 'Failed to initialize form. If this issue persists, please reload the page and try again.', 'woocommerce-mix-and-match-products' ),
					'i18n_validation_error' => __( 'Failed to validate configuration. If this issue persists, please reload the page and try again.', 'woocommerce-mix-and-match-products' ),
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
	public static function admin_header() {
		?>
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
		$paths['WooCommerce Mix and Match Products'] = WC_Mix_and_Match()->plugin_path() . '/templates/';
		return $paths;
	}

	/**
	 * Renders the MNM information in the WC status page
	 *
	 * @props to ProsPress
	 */
	public static function render_system_status_items() {
		$debug_data = array(
			'version'    => array(
				'name' => _x( 'Version', 'label for the system staus page', 'woocommerce-mix-and-match-products' ),
				'note' => get_option( 'wc_mix_and_match_version', null ),
			),
			'db_version' => array(
				'name' => _x( 'Database Version', 'label for the system staus page', 'woocommerce-mix-and-match-products' ),
				'note' => get_option( 'wc_mix_and_match_db_version', null ),
			),
		);

		$theme_overrides                   = self::get_theme_overrides();
		$debug_data['mnm_theme_overrides'] = array(
			'name'      => _x( 'Template Overrides', 'label for the system status page', 'woocommerce-mix-and-match-products' ),
			'mark'      => '',
			'mark_icon' => $theme_overrides['has_outdated_templates'] ? 'warning' : 'yes',
			'data'      => $theme_overrides,
		);

		if ( $theme_overrides['has_outdated_templates'] ) {
			$debug_data['mnm_outdated_templates'] = array(
				'name'      => _x( 'Outdated Templates', 'label for the system status page', 'woocommerce-mix-and-match-products' ),
				'mark'      => 'error',
				'mark_icon' => 'warning',
				'note'      => '<a href="' . esc_url( WC_Mix_and_Match()->get_resource_url( 'outdated-templates' ) ) . '" target="_blank">' . __( 'Learn how to update', 'woocommerce-mix-and-match-products' ) . '</a>',
			);
		}

		$debug_data = apply_filters( 'wc_mnm_system_status', $debug_data );

		include 'views/status.php';
	}

	/**
	 * Determine which of our files have been overridden by the theme.
	 *
	 * @props Jeremy Pry
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
			$theme_file  = false;
			$is_outdated = false;
			$locations   = array(
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
					$outdated    = true;
					$is_outdated = true;
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
	 * @param   mixed  $links
	 * @param   mixed  $file
	 * @return  array
	 */
	public static function plugin_row_meta( $links, $file ) {

		if ( WC_Mix_and_Match()->plugin_basename() === $file ) {
			$row_meta = array(
				'docs'    => '<a href="' . esc_url( WC_Mix_and_Match()->get_resource_url( 'docs' ) ) . '">' . __( 'Documentation', 'woocommerce-mix-and-match-products' ) . '</a>',
				'support' => '<a href="' . esc_url( WC_MNM_SUPPORT_URL ) . '">' . __( 'Support', 'woocommerce-mix-and-match-products' ) . '</a>',
			);

			$links = array_merge( $links, $row_meta );
		}

		return $links;
	}


	/**
	 * Include the upgrade notice that will fire when 1.3.0 is released.
	 *
	 * @param array $plugin_data information about the plugin
	 * @param array $response response from the server about the new version
	 */
	public static function update_notice( $plugin_data, $response ) {

		$new_version     = $response->new_version;
		$current_version = WC_Mix_and_Match()->version;

		$major_notices = array(
			'1.3.0' => sprintf( __( '<strong>Warning!</strong> Version 1.3.0 is a major update to the template system. Before updating, please test and update your custom templates with version 1.3.0 on a staging site. %1$sLearn more about the changes in version 1.3.0 &raquo;%2$s', 'woocommerce-mix-and-match-products' ), '<a href="' . esc_url( WC_Mix_and_Match()->get_resource_url( 'new-1.3' ) ) . '">', '</a>' ),
			'2.0.0' => sprintf( __( '<strong>Warning!</strong> Version 2.0.0 is a major update to the database and templates. Before updating, please test the upgrade on a staging site. %1$sLearn more about the changes in version 2.0.0 &raquo;%2$s', 'woocommerce-mix-and-match-products' ), '<a href="' . esc_url( WC_Mix_and_Match()->get_resource_url( 'new-2.0' ) ) . '">', '</a>' ),
		);

		$upgrade_notice = '';

		foreach ( $major_notices as $check_version => $notice ) {
			// Skip if the update notice is not relevant.
			if ( version_compare( $current_version, $check_version, '>' ) ) {
				continue;
			}

			$upgrade_notice .= '<p class="wc_plugin_upgrade_notice">' . $notice . '</p>';

		}

		echo $upgrade_notice ? '</p>' . wp_kses_post( $upgrade_notice ) . '<p class="hidden">' : '';
	}
}
// launch the admin class.
WC_Mix_and_Match_Admin::init();
