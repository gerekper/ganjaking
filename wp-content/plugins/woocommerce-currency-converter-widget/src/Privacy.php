<?php
/**
 * Handles the plugin privacy policy.
 *
 * @since 2.1.0
 */

namespace KoiLab\WC_Currency_Converter;

defined( 'ABSPATH' ) || exit;

use KoiLab\WC_Currency_Converter\Utilities\Exchange_Utils;

/**
 * Privacy class.
 */
class Privacy extends \WC_Abstract_Privacy {

	/**
	 * Constructor.
	 *
	 * @since 2.1.0
	 */
	public function __construct() {
		parent::__construct( __( 'WooCommerce Currency Converter', 'woocommerce-currency-converter-widget' ) );
	}

	/**
	 * Gets the content for the privacy policy page.
	 *
	 * @since 2.1.0
	 */
	public function get_privacy_message() {
		$provider = Exchange_Utils::get_provider();

		ob_start();
		?>
		<div contenteditable="false">
			<p class="wp-policy-help">
				<?php esc_html_e( 'By using this extension, you may be storing personal data or sharing data with an external service. Depending on what settings are enabled the specific information shared by your store will vary. ', 'woocommerce-currency-converter-widget' ); ?>
			</p>
		</div>
		<h2><?php esc_html_e( 'What we collect and store', 'woocommerce-currency-converter-widget' ); ?></h2>
		<p><?php esc_html_e( 'This extension does not collect any information about the customers.', 'woocommerce-currency-converter-widget' ); ?></p>
		<p><?php esc_html_e( 'When the customer clicks on any currency symbol of the widget included with this extension, we set the cookie "woocommerce_current_currency" to store the customer preferences about the currency to use when displaying prices in your store.', 'woocommerce-currency-converter-widget' ); ?></p>
		<h2><?php esc_html_e( 'What we share with others', 'woocommerce-currency-converter-widget' ); ?></h2>
		<p><?php esc_html_e( 'When using the exchange provider API to fetch the currency rates, this one may collect data from your store like:', 'woocommerce-currency-converter-widget' ); ?></p>
		<ul>
			<li><?php esc_html_e( 'The Internet protocol (IP) address.', 'woocommerce-currency-converter-widget' ); ?></li>
			<li><?php esc_html_e( 'The location of the device or computer.', 'woocommerce-currency-converter-widget' ); ?></li>
			<li><?php esc_html_e( 'The computer and device information.', 'woocommerce-currency-converter-widget' ); ?></li>
		</ul>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					/* translators: 1: Exchange provider name, 2: Exchange provider privacy policy URL */
					__( 'Please, visit the <strong>%1$s</strong> <a target="_blank" href="%2$s">Privacy Policy</a> page for more details.', 'woocommerce-currency-converter-widget' ),
					esc_html( $provider->get_name() ),
					esc_url( $provider->get_privacy_url() )
				)
			);
			?>
		</p>
		<?php
		return ob_get_clean();
	}
}
