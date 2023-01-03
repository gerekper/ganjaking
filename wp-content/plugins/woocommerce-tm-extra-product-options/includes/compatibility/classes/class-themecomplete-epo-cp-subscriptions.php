<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * WooCommerce Subscriptions
 * https://woocommerce.com/products/woocommerce-subscriptions/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_Subscriptions {

	/**
	 * Replacement name for Subscription sign up fee fields.
	 *
	 * @var string
	 */
	public $subscription_fee_name = 'tmsubfee_';

	/**
	 * Replacement class name for Subscription sign up fee fields.
	 *
	 * @var string
	 */
	public $subscription_fee_name_class = 'tmcp-sub-fee-field';

	/**
	 * Holds the total fee added by Subscription sign up fee fields.
	 *
	 * @var float
	 */
	public $subscription_tmfee = 0;

	/**
	 * Holds the variable subscription periods.
	 *
	 * @var array
	 */
	private $variations_subscription_period = [];

	/**
	 * Holds the variable subscription sign up fees.
	 *
	 * @var array
	 */
	private $variations_subscription_sign_up_fee = [];

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Subscriptions|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'plugins_loaded', [ $this, 'add_compatibility' ], 2 );

	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {

		if ( ! class_exists( 'WC_Subscriptions' ) ) {
			return;
		}

		// Enqueue scripts.
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 11 );

		// Add custom final total data in the JS template file.
		add_filter( 'wc_epo_after_js_final_totals', [ $this, 'wc_epo_after_js_final_totals' ], 10 );

		// Add to main JS script arguments.
		add_filter( 'wc_epo_script_args', [ $this, 'wc_epo_script_args' ], 10, 1 );
		// Add setting in main THEMECOMPLETE_EPO class.
		add_filter( 'wc_epo_get_settings', [ $this, 'wc_epo_get_settings' ], 10, 1 );
		add_filter( 'tm_epo_settings_headers', [ $this, 'tm_epo_settings_headers' ], 10, 1 );
		add_filter( 'tm_epo_settings_settings', [ $this, 'tm_epo_settings_settings' ], 10, 1 );

		// Add extra html data attributes to the totals template.
		add_action( 'wc_epo_template_tm_totals', [ $this, 'wc_epo_template_tm_totals' ], 10, 1 );

		// Add extra arguments to the totals template.
		add_filter( 'wc_epo_template_args_tm_totals', [ $this, 'wc_epo_template_args_tm_totals' ], 10, 2 );

		// Calculate subscription period and sign up fee per variation.
		add_action( 'wc_epo_print_price_fields_in_variation_loop', [ $this, 'wc_epo_print_price_fields_in_variation_loop' ], 10, 2 );

		// Add string setting.
		add_action( 'tm_epo_settings_string', [ $this, 'tm_epo_settings_string' ], 10, 1 );

		// Calculates the extra Subscription sign up fee.
		add_filter( 'woocommerce_subscriptions_product_sign_up_fee', [ $this, 'woocommerce_subscriptions_product_sign_up_fee' ], 10, 2 );

		// Flag subscription renewal.
		add_filter( 'woocommerce_subscriptions_renewal_order_items', [ $this, 'woocommerce_subscriptions_renewal_order_items' ], 10, 5 );
		add_filter( 'wcs_renewal_order_items', [ $this, 'woocommerce_subscriptions_renewal_order_items' ], 10, 1 );
		add_action( 'wcs_before_renewal_setup_cart_subscriptions', [ $this, 'wcs_before_renewal_setup_cart_subscriptions' ] );

		// Skip altering cart.
		add_filter( 'wc_epo_no_add_cart_item', [ $this, 'wc_epo_no_add_cart_item' ], 10, 1 );

		// Skip altering order again data.
		add_filter( 'wc_epo_no_order_again_cart_item_data', [ $this, 'wc_epo_no_order_again_cart_item_data' ], 10, 1 );

		// Skip altering order get_items.
		add_filter( 'wc_epo_no_order_get_items', [ $this, 'wc_epo_no_order_get_items' ], 10, 1 );

		// Add data to main element array in the builder.
		add_filter( 'wc_epo_builder_element_settings', [ $this, 'wc_epo_builder_element_settings' ], 10, 1 );

		// Initial value for the set_elements function.
		add_filter( 'wc_epo_set_elements_options', [ $this, 'wc_epo_set_elements_options' ], 10, 1 );

		// Alter price type.
		add_filter( 'tc_element_settings_override', [ $this, 'tc_element_settings_override' ], 10, 1 );
		add_filter( 'wc_epo_add_element_class', [ $this, 'wc_epo_add_element_class' ], 10, 3 );
		add_filter( 'wc_epo_builder_after_multiple_element_array', [ $this, 'wc_epo_builder_after_multiple_element_array' ], 10, 2 );
		add_filter( 'wc_epo_builder_element_array_in_loop_before', [ $this, 'wc_epo_builder_element_array_in_loop_before' ], 10, 5 );
		add_filter( 'wc_epo_builder_element_array_in_loop_after', [ $this, 'wc_epo_builder_element_array_in_loop_after' ], 10, 5 );
		add_action( 'wc_epo_builder_element_multiple_checkboxes_options', [ $this, 'wc_epo_builder_element_multiple_checkboxes_options' ], 10, 1 );
		add_filter( 'wc_epo_builder_element_multiple_checkboxes_options_js_object', [ $this, 'wc_epo_builder_element_multiple_checkboxes_options_js_object' ], 10, 5 );
		add_filter( 'wc_epo_obvalues', [ $this, 'wc_epo_obvalues' ], 10, 6 );
		add_filter( 'wc_epo_cbvalues', [ $this, 'wc_epo_cbvalues' ], 10, 7 );

		// Skip cart item loop.
		add_filter( 'wc_epo_add_cart_item_loop', [ $this, 'wc_epo_add_cart_item_loop' ], 10, 2 );

		// Alter the cart from session.
		add_filter( 'wc_epo_get_cart_item_from_session', [ $this, 'wc_epo_get_cart_item_from_session' ], 10, 2 );

		// Edit cart link product types.
		add_filter( 'wc_epo_can_be_edited_product_type', [ $this, 'wc_epo_can_be_edited_product_type' ], 10, 1 );

		// Pre-init cart item.
		add_filter( 'wc_epo_add_cart_item_data_helper', [ $this, 'wc_epo_add_cart_item_data_helper' ], 10, 1 );

		// Add field data to cart (subscription fees).
		add_filter( 'wc_epo_add_cart_item_data_loop', [ $this, 'wc_epo_add_cart_item_data_loop' ], 10, 11 );

		// No options in cart check.
		add_filter( 'wc_epo_no_epo_in_cart', [ $this, 'wc_epo_no_epo_in_cart' ], 10, 2 );

		// Field pre validation.
		add_filter( 'wc_epo_validate_field_field_names', [ $this, 'wc_epo_validate_field_field_names' ], 10, 5 );

		// Validate checkbox.
		add_filter( 'wc_epo_validate_checkbox', [ $this, 'wc_epo_validate_checkbox' ], 10, 2 );

		// Validate radio button.
		add_filter( 'wc_epo_validate_radiobutton', [ $this, 'wc_epo_validate_radiobutton' ], 10, 3 );

		// Alternative radio button check.
		add_filter( 'wc_epo_alt_validate_radiobutton', [ $this, 'wc_epo_alt_validate_radiobutton' ], 10, 3 );

		// Alter HTML name.
		add_filter( 'wc_epo_name_inc', [ $this, 'wc_epo_name_inc' ], 10, 6 );

		// Alter fieldtype key of the element in the template arguments.
		add_filter( 'wc_epo_display_template_args', [ $this, 'wc_epo_display_template_args' ], 10, 5 );

		// Gets the stored cart data for the order again functionality.
		add_filter( 'wc_epo_woocommerce_order_again_cart_item_data', [ $this, 'wc_epo_woocommerce_order_again_cart_item_data' ], 10, 2 );

		// Check for if the cart key for subscriptions exists.
		add_filter( 'wc_epo_woocommerce_order_again_cart_item_data_has_epo', [ $this, 'wc_epo_woocommerce_order_again_cart_item_data_has_epo' ], 10, 2 );

		// Adds meta data to the order.
		add_action( 'wc_epo_order_item_meta', [ $this, 'wc_epo_order_item_meta' ], 10, 3 );

		// Alter global epo table.
		add_filter( 'global_epos_fill_builder_display', [ $this, 'global_epos_fill_builder_display' ], 10, 9 );

		// Check for subscription fee type when displaying options on admin Order page.
		add_filter( 'wc_epo_html_tm_epo_order_item_is_other_fee', [ $this, 'wc_epo_html_tm_epo_order_item_is_other_fee' ], 10, 2 );

		// Alter the array of data for a variation. Used in the add to cart form.
		add_filter( 'woocommerce_available_variation', [ $this, 'woocommerce_available_variation' ], 10, 3 );

		// Add the variable subscription product type to plugin checks.
		add_filter( 'wc_epo_variable_product_type', [ $this, 'wc_epo_variable_product_type' ], 10, 1 );

		// Flag renewals.
		add_filter( 'woocommerce_order_again_cart_item_data', [ $this, 'woocommerce_order_again_cart_item_data' ], 50, 3 );
	}

	/**
	 * Add plugin setting (header)
	 *
	 * @param array $headers Array of settings.
	 * @since 1.0
	 */
	public function tm_epo_settings_headers( $headers = [] ) {
		$headers['subscriptions'] = [ 'tcfa tcfa-arrows-spin', esc_html__( 'WooCommerce Subscriptions', 'woocommerce-tm-extra-product-options' ) ];

		return $headers;
	}

	/**
	 * Add plugin setting (setting)
	 *
	 * @param array $settings Array of settings.
	 * @since 1.0
	 */
	public function tm_epo_settings_settings( $settings = [] ) {
		$label                     = esc_html__( 'WooCommerce Subscriptions', 'woocommerce-tm-extra-product-options' );
		$settings['subscriptions'] = [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			],
			[
				'title'   => esc_html__( 'Include addons on order again', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will add the saved addons to the subscription when using the order again functionality manually.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_order_again_include_addons',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],
		];

		return $settings;
	}

	/**
	 * Flag renewals
	 *
	 * @param array  $cart_item_meta The cart item meta.
	 * @param array  $item The order item.
	 * @param object $order The order object.
	 * @since 6.1
	 */
	public function woocommerce_order_again_cart_item_data( $cart_item_meta, $item, $order ) {
		if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_order_again_include_addons ) {
			if ( ! defined( 'THEMECOMPLETE_IS_SUBSCRIPTIONS_RENEWAL' ) ) {
				define( 'THEMECOMPLETE_IS_SUBSCRIPTIONS_RENEWAL', 1 );
			}
		}
		return $cart_item_meta;
	}

	/**
	 * Add the variable subscription product type to plugin checks
	 *
	 * @param array $type Array of product types.
	 * @since 5.0.12.11
	 */
	public function wc_epo_variable_product_type( $type ) {

		$type[] = 'variable-subscription';

		return $type;

	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0
	 */
	public function wp_enqueue_scripts() {

		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-subscriptions', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-subscriptions.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
		}

	}

	/**
	 * Add custom final total data in the JS template file
	 *
	 * @since 1.0
	 */
	public function wc_epo_after_js_final_totals() {

		?>
		<# if (data.show_sign_up_fee==true){ #><?php do_action( 'wc_epo_template_before_sign_up_fee' ); ?>
		<dt class="tm-subscription-fee">{{{ data.sign_up_fee }}}</dt>
		<dd class="tm-subscription-fee">
			<span class="price amount subscription-fee">{{{ data.formatted_subscription_fee_total }}}</span>
		</dd><?php do_action( 'wc_epo_template_after_sign_up_fee' ); ?>
		<# } #>
		<?php

	}

	/**
	 * Add to main JS script arguments
	 *
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	public function wc_epo_script_args( $args ) {

		$args['i18n_subscription_sign_up_fee'] = ( ! empty( THEMECOMPLETE_EPO()->tm_epo_subscription_fee_text ) ) ? THEMECOMPLETE_EPO()->tm_epo_subscription_fee_text : esc_html__( 'Sign up fee', 'woocommerce-tm-extra-product-options' );
		$args['i18n_sign_up_fee']              = ( ! empty( THEMECOMPLETE_EPO()->tm_epo_signup_fee_text ) ) ? THEMECOMPLETE_EPO()->tm_epo_signup_fee_text : esc_html__( ' sign-up fee', 'woocommerce-tm-extra-product-options' );
		$args['i18n_and_a']                    = esc_html__( ' and a ', 'woocommerce-tm-extra-product-options' );

		return $args;

	}

	/**
	 * Add setting in main THEMECOMPLETE_EPO class
	 *
	 * @param array $settings Array of settings.
	 * @since 1.0
	 */
	public function wc_epo_get_settings( $settings = [] ) {

		if ( class_exists( 'WC_Subscriptions' ) ) {
			$settings['tm_epo_subscription_fee_text']      = '';
			$settings['tm_epo_signup_fee_text']            = '';
			$settings['tm_epo_order_again_include_addons'] = 'yes';
		}

		return $settings;

	}

	/**
	 * Add extra html data attributes to the totals template
	 *
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	public function wc_epo_template_tm_totals( $args ) {

		$data                                  = [];
		$data['data-is-subscription']          = $args['is_subscription'];
		$data['data-subscription-sign-up-fee'] = $args['subscription_sign_up_fee'];
		$data['data-variations-subscription-sign-up-fee'] = esc_html( wp_json_encode( (array) $args['variations_subscription_sign_up_fee'] ) );
		$data['data-subscription-period']                 = esc_html( wp_json_encode( (array) $args['subscription_period'] ) );
		$data['data-variations-subscription-period']      = esc_html( wp_json_encode( (array) $args['variations_subscription_period'] ) );
		$data['data-ln-and-a']                            = __( ' and a ', 'woocommerce-tm-extra-product-options' );
		$data['data-ln-signup-fee']                       = __( ' sign-up fee', 'woocommerce-tm-extra-product-options' );

		THEMECOMPLETE_EPO_HTML()->create_attribute_list( $data );

	}

	/**
	 * Add extra arguments to the totals template
	 *
	 * @param array  $args Array of arguments.
	 * @param object $product The product object.
	 * @since 1.0
	 */
	public function wc_epo_template_args_tm_totals( $args, $product ) {

		$is_subscription     = false;
		$subscription_period = '';

		$subscription_sign_up_fee = 0;
		if ( class_exists( 'WC_Subscriptions_Product' ) ) {
			if ( WC_Subscriptions_Product::is_subscription( $product ) ) {
				$is_subscription = true;

				if ( function_exists( 'wcs_get_subscription_period_strings' ) ) {
					$billing_interval    = (int) WC_Subscriptions_Product::get_interval( $product );
					$billing_period      = WC_Subscriptions_Product::get_period( $product );
					$subscription_period = sprintf(
						// translators: 1$: recurring amount, 2$: subscription period (e.g. "month" or "3 months") (e.g. "$15 / month" or "$15 every 2nd month").
						_n( '%1$s / %2$s', '%1$s every %2$s', $billing_interval, 'woocommerce-tm-extra-product-options' ),
						'',
						wcs_get_subscription_period_strings( $billing_interval, $billing_period )
					);
				} else {
					$subscription_period = WC_Subscriptions_Product::get_price_string(
						$product,
						[
							'subscription_price' => false,
							'sign_up_fee'        => false,
							'trial_length'       => false,
							'price'              => null,
						]
					);
				}

				$subscription_sign_up_fee = WC_Subscriptions_Product::get_sign_up_fee( $product );
			}
		}

		$args['is_subscription']                     = $is_subscription;
		$args['subscription_sign_up_fee']            = $subscription_sign_up_fee;
		$args['variations_subscription_sign_up_fee'] = $this->variations_subscription_sign_up_fee;
		$args['subscription_period']                 = $subscription_period;
		$args['variations_subscription_period']      = $this->variations_subscription_period;

		return $args;
	}

	/**
	 * Calculate subscription period and sign up fee per variation
	 *
	 * @param object  $variation Variation object.
	 * @param integer $child_id Variation id.
	 * @since 1.0
	 */
	public function wc_epo_print_price_fields_in_variation_loop( $variation, $child_id ) {

		if ( class_exists( 'WC_Subscriptions_Product' ) ) {

			$this->variations_subscription_period[ $child_id ] = WC_Subscriptions_Product::get_price_string(
				$variation,
				[
					'subscription_price' => false,
					'sign_up_fee'        => false,
					'trial_length'       => false,
					'price'              => null,
				]
			);
			if ( is_callable( [ 'WC_Subscriptions_Product', 'get_sign_up_fee' ] ) ) {
				$this->variations_subscription_sign_up_fee[ $child_id ] = WC_Subscriptions_Product::get_sign_up_fee( $variation );
			} else {
				$this->variations_subscription_sign_up_fee[ $child_id ] = $variation->subscription_sign_up_fee;
			}
		} else {
			$this->variations_subscription_period[ $child_id ]      = '';
			$this->variations_subscription_sign_up_fee[ $child_id ] = '';
		}

	}

	/**
	 * Add string setting
	 *
	 * @param array $settings Array of settings.
	 * @since 1.0
	 */
	public function tm_epo_settings_string( $settings ) {

		$inserted = [
			[
				'title'   => esc_html__( 'Subscription sign up fee text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter the Subscription sign up fee text or leave blank for default.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_subscription_fee_text',
				'default' => '',
				'type'    => 'text',
			],
			[
				'title'   => esc_html__( 'Sign up fee text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter the Sign up fee text or leave blank for default.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_signup_fee_text',
				'default' => '',
				'type'    => 'text',
			],
		];
		array_splice( $settings, count( $settings ) - 1, 0, $inserted );

		return $settings;

	}

	/**
	 * Flag subscription renewal
	 *
	 * @param array $order_items Order items.
	 * @since 1.0
	 */
	public function woocommerce_subscriptions_renewal_order_items( $order_items ) {
		if ( ! defined( 'THEMECOMPLETE_IS_SUBSCRIPTIONS_RENEWAL' ) ) {
			define( 'THEMECOMPLETE_IS_SUBSCRIPTIONS_RENEWAL', 1 );
		}

		return $order_items;
	}

	/**
	 * Flag subscription renewal
	 *
	 * @since 4.9.7
	 */
	public function wcs_before_renewal_setup_cart_subscriptions() {
		if ( ! defined( 'THEMECOMPLETE_IS_SUBSCRIPTIONS_RENEWAL' ) ) {
			define( 'THEMECOMPLETE_IS_SUBSCRIPTIONS_RENEWAL', 1 );
		}
	}

	/**
	 * Calculates the extra Subscription sign up fee
	 *
	 * @param int|string $subscription_sign_up_fee The value of the sign-up fee,
	 *                   or 0 if the product is not a subscription or the
	 *                   subscription has no sign-up fee.
	 * @param mixed      $product A WC_Product object or product ID.
	 * @since 1.0
	 */
	public function woocommerce_subscriptions_product_sign_up_fee( $subscription_sign_up_fee = '', $product = '' ) {

		$options_fee = 0;

		if ( WC()->cart ) {
			$cart_contents = WC()->cart->cart_contents;
			if ( $cart_contents && ! is_product() && WC()->cart ) {
				foreach ( $cart_contents as $cart_key => $cart_item ) {
					foreach ( $cart_item as $key => $data ) {
						if ( 'tmsubscriptionfee' === $key ) {
							$options_fee = $data;
						}
					}
				}
				$subscription_sign_up_fee += $options_fee;
			}
		}

		return $subscription_sign_up_fee;

	}

	/**
	 * Add data to main element array in the builder
	 *
	 * [subscription_fee_type] = can set subscription fees
	 *
	 * @param array $data Array of data.
	 * @since 4.8
	 */
	public function wc_epo_builder_element_settings( $data = [] ) {

		foreach ( $data as $key => $value ) {
			switch ( $key ) {

				case 'date':
				case 'time':
				case 'range':
				case 'color':
				case 'textarea':
				case 'textfield':
				case 'upload':
					$data[ $key ]->subscription_fee_type = 'single';
					break;

				case 'selectbox':
				case 'radiobuttons':
				case 'checkboxes':
					$data[ $key ]->subscription_fee_type = 'multiple';
					break;

				case 'header':
				case 'divider':
				case 'variations':
				default:
					$data[ $key ]->subscription_fee_type = '';
					break;
			}
		}

		return $data;

	}

	/**
	 * Initial value for the set_elements function
	 *
	 * @param array $options Array of settings.
	 * @see   THEMECOMPLETE_EPO_BUILDER_Base->set_elements
	 * @since 4.8
	 */
	public function wc_epo_set_elements_options( $options = [] ) {

		if ( ! isset( $options['subscription_fee_type'] ) ) {
			$options['subscription_fee_type'] = '';
		}

		return $options;

	}

	/**
	 * Alter price type
	 *
	 * @param array $settings Array of settings.
	 * @since 5.0
	 */
	public function tc_element_settings_override( $settings = [] ) {
		$fee = array_search( 'fee', $settings, true );
		if ( false !== $fee && null !== $fee ) {
			array_splice( $settings, $fee, 0, 'subscriptionfee' );
		}

		return $settings;

	}

	/**
	 * Alter price type
	 *
	 * @param class  $class Class to use.
	 * @param string $key Setting key.
	 * @param mixed  $value Setting value.
	 * @since 5.0
	 */
	public function wc_epo_add_element_class( $class, $key, $value ) {

		if ( 'subscriptionfee' === $value ) {
			$class = $this;
		}

		return $class;

	}

	/**
	 * Subscription sign up fee setting
	 *
	 * @param string $name Element name.
	 * @param array  $args Array of arguments.
	 * @since 5.0
	 */
	public function add_setting_subscriptionfee( $name = '', $args = [] ) {

		return array_merge(
			[
				'id'          => $name . '_subscriptionfee',
				'wpmldisable' => 1,
				'default'     => '',
				'type'        => 'checkbox',
				'tags'        => [
					'class' => 'c',
					'id'    => 'builder_' . $name . '_subscriptionfee',
					'name'  => 'tm_meta[tmfbuilder][' . $name . '_subscriptionfee][]',
					'value' => '1',
				],
				'label'       => esc_html__( 'Subscription sign up fee', 'woocommerce-tm-extra-product-options' ),
			],
			$args
		);

	}

	/**
	 * Subscription sign up fee setting for checkboxes
	 *
	 * @param array  $o Array of settings.
	 * @param string $name Element name.
	 * @since 5.0
	 */
	public function wc_epo_builder_after_multiple_element_array( $o = [], $name = '' ) {

		$o['subscriptionfee'] = [
			'id'      => $name . '_subscriptionfee',
			'default' => '',
			'type'    => 'checkbox',
			'nodiv'   => 1,
			'tags'    => [
				'class' => 'c',
				'id'    => $name . '_subscriptionfee',
				'name'  => $name . '_subscriptionfee',
				'value' => '1',
			],
		];

		return $o;

	}

	/**
	 * Setup Subscription sign up fee setting for checkboxes in loop
	 *
	 * @param array       $options Array of options.
	 * @param array|null  $o Array of options.
	 * @param int|null    $ar Option counter.
	 * @param string|null $name Option name.
	 * @param int|null    $counter Counter.
	 * @since 5.0
	 */
	public function wc_epo_builder_element_array_in_loop_before( $options = [], $o = null, $ar = null, $name = null, $counter = null ) {

		if ( isset( $options['price_type'][ $ar ] ) && 'subscriptionfee' === $options['price_type'][ $ar ] ) {
			$options['price_type'][ $ar ]      = '';
			$options['subscriptionfee'][ $ar ] = '1';
		}

		return $options;

	}

	/**
	 * Setup Subscription sign up fee setting for checkboxes in loop
	 *
	 * @param array       $o Array of options.
	 * @param array|null  $options Array of options.
	 * @param int|null    $ar Option counter.
	 * @param string|null $name Option name.
	 * @param int|null    $counter Counter.
	 * @since 5.0
	 */
	public function wc_epo_builder_element_array_in_loop_after( $o = [], $options = null, $ar = null, $name = null, $counter = null ) {

		if ( ! isset( $options['subscriptionfee'][ $ar ] ) ) {
			$options['subscriptionfee'][ $ar ] = '';
		}

		$o['subscriptionfee']['default']      = $options['subscriptionfee'][ $ar ];// subscriptionfee.
		$o['subscriptionfee']['tags']['name'] = 'tm_meta[tmfbuilder][' . $name . '_subscriptionfee][' . ( is_null( $counter ) ? 0 : $counter ) . '][]';
		$o['subscriptionfee']['tags']['id']   = str_replace( [ '[', ']' ], '', $o['subscriptionfee']['tags']['name'] ) . '_' . $ar;

		return $o;

	}

	/**
	 * Setup Subscription sign up fee setting for checkboxes in loop
	 *
	 * @param array $o Array of options.
	 * @since 5.0
	 */
	public function wc_epo_builder_element_multiple_checkboxes_options( $o = [] ) {

		echo "<div class='tc-cell tc-col-12 tm_cell_subscriptionfee'><span class='tm-inline-label bsbb'>" . esc_html__( 'Subscription sign up fee', 'woocommerce-tm-extra-product-options' ) . '</span>';
		THEMECOMPLETE_EPO_HTML()->create_field( $o['subscriptionfee'], 1 );
		echo '</div>';

	}

	/**
	 * Setup Subscription sign up fee setting for checkboxes in loop
	 *
	 * @param array       $js_object JS array to of settings.
	 * @param int|null    $d_counter Counter.
	 * @param array|null  $o Array of options.
	 * @param string|null $name Option name.
	 * @param int|null    $counter Counter.
	 * @since 5.0
	 */
	public function wc_epo_builder_element_multiple_checkboxes_options_js_object( $js_object = [], $d_counter = null, $o = null, $name = null, $counter = null ) {

		$js_object[ $d_counter ][] = [
			'id'      => $name . '_subscriptionfee',
			'default' => (string) $o['subscriptionfee']['default'],
			'checked' => (string) $o['subscriptionfee']['default'] === (string) $o['subscriptionfee']['tags']['value'],
			'type'    => 'checkbox',
			'tags'    => [
				'name'  => 'tm_meta[tmfbuilder][' . $name . '_subscriptionfee][' . ( is_null( $counter ) ? 0 : $counter ) . '][]',
				'value' => $o['subscriptionfee']['tags']['value'],
			],
		];

		return $js_object;

	}

	/**
	 * Setup Subscription sign up fee setting for checkboxes in loop
	 *
	 * @param array      $obvalues Array of arguments.
	 * @param array|null $builder The element builder.
	 * @param array|null $value Array of settings.
	 * @param array|null $current_builder The current element builder.
	 * @param array|null $_titles_base The titles of the elements.
	 * @param mixed|null $_option_key The index of element id in internal array.
	 * @since 5.0
	 */
	public function wc_epo_obvalues( $obvalues = [], $builder = null, $value = null, $current_builder = null, $_titles_base = null, $_option_key = null ) {

		$_subscriptionfee_base = isset( $builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ] )
			? $builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ]
			: null;

		if ( is_null( $_subscriptionfee_base ) ) {
			$_subscriptionfee_base = array_map( [ $this, 'clear_array_values' ], $_titles_base );
		}

		if ( ! isset( $_subscriptionfee_base[ $_option_key ] ) ) {
			$_subscriptionfee_base[ $_option_key ] = array_map( [ $this, 'clear_array_values' ], $_titles_base[ $_option_key ] );
		}

		$obvalues['subscriptionfee'] = $_subscriptionfee_base[ $_option_key ];

		return $obvalues;

	}

	/**
	 * Clear array values
	 *
	 * @param mixed $val Value to clear.
	 * @since  1.0
	 * @access private
	 */
	private function clear_array_values( $val ) {
		if ( is_array( $val ) ) {
			return array_map( [ $this, 'clear_array_values' ], $val );
		} else {
			return '';
		}
	}

	/**
	 * Setup Subscription sign up fee setting for checkboxes in loop
	 *
	 * @param array      $cbvalues Array of arguments.
	 * @param array|null $builder The element builder.
	 * @param array|null $value Array of settings.
	 * @param array|null $current_builder The current element builder.
	 * @param array|null $_titles_base The titles of the elements.
	 * @param mixed|null $_option_key The index of element id in internal array.
	 * @param mixed|null $option_key The current key for the $_titles_base array.
	 * @since 5.0
	 */
	public function wc_epo_cbvalues( $cbvalues = [], $builder = null, $value = null, $current_builder = null, $_titles_base = null, $_option_key = null, $option_key = null ) {

		$_subscriptionfee_base = isset( $builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ] )
			? $builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ]
			: null;
		$_subscriptionfee      = isset( $builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ] )
			? ( isset( $current_builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ] )
				? $current_builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ]
				: $builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ] )
			: null;

		if ( is_null( $_subscriptionfee_base ) ) {
			$_subscriptionfee_base = array_map( [ $this, 'clear_array_values' ], $_titles_base );
		}
		if ( is_null( $_subscriptionfee ) ) {
			$_subscriptionfee = $_subscriptionfee_base;
		}

		if ( ! isset( $_subscriptionfee_base[ $_option_key ] ) ) {
			$_subscriptionfee_base[ $_option_key ] = array_map( [ $this, 'clear_array_values' ], $_titles_base[ $_option_key ] );
		}

		if ( ! isset( $_subscriptionfee[ $_option_key ] ) ) {
			$_subscriptionfee[ $_option_key ] = array_map( [ $this, 'clear_array_values' ], $_titles_base[ $_option_key ] );
		}

		$cbvalues['subscriptionfee'] = THEMECOMPLETE_EPO_HELPER()->build_array( $_subscriptionfee[ $_option_key ], $_subscriptionfee_base[ $option_key ] );

		return $cbvalues;

	}

	/**
	 * Skip altering order get_items
	 *
	 * @param boolean $ret true or false.
	 * @since 4.8
	 */
	public function wc_epo_no_order_get_items( $ret = false ) {

		if ( defined( 'THEMECOMPLETE_IS_SUBSCRIPTIONS_RENEWAL' ) ) {
			$ret = true;
		}

		return $ret;

	}

	/**
	 * Skip altering add to cart
	 *
	 * @param boolean $ret true or false.
	 * @since 4.9.7
	 */
	public function wc_epo_no_add_cart_item( $ret = false ) {

		if ( defined( 'THEMECOMPLETE_IS_SUBSCRIPTIONS_RENEWAL' ) ) {
			$ret = true;
		}

		return $ret;

	}

	/**
	 * Skip altering order again
	 *
	 * @param boolean $ret true or false.
	 * @since 4.9.7
	 */
	public function wc_epo_no_order_again_cart_item_data( $ret = false ) {

		if ( defined( 'THEMECOMPLETE_IS_SUBSCRIPTIONS_RENEWAL' ) ) {
			$ret = true;
		}

		return $ret;

	}

	/**
	 * Skip cart item loop
	 *
	 * @param boolean $ret true or false.
	 * @param array   $tmcp Element array.
	 * @since 4.8
	 */
	public function wc_epo_add_cart_item_loop( $ret = false, $tmcp = [] ) {

		if ( isset( $tmcp['subscription_fees'] ) ) {
			$ret = true;
		}

		return $ret;

	}

	/**
	 * Alter the cart from session
	 *
	 * @param array $cart_item The cart item.
	 * @param array $values Array values.
	 * @since 4.8
	 */
	public function wc_epo_get_cart_item_from_session( $cart_item = [], $values = [] ) {

		if ( ! empty( $values['tmsubscriptionfee'] ) ) {
			$cart_item['tmsubscriptionfee'] = $values['tmsubscriptionfee'];
		}

		return $cart_item;

	}

	/**
	 * Edit cart link product types
	 *
	 * @param array $types Array of product types.
	 * @since 4.8
	 */
	public function wc_epo_can_be_edited_product_type( $types = [] ) {

		$types[] = 'subscription';
		$types[] = 'variable-subscription';

		return $types;

	}

	/**
	 * Pre-init cart item
	 *
	 * @param array $cart_item_meta Cart item meta data.
	 * @since 4.8
	 */
	public function wc_epo_add_cart_item_data_helper( $cart_item_meta = [] ) {

		if ( empty( $cart_item_meta['tmsubscriptionfee'] ) ) {
			$cart_item_meta['tmsubscriptionfee'] = 0;
		}

		return $cart_item_meta;

	}

	/**
	 * Add field data to cart (subscription fees)
	 *
	 * @param array        $cart_item_meta Cart item meta data.
	 * @param class|null   $field_obj Element field class.
	 * @param array        $tmcp_post_fields Array of posted fields.
	 * @param array        $element The element array.
	 * @param integer      $field_loop The field loop index.
	 * @param string       $form_prefix The form prefix.
	 * @param integer|null $product_id The product id.
	 * @param bool|null    $per_product_pricing If the product has pricing, true or false.
	 * @param float|null   $cpf_product_price The product price.
	 * @param integer|null $variation_id The variation id.
	 * @param array        $post_data The posted data.
	 * @since 4.8
	 */
	public function wc_epo_add_cart_item_data_loop( $cart_item_meta = [], $field_obj = null, $tmcp_post_fields = [], $element = [], $field_loop = 0, $form_prefix = '', $product_id = null, $per_product_pricing = null, $cpf_product_price = null, $variation_id = null, $post_data = [] ) {

		$current_tmcp_post_fields = array_intersect_key(
			$tmcp_post_fields,
			array_flip(
				THEMECOMPLETE_EPO()->get_post_names(
					$element['options'],
					$element['type'],
					$field_loop,
					$form_prefix,
					$this->subscription_fee_name,
					$element
				)
			)
		);

		$holder_subscription_fees = THEMECOMPLETE_EPO()->tm_builder_elements[ $field_obj->element['type'] ]->subscription_fee_type;
		foreach ( $current_tmcp_post_fields as $attribute => $key ) {

			if ( ! empty( $holder_subscription_fees ) ) {
				if ( isset( $tmcp_post_fields[ $attribute . '_quantity' ] ) ) {
					if ( empty( $tmcp_post_fields[ $attribute . '_quantity' ] ) ) {
						continue;
					}
				}
				$meta = false;
				if ( $field_obj->is_setup() ) {
					$field_obj->attribute = $attribute;
					$field_obj->key       = $key;

					// Add field data to cart (subscription fees single).
					if ( 'single' === $holder_subscription_fees ) {
						if ( isset( $field_obj->key ) && '' !== $field_obj->key ) {
							$_price                   = THEMECOMPLETE_EPO()->calculate_price( $field_obj->post_data, $field_obj->element, $field_obj->key, $field_obj->attribute, 1, $field_obj->key_id, $field_obj->keyvalue_id, $field_obj->per_product_pricing, $field_obj->cpf_product_price, $field_obj->variation_id );
							$this->subscription_tmfee = $this->subscription_tmfee + (float) $_price;
							$meta                     = [
								'mode'                => 'builder',
								'cssclass'            => $field_obj->element['class'],
								'include_tax_for_fee_price_type' => $field_obj->element['include_tax_for_fee_price_type'],
								'tax_class_for_fee_price_type' => $field_obj->element['tax_class_for_fee_price_type'],
								'hidelabelincart'     => $field_obj->element['hide_element_label_in_cart'],
								'hidevalueincart'     => $field_obj->element['hide_element_value_in_cart'],
								'hidelabelinorder'    => $field_obj->element['hide_element_label_in_order'],
								'hidevalueinorder'    => $field_obj->element['hide_element_value_in_order'],
								'element'             => $field_obj->order_saved_element,
								'name'                => $field_obj->element['label'],
								'value'               => $field_obj->key,
								'price'               => 0,
								'section'             => $field_obj->element['uniqid'],
								'section_label'       => $field_obj->element['label'],
								'percentcurrenttotal' => 0,
								'fixedcurrenttotal'   => 0,
								'currencies'          => isset( $field_obj->element['currencies'] ) ? $field_obj->element['currencies'] : [],
								'price_per_currency'  => $field_obj->fill_currencies( 1 ),
								'quantity'            => 1,

								'subscription_fees'   => 'single',
							];

						}

						// Add field data to cart (subscription fees multiple).
					} elseif ( 'multiple' === $holder_subscription_fees ) {
						// select box placeholder check.
						if ( isset( $field_obj->element['options'][ esc_attr( $field_obj->key ) ] ) ) {
							$_price = THEMECOMPLETE_EPO()->calculate_price( $field_obj->post_data, $field_obj->element, $field_obj->key, $field_obj->attribute, 1, $field_obj->key_id, $field_obj->keyvalue_id, $field_obj->per_product_pricing, $field_obj->cpf_product_price, $field_obj->variation_id );

							$use_images = ( 'image' === $field_obj->element['replacement_mode'] ) ? ( 'center' === $field_obj->element['swatch_position'] ? 'images' : $field_obj->element['swatch_position'] ) : '';

							if ( $use_images ) {
								$_image_key = array_search( $field_obj->key, $field_obj->element['option_values'] ); // phpcs:ignore WordPress.PHP.StrictInArray
								if ( null === $_image_key || false === $_image_key ) {
									$_image_key = false;
								}
							} else {
								$_image_key = false;
							}

							$use_colors = ( 'color' === $field_obj->element['replacement_mode'] ) ? ( 'center' === $field_obj->element['swatch_position'] ? 'color' : $field_obj->element['swatch_position'] ) : '';
							if ( $use_colors ) {
								$_color_key = array_search( $field_obj->key, $field_obj->element['option_values'] ); // phpcs:ignore WordPress.PHP.StrictInArray
								if ( null === $_color_key || false === $_color_key ) {
									$_color_key = false;
								}
							} else {
								$_color_key = false;
							}

							$this->subscription_tmfee = $this->subscription_tmfee + (float) $_price;

							$meta = [
								'mode'                  => 'builder',
								'cssclass'              => $field_obj->element['class'],
								'include_tax_for_fee_price_type' => $field_obj->element['include_tax_for_fee_price_type'],
								'tax_class_for_fee_price_type' => $field_obj->element['tax_class_for_fee_price_type'],
								'hidelabelincart'       => $field_obj->element['hide_element_label_in_cart'],
								'hidevalueincart'       => $field_obj->element['hide_element_value_in_cart'],
								'hidelabelinorder'      => $field_obj->element['hide_element_label_in_order'],
								'hidevalueinorder'      => $field_obj->element['hide_element_value_in_order'],
								'element'               => $field_obj->order_saved_element,
								'name'                  => $field_obj->element['label'],
								'value'                 => $field_obj->element['options'][ esc_attr( $field_obj->key ) ],
								'price'                 => 0,
								'section'               => $field_obj->element['uniqid'],
								'section_label'         => $field_obj->element['label'],
								'percentcurrenttotal'   => 0,
								'fixedcurrenttotal'     => 0,
								'currencies'            => isset( $field_obj->element['currencies'] ) ? $field_obj->element['currencies'] : [],
								'price_per_currency'    => $field_obj->fill_currencies( 1 ),
								'quantity'              => 1,

								'subscription_fees'     => 'multiple',
								'multiple'              => '1',
								'key'                   => esc_attr( $field_obj->key ),
								'use_images'            => $use_images,
								'use_colors'            => $use_colors,
								'color'                 => ( false !== $_color_key && isset( $field_obj->element['color'][ $_color_key ] ) ) ? ( empty( $field_obj->element['color'][ $_color_key ] ) ? 'transparent' : $field_obj->element['color'][ $_color_key ] ) : '',
								'changes_product_image' => ! empty( $field_obj->element['changes_product_image'] ) ? $field_obj->element['changes_product_image'] : '',
								'images'                => ( false !== $_image_key && isset( $field_obj->element['images'][ $_image_key ] ) ) ? $field_obj->element['images'][ $_image_key ] : '',
								'imagesc'               => ( false !== $_image_key && isset( $field_obj->element['imagesc'][ $_image_key ] ) && ! empty( $field_obj->element['imagesc'][ $_image_key ] ) ) ? $field_obj->element['imagesc'][ $_image_key ] : '',
								'imagesp'               => ( false !== $_image_key && isset( $field_obj->element['imagesp'][ $_image_key ] ) ) ? $field_obj->element['imagesp'][ $_image_key ] : '',
							];

						}
					}
				}

				if ( is_array( $meta ) ) {
					if ( isset( $meta[0] ) && is_array( $meta[0] ) ) {
						foreach ( $meta as $k => $value ) {
							$cart_item_meta['tmcartepo'][]                = $value;
							$cart_item_meta['tmdata']['tmcartepo_data'][] = [
								'key'       => $key,
								'attribute' => $attribute,
							];
						}
					} else {
						$cart_item_meta['tmcartepo'][]                = $meta;
						$cart_item_meta['tmdata']['tmcartepo_data'][] = [
							'key'       => $key,
							'attribute' => $attribute,
						];
					}
				}
			}
			$cart_item_meta['tmsubscriptionfee'] = $this->subscription_tmfee;
		}

		return $cart_item_meta;

	}

	/**
	 * No options in cart check
	 *
	 * @param boolean $ret true or false.
	 * @param array   $cart_item The cart item.
	 * @since 4.8
	 */
	public function wc_epo_no_epo_in_cart( $ret = false, $cart_item = [] ) {

		$ret = $ret && empty( $cart_item['tmsubscriptionfee'] );

		return $ret;

	}

	/**
	 * Field pre validation
	 *
	 * @param array   $field_names The form prefix.
	 * @param class   $object The element field class.
	 * @param array   $element The element array.
	 * @param integer $loop The current loop index.
	 * @param string  $form_prefix The form prefix.
	 * @since 4.8
	 */
	public function wc_epo_validate_field_field_names( $field_names, $object, $element, $loop, $form_prefix ) {

		$subscription_field_names = THEMECOMPLETE_EPO()->get_post_names( $element['options'], $element['type'], $loop, $form_prefix, $this->subscription_fee_name, $element );

		$is_subscription_fee = false;
		if ( is_array( $element['is_subscription_fee'] ) ) {
			$is_subscription_fee = in_array( true, $element['is_subscription_fee'], true );
		} elseif ( ! is_array( $element['is_cart_fee'] ) ) {
			$is_subscription_fee = $element['is_subscription_fee'];
		}

		if ( $is_subscription_fee ) {
			$object->tmcp_attributes_subscription_fee = $subscription_field_names;
		}

		return $field_names;

	}

	/**
	 * Validate checkbox
	 *
	 * @param boolean $fail true or false.
	 * @param object  $object Field class.
	 * @since 4.8
	 */
	public function wc_epo_validate_checkbox( $fail, $object ) {

		$field_names = isset( $object->tmcp_attributes_subscription_fee ) ? $object->tmcp_attributes_subscription_fee : [];

		$check3 = array_intersect( $field_names, array_keys( $object->epo_post_fields ) );
		$fail3  = empty( $check3 ) || 0 === count( $check3 );

		$fail = $fail && $fail3;

		return $fail;

	}

	/**
	 * Validate radio button
	 *
	 * @param boolean $fail true or false.
	 * @param object  $object Field class.
	 * @param integer $index Element index.
	 * @since 4.8
	 */
	public function wc_epo_validate_radiobutton( $fail, $object, $index ) {

		$is_subscription_fee = $object->element['is_subscription_fee'][ $index ];

		if ( $is_subscription_fee ) {
			if ( ! isset( $object->epo_post_fields[ $object->tmcp_attributes_subscription_fee[ $index ] ] ) ) {
				$fail = true;
			}
		}

		return $fail;

	}

	/**
	 * Alternative radio button check
	 *
	 * @param boolean $is_alt true or false.
	 * @param object  $object Field class.
	 * @param integer $index Element index.
	 * @since 4.8
	 */
	public function wc_epo_alt_validate_radiobutton( $is_alt, $object, $index ) {

		$is_subscription_fee = $object->element['is_subscription_fee'][ $index ];

		if ( $is_subscription_fee ) {
			$is_alt = true;
		}

		return $is_alt;

	}

	/**
	 * Alter HTML name
	 *
	 * @param string       $name_inc The HTML name.
	 * @param string       $base_name_inc The base HTML name.
	 * @param array        $element The element array.
	 * @param mixed|null   $value The current $element['options'] array key.
	 * @param integer|null $choice_counter The choice counter.
	 * @param integer|null $element_counter The element counter.
	 * @since 4.8
	 */
	public function wc_epo_name_inc( $name_inc = '', $base_name_inc = '', $element = [], $value = null, $choice_counter = null, $element_counter = null ) {

		$is_subscription_fee = false;
		$element_object      = THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ];

		if ( 'post' === $element_object->is_post ) {

			if ( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {

				if ( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type ) {

					$key           = $element['type'] . '_subscriptionfee';
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );

				} elseif ( 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {

					$key = $element['type'] . '_subscriptionfee';
					if ( 'select' === $element['type'] ) {
						$key = 'selectbox_subscriptionfee';
					}
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ] );
				}
			} elseif ( 'multipleall' === $element_object->type || 'multiple' === $element_object->type ) {

				if ( 'checkbox' === $element['type'] ) {
					$key           = 'multiple_checkboxes_options_subscriptionfee';
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ][ $choice_counter ] );
				} elseif ( 'radio' === $element['type'] ) {
					$key           = 'radiobuttons_subscriptionfee';
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				} else {
					$key           = 'multiple_' . $element['type'] . '_options_subscriptionfee';
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ][ $choice_counter ] );
				}
			}
		}

		if ( $is_subscription_fee ) {
			$subscription_fee_name = $this->subscription_fee_name;
			$name_inc              = $subscription_fee_name . $name_inc;
		}

		return $name_inc;

	}

	/**
	 * Alter fieldtype key of the element in the template arguments
	 *
	 * @param array        $args Array of arguments.
	 * @param array        $element The element array.
	 * @param mixed|null   $value The current $element['options'] array key.
	 * @param integer|null $choice_counter The choice counter.
	 * @param integer|null $element_counter The element counter.
	 * @since 4.8
	 */
	public function wc_epo_display_template_args( $args = [], $element = [], $value = null, $choice_counter = null, $element_counter = null ) {

		$is_subscription_fee = false;
		$element_object      = THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ];

		if ( 'post' === $element_object->is_post ) {

			if ( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {

				if ( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {

					$key           = $element['type'] . '_subscriptionfee';
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );

				} elseif ( 'multiplesingle' === $element_object->type ) {

					$key = $element['type'] . '_subscriptionfee';
					if ( 'select' === $element['type'] ) {
						$key = 'selectbox_subscriptionfee';
					}
					$builder_value       = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;
					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ] );
				}
			} elseif ( 'multipleall' === $element_object->type || 'multiple' === $element_object->type ) {

				if ( 'checkbox' === $element['type'] ) {
					$key           = 'multiple_checkboxes_options_subscriptionfee';
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ][ $choice_counter ] );
				} elseif ( 'radio' === $element['type'] ) {
					$key           = 'radiobuttons_subscriptionfee';
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				} else {
					$key           = 'multiple_' . $element['type'] . '_options_subscriptionfee';
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ][ $choice_counter ] );
				}
			}
		}

		if ( $is_subscription_fee && isset( $args['fieldtype'] ) ) {
			$args['fieldtype']     = $this->subscription_fee_name_class;
			$changes_product_image = empty( $element['changes_product_image'] ) ? '' : $element['changes_product_image'];
			if ( ! empty( $changes_product_image ) ) {
				$args['fieldtype'] = $args['fieldtype'] . ' tm-product-image';
			}
		}

		return $args;

	}

	/**
	 * Gets the stored cart data for the order again functionality
	 *
	 * @param array $cart_item_meta Cart item meta data.
	 * @param array $item Item data.
	 * @since 4.8
	 */
	public function wc_epo_woocommerce_order_again_cart_item_data( $cart_item_meta = [], $item = [] ) {

		$_backup_cart = isset( $item['item_meta']['tmsubscriptionfee_data'] ) ? $item['item_meta']['tmsubscriptionfee_data'] : false;
		if ( ! $_backup_cart ) {
			$_backup_cart = isset( $item['item_meta']['_tmsubscriptionfee_data'] ) ? $item['item_meta']['_tmsubscriptionfee_data'] : false;
		}
		if ( $_backup_cart && is_array( $_backup_cart ) && isset( $_backup_cart[0] ) ) {
			if ( is_string( $_backup_cart[0] ) ) {
				$_backup_cart = themecomplete_maybe_unserialize( $_backup_cart[0] );
			}
			$cart_item_meta['tmsubscriptionfee'] = $_backup_cart[0];
		}

		return $cart_item_meta;

	}

	/**
	 * Check for if the cart key for subscriptions exists
	 *
	 * @param boolean $has_epo true or false..
	 * @param array   $cart_item_meta Cart item meta data.
	 * @since 4.8
	 */
	public function wc_epo_woocommerce_order_again_cart_item_data_has_epo( $has_epo = false, $cart_item_meta = [] ) {
		return $has_epo || isset( $cart_item_meta['tmsubscriptionfee'] );
	}

	/**
	 * Adds meta data to the order
	 *
	 * @param integer $item_id Item id.
	 * @param string  $cart_item_key Cart item key.
	 * @param array   $values Array of values.
	 * @since 4.8
	 */
	public function wc_epo_order_item_meta( $item_id, $cart_item_key, $values = [] ) {

		if ( ! empty( $values['tmsubscriptionfee'] ) ) {
			$order        = THEMECOMPLETE_EPO_HELPER()->tm_get_order_object();
			$currency_arg = [];
			if ( $order ) {
				$currency_arg = [ 'currency' => ( is_callable( [ $order, 'get_currency' ] ) ? $order->get_currency() : $order->get_order_currency() ) ];
			}
			$item = $item_id;
			$item->add_meta_data( '_tmsubscriptionfee_data', [ $values['tmsubscriptionfee'] ] );
			$item->add_meta_data( esc_html__( 'Options Subscription fee', 'woocommerce-tm-extra-product-options' ), wc_price( $values['tmsubscriptionfee'], $currency_arg ) );
		}

	}

	/**
	 * Alter global epo table
	 * Used inTHEMECOMPLETE_Extra_Product_Options->fill_builder_display
	 * $global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter )
	 *
	 * @param array   $global_epos Array of EPOs.
	 * @param integer $priority Priority.
	 * @param integer $pid id.
	 * @param integer $_s section id.
	 * @param integer $arr_element_counter Element counter.
	 * @param array   $element Element array.
	 * @param integer $value $element['options'] key.
	 * @param integer $choice_counter Choice counter.
	 * @param integer $element_counter Element counter.
	 * @since 4.8
	 */
	public function global_epos_fill_builder_display( $global_epos, $priority, $pid, $_s, $arr_element_counter, $element, $value, $choice_counter, $element_counter ) {

		$is_subscription_fee = false;

		$element_object = THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ];

		if ( 'post' === $element_object->is_post ) {

			if ( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {

				if ( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {
					$key           = $element['type'] . '_subscriptionfee';
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				} elseif ( 'multiplesingle' === $element_object->type ) {
					$key = $element['type'] . '_subscriptionfee';
					if ( 'select' === $element['type'] ) {
						$key = 'selectbox_subscriptionfee';
					}
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				}

				$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['is_subscription_fee'] = $is_subscription_fee;

			} elseif ( 'multipleall' === $element_object->type || 'multiple' === $element_object->type ) {

				if ( 'checkbox' === $element['type'] ) {
					$key           = 'multiple_checkboxes_options_subscriptionfee';
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ][ $choice_counter ] );
				} elseif ( 'radio' === $element['type'] ) {
					$key           = 'radiobuttons_subscriptionfee';
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				} else {
					$key           = 'multiple_' . $element['type'] . '_options_subscriptionfee';
					$builder_value = isset( $element['builder'][ $key ] ) ? $element['builder'][ $key ] : false;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ][ $choice_counter ] );
				}

				$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['is_subscription_fee'][] = $is_subscription_fee;

			}
		}

		return $global_epos;

	}

	/**
	 * Check for subscription fee typewhen displaying options on admin Order page
	 *
	 * @param boolean $ret true or false.
	 * @param string  $type Element price type.
	 * @since 4.8
	 */
	public function wc_epo_html_tm_epo_order_item_is_other_fee( $ret, $type ) {

		if ( 'subscriptionfee' === $type ) {
			$ret = true;
		}

		return $ret;

	}

	/**
	 * Alter the array of data for a variation. Used in the add to cart form.
	 *
	 * @param string $array Array of arguments.
	 * @param string $class The WC_Product_Variable class.
	 * @param string $variation The variation product.
	 * @since 4.8
	 */
	public function woocommerce_available_variation( $array, $class, $variation ) {

		if ( is_array( $array ) ) {

			if ( class_exists( 'WC_Subscriptions_Product' ) ) {

				$subscription_period = WC_Subscriptions_Product::get_price_string(
					$variation,
					[
						'subscription_price' => false,
						'sign_up_fee'        => false,
						'trial_length'       => false,
						'price'              => null,
					]
				);
				if ( is_callable( [ 'WC_Subscriptions_Product', 'get_sign_up_fee' ] ) ) {
					$subscription_sign_up_fee = WC_Subscriptions_Product::get_sign_up_fee( $variation );
				} else {
					$subscription_sign_up_fee = $variation->subscription_sign_up_fee;
				}
			} else {
				$subscription_sign_up_fee = '';
				$subscription_period      = '';
			}

			$array['tc_subscription_period']      = $subscription_period;
			$array['tc_subscription_sign_up_fee'] = $subscription_sign_up_fee;

		}

		return $array;

	}

}
