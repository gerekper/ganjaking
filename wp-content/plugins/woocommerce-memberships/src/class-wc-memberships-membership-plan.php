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

use SkyVerge\WooCommerce\Memberships\Helpers\Strings_Helper;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Membership Plan object.
 *
 * This class represents a single membership plan, eg "silver" or "gold" with its specific configuration.
 *
 * @since 1.0.0
 */
class WC_Memberships_Membership_Plan {


	/** @var int Membership Plan (post) ID */
	public $id;

	/** @var string Membership Plan name */
	public $name;

	/** @var  string Membership Plan (post) slug */
	public $slug;

	/** @var \WP_Post Membership Plan post object */
	public $post;

	/** @var int[] array of IDs of products that grant access */
	private $product_ids;

	/** @var string access method meta */
	protected $access_method_meta = '';

	/** @var string the default access method */
	protected $default_access_method = '';

	/** @var string access length meta */
	protected $access_length_meta = '';

	/** @var string access start date meta */
	protected $access_start_date_meta = '';

	/** @var string access end date meta */
	protected $access_end_date_meta = '';

	/** @var string product ids meta */
	protected $product_ids_meta = '';

	/** @var string members area sections meta */
	protected $members_area_sections_meta = '';

	/** @var string email content meta */
	protected $email_content_meta = '';

	/** @var array cached plan rules by memoization */
	private $rules = array();


	/**
	 * Membership Plan Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|\WP_Post|\WC_Memberships_Membership_Plan $id Membership Plan slug, post object or related post ID
	 */
	public function __construct( $id ) {

		if ( ! $id ) {
			return;
		}

		if ( is_numeric( $id ) ) {

			$post = get_post( $id );

			if ( ! $post ) {
				return;
			}

			$this->post = $post;

		} elseif ( is_object( $id ) ) {

			$this->post = $id;
		}

		if ( $this->post ) {

			// load in post data
			$this->id   = $this->post->ID;
			$this->name = $this->post->post_title;
			$this->slug = $this->post->post_name;
		}

		$this->set_meta_keys();

		// set the default access method
		$this->default_access_method = 'unlimited';
	}


	/**
	 * Returns the plan ID.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}


	/**
	 * Returns the plan name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}


	/**
	 * Returns the plan name formatted with its ID.
	 *
	 * @since 1.10.6
	 *
	 * @return string
	 */
	public function get_formatted_name() {

		return sprintf( '%1$s (#%2$s)', $this->get_name(), $this->get_id() );
	}


	/**
	 * Returns the plan slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}


	/**
	 * Returns meta keys used to store user membership meta data.
	 *
	 * @since 1.11.0
	 *
	 * @return string[]
	 */
	public function get_meta_keys() {

		return array(
			'_access_method',
			'_access_length',
			'_access_start_date',
			'_access_end_date',
			'_product_ids',
			'_members_area_sections',
			'_email_content',
		);
	}


	/**
	 * Sets the user membership meta keys for storing meta data.
	 *
	 * @since 1.11.1
	 */
	protected function set_meta_keys() {

		foreach ( $this->get_meta_keys() as $meta_key ) {

			$property = ltrim( $meta_key, '_' ) . '_meta';

			$this->$property = $meta_key;
		}
	}


	/**
	 * Returns the product ids that grant access to this plan.
	 *
	 * @since 1.0.0
	 *
	 * @return int[]
	 */
	public function get_product_ids() {

		if ( null === $this->product_ids ) {
			$this->product_ids = get_post_meta( $this->id, $this->product_ids_meta, true );
			$this->product_ids = is_array( $this->product_ids ) ? array_unique( array_map( 'absint', $this->product_ids ) ) : [];
		}

		return $this->product_ids;
	}


	/**
	 * Returns products that grant access to plan.
	 *
	 * @since 1.7.0
	 *
	 * @param bool $exclude_subscriptions optional, whether to exclude subscription products (default false, include them)
	 * @return \WC_Product[] array of products
	 */
	public function get_products( $exclude_subscriptions = false ) {

		$products = [];

		if ( $this->has_products() ) {

			foreach ( $this->get_product_ids() as $product_id ) {

				if ( $product = wc_get_product( $product_id ) ) {

					if ( true === $exclude_subscriptions ) {

						// by using Subscriptions method we can account for custom subscription product types
						if ( is_callable( 'WC_Subscriptions_Product::is_subscription' ) ) {
							$is_subscription = \WC_Subscriptions_Product::is_subscription( $product );
						} else {
							$is_subscription = $product->is_type( array( 'subscription', 'variable-subscription', 'subscription_variation' ) );
						}

						if ( $is_subscription ) {
							continue;
						}
					}

					$products[ $product_id ] = $product;
				}
			}
		}

		return $products;
	}


	/**
	 * Sets ids of products that can grant access to this plan.
	 *
	 * @since 1.7.0
	 *
	 * @param string|int|int[] $product_ids array or comma separated string of product ids or single id (numeric)
	 * @param bool $merge whether to merge the specified product ids to the existing ones, rather than replace values
	 */
	public function set_product_ids( $product_ids, $merge = false ) {

		if ( is_string( $product_ids ) ){
			$product_ids = explode( ',', $product_ids );
		}

		$product_ids = array_map( 'absint', (array) $product_ids );

		// ensure all products are valid
		foreach ( $product_ids as $index => $product_id ) {

			if ( $product_id <= 0 || ! wc_get_product( $product_id ) ) {
				// remove invalid product
				unset( $product_ids[ $index ] );
			}
		}

		if ( true === $merge ) {
			$product_ids = array_merge( $this->get_product_ids(), $product_ids );
		}

		$this->product_ids = array_unique( $product_ids );

		if ( empty( $this->product_ids ) ) {
			delete_post_meta( $this->id, $this->product_ids_meta );
		} else {
			update_post_meta( $this->id, $this->product_ids_meta, $this->product_ids );
		}
	}


	/**
	 * Deletes product ids meta.
	 *
	 * @since 1.7.0
	 *
	 * @param null|string|int|int[] $product_ids optional, if an array or single numeric value is passed, one or more ids will be removed from the product ids meta
	 */
	public function delete_product_ids( $product_ids = null ) {

		if ( empty( $product_ids ) ) {

			$this->product_ids = [];

			delete_post_meta( $this->id, $this->product_ids_meta );

		} else {

			if ( is_numeric( $product_ids ) ) {
				$product_ids = (array) $product_ids;
			}

			$remove_ids   = array_map( 'absint', $product_ids );
			$existing_ids = $this->get_product_ids();

			$this->product_ids = array_diff( array_unique( $existing_ids ), array_unique( $remove_ids ) );

			if ( empty( $this->product_ids ) ) {
				delete_post_meta( $this->id, $this->product_ids_meta );
			} else {
				update_post_meta( $this->id, $this->product_ids_meta, $this->product_ids );
			}
		}
	}


	/**
	 * Checks if this plan has any products that grant access.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_products() {

		return ! empty( $this->get_product_ids() );
	}


	/**
	 * Checks if this plan has a specified product that grant access.
	 *
	 * @since 1.0.0
	 *
	 * @param int $product_id Product ID to search for
	 * @return bool
	 */
	public function has_product( $product_id ) {

		return is_numeric( $product_id ) && in_array( (int) $product_id, $this->get_product_ids(), true );
	}


