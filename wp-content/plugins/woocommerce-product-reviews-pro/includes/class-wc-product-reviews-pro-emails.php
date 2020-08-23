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
 * Product Reviews Pro Emails class
 *
 * This class handles all email-related functionality in Product Reviews Pro.
 *
 * @since 1.3.0
 */
class WC_Product_Reviews_Pro_Emails {


	/**
	 * Sets up Product Reviews Pro emails.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {

		// hook in WC Emails
		add_filter( 'woocommerce_email_classes', array( $this, 'get_emails' ), 10, 1 );

		/* @see \WC_Product_Reviews_Pro_Emails::comment_notification() */
		add_action( 'wc_product_reviews_pro_new_comment_email',                array( 'WC_Emails', 'send_transactional_email' ), 10, 4 );
		/* @see \WC_Product_Reviews_Pro_AJAX::review_update_confirmation() */
		add_action( 'wc_product_reviews_pro_review_update_confirmation_email', array( 'WC_Emails', 'send_transactional_email' ), 10, 3 );
		/* @see \WC_Contribution::flag() */
		add_action( 'wc_product_reviews_pro_flagged_contribution_email',       array( 'WC_Emails', 'send_transactional_email' ), 10, 2 );

		// hook in WP comments for contribution replies notifications
		add_action( 'wp_insert_comment',     array( $this, 'comment_notification' ), 50, 2 );
		add_action( 'wp_set_comment_status', array( $this, 'comment_status_change' ), 50, 2 );

		// process unsubscribe request from emails
		add_action( 'woocommerce_init', array( $this, 'comment_notifications_unsubscribe_request' ) );

