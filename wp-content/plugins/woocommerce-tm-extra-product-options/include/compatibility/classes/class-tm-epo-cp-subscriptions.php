<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * WooCommerce Subscriptions (https://woocommerce.com/products/woocommerce-subscriptions/)
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_subscriptions {

	// Replacement name for Subscription sign up fee fields 
	public $subscription_fee_name = "tmsubfee_";
	public $subscription_fee_name_class = "tmcp-sub-fee-field";

	// Holds the total fee added by Subscription sign up fee fields 
	public $subscription_tmfee = 0;

	private $variations_subscription_period = array();
	private $variations_subscription_sign_up_fee = array();

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'plugins_loaded', array( $this, 'add_compatibility' ), 2 );

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

		// Enqueue scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 4 );

		// Add custom final total data in the JS template file
		add_filter( 'wc_epo_after_js_final_totals', array( $this, 'wc_epo_after_js_final_totals' ), 10 );

		// Add to main JS script arguments
		add_filter( 'wc_epo_script_args', array( $this, 'wc_epo_script_args' ), 10, 1 );
		// Add setting in main THEMECOMPLETE_EPO class
		add_filter( 'wc_epo_get_settings', array( $this, 'wc_epo_get_settings' ), 10, 1 );

		// Add extra html data attributes to the totals template
		add_action( 'wc_epo_template_tm_totals', array( $this, 'wc_epo_template_tm_totals' ), 10, 1 );

		// Add extra arguments to the totals template
		add_filter( 'wc_epo_template_args_tm_totals', array( $this, 'wc_epo_template_args_tm_totals' ), 10, 2 );

		// Calculate subscription period and sign up fee per variation
		add_action( 'wc_epo_print_price_fields_in_variation_loop', array( $this, 'wc_epo_print_price_fields_in_variation_loop' ), 10, 2 );

		// Add string setting
		add_action( 'tm_epo_settings_string', array( $this, 'tm_epo_settings_string' ), 10, 1 );

		// Calculates the extra Subscription sign up fee
		add_filter( 'woocommerce_subscriptions_product_sign_up_fee', array( $this, 'woocommerce_subscriptions_product_sign_up_fee' ), 10, 2 );

		// Flag subscription renewal
		add_filter( 'woocommerce_subscriptions_renewal_order_items', array( $this, 'woocommerce_subscriptions_renewal_order_items' ), 10, 5 );
		add_filter( 'wcs_renewal_order_items', array( $this, 'woocommerce_subscriptions_renewal_order_items' ), 10, 1 );
		add_action( 'wcs_before_renewal_setup_cart_subscriptions', array( $this, 'wcs_before_renewal_setup_cart_subscriptions' ) );

		// Skip altering cart
		add_filter( 'wc_epo_no_add_cart_item', array( $this, 'wc_epo_no_add_cart_item' ), 10, 1 );

		// Skip altering order again data
		add_filter( 'wc_epo_no_order_again_cart_item_data', array( $this, 'wc_epo_no_order_again_cart_item_data' ), 10, 1 );

		// Skip altering order get_items
		add_filter( 'wc_epo_no_order_get_items', array( $this, 'wc_epo_no_order_get_items' ), 10, 1 );

		// Add data to main element array in the builder
		add_filter( 'wc_epo_builder_element_settings', array( $this, 'wc_epo_builder_element_settings' ), 10, 1 );

		// Initial value for the set_elements function
		add_filter( 'wc_epo_set_elements_options', array( $this, 'wc_epo_set_elements_options' ), 10, 1 );

		// Alter price type
		add_filter( 'tc_element_settings_override', array( $this, 'tc_element_settings_override' ), 10, 2 );
		add_filter( 'wc_epo_add_element_class', array( $this, 'wc_epo_add_element_class' ), 10, 7 );
		add_filter( 'wc_epo_builder_after_multiple_element_array', array( $this, 'wc_epo_builder_after_multiple_element_array' ), 10, 2 );
		add_filter( 'wc_epo_builder_element_array_in_loop_before', array( $this, 'wc_epo_builder_element_array_in_loop_before' ), 10, 5 );
		add_filter( 'wc_epo_builder_element_array_in_loop_after', array( $this, 'wc_epo_builder_element_array_in_loop_after' ), 10, 5 );
		add_action( 'wc_epo_builder_element_multiple_checkboxes_options', array( $this, 'wc_epo_builder_element_multiple_checkboxes_options' ), 10, 1 );
		add_filter( 'wc_epo_builder_element_multiple_checkboxes_options_js_object', array( $this, 'wc_epo_builder_element_multiple_checkboxes_options_js_object' ), 10, 5 );
		add_filter( 'wc_epo_obvalues', array( $this, 'wc_epo_obvalues' ), 10, 6 );
		add_filter( 'wc_epo_cbvalues', array( $this, 'wc_epo_cbvalues' ), 10, 6 );


		// Skip cart item loop
		add_filter( 'wc_epo_add_cart_item_loop', array( $this, 'wc_epo_add_cart_item_loop' ), 10, 2 );

		// Alter the cart from session
		add_filter( 'wc_epo_get_cart_item_from_session', array( $this, 'wc_epo_get_cart_item_from_session' ), 10, 2 );

		// Edit cart link product types
		add_filter( 'wc_epo_can_be_edited_product_type', array( $this, 'wc_epo_can_be_edited_product_type' ), 10, 1 );

		// Pre-init cart item
		add_filter( 'wc_epo_add_cart_item_data_helper', array( $this, 'wc_epo_add_cart_item_data_helper' ), 10, 1 );

		// Add field data to cart (subscription fees)
		add_filter( 'wc_epo_add_cart_item_data_loop', array( $this, 'wc_epo_add_cart_item_data_loop' ), 10, 11 );

		// No options in cart check
		add_filter( 'wc_epo_no_epo_in_cart', array( $this, 'wc_epo_no_epo_in_cart' ), 10, 2 );

		// Field pre validation
		add_filter( 'wc_epo_validate_field_field_names', array( $this, 'wc_epo_validate_field_field_names' ), 10, 5 );

		// Validate checkbox
		add_filter( 'wc_epo_validate_checkbox', array( $this, 'wc_epo_validate_checkbox' ), 10, 2 );

		// Validate radio button
		add_filter( 'wc_epo_validate_radiobutton', array( $this, 'wc_epo_validate_radiobutton' ), 10, 3 );

		// Alternative radio button check
		add_filter( 'wc_epo_alt_validate_radiobutton', array( $this, 'wc_epo_alt_validate_radiobutton' ), 10, 3 );

		// Alter HTML name
		add_filter( 'wc_epo_name_inc', array( $this, 'wc_epo_name_inc' ), 10, 6 );

		// Alter fieldtype key of the element in the template arguments
		add_filter( 'wc_epo_display_template_args', array( $this, 'wc_epo_display_template_args' ), 10, 5 );

		// Gets the stored cart data for the order again functionality
		add_filter( 'wc_epo_woocommerce_order_again_cart_item_data', array( $this, 'wc_epo_woocommerce_order_again_cart_item_data' ), 10, 2 );

		// Check for if the cart key for subscriptions exists
		add_filter( 'wc_epo_woocommerce_order_again_cart_item_data_has_epo', array( $this, 'wc_epo_woocommerce_order_again_cart_item_data_has_epo' ), 10, 2 );

		// Adds meta data to the order - WC < 2.7
		add_action( 'wc_epo_order_item_meta', array( $this, 'wc_epo_order_item_meta' ), 10, 3 );

		// Alter global epo table
		add_filter( 'global_epos_fill_builder_display', array( $this, 'global_epos_fill_builder_display' ), 10, 9 );

		// Check for subscription fee typewhen displaying options on admin Order page
		add_filter( 'wc_epo_html_tm_epo_order_item_is_other_fee', array( $this, 'wc_epo_html_tm_epo_order_item_is_other_fee' ), 10, 2 );

		// Alter the array of data for a variation. Used in the add to cart form.
		add_filter( 'woocommerce_available_variation', array( $this, 'woocommerce_available_variation' ), 10, 3 );

	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0
	 */
	public function wp_enqueue_scripts() {

		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-subscriptions', THEMECOMPLETE_EPO_PLUGIN_URL . '/include/compatibility/assets/js/cp-subscriptions.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION, TRUE );
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
        <# } #><?php

	}

	/**
	 * Add to main JS script arguments
	 *
	 * @since 1.0
	 */
	public function wc_epo_script_args( $args ) {

		$args["i18n_sign_up_fee"] = ( ! empty( THEMECOMPLETE_EPO()->tm_epo_subscription_fee_text ) ) ? THEMECOMPLETE_EPO()->tm_epo_subscription_fee_text : esc_html__( 'Sign up fee', 'woocommerce-tm-extra-product-options' );

		return $args;

	}

	/**
	 * Add setting in main THEMECOMPLETE_EPO class
	 *
	 * @since 1.0
	 */
	public function wc_epo_get_settings( $settings = array() ) {

		if ( class_exists( 'WC_Subscriptions' ) ) {
			$settings["tm_epo_subscription_fee_text"] = "";
		}

		return $settings;

	}

	/**
	 * Add extra html data attributes to the totals template
	 *
	 * @since 1.0
	 */
	public function wc_epo_template_tm_totals( $args ) {

		$data                                             = array();
		$data['data-is-subscription']                     = $args['is_subscription'];
		$data['data-subscription-sign-up-fee']            = $args['subscription_sign_up_fee'];
		$data['data-variations-subscription-sign-up-fee'] = esc_html( wp_json_encode( (array) $args['variations_subscription_sign_up_fee'] ) );
		$data['data-subscription-period']                 = esc_html( wp_json_encode( (array) $args['subscription_period'] ) );
		$data['data-variations-subscription-period']      = esc_html( wp_json_encode( (array) $args['variations_subscription_period'] ) );

		THEMECOMPLETE_EPO_HTML()->create_attribute_list( $data );

	}

	/**
	 * Add extra arguments to the totals template
	 *
	 * @since 1.0
	 */
	public function wc_epo_template_args_tm_totals( $args, $product ) {

		$is_subscription     = FALSE;
		$subscription_period = '';

		$subscription_sign_up_fee = 0;
		if ( class_exists( 'WC_Subscriptions_Product' ) ) {
			if ( WC_Subscriptions_Product::is_subscription( $product ) ) {
				$is_subscription     = TRUE;
				$subscription_period = WC_Subscriptions_Product::get_price_string(
					$product,
					array(
						'subscription_price' => FALSE,
						'sign_up_fee'        => FALSE,
						'trial_length'       => FALSE,
						'price'              => NULL,
					)
				);

				$subscription_sign_up_fee = WC_Subscriptions_Product::get_sign_up_fee( $product );
			}
		}

		$args["is_subscription"]                     = $is_subscription;
		$args["subscription_sign_up_fee"]            = $subscription_sign_up_fee;
		$args["variations_subscription_sign_up_fee"] = $this->variations_subscription_sign_up_fee;
		$args["subscription_period"]                 = $subscription_period;
		$args["variations_subscription_period"]      = $this->variations_subscription_period;

		return $args;
	}

	/**
	 * Calculate subscription period and sign up fee per variation
	 *
	 * @since 1.0
	 */
	public function wc_epo_print_price_fields_in_variation_loop( $variation, $child_id ) {

		if ( class_exists( 'WC_Subscriptions_Product' ) ) {

			$this->variations_subscription_period[ $child_id ] = WC_Subscriptions_Product::get_price_string(
				$variation,
				array(
					'subscription_price' => FALSE,
					'sign_up_fee'        => FALSE,
					'trial_length'       => FALSE,
					'price'              => NULL,
				)
			);
			if ( is_callable( array( 'WC_Subscriptions_Product', 'get_sign_up_fee' ) ) ) {
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
	 * @since 1.0
	 */
	public function tm_epo_settings_string( $settings ) {

		$inserted = array( array(
			                   'title'   => esc_html__( 'Subscription sign up fee text', 'woocommerce-tm-extra-product-options' ),
			                   'desc'    => esc_html__( 'Enter the Subscription sign up fee text or leave blank for default.', 'woocommerce-tm-extra-product-options' ),
			                   'id'      => 'tm_epo_subscription_fee_text',
			                   'default' => '',
			                   'type'    => 'text',
		                   )
		);
		array_splice( $settings, count( $settings ) - 1, 0, $inserted );

		return $settings;

	}

	/**
	 * Flag subscription renewal
	 *
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
	 * @since 1.0
	 */
	public function woocommerce_subscriptions_product_sign_up_fee( $subscription_sign_up_fee = "", $product = "" ) {

		$options_fee = 0;

		if ( WC()->cart ) {
			$cart_contents = WC()->cart->cart_contents;
			if ( $cart_contents && ! is_product() && WC()->cart ) {
				foreach ( $cart_contents as $cart_key => $cart_item ) {
					foreach ( $cart_item as $key => $data ) {
						if ( $key == "tmsubscriptionfee" ) {
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
	 * @since 4.8
	 */
	public function wc_epo_builder_element_settings( $data = array() ) {

		foreach ( $data as $key => $value ) {
			switch ( $key ) {

				case 'date':
				case 'time':
				case 'range':
				case 'color':
				case 'textarea':
				case 'textfield':
				case 'upload':
					$data[ $key ]['subscription_fee_type'] = "single";
					break;

				case 'selectbox':
				case 'radiobuttons':
				case 'checkboxes':
					$data[ $key ]['subscription_fee_type'] = "multiple";
					break;

				case 'header':
				case 'divider':
				case 'variations':
				default:
					$data[ $key ]['subscription_fee_type'] = "";
					break;
			}
		}

		return $data;

	}

	/**
	 * Initial value for the set_elements function
	 *
	 * @see   THEMECOMPLETE_EPO_BUILDER_base->set_elements
	 * @since 4.8
	 */
	public function wc_epo_set_elements_options( $options = array() ) {

		if ( ! isset( $options["subscription_fee_type"] ) ) {
			$options["subscription_fee_type"] = "";
		}

		return $options;

	}

	/**
	 * Alter price type
	 *
	 * @since 5.0
	 */
	public function tc_element_settings_override( $settings = array(), $name = "" ) {
		$fee = array_search( "fee", $settings );
		if ( $fee !== FALSE && $fee !== NULL ) {
			array_splice( $settings, $fee, 0, "subscriptionfee" );
		}

		return $settings;

	}

	/**
	 * Alter price type
	 *
	 * @since 5.0
	 */
	public function wc_epo_add_element_class( $class, $key, $value, $name, $settings, $is_addon, $tabs_override ) {

		if ( $value === "subscriptionfee" ) {
			$class = $this;
		}


		return $class;

	}

	/**
	 * Subscription sign up fee setting
	 *
	 * @since 5.0
	 */
	public final function add_setting_subscriptionfee( $name = "", $args = array() ) {

		return array_merge( array(
			"id"          => $name . "_subscriptionfee",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "checkbox",
			"tags"        => array( "class" => "c", "id" => "builder_" . $name . "_subscriptionfee", "name" => "tm_meta[tmfbuilder][" . $name . "_subscriptionfee][]", "value" => "1" ),
			"label"       => esc_html__( 'Subscription sign up fee', 'woocommerce-tm-extra-product-options' ),
		), $args );

	}

	/**
	 * Subscription sign up fee setting for checkboxes
	 *
	 * @since 5.0
	 */
	public final function wc_epo_builder_after_multiple_element_array( $o = array(), $name = "" ) {

		$o["subscriptionfee"] = array(
			"id"      => $name . "_subscriptionfee",
			"default" => "",
			"type"    => "checkbox",
			"nodiv"   => 1,
			"tags"    => array( "class" => "c", "id" => $name . "_subscriptionfee", "name" => $name . "_subscriptionfee", "value" => "1" ),
		);

		return $o;

	}

	/**
	 * Setup Subscription sign up fee setting for checkboxes in loop
	 *
	 * @since 5.0
	 */
	public final function wc_epo_builder_element_array_in_loop_before( $options = array(), $o, $ar, $name, $counter ) {

		if ( isset( $options["price_type"][ $ar ] ) && $options["price_type"][ $ar ] === "subscriptionfee" ) {
			$options["price_type"][ $ar ]      = '';
			$options["subscriptionfee"][ $ar ] = '1';
		}

		return $options;

	}

	/**
	 * Setup Subscription sign up fee setting for checkboxes in loop
	 *
	 * @since 5.0
	 */
	public final function wc_epo_builder_element_array_in_loop_after( $o = array(), $options, $ar, $name, $counter ) {

		if ( ! isset( $options["subscriptionfee"][ $ar ] ) ) {
			$options["subscriptionfee"][ $ar ] = '';
		}

		$o["subscriptionfee"]["default"]      = $options["subscriptionfee"][ $ar ];//subscriptionfee
		$o["subscriptionfee"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_subscriptionfee][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
		$o["subscriptionfee"]["tags"]["id"]   = str_replace( array( "[", "]" ), "", $o["subscriptionfee"]["tags"]["name"] ) . "_" . $ar;


		return $o;

	}

	/**
	 * Setup Subscription sign up fee setting for checkboxes in loop
	 *
	 * @since 5.0
	 */
	public final function wc_epo_builder_element_multiple_checkboxes_options( $o = array() ) {

		echo "<div class='tc-cell tc-col-12 tm_cell_subscriptionfee'><span class='tm-inline-label bsbb'>" . esc_html__( "Subscription sign up fee", 'woocommerce-tm-extra-product-options' ) . "</span>";
		THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["subscriptionfee"], 1 );
		echo "</div>";

	}

	/**
	 * Setup Subscription sign up fee setting for checkboxes in loop
	 *
	 * @since 5.0
	 */
	public final function wc_epo_builder_element_multiple_checkboxes_options_js_object( $js_object = array(), $d_counter, $o, $name, $counter ) {

		$js_object[ $d_counter ][] = array(
			"id"      => $name . '_subscriptionfee',
			"default" => (string) $o["subscriptionfee"]["default"],
			"checked" => (string) $o["subscriptionfee"]["default"] === (string) $o["subscriptionfee"]["tags"]["value"],
			"type"    => "checkbox",
			"tags"    => array(
				"name"  => "tm_meta[tmfbuilder][" . $name . "_subscriptionfee][" . ( is_null( $counter ) ? 0 : $counter ) . "][]",
				"value" => $o["subscriptionfee"]["tags"]["value"],
			)
		);

		return $js_object;

	}

	/**
	 * Setup Subscription sign up fee setting for checkboxes in loop
	 *
	 * @since 5.0
	 */
	public final function wc_epo_obvalues( $obvalues = array(), $builder, $value, $current_builder, $_titles_base, $_option_key ) {

		$_subscriptionfee_base = isset( $builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ] )
			? $builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ]
			: NULL;

		if ( is_null( $_subscriptionfee_base ) ) {
			$_subscriptionfee_base = array_map( array( $this, '_tm_clear_array_values' ), $_titles_base );
		}


		if ( ! isset( $_subscriptionfee_base[ $_option_key ] ) ) {
			$_subscriptionfee_base[ $_option_key ] = array_map( array( $this, '_tm_clear_array_values' ), $_titles_base[ $_option_key ] );
		}

		$obvalues["subscriptionfee"] = $_subscriptionfee_base[ $_option_key ];

		return $obvalues;

	}

	/**
	 * Clear array values
	 *
	 * @since  1.0
	 * @access private
	 */
	private function _tm_clear_array_values( $val ) {
		if ( is_array( $val ) ) {
			return array_map( array( $this, '_tm_clear_array_values' ), $val );
		} else {
			return "";
		}
	}

	/**
	 * Setup Subscription sign up fee setting for checkboxes in loop
	 *
	 * @since 5.0
	 */
	public final function wc_epo_cbvalues( $cbvalues = array(), $builder, $value, $current_builder, $_titles_base, $_option_key ) {

		$_subscriptionfee_base = isset( $builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ] )
			? $builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ]
			: NULL;
		$_subscriptionfee      = isset( $builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ] )
			? isset( $current_builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ] )
				? $current_builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ]
				: $builder[ 'multiple_' . $value['id'] . '_subscriptionfee' ]
			: NULL;

		if ( is_null( $_subscriptionfee_base ) ) {
			$_subscriptionfee_base = array_map( array( $this, '_tm_clear_array_values' ), $_titles_base );
		}
		if ( is_null( $_subscriptionfee ) ) {
			$_subscriptionfee = $_subscriptionfee_base;
		}

		if ( ! isset( $_subscriptionfee_base[ $_option_key ] ) ) {
			$_subscriptionfee_base[ $_option_key ] = array_map( array( $this, '_tm_clear_array_values' ), $_titles_base[ $_option_key ] );
		}

		if ( ! isset( $_subscriptionfee[ $_option_key ] ) ) {
			$_subscriptionfee[ $_option_key ] = array_map( array( $this, '_tm_clear_array_values' ), $_titles_base[ $_option_key ] );
		}

		$cbvalues["subscriptionfee"] = THEMECOMPLETE_EPO_HELPER()->build_array( $_subscriptionfee[ $_option_key ], $_subscriptionfee_base[ $_option_key ] );

		return $cbvalues;

	}


	/**
	 * Skip altering order get_items
	 *
	 * @since 4.8
	 */
	public function wc_epo_no_order_get_items( $ret = FALSE ) {

		if ( defined( 'THEMECOMPLETE_IS_SUBSCRIPTIONS_RENEWAL' ) ) {
			$ret = TRUE;
		}

		return $ret;

	}

	/**
	 * Skip altering add to cart
	 *
	 * @since 4.9.7
	 */
	public function wc_epo_no_add_cart_item( $ret = FALSE ) {

		if ( defined( 'THEMECOMPLETE_IS_SUBSCRIPTIONS_RENEWAL' ) ) {
			$ret = TRUE;
		}

		return $ret;

	}

	/**
	 * Skip altering order again
	 *
	 * @since 4.9.7
	 */
	public function wc_epo_no_order_again_cart_item_data( $ret = FALSE ) {

		if ( defined( 'THEMECOMPLETE_IS_SUBSCRIPTIONS_RENEWAL' ) ) {
			$ret = TRUE;
		}

		return $ret;

	}

	/**
	 * Skip cart item loop
	 *
	 * @since 4.8
	 */
	public function wc_epo_add_cart_item_loop( $ret = FALSE, $tmcp = array() ) {

		if ( isset( $tmcp['subscription_fees'] ) ) {
			$ret = TRUE;
		}

		return $ret;

	}

	/**
	 * Alter the cart from session
	 *
	 * @since 4.8
	 */
	public function wc_epo_get_cart_item_from_session( $cart_item = array(), $values = array() ) {

		if ( ! empty( $values['tmsubscriptionfee'] ) ) {
			$cart_item['tmsubscriptionfee'] = $values['tmsubscriptionfee'];
		}

		return $cart_item;

	}

	/**
	 * Edit cart link product types
	 *
	 * @since 4.8
	 */
	public function wc_epo_can_be_edited_product_type( $types = array() ) {

		$types[] = "subscription";
		$types[] = "variable-subscription";

		return $types;

	}

	/**
	 * Pre-init cart item
	 *
	 * @since 4.8
	 */
	public function wc_epo_add_cart_item_data_helper( $cart_item_meta = array() ) {

		if ( empty( $cart_item_meta['tmsubscriptionfee'] ) ) {
			$cart_item_meta['tmsubscriptionfee'] = 0;
		}

		return $cart_item_meta;

	}

	/**
	 * Add field data to cart (subscription fees)
	 *
	 * @since 4.8
	 */
	public function wc_epo_add_cart_item_data_loop( $cart_item_meta = array(), $field_obj, $tmcp_post_fields = array(), $element = array(), $field_loop = 0, $form_prefix = "", $product_id, $per_product_pricing, $cpf_product_price, $variation_id, $post_data ) {

		$current_tmcp_post_fields = array_intersect_key(
			$tmcp_post_fields,
			array_flip(
				THEMECOMPLETE_EPO()->get_post_names( $element['options'], $element['type'], $field_loop, $form_prefix, $this->subscription_fee_name )
			)
		);

		$holder_subscription_fees = THEMECOMPLETE_EPO()->tm_builder_elements[ $field_obj->element['type'] ]['subscription_fee_type'];
		foreach ( $current_tmcp_post_fields as $attribute => $key ) {

			if ( ! empty( $holder_subscription_fees ) ) {
				if ( isset( $tmcp_post_fields[ $attribute . '_quantity' ] ) ) {
					if ( empty( $tmcp_post_fields[ $attribute . '_quantity' ] ) ) {
						continue;
					}
				}
				$meta = FALSE;
				if ( $field_obj->is_setup() ) {
					$field_obj->attribute = $attribute;
					$field_obj->key       = $key;

					// Add field data to cart (subscription fees single)
					if ( $holder_subscription_fees == "single" ) {
						if ( isset( $field_obj->key ) && $field_obj->key != '' ) {
							$_price                   = THEMECOMPLETE_EPO()->calculate_price( $field_obj->post_data, $field_obj->element, $field_obj->key, $field_obj->attribute, $field_obj->per_product_pricing, $field_obj->cpf_product_price, $field_obj->variation_id );
							$this->subscription_tmfee = $this->subscription_tmfee + (float) $_price;
							$meta                     = array(
								'mode'                           => 'builder',
								'cssclass'                       => $field_obj->element['class'],
								'include_tax_for_fee_price_type' => $field_obj->element['include_tax_for_fee_price_type'],
								'tax_class_for_fee_price_type'   => $field_obj->element['tax_class_for_fee_price_type'],
								'hidelabelincart'                => $field_obj->element['hide_element_label_in_cart'],
								'hidevalueincart'                => $field_obj->element['hide_element_value_in_cart'],
								'hidelabelinorder'               => $field_obj->element['hide_element_label_in_order'],
								'hidevalueinorder'               => $field_obj->element['hide_element_value_in_order'],
								'element'                        => $field_obj->order_saved_element,
								'name'                           => $field_obj->element['label'],
								'value'                          => $field_obj->key,
								'price'                          => 0,
								'section'                        => $field_obj->element['uniqid'],
								'section_label'                  => $field_obj->element['label'],
								'percentcurrenttotal'            => 0,
								'fixedcurrenttotal'              => 0,
								'currencies'                     => isset( $field_obj->element['currencies'] ) ? $field_obj->element['currencies'] : array(),
								'price_per_currency'             => $field_obj->fill_currencies(),
								'quantity'                       => 1,

								'subscription_fees' => 'single',
							);

						}

						// Add field data to cart (subscription fees multiple)
					} elseif ( $holder_subscription_fees == "multiple" ) {
						// select box placeholder check 
						if ( isset( $field_obj->element['options'][ esc_attr( $field_obj->key ) ] ) ) {
							$_price     = THEMECOMPLETE_EPO()->calculate_price( $field_obj->post_data, $field_obj->element, $field_obj->key, $field_obj->attribute, $field_obj->per_product_pricing, $field_obj->cpf_product_price, $field_obj->variation_id );
							$use_images = ! empty( $field_obj->element['use_images'] ) ? $field_obj->element['use_images'] : "";
							if ( $use_images ) {
								$_image_key = array_search( $field_obj->key, $field_obj->element['option_values'] );
								if ( $_image_key === NULL || $_image_key === FALSE ) {
									$_image_key = FALSE;
								}
							} else {
								$_image_key = FALSE;
							}

							$use_colors = ! empty( $field_obj->element['use_colors'] ) ? $field_obj->element['use_colors'] : "";
							if ( $use_colors ) {
								$_color_key = array_search( $field_obj->key, $field_obj->element['option_values'] );
								if ( $_color_key === NULL || $_color_key === FALSE ) {
									$_color_key = FALSE;
								}
							} else {
								$_color_key = FALSE;
							}

							$this->subscription_tmfee = $this->subscription_tmfee + (float) $_price;

							$meta = array(
								'mode'                           => 'builder',
								'cssclass'                       => $field_obj->element['class'],
								'include_tax_for_fee_price_type' => $field_obj->element['include_tax_for_fee_price_type'],
								'tax_class_for_fee_price_type'   => $field_obj->element['tax_class_for_fee_price_type'],
								'hidelabelincart'                => $field_obj->element['hide_element_label_in_cart'],
								'hidevalueincart'                => $field_obj->element['hide_element_value_in_cart'],
								'hidelabelinorder'               => $field_obj->element['hide_element_label_in_order'],
								'hidevalueinorder'               => $field_obj->element['hide_element_value_in_order'],
								'element'                        => $field_obj->order_saved_element,
								'name'                           => $field_obj->element['label'],
								'value'                          => $field_obj->element['options'][ esc_attr( $field_obj->key ) ],
								'price'                          => 0,
								'section'                        => $field_obj->element['uniqid'],
								'section_label'                  => $field_obj->element['label'],
								'percentcurrenttotal'            => 0,
								'fixedcurrenttotal'              => 0,
								'currencies'                     => isset( $field_obj->element['currencies'] ) ? $field_obj->element['currencies'] : array(),
								'price_per_currency'             => $field_obj->fill_currencies(),
								'quantity'                       => 1,

								'subscription_fees'     => 'multiple',
								'multiple'              => '1',
								'key'                   => esc_attr( $field_obj->key ),
								'use_images'            => $use_images,
								'use_colors'            => $use_colors,
								'color'                 => ( $_color_key !== FALSE && isset( $field_obj->element['color'][ $_color_key ] ) ) ? empty( $field_obj->element['color'][ $_color_key ] ) ? "transparent" : $field_obj->element['color'][ $_color_key ] : "",
								'changes_product_image' => ! empty( $field_obj->element['changes_product_image'] ) ? $field_obj->element['changes_product_image'] : "",
								'images'                => ( $_image_key !== FALSE && isset( $field_obj->element['images'][ $_image_key ] ) ) ? $field_obj->element['images'][ $_image_key ] : "",
								'imagesp'               => ( $_image_key !== FALSE && isset( $field_obj->element['imagesp'][ $_image_key ] ) ) ? $field_obj->element['imagesp'][ $_image_key ] : "",
							);

						}
					}
				}

				if ( is_array( $meta ) ) {
					if ( isset( $meta[0] ) && is_array( $meta[0] ) ) {
						foreach ( $meta as $k => $value ) {
							$cart_item_meta['tmcartepo'][]                = $value;
							$cart_item_meta['tmdata']['tmcartepo_data'][] = array( 'key' => $key, 'attribute' => $attribute );
						}
					} else {
						$cart_item_meta['tmcartepo'][]                = $meta;
						$cart_item_meta['tmdata']['tmcartepo_data'][] = array( 'key' => $key, 'attribute' => $attribute );
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
	 * @since 4.8
	 */
	public function wc_epo_no_epo_in_cart( $ret = FALSE, $cart_item = array() ) {

		$ret = $ret && empty( $cart_item["tmsubscriptionfee"] );

		return $ret;

	}

	/**
	 * Field pre validation
	 *
	 * @since 4.8
	 */
	public function wc_epo_validate_field_field_names( $field_names, $object, $element, $loop, $form_prefix ) {

		$subscription_field_names = THEMECOMPLETE_EPO()->get_post_names( $element['options'], $element['type'], $loop, $form_prefix, $this->subscription_fee_name );

		$is_subscription_fee = FALSE;
		if ( is_array( $element['is_subscription_fee'] ) ) {
			$is_subscription_fee = in_array( TRUE, $element['is_subscription_fee'] );
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
	 * @since 4.8
	 */
	public function wc_epo_validate_checkbox( $fail, $object ) {

		$field_names = isset( $object->tmcp_attributes_subscription_fee ) ? $object->tmcp_attributes_subscription_fee : array();

		$check3 = array_intersect( $field_names, array_keys( $object->epo_post_fields ) );
		$fail3  = empty( $check3 ) || count( $check3 ) == 0;

		$fail = $fail && $fail3;

		return $fail;

	}

	/**
	 * Validate radio button
	 *
	 * @since 4.8
	 */
	public function wc_epo_validate_radiobutton( $fail, $object, $index ) {

		$is_subscription_fee = $object->element['is_subscription_fee'][ $index ];

		if ( $is_subscription_fee ) {
			if ( ! isset( $object->epo_post_fields[ $object->tmcp_attributes_subscription_fee[ $index ] ] ) ) {
				$fail = TRUE;
			}
		}

		return $fail;

	}

	/**
	 * Alternative radio button check
	 *
	 * @since 4.8
	 */
	public function wc_epo_alt_validate_radiobutton( $is_alt, $object, $index ) {

		$is_subscription_fee = $object->element['is_subscription_fee'][ $index ];

		if ( $is_subscription_fee ) {
			$is_alt = TRUE;
		}

		return $is_alt;

	}

	/**
	 * Alter HTML name
	 *
	 * @since 4.8
	 */
	public function wc_epo_name_inc( $name_inc = "", $base_name_inc = "", $element = array(), $value, $choice_counter, $element_counter ) {

		$is_subscription_fee = FALSE;

		if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["is_post"] == "post" ) {


			if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "single" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multipleallsingle" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multiplesingle" ) {

				if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "single" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multipleallsingle" ) {
					
					$key = $element['type'] . '_subscriptionfee';
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				
				} elseif ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multiplesingle" ) {
					
					$key = $element['type'] . '_subscriptionfee';
					if ( $element['type'] === "select" ){
						$key = 'selectbox_subscriptionfee';					
					}
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;
					
					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				}

			} elseif ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multipleall" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multiple" ) {
				
				if ( $element['type'] === "checkbox" ){
					$key = 'multiple_checkboxes_options_subscriptionfee';
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ][$choice_counter] );
				} else if ( $element['type'] === "radio" ){
					$key = 'radiobuttons_subscriptionfee';
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				} else {
					$key = 'multiple_' . $element['type'] . '_options_subscriptionfee';
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;	

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ][$choice_counter] );
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
	 * @since 4.8
	 */
	public function wc_epo_display_template_args( $args = array(), $element = array(), $value, $choice_counter, $element_counter ) {

		$is_subscription_fee = FALSE;

		if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["is_post"] == "post" ) {


			if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "single" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multipleallsingle" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multiplesingle" ) {

				if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "single" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multipleallsingle" ) {
					
					$key = $element['type'] . '_subscriptionfee';
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				
				} elseif ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multiplesingle" ) {
					
					$key = $element['type'] . '_subscriptionfee';
					if ( $element['type'] === "select" ){
						$key = 'selectbox_subscriptionfee';					
					}
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;
					
					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				}

			} elseif ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multipleall" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multiple" ) {
				
				if ( $element['type'] === "checkbox" ){
					$key = 'multiple_checkboxes_options_subscriptionfee';
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ][$choice_counter] );
				} else if ( $element['type'] === "radio" ){
					$key = 'radiobuttons_subscriptionfee';
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				} else {
					$key = 'multiple_' . $element['type'] . '_options_subscriptionfee';
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;	

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ][$choice_counter] );
				}				

			}

		}		

		if ( $is_subscription_fee && isset( $args['fieldtype'] ) ) {
			$args['fieldtype']     = $this->subscription_fee_name_class;
			$changes_product_image = empty( $element['changes_product_image'] ) ? "" : $element['changes_product_image'];
			if ( ! empty( $changes_product_image ) ) {
				$args['fieldtype'] = $args['fieldtype'] . " tm-product-image";
			}

		}

		return $args;

	}

	/**
	 * Gets the stored cart data for the order again functionality
	 *
	 * @since 4.8
	 */
	public function wc_epo_woocommerce_order_again_cart_item_data( $cart_item_meta = array(), $item = array() ) {

		$_backup_cart = isset( $item['item_meta']['tmsubscriptionfee_data'] ) ? $item['item_meta']['tmsubscriptionfee_data'] : FALSE;
		if ( ! $_backup_cart ) {
			$_backup_cart = isset( $item['item_meta']['_tmsubscriptionfee_data'] ) ? $item['item_meta']['_tmsubscriptionfee_data'] : FALSE;
		}
		if ( $_backup_cart && is_array( $_backup_cart ) && isset( $_backup_cart[0] ) ) {
			if ( is_string( $_backup_cart[0] ) ) {
				$_backup_cart = maybe_unserialize( $_backup_cart[0] );
			}
			$cart_item_meta['tmsubscriptionfee'] = $_backup_cart[0];
		}

		return $cart_item_meta;

	}

	/**
	 * Check for if the cart key for subscriptions exists
	 *
	 * @since 4.8
	 */
	public function wc_epo_woocommerce_order_again_cart_item_data_has_epo( $has_epo = FALSE, $cart_item_meta = array() ) {
		return $has_epo || isset( $cart_item_meta['tmsubscriptionfee'] );
	}

	/**
	 * Adds meta data to the order
	 *
	 * @since 4.8
	 */
	public function wc_epo_order_item_meta( $item_id, $cart_item_key, $values = array() ) {

		if ( ! empty( $values['tmsubscriptionfee'] ) ) {
			$order        = THEMECOMPLETE_EPO_HELPER()->tm_get_order_object();
			$currency_arg = array();
			if ( $order ) {
				$currency_arg = array( 'currency' => ( is_callable( array( $order, 'get_currency' ) ) ? $order->get_currency() : $order->get_order_currency() ) );
			}
			//  WC < 2.7
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
				wc_add_order_item_meta( $item_id, '_tmsubscriptionfee_data', array( $values['tmsubscriptionfee'] ) );
				wc_add_order_item_meta( $item_id, esc_html__( "Options Subscription fee", 'woocommerce-tm-extra-product-options' ), wc_price( $values['tmsubscriptionfee'], $currency_arg ) );
				// WC >= 2.7 (crud)
			} else {
				$item = $item_id;
				$item->add_meta_data( '_tmsubscriptionfee_data', array( $values['tmsubscriptionfee'] ) );
				$item->add_meta_data( esc_html__( "Options Subscription fee", 'woocommerce-tm-extra-product-options' ), wc_price( $values['tmsubscriptionfee'], $currency_arg ) );
			}
		}

	}

	/**
	 * Alter global epo table
	 *
	 * @see   THEMECOMPLETE_Extra_Product_Options->fill_builder_display
	 * @since 4.8
	 */
	public function global_epos_fill_builder_display( $global_epos, $priority, $pid, $_s, $arr_element_counter, $element, $value, $choice_counter, $element_counter ) {

		$is_subscription_fee = FALSE;

		if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["is_post"] == "post" ) {


			if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "single" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multipleallsingle" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multiplesingle" ) {

				if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "single" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multipleallsingle" ) {
					
					$key = $element['type'] . '_subscriptionfee';
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				
				} elseif ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multiplesingle" ) {
					
					$key = $element['type'] . '_subscriptionfee';
					if ( $element['type'] === "select" ){
						$key = 'selectbox_subscriptionfee';					
					}
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;
					
					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				}
				
								$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['is_subscription_fee'] = $is_subscription_fee;

			} elseif ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multipleall" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multiple" ) {
				
				if ( $element['type'] === "checkbox" ){
					$key = 'multiple_checkboxes_options_subscriptionfee';
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ][$choice_counter] );
				} else if ( $element['type'] === "radio" ){
					$key = 'radiobuttons_subscriptionfee';
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[0] );
				} else {
					$key = 'multiple_' . $element['type'] . '_options_subscriptionfee';
					$builder_value = isset($element['builder'][ $key ])?$element['builder'][ $key ]:FALSE;	

					$is_subscription_fee = ! empty( $builder_value ) && ! empty( $builder_value[ $element_counter ][$choice_counter] );
				}
				
				$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['is_subscription_fee'][] = $is_subscription_fee;				

			}

		}	

		return $global_epos;

	}

	/**
	 * Check for subscription fee typewhen displaying options on admin Order page
	 *
	 * @since 4.8
	 */
	public function wc_epo_html_tm_epo_order_item_is_other_fee( $ret, $type ) {

		if ( $type == "subscriptionfee" ) {
			$ret = TRUE;
		}

		return $ret;

	}

	/**
	 * Alter the array of data for a variation. Used in the add to cart form.
	 *
	 * @since 4.8
	 */
	public function woocommerce_available_variation( $array, $class, $variation ) {

		if ( is_array( $array ) ) {

			if ( class_exists( 'WC_Subscriptions_Product' ) ) {

				$subscription_period = WC_Subscriptions_Product::get_price_string(
					$variation,
					array(
						'subscription_price' => FALSE,
						'sign_up_fee'        => FALSE,
						'trial_length'       => FALSE,
						'price'              => NULL,
					)
				);
				if ( is_callable( array( 'WC_Subscriptions_Product', 'get_sign_up_fee' ) ) ) {
					$subscription_sign_up_fee = WC_Subscriptions_Product::get_sign_up_fee( $variation );
				} else {
					$subscription_sign_up_fee = $variation->subscription_sign_up_fee;
				}
			} else {
				$subscription_sign_up_fee = '';
				$subscription_period      = '';
			}

			$array["tc_subscription_period"]      = $subscription_period;
			$array["tc_subscription_sign_up_fee"] = $subscription_sign_up_fee;

		}

		return $array;

	}

}