	/**
	 * Ensures that an access method is one of the accepted types.
	 *
	 * @since 1.7.0
	 *
	 * @param string $method either 'manual-only', 'signup' or 'purchase'
	 * @return string defaults to manual-only if an invalid method is supplied
	 */
	private function validate_access_method( $method ) {

		$valid_access_methods = wc_memberships()->get_plans_instance()->get_membership_plans_access_methods();

		return in_array( $method, $valid_access_methods, true ) ? $method : 'manual-only';
	}


	/**
	 * Sets the method to grant access to the membership.
	 *
	 * @since 1.7.0
	 *
	 * @param string $method either 'manual-only', 'signup' or 'purchase'
	 */
	public function set_access_method( $method ) {

		update_post_meta( $this->id, $this->access_method_meta, $this->validate_access_method( $method ) );
	}


	/**
	 * Returns the method to grant access to the membership.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_access_method() {

		$grant_access_type = get_post_meta( $this->id, $this->access_method_meta, true );

		// backwards compatibility check
		if ( empty( $grant_access_type ) ) {

			$product_ids = $this->get_product_ids();

			if ( ! empty( $product_ids ) ) {
				$grant_access_type = 'purchase';
			}
		}

		return $this->validate_access_method( $grant_access_type );
	}


	/**
	 * Removes the access method meta.
	 *
	 * Will default the access method to manual-only.
	 *
	 * @since 1.7.0
	 */
	public function delete_access_method() {

		delete_post_meta( $this->id, $this->access_method_meta );
	}


	/**
	 * Checks the plan's access method.
	 *
	 * @since 1.7.0
	 *
	 * @param array|string $method either 'manual-only', 'signup' or 'purchase'
	 * @return bool
	 */
	public function is_access_method( $method ) {
		return is_array( $method ) ? in_array( $this->get_access_method(), $method, true ) : $method === $this->get_access_method();
	}


	/**
	 * Sets the plan access length.
	 *
	 * @since 1.7.0
	 *
	 * @param string $access_length an access period defined as "2 weeks", "5 months", "1 year" etc.
	 * @return bool success
	 */
	public function set_access_length( $access_length ) {

		$success       = false;
		$access_length = (string) wc_memberships_parse_period_length( $access_length );

		if ( ! empty( $access_length ) ) {
			$success = (bool) update_post_meta( $this->id, $this->access_length_meta, $access_length );
		}

		return $success;
	}


	/**
	 * Returns the access length amount.
	 *
	 * Returns the amount part of the access length.
	 * For example, returns '5' for the period '5 days'
	 *
	 * @since 1.0.0
	 *
	 * @return int|string amount or empty string if no schedule
	 */
	public function get_access_length_amount() {
		return wc_memberships_parse_period_length( $this->get_access_length(), 'amount' );
	}


	/**
	 * Returns the access length period.
	 *
	 * Returns the period part of the access length.
	 * For example, returns 'days' for the period '5 days'
	 *
	 * @since 1.0.0
	 *
	 * @return string a period
	 */
	public function get_access_length_period() {
		return wc_memberships_parse_period_length( $this->get_access_length(), 'period' );
	}


	/**
	 * Checks whether this plan has a specific period length set.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function has_access_length() {

		$period = $this->get_access_length_period();
		$amount = $this->get_access_length_amount();

		return is_int( $amount ) && ! empty( $period );
	}


	/**
	 * Returns the plan's access length.
	 *
	 * @since 1.0.0
	 *
	 * @return string access length in strtotime-friendly format, eg. "5 days", or empty string when unlimited
	 */
	public function get_access_length() {

		// get access length for specific length membership plan
		$access_length = get_post_meta( $this->id, $this->access_length_meta, true );

		// get access length for fixed length membership plan
		if ( $access_end = wc_memberships_parse_date( $this->get_access_end_date_meta(), 'mysql' ) ) {

			// get the access length relative to remaining days from now to a certain date
			$start_time    = $this->get_access_start_date( 'timestamp' );
			$end_time      = strtotime( $access_end );
			$access_days   = ( $end_time - $start_time ) / DAY_IN_SECONDS;
			$access_length = sprintf( '%d days', max( 1, (int) $access_days ) );
		}

		return ! empty( $access_length ) ? $access_length : '';
	}


	/**
	 * Returns the plan access length in seconds.
	 *
	 * Note: when the plan length is specified in months, the actual amount is dependant on the current month.
	 *
	 * @since 1.11.0
	 *
	 * @return int|null returns null if plan has no set access length or fixed dates
	 */
	public function get_access_length_in_seconds() {

		$type        = $this->get_access_length_type();
		$in_seconds  = 0;

		if ( 'fixed' === $type ) {

			$start_time = $this->get_access_start_date( 'timestamp' );
			$end_time   = $this->get_access_end_date( 'timestamp' );

			if ( $start_time && $end_time ) {
				$in_seconds = max( 0, (int) $end_time - (int) $start_time );
			}

		} elseif ( 'specific' === $type ) {

			$start  = current_time( 'timestamp', true );
			$amount = $this->get_access_length_amount();
			$period = $this->get_access_length_period();

			if ( 'months' === $period ) {
				$in_seconds = wc_memberships_add_months_to_timestamp( $start, $amount );
			} else {
				$in_seconds = max( 0, strtotime( "+{$amount} {$period}", $start ) );
			}

			$in_seconds -= $start;
		}

		return $in_seconds > 0 ? $in_seconds : null;
	}


	/**
	 * Returns the membership plan access length in a human readable format.
	 *
	 * Note: this may result in approximations, e.g. "2 months (57 days)" and so on.
	 *
	 * @since 1.7.0
	 *
	 * @return string parses the access length and returns the number of years, months, etc.and the total number of days of a membership plan length
	 */
	public function get_human_access_length() {

		$standard_length = $this->get_access_length();

		if ( empty( $standard_length ) ) {

			$human_length = __( 'Unlimited', 'woocommerce-memberships' );

		} else {

			$present = current_time( 'timestamp', true );
			$future  = strtotime( $standard_length, $present );
			$n_days  = ( $future - $present ) / DAY_IN_SECONDS;
			/* translators: Placeholders: %d - number of days */
			$days    = sprintf( _n( '%d day', '%d days', $n_days ), $n_days );
			$diff    = human_time_diff( $present, $future );

			if ( $n_days >= 31 ) {
				$human_length = is_rtl() ? "({$days}) " . $diff : $diff . " ({$days})";
			} else {
				$human_length = $days;
			}
		}

		/**
		 * Filters a User Membership access length in a human friendly form.
		 *
		 * @since 1.7.2
		 *
		 * @param string $human_length the length in human friendly format
		 * @param string $standard_length the length in machine friendly format
		 * @param int $user_membership_id the User Membership ID
		 */
		return apply_filters( 'wc_memberships_membership_plan_human_access_length', $human_length, $standard_length, $this->id );
	}


