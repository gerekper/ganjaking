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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Blocks;

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Restricted content block.
 *
 * Creates a block to display content to members only.
 *
 * @since 1.15.0
 */
class Member_Content extends Block implements Dynamic_Content_Block {


	/**
	 * Block constructor.
	 *
	 * @since 1.15.0
	 */
	public function __construct() {

		$this->block_type = 'member-content';

		parent::__construct();

		add_filter( 'wc_memberships_trimmed_restricted_excerpt', [ $this, 'remove_block_from_restricted_content_excerpt' ], 1, 4 );
	}


	/**
	 * Gets all user memberships that have an active status.
	 *
	 * @since 1.15.0
	 *
	 * @param int $user_id the user ID to retrieve memberships for
	 * @param int[] $plans membership plan IDs to limit results for
	 * @return \WC_Memberships_User_Membership[] array of user memberships
	 */
	private function get_active_user_memberships( $user_id, $plans = [] ) {

		$user_memberships = wc_memberships_get_user_active_memberships( $user_id );

		if ( ! empty( $plans ) ) {

			foreach ( $user_memberships as $user_membership_id => $user_membership ) {

				if ( ! in_array( $user_membership->get_plan_id(), $plans, false ) ) {

					unset( $user_memberships[ $user_membership_id ] );
				}
			}
		}

		return $user_memberships;
	}


	/**
	 * Gets a user access delay offset if they have access only through a free trial membership.
	 *
	 * If the user has more than one membership in free trial, picks the user membership with the earliest free trial end.
	 *
	 * @since 1.15.0
	 *
	 * @param \WC_Memberships_User_Membership[] $user_memberships active user memberships
	 * @return bool
	 */
	private function get_free_trial_offset_time( $user_memberships ) {

		$free_trial_end_time = [];

		foreach ( $user_memberships as $id => $user_membership ) {

			if ( $user_membership instanceof \WC_Memberships_Integration_Subscriptions_User_Membership && $user_membership->has_status( 'free_trial' ) ) {

				$free_trial_end_time[] = $user_membership->get_free_trial_end_date( 'timestamp' );

			} else {

				// bail as the user has access via an active membership that is not in free trial
				$free_trial_end_time[] = 0;
				break;
			}
		}

		// pick the earliest date by comparing timestamps
		$free_trial_end_time = ! empty( $free_trial_end_time ) ? min( $free_trial_end_time ) : 0;

		// obtain the time remaining to the earliest date when a free trial membership that grants access ends
		return $free_trial_end_time > 0 ? $this->get_date_offset_time( date( 'Y-m-d H:i:s', $free_trial_end_time ) ) : 0;
	}


	/**
	 * Gets a time offset for a given date compared to today's date.
	 *
	 * Calculates the relative time remaining to a set date from today's date.
	 *
	 * @since 1.15.0
	 *
	 * @param string $date date in MySQL format
	 * @return int time remaining from today to reach the date (0 if in the past)
	 */
	private function get_date_offset_time( $date ) {

		try {

			// we parse dates assuming local timezone, as that's likely the user intention when setting them, but convert to UTC for internal purposes
			$utc_timezone  = new \DateTimeZone( 'UTC' );
			$site_timezone = new \DateTimeZone( wc_timezone_string() );

			$today_date = new \DateTime( 'now', $site_timezone );
			$today_date->setTimezone( $utc_timezone );

			$today_time = $today_date->getTimestamp();

			$access_date = new \DateTime( $date, $site_timezone );
			$access_date->setTimezone( $utc_timezone );

			$access_offset = $access_date->getTimestamp();

			if ( $access_offset > $today_time ) {
				$access_offset -= $today_time;
			} else {
				$access_offset = 0;
			}

		} catch ( \Exception $e ) {

			$access_offset = 0;
		}

		return $access_offset;
	}


