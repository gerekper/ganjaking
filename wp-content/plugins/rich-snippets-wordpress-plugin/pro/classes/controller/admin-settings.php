<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Settings_Section;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Settings.
 *
 * Admin settings actions.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.19.0
 */
class Admin_Settings_Controller extends \wpbuddy\rich_snippets\Admin_Settings_Controller {
	/**
	 * Admin_Settings_Controller constructor.
	 *
	 * @since 2.19.0
	 */
	public function __construct() {
		add_filter( 'wpbuddy/rich_snippets/settings', [ $this, 'pro_settings' ] );
		parent::__construct();
	}

	/**
	 * Add PRO settings.
	 *
	 * @param Settings_Section[] $settings
	 *
	 * @return Settings_Section[]
	 *
	 * @since 2.19.0
	 */
	public function pro_settings( $settings ) {

		if ( true === (bool) get_option( 'wpb_rs/predefined/message/hidden', false ) ) {
			$settings['actions']->add_setting( array(
				'title'       => __( 'Reinstall predefined global snippets.', 'rich-snippets-schema' ),
				'label'       => __( 'Go for it!', 'rich-snippets-schema' ),
				'type'        => 'button',
				'href'        => admin_url( 'edit.php?post_type=wpb-rs-global&install_predefined=1&_wpnonce=' ) . wp_create_nonce( 'wpbrs_install_predefined' ),
				'description' => __( 'The plugin comes shipped with pre-installed snippets. If you messed them up, just hit the above button to re-install and/or repair them.', 'rich-snippets-schema' ),
			) );
		}

		if ( function_exists( 'WC' ) ) {
			$settings['frontend']->add_setting( array(
				'label'             => __( 'Remove WooCommerce schema', 'rich-snippets-schema' ),
				'title'             => __( 'WooCommerce', 'rich-snippets-schema' ),
				'type'              => 'checkbox',
				'name'              => 'remove_wc_schema',
				'default'           => false,
				'sanitize_callback' => array( Helper_Model::instance(), 'string_to_bool' ),
				'autoload'          => true,
				'description'       => __( 'WooCommerce adds its own schema.org syntax for products. If you don\'t want to use it, the plugin can try to remove it for you so that you can write your own Rich Snippets for products.', 'rich-snippets-schema' ),
			) );

			$settings['frontend']->add_setting( array(
				'label'             => __( 'PreOrder instead of LimitedAvailability', 'rich-snippets-schema' ),
				'title'             => __( 'Item availability', 'rich-snippets-schema' ),
				'type'              => 'checkbox',
				'name'              => 'wc_availability_use_preorder',
				'default'           => false,
				'sanitize_callback' => array( Helper_Model::instance(), 'string_to_bool' ),
				'autoload'          => true,
				'description'       => __( 'Set schema.org/PreOrder instead of schema.org/LimitedAvailability if item is out of stock and the "WooCommerde: Availability" or "WooCommerce: Offers" field types are used.', 'rich-snippets-schema' ),
			) );
		}

		return $settings;
	}
}