	/**
	 * Removes the access length information.
	 *
	 * Note this only removes the access length for specific-length membership plans if the membership has a fixed length, use the following methods:
	 * @see \WC_Memberships_Membership_Plan::delete_access_start_date()
	 * @see \WC_Memberships_Membership_Plan::delete_access_end_date()
	 *
	 * @since 1.7.0
	 *
	 * @return bool success
	 */
	public function delete_access_length() {

		return (bool) delete_post_meta( $this->id, $this->access_length_meta );
	}


	/**
	 * Returns the plan's access length type.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_access_length_type() {

		$access_length = $this->default_access_method;
		$access_end    = $this->get_access_end_date_meta();

		if ( ! empty( $access_end ) ) {
			$access_length = 'fixed';
		} elseif ( $this->has_access_length() ) {
			$access_length = 'specific';
		}

		return $access_length;
	}


	/**
	 * Checks the plan's access length type.
	 *
	 * @since 1.7.0
	 *
	 * @param array|string $type either 'specific', 'fixed' or 'unlimited'
	 * @return bool
	 */
	public function is_access_length_type( $type ) {

		return is_array( $type ) ? in_array( $this->get_access_length_type(), $type, true ) : $type === $this->get_access_length_type();
	}


	/**
	 * Sets the plan access start date.
	 *
	 * Note: this only affects memberships of fixed length.
	 *
	 * @since 1.7.0
	 *
	 * @param string|null $date optional, defaults to now, otherwise a date in mysql format
	 * @return bool success
	 */
	public function set_access_start_date( $date = null ) {

		$success = false;
		$date    = null === $date ? (string) date( 'Y-m-d H:i:s', current_time( 'timestamp', true ) ) : $date;

		if ( $start_date = wc_memberships_parse_date( $date, 'mysql' ) ) {

			$success = (bool) update_post_meta( $this->id, $this->access_start_date_meta, $start_date );
		}

		return $success;
	}


	/**
	 * Returns the plan's access start date.
	 *
	 * This is usually 'today', but for fixed membership plans it could be a date in the future or in the past.
	 * Note: this does not reflect a user membership start date.
	 *
	 * @since 1.7.0
	 *
	 * @param string $format optional, either 'mysql' (default) or 'timestamp' for timestamp
	 * @return string|int
	 */
	public function get_access_start_date( $format = 'mysql' ) {

		if ( $this->is_access_length_type( 'fixed' ) ) {
			$start_date = $this->validate_access_start_date( get_post_meta( $this->id, $this->access_start_date_meta, true ) );
		}

		if ( empty( $start_date ) ) {
			$start_date = strtotime( 'today', current_time( 'timestamp', true ) );
		}

		return wc_memberships_format_date( $start_date, $format );
	}


	/**
	 * Checks if the start access date is set after the end access date.
	 *
	 * If so, rolls back the start access date to one day before the end access date.
	 *
	 * @since 1.7.0
	 *
	 * @param string $access_start_date a date in mysql format
	 * @return false|string false on error or MySQL date upon validation
	 */
	private function validate_access_start_date( $access_start_date ) {

		$start_date = wc_memberships_parse_date( $access_start_date, 'mysql' );

		if ( $start_date && ( $end_date = wc_memberships_parse_date( $this->get_access_end_date_meta(), 'mysql' ) ) ) {

			$start_time = strtotime( $start_date );
			$end_time   = strtotime( $end_date );

			if ( $start_time >= $end_time ) {

				// force push the fixed dates one day apart from each other
				$start_date = date( 'Y-m-d H:i:s', strtotime( 'yesterday', $end_time ) );
				$end_date   = date( 'Y-m-d H:i:s', strtotime( 'tomorrow',  $end_time ) );

				$this->set_access_start_date( $start_date );
				$this->set_access_end_date( $end_date );
			}
		}

		return $start_date;
	}


	/**
	 * Returns the access start date, adjusted for the local site timezone.
	 *
	 * @since 1.7.0
	 *
	 * @param string $format optional, the date format: either 'mysql' (default) or 'timestamp'
	 * @return string|int
	 */
	public function get_local_access_start_date( $format = 'mysql' ) {

		// get the date timestamp
		$date = $this->get_access_start_date( 'timestamp' );

		// adjust the date to the site's local timezone
		return wc_memberships_adjust_date_by_timezone( $date, $format );
	}


	/**
	 * Deletes the access start date meta
	 *
	 * Note: this only affects membership plans of fixed length.
	 *
	 * @since 1.7.0
	 *
	 * @return bool success
	 */
	public function delete_access_start_date() {

		return (bool) delete_post_meta( $this->id, $this->access_start_date_meta );
	}


	/**
	 * Sets the plan's access end date
	 *
	 * Note: this only affects membership plans of fixed length.
	 *
	 * @since 1.7.0
	 *
	 * @param string $date a date in MySQL format
	 * @return bool
	 */
	public function set_access_end_date( $date ) {

		$success = false;

		if ( $end_date = wc_memberships_parse_date( $date, 'mysql' ) ) {

			$success = (bool) update_post_meta( $this->id, $this->access_end_date_meta, $end_date );
		}

		return $success;
	}


	/**
	 * Returns the plan's access end date.
	 *
	 * Note: this will return the access end date for fixed length membership plans otherwise it will return the expiration date.
	 *
	 * @since 1.7.0
	 *
	 * @param string $format optional, the date format: either 'mysql' (default) or 'timestamp'
	 * @param array $args optional arguments passed to fallback method
	 * @return string|int returns empty string regardless of $format for unlimited memberships
	 */
	public function get_access_end_date( $format = 'mysql', $args = array() ) {

		$end_date = get_post_meta( $this->id, $this->access_end_date_meta, true );
		$end_date = empty( $end_date ) ? $this->get_expiration_date( current_time( 'timestamp', true ), $args ) : $end_date;

		return ! empty( $end_date ) ? wc_memberships_format_date( $end_date, $format ) : '';
	}


	/**
	 * Returns the plan's access end date, adjusted for the local site timezone.
	 *
	 * @since 1.7.0
	 *
	 * @param string $format optional, the date format: either 'mysql' (default) or 'timestamp'
	 * @return string|int returns empty string regardless of $format for unlimited memberships
	 */
	public function get_local_access_end_date( $format = 'mysql' ) {

		$access_end_date = $this->get_access_end_date( $format );

		return ! empty( $access_end_date ) ? wc_memberships_adjust_date_by_timezone( $access_end_date, $format ) : '';
	}


	/**
	 * Returns the plan's access end date meta.
	 *
	 * @see \WC_Memberships_Membership_Plan::get_expiration_date()
	 *
	 * @since 1.7.0
	 *
	 * @return string|null
	 */
	protected function get_access_end_date_meta() {

		$access_end_date = get_post_meta( $this->id, $this->access_end_date_meta, true );

		return ! empty( $access_end_date ) ? $access_end_date : null;
	}


	/**
	 * Deletes the access end date meta.
	 *
	 * @since 1.7.0
	 *
	 * @return bool success
	 */
	public function delete_access_end_date() {

		return (bool) delete_post_meta( $this->id, $this->access_end_date_meta );
	}