		// process review update confirmation request from emails
		add_action( 'init', array( $this, 'review_update_confirmation_request' ) );
	}


	/**
	 * Returns class names and paths.
	 *
	 * @since 1.10.0
	 *
	 * @return array
	 */
	private function get_classes() {

		return array(
			'WC_Product_Reviews_Pro_Emails_New_Comment'                => '/includes/emails/class-wc-product-reviews-pro-emails-new-comment.php',
			'WC_Product_Reviews_Pro_Emails_Review_Update_Confirmation' => '/includes/emails/class-wc-product-reviews-pro-emails-review-update-confirmation.php',
			'WC_Product_Reviews_Pro_Emails_Flagged_Contribution'       => '/includes/emails/class-wc-product-reviews-pro-emails-flagged-contribution.php',
		);
	}


	/**
	 * Returns Product Reviews Pro email objects.
	 *
	 * @since 1.10.0
	 *
	 * @param array $emails array of email objects
	 * @return array
	 */
	public function get_emails( $emails = array() ) {

		// applies when this method is called directly and not as WooCommerce hook callback
		if ( empty( $emails ) && ! class_exists( 'WC_Email' ) ) {
			wc()->mailer();
		}

		$plugin_path = wc_product_reviews_pro()->get_plugin_path();

		foreach ( $this->get_classes() as $class_name => $class_path ) {

			$file = $plugin_path . $class_path;

			if ( is_readable( $file ) ) {

				require_once( $file );

				if ( class_exists( $class_name ) ) {

					$emails[ $class_name ] = new $class_name();
				}
			}
		}

		return $emails;
	}


	/**
	 * Returns an email instance.
	 *
	 * @since 1.10.0
	 *
	 * @param string $which email class name
	 * @return null|\WC_Product_Reviews_Pro_Emails_New_Comment|\WC_Product_Reviews_Pro_Emails_Review_Update_Confirmation|\WC_Product_Reviews_Pro_Emails_Flagged_Contribution
	 */
	public function get_email( $which ) {

		$emails = $this->get_emails();

		return isset( $emails[ $which ] ) ? $emails[ $which ] : null;
	}


	/**
	 * Hooks wp_insert_comment for new comments that are approved
	 *
	 * Sends email notifications to subscribers of replies to product contribution comments
	 *
	 * @since 1.3.0
	 * @param int $comment_id
	 * @param \WP_Comment $comment
	 */
	public function comment_notification( $comment_id, $comment ) {

		// Only fired for replies that have a top level comment
		if ( 1 === (int) $comment->comment_approved && $comment->comment_parent > 0 ) {

			$product = wc_get_product( $comment->comment_post_ID );

			if ( $product ) {

				$contribution = wc_product_reviews_pro_get_contribution( $comment );

				$top_level  = wc_product_reviews_pro_get_top_level_contribution( $contribution );
				$users      = get_comment_meta( $top_level->id, 'wc_product_reviews_pro_notify_users', true );
				$emails		= get_comment_meta( $top_level->id, 'wc_product_reviews_pro_notify_emails', true );

				$users	= ! empty( $users )  ? $users  : array();
				$emails	= ! empty( $emails ) ? $emails : array();

				// merge comment watchers
				$new_users = array_merge( $users, $emails );

				if ( ! empty( $new_users ) ) {

					do_action( 'wc_product_reviews_pro_new_comment_email', $new_users, $product, $top_level, $contribution );
				}
			}
		}
	}


	/**
	 * Hooks wp_set_comment_status when a comment status changes to 'approve'
	 *
	 * Sends email notifications to comment subscribers upon comment approval
	 *
	 * @since 1.3.0
	 * @param int $comment_id
	 * @param string $comment_status
	 */
	public function comment_status_change( $comment_id, $comment_status ) {

		if ( 'approve' === $comment_status ) {

			$comment = get_comment( $comment_id );

			if ( isset( $comment->comment_ID ) ) {

				$this->comment_notification( $comment->comment_ID, $comment );
			}
		}
	}


	/**
	 * Process link to unsubscribe from new comment notifications
	 *
	 * @since 1.3.0
	 */
	public function comment_notifications_unsubscribe_request() {

		if ( isset( $_GET['wc_prp_comments_notifications'], $_GET['user'], $_GET['contribution'], $_GET['type'] ) ) {

			if ( 'unsubscribe' !== $_GET['wc_prp_comments_notifications'] ) {
				return;
			}

			$user			 = $_GET['user'];
			$contribution_id = (int) $_GET['contribution'];
			$contribution    = wc_product_reviews_pro_get_contribution( $contribution_id );
			$type			 = $_GET['type'];

			$result = wc_product_reviews_pro_add_comment_notification_subscriber( 'unsubscribe', $user, $contribution, $type );

			if ( null !== $result ) {
				wc_add_notice( __( 'You will be no longer receiving email notifications for comments on the review you had subscribed to.', 'woocommerce-product-reviews-pro' ), 'success' );
			} else {
				wc_add_notice( __( 'An error occurred. Your request could not be processed.', 'woocommerce-product-reviews-pro' ), 'error' );
			}
		}
	}


	/**
	 * Process link to update review from confirmation mail.
	 *
	 * @since 1.8.0
	 */
	public function review_update_confirmation_request() {

		if ( isset( $_GET['wc_prp_update_review'], $_GET['user'], $_GET['contribution'], $_GET['_wpnonce'] ) ) {

			if ( 'update' !== $_GET['wc_prp_update_review'] ) {
				return;
			}

			if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'wc_prp_update_review_' . wc_clean( $_GET['contribution'] ) ) ) {

				wc_add_notice( __( 'You have taken too long, please go back and try again.', 'woocommerce-product-reviews-pro' ), 'error' );

				return;
			}

			$data = get_comment_meta( $_GET['contribution'], 'new_review_data', true );

			if ( empty( $data ) || ( ( (int) $data['comment_id'] !== (int) $_GET['contribution'] ) && ( $data['user_id'] !== $_GET['user'] ) ) ) {

				wc_add_notice( __( 'An error occurred. Your request could not be processed.', 'woocommerce-product-reviews-pro' ), 'error' );

				return;
			}

			$update = wc_product_reviews_pro_update_review_data( $data );

			if ( true === $update ) {
				wc_add_notice( __( 'Review updated successfully!', 'woocommerce-product-reviews-pro' ), 'success' );
			} else {
				wc_add_notice( __( 'An error occurred. Your request could not be processed.', 'woocommerce-product-reviews-pro' ), 'error' );
			}
		}
	}


}
