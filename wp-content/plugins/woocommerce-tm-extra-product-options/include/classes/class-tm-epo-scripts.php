<?php
/**
 * Extra Product Options Frontend Scripts
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_Scripts {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	// Contains files to be defered 
	public $defered_files = array();

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

		// Load js,css files 
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), 5 );
		add_action( 'wp_head', array( $this, 'frontend_templates' ), 9999 );
		add_action( 'woocommerce_tm_custom_price_fields_enqueue_scripts', array( $this, 'custom_frontend_scripts' ) );
		add_action( 'woocommerce_tm_epo_enqueue_scripts', array( $this, 'custom_frontend_scripts' ) );

		// Custom optional dequeue_scripts 
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_scripts' ), 9999 );

		// Custom CSS/JS support
		add_action( 'wp_enqueue_scripts', array( $this, 'print_extra_css_js' ), 99999 );

	}

	/**
	 * Custom CSS/JS support
	 *
	 * @since 1.0
	 */
	public function print_extra_css_js() {

		if ( ! empty( THEMECOMPLETE_EPO()->tm_epo_css_code ) ) {
			wp_register_style( 'themecomplete-extra-css', FALSE );
			wp_add_inline_style( 'themecomplete-extra-css', THEMECOMPLETE_EPO()->tm_epo_css_code );
			wp_enqueue_style( 'themecomplete-extra-css' );
		}
		if ( ! empty( THEMECOMPLETE_EPO()->tm_epo_js_code ) ) {
			wp_register_script( 'themecomplete-extra-js', FALSE );
			wp_add_inline_script( 'themecomplete-extra-js', THEMECOMPLETE_EPO()->tm_epo_js_code );
			wp_enqueue_script( 'themecomplete-extra-js' );
		}

	}

	/**
	 * Load js,css files
	 *
	 * @since 1.0
	 */
	public function frontend_scripts() {

		global $product;

		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {

			do_action( 'wc_epo_enqueue_scripts_before' );

			$this->custom_frontend_scripts();

			do_action( 'wc_epo_enqueue_scripts_after' );

		}

		return;

	}

	/**
	 * Load template files
	 *
	 * @since 1.0
	 */
	public function frontend_templates() {

		if ( ! THEMECOMPLETE_EPO()->can_load_scripts() ) {
			return;
		}

		$product = wc_get_product();

		add_filter( 'woocommerce_price_trim_zeros', array( $this, 'woocommerce_price_trim_zeros' ), 999999999 );

		// remove filters
		global $wp_filter;
		$saved_filter = FALSE;
		if ( isset( $wp_filter['raw_woocommerce_price'] ) ) {
			$saved_filter = $wp_filter['raw_woocommerce_price'];
			unset( $wp_filter['raw_woocommerce_price'] );
		}
		$saved_filter_formatted_woocommerce_price = FALSE;
		if ( isset( $wp_filter['formatted_woocommerce_price'] ) ) {
			$saved_filter_formatted_woocommerce_price = $wp_filter['formatted_woocommerce_price'];
			unset( $wp_filter['formatted_woocommerce_price'] );
		}
		$saved_filter_woocommerce_price_trim_zeros = FALSE;
		if ( isset( $wp_filter['woocommerce_price_trim_zeros'] ) ) {
			$saved_filter_woocommerce_price_trim_zeros = $wp_filter['woocommerce_price_trim_zeros'];
			unset( $wp_filter['woocommerce_price_trim_zeros'] );
		}
		$saved_filter_wc_price_args = FALSE;
		if ( isset( $wp_filter['wc_price_args'] ) ) {
			$saved_filter_wc_price_args = $wp_filter['wc_price_args'];
			unset( $wp_filter['wc_price_args'] );
		}

		$price1 = wc_price( 1234567890, array(
			'currency'           => get_woocommerce_currency(),
			'decimal_separator'  => '.',
			'thousand_separator' => ',',
			'decimals'           => 0,
		) );

		$price2 = wc_price( 987654321, array(
			'currency'           => get_woocommerce_currency(),
			'decimal_separator'  => '.',
			'thousand_separator' => ',',
			'decimals'           => 0,
		) );

		$formatted_price      = $price1;
		$formatted_sale_price = ( function_exists( 'wc_get_price_to_display' )
			? wc_format_sale_price( $price1, $price2 )
			: '<del>' . $price1 . '</del> <ins>' . $price2 . '</ins>'
		);

		// restore filters
		if ( $saved_filter ) {
			$wp_filter['raw_woocommerce_price'] = $saved_filter;
		}
		if ( $saved_filter_formatted_woocommerce_price ) {
			$wp_filter['formatted_woocommerce_price'] = $saved_filter_formatted_woocommerce_price;
		}
		if ( $saved_filter_woocommerce_price_trim_zeros ) {
			$wp_filter['woocommerce_price_trim_zeros'] = $saved_filter_woocommerce_price_trim_zeros;
		}
		if ( $saved_filter_wc_price_args ) {
			$wp_filter['wc_price_args'] = $saved_filter_wc_price_args;
		}

		remove_filter( 'woocommerce_price_trim_zeros', array( $this, 'woocommerce_price_trim_zeros' ), 999999999 );

		$formatted_sale_price = str_replace( '1,234,567,890', '{{{ data.price }}}', $formatted_sale_price );
		$formatted_sale_price = str_replace( '987,654,321', '{{{ data.sale_price }}}', $formatted_sale_price );


		$formatted_price = str_replace( '1,234,567,890', '{{{ data.price }}}', $formatted_price );

		$suffix = '';
		if ( $product ) {
			$suffix               = $product->get_price_suffix();
			$formatted_price      .= $suffix;
			$formatted_sale_price .= $suffix;
		}
		wc_get_template( 'tc-js-templates.php', array( 'formatted_price' => $formatted_price, 'formatted_sale_price' => $formatted_sale_price ), THEMECOMPLETE_EPO_DISPLAY()->get_namespace(), THEMECOMPLETE_EPO_TEMPLATE_PATH );

	}

	/**
	 * Custom optional dequeue_scripts
	 *
	 * @since 1.0
	 */
	public function dequeue_scripts() {

		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {

			do_action( 'wc_epo_dequeue_scripts' );

		}

	}

	/**
	 * Returns an array of the css scripts used
	 *
	 * @since 1.0
	 */
	public function css_array() {

		$ext = ".min";
		if ( THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode == "dev" ) {
			$ext = "";
		}
		if ( THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode == "multiple" || THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode == "dev" ) {
			$css_array = array(
				// The version of the fontawesome is customized
				'themecomplete-fontawesome' => array(
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/fontawesome' . $ext . '.css',
					'deps'    => FALSE,
					'version' => '5.12',
					'media'   => 'screen',
				),
				'themecomplete-animate'     => array(
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/animate' . $ext . '.css',
					'deps'    => FALSE,
					'version' => THEMECOMPLETE_EPO_VERSION,
					'media'   => 'all',
				),
				'themecomplete-epo'         => array(
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/tm-epo' . $ext . '.css',
					'deps'    => FALSE,
					'version' => THEMECOMPLETE_EPO_VERSION,
					'media'   => 'all',
				),
			);

			$product      = wc_get_product();
			$product_type = themecomplete_get_product_type( $product );
			$is_composite = $product_type == 'composite';

			if ( $is_composite || ! is_product() || in_array( "color", THEMECOMPLETE_EPO()->current_option_features ) ) {
				$css_array['spectrum'] = array(
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/spectrum' . $ext . '.css',
					'deps'    => FALSE,
					'version' => '1.8',
					'media'   => 'screen',
				);
			}

			if ( $is_composite || ! is_product() || in_array( "range", THEMECOMPLETE_EPO()->current_option_features ) ) {
				$css_array['nouislider'] = array(
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/nouislider' . $ext . '.css',
					'deps'    => FALSE,
					'version' => '13.1.1',
					'media'   => 'screen',
				);
			}

			if ( $is_composite || ! is_product() || in_array( "sectionslider", THEMECOMPLETE_EPO()->current_option_features ) ) {
				$css_array['owl-carousel2']       = array(
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/owl.carousel' . $ext . '.css',
					'deps'    => FALSE,
					'version' => '2.2',
					'media'   => 'all',
				);
				$css_array['owl-carousel2-theme'] = array(
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/owl.theme.default' . $ext . '.css',
					'deps'    => FALSE,
					'version' => '2.2',
					'media'   => 'all',
				);
			}
		} else {
			$css_array = array(
				'themecomplete-epo' => array(
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/epo.min.css',
					'deps'    => FALSE,
					'version' => THEMECOMPLETE_EPO_VERSION,
					'media'   => 'all',
				),
			);
		}

		if ( is_rtl() ) {
			$css_array['themecomplete-epo-rtl'] = array(
				'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/tm-epo-rtl' . $ext . '.css',
				'deps'    => FALSE,
				'version' => THEMECOMPLETE_EPO_VERSION,
				'media'   => 'all',
			);
		}

		return $css_array;

	}

	/**
	 * Trims zero from the WooCommerce price
	 *
	 * @since 1.0
	 */
	public function woocommerce_price_trim_zeros() {
		return TRUE;
	}

	/**
	 * Format array for the datepicker
	 *
	 * WordPress stores the locale information in an array with a alphanumeric index, and
	 * the datepicker wants a numerical index. This function replaces the index with a number
	 */
	private function strip_array_indices( $ArrayToStrip = array() ) {

		$NewArray = array();
		foreach ( $ArrayToStrip as $objArrayItem ) {
			$NewArray[] = $objArrayItem;
		}

		return ( $NewArray );

	}

	/**
	 * Load js,css files
	 *
	 * @since 1.0
	 */
	public function custom_frontend_scripts() {

		$this->defered_files = array();
		$ext                 = ".min";
		if ( THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode == "dev" ) {
			$ext = "";
		}
		do_action( 'tm_epo_register_addons_scripts' );
		if ( apply_filters( 'wc_epo_register_addons_scripts', FALSE ) ) {
			return;
		}
		$product = wc_get_product();

		if ( $enqueue_styles = apply_filters( 'tm_epo_enqueue_styles', $this->css_array() ) ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				wp_enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
			}
		}

		$dependencies   = array();
		$dependencies[] = 'jquery-ui-slider';
		$dependencies[] = 'wp-util';
		$dependencies[] = 'jquery';

		$product_type = themecomplete_get_product_type( $product );
		$is_composite = $product_type == 'composite';

		if ( $is_composite || ! is_product() || ( in_array( "product", THEMECOMPLETE_EPO()->current_option_features ) ) || in_array( "sectionslider", THEMECOMPLETE_EPO()->current_option_features ) ) {
			wp_register_script( 'wc-jquery-ui-touchpunch', WC()->plugin_url() . '/assets/js/jquery-ui-touch-punch/jquery-ui-touch-punch' . $ext . '.js', array( 'jquery-ui-slider' ), '0.2.3', TRUE );
			$dependencies[] = 'wc-jquery-ui-touchpunch';
		}

		if ( THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode == "multiple" || THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode == "dev" ) {

			$dependencies[] = 'themecomplete-api';
			wp_register_script( 'themecomplete-api', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-api' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, TRUE );

			$dependencies[] = 'jquery-tcfloatbox';
			wp_register_script( 'jquery-tcfloatbox', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tcfloatbox' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, TRUE );

			$dependencies[] = 'jquery-tctooltip';
			wp_register_script( 'jquery-tctooltip', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tctooltip' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, TRUE );

			if ( $is_composite || ! is_product() || ( in_array( "validation", THEMECOMPLETE_EPO()->current_option_features ) ) ) {
				// This is a customized version of the jQuery Validation Plugin
				$dependencies[] = 'themecomplete-jquery-validate';
				wp_register_script( 'themecomplete-jquery-validate', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.validate' . $ext . '.js', '', '1.19.0', TRUE );
			}

			if ( $is_composite || ! is_product() || in_array( "lazyload", THEMECOMPLETE_EPO()->current_option_features ) ) {
				$dependencies[]        = 'lazyloadxt-extra';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.lazyloadxt.extra' . $ext . '.js';
				wp_register_script( 'lazyloadxt-extra', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.lazyloadxt.extra' . $ext . '.js', array( 'jquery' ), "1.1", TRUE );
			}

			if ( $is_composite || ! is_product() || ( in_array( "product", THEMECOMPLETE_EPO()->current_option_features ) ) || in_array( "range", THEMECOMPLETE_EPO()->current_option_features ) ) {
				$dependencies[]        = 'nouislider';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/nouislider' . $ext . '.js';
				wp_register_script( 'nouislider', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/nouislider' . $ext . '.js', array( 'jquery' ), "13.1.1", TRUE );
			}

			if ( $is_composite || ! is_product() || ( in_array( "product", THEMECOMPLETE_EPO()->current_option_features ) ) || ( in_array( "date", THEMECOMPLETE_EPO()->current_option_features ) || in_array( "time", THEMECOMPLETE_EPO()->current_option_features ) ) ) {
				// This is a customized version of the jQuery Datepicker Plugin
				$dependencies[] = 'jquery-ui-core';
				$dependencies[] = 'themecomplete-datepicker';
				$dependencies[] = 'jquery-resizestop';

				wp_register_script( 'jquery-resizestop', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.resizestop' . $ext . '.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION, TRUE );

				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-datepicker' . $ext . '.js';
				wp_register_script( 'themecomplete-datepicker', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-datepicker' . $ext . '.js', array( 'jquery', 'jquery-ui-core', 'jquery-resizestop' ), THEMECOMPLETE_EPO_VERSION, TRUE );
			}

			if ( $is_composite || ! is_product() || ( in_array( "product", THEMECOMPLETE_EPO()->current_option_features ) ) || in_array( "time", THEMECOMPLETE_EPO()->current_option_features ) ) {
				// This is a customized version of the jQuery Timepicker Plugin
				$dependencies[]        = 'jquery-ui-core';
				$dependencies[]        = 'themecomplete-datepicker';
				$dependencies[]        = 'themecomplete-timepicker';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-timepicker' . $ext . '.js';
				wp_register_script( 'themecomplete-timepicker', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-timepicker' . $ext . '.js', array( 'jquery', 'jquery-ui-core', 'themecomplete-datepicker' ), THEMECOMPLETE_EPO_VERSION, TRUE );
			}
			if ( $is_composite || ! is_product() || ( in_array( "product", THEMECOMPLETE_EPO()->current_option_features ) ) || in_array( "sectionslider", THEMECOMPLETE_EPO()->current_option_features ) ) {
				$dependencies[]        = 'owl-carousel2';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/owl.carousel' . $ext . '.js';
				wp_register_script( 'owl-carousel2', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/owl.carousel' . $ext . '.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION, TRUE );
			}

			if ( $is_composite || ! is_product() || ( in_array( "product", THEMECOMPLETE_EPO()->current_option_features ) ) || in_array( "color", THEMECOMPLETE_EPO()->current_option_features ) ) {
				$dependencies[]        = 'spectrum';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/spectrum' . $ext . '.js';
				wp_register_script( 'spectrum', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/spectrum' . $ext . '.js', array( 'jquery' ), "1.8", TRUE );
			}

			if ( $is_composite || ! is_product() || ( in_array( "product", THEMECOMPLETE_EPO()->current_option_features ) ) || ( in_array( "date", THEMECOMPLETE_EPO()->current_option_features ) || in_array( "time", THEMECOMPLETE_EPO()->current_option_features ) ) ) {
				$dependencies[]        = 'jquery-mask';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.mask' . $ext . '.js';
				wp_register_script( 'jquery-mask', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.mask' . $ext . '.js', array( 'jquery' ), "1.14.15", TRUE );
			}

			if ( ! is_product() || ( in_array( "product", THEMECOMPLETE_EPO()->current_option_features ) ) ) {
				$dependencies[]        = 'themecomplete-epo-product';
				$dependencies[]        = 'wc-add-to-cart-variation';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-epo-product' . $ext . '.js';
				wp_register_script( 'themecomplete-epo-product', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-epo-product' . $ext . '.js', array( 'jquery' ), "", TRUE );
			}

			$dependencies[]        = 'themecomplete-math-expression-evaluator';
			$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/math-expression-evaluator' . $ext . '.js';
			wp_register_script( 'themecomplete-math-expression-evaluator', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/math-expression-evaluator' . $ext . '.js', array( 'jquery' ), "1.12.17", TRUE );

			$dependencies          = array_unique( $dependencies );
			$dependencies          = apply_filters( 'wc_epo_script_dependencies', $dependencies );
			$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-epo' . $ext . '.js';
			wp_register_script( 'themecomplete-epo', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-epo' . $ext . '.js', $dependencies, THEMECOMPLETE_EPO_VERSION, TRUE );
			wp_enqueue_script( 'themecomplete-epo' );

		} else {
			$dependencies[] = 'jquery-ui-core';
			$dependencies[] = 'wc-add-to-cart-variation';

			$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/epo.min.js';
			wp_register_script( 'themecomplete-epo', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/epo.min.js', $dependencies, THEMECOMPLETE_EPO_VERSION, TRUE );
			wp_enqueue_script( 'themecomplete-epo' );
		}

		$extra_fee = 0;
		global $wp_locale;
		$args = array(
			'product_id'                => themecomplete_get_id( $product ),
			'ajax_url'                  => admin_url( 'admin-ajax' ) . '.php',//WPML 3.3.3 fix
			'extraFee'                  => apply_filters( 'woocommerce_tm_final_price_extra_fee', $extra_fee, $product ),
			'i18n_extra_fee'            => esc_html__( 'Extra fee', 'woocommerce-tm-extra-product-options' ),
			'i18n_unit_price'           => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_options_unit_price_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_options_unit_price_text ) : esc_html__( 'Unit price', 'woocommerce-tm-extra-product-options' ),
			'i18n_options_total'        => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_options_total_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_options_total_text ) : esc_html__( 'Options amount', 'woocommerce-tm-extra-product-options' ),
			'i18n_fees_total'           => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_fees_total_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_fees_total_text ) : esc_html__( 'Fees amount', 'woocommerce-tm-extra-product-options' ),
			'i18n_final_total'          => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_final_total_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_final_total_text ) : esc_html__( 'Final total', 'woocommerce-tm-extra-product-options' ),
			'i18n_prev_text'            => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_slider_prev_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_slider_prev_text ) : esc_html__( 'Prev', 'woocommerce-tm-extra-product-options' ),
			'i18n_next_text'            => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_slider_next_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_slider_next_text ) : esc_html__( 'Next', 'woocommerce-tm-extra-product-options' ),
			'i18n_cancel'               => esc_html__( 'Cancel', 'woocommerce-tm-extra-product-options' ),
			'i18n_close'                => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_close_button_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_close_button_text ) : esc_html__( 'Close', 'woocommerce-tm-extra-product-options' ),
			'i18n_addition_options'     => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_additional_options_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_additional_options_text ) : esc_html__( 'Additional options', 'woocommerce-tm-extra-product-options' ),
			'i18n_characters_remaining' => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_characters_remaining_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_characters_remaining_text ) : esc_html__( 'characters remaining', 'woocommerce-tm-extra-product-options' ),

			'i18n_option_label' => esc_html__( 'Label', 'woocommerce-tm-extra-product-options' ),
			'i18n_option_value' => esc_html__( 'Value', 'woocommerce-tm-extra-product-options' ),
			'i18n_option_qty'   => esc_html__( 'Qty', 'woocommerce-tm-extra-product-options' ),
			'i18n_option_price' => esc_html__( 'Price', 'woocommerce-tm-extra-product-options' ),

			'i18n_uploading_files'   => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_uploading_files_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_uploading_files_text ) : esc_html__( 'Uploading files', 'woocommerce-tm-extra-product-options' ),
			'i18n_uploading_message' => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_uploading_message_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_uploading_message_text ) : esc_html__( 'Your files are being uploaded', 'woocommerce-tm-extra-product-options' ),

			'currency_format_num_decimals'                => apply_filters( 'wc_epo_price_decimals', esc_attr( wc_get_price_decimals() ) ),
			'currency_format_symbol'                      => esc_attr( get_woocommerce_currency_symbol() ),
			'currency_format_decimal_sep'                 => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
			'currency_format_thousand_sep'                => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
			'currency_format'                             => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
			'css_styles'                                  => THEMECOMPLETE_EPO()->tm_epo_css_styles,
			'css_styles_style'                            => THEMECOMPLETE_EPO()->tm_epo_css_styles_style,
			'tm_epo_options_placement'                    => THEMECOMPLETE_EPO()->tm_epo_options_placement,
			'tm_epo_totals_box_placement'                 => THEMECOMPLETE_EPO()->tm_epo_totals_box_placement,
			'tm_epo_no_lazy_load'                         => THEMECOMPLETE_EPO()->tm_epo_no_lazy_load,
			'tm_epo_show_only_active_quantities'          => THEMECOMPLETE_EPO()->tm_epo_show_only_active_quantities,
			'tm_epo_hide_add_cart_button'                 => THEMECOMPLETE_EPO()->tm_epo_hide_add_cart_button,
			'tm_epo_auto_hide_price_if_zero'              => THEMECOMPLETE_EPO()->tm_epo_auto_hide_price_if_zero,
			'tm_epo_show_price_inside_option'             => THEMECOMPLETE_EPO()->tm_epo_show_price_inside_option,
			'tm_epo_show_price_inside_option_hidden_even' => THEMECOMPLETE_EPO()->tm_epo_show_price_inside_option_hidden_even,
			'tm_epo_multiply_price_inside_option'         => THEMECOMPLETE_EPO()->tm_epo_multiply_price_inside_option,
			"tm_epo_global_enable_validation"             => THEMECOMPLETE_EPO()->tm_epo_global_enable_validation,
			"tm_epo_global_input_decimal_separator"       => THEMECOMPLETE_EPO()->tm_epo_global_input_decimal_separator,
			"tm_epo_global_displayed_decimal_separator"   => THEMECOMPLETE_EPO()->tm_epo_global_displayed_decimal_separator,
			"tm_epo_remove_free_price_label"              => THEMECOMPLETE_EPO()->tm_epo_remove_free_price_label,
			"tm_epo_global_product_image_selector"        => THEMECOMPLETE_EPO()->tm_epo_global_product_image_selector,
			"tm_epo_upload_inline_image_preview"          => THEMECOMPLETE_EPO()->tm_epo_upload_inline_image_preview,
			"tm_epo_global_product_element_scroll_offset" => THEMECOMPLETE_EPO()->tm_epo_global_product_element_scroll_offset,
			"tm_epo_global_product_image_mode"            => THEMECOMPLETE_EPO()->tm_epo_global_product_image_mode,
			"tm_epo_global_move_out_of_stock"             => THEMECOMPLETE_EPO()->tm_epo_global_move_out_of_stock,
			"tm_epo_progressive_display"                  => THEMECOMPLETE_EPO()->tm_epo_progressive_display,
			"tm_epo_animation_delay"                      => THEMECOMPLETE_EPO()->tm_epo_animation_delay,
			"tm_epo_start_animation_delay"                => THEMECOMPLETE_EPO()->tm_epo_start_animation_delay,
			"tm_epo_global_error_label_placement"         => THEMECOMPLETE_EPO()->tm_epo_global_error_label_placement,

			"tm_epo_global_validator_messages" => array(
				"required"                 => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_this_field_is_required_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_this_field_is_required_text ) : esc_html__( "This field is required.", 'woocommerce-tm-extra-product-options' ),
				"email"                    => esc_html__( "Please enter a valid email address.", 'woocommerce-tm-extra-product-options' ),
				"url"                      => esc_html__( "Please enter a valid URL.", 'woocommerce-tm-extra-product-options' ),
				"number"                   => esc_html__( "Please enter a valid number.", 'woocommerce-tm-extra-product-options' ),
				"digits"                   => esc_html__( "Please enter only digits.", 'woocommerce-tm-extra-product-options' ),
				"max"                      => esc_html__( "Please enter a value less than or equal to {0}.", 'woocommerce-tm-extra-product-options' ),
				"min"                      => esc_html__( "Please enter a value greater than or equal to {0}.", 'woocommerce-tm-extra-product-options' ),
				"maxlengthsingle"          => esc_html__( "Please enter no more than {0} character.", 'woocommerce-tm-extra-product-options' ),
				"maxlength"                => esc_html__( "Please enter no more than {0} characters.", 'woocommerce-tm-extra-product-options' ),
				"minlengthsingle"          => esc_html__( "Please enter at least {0} character.", 'woocommerce-tm-extra-product-options' ),
				"minlength"                => esc_html__( "Please enter at least {0} characters.", 'woocommerce-tm-extra-product-options' ),
				"epolimitsingle"           => esc_html__( "Please select up to {0} choice.", 'woocommerce-tm-extra-product-options' ),
				"epolimit"                 => esc_html__( "Please select up to {0} choices.", 'woocommerce-tm-extra-product-options' ),
				"epoexactsingle"           => esc_html__( "Please select exactly {0} choice.", 'woocommerce-tm-extra-product-options' ),
				"epoexact"                 => esc_html__( "Please select exactly {0} choices.", 'woocommerce-tm-extra-product-options' ),
				"epominsingle"             => esc_html__( "Please select at least {0} choice.", 'woocommerce-tm-extra-product-options' ),
				"epomin"                   => esc_html__( "Please select at least {0} choices.", 'woocommerce-tm-extra-product-options' ),
				"step"                     => esc_html__( "Please enter a multiple of {0}.", 'woocommerce-tm-extra-product-options' ),
				"lettersonly"              => esc_html__( "Please enter only letters.", 'woocommerce-tm-extra-product-options' ),
				"lettersspaceonly"         => esc_html__( "Please enter only letters or spaces.", 'woocommerce-tm-extra-product-options' ),
				"alphanumeric"             => esc_html__( "Please enter only letters, numbers or underscores.", 'woocommerce-tm-extra-product-options' ),
				"alphanumericunicode"      => esc_html__( "Please enter only unicode letters and numbers.", 'woocommerce-tm-extra-product-options' ),
				"alphanumericunicodespace" => esc_html__( "Please enter only unicode letters, numbers or spaces.", 'woocommerce-tm-extra-product-options' ),
			),

			'first_day'       => intval( get_option( 'start_of_week' ) ),
			'monthNames'      => $this->strip_array_indices( $wp_locale->month ),
			'monthNamesShort' => $this->strip_array_indices( $wp_locale->month_abbrev ),
			'dayNames'        => $this->strip_array_indices( $wp_locale->weekday ),
			'dayNamesShort'   => $this->strip_array_indices( $wp_locale->weekday_abbrev ),
			'dayNamesMin'     => $this->strip_array_indices( $wp_locale->weekday_initial ),
			'isRTL'           => $wp_locale->text_direction == 'rtl',
			'text_direction'  => $wp_locale->text_direction,
			'is_rtl'          => is_rtl(),
			'closeText'       => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_closeText ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_closeText ) : esc_html__( 'Done', 'woocommerce-tm-extra-product-options' ),
			'currentText'     => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_currentText ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_currentText ) : esc_html__( 'Today', 'woocommerce-tm-extra-product-options' ),

			'hourText'   => esc_html__( 'Hour', 'woocommerce-tm-extra-product-options' ),
			'minuteText' => esc_html__( 'Minute', 'woocommerce-tm-extra-product-options' ),
			'secondText' => esc_html__( 'Second', 'woocommerce-tm-extra-product-options' ),

			'floating_totals_box'               => THEMECOMPLETE_EPO()->tm_epo_floating_totals_box,
			'floating_totals_box_visibility'    => THEMECOMPLETE_EPO()->tm_epo_floating_totals_box_visibility,
			'floating_totals_box_add_button'    => THEMECOMPLETE_EPO()->tm_epo_floating_totals_box_add_button,
			'floating_totals_box_pixels'        => THEMECOMPLETE_EPO()->tm_epo_totals_box_pixels,
			'floating_totals_box_html_before'   => apply_filters( 'floating_totals_box_html_before', '' ),
			'floating_totals_box_html_after'    => apply_filters( 'floating_totals_box_html_after', '' ),
			'tm_epo_show_unit_price'            => THEMECOMPLETE_EPO()->tm_epo_show_unit_price,
			'tm_epo_fees_on_unit_price'         => THEMECOMPLETE_EPO()->tm_epo_fees_on_unit_price,
			'tm_epo_total_price_as_unit_price'  => THEMECOMPLETE_EPO()->tm_epo_total_price_as_unit_price,
			'tm_epo_enable_final_total_box_all' => THEMECOMPLETE_EPO()->tm_epo_enable_final_total_box_all,
			'tm_epo_change_original_price'      => THEMECOMPLETE_EPO()->tm_epo_change_original_price,
			'tm_epo_change_variation_price'     => THEMECOMPLETE_EPO()->tm_epo_change_variation_price,
			'tm_epo_enable_in_shop'             => THEMECOMPLETE_EPO()->tm_epo_enable_in_shop,
			'tm_epo_disable_error_scroll'       => THEMECOMPLETE_EPO()->tm_epo_disable_error_scroll,
			'tm_epo_global_options_price_sign'  => THEMECOMPLETE_EPO()->tm_epo_global_options_price_sign,

			'minus_sign' => apply_filters( 'wc_epo_get_price_for_cart_minus_sign', "<span class='tc-minus-sign'>-</span>" ),
			'plus_sign'  => apply_filters( 'wc_epo_get_price_for_cart_plus_sign', "<span class='tc-minus-sign'>+</span>" ),

			'tm_epo_upload_popup' => THEMECOMPLETE_EPO()->tm_epo_upload_popup,

			'current_free_text' => esc_html( THEMECOMPLETE_EPO()->current_free_text ),

			'cart_total' => floatval( WC()->cart->get_cart_contents_total() ),

			'quickview_container' => esc_html( wp_json_encode( apply_filters( 'wc_epo_js_quickview_container', array() ) ) ),
			'quickview_array'     => esc_html( wp_json_encode( apply_filters( 'wc_epo_get_quickview_containers', array() ) ) ),
		);

		$args = apply_filters( 'wc_epo_script_args', $args, $this );

		wp_localize_script( 'themecomplete-epo', 'TMEPOJS', $args );

	}

}