	/**
	 * Returns the plan's expiration date.
	 *
	 * Calculates when a membership plan will expire relatively to a start date.
	 *
	 * @since 1.3.8
	 *
	 * @param int|string $start optional: a date string or timestamp as the start time relative to the expiry date to calculate expiration for (default: current time)
	 * @param array $args optional: additional arguments passed in hooks
	 * @return string date in MySQL Y-m-d H:i:s format or empty for unlimited plans (no expiry)
	 */
	public function get_expiration_date( $start = '', $args = array() ) {

		$end      = '';
		$end_date = '';

		// start is placed here again for backwards compatibility reasons
		// (see filter arguments at the end of method)
		$args = wp_parse_args( $args, array(
			'plan_id' => $this->id,
			'start'   => $start,
		) );

		// unlimited length plans have no end date, calculate only for those who have
		if ( ! $this->is_access_length_type( 'unlimited' ) ) {

			// get the access length for fixed and specific length membership plans
			$access_length = $this->get_access_length();

			// get the start time to get the relative end time later
			if ( $this->is_access_length_type( 'fixed' ) ) {
				$start = $this->get_access_start_date( 'timestamp' );
			} elseif ( empty( $start ) ) {
				if ( ! empty( $args['start'] ) ) {
					$start = is_numeric( $args['start'] ) ? (int) $args['start'] : strtotime( $args['start'] );
				} else {
					$start = current_time( 'timestamp', true );
				}
			} elseif ( is_string( $start ) && ! is_numeric( $start ) ) {
				$start = strtotime( $start );
			} else {
				$start = is_numeric( $start ) ? (int) $start : current_time( 'timestamp', true );
			}

			// tweak end date for months calculation
			if ( Framework\SV_WC_Helper::str_ends_with( $access_length, 'months' ) ) {
				$end = wc_memberships_add_months_to_timestamp( (int) $start, $this->get_access_length_amount() );
			} else {
				$end = strtotime( '+ ' . $access_length, (int) $start );
			}

			// format the end date
			if ( isset( $args['format'] ) && 'timestamp' === $args['format'] ) {
				$end_date = $end;
			} else {
				$end_date = date( 'Y-m-d H:i:s', $end );
			}
		}

		/**
		 * Filters the plan's expiration date.
		 *
		 * @since 1.5.3
		 *
		 * @param int|string $expiration_date date in MySQL Y-m-d H:i:s format (or optionally timestamp), empty string for unlimited plans
		 * @param int|string $expiration_timestamp timestamp, empty string for unlimited plans
		 * @param array $args associative array of additional arguments as passed to get expiration method
		 */
		return apply_filters( 'wc_memberships_plan_expiration_date', $end_date, $end, $args );
	}


	/**
	 * Sets members area sections for this plan.
	 *
	 * @see wc_memberships_get_members_area_sections()
	 *
	 * @since 1.7.0
	 *
	 * @param null|string|array $sections array of section keys or single section key (string).
	 * @return bool success
	 */
	public function set_members_area_sections( $sections = null ) {

		$default_sections = wc_memberships_get_members_area_sections( $this->id );
		$sections         = null === $sections ? array_keys( $default_sections ) : $sections;

		// validate sections
		if ( is_string( $sections ) ) {
			$sections = array_key_exists( $sections, $default_sections ) ? (array) $sections : array();
		} elseif ( ! empty( $sections ) && is_array( $sections ) ) {
			$sections = array_intersect( $sections, array_keys( $default_sections ) );
		} else {
			$sections = array();
		}

		return (bool) update_post_meta( $this->id, $this->members_area_sections_meta, $sections );
	}


	/**
	 * Gets members area sections for this plan.
	 *
	 * @see wc_memberships_get_members_area_sections()
	 *
	 * @since 1.4.0
	 *
	 * @return string[] array of section IDs
	 */
	public function get_members_area_sections() {

		$members_area_sections = get_post_meta( $this->id, $this->members_area_sections_meta, true );

		return is_array( $members_area_sections ) ? $members_area_sections : array();
	}


	/**
	 * Removes the members area sections for this plan.
	 *
	 * @since 1.7.4
	 *
	 * @return bool success
	 */
	public function delete_members_area_sections() {

		return (bool) delete_post_meta( $this->id, $this->members_area_sections_meta );
	}


	/**
	 * Sets the plan's email content.
	 *
	 * @since 1.7.0
	 *
	 * @param array|string $email email to update, or associative array with all emails to update
	 * @param string $content content to set, default empty string
	 * @return bool success
	 */
	public function set_email_content( $email, $content = '' ) {

		$success       = false;
		$emails        = wc_memberships()->get_emails_instance()->get_email_classes();
		$email_content = get_post_meta( $this->id, $this->email_content_meta, true );
		$email_content = ! is_array( $email_content ) ? array() : $email_content;

		if ( is_array( $email ) && ! empty( $email ) ) {

			foreach ( $email as $email_key => $new_content ) {

				// ensure the email class is capitalized
				$email_key = implode( '_', array_map( 'ucfirst', explode( '_', $email_key ) ) );

				if ( isset( $emails[ $email_key ] ) && method_exists( $emails[ $email_key ], 'get_default_body' ) ) {

					$new_content = empty( $new_content ) ? null : trim( $new_content );
					$new_content = empty( $new_content ) ? wp_kses_post( $emails[ $email_key ]->get_default_body() ) : $new_content;

					$email_content[ $email_key ] = $new_content;
				}
			}

			$success = (bool) update_post_meta( $this->id, $this->email_content_meta, $email_content );

		} elseif ( is_string( $email ) ) {

			// ensure the email class is capitalized
			$email = implode( '_', array_map( 'ucfirst', explode( '_', $email ) ) );

			if (    isset( $emails[ $email ] )
			     && method_exists( $emails[ $email ], 'get_default_body' ) ) {

				$new_content = empty( $content ) ? null : trim( $content );
				$new_content = empty( $new_content ) ? wp_kses_post( $emails[ $email ]->get_default_body() ) : $new_content;

				$email_content[ $email ] = $new_content;

				$success = (bool) update_post_meta( $this->id, $this->email_content_meta, $email_content );
			}
		}

		return $success;
	}


	/**
	 * Gets the plan's email content.
	 *
	 * @since 1.7.0
	 *
	 * @param string $email which email content to retrieve
	 * @return string may contain HTML
	 */
	public function get_email_content( $email ) {

		// ensure the email class is capitalized
		$email  = implode( '_', array_map( 'ucfirst', explode( '_', $email ) ) );
		$emails = wc_memberships()->get_emails_instance()->get_email_classes();

		if ( ! isset( $emails[ $email ] ) || ! $emails[ $email ] instanceof \WC_Memberships_User_Membership_Email ) {
			return '';
		}

		$email_content = get_post_meta( $this->id, $this->email_content_meta, true );

		if ( empty( $email_content ) || ! isset( $email_content[ $email ] ) ) {
			$email_content = wc_memberships()->get_emails_instance()->get_email_default_content( $email );
		} else {
			$email_content = is_string( $email_content[ $email ] ) ? $email_content[ $email ] : '';
		}

		return $email_content;
	}


