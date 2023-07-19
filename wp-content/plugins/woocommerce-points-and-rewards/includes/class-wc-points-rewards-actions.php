<?php
/**
 * WooCommerce Points and Rewards
 *
 * @package     WC-Points-Rewards/Classes
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Internal\Admin\ProductReviews\ReviewsUtil;

/**
 * # WooCommerce Core Actions Integration Class
 *
 * This class adds the WooCommerce core actions of product review and user
 * account registration as point earning actions.  This also provides a sample
 * integration for 3rd party plugins to follow to add their own custom point
 * reward actions.
 */
class WC_Points_Rewards_Actions {


	/**
	 * Initialize the WooCommerce core Points & Rewards integration class
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// add the WooCommerce core action settings
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			add_filter( 'wc_points_rewards_action_settings', array( $this, 'points_rewards_action_settings' ), 1 );
		}

		// add the WooCommerce core actions event descriptions
		add_filter( 'wc_points_rewards_event_description', array( $this, 'add_action_event_descriptions' ), 10, 3 );

		// add points for user signup & writing a review
		add_action( 'comment_post', array( $this, 'product_review_action' ), 10, 2 );
		add_action( 'comment_unapproved_to_approved', array( $this, 'product_review_approve_action' ) );
		add_action( 'user_register', array( $this, 'create_account_action' ) );
		add_action( 'comment_unapproved_review', array( $this, 'product_review_unapproved_action' ), 10, 2 );
	}


	/**
	 * Adds the WooCommerce core actions integration settings
	 *
	 * @since 1.0
	 * @param array $settings the settings array
	 * @return array the settings array
	 */
	public function points_rewards_action_settings( $settings ) {

		$settings = array_merge(
			$settings,
			array(
				array(
					'title'    => __( 'Points earned for account signup', 'woocommerce-points-and-rewards' ),
					'desc_tip' => __( 'Enter the amount of points earned when a customer signs up for an account.', 'woocommerce-points-and-rewards' ),
					'id'       => 'wc_points_rewards_account_signup_points',
					'type'     => 'number',
					'custom_attributes' => array(
						'min'  => '0',
						'step' => '0.01',
					),
					'css'      => 'max-width:70px;',
				),

				array(
					'title'    => __( 'Points earned for writing a review', 'woocommerce-points-and-rewards' ),
					'desc_tip' => __( 'Enter the amount of points earned when a customer first reviews a product.', 'woocommerce-points-and-rewards' ),
					'id'       => 'wc_points_rewards_write_review_points',
					'type'     => 'number',
					'custom_attributes' => array(
						'min'  => '0',
						'step' => '0.01',
					),
					'css'      => 'max-width:70px;',
				),
			)
		);

		return $settings;
	}


	/**
	 * Provides an event description if the event type is one of 'product-review' or
	 * 'account-signup'
	 *
	 * @since 1.0
	 * @param string $event_description the event description
	 * @param string $event_type the event type
	 * @param object $event the event log object, or null
	 * @return string the event description
	 */
	public function add_action_event_descriptions( $event_description, $event_type, $event ) {
		global $wc_points_rewards;

		$points_label = $wc_points_rewards->get_points_label( $event ? $event->points : null );

		// set the description if we know the type
		switch ( $event_type ) {
			case 'product-review': $event_description = sprintf( __( '%s earned for product review', 'woocommerce-points-and-rewards' ), $points_label ); break;
			case 'account-signup': $event_description = sprintf( __( '%s earned for account signup', 'woocommerce-points-and-rewards' ), $points_label ); break;
		}

		return $event_description;
	}

