<?php
/**
 * WooCommerce Product Reviews Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Contribution Flag object.
 *
 * A contribution may be flagged as inappropriate by customers.
 * The object is a wrapper for a contribution (comment) meta.
 *
 * @since 1.10.0
 */
class WC_Product_Reviews_Pro_Contribution_Flag {


	/** @var int the contribution's own ID */
	private $contribution_id;

	/** @var string the unique identifier of a flag (alphanumeric) */
	private $id;

	/** @var int the ID of the customer that created the flag */
	private $user_id;

	/** @var \WP_User|null user object of the customer that created the flag */
	private $user;

	/** @var string the flag reason set by the customer*/
	private $reason;

	/** @var bool whether the flag has been resolved by an admin */
	private $resolved;

	/** @var int timestamp of the flag at the time it was submitted (in UTC) */
	private $timestamp;

	/** @var string IP address of the user that flagged the contribution at the time of submission */
	private $ip;


	/**
	 * Flag constructor.
	 *
	 * @since 1.10.0
	 *
	 * @param int $contribution_id the contribution ID
	 * @param array $flag_data raw data
	 */
	public function __construct( $contribution_id, array $flag_data ) {

		$this->id              = key( $flag_data );
		$this->contribution_id = $contribution_id;

		$flag_data = current( $flag_data );

		$this->user_id   = isset( $flag_data['user_id'] )   && is_numeric( $flag_data['user_id'] )   ? (int) $flag_data['user_id']               : 0;
		$this->reason    = isset( $flag_data['reason'] )    && is_string( $flag_data['reason'] )     ? wp_strip_all_tags( $flag_data['reason'] ) : '';
		$this->resolved  = isset( $flag_data['resolved'] )  && is_bool( $flag_data['resolved'] )     ? $flag_data['resolved']                    : false;
		$this->timestamp = isset( $flag_data['timestamp'] ) && is_numeric( $flag_data['timestamp'] ) ? (int) $flag_data['timestamp']             : current_time( 'timestamp', true );
		$this->ip        = isset( $flag_data['ip'] )        && is_string( $flag_data['ip'] )         ? $flag_data['ip']                          : '';
	}


	/**
	 * Returns the flagged contribution's ID.
	 *
	 * @since 1.10.0
	 *
	 * @return int
	 */
	public function get_contribution_id() {

		return $this->contribution_id;
	}


	/**
	 * Returns the ID of the flag.
	 *
	 * @since 1.10.0
	 *
	 * @return string alphanumeric string
	 */
	public function get_id() {

		return is_string( $this->id ) ? $this->id : uniqid( '', false );
	}


	/**
	 * Set the ID of the flag.
	 *
	 * @since 1.10.0
	 *
	 * @param null|string $id optional: will generate the flag ID with `uniqid` when unspecified (recommended)
	 */
	public function set_id( $id = null ) {

		if ( null === $id ) {
			$id = uniqid( '', false );
		}

		$this->id = $id;
	}


	/**
	 * Returns the timestamp when the flag was set.
	 *
	 * @since 1.10.0
	 *
	 * @return int
	 */
	public function get_timestamp() {

		return ! $this->timestamp ? current_time( 'timestamp', true ) : (int) $this->timestamp;
	}


	/**
	 * Returns the date when the timestamp was set.
	 *
	 * @since 1.10.0
	 *
	 * @param string $format either 'timestamp', 'mysql' or any valid PHP date format string
	 * @param bool $utc whether to return the date in UTC or localized according to the site current timezone (default)
	 * @return int|string
	 */
	public function get_date( $format = 'mysql', $utc = false ) {

		$date      = '';
		$timestamp = $this->get_timestamp();
		$offset    = wc_timezone_offset();

		if ( 'timestamp' === $format ) {

			$date = $timestamp;

			if ( ! $utc ) {
				$date += $offset;
			}

		} elseif ( is_string( $format ) ) {

			$format = 'mysql' === $format ? 'Y-m-d H:i:s' : $format;
			$date   = date_i18n( $format, $this->get_timestamp() + ( ! $utc ? $offset : 0 ) );
		}

		return $date;
	}


	/**
	 * Returns the time when the timestamp was set.
	 *
	 * @see \WC_Product_Reviews_Pro_Contribution_Flag::get_date() alias
	 *
	 * @since 1.10.0
	 *
	 * @param string $format either 'timestamp', 'mysql' or any valid PHP time format string
	 * @param bool $utc whether to return the time in UTC or localized according to the site current timezone (default)
	 * @return int|string
	 */
	public function get_time( $format = 'mysql', $utc = false ) {

		return $this->get_date( $format, $utc );
	}


	/**
	 * Returns the flagging user ID.
	 *
	 * @since 1.10.0
	 *
	 * @return int 0 if the flag was set by a guest
	 */
	public function get_user_id() {

		return $this->user_id;
	}


	/**
	 * Sets the flagging user ID.
	 *
	 * @since 1.10.0
	 *
	 * @param int $user_id
	 */
	public function set_user_id( $user_id ) {

		if ( is_numeric( $user_id ) ) {

			$this->user_id = (int) $user_id;
		}
	}


	/**
	 * Returns the flagging user object.
	 *
	 * @since 1.10.0
	 *
	 * @return null|\WP_User returns null if the flag was set by a guest
	 */
	public function get_user() {

		if ( null === $this->user ) {
			$this->user = $this->user_id > 0 ? get_user_by( 'id', $this->user_id ) : null;
		}

		return $this->user instanceof \WP_User ? $this->user : null;
	}