	/**
	 * Deletes the plan's email content.
	 *
	 * @since 1.7.0
	 *
	 * @param string $email email to delete content for, 'all' or 'any' for all
	 * @return bool success
	 */
	public function delete_email_content( $email = '' ) {

		$success = [];
		$emails  = wc_memberships()->get_emails_instance()->get_email_classes();

		if ( in_array( $email, array( 'all', 'any' ), true ) ) {

			$success[] = (bool) delete_post_meta( $this->id, $this->email_content_meta );

		} else {

			// ensure the email class is capitalized
			$email = implode( '_', array_map( 'ucfirst', explode( '_', $email ) ) );

			if ( isset( $emails[ $email ] ) ) {

				$email_content = get_post_meta( $this->id, $this->email_content_meta, true );

				if ( ! empty( $email_content ) && is_array( $email_content ) ) {

					unset( $email_content[ $email ] );

					$success[] = (bool) update_post_meta( $this->id, $this->email_content_meta, $email_content );
				}
			}
		}

		return ! empty( $success ) && ! in_array( false, $success, true );
	}


	/**
	 * Returns the plan's current rules.
	 *
	 * @since 1.0.0
	 *
	 * @param string $rule_type optional rule type: one of 'content_restriction', 'product_restriction' or 'purchasing_discount' (default 'all' to return every rule)
	 * @param bool $edit fetch all rules when editing or only consider rules applicable when a plan is published (default true)
	 * @return \WC_Memberships_Membership_Plan_Rule[]
	 */
	public function get_rules( $rule_type = 'all', $edit = true ) {

		if ( ! isset( $this->rules[ $rule_type ] ) ) {

			$this->rules[ $rule_type ] = array();

			$plan_rules = wc_memberships()->get_rules_instance()->get_plan_rules( $this->id, $edit );

			if ( ! empty( $plan_rules ) ) {
				foreach ( $plan_rules as $rule_id => $plan_rule ) {
					if ( 'all' === $rule_type || $plan_rule->is_type( $rule_type ) ) {
						$this->rules[ $rule_type ][ $rule_id ] = $plan_rule;
					}
				}
			}
		}

		return $this->rules[ $rule_type ];
	}


	/**
	 * Returns a rule that is part of this plan.
	 *
	 * @since 1.9.0
	 *
	 * @param string $rule_id rule alphanumeric identifier
	 * @return null|\WC_Memberships_Membership_Plan_Rule
	 */
	public function get_rule( $rule_id ) {

		$rule = wc_memberships()->get_rules_instance()->get_rule( $rule_id );

		return $rule && $this->id === $rule->get_membership_plan_id() ? $rule : null;
	}


	/**
	 * Returns the plan's content restriction rules.
	 *
	 * @since 1.0.0
	 *
	 * @return \WC_Memberships_Membership_Plan_Rule[]
	 */
	public function get_content_restriction_rules() {
		return $this->get_rules( 'content_restriction', false );
	}


	/**
	 * Returns the plan's product restriction rules.
	 *
	 * @since 1.0.0
	 *
	 * @return \WC_Memberships_Membership_Plan_Rule[]
	 */
	public function get_product_restriction_rules() {
		return $this->get_rules( 'product_restriction', false );
	}


	/**
	 * Returns the plan's purchasing discount rules.
	 *
	 * @since 1.0.0
	 *
	 * @return \WC_Memberships_Membership_Plan_Rule[]
	 */
	public function get_purchasing_discount_rules() {
		return $this->get_rules( 'purchasing_discount', false );
	}


	/**
	 * Sets rules for the plan.
	 *
	 * @since 1.9.0
	 *
	 * @param array|string|\WC_Memberships_Membership_Plan_Rule $rules one or more rules to add or update
	 */
	public function set_rules( $rules ) {

		// if matching rules are found they will be updated instead
		wc_memberships()->get_rules_instance()->add_rules( is_array( $rules ) ? $rules : (array) $rules );

		// clear cached rules
		$this->rules = array();
	}


	/**
	 * Removes rules from the plan.
	 *
	 * If no rules are specified, will delete all rules for this plan.
	 *
	 * @since 1.9.0
	 *
	 * @param null|array|string|\WC_Memberships_Membership_Plan_Rule $rules optional, specific rules to delete or leave null to wipe all
	 * @return bool success
	 */
	public function delete_rules( $rules = null ) {

		if ( null === $rules ) {
			$rules = $this->get_rules();
		}

		$rules        = is_array( $rules ) ? $rules : (array) $rules;
		$success      = false;
		$delete_rules = array();

		foreach ( $rules as $rule ) {

			if ( is_array( $rule ) && isset( $rule['id'] ) ) {
				$rule = wc_memberships()->get_rules_instance()->get_rule( $rule['id'] );
			} elseif ( is_string( $rule ) ) {
				$rule = wc_memberships()->get_rules_instance()->get_rule( $rule );
			}

			// validate if the rule belongs to the plan
			if ( $rule instanceof \WC_Memberships_Membership_Plan_Rule && $this->id === $rule->get_membership_plan_id() ) {
				$delete_rules[] = $rule;
			}
		}

		if ( ! empty( $delete_rules ) ) {

			$success = wc_memberships()->get_rules_instance()->delete_rules( $delete_rules );

			// clear cached rules
			$this->rules = array();
		}

		return $success;
	}


	/**
	 * Compresses and merges similar rules that could be applied to multiple objects.
	 *
	 * As a result deletes redundant rules or discarded duplicates.
	 *
	 * @since 1.9.0
	 */
	public function compact_rules() {

		$compact_rules = $this->get_compact_rules();
		$compact_array = array();
		$all_rules     = $this->get_rules();
		$rules_array   = array();

		// to be safely deleted as a merge result, we need to convert the objects to arrays
		foreach ( $compact_rules as $compact_rule ) {
			$compact_array[ $compact_rule->get_id() ] = $compact_rule->get_raw_data();
		}
		foreach ( $all_rules as $rule ) {
			$rules_array[ $rule->get_id() ] = $rule->get_raw_data();
		}

		$delete_rules = array_diff_key( $rules_array, $compact_array );

		if ( ! empty( $delete_rules ) ) {
			$this->delete_rules( $delete_rules );
		}

		$this->set_rules( $compact_rules );
	}


