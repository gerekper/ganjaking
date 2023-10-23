<?php
/**
 * Currency Converter Widget.
 *
 * @since 1.9.0
 */

namespace KoiLab\WC_Currency_Converter;

defined( 'ABSPATH' ) || exit;

/**
 * Currency Converter Widget class.
 */
class Widget extends \WC_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.9.0
	 */
	public function __construct() {
		$this->widget_id          = 'woocommerce_currency_converter';
		$this->widget_name        = __( 'WooCommerce Currency Converter', 'woocommerce-currency-converter-widget' );
		$this->widget_cssclass    = 'widget_currency_converter';
		$this->widget_description = __( 'Allow users to choose a currency for prices to be displayed in.', 'woocommerce-currency-converter-widget' );
		$this->settings           = array(
			'title'            => array(
				'type'  => 'text',
				'label' => __( 'Title:', 'woocommerce-currency-converter-widget' ),
				'std'   => __( 'Currency converter', 'woocommerce-currency-converter-widget' ),
			),
			'currency_codes'   => array(
				'type'  => 'textarea',
				'label' => __( 'Currency codes:', 'woocommerce-currency-converter-widget' ),
				'std'   => __( "USD\nEUR", 'woocommerce-currency-converter-widget' ),
				'desc'  => __( "Use * to control how the amounts and currency symbols are displayed. Example: SEK* becomes 999kr. USD * becomes 999 $. If you omit * and just provide the currency (USD, EUR), WooCommerce's default currency position will be used.", 'woocommerce-currency-converter-widget' ),
			),
			'currency_display' => array(
				'type'    => 'select',
				'label'   => __( 'Currency Display Mode:', 'woocommerce-currency-converter-widget' ),
				'std'     => '',
				'options' => array(
					''       => __( 'Buttons', 'woocommerce-currency-converter-widget' ),
					'select' => __( 'Select Box', 'woocommerce-currency-converter-widget' ),
				),
			),
			'message'          => array(
				'type'  => 'textarea',
				'label' => __( 'Widget message:', 'woocommerce-currency-converter-widget' ),
				'std'   => __( 'Currency conversions are estimated and should be used for informational purposes only.', 'woocommerce-currency-converter-widget' ),
			),
			'show_symbols'     => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show currency symbols', 'woocommerce-currency-converter-widget' ),
			),
			'show_reset'       => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show reset link', 'woocommerce-currency-converter-widget' ),
			),
			'disable_location' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( "Disable location detection and default to the store's currency.", 'woocommerce-currency-converter-widget' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output the widget content
	 *
	 * @since 1.9.0
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		$this->maybe_set_cookie( $instance );
		$this->widget_start( $args, $instance );

		/**
		 * Fires just after displaying the widget title.
		 *
		 * @since 1.4.0
		 *
		 * @param array $instance Widget instance.
		 */
		do_action( 'woocommerce_currency_converter', $instance, true );

		$this->widget_end( $args );
	}

	/**
	 * Sets the current currency into a cookie.
	 *
	 * @since 1.6.4
	 *
	 * @param array $instance Widget instance.
	 */
	public function maybe_set_cookie( $instance ) {
		$current_currency = $this->get_current_currency( $instance );

		// Save the currency in the cookie.
		if ( empty( $_COOKIE['woocommerce_current_currency'] ) || ( $_COOKIE['woocommerce_current_currency'] !== $current_currency ) ) {
			?>
			<script type="text/javascript">
				let set_initial_currency = JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( $current_currency ) ); ?>' ) );
			</script>
			<?php
		}
	}

	/**
	 * Gets the current currency to set the cookie.
	 *
	 * @since 1.9.0
	 *
	 * @param array $instance Widget instance.
	 * @return string
	 */
	protected function get_current_currency( array $instance ) {
		// If a cookie is set then use that.
		if ( ! empty( $_COOKIE['woocommerce_current_currency'] ) ) {
			return wc_clean( wp_unslash( $_COOKIE['woocommerce_current_currency'] ) );
		}

		// Assume default currency from WooCommerce.
		$current_currency = get_woocommerce_currency();
		$disable_location = ( isset( $instance['disable_location'] ) && wc_string_to_bool( $instance['disable_location'] ) );

		/**
		 * Filter the 'disable_location' settings value.
		 *
		 * @since 1.6.0
		 *
		 * @param bool $disable_location The 'disable_location' settings value.
		 */
		$disable_location = apply_filters( 'woocommerce_disable_location_based_currency', $disable_location );

		// Get the currency based on the customer's location.
		if ( ! $disable_location ) {
			$local_currency = \WC_Currency_Converter::get_users_default_currency();
			$currencies     = ( isset( $instance['currency_codes'] ) ? explode( "\n", $instance['currency_codes'] ) : array() );

			// If it's an allowed currency, then use it.
			if ( $local_currency && is_array( $currencies ) && in_array( $local_currency, $currencies, true ) ) {
				$current_currency = sanitize_text_field( $local_currency );
			}
		}

		return $current_currency;
	}
}

class_alias( Widget::class, 'Themesquad\WC_Currency_Converter\Widget' );
