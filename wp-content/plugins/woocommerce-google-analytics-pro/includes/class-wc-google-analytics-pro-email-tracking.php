<?php
/**
 * WooCommerce Google Analytics Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * The email tracking class.
 *
 * @since 1.0.0
 */
class WC_Google_Analytics_Pro_Email_Tracking {


	/** @var array the \WC_Email instances that should be tracked **/
	private $emails;


	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		foreach ( $this->get_emails() as $tracked_email ) {

			// add filters for additional content for all tracked emails
			add_filter( 'woocommerce_email_additional_content_' . $tracked_email->id, [ $this, 'track_opens' ], 10, 2 );
		}
	}


	/**
	 * Gets the emails that should be tracked.
	 *
	 * @since 1.0.0
	 * @return array associative array of \WC_Emails
	 */
	public function get_emails() {

		if ( null === $this->emails ) {

			$all_emails   = WC()->mailer()->get_emails();
			$track_emails = [];

			// only track customer emails
			if ( ! empty( $all_emails ) ) {

				$track_emails = array_filter( $all_emails, static function ( $email ) {

					return 0 === strpos( $email->id, 'customer_' );
				} );
			}

			/**
			 * Filter which emails should be tracked
			 *
			 * By default, only customer emails are tracked.
			 *
			 * @since 1.0.0
			 * @param array $track_emails Associative array of emails to be tracked
			 */
			$this->emails = apply_filters( 'wc_google_analytics_pro_track_emails', $track_emails );
		}

		return $this->emails;
	}


	/**
	 * Gets an email based on its ID.
	 *
	 * @since 1.8.1
	 *
	 * @param string $email_id the email ID
	 * @return \WC_Email|null
	 */
	private function get_email_by_id( $email_id ) {

		$found_email = null;

		foreach ( $this->get_emails() as $email ) {

			if ( $email_id === $email->id ) {

				$found_email = $email;
				break;
			}
		}

		return $found_email;
	}


	/** Tracking methods ************************************************/


	/**
	 * Adds the tracking image to the email HTML content.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $content content to show below main email content
	 * @param mixed $object object this email is for, for example an order or customer
	 *
	 * @return string|void
	 */
	public function track_opens( $content, $object ) {

		// get the integration class instance
		$integration = wc_google_analytics_pro()->get_integration();

		$tracking_id = $integration->get_tracking_id();

		// skip if no tracking ID
		if ( ! $tracking_id ) {
			return $content;
		}

		$email_id = str_replace( 'woocommerce_email_additional_content_', '', current_filter() );
		$email    = $this->get_email_by_id( $email_id );

		// skip if we're not tracking this email
		if ( ! $email ) {
			return $content;
		}

		// skip if plain email
		$email_type = ! empty( $email->settings['email_type'] ) ? $email->settings['email_type'] : null;
		if ( 'html' !== $email_type ) {
			return $content;
		}

		$cid = $uid = null;

		if ( $object instanceof \WC_Order ) {

			$order = $object;

			// try to get client & user ID from order
			$cid = get_post_meta( $order->get_id(), '_wc_google_analytics_pro_identity', true );
			$uid = $order->get_customer_id();

		} elseif ( $object instanceof \WP_User ) {

			$user = $object;

			// try to get client & user ID from user data
			$uid = $user->ID;
			$cid = get_user_meta( $user->ID, '_wc_google_analytics_pro_identity', true );
		}

		// fall back to generating UUID

		// skip tracking email open if not enabled for the user's role
		if ( null !== $uid && ! $integration->is_tracking_enabled_for_user_role( $uid ) ) {
			return $content;
		}

		$track_user_id = 'yes' === $integration->get_option( 'track_user_id' );

		// by default, a UUID will only be generated if we have no CID, we have a user id and user-id tracking is enabled
		// note: when changing this logic here, adjust the logic in WC_Google_Analytics_Pro_Integration::get_cid() as well
		$generate_uuid = ! $cid && $uid && $track_user_id;

		/** This filter is documented in includes/class-wc-google-analytics-pro-integration.php */
		$generate_uuid = apply_filters( 'wc_google_analytics_pro_generate_client_id', $generate_uuid );

		if ( $generate_uuid ) {
			$cid = $integration->generate_uuid();
		}

		// bail out if tracking user ID is enabled and we don't have a proper user ID nor client ID (registered users/guests)
		// or tracking user ID is disabled and we don't have proper CID
		if ( ( ! $track_user_id && ! $cid ) || ( $track_user_id && ! $cid && ! $uid ) ) {
			return $content;
		}

		$url   = 'https://www.google-analytics.com/collect?';
		$query = urldecode( http_build_query( [
			'v'   => 1,
			'tid' => $tracking_id,                                              // Tracking ID. Required
			'cid' => $cid,                                                      // Client (anonymous) ID. Required
			'uid' => $uid,                                                      // User ID
			't'   => 'event',                                                   // Tracking an event
			'ec'  => 'Emails',                                                  // Event Category
			'ea'  => 'open',                                                    // Event Action
			'el'  => urlencode( $email->title ),                                // Event Label - email title
			'dp'  => urlencode( '/emails/' . sanitize_title( $email->title ) ), // Document Path. Unique for each email
			'dt'  => urlencode( $email->title ),                                // Document Title - email title
		], '', '&' ) );

		$tracking_image = sprintf( '<img src="%s" alt="" />', $url . $query );

		return $content . $tracking_image;
	}


}