	/**
	 * Compacts rules meant for general content and product restriction.
	 *
	 * @since 1.9.0
	 *
	 * @return \WC_Memberships_Membership_Plan_Rule[]
	 */
	private function get_compact_rules() {

		$rules         = $this->get_rules(); // array with all rules
		$common_rules  = array();            // temporary array to sort rules temporarily
		$compact_rules = array();            // final array maybe with merged rules

		foreach ( $rules as $rule_id => $rule ) {

			// sanity checks
			if ( $this->id !== $rule->get_membership_plan_id() ) {
				continue;
			} elseif ( $rule->is_new() ) {
				$rule->set_id();
			}

			// Build an array key with property values that might be shared by multiple rules.
			// This leaves out the object IDs (if any) and the membership plan id (presumed to be the same).
			$common_values = http_build_query( array(
				'access_schedule'               => $rule->get_access_schedule(),
				'access_schedule_exclude_trial' => $rule->is_access_schedule_excluding_trial(),
				'access_type'                   => $rule->get_access_type(),
				'active'                        => $rule->is_active(),
				'content_type'                  => $rule->get_content_type(),
				'content_type_name'             => $rule->get_content_type_name(),
				'discount_amount'               => $rule->get_discount_amount(),
				'discount_type'                 => $rule->get_discount_type(),
				'rule_type'                     => $rule->get_rule_type(),
				'meta_data'                     => $rule->get_meta_data(),
			) );

			// Further subdivide rules between those that target higher level content (e.g. a whole taxonomy or post type) and those that target individual objects (posts, terms):
			if ( ! isset( $common_rules[ $common_values ] ) ) {
				$common_rules[ $common_values ] = array( 'parent' => array(), 'children' => array() );
			}
			if ( $rule->has_objects() ) {
				$common_rules[ $common_values ]['children'][] = $rule;
			} else {
				$common_rules[ $common_values ]['parent'][]   = $rule;
			}
		}

		foreach ( $common_rules as $common_group ) {

			/* @type $parent \WC_Memberships_Membership_Plan_Rule[] */
			$parent   = $common_group['parent'];
			/* @type $children \WC_Memberships_Membership_Plan_Rule[] */
			$children = $common_group['children'];

			// If there are rules pertaining higher level content (e.g. a whole post type or taxonomy) then we can discard rules targeting individual objects.
			// Also, if there are multiple parent groups with the same conditions, we can keep only the first one to avoid duplicates.
			if ( ! empty( $parent ) ) {

				$rule = reset( $parent );

				$compact_rules[ $rule->get_id() ] = $rule;

				continue;
			}

			$object_ids = array();

			foreach ( $children as $rule ) {
				$object_ids = array_unique( array_merge( $object_ids, $rule->get_object_ids() ) );
			}

			// Similarly, we don't need multiple copies of the rule with the same condition, we only add more IDs to a single one.
			$rule = reset( $children );

			$rule->set_object_ids( $object_ids );

			$compact_rules[ $rule->get_id() ] = $rule;
		}

		return $compact_rules;
	}


	/**
	 * Returns restricted posts (content or products) for the plan.
	 *
	 * @since 1.4.0
	 *
	 * @param string $type 'content_restriction', 'product_restriction', 'purchasing_discount'
	 * @param int $paged pagination (optional)
	 * @param array $custom_query_args optional arguments to pass while querying posts before returning results
	 * @return null|\WP_Query query results of restricted posts accessible to this membership
	 */
	private function get_restricted( $type, $paged = 1, $custom_query_args = array() ) {

		$query    = null;
		$post_ids = array( array() );
		$rules    = $this->get_rules( $type, false );

		// sanity check
		if ( empty( $rules ) || ! is_array( $rules ) ) {
			return $query;
		}

		foreach ( $rules as $rule ) {

			if ( $rule->is_content_type( 'post_type' ) ) {

				if ( $rule->has_objects() ) {

					// specific posts are restricted for this rule
					$post_ids[] = $rule->get_object_ids();

				} else {

					// all posts of a type are restricted
					$post_ids_query = new \WP_Query( array(
						'fields'    => 'ids',
						'nopaging'  => true,
						'post_type' => $rule->get_content_type_name(),
					) );

					if ( ! empty( $post_ids_query->posts ) ) {
						$post_ids[] = $post_ids_query->posts;
					}
				}

			} elseif ( $rule->is_content_type( 'taxonomy' ) ) {

				$content_type_name = $rule->get_content_type_name();

				if ( ! empty( $content_type_name ) ) {

					if ( ! $rule->has_objects() ) {
						$terms = get_terms( $content_type_name, array(
							'fields' => 'ids',
						) );
					} else {
						$terms = $rule->get_object_ids();
					}

					$taxonomy_post_ids_query = new \WP_Query( array(
						'fields'    => 'ids',
						'nopaging'  => true,
						'tax_query' => array(
							array(
								'taxonomy' => $content_type_name,
								'field'    => 'term_id',
								'terms'    => $terms,
							),
						),
					) );

					if ( ! empty( $taxonomy_post_ids_query->posts ) ) {
						$post_ids[] = $taxonomy_post_ids_query->posts;
					}
				}
			}
		}

		$post_ids = array_unique( array_map( 'absint', array_merge( ...$post_ids ) ) );

		// remove from found results items that are forced public for everyone
		if ( ! empty( $post_ids ) ) {

			foreach ( $post_ids as $index => $post_id ) {

				if ( 'purchasing_discount' === $type && wc_memberships()->get_member_discounts_instance()->is_product_excluded_from_member_discounts( $post_id ) ) {
					unset( $post_ids[ $index ] );
				} elseif ( ( 'content_restriction' === $type || 'product_restriction' === $type ) && wc_memberships()->get_restrictions_instance()->is_post_public( $post_id ) ) {
					unset( $post_ids[ $index ] );
				}
			}
		}

		if ( ! empty( $post_ids ) ) {

			// special handling for products
			if ( 'purchasing_discount' === $type || 'product_restriction' === $type ) {

				// ensure that for variations we list parent variable products
				$post_types = array( 'product' );
				$parent_ids = array();

				/**
				 * Filter to show hidden products when queried from plan.
				 *
				 * @since 1.8.5
				 *
				 * @param bool $exclude_hidden Whether to show products marked hidden from catalog or not (default false: show all products, including hidden ones)
				 */
				$exclude_hidden = (bool) apply_filters( 'wc_memberships_plan_exclude_hidden_products', false );

				foreach ( $post_ids as $post_id ) {

					if ( $product = wc_get_product( $post_id ) ) {

						$product_id = $post_id;

						if ( $exclude_hidden && ! $product->is_visible() ) {
							continue;
						}

						$parent = $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id( 'edit' ) ) : null;

						if ( $parent && $parent->is_type( 'variable' ) ) {

							if ( $exclude_hidden && ! $parent->is_visible() ) {
								continue;
							}

							$parent_id        = $parent->get_id();
							$can_list_product = true;

							// sanity check: maybe a variation is included in this plan
							// but the parent variable product is being restricted
							// by the rules of another plan the user is not member of
							if ( ! in_array( $parent_id, $post_ids, false ) ) {
								$can_list_product = current_user_can( 'wc_memberships_view_restricted_product', $parent_id );
							}

							if ( $can_list_product ) {
								$parent_ids[] = $parent_id;
							}

						} elseif ( $this->has_product_discount( $product ) || ( 'product_restriction' === $type && current_user_can( 'wc_memberships_view_restricted_product', $product_id ) ) ) {

							$parent_ids[] = $product_id;
						}
					}
				}

				$post_ids = array_unique( $parent_ids );

				// remove product ids that should be ignored for discount
				if ( 'purchasing_discount' === $type && ! empty( $post_ids ) ) {
					$post_ids = $this->filter_sale_products_excluded_from_member_discounts( $post_ids );
				}

			} else {

				// avoid use of 'any' in query args, to include post types
				// marked as 'excluded_from_search' which wouldn't be returned
				$post_types = get_post_types( array(
					'public' => true,
				) );
			}

			// sanity check, otherwise WP_Query will return all posts
			if ( ! empty( $post_ids ) ) {

				$query_args = array(
					'post_type'           => $post_types,
					'post__in'            => $post_ids,
					'ignore_sticky_posts' => true,
				);

				if ( $paged > 0 ) {

					$query_args['paged'] = $paged;

				} else {

					$query_args['nopaging']       = true;
					$query_args['posts_per_page'] = -1;
				}

				/**
				 * Filters restricted content query args
				 *
				 * @since 1.6.3
				 *
				 * @param array $query_args args passed to WP_Query
				 * @param string $query_type type of request: 'content_restriction', 'product_restriction', 'purchasing_discount'
				 * @param int $query_paged pagination request
				 */
				$query_args = apply_filters( 'wc_memberships_get_restricted_posts_query_args', array_merge( $query_args, $custom_query_args ), $type, $paged );

				$query = new \WP_Query( $query_args );
			}
		}

