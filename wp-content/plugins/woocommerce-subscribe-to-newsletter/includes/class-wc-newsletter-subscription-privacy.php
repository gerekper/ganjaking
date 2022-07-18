<?php
/**
 * Newsletter Subscription Privacy
 *
 * Privacy/GDPR related functionality which ties into WordPress functionality.
 *
 * @package WC_Newsletter_Subscription
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_Privacy.
 */
class WC_Newsletter_Subscription_Privacy extends WC_Abstract_Privacy {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		parent::__construct( __( 'Subscribe to Newsletter', 'woocommerce-subscribe-to-newsletter' ) );
	}

	/**
	 * Gets the content for the privacy policy page.
	 *
	 * @since 3.0.0
	 */
	public function get_privacy_message() {
		ob_start();
		?>
		<p class="privacy-policy-tutorial">
			<?php esc_html_e( 'By using WooCommerce Subscribe to Newsletter, you may be storing personal data or sharing data with an external service. Depending on what settings are enabled the specific information shared by your store will vary.', 'woocommerce-subscribe-to-newsletter' ); ?>
		</p>
		<h2><?php esc_html_e( 'What we collect and store', 'woocommerce-subscribe-to-newsletter' ); ?></h2>
		<p><?php esc_html_e( 'When you purchase, create an account, or subscribe to our newsletter by using one of the many forms you can find on our site, we’ll track:', 'woocommerce-subscribe-to-newsletter' ); ?></p>
		<ul>
			<li><?php esc_html_e( 'Location, IP address, name, email, and the products you purchase.', 'woocommerce-subscribe-to-newsletter' ); ?></li>
		</ul>
		<p><?php esc_html_e( 'We’ll use this information for purposes, such as, to:', 'woocommerce-subscribe-to-newsletter' ); ?></p>
		<ul>
			<li><?php esc_html_e( 'Subscribe to our newsletter.', 'woocommerce-subscribe-to-newsletter' ); ?></li>
			<li><?php esc_html_e( 'Email you with information based on your interests.', 'woocommerce-subscribe-to-newsletter' ); ?></li>
			<li><?php esc_html_e( 'Send you tailored offers and discounts.', 'woocommerce-subscribe-to-newsletter' ); ?></li>
		</ul>
		<h2><?php esc_html_e( 'What we share with others', 'woocommerce-subscribe-to-newsletter' ); ?></h2>
		<p>
			<?php
			$provider_info = $this->get_provider_info();

			echo wp_kses_post(
				sprintf(
					/* translators: 1: provider name 2: provider privacy policy URL */
					__( 'We share the information mentioned above with %1$s. Please, visit its <a target="_blank" href="%2$s">Privacy Policy</a> page for more details.', 'woocommerce-subscribe-to-newsletter' ),
					esc_html( $provider_info['name'] ),
					esc_url( $provider_info['privacy_url'] )
				)
			);
			?>
		</p>
		<?php

		/**
		 * Filters the privacy policy content.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content The privacy policy content.
		 */
		return apply_filters( 'wc_newsletter_subscription_privacy_policy_content', ob_get_clean() );
	}

	/**
	 * Gets the provider info used in the privacy policy content.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function get_provider_info() {
		$provider = wc_newsletter_subscription_get_provider();
		$info     = array(
			'name'        => 'WooCommerce',
			'privacy_url' => 'https://woocommerce.com/document/marketplace-privacy/',
		);

		if ( $provider ) {
			$info = array(
				'name'        => $provider->get_name(),
				'privacy_url' => $provider->get_privacy_url(),
			);
		}

		return $info;
	}
}

new WC_Newsletter_Subscription_Privacy();