	/**
	 * Returns the formatted name of the reporting user.
	 *
	 * @since 1.10.0
	 *
	 * @param bool $html whether HTML should be allowed in the output (default true)
	 * @return string formatted name (may contain HTML)
	 */
	public function get_user_display_name( $html = true ) {

		if ( $this->is_anonymous() ) {

			/* translators: Placeholder: %s - IP address of the visitor in parenthesis */
			$display_name = sprintf( strtolower( __( 'Visitor %s', 'woocommerce-product-reviews-pro' ) ), $this->has_ip() ? '(' . $this->get_ip() . ')' : '' );

		} else {

			/* @see \get_edit_user_link() it is not safe to use this function straight because if the current method is called while in email context, that function may check for current user ID for capability and necessarily return false */
			$edit_user_url = add_query_arg( 'user_id', $this->get_user_id(), self_admin_url( 'user-edit.php' ) );

			if ( false !== $html ) {
				$display_name = '<a href="' . esc_url( $edit_user_url ) . '">' . strtolower( __( 'Customer', 'woocommerce-product-reviews-pro' )  ) . '</a>';
			} else {
				/* translators: Placeholder: %s - customer's profile edit screen URL in parenthesis */
				$display_name = sprintf( strtolower( __( 'Customer %s', 'woocommerce-product-reviews-pro' ) ), '(' . esc_url( $edit_user_url ) . ')' );
			}
		}

		/**
		 * Filters the flagged contribution reporting user's name.
		 *
		 * @since 1.10.0
		 *
		 * @param string $display_name formatted display name
		 * @param \WC_Product_Reviews_Pro_Contribution_Flag $flag flag object
		 * @param bool $html whether the output may include HTML
		 */
		return (string) apply_filters( 'wc_product_reviews_pro_flagged_contribution_user_display_name', $display_name, $this, $html );
	}


	/**
	 * Returns the reporting user's email address.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_user_email() {

		return ! $this->is_anonymous() ? $this->get_user()->user_email : '';
	}


	/**
	 * Returns the IP address of the user that created the flag.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_ip() {

		return $this->ip;
	}


	/**
	 * Checks whether the flag has a set IP address of the submitter.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function has_ip() {

		$ip = trim( $this->get_ip() );

		return ! empty( $ip );
	}


	/**
	 * Checks whether the flag was anonymous
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function is_anonymous() {

		return null === $this->get_user();
	}


	/**
	 * Returns the flagging reason.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_reason() {

		return is_string( $this->reason ) ? $this->reason : '';
	}


	/**
	 * Checks whether a reason was left for this flag.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function has_reason() {

		$reason = trim( $this->get_reason() );

		return ! empty( $reason );
	}


	/**
	 * Sets a reason for flagging.
	 *
	 * @since 1.10.0
	 *
	 * @param string $reason reason given for flagging
	 */
	public function set_reason( $reason ) {

		if ( is_string( $reason ) ) {
			$this->reason = wp_strip_all_tags( $reason );
		}
	}


	/**
	 * Deletes the reason for flagging.
	 *
	 * @since 1.10.0
	 */
	public function delete_reason() {

		$this->reason = '';
	}


	/**
	 * Returns the resolved status.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	private function get_resolved_status() {

		return (bool) $this->resolved;
	}


	/**
	 * Returns whether the flag has been marked as resolved.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function is_resolved() {

		return true === $this->get_resolved_status();
	}


	/**
	 * Returns whether the flag has been marked as unresolved.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function is_unresolved() {

		return false === $this->get_resolved_status();
	}


	/**
	 * Mark the flag as resolved.
	 *
	 * @since 1.10.0
	 */
	public function mark_resolved() {

		$this->resolved = true;
	}


	/**
	 * Mark the flag as unresolved.
	 *
	 * @since 1.10.0
	 */
	public function mark_unresolved() {

		$this->resolved = false;
	}


	/**
	 * Returns an array with flag raw data.
	 *
	 * @since 1.10.0
	 *
	 * @return array
	 */
	public function get_raw_data() {

		return array(
			$this->get_id() => array(
				'user_id'   => $this->get_user_id(),
				'reason'    => $this->get_reason(),
				'resolved'  => $this->get_resolved_status(),
				'timestamp' => $this->get_timestamp(),
				'ip'        => $this->get_ip(),
			)
		);
	}


	/**
	 * Saves the contribution flag.
	 *
	 * @since 1.10.0
	 *
	 * @return bool success status
	 */
	public function save() {

		$comment_id = $this->get_contribution_id();
		$flags_data = get_comment_meta( $comment_id, 'flags', true );

		return (bool) $comment_id > 0 ? update_comment_meta( $comment_id, 'flags', array_merge( is_array( $flags_data ) ? $flags_data : array(), $this->get_raw_data() ) ) : false;
	}


	/**
	 * Deletes the current flag from the contribution's data.
	 *
	 * @since 1.0.0
	 *
	 * @return bool success status
	 */
	public function delete() {

		$success    = false;
		$comment_id = $this->get_contribution_id();

		if ( $comment_id > 0 ) {

			$flags_data = get_comment_meta( $comment_id, 'flags', true );

			if ( ! empty( $flags_data ) && is_array( $flags_data ) ) {

				unset( $flags_data[ $this->get_id() ] );

				$success = update_comment_meta( $comment_id, 'flags', $flags_data );
			}
		}

		return (bool) $success;
	}


}