		return $query;
	}


	/**
	 * Filters out from an array of products IDs products on sale that should be excluded from discounts.
	 *
	 * @since 1.7.0
	 *
	 * @param int[] $product_ids array of WC_Product post IDs
	 * @return int[] array of product IDs
	 */
	private function filter_sale_products_excluded_from_member_discounts( array $product_ids ) {

		// If we are excluding products on sale from member discounts,
		// we must also check if any of the remainder products are on sale.
		$discounts = wc_memberships()->get_member_discounts_instance();

		if ( $discounts && ! empty( $product_ids ) && ( $exclude_on_sale_products = $discounts->excluding_on_sale_products_from_member_discounts() ) ) {

			foreach ( $product_ids as $product_id ) {

				if ( $exclude_on_sale_products && $discounts->product_is_on_sale_before_discount( $product_id ) ) {

					foreach ( array_keys( $product_ids, $product_id, true ) as $key ) {
						unset( $product_ids[ $key ] );
					}
				}
			}
		}

		return $product_ids;
	}


	/**
	 * Returns the plan's restricted content.
	 *
	 * @since 1.4.0
	 *
	 * @param int $paged pagination (optional)
	 * @param array $args optional arguments to query posts
	 * @return \WP_Query
	 */
	public function get_restricted_content( $paged = 1, $args = array() ) {
		return $this->get_restricted( 'content_restriction', $paged, $args  );
	}


	/**
	 * Returns the plan's restricted products.
	 *
	 * @since 1.4.0
	 *
	 * @param int $paged pagination (optional)
	 * @param array $args optional arguments to query posts
	 * @return \WP_Query
	 */
	public function get_restricted_products( $paged = 1, $args = array() ) {
		return $this->get_restricted( 'product_restriction', $paged, $args  );
	}


	/**
	 * Returns the plan's discounted products.
	 *
	 * @since 1.4.0
	 *
	 * @param int $paged pagination (optional)
	 * @param array $args optional arguments to query posts
	 * @return \WP_Query
	 */
	public function get_discounted_products( $paged = 1, $args = array() ) {
		return $this->get_restricted( 'purchasing_discount', $paged, $args );
	}


	/**
	 * Checks whether the plan offers a discount for the specified product.
	 *
	 * @since 1.7.1
	 *
	 * @param int|\WC_Product $product the product
	 * @return bool
	 */
	public function has_product_discount( $product ) {
		return (bool) $this->get_product_discount( $product );
	}


	/**
	 * Returns a product discount fixed amount or percentage based on the current plan rules.
	 *
	 * @since 1.4.0
	 *
	 * @param int|\WC_Product $product product to check discounts for
	 * @return float|int|string a number as a fixed amount or % percentage amount
	 */
	public function get_product_discount( $product ) {

		$member_discount = '';
		$product_id      = $product instanceof \WC_Product ? $product->get_id() : $product;
		$discount_rules  = wc_memberships()->get_rules_instance()->get_product_purchasing_discount_rules( $product_id );

		foreach ( $discount_rules as $discount_rule ) {

			// only get discounts that match the current membership plan & are active
			if ( $discount_rule->is_active() && $this->id === $discount_rule->get_membership_plan_id() ) {

				switch( $discount_rule->get_discount_type() ) {

					case 'percentage' :
						$member_discount = abs( $discount_rule->get_discount_amount() ) . '%';
					break;

					case 'amount' :
					default :
						$member_discount = abs( $discount_rule->get_discount_amount() );
					break;
				}
			}
		}

		return ! empty( $member_discount ) && ! wc_memberships()->get_member_discounts_instance()->is_product_excluded_from_member_discounts( $product ) ? $member_discount : '';
	}


	/**
	 * Returns the formatted product discount based on current plan rules.
	 *
	 * @see \WC_Memberships_Membership_Plan::get_product_discount()
	 *
	 * @since 1.7.1
	 *
	 * @param \WC_Product|\WC_Product_Variation $product the product object
	 * @return string HTML
	 */
	public function get_formatted_product_discount( $product ) {

		$member_discount = $this->get_product_discount( $product );

		if ( empty( $member_discount ) && ( $child_products = $product->get_children() ) ) {

			// If the product has no discount and it's variable,
			// check if the variations have direct discounts.
			$child_discounts               = array();
			$children_fixed_discounts      = array();
			$children_percentage_discounts = array();

			foreach ( $child_products as $child_product_id ) {

				$child_discount = $this->get_product_discount( $child_product_id );

				if ( ! empty( $child_discount ) ) {

					if ( is_numeric ( $child_discount ) ) {
						$children_fixed_discounts[]      = (float) $child_discount;
					} else {
						$children_percentage_discounts[] = (float) rtrim( $child_discount, '%' );
					}
				}
			}

			if ( ! empty( $children_fixed_discounts ) ) {
				$child_discounts[] = $this->get_product_from_to_discount( $children_fixed_discounts, 'fixed' );
			}

			if ( ! empty( $children_percentage_discounts ) ) {
				$child_discounts[] = $this->get_product_from_to_discount( $children_percentage_discounts, 'percentage' );
			}

			if ( ! empty( $child_discounts ) ) {
				$member_discount = Strings_Helper::get_human_readable_items_list( $child_discounts, 'or' );
			}

		} elseif ( ! empty( $member_discount ) && is_numeric( $member_discount ) ) {

			// format fixed amount discounts
			$member_discount = wc_price( $member_discount );
		}

		return $member_discount;
	}


	/**
	 * Returns a product discounts range (used for variations).
	 *
	 * @see \WC_Memberships_Membership_Plan::get_formatted_product_discount()
	 *
	 * @since 1.7.1
	 *
	 * @param int[]|float[] $discounts array of numbers
	 * @param string $type type of discount range, 'fixed' amount or 'percentage' amount
	 * @return string HTML formatted range
	 */
	private function get_product_from_to_discount( $discounts, $type ) {

		$member_discount = '';
		$min_discount    = min( $discounts );
		$max_discount    = max( $discounts );

		if ( $max_discount > $min_discount ) {

			if ( in_array( $type, array( 'fixed', 'percentage' ), true ) ) {

				$min_discount = 'fixed' === $type ? wc_price( $min_discount ) : $min_discount . '%';
				$max_discount = 'fixed' === $type ? wc_price( $max_discount ) : $max_discount . '%';

				if ( is_rtl() ) {
					$member_discount = $max_discount . '-' . $min_discount;
				} else {
					$member_discount = $min_discount . '-' . $max_discount;
				}
			}

		} elseif ( $min_discount > 0 ) {

			$member_discount = 'fixed' === $type ? wc_price( $min_discount ) : $min_discount . '%';
		}

		return $member_discount;
	}


	/**
	 * Returns related user memberships for the current plan.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args optional arguments to pass to `get_posts()` with defaults
	 * @return int[]|\WC_Memberships_User_Membership[] array of user memberships or user membership IDs
	 */
	public function get_memberships( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'post_type'   => 'wc_user_membership',
			'post_status' => 'any',
			'post_parent' => $this->id,
			'nopaging'    => true,
		) );

		$user_membership_posts = get_posts( $args );
		$user_memberships      = array();

		if ( isset( $args['fields'] ) && 'ids' === $args['fields'] ) {

			$user_memberships = (array) $user_membership_posts;

		} elseif ( ! empty( $user_membership_posts ) ) {

			foreach ( $user_membership_posts as $user_membership_post ) {

				$user_memberships[] = wc_memberships_get_user_membership( $user_membership_post );
			}
		}

		return $user_memberships;
	}


	/**
	 * Returns the number of user memberships related to the current plan.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $status members statuses to count (optional, defaults to 'any')
	 * @return int
	 */
	public function get_memberships_count( $status = 'any' ) {

		$default_statuses = wc_memberships_get_user_membership_statuses( false );

		if ( 'any' === $status ) {
			$status = $default_statuses;
		}

		$statuses    = (array) $status;
		$post_status = array();
		$members     = array();

		if ( ! empty( $statuses ) ) {

			// enforces a 'wcm-' prefix if missing
			foreach ( $statuses as $status_key ) {

				$status_key = Framework\SV_WC_Helper::str_starts_with( $status_key, 'wcm-' ) ? $status_key : 'wcm-' . $status_key;

				if ( in_array( $status_key, $default_statuses, true ) ) {
					$post_status[] = $status_key;
				}
			}
		}

		if ( ! empty( $post_status ) ) {

			$members = get_posts( array(
				'post_type'   => 'wc_user_membership',
				'post_status' => $post_status,
				'post_parent' => $this->id,
				'fields'      => 'ids',
				'nopaging'    => true,
			) );
		}

		return is_array( $members ) ? count( $members ) : 0;
	}


	/**
	 * Checks if the plan has any active user memberships.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_active_memberships() {
		return $this->get_memberships_count( 'active' ) > 0;
	}


	/**
	 * Grants a user access to this plan from a purchase.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id user ID
	 * @param int $product_id product ID
	 * @param int $order_id order ID
	 * @return int|null new/existing User Membership ID or null on failure
	 */
	public function grant_access_from_purchase( $user_id, $product_id, $order_id ) {

		$user_membership_id = null;
		$action             = 'create';
		$product            = is_numeric( $product_id ) ? wc_get_product( $product_id ) : $product_id;
		$order              = is_numeric( $order_id )   ? wc_get_order( $order_id )     : $order_id;

		// sanity check
		if ( ! $product instanceof \WC_Product || ! $order instanceof \WC_Order || ! get_user_by( 'id', $user_id ) ) {
			return null;
		}

		$product_id     = $product->get_id();
		$order_status   = $order->get_status();
		$access_granted = wc_memberships_get_order_access_granted_memberships( $order_id );

		// check if user is perhaps a member, but membership is expired/cancelled
		if (    wc_memberships_is_user_member( $user_id, $this->id, false )
		     && ( $existing_membership = wc_memberships_get_user_membership( $user_id, $this->id ) ) ) {

			$user_membership_id  = $existing_membership->get_id();
			$past_order_id       = $existing_membership->get_order_id();

			// Do not allow the same order to renew or reactivate the membership:
			// this prevents admins changing order statuses from extending/reactivating the membership.
			if ( ! empty( $past_order_id ) && (int) $order_id === $past_order_id ) {

				// however, there is an exception when the intended behaviour
				// is to extend membership length when the option is enabled
				// and the purchase order includes multiple access granting products
				if ( wc_memberships_cumulative_granting_access_orders_allowed() ) {

					if (    isset( $access_granted[ $user_membership_id ] )
					     && $access_granted[ $user_membership_id ]['granting_order_status'] !== $order_status ) {

						// bail if this is an order status change and not a cumulative purchase
						if ( 'yes' === $access_granted[ $user_membership_id ]['already_granted'] ) {

							return null;
						}
					}

				} else {

					return null;
				}
			}

			// otherwise... continue as usual
			$action = 'renew';

			if ( $existing_membership->is_active() || $existing_membership->is_delayed() ) {

				/**
				 * Filter whether an already active (or delayed) membership will be renewed.
				 *
				 * @since 1.0.0
				 *
				 * @param bool $renew whether to renew
				 * @param WC_Memberships_Membership_Plan $plan the current membership plan
				 * @param array $args contextual arguments
				 */
				$renew_membership = apply_filters( 'wc_memberships_renew_membership', (bool) $this->get_access_length_amount(), $this, array(
					'user_id'    => $user_id,
					'product_id' => $product_id,
					'order_id'   => $order_id,
				) );

				if ( ! $renew_membership ) {
					return null;
				}
			}
		}

		// create/update the user membership
		try {
			$user_membership = wc_memberships_create_user_membership( array(
				'user_membership_id' => $user_membership_id,
				'user_id'            => $user_id,
				'product_id'         => $product_id,
				'order_id'           => $order_id,
				'plan_id'            => $this->id,
			), $action );
		} catch ( Framework\SV_WC_Plugin_Exception $e ) {
			return null;
		}

		// Add a membership note.
		if ( 'create' === $action ) {

			$user_membership->add_note(
				/* translators: Placeholders: %1$s - product name, %2$s - order number. */
				sprintf( __( 'Membership access granted from purchasing %1$s (Order %2$s)', 'woocommerce-memberships' ),
					$product->get_title(),
					$order->get_order_number()
				)
			);

		} elseif ( 'renew' === $action ) {

			// Do not bother if the membership is fixed and is ended.
			if ( ! ( $this->is_access_length_type( 'fixed' ) && ! $user_membership->is_active() && ! $user_membership->is_delayed() ) ) {

				$user_membership->add_note(
					/* translators: Placeholders: %1$s - product name, %2$s - order number. */
					sprintf( __( 'Membership access renewed from purchasing %1$s (Order %2$s)', 'woocommerce-memberships' ),
						$product->get_title(),
						$order->get_order_number()
					)
				);
			}
		}

		// save a post meta with the initial order status to check for later order status changes
		if ( ! isset( $access_granted[ $user_membership->get_id() ] ) ) {

			wc_memberships_set_order_access_granted_membership( $order, $user_membership, array(
				'already_granted'       => 'yes',
				'granting_order_status' => $order_status,
			) );
		}

		/**
		 * Fires after a user has been granted membership access from a purchase.
		 *
		 * @since 1.0.0
		 *
		 * @param \WC_Memberships_Membership_Plan $membership_plan the plan that user was granted access to
		 * @param array $args contextual arguments
		 */
		do_action( 'wc_memberships_grant_membership_access_from_purchase', $this, array(
			'user_id'            => $user_id,
			'product_id'         => $product_id,
			'order_id'           => $order_id,
			'user_membership_id' => $user_membership->get_id(),
		) );

		return $user_membership->get_id();
	}


}
