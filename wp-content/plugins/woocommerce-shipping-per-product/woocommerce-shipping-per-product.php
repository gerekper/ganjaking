<?php
/**
 * Plugin Name: WooCommerce Shipping Per Product v2
 * Plugin URI: https://woocommerce.com/products/per-product-shipping/
 * Description: Per product shipping allows you to define different shipping costs for products, based on customer location. These costs can be added to other shipping methods, or used as a standalone shipping method.
 * Version: 2.3.16
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Requires at least: 3.3
 * Tested up to: 5.8
 * WC requires at least: 2.6
 * WC tested up to: 5.9
 *
 * Copyright: © 2021 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 18590:ba16bebba1d74992efc398d575bf269e
 *
 * @package WC_Shipping_Per_Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( __FILE__, 'woocommerce_shipping_per_product_install' );

/**
 * Installer.
 */
function woocommerce_shipping_per_product_install() {
	include_once 'installer.php';
}

/**
 * WooCommerce fallback notice.
 *
 * @since 2.3.8
 */
function woocommerce_shipping_per_product_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Per Product Shipping requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-shipping-per-product' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

if ( ! class_exists( 'WC_Shipping_Per_Product_Init' ) ) :
	define( 'PER_PRODUCT_SHIPPING_VERSION', '2.3.16' ); // WRCS: DEFINED_VERSION.
	define( 'PER_PRODUCT_SHIPPING_FILE', __FILE__ );

	/**
	 * Wrapper class to init the plugin.
	 *
	 * @since 1.0.0
	 */
	class WC_Shipping_Per_Product_Init {
		/**
		 * The single instance of the class.
		 *
		 * @var $_instance
		 * @since 2.3.8
		 */
		protected static $_instance = null;

		/**
		 *
		 */
		protected static $shipping_costs = array();

		/**
		 * Constructor.
		 */
		public function __construct() {
			if ( is_admin() ) {
				include_once 'includes/class-wc-shipping-per-product-admin.php';
				new WC_Shipping_Per_Product_Admin( $this );
			}

			include_once 'includes/functions-wc-shipping-per-product.php';

			add_action( 'woocommerce_shipping_init', array( $this, 'load_shipping_method' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
			add_filter( 'woocommerce_translations_updates_for_woocommerce_shipping_per_product', '__return_true' );
			add_action( 'admin_init', array( $this, 'register_importer' ) );
			$this->load_post_wc_class();
		}

		/**
		 * Main Instance.
		 *
		 * Ensures only one instance is loaded or can be loaded.
		 *
		 * @since 2.3.8
		 * @return WC_Shipping_Per_Product_Init
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Loads any class that needs to check for WC loaded.
		 *
		 * @since 2.2.13
		 */
		public function load_post_wc_class() {
			require_once __DIR__ . '/includes/class-wc-shipping-per-product-privacy.php';
		}

		/**
		 * Load shipping method class and related hooks.
		 */
		public function load_shipping_method() {
			// Priority 15 because Filter must trigger after Advanced Shipping Packages because it expects there to be only one package.
			add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'split_shipping_packages_per_product' ), 15 );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'register_shipping_method' ) );
		}

		/**
		 * Filter plugin action links.
		 *
		 * @since 2.2.9
		 * @version 2.2.9
		 *
		 * @param array $links Plugin action links.
		 *
		 * @return array Plugin action links.
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=per_product' ) . '">' . __( 'Settings', 'woocommerce-shipping-per-product' ) . '</a>',
				'<a href="https://woocommerce.com/my-account/create-a-ticket/?form=18590">' . __( 'Support', 'woocommerce-shipping-per-product' ) . '</a>',
				'<a href="https://docs.woocommerce.com/document/per-product-shipping/">' . __( 'Docs', 'woocommerce-shipping-per-product' ) . '</a>',
			);
			return array_merge( $plugin_links, $links );
		}

		/**
		 * Show row meta on the plugin screen.
		 *
		 * @param array  $links Plugin Row Meta.
		 * @param string $file  Plugin Base file.
		 *
		 * @return array
		 */
		public function plugin_row_meta( $links, $file ) {
			if ( plugin_basename( __FILE__ ) === $file ) {
				$row_meta = array(
					'docs'    => '<a href="' . esc_url( apply_filters( 'woocommerce_per_product_shipping_docs_url', 'http://docs.woothemes.com/document/per-product-shipping/' ) ) . '" title="' . esc_attr( __( 'View Documentation', 'woocommerce-shipping-per-product' ) ) . '">' . __( 'Docs', 'woocommerce-shipping-per-product' ) . '</a>',
					'support' => '<a href="' . esc_url( apply_filters( 'woocommerce_per_product_shipping_support_url', 'https://woocommerce.com/my-account/create-a-ticket/?form=18590' ) ) . '" title="' . esc_attr__( 'Visit Premium Customer Support Forum', 'woocommerce-shipping-per-product' ) . '">' . __( 'Premium Support', 'wc_shipping_per_products' ) . '</a>',
				);
				return array_merge( $links, $row_meta );
			}
			return (array) $links;
		}

		/**
		 * Register the importer.
		 */
		public function register_importer() {
			if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
				register_importer( 'woocommerce_per_product_shipping_csv', __( 'WooCommerce Per-product shipping rates (CSV)', 'woocommerce-shipping-per-product' ), __( 'Import <strong>per-product shipping rates</strong> to your store via a csv file.', 'woocommerce-shipping-per-product' ), array( $this, 'importer' ) );
			}
		}

		/**
		 * Load the importer.
		 */
		public function importer() {
			require_once ABSPATH . 'wp-admin/includes/import.php';

			if ( ! class_exists( 'WP_Importer' ) ) {
				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
				if ( file_exists( $class_wp_importer ) ) {
					require $class_wp_importer;
				}
			}

			include_once 'includes/class-wc-shipping-per-product-importer.php';

			$importer = new WC_Shipping_Per_Product_Importer();
			$importer->dispatch();
		}

		/**
		 * Register the shipping method.
		 *
		 * @param array $methods Shipping methods.
		 *
		 * @return array Shipping methods.
		 */
		public function register_shipping_method( $methods ) {
			include_once 'includes/class-wc-shipping-per-product.php';
			$methods['per_product'] = 'WC_Shipping_Per_Product';

			// For backwards compatibility with 2.2.x installations we load the legacy shipping method if it's enabled.
			$use_legacy = $this->use_legacy_shipping_method();

			if ( false !== $use_legacy ) {
				// In this case the legacy shipping method is not enabled but some products may be using the deprecated _per_product_shipping_add_to_all option which will be checked in this filter.
				add_filter( 'woocommerce_package_rates', array( $this, 'adjust_package_rates' ), 10, 2 );
			}

			if ( true === $use_legacy ) {
				include_once 'includes/class-wc-shipping-legacy-per-product.php';
				$methods['legacy_per_product'] = 'WC_Shipping_Legacy_Per_Product';
			}

			return $methods;
		}

		/**
		 * Check if we should be using the legacy shipping method.
		 *
		 * @return bool|null
		 */
		public function use_legacy_shipping_method() {
			// Don't use legacy if a new method has been added to a zone.
			$data_store  = WC_Data_Store::load( 'shipping-zone' );
			$raw_zones   = $data_store->get_zones();
			$raw_zones[] = (object) array( 'zone_id' => 0 ); // Wee need to add zone 0 which is ' Locations not covered by your other zones'.
			// We have to use raw data or an infinite loop will occur.
			foreach ( $raw_zones as $raw_zone ) {
				$raw_methods = $data_store->get_methods( $raw_zone->zone_id, true );
				foreach ( $raw_methods as $raw_method ) {
					if ( 'per_product' === $raw_method->method_id ) {
						return false;
					}
				}
			}

			$options = get_option( 'woocommerce_per_product_settings' );
			if ( $options && isset( $options['enabled'] ) && 'yes' === $options['enabled'] ) {
				return true;
			}

			return null; // Return null because we may still need to adjust woocommerce_package_rates.
		}

		/**
		 * Generate a unique package key for a given shipping package to be used for caching package rates.
		 *
		 * @param array $package A shipping package in the form returned by WC_Cart->get_shipping_packages().
		 * @return string key hash
		 */
		protected function get_package_shipping_rates_cache_key( $package ) {
			return md5( json_encode( array( array_keys( $package['contents'] ), $package['contents_cost'], $package['applied_coupons'] ) ) );
		}

		/**
		 * Adjust package rates.
		 *
		 * @param array $rates   Rates.
		 * @param array $package Package.
		 *
		 * @return array
		 */
		public function adjust_package_rates( $rates, $package ) {
			// Some extensions (e.g. Subscriptions) call the action manually,
			// so we need to ensure item cost additions only run once.
			$cache_key = $this->get_package_shipping_rates_cache_key( $package );

			if ( isset( self::$shipping_costs[ $cache_key ] ) ) {
				return self::$shipping_costs[ $cache_key ];
			}

			$_tax = new WC_Tax();
			if ( $rates ) {
				foreach ( $rates as $rate_id => $rate ) {
					// Skip free shipping.
					if ( 0 === (int) $rate->cost && apply_filters( 'woocommerce_per_product_shipping_skip_free_method_' . $rate->method_id, true ) ) {
						continue;
					}
					// Skip self.
					if ( 'legacy_per_product' === $rate->method_id ) {
						continue;
					}
					if ( count( $package['contents'] ) > 0 ) {
						foreach ( $package['contents'] as $item_id => $values ) {
							if ( $values['quantity'] > 0 ) {
								if ( $values['data']->needs_shipping() ) {
									$item_shipping_cost = 0;
									$rule               = false;
									if ( $values['variation_id'] ) {
										$rule = woocommerce_per_product_shipping_get_matching_rule( $values['variation_id'], $package, false );
									}
									if ( false === $rule ) {
										$rule = woocommerce_per_product_shipping_get_matching_rule( $values['product_id'], $package, false );
									}
									if ( empty( $rule ) ) {
										continue;
									}
									$item_shipping_cost += (float) $rule->rule_item_cost * (int) $values['quantity'];
									$item_shipping_cost += (float) $rule->rule_cost;
									$rate->cost         += $item_shipping_cost;
									$rate_options        = get_option( 'woocommerce_' . $rate->get_method_id() . '_' . $rate->get_instance_id() . '_settings', true );
									if ( isset( $rate_options['tax_status'] ) && 'taxable' === $rate_options['tax_status'] ) {
										$tax_rates  = $_tax->get_shipping_tax_rates( $values['data']->get_tax_class() );
										$item_taxes = $_tax->calc_shipping_tax( $item_shipping_cost, $tax_rates );
										$taxes      = array();
										// Sum the item taxes.
										foreach ( array_keys( $rate->taxes + $item_taxes ) as $key ) {
											$taxes[ $key ] = ( isset( $item_taxes[ $key ] ) ? $item_taxes[ $key ] : 0 ) + ( isset( $rate->taxes[ $key ] ) ? $rate->taxes[ $key ] : 0 );
										}
										$rate->set_taxes( $taxes );
									}
								}
							}
						}
					}
				}
			}

			self::$shipping_costs[ $cache_key ] = $rates;

			return $rates;
		}

		/**
		 * Splits products with per product shipping enabled into separate packages.
		 *
		 * @param array $packages Packages to maybe split.
		 * @return array
		 */
		public function split_shipping_packages_per_product( array $packages ) {

			$new_packages = array();
			foreach ( $packages as $package_id => $package ) {

				/** @var $per_product_shipping_method WC_Shipping_Per_Product */
				$per_product_shipping_method = null;

				$zone = WC_Shipping_Zones::get_zone_matching_package( $package );

				foreach ( $zone->get_shipping_methods( true ) as $method ) {
					if ( $method instanceof WC_Shipping_Per_Product ) {
						$per_product_shipping_method = $method;
						break;
					}
				};

				if ( null === $per_product_shipping_method ) {
					continue; // Per Product shipping not enabled for the zone this package is going to.
				}

				$new_package = array();

				foreach ( $package['contents'] as $item_id => $values ) {

					if ( $per_product_shipping_method->is_per_product_shipping_product( $values, $package ) ) {
						// Item shipping is calculated per-product, split it off and make new package.
						$new_package['contents'][ $item_id ] = $values;
						unset( $packages[ $package_id ]['contents'][ $item_id ] );
					}
				}

				if ( ! empty( $new_package ) ) {
					// Recalculate totals.
					$new_package['contents_cost'] = array_sum( wp_list_pluck( $new_package['contents'], 'line_total' ) );
					$package['contents_cost']     = array_sum( wp_list_pluck( $package['contents'], 'line_total' ) );

					// Copy field from original package.
					$new_package['applied_coupons'] = $package['applied_coupons'];
					$new_package['user']            = $package['user'];
					$new_package['destination']     = $package['destination'];
					$new_package['cart_subtotal']   = $package['cart_subtotal'];
					$new_package['ship_via']        = apply_filters( 'woocommerce_per_product_shipping_ship_via', array( WC_Shipping_Per_Product::METHOD_ID, 'free_shipping', 'local_pickup' ) );

					$new_packages[] = $new_package;

					if ( empty( $packages[ $package_id ]['contents'] ) ) {
						unset( $packages[ $package_id ] ); // remove empty packages.
					}
				}
			}

			return array_merge( $new_packages, $packages );
		}

	}
endif;

add_action( 'plugins_loaded', 'woocommerce_shipping_per_product_init' );

/**
 * Function that initalizes the extension.
 *
 * @since 2.3.8
 * @return object Instance of the extension.
 */
function woocommerce_shipping_per_product_init() {
	load_plugin_textdomain( 'woocommerce-shipping-per-product', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_shipping_per_product_missing_wc_notice' );
		return;
	}

	WC_Shipping_Per_Product_Init::instance();
}
