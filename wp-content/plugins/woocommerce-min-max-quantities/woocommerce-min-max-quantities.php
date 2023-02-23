<?php
/**
 * Plugin Name: WooCommerce Min/Max Quantities
 * Plugin URI: https://woocommerce.com/products/minmax-quantities/
 * Description: Define minimum/maximum allowed quantities for products, variations and orders.
 * Version: 4.0.7
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Requires at least: 4.4
 * Tested up to: 6.0
 * WC tested up to: 6.9.0
 * WC requires at least: 3.9.0
 *
 * Text Domain: woocommerce-min-max-quantities
 * Domain Path: /languages
 *
 * Copyright: © 2022 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Woo: 18616:2b5188d90baecfb781a5aa2d6abb900a
 *
 * @package woocommerce-min-max-quantities
 */

if ( ! class_exists( 'WC_Min_Max_Quantities' ) ) :

	define( 'WC_MIN_MAX_QUANTITIES', '4.0.7' ); // WRCS: DEFINED_VERSION.

	/**
	 * Min Max Quantities class.
	 */
	class WC_Min_Max_Quantities {

		/**
		 * Minimum WooCommerce version.
		 *
		 * @var string
		 */
		public $min_wc_version = '3.9.0';

		/**
		 * Minimum order quantity.
		 *
		 * @var int
		 */
		public $minimum_order_quantity;

		/**
		 * Maximum order quantity.
		 *
		 * @var int
		 */
		public $maximum_order_quantity;

		/**
		 * Minimum order value.
		 *
		 * @var int
		 */
		public $minimum_order_value;

		/**
		 * Maximum order value.
		 *
		 * @var int
		 */
		public $maximum_order_value;

		/**
		 * List of excluded product titles.
		 *
		 * @var array
		 */
		public $excludes = array();

		/**
		 * Instance of compatibility class.
		 *
		 * @var WC_MMQ_Compatibility
		 */
		public $compatibility;

		/**
		 * Instance of addons class.
		 *
		 * @var WC_Min_Max_Quantities_Addons
		 */
		public $addons;

		/**
		 * Class instance.
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Get the class instance.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		public function __construct() {

			if ( ! function_exists( 'WC' ) || version_compare( WC()->version, $this->min_wc_version ) < 0 ) {
				add_action( 'admin_notices', array( $this, 'woocommerce_required_notice' ) );
				return;
			}

			$this->maybe_define_constant( 'WC_MMQ_ABSPATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			/**
			 * Localisation.
			 */
			$this->load_plugin_textdomain();

			if ( is_admin() ) {
				include_once WC_MMQ_ABSPATH . '/includes/class-wc-min-max-quantities-admin.php';
			}

			// Extensions compatibility functions and hooks.
			include_once WC_MMQ_ABSPATH . 'includes/compatibility/class-wc-min-max-quantities-compatibility.php';
			$this->compatibility = WC_MMQ_Compatibility::instance();

			if ( $this->compatibility->is_module_loaded( 'product_addons' ) ) {
				$this->addons = new WC_Min_Max_Quantities_Addons();
			}

			$this->minimum_order_quantity = absint( get_option( 'woocommerce_minimum_order_quantity' ) );
			$this->maximum_order_quantity = absint( get_option( 'woocommerce_maximum_order_quantity' ) );
			$this->minimum_order_value    = absint( get_option( 'woocommerce_minimum_order_value' ) );
			$this->maximum_order_value    = absint( get_option( 'woocommerce_maximum_order_value' ) );

			// Check items.
			add_action( 'woocommerce_check_cart_items', array( $this, 'check_cart_items' ) );

			// If we have errors, make sure those are shown on the checkout page
			add_action( 'woocommerce_cart_has_errors', array( $this, 'output_errors' ) );

			// Quantity selelectors (2.0+).
			add_filter( 'woocommerce_quantity_input_args', array( $this, 'update_quantity_args' ), 10, 2 );
			add_filter( 'woocommerce_available_variation', array( $this, 'available_variation' ), 10, 3 );
			add_filter( 'wc_min_max_use_group_as_min_quantity', array( $this, 'use_group_as_min_quantity' ), 10, 3 );

			// Prevent add to cart.
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart' ), 10, 4 );

			// Min add to cart ajax.
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'add_to_cart_link' ), 10, 2 );

			// Show a notice when items would have to be on back order because of min/max.
			add_filter( 'woocommerce_get_availability', array( $this, 'maybe_show_backorder_message' ), 10, 2 );

			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

			add_filter( 'woocommerce_add_to_cart_product_id', array( $this, 'modify_add_to_cart_quantity' ) );

			// Declare HPOS compatibility.
			add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
		}

		/**
		 * Define constants if not present.
		 *
		 * @since 4.0.4
		 *
		 * @return boolean
		 */
		protected function maybe_define_constant( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Plugin URL getter.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Output a notice if Woocommerce isn't active.
		 */
		public function woocommerce_required_notice() {

			?><div class="notice notice-error is-dismissible">
				<p>
					<?php
					/* translators: Minimum required WooCommerce version */
					echo sprintf( __( '<strong>Min/Max Quantities</strong> requires at least WooCommerce <strong>%s</strong>.', 'woocommerce-min-max-quantities' ), $this->min_wc_version );
					?>
				</p>
			</div><?php
		}

		/**
		 * Load scripts.
		 */
		public function load_scripts() {
			// Only load on single product page and cart page.
			if ( is_product() || is_cart() ) {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_register_script( 'wc-mmq-frontend', $this->plugin_url() . '/assets/js/frontend/validate' . $suffix . '.js', array( 'jquery' ), WC_MIN_MAX_QUANTITIES );
				wp_enqueue_script( 'wc-mmq-frontend' );
			}
		}

		/**
		 * Declare HPOS( Custom Order tables) compatibility.
		 *
		 * @since 4.0.2
		 */
		public function declare_hpos_compatibility () {
			if ( ! class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				return;
			}

			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}

		/**
		 * Load Localisation files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 *
		 * Frontend/global Locales found in:
		 * - WP_LANG_DIR/woocommerce-min-max-quantities/woocommerce-min-max-quantities-LOCALE.mo
		 * - woocommerce-min-max-quantities/woocommerce-min-max-quantities-LOCALE.mo (which if not found falls back to:)
		 * - WP_LANG_DIR/plugins/woocommerce-min-max-quantities-LOCALE.mo
		 */
		public function load_plugin_textdomain() {
			// phpcs:ignore
			$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-min-max-quantities' );

			load_textdomain( 'woocommerce-min-max-quantities', WP_LANG_DIR . '/woocommerce-min-max-quantities/woocommerce-min-max-quantities-' . $locale . '.mo' );
			load_plugin_textdomain( 'woocommerce-min-max-quantities', false, plugin_basename( dirname( __FILE__ ) ) . '/' );
		}

		/**
		 * Add an error.
		 *
		 * @since 1.0.0
		 * @version 2.3.18
		 * @param string $error Error text.
		 */
		public function add_error( $error = '' ) {
			if ( $error && ! wc_has_notice( $error, 'error' ) ) {
				wc_add_notice( $error, 'error', array( 'source' => 'woocommerce-min-max-quantities' ) );
			}
		}

		/**
		 * Output any plugin specific error messages
		 *
		 * We use this instead of wc_print_notices so we
		 * can remove any error notices that aren't from us.
		 */
		public function output_errors() {
			$notices  = wc_get_notices( 'error' );
			$messages = array();

			foreach ( $notices as $i => $notice ) {
				if ( isset( $notice['notice'] ) && isset( $notice['data']['source'] ) && 'woocommerce-min-max-quantities' === $notice['data']['source'] ) {
					$messages[] = $notice['notice'];
				} else {
					unset( $notice[ $i ] );
				}
			}

			if ( ! empty( $messages ) ) {
				ob_start();

				wc_get_template(
					'notices/error.php',
					array(
						'messages' => array_filter( $messages ), // @deprecated 3.9.0
						'notices'  => array_filter( $notices ),
					)
				);

				echo wc_kses_notice( ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Add quantity property to add to cart button on shop loop for simple products.
		 *
		 * @param  string     $html    Add to cart link.
		 * @param  WC_Product $product Product object.
		 * @return string
		 */
		public function add_to_cart_link( $html, $product ) {

			if ( 'variable' !== $product->get_type() ) {
				$quantity_attribute = 1;
				$minimum_quantity   = absint( get_post_meta( $product->get_id(), 'minimum_allowed_quantity', true ) );
				$group_of_quantity  = $this->get_group_of_quantity_for_product( $product );

				if ( $minimum_quantity || $group_of_quantity ) {

					$quantity_attribute = $minimum_quantity;

					if ( $group_of_quantity > 0 && $minimum_quantity < $group_of_quantity ) {
						$quantity_attribute = $group_of_quantity;
					}

					$html = str_replace( '<a ', '<a data-quantity="' . $quantity_attribute . '" ', $html );
				}
			}

			return $html;
		}

		/**
		 * Get product or variation ID to check
		 *
		 * @param array $values List of values.
		 * @return int
		 */
		public function get_id_to_check( $values ) {
			if ( $values['variation_id'] ) {
				$min_max_rules     = get_post_meta( $values['variation_id'], 'min_max_rules', true );
				$allow_combination = ( 'yes' === get_post_meta( $values['product_id'], 'allow_combination', true ) );

				if ( 'yes' === $min_max_rules && ! $allow_combination ) {
					$checking_id = $values['variation_id'];
				} else {
					$checking_id = $values['product_id'];
				}
			} else {
				$checking_id = $values['product_id'];
			}

			return $checking_id;
		}

		/**
		 * Validate cart items against set rules
		 *
		 * @throws Exception
		 */
		public function check_cart_items() {

			try {
				$checked_ids         = array();
				$product_quantities  = array();
				$category_quantities = array();
				$total_quantity      = 0;
				$total_cost          = 0;
				$apply_cart_rules    = false;

				// Count items + variations first.
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$product     = $values[ 'data' ];
					$checking_id = $this->get_id_to_check( $values );

					/**
					 * Use this filter to prevent the quantity of a cart item from being counted in cart-level rules.
					 *
					 * @since 2.4.11
					 *
					 * @param  boolean $do_not_count
					 * @param  int     $checking_id
					 * @param  string  $cart_item_key
					 * @param  array   $values
					 */
					if ( apply_filters( 'wc_min_max_cart_quantity_do_not_count', false, $checking_id, $cart_item_key, $values ) ) {
						$values[ 'quantity' ] = 0;
					}

					if ( ! isset( $product_quantities[ $checking_id ] ) ) {
						$product_quantities[ $checking_id ] = $values[ 'quantity' ];
					} else {
						$product_quantities[ $checking_id ] += $values[ 'quantity' ];
					}

					/**
					 * Use this filter to prevent the quantity or cost of a cart item from being counted in cart-level rules.
					 *
					 * @since 2.3.6
					 *
					 * @param  boolean $do_not_count
					 * @param  int     $checking_id
					 * @param  string  $cart_item_key
					 * @param  array   $values
					 */
					$minmax_do_not_count = apply_filters( 'wc_min_max_quantity_minmax_do_not_count', ( 'yes' === get_post_meta( $checking_id, 'variation_minmax_do_not_count', true ) ? 'yes' : get_post_meta( $values[ 'product_id' ], 'minmax_do_not_count', true ) ), $checking_id, $cart_item_key, $values );

					/**
					 * Use this filter to exclude a product from cart-level rules.
					 *
					 * @since 2.3.6
					 *
					 * @param  boolean $exclude
					 * @param  int     $checking_id
					 * @param  string  $cart_item_key
					 * @param  array   $values
					 */
					$minmax_cart_exclude = apply_filters( 'wc_min_max_quantity_minmax_cart_exclude', ( 'yes' === get_post_meta( $checking_id, 'variation_minmax_cart_exclude', true ) ? 'yes' : get_post_meta( $values[ 'product_id' ], 'minmax_cart_exclude', true ) ), $checking_id, $cart_item_key, $values );

					if ( 'yes' !== $minmax_do_not_count && 'yes' !== $minmax_cart_exclude ) {
						$total_cost += (float) $product->get_price() * (float) $values['quantity'];
					}
				}

				// Check cart items.
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$checking_id    = $this->get_id_to_check( $values );
					$terms          = get_the_terms( $values[ 'product_id' ], 'product_cat' );
					$found_term_ids = array();

					// If a product belongs to multiple categories with different 'Group of' values, find the category with the smallest 'Group of' value.
					$min_group_of_category_id    = 0;
					$min_group_of_category_value = 0;

					if ( $terms ) {

						foreach ( $terms as $term ) {

							if ( 'yes' === get_post_meta( $checking_id, 'variation_minmax_category_group_of_exclude', true ) || 'yes' === get_post_meta( $values[ 'product_id' ], 'minmax_category_group_of_exclude', true ) ) {
								continue;
							}

							if ( in_array( $term->term_id, $found_term_ids, true ) ) {
								continue;
							}

							$found_term_ids[]        = $term->term_id;
							$category_group_of_value = absint( get_term_meta( $term->term_id, 'group_of_quantity', true ) );

							if ( 0 !== $category_group_of_value && ( 0 === $min_group_of_category_value || $category_group_of_value < $min_group_of_category_value ) ) {
								$min_group_of_category_value = $category_group_of_value;
								$min_group_of_category_id    = $term->term_id;
							}

							// Record count in parents of this category too.
							$parents = get_ancestors( $term->term_id, 'product_cat' );

							foreach ( $parents as $parent ) {
								if ( in_array( $parent, $found_term_ids, true ) ) {
									continue;
								}

								$found_term_ids[]        = $parent;
								$category_group_of_value = absint( get_term_meta( $parent, 'group_of_quantity', true ) );

								if ( 0 !== $category_group_of_value && ( 0 === $min_group_of_category_value || $category_group_of_value < $min_group_of_category_value ) ) {
									$min_group_of_category_value = $category_group_of_value;
									$min_group_of_category_id    = $parent;
								}
							}
						}
					}

					if ( 0 !== $min_group_of_category_id && 0 !== $min_group_of_category_value ) {
						$category_quantities[ $min_group_of_category_id ] = isset( $category_quantities[ $min_group_of_category_id ] )
							? $category_quantities[ $min_group_of_category_id ] + $values[ 'quantity' ]
							: $values[ 'quantity' ];
					}

					// Check item rules once per product ID.
					if ( in_array( $checking_id, $checked_ids, true ) ) {
						continue;
					}

					$product = $values[ 'data' ];

					/**
					 * Use this filter to prevent the quantity or cost of a cart item from being counted in cart-level rules.
					 *
					 * @since 2.3.6
					 *
					 * @param  boolean $do_not_count
					 * @param  int     $checking_id
					 * @param  string  $cart_item_key
					 * @param  array   $values
					 */
					$minmax_do_not_count = apply_filters( 'wc_min_max_quantity_minmax_do_not_count', ( 'yes' === get_post_meta( $checking_id, 'variation_minmax_do_not_count', true ) ? 'yes' : get_post_meta( $values[ 'product_id' ], 'minmax_do_not_count', true ) ), $checking_id, $cart_item_key, $values );

					/**
					 * Use this filter to exclude a product from cart-level rules.
					 *
					 * @since 2.3.6
					 *
					 * @param  boolean $exclude
					 * @param  int     $checking_id
					 * @param  string  $cart_item_key
					 * @param  array   $values
					 */
					$minmax_cart_exclude = apply_filters( 'wc_min_max_quantity_minmax_cart_exclude', ( 'yes' === get_post_meta( $checking_id, 'variation_minmax_cart_exclude', true ) ? 'yes' : get_post_meta( $values[ 'product_id' ], 'minmax_cart_exclude', true ) ), $checking_id, $cart_item_key, $values );

					if ( 'yes' === $minmax_do_not_count || 'yes' === $minmax_cart_exclude ) {
						// Do not count.
						$this->excludes[] = $product->get_name();

					} else {
						$total_quantity += $product_quantities[ $checking_id ];
					}

					if ( 'yes' !== $minmax_cart_exclude ) {
						$apply_cart_rules = true;
					}

					$checked_ids[] = $checking_id;

					if ( $values[ 'variation_id' ] ) {
						$min_max_rules     = get_post_meta( $values[ 'variation_id' ], 'min_max_rules', true );
						$allow_combination = 'yes' === get_post_meta( $values[ 'product_id' ], 'allow_combination', true );

						// Variation level min max rules enabled.
						if ( 'yes' === $min_max_rules && ! $allow_combination ) {

							/**
							 * Use this filter to filter the Minimum Quantity of a product/variation.
							 *
							 * @since 2.2.7
							 *
							 * @param  string  $quantity
							 * @param  int     $variation_id
							 * @param  string  $cart_item_key
							 * @param  array   $cart_item
							 */
							$minimum_quantity = absint( apply_filters( 'wc_min_max_quantity_minimum_allowed_quantity', get_post_meta( $values[ 'variation_id' ], 'variation_minimum_allowed_quantity', true ), $values[ 'variation_id' ], $cart_item_key, $values ) );

							/**
							 * Use this filter to filter the Maximum Quantity of a product/variation.
							 *
							 * @since 2.2.7
							 *
							 * @param  string  $quantity
							 * @param  int     $variation_id
							 * @param  string  $cart_item_key
							 * @param  array   $cart_item
							 */
							$maximum_quantity = absint( apply_filters( 'wc_min_max_quantity_maximum_allowed_quantity', get_post_meta( $values[ 'variation_id' ], 'variation_maximum_allowed_quantity', true ), $values[ 'variation_id' ], $cart_item_key, $values ) );

							/**
							 * Use this filter to filter the Group of quantity of a product/variation.
							 *
							 * @since 2.2.7
							 *
							 * @param  string  $quantity
							 * @param  int     $variation_id
							 * @param  string  $cart_item_key
							 * @param  array   $cart_item
							 */
							$group_of_quantity = absint( apply_filters( 'wc_min_max_quantity_group_of_quantity', get_post_meta( $values[ 'variation_id' ], 'variation_group_of_quantity', true ), $values[ 'variation_id' ], $cart_item_key, $values ) );

						} else {

							/**
							 * Use this filter to filter the Minimum Quantity of a product/variation.
							 *
							 * @since 2.2.7
							 *
							 * @param  string  $quantity
							 * @param  int     $product_id
							 * @param  string  $cart_item_key
							 * @param  array   $cart_item
							 */
							$minimum_quantity = absint( apply_filters( 'wc_min_max_quantity_minimum_allowed_quantity', get_post_meta( $values[ 'product_id' ], 'minimum_allowed_quantity', true ), $values[ 'product_id' ], $cart_item_key, $values ) );

							/**
							 * Use this filter to filter the Maximum Quantity of a product/variation.
							 *
							 * @since 2.2.7
							 *
							 * @param  string  $quantity
							 * @param  int     $product_id
							 * @param  string  $cart_item_key
							 * @param  array   $cart_item
							 */
							$maximum_quantity = absint( apply_filters( 'wc_min_max_quantity_maximum_allowed_quantity', get_post_meta( $values[ 'product_id' ], 'maximum_allowed_quantity', true ), $values[ 'product_id' ], $cart_item_key, $values ) );

							/**
							 * Use this filter to filter the Group of quantity of a product/variation.
							 *
							 * @since 2.2.7
							 *
							 * @param  string  $quantity
							 * @param  int     $product_id
							 * @param  string  $cart_item_key
							 * @param  array   $cart_item
							 */
							$group_of_quantity = absint( apply_filters( 'wc_min_max_quantity_group_of_quantity', get_post_meta( $values[ 'product_id' ], 'group_of_quantity', true ), $values[ 'product_id' ], $cart_item_key, $values ) );
						}
					} else {

						/**
						 * Use this filter to filter the Minimum Quantity of a product/variation.
						 *
						 * @since 2.2.7
						 *
						 * @param  string  $quantity
						 * @param  int     $product_id
						 * @param  string  $cart_item_key
						 * @param  array   $cart_item
						 */
						$minimum_quantity = absint( apply_filters( 'wc_min_max_quantity_minimum_allowed_quantity', get_post_meta( $checking_id, 'minimum_allowed_quantity', true ), $checking_id, $cart_item_key, $values ) );

						/**
						 * Use this filter to filter the Maximum Quantity of a product/variation.
						 *
						 * @since 2.2.7
						 *
						 * @param  string  $quantity
						 * @param  int     $product_id
						 * @param  string  $cart_item_key
						 * @param  array   $cart_item
						 */
						$maximum_quantity = absint( apply_filters( 'wc_min_max_quantity_maximum_allowed_quantity', get_post_meta( $checking_id, 'maximum_allowed_quantity', true ), $checking_id, $cart_item_key, $values ) );

						/**
						 * Use this filter to filter the Group of quantity of a product/variation.
						 *
						 * @since 2.2.7
						 *
						 * @param  string  $quantity
						 * @param  int     $product_id
						 * @param  string  $cart_item_key
						 * @param  array   $cart_item
						 */
						$group_of_quantity = absint( apply_filters( 'wc_min_max_quantity_group_of_quantity', get_post_meta( $checking_id, 'group_of_quantity', true ), $checking_id, $cart_item_key, $values ) );
					}

				$this->check_rules( $product, $product_quantities[ $checking_id ], $minimum_quantity, $maximum_quantity, $group_of_quantity, $checking_id );
			}

			// Cart rules.
			if ( $apply_cart_rules ) {

					$excludes = '';

					if ( count( $this->excludes ) > 0 ) {
						$excludes = ' (' . __( 'excludes ', 'woocommerce-min-max-quantities' ) . implode( ', ', $this->excludes ) . ')';
					}

					if ( $this->minimum_order_quantity > 0 && $total_quantity < $this->minimum_order_quantity ) {
						/* translators: %d: Minimum amount of items in the cart */
						$notice = sprintf( __( 'To place an order, your cart must contain at least %d items.', 'woocommerce-min-max-quantities' ), $this->minimum_order_quantity ) . $excludes;
						throw new Exception( $notice );
					}

					if ( $this->maximum_order_quantity > 0 && $total_quantity > $this->maximum_order_quantity ) {
						/* translators: %d: Maximum amount of items in the cart */
						$notice = sprintf( __( 'Your cart must not contain more than %d items to place an order.', 'woocommerce-min-max-quantities' ), $this->maximum_order_quantity );

						throw new Exception( $notice );

					}

					// Check cart value.
					if ( $this->minimum_order_value && $total_cost < $this->minimum_order_value ) {
						/* translators: %s: Minimum order value */
						$notice = sprintf( __( 'To place an order, your cart total must be at least %s.', 'woocommerce-min-max-quantities' ), wc_price( $this->minimum_order_value ) ) . $excludes;

						throw new Exception( $notice );
					}

					if ( $this->maximum_order_value && $total_cost > $this->maximum_order_value ) {
						/* translators: %s: Maximum order value */
						$notice = sprintf( __( 'Your cart total must not be higher than %s to place an order.', 'woocommerce-min-max-quantities' ), wc_price( $this->maximum_order_value ) );

						throw new Exception( $notice );
					}
				}

				// Check category rules.
				foreach ( $category_quantities as $category => $quantity ) {

					$group_of_quantity = get_term_meta( $category, 'group_of_quantity', true );

					if ( $group_of_quantity > 0 && ( intval( $quantity ) % intval( $group_of_quantity ) > 0 ) ) {

						$term          = get_term_by( 'id', $category, 'product_cat' );
						$product_names = array();

						foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

							// If exclude is enable, skip.
							if ( 'yes' === get_post_meta( $values[ 'product_id' ], 'minmax_category_group_of_exclude', true ) || 'yes' === get_post_meta( $values[ 'variation_id' ], 'variation_minmax_category_group_of_exclude', true ) ) {
								continue;
							}

							if ( has_term( $category, 'product_cat', $values[ 'product_id' ] ) ) {
								$product_names[] = $values[ 'data' ]->get_title();
							}
						}

						if ( $product_names ) {
							/* translators: %1$s: Category name, %2$s: Comma separated list of product names, %3$d: Group amount */
							$notice = sprintf( __( 'Products in the <strong>%1$s</strong> category (<em>%2$s</em>) must be bought in multiples of %3$d.', 'woocommerce-min-max-quantities' ), $term->name, implode( ', ', $product_names ), $group_of_quantity, $group_of_quantity - ( $quantity % $group_of_quantity ) );

							throw new Exception( $notice );
						}
					}
				}
			} catch ( Exception $e ) {

				if ( WC_MMQ_Core_Compatibility::is_store_api_request() ) {
					throw $e;

				} else {

					$notice = $e->getMessage();

					if ( $notice ) {
						wc_add_notice( $notice, 'error' );
					}
				}
			}
		}

		/**
		 * If the minimum allowed quantity for purchase is lower then the current stock, we need to
		 * let the user know that they are on backorder, or out of stock.
		 *
		 * @param array      $args    List of arguments.
		 * @param WC_Product $product Product object.
		 */
		public function maybe_show_backorder_message( $args, $product ) {
			if ( ! $product->managing_stock() ) {
				return $args;
			}

			// Figure out what our minimum_quantity is.
			$product_id = $product->get_id();
			if ( 'WC_Product_Variation' === get_class( $product ) ) {
				$variation_id  = $product->get_id();
				$min_max_rules = get_post_meta( $variation_id, 'min_max_rules', true );
				if ( 'yes' === $min_max_rules ) {
					$minimum_quantity = absint( get_post_meta( $variation_id, 'variation_minimum_allowed_quantity', true ) );
				} else {
					$minimum_quantity = absint( get_post_meta( $product_id, 'minimum_allowed_quantity', true ) );
				}
			} else {
				$minimum_quantity = absint( get_post_meta( $product_id, 'minimum_allowed_quantity', true ) );
			}

			// If the minimum quantity allowed for purchase is smaller then the amount in stock, we need
			// clearer messaging.
			if ( $minimum_quantity > 0 && $product->get_stock_quantity() < $minimum_quantity ) {
				if ( $product->backorders_allowed() ) {
					return array(
						'availability' => __( 'Available on backorder', 'woocommerce-min-max-quantities' ),
						'class'        => 'available-on-backorder',
					);
				} else {
					return array(
						'availability' => __( 'Out of stock', 'woocommerce-min-max-quantities' ),
						'class'        => 'out-of-stock',
					);
				}
			}

			return $args;
		}

		/**
		 * Add respective error message depending on rules checked.
		 *
		 * @throws Exception
		 *
		 * @param WC_Product $product           Product object.
		 * @param int        $quantity          Quantity to check.
		 * @param int        $minimum_quantity  Minimum quantity.
		 * @param int        $maximum_quantity  Maximum quanitty.
		 * @param int        $group_of_quantity Group quantity.
		 * @param int|null   $checking_id       Variation ID
		 * @return void
		 */
		public function check_rules( $product, $quantity, $minimum_quantity, $maximum_quantity, $group_of_quantity, $checking_id = null ) {

			try {

				$parent_id         = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
				$variation_title   = $checking_id ? get_the_title( $checking_id ) : $product->get_title();
				$allow_combination = 'yes' === get_post_meta( $parent_id, 'allow_combination', true );

				if ( $allow_combination ) {
					$parent_product  = wc_get_product( $parent_id );
					$variation_title = $parent_product->get_title();
				}

				if ( $minimum_quantity > 0 && $quantity < $minimum_quantity ) {

					if ( $allow_combination && ( $product->is_type( 'variation' ) || $product->is_type( 'variable' ) ) ) {

						/* translators: %1$s: Product name, %2$s: Minimum order quantity, %3$s: Total cart quantity */
						$notice = sprintf( __( 'To place an order, the quantity of "%1$s" must be at least %2$s. You currently have %3$s in your cart.', 'woocommerce-min-max-quantities' ), $variation_title, $minimum_quantity, $quantity );

						throw new Exception( $notice );

					} else {

						if ( WC_MMQ_Core_Compatibility::is_store_api_request( 'cart' ) ) {
							/* translators: %1$s: Product name, %2$s: Minimum order quantity */
							$notice = sprintf( __( 'The quantity of "%1$s" has been increased to %2$s. This is the minimum required quantity.', 'woocommerce-min-max-quantities' ), $variation_title, $minimum_quantity );
						} else {
							/* translators: %1$s: Product name, %2$s: Minimum order quantity */
							$notice = sprintf( __( 'To place an order, the quantity of "%1$s" must be at least %2$s.', 'woocommerce-min-max-quantities' ), $variation_title, $minimum_quantity );
						}

						throw new Exception( $notice );
					}

				} elseif ( $maximum_quantity > 0 && $quantity > $maximum_quantity ) {
					if ( $allow_combination && ( $product->is_type( 'variation' ) || $product->is_type( 'variable' ) ) ) {

						/* translators: %1$s: Product name, %2$s: Maximum order quantity, %3$s: Total cart quantity */
						$notice = sprintf( __( 'The quantity of "%1$s" cannot be higher than %2$s to place an order. You currently have %3$s in your cart.', 'woocommerce-min-max-quantities' ), $variation_title, $maximum_quantity, $quantity );

						throw new Exception( $notice );

					} else {

						if ( WC_MMQ_Core_Compatibility::is_store_api_request( 'cart' ) ) {
							/* translators: %1$s: Product name, %2$s: Maximum order quantity */
							$notice = sprintf( __( 'The quantity of "%1$s" has been decreased to %2$s. This is the maximum allowed quantity.', 'woocommerce-min-max-quantities' ), $variation_title, $maximum_quantity );
						} else {
							/* translators: %1$s: Product name, %2$s: Maximum order quantity */
							$notice = sprintf( __( 'The quantity of "%1$s" cannot be higher than %2$s to place an order.', 'woocommerce-min-max-quantities' ), $variation_title, $maximum_quantity );
						}

						throw new Exception( $notice );
					}
				}

				if ( $group_of_quantity > 0 && ( intval( $quantity ) % intval( $group_of_quantity ) > 0 ) ) {

					if ( $allow_combination && ( $product->is_type( 'variation' ) || $product->is_type( 'variable' ) ) ) {

						/* translators: %1$s: Product name, %2$d: Group amount */
						$notice = sprintf( __( '"%1$s" must be bought in multiples of %2$d. Please adjust its quantity to continue.', 'woocommerce-min-max-quantities' ), $variation_title, $group_of_quantity, $group_of_quantity - ( $quantity % $group_of_quantity ) );

						throw new Exception( $notice );

					} else {

						if ( WC_MMQ_Core_Compatibility::is_store_api_request( 'cart' ) ) {
							/* translators: %1$s: Product name, %2$d: Group amount */
							$notice = sprintf( __( 'The quantity of "%1$s" has been adjusted. "%1$s" must be bought in multiples of %2$d.', 'woocommerce-min-max-quantities' ), $variation_title, $group_of_quantity, $group_of_quantity - ( $quantity % $group_of_quantity ) );
						} else {
							/* translators: %1$s: Product name, %2$d: Group amount */
							$notice = sprintf( __( '"%1$s" must be bought in multiples of %2$d. Please adjust its quantity to continue.', 'woocommerce-min-max-quantities' ), $variation_title, $group_of_quantity, $group_of_quantity - ( $quantity % $group_of_quantity ) );
						}
					}

					throw new Exception( $notice );
				}

			} catch ( Exception $e ) {

				if ( WC_MMQ_Core_Compatibility::is_store_api_request() ) {

					throw $e;

				} else {

					$notice = $e->getMessage();

					if ( $notice ) {
						wc_add_notice( $notice, 'error' );
					}
				}
			}
		}

		/**
		 * Add to cart validation
		 *
		 * @param  mixed $pass         Filter value.
		 * @param  mixed $product_id   Product ID.
		 * @param  mixed $quantity     Quantity.
		 * @param  int   $variation_id Variation ID (default none).
		 * @return mixed
		 */
		public function add_to_cart( $pass, $product_id, $quantity, $variation_id = 0 ) {

			$allow_combination = 'yes' === get_post_meta( $product_id, 'allow_combination', true );

			// Product level.
			if ( $variation_id ) {

				$min_max_rules = get_post_meta( $variation_id, 'min_max_rules', true );

				if ( 'yes' === $min_max_rules ) {

					// Cast both 0 and empty values to zero, as we shouldn't do any validation for 0/empty values.
					$maximum_quantity  = absint( get_post_meta( $variation_id, 'variation_maximum_allowed_quantity', true ) );
					$minimum_quantity  = absint( get_post_meta( $variation_id, 'variation_minimum_allowed_quantity', true ) );
					$group_of_quantity = absint( get_post_meta( $variation_id, 'variation_group_of_quantity', true ) );

					// If the Minimum Quantity is not set on variation level, fall back to the parent's.
					if ( 0 === $maximum_quantity ) {
						$maximum_quantity = absint( get_post_meta( $product_id, 'maximum_allowed_quantity', true ) );
					}

					// If the Maximum Quantity is not set on variation level, fall back to the parent's.
					if ( 0 === $minimum_quantity ) {
						$minimum_quantity = absint( get_post_meta( $product_id, 'minimum_allowed_quantity', true ) );
					}

					// If the Group of Quantity is not set on variation level, fall back to the parent's.
					if ( 0 === $group_of_quantity ) {
						$group_of_quantity = absint( get_post_meta( $product_id, 'group_of_quantity', true ) );
					}

				} else {

					// Cast both 0 and empty values to zero, as we shouldn't do any validation for 0/empty values.
					$maximum_quantity  = absint( get_post_meta( $product_id, 'maximum_allowed_quantity', true ) );
					$minimum_quantity  = absint( get_post_meta( $product_id, 'minimum_allowed_quantity', true ) );
					$group_of_quantity = absint( get_post_meta( $product_id, 'group_of_quantity', true ) );

				}
			} else {

				// Cast both 0 and empty values to zero, as we shouldn't do any validation for 0/empty values.
				$maximum_quantity  = absint( get_post_meta( $product_id, 'maximum_allowed_quantity', true ) );
				$minimum_quantity  = absint( get_post_meta( $product_id, 'minimum_allowed_quantity', true ) );
				$group_of_quantity = absint( get_post_meta( $product_id, 'group_of_quantity', true ) );

			}

			// Validate if the selected product/variation quantity satisfies the Minimum/Maximum/Group of quantity restrictions.
			if ( 0 !== $group_of_quantity && ! $allow_combination ) {

				if ( $quantity % $group_of_quantity ) {

					$_product_id = $variation_id ? $variation_id : $product_id;
					$_product    = wc_get_product( $_product_id );

					/* translators: %1$s: Product name, %2$d: Group of quantity */
					$message = sprintf( __( '"%1$s" can only be bought in multiples of %2$d.', 'woocommerce-min-max-quantities' ), $_product->get_name(), $group_of_quantity );
					$this->add_error( $message );
					return false;
				}

				/*
				 * Backwards compatibility for versions earlier than v3.
				 *
				 * If an invalid Minimum/Maximum Quantity has been saved in the database, adjust it and validate add-to-cart quantity based on the adjusted, valid value.
				 */
				$minimum_quantity = self::adjust_min_quantity( $minimum_quantity, $group_of_quantity );
				$maximum_quantity = self::adjust_max_quantity( $maximum_quantity, $group_of_quantity, $minimum_quantity );

			}

			// Check if the add-to-cart quantity is greater than the Minimum Quantity.
			if ( 0 !== $minimum_quantity && ! $allow_combination ) {

				if ( $quantity < $minimum_quantity ) {

					$_product_id = $variation_id ? $variation_id : $product_id;
					$_product    = wc_get_product( $_product_id );

					/* translators: %1$s: Product name, %2$d: Minimum Quantity */
					$message = sprintf( __( 'The minimum required quantity for "%1$s" is %2$d.', 'woocommerce-min-max-quantities' ), $_product->get_name(), $minimum_quantity );
					$this->add_error( $message );
					return false;
				}
			}

			// Check if the add-to-cart quantity is less than the Maximum Quantity.
			if ( 0 !== $maximum_quantity ) {

				/*
				 * Backwards compatibility for versions earlier than v3.
				 *
				 * If Maximum Quantity is less than Minimum, set Maximum Quantity equal to Minimum value.
				 */
				if ( 0 !== $minimum_quantity ) {
					if ( $minimum_quantity > $maximum_quantity ) {
						$maximum_quantity = $minimum_quantity;
					}
				}

				if ( $quantity > $maximum_quantity ) {

					$_product_id = $variation_id ? $variation_id : $product_id;
					$_product    = wc_get_product( $_product_id );

					/* translators: %1$s: Product name, %2$d: Maximum quantity */
					$message = sprintf( __( 'The maximum allowed quantity for "%1$s" is %2$d.', 'woocommerce-min-max-quantities' ), $_product->get_name(), $maximum_quantity );
					$this->add_error( $message );
					return false;
				}
			}

			return $pass;
		}

		/**
		 * Updates the quantity arguments.
		 *
		 * @param array      $data    List of data to update.
		 * @param WC_Product $product Product object.
		 * @return array
		 */
		public function update_quantity_args( $data, $product ) {
			// Multiple shipping address product plugin compat
			// don't update the quantity args when on set multiple address page.
			if ( is_a( $this->addons, 'WC_Min_Max_Quantities_Addons' ) && $this->addons->is_multiple_shipping_address_page() ) {
				return $data;
			}

			// Cast both 0 and empty values to zero, as we shouldn't do any adjustments for 0/empty values.
			$group_of_quantity = absint( $this->get_group_of_quantity_for_product( $product ) );
			$minimum_quantity  = absint( get_post_meta( $product->get_id(), 'minimum_allowed_quantity', true ) );
			$maximum_quantity  = absint( get_post_meta( $product->get_id(), 'maximum_allowed_quantity', true ) );
			$allow_combination = 'yes' === get_post_meta(  $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(), 'allow_combination', true );

			/*
			* If it's a variable product and allow combination is enabled,
			* we don't need to set the quantity to default minimum.
			*/
			if ( $allow_combination ) {
				$data[ 'max_value' ] = absint( get_post_meta( $product->get_parent_id(), 'maximum_allowed_quantity', true ) );
				return $data;
			}

			// If variable product, only apply in cart.
			$variation_id = $product->get_id();

			if ( is_cart() && $product->is_type( 'variation' ) ) {
				$parent_variable_id = $product->get_parent_id();

				// Cast both 0 and empty values to zero, as we shouldn't do any adjustments for 0/empty values.
				$group_of_quantity = absint( get_post_meta( $parent_variable_id, 'group_of_quantity', true ) );
				$minimum_quantity  = absint( get_post_meta( $parent_variable_id, 'minimum_allowed_quantity', true ) );
				$maximum_quantity  = absint( get_post_meta( $parent_variable_id, 'maximum_allowed_quantity', true ) );
				$allow_combination = 'yes' === get_post_meta( $parent_variable_id, 'allow_combination', true );

				$min_max_rules = get_post_meta( $variation_id, 'min_max_rules', true );

				if ( 'no' === $min_max_rules || empty( $min_max_rules ) ) {
					$min_max_rules = false;

				} else {
					$min_max_rules = true;

				}

				// Cast both 0 and empty values to zero, as we shouldn't do any adjustments for 0/empty values.
				$variation_minimum_quantity  = absint( get_post_meta( $variation_id, 'variation_minimum_allowed_quantity', true ) );
				$variation_maximum_quantity  = absint( get_post_meta( $variation_id, 'variation_maximum_allowed_quantity', true ) );
				$variation_group_of_quantity = absint( get_post_meta( $variation_id, 'variation_group_of_quantity', true ) );

				// Override product level.
				if ( $min_max_rules && $variation_minimum_quantity ) {
					$minimum_quantity = $variation_minimum_quantity;

				}

				// Override product level.
				if ( $min_max_rules && $variation_maximum_quantity ) {
					$maximum_quantity = $variation_maximum_quantity;
				}

				// Override product level.
				if ( $min_max_rules && $variation_group_of_quantity ) {
					$group_of_quantity = $variation_group_of_quantity;
				}
			}

			if ( 0 !== $group_of_quantity ) {
				$data[ 'step' ] = $group_of_quantity;

				/*
				 * Backwards compatibility for versions earlier than v3.
				 *
				 * If an invalid Minimum/Maximum Quantity has been saved in the database, adjust it and validate add-to-cart quantity based on the adjusted, valid value.
				 */
				if ( 0 !== $minimum_quantity ) {
					$adjusted_min_quantity = self::adjust_min_quantity( $minimum_quantity, $data[ 'step' ] );

					if ( $adjusted_min_quantity !== $minimum_quantity ) {
						$minimum_quantity = $adjusted_min_quantity;
					}
				}

				if ( 0 !== $maximum_quantity ) {
					$adjusted_max_quantity = self::adjust_max_quantity( $maximum_quantity, $data[ 'step' ], $minimum_quantity );

					if ( $adjusted_max_quantity !== $maximum_quantity ) {
						$maximum_quantity = $adjusted_max_quantity;
					}
				}

				/**
				 * Check if we should use the group of setting as our minimum.
				 *
				 * @since 2.4.22
				 * @param boolean    $use_group Whether we should use the group of setting.
				 * @param WC_Product $product   Product object.
				 * @param array      $data      Available product data.
				 */
				if ( ( ! isset( $minimum_quantity ) || 0 === $minimum_quantity ) && apply_filters( 'wc_min_max_use_group_as_min_quantity', true, $product, $data ) ) {
					$data['min_value'] = $group_of_quantity;
				}
			}

			if ( isset( $minimum_quantity ) && 0 !== $minimum_quantity ) {

				if ( $product->managing_stock() && ! $product->backorders_allowed() && absint( $minimum_quantity ) > $product->get_stock_quantity() ) {
					$data['min_value'] = $product->get_stock_quantity();

				} else {
					$data['min_value'] = $minimum_quantity;
				}
			}

			if ( $maximum_quantity ) {

				if ( $product->managing_stock() && $product->backorders_allowed() ) {
					$data['max_value'] = $maximum_quantity;

				} elseif ( $product->managing_stock() && absint( $maximum_quantity ) > $product->get_stock_quantity() ) {
					$data['max_value'] = $product->get_stock_quantity();

				} else {
					$data['max_value'] = $maximum_quantity;
				}
			}

			// Don't apply for cart or checkout as cart/checkout form has qty already pre-filled.
			if ( ! is_cart() && ! is_checkout() ) {
				// If we have a group of quantity and no minimum then set the quantity to the group of quantity.
				if ( ! empty( $minimum_quantity ) ) {
					$data['input_value'] = $minimum_quantity;
				} elseif ( ! empty( $group_of_quantity ) && $this->use_group_as_min_quantity( true, $product, $data ) ) {
					$data['input_value'] = $group_of_quantity;
				}
			}

			return $data;
		}

		/**
		 * If on a grouped product page, don't use Group as for our minimum.
		 *
		 * @param boolean $use_group Whether to use group quantity as minimum. Default true.
		 * @param object  $product   Product object.
		 * @param array   $data      Available product data.
		 * @return boolean
		 */
		public function use_group_as_min_quantity( $use_group, $product, $data ) {
			$parent_product = wc_get_product( get_queried_object_id() );

			if (
				$parent_product
				&& $parent_product->get_id() !== $product->get_id()
				&& 'grouped' === $parent_product->get_type()
			) {
				return false;
			}

			return $use_group;
		}

		/**
		 * Adds variation min max settings to the localized variation parameters to be used by JS.
		 *
		 * @param array  $data      Available variation data.
		 * @param object $product   Product object.
		 * @param object $variation Variation object.
		 * @return array $data
		 */
		public function available_variation( $data, $product, $variation ) {
			$variation_id = $variation->get_id();

			$min_max_rules = get_post_meta( $variation_id, 'min_max_rules', true );

			if ( 'no' === $min_max_rules || empty( $min_max_rules ) ) {
				$min_max_rules = false;

			} else {
				$min_max_rules = true;

			}

			// Cast both 0 and empty values to zero, as we shouldn't do any adjustments for 0/empty values.
			$minimum_quantity  = absint( get_post_meta( $product->get_id(), 'minimum_allowed_quantity', true ) );
			$maximum_quantity  = absint( get_post_meta( $product->get_id(), 'maximum_allowed_quantity', true ) );
			$group_of_quantity = $this->get_group_of_quantity_for_product( $product );
			$allow_combination = 'yes' === get_post_meta( $product->get_id(), 'allow_combination', true );

			$variation_minimum_quantity  = absint( get_post_meta( $variation_id, 'variation_minimum_allowed_quantity', true ) );
			$variation_maximum_quantity  = absint( get_post_meta( $variation_id, 'variation_maximum_allowed_quantity', true ) );
			$variation_group_of_quantity = absint( get_post_meta( $variation_id, 'variation_group_of_quantity', true ) );

			// Override product level.
			if ( $variation->managing_stock() ) {
				$product = $variation;

			}

			// Override product level.
			if ( $min_max_rules && ! $allow_combination && $variation_minimum_quantity ) {
				$minimum_quantity = $variation_minimum_quantity;
			}

			// Override product level.
			if ( $min_max_rules && ! $allow_combination && $variation_maximum_quantity ) {
				$maximum_quantity = $variation_maximum_quantity;
			}

			// Override product level.
			if ( $min_max_rules && ! $allow_combination && $variation_group_of_quantity ) {
				$group_of_quantity = $variation_group_of_quantity;

			}

			if ( 0 !== $group_of_quantity ) {
				$data['step'] = $group_of_quantity;

				/*
				 * Backwards compatibility for versions earlier than v3.
				 *
				 * If an invalid Minimum/Maximum Quantity has been saved in the database, adjust it and validate add-to-cart quantity based on the adjusted, valid value.
				 */
				if ( 0 !== $minimum_quantity ) {
					$adjusted_min_quantity = self::adjust_min_quantity( $minimum_quantity, $data[ 'step' ] );

					if ( $adjusted_min_quantity !== $minimum_quantity ) {
						$minimum_quantity = $adjusted_min_quantity;
					}
				}

				if ( 0 !== $maximum_quantity ) {
					$adjusted_max_quantity = self::adjust_max_quantity( $maximum_quantity, $data[ 'step' ], $minimum_quantity );

					if ( $adjusted_max_quantity !== $maximum_quantity ) {
						$maximum_quantity = $adjusted_max_quantity;
					}
				}
			}

			if ( $minimum_quantity ) {

				if ( $product->managing_stock() && ! $product->backorders_allowed() && absint( $minimum_quantity ) > $product->get_stock_quantity() ) {
					$data['min_qty'] = $product->get_stock_quantity();

				} else {
					$data['min_qty'] = $minimum_quantity;
				}
			}

			if ( $maximum_quantity ) {

				if ( $product->managing_stock() && $product->backorders_allowed() ) {
					$data['max_qty'] = $maximum_quantity;

				} elseif ( $product->managing_stock() && absint( $maximum_quantity ) > $product->get_stock_quantity() ) {
					$data['max_qty'] = $product->get_stock_quantity();

				} else {
					$data['max_qty'] = $maximum_quantity;
				}
			}

			// Don't apply for cart as cart has qty already pre-filled.
			if ( ! is_cart() ) {
				if ( ! $minimum_quantity && $group_of_quantity ) {
					$data['input_value'] = $group_of_quantity;
				} else {
					$data['input_value'] = ! empty( $minimum_quantity ) ? $minimum_quantity : 1;
				}

				if ( $allow_combination ) {
					$data['input_value'] = 1;
					$data['min_qty']     = 1;
					$data['step']        = 1;
				}
			}

			return $data;
		}

		/**
		 * Get group_of_quantity setting for a product.
		 *
		 * @param WC_Product $product Product object.
		 *
		 * @return int
		 */
		public function get_group_of_quantity_for_product( $product ) {
			$transient_name    = 'wc_min_max_group_quantity_' . $product->get_id();
			$transient_version = WC_Cache_Helper::get_transient_version( 'wc_min_max_group_quantity' );
			$transient_value   = get_transient( $transient_name );

			if ( isset( $transient_value['value'], $transient_value['version'] ) && $transient_value['version'] === $transient_version ) {
				return absint( $transient_value['value'] );
			}

			$group_of_quantity = get_post_meta( $product->get_id(), 'group_of_quantity', true );

			// If the product level group_of_quantity is not set, check for category settings.
			// If the product has multiple categories, use the smallest value.
			if ( ! $group_of_quantity && 'yes' !== get_post_meta( $product->get_id(), 'minmax_category_group_of_exclude', true ) ) {
				$terms          = get_the_terms( $product->get_id(), 'product_cat' );
				$found_settings = array();

				if ( $terms && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$found_settings[] = intval( get_term_meta( $term->term_id, 'group_of_quantity', true ) );
					}

					$found_settings = array_filter( $found_settings );

					if ( ! empty( $found_settings ) ) {
						$group_of_quantity = min( $found_settings );
					}
				}
			}

			$transient_value = array(
				'version' => $transient_version,
				'value'   => absint( $group_of_quantity ),
			);

			set_transient( $transient_name, $transient_value, DAY_IN_SECONDS * 30 );

			return absint( $group_of_quantity );
		}

		/**
		 * Modify quantity for add to cart action inside loop to respect minimum rules.
		 *
		 * @param int $product_id Product ID.
		 *
		 * @return int
		 */
		public function modify_add_to_cart_quantity( $product_id ) {
			if ( ! isset( $_GET['add-to-cart'] ) || ! is_numeric( wp_unslash( $_GET['add-to-cart'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				return $product_id;
			}

			if ( ! empty( $_REQUEST['quantity'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return $product_id;
			}

			$product = wc_get_product( $product_id );

			if ( ! is_a( $product, 'WC_Product' ) || 'variable' === $product->get_type() ) {
				return $product_id;
			}

			$quantity = 0;

			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( intval( $product->get_id() ) === intval( $cart_item['product_id'] ) ) {
					$quantity = $cart_item['quantity'];
					break; // stop the loop if product is found.
				}
			}

			$minimum_quantity  = absint( get_post_meta( $product->get_id(), 'minimum_allowed_quantity', true ) );
			$group_of_quantity = $this->get_group_of_quantity_for_product( $product );

			if ( $quantity < $minimum_quantity ) {
				$_REQUEST['quantity'] = $minimum_quantity - $quantity;
				return $product_id;
			}

			if ( $group_of_quantity ) {
				if ( $group_of_quantity > $quantity ) {
					$_REQUEST['quantity'] = $group_of_quantity - $quantity;
					return $product_id;
				}

				$remainder = $quantity % $group_of_quantity;

				if ( 0 === $remainder ) {
					$_REQUEST['quantity'] = $group_of_quantity;
				} else {
					$_REQUEST['quantity'] = $group_of_quantity - $remainder;
				}
				return $product_id;
			}

			return $product_id;
		}

		/**
		 * Filter Minimum Quantity based on "Group of" option on runtime.
		 *
		 * @param  int  $min_quantity
		 * @param  int  $group_of_quantity
		 *
		 * @return int
		 */
		public static function adjust_min_quantity( $min_quantity, $group_of_quantity ) {

			// Zero min quantity is always allowed.
			if ( ! $min_quantity || ! $group_of_quantity) {
				return $min_quantity;
			}

			if ( $min_quantity < $group_of_quantity ) {

				// If Group of = 2 and Minimum Quantity = 1, set Minimum Quantity to 2.
				$min_quantity = $group_of_quantity;

			} elseif ( $min_quantity > $group_of_quantity ) {
				$remainder = $min_quantity / $group_of_quantity;

				// If Group of = 2 and Minimum Quantity = 5, set Minimum Quantity to 2 * ceil( 5/2 ) = 6.
				// If Group of = 4 and Minimum Quantity = 5, set Minimum Quantity to 4 * ceil( 5/4 ) = 8.
				if ( $remainder ) {
					$min_quantity = $group_of_quantity * ceil( $remainder );
				}
			}

			return absint( $min_quantity );
		}

		/**
		 * Filter Maximum Quantity based on "Group of" option on runtime.
		 *
		 * @param  int  $max_quantity
		 * @param  int  $group_of_quantity
		 * @param  int  $min_quantity
		 *
		 * @return int
		 */
		public static function adjust_max_quantity( $max_quantity, $group_of_quantity, $min_quantity = 0 ) {

			// Return early for infinite max quantities.
			if ( empty( $max_quantity ) ) {
				return $max_quantity;
			}

			// If the Minimum Quantity is greater than the Maximum, then set the Maximum equal to the Minimum.
			if ( ! empty ( $min_quantity ) && $min_quantity > $max_quantity ) {
				$max_quantity = $min_quantity;
			}

			// If the Group of Quantity is 0, skip quantity adjustments based on the step.
			if ( empty( $group_of_quantity ) ) {
				return $max_quantity;
			}

			if ( $max_quantity > $group_of_quantity ) {
				$remainder = $max_quantity / $group_of_quantity;

				// If Group of = 4 and Maximum Quantity = 5, set Maximum Quantity to 4 * floor( 5/4 ) = 4.
				// If Group of = 4 and Maximum Quantity = 9, set Maximum Quantity to 4 * floor( 9/4 ) = 8.
				if ( $remainder ) {
					$max_quantity = $group_of_quantity * floor( $remainder );
				}
			} elseif ( $max_quantity < $group_of_quantity ) {
				// If Group of = 2 and Maximum Quantity = 1, set Maximum Quantity to 2.
				$max_quantity = $group_of_quantity;
			}

			return absint( $max_quantity );
		}
	}

	add_action( 'plugins_loaded', array( 'WC_Min_Max_Quantities', 'get_instance' ) );
	// Subscribe to automated translations.
	add_filter( 'woocommerce_translations_updates_for_' . basename( __FILE__, '.php' ), '__return_true' );

endif;
