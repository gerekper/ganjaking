<?php
/**
 * Extra Product Options Frontend Display
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_Display {

	// theme folder name for overrding plugin template files
	public $_namespace = 'tm-extra-product-options';

	// Prevent option duplication for bad coded themes 
	private $tm_options_have_been_displayed = FALSE;
	private $tm_options_single_have_been_displayed = FALSE;
	private $tm_options_totals_have_been_displayed = FALSE;

	// Set of variables to ensure that the correct options are displayed on complex layouts 
	private $epo_id = 0;
	private $epo_internal_counter = 0;
	private $epo_internal_counter_check = array();
	private $original_epo_internal_counter = 0;
	private $current_product_id_to_be_displayed = 0;
	private $current_product_id_to_be_displayed_check = array();

	// Inline styles 
	public $inline_styles;
	public $inline_styles_head;

	// Float direction for radio and checkboxes image replacements 
	public $float_direction = "left";
	public $float_direction_opposite = "right";

	// Unique form prefix
	public $unique_form_prefix = "";

	// Associated product discounts
	private $discount = '';
	private $discount_type = '';

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

		$this->inline_styles      = '';
		$this->inline_styles_head = '';

		// Display in frontend
		add_action( 'woocommerce_tm_epo', array( $this, 'frontend_display' ), 10, 3 );
		add_action( 'woocommerce_tm_epo_fields', array( $this, 'tm_epo_fields' ), 10, 4 );
		add_action( 'woocommerce_tm_epo_totals', array( $this, 'tm_epo_totals' ), 10, 3 );

		// Display in frontend (Compatibility for older plugin versions)
		add_action( 'woocommerce_tm_custom_price_fields', array( $this, 'frontend_display' ) );
		add_action( 'woocommerce_tm_custom_price_fields_only', array( $this, 'tm_epo_fields' ) );
		add_action( 'woocommerce_tm_custom_price_fields_totals', array( $this, 'tm_epo_totals' ) );

		// Ensures the correct display order of options when multiple prodcuts are displayed 
		add_action( 'woocommerce_before_single_product', array( $this, 'woocommerce_before_single_product' ), 1 );
		add_action( 'woocommerce_after_single_product', array( $this, 'woocommerce_after_single_product' ), 9999 );

		// Internal variables
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'woocommerce_before_add_to_cart_button' ) );

		// Add custom inline css 
		add_action( 'template_redirect', array( $this, 'tm_variation_css_check' ), 9999 );

		// Alter the array of data for a variation. Used in the add to cart form.
		add_filter( 'woocommerce_available_variation', array( $this, 'woocommerce_available_variation' ), 10, 3 );

	}

	/**
	 * Apply asoociated product discount
	 *
	 * @since 5.0.8
	 */
	public function set_discount($discount = '', $discount_type = '') {
		$this->discount = $discount;
		$this->discount_type = $discount_type;
	}

	/**
	 * Change internal epo counter
	 * Currently used for associated products
	 *
	 * @since 5.0.8
	 */
	public function set_epo_internal_counter($counter = 0) {
		$this->original_epo_internal_counter = $this->epo_internal_counter;
		$this->epo_internal_counter = $counter;
	}

	/**
	 * Restore internal epo counter
	 * Currently used for associated products
	 *
	 * @since 5.0.8
	 */
	public function restore_epo_internal_counter() {
		$this->epo_internal_counter = $this->original_epo_internal_counter;
	}

	/**
	 * Returns the $_namespace variable
	 *
	 * @since 1.0
	 */
	public function get_namespace() {
		return $this->_namespace . "/";
	}


	/**
	 * Get the tax rate of the give tax classes
	 *
	 * @param $classes
	 *
	 * @return int
	 */
	public function get_tax_rate( $classes ) {

		$tax_rate = 0;

		if ( class_exists( 'WC_Tax' ) && version_compare( get_option( 'woocommerce_version' ), '2.4', '>=' ) ) {
			$tax_rates    = WC_Tax::get_rates( $classes );
			$precision    = wc_get_rounding_precision();
			$price_of_one = 1 * ( pow( 10, $precision ) );
			$taxes_of_one = array_sum( WC_Tax::calc_tax( $price_of_one, $tax_rates, wc_prices_include_tax() ) );
			$taxes_of_one = $taxes_of_one / ( pow( 10, $precision ) );
			$tax_rate     = 100 * $taxes_of_one;
		}

		return $tax_rate;

	}

	/**
	 * Add validation rules
	 *
	 * @param $element
	 *
	 * @return array
	 */
	public function get_tm_validation_rules( $element ) {

		$rules = array();
		if ( $element['required'] ) {
			$rules['required'] = TRUE;
		}
		if ( isset( $element['min_chars'] ) && $element['min_chars'] !== '' && $element['min_chars'] !== FALSE ) {
			$rules['minlength'] = absint( $element['min_chars'] );
		}
		if ( isset( $element['max_chars'] ) && $element['max_chars'] !== '' && $element['max_chars'] !== FALSE ) {
			$rules['maxlength'] = absint( $element['max_chars'] );
		}
		if ( isset( $element['min'] ) && $element['min'] !== '' ) {
			$rules['min'] = floatval( $element['min'] );
		}
		if ( isset( $element['max'] ) && $element['max'] !== '' ) {
			$rules['max'] = floatval( $element['max'] );
		}
		if ( ! empty( $element['validation1'] ) ) {
			$rules[ $element['validation1'] ] = TRUE;
		}

		return $rules;

	}

	/**
	 * Alter the array of data for a variation.
	 * Used in the add to cart form.
	 *
	 * @since 1.0
	 */
	public function woocommerce_available_variation( $array, $class, $variation ) {

		if ( is_array( $array ) ) {

			$tax_rate = $this->get_tax_rate( themecomplete_get_tax_class( $variation ) );

			$base_taxes_of_one   = 0;
			$modded_taxes_of_one = 0;


			$non_base_location_prices = - 1;
			$base_tax_rate            = $tax_rate;

			if ( class_exists( 'WC_Tax' ) && version_compare( get_option( 'woocommerce_version' ), '2.4', '>=' ) ) {
				$tax_rates      = WC_Tax::get_rates( themecomplete_get_tax_class( $variation ) );
				$base_tax_rates = WC_Tax::get_base_tax_rates( themecomplete_get_tax_class( $variation ) );
				$base_tax_rate  = 0;
				foreach ( $base_tax_rates as $key => $value ) {
					$base_tax_rate = $base_tax_rate + floatval( $value['rate'] );
				}

				$non_base_location_prices = ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', TRUE ) ) == TRUE ? 1 : 0;

				$precision    = wc_get_rounding_precision();
				$price_of_one = 1 * ( pow( 10, $precision ) );

				if ( $non_base_location_prices ) {
					$prices_include_tax = TRUE;
				} else {
					$prices_include_tax = wc_prices_include_tax();
				}

				$base_taxes_of_one   = array_sum( WC_Tax::calc_tax( $price_of_one, $base_tax_rates, TRUE ) );
				$modded_taxes_of_one = array_sum( WC_Tax::calc_tax( $price_of_one - $base_taxes_of_one, $tax_rates, FALSE ) );

				$base_taxes_of_one   = $base_taxes_of_one / ( pow( 10, $precision ) );
				$modded_taxes_of_one = $modded_taxes_of_one / ( pow( 10, $precision ) );

			}


			$array["tc_tax_rate"]                 = $tax_rate;
			$array["tc_is_taxable"]               = $variation->is_taxable();
			$array["tc_base_tax_rate"]            = $base_tax_rate;
			$array["tc_base_taxes_of_one"]        = $base_taxes_of_one;
			$array["tc_modded_taxes_of_one"]      = $modded_taxes_of_one;
			$array["tc_non_base_location_prices"] = $non_base_location_prices;
			$array["tc_is_on_sale"]               = $variation->is_on_sale();
			if ( isset($_REQUEST['discount_type']) && isset($_REQUEST['discount']) && !empty($_REQUEST['discount'])){

				$current_price = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $variation->get_price(), $_REQUEST['discount'], $_REQUEST['discount_type']);
				$variation->set_sale_price($current_price); 
				$variation->set_price($current_price);  

				// See if prices should be shown for each variation after selection.
				$show_variation_price = apply_filters( 'woocommerce_show_variation_price', $variation->get_price() === '' || $class->get_variation_sale_price( 'min' ) !== $class->get_variation_sale_price( 'max' ) || $class->get_variation_regular_price( 'min' ) !== $class->get_variation_regular_price( 'max' ), $class, $variation );

				$array["display_price"] = wc_get_price_to_display( $variation );
				$array["price_html"]    = $show_variation_price ? '<span class="price">' . $variation->get_price_html() . '</span>' : '';

			}
		}

		return $array;

	}

	/**
	 * This loads only on quick view modules
	 * and on the ajax request of composite bundles
	 * and it is required in order to show custom styles
	 *
	 * @since 4.8
	 */
	public function tm_add_inline_style_qv() {

		if ( ! empty( $this->inline_styles ) ) {
			global $wp_scripts;
			$wp_scripts = new WP_Scripts();
			$this->tm_add_inline_style_reg();
			wp_print_footer_scripts();
		}

	}

	/**
	 * Register inline styles
	 *
	 * @since 4.8
	 */
	public function tm_add_inline_style_reg() {

		if ( ! empty( $this->inline_styles ) ) {
			wp_register_style( 'themecomplete-styles-footer', FALSE );
			wp_add_inline_style( 'themecomplete-styles-footer', $this->inline_styles );
			wp_enqueue_style( 'themecomplete-styles-footer' );
		}

	}

	/**
	 * Handles any extra styling associated with the fields
	 *
	 * @since 4.8
	 */
	public function tm_add_inline_style() {

		if ( ! empty( $this->inline_styles ) ) {
			if ( THEMECOMPLETE_EPO()->is_quick_view() || ( is_ajax() && ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) ) {
				$this->tm_add_inline_style_qv();
			} else {
				$this->tm_add_inline_style_reg();
			}
		}

	}

	/**
	 * Handles any extra styling associated with the fields
	 *
	 * @since 4.8.5
	 */
	public function add_inline_style( $css_string = "" ) {

		$this->inline_styles = $this->inline_styles . $css_string;

	}

	/**
	 * Add custom inline css
	 * Used to hide the native variations
	 *
	 * @since 1.0
	 */
	public function tm_variation_css_check( $echo = 0, $product_id = 0 ) {

		if ( ! is_product() ) {
			return;
		}

		if ( is_rtl() ) {
			$this->float_direction          = "right";
			$this->float_direction_opposite = "left";
		}

		$post_id = get_the_ID();

		if ( $product_id && $product_id !== $post_id ) {
			$post_id = $product_id;
		}

		$post_id = floatval( $post_id );

		$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id, 'product' ) );

		if ( $original_product_id !== $post_id ) {
			$post_id = $original_product_id;
		}

		$has_epo = THEMECOMPLETE_EPO_API()->has_options( $post_id );

		if ( $has_epo !== FALSE && is_array( $has_epo ) && isset( $has_epo['variations'] ) == TRUE && empty( $has_epo['variations_disabled'] ) ) {
			if ( $product_id ) {
				$css_string = "#product-" . $product_id . " form .variations,.post-" . $product_id . " form .variations {display:none;}";
			} else {
				$css_string = "form .variations{display:none;}";
			}

			$this->inline_styles_head = $this->inline_styles_head . $css_string;
			if ( $echo ) {
				$this->tm_variation_css_check_do();
			} else {
				add_action( 'wp_head', array( $this, 'tm_variation_css_check_do' ) );
			}
		}

	}

	/**
	 * Print inline css
	 *
	 * @see   tm_variation_css_check
	 * @since 1.0
	 */
	public function tm_variation_css_check_do() {

		if ( ! empty( $this->inline_styles_head ) ) {
			wp_register_style( 'themecomplete-styles-header', FALSE );
			wp_add_inline_style( 'themecomplete-styles-header', $this->inline_styles_head );
			wp_enqueue_style( 'themecomplete-styles-header' );
		}

	}

	/**
	 * Internal variables
	 *
	 * @since 1.0
	 */
	public function woocommerce_before_add_to_cart_button( $product_id = FALSE ) {

		$this->epo_id ++;
		echo '<input type="hidden" class="tm-epo-counter" name="tm-epo-counter" value="' . esc_attr( $this->epo_id ) . '" />';

		if ( $product_id ) {
			$pid = $product_id;
		} else {
			global $product;
			$pid = themecomplete_get_id( $product );
		}

		if ( ! empty( $pid ) ) {
			echo '<input type="hidden" class="tc-add-to-cart" name="tcaddtocart" value="' . esc_attr( $pid ) . '" />';
		}

	}

	/**
	 * Ensures the correct display order of options when multiple prodcuts are displayed
	 *
	 * @since 1.0
	 */
	public function woocommerce_before_single_product() {

		global $woocommerce;
		if ( ! property_exists( $woocommerce, 'product_factory' ) || $woocommerce->product_factory === NULL ) {
			return;// bad function call
		}
		global $product;
		if ( $product ) {
			if ( ! is_product() ) {
				$this->tm_variation_css_check( 1, themecomplete_get_id( $product ) );
			}
			$this->current_product_id_to_be_displayed                                                                                                                                  = themecomplete_get_id( $product );
			$this->current_product_id_to_be_displayed_check[ "tc" . "-" . count( $this->current_product_id_to_be_displayed_check ) . "-" . $this->current_product_id_to_be_displayed ] = $this->current_product_id_to_be_displayed;
		}

	}

	/**
	 * Ensures the correct display order of options when multiple prodcuts are displayed
	 *
	 * @since 1.0
	 */
	public function woocommerce_after_single_product() {

		$this->current_product_id_to_be_displayed = 0;
		$this->unique_form_prefix                 = '';

	}

	/**
	 * Handles the display of all the extra options on the product page.
	 *
	 * IMPORTANT:
	 * We do not support plugins that pollute the global $woocommerce.
	 *
	 */
	public function frontend_display( $product_id = 0, $form_prefix = "", $dummy_prefix = FALSE ) {

		global $product, $woocommerce;
		if ( ! property_exists( $woocommerce, 'product_factory' )
		     || $woocommerce->product_factory === NULL
		     || ( $this->tm_options_have_been_displayed && ( ! ( THEMECOMPLETE_EPO()->is_bto || ( THEMECOMPLETE_EPO()->is_enabled_shortcodes() && ! is_product() ) || ( ( is_shop() || is_product_category() || is_product_tag() ) && THEMECOMPLETE_EPO()->tm_epo_enable_in_shop == "yes" ) ) ) )
		) {
			return;// bad function call
		}

		$this->tm_epo_fields( $product_id, $form_prefix, FALSE, $dummy_prefix );
		$this->tm_add_inline_style();
		$this->tm_epo_totals( $product_id, $form_prefix );
		if ( ! THEMECOMPLETE_EPO()->is_bto ) {
			$this->tm_options_have_been_displayed = TRUE;
		}

	}

	/**
	 * Batch add plugin options
	 *
	 * @param string $form_prefix
	 * @param bool   $dummy_prefix
	 */
	private function tm_epo_fields_batch( $form_prefix = "", $dummy_prefix = FALSE ) {

		foreach ( $this->current_product_id_to_be_displayed_check as $key => $product_id ) {
			if ( ! empty( $product_id ) ) {
				$this->inline_styles      = '';
				$this->inline_styles_head = '';

				$this->tm_variation_css_check( 1, $product_id );

				$this->tm_epo_fields( $product_id, $form_prefix, FALSE, $dummy_prefix );
				$this->tm_add_inline_style();

				if ( THEMECOMPLETE_EPO()->tm_epo_options_placement == THEMECOMPLETE_EPO()->tm_epo_totals_box_placement ) {
					$this->tm_epo_totals( $product_id, $form_prefix );
				} else {
					if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
						unset( $this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] );
					}
				}
			}
		}
		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
			if ( THEMECOMPLETE_EPO()->tm_epo_options_placement != THEMECOMPLETE_EPO()->tm_epo_totals_box_placement ) {
				$this->epo_internal_counter       = 0;
				$this->epo_internal_counter_check = array();
			}
		}

	}

	/**
	 * Display the options in the frontend
	 *
	 * @param int    $product_id
	 * @param string $form_prefix
	 * @param bool   $is_from_shortcode
	 * @param bool   $dummy_prefix
	 */
	public function tm_epo_fields( $product_id = 0, $form_prefix = "", $is_from_shortcode = FALSE, $dummy_prefix = FALSE ) {

		global $woocommerce;

		if ( ! property_exists( $woocommerce, 'product_factory' )
		     || $woocommerce->product_factory === NULL
		     || ( $this->tm_options_have_been_displayed && ( ! ( THEMECOMPLETE_EPO()->is_bto || ( ( THEMECOMPLETE_EPO()->is_enabled_shortcodes() && ! $is_from_shortcode ) && ! is_product() ) || ( ( is_shop() || is_product_category() || is_product_tag() ) && THEMECOMPLETE_EPO()->tm_epo_enable_in_shop == "yes" ) ) ) )
		) {
			return;// bad function call
		}

		if ($product_id instanceof WC_PRODUCT){
			$product = $product_id;
			$product_id = themecomplete_get_id($product);
		} else {
			if ( ! $product_id ) {
				global $product;
				if ( $product ) {
					$product_id = themecomplete_get_id( $product );
				}
			} else {
				$product = wc_get_product( $product_id );
			}
		}

		if ( ! $product_id || empty( $product ) ) {
			if ( ! empty( $this->current_product_id_to_be_displayed ) ) {
				$product_id = $this->current_product_id_to_be_displayed;
				$product    = wc_get_product( $product_id );
			} else {
				$this->tm_epo_fields_batch( $form_prefix, $dummy_prefix );

				return;
			}
		}

		if ( ! $product_id || empty( $product ) ) {
			return;
		}

		$type = themecomplete_get_product_type( $product );

		if ( $type === "grouped" ) {
			return;
		}

		// Always dispay composite hidden fields if product is composite
		if ( $form_prefix ) {
			$_bto_id     = $form_prefix;
			$form_prefix = "_" . $form_prefix;
			if ( THEMECOMPLETE_EPO()->is_bto ) {
				echo '<input type="hidden" class="cpf-bto-id" name="cpf_bto_id[]" value="' . esc_attr( $form_prefix ) . '" />';
				echo '<input type="hidden" value="" name="cpf_bto_price[' . esc_attr( $_bto_id ) . ']" class="cpf-bto-price" />';
				echo '<input type="hidden" value="0" name="cpf_bto_optionsprice[]" class="cpf-bto-optionsprice" />';
			}
		}

		$post_id = $product_id;

		$cpf_price_array = THEMECOMPLETE_EPO()->get_product_tm_epos( $post_id );

		if ( ! $cpf_price_array ) {
			return;
		}
		$global_price_array = $cpf_price_array['global'];
		$local_price_array  = $cpf_price_array['local'];
		if ( empty( $global_price_array ) && empty( $local_price_array ) ) {
			if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
				if ( empty( $this->epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] ) ) {
					// First time displaying the fields and totals havenn't been displayed
					$this->epo_internal_counter ++;
					$this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] = $this->epo_internal_counter;
				} else {
					// Totals have already been displayed
					unset( $this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] );

					$this->current_product_id_to_be_displayed = 0;
					$this->unique_form_prefix                 = '';
				}
				$_epo_internal_counter = $this->epo_internal_counter;
			} else {
				$_epo_internal_counter = 0;
			}

			return;
		}

		$global_prices = array( 'before' => array(), 'after' => array() );
		foreach ( $global_price_array as $priority => $priorities ) {
			foreach ( $priorities as $pid => $field ) {
				if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {
					foreach ( $field['sections'] as $section_id => $section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[ $section['sections_placement'] ][ $priority ][ $pid ]['sections'][ $section_id ] = $section;
						}
					}
				}
			}
		}

		$tabindex        = 0;
		$_currency       = get_woocommerce_currency_symbol();
		$unit_counter    = 0;
		$field_counter   = 0;
		$element_counter = 0;

		if ( ! $form_prefix ) {
			if ( THEMECOMPLETE_EPO()->is_quick_view() ) {
				if ( ! $this->unique_form_prefix ) {
					$this->unique_form_prefix = uniqid( '' );
				}
				$form_prefix = '_' . 'tcform' . $this->unique_form_prefix;
			}
		}

		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
			if ( empty( $this->epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] ) ) {
				// First time displaying the fields and totals havenn't been displayed
				$this->epo_internal_counter ++;
				$this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] = $this->epo_internal_counter;
			} else {
				// Totals have already been displayed
				unset( $this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] );

				$this->current_product_id_to_be_displayed = 0;
				$this->unique_form_prefix                 = '';
			}
			$_epo_internal_counter = $this->epo_internal_counter;
		} else {
			if (THEMECOMPLETE_EPO()->is_inline_epo && $this->epo_internal_counter){
				$_epo_internal_counter = $this->epo_internal_counter;
			} else {
				$_epo_internal_counter = 0;
			}
		}

		if ( ! $form_prefix ) {
			if ( THEMECOMPLETE_EPO()->wc_vars["is_page"] ) {
				$form_prefix = '_' . 'tcform' . $_epo_internal_counter;
			}
		}

		$forcart   = "main";
		$classcart = "tm-cart-main";
		if ( ! empty( $form_prefix ) ) {
			$forcart   = $form_prefix;
			$classcart = "tm-cart-" . str_replace( "_", "", $form_prefix );
		}
		$isfromshortcode = "";
		if ( ! empty( $is_from_shortcode ) ) {
			$isfromshortcode = " tc-shortcode";
		}

		global $wp_filter;
		$saved_filter = FALSE;
		if ( isset( $wp_filter['image_downsize'] ) ) {
			$saved_filter = $wp_filter['image_downsize'];
			unset( $wp_filter['image_downsize'] );
		}

		wc_get_template(
			'tm-start.php',
			array(
				'isfromshortcode'      => $isfromshortcode,
				'classcart'            => $classcart,
				'forcart'              => $forcart,
				'form_prefix'          => str_replace( "_", "", $form_prefix ),
				'product_id'           => $product_id,
				'epo_internal_counter' => $_epo_internal_counter,
				'is_from_shortcode'    => $is_from_shortcode,
			),
			$this->get_namespace(),
			THEMECOMPLETE_EPO_TEMPLATE_PATH
		);

		if ( ( ! empty( THEMECOMPLETE_EPO()->cart_edit_key ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'tm-edit' ) ) || ( ! empty( THEMECOMPLETE_EPO()->cart_edit_key ) && isset( $_REQUEST['update-composite'] ) ) ) {
			$_cart = WC()->cart;
			if ( isset( $_cart->cart_contents ) && isset( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ] ) ) {

				if ( ! empty( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata'] ) ) {
					if ( ! empty( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata']['tmcp_post_fields'] ) && is_array( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata']['tmcp_post_fields'] ) ) {
						$tmcp_post_fields = $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata']['tmcp_post_fields'];
						foreach ( $tmcp_post_fields as $posted_name => $posted_value ) {
							$_GET[ $posted_name ] = $posted_value;
						}
					}
				}

			}
		}

		// global options before local
		foreach ( $global_prices['before'] as $priorities ) {
			foreach ( $priorities as $field ) {
				$args    = array(
					'tabindex'        => $tabindex,
					'unit_counter'    => $unit_counter,
					'field_counter'   => $field_counter,
					'element_counter' => $element_counter,
					'_currency'       => $_currency,
					'product_id'      => $product_id,
				);
				$_return = $this->get_builder_display( $field, 'before', $args, $form_prefix, $dummy_prefix );
				extract( $_return, EXTR_OVERWRITE );
			}
		}

		$args    = array(
			'tabindex'        => $tabindex,
			'unit_counter'    => $unit_counter,
			'field_counter'   => $field_counter,
			'element_counter' => $element_counter,
			'_currency'       => $_currency,
			'product_id'      => $product_id,
		);
		$_return = $this->get_normal_display( $local_price_array, $args, $form_prefix, $dummy_prefix );
		extract( $_return, EXTR_OVERWRITE );

		// global options after local
		foreach ( $global_prices['after'] as $priorities ) {
			foreach ( $priorities as $field ) {
				$args    = array(
					'tabindex'        => $tabindex,
					'unit_counter'    => $unit_counter,
					'field_counter'   => $field_counter,
					'element_counter' => $element_counter,
					'_currency'       => $_currency,
					'product_id'      => $product_id,
				);
				$_return = $this->get_builder_display( $field, 'after', $args, $form_prefix, $dummy_prefix );
				extract( $_return, EXTR_OVERWRITE );
			}
		}

		wc_get_template(
			'tm-end.php',
			array(),
			$this->get_namespace(),
			THEMECOMPLETE_EPO_TEMPLATE_PATH
		);

		if ( $saved_filter ) {
			$wp_filter['image_downsize'] = $saved_filter;
		}

		$this->tm_options_single_have_been_displayed = TRUE;

	}

	/**
	 * Displays the option created from the builder mode
	 *
	 * @since 1.0
	 */
	public function get_builder_display( $field, $where, $args, $form_prefix = "", $dummy_prefix = FALSE ) {

		// $form_prefix	shoud be passed with _ if not empty 

		$columns = array(

			"w1"    => array( "tcwidth-1", 1 ),
			"w2"    => array( "tcwidth-2", 2 ),
			"w3"    => array( "tcwidth-3", 3 ),
			"w4"    => array( "tcwidth-4", 4 ),
			"w5"    => array( "tcwidth-5", 5 ),
			"w6"    => array( "tcwidth-6", 6 ),
			"w7"    => array( "tcwidth-7", 7 ),
			"w8"    => array( "tcwidth-8", 8 ),
			"w9"    => array( "tcwidth-9", 9 ),
			"w10"   => array( "tcwidth-10", 10 ),
			"w11"   => array( "tcwidth-11", 11 ),
			"w12"   => array( "tcwidth-12", 12 ),
			"w12-5" => array( "tcwidth-12-5", 12.5 ),
			"w13"   => array( "tcwidth-13", 13 ),
			"w14"   => array( "tcwidth-14", 14 ),
			"w15"   => array( "tcwidth-15", 15 ),
			"w16"   => array( "tcwidth-16", 16 ),
			"w17"   => array( "tcwidth-17", 17 ),
			"w18"   => array( "tcwidth-18", 18 ),
			"w19"   => array( "tcwidth-19", 19 ),
			"w20"   => array( "tcwidth-20", 20 ),
			"w21"   => array( "tcwidth-21", 21 ),
			"w22"   => array( "tcwidth-22", 22 ),
			"w23"   => array( "tcwidth-23", 23 ),
			"w24"   => array( "tcwidth-24", 24 ),
			"w25"   => array( "tcwidth-25", 25 ),
			"w26"   => array( "tcwidth-26", 26 ),
			"w27"   => array( "tcwidth-27", 27 ),
			"w28"   => array( "tcwidth-28", 28 ),
			"w29"   => array( "tcwidth-29", 29 ),
			"w30"   => array( "tcwidth-30", 30 ),
			"w31"   => array( "tcwidth-31", 31 ),
			"w32"   => array( "tcwidth-32", 32 ),
			"w33"   => array( "tcwidth-33", 33 ),
			"w34"   => array( "tcwidth-34", 34 ),
			"w35"   => array( "tcwidth-35", 35 ),
			"w36"   => array( "tcwidth-36", 36 ),
			"w37"   => array( "tcwidth-37", 37 ),
			"w37-5" => array( "tcwidth-37-5", 37.5 ),
			"w38"   => array( "tcwidth-38", 38 ),
			"w39"   => array( "tcwidth-39", 39 ),
			"w40"   => array( "tcwidth-40", 40 ),
			"w41"   => array( "tcwidth-41", 41 ),
			"w42"   => array( "tcwidth-42", 42 ),
			"w43"   => array( "tcwidth-43", 43 ),
			"w44"   => array( "tcwidth-44", 44 ),
			"w45"   => array( "tcwidth-45", 45 ),
			"w46"   => array( "tcwidth-46", 46 ),
			"w47"   => array( "tcwidth-47", 47 ),
			"w48"   => array( "tcwidth-48", 48 ),
			"w49"   => array( "tcwidth-49", 49 ),
			"w50"   => array( "tcwidth-50", 50 ),
			"w51"   => array( "tcwidth-51", 51 ),
			"w52"   => array( "tcwidth-52", 52 ),
			"w53"   => array( "tcwidth-53", 53 ),
			"w54"   => array( "tcwidth-54", 54 ),
			"w55"   => array( "tcwidth-55", 55 ),
			"w56"   => array( "tcwidth-56", 56 ),
			"w57"   => array( "tcwidth-57", 57 ),
			"w58"   => array( "tcwidth-58", 58 ),
			"w59"   => array( "tcwidth-59", 59 ),
			"w60"   => array( "tcwidth-60", 60 ),
			"w61"   => array( "tcwidth-61", 61 ),
			"w62"   => array( "tcwidth-62", 62 ),
			"w62-5" => array( "tcwidth-62-5", 62.5 ),
			"w63"   => array( "tcwidth-63", 63 ),
			"w64"   => array( "tcwidth-64", 64 ),
			"w65"   => array( "tcwidth-65", 65 ),
			"w66"   => array( "tcwidth-66", 66 ),
			"w67"   => array( "tcwidth-67", 67 ),
			"w68"   => array( "tcwidth-68", 68 ),
			"w69"   => array( "tcwidth-69", 69 ),
			"w70"   => array( "tcwidth-70", 70 ),
			"w71"   => array( "tcwidth-71", 71 ),
			"w72"   => array( "tcwidth-72", 72 ),
			"w73"   => array( "tcwidth-73", 73 ),
			"w74"   => array( "tcwidth-74", 74 ),
			"w75"   => array( "tcwidth-75", 75 ),
			"w76"   => array( "tcwidth-76", 76 ),
			"w77"   => array( "tcwidth-77", 77 ),
			"w78"   => array( "tcwidth-78", 78 ),
			"w79"   => array( "tcwidth-79", 79 ),
			"w80"   => array( "tcwidth-80", 80 ),
			"w81"   => array( "tcwidth-81", 81 ),
			"w82"   => array( "tcwidth-82", 82 ),
			"w83"   => array( "tcwidth-83", 83 ),
			"w84"   => array( "tcwidth-84", 84 ),
			"w85"   => array( "tcwidth-85", 85 ),
			"w86"   => array( "tcwidth-86", 86 ),
			"w87"   => array( "tcwidth-87", 87 ),
			"w87-5" => array( "tcwidth-86-5", 87.5 ),
			"w88"   => array( "tcwidth-88", 88 ),
			"w89"   => array( "tcwidth-89", 89 ),
			"w90"   => array( "tcwidth-90", 90 ),
			"w91"   => array( "tcwidth-91", 91 ),
			"w92"   => array( "tcwidth-92", 92 ),
			"w93"   => array( "tcwidth-93", 93 ),
			"w94"   => array( "tcwidth-94", 94 ),
			"w95"   => array( "tcwidth-95", 95 ),
			"w96"   => array( "tcwidth-96", 96 ),
			"w97"   => array( "tcwidth-97", 97 ),
			"w98"   => array( "tcwidth-98", 98 ),
			"w99"   => array( "tcwidth-99", 99 ),
			"w100"  => array( "tcwidth-100", 100 ),

		);

		$tabindex        = $args['tabindex'];
		$unit_counter    = $args['unit_counter'];
		$field_counter   = $args['field_counter'];
		$element_counter = $args['element_counter'];
		$_currency       = $args['_currency'];
		$product_id      = $args['product_id'];

		$element_type_counter = array();


		if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {

			$args = array(
				'field_id' => 'tm-epo-field-' . $unit_counter,
			);
			wc_get_template(
				'tm-builder-start.php',
				$args,
				$this->get_namespace(),
				THEMECOMPLETE_EPO_TEMPLATE_PATH
			);

			$_section_totals = 0;

			foreach ( $field['sections'] as $section ) {
				if ( ! isset( $section['sections_placement'] ) || $section['sections_placement'] != $where ) {
					continue;
				}
				if ( isset( $section['sections_size'] ) && isset( $columns[ $section['sections_size'] ] ) ) {
					$size = $columns[ $section['sections_size'] ][0];
				} else {
					$size = "tcwidth-100";
				}

				$_section_totals = $_section_totals + $columns[ $section['sections_size'] ][1];
				if ( $_section_totals > 100 ) {
					$_section_totals = $columns[ $section['sections_size'] ][1];
				}

				$divider = isset( $section['divider_type'] ) ? $section['divider_type'] : "";

				$label_size = 'h3';
				if ( ! empty( $section['label_size'] ) ) {
					switch ( $section['label_size'] ) {
						case "1":
							$label_size = 'h1';
							break;
						case "2":
							$label_size = 'h2';
							break;
						case "3":
							$label_size = 'h3';
							break;
						case "4":
							$label_size = 'h4';
							break;
						case "5":
							$label_size = 'h5';
							break;
						case "6":
							$label_size = 'h6';
							break;
						case "7":
							$label_size = 'p';
							break;
						case "8":
							$label_size = 'div';
							break;
						case "9":
							$label_size = 'span';
							break;
					}
				}

				$args = array(
					'column'               => $size,
					'style'                => $section['sections_style'],
					'uniqid'               => $section['sections_uniqid'],
					'logic'                => wp_json_encode( (array) json_decode( stripslashes_deep( $section['sections_clogic'] ) ) ),
					'haslogic'             => $section['sections_logic'],
					'sections_class'       => $section['sections_class'],
					'sections_type'        => $section['sections_type'],
					'title_size'           => $label_size,
					'title'                => ! empty( $section['label'] ) ? $section['label'] : "",
					'title_color'          => ! empty( $section['label_color'] ) ? $section['label_color'] : "",
					'title_position'       => ! empty( $section['label_position'] ) ? $section['label_position'] : "",
					'description'          => ! empty( $section['description'] ) ? $section['description'] : "",
					'description_color'    => ! empty( $section['description_color'] ) ? $section['description_color'] : "",
					'description_position' => ! empty( $section['description_position'] ) ? $section['description_position'] : "",
					'divider'              => $divider,
				);

				// custom variations check
				if (
					isset( $section['elements'] )
					&& is_array( $section['elements'] )
					&& isset( $section['elements'][0] )
					&& is_array( $section['elements'][0] )
					&& isset( $section['elements'][0]['type'] )
					&& $section['elements'][0]['type'] == 'variations'
				) {
					$args['sections_class'] = $args['sections_class'] . " tm-epo-variation-section tc-clearfix";
					if (
						isset( $section['elements'][0]['builder'] ) &&
						isset( $section['elements'][0]['builder']['variations_disabled'] ) &&
						$section['elements'][0]['builder']['variations_disabled'] == 1
					) {
						$args['sections_class'] .= ' tm-hidden';
					}
				}
				wc_get_template(
					'tm-builder-section-start.php',
					$args,
					$this->get_namespace(),
					THEMECOMPLETE_EPO_TEMPLATE_PATH
				);

				if ( isset( $section['elements'] ) && is_array( $section['elements'] ) ) {
					$totals = 0;

					$slide_counter = 0;
					$use_slides    = FALSE;
					$doing_slides  = FALSE;
					if ( $section['sections_slides'] !== "" && $section['sections_type'] == "slider" ) {
						$sections_slides = explode( ",", $section['sections_slides'] );
						$use_slides      = TRUE;
					}

					foreach ( $section['elements'] as $element ) {

						$element = apply_filters( 'wc_epo_get_element_for_display', $element );

						$empty_rules = "";
						if ( isset( $element['rules_filtered'] ) ) {
							$empty_rules = wp_json_encode( ( $element['rules_filtered'] ) );
						}
						$empty_original_rules = "";
						if ( isset( $element['original_rules_filtered'] ) ) {
							$empty_original_rules = wp_json_encode( ( $element['original_rules_filtered'] ) );
						}
						if ( empty( $empty_original_rules ) ) {
							$empty_original_rules = "";
						}
						$empty_rules_type = "";
						if ( isset( $element['rules_type'] ) ) {
							$empty_rules_type = wp_json_encode( ( $element['rules_type'] ) );
						}
						if ( isset( $element['size'] ) && isset( $columns[ $element['size'] ] ) ) {
							$size = $columns[ $element['size'] ][0];
						} else {
							$size = "tcwidth-100";
						}
						$test_for_first_slide = FALSE;
						if ( $use_slides && isset( $sections_slides[ $slide_counter ] ) ) {
							$sections_slides[ $slide_counter ] = intval( $sections_slides[ $slide_counter ] );

							if ( $sections_slides[ $slide_counter ] > 0 && ! $doing_slides ) {
								echo '<div class="tm-slide tc-row tc-cell tcwidth-100">';
								$doing_slides         = TRUE;
								$test_for_first_slide = TRUE;
							}
						}

						$cart_fee_name = THEMECOMPLETE_EPO()->cart_fee_name;
						$totals        = $totals + $columns[ $element['size'] ][1];
						if ( $totals > 100 && ! $test_for_first_slide ) {
							$totals = $columns[ $element['size'] ][1];
						}

						$divider       = isset( $element['divider_type'] ) ? $element['divider_type'] : "";
						$divider_class = "";
						if ( isset( $element['divider_type'] ) ) {
							$divider_class = "";
							if ( $element['type'] == 'divider' && ! empty( $element['class'] ) ) {
								$divider_class = " " . $element['class'];
							}
						}
						$label_size = 'h3';
						if ( ! empty( $element['label_size'] ) ) {
							switch ( $element['label_size'] ) {
								case "1":
									$label_size = 'h1';
									break;
								case "2":
									$label_size = 'h2';
									break;
								case "3":
									$label_size = 'h3';
									break;
								case "4":
									$label_size = 'h4';
									break;
								case "5":
									$label_size = 'h5';
									break;
								case "6":
									$label_size = 'h6';
									break;
								case "7":
									$label_size = 'p';
									break;
								case "8":
									$label_size = 'div';
									break;
								case "9":
									$label_size = 'span';
									break;
								case "10":
									$label_size = 'label';
									break;
							}
						}

						$variations_builder_element_start_args = array();
						$tm_validation                         = $this->get_tm_validation_rules( $element );
						$args                                  = apply_filters( 'wc_epo_builder_element_start_args', array(
							'tm_element_settings'  => $element,
							'column'               => $size,
							'class'                => ! empty( $element['class'] ) ? $element['class'] : "",
							'container_id'         => ! empty( $element['container_id'] ) ? $element['container_id'] : "",
							'title_size'           => $label_size,
							'title'                => ! empty( $element['label'] ) ? $element['label'] : "",
							'title_position'       => ! empty( $element['label_position'] ) ? $element['label_position'] : "",
							'title_color'          => ! empty( $element['label_color'] ) ? $element['label_color'] : "",
							'description'          => ! empty( $element['description'] ) ? $element['description'] : "",
							'description_color'    => ! empty( $element['description_color'] ) ? $element['description_color'] : "",
							'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : "",
							'divider'              => $divider,
							'divider_class'        => $divider_class,
							'required'             => $element['required'],
							'type'                 => $element['type'],
							'use_images'           => $element['use_images'],
							'use_colors'           => $element['use_colors'],
							'use_url'              => $element['use_url'],
							'enabled'              => $element['enabled'],
							'rules'                => $empty_rules,
							'original_rules'       => $empty_original_rules,
							'rules_type'           => $empty_rules_type,
							'element'              => $element['type'],
							'class_id'             => "tm-element-ul-" . $element['type'] . " element_" . $element_counter . $form_prefix,// this goes on ul
							'uniqid'               => $element['uniqid'],
							'logic'                => wp_json_encode( (array) json_decode( stripslashes_deep( $element['clogic'] ) ) ),
							'haslogic'             => $element['logic'],
							'clear_options'        => empty( $element['clear_options'] ) ? "" : $element['clear_options'],
							'limit'                => empty( $element['limit'] ) ? "" : 'tm-limit',
							'exactlimit'           => empty( $element['exactlimit'] ) ? "" : 'tm-exactlimit',
							'minimumlimit'         => empty( $element['minimumlimit'] ) ? "" : 'tm-minimumlimit',
							'tm_validation'        => wp_json_encode( ( $tm_validation ) ),
							'extra_class'          => '',
						), $element, $element_counter, $form_prefix );

						if ( $element['type'] === "product" ) {
							$args['extra_class']       = "cpf-type-product-" . $element['layout_mode'];
							$args['element_data_attr'] = array(
								"data-product-layout-mode" => $element['layout_mode'],
								"data-quantity-min"        => $element['quantity_min'],
								"data-quantity-max"        => $element['quantity_max'],
								"data-priced-individually" => $element['priced_individually'],
								"data-discount"            => $element['discount'],
								"data-discount-type"       => $element['discount_type'],
							);
							if ($element['mode'] !== "product"){
								if ($element['layout_mode'] === "radio" || $element['layout_mode'] === "thumbnail"){
									$args['clear_options'] = "clear";
								}
							}
						}
						if ( $element['type'] != "variations" ) {
							if ($element['enabled']){
								wc_get_template(
									'tm-builder-element-start.php',
									$args,
									$this->get_namespace(),
									THEMECOMPLETE_EPO_TEMPLATE_PATH
								);
							}
						} else {
							$variations_builder_element_start_args = $args;
						}

						if ( ( $element['type'] != "variations" && $element['enabled'] ) || $element['type'] === "variations" ) {

							$field_counter = 0;

							$init_class = "THEMECOMPLETE_EPO_FIELDS_" . $element['type'];
							if ( ! class_exists( $init_class ) && ! empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["_is_addon"] ) ) {
								$init_class = "THEMECOMPLETE_EPO_FIELDS";
							}

							if ( isset( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ] )
							     && ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["is_post"] == "post" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["is_post"] == "display" )
							     && class_exists( $init_class )
							) {

								$field_obj = new $init_class();

								if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["is_post"] == "post" ) {

									if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "single" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multipleallsingle" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multiplesingle" ) {

										$tabindex ++;
										$name_inc      = THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["post_name_prefix"] . "_" . $element_counter . ( $dummy_prefix ? "" : ( ( $form_prefix !== "" ) ? "_" . str_replace( "_", "", $form_prefix ) : "" ) );
										$base_name_inc = $name_inc;

										$is_cart_fee = ! empty( $element['is_cart_fee'] );

										if ( $is_cart_fee ) {
											$name_inc = $cart_fee_name . $base_name_inc;
										}

										$name_inc = apply_filters( 'wc_epo_name_inc', $name_inc, $base_name_inc, $element, FALSE, FALSE, FALSE );

										do_action( 'wc_epo_get_builder_display_single', $element, $name_inc, FALSE );

										$fieldtype = 'tmcp-field';
										if ( $is_cart_fee ) {
											$fieldtype = THEMECOMPLETE_EPO()->cart_fee_class;
										}
										if ( ! empty( $element['class'] ) ) {
											$fieldtype .= " " . $element['class'];
										}

										if (THEMECOMPLETE_EPO()->get_element_price_type("", $element, 0, 1, 0) === "math"){
											$fieldtype .= " tc-is-math";
										}

										$uniqid_suffix = uniqid();

										$display = $field_obj->display_field( $element, array(
											'id'              => 'tmcp_' . THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["post_name_prefix"] . '_' . $tabindex . $form_prefix . $uniqid_suffix,
											'name'            => 'tmcp_' . $name_inc,
											'name_inc'        => $name_inc,
											'element_counter' => $element_counter,
											'tabindex'        => $tabindex,
											'form_prefix'     => $form_prefix,
											'fieldtype'       => $fieldtype,
											'field_counter'   => $field_counter,
											'product_id'      => isset( $product_id ) ? $product_id : 0,
										) );

										if ( is_array( $display ) ) {

											$original_amount = "";
											if ( isset( $element['original_rules_filtered'][0] ) && isset( $element['original_rules_filtered'][0][0] ) ) {
												$original_amount = $element['original_rules_filtered'][0][0];
											} else {
												$selected_index = 0;
												if ( isset( $element["default_value"] ) && $element["default_value"] !== "" ) {
													$selected_index = array_keys( $element["options"] );
													$selected_index = $selected_index[ $element["default_value"] ];
												} else {
													$selected_index = array_keys( $element["options"] );
													$selected_index = $selected_index[0];
												}
												$original_amount = $element['original_rules_filtered'][ esc_attr( $selected_index ) ];
												if ( isset( $original_amount[0] ) ) {
													$original_amount = $original_amount[0];
												} else {
													$original_amount = "";
												}
											}
											if ( isset( $display["default_value_counter"] ) && $display["default_value_counter"] !== FALSE ) {
												$original_amount = $element['original_rules_filtered'][ $display['default_value_counter'] ][0];
											}

											$amount = "";
											if ( isset( $element['rules_filtered'][0] ) && isset( $element['rules_filtered'][0][0] ) ) {
												$amount = $element['rules_filtered'][0][0];
											} else {
												$selected_index = 0;
												if ( isset( $element["default_value"] ) && $element["default_value"] !== "" ) {
													$selected_index = array_keys( $element["options"] );
													$selected_index = $selected_index[ $element["default_value"] ];
												} else {
													$selected_index = array_keys( $element["options"] );
													$selected_index = $selected_index[0];
												}
												$amount = $element['rules_filtered'][ esc_attr( $selected_index ) ];
												if ( isset( $amount[0] ) ) {
													$amount = $amount[0];
												} else {
													$amount = "";
												}
											}
											if ( isset( $display["default_value_counter"] ) && $display["default_value_counter"] !== FALSE ) {
												$amount = $element['rules_filtered'][ $display['default_value_counter'] ][0];
											}

											$original_rules = isset( $element['original_rules_filtered'] ) ? wp_json_encode( ( $element['original_rules_filtered'] ) ) : '';
											if ( empty( $original_rules ) ) {
												$original_rules = "";
											}

											$args = array(
												'id'              => 'tmcp_' . THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["post_name_prefix"] . '_' . $tabindex . $form_prefix . $uniqid_suffix,
												'name'            => 'tmcp_' . $name_inc,
												'amount'          => $amount . ' ' . $_currency,
												'original_amount' => $original_amount . ' ' . $_currency,
												'tabindex'        => $tabindex,
												'fieldtype'       => $fieldtype,
												'rules'           => isset( $element['rules_filtered'] ) ? wp_json_encode( ( $element['rules_filtered'] ) ) : '',
												'original_rules'  => $original_rules,
												'rules_type'      => isset( $element['rules_type'] ) ? wp_json_encode( ( $element['rules_type'] ) ) : '',
												'tm_element_settings' => $element,
												'class'               => ! empty( $element['class'] ) ? $element['class'] : "",
												'field_counter'       => $field_counter,
												'tax_obj'             => ! $is_cart_fee ? FALSE : wp_json_encode( ( array(
													'is_fee'    => $is_cart_fee,
													'has_fee'   => isset( $element['include_tax_for_fee_price_type'] ) ? $element['include_tax_for_fee_price_type'] : '',
													'tax_class' => isset( $element['tax_class_for_fee_price_type'] ) ? $element['tax_class_for_fee_price_type'] : '',
													'tax_rate'  => $this->get_tax_rate( isset( $element['tax_class_for_fee_price_type'] ) ? $element['tax_class_for_fee_price_type'] : '' ),
												) ) ),
											);

											$args         = apply_filters( 'wc_epo_display_template_args', array_merge( $args, $display ), $element, FALSE, FALSE, FALSE );
											$args['args'] = $args;
											if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["_is_addon"] ) {
												do_action( "tm_epo_display_addons", $element, $args, array(
													'name_inc'        => $name_inc,
													'element_counter' => $element_counter,
													'tabindex'        => $tabindex,
													'form_prefix'     => $form_prefix,
													'field_counter'   => $field_counter
												), THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["namespace"] );
											} elseif ( is_readable( apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, $element['type'], $element ) . apply_filters( 'wc_epo_template_element', 'tm-' . $element['type'] . '.php', $element['type'], $element ) ) ) {
												wc_get_template(
													apply_filters( 'wc_epo_template_element', 'tm-' . $element['type'] . '.php', $element['type'], $element ),
													$args,
													$this->get_namespace(),
													apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, $element['type'], $element )
												);
											}
										}

									} elseif ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multipleall" || THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multiple" ) {

										$field_obj->display_field_pre( $element, array(
											'element_counter' => $element_counter,
											'tabindex'        => $tabindex,
											'form_prefix'     => $form_prefix,
											'field_counter'   => $field_counter,
											'product_id'      => isset( $product_id ) ? $product_id : 0,
										) );

										$choice_counter = 0;
										
										if (!isset($element_type_counter[ $element['type'] ])){
											$element_type_counter[ $element['type'] ] = 0;
										}
										
										foreach ( $element['options'] as $value => $label ) {

											$tabindex ++;
											if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["type"] == "multipleall" ) {
												$name_inc = THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["post_name_prefix"] . "_" . $element_counter . "_" . $field_counter . ( $dummy_prefix ? "" : ( ( $form_prefix !== "" ) ? "_" . str_replace( "_", "", $form_prefix ) : "" ) );
											} else {
												$name_inc = THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["post_name_prefix"] . "_" . $element_counter . ( $dummy_prefix ? "" : ( ( $form_prefix !== "" ) ? "_" . str_replace( "_", "", $form_prefix ) : "" ) );
											}
											$base_name_inc = $name_inc;

											$is_cart_fee = ! empty( $element['is_cart_fee_multiple'][ $field_counter ] );
											if ( $is_cart_fee ) {
												$name_inc = $cart_fee_name . $name_inc;
											}

											$name_inc = apply_filters( 'wc_epo_name_inc', $name_inc, $base_name_inc, $element, $value, $choice_counter, $element_type_counter[ $element['type'] ] );

											do_action( 'wc_epo_get_builder_display_single', $element, $name_inc, $value );

											if ( ! empty( THEMECOMPLETE_EPO()->cart_edit_key ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'tm-edit' ) ) {
												$_cart = WC()->cart;
												if ( isset( $_cart->cart_contents ) && isset( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ] ) ) {
													if ( ! empty( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartepo'] ) ) {
														$saved_epos = $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartepo'];
														foreach ( $saved_epos as $key => $val ) {
															if ( $element['uniqid'] == $val["section"] && $value == $val["key"] ) {
																$_GET[ 'tmcp_' . $name_inc ] = $val["key"];
																if ( isset( $val['quantity'] ) ) {
																	$_GET[ 'tmcp_' . $name_inc . '_quantity' ] = $val['quantity'];
																}
															}
														}
													}
													if ( ! empty( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartfee'] ) ) {
														$saved_fees = $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartfee'];
														foreach ( $saved_fees as $key => $val ) {
															if ( $element['uniqid'] == $val["section"] && $value == $val["key"] ) {
																$_GET[ 'tmcp_' . $name_inc ] = $val["key"];
																if ( isset( $val['quantity'] ) ) {
																	$_GET[ 'tmcp_' . $name_inc . '_quantity' ] = $val['quantity'];
																}
															}
														}
													}
												}
											}

											$fieldtype = 'tmcp-field';
											if ( $is_cart_fee ) {
												$fieldtype = THEMECOMPLETE_EPO()->cart_fee_class;
											}
											if ( ! empty( $element['class'] ) ) {
												$fieldtype .= " " . $element['class'];
											}

											if (THEMECOMPLETE_EPO()->get_element_price_type("", $element, $value, 1, 0) === "math"){
												$fieldtype .= " tc-is-math";
											}

											$uniqid_suffix = uniqid();

											$display = $field_obj->display_field( $element, array(
												'id'              => 'tmcp_' . THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["post_name_prefix"] . '_' . $element_counter . "_" . $field_counter . "_" . $tabindex . $form_prefix . $uniqid_suffix,
												'name'            => 'tmcp_' . $name_inc,
												'name_inc'        => $name_inc,
												'value'           => $value,
												'label'           => $label,
												'element_counter' => $element_counter,
												'tabindex'        => $tabindex,
												'form_prefix'     => $form_prefix,
												'fieldtype'       => $fieldtype,
												'border_type'     => THEMECOMPLETE_EPO()->tm_epo_css_selected_border,
												'field_counter'   => $field_counter,
												'product_id'      => isset( $product_id ) ? $product_id : 0,
											) );

											if ( is_array( $display ) ) {

												$original_amount = $element['original_rules_filtered'][ $value ][0];

												$amount = $element['rules_filtered'][ $value ][0];

												$original_rules = isset( $element['original_rules_filtered'][ $value ] ) ? wp_json_encode( ( $element['original_rules_filtered'][ $value ] ) ) : '';
												if ( empty( $original_rules ) ) {
													$original_rules = "";
												}

												$args = array(
													'id'              => 'tmcp_' . THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["post_name_prefix"] . '_' . $element_counter . "_" . $field_counter . "_" . $tabindex . $form_prefix . $uniqid_suffix,
													'name'            => 'tmcp_' . $name_inc,
													'amount'          => $amount . ' ' . $_currency,
													'original_amount' => $original_amount . ' ' . $_currency,
													'tabindex'        => $tabindex,
													'fieldtype'       => $fieldtype,
													'rules'           => isset( $element['rules_filtered'][ $value ] ) ? wp_json_encode( ( $element['rules_filtered'][ $value ] ) ) : '',
													'original_rules'  => $original_rules,
													'rules_type'      => isset( $element['rules_type'][ $value ] ) ? wp_json_encode( ( $element['rules_type'][ $value ] ) ) : '',
													'border_type' => THEMECOMPLETE_EPO()->tm_epo_css_selected_border,
													'tm_element_settings' => $element,
													'class'               => ! empty( $element['class'] ) ? $element['class'] : "",
													'field_counter'       => $field_counter,
													'tax_obj'             => ! $is_cart_fee ? FALSE : wp_json_encode( ( array(
														'is_fee'    => $is_cart_fee,
														'has_fee'   => isset( $element['include_tax_for_fee_price_type'] ) ? $element['include_tax_for_fee_price_type'] : '',
														'tax_class' => isset( $element['tax_class_for_fee_price_type'] ) ? $element['tax_class_for_fee_price_type'] : '',
														'tax_rate'  => $this->get_tax_rate( isset( $element['tax_class_for_fee_price_type'] ) ? $element['tax_class_for_fee_price_type'] : '' ),
													) ) ),
												);

												$args = apply_filters( 'wc_epo_display_template_args', array_merge( $args, $display ), $element, $value, $choice_counter, $element_type_counter[ $element['type'] ] );

												if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["_is_addon"] ) {
													do_action( "tm_epo_display_addons", $element, $args, array(
														'name_inc'        => $name_inc,
														'element_counter' => $element_counter,
														'tabindex'        => $tabindex,
														'form_prefix'     => $form_prefix,
														'field_counter'   => $field_counter,
														'border_type'     => THEMECOMPLETE_EPO()->tm_epo_css_selected_border
													), THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["namespace"] );
												} elseif ( is_readable( apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, $element['type'], $element ) . apply_filters( 'wc_epo_template_element', 'tm-' . $element['type'] . '.php', $element['type'], $element ) ) ) {
													wc_get_template(
														apply_filters( 'wc_epo_template_element', 'tm-' . $element['type'] . '.php', $element['type'], $element ),
														$args,
														$this->get_namespace(),
														apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, $element['type'], $element )
													);
												}
											}

											$choice_counter ++;

											$element_type_counter[ $element['type'] ] ++;

											$field_counter ++;

										}

									}

									$element_counter ++;

								} elseif ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["is_post"] == "display" ) {

									$display = $field_obj->display_field( $element, array(
										'product_id'      => $product_id,
										'element_counter' => $element_counter,
										'tabindex'        => $tabindex,
										'form_prefix'     => $form_prefix,
										'field_counter'   => $field_counter,
										'args'            => $args,
										'product_id'      => isset( $product_id ) ? $product_id : 0,
									) );

									if ( is_array( $display ) ) {
										$args = array(
											'tm_element_settings' => $element,
											'class'               => ! empty( $element['class'] ) ? $element['class'] : "",
											'form_prefix'         => $form_prefix,
											'field_counter'       => $field_counter,
											'tm_element'          => $element,
											'tm__namespace'       => $this->get_namespace(),
											'tm_template_path'    => THEMECOMPLETE_EPO_TEMPLATE_PATH,
											'tm_product_id'       => $product_id,
										);

										if ( $element['type'] == "variations" ) {
											$args["variations_builder_element_start_args"] = $variations_builder_element_start_args;
											$args["variations_builder_element_end_args"]   = array(
												'tm_element_settings'  => $element,
												'element'              => $element['type'],
												'description'          => ! empty( $element['description'] ) ? $element['description'] : "",
												'description_color'    => ! empty( $element['description_color'] ) ? $element['description_color'] : "",
												'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : "",
											);

										}

										$args = array_merge( $args, $display );

										if ( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["_is_addon"] ) {
											do_action( "tm_epo_display_addons", $element, $args, array(
												'name_inc'        => '',
												'element_counter' => $element_counter,
												'tabindex'        => $tabindex,
												'form_prefix'     => $form_prefix,
												'field_counter'   => $field_counter
											), THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]["namespace"] );
										} elseif ( is_readable( apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, $element['type'], $element ) . apply_filters( 'wc_epo_template_element', 'tm-' . $element['type'] . '.php', $element['type'], $element ) ) ) {
											wc_get_template(
												apply_filters( 'wc_epo_template_element', 'tm-' . $element['type'] . '.php', $element['type'], $element ),
												$args,
												$this->get_namespace(),
												apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, $element['type'], $element )
											);
										}
									}
								}

								unset( $field_obj ); // clear memory
							}

						}

						if ( $element['type'] != "variations" ) {
							if ($element['enabled']){
								wc_get_template(
									'tm-builder-element-end.php',
									array(
										'tm_element_settings'  => $element,
										'element'              => $element['type'],
										'enabled'              => $element['enabled'],
										'description'          => ! empty( $element['description'] ) ? $element['description'] : "",
										'description_color'    => ! empty( $element['description_color'] ) ? $element['description_color'] : "",
										'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : "",
									),
									$this->get_namespace(),
									THEMECOMPLETE_EPO_TEMPLATE_PATH
								);
							}
						}

						if ( $use_slides && isset( $sections_slides[ $slide_counter ] ) ) {
							$sections_slides[ $slide_counter ] = $sections_slides[ $slide_counter ] - 1;

							if ( $sections_slides[ $slide_counter ] <= 0 ) {
								echo '</div>';
								$slide_counter ++;
								$doing_slides = FALSE;
							}
						}

					}
				}
				$args = array(
					'column'               => $size,
					'style'                => $section['sections_style'],
					'sections_type'        => $section['sections_type'],
					'title_size'           => $label_size,
					'title'                => ! empty( $section['label'] ) ? $section['label'] : "",
					'title_color'          => ! empty( $section['label_color'] ) ? $section['label_color'] : "",
					'description'          => ! empty( $section['description'] ) ? $section['description'] : "",
					'description_color'    => ! empty( $section['description_color'] ) ? $section['description_color'] : "",
					'description_position' => ! empty( $section['description_position'] ) ? $section['description_position'] : "",
				);
				wc_get_template(
					'tm-builder-section-end.php',
					$args,
					$this->get_namespace(),
					THEMECOMPLETE_EPO_TEMPLATE_PATH
				);

			}

			wc_get_template(
				'tm-builder-end.php',
				array(),
				$this->get_namespace(),
				THEMECOMPLETE_EPO_TEMPLATE_PATH
			);

			$unit_counter ++;

		}

		return array(
			'tabindex'        => $tabindex,
			'unit_counter'    => $unit_counter,
			'field_counter'   => $field_counter,
			'element_counter' => $element_counter,
			'_currency'       => $_currency,
		);

	}

	/**
	 * Displays the option created from the normal (local) mode
	 *
	 * @since 4.8
	 *
	 * @param int    $product_id
	 * @param string $form_prefix
	 * @param bool   $is_from_shortcode
	 */
	public function get_normal_display( $local_price_array = array(), $args = array(), $form_prefix, $dummy_prefix ) {

		$tabindex        = $args['tabindex'];
		$unit_counter    = $args['unit_counter'];
		$field_counter   = $args['field_counter'];
		$element_counter = $args['element_counter'];
		$_currency       = $args['_currency'];
		$product_id      = $args['product_id'];

		$form_prefix_onform = $form_prefix !== "" ? "_" . str_replace( "_", "", $form_prefix ) : "";

		// Normal (local) options
		if ( is_array( $local_price_array ) && sizeof( $local_price_array ) > 0 ) {

			$attributes      = themecomplete_get_attributes( floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $product_id ) ) );
			$wpml_attributes = themecomplete_get_attributes( $product_id );

			$fieldtype = "tmcp-field";

			if ( is_array( $attributes ) && count( $attributes ) > 0 ) {
				foreach ( $local_price_array as $field ) {
					if ( isset( $field['name'] ) && isset( $attributes[ $field['name'] ] ) && ! $attributes[ $field['name'] ]['is_variation'] ) {

						$attribute      = $attributes[ $field['name'] ];
						$wpml_attribute = isset( $wpml_attributes[ $field['name'] ] ) ? $wpml_attributes[ $field['name'] ] : array();

						$empty_rules = "";
						if ( isset( $field['rules_filtered'][0] ) ) {
							$empty_rules = wp_json_encode( ( $field['rules_filtered'][0] ) );
						}
						if ( empty( $empty_rules ) ) {
							$empty_rules = "";
						}
						$empty_rules_type = "";
						if ( isset( $field['rules_type'][0] ) ) {
							$empty_rules_type = wp_json_encode( ( $field['rules_type'][0] ) );
						}

						$args = array(
							'title'          => ( ! $attribute['is_taxonomy'] && isset( $attributes[ $field['name'] ]["name"] ) )
								? wc_attribute_label( $attributes[ $field['name'] ]["name"] )
								: wc_attribute_label( $field['name'] ),
							'required'       => wc_attribute_label( $field['required'] ),
							'field_id'       => 'tm-epo-field-' . $unit_counter,
							'type'           => $field['type'],
							'rules'          => $empty_rules,
							'original_rules' => $empty_rules,
							'rules_type'     => $empty_rules_type,
							'li_class'       => 'tc-normal-mode',
						);
						wc_get_template(
							'tm-field-start.php',
							$args,
							$this->get_namespace(),
							THEMECOMPLETE_EPO_TEMPLATE_PATH
						);

						$name_inc      = "";
						$field_counter = 0;

						if ( $attribute['is_taxonomy'] ) {

							$orderby    = themecomplete_attribute_orderby( $attribute['name'] );
							$order_args = 'orderby=name&hide_empty=0';
							switch ( $orderby ) {
								case 'name' :
									$order_args = array( 'orderby' => 'name', 'hide_empty' => FALSE, 'menu_order' => FALSE );
									break;
								case 'id' :
									$order_args = array( 'orderby' => 'id', 'order' => 'ASC', 'menu_order' => FALSE, 'hide_empty' => FALSE );
									break;
								case 'menu_order' :
									$order_args = array( 'menu_order' => 'ASC', 'hide_empty' => FALSE );
									break;
							}

							// Terms in current lang
							$_current_terms  = THEMECOMPLETE_EPO_WPML()->get_terms( THEMECOMPLETE_EPO_WPML()->get_lang(), $attribute['name'], $order_args );
							$_current_terms2 = get_terms( $attribute['name'], $order_args );
							$_current_terms  = THEMECOMPLETE_EPO_WPML()->order_terms( $_current_terms, $_current_terms2 );

							$current_language = apply_filters( 'wpml_current_language', FALSE );
							$default_language = apply_filters( 'wpml_default_language', FALSE );
							do_action( 'wpml_switch_language', $default_language );

							// Terms in default WPML lang
							$_default_terms  = THEMECOMPLETE_EPO_WPML()->get_terms( THEMECOMPLETE_EPO_WPML()->get_lang(), $attribute['name'], $order_args );
							$_default_terms2 = get_terms( $attribute['name'], $order_args );
							$_default_terms  = THEMECOMPLETE_EPO_WPML()->order_terms( $_default_terms, $_default_terms2 );

							do_action( 'wpml_switch_language', $current_language );

							$_tems_to_use = THEMECOMPLETE_EPO_WPML()->merge_terms( $_current_terms, $_default_terms );

							$slugs = THEMECOMPLETE_EPO_WPML()->merge_terms_slugs( $_current_terms, $_default_terms );

							switch ( $field['type'] ) {

								case "select":
									$name_inc = "select_" . $element_counter;
									$tabindex ++;

									$args = array(
										'id'              => 'tmcp_select_' . $tabindex . $form_prefix,
										'name'            => 'tmcp_' . $name_inc . ( $dummy_prefix ? "" : $form_prefix_onform ),
										'amount'          => '0 ' . $_currency,
										'original_amount' => '0 ' . $_currency,
										'tabindex'        => $tabindex,
										'fieldtype'       => $fieldtype,
										'rules'           => '',
										'original_rules'  => '',
										'rules_type'      => '',
										'textafterprice'         => '',
										'textbeforeprice'        => '',
										'class'                  => '',
										'class_label'            => '',
										'element_data_attr_html' => '',
										'hide_amount'            => ! empty( $field['hide_price'] ) ? " hidden" : "",
										'tax_obj'                => FALSE,

										'options'     => array(),
										'placeholder' => '',
									);
									if ( $_tems_to_use && is_array( $_tems_to_use ) ) {
										foreach ( $_tems_to_use as $trid => $term ) {
											if ( ! isset( $slugs[ $term->slug ] ) ) {
												$slugs[ $term->slug ] = $term->slug;
											}
											$has_term = has_term( (int) $term->term_id, $attribute['name'], floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $product_id ) ) ) ? 1 : 0;

											if ( $has_term ) {
												$wpml_term_id = THEMECOMPLETE_EPO_WPML()->is_active() ? icl_object_id( $term->term_id, $attribute['name'], FALSE ) : FALSE;
												if ( $wpml_term_id ) {
													$wpml_term = get_term( $wpml_term_id, $attribute['name'] );
												} else {
													$wpml_term = $term;
												}

												$option = array(
													'value_to_show'       => sanitize_title( $term->slug ),
													'data_price'          => '',
													'data_rules'          => ( isset( $field['rules_filtered'][ $slugs[ $term->slug ] ] )
														? wp_json_encode( ( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) )
														: ( isset( $field['rules_filtered'][ $term->slug ] )
															? wp_json_encode( $field['rules_filtered'][ $term->slug ] )
															: '' ) ),
													'data_original_rules' => ( isset( $field['rules_filtered'][ $slugs[ $term->slug ] ] )
														? wp_json_encode( ( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) )
														: ( isset( $field['rules_filtered'][ $term->slug ] )
															? wp_json_encode( $field['rules_filtered'][ $term->slug ] )
															: '' ) ),
													'data_rulestype'      => ( isset( $field['rules_type'][ $slugs[ $term->slug ] ] )
														? wp_json_encode( ( $field['rules_type'][ $slugs[ $term->slug ] ] ) )
														: ( isset( $field['rules_type'][ $term->slug ] )
															? wp_json_encode( $field['rules_type'][ $term->slug ] )
															: '' ) ),
													'text'                => $wpml_term->name,
												);

												if ( isset( $_POST[ 'tmcp_' . $name_inc . $form_prefix_onform ] ) ) {
													$option['selected'] = $_POST[ 'tmcp_' . $name_inc . $form_prefix_onform ];
													$option['current']  = esc_attr( $option['value_to_show'] );
												}

												$args['options'][] = $option;

											}
										}
									}

									wc_get_template(
										'tm-' . $field['type'] . '.php',
										$args,
										$this->get_namespace(),
										THEMECOMPLETE_EPO_TEMPLATE_PATH
									);
									$element_counter ++;
									break;

								case "radio":
								case "checkbox":
									if ( $_tems_to_use && is_array( $_tems_to_use ) ) {
										$labelclass       = '';
										$labelclass_start = '';
										$labelclass_end   = '';
										if ( THEMECOMPLETE_EPO()->tm_epo_css_styles == "on" ) {
											$labelclass       = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
											$labelclass_start = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
											$labelclass_end   = TRUE;
										}

										foreach ( $_tems_to_use as $trid => $term ) {
											if ( ! isset( $slugs[ $term->slug ] ) ) {
												$slugs[ $term->slug ] = $term->slug;
											}

											$has_term = has_term( (int) $term->term_id, $attribute['name'], floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $product_id ) ) ) ? 1 : 0;

											if ( $has_term ) {

												$wpml_term_id = THEMECOMPLETE_EPO_WPML()->is_active() ? icl_object_id( $term->term_id, $attribute['name'], FALSE ) : FALSE;

												if ( $wpml_term_id ) {
													$wpml_term = get_term( $wpml_term_id, $attribute['name'] );
												} else {
													;
													$wpml_term = $term;
												}

												$tabindex ++;

												if ( $field['type'] == 'radio' ) {
													$name_inc = "radio_" . $element_counter;
												}
												if ( $field['type'] == 'checkbox' ) {
													$name_inc = "checkbox_" . $element_counter . "_" . $field_counter;
												}

												$original_rules = ( isset( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) ? wp_json_encode( ( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) ) : ( isset( $field['rules_filtered'][ $term->slug ] ) ? wp_json_encode( $field['rules_filtered'][ $term->slug ] ) : '' ) );
												if ( empty( $original_rules ) ) {
													$original_rules = "";
												}

												$checked = FALSE;
												$value   = sanitize_title( $term->slug );
												switch ( $field['type'] ) {

													case "radio":

														$selected_value = '';
														$name           = 'tmcp_' . $name_inc . ( $dummy_prefix ? "" : $form_prefix_onform );

														if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no" && isset( $_POST[ $name ] ) ) {
															$selected_value = $_POST[ $name ];
														} elseif ( isset( $_GET[ $name ] ) ) {
															$selected_value = $_GET[ $name ];
														} elseif ( empty( $_POST ) || ! isset( $_POST[ $name ] ) || THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "yes" ) {
															$selected_value = - 1;
														}

														$checked = $selected_value !== - 1 && esc_attr( stripcslashes( $selected_value ) ) == esc_attr( $value );
														break;

													case "checkbox":

														$selected_value = '';
														$name           = 'tmcp_' . $name_inc . ( $dummy_prefix ? "" : $form_prefix_onform );
														if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no" && isset( $_POST[ $name ] ) ) {
															$selected_value = $_POST[ $name ];
														} elseif ( isset( $_GET[ $name ] ) ) {
															$selected_value = $_GET[ $name ];
														} elseif ( ( ( THEMECOMPLETE_EPO()->is_quick_view() || empty( $_POST ) ) && empty( THEMECOMPLETE_EPO()->cart_edit_key ) ) || THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "yes" ) {
															$selected_value = - 1;
														}

														$checked = $selected_value !== - 1 && esc_attr( stripcslashes( $selected_value ) ) == esc_attr( $value );
														break;
												}
												$args = array(
													'id'              => 'tmcp_choice_' . $element_counter . "_" . $field_counter . "_" . $tabindex . $form_prefix,
													'name'            => $name,
													'amount'          => '0 ' . $_currency,
													'original_amount' => '0 ' . $_currency,
													'tabindex'        => $tabindex,
													'fieldtype'       => $fieldtype,
													'rules'           => ( isset( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) ? wp_json_encode( ( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) ) : ( isset( $field['rules_filtered'][ $term->slug ] ) ? wp_json_encode( $field['rules_filtered'][ $term->slug ] ) : '' ) ),
													'original_rules'  => $original_rules,
													'rules_type'      => ( isset( $field['rules_type'][ $slugs[ $term->slug ] ] ) ? wp_json_encode( ( $field['rules_type'][ $slugs[ $term->slug ] ] ) ) : ( isset( $field['rules_type'][ $term->slug ] ) ? wp_json_encode( $field['rules_type'][ $term->slug ] ) : '' ) ),

													'label_mode'       => '',
													'label_to_display' => $wpml_term->name,
													'swatch_class'     => '',
													'swatch'           => array(),
													'altsrc'           => array(),

													'textafterprice'         => '',
													'textbeforeprice'        => '',
													'class'                  => '',
													'element_data_attr_html' => '',
													'li_class'               => '',
													'limit'                  => '',
													'exactlimit'             => '',
													'minimumlimit'           => '',
													'url'                    => '',
													'image'                  => '',
													'imagec'                 => '',
													'imagep'                 => '',
													'imagel'                 => '',
													'image_variations'       => '',
													'checked'                => $checked,
													'use'                    => '',
													'labelclass_start'       => $labelclass_start,
													'labelclass'             => $labelclass,
													'labelclass_end'         => $labelclass_end,

													'hide_amount' => ! empty( $field['hide_price'] ) ? " hidden" : "",
													'tax_obj'     => FALSE,
													'border_type' => '',

													'label'      => $wpml_term->name,
													'value'      => $value,
													'use_images' => "",
													'grid_break' => "",
													'percent'    => "",
													'limit'      => empty( $field['limit'] ) ? "" : $field['limit'],

												);
												wc_get_template(
													'tm-' . $field['type'] . '.php',
													$args,
													$this->get_namespace(),
													THEMECOMPLETE_EPO_TEMPLATE_PATH
												);

												$field_counter ++;
											}
										}
									}

									$element_counter ++;
									break;

							}
						} else {

							$options      = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
							$wpml_options = isset( $wpml_attribute['value'] ) ? array_map( 'trim', explode( WC_DELIMITER, $wpml_attribute['value'] ) ) : $options;

							switch ( $field['type'] ) {

								case "select":
									$name_inc = "select_" . $element_counter;
									$tabindex ++;

									$args = array(
										'id'              => 'tmcp_select_' . $tabindex . $form_prefix,
										'name'            => 'tmcp_' . $name_inc . ( $dummy_prefix ? "" : $form_prefix_onform ),
										'amount'          => '0 ' . $_currency,
										'original_amount' => '0 ' . $_currency,
										'tabindex'        => $tabindex,
										'fieldtype'       => $fieldtype,
										'rules'           => '',
										'original_rules'  => '',
										'rules_type'      => '',

										'textafterprice'         => '',
										'textbeforeprice'        => '',
										'class'                  => '',
										'class_label'            => '',
										'element_data_attr_html' => '',
										'hide_amount'            => ! empty( $field['hide_price'] ) ? " hidden" : "",
										'tax_obj'                => FALSE,
										'border_type'            => '',

										'options'     => array(),
										'placeholder' => '',

									);
									foreach ( $options as $k => $option ) {

										$option = array(
											'value_to_show'       => sanitize_title( $option ),
											'data_price'          => '',
											'data_rules'          => ( isset( $field['rules_filtered'][ esc_attr( sanitize_title( $option ) ) ] ) ? wp_json_encode( ( $field['rules_filtered'][ esc_attr( sanitize_title( $option ) ) ] ) ) : '' ),
											'data_original_rules' => ( isset( $field['rules_filtered'][ esc_attr( sanitize_title( $option ) ) ] ) ? wp_json_encode( ( $field['rules_filtered'][ esc_attr( sanitize_title( $option ) ) ] ) ) : '' ),
											'data_rulestype'      => ( isset( $field['rules_type'][ esc_attr( sanitize_title( $option ) ) ] ) ? wp_json_encode( ( $field['rules_type'][ esc_attr( sanitize_title( $option ) ) ] ) ) : '' ),
											'text'                => apply_filters( 'woocommerce_tm_epo_option_name', isset( $wpml_options[ $k ] ) ? $wpml_options[ $k ] : $option, NULL, NULL ),
										);

										if ( isset( $_POST[ 'tmcp_' . $name_inc . $form_prefix_onform ] ) ) {
											$option['selected'] = $_POST[ 'tmcp_' . $name_inc . $form_prefix_onform ];
											$option['current']  = esc_attr( $option['value_to_show'] );
										}

										$args['options'][] = $option;

									}
									wc_get_template(
										'tm-' . $field['type'] . '.php',
										$args,
										$this->get_namespace(),
										THEMECOMPLETE_EPO_TEMPLATE_PATH
									);
									$element_counter ++;
									break;

								case "radio":
								case "checkbox":
									$labelclass       = '';
									$labelclass_start = '';
									$labelclass_end   = '';
									if ( THEMECOMPLETE_EPO()->tm_epo_css_styles == "on" ) {
										$labelclass       = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
										$labelclass_start = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
										$labelclass_end   = TRUE;
									}

									foreach ( $options as $k => $option ) {
										$tabindex ++;

										if ( $field['type'] == 'radio' ) {
											$name_inc = "radio_" . $element_counter;
										}
										if ( $field['type'] == 'checkbox' ) {
											$name_inc = "checkbox_" . $element_counter . "_" . $field_counter;
										}

										$original_rules = isset( $field['rules_filtered'][ sanitize_title( $option ) ] ) ? wp_json_encode( ( $field['rules_filtered'][ sanitize_title( $option ) ] ) ) : '';
										if ( empty( $original_rules ) ) {
											$original_rules = "";
										}

										$checked = FALSE;
										$value   = sanitize_title( $option );
										switch ( $field['type'] ) {

											case "radio":

												$selected_value = '';
												$name           = 'tmcp_' . $name_inc . ( $dummy_prefix ? "" : $form_prefix_onform );

												if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no" && isset( $_POST[ $name ] ) ) {
													$selected_value = $_POST[ $name ];
												} elseif ( isset( $_GET[ $name ] ) ) {
													$selected_value = $_GET[ $name ];
												} elseif ( empty( $_POST ) || ! isset( $_POST[ $name ] ) || THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "yes" ) {
													$selected_value = - 1;
												}

												$checked = $selected_value !== - 1 && esc_attr( stripcslashes( $selected_value ) ) == esc_attr( $value );
												break;

											case "checkbox":

												$selected_value = '';
												$name           = 'tmcp_' . $name_inc . ( $dummy_prefix ? "" : $form_prefix_onform );
												if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no" && isset( $_POST[ $name ] ) ) {
													$selected_value = $_POST[ $name ];
												} elseif ( isset( $_GET[ $name ] ) ) {
													$selected_value = $_GET[ $name ];
												} elseif ( ( ( THEMECOMPLETE_EPO()->is_quick_view() || empty( $_POST ) ) && empty( THEMECOMPLETE_EPO()->cart_edit_key ) ) || THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "yes" ) {
													$selected_value = - 1;
												}

												$checked = $selected_value !== - 1 && esc_attr( stripcslashes( $selected_value ) ) == esc_attr( $value );
												break;
										}

										$label = apply_filters( 'woocommerce_tm_epo_option_name', isset( $wpml_options[ $k ] ) ? $wpml_options[ $k ] : $option, NULL, NULL );

										$args = array(
											'id'              => 'tmcp_choice_' . $element_counter . "_" . $field_counter . "_" . $tabindex . $form_prefix,
											'name'            => $name,
											'amount'          => '0 ' . $_currency,
											'original_amount' => '0 ' . $_currency,
											'tabindex'        => $tabindex,
											'fieldtype'       => $fieldtype,
											'rules'           => isset( $field['rules_filtered'][ sanitize_title( $option ) ] ) ? wp_json_encode( ( $field['rules_filtered'][ sanitize_title( $option ) ] ) ) : '',
											'original_rules'  => $original_rules,
											'rules_type'      => isset( $field['rules_type'][ sanitize_title( $option ) ] ) ? wp_json_encode( ( $field['rules_type'][ sanitize_title( $option ) ] ) ) : '',

											'label_mode'       => '',
											'label_to_display' => $label,
											'swatch_class'     => '',
											'swatch'           => array(),
											'altsrc'           => array(),

											'textafterprice'         => '',
											'textbeforeprice'        => '',
											'class'                  => '',
											'element_data_attr_html' => '',
											'li_class'               => '',
											'limit'                  => '',
											'exactlimit'             => '',
											'minimumlimit'           => '',
											'url'                    => '',
											'image'                  => '',
											'imagec'                 => '',
											'imagep'                 => '',
											'imagel'                 => '',
											'image_variations'       => '',
											'checked'                => $checked,
											'use'                    => '',
											'labelclass_start'       => $labelclass_start,
											'labelclass'             => $labelclass,
											'labelclass_end'         => $labelclass_end,
											'hide_amount'            => ! empty( $field['hide_price'] ) ? " hidden" : "",
											'tax_obj'                => FALSE,
											'border_type'            => '',

											'label'      => $label,
											'value'      => $value,
											'use_images' => "",
											'grid_break' => "",
											'percent'    => "",
											'limit'      => empty( $field['limit'] ) ? "" : $field['limit'],
										);
										wc_get_template(
											'tm-' . $field['type'] . '.php',
											$args,
											$this->get_namespace(),
											THEMECOMPLETE_EPO_TEMPLATE_PATH
										);
										$field_counter ++;
									}
									$element_counter ++;
									break;

							}
						}

						wc_get_template(
							'tm-field-end.php',
							array(),
							$this->get_namespace(),
							THEMECOMPLETE_EPO_TEMPLATE_PATH
						);

						$unit_counter ++;
					}
				}
			}
		}

		return array(
			'tabindex'        => $tabindex,
			'unit_counter'    => $unit_counter,
			'field_counter'   => $field_counter,
			'element_counter' => $element_counter,
			'_currency'       => $_currency,
		);

	}

	/**
	 * Display totals box
	 *
	 * @param int    $product_id
	 * @param string $form_prefix
	 * @param bool   $is_from_shortcode
	 */
	public function tm_epo_totals( $product_id = 0, $form_prefix = "", $is_from_shortcode = FALSE ) {

		global $product, $woocommerce;

		if ( ! property_exists( $woocommerce, 'product_factory' )
		     || $woocommerce->product_factory === NULL
		     || ( $this->tm_options_have_been_displayed && ( ! ( THEMECOMPLETE_EPO()->is_bto || ( ( THEMECOMPLETE_EPO()->is_enabled_shortcodes() && ! $is_from_shortcode ) && ! is_product() ) || ( ( is_shop() || is_product_category() || is_product_tag() ) && THEMECOMPLETE_EPO()->tm_epo_enable_in_shop == "yes" ) ) ) )
		) {
			return;// bad function call
		}

		$this->print_price_fields( $product_id, $form_prefix, $is_from_shortcode );
		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) && ! $is_from_shortcode ) {
			$this->tm_options_totals_have_been_displayed = TRUE;
		}

	}

	/**
	 * Batch displayh totals box
	 *
	 * @param string $form_prefix
	 */
	private function tm_epo_totals_batch( $form_prefix = "" ) {

		foreach ( $this->current_product_id_to_be_displayed_check as $key => $product_id ) {
			if ( ! empty( $product_id ) ) {
				$this->print_price_fields( $product_id, $form_prefix );
				if ( THEMECOMPLETE_EPO()->tm_epo_options_placement != THEMECOMPLETE_EPO()->tm_epo_totals_box_placement ) {
					if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
						unset( $this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] );
					}
				}
			}
		}
		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
			if ( THEMECOMPLETE_EPO()->tm_epo_options_placement != THEMECOMPLETE_EPO()->tm_epo_totals_box_placement ) {
				$this->epo_internal_counter       = 0;
				$this->epo_internal_counter_check = array();
			}
		}

	}

	/**
	 * Display totals box
	 *
	 * @param int    $product_id
	 * @param string $form_prefix
	 * @param bool   $is_from_shortcode
	 */
	private function print_price_fields( $product_id = 0, $form_prefix = "", $is_from_shortcode = FALSE ) {
		
		if ($product_id instanceof WC_PRODUCT){
			$product = $product_id;
			$product_id = themecomplete_get_id($product);
		} else {
			$product_id = floatval( trim( $product_id ) );
			if ( ! $product_id ) {
				global $product;
				if ( $product ) {
					$product_id = themecomplete_get_id( $product );
				}
			} else {
				$product = wc_get_product( $product_id );
			}
		}

		if ( ! $product_id || empty( $product ) ) {
			if ( ! empty( $this->current_product_id_to_be_displayed ) ) {
				$product_id = $this->current_product_id_to_be_displayed;
				$product    = wc_get_product( $product_id );
			} else {
				$this->tm_epo_totals_batch( $form_prefix );

				return;
			}
		}
		if ( ! $product_id || empty( $product ) ) {
			return;
		}

		$type = themecomplete_get_product_type( $product );

		if ( $type === "grouped" ) {
			return;
		}

		$cpf_price_array = THEMECOMPLETE_EPO()->get_product_tm_epos( $product_id, "", FALSE, TRUE );
		if ( ! $cpf_price_array ) {
			return;
		}

		if ( THEMECOMPLETE_EPO()->is_associated === FALSE && $cpf_price_array && THEMECOMPLETE_EPO()->tm_epo_enable_final_total_box_all == "no" ) {
			$global_price_array = $cpf_price_array['global'];
			$local_price_array  = $cpf_price_array['local'];
			if ( empty( $global_price_array ) && empty( $local_price_array ) ) {
				if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
					if ( empty( $this->epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] ) ) {
						// First time displaying totals and fields haven't been displayed
						$this->epo_internal_counter ++;
						$this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] = $this->epo_internal_counter;
					} else {
						// Fields have already been displayed
						unset( $this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] );
						$this->current_product_id_to_be_displayed = 0;
						$this->unique_form_prefix                 = '';
					}
					$_epo_internal_counter = $this->epo_internal_counter;
				} else {
					$_epo_internal_counter = 0;
				}

				return;
			}
		}

		if ( THEMECOMPLETE_EPO()->is_associated === FALSE && ! $cpf_price_array && THEMECOMPLETE_EPO()->tm_epo_enable_final_total_box_all == "no" ) {
			return;
		}

		THEMECOMPLETE_EPO()->set_tm_meta( $product_id );

		$force_quantity = 0;
		if ( THEMECOMPLETE_EPO()->cart_edit_key ) {
			$cart_item_key = THEMECOMPLETE_EPO()->cart_edit_key;
			$cart_item     = WC()->cart->get_cart_item( $cart_item_key );

			if ( isset( $cart_item["quantity"] ) ) {
				$force_quantity = $cart_item["quantity"];
			}
		}

		if ( ! $form_prefix && THEMECOMPLETE_EPO()->is_quick_view() ) {
			if ( ! $this->unique_form_prefix ) {
				$this->unique_form_prefix = uniqid( '' );
			}
			$form_prefix = '_' . 'tcform' . $this->unique_form_prefix;
		}

		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
			if ( empty( $this->epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] ) ) {
				// First time displaying totals and fields haven't been displayed
				$this->epo_internal_counter ++;
				$this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] = $this->epo_internal_counter;
			} else {
				// Fields have already been displayed
				unset( $this->epo_internal_counter_check[ "tc" . $this->epo_internal_counter ] );
				$this->current_product_id_to_be_displayed = 0;
				$this->unique_form_prefix                 = '';
			}
			$_epo_internal_counter = $this->epo_internal_counter;
		} else {
			if (THEMECOMPLETE_EPO()->is_inline_epo && $this->epo_internal_counter){
				$_epo_internal_counter = $this->epo_internal_counter;
			} else {
				$_epo_internal_counter = 0;
			}
		}

		if ( ! $form_prefix && THEMECOMPLETE_EPO()->wc_vars["is_page"] ) {
			$form_prefix = 'tcform' . $_epo_internal_counter;
		}

		if ( $form_prefix ) {
			$form_prefix = "_" . $form_prefix;
		}

		$minmax = array();

		$minmax['min_price']         = $product->get_price();
		$minmax['min_regular_price'] = $product->get_regular_price();

		if ( function_exists( 'WC_CP' ) && version_compare( WC_CP()->version, "3.8", "<" ) && themecomplete_get_product_type( $product ) == "composite" && is_callable( array( $product, 'get_base_price' ) ) ) {
			$_price = apply_filters( 'woocommerce_tm_epo_price_compatibility', $product->get_base_price(), $product );
		} else {
			$_price = apply_filters( 'woocommerce_tm_epo_price_compatibility', $minmax['min_price'], $product );
		}

		$price            = array();
		$price['product'] = array(); // product price rules
		$price['price']   = apply_filters( 'wc_epo_product_price', $_price, "", FALSE ); // product price

		$price = apply_filters( 'wc_epo_product_price_rules', $price, $product );

		$regular_price            = array();
		$regular_price['product'] = array(); // product price rules
		$regular_price['price']   = apply_filters( 'wc_epo_product_price', $minmax['min_regular_price'], "", FALSE ); // product price

		$regular_price = apply_filters( 'wc_epo_product_regular_price_rules', $regular_price, $product );

		// Woothemes Dynamic Pricing (not yet fully compatible)
		if ( class_exists( 'WC_Dynamic_Pricing' ) ) {
			$id = isset( $product->variation_id ) ? $product->variation_id : themecomplete_get_id( $product );
			$dp = WC_Dynamic_Pricing::instance();
			if ( $dp &&
			     is_object( $dp ) && property_exists( $dp, "discounted_products" )
			     && isset( $dp->discounted_products[ $id ] )
			) {
				$_price = $dp->discounted_products[ $id ];
			} else {
				$_price = $product->get_price();
			}
			$price['price'] = apply_filters( 'wc_epo_product_price', $_price, "", FALSE ); // product price
		}

		$variations = array();

		if ( themecomplete_get_product_type( $product ) === "variable" && THEMECOMPLETE_EPO()->tm_epo_no_variation_prices_array !== 'yes' ) {

			foreach ( $product->get_available_variations() as $variation ) {

				$child_id = $variation["variation_id"];
				$product_variation = wc_get_product( $child_id );

				if ($this->discount){
					$current_price = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $product_variation->get_price(), $this->discount, $this->discount_type);
					$product_variation->set_sale_price($current_price); 
					$product_variation->set_price($current_price);  
				}

				// Make sure we always have untaxed price here
				if (!wc_prices_include_tax() && 'incl' === get_option( 'woocommerce_tax_display_shop' )){
					$variation_price = wc_get_price_excluding_tax(
						$product_variation,
						array(
							'qty'   => 1,
							'price' => $product_variation->get_price(),
						)
					);
				} else {
					$variation_price = $product_variation->get_price();
				}				
				
				if (isset($variation['attributes']) && is_array($variation['attributes'])){
					$atts = 0;
					foreach ($variation['attributes'] as $att => $value_att) {
						if (isset($_REQUEST[$att]) && $_REQUEST[$att] == $value_att){
							$atts++;
						}
					}
					if ($atts === count($variation['attributes'])){
						$price['price']   = apply_filters( 'wc_epo_product_price', $variation_price, "", FALSE ); // product price
					}
				}

				$variation = wc_get_product( $child_id );

				do_action( 'wc_epo_print_price_fields_in_variation_loop', $product_variation, $child_id );

				$variations[ $child_id ] = apply_filters( 'woocommerce_tm_epo_price_compatibility', apply_filters( 'wc_epo_product_price', $variation_price, "", FALSE ), $product_variation, $child_id );

			}

		}

		global $woocommerce;
		$cart = $woocommerce->cart;

		$tax_rate = $this->get_tax_rate( themecomplete_get_tax_class( $product ) );

		$taxable          = $product->is_taxable();
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$tax_string       = "";
		if ( $taxable && THEMECOMPLETE_EPO()->tm_epo_global_tax_string_suffix == "yes" ) {

			if ( $tax_display_mode == 'excl' ) {

				$tax_string = ' <small>' . apply_filters( 'wc_epo_ex_tax_or_vat_string', WC()->countries->ex_tax_or_vat() ) . '</small>';

			} else {

				$tax_string = ' <small>' . apply_filters( 'inc_tax_or_vat', WC()->countries->inc_tax_or_vat() ) . '</small>';

			}

		}

		$base_taxes_of_one   = 0;
		$modded_taxes_of_one = 0;

		$is_vat_exempt            = - 1;
		$non_base_location_prices = - 1;
		$base_tax_rate            = $tax_rate;
		if ( class_exists( 'WC_Tax' ) && version_compare( get_option( 'woocommerce_version' ), '2.4', '>=' ) ) {
			$tax_rates      = WC_Tax::get_rates( themecomplete_get_tax_class( $product ) );
			$base_tax_rates = WC_Tax::get_base_tax_rates( themecomplete_get_tax_class( $product, 'unfiltered' ) );
			$base_tax_rate  = 0;
			foreach ( $base_tax_rates as $key => $value ) {
				$base_tax_rate = $base_tax_rate + floatval( $value['rate'] );
			}
			$is_vat_exempt            = ( ! empty( WC()->customer ) && WC()->customer->is_vat_exempt() ) == TRUE ? 1 : 0;
			$non_base_location_prices = ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', TRUE ) ) == TRUE ? 1 : 0;

			$precision    = wc_get_rounding_precision();
			$price_of_one = 1 * ( pow( 10, $precision ) );

			$taxes_of_one = array_sum( WC_Tax::calc_tax( $price_of_one, $tax_rates, wc_prices_include_tax() ) );
			if ( $non_base_location_prices ) {
				$prices_include_tax = TRUE;
			} else {
				$prices_include_tax = wc_prices_include_tax();
			}
			$base_taxes_of_one   = array_sum( WC_Tax::calc_tax( $price_of_one, $base_tax_rates, $prices_include_tax ) );
			$modded_taxes_of_one = array_sum( WC_Tax::calc_tax( $price_of_one - $base_taxes_of_one, $tax_rates, FALSE ) );

			$taxes_of_one        = $taxes_of_one / ( pow( 10, $precision ) );
			$base_taxes_of_one   = $base_taxes_of_one / ( pow( 10, $precision ) );
			$modded_taxes_of_one = $modded_taxes_of_one / ( pow( 10, $precision ) );

		}

		$forcart        = "main";
		$classcart      = "tm-cart-main";
		$classtotalform = "tm-totals-form-main";
		$form_prefix_id = str_replace( "_", "", $form_prefix );
		if ( ! empty( $form_prefix ) ) {
			$forcart        = $form_prefix_id;
			$classcart      = "tm-cart-" . $form_prefix_id;
			$classtotalform = "tm-totals-form-" . $form_prefix_id;
		}

		do_action( "wc_epo_before_totals_box", array( 'product_id' => $product_id, 'form_prefix' => $form_prefix, 'is_from_shortcode' => $is_from_shortcode ) );
		if ( $is_from_shortcode ) {
			add_action( "wc_epo_totals_form", array( $this, "woocommerce_before_add_to_cart_button" ), 10, 1 );
		}

		$tm_epo_final_total_box = ( empty( THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] ) ) ? THEMECOMPLETE_EPO()->tm_epo_final_total_box : THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'];
		if (THEMECOMPLETE_EPO()->is_associated === TRUE && THEMECOMPLETE_EPO()->tm_epo_enable_final_total_box_all == "no"){
			$tm_epo_final_total_box = "disable";
		}

		wc_get_template(
			'tm-totals.php',
			apply_filters( 'wc_epo_template_args_tm_totals',
				array(

					'classcart'      => $classcart,
					'forcart'        => $forcart,
					'classtotalform' => $classtotalform,
					'is_on_sale'     => $product->is_on_sale(),

					'theme_name' => THEMECOMPLETE_EPO()->get_theme( 'Name' ),
					'variations' => wp_json_encode( (array) $variations ),

					'is_sold_individually'     => $product->is_sold_individually(),
					'hidden'                   => ( THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] ) ? ( ( THEMECOMPLETE_EPO()->tm_epo_final_total_box == 'hide' || THEMECOMPLETE_EPO()->tm_epo_final_total_box == 'disable' || THEMECOMPLETE_EPO()->tm_epo_final_total_box == 'disable_change' ) ? ' hidden' : '' ) : ( ( THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] == 'hide' || THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] == 'disable' || THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] == 'disable_change' ) ? ' hidden' : '' ),
					'price_override'           => ( THEMECOMPLETE_EPO()->tm_epo_global_override_product_price == 'no' )
						? 0
						: ( ( THEMECOMPLETE_EPO()->tm_epo_global_override_product_price == 'yes' )
							? 1
							: ( ! empty( THEMECOMPLETE_EPO()->tm_meta_cpf['price_override'] ) ? 1 : 0 ) ),
					'form_prefix'              => $form_prefix_id,
					'type'                     => themecomplete_get_product_type( $product ),
					'price'                    => ( is_object( $product ) ? apply_filters( 'woocommerce_tm_final_price', $price['price'], $product ) : '' ),
					'regular_price'            => ( is_object( $product ) ? apply_filters( 'woocommerce_tm_final_price', $regular_price['price'], $product ) : '' ),
					'is_vat_exempt'            => $is_vat_exempt,
					'non_base_location_prices' => $non_base_location_prices,
					'taxable'                  => $taxable,
					'tax_display_mode'         => $tax_display_mode,
					'prices_include_tax'       => wc_prices_include_tax(),
					'tax_rate'                 => $tax_rate,
					'base_tax_rate'            => $base_tax_rate,
					'base_taxes_of_one'        => $base_taxes_of_one,
					'taxes_of_one'             => $taxes_of_one,
					'modded_taxes_of_one'      => $modded_taxes_of_one,
					'tax_string'               => $tax_string,
					'product_price_rules'      => wp_json_encode( (array) $price['product'] ),
					'fields_price_rules'       => 0,
					'force_quantity'           => $force_quantity,
					'product_id'               => $product_id,
					'epo_internal_counter'     => $_epo_internal_counter,
					'is_from_shortcode'        => $is_from_shortcode,
					'tm_epo_final_total_box'   => $tm_epo_final_total_box,

				), $product ),
			$this->get_namespace(),
			THEMECOMPLETE_EPO_TEMPLATE_PATH
		);

		do_action( "wc_epo_after_totals_box", array( 'product_id' => $product_id, 'form_prefix' => $form_prefix, 'is_from_shortcode' => $is_from_shortcode ) );

	}

}
