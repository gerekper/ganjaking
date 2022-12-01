<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

/**
 * WooCommerce Memberships CLI Command.
 *
 * Base class that must be extended by any WooCommerce Memberships sub commands for WP CLI.
 * It extends \WC_CLI_Command which in turn is a \WP_CLI_Command child.
 *
 * TODO since WooCommerce 3.0 WP CLI support in WooCommerce moved to a new model that integrates with the REST API. When supporting the REST API, Memberships will also move WC CLI support accordingly - currently it re-implements the objects before WC 3.0 {FN 2017-07-14}
 *
 * @since 1.7.0
 * @deprecated since 1.13.0
 */
class WC_Memberships_CLI_Command extends \WC_CLI_Command {


	/**
	 * Gets a deprecation warning inviting legacy WP CLI users to switch over the WP REST CLI.
	 *
	 * @since 1.13.0
	 * @deprecated since 1.13.0
	 *
	 * @param null|string $suggestion suggested command replacement
	 * @return string
	 */
	protected function get_deprecation_warning( $suggestion = null ) {

		if ( '' !== $suggestion && is_string( $suggestion ) ) {
			$suggestion = sprintf( 'Try using "%s".', $suggestion );
		} else {
			$suggestion = '';
		}

		return sprintf( 'Heads up! The legacy Memberships WP CLI support is being gradually deprecated in favor of WP REST CLI handling. It will be removed in a future version. %s', $suggestion );
	}


	/**
	 * Get a Member from a user id, login name or email address.
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param int|string $customer a user id, email or login name
	 * @return null|false|\WP_User
	 */
	protected function get_member( $customer ) {

		$member = null;

		if ( is_numeric( $customer ) ) {
			$member = get_user_by( 'id', (int) $customer  );
		} elseif ( is_string( $customer ) ) {
			if ( is_email( $customer ) ) {
				$member = get_user_by( 'email', $customer );
			} else {
				$member = get_user_by( 'login', $customer );
			}
		}

		return $member;
	}


	/**
	 * Loosely parse a date for Memberships date use.
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param string $date a date in mysql string format
	 * @return false|string a datetime string or false if not a valid date
	 */
	protected function parse_membership_date( $date ) {
		return wc_memberships_parse_date( $date, 'mysql' );
	}


	/**
	 * Check if a Membership status is valid.
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param string $status status to check
	 * @return bool
	 */
	protected function is_valid_membership_status( $status ) {

		$statuses = $this->get_membership_status_keys();
		$status   = $this->trim_membership_status_prefix( $status );

		return ! empty( $statuses ) ? in_array( $status, $statuses, true ) : false;
	}


	/**
	 * Get User Membership status keys.
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param bool $trim trim prefix from the status keys
	 * @return array
	 */
	protected function get_membership_status_keys( $trim = true ) {

		$keys     = array();
		$statuses = wc_memberships_get_user_membership_statuses();

		if ( ! empty( $statuses ) ) {

			foreach ( array_keys( $statuses ) as $key ) {
				$keys[] = true === $trim ? $this->trim_membership_status_prefix( $key ) : $key;
			}
		}

		return $keys;
	}


	/**
	 * Removes the WooCommerce User Membership status prefix.
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param string $status
	 * @return string
	 */
	protected function trim_membership_status_prefix( $status ) {
		return Framework\SV_WC_Helper::str_starts_with( $status, 'wcm-' ) ? substr( $status, 4 ) : $status;
	}


	/**
	 * Add the WooCommerce User Membership status prefix.
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param $status
	 * @return string
	 */
	protected function add_membership_status_prefix( $status ) {
		return Framework\SV_WC_Helper::str_starts_with( $status, 'wcm-' ) ? $status : 'wcm-' . $status;
	}


	/**
	 * Get Meta Query arguments for date filtering.
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param string $meta_key
	 * @param string|array $dates
	 * @return array
	 */
	protected function get_date_range_meta_query_args( $meta_key, $dates ) {

		$args = array();

		if ( ! empty( $dates ) || ! empty( $meta_key ) ) {

			$dates   = ! is_array( $dates ) ? explode( ',', $dates ) : $dates;
			$count   = count( $dates );
			$compare = '>=';
			$errors  = 0;

			foreach ( $dates as $date ) {

				if ( false === $this->parse_membership_date( $date ) ) {

					\WP_CLI::warning( sprintf( 'Date "%s" is not valid.', $date ) );
					$errors++;
				}
			}

			if ( 0 === $errors ) {

				if ( $count >= 3 ) {
					$compare = 'IN';
				} elseif ( 2 === $count ) {
					$compare = 'BETWEEN';
				}

				$args = array(
					'key'     => $meta_key,
					'value'   => 1 === $count ? $dates[0] : $dates,
					'compare' => $compare,
					'type'    => 'DATETIME',
				);
			}
		}

		return $args;
	}


}
