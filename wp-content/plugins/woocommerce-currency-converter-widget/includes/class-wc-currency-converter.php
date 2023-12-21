<?php
/**
 * Currency Converter Main Class
 *
 * @package woocommerce-currency-converter-widget
 */

use KoiLab\WC_Currency_Converter\Plugin;
use KoiLab\WC_Currency_Converter\Exchange\Rates;
use KoiLab\WC_Currency_Converter\Utilities\Currency_Utils;
use KoiLab\WC_Currency_Converter\Utilities\L10n_Utils;

/**
 * Currency Converter Main Class
 */
class WC_Currency_Converter extends Plugin {

	/**
	 * Current Currency
	 *
	 * @var string
	 */
	public $currency;

	/**
	 * Widget object
	 *
	 * @var \KoiLab\WC_Currency_Converter\Widget
	 */
	private $widget;

	/**
	 * Currencies needed in the current page.
	 *
	 * @var array
	 */
	private $currencies_in_page = array();

	/**
	 * Constructor.
	 */
	protected function __construct() {
		parent::__construct();

		add_action( 'widgets_init', array( $this, 'widgets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'woocommerce_currency_converter', array( $this, 'get_converter_form' ), 10, 2 );
		add_shortcode( 'woocommerce_currency_converter', array( $this, 'shortcode' ) );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $key Key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'base':
				_doing_it_wrong( 'WC_Currency_Converter->base', 'This property is deprecated and will be removed in future versions.', '2.0.0' );
				return 'USD';
			case 'rates':
				_doing_it_wrong( 'WC_Currency_Converter->rates', 'This property is deprecated and will be removed in future versions.', '2.0.0' );
				return ( new Rates() )->get_all();
		}
	}

	/**
	 * Looks at how a currency should be formatted and returns the currency's correct position for the symbol
	 *
	 * @param string $currency Currency characters.
	 */
	public function get_symbol_position( $currency ) {
		if ( '* ' === substr( $currency, 0, 2 ) ) {
			return 'left_space';
		} elseif ( '*' === substr( $currency, 0, 1 ) ) {
			return 'left';
		} elseif ( ' *' === substr( $currency, -2 ) ) {
			return 'right_space';
		} elseif ( '*' === substr( $currency, -1 ) ) {
			return 'right';
		} else {
			return get_option( 'woocommerce_currency_pos' );
		}
	}

	/**
	 * Extract an array of currencies from a widget instance
	 *
	 * @param array $instance Arguments.
	 *
	 * @return array of currencies
	 */
	public function get_currencies_from_instance( $instance ) {
		$currencies = array();
		if ( ! empty( $instance['currency_codes'] ) ) {
			// Split on a comma if there is one. Else, use a new line split.
			if ( stristr( $instance['currency_codes'], ',' ) ) {
				$currencies = array_map( 'trim', array_filter( explode( ',', $instance['currency_codes'] ) ) );
			} else {
				$currencies = array_map( 'trim', array_filter( explode( "\n", $instance['currency_codes'] ) ) );
			}
		}

		if ( empty( $currencies ) ) {
			$currencies = array( 'USD', 'EUR' );
		} else {
			$default_currency     = get_woocommerce_currency();
			$has_default_currency = false;

			foreach ( $currencies as $currency ) {
				if ( false !== strpos( $currency, $default_currency ) ) {
					array_unshift( $currencies, $currency );
					$has_default_currency = true;
					break;
				}
			}

			if ( ! $has_default_currency ) {
				array_unshift( $currencies, $default_currency );
			}

			$currencies = array_values( array_unique( $currencies ) );
		}

		return $currencies;
	}

	/**
	 * Display the currency converter form.
	 *
	 * @since  1.4.0
	 *
	 * @param array $instance The widget instance.
	 * @param bool  $echo     Whether to display or return the output.
	 * @return string
	 */
	public function get_converter_form( $instance, $echo = true ) {
		$currencies       = $this->get_currencies_from_instance( $instance );
		$symbol_positions = array();

		// Figure out where the currency symbols should be displayed.
		foreach ( $currencies as $key => $currency ) {
			$display_currency                      = trim( str_replace( '*', '', $currency ) );
			$symbol_positions[ $display_currency ] = $this->get_symbol_position( $currency );
			if ( strpos( $currency, '*' ) !== false ) {
				$currencies[ $key ] = $display_currency;
			}
		}

		// Mark currencies in instance as required for this page.
		$this->currencies_in_page = array_merge( $this->currencies_in_page, $currencies );

		// Is location based currency enabled or disabled?
		$disable_location = isset( $instance['disable_location'] ) ? $instance['disable_location'] : false;
		$disable_location = apply_filters( 'woocommerce_disable_location_based_currency', $disable_location );

		// Assume default currency from WooCommerce.
		$current_currency = get_woocommerce_currency();

		if ( ! empty( $_COOKIE['woocommerce_current_currency'] ) ) {
			// If a cookie is set then use that.
			$current_currency = sanitize_text_field( wp_unslash( $_COOKIE['woocommerce_current_currency'] ) );
		} elseif ( ! $disable_location ) {
			// If location detection is enabled, get the users local currency based on their location.
			$users_default_currency = self::get_users_default_currency();
			// If it's an allowed currency, then use it.
			if ( isset( $users_default_currency ) && is_array( $currencies ) && in_array( $users_default_currency, $currencies ) ) {
				$current_currency = $users_default_currency;
			}
		}

		// Scripts are registered later in block themes.
		if ( ! wp_script_is( 'wc_currency_converter_inline' ) ) {
			$this->enqueue_assets();
		}

		wp_enqueue_script( 'wc_currency_converter' );
		wp_enqueue_script( 'wc_currency_converter_inline' );

		$wc_currency_converter_inline_params = array(
			'current_currency' => esc_js( $current_currency ),
			'symbol_positions' => $symbol_positions,
		);

		wp_localize_script(
			'wc_currency_converter_inline',
			'wc_currency_converter_inline_params',
			apply_filters( 'wc_currency_converter_inline_params', $wc_currency_converter_inline_params )
		);

		$params = array(
			'currencies' => $currencies,
			'instance'   => $instance,
		);

		ob_start();
		wc_get_template( 'content-widget-currency-converter.php', $params, '', WC_CURRENCY_CONVERTER_PATH . 'templates/' );
		$html = ob_get_clean();

		if ( $echo ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $html;
	}

	/**
	 * Shortcode wrapper.
	 *
	 * @since  1.4.0
	 *
	 * @param array  $atts    Arguments.
	 * @param string $content The contents, if this is a wrapping shortcode.
	 * @return string
	 */
	public function shortcode( $atts, $content = null ) {
		$settings = shortcode_atts(
			array(
				'currency_codes'   => '',
				'message'          => '',
				'show_symbols'     => '0',
				'show_reset'       => '0',
				'currency_display' => '',
				'disable_location' => '0',
			),
			$atts
		);

		return $this->get_converter_form( $settings, false );
	}

	/**
	 * Init Widgets
	 */
	public function widgets() {
		$this->widget = new \KoiLab\WC_Currency_Converter\Widget();

		register_widget( $this->widget );
	}

	/**
	 * Enqueue Styles and scripts
	 */
	public function enqueue_assets() {
		if ( ! $this->widget ) {
			return;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Styles.
		wp_enqueue_style( 'currency_converter_styles', plugins_url( '/assets/css/converter.css', __DIR__ ), array(), WC_CURRENCY_CONVERTER_VERSION );

		// Scripts.
		wp_register_script( 'moneyjs', plugins_url( '/assets/js/money' . $suffix . '.js', __DIR__ ), array(), '0.2.0', true );
		wp_enqueue_script( 'jquery-cookie' );
		wp_register_script( 'wc_currency_converter_inline', plugins_url( '/assets/js/conversion_inline' . $suffix . '.js', __DIR__ ), array( 'jquery' ), WC_CURRENCY_CONVERTER_VERSION, true );

		wp_register_script(
			'wc_currency_converter',
			plugins_url( '/assets/js/conversion' . $suffix . '.js', __DIR__ ),
			array(
				'jquery',
				'moneyjs',
				'accounting',
				'jquery-cookie',
				'wc_currency_converter_inline',
			),
			WC_CURRENCY_CONVERTER_VERSION,
			true
		);

		if ( ! has_action( 'wp_print_footer_scripts', array( $this, 'localize_script' ) ) ) {
			add_action( 'wp_print_footer_scripts', array( $this, 'localize_script' ), 5 );
		}
	}

	/**
	 * Creates the `wc_currency_converter_params` object on the JS side for the converter forms to work.
	 *
	 * @since 1.6.27
	 */
	public function localize_script() {
		$currencies = array_unique(
			array_merge(
				$this->get_currencies_in_widget(),
				$this->currencies_in_page
			)
		);

		$rates = new Rates();

		$wc_currency_converter_params = array(
			'current_currency'       => isset( $_COOKIE['woocommerce_current_currency'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['woocommerce_current_currency'] ) ) : '',
			'currencies'             => wp_json_encode( $this->get_symbols( $currencies ) ),
			'rates'                  => $rates->get_rates( $currencies ),
			'base'                   => $rates->get_base(),
			'currency_format_symbol' => get_woocommerce_currency_symbol(),
			'currency'               => get_woocommerce_currency(),
			'currency_pos'           => get_option( 'woocommerce_currency_pos' ),
			'num_decimals'           => wc_get_price_decimals(),
			'trim_zeros'             => intval( get_option( 'woocommerce_price_num_decimals' ) ) === 0,
			'thousand_sep'           => esc_attr( wc_get_price_thousand_separator() ),
			'decimal_sep'            => esc_attr( wc_get_price_decimal_separator() ),
			'i18n_oprice'            => __( 'Original price:', 'woocommerce-currency-converter-widget' ),
			'zero_replace'           => $this->get_zero_replace(),
			'currency_rate_default'  => apply_filters( 'wc_currency_converter_default_rate', 1 ),
			'locale_info'            => $this->get_locale_info( $currencies ),
		);

		wp_localize_script( 'wc_currency_converter', 'wc_currency_converter_params', apply_filters( 'wc_currency_converter_params', $wc_currency_converter_params ) );
	}

	/**
	 * Returns an array of symbols for the given currencies
	 *
	 * @param $currencies
	 *
	 * @return array of symbols
	 */
	private function get_symbols( $currencies ) {
		$symbols = array();
		foreach ( $currencies as $code ) {
			$symbols[ $code ] = get_woocommerce_currency_symbol( $code );
		}

		return $symbols;
	}

	/**
	 * Returns an array of currencies that are being used by the widget
	 *
	 * @return array of currencies being used by the widget
	 */
	private function get_currencies_in_widget() {
		$instances = $this->widget->get_settings();

		$currencies = array();
		foreach ( $instances as $instance ) {
			foreach ( $this->get_currencies_from_instance( $instance ) as $currency ) {
				$currencies[] = trim( str_replace( '*', '', $currency ) ); // Remove possible symbol placement indicator.
			}
		}

		return array_unique( $currencies );
	}

	/**
	 * Return a string with decimal separator and 0s for how many decimal places should be used
	 *
	 * @return string
	 */
	private function get_zero_replace() {
		$zero_replace = get_option( 'woocommerce_price_decimal_sep', '.' );
		$decimals     = absint( get_option( 'woocommerce_price_num_decimals' ) );

		for ( $i = 0; $i < $decimals; $i++ ) {
			$zero_replace .= '0';
		}

		return $zero_replace;
	}

	/**
	 * Return an array of local_info for the given currencies
	 *
	 * @param $currencies
	 *
	 * @return array of local_info
	 */
	private function get_locale_info( $currencies ) {
		$locale_info = L10n_Utils::get_locales();

		$locale_info = array_filter(
			$locale_info,
			function ( $element ) use ( $currencies ) {
				return in_array( $element['currency_code'], $currencies );
			}
		);

		return array_map(
			function ( $element ) {
				return array(
					'currency_code' => $element['currency_code'],
					'thousand_sep'  => $element['thousand_sep'],
					'decimal_sep'   => $element['decimal_sep'],
				);
			},
			$locale_info
		);
	}

	/**
	 * Function to return the users default currency code.
	 *
	 * @since  1.4.1
	 *
	 * @return string
	 */
	public static function get_users_default_currency() {
		$location = WC_Geolocation::geolocate_ip();

		if ( isset( $location['country'] ) ) {
			return Currency_Utils::get_by_country( $location['country'] );
		}

		return false;
	}
}
