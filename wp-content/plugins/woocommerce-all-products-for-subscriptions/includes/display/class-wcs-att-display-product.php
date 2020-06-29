<?php
/**
 * WCS_ATT_Display_Product class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce All Products For Subscriptions
 * @since    2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single-product template modifications.
 *
 * @class    WCS_ATT_Display_Product
 * @version  3.1.0
 */
class WCS_ATT_Display_Product {

	/**
	 * Initialization.
	 */
	public static function init() {
		self::add_hooks();
	}

	/**
	 * Single-product display hooks.
	 */
	private static function add_hooks() {

		// Display subscription options in the single-product template.
		add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'show_subscription_options' ), 100 );

		// Changes the single-product add-to-cart button text when a product with the force subscription is set.
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( __CLASS__, 'single_add_to_cart_text' ), 10, 2 );

		// Changes the shop button text when a product has subscription options.
		add_filter( 'woocommerce_product_add_to_cart_text', array( __CLASS__, 'add_to_cart_text' ), 10, 2 );

		// Changes the shop button action when a product has subscription options.
		add_filter( 'woocommerce_product_add_to_cart_url', array( __CLASS__, 'add_to_cart_url' ), 10, 2 );
		add_filter( 'woocommerce_product_supports', array( __CLASS__, 'supports_ajax_add_to_cart' ), 10, 3 );

		// Replace plain variation price html with subscription options template.
		add_filter( 'woocommerce_available_variation', array( __CLASS__, 'add_subscription_options_to_variation_data' ), 0, 3 );

		// Add product page class if a product has subscription plans.
		add_filter( 'woocommerce_post_class', array( __CLASS__, 'add_product_class' ), 10, 2 );
	}

	/**
	 * Options for purchasing a product once or creating a subscription from it.
	 *
	 * @param  WC_Product       $product
	 * @param  WC_Product|null  $parent_product
	 * @return void
	 */
	public static function get_subscription_options_content( $product, $parent_product = null ) {

		if ( ! WCS_ATT_Product::supports_feature( $product, 'subscription_scheme_options_product_single' ) ) {
			return '';
		}

		/*
		 * Subscription options for variable products are embedded inside the variation data 'price_html' field and updated by the core variations script.
		 */
		if ( $product->is_type( 'variable' ) ) {
			if ( self::modify_variation_data_price_html( $product ) ) {
				return '';
			} else {
				return '<div class="wcsatt-options-wrapper wcsatt-options-wrapper--variation"></div>';
			}
		}

		$product_id                      = WCS_ATT_Core_Compatibility::get_product_id( $product );
		$subscription_schemes            = WCS_ATT_Product_Schemes::get_subscription_schemes( $product );
		$base_scheme                     = WCS_ATT_Product_Schemes::get_base_subscription_scheme( $product );
		$force_subscription              = WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $product );
		$default_subscription_scheme_key = WCS_ATT_Product_Schemes::get_default_subscription_scheme( $product, 'key' );
		$posted_subscription_scheme_key  = WCS_ATT_Product_Schemes::get_posted_subscription_scheme( $product_id );
		$options                         = array();
		$layout                          = self::get_subscription_options_layout( $parent_product ? $parent_product : $product );
		$display_dropdown                = 'grouped' === $layout;

		// Filter default key.
		$default_subscription_scheme_key = apply_filters( 'wcsatt_get_default_subscription_scheme_id', $default_subscription_scheme_key, $subscription_schemes, false === $force_subscription, $product ); // Why 'false === $force_subscription'? The answer is back-compat.

		// Option selected by default.
		if ( null !== $posted_subscription_scheme_key ) {
			$default_subscription_scheme_key = $posted_subscription_scheme_key;
		}

		$default_subscription_scheme_option_value = WCS_ATT_Product_Schemes::stringify_subscription_scheme_key( $default_subscription_scheme_key );

		// Non-recurring (one-time) option.
		if ( false === $force_subscription ) {

			$none_string = _x( 'one time', 'product subscription selection - negative response', 'woocommerce-all-products-for-subscriptions' );

			$options[] = array(
				'class'       => 'one-time-option',
				'description' => apply_filters( 'wcsatt_single_product_one_time_option_description', $none_string, $product ),
				'value'       => '0',
				'selected'    => '0' === $default_subscription_scheme_option_value,
				'data'        => apply_filters( 'wcsatt_single_product_one_time_option_data', array(), $product, $parent_product )
			);
		}

		// Subscription options.
		foreach ( $subscription_schemes as $subscription_scheme ) {

			$option_price_html_args = array(
				'context'         => 'radio',
				'append_discount' => true
			);

			$scheme_key       = $subscription_scheme->get_key();
			$is_base_scheme   = $base_scheme->get_key() === $scheme_key;
			$has_price_filter = $subscription_scheme->has_price_filter();

			/**
			 * 'wcsatt_single_product_subscription_option_price_html_args' filter
			 *
			 * Use this filter to override subscription plan price strings.
			 *
			 * For example, add [ 'append_discount' => true ] to append discounts to plan prices.
			 *
			 * @param  array            $option_price_html_args
			 * @param  WCS_ATT_Scheme   $subscription_scheme
			 * @param  WC_Product       $product
			 * @param  WC_Product|null  $parent_product
			 */
			$option_price_html_args = apply_filters( 'wcsatt_single_product_subscription_option_price_html_args', $option_price_html_args, $subscription_scheme, $product, $parent_product );

			$is_nyp = class_exists( 'WCS_ATT_Integration_NYP' ) && WC_Name_Your_Price_Helpers::is_nyp( $product );

			if ( $is_nyp ) {
				WCS_ATT_Integration_NYP::before_subscription_option_get_price_html();
			}

			// Get price.
			$sub_price_html = WCS_ATT_Product_Prices::get_price_html( $product, $scheme_key, $option_price_html_args );

			if ( $is_nyp ) {
				WCS_ATT_Integration_NYP::after_subscription_option_get_price_html();
			}

			$option_data = array(
				'discount_from_regular' => apply_filters( 'wcsatt_discount_from_regular', false ),
				'option_has_price'      => false,
				'subscription_scheme'   => array_merge( $subscription_scheme->get_data(), array(
					'is_prorated'           => WCS_ATT_Sync::is_first_payment_prorated( $product, $scheme_key ),
					'is_base'               => $is_base_scheme,
					'has_price_filter'      => $has_price_filter
				) ),
			);

			// Dropdown price strings need special handling, as html is not allowed.
			if ( $display_dropdown ) {

				$dropdown_option_price_html_args = apply_filters( 'wcsatt_single_product_subscription_dropdown_option_price_html_args', array(
					'context'      => 'dropdown',
					'price'        => '%p',
					'append_price' => false === $force_subscription,
					'hide_price'   => $subscription_scheme->get_length() > 0 && false === $force_subscription // "Deliver every month for 6 months for $8.00 (10% off)" is just too confusing, isn't it?
				), $subscription_scheme, $product, $parent_product );

				$option_data[ 'dropdown_details_html' ] = WCS_ATT_Product_Prices::get_price_html( $product, $subscription_scheme->get_key(), $dropdown_option_price_html_args );
			}

			$option_data = apply_filters( 'wcsatt_single_product_subscription_option_data', $option_data, $subscription_scheme, $product, $parent_product );

			$option_has_price   = $option_data[ 'option_has_price' ] || false !== strpos( $sub_price_html, 'amount' );
			$option_price_class = $option_has_price ? 'price' : 'no-price';

			$option_description = apply_filters( 'wcsatt_single_product_subscription_option_description', '<span class="' . $option_price_class . ' subscription-price">' . $sub_price_html . '</span>', $sub_price_html, $has_price_filter, false === $force_subscription, $product, $subscription_scheme );

			$option = array(
				'class'       => 'subscription-option',
				'value'       => $scheme_key,
				'selected'    => $default_subscription_scheme_option_value === $scheme_key,
				'description' => $option_description,
				'data'        => $option_data
			);

			// Now that all data has been filtered, create dropdown descriptions.
			if ( $display_dropdown ) {
				$option[ 'dropdown' ] = self::format_subscription_options_dropdown_description( $option[ 'data' ][ 'dropdown_details_html' ], $product, $subscription_scheme, $dropdown_option_price_html_args );
			}

			$options[] = $option;
		}

		/**
		 * 'wcsatt_single_product_options' filter.
		 *
		 * @param  array       $options
		 * @param  array       $subscription_schemes
		 * @param  WC_Product  $product
		 */
		$options      = apply_filters( 'wcsatt_single_product_options', $options, $subscription_schemes, $product );
		$options_html = '';

		// Anything to display?
		if ( count( $options ) > 0 ) {

			/*
			 * When the "grouped" layout is active and one-time purchases are allowed, a "purchase one-time or subscribe" prompt needs to be displayed above the subscription plans.
			 * By default, this prompt is a pair of radio inputs, but it's also possible to have a checkbox there.
			 */
			$prompt_type     = 'grouped' === $layout && false === $force_subscription ? 'radio' : 'text';
			$prompt          = '';
			$text            = self::get_subscription_options_prompt_text( $parent_product ? $parent_product : $product );
			$wrapper_classes = array();
			$prompt_classes  = array();

			/**
			 * 'wcsatt_grouped_layout_prompt_type' filter.
			 *
			 * Accepted values: 'radio', 'checkbox'.
			 *
			 * @since  3.0.0
			 *
			 * @param  string      $type
			 * @param  WC_Product  $product
			 */
			$prompt_type = apply_filters( 'wcsatt_grouped_layout_prompt_type', $prompt_type, $product );

			if ( $text ) {

				ob_start();

				wc_get_template( 'single-product/product-subscription-options-prompt-text.php', array(
					'text' => $text
				), false, WCS_ATT()->plugin_path() . '/templates/' );

				$prompt = ob_get_clean();
			}

			$wrapper_classes[] = 'wcsatt-options-wrapper-' . $layout;
			$wrapper_classes[] = 'wcsatt-options-wrapper-' . $prompt_type;
			$wrapper_classes[] = 'grouped' === $layout || count( $options ) === 1 ? 'closed' : 'open';
			$wrapper_classes[] = $product->is_type( 'variation' ) && ! self::modify_variation_data_price_html( $parent_product ) ? 'wcsatt-options-wrapper--variation' : '';

			if ( in_array( $prompt_type, array( 'radio', 'checkbox' ) ) ) {

				$prompt_html_args = array(
					'context' => 'prompt'
				);

				if ( 'checkbox' === $prompt_type ) {
					$prompt_html_args[ 'subscribe_options_html' ] = _x( 'Choose a subscription plan', 'Subscribe call-to-action - checkbox', 'woocommerce-all-products-for-subscriptions' );
				}

				/**
				 * 'wcsatt_single_product_subscription_prompt_html_args' filter
				 *
				 * Use this filter to modify the format of the subscription prompt price string.
				 *
				 * For example, add [ 'allow_discount' => false ] to always display the base plan price instead of the max discount.
				 *
				 * @since  3.0.0
				 *
				 * @param  array            $option_price_html_args
				 * @param  array            $options
				 * @param  WC_Product       $product
				 * @param  WC_Product|null  $parent_product
				 */
				$prompt_html_args = apply_filters( 'wcsatt_single_product_subscription_prompt_html_args', $prompt_html_args, $options, $product, $parent_product );

				if ( 'radio' === $prompt_type ) {

					$subscription_cta = WCS_ATT_Product_Prices::get_price_html( $product, null, $prompt_html_args );

					ob_start();

					wc_get_template( 'single-product/product-subscription-options-prompt-radio.php', array(
						'one_time_cta'     => __( 'One-time purchase', 'woocommerce-all-products-for-subscriptions' ),
						'subscription_cta' => $subscription_cta
					), false, WCS_ATT()->plugin_path() . '/templates/' );

					$prompt .= ob_get_clean();

				} elseif ( 'checkbox' === $prompt_type ) {

					$cta = WCS_ATT_Product_Prices::get_price_html( $product, null, $prompt_html_args );

					ob_start();

					wc_get_template( 'single-product/product-subscription-options-prompt-checkbox.php', array(
						'cta' => $cta
					), false, WCS_ATT()->plugin_path() . '/templates/' );

					$prompt .= ob_get_clean();
				}
			}

			$prompt_classes[] = 'wcsatt-options-product-prompt-' . $layout;
			$prompt_classes[] = 'wcsatt-options-product-prompt-' . $prompt_type;
			$prompt_classes[] = 'wcsatt-options-product-prompt--' . ( $prompt ? 'visible' : 'hidden' );

			ob_start();

			wc_get_template( 'single-product/product-subscription-options.php', array(
				'layout'           => $layout,
				'product'          => $product,
				'product_id'       => $product_id,
				'options'          => $options,
				'prompt'           => $prompt,
				'prompt_type'      => $prompt_type,
				'prompt_classes'   => $prompt_classes,
				'wrapper_classes'  => $wrapper_classes,
				'display_dropdown' => $display_dropdown,
				'allow_one_time'   => false === $force_subscription,
				'sign_up_text'     => self::get_subscription_options_button_text( $parent_product ? $parent_product : $product ),
				'dropdown_label'   => $force_subscription ? '' : self::get_subscription_options_dropdown_label( $product ),
				'hide_wrapper'     => count( $options ) === 1 || ( $product->is_type( 'bundle' ) && $product->requires_input() ) || $product->is_type( 'composite' )
			), false, WCS_ATT()->plugin_path() . '/templates/' );

			$options_html = ob_get_clean();
		}

		return $options_html;
	}

	/**
	 * Formats a dropdown description by cleaning up an html price string and replacing the price in a placeholder.
	 *
	 * @since  3.0.0
	 *
	 * @param  string          $dropdown_details_html
	 * @param  WC_Product      $product
	 * @param  WCS_ATT_Scheme  $subscription_scheme
	 * @param  array           $args
	 * @return string
	 */
	public static function format_subscription_options_dropdown_description( $dropdown_details_html, $product, $subscription_scheme, $args = array() ) {

		$formatted_discount = isset( $args[ 'allow_discount' ] ) && false === $args[ 'allow_discount' ] ? '' : WCS_ATT_Product_Prices::get_formatted_discount( $product, $subscription_scheme );
		$dropdown_price     = WCS_ATT_Product_Prices::get_formatted_price( wc_get_price_to_display( $product, array(
			'price' => WCS_ATT_Product_Prices::get_price( $product, $subscription_scheme->get_key() )
		) ) );
		$dropdown_details            = ucfirst( trim( wp_kses( $dropdown_details_html, array() ) ) );
		$dropdown_description_string = $formatted_discount ? _x( '%1$s (%2$s off)', 'discounted dropdown option price', 'woocommerce-all-products-for-subscriptions' ) : '%s';

		return sprintf( $dropdown_description_string, str_replace( '%p', $dropdown_price, $dropdown_details ), $formatted_discount );
	}

	/**
	 * Controls where variation subscription scheme options will be rendered: In the variation data array's 'price_html' key, or before the add to cart button.
	 *
	 * @since  2.3.1
	 *
	 * @param  WC_Product_Variable  $variable_product
	 * @return bool
	 */
	protected static function modify_variation_data_price_html( $variable_product  ) {
		return apply_filters( 'wcsatt_modify_variation_data_price_html', true, $variable_product );
	}

	/**
	 * Label for subscription plans dropdown.
	 *
	 * @since  3.0.0
	 *
	 * @param  WC_Product  $product
	 * @return string
	 */
	protected static function get_subscription_options_dropdown_label( $product ) {

		$label = __( 'Deliver:', 'woocommerce-all-products-for-subscriptions' );

		if ( ! $product->needs_shipping() ) {

			$label = __( 'Renew:', 'woocommerce-all-products-for-subscriptions' );

			if ( $product->is_type( array( 'bundle', 'composite' ) ) ) {
				$label = __( 'Choose a plan:', 'woocommerce-all-products-for-subscriptions' );
			}
		}

		return apply_filters( 'wcsatt_single_product_subscription_options_label', $label, $product );
	}

	/**
	 * Returns the subscription options text prompt.
	 *
	 * @since  3.0.0
	 *
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function get_subscription_options_prompt_text( $product ) {

		$text           = $product->get_meta( '_wcsatt_subscription_prompt', true );
		$allow_one_time = false === WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $product );

		/*
		 * Display default text when:
		 *
		 * - the "flat" layout is selected, OR
		 * - the "grouped" layout is selected and one-time purchases are not allowed.
		 */
		if ( empty( $text ) && ( 'flat' === self::get_subscription_options_layout( $product ) || false === $allow_one_time ) ) {

			$text = $allow_one_time ? __( 'Choose a purchase plan:', 'woocommerce-all-products-for-subscriptions' ) : __( 'Choose a subscription plan:', 'woocommerce-all-products-for-subscriptions' );
			$text = apply_filters( 'wcsatt_default_prompt_text', '<span class="wcsatt-options-prompt-text-label">' . $text . '</span>', $product );

		} elseif ( ! empty( $text ) ) {

			$clean_text    = do_shortcode( wp_kses_post( $text ) );
			$stripped_text = wp_strip_all_tags( $clean_text );

			if ( $clean_text === $stripped_text ) {
				$text = '<span class="wcsatt-options-prompt-text-label">' . $clean_text . '</span>';
			} else {
				$text = wpautop( $clean_text );
			}
		}

		return $text;
	}

	/**
	 * Returns the subscription options layout.
	 *
	 * @since  3.0.0
	 *
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function get_subscription_options_layout( $product ) {

		$layout = $product->get_meta( '_wcsatt_layout', true );

		return in_array( $layout, array( 'flat', 'grouped' ) ) ? $layout : 'flat';
	}

	/**
	 * Return add-to-cart button replacement text when choosing a subscription plan.
	 * Returns null if the text should not be modified.
	 *
	 * @since  3.0.0
	 *
	 * @param  WC_Product  $product
	 * @return string|null
	 */
	public static function get_subscription_options_button_text( $product ) {

		if ( WCS_ATT_Product_Schemes::has_subscription_schemes( $product ) ) {

			$button_text = null;

			if ( $product->is_type( 'bundle' ) && isset( $_GET[ 'update-bundle' ] ) ) {
				$updating_cart_key = wc_clean( $_GET[ 'update-bundle' ] );
				if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
					return $button_text;
				}
			} elseif ( $product->is_type( 'composite' ) && isset( $_GET[ 'update-composite' ] ) ) {
				$updating_cart_key = wc_clean( $_GET[ 'update-composite' ] );
				if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
					return $button_text;
				}
			}

			$button_text = get_option( WC_Subscriptions_Admin::$option_prefix . '_add_to_cart_button_text', __( 'Sign up', 'woocommerce-all-products-for-subscriptions' ) );

			return apply_filters( 'wcsatt_single_add_to_cart_text', $button_text, $product );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Filters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Replace plain variation price html with subscription options template.
	 * Subscription options are updated by the core variations script when a variation is selected.
	 *
	 * @param  array                 $variation_data
	 * @param  WC_Product_Variable   $variable_product
	 * @param  WC_Product_Variation  $variation_product
	 * @return array
	 */
	public static function add_subscription_options_to_variation_data( $variation_data, $variable_product, $variation_product ) {

		global $product;

		$is_current_product = false;

		if ( is_object( $product ) && is_a( $product, 'WC_Product' ) && ! doing_action( 'wc_ajax_woocommerce_show_composited_product' ) ) {
			$is_current_product = $variable_product->get_id() === $product->get_id();
		} elseif ( doing_action( 'wc_ajax_get_variation' ) ) {
			$is_current_product = true;
		}

		if ( ! $is_current_product ) {
			return $variation_data;
		}

		if ( $subscription_options_content = self::get_subscription_options_content( $variation_product, $variable_product ) ) {

			$modify_variation_data_price_html     = self::modify_variation_data_price_html( $variable_product );
			$subscription_schemes                 = WCS_ATT_Product_Schemes::get_subscription_schemes( $variation_product );
			$force_subscription                   = WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $variation_product );
			$price_filter_exists                  = WCS_ATT_Product_Schemes::price_filter_exists( $subscription_schemes );
			$is_single_scheme_forced_subscription = $force_subscription && sizeof( $subscription_schemes ) === 1;
			$has_equal_variation_prices           = '' === $variation_data[ 'price_html' ];

			/*
			 * When should we keep the existing price string?
			 *
			 * - When dealing with a single-scheme, force-subscription case (non-empty price string with subscription details).
			 * - When no scheme overrides the original variation price and all variation prices are equal and hidden (empty price string).
			 */
			if ( $is_single_scheme_forced_subscription || ( false === $price_filter_exists && $has_equal_variation_prices ) ) {

				if ( $modify_variation_data_price_html ) {
					$variation_data[ 'price_html' ] = $variation_data[ 'price_html' ] . $subscription_options_content;
				} else {
					$variation_data[ 'satt_price_html' ]   = $variation_data[ 'price_html' ];
					$variation_data[ 'satt_options_html' ] = $subscription_options_content;
				}

			} else {

				/*
				 * At this point, the variation price string will include subscription details because it has been filtered by 'WCS_ATT_Product_Prices::get_price_html'.
				 * We need to somehow generate the original, subscription-less price string.
				 */

				if ( $force_subscription ) {
					// To get the subscription-less price string, we need to enable the one-time option.
					WCS_ATT_Product_Schemes::set_forced_subscription_scheme( $variation_product, false );
				}

				// Back up the currently applied scheme key.
				$active_scheme_key = WCS_ATT_Product_Schemes::get_subscription_scheme( $variation_product );

				// Set the one-time scheme on the object.
				WCS_ATT_Product_Schemes::set_subscription_scheme( $variation_product, false );

				// Get the price string :)
				$price_html = $variation_product->get_price_html();

				$variation_data[ 'price_html' ] = '<span class="price">' . $price_html . '</span>';

				if ( $modify_variation_data_price_html ) {
					$variation_data[ 'price_html' ] .= $subscription_options_content;
				} else {
					$variation_data[ 'satt_price_html' ]   = $variation_data[ 'price_html' ];
					$variation_data[ 'satt_options_html' ] = $subscription_options_content;
				}

				// Un-do.
				WCS_ATT_Product_Schemes::set_subscription_scheme( $variation_product, $active_scheme_key );

				if ( $force_subscription ) {
					WCS_ATT_Product_Schemes::set_forced_subscription_scheme( $variation_product, true );
				}
			}
		}

		return $variation_data;
	}

	/**
	 * Displays single-product options for purchasing a product once or creating a subscription from it.
	 *
	 * @return void
	 */
	public static function show_subscription_options() {

		global $product;

		// Include the SATT script in footer.
		wp_enqueue_script( 'wcsatt-single-product' );

		echo self::get_subscription_options_content( $product );
	}

	/**
	 * Overrides the single-product add-to-cart button text with "Sign up".
	 *
	 * @since  1.1.1
	 *
	 * @param  string      $button_text
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function single_add_to_cart_text( $button_text, $product ) {

		if ( WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $product ) ) {

			$text        = self::get_subscription_options_button_text( $product );
			$button_text = $text ? $text : $button_text;
		}

		return $button_text;
	}

	/**
	 * Changes the shop add-to-cart button text when a product has subscription options.
	 *
	 * @since  2.0.0
	 *
	 * @param  string      $button_text
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function add_to_cart_text( $button_text, $product ) {

		if ( WCS_ATT_Product_Schemes::has_subscription_schemes( $product ) && $product->is_purchasable() && $product->is_in_stock() ) {

			$button_text = __( 'Select options', 'woocommerce' );
			$bypass      = false;

			if ( $product->is_type( 'bundle' ) && $product->requires_input() ) {
				$bypass = true;
			}

			if ( ! $bypass && WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $product ) ) {
				$button_text = get_option( WC_Subscriptions_Admin::$option_prefix . '_add_to_cart_button_text', __( 'Sign up', 'woocommerce-all-products-for-subscriptions' ) );
			}

			$button_text = apply_filters( 'wcsatt_add_to_cart_text', $button_text, $product );
		}

		return $button_text;
	}

	/**
	 * Changes the shop add-to-cart button action when a product has subscription options.
	 *
	 * @since  2.0.0
	 *
	 * @param  string      $url
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function add_to_cart_url( $url, $product ) {

		if ( WCS_ATT_Product_Schemes::has_subscription_schemes( $product ) && $product->is_purchasable() && $product->is_in_stock() ) {

			if ( ! WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $product ) ) {
				$url = $product->get_permalink();
			}

			$url = apply_filters( 'wcsatt_add_to_cart_url', $url, $product );
		}

		return $url;
	}

	/**
	 * Changes the shop add-to-cart button URL when a product has subscription options.
	 *
	 * @since  2.0.0
	 *
	 * @param  array       $supports
	 * @param  string      $feature
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function supports_ajax_add_to_cart( $supports, $feature, $product ) {

		if ( 'ajax_add_to_cart' === $feature ) {

			if ( WCS_ATT_Product_Schemes::has_subscription_schemes( $product ) ) {

				if ( ! WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $product ) ) {
					$supports = false;
				}

				$supports = apply_filters( 'wcsatt_product_supports_ajax_add_to_cart', $supports, $product );
			}
		}

		return $supports;
	}

	/**
	 * Add product page class if a product has subscription plans.
	 *
	 * @since  3.0.0
	 *
	 * @param  array       $classes
	 * @param  WC_Product  $product
	 */
	public static function add_product_class( $classes, $product ) {

		if ( WCS_ATT_Product_Schemes::has_subscription_schemes( $product ) ) {
			$classes[] = 'has-subscription-plans';
		}

		return $classes;
	}
}

WCS_ATT_Display_Product::init();
