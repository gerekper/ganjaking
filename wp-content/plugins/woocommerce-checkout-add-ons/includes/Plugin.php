<?php
/**
 * WooCommerce Checkout Add-Ons
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons;

use SkyVerge\WooCommerce\Checkout_Add_Ons\Integrations\WC_Subscriptions_Integration;
use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Admin\Admin;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Frontend\Frontend;

defined( 'ABSPATH' ) or exit;

/**
 * Checkout Add-Ons main plugin class.
 *
 * @since 2.0.0
 */
class Plugin extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '2.5.2';

	/** plugin id */
	const PLUGIN_ID = 'checkout_add_ons';

	/** plugin meta prefix */
	const PLUGIN_PREFIX = 'wc_checkout_add_ons_';

	/** @var Plugin single instance of this plugin */
	protected static $instance;

	/** @var Admin instance of admin handler */
	private $admin;

	/** @var Frontend instance of frontend handler */
	private $frontend;

	/** @var Export_Handler instance of export handler */
	private $export_handler;

	/** @var bool if WooCommerce Subscriptions is active */
	private $subscriptions_active;

	/** @var WC_Subscriptions_Integration */
	private $subscription_integration;


	/**
	 * Constructs the class.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array( 'text_domain' => 'woocommerce-checkout-add-ons' )
		);

		// set up handlers
		add_action( 'init', array( $this, 'setup_handlers' ) );

		// save checkout add-ons value
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'process_shop_order_meta' ), 10, 2 );

		// override default select/multiselect/radio value sanitization in special cases
		add_filter( 'sanitize_title', array( $this, 'sanitize_select_field_values' ), 10, 3 );
	}


	/**
	 * Instantiates handlers and stores a reference to them.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function setup_handlers() {

		$this->frontend                 = new Frontend();
		$this->export_handler           = new Export_Handler();
		$this->admin                    = new Admin();
		$this->subscription_integration = new WC_Subscriptions_Integration();
	}


	/**
	 * Gets deprecated hooks.
	 *
	 * $old_hook_name = array {
	 *   @type string $version version the hook was deprecated/removed in
	 *   @type bool $removed if present and true, the message will indicate the hook was removed instead of deprecated
	 *   @type string|bool $replacement if present and a string, the message will indicate the replacement hook to use,
	 *     otherwise (if bool and false) the message will indicate there is no replacement available.
	 * }
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_deprecated_hooks() {

		return array(
			'wc_checkout_add_ons_add_on_tax_class' => array(
				'version'     => '2.0.0',
				'removed'     => true,
				'map'         => true,
				'replacement' => 'woocommerce_checkout_add_on_get_tax_class',
			),
			'wc_checkout_add_ons_add_on_get_cost' => array(
				'version'     => '2.0.0',
				'removed'     => true,
				'map'         => true,
				'replacement' => 'woocommerce_checkout_add_on_get_adjustment',
			),
			'wc_checkout_add_ons_add_on_get_cost_type' => array(
				'version'     => '2.0.0',
				'removed'     => true,
				'map'         => true,
				'replacement' => 'woocommerce_checkout_add_on_get_adjustment_type',
			),
			'wc_checkout_add_ons_options' => array(
				'version'     => '2.0.0',
				'removed'     => true,
				'map'         => true,
				'replacement' => 'woocommerce_checkout_add_on_get_options',
			),
			'wc_checkout_add_ons_add_on_name' => array(
				'version'     => '2.0.0',
				'removed'     => true,
				'map'         => true,
				'replacement' => 'woocommerce_checkout_add_on_get_name',
			),
			'wc_checkout_add_ons_add_on_label' => array(
				'version'     => '2.0.0',
				'removed'     => true,
				'map'         => true,
				'replacement' => 'woocommerce_checkout_add_on_get_label',
			),
		);
	}


	/**
	 * Processes checkout add-on values when order is saved.
	 *
	 * @since 1.2.0
	 *
	 * @param int $order_id Order ID
	 * @param \WP_Post $post
	 */
	public function process_shop_order_meta( $order_id, $post ) {

		$this->save_order_item_values( $order_id, $_POST );
	}


	/**
	 * Saves checkout add-on values.
	 *
	 * @since 1.2.0
	 *
	 * @param int $order_id Order ID
	 * @param array $items Order items to save
	 */
	public function save_order_item_values( $order_id, $items ) {

		if ( isset( $items['checkout_add_on_item_id'] ) ) {

			$item_ids = $items['checkout_add_on_item_id'];

			foreach ( $item_ids as $item_id ) {

				$item_id = absint( $item_id );

				if ( isset( $items['checkout_add_on_value'][ $item_id ] ) && isset( $items['checkout_add_on_id'][ $item_id ] ) ) {

					$add_on = Add_On_Factory::get_add_on( $items['checkout_add_on_id'][ $item_id ] );

					wc_update_order_item_meta( $item_id, '_wc_checkout_add_on_value', $items['checkout_add_on_value'][ $item_id ] );
					wc_update_order_item_meta( $item_id, '_wc_checkout_add_on_label', $add_on->normalize_value( $items['checkout_add_on_value'][ $item_id ], true ) );
				}
			}
		}
	}


	/**
	 * Replaces some characters lost from select/multiselect/radio value sanitization.
	 *
	 * @since 1.6.1
	 *
	 * @param string $title The sanitized value.
	 * @param string $raw_title The raw value.
	 * @param string $context The context for which the title is being sanitized.
	 * @return string $title The sanitized value with special handling.
	 */
	public function sanitize_select_field_values( $title, $raw_title, $context ) {

		if ( 'wc_checkout_add_ons_sanitize' !== $context ) {
			return $title;
		}

		$title = remove_accents( $title );

		// If the value is a negative, add the leading dash back
		if ( is_numeric( $raw_title ) && Framework\SV_WC_Helper::str_starts_with( $raw_title, '-' ) ) {
			$title = '-' . $title;
		}

		return $title;
	}


	/** Helper methods ******************************************************/


	/**
	 * Gets the Admin instance.
	 *
	 * @since 1.8.0
	 *
	 * @return Admin
	 */
	public function get_admin_instance() {
		return $this->admin;
	}


	/**
	 * Gets the Front End instance.
	 *
	 * @since 1.8.0
	 *
	 * @return Frontend|null
	 */
	public function get_frontend_instance() {
		return $this->frontend;
	}


	/**
	 * Gets the Export Handler instance.
	 *
	 * @since 1.8.0
	 *
	 * @return Export_Handler|null
	 */
	public function get_export_handler() {
		return $this->export_handler;
	}


	/**
	 * Allows other plugins to easily get add-ons for a given order.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id WC_Order ID
	 * @return Add_On[] array of Add_On objects
	 */
	public function get_order_add_ons( $order_id ) {

		$order         = wc_get_order( $order_id );
		$order_add_ons = array();

		foreach ( $order->get_items( 'fee' ) as $fee_id => $fee ) {

			// bail for fees that aren't add-ons or deleted add-ons
			if ( empty( $fee['wc_checkout_add_on_id'] ) ) {
				continue;
			}

			if ( $add_on = Add_On_Factory::get_add_on( $fee['wc_checkout_add_on_id'] ) ) {

				$order_add_ons[ $fee['wc_checkout_add_on_id'] ] = array(
					'name'             => $add_on->get_name(),
					'checkout_label'   => $add_on->get_label(),
					'value'            => $fee['wc_checkout_add_on_value'],
					'normalized_value' => maybe_unserialize( $fee['wc_checkout_add_on_label'] ),
					'total'            => $fee['line_total'],
					'total_tax'        => $fee['line_tax'],
					'fee_id'           => $fee_id,
				);
			}
		}

		return $order_add_ons;
	}


	/**
	 * Allows other plugins to easily get add-ons for a given order.
	 *
	 * @since 2.0.5
	 *
	 * @param int $order_id WC_Order ID
	 * @return Add_On[] array of Add_On objects
	 */
	public function get_order_renewable_add_ons( $order_id ) {

		$order         = wc_get_order( $order_id );
		$order_add_ons = array();

		foreach ( $order->get_items( 'fee' ) as $fee_id => $fee ) {

			// bail for fees that aren't add-ons or deleted add-ons
			if ( empty( $fee['wc_checkout_add_on_id'] ) ) {
				continue;
			}

			if ( $add_on = Add_On_Factory::get_add_on( $fee['wc_checkout_add_on_id'] ) ) {

				if ( $add_on->is_renewable() ) {

					$order_add_ons[ $fee['wc_checkout_add_on_id'] ] = array(
						'name'             => $add_on->get_name(),
						'checkout_label'   => $add_on->get_label(),
						'value'            => $fee['wc_checkout_add_on_value'],
						'normalized_value' => maybe_unserialize( $fee['wc_checkout_add_on_label'] ),
						'total'            => $fee['line_total'],
						'total_tax'        => $fee['line_tax'],
						'fee_id'           => $fee_id,
					);
				}
			}
		}

		return $order_add_ons;
	}


	/**
	 * Gets the URL to the settings page.
	 *
	 * @see SV_WC_Plugin::is_plugin_settings()
	 *
	 * @since 2.0.0
	 *
	 * @param string|null $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = null ) {
		return admin_url( 'admin.php?page=wc_checkout_add_ons' );
	}


	/**
	 * Gets the plugin documentation URL.
	 *
	 * @see SV_WC_Plugin::get_documentation_url()
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {
		return 'https://docs.woocommerce.com/document/woocommerce-checkout-add-ons/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @see SV_WC_Plugin::get_support_url()
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_support_url() {
		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @see SV_WC_Plugin::get_sales_page_url()
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {
		return 'https://woocommerce.com/products/woocommerce-checkout-add-ons/';
	}


	/**
	 * Returns true if on the gateway settings page.
	 *
	 * @see SV_WC_Plugin::is_plugin_settings()
	 *
	 * @since 2.0.0
	 *
	 * @return boolean true if on the settings page
	 */
	public function is_plugin_settings() {
		return isset( $_GET['page'] ) && 'wc_checkout_add_ons' === $_GET['page'];
	}


	/**
	 * Determines if WooCommerce Subscriptions is active.
	 *
	 * @since 1.7.1
	 * @return bool
	 */
	public function is_subscriptions_active() {

		if ( is_bool( $this->subscriptions_active ) ) {
			return $this->subscriptions_active;
		}

		return $this->subscriptions_active = $this->is_plugin_active( 'woocommerce-subscriptions.php' );
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @see SV_WC_Plugin::get_plugin_name()
	 *
	 * @since 2.0.0
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Checkout Add-Ons', 'woocommerce-checkout-add-ons' );
	}


	/**
	 * Returns __DIR__.
	 *
	 * @since 2.0.0
	 *
	 * @see SV_WC_Plugin::get_file()
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __DIR__;
	}


	/**
	 * Initializes the lifecycle handler.
	 *
	 * @since 2.0.0
	 */
	protected function init_lifecycle_handler() {

		$this->lifecycle_handler = new Lifecycle( $this );
	}


	/**
	 * Main Checkout Add-Ons Instance, ensures only one instance is/can be loaded
	 *
	 * @see wc_checkout_add_ons()
	 *
	 * @since 2.0.0
	 *
	 * @return Plugin
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


}