	/**
	 * Gets a time offset relative to the oldest start date of active user memberships.
	 *
	 * @since 1.15.0
	 *
	 * @param string $period a period of time defined as "n days", "n weeks", "n months", "n years"...
	 * @param int[] $membership_plans optional IDs of membership plans to validate any user membership (may be none, if any plan is valid)
	 * @param \WC_Memberships_User_Membership[] $user_memberships optional memberships whose start times will be used to calculate the relative offset from (if unspecified will fetch all user memberships for the current user)
	 * @return int
	 */
	private function get_period_offset_time( $period, $membership_plans = [], $user_memberships = [] ) {

		$access_offset = 0;
		$period_parts  = explode( ' ', $period );
		$period_amount = isset( $period_parts[0] ) ? (int) $period_parts[0] : 0;
		$period_type   = isset( $period_parts[1] ) ? trim( (string) $period_parts[1] ) : '';

		if ( $period_amount > 0 && in_array( $period_type, [ 'days', 'weeks', 'months', 'years' ], true ) ) {

			$all_active_time   = [];
			$delay_access_time = $period_amount;
			$user_memberships  = ! empty( $user_memberships ) ? $user_memberships : $this->get_active_user_memberships( get_current_user_id(), $membership_plans );

			// note: to handle months we need to calculate the access time relative to each membership start date, as they're of variable length
			switch ( $period_type ) {
				case 'days' :
					$delay_access_time *= DAY_IN_SECONDS;
				break;
				case 'weeks' :
					$delay_access_time *= WEEK_IN_SECONDS;
				break;
				case 'years' :
					$delay_access_time *= YEAR_IN_SECONDS;
				break;
				default :
					$delay_access_time = 0;
				break;
			}

			foreach ( $user_memberships as $user_membership ) {

				if ( empty( $membership_plans ) || in_array( $user_membership->get_plan_id(), $membership_plans, false ) ) {

					// calculate months offset time relative to the membership's start date
					if ( 'months' === $period_type ) {
						$start_time        = $user_membership->get_start_date( 'timestamp' );
						$delay_access_time = wc_memberships_add_months_to_timestamp( $start_time, $period_amount ) - $start_time;
					}

					$total_active_time = $user_membership->get_total_active_time();

					if ( $total_active_time > $delay_access_time ) {

						// bail as we have at least one membership with immediate access
						$all_active_time = [];
						break;
					}

					$all_active_time[] = $total_active_time;
				}
			}

			// pick the longest amount of time a membership has been active and subtract the period offset, to obtain the shortest amount of time possible
			$access_offset = ! empty( $all_active_time ) ? absint( max( $all_active_time ) - $delay_access_time ) : 0;
		}

		return $access_offset;
	}


	/**
	 * Renders the block content.
	 *
	 * Displays restricted content to members.
	 *
	 * @since 1.15.0
	 *
	 * @param array $attributes block attributes
	 * @param string $content HTML content
	 * @return string HTML
	 */
	public function render( $attributes, $content ) {

		$is_member = $is_admin = false;

		$access_offset       = 0;
		$user_id             = get_current_user_id();
		$user_memberships    = [];
		$membership_plans    = isset( $attributes['membershipPlans'] ) ? array_map( 'absint', (array) $attributes['membershipPlans'] ) : [];
		$delay_access        = isset( $attributes['delayAccess'] ) ? $attributes['delayAccess'] : 'immediate';
		$after_free_trial    = isset( $attributes['afterFreeTrial'] ) && in_array( $attributes['afterFreeTrial'], [ 1, '1', true, 'true' ], true ) && ! Framework\SV_WC_Helper::str_exists( $delay_access, '-' ) && wc_memberships()->get_integrations_instance()->is_subscriptions_active();
		$restriction_message = isset( $attributes['showRestrictionMessage'] ) ? $attributes['showRestrictionMessage'] : false;
		$restriction_message = 'custom' === $restriction_message && isset( $attributes['customRestrictionMessage'] ) ? $attributes['customRestrictionMessage'] : $restriction_message; // false or 'default', or HTML string

		if ( $user_id > 0 ) {

			// skip for admins: they can see all restricted content
			if ( current_user_can( 'wc_memberships_access_all_restricted_content' ) ) {

				$is_member = $is_admin = true;

			// no plans are specified: check if user is active member of at least one plan
			} elseif ( empty( $membership_plans ) ) {

				// if access is only after trial, though, then we need to loop memberships and skip subscription-tied memberships in free trial
				if ( $after_free_trial ) {

					$user_memberships = $this->get_active_user_memberships( $user_id );
					$access_offset    = $this->get_free_trial_offset_time( $user_memberships );
					$is_member        = $access_offset <= 0;

				} else {

					$is_member = wc_memberships_is_user_active_member( $user_id );
				}

			// grant access to content only to members of specific membership plans only
			} else {

				// again, if only allowing access after free trial, we need to loop memberships
				if ( $after_free_trial ) {

					$user_memberships = $this->get_active_user_memberships( $user_id, $membership_plans );
					$access_offset    = $this->get_free_trial_offset_time( $user_memberships );
					$is_member        = $access_offset <= 0;

				} else {

					foreach ( $membership_plans as $membership_plan_id ) {

						if ( wc_memberships_is_user_active_member( $user_id, (int) $membership_plan_id ) ) {

							$is_member = true;
							break;
						}
					}
				}
			}

			// if access condition is not immediate, treat as non-member until further evaluation is done
			if ( ! $is_admin && ( $is_member || $access_offset > 0 ) && 'immediate' !== $delay_access ) {

				$is_member = false;

				// the delay access could be a date in ISO format or a period
				if ( Framework\SV_WC_Helper::str_exists( $delay_access, '-' ) ) {

					// fixed dates access dripping disregard free trial offset
					$access_offset = $this->get_date_offset_time( $delay_access );
					$is_member     = $access_offset <= 0;

				} elseif ( is_string( $delay_access ) ) {

					// for relative periods, these can be cumulative with a free trial offset
					$access_offset += $this->get_period_offset_time( $delay_access, $membership_plans, $user_memberships );
					$is_member      = $access_offset <= 0;
				}
			}
		}

		// display a restriction message or nothing to non-members
		if ( ! $is_member && ! $is_admin ) {

			// if non-member because of a delay access, confirm that is not scheduled for the same day, as that's the smallest unit we consider for dripping
			if ( $access_offset > 0 ) {
				$today_time = current_time( 'timestamp', true );
				$delay_time = $access_offset + $today_time;
				$is_member  = (int) date( 'Ymd', $delay_time ) === (int) date( 'Ymd', $today_time );
			}

			if ( ! $is_member ) {
				if ( is_string( $restriction_message ) && '' !== trim( $restriction_message ) ) {
					$content = $this->get_content_restricted_message( $restriction_message, $membership_plans, max( 0, $access_offset ) );
				} else {
					$content = ''; // use no content restricted message, just hide content
				}
			}
		}

		return $content;
	}


