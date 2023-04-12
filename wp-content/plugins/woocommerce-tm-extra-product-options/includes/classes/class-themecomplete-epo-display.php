<?php
/**
 * Extra Product Options Frontend Display
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Frontend Display
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
class THEMECOMPLETE_EPO_Display {

	/**
	 * If both the options and the totals box have been displayed or not
	 *
	 * Prevents option duplication for bad coded themes.
	 *
	 * @var boolean
	 */
	private $tm_options_have_been_displayed = false;

	/**
	 * If the options has been displayed or not
	 *
	 * @var boolean
	 */
	private $tm_options_single_have_been_displayed = false;

	/**
	 * If the totals box has been displayed or not
	 *
	 * @var boolean
	 */
	private $tm_options_totals_have_been_displayed = false;

	/**
	 * The id of the current set of options
	 *
	 * @var integer
	 */
	private $epo_id = 0;

	/**
	 * The current id of the set of options displayed
	 *
	 * This is different from $epo_id as it supports
	 * the product element.
	 *
	 * @var integer
	 */
	private $epo_internal_counter = 0;

	/**
	 * Array of the $epo_internal_counter
	 *
	 * @var array
	 */
	private $epo_internal_counter_check = [];

	/**
	 * The original $epo_internal_counter
	 *
	 * Used when printing options for the product element.
	 *
	 * @var integer
	 */
	private $original_epo_internal_counter = 0;

	/**
	 * The id of the product the options belong to.
	 *
	 * @var integer
	 */
	private $current_product_id_to_be_displayed = 0;

	/**
	 * Array of the $current_product_id_to_be_displayed
	 *
	 * @var array
	 */
	private $current_product_id_to_be_displayed_check = [];

	/**
	 * Inline styles printed at totals box
	 *
	 * @var string
	 */
	public $inline_styles;

	/**
	 * Inline styles printed at html head
	 *
	 * @var string
	 */
	public $inline_styles_head;

	/**
	 * Unique form prefix
	 *
	 * @var string
	 */
	public $unique_form_prefix = '';

	/**
	 * Associated product discount
	 *
	 * @var string
	 */
	private $discount = '';

	/**
	 * Associated product discount type
	 *
	 * @var string
	 */
	private $discount_type = '';

	/**
	 * If the associated product discount is applied to the addons
	 *
	 * @var string
	 */
	private $discount_exclude_addons = '';

	/**
	 * Flag to blocking option display
	 *
	 * @var boolean
	 */
	public $block_epo = false;

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_Display|null
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

		$this->inline_styles      = '';
		$this->inline_styles_head = '';

		// Display in frontend.
		add_action( 'woocommerce_tm_epo', [ $this, 'frontend_display' ], 10, 3 );
		add_action( 'woocommerce_tm_epo_fields', [ $this, 'tm_epo_fields' ], 10, 4 );
		add_action( 'woocommerce_tm_epo_totals', [ $this, 'tm_epo_totals' ], 10, 3 );

		// Display in frontend (Compatibility for older plugin versions).
		add_action( 'woocommerce_tm_custom_price_fields', [ $this, 'frontend_display' ] );
		add_action( 'woocommerce_tm_custom_price_fields_only', [ $this, 'tm_epo_fields' ] );
		add_action( 'woocommerce_tm_custom_price_fields_totals', [ $this, 'tm_epo_totals' ] );

		// Ensures the correct display order of options when multiple prodcuts are displayed.
		add_action( 'woocommerce_before_single_product', [ $this, 'woocommerce_before_single_product' ], 1 );
		add_action( 'woocommerce_after_single_product', [ $this, 'woocommerce_after_single_product' ], 9999 );

		// Internal variables.
		add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'woocommerce_before_add_to_cart_button' ] );

		// Add custom inline css.
		add_action( 'template_redirect', [ $this, 'tm_variation_css_check' ], 9999 );

		// Alter the array of data for a variation. Used in the add to cart form.
		add_filter( 'woocommerce_available_variation', [ $this, 'woocommerce_available_variation' ], 10, 3 );

	}

	/**
	 * Apply asoociated product discount
	 *
	 * @param string $discount Associated product discount.
	 * @param string $discount_type Associated product discount type.
	 * @param string $discount_exclude_addons If the associated product discount is applied to the addons.
	 * @since 5.0.8
	 */
	public function set_discount( $discount = '', $discount_type = '', $discount_exclude_addons = '' ) {
		$this->discount                = $discount;
		$this->discount_type           = $discount_type;
		$this->discount_exclude_addons = $discount_exclude_addons;
	}

	/**
	 * Change internal epo counter
	 * Currently used for associated products
	 *
	 * @param integer $counter The value to set for the internal epo counter.
	 * @since 5.0.8
	 */
	public function set_epo_internal_counter( $counter = 0 ) {
		$this->original_epo_internal_counter = $this->epo_internal_counter;
		$this->epo_internal_counter          = $counter;
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
	 * Returns the path for overriding the templates
	 *
	 * @since 6.0
	 */
	public function get_template_path() {
		return apply_filters( 'wc_epo_template_override_path', THEMECOMPLETE_EPO_NAMESPACE . '/' );
	}

	/**
	 * Returns the default path for the templates
	 *
	 * @since 6.0
	 */
	public function get_default_path() {
		return apply_filters( 'wc_epo_default_template_path', THEMECOMPLETE_EPO_TEMPLATE_PATH );
	}


	/**
	 * Get the tax rate of the given tax classes
	 *
	 * @param string $classes Tax class.
	 *
	 * @return int
	 */
	public function get_tax_rate( $classes ) {

		return themecomplete_get_tax_rate( $classes );

	}

	/**
	 * Add validation rules
	 *
	 * @param array $element The element array.
	 * @return array
	 */
	public function get_tm_validation_rules( $element ) {

		$rules = [];
		if ( $element['required'] ) {
			$rules['required'] = true;
		}
		if ( isset( $element['min_chars'] ) && '' !== $element['min_chars'] && false !== $element['min_chars'] ) {
			$rules['minlength'] = absint( $element['min_chars'] );
		}
		if ( isset( $element['max_chars'] ) && '' !== $element['max_chars'] && false !== $element['max_chars'] ) {
			$rules['maxlength'] = absint( $element['max_chars'] );
		}
		if ( isset( $element['min'] ) && '' !== $element['min'] && ( 'number' === $element['validation1'] || 'digits' === $element['validation1'] ) ) {
			$rules['min'] = floatval( $element['min'] );
		}
		if ( isset( $element['max'] ) && '' !== $element['max'] && ( 'number' === $element['validation1'] || 'digits' === $element['validation1'] ) ) {
			$rules['max'] = floatval( $element['max'] );
		}
		if ( ! empty( $element['validation1'] ) ) {
			$rules[ $element['validation1'] ] = true;
		}
		if ( ! empty( $element['repeater'] ) && ! empty( $element['repeater_min_rows'] ) ) {
			$rules['repeaterminrows'] = $element['repeater_min_rows'];
		}
		if ( ! empty( $element['repeater'] ) && ! empty( $element['repeater_max_rows'] ) ) {
			$rules['repeatermaxrows'] = $element['repeater_max_rows'];
		}

		return $rules;

	}

	/**
	 * Alter the array of data for a variation.
	 * Used in the add to cart form.
	 *
	 * @param array               $array Array of variation arguments.
	 * @param WC_Product_Variable $class The WC_Product_Variable class.
	 * @param WC_Product          $variation Variation product object or ID.
	 * @since 1.0
	 */
	public function woocommerce_available_variation( $array, $class, $variation ) {

		if ( apply_filters( 'wc_epo_woocommerce_available_variation_check', true ) && ! ( THEMECOMPLETE_EPO()->can_load_scripts() || wp_doing_ajax() ) && ! ( isset( $_REQUEST['wc-ajax'] ) && 'get_variation' === $_REQUEST['wc-ajax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $array;
		}

		if ( is_array( $array ) ) {

			$tax_rate = $this->get_tax_rate( themecomplete_get_tax_class( $variation ) );

			$taxes_of_one        = 0;
			$base_taxes_of_one   = 0;
			$modded_taxes_of_one = 0;

			$non_base_location_prices = -1;
			$base_tax_rate            = $tax_rate;

			if ( class_exists( 'WC_Tax' ) && version_compare( get_option( 'woocommerce_version' ), '2.4', '>=' ) ) {
				$tax_rates      = WC_Tax::get_rates( themecomplete_get_tax_class( $variation ) );
				$base_tax_rates = WC_Tax::get_base_tax_rates( themecomplete_get_tax_class( $variation ) );
				$base_tax_rate  = 0;
				foreach ( $base_tax_rates as $key => $value ) {
					$base_tax_rate = $base_tax_rate + floatval( $value['rate'] );
				}

				$non_base_location_prices = true === ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) ? 1 : 0;

				$precision    = wc_get_rounding_precision();
				$price_of_one = 1 * ( pow( 10, $precision ) );

				if ( $non_base_location_prices ) {
					$prices_include_tax = true;
				} else {
					$prices_include_tax = wc_prices_include_tax();
				}

				$taxes_of_one        = array_sum( WC_Tax::calc_tax( $price_of_one, $tax_rates, wc_prices_include_tax() ) );
				$base_taxes_of_one   = array_sum( WC_Tax::calc_tax( $price_of_one, $base_tax_rates, true ) );
				$modded_taxes_of_one = array_sum( WC_Tax::calc_tax( $price_of_one - $base_taxes_of_one, $tax_rates, false ) );

				$taxes_of_one        = $taxes_of_one / ( pow( 10, $precision ) );
				$base_taxes_of_one   = $base_taxes_of_one / ( pow( 10, $precision ) );
				$modded_taxes_of_one = $modded_taxes_of_one / ( pow( 10, $precision ) );
			}

			$array['tc_tax_rate']                 = $tax_rate;
			$array['tc_is_taxable']               = $variation->is_taxable();
			$array['tc_base_tax_rate']            = $base_tax_rate;
			$array['tc_base_taxes_of_one']        = $base_taxes_of_one;
			$array['tc_taxes_of_one']             = $taxes_of_one;
			$array['tc_modded_taxes_of_one']      = $modded_taxes_of_one;
			$array['tc_non_base_location_prices'] = $non_base_location_prices;
			$array['tc_is_on_sale']               = $variation->is_on_sale();
			if ( isset( $_REQUEST['discount_type'] ) && isset( $_REQUEST['discount'] ) && ! empty( $_REQUEST['discount'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				$current_price = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $variation->get_price(), sanitize_text_field( wp_unslash( $_REQUEST['discount'] ) ), sanitize_text_field( wp_unslash( $_REQUEST['discount_type'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$variation->set_sale_price( $current_price );
				$variation->set_price( $current_price );

				// See if prices should be shown for each variation after selection.
				$show_variation_price = apply_filters( 'woocommerce_show_variation_price', $variation->get_price() === '' || $class->get_variation_sale_price( 'min' ) !== $class->get_variation_sale_price( 'max' ) || $class->get_variation_regular_price( 'min' ) !== $class->get_variation_regular_price( 'max' ), $class, $variation );

				$array['display_price'] = wc_get_price_to_display( $variation );
				$array['price_html']    = $show_variation_price ? '<span class="price">' . $variation->get_price_html() . '</span>' : '';

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
			$wp_scripts = new WP_Scripts(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
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
			wp_register_style( 'themecomplete-styles-footer', false, [], THEMECOMPLETE_EPO_VERSION );
			wp_add_inline_style( 'themecomplete-styles-footer', $this->inline_styles );
			wp_enqueue_style( 'themecomplete-styles-footer' );
			$this->inline_styles = '';
		}

	}

	/**
	 * Handles any extra styling associated with the fields
	 *
	 * @since 4.8
	 */
	public function tm_add_inline_style() {

		if ( ! empty( $this->inline_styles ) ) {
			if ( THEMECOMPLETE_EPO()->is_quick_view() || ( wp_doing_ajax() && ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) ) {
				$this->tm_add_inline_style_qv();
			} else {
				$this->tm_add_inline_style_reg();
			}
		}

	}

	/**
	 * Handles any extra styling associated with the fields
	 *
	 * @param string $css_string CSS code to add.
	 * @since 4.8.5
	 */
	public function add_inline_style( $css_string = '' ) {

		$this->inline_styles = $this->inline_styles . $css_string;

	}

	/**
	 * Add custom inline css
	 * Used to hide the native variations
	 *
	 * @param integer $echo If the result should be displayed or retuned.
	 * @param integer $product_id The product id.
	 * @since 1.0
	 */
	public function tm_variation_css_check( $echo = 0, $product_id = 0 ) {

		if ( ! is_product() ) {
			return;
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

		if ( false !== $has_epo && is_array( $has_epo ) && isset( $has_epo['variations'] ) && true === $has_epo['variations'] && empty( $has_epo['variations_disabled'] ) ) {
			if ( $product_id ) {
				$css_string = '#product-' . $product_id . ' form .variations,.post-' . $product_id . ' form .variations {display:none;}';
			} else {
				$css_string = 'form .variations{display:none;}';
			}

			$this->inline_styles_head = $this->inline_styles_head . $css_string;
			if ( $echo ) {
				$this->tm_variation_css_check_do();
			} else {
				add_action( 'wp_head', [ $this, 'tm_variation_css_check_do' ] );
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
			wp_register_style( 'themecomplete-styles-header', false, [], THEMECOMPLETE_EPO_VERSION );
			wp_add_inline_style( 'themecomplete-styles-header', $this->inline_styles_head );
			wp_enqueue_style( 'themecomplete-styles-header' );
		}

	}

	/**
	 * Internal variables
	 *
	 * @param integer|false $product_id The product id.
	 * @since 1.0
	 */
	public function woocommerce_before_add_to_cart_button( $product_id = false ) {

		$this->epo_id ++;
		echo '<input type="hidden" class="tm-epo-counter" name="tm-epo-counter" value="' . esc_attr( $this->epo_id ) . '">';

		if ( $product_id ) {
			$pid = $product_id;
		} else {
			global $product;
			$pid = themecomplete_get_id( $product );
		}

		if ( ! empty( $pid ) ) {
			echo '<input type="hidden" data-epo-id="' . esc_attr( $this->epo_id ) . '" class="tc-add-to-cart" name="tcaddtocart" value="' . esc_attr( $pid ) . '">';
		}

	}

	/**
	 * Ensures the correct display order of options when multiple products are displayed
	 *
	 * @since 1.0
	 */
	public function woocommerce_before_single_product() {

		global $woocommerce;
		if ( ! property_exists( $woocommerce, 'product_factory' ) || null === $woocommerce->product_factory ) {
			return;// bad function call.
		}
		global $product;
		if ( $product ) {
			if ( ! is_product() ) {
				$this->tm_variation_css_check( 1, themecomplete_get_id( $product ) );
			}
			$this->current_product_id_to_be_displayed = themecomplete_get_id( $product );
			$this->current_product_id_to_be_displayed_check[ 'tc-' . count( $this->current_product_id_to_be_displayed_check ) . '-' . $this->current_product_id_to_be_displayed ] = $this->current_product_id_to_be_displayed;
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
	 * @param integer $product_id The product id.
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $dummy_prefix If we should use the form prefix.
	 */
	public function frontend_display( $product_id = 0, $form_prefix = '', $dummy_prefix = false ) {

		if ( $this->block_epo ) {
			return;
		}

		global $product, $woocommerce;
		if ( ! property_exists( $woocommerce, 'product_factory' )
			|| null === $woocommerce->product_factory
			|| ( $this->tm_options_have_been_displayed && ( ! ( THEMECOMPLETE_EPO()->is_bto || ( THEMECOMPLETE_EPO()->is_enabled_shortcodes() && ! is_product() ) || ( ( is_shop() || is_product_category() || is_product_tag() ) && 'yes' === THEMECOMPLETE_EPO()->tm_epo_enable_in_shop ) ) ) )
		) {
			return;// bad function call.
		}

		$this->tm_epo_fields( $product_id, $form_prefix, false, $dummy_prefix );
		$this->tm_add_inline_style();
		$this->tm_epo_totals( $product_id, $form_prefix );
		if ( ! THEMECOMPLETE_EPO()->is_bto ) {
			$this->tm_options_have_been_displayed = true;
		}

	}

	/**
	 * Batch add plugin options
	 *
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $dummy_prefix If we should use the form prefix.
	 */
	private function tm_epo_fields_batch( $form_prefix = '', $dummy_prefix = false ) {

		foreach ( $this->current_product_id_to_be_displayed_check as $key => $product_id ) {
			if ( ! empty( $product_id ) ) {
				$this->inline_styles      = '';
				$this->inline_styles_head = '';

				$this->tm_variation_css_check( 1, $product_id );

				$this->tm_epo_fields( $product_id, $form_prefix, false, $dummy_prefix );
				$this->tm_add_inline_style();

				if ( THEMECOMPLETE_EPO()->tm_epo_options_placement === THEMECOMPLETE_EPO()->tm_epo_totals_box_placement ) {
					$this->tm_epo_totals( $product_id, $form_prefix );
				} else {
					if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
						unset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] );
					}
				}
			}
		}
		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
			if ( THEMECOMPLETE_EPO()->tm_epo_options_placement !== THEMECOMPLETE_EPO()->tm_epo_totals_box_placement ) {
				$this->epo_internal_counter       = 0;
				$this->epo_internal_counter_check = [];
			}
		}

	}

	/**
	 * Display the options in the frontend
	 *
	 * @param integer $product_id The product id.
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $is_from_shortcode If we are in a shortcode.
	 * @param boolean $dummy_prefix If we should use the form prefix.
	 */
	public function tm_epo_fields( $product_id = 0, $form_prefix = '', $is_from_shortcode = false, $dummy_prefix = false ) {

		if ( $this->block_epo ) {
			return;
		}

		global $woocommerce;

		if ( ! empty( $GLOBALS['THEMECOMPLETE_IS_FROM_SHORTCODE'] ) ) {
			$is_from_shortcode = true;
		}

		if ( ! property_exists( $woocommerce, 'product_factory' )
			|| null === $woocommerce->product_factory
			|| ( $this->tm_options_have_been_displayed && ( ! ( THEMECOMPLETE_EPO()->is_bto || ( ( THEMECOMPLETE_EPO()->is_enabled_shortcodes() && ! $is_from_shortcode ) && ! is_product() ) || ( ( is_shop() || is_product_category() || is_product_tag() ) && 'yes' === THEMECOMPLETE_EPO()->tm_epo_enable_in_shop ) ) ) )
		) {
			return;// bad function call.
		}

		if ( $product_id instanceof WC_PRODUCT ) {
			$product    = $product_id;
			$product_id = themecomplete_get_id( $product );
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

		if ( 'grouped' === $type ) {
			return;
		}

		// Always dispay composite hidden fields if product is composite.
		if ( $form_prefix ) {
			$_bto_id     = $form_prefix;
			$form_prefix = '_' . $form_prefix;
			if ( THEMECOMPLETE_EPO()->is_bto ) {
				echo '<input type="hidden" class="cpf-bto-id" name="cpf_bto_id[]" value="' . esc_attr( $form_prefix ) . '">';
				echo '<input type="hidden" value="" name="cpf_bto_price[' . esc_attr( $_bto_id ) . ']" class="cpf-bto-price">';
				echo '<input type="hidden" value="0" name="cpf_bto_optionsprice[]" class="cpf-bto-optionsprice">';
			}
		}

		if ( ! $form_prefix ) {
			if ( THEMECOMPLETE_EPO()->is_quick_view() ) {
				if ( ! $this->unique_form_prefix ) {
					$this->unique_form_prefix = uniqid( '' );
				}
				$form_prefix = '_tcform' . $this->unique_form_prefix;
			} elseif ( THEMECOMPLETE_EPO()->wc_vars['is_page'] ) {
				// Workaroung to cover options in pages.
				if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
					$temp_this_epo_internal_counter = $this->epo_internal_counter;
					if ( empty( $temp_this_epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ 'tc' . $temp_this_epo_internal_counter ] ) ) {
						// First time displaying the fields and totals haven't been displayed.
						$temp_this_epo_internal_counter ++;
					}
					$temp_epo_internal_counter = $temp_this_epo_internal_counter;
				} else {
					$temp_epo_internal_counter = 0;
				}

				$form_prefix = '_tcform' . $temp_epo_internal_counter;
			}
		}

		$post_id = $product_id;

		$cpf_price_array = THEMECOMPLETE_EPO()->get_product_tm_epos( $post_id, $form_prefix );

		if ( ! $cpf_price_array ) {
			return;
		}
		$global_price_array = $cpf_price_array['global'];
		$local_price_array  = $cpf_price_array['local'];

		if ( empty( $global_price_array ) && empty( $local_price_array ) ) {
			if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
				if ( empty( $this->epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] ) ) {
					// First time displaying the fields and totals haven't been displayed.
					$this->epo_internal_counter ++;
					$this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] = $this->epo_internal_counter;
				} else {
					// Totals have already been displayed.
					unset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] );

					$this->current_product_id_to_be_displayed = 0;
					$this->unique_form_prefix                 = '';
				}
				$_epo_internal_counter = $this->epo_internal_counter;
			} else {
				$_epo_internal_counter = 0;
			}

			return;
		}

		$global_prices = [
			'before' => [],
			'after'  => [],
		];
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

		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
			if ( empty( $this->epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] ) ) {
				// First time displaying the fields and totals haven't been displayed.
				$this->epo_internal_counter ++;
				$this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] = $this->epo_internal_counter;
			} else {
				// Totals have already been displayed.
				unset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] );

				$this->current_product_id_to_be_displayed = 0;
				$this->unique_form_prefix                 = '';
			}
			$_epo_internal_counter = $this->epo_internal_counter;
		} else {
			if ( THEMECOMPLETE_EPO()->is_inline_epo && $this->epo_internal_counter ) {
				$_epo_internal_counter = $this->epo_internal_counter;
			} else {
				$_epo_internal_counter = 0;
			}
		}

		$forcart   = 'main';
		$classcart = 'tm-cart-main';
		if ( ! empty( $form_prefix ) ) {
			$forcart   = $form_prefix;
			$classcart = 'tm-cart-' . str_replace( '_', '', $form_prefix );
		}
		$isfromshortcode = '';
		if ( ! empty( $is_from_shortcode ) ) {
			$isfromshortcode = ' tc-shortcode';
		}

		global $wp_filter;
		$saved_filter = false;
		if ( isset( $wp_filter['image_downsize'] ) ) {
			$saved_filter = $wp_filter['image_downsize'];
			unset( $wp_filter['image_downsize'] );
		}

		wc_get_template(
			'tm-start.php',
			[
				'isfromshortcode'      => $isfromshortcode,
				'classcart'            => $classcart,
				'forcart'              => $forcart,
				'form_prefix'          => str_replace( '_', '', $form_prefix ),
				'product_id'           => $product_id,
				'epo_internal_counter' => $_epo_internal_counter,
				'is_from_shortcode'    => $is_from_shortcode,
			],
			$this->get_template_path(),
			$this->get_default_path()
		);

		if ( ( ! empty( THEMECOMPLETE_EPO()->cart_edit_key ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'tm-edit' ) ) || ( ! empty( THEMECOMPLETE_EPO()->cart_edit_key ) && isset( $_REQUEST['update-composite'] ) ) ) {
			$_cart = WC()->cart;
			if ( isset( $_cart->cart_contents ) && isset( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ] ) ) {
				if ( ! empty( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata'] ) ) {
					if ( ! empty( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata']['tmcp_post_fields'] ) && is_array( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata']['tmcp_post_fields'] ) ) {
						$tmcp_post_fields  = $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata']['tmcp_post_fields'];
						$saved_form_prefix = $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata']['form_prefix'];
						foreach ( $tmcp_post_fields as $posted_name => $posted_value ) {
							if ( '' !== $saved_form_prefix ) {
								$posted_name = str_replace( $saved_form_prefix, '', $posted_name );
							}
							$_REQUEST[ $posted_name ] = $posted_value;
						}
					}
				}
			}
		}

		// global options before local.
		foreach ( $global_prices['before'] as $priorities ) {
			foreach ( $priorities as $gid => $field ) {
				$args    = [
					'tabindex'        => $tabindex,
					'unit_counter'    => $unit_counter,
					'field_counter'   => $field_counter,
					'element_counter' => $element_counter,
					'_currency'       => $_currency,
					'product_id'      => $product_id,
					'gid'             => $gid,
				];
				$_return = $this->get_builder_display( $post_id, $field, 'before', $args, $form_prefix, $dummy_prefix );

				$tabindex        = $_return['tabindex'];
				$unit_counter    = $_return['unit_counter'];
				$field_counter   = $_return['field_counter'];
				$element_counter = $_return['element_counter'];
				$_currency       = $_return['_currency'];

			}
		}

		$args    = [
			'tabindex'        => $tabindex,
			'unit_counter'    => $unit_counter,
			'field_counter'   => $field_counter,
			'element_counter' => $element_counter,
			'_currency'       => $_currency,
			'product_id'      => $product_id,
		];
		$_return = $this->get_normal_display( $local_price_array, $args, $form_prefix, $dummy_prefix );

		$tabindex        = $_return['tabindex'];
		$unit_counter    = $_return['unit_counter'];
		$field_counter   = $_return['field_counter'];
		$element_counter = $_return['element_counter'];
		$_currency       = $_return['_currency'];

		// global options after local.
		foreach ( $global_prices['after'] as $priorities ) {
			foreach ( $priorities as $gid => $field ) {
				$args    = [
					'tabindex'        => $tabindex,
					'unit_counter'    => $unit_counter,
					'field_counter'   => $field_counter,
					'element_counter' => $element_counter,
					'_currency'       => $_currency,
					'product_id'      => $product_id,
					'gid'             => $gid,
				];
				$_return = $this->get_builder_display( $post_id, $field, 'after', $args, $form_prefix, $dummy_prefix );

				$tabindex        = $_return['tabindex'];
				$unit_counter    = $_return['unit_counter'];
				$field_counter   = $_return['field_counter'];
				$element_counter = $_return['element_counter'];
				$_currency       = $_return['_currency'];
			}
		}

		wc_get_template(
			'tm-end.php',
			[],
			$this->get_template_path(),
			$this->get_default_path()
		);

		if ( $saved_filter ) {
			$wp_filter['image_downsize'] = $saved_filter; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}

		$this->tm_options_single_have_been_displayed = true;

	}

	/**
	 * Displays the option created from the builder mode
	 *
	 * @param integer $post_id The post id.
	 * @param array   $field The field options array.
	 * @param string  $where The field placement 'before' or 'after'.
	 * @param array   $args The variable arguemnts..
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $dummy_prefix If we should use the form prefix.
	 * @since 1.0
	 */
	public function get_builder_display( $post_id, $field, $where, $args, $form_prefix = '', $dummy_prefix = false ) {

		if ( ! $post_id ) {
			$post_id = 0;
		}
		$columns = [];
		for ( $x = 1; $x <= 100; $x++ ) {
			$columns[ 'w' . $x ] = [ 'tcwidth-' . $x, $x ];
		}
		$columns['w12-5'] = [ 'tcwidth-12-5', 12.5 ];
		$columns['w37-5'] = [ 'tcwidth-37-5', 37.5 ];
		$columns['w62-5'] = [ 'tcwidth-62-5', 62.5 ];
		$columns['w87-5'] = [ 'tcwidth-87-5', 87.5 ];

		$tabindex        = $args['tabindex'];
		$unit_counter    = $args['unit_counter'];
		$field_counter   = $args['field_counter'];
		$element_counter = $args['element_counter'];
		$_currency       = $args['_currency'];
		$product_id      = $args['product_id'];
		$gid             = isset( $args['gid'] ) ? $args['gid'] : '0';

		$element_type_counter = [];

		if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {

			$args = [
				'field_id' => 'tc-epo-form-' . $gid . '-' . $unit_counter,
			];
			wc_get_template(
				'tm-builder-start.php',
				$args,
				$this->get_template_path(),
				$this->get_default_path()
			);

			$_section_totals = 0;

			foreach ( $field['sections'] as $section ) {
				if ( ! isset( $section['sections_placement'] ) || $section['sections_placement'] !== $where ) {
					continue;
				}
				if ( isset( $section['sections_size'] ) && isset( $columns[ $section['sections_size'] ] ) ) {
					$size = $columns[ $section['sections_size'] ][0];
				} else {
					$size = 'tcwidth-100';
				}

				$_section_totals = $_section_totals + $columns[ $section['sections_size'] ][1];
				if ( $_section_totals > 100 ) {
					$_section_totals = $columns[ $section['sections_size'] ][1];
				}

				$divider = isset( $section['divider_type'] ) ? $section['divider_type'] : '';

				$label_size = 'h3';
				if ( ! empty( $section['label_size'] ) ) {
					switch ( $section['label_size'] ) {
						case '1':
							$label_size = 'h1';
							break;
						case '2':
							$label_size = 'h2';
							break;
						case '3':
							$label_size = 'h3';
							break;
						case '4':
							$label_size = 'h4';
							break;
						case '5':
							$label_size = 'h5';
							break;
						case '6':
							$label_size = 'h6';
							break;
						case '7':
							$label_size = 'p';
							break;
						case '8':
							$label_size = 'div';
							break;
						case '9':
							$label_size = 'span';
							break;
					}
				}

				$section_args = [
					'column'                       => $size,
					'style'                        => $section['sections_style'],
					'uniqid'                       => $section['sections_uniqid'],
					'logic'                        => wp_json_encode( (array) json_decode( stripslashes_deep( $section['sections_clogic'] ) ) ),
					'haslogic'                     => $section['sections_logic'],
					'sections_class'               => $section['sections_class'],
					'sections_type'                => $section['sections_type'],
					'sections_popupbutton'         => $section['sections_popupbutton'],
					'sections_popupbuttontext'     => $section['sections_popupbuttontext'],
					'sections_background_color'    => $section['sections_background_color'],
					'label_background_color'       => $section['label_background_color'],
					'description_background_color' => $section['description_background_color'],
					'label_size'                   => $label_size,
					'label'                        => ! empty( $section['label'] ) ? $section['label'] : '',
					'label_color'                  => ! empty( $section['label_color'] ) ? $section['label_color'] : '',
					'label_position'               => ! empty( $section['label_position'] ) ? $section['label_position'] : '',
					'description'                  => ! empty( $section['description'] ) ? $section['description'] : '',
					'description_color'            => ! empty( $section['description_color'] ) ? $section['description_color'] : '',
					'description_position'         => ! empty( $section['description_position'] ) ? $section['description_position'] : '',
					'divider'                      => $divider,
				];

				// custom variations check.
				if (
					isset( $section['elements'] )
					&& is_array( $section['elements'] )
					&& isset( $section['elements'][0] )
					&& is_array( $section['elements'][0] )
					&& isset( $section['elements'][0]['type'] )
					&& 'variations' === $section['elements'][0]['type']
				) {
					if ( THEMECOMPLETE_EPO()->associated_type === 'variation' ) {
						continue;
					}
					$section_args['sections_class'] = $section_args['sections_class'] . ' tm-epo-variation-section tc-clearfix';

					if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
						$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $post_id, 'product' );
					}
					if ( ( isset( $wpml_is_original_product ) && empty( $wpml_is_original_product ) ) ) {
						if (
							isset( $section['elements'][0]['original_builder'] ) &&
							isset( $section['elements'][0]['original_builder']['variations_disabled'] ) &&
							(int) 1 === (int) $section['elements'][0]['original_builder']['variations_disabled']
						) {
							$section_args['sections_class'] .= ' tm-hidden';
						}
					} elseif (
						isset( $section['elements'][0]['builder'] ) &&
						isset( $section['elements'][0]['builder']['variations_disabled'] ) &&
						(int) 1 === (int) $section['elements'][0]['builder']['variations_disabled']
					) {
						$section_args['sections_class'] .= ' tm-hidden';
					}
				}
				if ( '' !== $section_args['style'] ) {
					$section_args['label_position'] = '';
				}
				wc_get_template(
					'tm-builder-section-start.php',
					$section_args,
					$this->get_template_path(),
					$this->get_default_path()
				);

				if ( isset( $section['elements'] ) && is_array( $section['elements'] ) ) {
					$totals = 0;

					$slide_counter = 0;
					$use_slides    = false;
					$doing_slides  = false;
					if ( '' !== $section['sections_slides'] && ( 'slider' === $section['sections_type'] || 'tabs' === $section['sections_type'] ) ) {
						$sections_slides = explode( ',', $section['sections_slides'] );
						$use_slides      = true;

						if ( 'tabs' === $section['sections_type'] ) {
							$sections_tabs_labels = isset( $section['sections_tabs_labels'] ) ? $section['sections_tabs_labels'] : '';
							$sections_tabs_labels = json_decode( $sections_tabs_labels );

							if ( ! is_array( $sections_tabs_labels ) ) {
								$sections_slides = '';
								$use_slides      = false;
							} else {
								echo '<div class="tc-tabs tc-container"><div class="tc-tabs-wrap tcwidth-100">';
								echo '<div class="tc-tab-headers tcwidth-100">';
								foreach ( $sections_tabs_labels as $tab_index => $tab_label ) {
									echo '<div class="tc-tab-header tma-tab-label"><h4 tabindex="0" data-id="tc-tab-slide' . esc_attr( $tab_index ) . '" data-tab="tab' . esc_attr( $tab_index ) . '" class="tab-header' . ( 0 === $tab_index ? ' open' : '' ) . '"><span class="tab-header-label">';
									echo apply_filters( 'wc_epo_kses', wp_kses_post( $tab_label ), $tab_label, false ); // phpcs:ignore WordPress.Security.EscapeOutput
									echo '</span></h4></div>';
								}
								echo '</div>';
								echo '<div class="tc-tab-content tcwidth-100">';
							}
						} elseif ( 'slider' === $section['sections_type'] ) {
							echo '<div class="tc-slider-content">';
						}
					}

					foreach ( $section['elements'] as $element ) {

						$element = apply_filters( 'wc_epo_get_element_for_display', $element );

						$empty_rules = '';
						if ( isset( $element['rules_filtered'] ) ) {
							$empty_rules = wp_json_encode( ( $element['rules_filtered'] ) );
						}
						$empty_original_rules = '';
						if ( isset( $element['original_rules_filtered'] ) ) {
							$empty_original_rules = wp_json_encode( ( $element['original_rules_filtered'] ) );
						}
						if ( empty( $empty_original_rules ) ) {
							$empty_original_rules = '';
						}
						$empty_rules_type = '';
						if ( isset( $element['rules_type'] ) ) {
							$empty_rules_type = wp_json_encode( ( $element['rules_type'] ) );
						}
						if ( isset( $element['size'] ) && isset( $columns[ $element['size'] ] ) ) {
							$size = $columns[ $element['size'] ][0];
						} else {
							$size = 'tcwidth-100';
						}
						$test_for_first_slide = false;
						if ( $use_slides && isset( $sections_slides[ $slide_counter ] ) ) {
							$sections_slides[ $slide_counter ] = (int) $sections_slides[ $slide_counter ];

							if ( $sections_slides[ $slide_counter ] > 0 && ! $doing_slides ) {
								echo '<div class="tc-tab-slide tc-tab-slide' . esc_attr( $slide_counter ) . ' tc-row">';
								$doing_slides         = true;
								$test_for_first_slide = true;
							}
						}

						$cart_fee_name = THEMECOMPLETE_EPO()->cart_fee_name;
						$totals        = $totals + $columns[ $element['size'] ][1];
						if ( $totals > 100 && ! $test_for_first_slide ) {
							$totals = $columns[ $element['size'] ][1];
						}

						$repeater              = isset( $element['repeater'] ) ? $element['repeater'] : '';
						$repeater_quantity     = isset( $element['repeater_quantity'] ) ? $element['repeater_quantity'] : '';
						$repeater_min_rows     = isset( $element['repeater_min_rows'] ) ? $element['repeater_min_rows'] : '';
						$repeater_max_rows     = isset( $element['repeater_max_rows'] ) ? $element['repeater_max_rows'] : '';
						$repeater_button_label = isset( $element['repeater_button_label'] ) ? $element['repeater_button_label'] : '';

						$divider       = isset( $element['divider_type'] ) ? $element['divider_type'] : '';
						$divider_class = '';
						if ( isset( $element['divider_type'] ) ) {
							$divider_class = '';
							if ( 'divider' === $element['type'] && ! empty( $element['class'] ) ) {
								$divider_class = ' ' . $element['class'];
							}
						}
						$label_size = 'h3';
						if ( ! empty( $element['label_size'] ) ) {
							switch ( $element['label_size'] ) {
								case '1':
									$label_size = 'h1';
									break;
								case '2':
									$label_size = 'h2';
									break;
								case '3':
									$label_size = 'h3';
									break;
								case '4':
									$label_size = 'h4';
									break;
								case '5':
									$label_size = 'h5';
									break;
								case '6':
									$label_size = 'h6';
									break;
								case '7':
									$label_size = 'p';
									break;
								case '8':
									$label_size = 'div';
									break;
								case '9':
									$label_size = 'span';
									break;
								case '10':
									$label_size = 'label';
									break;
							}
						}

						$variations_builder_element_start_args = [];
						$tm_validation                         = $this->get_tm_validation_rules( $element );
						$args                                  = apply_filters(
							'wc_epo_builder_element_start_args',
							[
								'tm_element_settings'  => $element,
								'column'               => $size,
								'class'                => ! empty( $element['class'] ) ? $element['class'] : '',
								'container_id'         => ! empty( $element['container_id'] ) ? $element['container_id'] : '',
								'label_size'           => $label_size,
								'label'                => ! empty( $element['label'] ) ? $element['label'] : '',
								'label_position'       => ! empty( $element['label_position'] ) ? $element['label_position'] : '',
								'label_color'          => ! empty( $element['label_color'] ) ? $element['label_color'] : '',
								'description'          => ! empty( $element['description'] ) ? $element['description'] : '',
								'description_color'    => ! empty( $element['description_color'] ) ? $element['description_color'] : '',
								'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : '',
								'divider'              => $divider,
								'divider_class'        => $divider_class,
								'required'             => $element['required'],
								'type'                 => $element['type'],
								'replacement_mode'     => $element['replacement_mode'],
								'swatch_position'      => $element['swatch_position'],
								'use_url'              => $element['use_url'],
								'enabled'              => $element['enabled'],
								'rules'                => $empty_rules,
								'original_rules'       => $empty_original_rules,
								'rules_type'           => $empty_rules_type,
								'element'              => $element['type'],
								'class_id'             => 'tm-element-ul-' . $element['type'] . ' element_' . $element_counter . $form_prefix, // this goes on ul.
								'uniqid'               => $element['uniqid'],
								'logic'                => wp_json_encode( (array) json_decode( stripslashes_deep( $element['clogic'] ) ) ),
								'haslogic'             => $element['logic'],
								'clear_options'        => empty( $element['clear_options'] ) ? '' : $element['clear_options'],
								'limit'                => empty( $element['limit'] ) ? '' : 'tm-limit',
								'exactlimit'           => empty( $element['exactlimit'] ) ? '' : 'tm-exactlimit',
								'minimumlimit'         => empty( $element['minimumlimit'] ) ? '' : 'tm-minimumlimit',
								'tm_validation'        => wp_json_encode( ( $tm_validation ) ),
								'extra_class'          => '',
								'repeater'             => $repeater,
								'repeater_quantity'    => $repeater_quantity,
								'repeater_min_rows'    => $repeater_min_rows,
								'repeater_max_rows'    => $repeater_max_rows,
							],
							$element,
							$element_counter,
							$form_prefix
						);

						if ( 'product' === $element['type'] ) {
							$args['extra_class']       = 'cpf-type-product-' . $element['layout_mode'] . ' cpf-type-product-mode-' . $element['mode'];
							$args['element_data_attr'] = [
								'data-mode'                => isset( $element['mode'] ) ? $element['mode'] : '',
								'data-product-layout-mode' => isset( $element['layout_mode'] ) ? $element['layout_mode'] : '',
								'data-quantity-min'        => isset( $element['quantity_min'] ) ? $element['quantity_min'] : '',
								'data-quantity-max'        => isset( $element['quantity_max'] ) ? $element['quantity_max'] : '',
								'data-priced-individually' => isset( $element['priced_individually'] ) ? $element['priced_individually'] : '',
								'data-discount'            => isset( $element['discount'] ) ? $element['discount'] : '',
								'data-discount-type'       => isset( $element['discount_type'] ) ? $element['discount_type'] : '',
								'data-discount-exclude-addons' => isset( $element['discount_exclude_addons'] ) ? $element['discount_exclude_addons'] : '',
								'data-show-image'          => isset( $element['show_image'] ) ? $element['show_image'] : '1',
								'data-show-title'          => isset( $element['show_title'] ) ? $element['show_title'] : '1',
								'data-show-price'          => isset( $element['show_price'] ) ? $element['show_price'] : '1',
								'data-show-description'    => isset( $element['show_description'] ) ? $element['show_description'] : '1',
								'data-show-meta'           => isset( $element['show_meta'] ) ? $element['show_meta'] : '1',
								'data-disable-epo'         => isset( $element['disable_epo'] ) ? $element['disable_epo'] : '',
							];
							if ( 'product' !== $element['mode'] ) {
								if ( 'radio' === $element['layout_mode'] || 'thumbnail' === $element['layout_mode'] ) {
									$args['clear_options'] = '1';
								}
							}
						}

						if ( 'variations' === $element['type'] ) {
							$variations_builder_element_start_args = $args;
						}

						if ( ( 'variations' !== $element['type'] && $element['enabled'] ) || 'variations' === $element['type'] ) {

							$field_counter = 0;

							$init_class = 'THEMECOMPLETE_EPO_FIELDS_' . $element['type'];
							if ( ! class_exists( $init_class ) && isset( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ] ) && ! empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]->is_addon ) ) {
								$init_class = 'THEMECOMPLETE_EPO_FIELDS';
							}

							if ( isset( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ] )
								&& ( 'post' === THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]->is_post || 'display' === THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]->is_post )
								&& class_exists( $init_class )
							) {

								$element_object = THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ];

								if ( 'post' === $element_object->is_post ) {

									if ( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {

										$name_inc    = $element['raw_name_inc'] . ( $dummy_prefix ? '' : $element['raw_name_inc_prefix'] );
										$is_cart_fee = ! empty( $element['is_cart_fee'] );

										if ( ! isset( $element_type_counter[ $element['type'] ] ) ) {
											$element_type_counter[ $element['type'] ] = 0;
										}

										$posted_name = 'tmcp_' . $name_inc;

										$get_posted_name = [ '' ];
										if ( isset( $_REQUEST[ $posted_name ] ) ) {
											$get_posted_name = wp_unslash( $_REQUEST[ $posted_name ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
											if ( ! is_array( $get_posted_name ) ) {
												$get_posted_name = [ $get_posted_name ];
											}
										}

										if ( count( $get_posted_name ) > 1 && 'multipleallsingle' === $element_object->type ) {
											$get_posted_name = [ $get_posted_name[0] ];
										}

										do_action( 'wc_epo_get_builder_display_single', $element, $name_inc, null );

										$fieldtype = 'tmcp-field';
										if ( $is_cart_fee ) {
											$fieldtype = THEMECOMPLETE_EPO()->cart_fee_class;
										}
										if ( ! empty( $element['class'] ) ) {
											$fieldtype .= ' ' . $element['class'];
										}

										if ( THEMECOMPLETE_EPO()->get_element_price_type( '', $element, 0, 1, 0 ) === 'math' ) {
											$fieldtype .= ' tc-is-math';
										}

										$uniqid_suffix = uniqid();

										$args['get_posted_key_count'] = count( $get_posted_name );

										foreach ( $get_posted_name as $get_posted_key => $get_posted_value ) {
											$html_name          = $posted_name . ( ! empty( $repeater ) ? '[' . $get_posted_key . ']' : '' );
											$html_quantity_name = $posted_name . '_quantity' . ( ! empty( $repeater ) ? '[' . $get_posted_key . ']' : '' );
											if ( 'singlemultiple' === $element_object->type ) {
												if ( empty( $repeater ) ) {
													$html_name          = $posted_name . '[0][]';
													$html_quantity_name = $posted_name . '_quantity[0]';
												} else {
													$html_name .= '[]';
												}
											}
											$args['get_posted_key'] = $get_posted_key;
											$tabindex ++;
											$field_obj = new $init_class();
											$display   = $field_obj->display_field(
												$element,
												[
													'id'   => 'tmcp_' . $element_object->post_name_prefix . '_' . $tabindex . $form_prefix . $uniqid_suffix,
													'get_posted_key' => $get_posted_key,
													'repeater' => $repeater,
													'name' => $html_name,
													'name_inc' => $name_inc,
													'posted_name' => $posted_name,
													'element_counter' => $element_counter,
													'tabindex' => $tabindex,
													'form_prefix' => $form_prefix,
													'fieldtype' => $fieldtype,
													'field_counter' => $field_counter,
													'product_id' => isset( $product_id ) ? $product_id : 0,
												]
											);

											if ( is_array( $display ) ) {

												$original_amount = '';
												if ( isset( $element['original_rules_filtered'][0] ) && isset( $element['original_rules_filtered'][0][0] ) ) {
													$original_amount = $element['original_rules_filtered'][0][0];
												} else {
													$selected_index = 0;
													if ( isset( $element['default_value'] ) && '' !== $element['default_value'] && isset( $selected_index[ $element['default_value'] ] ) ) {
														$selected_index = array_keys( $element['options'] );
														$selected_index = $selected_index[ $element['default_value'] ];
													} else {
														$selected_index = array_keys( $element['options'] );
														$selected_index = $selected_index[0];
													}
													$original_amount = $element['original_rules_filtered'][ esc_attr( $selected_index ) ];
													if ( isset( $original_amount[0] ) ) {
														$original_amount = $original_amount[0];
													} else {
														$original_amount = '';
													}
												}
												if ( isset( $display['default_value_counter'] ) && false !== $display['default_value_counter'] ) {
													$original_amount = $element['original_rules_filtered'][ $display['default_value_counter'] ][0];
												}

												$amount = '';
												if ( isset( $element['rules_filtered'][0] ) && isset( $element['rules_filtered'][0][0] ) ) {
													$amount = $element['rules_filtered'][0][0];
												} else {
													$selected_index = 0;
													if ( isset( $element['default_value'] ) && '' !== $element['default_value'] && isset( $selected_index[ $element['default_value'] ] ) ) {
														$selected_index = array_keys( $element['options'] );
														$selected_index = $selected_index[ $element['default_value'] ];
													} else {
														$selected_index = array_keys( $element['options'] );
														$selected_index = $selected_index[0];
													}
													$amount = $element['rules_filtered'][ esc_attr( $selected_index ) ];
													if ( isset( $amount[0] ) ) {
														$amount = $amount[0];
													} else {
														$amount = '';
													}
												}
												if ( isset( $display['default_value_counter'] ) && false !== $display['default_value_counter'] ) {
													$amount = $element['rules_filtered'][ $display['default_value_counter'] ][0];
												}

												$original_rules = isset( $element['original_rules_filtered'] ) ? wp_json_encode( ( $element['original_rules_filtered'] ) ) : '';
												if ( empty( $original_rules ) ) {
													$original_rules = '';
												}

												$element_args = [
													'id'   => 'tmcp_' . $element_object->post_name_prefix . '_' . $tabindex . $form_prefix . $uniqid_suffix,
													'name' => $html_name,
													'get_posted_key' => $get_posted_key,
													'posted_name' => $posted_name,
													'quantity_name' => $html_quantity_name,
													'amount' => '',
													'original_amount' => '',
													'required' => $element['required'],
													'tabindex' => $tabindex,
													'fieldtype' => $fieldtype,
													'rules' => isset( $element['rules_filtered'] ) ? wp_json_encode( ( $element['rules_filtered'] ) ) : '',
													'original_rules' => $original_rules,
													'rules_type' => isset( $element['rules_type'] ) ? wp_json_encode( ( $element['rules_type'] ) ) : '',
													'tm_element_settings' => $element,
													'class' => ! empty( $element['class'] ) ? $element['class'] : '',
													'field_counter' => $field_counter,
													'tax_obj' => ! $is_cart_fee ? false : wp_json_encode(
														( [
															'is_fee'    => $is_cart_fee,
															'has_fee'   => isset( $element['include_tax_for_fee_price_type'] ) ? $element['include_tax_for_fee_price_type'] : '',
															'tax_class' => isset( $element['tax_class_for_fee_price_type'] ) ? $element['tax_class_for_fee_price_type'] : '',
															'tax_rate'  => $this->get_tax_rate( isset( $element['tax_class_for_fee_price_type'] ) ? $element['tax_class_for_fee_price_type'] : '' ),
														] )
													),
												];

												$element_args         = apply_filters( 'wc_epo_display_template_args', array_merge( $element_args, $display ), $element, false, false, $element_type_counter[ $element['type'] ] );
												$element_args['args'] = $element_args;

												if ( 'variations' !== $element['type'] ) {
													if ( $element['enabled'] ) {
														wc_get_template(
															'tm-builder-element-start.php',
															$args,
															$this->get_template_path(),
															$this->get_default_path()
														);
													}
												}
												if ( $element_object->is_addon ) {
													do_action(
														'tm_epo_display_addons',
														$element,
														$element_args,
														[
															'name_inc'        => $name_inc,
															'element_counter' => $element_counter,
															'tabindex'        => $tabindex,
															'form_prefix'     => $form_prefix,
															'field_counter'   => $field_counter,
														],
														$element_object->namespace
													);
												} elseif ( is_readable( apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, $element['type'], $element ) . apply_filters( 'wc_epo_template_element', 'tm-' . $element['type'] . '.php', $element['type'], $element ) ) ) {
													wc_get_template(
														apply_filters( 'wc_epo_template_element', 'tm-' . $element['type'] . '.php', $element['type'], $element ),
														$element_args,
														$this->get_template_path(),
														apply_filters( 'wc_epo_template_path_element', $this->get_default_path(), $element['type'], $element )
													);
												}
												if ( 'variations' !== $element['type'] ) {
													if ( $element['enabled'] ) {
														wc_get_template(
															'tm-builder-element-end.php',
															[
																'repeater' => $repeater,
																'repeater_quantity' => $repeater_quantity,
																'repeater_min_rows'    => $repeater_min_rows,
																'repeater_max_rows'    => $repeater_max_rows,
																'repeater_button_label' => $repeater_button_label,
																'get_posted_key' => $get_posted_key,
																'get_posted_key_count' => $args['get_posted_key_count'],
																'tm_element_settings' => $element,
																'element'     => $element['type'],
																'enabled'     => $element['enabled'],
																'description' => ! empty( $element['description'] ) ? $element['description'] : '',
																'description_color' => ! empty( $element['description_color'] ) ? $element['description_color'] : '',
																'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : '',
															],
															$this->get_template_path(),
															$this->get_default_path()
														);
													}
												}
											}
											unset( $field_obj );
										}

										$element_type_counter[ $element['type'] ] ++;

									} elseif ( 'multipleall' === $element_object->type || 'multiple' === $element_object->type ) {

										if ( ! isset( $element_type_counter[ $element['type'] ] ) ) {
											$element_type_counter[ $element['type'] ] = 0;
										}

										$get_posted_name = [];

										if ( 'multipleall' === $element_object->type ) {
											$_field_counter = 0;
											foreach ( $element['options'] as $value => $label ) {

												$name_inc = $element['raw_name_inc'][ $_field_counter ] . ( $dummy_prefix ? '' : $element['raw_name_inc_prefix'][ $_field_counter ] );

												$is_cart_fee = ! empty( $element['is_cart_fee_multiple'][ $_field_counter ] );

												$posted_name = 'tmcp_' . $name_inc;

												if ( isset( $_REQUEST[ $posted_name ] ) && is_array( $_REQUEST[ $posted_name ] ) ) {
													$get_posted_name = array_replace( $get_posted_name, wp_unslash( $_REQUEST[ $posted_name ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
													if ( ! is_array( $get_posted_name ) ) {
														$get_posted_name = [ $get_posted_name ];
													}
													end( $get_posted_name );
													$get_posted_max = key( $get_posted_name );
													if ( $get_posted_max >= count( $get_posted_name ) ) {
														$get_posted_name = $get_posted_name + array_diff_key( array_fill( 0, $get_posted_max, false ), $get_posted_name );
														ksort( $get_posted_name );
													}
												}

												$_field_counter ++;

											}
										} else {
											if ( ! isset( $element['raw_name_inc'] ) ) {
												$name_inc        = '';
												$posted_name     = '';
												$get_posted_name = [];
											} else {
												$name_inc = $element['raw_name_inc'][0] . ( $dummy_prefix ? '' : $element['raw_name_inc_prefix'][0] );

												$is_cart_fee = ! empty( $element['is_cart_fee_multiple'][0] );

												$posted_name = 'tmcp_' . $name_inc;

												if ( isset( $_REQUEST[ $posted_name ] ) && is_array( $_REQUEST[ $posted_name ] ) ) {
													$get_posted_name = wp_unslash( $_REQUEST[ $posted_name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
													if ( ! is_array( $get_posted_name ) ) {
														$get_posted_name = [ $get_posted_name ];
													}
													end( $get_posted_name );
													$get_posted_max = key( $get_posted_name );
													if ( $get_posted_max >= count( $get_posted_name ) ) {
														$get_posted_name = $get_posted_name + array_diff_key( array_fill( 0, $get_posted_max, false ), $get_posted_name );
														ksort( $get_posted_name );
													}
												}
											}
										}

										if ( empty( $get_posted_name ) ) {
											$get_posted_name = [ '' ];
										}

										$args['get_posted_key_count'] = count( $get_posted_name );

										foreach ( $get_posted_name as $get_posted_key => $get_posted_value ) {
											$field_counter          = 0;
											$args['get_posted_key'] = $get_posted_key;

											if ( 'variations' !== $element['type'] ) {
												if ( $element['enabled'] ) {
													wc_get_template(
														'tm-builder-element-start.php',
														$args,
														$this->get_template_path(),
														$this->get_default_path()
													);
												}
											}

											$field_obj = new $init_class();
											$field_obj->display_field_pre(
												$element,
												[
													'element_counter' => $element_counter,
													'tabindex' => $tabindex,
													'form_prefix' => $form_prefix,
													'field_counter' => $field_counter,
													'product_id' => isset( $product_id ) ? $product_id : 0,
												]
											);

											$choice_counter = 0;

											foreach ( $element['options'] as $value => $label ) {

												$tabindex ++;

												$name_inc = $element['raw_name_inc'][ $field_counter ] . ( $dummy_prefix ? '' : $element['raw_name_inc_prefix'][ $field_counter ] );

												$is_cart_fee = ! empty( $element['is_cart_fee_multiple'][ $field_counter ] );

												$posted_name = 'tmcp_' . $name_inc;
												do_action( 'wc_epo_get_builder_display_single', $element, $name_inc, $value );

												$html_name          = $posted_name . ( ! empty( $repeater ) ? '[' . $get_posted_key . ']' : '' );
												$html_quantity_name = $posted_name . '_quantity' . ( ! empty( $repeater ) ? '[' . $get_posted_key . ']' : '' );

												if ( ! empty( THEMECOMPLETE_EPO()->cart_edit_key ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'tm-edit' ) ) {
													$_cart = WC()->cart;
													if ( isset( $_cart->cart_contents ) && isset( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ] ) ) {
														if ( ! empty( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartepo'] ) ) {
															$saved_epos = $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartepo'];
															foreach ( $saved_epos as $key => $val ) {
																if ( $element['uniqid'] === $val['section'] && (string) $value === (string) $val['key'] ) {
																	if ( isset( $val['quantity'] ) ) {
																		if ( ! empty( $repeater ) ) {
																			$_REQUEST[ $posted_name . '_quantity' ][ $get_posted_key ] = $val['quantity'];
																		} else {
																			$_REQUEST[ $posted_name . '_quantity' ] = $val['quantity'];
																		}
																	}
																}
															}
														}
														if ( ! empty( $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartfee'] ) ) {
															$saved_fees = $_cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartfee'];
															foreach ( $saved_fees as $key => $val ) {
																if ( $element['uniqid'] === $val['section'] && (string) $value === (string) $val['key'] ) {
																	if ( isset( $val['quantity'] ) ) {
																		if ( isset( $val['quantity'] ) ) {
																			if ( ! empty( $repeater ) ) {
																				$_REQUEST[ $posted_name . '_quantity' ][ $get_posted_key ] = $val['quantity'];
																			} else {
																				$_REQUEST[ $posted_name . '_quantity' ] = $val['quantity'];
																			}
																		}
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
													$fieldtype .= ' ' . $element['class'];
												}

												if ( THEMECOMPLETE_EPO()->get_element_price_type( '', $element, $value, 1, 0 ) === 'math' ) {
													$fieldtype .= ' tc-is-math';
												}

												$uniqid_suffix = uniqid();

												$display = $field_obj->display_field(
													$element,
													[
														'id'   => 'tmcp_' . $element_object->post_name_prefix . '_' . $element_counter . '_' . $field_counter . '_' . $tabindex . $form_prefix . $uniqid_suffix,
														'get_posted_key' => $get_posted_key,
														'repeater' => $repeater,
														'name' => $html_name,
														'name_inc' => $name_inc,
														'posted_name' => $posted_name,
														'value' => $value,
														'label' => $label,
														'element_counter' => $element_counter,
														'tabindex' => $tabindex,
														'form_prefix' => $form_prefix,
														'fieldtype' => $fieldtype,
														'border_type' => THEMECOMPLETE_EPO()->tm_epo_css_selected_border,
														'field_counter' => $field_counter,
														'product_id' => isset( $product_id ) ? $product_id : 0,
													]
												);

												if ( is_array( $display ) ) {

													$original_amount = $element['original_rules_filtered'][ $value ][0];

													$amount = $element['rules_filtered'][ $value ][0];

													$original_rules = isset( $element['original_rules_filtered'][ $value ] ) ? wp_json_encode( ( $element['original_rules_filtered'][ $value ] ) ) : '';
													if ( empty( $original_rules ) ) {
														$original_rules = '';
													}

													$element_args = [
														'id'   => 'tmcp_' . $element_object->post_name_prefix . '_' . $element_counter . '_' . $field_counter . '_' . $tabindex . $form_prefix . $uniqid_suffix,
														'name' => $html_name,
														'get_posted_key' => $get_posted_key,
														'posted_name' => $posted_name,
														'quantity_name' => $html_quantity_name,
														'amount' => '',
														'original_amount' => '',
														'required' => $element['required'],
														'tabindex' => $tabindex,
														'fieldtype' => $fieldtype,
														'rules' => isset( $element['rules_filtered'][ $value ] ) ? wp_json_encode( ( $element['rules_filtered'][ $value ] ) ) : '',
														'original_rules' => $original_rules,
														'rules_type' => isset( $element['rules_type'][ $value ] ) ? wp_json_encode( ( $element['rules_type'][ $value ] ) ) : '',
														'border_type' => THEMECOMPLETE_EPO()->tm_epo_css_selected_border,
														'tm_element_settings' => $element,
														'class' => ! empty( $element['class'] ) ? $element['class'] : '',
														'field_counter' => $field_counter,
														'tax_obj' => ! $is_cart_fee ? false : wp_json_encode(
															( [
																'is_fee'    => $is_cart_fee,
																'has_fee'   => isset( $element['include_tax_for_fee_price_type'] ) ? $element['include_tax_for_fee_price_type'] : '',
																'tax_class' => isset( $element['tax_class_for_fee_price_type'] ) ? $element['tax_class_for_fee_price_type'] : '',
																'tax_rate'  => $this->get_tax_rate( isset( $element['tax_class_for_fee_price_type'] ) ? $element['tax_class_for_fee_price_type'] : '' ),
															] )
														),
													];

													$element_args         = apply_filters( 'wc_epo_display_template_args', array_merge( $element_args, $display ), $element, $value, $choice_counter, $element_type_counter[ $element['type'] ] );
													$element_args['args'] = $element_args;
													if ( $element_object->is_addon ) {
														do_action(
															'tm_epo_display_addons',
															$element,
															$element_args,
															[
																'name_inc'        => $name_inc,
																'element_counter' => $element_counter,
																'tabindex'        => $tabindex,
																'form_prefix'     => $form_prefix,
																'field_counter'   => $field_counter,
																'border_type'     => THEMECOMPLETE_EPO()->tm_epo_css_selected_border,
															],
															$element_object->namespace
														);
													} elseif ( is_readable( apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, $element['type'], $element ) . apply_filters( 'wc_epo_template_element', 'tm-' . $element['type'] . '.php', $element['type'], $element ) ) ) {
														wc_get_template(
															apply_filters( 'wc_epo_template_element', 'tm-' . $element['type'] . '.php', $element['type'], $element ),
															$element_args,
															$this->get_template_path(),
															apply_filters( 'wc_epo_template_path_element', $this->get_default_path(), $element['type'], $element )
														);
													}
												}

												$choice_counter ++;

												$field_counter ++;

											}

											if ( 'variations' !== $element['type'] ) {
												if ( $element['enabled'] ) {
													wc_get_template(
														'tm-builder-element-end.php',
														[
															'repeater' => $repeater,
															'repeater_quantity' => $repeater_quantity,
															'repeater_min_rows'    => $repeater_min_rows,
															'repeater_max_rows'    => $repeater_max_rows,
															'repeater_button_label' => $repeater_button_label,
															'get_posted_key' => $get_posted_key,
															'get_posted_key_count' => $args['get_posted_key_count'],
															'tm_element_settings' => $element,
															'element'     => $element['type'],
															'enabled'     => $element['enabled'],
															'description' => ! empty( $element['description'] ) ? $element['description'] : '',
															'description_color' => ! empty( $element['description_color'] ) ? $element['description_color'] : '',
															'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : '',
														],
														$this->get_template_path(),
														$this->get_default_path()
													);
												}
											}

											unset( $field_obj );

										}

										$element_type_counter[ $element['type'] ] ++;

									}

									$element_counter ++;

								} elseif ( 'display' === $element_object->is_post ) {
									$field_obj = new $init_class();
									$display   = $field_obj->display_field(
										$element,
										[
											'element_counter' => $element_counter,
											'tabindex'    => $tabindex,
											'form_prefix' => $form_prefix,
											'field_counter' => $field_counter,
											'args'        => $args,
											'product_id'  => isset( $product_id ) ? $product_id : 0,
										]
									);

									if ( is_array( $display ) ) {
										$element_args = [
											'tm_element_settings' => $element,
											'class'       => ! empty( $element['class'] ) ? $element['class'] : '',
											'form_prefix' => $form_prefix,
											'field_counter' => $field_counter,
											'tm_element'  => $element,
											'epo_template_path' => $this->get_template_path(),
											'epo_default_path' => $this->get_default_path(),
											'tm_product_id' => $product_id,
										];

										if ( 'variations' === $element['type'] ) {
											$element_args['variations_builder_element_start_args'] = $variations_builder_element_start_args;
											$element_args['variations_builder_element_end_args']   = [
												'repeater' => '',
												'repeater_quantity' => '',
												'repeater_min_rows' => '',
												'repeater_max_rows' => '',
												'repeater_button_label' => '',
												'get_posted_key' => '',
												'get_posted_key_count' => '',
												'tm_element_settings' => $element,
												'element'  => $element['type'],
												'description' => ! empty( $element['description'] ) ? $element['description'] : '',
												'description_color' => ! empty( $element['description_color'] ) ? $element['description_color'] : '',
												'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : '',
											];

										}

										$element_args = array_merge( $element_args, $display );
										if ( 'variations' !== $element['type'] ) {
											if ( $element['enabled'] ) {
												wc_get_template(
													'tm-builder-element-start.php',
													$args,
													$this->get_template_path(),
													$this->get_default_path()
												);
											}
										}
										if ( $element_object->is_addon ) {
											do_action(
												'tm_epo_display_addons',
												$element,
												$element_args,
												[
													'name_inc' => '',
													'element_counter' => $element_counter,
													'tabindex' => $tabindex,
													'form_prefix' => $form_prefix,
													'field_counter' => $field_counter,
												],
												$element_object->namespace
											);
										} elseif ( is_readable( apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, $element['type'], $element ) . apply_filters( 'wc_epo_template_element', 'tm-' . $element['type'] . '.php', $element['type'], $element ) ) ) {
											wc_get_template(
												apply_filters( 'wc_epo_template_element', 'tm-' . $element['type'] . '.php', $element['type'], $element ),
												$element_args,
												$this->get_template_path(),
												apply_filters( 'wc_epo_template_path_element', $this->get_default_path(), $element['type'], $element )
											);
										}
										if ( 'variations' !== $element['type'] ) {
											if ( $element['enabled'] ) {
												wc_get_template(
													'tm-builder-element-end.php',
													[
														'repeater' => $repeater,
														'repeater_quantity' => $repeater_quantity,
														'repeater_min_rows'    => $repeater_min_rows,
														'repeater_max_rows'    => $repeater_max_rows,
														'repeater_button_label' => $repeater_button_label,
														'get_posted_key' => isset( $get_posted_key ) ? $get_posted_key : 0,
														'get_posted_key_count' => isset( $args['get_posted_key_count'] ) ? $args['get_posted_key_count'] : 0,
														'tm_element_settings' => $element,
														'element'     => $element['type'],
														'enabled'     => $element['enabled'],
														'description' => ! empty( $element['description'] ) ? $element['description'] : '',
														'description_color' => ! empty( $element['description_color'] ) ? $element['description_color'] : '',
														'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : '',
													],
													$this->get_template_path(),
													$this->get_default_path()
												);
											}
										}
									}

									unset( $field_obj );
								}
							}
						}

						if ( $use_slides && isset( $sections_slides[ $slide_counter ] ) ) {
							$sections_slides[ $slide_counter ] = $sections_slides[ $slide_counter ] - 1;

							if ( $sections_slides[ $slide_counter ] <= 0 ) {
								echo '</div>';
								$slide_counter ++;
								$doing_slides = false;
							}
						}
					}

					if ( '' !== $section['sections_slides'] && ( 'slider' === $section['sections_type'] || 'tabs' === $section['sections_type'] ) ) {
						if ( 'tabs' === $section['sections_type'] ) {
							if ( is_array( $sections_tabs_labels ) ) {
								echo '</div>';
								echo '</div></div>';
							}
						} elseif ( 'slider' === $section['sections_type'] ) {
							echo '</div>';
						}
					}
				}

				wc_get_template(
					'tm-builder-section-end.php',
					$section_args,
					$this->get_template_path(),
					$this->get_default_path()
				);

			}

			wc_get_template(
				'tm-builder-end.php',
				[],
				$this->get_template_path(),
				$this->get_default_path()
			);

			$unit_counter ++;

		}

		return [
			'tabindex'        => $tabindex,
			'unit_counter'    => $unit_counter,
			'field_counter'   => $field_counter,
			'element_counter' => $element_counter,
			'_currency'       => $_currency,
		];

	}

	/**
	 * Displays the option created from the normal (local) mode
	 *
	 * @param array   $local_price_array The normal options array.
	 * @param array   $args The variable arguemnts..
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $dummy_prefix If we should use the form prefix.
	 * @since 4.8
	 */
	public function get_normal_display( $local_price_array = [], $args = [], $form_prefix = null, $dummy_prefix = null ) {

		$tabindex        = $args['tabindex'];
		$unit_counter    = $args['unit_counter'];
		$field_counter   = $args['field_counter'];
		$element_counter = $args['element_counter'];
		$_currency       = $args['_currency'];
		$product_id      = $args['product_id'];

		$form_prefix_onform = '' !== $form_prefix ? '_' . str_replace( '_', '', $form_prefix ) : '';

		// Normal (local) options.
		if ( is_array( $local_price_array ) && count( $local_price_array ) > 0 ) {

			$attributes      = themecomplete_get_attributes( floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $product_id ) ) );
			$wpml_attributes = themecomplete_get_attributes( $product_id );

			$fieldtype = 'tmcp-field';

			if ( is_array( $attributes ) && count( $attributes ) > 0 ) {
				foreach ( $local_price_array as $field ) {
					if ( isset( $field['name'] ) && isset( $attributes[ $field['name'] ] ) && ! $attributes[ $field['name'] ]['is_variation'] ) {

						$attribute      = $attributes[ $field['name'] ];
						$wpml_attribute = isset( $wpml_attributes[ $field['name'] ] ) ? $wpml_attributes[ $field['name'] ] : [];

						$empty_rules = '';
						if ( isset( $field['rules_filtered'][0] ) ) {
							$empty_rules = wp_json_encode( ( $field['rules_filtered'][0] ) );
						}
						if ( empty( $empty_rules ) ) {
							$empty_rules = '';
						}
						$empty_rules_type = '';
						if ( isset( $field['rules_type'][0] ) ) {
							$empty_rules_type = wp_json_encode( ( $field['rules_type'][0] ) );
						}

						$args = [
							'label'          => ( ! $attribute['is_taxonomy'] && isset( $attributes[ $field['name'] ]['name'] ) )
								? wc_attribute_label( $attributes[ $field['name'] ]['name'] )
								: wc_attribute_label( $field['name'] ),
							'required'       => wc_attribute_label( $field['required'] ),
							'field_id'       => 'tc-epo-field-' . $unit_counter,
							'type'           => $field['type'],
							'rules'          => $empty_rules,
							'original_rules' => $empty_rules,
							'rules_type'     => $empty_rules_type,
							'li_class'       => 'tc-normal-mode',
						];
						wc_get_template(
							'tm-field-start.php',
							$args,
							$this->get_template_path(),
							$this->get_default_path()
						);

						$name_inc      = '';
						$field_counter = 0;

						if ( $attribute['is_taxonomy'] ) {

							$orderby    = wc_attribute_orderby( $attribute['name'] );
							$order_args = 'orderby=name&hide_empty=0';
							switch ( $orderby ) {
								case 'name':
									$order_args = [
										'orderby'    => 'name',
										'hide_empty' => false,
										'menu_order' => false,
									];
									break;
								case 'id':
									$order_args = [
										'orderby'    => 'id',
										'order'      => 'ASC',
										'menu_order' => false,
										'hide_empty' => false,
									];
									break;
								case 'menu_order':
									$order_args = [
										'menu_order' => 'ASC',
										'hide_empty' => false,
									];
									break;
							}

							// Terms in current lang.
							$_current_terms  = THEMECOMPLETE_EPO_WPML()->get_terms( THEMECOMPLETE_EPO_WPML()->get_lang(), $attribute['name'], $order_args );
							$_current_terms2 = get_terms( $attribute['name'], $order_args );
							$_current_terms  = THEMECOMPLETE_EPO_WPML()->order_terms( $_current_terms, $_current_terms2 );

							$current_language = apply_filters( 'wpml_current_language', false );
							$default_language = apply_filters( 'wpml_default_language', false );
							do_action( 'wpml_switch_language', $default_language );

							// Terms in default WPML lang.
							$_default_terms  = THEMECOMPLETE_EPO_WPML()->get_terms( THEMECOMPLETE_EPO_WPML()->get_lang(), $attribute['name'], $order_args );
							$_default_terms2 = get_terms( $attribute['name'], $order_args );
							$_default_terms  = THEMECOMPLETE_EPO_WPML()->order_terms( $_default_terms, $_default_terms2 );

							do_action( 'wpml_switch_language', $current_language );

							$_tems_to_use = THEMECOMPLETE_EPO_WPML()->merge_terms( $_current_terms, $_default_terms );

							$slugs = THEMECOMPLETE_EPO_WPML()->merge_terms_slugs( $_current_terms, $_default_terms );

							switch ( $field['type'] ) {

								case 'select':
									$name_inc = 'select_' . $element_counter;
									$tabindex ++;

									$args = [
										'id'              => 'tmcp_select_' . $tabindex . $form_prefix,
										'name'            => 'tmcp_' . $name_inc . ( $dummy_prefix ? '' : $form_prefix_onform ),
										'amount'          => '',
										'original_amount' => '',
										'tabindex'        => $tabindex,
										'fieldtype'       => $fieldtype,
										'rules'           => '',
										'original_rules'  => '',
										'rules_type'      => '',
										'textafterprice'  => '',
										'textbeforeprice' => '',
										'class'           => '',
										'class_label'     => '',
										'element_data_attr_html' => '',
										'hide_amount'     => ! empty( $field['hide_price'] ) ? ' hidden' : '',
										'tax_obj'         => false,

										'options'         => [],
										'placeholder'     => '',
									];
									if ( $_tems_to_use && is_array( $_tems_to_use ) ) {
										foreach ( $_tems_to_use as $trid => $term ) {
											if ( ! isset( $slugs[ $term->slug ] ) ) {
												$slugs[ $term->slug ] = $term->slug;
											}
											$has_term = has_term( (int) $term->term_id, $attribute['name'], floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $product_id ) ) ) ? 1 : 0;

											if ( $has_term ) {
												$wpml_term_id = THEMECOMPLETE_EPO_WPML()->is_active() ? icl_object_id( $term->term_id, $attribute['name'], false ) : false;
												if ( $wpml_term_id ) {
													$wpml_term = get_term( $wpml_term_id, $attribute['name'] );
												} else {
													$wpml_term = $term;
												}

												$option = [
													'value_to_show' => sanitize_title( $term->slug ),
													'data_price' => '',
													'data_rules' => ( isset( $field['rules_filtered'][ $slugs[ $term->slug ] ] )
														? wp_json_encode( ( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) )
														: ( isset( $field['rules_filtered'][ $term->slug ] )
															? wp_json_encode( $field['rules_filtered'][ $term->slug ] )
															: '' ) ),
													'data_original_rules' => ( isset( $field['rules_filtered'][ $slugs[ $term->slug ] ] )
														? wp_json_encode( ( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) )
														: ( isset( $field['rules_filtered'][ $term->slug ] )
															? wp_json_encode( $field['rules_filtered'][ $term->slug ] )
															: '' ) ),
													'data_rulestype' => ( isset( $field['rules_type'][ $slugs[ $term->slug ] ] )
														? wp_json_encode( ( $field['rules_type'][ $slugs[ $term->slug ] ] ) )
														: ( isset( $field['rules_type'][ $term->slug ] )
															? wp_json_encode( $field['rules_type'][ $term->slug ] )
															: '' ) ),
													'text' => $wpml_term->name,
												];

												if ( isset( $_POST[ 'tmcp_' . $name_inc . $form_prefix_onform ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
													$option['selected'] = wp_unslash( $_POST[ 'tmcp_' . $name_inc . $form_prefix_onform ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
													$option['current']  = esc_attr( $option['value_to_show'] );
												}

												$args['options'][] = $option;

											}
										}
									}

									wc_get_template(
										'tm-' . $field['type'] . '.php',
										$args,
										$this->get_template_path(),
										$this->get_default_path()
									);
									$element_counter ++;
									break;

								case 'radio':
								case 'checkbox':
									if ( $_tems_to_use && is_array( $_tems_to_use ) ) {
										$labelclass       = '';
										$labelclass_start = '';
										$labelclass_end   = '';
										if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_css_styles ) {
											$labelclass       = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
											$labelclass_start = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
											$labelclass_end   = true;
										}

										foreach ( $_tems_to_use as $trid => $term ) {
											if ( ! isset( $slugs[ $term->slug ] ) ) {
												$slugs[ $term->slug ] = $term->slug;
											}

											$has_term = has_term( (int) $term->term_id, $attribute['name'], floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $product_id ) ) ) ? 1 : 0;

											if ( $has_term ) {

												$wpml_term_id = THEMECOMPLETE_EPO_WPML()->is_active() ? icl_object_id( $term->term_id, $attribute['name'], false ) : false;

												if ( $wpml_term_id ) {
													$wpml_term = get_term( $wpml_term_id, $attribute['name'] );
												} else {
													;
													$wpml_term = $term;
												}

												$tabindex ++;

												if ( 'radio' === $field['type'] ) {
													$name_inc = 'radio_' . $element_counter;
												}
												if ( 'checkbox' === $field['type'] ) {
													$name_inc = 'checkbox_' . $element_counter . '_' . $field_counter;
												}

												$original_rules = ( isset( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) ? wp_json_encode( ( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) ) : ( isset( $field['rules_filtered'][ $term->slug ] ) ? wp_json_encode( $field['rules_filtered'][ $term->slug ] ) : '' ) );
												if ( empty( $original_rules ) ) {
													$original_rules = '';
												}

												$checked = false;
												$value   = sanitize_title( $term->slug );
												switch ( $field['type'] ) {

													case 'radio':
														$selected_value = '';
														$name           = 'tmcp_' . $name_inc . ( $dummy_prefix ? '' : $form_prefix_onform );

														if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add && isset( $_POST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
															$selected_value = wp_unslash( $_POST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
														} elseif ( empty( $_POST ) && isset( $_REQUEST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification
															$selected_value = wp_unslash( $_REQUEST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
														} elseif ( empty( $_POST ) || ! isset( $_POST[ $name ] ) || 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add ) { // phpcs:ignore WordPress.Security.NonceVerification
															$selected_value = -1;
														}

														$checked = -1 !== $selected_value && esc_attr( stripcslashes( $selected_value ) ) === esc_attr( $value );
														break;

													case 'checkbox':
														$selected_value = '';
														$name           = 'tmcp_' . $name_inc . ( $dummy_prefix ? '' : $form_prefix_onform );
														if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add && isset( $_POST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
															$selected_value = wp_unslash( $_POST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
														} elseif ( empty( $_POST ) && isset( $_REQUEST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification
															$selected_value = wp_unslash( $_REQUEST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
														} elseif ( ( ( THEMECOMPLETE_EPO()->is_quick_view() || empty( $_POST ) ) && empty( THEMECOMPLETE_EPO()->cart_edit_key ) ) || 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add ) { // phpcs:ignore WordPress.Security.NonceVerification
															$selected_value = -1;
														}

														$checked = -1 !== $selected_value && esc_attr( stripcslashes( $selected_value ) ) === esc_attr( $value );
														break;
												}
												$args = [
													'id'   => 'tmcp_choice_' . $element_counter . '_' . $field_counter . '_' . $tabindex . $form_prefix,
													'name' => $name,
													'amount' => '',
													'original_amount' => '',
													'tabindex' => $tabindex,
													'fieldtype' => $fieldtype,
													'rules' => ( isset( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) ? wp_json_encode( ( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) ) : ( isset( $field['rules_filtered'][ $term->slug ] ) ? wp_json_encode( $field['rules_filtered'][ $term->slug ] ) : '' ) ),
													'original_rules' => $original_rules,
													'rules_type' => ( isset( $field['rules_type'][ $slugs[ $term->slug ] ] ) ? wp_json_encode( ( $field['rules_type'][ $slugs[ $term->slug ] ] ) ) : ( isset( $field['rules_type'][ $term->slug ] ) ? wp_json_encode( $field['rules_type'][ $term->slug ] ) : '' ) ),
													'label_mode' => '',
													'label_to_display' => $wpml_term->name,
													'swatch_class' => '',
													'swatch' => [],
													'altsrc' => [],
													'textafterprice' => '',
													'textbeforeprice' => '',
													'class' => '',
													'element_data_attr_html' => '',
													'li_class' => '',
													'exactlimit' => '',
													'minimumlimit' => '',
													'url'  => '',
													'image' => '',
													'imagec' => '',
													'imagep' => '',
													'imagel' => '',
													'image_variations' => '',
													'checked' => $checked,
													'use'  => '',
													'labelclass_start' => $labelclass_start,
													'labelclass' => $labelclass,
													'labelclass_end' => $labelclass_end,
													'hide_amount' => ! empty( $field['hide_price'] ) ? ' hidden' : '',
													'tax_obj' => false,
													'border_type' => '',
													'label' => $wpml_term->name,
													'value' => $value,
													'replacement_mode' => 'none',
													'swatch_position' => 'center',
													'percent' => '',
													'limit' => empty( $field['limit'] ) ? '' : $field['limit'],

												];
												wc_get_template(
													'tm-' . $field['type'] . '.php',
													$args,
													$this->get_template_path(),
													$this->get_default_path()
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

								case 'select':
									$name_inc = 'select_' . $element_counter;
									$tabindex ++;

									$args = [
										'id'              => 'tmcp_select_' . $tabindex . $form_prefix,
										'name'            => 'tmcp_' . $name_inc . ( $dummy_prefix ? '' : $form_prefix_onform ),
										'amount'          => '',
										'original_amount' => '',
										'tabindex'        => $tabindex,
										'fieldtype'       => $fieldtype,
										'rules'           => '',
										'original_rules'  => '',
										'rules_type'      => '',
										'textafterprice'  => '',
										'textbeforeprice' => '',
										'class'           => '',
										'class_label'     => '',
										'element_data_attr_html' => '',
										'hide_amount'     => ! empty( $field['hide_price'] ) ? ' hidden' : '',
										'tax_obj'         => false,
										'border_type'     => '',
										'options'         => [],
										'placeholder'     => '',

									];
									foreach ( $options as $k => $option ) {

										$option = [
											'value_to_show' => sanitize_title( $option ),
											'data_price' => '',
											'data_rules' => ( isset( $field['rules_filtered'][ esc_attr( sanitize_title( $option ) ) ] ) ? wp_json_encode( ( $field['rules_filtered'][ esc_attr( sanitize_title( $option ) ) ] ) ) : '' ),
											'data_original_rules' => ( isset( $field['rules_filtered'][ esc_attr( sanitize_title( $option ) ) ] ) ? wp_json_encode( ( $field['rules_filtered'][ esc_attr( sanitize_title( $option ) ) ] ) ) : '' ),
											'data_rulestype' => ( isset( $field['rules_type'][ esc_attr( sanitize_title( $option ) ) ] ) ? wp_json_encode( ( $field['rules_type'][ esc_attr( sanitize_title( $option ) ) ] ) ) : '' ),
											'text'       => apply_filters( 'woocommerce_tm_epo_option_name', isset( $wpml_options[ $k ] ) ? $wpml_options[ $k ] : $option, null, null ),
										];

										if ( isset( $_POST[ 'tmcp_' . $name_inc . $form_prefix_onform ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
											$option['selected'] = wp_unslash( $_POST[ 'tmcp_' . $name_inc . $form_prefix_onform ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
											$option['current']  = esc_attr( $option['value_to_show'] );
										}

										$args['options'][] = $option;

									}
									wc_get_template(
										'tm-' . $field['type'] . '.php',
										$args,
										$this->get_template_path(),
										$this->get_default_path()
									);
									$element_counter ++;
									break;

								case 'radio':
								case 'checkbox':
									$labelclass       = '';
									$labelclass_start = '';
									$labelclass_end   = '';
									if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_css_styles ) {
										$labelclass       = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
										$labelclass_start = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
										$labelclass_end   = true;
									}

									foreach ( $options as $k => $option ) {
										$tabindex ++;

										if ( 'radio' === $field['type'] ) {
											$name_inc = 'radio_' . $element_counter;
										}
										if ( 'checkbox' === $field['type'] ) {
											$name_inc = 'checkbox_' . $element_counter . '_' . $field_counter;
										}

										$original_rules = isset( $field['rules_filtered'][ sanitize_title( $option ) ] ) ? wp_json_encode( ( $field['rules_filtered'][ sanitize_title( $option ) ] ) ) : '';
										if ( empty( $original_rules ) ) {
											$original_rules = '';
										}

										$checked = false;
										$value   = sanitize_title( $option );
										switch ( $field['type'] ) {

											case 'radio':
												$selected_value = '';
												$name           = 'tmcp_' . $name_inc . ( $dummy_prefix ? '' : $form_prefix_onform );

												if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add && isset( $_POST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
													$selected_value = wp_unslash( $_POST[ $name ] );// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
												} elseif ( empty( $_POST ) && isset( $_REQUEST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
													$selected_value = wp_unslash( $_REQUEST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
												} elseif ( empty( $_POST ) || ! isset( $_POST[ $name ] ) || 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add ) { // phpcs:ignore WordPress.Security.NonceVerification
													$selected_value = -1;
												}

												$checked = -1 !== $selected_value && esc_attr( stripcslashes( $selected_value ) ) === esc_attr( $value );
												break;

											case 'checkbox':
												$selected_value = '';
												$name           = 'tmcp_' . $name_inc . ( $dummy_prefix ? '' : $form_prefix_onform );
												if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add && isset( $_POST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
													$selected_value = wp_unslash( $_POST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
												} elseif ( empty( $_POST ) && isset( $_REQUEST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
													$selected_value = wp_unslash( $_REQUEST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
												} elseif ( ( ( THEMECOMPLETE_EPO()->is_quick_view() || empty( $_POST ) ) && empty( THEMECOMPLETE_EPO()->cart_edit_key ) ) || 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add ) { // phpcs:ignore WordPress.Security.NonceVerification
													$selected_value = -1;
												}

												$checked = -1 !== $selected_value && esc_attr( stripcslashes( $selected_value ) ) === esc_attr( $value );
												break;
										}

										$label = apply_filters( 'woocommerce_tm_epo_option_name', isset( $wpml_options[ $k ] ) ? $wpml_options[ $k ] : $option, null, null );

										$args = [
											'id'           => 'tmcp_choice_' . $element_counter . '_' . $field_counter . '_' . $tabindex . $form_prefix,
											'name'         => $name,
											'amount'       => '',
											'original_amount' => '',
											'tabindex'     => $tabindex,
											'fieldtype'    => $fieldtype,
											'rules'        => isset( $field['rules_filtered'][ sanitize_title( $option ) ] ) ? wp_json_encode( ( $field['rules_filtered'][ sanitize_title( $option ) ] ) ) : '',
											'original_rules' => $original_rules,
											'rules_type'   => isset( $field['rules_type'][ sanitize_title( $option ) ] ) ? wp_json_encode( ( $field['rules_type'][ sanitize_title( $option ) ] ) ) : '',
											'label_mode'   => '',
											'label_to_display' => $label,
											'swatch_class' => '',
											'swatch'       => [],
											'altsrc'       => [],
											'textafterprice' => '',
											'textbeforeprice' => '',
											'class'        => '',
											'element_data_attr_html' => '',
											'li_class'     => '',
											'exactlimit'   => '',
											'minimumlimit' => '',
											'url'          => '',
											'image'        => '',
											'imagec'       => '',
											'imagep'       => '',
											'imagel'       => '',
											'image_variations' => '',
											'checked'      => $checked,
											'use'          => '',
											'labelclass_start' => $labelclass_start,
											'labelclass'   => $labelclass,
											'labelclass_end' => $labelclass_end,
											'hide_amount'  => ! empty( $field['hide_price'] ) ? ' hidden' : '',
											'tax_obj'      => false,
											'border_type'  => '',
											'label'        => $label,
											'value'        => $value,
											'replacement_mode' => 'none',
											'swatch_position' => 'center',
											'percent'      => '',
											'limit'        => empty( $field['limit'] ) ? '' : $field['limit'],
										];
										wc_get_template(
											'tm-' . $field['type'] . '.php',
											$args,
											$this->get_template_path(),
											$this->get_default_path()
										);
										$field_counter ++;
									}
									$element_counter ++;
									break;

							}
						}

						wc_get_template(
							'tm-field-end.php',
							[],
							$this->get_template_path(),
							$this->get_default_path()
						);

						$unit_counter ++;
					}
				}
			}
		}

		return [
			'tabindex'        => $tabindex,
			'unit_counter'    => $unit_counter,
			'field_counter'   => $field_counter,
			'element_counter' => $element_counter,
			'_currency'       => $_currency,
		];

	}

	/**
	 * Display totals box
	 *
	 * @param integer $product_id The product id.
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $is_from_shortcode If we are in a shortcode.
	 */
	public function tm_epo_totals( $product_id = 0, $form_prefix = '', $is_from_shortcode = false ) {

		if ( $this->block_epo ) {
			return;
		}

		global $product, $woocommerce;

		if ( ! property_exists( $woocommerce, 'product_factory' )
			|| null === $woocommerce->product_factory
			|| ( $this->tm_options_have_been_displayed && ( ! ( THEMECOMPLETE_EPO()->is_bto || ( ( THEMECOMPLETE_EPO()->is_enabled_shortcodes() && ! $is_from_shortcode ) && ! is_product() ) || ( ( is_shop() || is_product_category() || is_product_tag() ) && 'yes' === THEMECOMPLETE_EPO()->tm_epo_enable_in_shop ) ) ) )
		) {
			return;// bad function call.
		}

		$this->print_price_fields( $product_id, $form_prefix, $is_from_shortcode );
		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) && ! $is_from_shortcode ) {
			$this->tm_options_totals_have_been_displayed = true;
		}

	}

	/**
	 * Batch displayh totals box
	 *
	 * @param string $form_prefix The form prefix.
	 */
	private function tm_epo_totals_batch( $form_prefix = '' ) {

		foreach ( $this->current_product_id_to_be_displayed_check as $key => $product_id ) {
			if ( ! empty( $product_id ) ) {
				$this->print_price_fields( $product_id, $form_prefix );
				if ( THEMECOMPLETE_EPO()->tm_epo_options_placement !== THEMECOMPLETE_EPO()->tm_epo_totals_box_placement ) {
					if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
						unset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] );
					}
				}
			}
		}
		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
			if ( THEMECOMPLETE_EPO()->tm_epo_options_placement !== THEMECOMPLETE_EPO()->tm_epo_totals_box_placement ) {
				$this->epo_internal_counter       = 0;
				$this->epo_internal_counter_check = [];
			}
		}

	}

	/**
	 * Display totals box
	 *
	 * @param integer $product_id The product id.
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $is_from_shortcode If we are in a shortcode.
	 */
	private function print_price_fields( $product_id = 0, $form_prefix = '', $is_from_shortcode = false ) {

		if ( $product_id instanceof WC_PRODUCT ) {
			$product    = $product_id;
			$product_id = themecomplete_get_id( $product );
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

		if ( 'grouped' === $type ) {
			return;
		}

		$cpf_price_array = THEMECOMPLETE_EPO()->get_product_tm_epos( $product_id, $form_prefix, false, true );
		if ( ! $cpf_price_array ) {
			return;
		}

		if ( THEMECOMPLETE_EPO()->is_associated === false && $cpf_price_array && 'no' === THEMECOMPLETE_EPO()->tm_epo_enable_final_total_box_all ) {
			$global_price_array = $cpf_price_array['global'];
			$local_price_array  = $cpf_price_array['local'];
			if ( empty( $global_price_array ) && empty( $local_price_array ) ) {
				if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
					if ( empty( $this->epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] ) ) {
						// First time displaying totals and fields haven't been displayed.
						$this->epo_internal_counter ++;
						$this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] = $this->epo_internal_counter;
					} else {
						// Fields have already been displayed.
						unset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] );
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

		if ( THEMECOMPLETE_EPO()->is_associated === false && ! $cpf_price_array && 'no' === THEMECOMPLETE_EPO()->tm_epo_enable_final_total_box_all ) {
			return;
		}

		THEMECOMPLETE_EPO()->set_tm_meta( $product_id );

		$force_quantity = 0;
		if ( THEMECOMPLETE_EPO()->cart_edit_key ) {
			$cart_item_key = THEMECOMPLETE_EPO()->cart_edit_key;
			$cart_item     = WC()->cart->get_cart_item( $cart_item_key );

			if ( isset( $cart_item['quantity'] ) ) {
				$force_quantity = $cart_item['quantity'];
			}
		}

		if ( ! $form_prefix && THEMECOMPLETE_EPO()->is_quick_view() ) {
			if ( ! $this->unique_form_prefix ) {
				$this->unique_form_prefix = uniqid( '' );
			}
			$form_prefix = '_tcform' . $this->unique_form_prefix;
		}

		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
			if ( empty( $this->epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] ) ) {
				// First time displaying totals and fields haven't been displayed.
				$this->epo_internal_counter ++;
				$this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] = $this->epo_internal_counter;
			} else {
				// Fields have already been displayed.
				unset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] );
				$this->current_product_id_to_be_displayed = 0;
				$this->unique_form_prefix                 = '';
			}
			$_epo_internal_counter = $this->epo_internal_counter;
		} else {
			if ( THEMECOMPLETE_EPO()->is_inline_epo && $this->epo_internal_counter ) {
				$_epo_internal_counter = $this->epo_internal_counter;
			} else {
				$_epo_internal_counter = 0;
			}
		}

		if ( ! $form_prefix && THEMECOMPLETE_EPO()->wc_vars['is_page'] ) {
			$form_prefix = 'tcform' . $_epo_internal_counter;
		}

		if ( $form_prefix ) {
			$form_prefix = '_' . $form_prefix;
		}

		$minmax = [];

		$minmax['min_price']         = $product->get_price();
		$minmax['min_regular_price'] = $product->get_regular_price();

		if ( function_exists( 'WC_CP' ) && version_compare( WC_CP()->version, '3.8', '<' ) && 'composite' === themecomplete_get_product_type( $product ) && is_callable( [ $product, 'get_base_price' ] ) ) {
			$_price = apply_filters( 'woocommerce_tm_epo_price_compatibility', $product->get_base_price(), $product );
		} else {
			$_price = apply_filters( 'woocommerce_tm_epo_price_compatibility', $minmax['min_price'], $product );
		}

		$price            = [];
		$price['product'] = []; // product price rules.
		$price['price']   = apply_filters( 'wc_epo_product_price', $_price ); // product price.

		$price = apply_filters( 'wc_epo_product_price_rules', $price, $product );

		$regular_price            = [];
		$regular_price['product'] = []; // product price rules.
		$regular_price['price']   = apply_filters( 'wc_epo_product_price', $minmax['min_regular_price'] ); // product price.

		$regular_price = apply_filters( 'wc_epo_product_regular_price_rules', $regular_price, $product );

		// Woothemes Dynamic Pricing (not yet fully compatible).
		if ( class_exists( 'WC_Dynamic_Pricing' ) ) {
			$id = isset( $product->variation_id ) ? $product->variation_id : themecomplete_get_id( $product );
			$dp = WC_Dynamic_Pricing::instance();
			if ( $dp &&
				is_object( $dp ) && property_exists( $dp, 'discounted_products' )
				&& isset( $dp->discounted_products[ $id ] )
			) {
				$_price = $dp->discounted_products[ $id ];
			} else {
				$_price = $product->get_price();
			}
			$price['price'] = apply_filters( 'wc_epo_product_price', $_price ); // product price.
		}

		$variations = [];

		if ( in_array( themecomplete_get_product_type( $product ), apply_filters( 'wc_epo_variable_product_type', [ 'variable' ], $product ), true ) && 'yes' !== THEMECOMPLETE_EPO()->tm_epo_no_variation_prices_array ) {

			foreach ( $product->get_available_variations() as $variation ) {

				$child_id          = $variation['variation_id'];
				$product_variation = wc_get_product( $child_id );

				if ( $this->discount ) {
					$current_price = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $product_variation->get_price(), $this->discount, $this->discount_type );
					$product_variation->set_sale_price( $current_price );
					$product_variation->set_price( $current_price );
				}

				// Make sure we always have untaxed price here.
				if ( ! wc_prices_include_tax() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
					$variation_price = themecomplete_get_price_excluding_tax(
						$product_variation,
						[
							'qty'   => 1,
							'price' => $product_variation->get_price(),
						]
					);
				} else {
					$variation_price = $product_variation->get_price();
				}

				if ( isset( $variation['attributes'] ) && is_array( $variation['attributes'] ) ) {
					$atts = 0;
					foreach ( $variation['attributes'] as $att => $value_att ) {
						if ( isset( $_REQUEST[ $att ] ) && (string) $_REQUEST[ $att ] === (string) $value_att ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$atts++;
						}
					}
					if ( count( $variation['attributes'] ) === $atts ) {
						$price['price'] = apply_filters( 'wc_epo_product_price', $variation_price ); // product price.
					}
				}

				$variation = wc_get_product( $child_id );

				do_action( 'wc_epo_print_price_fields_in_variation_loop', $product_variation, $child_id );

				$variations[ $child_id ] = apply_filters( 'woocommerce_tm_epo_price_compatibility', apply_filters( 'wc_epo_product_price', $variation_price, '', false ), $product_variation, $child_id );

			}
		}

		global $woocommerce;
		$cart = $woocommerce->cart;

		$tax_rate = $this->get_tax_rate( themecomplete_get_tax_class( $product ) );

		$taxable          = $product->is_taxable();
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$tax_string       = '';
		if ( $taxable && 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_tax_string_suffix ) {
			if ( 'excl' === $tax_display_mode ) {

				$tax_string = ' <small>' . apply_filters( 'wc_epo_ex_tax_or_vat_string', WC()->countries->ex_tax_or_vat() ) . '</small>';

			} else {

				$tax_string = ' <small>' . apply_filters( 'wc_epo_inc_tax_or_vat_string', WC()->countries->inc_tax_or_vat() ) . '</small>';

			}
		}
		if ( $taxable && 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_wc_price_suffix ) {
			$tax_string .= ' <small>' . get_option( 'woocommerce_price_display_suffix' ) . '</small>';
		}

		$taxes_of_one        = 0;
		$base_taxes_of_one   = 0;
		$modded_taxes_of_one = 0;

		$is_vat_exempt            = -1;
		$non_base_location_prices = -1;
		$base_tax_rate            = $tax_rate;
		if ( class_exists( 'WC_Tax' ) && version_compare( get_option( 'woocommerce_version' ), '2.4', '>=' ) ) {
			$tax_rates      = WC_Tax::get_rates( themecomplete_get_tax_class( $product ) );
			$base_tax_rates = WC_Tax::get_base_tax_rates( themecomplete_get_tax_class( $product, 'unfiltered' ) );
			$base_tax_rate  = 0;
			foreach ( $base_tax_rates as $key => $value ) {
				$base_tax_rate = $base_tax_rate + floatval( $value['rate'] );
			}
			$tax_rate = 0;
			foreach ( $tax_rates as $key => $value ) {
				$tax_rate = $tax_rate + floatval( $value['rate'] );
			}
			$is_vat_exempt            = true === ( ! empty( WC()->customer ) && WC()->customer->is_vat_exempt() ) ? 1 : 0;
			$non_base_location_prices = ( $tax_rates !== $base_tax_rates && true === apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) ? 1 : 0;

			$precision    = wc_get_rounding_precision();
			$price_of_one = 1 * ( pow( 10, $precision ) );

			$taxes_of_one = array_sum( WC_Tax::calc_tax( $price_of_one, $tax_rates, wc_prices_include_tax() ) );
			if ( $non_base_location_prices ) {
				$prices_include_tax = true;
			} else {
				$prices_include_tax = wc_prices_include_tax();
			}
			$base_taxes_of_one   = array_sum( WC_Tax::calc_tax( $price_of_one, $base_tax_rates, $prices_include_tax ) );
			$modded_taxes_of_one = array_sum( WC_Tax::calc_tax( $price_of_one - $base_taxes_of_one, $tax_rates, false ) );

			$taxes_of_one        = $taxes_of_one / ( pow( 10, $precision ) );
			$base_taxes_of_one   = $base_taxes_of_one / ( pow( 10, $precision ) );
			$modded_taxes_of_one = $modded_taxes_of_one / ( pow( 10, $precision ) );

		}

		$forcart        = 'main';
		$classcart      = 'tm-cart-main';
		$classtotalform = 'tm-totals-form-main';
		$form_prefix_id = str_replace( '_', '', $form_prefix );
		if ( ! empty( $form_prefix ) ) {
			$forcart        = $form_prefix_id;
			$classcart      = 'tm-cart-' . $form_prefix_id;
			$classtotalform = 'tm-totals-form-' . $form_prefix_id;
		}

		if ( THEMECOMPLETE_EPO()->is_associated ) {
			$classtotalform .= ' tm-totals-form-inline';
			$classcart      .= ' tm-cart-inline';
		}

		do_action(
			'wc_epo_before_totals_box',
			[
				'product_id'        => $product_id,
				'form_prefix'       => $form_prefix,
				'is_from_shortcode' => $is_from_shortcode,
			]
		);
		if ( $is_from_shortcode ) {
			add_action( 'wc_epo_totals_form', [ $this, 'woocommerce_before_add_to_cart_button' ], 10, 1 );
		}

		$tm_epo_final_total_box = ( empty( THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] ) ) ? THEMECOMPLETE_EPO()->tm_epo_final_total_box : THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'];
		if ( THEMECOMPLETE_EPO()->is_associated === true && ! THEMECOMPLETE_EPO_API()->has_options( $product_id ) ) {
			$tm_epo_final_total_box = 'disable';
		}

		$tc_form_prefix_name = 'tc_form_prefix';
		if ( THEMECOMPLETE_EPO()->is_associated ) {
			$tc_form_prefix_name = 'tc_form_prefix_assoc[' . THEMECOMPLETE_EPO()->associated_element_uniqid . ']';
			if ( false !== THEMECOMPLETE_EPO()->associated_product_counter ) {
				$tc_form_prefix_name = $tc_form_prefix_name . '[' . THEMECOMPLETE_EPO()->associated_product_counter . ']';
			}
		}

		wc_get_template(
			'tm-totals.php',
			apply_filters(
				'wc_epo_template_args_tm_totals',
				[

					'classcart'                => $classcart,
					'forcart'                  => $forcart,
					'classtotalform'           => $classtotalform,
					'is_on_sale'               => $product->is_on_sale(),

					'variations'               => wp_json_encode( (array) $variations ),

					'is_sold_individually'     => $product->is_sold_individually(),
					'hidden'                   => ( THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] ) ? ( ( 'hide' === THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] || 'disable' === THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] || 'disable_change' === THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] ) ? ' hidden' : '' ) : ( ( 'hide' === THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] || 'disable' === THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] || 'disable_change' === THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] ) ? ' hidden' : '' ),
					'price_override'           => ( 'no' === THEMECOMPLETE_EPO()->tm_epo_global_override_product_price )
						? 0
						: ( ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_override_product_price )
							? 1
							: ( ! empty( THEMECOMPLETE_EPO()->tm_meta_cpf['price_override'] ) ? 1 : 0 ) ),
					'form_prefix'              => $form_prefix_id,
					'tc_form_prefix_name'      => $tc_form_prefix_name,
					'tc_form_prefix_class'     => ( THEMECOMPLETE_EPO()->is_associated ) ? 'tc_form_prefix_assoc' : 'tc_form_prefix',
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

				],
				$product
			),
			$this->get_template_path(),
			$this->get_default_path()
		);

		do_action(
			'wc_epo_after_totals_box',
			[
				'product_id'        => $product_id,
				'form_prefix'       => $form_prefix,
				'is_from_shortcode' => $is_from_shortcode,
			]
		);

	}

}
