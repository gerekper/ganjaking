<?php
/**
 * Integration with WooCommerce Point and Rewards plugin
 *
 * Rewards the user when subscribing to the newsletter
 *
 * @package WC_Newsletter_Subscription
 * @since   2.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_Points_Rewards.
 */
class WC_Newsletter_Subscription_Points_Rewards {

	/**
	 * Constructor.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {
		add_filter( 'wc_points_rewards_action_settings', array( $this, 'add_settings' ) );
		add_filter( 'wc_points_rewards_event_description', array( $this, 'event_description' ), 10, 2 );
		add_action( 'wc_subscribed_to_newsletter', array( $this, 'reward_newsletter_signup' ) );
	}

	/**
	 * Adds settings to the Points and Rewards plugin.
	 *
	 * @since 2.9.0
	 *
	 * @param array $settings Settings.
	 * @return array
	 */
	public function add_settings( $settings ) {
		$settings[] = array(
			'title'    => esc_html__( 'Points earned for newsletter signup', 'woocommerce-subscribe-to-newsletter' ),
			'desc_tip' => esc_html__( 'Enter the amount of points earned when a customer signs up for a newsletter via the "Subscribe to Newsletter" extension.', 'woocommerce-subscribe-to-newsletter' ),
			'id'       => 'wc_points_rewards_wc_newsletter_signup',
		);

		return $settings;
	}

	/**
	 * Defines the description for the newsletter subscription event.
	 *
	 * @since 2.9.0
	 *
	 * @param string $description The event description.
	 * @param string $type        The event type.
	 * @return string
	 */
	public function event_description( $description, $type ) {
		if ( 'wc-newsletter-signup' !== $type ) {
			return $description;
		}

		$label         = get_option( 'wc_points_rewards_points_label', '' );
		$points_labels = explode( ':', $label );

		if ( is_array( $points_labels ) && isset( $points_labels[1] ) ) {
			$label = $points_labels[1];
		}

		/* translators: %s: points label */
		return sprintf( esc_html__( '%s earned for newsletter signup', 'woocommerce-subscribe-to-newsletter' ), $label );
	}

	/**
	 * Rewards user on subscribe to the newsletter.
	 *
	 * @since 2.9.0
	 *
	 * @param string $email User email.
	 */
	public function reward_newsletter_signup( $email ) {
		if ( ! class_exists( 'WC_Points_Rewards_Manager' ) ) {
			return;
		}

		$user = get_user_by( 'email', $email );

		if ( ! $user ) {
			return;
		}

		// Get the points for this action.
		$points = get_option( 'wc_points_rewards_wc_newsletter_signup', 0 );

		if ( ! $points ) {
			return;
		}

		$entries = WC_Points_Rewards_Points_Log::get_points_log_entries(
			array(
				'user'       => $user->ID,
				'event_type' => 'wc-newsletter-signup',
			)
		);

		// Check if the user was rewarded for signing up for the newsletter.
		if ( 0 < count( $entries ) ) {
			return;
		}

		// Additional data to persist in the points event log.
		$data = array( 'email' => $email );

		WC_Points_Rewards_Manager::increase_points( $user->ID, $points, 'wc-newsletter-signup', $data );
	}
}

return new WC_Newsletter_Subscription_Points_Rewards();