	/**
	 * Gets a block content restricted message.
	 *
	 * Helper method, do not open to public.
	 * @see \WC_Memberships_User_Messages::get_message() for standard messages usage
	 *
	 * @since 1.15.0
	 *
	 * @param string $restriction_message may be 'default' to use a restriction message defined in settings, or full HTML string for a custom one
	 * @param int[] $membership_plans membership plans IDs
	 * @param int $access_time_offset delayed access timestamp offset
	 * @return string HTML
	 */
	private function get_content_restricted_message( $restriction_message, $membership_plans, $access_time_offset ) {

		if ( $access_time_offset > 0 ) {

			$message_code    = 'content_delayed_message';
			$access_time     = current_time( 'timestamp', true ) + $access_time_offset;
			$access_products = [];

		} else {

			$message_code    = 'content_restricted_message';
			$access_time     = 0;
			$access_products = [ [] ];

			// maybe get plans if the restriction applies to all plans
			$membership_plans = empty( $membership_plans ) ? wc_memberships_get_membership_plans() : $membership_plans;

			foreach ( $membership_plans as $membership_plan_id ) {

				if ( $membership_plan = wc_memberships_get_membership_plan( $membership_plan_id ) ) {

					$access_products[] = $membership_plan->get_product_ids();
				}
			}

			// gather products
			$access_products = array_unique( array_merge( ...$access_products ) );

			// if no products, tweak message code
			if ( empty( $access_products ) ) {
				$message_code .= '_no_products';
			}
		}

		$message_args = [
			'context'     => 'content',
			'products'    => array_values( $access_products ),
			'access_time' => $access_time,
		];

		// unless the restriction message is a custom content string, use the default message as stored in settings (or default value)
		if ( 'default' === $restriction_message ) {
			$restriction_message = \WC_Memberships_User_Messages::get_message( $message_code, $message_args );
		}

		$message = \WC_Memberships_User_Messages::parse_message_merge_tags( $restriction_message, $message_args );

		ob_start();

		// ensure that the block HTML class are persisted in the output content ?>
		<div class="<?php echo sanitize_html_class( $this->block_class ); ?>">
			<?php echo \WC_Memberships_User_Messages::get_notice_html( $message_code, $message, $message_args ); ?>
		</div>
		<?php

		return ob_get_clean();
	}


}
