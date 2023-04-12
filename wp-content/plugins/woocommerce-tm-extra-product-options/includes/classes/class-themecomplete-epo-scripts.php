<?php
/**
 * Extra Product Options Frontend Scripts
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Frontend Scripts
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
class THEMECOMPLETE_EPO_Scripts {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_Scripts|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Contains files to be defered
	 *
	 * @var array
	 */
	public $defered_files = [];

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

		// Load js,css files.
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_scripts' ], 5 );
		add_action( 'wp_head', [ $this, 'frontend_templates' ], 9999 );
		add_action( 'woocommerce_tm_custom_price_fields_enqueue_scripts', [ $this, 'custom_frontend_scripts' ] );
		add_action( 'woocommerce_tm_epo_enqueue_scripts', [ $this, 'custom_frontend_scripts' ] );

		// Custom optional dequeue_scripts.
		add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_scripts' ], 9999 );

		// Custom CSS/JS support.
		add_action( 'wp_enqueue_scripts', [ $this, 'print_extra_css_js' ], 99999 );

	}

	/**
	 * Custom CSS/JS support
	 *
	 * @since 1.0
	 */
	public function print_extra_css_js() {

		if ( ! empty( THEMECOMPLETE_EPO()->tm_epo_css_code ) ) {
			wp_register_style( 'themecomplete-extra-css', false, [], THEMECOMPLETE_EPO_VERSION );
			wp_add_inline_style( 'themecomplete-extra-css', THEMECOMPLETE_EPO()->tm_epo_css_code );
			wp_enqueue_style( 'themecomplete-extra-css' );
		}
		if ( ! empty( THEMECOMPLETE_EPO()->tm_epo_js_code ) ) {
			wp_register_script( 'themecomplete-extra-js', false, [], THEMECOMPLETE_EPO_VERSION, false );
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

		add_filter( 'woocommerce_price_trim_zeros', [ $this, 'woocommerce_price_trim_zeros' ], 999999999 );

		// remove filters.
		global $wp_filter;
		$saved_filter = false;
		if ( isset( $wp_filter['raw_woocommerce_price'] ) ) {
			$saved_filter = $wp_filter['raw_woocommerce_price'];
			unset( $wp_filter['raw_woocommerce_price'] );
		}
		$saved_filter_formatted_woocommerce_price = false;
		if ( isset( $wp_filter['formatted_woocommerce_price'] ) ) {
			$saved_filter_formatted_woocommerce_price = $wp_filter['formatted_woocommerce_price'];
			unset( $wp_filter['formatted_woocommerce_price'] );
		}
		$saved_filter_woocommerce_price_trim_zeros = false;
		if ( isset( $wp_filter['woocommerce_price_trim_zeros'] ) ) {
			$saved_filter_woocommerce_price_trim_zeros = $wp_filter['woocommerce_price_trim_zeros'];
			unset( $wp_filter['woocommerce_price_trim_zeros'] );
		}
		$saved_filter_wc_price_args = false;
		if ( isset( $wp_filter['wc_price_args'] ) ) {
			$saved_filter_wc_price_args = $wp_filter['wc_price_args'];
			unset( $wp_filter['wc_price_args'] );
		}

		$price1 = wc_price(
			1234567890,
			[
				'currency'           => get_woocommerce_currency(),
				'decimal_separator'  => '.',
				'thousand_separator' => ',',
				'decimals'           => 0,
			]
		);

		$price2 = wc_price(
			987654321,
			[
				'currency'           => get_woocommerce_currency(),
				'decimal_separator'  => '.',
				'thousand_separator' => ',',
				'decimals'           => 0,
			]
		);

		$formatted_price      = $price1;
		$formatted_sale_price = ( function_exists( 'wc_get_price_to_display' )
			? wc_format_sale_price( $price1, $price2 )
			: '<del>' . $price1 . '</del> <ins>' . $price2 . '</ins>'
		);

		// restore filters.
		if ( $saved_filter ) {
			$wp_filter['raw_woocommerce_price'] = $saved_filter; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
		if ( $saved_filter_formatted_woocommerce_price ) {
			$wp_filter['formatted_woocommerce_price'] = $saved_filter_formatted_woocommerce_price; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
		if ( $saved_filter_woocommerce_price_trim_zeros ) {
			$wp_filter['woocommerce_price_trim_zeros'] = $saved_filter_woocommerce_price_trim_zeros; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
		if ( $saved_filter_wc_price_args ) {
			$wp_filter['wc_price_args'] = $saved_filter_wc_price_args; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}

		remove_filter( 'woocommerce_price_trim_zeros', [ $this, 'woocommerce_price_trim_zeros' ], 999999999 );

		$formatted_sale_price = str_replace( '1,234,567,890', '{{{ data.price }}}', $formatted_sale_price );
		$formatted_sale_price = str_replace( '987,654,321', '{{{ data.sale_price }}}', $formatted_sale_price );

		$formatted_price = str_replace( '1,234,567,890', '{{{ data.price }}}', $formatted_price );

		$suffix = '';
		if ( $product ) {
			$taxable          = $product->is_taxable();
			$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
			$suffix           = '';
			if ( $taxable && 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_tax_string_suffix ) {
				if ( 'excl' === $tax_display_mode ) {

					$suffix = ' <small>' . apply_filters( 'wc_epo_ex_tax_or_vat_string', WC()->countries->ex_tax_or_vat() ) . '</small>';

				} else {

					$suffix = ' <small>' . apply_filters( 'wc_epo_inc_tax_or_vat_string', WC()->countries->inc_tax_or_vat() ) . '</small>';

				}
			}
			if ( $taxable && 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_wc_price_suffix ) {
				$suffix .= ' <small>' . get_option( 'woocommerce_price_display_suffix' ) . '</small>';
			}
			$formatted_price      .= $suffix;
			$formatted_sale_price .= $suffix;
		}
		wc_get_template(
			'tc-js-templates.php',
			[
				'formatted_price'      => $formatted_price,
				'formatted_sale_price' => $formatted_sale_price,
			],
			THEMECOMPLETE_EPO_DISPLAY()->get_template_path(),
			THEMECOMPLETE_EPO_DISPLAY()->get_default_path()
		);

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

		$ext = '.min';
		if ( 'dev' === THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode ) {
			$ext = '';
		}
		if ( 'multiple' === THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode || 'dev' === THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode ) {
			$css_array = [
				// The version of the fontawesome is customized.
				'themecomplete-fontawesome' => [
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/fontawesome' . $ext . '.css',
					'deps'    => false,
					'version' => '5.12',
					'media'   => 'screen',
				],
				'themecomplete-animate'     => [
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/animate' . $ext . '.css',
					'deps'    => false,
					'version' => THEMECOMPLETE_EPO_VERSION,
					'media'   => 'all',
				],
				'themecomplete-epo'         => [
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/tm-epo' . $ext . '.css',
					'deps'    => false,
					'version' => THEMECOMPLETE_EPO_VERSION,
					'media'   => 'all',
				],
			];

			$product      = wc_get_product();
			$product_type = themecomplete_get_product_type( $product );
			$is_composite = 'composite' === $product_type;

			if ( $is_composite || ! is_product() || in_array( 'product', THEMECOMPLETE_EPO()->current_option_features, true ) || in_array( 'color', THEMECOMPLETE_EPO()->current_option_features, true ) ) {
				$css_array['spectrum'] = [
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/spectrum' . $ext . '.css',
					'deps'    => false,
					'version' => '2.0',
					'media'   => 'screen',
				];
			}

			if ( $is_composite || ! is_product() || in_array( 'product', THEMECOMPLETE_EPO()->current_option_features, true ) || in_array( 'range', THEMECOMPLETE_EPO()->current_option_features, true ) ) {
				$css_array['nouislider'] = [
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/nouislider' . $ext . '.css',
					'deps'    => false,
					'version' => '13.1.1',
					'media'   => 'screen',
				];
			}

			if ( $is_composite || ! is_product() || in_array( 'product', THEMECOMPLETE_EPO()->current_option_features, true ) || in_array( 'sectionslider', THEMECOMPLETE_EPO()->current_option_features, true ) ) {
				$css_array['owl-carousel2']       = [
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/owl.carousel' . $ext . '.css',
					'deps'    => false,
					'version' => '2.2',
					'media'   => 'all',
				];
				$css_array['owl-carousel2-theme'] = [
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/owl.theme.default' . $ext . '.css',
					'deps'    => false,
					'version' => '2.2',
					'media'   => 'all',
				];
			}
		} else {
			$css_array = [
				'themecomplete-epo' => [
					'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/epo.min.css',
					'deps'    => false,
					'version' => THEMECOMPLETE_EPO_VERSION,
					'media'   => 'all',
				],
			];
		}

		$css_array['themecomplete-epo-smallscreen'] = [
			'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/tm-epo-smallscreen' . $ext . '.css',
			'deps'    => false,
			'version' => THEMECOMPLETE_EPO_VERSION,
			'media'   => 'only screen and (max-width: ' . apply_filters( 'woocommerce_style_smallscreen_breakpoint', '768px' ) . ')',
		];

		if ( is_rtl() ) {
			$css_array['themecomplete-epo-rtl'] = [
				'src'     => THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/tm-epo-rtl' . $ext . '.css',
				'deps'    => false,
				'version' => THEMECOMPLETE_EPO_VERSION,
				'media'   => 'all',
			];
		}

		return $css_array;

	}

	/**
	 * Trims zero from the WooCommerce price
	 *
	 * @since 1.0
	 */
	public function woocommerce_price_trim_zeros() {
		return true;
	}

	/**
	 * Format array for the datepicker
	 *
	 * WordPress stores the locale information in an array with a alphanumeric index, and
	 * the datepicker wants a numerical index. This function replaces the index with a number.
	 *
	 * @param array $array_to_strip The array to strip indices from.
	 */
	private function strip_array_indices( $array_to_strip = [] ) {

		$new_array = [];
		foreach ( $array_to_strip as $array_item ) {
			$new_array[] = $array_item;
		}

		return ( $new_array );

	}

	/**
	 * Load js,css files
	 *
	 * @since 1.0
	 */
	public function custom_frontend_scripts() {

		$this->defered_files = [];
		$ext                 = '.min';
		if ( 'dev' === THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode ) {
			$ext = '';
		}
		do_action( 'tm_epo_register_addons_scripts' );
		if ( apply_filters( 'wc_epo_register_addons_scripts', false ) ) {
			return;
		}
		$product = wc_get_product();

		$enqueue_styles = apply_filters( 'tm_epo_enqueue_styles', $this->css_array() );
		if ( $enqueue_styles && is_array( $enqueue_styles ) ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				wp_enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
			}
		}
		global $post, $product;

		$dependencies   = [];
		$dependencies[] = 'jquery-ui-slider';
		$dependencies[] = 'wp-util';
		$dependencies[] = 'jquery';

		$product_type = themecomplete_get_product_type( $product );
		$is_composite = 'composite' === $product_type;

		if ( $is_composite || ! is_product() || ( in_array( 'product', THEMECOMPLETE_EPO()->current_option_features, true ) ) || in_array( 'sectionslider', THEMECOMPLETE_EPO()->current_option_features, true ) ) {
			wp_register_script( 'wc-jquery-ui-touchpunch', WC()->plugin_url() . '/assets/js/jquery-ui-touch-punch/jquery-ui-touch-punch' . $ext . '.js', [ 'jquery-ui-slider' ], '0.2.3', true );
			$dependencies[] = 'wc-jquery-ui-touchpunch';
		}

		if ( 'multiple' === THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode || 'dev' === THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode ) {

			$dependencies[] = 'themecomplete-api';
			wp_register_script( 'themecomplete-api', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-api' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );

			$dependencies[] = 'jquery-tcfloatbox';
			wp_register_script( 'jquery-tcfloatbox', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tcfloatbox' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );

			$dependencies[] = 'jquery-tctooltip';
			wp_register_script( 'jquery-tctooltip', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tctooltip' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );

			if ( $is_composite || ! is_product() || ( in_array( 'validation', THEMECOMPLETE_EPO()->current_option_features, true ) ) ) {
				// This is a customized version of the jQuery Validation Plugin.
				$dependencies[] = 'themecomplete-jquery-validate';
				wp_register_script( 'themecomplete-jquery-validate', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.validate' . $ext . '.js', '', '1.19.0', true );
			}

			if ( $is_composite || ! is_product() || in_array( 'lazyload', THEMECOMPLETE_EPO()->current_option_features, true ) ) {
				$dependencies[]        = 'lazyloadxt-extra';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.lazyloadxt.extra' . $ext . '.js';
				wp_register_script( 'lazyloadxt-extra', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.lazyloadxt.extra' . $ext . '.js', [ 'jquery' ], '1.1', true );
			}

			if ( $is_composite || ! is_product() || ( in_array( 'product', THEMECOMPLETE_EPO()->current_option_features, true ) ) || in_array( 'range', THEMECOMPLETE_EPO()->current_option_features, true ) ) {
				$dependencies[]        = 'nouislider';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/nouislider' . $ext . '.js';
				wp_register_script( 'nouislider', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/nouislider' . $ext . '.js', [ 'jquery' ], '13.1.1', true );
			}

			if ( $is_composite || ! is_product() || ( in_array( 'product', THEMECOMPLETE_EPO()->current_option_features, true ) ) || ( in_array( 'date', THEMECOMPLETE_EPO()->current_option_features, true ) || in_array( 'time', THEMECOMPLETE_EPO()->current_option_features, true ) ) ) {
				// This is a customized version of the jQuery Datepicker Plugin.
				$dependencies[] = 'jquery-ui-core';
				$dependencies[] = 'themecomplete-datepicker';
				$dependencies[] = 'jquery-resizestop';

				wp_register_script( 'jquery-resizestop', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.resizestop' . $ext . '.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );

				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-datepicker' . $ext . '.js';
				wp_register_script( 'themecomplete-datepicker', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-datepicker' . $ext . '.js', [ 'jquery', 'jquery-ui-core', 'jquery-resizestop' ], THEMECOMPLETE_EPO_VERSION, true );
			}

			if ( $is_composite || ! is_product() || ( in_array( 'product', THEMECOMPLETE_EPO()->current_option_features, true ) ) || in_array( 'time', THEMECOMPLETE_EPO()->current_option_features, true ) ) {
				// This is a customized version of the jQuery Timepicker Plugin.
				$dependencies[]        = 'jquery-ui-core';
				$dependencies[]        = 'themecomplete-datepicker';
				$dependencies[]        = 'themecomplete-timepicker';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-timepicker' . $ext . '.js';
				wp_register_script( 'themecomplete-timepicker', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-timepicker' . $ext . '.js', [ 'jquery', 'jquery-ui-core', 'themecomplete-datepicker' ], THEMECOMPLETE_EPO_VERSION, true );
			}
			if ( $is_composite || ! is_product() || ( in_array( 'product', THEMECOMPLETE_EPO()->current_option_features, true ) ) || in_array( 'sectionslider', THEMECOMPLETE_EPO()->current_option_features, true ) ) {
				$dependencies[]        = 'owl-carousel2';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/owl.carousel' . $ext . '.js';
				wp_register_script( 'owl-carousel2', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/owl.carousel' . $ext . '.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
			}

			if ( $is_composite || ! is_product() || ( in_array( 'product', THEMECOMPLETE_EPO()->current_option_features, true ) ) || in_array( 'sectiontabs', THEMECOMPLETE_EPO()->current_option_features, true ) ) {
				$dependencies[]        = 'themecomplete-tabs';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tctabs' . $ext . '.js';
				wp_register_script( 'themecomplete-tabs', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tctabs' . $ext . '.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
			}

			if ( $is_composite || ! is_product() || ( in_array( 'product', THEMECOMPLETE_EPO()->current_option_features, true ) ) || in_array( 'color', THEMECOMPLETE_EPO()->current_option_features, true ) ) {
				$dependencies[]        = 'spectrum';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/spectrum' . $ext . '.js';
				wp_register_script( 'spectrum', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/spectrum' . $ext . '.js', [ 'jquery' ], '2.0', true );
			}

			if ( $is_composite || ! is_product() || ( in_array( 'product', THEMECOMPLETE_EPO()->current_option_features, true ) ) || ( in_array( 'date', THEMECOMPLETE_EPO()->current_option_features, true ) || in_array( 'time', THEMECOMPLETE_EPO()->current_option_features, true ) ) ) {
				$dependencies[]        = 'jquery-mask';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.mask' . $ext . '.js';
				wp_register_script( 'jquery-mask', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.mask' . $ext . '.js', [ 'jquery' ], '1.14.15', true );
			}

			// Not supported for composite products.
			if ( ! is_product() || ( in_array( 'product', THEMECOMPLETE_EPO()->current_option_features, true ) ) ) {
				$dependencies[]        = 'themecomplete-epo-product';
				$dependencies[]        = 'wc-add-to-cart-variation';
				$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-epo-product' . $ext . '.js';
				wp_register_script( 'themecomplete-epo-product', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-epo-product' . $ext . '.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
			}

			$dependencies[]        = 'themecomplete-tm-math';
			$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-math' . $ext . '.js';
			wp_register_script( 'themecomplete-tm-math', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-math' . $ext . '.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );

			$dependencies          = array_unique( $dependencies );
			$dependencies          = apply_filters( 'wc_epo_script_dependencies', $dependencies );
			$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-epo' . $ext . '.js';
			wp_register_script( 'themecomplete-epo', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-epo' . $ext . '.js', $dependencies, THEMECOMPLETE_EPO_VERSION, true );
			wp_enqueue_script( 'themecomplete-epo' );

		} else {
			$dependencies[] = 'jquery-ui-core';
			$dependencies[] = 'wc-add-to-cart-variation';

			$this->defered_files[] = THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/epo.min.js';
			wp_register_script( 'themecomplete-epo', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/epo.min.js', $dependencies, THEMECOMPLETE_EPO_VERSION, true );
			wp_enqueue_script( 'themecomplete-epo' );
		}

		// constants.
		$constants = THEMECOMPLETE_EPO()->tm_epo_math;

		$extra_fee = 0;
		global $wp_locale;
		$args = [
			'product_id'                                  => sprintf( '%d', themecomplete_get_id( $product ) ),
			'ajax_url'                                    => admin_url( 'admin-ajax' ) . '.php', // WPML 3.3.3 fix.
			'extraFee'                                    => apply_filters( 'woocommerce_tm_final_price_extra_fee', $extra_fee, $product ),
			'i18n_extra_fee'                              => esc_html__( 'Extra fee', 'woocommerce-tm-extra-product-options' ),
			'i18n_unit_price'                             => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_options_unit_price_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_options_unit_price_text ) : esc_html__( 'Unit price', 'woocommerce-tm-extra-product-options' ),
			'i18n_options_total'                          => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_options_total_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_options_total_text ) : esc_html__( 'Options amount', 'woocommerce-tm-extra-product-options' ),
			'i18n_vat_options_total'                      => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_vat_options_total_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_vat_options_total_text ) : esc_html__( 'Options VAT amount', 'woocommerce-tm-extra-product-options' ),
			'i18n_fees_total'                             => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_fees_total_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_fees_total_text ) : esc_html__( 'Fees amount', 'woocommerce-tm-extra-product-options' ),
			'i18n_final_total'                            => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_final_total_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_final_total_text ) : esc_html__( 'Final total', 'woocommerce-tm-extra-product-options' ),
			'i18n_prev_text'                              => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_slider_prev_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_slider_prev_text ) : esc_html__( 'Prev', 'woocommerce-tm-extra-product-options' ),
			'i18n_next_text'                              => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_slider_next_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_slider_next_text ) : esc_html__( 'Next', 'woocommerce-tm-extra-product-options' ),
			'i18n_cancel'                                 => esc_html__( 'Cancel', 'woocommerce-tm-extra-product-options' ),
			'i18n_close'                                  => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_close_button_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_close_button_text ) : esc_html__( 'Close', 'woocommerce-tm-extra-product-options' ),
			'i18n_addition_options'                       => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_additional_options_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_additional_options_text ) : esc_html__( 'Additional options', 'woocommerce-tm-extra-product-options' ),
			'i18n_characters_remaining'                   => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_characters_remaining_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_characters_remaining_text ) : esc_html__( 'characters remaining', 'woocommerce-tm-extra-product-options' ),

			'i18n_option_label'                           => esc_html__( 'Label', 'woocommerce-tm-extra-product-options' ),
			'i18n_option_value'                           => esc_html__( 'Value', 'woocommerce-tm-extra-product-options' ),
			'i18n_option_qty'                             => esc_html__( 'Qty', 'woocommerce-tm-extra-product-options' ),
			'i18n_option_price'                           => esc_html__( 'Price', 'woocommerce-tm-extra-product-options' ),

			'i18n_uploading_files'                        => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_uploading_files_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_uploading_files_text ) : esc_html__( 'Uploading files', 'woocommerce-tm-extra-product-options' ),
			'i18n_uploading_message'                      => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_uploading_message_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_uploading_message_text ) : esc_html__( 'Your files are being uploaded', 'woocommerce-tm-extra-product-options' ),

			'i18n_file'                                   => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_uploading_message_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_uploading_num_file ) : esc_html__( 'file', 'woocommerce-tm-extra-product-options' ),
			'i18n_files'                                  => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_uploading_message_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_uploading_num_files ) : esc_html__( 'files', 'woocommerce-tm-extra-product-options' ),

			'currency_format_num_decimals'                => apply_filters( 'wc_epo_price_decimals', esc_attr( wc_get_price_decimals() ) ),
			'currency_format_symbol'                      => esc_attr( get_woocommerce_currency_symbol() ),
			'currency_format_decimal_sep'                 => esc_attr( stripslashes_deep( get_option( 'woocommerce_price_decimal_sep' ) ) ),
			'currency_format_thousand_sep'                => esc_attr( stripslashes_deep( get_option( 'woocommerce_price_thousand_sep' ) ) ),
			'currency_format'                             => esc_attr( str_replace( [ '%1$s', '%2$s' ], [ '%s', '%v' ], get_woocommerce_price_format() ) ),
			'css_styles'                                  => THEMECOMPLETE_EPO()->tm_epo_css_styles,
			'css_styles_style'                            => THEMECOMPLETE_EPO()->tm_epo_css_styles_style,
			'tm_epo_options_placement'                    => THEMECOMPLETE_EPO()->tm_epo_options_placement,
			'tm_epo_totals_box_placement'                 => THEMECOMPLETE_EPO()->tm_epo_totals_box_placement,
			'tm_epo_no_lazy_load'                         => THEMECOMPLETE_EPO()->tm_epo_no_lazy_load,
			'tm_epo_preload_lightbox_image'               => THEMECOMPLETE_EPO()->tm_epo_preload_lightbox_image,
			'tm_epo_show_only_active_quantities'          => THEMECOMPLETE_EPO()->tm_epo_show_only_active_quantities,
			'tm_epo_hide_add_cart_button'                 => THEMECOMPLETE_EPO()->tm_epo_hide_add_cart_button,
			'tm_epo_hide_all_add_cart_button'             => THEMECOMPLETE_EPO()->tm_epo_hide_all_add_cart_button,
			'tm_epo_hide_required_add_cart_button'        => THEMECOMPLETE_EPO()->tm_epo_hide_required_add_cart_button,
			'tm_epo_auto_hide_price_if_zero'              => THEMECOMPLETE_EPO()->tm_epo_auto_hide_price_if_zero,
			'tm_epo_show_price_inside_option'             => THEMECOMPLETE_EPO()->tm_epo_show_price_inside_option,
			'tm_epo_show_price_inside_option_hidden_even' => THEMECOMPLETE_EPO()->tm_epo_show_price_inside_option_hidden_even,
			'tm_epo_multiply_price_inside_option'         => THEMECOMPLETE_EPO()->tm_epo_multiply_price_inside_option,
			'tm_epo_global_enable_validation'             => THEMECOMPLETE_EPO()->tm_epo_global_enable_validation,
			'tm_epo_global_input_decimal_separator'       => THEMECOMPLETE_EPO()->tm_epo_global_input_decimal_separator,
			'tm_epo_global_displayed_decimal_separator'   => THEMECOMPLETE_EPO()->tm_epo_global_displayed_decimal_separator,
			'tm_epo_remove_free_price_label'              => THEMECOMPLETE_EPO()->tm_epo_remove_free_price_label,
			'tm_epo_global_product_image_selector'        => THEMECOMPLETE_EPO()->tm_epo_global_product_image_selector,
			'tm_epo_upload_inline_image_preview'          => THEMECOMPLETE_EPO()->tm_epo_upload_inline_image_preview,
			'tm_epo_global_product_element_scroll_offset' => THEMECOMPLETE_EPO()->tm_epo_global_product_element_scroll_offset,
			'tm_epo_global_product_element_scroll'        => THEMECOMPLETE_EPO()->tm_epo_global_product_element_scroll,
			'tm_epo_global_product_image_mode'            => THEMECOMPLETE_EPO()->tm_epo_global_product_image_mode,
			'tm_epo_global_move_out_of_stock'             => THEMECOMPLETE_EPO()->tm_epo_global_move_out_of_stock,
			'tm_epo_progressive_display'                  => THEMECOMPLETE_EPO()->tm_epo_progressive_display,
			'tm_epo_animation_delay'                      => THEMECOMPLETE_EPO()->tm_epo_animation_delay,
			'tm_epo_start_animation_delay'                => THEMECOMPLETE_EPO()->tm_epo_start_animation_delay,
			'tm_epo_global_error_label_placement'         => THEMECOMPLETE_EPO()->tm_epo_global_error_label_placement,
			'tm_epo_global_tooltip_max_width'             => THEMECOMPLETE_EPO()->tm_epo_global_tooltip_max_width,
			'tm_epo_global_product_element_quantity_sync' => THEMECOMPLETE_EPO()->tm_epo_global_product_element_quantity_sync,

			'tm_epo_global_validator_messages'            => [
				'required'                 => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_this_field_is_required_text ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_this_field_is_required_text ) : esc_html__( 'This field is required.', 'woocommerce-tm-extra-product-options' ),
				'email'                    => esc_html__( 'Please enter a valid email address.', 'woocommerce-tm-extra-product-options' ),
				'url'                      => esc_html__( 'Please enter a valid URL.', 'woocommerce-tm-extra-product-options' ),
				'number'                   => esc_html__( 'Please enter a valid number.', 'woocommerce-tm-extra-product-options' ),
				'digits'                   => esc_html__( 'Please enter only digits.', 'woocommerce-tm-extra-product-options' ),
				'max'                      => esc_html__( 'Please enter a value less than or equal to {0}.', 'woocommerce-tm-extra-product-options' ),
				'min'                      => esc_html__( 'Please enter a value greater than or equal to {0}.', 'woocommerce-tm-extra-product-options' ),
				'maxlengthsingle'          => esc_html__( 'Please enter no more than {0} character.', 'woocommerce-tm-extra-product-options' ),
				'maxlength'                => esc_html__( 'Please enter no more than {0} characters.', 'woocommerce-tm-extra-product-options' ),
				'minlengthsingle'          => esc_html__( 'Please enter at least {0} character.', 'woocommerce-tm-extra-product-options' ),
				'minlength'                => esc_html__( 'Please enter at least {0} characters.', 'woocommerce-tm-extra-product-options' ),
				'epolimitsingle'           => esc_html__( 'Please select up to {0} choice.', 'woocommerce-tm-extra-product-options' ),
				'epolimit'                 => esc_html__( 'Please select up to {0} choices.', 'woocommerce-tm-extra-product-options' ),
				'epoexactsingle'           => esc_html__( 'Please select exactly {0} choice.', 'woocommerce-tm-extra-product-options' ),
				'epoexact'                 => esc_html__( 'Please select exactly {0} choices.', 'woocommerce-tm-extra-product-options' ),
				'epominsingle'             => esc_html__( 'Please select at least {0} choice.', 'woocommerce-tm-extra-product-options' ),
				'epomin'                   => esc_html__( 'Please select at least {0} choices.', 'woocommerce-tm-extra-product-options' ),
				'step'                     => esc_html__( 'Please enter a multiple of {0}.', 'woocommerce-tm-extra-product-options' ),
				'lettersonly'              => esc_html__( 'Please enter only letters.', 'woocommerce-tm-extra-product-options' ),
				'lettersspaceonly'         => esc_html__( 'Please enter only letters or spaces.', 'woocommerce-tm-extra-product-options' ),
				'alphanumeric'             => esc_html__( 'Please enter only letters, numbers or underscores.', 'woocommerce-tm-extra-product-options' ),
				'alphanumericunicode'      => esc_html__( 'Please enter only unicode letters and numbers.', 'woocommerce-tm-extra-product-options' ),
				'alphanumericunicodespace' => esc_html__( 'Please enter only unicode letters, numbers or spaces.', 'woocommerce-tm-extra-product-options' ),
				'repeaterminrows'          => esc_html__( 'Minimum number of rows is {0}', 'woocommerce-tm-extra-product-options' ),
				'repeatermaxrows'          => esc_html__( 'Maximum number of rows is {0}', 'woocommerce-tm-extra-product-options' ),
			],

			'first_day'                                   => (int) get_option( 'start_of_week' ),
			'monthNames'                                  => $this->strip_array_indices( $wp_locale->month ),
			'monthNamesShort'                             => $this->strip_array_indices( $wp_locale->month_abbrev ),
			'dayNames'                                    => $this->strip_array_indices( $wp_locale->weekday ),
			'dayNamesShort'                               => $this->strip_array_indices( $wp_locale->weekday_abbrev ),
			'dayNamesMin'                                 => $this->strip_array_indices( $wp_locale->weekday_initial ),
			'isRTL'                                       => 'rtl' === $wp_locale->text_direction,
			'text_direction'                              => $wp_locale->text_direction,
			'is_rtl'                                      => is_rtl(),
			'closeText'                                   => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_closetext ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_closetext ) : esc_html__( 'Done', 'woocommerce-tm-extra-product-options' ),
			'currentText'                                 => ( ! empty( THEMECOMPLETE_EPO()->tm_epo_currenttext ) ) ? esc_html( THEMECOMPLETE_EPO()->tm_epo_currenttext ) : esc_html__( 'Today', 'woocommerce-tm-extra-product-options' ),

			'hourText'                                    => esc_html__( 'Hour', 'woocommerce-tm-extra-product-options' ),
			'minuteText'                                  => esc_html__( 'Minute', 'woocommerce-tm-extra-product-options' ),
			'secondText'                                  => esc_html__( 'Second', 'woocommerce-tm-extra-product-options' ),

			'floating_totals_box'                         => THEMECOMPLETE_EPO()->tm_epo_floating_totals_box,
			'floating_totals_box_visibility'              => THEMECOMPLETE_EPO()->tm_epo_floating_totals_box_visibility,
			'floating_totals_box_add_button'              => THEMECOMPLETE_EPO()->tm_epo_floating_totals_box_add_button,
			'floating_totals_box_pixels'                  => THEMECOMPLETE_EPO()->tm_epo_floating_totals_box_pixels,
			'floating_totals_box_html_before'             => apply_filters( 'floating_totals_box_html_before', '' ),
			'floating_totals_box_html_after'              => apply_filters( 'floating_totals_box_html_after', '' ),
			'tm_epo_show_unit_price'                      => THEMECOMPLETE_EPO()->tm_epo_show_unit_price,
			'tm_epo_fees_on_unit_price'                   => THEMECOMPLETE_EPO()->tm_epo_fees_on_unit_price,
			'tm_epo_total_price_as_unit_price'            => THEMECOMPLETE_EPO()->tm_epo_total_price_as_unit_price,
			'tm_epo_enable_final_total_box_all'           => THEMECOMPLETE_EPO()->tm_epo_enable_final_total_box_all,
			'tm_epo_enable_original_final_total'          => THEMECOMPLETE_EPO()->tm_epo_enable_original_final_total,
			'tm_epo_enable_vat_options_total'             => THEMECOMPLETE_EPO()->tm_epo_enable_vat_options_total,
			'tm_epo_change_original_price'                => THEMECOMPLETE_EPO()->tm_epo_change_original_price,
			'tm_epo_change_variation_price'               => THEMECOMPLETE_EPO()->tm_epo_change_variation_price,
			'tm_epo_enable_in_shop'                       => THEMECOMPLETE_EPO()->tm_epo_enable_in_shop,
			'tm_epo_disable_error_scroll'                 => THEMECOMPLETE_EPO()->tm_epo_disable_error_scroll,
			'tm_epo_global_options_price_sign'            => THEMECOMPLETE_EPO()->tm_epo_global_options_price_sign,
			'tm_epo_trim_zeros'                           => THEMECOMPLETE_EPO()->tm_epo_trim_zeros,
			'tm_epo_math'                                 => $constants,

			'minus_sign'                                  => apply_filters( 'wc_epo_get_price_for_cart_minus_sign', "<span class='tc-minus-sign'>-</span>" ),
			'plus_sign'                                   => apply_filters( 'wc_epo_get_price_for_cart_plus_sign', "<span class='tc-minus-sign'>+</span>" ),

			'option_plus_sign'                            => THEMECOMPLETE_EPO()->tm_epo_global_options_price_sign === '' ? apply_filters( 'wc_epo_price_in_dropdown_plus_sign', '+' ) : '',
			'option_minus_sign'                           => apply_filters( 'wc_epo_price_in_dropdown_minus_sign', '-' ),

			'tm_epo_upload_popup'                         => THEMECOMPLETE_EPO()->tm_epo_upload_popup,

			'current_free_text'                           => esc_html( THEMECOMPLETE_EPO()->current_free_text ),
			'assoc_current_free_text'                     => esc_html( THEMECOMPLETE_EPO()->assoc_current_free_text ),

			'cart_total'                                  => ( function_exists( 'WC' ) && WC()->cart ) ? floatval( WC()->cart->get_cart_contents_total() ) : 0,

			'quickview_container'                         => esc_html( wp_json_encode( apply_filters( 'wc_epo_js_quickview_container', [] ) ) ),
			'quickview_array'                             => esc_html( wp_json_encode( apply_filters( 'wc_epo_get_quickview_containers', [] ) ) ),
			'tax_display_mode'                            => get_option( 'woocommerce_tax_display_shop' ),
			'prices_include_tax'                          => wc_prices_include_tax(),

			'lookupTables'                                => wp_json_encode( THEMECOMPLETE_EPO()->lookup_tables ),
			'WP_DEBUG'                                    => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'theme_name'                                  => THEMECOMPLETE_EPO()->get_theme( 'Name' ),
		];

		$args = apply_filters( 'wc_epo_script_args', $args, $this );

		wp_localize_script( 'themecomplete-epo', 'TMEPOJS', $args );

	}

}
