<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Photography Install.
 *
 * @package  WC_Photography/Install
 * @category Class
 * @author   WooThemes
 */
class WC_Photography_Install {

	/**
	 * Initialize the install actions.
	 */
	public function __construct() {
		// Hooks.
		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
	}

	/**
	 * Check version.
	 *
	 * @return void
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'wc_photography_version' ) != WC_PHOTOGRAPHY_VERSION ) ) {
			self::install();

			do_action( 'wc_photography_updated' );
		}
	}

	/**
	 * Install/update the WooCommerce Photography.
	 */
	public static function install() {
		WC_Photography_Taxonomies::register_collections();

		// Update version.
		update_option( 'wc_photography_version', WC_PHOTOGRAPHY_VERSION );

		$settings = get_option( 'woocommerce_photography' );
		if ( ! $settings ) {
			$settings = self::get_initial_settings();

			update_option( 'woocommerce_photography', $settings );
		}

		self::setup_user_caps();

		// Flush rules after install.
		flush_rewrite_rules();
	}

	/**
	 * Initial settings.
	 *
	 * @return array
	 */
	public static function get_initial_settings() {
		return array(
			'image_text_option'              => 'image_id',
			'collections_default_visibility' => 'restricted',
			'thumbnail_image_size'           => array(
				'width'  => 200,
				'height' => 200,
				'crop'   => false,
			),
			'lightbox_image_size'            => array(
				'width'  => 600,
				'height' => 600,
				'crop'   => false,
			),
		);
	}

	/**
	 * Setup user caps.
	 */
	public static function setup_user_caps() {
		$admin        = get_role( 'administrator' );
		$shop_manager = get_role( 'shop_manager' );

		// Photography caps.
		if ( ! empty( $admin ) && ! empty( $shop_manager ) ) {
			$admin->add_cap( 'manage_photography' );
			$shop_manager->add_cap( 'manage_photography' );
		}
	}
}

new WC_Photography_Install();
