<?php
/**
 * Currency Converter Main Class
 *
 * @package woocommerce-currency-converter-widget
 */

use Themesquad\WC_Currency_Converter\Plugin;
use Themesquad\WC_Currency_Converter\Utilities\Currency_Utils;
use Themesquad\WC_Currency_Converter\Utilities\L10n_Utils;

/**
 * Currency Converter Main Class
 */
class WC_Currency_Converter extends Plugin {

	/**
	 * Base Currency
	 *
	 * @var string
	 */
	public $base;

	/**
	 * Current Currency
	 *
	 * @var string
	 */
	public $currency;

	/**
	 * Rates
	 *
	 * @var array
	 */
	public $rates;

	/**
	 * Widget object
	 *
	 * @var \Themesquad\WC_Currency_Converter\Widget
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

		$rates = get_transient( 'woocommerce_currency_converter_rates' );

		if ( false === $rates ) {
			$app_id      = get_option( 'wc_currency_converter_app_id' );
			$app_id      = $app_id ? $app_id : 'e65018798d4a4585a8e2c41359cc7f3c';
			$rates       = wp_remote_retrieve_body( wp_safe_remote_get( 'http://openexchangerates.org/api/latest.json?app_id=' . $app_id ) );
			$check_rates = json_decode( $rates );

			// Check for error.
			if ( is_wp_error( $rates ) || ! empty( $check_rates->error ) || empty( $rates ) ) {

				if ( 401 === $check_rates->status ) {
					add_action( 'admin_notices', array( $this, 'admin_notice_wrong_key' ) );
				}
			} else {
				set_transient( 'woocommerce_currency_converter_rates', $rates, HOUR_IN_SECONDS * 12 );
			}
		}

		$rates = json_decode( $rates );

		if ( $rates && ! empty( $rates->base ) && ! empty( $rates->rates ) ) {
			$this->base  = $rates->base;
			$this->rates = $rates->rates;

			// Actions.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'widgets_init', array( $this, 'widgets' ) );

			// Shortcode.
			add_shortcode( 'woocommerce_currency_converter', array( $this, 'shortcode' ) );
			add_action( 'woocommerce_currency_converter', array( $this, 'get_converter_form' ), 10, 2 );
		}

		add_action( 'init', array( $this, 'includes' ) );
	}

	/**
	 * Files to be included.
	 */
	public function includes() {
		include_once __DIR__ . '/woocommerce-currency-converter-privacy.php';
	}

	/**
	 * Display admin notices
	 */
	public function admin_notice_wrong_key() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'WooCommerce Currency Converter: Incorrect key entered!', 'woocommerce-currency-converter-widget' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Show Admin Settings.
	 *
	 * @deprecated 1.8.0
	 */
	public function admin_settings() {
		wc_deprecated_function( __FUNCTION__, '1.8.0' );
	}