	/**
	 * Determine if a reward point can be applied to a review.
	 *
	 * A reward point can be applied to a review only if:
	 * - reward points are enabled for review.
	 * - it is a review and not a comment.
	 * - it is the only approved review of the user for the product.
	 *
	 * @param WP_Comment|string|int $comment Comment to determine.
	 * @return bool
	 */
	public function can_reward_review( $comment = null ) {
		// Check if rewards are awared for reviews.
		$points = get_option( 'wc_points_rewards_write_review_points' );
		if ( empty( $points ) || ! is_numeric( $points ) ) {
			return false;
		}

		// Check if the comment is of type review.
		$comment = get_comment( $comment );
		if ( is_null( $comment ) || ! is_a( $comment, WP_Comment::class ) || 'review' !== $comment->comment_type || empty( $comment->user_id ) ) {
			return false;
		}

		// Check if there are no approved reviews for the customer already.
		$can_reward = false;
		$args       = array(
			'user_id' => $comment->user_id,
			'count'   => true,
		);

		/**
		 * The `comments_clauses_without_product_reviews` filter adds additional joins and where clauses
		 * causing the `get_approved_comments` function to return 0 on bulk approval. Hence, we remove this filter
		 * before the `get_approved_comments` function call and add it back for the rest of the execution.
		 */
		remove_filter( 'comments_clauses', array( ReviewsUtil::class, 'comments_clauses_without_product_reviews' ), 10, 1 );
		$reviews_count = get_approved_comments( $comment->comment_post_ID, $args );
		add_filter( 'comments_clauses', array( ReviewsUtil::class, 'comments_clauses_without_product_reviews' ), 10, 1 );

		// ReviewsUtil is a class from WooCommerce internal namespace. We'd want to log if that class no longer exists.
		if ( ! class_exists( ReviewsUtil::class ) ) {
			wc_points_rewards()->log( esc_html__( 'ReviewsUtil class not found', 'woocommerce-points-and-rewards' ) );
		}

		// Points are rewarded after the review is approved. Hence there should be only one approved review.
		if ( 1 === $reviews_count ) {
			$can_reward = true;
		}

		/**
		 * Filter to determine if a reward point can be applied to a review.
		 *
		 * @since 1.7.37
		 *
		 * @param bool       $can_reward Whether a reward point can be applied to a review.
		 * @param WP_Comment $comment    Review to evaluate.
		 */
		return apply_filters( 'wc_points_rewards_can_reward_review', $can_reward, $comment );
	}

	/**
	 * Add points to customer for posting a product review
	 *
	 * @since 1.0
	 *
	 * @param int        $comment_id The comment ID.
	 * @param int|string $approved   1 if the comment is approved, 0 if not, 'spam' if spam.
	 */
	public function product_review_action( $comment_id, $approved = 0 ) {
		if ( ! is_user_logged_in() || ! $approved ) {
			return;
		}

		$comment    = get_comment( $comment_id );
		$can_reward = $this->can_reward_review( $comment );

		if ( $can_reward ) {
			$points = intval( get_option( 'wc_points_rewards_write_review_points' ) );
			$this->reward_points_for_review( $comment_id, get_current_user_id(), $points, array( 'product_id' => get_the_ID() ) );
		}
	}

	/**
	 * Triggered when a comment is approved
	 *
	 * @param WP_Comment $comment Comment object.
	 */
	public function product_review_approve_action( $comment ) {
		$can_reward = $this->can_reward_review( $comment );

		if ( $can_reward ) {
			$points = intval( get_option( 'wc_points_rewards_write_review_points' ) );
			$this->reward_points_for_review( $comment->comment_ID, $comment->user_id, $points, array( 'product_id' => $comment->comment_post_ID ) );
		}
	}

	/**
	 * Reward points for a review.
	 *
	 * @param string $comment_id The comment ID as a numeric string.
	 * @param string $user_id    The user ID as a numeric string.
	 * @param int    $points     Number of points to award.
	 * @param mixed  $data       Arbitrary data associated with the log.
	 */
	private function reward_points_for_review( $comment_id, $user_id, $points, $data ) {
		$was_rewarded = WC_Points_Rewards_Manager::increase_points( $user_id, $points, 'product-review', $data );
		// If points was rewarded, then add the awarded points to the review meta.
		if ( $was_rewarded ) {
			update_comment_meta( $comment_id, 'wc_points_reward_points_rewarded', $points );
		}
	}

	/**
	 * Triggered when a review is unapproved.
	 *
	 * @param string     $comment_id The comment ID as a numeric string.
	 * @param WP_Comment $comment    Comment object.
	 */
	public function product_review_unapproved_action( $comment_id, $comment ) {
		$points = intval( get_comment_meta( $comment_id, 'wc_points_reward_points_rewarded', true ) ); // Get the points rewarded for this review.
		if ( $points <= 0 ) {
			return;
		}

		$was_removed = WC_Points_Rewards_Manager::decrease_points( $comment->user_id, $points, 'product-review', array( 'product_id' => $comment->comment_post_ID ) );
		// Update the awarded points to zero after removing the points.
		if ( $was_removed ) {
			update_comment_meta( $comment_id, 'wc_points_reward_points_rewarded', 0 );
		}
	}

	/**
	 * Add points to customer for creating an account
	 *
	 * @since 1.0
	 */
	public function create_account_action( $user_id ) {
		$points = get_option( 'wc_points_rewards_account_signup_points' );

		if ( ! empty( $points ) )
			WC_Points_Rewards_Manager::increase_points( $user_id, $points, 'account-signup' );
	}


}
