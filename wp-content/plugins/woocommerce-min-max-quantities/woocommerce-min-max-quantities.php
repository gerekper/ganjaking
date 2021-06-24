<?php
/**
 * Plugin Name: WooCommerce Min/Max Quantities
 * Plugin URI: https://woocommerce.com/products/minmax-quantities/
 * Description: Define minimum/maximum allowed quantities for products, variations and orders.
 * Version: 2.4.23
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Requires at least: 4.0
 * Tested up to: 5.7
 * WC tested up to: 5.4
 * WC requires at least: 2.6
 *
 * Text Domain: woocommerce-min-max-quantities
 * Domain Path: /languages
 *
 * Copyright: © 2021 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Woo: 18616:2b5188d90baecfb781a5aa2d6abb900a
 *
 * @package woocommerce-min-max-quantities
 */

if ( ! class_exists( 'WC_Min_Max_Quantities' ) ) :

	define( 'WC_MIN_MAX_QUANTITIES', '2.4.23' ); // WRCS: DEFINED_VERSION.

	/**
	 * Min Max Quantities class.
	 */
	class WC_Min_Max_Quantities {

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
			if ( ! class_exists( 'WooCommerce' ) ) {
				add_action( 'admin_notices', array( $this, 'admin_notice' ) );
				return;
			}

			/**
			 * Localisation.
			 */
			$this->load_plugin_textdomain();

			if ( is_admin() ) {
				include_once __DIR__ . '/includes/class-wc-min-max-quantities-admin.php';
			}

			include_once __DIR__ . '/includes/class-wc-min-max-quantities-addons.php';

			$this->addons = new WC_Min_Max_Quantities_Addons();

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
		}

		/**
		 * Output a notice if Woocommerce isn't active.
		 */
		public function admin_notice() {
			// Make sure the get_current_screen function exists.
			if ( ! function_exists( 'get_current_screen' ) ) {
				require_once ABSPATH . 'wp-admin/includes/screen.php';
			}

			// Only show notice if on the plugins page and user has proper permissions.
			$current_screen = get_current_screen();
			if ( 'plugins' !== $current_screen->id || ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) ) {
				return;
			}
			?>

			<div class="notice notice-error is-dismissible">
				<p>
					<?php /* translators: %s is the WooCommerce link. */ ?>
					<?php printf( esc_html__( 'Min/Max Quantities requires the WooCommerce plugin to be installed and active. You can download %s here.', 'woocommerce-min-max-quantities' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ); ?>
				</p>
			</div>

			<?php
		}

		/**
		 * Load scripts.
		 */
		public function load_scripts() {
			// Only load on single product page and cart page.
			if ( is_product() || is_cart() ) {
				wc_enqueue_js(
					"
					jQuery( 'body' ).on( 'show_variation', function( event, variation ) {
						const step = 'undefined' !== typeof variation.step ? variation.step : 1;
						jQuery( 'form.variations_form' ).find( 'input[name=quantity]' ).prop( 'step', step ).val( variation.input_value );
					});
					"
				);
			}
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

				if ( 'yes' === $min_max_rules || ! $allow_combination ) {
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
		 */
		public function check_cart_items() {
			$checked_ids         = array();
			$product_quantities  = array();
			$category_quantities = array();
			$total_quantity      = 0;
			$total_cost          = 0;
			$apply_cart_rules    = false;

			// Count items + variations first.
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$product     = $values['data'];
				$checking_id = $this->get_id_to_check( $values );

				if ( apply_filters( 'wc_min_max_cart_quantity_do_not_count', false, $checking_id, $cart_item_key, $values ) ) {
					$values['quantity'] = 0;
				}

				if ( ! isset( $product_quantities[ $checking_id ] ) ) {
					$product_quantities[ $checking_id ] = $values['quantity'];
				} else {
					$product_quantities[ $checking_id ] += $values['quantity'];
				}

				// Do_not_count and cart_exclude from variation or product.
				$minmax_do_not_count = apply_filters( 'wc_min_max_quantity_minmax_do_not_count', ( 'yes' === get_post_meta( $checking_id, 'variation_minmax_do_not_count', true ) ? 'yes' : get_post_meta( $values['product_id'], 'minmax_do_not_count', true ) ), $checking_id, $cart_item_key, $values );

				$minmax_cart_exclude = apply_filters( 'wc_min_max_quantity_minmax_cart_exclude', ( 'yes' === get_post_meta( $checking_id, 'variation_minmax_cart_exclude', true ) ? 'yes' : get_post_meta( $values['product_id'], 'minmax_cart_exclude', true ) ), $checking_id, $cart_item_key, $values );

				if ( 'yes' !== $minmax_do_not_count && 'yes' !== $minmax_cart_exclude ) {
					$total_cost += $product->get_price() * $values['quantity'];
				}
			}

			// Check cart items.
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$checking_id    = $this->get_id_to_check( $values );
				$terms          = get_the_terms( $values['product_id'], 'product_cat' );
				$found_term_ids = array();

				if ( $terms ) {

					foreach ( $terms as $term ) {

						if ( 'yes' === get_post_meta( $checking_id, 'variation_minmax_category_group_of_exclude', true ) || 'yes' === get_post_meta( $values['product_id'], 'minmax_category_group_of_exclude', true ) ) {
							continue;
						}

						if ( in_array( $term->term_id, $found_term_ids, true ) ) {
							continue;
						}

						$found_term_ids[]                      = $term->term_id;
						$category_quantities[ $term->term_id ] = isset( $category_quantities[ $term->term_id ] ) ? $category_quantities[ $term->term_id ] + $values['quantity'] : $values['quantity'];

						// Record count in parents of this category too.
						$parents = get_ancestors( $term->term_id, 'product_cat' );

						foreach ( $parents as $parent ) {
							if ( in_array( $parent, $found_term_ids, true ) ) {
								continue;
							}

							$found_term_ids[]               = $parent;
							$category_quantities[ $parent ] = isset( $category_quantities[ $parent ] ) ? $category_quantities[ $parent ] + $values['quantity'] : $values['quantity'];
						}
					}
				}

				// Check item rules once per product ID.
				if ( in_array( $checking_id, $checked_ids, true ) ) {
					continue;
				}

				$product = $values['data'];

				// Do_not_count and cart_exclude from variation or product.
				$minmax_do_not_count = apply_filters( 'wc_min_max_quantity_minmax_do_not_count', ( 'yes' === get_post_meta( $checking_id, 'variation_minmax_do_not_count', true ) ? 'yes' : get_post_meta( $values['product_id'], 'minmax_do_not_count', true ) ), $checking_id, $cart_item_key, $values );

				$minmax_cart_exclude = apply_filters( 'wc_min_max_quantity_minmax_cart_exclude', ( 'yes' === get_post_meta( $checking_id, 'variation_minmax_cart_exclude', true ) ? 'yes' : get_post_meta( $values['product_id'], 'minmax_cart_exclude', true ) ), $checking_id, $cart_item_key, $values );

				if ( 'yes' === $minmax_do_not_count || 'yes' === $minmax_cart_exclude ) {
					// Do not count.
					$this->excludes[] = $product->get_title();

				} else {
					$total_quantity += $product_quantities[ $checking_id ];
				}

				if ( 'yes' !== $minmax_cart_exclude ) {
					$apply_cart_rules = true;
				}

				$checked_ids[] = $checking_id;

				if ( $values['variation_id'] ) {
					$min_max_rules = get_post_meta( $values['variation_id'], 'min_max_rules', true );

					// Variation level min max rules enabled.
					if ( 'yes' === $min_max_rules ) {
						$minimum_quantity = absint( apply_filters( 'wc_min_max_quantity_minimum_allowed_quantity', get_post_meta( $values['variation_id'], 'variation_minimum_allowed_quantity', true ), $values['variation_id'], $cart_item_key, $values ) );

						$maximum_quantity = absint( apply_filters( 'wc_min_max_quantity_maximum_allowed_quantity', get_post_meta( $values['variation_id'], 'variation_maximum_allowed_quantity', true ), $values['variation_id'], $cart_item_key, $values ) );

						$group_of_quantity = absint( apply_filters( 'wc_min_max_quantity_group_of_quantity', get_post_meta( $values['variation_id'], 'variation_group_of_quantity', true ), $values['variation_id'], $cart_item_key, $values ) );
					} else {
						$minimum_quantity = absint( apply_filters( 'wc_min_max_quantity_minimum_allowed_quantity', get_post_meta( $values['product_id'], 'minimum_allowed_quantity', true ), $values['product_id'], $cart_item_key, $values ) );

						$maximum_quantity = absint( apply_filters( 'wc_min_max_quantity_maximum_allowed_quantity', get_post_meta( $values['product_id'], 'maximum_allowed_quantity', true ), $values['product_id'], $cart_item_key, $values ) );

						$group_of_quantity = absint( apply_filters( 'wc_min_max_quantity_group_of_quantity', get_post_meta( $values['product_id'], 'group_of_quantity', true ), $values['product_id'], $cart_item_key, $values ) );
					}
				} else {
					$minimum_quantity = absint( apply_filters( 'wc_min_max_quantity_minimum_allowed_quantity', get_post_meta( $checking_id, 'minimum_allowed_quantity', true ), $checking_id, $cart_item_key, $values ) );

					$maximum_quantity = absint( apply_filters( 'wc_min_max_quantity_maximum_allowed_quantity', get_post_meta( $checking_id, 'maximum_allowed_quantity', true ), $checking_id, $cart_item_key, $values ) );

					$group_of_quantity = absint( apply_filters( 'wc_min_max_quantity_group_of_quantity', get_post_meta( $checking_id, 'group_of_quantity', true ), $checking_id, $cart_item_key, $values ) );
				}

				$this->check_rules( $product, $product_quantities[ $checking_id ], $minimum_quantity, $maximum_quantity, $group_of_quantity );
			}

			// Cart rules.
			if ( $apply_cart_rules ) {

				$excludes = '';

				if ( count( $this->excludes ) > 0 ) {
					$excludes = ' (' . __( 'excludes ', 'woocommerce-min-max-quantities' ) . implode( ', ', $this->excludes ) . ')';
				}

				if ( $this->minimum_order_quantity > 0 && $total_quantity < $this->minimum_order_quantity ) {
					/* translators: %d: Minimum amount of items in the cart */
					$this->add_error( sprintf( __( 'The minimum required items in cart is %d. Please increase the quantity in your cart.', 'woocommerce-min-max-quantities' ), $this->minimum_order_quantity ) . $excludes );

					return;

				}

				if ( $this->maximum_order_quantity > 0 && $total_quantity > $this->maximum_order_quantity ) {
					/* translators: %d: Maximum amount of items in the cart */
					$this->add_error( sprintf( __( 'The maximum allowed order quantity is %d. Please decrease the quantity in your cart.', 'woocommerce-min-max-quantities' ), $this->maximum_order_quantity ) );

					return;

				}

				// Check cart value.
				if ( $this->minimum_order_value && $total_cost < $this->minimum_order_value ) {
					/* translators: %s: Minimum order value */
					$this->add_error( sprintf( __( 'The minimum required order value is %s. Please increase the quantity in your cart.', 'woocommerce-min-max-quantities' ), wc_price( $this->minimum_order_value ) ) . $excludes );

					return;
				}

				if ( $this->maximum_order_value && $total_cost > $this->maximum_order_value ) {
					/* translators: %s: Maximum order value */
					$this->add_error( sprintf( __( 'The maximum allowed order value is %s. Please decrease the quantity in your cart.', 'woocommerce-min-max-quantities' ), wc_price( $this->maximum_order_value ) ) );

					return;
				}
			}

			// Check category rules.
			foreach ( $category_quantities as $category => $quantity ) {

				$group_of_quantity = intval( version_compare( WC_VERSION, '3.6', 'ge' ) ? get_term_meta( $category, 'group_of_quantity', true ) : get_woocommerce_term_meta( $category, 'group_of_quantity', true ) );

				if ( $group_of_quantity > 0 && ( intval( $quantity ) % intval( $group_of_quantity ) > 0 ) ) {

					$term          = get_term_by( 'id', $category, 'product_cat' );
					$product_names = array();

					foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

						// If exclude is enable, skip.
						if ( 'yes' === get_post_meta( $values['product_id'], 'minmax_category_group_of_exclude', true ) || 'yes' === get_post_meta( $values['variation_id'], 'variation_minmax_category_group_of_exclude', true ) ) {
							continue;
						}

						if ( has_term( $category, 'product_cat', $values['product_id'] ) ) {
							$product_names[] = $values['data']->get_title();
						}
					}

					if ( $product_names ) {
						/* translators: %1$s: Category name, %2$s: Comma separated list of product names, %3$d: Group amount */
						$this->add_error( sprintf( __( 'Items in the <strong>%1$s</strong> category (<em>%2$s</em>) must be bought in groups of %3$d. Please add or remove the items to continue.', 'woocommerce-min-max-quantities' ), $term->name, implode( ', ', $product_names ), $group_of_quantity, $group_of_quantity - ( $quantity % $group_of_quantity ) ) );
						return;
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
				$variation_id  = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $product->variation_id ) ) ? $product->variation_id : $product->get_id();
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
		 * @param WC_Product $product           Product object.
		 * @param int        $quantity          Quantity to check.
		 * @param int        $minimum_quantity  Minimum quantity.
		 * @param int        $maximum_quantity  Maximum quanitty.
		 * @param int        $group_of_quantity Group quantity.
		 * @return void
		 */
		public function check_rules( $product, $quantity, $minimum_quantity, $maximum_quantity, $group_of_quantity ) {
			$parent_id = $product->is_type( 'variation' ) ? ( version_compare( WC_VERSION, '3.0', '<' ) ? $product->parent_id : $product->get_parent_id() ) : $product->get_id();

			$allow_combination = 'yes' === get_post_meta( $parent_id, 'allow_combination', true );

			if ( $minimum_quantity > 0 && $quantity < $minimum_quantity ) {

				if ( $allow_combination && ( $product->is_type( 'variation' ) || $product->is_type( 'variable' ) ) ) {
					/* translators: %1$s: Product name, %2$s: Minimum order quantity */
					$this->add_error( sprintf( __( 'The minimum required order quantity for %1$s is %2$s. Please increase the quantity in your cart or add additional variation of this product.', 'woocommerce-min-max-quantities' ), $product->get_title(), $minimum_quantity ) );
				} else {
					/* translators: %1$s: Product name, %2$s: Minimum order quantity */
					$this->add_error( sprintf( __( 'The minimum required order quantity for %1$s is %2$s. Please increase the quantity in your cart.', 'woocommerce-min-max-quantities' ), $product->get_title(), $minimum_quantity ) );
				}
			} elseif ( $maximum_quantity > 0 && $quantity > $maximum_quantity ) {
				if ( $allow_combination && ( $product->is_type( 'variation' ) || $product->is_type( 'variable' ) ) ) {
					/* translators: %1$s: Product name, %2$s: Maximum order quantity */
					$this->add_error( sprintf( __( 'The maximum allowed quantity for %1$s is %2$s. Please decrease the quantity in your cart or remove additional variation of this product.', 'woocommerce-min-max-quantities' ), $product->get_title(), $maximum_quantity ) );
				} else {
					/* translators: %1$s: Product name, %2$s: Maximum order quantity */
					$this->add_error( sprintf( __( 'The maximum allowed quantity for %1$s is %2$s. Please decrease the quantity in your cart.', 'woocommerce-min-max-quantities' ), $product->get_title(), $maximum_quantity ) );
				}
			}

			if ( $group_of_quantity > 0 && ( intval( $quantity ) % intval( $group_of_quantity ) > 0 ) ) {
				/* translators: %1$s: Product name, %2$d: Group amount */
				$this->add_error( sprintf( __( '%1$s must be bought in groups of %2$d. Please increase or decrease the quantity to continue.', 'woocommerce-min-max-quantities' ), $product->get_title(), $group_of_quantity, $group_of_quantity - ( $quantity % $group_of_quantity ) ) );
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
			$rule_for_variation = false;
			$allow_combination  = 'yes' === get_post_meta( $product_id, 'allow_combination', true );
			$exclude_from_count = false;

			// Product level.
			if ( $variation_id ) {

				$min_max_rules       = get_post_meta( $variation_id, 'min_max_rules', true );
				$minmax_do_not_count = get_post_meta( $variation_id, 'variation_minmax_do_not_count', true );
				$minmax_cart_exclude = get_post_meta( $variation_id, 'variation_minmax_cart_exclude', true );

				if ( 'yes' === $min_max_rules ) {

					$maximum_quantity   = absint( get_post_meta( $variation_id, 'variation_maximum_allowed_quantity', true ) );
					$minimum_quantity   = absint( get_post_meta( $variation_id, 'variation_minimum_allowed_quantity', true ) );
					$rule_for_variation = true;

				} else {

					$maximum_quantity = absint( get_post_meta( $product_id, 'maximum_allowed_quantity', true ) );
					$minimum_quantity = absint( get_post_meta( $product_id, 'minimum_allowed_quantity', true ) );

					if ( ! $allow_combination ) {
						$rule_for_variation = true;
					}
				}
			} else {

				$minmax_do_not_count = get_post_meta( $product_id, 'minmax_do_not_count', true );
				$minmax_cart_exclude = get_post_meta( $product_id, 'minmax_cart_exclude', true );
				$maximum_quantity    = absint( get_post_meta( $product_id, 'maximum_allowed_quantity', true ) );
				$minimum_quantity    = absint( get_post_meta( $product_id, 'minimum_allowed_quantity', true ) );

			}

			if ( 'yes' === $minmax_do_not_count || 'yes' === $minmax_cart_exclude ) {
				$exclude_from_count = true;
			}

			$total_quantity = $quantity;

			// Count items.
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

				$checking_id = $values['variation_id'] ? $values['variation_id'] : $values['product_id'];

				if ( apply_filters( 'wc_min_max_cart_quantity_do_not_count', false, $checking_id, $cart_item_key, $values ) ) {
					continue;
				}

				if ( $rule_for_variation ) {

					if ( $values['variation_id'] === $variation_id ) {

						$total_quantity += $values['quantity'];
					}
				} else {

					if ( $values['product_id'] === $product_id ) {

						$total_quantity += $values['quantity'];
					}
				}
			}

			if ( isset( $maximum_quantity ) && $maximum_quantity > 0 ) {
				if ( $total_quantity > 0 && $total_quantity > $maximum_quantity ) {

					$_product = wc_get_product( $product_id );

					/* translators: %1$s: Product name, %2$d: Maximum quantity, %3$s: Currenty quantity */
					$message = sprintf( __( 'The maximum allowed quantity for %1$s is %2$d (you currently have %3$s in your cart). Please decrease the quantity in your cart.', 'woocommerce-min-max-quantities' ), $_product->get_title(), $maximum_quantity, $total_quantity - $quantity );

					// If quantity requirement is met, show cart link.
					if ( intval( $maximum_quantity ) <= intval( $total_quantity - $quantity ) ) {
						/* translators: %1$s: Product name, %2$d: Maximum quantity, %3$s: Currenty quantity, %4$s: Cart link */
						$message = sprintf( __( 'The maximum allowed quantity for %1$s is %2$d (you currently have %3$s in your cart). Please decrease the quantity in your cart. <a href="%4$s" class="woocommerce-min-max-quantities-error-cart-link button wc-forward">View cart</a>', 'woocommerce-min-max-quantities' ), $_product->get_title(), $maximum_quantity, $total_quantity - $quantity, esc_url( wc_get_cart_url() ) );
					}

					$this->add_error( $message );

					$pass = false;
				}
			}

			if ( isset( $minimum_quantity ) && $minimum_quantity > 0 ) {
				if ( $pass && $total_quantity < $minimum_quantity ) {

					$_product = wc_get_product( $product_id );

					/* translators: %1$s: Product name, %2$d: Minimum quantity, %3$s: Currenty quantity */
					$this->add_error( sprintf( __( 'The minimum required quantity for %1$s is %2$d (you currently have %3$s in your cart). Please increase the quantity in your cart.', 'woocommerce-min-max-quantities' ), $_product->get_title(), $minimum_quantity, $total_quantity ) );

					$pass = true;
				}
			}

			// If product level quantity are not set then check global order quantity.
			if ( empty( $maximum_quantity ) && empty( $minimum_quantity ) ) {
				$total_quantity = 0;

				// Check each product in the cart to determine if it should count towards total.
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$product     = $values['data'];
					$checking_id = $this->get_id_to_check( $values );

					if ( apply_filters( 'wc_min_max_cart_quantity_do_not_count', false, $checking_id, $cart_item_key, $values ) ) {
						$values['quantity'] = 0;
					}

					// Do_not_count and cart_exclude from variation or product.
					$minmax_do_not_count = apply_filters( 'wc_min_max_quantity_minmax_do_not_count', ( 'yes' === get_post_meta( $checking_id, 'variation_minmax_do_not_count', true ) ? 'yes' : get_post_meta( $values['product_id'], 'minmax_do_not_count', true ) ), $checking_id, $cart_item_key, $values );

					$minmax_cart_exclude = apply_filters( 'wc_min_max_quantity_minmax_cart_exclude', ( 'yes' === get_post_meta( $checking_id, 'variation_minmax_cart_exclude', true ) ? 'yes' : get_post_meta( $values['product_id'], 'minmax_cart_exclude', true ) ), $checking_id, $cart_item_key, $values );

					// If either the do not count or exclude options are set, don't count this product.
					if ( 'yes' === $minmax_do_not_count || 'yes' === $minmax_cart_exclude ) {
						$this->excludes[] = $product->get_title();
					} else {
						$total_quantity += $values['quantity'];
					}
				}

				// Check if we should count the product being added to our total.
				if ( ! $exclude_from_count ) {
					$total_quantity += $quantity;
				}

				if ( $this->maximum_order_quantity && $this->maximum_order_quantity > 0 ) {
					if ( $total_quantity > $this->maximum_order_quantity ) {
						$error_message    = '';
						$excludes_message = '';

						// If we have excluded products, add that as a label to our error.
						if ( count( $this->excludes ) > 0 ) {
							$excludes_message = ' (' . __( 'excludes ', 'woocommerce-min-max-quantities' ) . implode( ', ', $this->excludes ) . ')';
						}

						if ( 0 === $total_quantity - $quantity ) {
							/* translators: %d: Maximum quantity in cart */
							$error_message = sprintf( __( 'The maximum allowed items in cart is %d. Please decrease the quantity in your cart.', 'woocommerce-min-max-quantities' ), $this->maximum_order_quantity );
						} else {
							/* translators: %1$d: Maximum quanity, %2$d: Current quantity */
							$error_message = sprintf( __( 'The maximum allowed items in cart is %1$d (you currently have %2$d in your cart). Please decrease the quantity in your cart.', 'woocommerce-min-max-quantities' ), $this->maximum_order_quantity, $total_quantity - $quantity );

							if ( intval( $this->maximum_order_quantity ) <= intval( $total_quantity - $quantity ) ) {
								/* translators: %1$d: Maximum quanity, %2$d: Current quantity, %3$s: Cart link */
								$error_message = sprintf( __( 'The maximum allowed items in cart is %1$d (you currently have %2$d in your cart). Please decrease the quantity in your cart. <a href="%3$s" class="woocommerce-min-max-quantities-error-cart-link button wc-forward">View cart</a>', 'woocommerce-min-max-quantities' ), $this->maximum_order_quantity, $total_quantity - $quantity, esc_url( wc_get_cart_url() ) );
							}
						}

						$this->add_error( $error_message . $excludes_message );
						$pass = false;
					}
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
			if ( $this->addons->is_multiple_shipping_address_page() ) {
				return $data;
			}

			$group_of_quantity = $this->get_group_of_quantity_for_product( $product );
			$minimum_quantity  = get_post_meta( $product->get_id(), 'minimum_allowed_quantity', true );
			$maximum_quantity  = get_post_meta( $product->get_id(), 'maximum_allowed_quantity', true );
			$allow_combination = 'yes' === get_post_meta( version_compare( WC_VERSION, '3.0', '<' ) ? $product->get_id() : $product->get_parent_id(), 'allow_combination', true );

			/*
			* If its a variable product and allow combination is enabled,
			* we don't need to set the quantity to default minimum.
			*/
			if ( $allow_combination && $product->is_type( 'variation' ) ) {
				return $data;
			}

			// If variable product, only apply in cart.
			$variation_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $product->variation_id ) ) ? $product->variation_id : $product->get_id();

			if ( is_cart() && $product->is_type( 'variation' ) ) {
				$parent_variable_id = version_compare( WC_VERSION, '3.0', '<' ) ? $product->get_id() : $product->get_parent_id();

				$group_of_quantity = get_post_meta( $parent_variable_id, 'group_of_quantity', true );
				$minimum_quantity  = get_post_meta( $parent_variable_id, 'minimum_allowed_quantity', true );
				$maximum_quantity  = get_post_meta( $parent_variable_id, 'maximum_allowed_quantity', true );
				$allow_combination = 'yes' === get_post_meta( $parent_variable_id, 'allow_combination', true );

				$min_max_rules = get_post_meta( $variation_id, 'min_max_rules', true );

				if ( 'no' === $min_max_rules || empty( $min_max_rules ) ) {
					$min_max_rules = false;

				} else {
					$min_max_rules = true;

				}

				$variation_minimum_quantity  = get_post_meta( $variation_id, 'variation_minimum_allowed_quantity', true );
				$variation_maximum_quantity  = get_post_meta( $variation_id, 'variation_maximum_allowed_quantity', true );
				$variation_group_of_quantity = get_post_meta( $variation_id, 'variation_group_of_quantity', true );

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

			if ( isset( $minimum_quantity ) && strlen( $minimum_quantity ) ) {

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

			if ( $group_of_quantity ) {
				$data['step'] = 1;

				// If both minimum and maximum quantity are set, make sure both are equally divisble by qroup of quantity.
				if ( $maximum_quantity && ( isset( $minimum_quantity ) && strlen( $minimum_quantity ) ) ) {

					if ( absint( $maximum_quantity ) % absint( $group_of_quantity ) === 0 && absint( $minimum_quantity ) % absint( $group_of_quantity ) === 0 ) {
						$data['step'] = $group_of_quantity;

					}
				} elseif ( ! $maximum_quantity || absint( $maximum_quantity ) % absint( $group_of_quantity ) === 0 ) {

					$data['step'] = $group_of_quantity;
				}

				/**
				 * Check if we should use the group of setting as our minimum.
				 *
				 * @since 2.4.22
				 * @param boolean    $use_group Whether we should use the group of setting.
				 * @param WC_Product $product   Product object.
				 * @param array      $data      Available product data.
				 */
				if ( ( ! isset( $minimum_quantity ) || ! strlen( $minimum_quantity ) ) && apply_filters( 'wc_min_max_use_group_as_min_quantity', true, $product, $data ) ) {
					$data['min_value'] = $group_of_quantity;
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
			$variation_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $variation->variation_id ) ) ? $variation->variation_id : $variation->get_id();

			$min_max_rules = get_post_meta( $variation_id, 'min_max_rules', true );

			if ( 'no' === $min_max_rules || empty( $min_max_rules ) ) {
				$min_max_rules = false;

			} else {
				$min_max_rules = true;

			}

			$minimum_quantity  = get_post_meta( $product->get_id(), 'minimum_allowed_quantity', true );
			$maximum_quantity  = get_post_meta( $product->get_id(), 'maximum_allowed_quantity', true );
			$group_of_quantity = get_post_meta( $product->get_id(), 'group_of_quantity', true );
			$allow_combination = 'yes' === get_post_meta( $product->get_id(), 'allow_combination', true );

			$variation_minimum_quantity  = get_post_meta( $variation_id, 'variation_minimum_allowed_quantity', true );
			$variation_maximum_quantity  = get_post_meta( $variation_id, 'variation_maximum_allowed_quantity', true );
			$variation_group_of_quantity = get_post_meta( $variation_id, 'variation_group_of_quantity', true );

			// Override product level.
			if ( $variation->managing_stock() ) {
				$product = $variation;

			}

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

			if ( $minimum_quantity ) {

				if ( $product->managing_stock() && $product->backorders_allowed() && absint( $minimum_quantity ) > $product->get_stock_quantity() ) {
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

			if ( $group_of_quantity ) {
				$data['step'] = 1;

				// If both minimum and maximum quantity are set, make sure both are equally divisible by qroup of quantity.
				if ( $maximum_quantity && $minimum_quantity ) {

					if ( absint( $maximum_quantity ) % absint( $group_of_quantity ) === 0 && absint( $minimum_quantity ) % absint( $group_of_quantity ) === 0 ) {
						$data['step'] = $group_of_quantity;

					}
				} elseif ( ! $maximum_quantity || absint( $maximum_quantity ) % absint( $group_of_quantity ) === 0 ) {

					$data['step'] = $group_of_quantity;
				}

				// Set the minimum only when minimum is not set.
				if ( ! $minimum_quantity ) {
					$data['min_qty'] = $group_of_quantity;
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
					$data['max_qty']     = '';
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
		 * return int
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
				$found_settings = [];

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
	}

	add_action( 'plugins_loaded', array( 'WC_Min_Max_Quantities', 'get_instance' ) );

endif;
