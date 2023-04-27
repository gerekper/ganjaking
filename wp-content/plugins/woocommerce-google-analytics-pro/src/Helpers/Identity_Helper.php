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
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers;

defined( 'ABSPATH' ) or exit;

/**
 * Identity helper class.
 *
 * This class has helpful methods to get the visitor identity, such as cid (client ID) or user ID.
 *
 * @since 2.0.0
 */
class Identity_Helper {


	/**
	 * Gets a unique identity for the current user.
	 *
	 * @link http://www.stumiller.me/implementing-google-analytics-measurement-protocol-in-php-and-wordpress/
	 *
	 * @since 2.0.0
	 *
	 * @param bool $force_generate_uuid (optional) whether to force generating a UUID if no CID can be found from cookies, defaults to false
	 * @return string the visitor's ID from Google's cookie, or user's meta, or generated
	 */
	public static function get_cid( bool $force_generate_uuid = false ) : string {

		$identity = '';

		// get identity via GA cookie
		if ( isset( $_COOKIE['_ga'] ) ) {

			[, , $cid1, $cid2] = explode('.', $_COOKIE['_ga'], 4);

			$identity = $cid1 . '.' . $cid2;
		}

		// generate UUID if identity is not set
		if ( empty( $identity ) ) {

			// neither cookie set and named identity not passed, cookies are probably disabled for visitor or GA tracking might be blocked
			if ( wc_google_analytics_pro()->get_integration()->debug_mode_on() ) {

				wc_google_analytics_pro()->log( 'No identity found. Cookies are probably disabled for visitor or GA tracking might be blocked.' );
			}

			// by default, a UUID will only be generated if we have no CID, we have a user logged in and user-id tracking is enabled
			// note: when changing this logic here, adjust the logic in Email_tracking::track_opens() as well
			$generate_uuid = $force_generate_uuid || ( ! $identity && is_user_logged_in() && 'yes' === wc_google_analytics_pro()->get_integration()->get_option( 'track_user_id' ) );

			/**
			 * Filters whether a client ID should be generated.
			 *
			 * Allows generating a UUID for to be used as the client ID, when it can't be determined from cookies or other sources, such as the order or user meta.
			 *
			 * @since 1.3.5
			 *
			 * @param bool $generate_uuid the generate UUID flag
			 */
			$generate_uuid = apply_filters( 'wc_google_analytics_pro_generate_client_id', $generate_uuid );

			if ( $generate_uuid ) {

				$identity = self::generate_uuid();
			}
		}

		return $identity;
	}

	/**
	 * Get current user ID, if logged in.
	 *
	 * @since 2.0.0
	 *
	 * @return int|null
	 */
	public static function get_uid(): ?int {

		return is_user_logged_in() ? get_current_user_id() : null;
	}


	/**
	 * Gets the current visitor identities.
	 *
	 * Returns 1 or 2 identities - the CID (GA client ID from cookie) and
	 * current user ID, if available.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public static function get_all(): array {

		$identities = [];

		// set CID only if it is not null
		if ( ! empty( $cid = self::get_cid() ) ) {
			$identities['cid'] = $cid;
		}

		if ( $uid = self::get_uid() ) {
			$identities['uid'] = $uid;
		}

		return $identities;
	}


	/**
	 * Generates a UUID v4.
	 *
	 * Needed to generate a CID when one isn't available.
	 *
	 * @link https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid/15875555#15875555
	 *
	 * @since 2.0.0
	 *
	 * @return string the generated UUID
	 */
	public static function generate_uuid() : string {

		try {

			$bytes = random_bytes( 16 );

			$bytes[6] = chr( ord( $bytes[6] ) & 0x0f | 0x40 ); // set version to 0100
			$bytes[8] = chr( ord( $bytes[8] ) & 0x3f | 0x80 ); // set bits 6-7 to 10

			return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $bytes ), 4 ) );

		} catch( \Exception $e ) {

			// fall back to mt_rand if random_bytes is unavailable
			return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
				// 16 bits for "time_mid"
				mt_rand( 0, 0xffff ),
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand( 0, 0x0fff ) | 0x4000,
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand( 0, 0x3fff ) | 0x8000,
				// 48 bits for "node"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
			);
		}
	}


}
