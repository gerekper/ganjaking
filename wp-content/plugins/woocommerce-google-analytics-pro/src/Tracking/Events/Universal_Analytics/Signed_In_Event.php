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
 * @copyright   Copyright (c) 2015-2024, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Identity_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics_Event;
use WP_User;

defined( 'ABSPATH' ) or exit;

/**
 * The "signed in" event.
 *
 * @since 2.0.0
 */
class Signed_In_Event extends Universal_Analytics_Event {


	/** @var string the event ID */
	public const ID = 'signed_in';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Signed In', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer signs in.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'signed in';
	}


	/**
	 * @inheritdoc
	 */
	public function register_hooks() : void {

		add_action( 'wp_login', [ $this, 'track' ], 10, 2 );
	}


	/**
	 * @inheritdoc
	 *
	 * @param string $user_login the signed-in username (unused)
	 * @param WP_User $user the logged-in user object
	 */
	public function track( $user_login = null, $user = null ) : void {

		if ( ! $user instanceof WP_User ) {
			return;
		}

		// note: we send the user ID and not their login (email) to comply with Google policy for not sending personally identifiable information
		$properties = [
			'eventCategory' => 'My Account',
			'eventLabel'    => $user->ID,
		];

		$ec      = [];
		$post_id = url_to_postid( wp_unslash( $_SERVER['REQUEST_URI'] ) );

		// logged in at checkout
		if ( $post_id && $post_id === (int) get_option( 'woocommerce_checkout_page_id' ) ) {
			$ec['checkout_option'] = [
				'step'   => 1,
				'option' => __( 'Registered User', 'woocommerce-google-analytics-pro' ) // can't check is_user_logged_in() as it still returns false here
			];
		}

		$this->record_via_api( $properties, $ec, [ 'uid' => $user->ID ] );

		// store CID in user meta if it is not empty
		if ( ! empty( $cid = Identity_Helper::get_cid() ) ) {

			// store GA identity in user meta
			update_user_meta( $user->ID, '_wc_google_analytics_pro_identity', $cid );
		}
	}


}