	/**
	 * Save Admin Settings.
	 *
	 * @deprecated 1.8.0
	 */
	public function save_admin_settings() {
		wc_deprecated_function( __FUNCTION__, '1.8.0' );
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
	 * @param array $instance Arguments.
	 * @param bool  $echo     Whether to display or return the output.
	 *
	 * @return string
	 */
	public function get_converter_form( $instance, $echo = true ) {
		wp_enqueue_script( 'wc_currency_converter' );
		wp_enqueue_script( 'wc_currency_converter_inline' );

		$html  = '<form method="post" id="currency_converter" action="">' . "\n";
		$html .= '<div>' . "\n";

		if ( ! empty( $instance['message'] ) ) {
			$html .= wpautop( wp_kses_post( $instance['message'] ) );
		}

		$currencies = $this->get_currencies_from_instance( $instance );

		// Figure out where the currency symbols should be displayed.
		$symbol_positions = array();
		foreach ( $currencies as $key => $currency ) {
			$display_currency                      = trim( str_replace( '*', '', $currency ) );
			$symbol_positions[ $display_currency ] = $this->get_symbol_position( $currency );
			if ( strpos( $currency, '*' ) !== false ) {
				$currencies[ $key ] = $display_currency;
			}
		}

		if ( $currencies ) {
			// Mark currencies in instance as required for this page.
			$this->currencies_in_page = array_merge( $this->currencies_in_page, $currencies );

			if ( ! empty( $instance['currency_display'] ) && 'select' === $instance['currency_display'] ) {
				$html .= '<label for="currency_switcher" class="currency_switcher_label">' . esc_html__( 'Choose a Currency', 'woocommerce-currency-converter-widget' ) . '</label>';
				$html .= '<select id="currency_switcher" class="currency_switcher select" data-default="' . get_woocommerce_currency() . '">';

				foreach ( $currencies as $currency ) {
					$label = empty( $instance['show_symbols'] ) ? $currency : $currency . ' (' . get_woocommerce_currency_symbol( $currency ) . ')';
					$html .= '<option value="' . esc_attr( $currency ) . '">' . esc_html( $label ) . '</option>';
				}

				$html .= '</select>';

				if ( ! empty( $instance['show_reset'] ) ) {
					$html .= ' <a href="#" class="wc-currency-converter-reset reset">' . __( 'Reset', 'woocommerce-currency-converter-widget' ) . '</a>';
				}

			} else {
				$html .= '<ul class="currency_switcher">';

				foreach ( $currencies as $currency ) {
					$class = get_woocommerce_currency() === $currency ? 'default currency-' . $currency : 'currency-' . $currency;
					$label = empty( $instance['show_symbols'] ) ? $currency : $currency . ' (' . get_woocommerce_currency_symbol( $currency ) . ')';

					$html .= '<li><a href="#" class="' . esc_attr( $class ) . '" data-currencycode="' . esc_attr( $currency ) . '">' . esc_html( $label ) . '</a></li>';
				}

				if ( ! empty( $instance['show_reset'] ) ) {
					$html .= '<li><a href="#" class="wc-currency-converter-reset reset">' . __( 'Reset', 'woocommerce-currency-converter-widget' ) . '</a></li>';
				}

				$html .= '</ul>';
			}
		}

		$html .= '</div>' . "\n";
		$html .= '</form>' . "\n";

		// The current currency to use.
		$current_currency = null;

		// Is location based currency enabled or disabled?
		$disable_location_based_currency = isset( $instance['disable_location'] ) ? $instance['disable_location'] : false;
		$disable_location_based_currency = apply_filters( 'woocommerce_disable_location_based_currency', $disable_location_based_currency );

		// Assume default currency from WooCommerce.
		$current_currency = get_woocommerce_currency();

		if ( ! empty( $_COOKIE['woocommerce_current_currency'] ) ) {
			// If a cookie is set then use that.
			$current_currency = $_COOKIE['woocommerce_current_currency'];
		} elseif ( ! $disable_location_based_currency ) {
			// If location detection is enabled, get the users local currency based on their location.
			$users_default_currency = self::get_users_default_currency();
			// If its an allowed currency, then use it.
			if ( isset( $users_default_currency ) && is_array( $currencies ) && in_array( $users_default_currency, $currencies ) ) {
				$current_currency = $users_default_currency;
			}
		}

		$wc_currency_converter_inline_params = array(
			'current_currency' => esc_js( $current_currency ),
			'symbol_positions' => $symbol_positions,
		);

		wp_localize_script(
			'wc_currency_converter_inline',
			'wc_currency_converter_inline_params',
			apply_filters( 'wc_currency_converter_inline_params', $wc_currency_converter_inline_params )
		);

		if ( $echo ) {
			echo $html;
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
	 *
	 * @return string
	 */
	public function shortcode( $atts, $content = null ) {
		$defaults = array( 'currency_codes' => '', 'message' => '', 'show_symbols' => '0', 'show_reset' => '0', 'currency_display' => '', 'disable_location' => '0' );
		$settings = shortcode_atts( $defaults, $atts );

		return $this->get_converter_form( $settings, false );
	}

	/**
	 * Init Widgets
	 */
	public function widgets() {
		include_once __DIR__ . '/currency-converter-widget.php'; // Deprecated.

		$this->widget = new \Themesquad\WC_Currency_Converter\Widget();

		register_widget( $this->widget );
	}

	/**
	 * Enqueue Styles and scripts
	 */
	public function enqueue_assets() {
		if ( is_admin() ) {
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

		$wc_currency_converter_params = array(
			'current_currency'       => isset( $_COOKIE['woocommerce_current_currency'] ) ? $_COOKIE['woocommerce_current_currency'] : '',
			'currencies'             => wp_json_encode( $this->get_symbols( $currencies ) ),
			'rates'                  => $this->get_currencies_rates( $currencies ),
			'base'                   => $this->base,
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
	 * Returns an array of rates for the given currencies
	 *
	 * @param $currencies
	 *
	 * @return array of rates
	 */
	private function get_currencies_rates( $currencies ) {
		$rates = array();

		foreach ( $currencies as $currency ) {
			$rates[ $currency ] = ( (array) $this->rates )[ $currency ];
		}

		return $rates;
	}

	/**
	 * Return a string with decimal separator and 0s for how many decimal places should be used
	 *
	 * @return string
	 */
	private function get_zero_replace() {
		$zero_replace = get_option( 'woocommerce_price_decimal_sep', '.' );
		$decimals     = absint( get_option( 'woocommerce_price_num_decimals' ) );

		for ( $i = 0; $i < $decimals; $i ++ ) {
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

		$locale_info = array_filter( $locale_info, function ( $element ) use ( $currencies ) {
			return in_array( $element['currency_code'], $currencies );
		} );

		return array_map( function ( $element ) {
			return [
				'currency_code' => $element['currency_code'],
				'thousand_sep'  => $element['thousand_sep'],
				'decimal_sep'   => $element['decimal_sep']
			];
		}, $locale_info );
	}

	/**
	 * Update order meta with currency information
	 *
	 * @deprecated 1.8.0
	 *
	 * @param int $order_id Order ID.
	 */
	public function update_order_meta( $order_id ) {
		wc_deprecated_function( __FUNCTION__, '1.8.0', '\Themesquad\WC_Currency_Converter\Checkout::init()' );
	}

	/**
	 * Function to return a the users default currency code
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

	/**
	 * Function to return a currency code based on a country code
	 *
	 * @since 1.4.1
	 * @deprecated 1.9.0
	 *
	 * @param string $country_code Country code.
	 * @return string|false
	 */
	public static function get_currency_from_country_code( $country_code ) {
		wc_deprecated_function( __FUNCTION__, '1.9.0', '\Themesquad\WC_Currency_Converter\Utilities\Currency_Utils::get_by_country()' );

		return Currency_Utils::get_by_country( $country_code );
	}
}
