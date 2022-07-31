<?php
/**
 * All Products for Subscriptions - Handles subscription contents switching
 *
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    2.0.0
 * @version  2.0.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Main WC_MNM_APFS_Switching_Compatibility class
 **/
if ( ! class_exists( 'WC_MNM_APFS_Switching_Compatibility' ) ) :

	class WC_MNM_APFS_Switching_Compatibility {

		/**
		 * Runtime cache.
		 *
		 * @var    array
		 */
		private static $cache = array();

		/**
		 * Hooks for MNM support.
		 */
		public static function add_hooks() {

			/*-----------------------------------------------------------------------------------*/
			/*  All types: Application layer integration                                         */
			/*-----------------------------------------------------------------------------------*/

			// Hide child cart item options.
			add_filter( 'wcsatt_show_cart_item_options', array( __CLASS__, 'hide_child_item_options' ), 10, 3 );

			// Child items inherit the active subscription scheme of their parent.
			add_filter( 'wcsatt_set_subscription_scheme_id', array( __CLASS__, 'set_child_item_subscription_scheme' ), 10, 3 );

			// Child cart items inherit the subscription schemes of their parent, with some modifications.
			add_action( 'woocommerce_cart_loaded_from_session', array( __CLASS__, 'apply_child_item_subscription_schemes' ), 0 );

			// Child cart items inherit the subscription schemes of their parent, with some modifications (first add).
			add_filter( 'woocommerce_add_cart_item', array( __CLASS__, 'set_child_item_schemes' ), 0, 2 );

			// Pass subscription details placeholder to JS script.
			add_filter( 'wcsatt_single_product_subscription_option_data', array( __CLASS__, 'container_subscription_option_data' ), 10, 3 );

			/*-----------------------------------------------------------------------------------*/
			/*  Cart                                                                             */
			/*-----------------------------------------------------------------------------------*/

			// Add subscription details next to subtotal of per-item-priced container container cart items.
			add_filter( 'woocommerce_cart_item_subtotal', array( __CLASS__, 'filter_container_item_subtotal' ), 1000, 3 );

			// Modify container cart item options to include child item prices.
			add_filter( 'wcsatt_cart_item_options', array( __CLASS__, 'container_item_options' ), 10, 4 );

			/*-----------------------------------------------------------------------------------*/
			/*  Subscriptions management: 'My Account > Subscriptions' actions                                                                             */
			/*-----------------------------------------------------------------------------------*/

			// Change text for Mix and Match switch link.
			if ( version_compare( WC_Subscriptions::$version, '4.5.0', '>=' ) ) {
				add_filter( 'woocommerce_subscriptions_switch_link_text', array( __CLASS__, 'switch_link_text' ), 10, 4 );
			} else {
				add_filter( 'woocommerce_subscriptions_switch_link', array( __CLASS__, 'switch_link' ), 10, 4 );
			}

			// Don't count container child items and hidden container container/child items.
			add_filter( 'wcs_can_items_be_removed', array( __CLASS__, 'can_remove_subscription_items' ), 10, 2 );

			// Hide "Remove" buttons of child line items under 'My Account > Subscriptions'.
			add_filter( 'wcs_can_item_be_removed', array( __CLASS__, 'can_remove_child_subscription_item' ), 10, 3 );

			// Handle parent subscription line item removals under 'My Account > Subscriptions'.
			add_action( 'wcs_user_removed_item', array( __CLASS__, 'user_removed_parent_subscription_item' ), 10, 2 );

			// Handle parent subscription line item re-additions under 'My Account > Subscriptions'.
			add_action( 'wcs_user_readded_item', array( __CLASS__, 'user_readded_parent_subscription_item' ), 10, 2 );

			/*-----------------------------------------------------------------------------------*/
			/*  Subscriptions management: Switching                                              */
			/*-----------------------------------------------------------------------------------*/

			// Add extra 'Allow Switching' options. See 'WCS_ATT_Admin::allow_switching_options'.
			add_filter( 'woocommerce_subscriptions_allow_switching_options', array( __CLASS__, 'add_container_switching_options' ), 11 );

			// Add the settings to control whether Switching is enabled and how it will behave
			add_filter( 'woocommerce_subscription_settings', array( __CLASS__, 'add_settings' ), 20 );

			// Hide "Upgrade or Downgrade" switching buttons of container line items under 'My Account > Subscriptions'.
			add_filter( 'woocommerce_subscriptions_can_item_be_switched', array( __CLASS__, 'can_switch_container_type_item' ), 10, 3 );

			// Add content switching support to container products.
			add_filter( 'wcsatt_product_supports_feature', array( __CLASS__, 'container_supports_switching' ), 10, 4 );

			// Make WCS see products with a switched scheme as non-identical ones.
			add_filter( 'woocommerce_subscriptions_switch_is_identical_product', array( __CLASS__, 'container_is_identical' ), 10, 6 );

			// Only allow content switching: Container schemes should be limited to the one matching the subscription while the product is being switched.
			add_filter( 'wcsatt_product_subscription_schemes', array( __CLASS__, 'limit_switched_container_type_schemes' ), 10, 2 );

			// Disallow plan switching for container. Only content switching permitted!
			add_filter( 'wcsatt_force_subscription', array( __CLASS__, 'force_switched_container_type_subscription' ), 10, 2 );

			// Restore container configuration when switching.
			add_filter( 'woocommerce_subscriptions_switch_url', array( __CLASS__, 'container_type_switch_configuration_url' ), 10, 4 );

			// Change the order item status of old child items when the new parent is added.
			add_action( 'woocommerce_subscription_item_switched', array( __CLASS__, 'remove_switched_subscription_child_items' ), 10, 4 );

			// Disable proration when switching.
			add_filter( 'wcs_switch_proration_switch_type', array( __CLASS__, 'force_switch_type' ), 10, 3 );
			add_filter( 'woocommerce_before_calculate_totals', array( __CLASS__, 'restore_switch_type' ), 100 );

			// Copy switch parameters from parent item.
			add_filter( 'wc_mnm_child_cart_item_data', array( __CLASS__, 'child_item_switch_cart_data' ), 10, 2 );

			/*-----------------------------------------------------------------------------------*/
			/*  Subscriptions management: Add products/carts to subscriptions                                                                             */
			/*-----------------------------------------------------------------------------------*/

			// Modify the validation context when adding a container to an order.
			add_action( 'wcsatt_pre_add_product_to_subscription_validation', array( __CLASS__, 'set_container_type_validation_context' ), 10 );

			// Modify the validation context when adding a container to an order.
			add_action( 'wcsatt_post_add_product_to_subscription_validation', array( __CLASS__, 'reset_container_type_validation_context' ), 10 );

			// Don't attempt to increment the quantity of container subscription items when adding to an existing subscription.
			add_filter( 'wcsatt_add_cart_to_subscription_found_item', array( __CLASS__, 'found_container_in_subscription' ), 10, 4 );

			// Add container to subscriptions.
			add_filter( 'wscatt_add_cart_item_to_subscription_callback', array( __CLASS__, 'add_container_to_subscription_callback' ), 10, 3 );

			// When loading child items, always set the active container scheme on the child objects.
			add_filter( 'wc_mnm_child_item_product', array( __CLASS__, 'set_child_item_scheme' ), 10, 2 );
			add_action( 'wc_mnm_before_sync', array( __CLASS__, 'set_container_default_scheme' ) );

			// Add scheme data to runtime price cache hashes.
			add_filter( 'wc_mnm_prices_hash', array( __CLASS__, 'container_prices_hash' ), 10, 2 );

		}

		/*
		|--------------------------------------------------------------------------
		| Helpers
		|--------------------------------------------------------------------------
		*/

		/**
		 * True if there are sub schemes inherited from a container.
		 *
		 * @param  array  $cart_item
		 * @return boolean
		 */
		private static function has_scheme_data( $cart_item ) {
			return ! is_null( WCS_ATT_Cart::get_subscription_scheme( $cart_item ) );
		}


		/**
		 * Calculates container item subtotals.
		 *
		 * @param  array   $cart_item
		 * @param  string  $scheme_key
		 * @param  string  $tax
		 * @return double
		 */
		private static function calculate_container_item_subtotal( $cart_item, $scheme_key, $tax = '' ) {

			$product          = $cart_item[ 'data' ];
			$tax_display_cart = '' === $tax ? get_option( 'woocommerce_tax_display_cart' ) : $tax;

			if ( 'excl' === $tax_display_cart ) {
				$subtotal = wc_get_price_excluding_tax( $product, array( 'price' => WCS_ATT_Product_Prices::get_price( $product, $scheme_key ) ) );
			} else {
				$subtotal = wc_get_price_including_tax( $product, array( 'price' => WCS_ATT_Product_Prices::get_price( $product, $scheme_key ) ) );
			}

			$child_items = wc_mnm_get_child_cart_items( $cart_item );

			if ( ! empty( $child_items ) ) {

				foreach ( $child_items as $child_key => $child_item ) {

					$child_qty = ceil( $child_item[ 'quantity' ] / $cart_item[ 'quantity' ] );

					if ( 'excl' === $tax_display_cart ) {
						$subtotal += wc_get_price_excluding_tax( $child_item[ 'data' ], array( 'price' => WCS_ATT_Product_Prices::get_price( $child_item[ 'data' ], $scheme_key ), 'qty' => $child_qty ) );
					} else {
						$subtotal += wc_get_price_including_tax( $child_item[ 'data' ], array( 'price' => WCS_ATT_Product_Prices::get_price( $child_item[ 'data' ], $scheme_key ), 'qty' => $child_qty ) );
					}
				}
			}

			return $subtotal;
		}

		/**
		 * Add containers to subscriptions using 'WC_Mix_and_Match_Order::add_container_to_order'.
		 *
		 * @param  WC_Subscription  $subscription
		 * @param  array            $cart_item
		 * @param  WC_Cart          $recurring_cart
		 */
		public static function add_container_to_order( $subscription, $cart_item, $recurring_cart ) {

			$configuration = $cart_item[ 'mnm_config' ];

			// Copy child item totals over from recurring cart.
			foreach ( wc_mnm_get_child_cart_items( $cart_item, $recurring_cart->cart_contents ) as $child_cart_item_key => $child_cart_item ) {

				$child_item_id = $child_cart_item[ 'mnm_child_id' ];

				$configuration[ $child_item_id ][ 'args' ] = array(
					'subtotal' => $child_cart_item[ 'line_total' ],
					'total'    => $child_cart_item[ 'line_subtotal' ]
				);
			}

			return WC_Mix_and_Match()->order->add_container_to_order( $cart_item[ 'data' ], $subscription, $cart_item[ 'quantity' ], array( 'configuration' => $configuration ) );
		}

		/*
		|--------------------------------------------------------------------------
		| Hooks - Application
		|--------------------------------------------------------------------------
		*/

		/**
		 * Hide child cart item subscription options.
		 *
		 * @param  boolean  $show
		 * @param  array    $cart_item
		 * @param  string   $cart_item_key
		 * @return boolean
		 */
		public static function hide_child_item_options( $show, $cart_item, $cart_item_key ) {

			if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {
				if ( self::has_scheme_data( $container_cart_item ) ) {
					$show = false;
				}
			}

			return $show;
		}

		/**
		 * Child cart items inherit the active subscription scheme id of their parent.
		 *
		 * @param  string  $scheme_key
		 * @param  array   $cart_item
		 * @param  array   $cart_level_schemes
		 * @return string
		 */
		public static function set_child_item_subscription_scheme( $scheme_key, $cart_item, $cart_level_schemes ) {

			if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {
				if ( self::has_scheme_data( $container_cart_item ) ) {
					$scheme_key = $container_cart_item[ 'wcsatt_data' ][ 'active_subscription_scheme' ];
				}
			}

			return $scheme_key;
		}

		/**
		 * Child cart items inherit the subscription schemes of their parent, with some modifications.
		 *
		 * @param  WC_Cart  $cart
		 * @return void
		 */
		public static function apply_child_item_subscription_schemes( $cart ) {

			foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {

				// Is it a child item?
				if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {
					if ( self::has_scheme_data( $container_cart_item ) ) {
						self::set_child_product_subscription_schemes( $cart_item[ 'data' ], $container_cart_item[ 'data' ] );
					} elseif ( WCS_ATT_Product_Schemes::has_subscription_schemes( $cart_item[ 'data' ] ) ) {
						WCS_ATT_Product_Schemes::set_subscription_schemes( $cart_item[ 'data' ], array() );
					}
				}
			}
		}

		/**
		 * Copies product schemes to a child product.
		 *
		 * @param  WC_Product  $child_product
		 * @param  WC_Product  $container_product
		 */
		private static function set_child_product_subscription_schemes( $child_product, $container_product ) {

			$container_schemes     = WCS_ATT_Product_Schemes::get_subscription_schemes( $container_product );
			$child_product_schemes = WCS_ATT_Product_Schemes::get_subscription_schemes( $child_product );

			$container_schemes_hash     = '';
			$child_product_schemes_hash = '';

			foreach ( $container_schemes as $scheme_key => $scheme ) {
				$container_schemes_hash .= $scheme->get_hash();
			}

			foreach ( $child_product_schemes as $scheme_key => $scheme ) {
				$child_product_schemes_hash .= $scheme->get_hash();
			}

			// Copy container schemes to child.
			if ( $container_schemes_hash !== $child_product_schemes_hash ) {

				$child_product_schemes = array();

				// Modify child object schemes: "Override" pricing mode is only applicable for container.
				foreach ( $container_schemes as $scheme_key => $scheme ) {

					$child_product_schemes[ $scheme_key ] = clone $scheme;
					$child_product_scheme                 = $child_product_schemes[ $scheme_key ];

					if ( $child_product_scheme->has_price_filter() && 'override' === $child_product_scheme->get_pricing_mode() ) {
						$child_product_scheme->set_pricing_mode( 'inherit' );
						$child_product_scheme->set_discount( '' );
					}
				}

				WCS_ATT_Product_Schemes::set_subscription_schemes( $child_product, $child_product_schemes );
			}

			$container_scheme     = WCS_ATT_Product_Schemes             :: get_subscription_scheme( $container_product );
			$child_product_scheme = WCS_ATT_Product_Schemes             :: get_subscription_scheme( $child_product );
			$scheme_to_set        = is_null( $container_scheme ) ? false: $container_scheme;

			// Set active container scheme on child.
			if ( $scheme_to_set !== $child_product_scheme ) {
				WCS_ATT_Product_Schemes::set_subscription_scheme( $child_product, $scheme_to_set );
			}

			// Copy "Force Subscription" state.
			WCS_ATT_Product_Schemes::set_forced_subscription_scheme( $child_product, $scheme_to_set ? WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $container_product ) : false );

			return $child_product;
		}

		/**
		 * Child cart items inherit the subscription schemes of their parent, with some modifications (first add).
		 *
		 * @param  array   $cart_item
		 * @param  string  $cart_item_key
		 * @return array
		 */
		public static function set_child_item_schemes( $cart_item, $cart_item_key ) {

			// Is it a child item?
			if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {
				if ( self::has_scheme_data( $container_cart_item ) ) {
					self::set_child_product_subscription_schemes( $cart_item[ 'data' ], $container_cart_item[ 'data' ] );
				}
			}

			return $cart_item;
		}

		/**
		 * Pass subscription details placeholder to JS script.
		 *
		 * @since  3.0.0
		 *
		 * @param  array           $data
		 * @param  WCS_ATT_Scheme  $subscription_scheme
		 * @param  WC_Product      $product
		 * @return array
		 */
		public static function container_subscription_option_data( $data, $subscription_scheme, $product ) {

			if ( $product->is_type( 'mix-and-match' ) ) {

				$subscription_schemes  = WCS_ATT_Product_Schemes::get_subscription_schemes( $product );
				$force_subscription    = WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $product );
				$dropdown_details_html = isset( $data[ 'dropdown_details_html' ] ) ? $data[ 'dropdown_details_html' ] : WCS_ATT_Product_Prices::get_price_html(
                    $product,
                    $subscription_scheme->get_key(),
                    array(
					'context'      => 'dropdown',
					'price'        => '%p',
					'append_price' => false === $force_subscription,
					'hide_price'   => $subscription_scheme->get_length() > 0 && false === $force_subscription // "Deliver every month for 6 months for $8.00 (10% off)" is just too confusing, isn't it?
                    ) 
                );

				// Base scheme defines the prompt string.
				if ( $data[ 'subscription_scheme' ][ 'is_base' ] ) {
					$data[ 'prompt_details_html' ] = WCS_ATT_Product_Prices::get_price_html(
                        $product,
                        null,
                        array(
						'context'    => 'prompt',
						'base_price' => '%p'
                        ) 
                    );
				}

				$data[ 'option_details_html' ] = WCS_ATT_Product_Prices::get_price_html(
                    $product,
                    $subscription_scheme->get_key(),
                    array(
					'context' => 1 === sizeof( $subscription_schemes ) && $force_subscription ? 'catalog' : 'options',
					'price'   => '%p'
                    ) 
                );

				$data[ 'option_has_price' ]           = false !== strpos( $data[ 'option_details_html' ], '%p' );
				$data[ 'dropdown_format' ]            = ucfirst( trim( wp_kses( $dropdown_details_html, array() ) ) );
				$data[ 'dropdown_discounted_format' ] = sprintf( _x( '%1$s (%2$s off)', 'discounted dropdown option price', 'wc-mnm-satt-bridge', 'woocommerce-mix-and-match-products' ), '%p', sprintf( _x( '%s%%', 'dropdown option discount', 'wc-mnm-satt-bridge', 'woocommerce-mix-and-match-products' ), '%d' ) );
				$data[ 'dropdown_discount_decimals' ] = WCS_ATT_Product_Prices::get_formatted_discount_precision();
				$data[ 'dropdown_sale_format' ]       = sprintf( _x( '%1$s (was %2$s)', 'dropdown option sale price', 'wc-mnm-satt-bridge', 'woocommerce-mix-and-match-products' ), '%p', '%r' );
			}

			return $data;
		}

		/*
		|--------------------------------------------------------------------------
		| Hooks - Cart Templates
		|--------------------------------------------------------------------------
		*/

		/**
		 * Add subscription details next to subtotal of per-item-priced container container cart items.
		 *
		 * @param  string  $subtotal
		 * @param  array   $cart_item
		 * @param  string  $cart_item_key
		 * @return string
		 */
		public static function filter_container_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {

			// MnM container subtotals originally modified by WCS are not overwritten by MnM.
			if ( $cart_item[ 'data' ]->is_type( 'mix-and-match' ) ) {
				return $subtotal;
			}

			if ( wc_mnm_is_container_cart_item( $cart_item ) && self::has_scheme_data( $cart_item ) ) {

				if ( $scheme = WCS_ATT_Product_Schemes::get_subscription_scheme( $cart_item[ 'data' ], 'object' ) ) {

					if ( $scheme->is_synced() ) {
						$subtotal = wc_price( self::calculate_container_item_subtotal( $cart_item, $scheme->get_key() ) );
					}

					$subtotal = WCS_ATT_Product_Prices::get_price_string(
                        $cart_item[ 'data' ],
                        array(
						'price' => $subtotal
                        ) 
                    );
				}

				$subtotal = WC_Subscriptions_Switcher::add_cart_item_switch_direction( $subtotal, $cart_item, $cart_item_key );
			}

			return $subtotal;
		}

		/**
		 * Modify container cart item subscription options to include child item prices.
		 *
		 * @param  array   $options
		 * @param  array   $subscription_schemes
		 * @param  array   $cart_item
		 * @param  string  $cart_item_key
		 * @return boolean
		 */
		public static function container_item_options( $options, $subscription_schemes, $cart_item, $cart_item_key ) {

			$child_items = wc_mnm_get_child_cart_items( $cart_item );

			if ( ! empty( $child_items ) ) {

				$product                        = $cart_item[ 'data' ];
				$price_filter_exists            = WCS_ATT_Product_Schemes::price_filter_exists( $subscription_schemes );
				$force_subscription             = WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $product );
				$active_subscription_scheme_key = WCS_ATT_Product_Schemes::get_subscription_scheme( $product );
				$scheme_keys                    = array_merge( $force_subscription ? array() : array( false ), array_keys( $subscription_schemes ) );

				if ( $price_filter_exists ) {

					$container_price = array();

					foreach ( $scheme_keys as $scheme_key ) {
						$price_key                  = false === $scheme_key ? '0' : $scheme_key;
						$container_price[ $price_key ] = self::calculate_container_item_subtotal( $cart_item, $scheme_key );
					}

					$options = array();

					// Non-recurring (one-time) option.
					if ( false === $force_subscription ) {

						$options[] = array(
							'class'       => 'one-time-option',
							'description' => wc_price( $container_price[ '0' ] ),
							'value'       => '0',
							'selected'    => false === $active_subscription_scheme_key,
						);
					}

					// Subscription options.
					foreach ( $subscription_schemes as $subscription_scheme ) {

						$subscription_scheme_key = $subscription_scheme->get_key();

						$description = WCS_ATT_Product_Prices::get_price_string(
                            $product,
                            array(
							'scheme_key' => $subscription_scheme_key,
							'price'      => wc_price( $container_price[ $subscription_scheme_key ] )
                            ) 
                        );

						$options[] = array(
							'class'       => 'subscription-option',
							'description' => $description,
							'value'       => $subscription_scheme_key,
							'selected'    => $active_subscription_scheme_key === $subscription_scheme_key,
						);
					}
				}
			}

			return $options;
		}

		/*
		|--------------------------------------------------------------------------
		| Hooks - Subscriptions View
		|--------------------------------------------------------------------------
		*/

		/**
		 * Change the switch button text for Mix and Match subscriptions.
		 *
		 * @since 2.1.0
		 *
		 * @param string $switch_link_text The switch link html.
		 * @param int $item_id The order item ID of a subscription line item
		 * @param array $item An order line item
		 * @param object $subscription A WC_Subscription object
		 * @return string
		 *
		 */
		public static function switch_link_text( $switch_link_text, $item_id, $item, $subscription ) {

			$product = $item->get_product();

			if ( $product->is_type( 'mix-and-match' ) ) {
				$switch_url  = WC_Subscriptions_Switcher::get_switch_url( $item_id, $item, $subscription );
				$switch_link_text = get_option( 'wc_mnm_subscription_switch_button_text', __( 'Update selections', 'woocommerce-mix-and-match-products' ) );
			}

			return $switch_link_text;
		}
	
		/**
		 * Change the switch button text for Mix and Match subscriptions.
		 *
		 * @since 2.0.9
		 *
		 * @param string $switch_link The switch link html.
		 * @param int $item_id The order item ID of a subscription line item
		 * @param array $item An order line item
		 * @param object $subscription A WC_Subscription object
		 * @return string
		 *
		 */
		public static function switch_link( $switch_link, $item_id, $item, $subscription ) {

			$product = $item->get_product();

			if ( $product->is_type( 'mix-and-match' ) ) {
				$switch_url  = WC_Subscriptions_Switcher::get_switch_url( $item_id, $item, $subscription );
				$switch_text = get_option( 'wc_mnm_subscription_switch_button_text', __( 'Update selections', 'woocommerce-mix-and-match-products' ) );
				$switch_link = sprintf( '<a href="%s" class="wcs-switch-link button">%s</a>', esc_url( $switch_url ), esc_html( $switch_text ) );
			}

			return $switch_link;
		}

		/**
		 * Don't count container child items and hidden container container/child items.
		 *
		 * @param  boolean          $can
		 * @param  WC_Subscription  $subscription
		 * @return boolean
		 */
		public static function can_remove_subscription_items( $can, $subscription ) {

			if ( $can ) {

				$items    = $subscription->get_items();
				$count    = sizeof( $items );
				$subtract = 0;

				foreach ( $items as $item ) {

					if ( wc_mnm_is_container_order_item( $item, $subscription ) ) {

						$parent_item_visible = apply_filters( 'woocommerce_order_item_visible', true, $item );

						if ( ! $parent_item_visible ) {
							$subtract += 1;
						}

						$child_order_items = wc_mnm_get_child_order_items( $item, $subscription );

						foreach ( $child_order_items as $child_item_key => $child_order_item ) {
							if ( ! $parent_item_visible ) {
								if ( ! apply_filters( 'woocommerce_order_item_visible', true, $child_order_item ) ) {
									$subtract += 1;
								}
							} else {
								$subtract += 1;
							}
						}
					}
				}

				$can = $count - $subtract > 1;
			}

			return $can;
		}

		/**
		 * Prevent direct removal of child subscription items from 'My Account > Subscriptions'.
		 * Does ~nothing~ to prevent removal at an application level, e.g. via a REST API call.
		 *
		 * @param  boolean          $can
		 * @param  WC_Order_Item    $item
		 * @param  WC_Subscription  $subscription
		 * @return boolean
		 */
		public static function can_remove_child_subscription_item( $can, $item, $subscription ) {

			if ( wc_mnm_is_child_order_item( $item, $subscription ) ) {
				$can = false;
			}

			return $can;
		}

		/**
		 * Handle parent subscription line item removals under 'My Account > Subscriptions'.
		 *
		 * @param  WC_Order_Item  $item
		 * @param  WC_Order       $subscription
		 * @return void
		 */
		public static function user_removed_parent_subscription_item( $item, $subscription ) {

			if ( wc_mnm_is_container_order_item( $item, $subscription ) ) {

				$child_items     = wc_mnm_get_child_order_items( $item, $subscription );
				$child_item_keys = array();

				if ( ! empty( $child_items ) ) {
					foreach ( $child_items as $child_item ) {

						$child_item_keys[] = $child_item->get_id();

						$child_product_id = wcs_get_canonical_product_id( $child_item );

						// Remove the line item from subscription but preserve its data in the DB.
						wcs_update_order_item_type( $child_item->get_id(), 'line_item_removed', $subscription->get_id() );

						WCS_Download_Handler::revoke_downloadable_file_permission( $child_product_id, $subscription->get_id(), $subscription->get_user_id() );

						// Add order note.
						$subscription->add_order_note( sprintf( _x( '"%1$s" (Product ID: #%2$d) removal triggered by "%3$s" via the My Account page.', 'used in order note', 'wc-mnm-satt-bridge', 'woocommerce-mix-and-match-products' ), wcs_get_line_item_name( $child_item ), $child_product_id, wcs_get_line_item_name( $item ) ) );

						// Trigger WCS action.
						do_action( 'wcs_user_removed_item', $child_item, $subscription );
					}

					// Update session data for un-doing.
					$removed_mnm_child_item_ids = WC()->session->get( 'removed_mnm_child_subscription_items', array() );
					$removed_mnm_child_item_ids[ $item->get_id() ] = $child_item_keys;
					WC()->session->set( 'removed_mnm_child_subscription_items', $removed_mnm_child_item_ids );
				}
			}
		}

		/**
		 * Handle parent subscription line item re-additions under 'My Account > Subscriptions'.
		 *
		 * @param  WC_Order_Item  $item
		 * @param  WC_Order       $subscription
		 * @return void
		 */
		public static function user_readded_parent_subscription_item( $item, $subscription ) {

			if ( wc_mnm_is_container_order_item( $item, $subscription ) ) {

				$removed_mnm_child_item_ids = WC()->session->get( 'removed_mnm_child_subscription_items', array() );
				$removed_mnm_child_item_ids = isset( $removed_mnm_child_item_ids[ $item->get_id() ] ) ? $removed_mnm_child_item_ids[ $item->get_id() ] : array();

				if ( ! empty( $removed_mnm_child_item_ids ) ) {

					foreach ( $removed_mnm_child_item_ids as $removed_mnm_child_item_id ) {

						// Update the line item type.
						wcs_update_order_item_type( $removed_mnm_child_item_id, 'line_item', $subscription->get_id() );
					}
				}

				$child_items = wc_mnm_get_child_order_items( $item, $subscription );

				if ( ! empty( $child_items ) ) {
					foreach ( $child_items as $child_item ) {

						$child_product    = $subscription->get_product_from_item( $child_item );
						$child_product_id = wcs_get_canonical_product_id( $child_item );

						if ( $child_product && $child_product->exists() && $child_product->is_downloadable() ) {

							$downloads = wcs_get_objects_property( $child_product, 'downloads' );

							foreach ( array_keys( $downloads ) as $download_id ) {
								wc_downloadable_file_permission( $download_id, $child_product_id, $subscription, $child_item[ 'qty' ] );
							}
						}

						// Add order note.
						$subscription->add_order_note( sprintf( _x( '"%1$s" (Product ID: #%2$d) removal un-done by "%3$s" via the My Account page.', 'used in order note', 'wc-mnm-satt-bridge', 'woocommerce-mix-and-match-products' ), wcs_get_line_item_name( $child_item ), wcs_get_canonical_product_id( $child_item ), wcs_get_line_item_name( $item ) ) );

						// Trigger WCS action.
						do_action( 'wcs_user_readded_item', $child_item, $subscription );
					}
				}
			}
		}

		/**
		 * Add extra 'Allow Switching' options for content switching of Mix and Match containers
		 *
		 * @See: 'WCS_ATT_Admin::allow_switching_options'.
		 *
		 * @param  array  $data
		 * @return array
		 */
		public static function add_container_switching_options( $data ) {

			$switch_option_mnm_contents = get_option( WC_Subscriptions_Admin::$option_prefix . '_allow_switching_mnm_contents', '' );

			if ( '' === $switch_option_mnm_contents ) {
				update_option( WC_Subscriptions_Admin::$option_prefix . '_allow_switching_mnm_contents', 'yes' );
			}

			$data[] = array(
				'id'    => 'mnm_contents',
				'label' => __( 'Between Mix and Match Configurations', 'wc-mnm-satt-bridge', 'woocommerce-mix-and-match-products' )
			);

			return $data;
		}

		/**
		 * Add Switch settings to the Subscription's settings page.
		 *
		 * @since 2.0.9
		 */
		public static function add_settings( $settings ) {

			$switching_settings = array(
					'name'     => __( 'Mix and Match Configuration Switch Button Text', 'woocommerce-subscriptions', 'woocommerce-mix-and-match-products' ),
					'desc'     => __( 'Customize the text displayed on the button next to the mix and match product subscription on the subscriber\'s account page. The default is "Update selections", but you may wish to change this to "Change selections".', 'woocommerce-subscriptions', 'woocommerce-mix-and-match-products' ),
					'tip'      => '',
					'id'       => 'wc_mnm_subscription_switch_button_text',
					'css'      => 'min-width:150px;',
					'default'  => __( 'Update selections', 'woocommerce-mix-and-match-products' ),
					'type'     => 'text',
					'desc_tip' => true,
			);

			// Insert the switch settings in after the switch button text setting otherwise add them to the end.
			if ( ! WC_Subscriptions_Admin::insert_setting_after( $settings, WC_Subscriptions_Admin::$option_prefix . '_switch_button_text', $switching_settings ) ) {
				$settings = array_merge( $settings, array( $switching_settings ) );
			}

			return $settings;
		}

		/**
		 * Prevent direct switching of child subscription items from 'My Account > Subscriptions'.
		 * Allow content switching for parent items only, which means that a matching scheme must exist.
		 *
		 * @param  boolean          $can
		 * @param  WC_Order_Item    $item
		 * @param  WC_Subscription  $subscription
		 * @return boolean
		 */
		public static function can_switch_container_type_item( $can, $item, $subscription ) {

			$is_child_type_order_item               = wc_mnm_is_child_order_item( $item, $subscription );
			$is_container_type_container_order_item = wc_mnm_is_container_order_item( $item, $subscription );

			if ( $is_container_type_container_order_item && ! $is_child_type_order_item ) {

				// See 'WCS_ATT_Manage_Switch::can_switch_item' for > 3.1.17
				if ( version_compare( WCS_ATT::VERSION, '3.1.17' ) < 0 ) {

					$product = $item->get_product();
					$schemes = WCS_ATT_Product_Schemes::get_subscription_schemes( $product );
					$found   = false;

					// Does a matching scheme exist?
					foreach ( $schemes as $scheme ) {
						if ( $scheme->matches_subscription( $subscription ) ) {
							$found = true;
							break;
						}
					}

					if ( ! $found ) {
						$can = false;
					}

				}

			} elseif ( $is_child_type_order_item ) {

				// Don't render 'Upgrade/Downgrade' button for child items: Switches are handled through the parent!
				if ( doing_action( 'woocommerce_order_item_meta_end' ) ) {
					$can = false;
				// If the parent is switchable, then the child is switchable, too!
				} else {
					$can = WC_Subscriptions_Switcher::can_item_be_switched( wc_mnm_get_order_item_container( $item, $subscription ), $subscription );
				}
			}

			return $can;
		}

		/**
		 * Add content switching support.
		 *
		 * @param  bool        $is_feature_supported
		 * @param  WC_Product  $product
		 * @param  string      $feature
		 * @param  array       $args
		 * @return bool
		 */
		public static function container_supports_switching( $is_feature_supported, $product, $feature, $args ) {

			if ( 'subscription_scheme_switching' === $feature && $product->is_type( 'mix-and-match' ) ) {

				$is_feature_supported = false;

			} elseif ( 'subscription_content_switching' === $feature && false === $is_feature_supported && $product->is_type( 'mix-and-match' ) ) {

				$subscription_has_fixed_length = isset( $args[ 'subscription' ] ) ? $args[ 'subscription' ]->get_time( 'end', '' ) : false;
				// Length Proration must be enabled for switching to be possible when the current subscription/plan has a fixed length.
				if ( $subscription_has_fixed_length && 'yes' !== get_option( WC_Subscriptions_Admin::$option_prefix . '_apportion_length', 'no' ) ) {

					$is_feature_supported = false;

				} else {

					$option_value = get_option( WC_Subscriptions_Admin::$option_prefix . '_allow_switching_mnm_contents', 'yes' );

					if ( 'no' !== $option_value ) {
						$subscription_schemes = WCS_ATT_Product_Schemes::get_subscription_schemes( $product );
						$is_feature_supported = sizeof( $subscription_schemes );
					}

				}

			}

			return $is_feature_supported;
		}


		/**
		 * Make WCS see containers with a switched content as non-identical ones.
		 *
		 * @param  boolean        $is_identical
		 * @param  int            $product_id
		 * @param  int            $quantity
		 * @param  int            $variation_id
		 * @param  WC_Order       $subscription
		 * @param  WC_Order_Item  $item
		 * @return boolean
		 */
		public static function container_is_identical( $is_identical, $product_id, $quantity, $variation_id, $subscription, $item ) {

			if ( $is_identical ) {

				if ( wc_mnm_is_container_order_item( $item, $subscription ) ) {

					$product = wc_get_product( $product_id );

					if ( $product->is_type( 'mix-and-match' ) ) {

						$configuration = WC_Mix_and_Match()->cart->get_posted_container_configuration( $product_id );

						foreach ( $configuration as $child_item_id => $child_item_configuration ) {

							/**
							 * 'wc_mnm_child_item_cart_item_identifier' filter.
							 *
							 * Filters the config data array - use this to add any container-specific data that should result in unique container item ids being produced when the input data changes, such as add-ons data.
							 *
							 * @param  array  $posted_item_config
							 * @param  int    $child_item_id
							 * @param  mixed  $product_id
							 */
							$configuration[ $child_item_id ] = apply_filters( 'wc_mnm_child_item_cart_item_identifier', $child_item_configuration, $child_item_id, $product_id );
						}

						$is_identical = $item->get_meta( '_mnm_config', true ) === $configuration;

					}

				}
			}

			return $is_identical;
		}

		/**
		 * Retrieve subscription switch-related parameters of child items from the parent cart item data array.
		 *
		 * @param  array  $child_item_cart_data
		 * @param  array  $cart_item_data
		 * @return array
		 */
		public static function child_item_switch_cart_data( $child_item_cart_data, $cart_item_data ) {

			if ( ! isset( $_GET[ 'switch-subscription' ] ) ) {
				return $child_item_cart_data;
			}

			if ( empty( $cart_item_data[ 'subscription_switch' ] ) ) {
				return $child_item_cart_data;
			}

			if ( ! isset( $cart_item_data[ 'subscription_switch' ][ 'subscription_id' ], $cart_item_data[ 'subscription_switch' ][ 'item_id' ], $cart_item_data[ 'subscription_switch' ][ 'next_payment_timestamp' ] ) ) {
				return $child_item_cart_data;
			}

			$subscription_id   = $cart_item_data[ 'subscription_switch' ][ 'subscription_id' ];
			$container_item_id = $cart_item_data[ 'subscription_switch' ][ 'item_id' ];

			$child_item_cart_data[ 'subscription_switch' ] = array(
				'subscription_id'        => $subscription_id,
				'item_id'                => '',
				'next_payment_timestamp' => $cart_item_data[ 'subscription_switch' ][ 'next_payment_timestamp' ],
				'upgraded_or_downgraded' => ''
			);

			$subscription = wcs_get_subscription( $subscription_id );

			if ( $container_item_id ) {

				$parent_item       = wcs_get_order_item( $container_item_id, $subscription );
				$child_item_id     = $child_item_cart_data[ 'mnm_child_id' ];
				$child_order_items = wc_mnm_get_child_order_items( $parent_item, $subscription );

				foreach ( $child_order_items as $child_order_item_id => $child_order_item ) {
					if ( absint( $child_item_id ) === absint( $child_order_item->get_id() ) ) {
						$child_item_cart_data[ 'subscription_switch' ][ 'item_id' ] = $child_order_item_id;
						break;
					}
				}
			}

			return $child_item_cart_data;
		}

		/**
		 * Restore container configuration when switching.
		 *
		 * @param  string           $url
		 * @param  int              $item_id
		 * @param  WC_Order_Item    $item
		 * @param  WC_Subscription  $subscription
		 * @return string
		 */
		public static function container_type_switch_configuration_url( $url, $item_id, $item, $subscription ) {

			if ( wc_mnm_is_container_order_item( $item, $subscription ) ) {

				if ( $configuration = WC_Mix_and_Match_Order::get_current_container_configuration( $item, $subscription ) ) {

					$args = WC_Mix_and_Match()->cart->rebuild_posted_container_form_data( $configuration, $item->get_product() );

					$key = key( $args );
					$array = current( $args );

					$args_data = array_map( 'urlencode', $array );
					$args_keys = array_map( 'urlencode', array_keys( $array ) );

					if ( ! empty( $array ) ) {
						$url = add_query_arg( array( urlencode( $key ) => array_combine( $args_keys, $args_data ) ), $url );
					}

				}

			}

			return $url;
		}

		/**
		 * Changes the order item status of old child items when the new parent is added.
		 *
		 * @param  WC_Order         $order
		 * @param  WC_Subscription  $subscription
		 * @param  int              $adding_item_id
		 * @param  int              $removing_item_id
		 * @return void
		 */
		public static function remove_switched_subscription_child_items( $order, $subscription, $adding_item_id, $removing_item_id ) {

			$removing_item = $subscription->get_item( $removing_item_id );

			if ( $child_items = wc_mnm_get_child_order_items( $removing_item, $subscription, true ) ) {
				foreach ( $child_items as $child_item ) {
					wcs_update_order_item_type( $child_item, 'line_item_switched', $subscription->get_id() );
				}
			}
		}

		/**
		 * Disallow plan switching for mix and match. Only content switching permitted!
		 *
		 * @param  boolean     $is_forced
		 * @param  WC_Product  $product
		 * @return boolean
		 */
		public static function force_switched_container_type_subscription( $is_forced, $product ) {

			if ( $product->is_type( 'mix-and-match' ) ) {
				if ( ! $is_forced && WCS_ATT_Manage_Switch::is_switch_request() ) {
					$is_forced = WCS_ATT_Manage_Switch::is_switch_request_for_product( $product );
				}
			}

			return $is_forced;
		}

		/**
		 * Container schemes should be limited to the one matching the subscription while the product is being switched.
		 * This is the meaning of 'content switching': It's not permitted to apply plan changes, only content changes are allowed.
		 *
		 * @param  array       $schemes
		 * @param  WC_Product  $product
		 * @return array
		 */
		public static function limit_switched_container_type_schemes( $schemes, $product ) {

			if ( $product->is_type( 'mix-and-match' ) ) {
				if ( WCS_ATT_Manage_Switch::is_switch_request_for_product( $product ) ) {

					$subscription = wcs_get_subscription( $_GET[ 'switch-subscription' ] );

					if ( ! $subscription ) {
						return $schemes;
					}

					// Does a matching scheme exist?
					foreach ( $schemes as $scheme_id => $scheme ) {
						if ( $scheme->matches_subscription( $subscription ) ) {
							$schemes = array( $scheme_id => $scheme );
							break;
						}
					}
				}
			}

			return $schemes;
		}

		/*
		|--------------------------------------------------------------------------
		| Hooks - Add to Subscription
		|--------------------------------------------------------------------------
		*/

		/**
		 * Modify the validation context when adding a container product to an order.
		 *
		 * @param  int  $product_id
		 */
		public static function set_container_type_validation_context( $product_id ) {
			add_filter( 'wc_mnm_container_validation_context', array( __CLASS__, 'set_add_to_order_validation_context' ) );
			add_filter( 'wc_mnm_add_to_order_container_validation', array( __CLASS__, 'validate_container_type_stock' ), 10, 4 );
		}

		/**
		 * Modify the validation context when adding a container product to an order.
		 *
		 * @param  int  $product_id
		 */
		public static function reset_container_type_validation_context( $product_id ) {
			remove_filter( 'wc_mnm_container_validation_context', array( __CLASS__, 'set_add_to_order_validation_context' ) );
			remove_filter( 'wc_mnm_add_to_order_container_validation', array( __CLASS__, 'validate_container_type_stock' ), 10, 4 );
		}

		/**
		 * Sets the validation context to 'add-to-order'.
		 *
		 * @param  WC_Product_Mix_and_Match  $container
		 */
		public static function set_add_to_order_validation_context( $container ) {
			return 'add-to-order';
		}

		/**
		 * Validates container stock in 'add-to-order' context.
		 *
		 * @param  boolean                  $is_valid
		 * @param  WC_Product_Mix_and_Match $container
		 * @param  WC_MNM_Stock_Manager     $mnm_stock
		 * @param  array                    $configuration
		 * @param  boolean  $is_valid
		 */
		public static function validate_container_type_stock( $is_valid, $container, $stock_manager, $configuration ) {

			if ( $is_valid ) {

				try {

					$stock_manager->validate_stock( array( 'throw_exception' => true, 'context' => 'add-to-order' ) );

				} catch ( Exception $e ) {

					$notice = $e->getMessage();

					if ( $notice ) {
						wc_add_notice( $notice, 'error' );
					}

					$is_valid = false;
				}
			}

			return $is_valid;
		}

		/**
		 * Don't attempt to increment the quantity of container subscription items when adding to an existing subscription.
		 * Also omit child items -- they'll be added by their parent.
		 *
		 * @param  false|WC_Order_Item_Product  $found_order_item
		 * @param  array                        $matching_cart_item
		 * @param  WC_Cart                      $recurring_cart
		 * @param  WC_Subscription              $subscription
		 * @return false|WC_Order_Item_Product
		 */
		public static function found_container_in_subscription( $found_order_item, $matching_cart_item, $recurring_cart, $subscription ) {

			if ( $found_order_item ) {
				if ( self::is_container_type_product( $matching_cart_item[ 'data' ] ) ) {
					$found_order_item = false;
				} elseif ( wc_mnm_is_child_cart_item( $matching_cart_item, $recurring_cart->cart_contents ) ) {
					$found_order_item = false;
				}
			}

			return $found_order_item;
		}

		/**
		 * Return 'add_container_to_order' as a callback for adding mix and match containers to subscriptions.
		 * Do not add child items as they'll be added by their parent.
		 *
		 * @param  array    $callback
		 * @param  array    $cart_item
		 * @param  WC_Cart  $recurring_cart
		 */
		public static function add_container_to_subscription_callback( $callback, $cart_item, $recurring_cart ) {

			if ( wc_mnm_is_container_cart_item( $cart_item, $recurring_cart->cart_contents ) ) {

				if ( $cart_item[ 'data' ]->is_type( 'mix-and-match' ) ) {
					$callback = array( __CLASS__, 'add_container_to_order' );
				}

			} elseif ( wc_mnm_is_child_cart_item( $cart_item, $recurring_cart->cart_contents ) ) {
				$callback = null;
			}

			return $callback;
		}

		/*
		|--------------------------------------------------------------------------
		| Hooks - Container
		|--------------------------------------------------------------------------
		*/

		/**
		 * Set default scheme on container product.
		 *
		 * @param  WC_Product_Mix_and_Match $container
		 */
		public static function set_container_default_scheme( $container ) {

			// Set the default scheme when one-time purchases are disabled, no scheme is set on the object, and only a single sub scheme exists.
			if ( WCS_ATT_Product_Schemes::has_single_forced_subscription_scheme( $container ) && ! WCS_ATT_Product_Schemes::get_subscription_scheme( $container ) ) {
				WCS_ATT_Product_Schemes::set_subscription_scheme( $container, WCS_ATT_Product_Schemes::get_default_subscription_scheme( $container ) );
			}

		}

		/**
		 * When loading child item's product, always set the active container scheme on the child product.
		 *
		 * @Note - This is done differently from WCS_ATT_Integration_PB_CP::set_bundled_items_scheme() because in MNM 2.0, $child_item->get_product() returns a cached value of the product object and $child-item->product is private.
		 * This make the product object not directly manipulatable on the `wc_mnm_get_child_items` filter the way it is on `woocommerce_bundled_items` for Product Bundles.
		 * So we need to manipulate the product object when it initialized, on the `wc_mnm_child_item_product` filter. see WC_MNM_Child_Item::get_product()
		 *
		 * @param  WC_Product $child_product
		 * @param  WC_MNM_Child_Item
		 * @return WC_Product
		 */
		public static function set_child_item_scheme( $child_product, $child_item ) {

			if ( $child_product ) {

				$container = $child_item->get_container();

				if ( $container ) {

					if ( WCS_ATT_Product_Schemes::has_subscription_schemes( $container ) ) {

						self::set_child_product_subscription_schemes( $child_product, $container );

					} else {

						WCS_ATT_Product_Schemes::set_subscription_schemes( $child_product, array() );

					}

				}

			}

			return $child_product;
		}




		/**
		 * Add scheme data to runtime price cache hashes.
		 *
		 * @param  array              $hash
		 * @param  WC_Product_Mix_and_Match  $container
		 * @return array
		 */
		public static function container_prices_hash( $hash, $container ) {

			if ( $scheme = WCS_ATT_Product_Schemes::get_subscription_scheme( $container ) ) {
				$hash[ 'satt_scheme' ] = $scheme;
			}

			return $hash;
		}

		/**
		 * Calculate correct switch type for containers and force crossgrade to disable proration calculations. Remember to cache the initial value.
		 *
		 * @param  string           $switch_type
		 * @param  WC_Subscription  $subscription
		 * @param  array            $cart_item
		 * @return string
		 */
		public static function force_switch_type( $switch_type, $subscription, $cart_item ) {

			$is_container_type_container_cart_item = wc_mnm_is_container_cart_item( $cart_item );
			$is_container_type_cart_item           = wc_mnm_is_child_cart_item( $cart_item );

			// If it's a container parent/child item, fake a crossgrade switch type as APFS doesn't support switch proration for these types.
			if ( $is_container_type_container_cart_item || $is_container_type_cart_item ) {

				// Calculate correct switch type based on aggregated parent/child costs.
				if ( $is_container_type_container_cart_item && ! empty( $cart_item[ 'subscription_switch' ][ 'item_id' ] ) ) {

					if ( $item = $subscription->get_item( $cart_item[ 'subscription_switch' ][ 'item_id' ] ) ) {

						$aggregated_total_old = $item->get_total();

						$child_items = wc_mnm_get_child_order_items( $item, $subscription );

						if ( ! empty( $child_items ) ) {
							foreach ( $child_items as $child_item ) {
								$aggregated_total_old += $child_item->get_total();
							}
						}

						remove_filter( 'woocommerce_product_get_price', 'WC_Subscriptions_Cart::set_subscription_prices_for_calculation', 100 );
						$aggregated_total_new = self::calculate_container_item_subtotal( $cart_item, '', 'excl' );
						add_filter( 'woocommerce_product_get_price', 'WC_Subscriptions_Cart::set_subscription_prices_for_calculation', 100, 2 );

						if ( $aggregated_total_old < $aggregated_total_new ) {
							$switch_type = 'upgrade';
						} elseif ( $aggregated_total_old > $aggregated_total_new && $aggregated_total_new >= 0 ) {
							$switch_type = 'downgrade';
						} else {
							$switch_type = 'crossgrade';
						}
					}
				}

				if ( isset( $cart_item[ 'key' ] ) ) {
					self::$cache[ 'wcs_switch_types' ][ $cart_item[ 'key' ] ] = sprintf( '%sd', $switch_type );
				}

				$switch_type = 'crossgrade';
			}

			return $switch_type;
		}

		/**
		 * Restore initial switch type if applicable.
		 *
		 * @since 2.4.0
		 *
		 * @param  WC_Cart  $cart
		 * @return void
		 */
		public static function restore_switch_type( $cart ) {

			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
				if ( wc_mnm_is_container_cart_item( $cart_item ) || wc_mnm_is_child_cart_item( $cart_item ) ) {
					if ( isset( self::$cache[ 'wcs_switch_types' ][ $cart_item_key ], $cart_item[ 'subscription_switch' ], $cart_item[ 'subscription_switch' ][ 'upgraded_or_downgraded' ] ) ) {
						WC()->cart->cart_contents[ $cart_item_key ][ 'subscription_switch' ][ 'upgraded_or_downgraded' ] = self::$cache[ 'wcs_switch_types' ][ $cart_item_key ];
					}
				}
			}
		}

	} // End class: do not remove or there will be no more guacamole for you.

endif; // End class_exists check.

WC_MNM_APFS_Switching_Compatibility::add_hooks();
