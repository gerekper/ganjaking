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
 * Abstract contribution object.
 *
 * The class names for contributions do not follow standard conventions for the extension.
 * This is because originally these were intended to be merged into WooCommerce core.
 * The current naming convention as been kept for backwards compatibility.
 *
 * @since 1.0.0
 */
abstract class WC_Contribution {


	/** @var int Contribution (comment) ID */
	public $id;

	/** @var int related Product (post) ID */
	public $product_id;

	/** @var string contributor name */
	public $contributor_name;

	/** @var string contributor email */
	public $contributor_email;

	/** @var string contributor IP */
	public $contributor_ip;

	/** @var string contribution date */
	public $contribution_date;

	/** @var string contribution date in gmt */
	public $contribution_date_gmt;

	/** @var string contribution content (text) */
	public $content;

	/** @var int net contribution vote, used as a roll-up of the sum of positive_votes/negative_votes meta in order to improve sort performance */
	public $karma;

	/** @var int contribution moderation status (0 = not approved, 1 = approved, 2 = flagged as inappropriate) */
	public $moderation;

	/** @var string contribution type */
	public $type;

	/** @var int contribution parent (used for threaded comments) */
	public $parent;

	/** @var int contributor (user) ID (if contributor was logged in) */
	public $contributor_id;

	/** @var string contribution_title (not used for questions or comments) */
	public $title;

	/** @var float numeric rating (only used for reviews) */
	public $rating;

	/** @var int number of positive votes */
	public $positive_votes;

	/** @var int number of negative votes */
	public $negative_votes;

	/** @var int number of times this contribution flagged inappropriate */
	public $flag_count;

	/** @var int attached media ID (not used for comments) */
	public $attachment_id;

	/** @var string attached media URL (not used for comments) */
	public $attachment_url;

	/** @var string attached media type (not used for comments) */
	public $attachment_type;

	/** @var \WP_Comment the actual comment object **/
	public $comment;

	/** @var string Failure message for vote and flag methods **/
	private $_failure_message;


	/**
	 * Gets the comment object and sets the ID for the loaded contribution.
	 *
	 * @since 1.0.0
	 *
	 * @param int|\WC_Contribution|\WP_Comment $contribution contribution ID, comment object, or contribution object
	 */
	public function __construct( $contribution ) {

		if ( is_numeric( $contribution ) ) {

			$this->id      = absint( $contribution );
			$this->comment = get_comment( $this->id );

		} elseif ( $contribution instanceof self ) {

			$this->id      = absint( $contribution->id );
			$this->comment = $contribution->comment;

		} elseif ( $contribution instanceof \WP_Comment || isset( $contribution->comment_ID ) ) {

			$this->id      = absint( $contribution->comment_ID );
			$this->comment = $contribution;

		}

		// populate contribution data from database
		if ( $this->comment ) {

			$this->populate();
		}
	}


	/**
	 * Populates a contribution from the database.
	 *
	 * @since 1.0.0
	 */
	private function populate() {

		// Load comment data from database
		$comment = $this->get_comment_data();

		// Bail out if comment data is not available
		if ( ! $comment ) {
			return;
		}

		// Standard comment data
		$this->id                    = $comment->comment_ID;
		$this->product_id            = $comment->comment_post_ID;
		$this->contributor_name      = $comment->comment_author;
		$this->contributor_email     = $comment->comment_author_email;
		$this->contributor_ip        = $comment->comment_author_IP;
		$this->contribution_date     = $comment->comment_date;
		$this->contribution_date_gmt = $comment->comment_date_gmt;
		$this->content               = $comment->comment_content;
		$this->karma                 = $comment->comment_karma;
		$this->moderation            = $comment->comment_approved;
		$this->type                  = $comment->comment_type;
		$this->parent                = $comment->comment_parent;
		$this->contributor_id        = $comment->user_id;

		/**
		 * Filters meta keys to load contribution data from.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] $keys array of comment meta keys to load contribution data from
		 */
		$meta_keys = (array) apply_filters( "wc_contribution_{$this->type}_load_meta_keys", array(
			'title',
			'rating',
			'positive_votes',
			'negative_votes',
			'flag_count',
			'attachment_id',
			'attachment_url',
			'attachment_type',
		) );

		foreach ( $meta_keys as $key ) {

			$this->{$key} = get_comment_meta( $this->id, $key, true );
		}
	}


	/**
	 * Returns the contribution's comment data.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Comment
	 */
	public function get_comment_data() {

		return $this->comment;
	}


	/**
	 * Returns the contribution's ID.
	 *
	 * @since 1.10.0
	 *
	 * @return int
	 */
	public function get_id() {

		return $this->id;
	}


	/**
	 * Returns the contribution parent.
	 *
	 * TODO this should be renamed as get_parent_id() as get_parent() is misleading hinting it may return a contribution object and not a comment ID {FN 2018-02-06}
	 *
	 * @since 1.10.0
	 *
	 * @return int
	 */
	public function get_parent() {

		return $this->parent;
	}


	/**
	 * Checks if the contribution has a parent.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function has_parent() {

		return (int) $this->parent > 0;
	}


	/**
	 * Returns the contribution's type.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_type() {

		return $this->type;
	}


	/**
	 * Determines if this contribution is of the provided type.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $type contribution type or types to compare against
	 * @return bool
	 */
	public function is_type( $type ) {

		return is_array( $type ) ? in_array( $this->get_type(), $type, true ) : $type === $this->get_type();
	}


	/**
	 * Returns the contribution title.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_title() {

		return $this->title;
	}


	/**
	 * Checks whether the title is not empty.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function has_title() {

		return ! empty( $this->title );
	}


	/**
	 * Returns the contribution's content.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_content() {

		return $this->content;
	}


	/**
	 * Returns the related product ID.
	 *
	 * @since 1.10.0
	 *
	 * @return int
	 */
	public function get_product_id() {

		return $this->product_id;
	}


	/**
	 * Returns the related product object.
	 *
	 * @since 1.10.0
	 *
	 * @return null|\WC_Product
	 */
	public function get_product() {

		$product = wc_get_product( $this->get_product_id() );

		return $product instanceof \WC_Product ? $product : null;
	}


	/**
	 * Returns the front end link to this contribution.
	 *
	 * @since 1.0.0
	 *
	 * @return string URL
	 */
	public function get_permalink() {

		return get_comment_link( $this->id );
	}


	/**
	 * Returns the contribution admin edit link.
	 *
	 * @since 1.10.0
	 *
	 * @return string URL
	 */
	public function get_edit_link() {

		return get_edit_comment_link( $this->get_comment_data() );
	}


	/**
	 * Returns the contribution attachment ID.
	 *
	 * @since 1.10.0
	 *
	 * @return int
	 */
	public function get_attachment_id() {

		return $this->attachment_id;
	}


	/**
	 * Returns the contribution attachment type.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_attachment_type() {

		return $this->attachment_type;
	}


	/**
	 * Returns the contribution attachment URL.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_attachment_url() {

		// get attachment URLs for photos in the media folder, which will have an ID instead
		if ( ! $this->attachment_url && $this->attachment_id ) {
			$attachment_url = wp_get_attachment_url( $this->attachment_id );
		} else {
			$attachment_url = $this->attachment_url;
		}

		return $attachment_url;
	}


	/**
	 * Checks if contribution has an attachment.
	 *
	 * Important! This does not check if the attachment has been removed or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_attachment() {

		return (bool) $this->get_attachment_type();
	}


	/**
	 * Returns the contributor's ID.
	 *
	 * @since 1.10.0
	 *
	 * @return int
	 */
	public function get_contributor_id() {

		return $this->contributor_id;
	}


	/**
	 * Returns the contributor's name.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_contributor_name() {

		return $this->contributor_name;
	}


	/**
	 * Returns the contributor's email.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_contributor_email() {

		return $this->contributor_email;
	}


	/**
	 * Returns the contributor's IP address.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	public function get_contributor_ip() {

		return $this->contributor_ip;
	}


	/**
	 * Returns the contribution's date.
	 *
	 * @since 1.10.0
	 *
	 * @return string date in MySQL format
	 */
	public function get_contribution_date() {

		return $this->contribution_date;
	}


	/**
	 * Returns the contribution date's in GMT equivalent.
	 *
	 * @since 1.10.0
	 *
	 * @return string date in MySQL format (UTC)
	 */
	public function get_contribution_date_gmt() {

		return $this->contribution_date_gmt;
	}


	/**
	 * Returns the contribution's karma value.
	 *
	 * @since 1.10.0
	 *
	 * @return int
	 */
	public function get_karma() {

		return $this->karma;
	}


	/**
	 * Returns the contribution's moderation status.
	 *
	 * @since 1.10.0
	 *
	 * @return int 0 = not approved, 1 = approved, 2 = flagged as inappropriate
	 */
	public function get_moderation() {

		return $this->moderation;
	}


	/**
	 * Returns the contribution rating.
	 *
	 * @since 1.10.0
	 *
	 * @return float
	 */
	public function get_rating() {

		return $this->rating;
	}


	/**
	 * Returns the voting URL, used when JS is not enabled/supported.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type vote type: 'positive' or 'negative'. Defaults to 'positive'
	 * @param string $base_url base URL to use (defaults to the current URL)
	 * @return string URL
	 */
	public function get_vote_url( $type = 'positive', $base_url = '' ) {

		$base_url = ! empty( $base_url ) ? $base_url : "//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$vote_url = add_query_arg( 'action', 'vote_for_contribution', $base_url );
		$vote_url = add_query_arg( 'type', $type, $vote_url );
		$vote_url = add_query_arg( 'comment_id', $this->get_id(), $vote_url );

		return $vote_url;
	}


	/**
	 * Casts a vote for this contribution.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type optional vote type: 'positive' or 'negative' (defaults to 'positive')
	 * @param int|null $user_id optional user ID to cast vote as (defaults to current user ID)
	 * @return int|false vote count if successful, false otherwise
	 */
	public function cast_vote( $type = 'positive', $user_id = null ) {

		$user_id       = ! empty( $user_id ) ? $user_id : get_current_user_id();
		$previous_type = '';
		$comment_id    = $this->get_id();
		$result        = false;

		// user ID is required to vote
		if ( ! $user_id ) {

			$this->_failure_message = __( 'You must be logged in to vote', 'woocommerce-product-reviews-pro' );

			$result = false;

		// users are not allowed to vote for their own contribution
		} elseif ( (int) $user_id === (int) $this->contributor_id ) {

			$this->_failure_message = __( "You can't vote for yourself", 'woocommerce-product-reviews-pro' );

		} else {

			$votes = array(
				'positive' => $this->get_positive_votes(),
				'negative' => $this->get_negative_votes(),
			);

			$users_votes = $this->get_users_votes();

			// special cases: user is removing or changing their vote
			if ( $this->has_user_voted( $user_id ) ) {

				$previous_type = $this->get_user_vote( $user_id );

				// remove user's previous vote
				$votes[ $previous_type ]--;
				update_comment_meta( $comment_id, $previous_type . '_votes', $votes[ $previous_type ] );

				// forget user's vote
				unset( $users_votes[ $user_id ] );
			}

			// cast new vote if user has not voted before, OR, if they are changing their vote
			if ( $type !== $previous_type || ! $this->has_user_voted() ) {

				$votes[ $type ]++;

				update_comment_meta( $comment_id, $type . '_votes', $votes[ $type ] );

				// remember that this user has now voted
				$users_votes[ $user_id ] = $type;
			}

			$this->positive_votes = $votes['positive'];
			$this->negative_votes = $votes['negative'];

			// update comment karma
			wp_update_comment( array(
				'comment_ID'    => $comment_id,
				'comment_karma' => $this->positive_votes - $this->negative_votes,
			) );

			// update user's votes
			update_comment_meta( $comment_id, 'users_votes', $users_votes );

			$result = $votes[ $type ];
		}

		return $result;
	}


	/**
	 * Returns users who have voted for this contribution.
	 *
	 * @since 1.0.0
	 *
	 * @return array associative array with user ID / vote pairs
	 */
	public function get_users_votes() {

		$users_votes = get_comment_meta( $this->get_id(), 'users_votes', true );

		return ! empty( $users_votes ) && is_array( $users_votes ) ? $users_votes : array();
	}


	/**
	 * Returns a specific user's vote.
	 *
	 * @since 1.0.0
	 *
	 * @param int|null $user_id optional, defaults to the current user, if logged in
	 * @return string|null vote type if user has voted, null otherwise.
	 */
	public function get_user_vote( $user_id = null ) {

		// Use the provided user ID or current user ID
		$user_id = ! empty( $user_id ) ? $user_id : get_current_user_id();

		// Get all users' votes
		$users_votes = $this->get_users_votes();

		return isset( $users_votes[ $user_id ] ) ? $users_votes[ $user_id ] : null;
	}


	/**
	 * Checks if a user has voted for the contribution.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $user_id optional, defaults to the current user, if logged in
	 * @return bool
	 */
	public function has_user_voted( $user_id = '' ) {

		return (bool) $this->get_user_vote( $user_id );
	}


	/**
	 * Returns the contribution positive votes count.
	 *
	 * @since 1.10.0
	 *
	 * @return int
	 */
	public function get_positive_votes() {

		return (int) $this->positive_votes;
	}


	/**
	 * Returns the contribution negative votes count.
	 *
	 * @since 1.10.0
	 *
	 * @return int
	 */
	public function get_negative_votes() {

		return (int) $this->negative_votes;
	}


	/**
	 * Returns the total vote count.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_vote_count() {

		return (int) $this->get_positive_votes() + (int) $this->get_negative_votes();
	}


	/**
	 * Returns helpfulness score.
	 *
	 * 1 = most helpful, 0 = meh, -1 = awful
	 *
	 * @since 1.0.0
	 *
	 * @return int|float a number between -1 and 1
	 */
	public function get_helpfulness_ratio() {

		$positive_votes = $this->get_positive_votes();
		$vote_count     = $this->get_vote_count();

		return $positive_votes && $vote_count ? $positive_votes / $vote_count : 0;
	}


	/**
	 * Flags contribution for removal.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $reason optional flag reason (default empty)
	 * @param int|null $user_id optional user ID to cast vote as (defaults to current user's ID)
	 * @return bool
	 */
	public function flag( $reason = null, $user_id = null ) {

		$user_id    = ! empty( $user_id ) ? (int) $user_id : get_current_user_id();
		$comment_id = $this->get_id();
		$success    = false;

		// users are not allowed to flag more than once
		if ( $this->has_user_flagged( $user_id ) ) {

			$this->_failure_message = __( 'You have already flagged this contribution', 'woocommerce-product-reviews-pro' );

		// users are not allowed to flag for their own contribution
		} elseif ( (int) $user_id === (int) $this->contributor_id && is_user_logged_in() ) {

			$this->_failure_message = __( "You can't flag your own contributions!", 'woocommerce-product-reviews-pro' );

		} else {

			// this will trigger a data upgrade if the current contribution is still using a legacy format
			$this->get_flags();

			// create a new flag
			$flag = new \WC_Product_Reviews_Pro_Contribution_Flag( $comment_id, array(
				uniqid( '', false ) => array(
					'user_id'   => $user_id,
					'reason'    => is_string( $reason ) ? trim( $reason ) : '',
					'resolved'  => false, // since the flag is new, it's always unresolved at this stage
					'timestamp' => current_time( 'timestamp', true ),
					'ip'        => \WC_Geolocation::get_ip_address(),
				)
			) );

			if ( $success = $flag->save() ) {

				// update the flags count
				$this->increase_flag_count();

				// mark the current user as having flagged this contribution
				if ( $user_id ) {

					$users_flagged = array_merge( $this->get_users_flagged(), array( $user_id ) );

					update_comment_meta( $comment_id, 'users_flagged', implode( ',', array_unique( $users_flagged ) ) );
				}

				// set flag cookie, expiring in 10 years
				if ( ! $user_id || get_current_user_id() === (int) $user_id ) {

					$flagged_comments[] = $comment_id;

					setcookie( 'wc_product_reviews_pro_flagged_comments', implode( ',', $flagged_comments ), time() + ( 10 * YEAR_IN_SECONDS ) );
				}

				if ( $this->is_flag_count_above_threshold() ) {

					$author_id = $this->get_contributor_id();
					$is_admin  = $author_id > 0 && user_can( $author_id, 'manage_woocommerce' );

					/**
					 * Filters whether a flagged contribution should be set to pending approval status.
					 *
					 * @since 1.10.0
					 *
					 * @param bool $set_to_pending default true unless the contribution author is a shop manager or an admin
					 * @param \WC_Contribution $contribution the contribution object
					 * @param \WC_Product_Reviews_Pro_Contribution_Flag the flag object
					 */
					if ( (bool) apply_filters( 'wc_product_reviews_pro_flagged_contribution_set_to_pending_approval', ! $is_admin, $this, $flag ) ) {

						wp_update_comment( array(
							'comment_ID'       => $comment_id,
							'comment_approved' => 0,
						) );
					}
				}

				/**
				 * Triggers an admin email notification when a contribution is flagged.
				 *
				 * @see \WC_Product_Reviews_Pro_Emails_Flagged_Contribution
				 *
				 * @since 1.10.0
				 *
				 * @param int $comment_id the ID of the contribution being flagged
				 * @param \WC_Product_Reviews_Pro_Contribution_Flag $flag the flag object
				 */
				do_action( 'wc_product_reviews_pro_flagged_contribution_email', $comment_id, $flag );

			} else {

				$this->_failure_message = __( 'An error occurred. Please try again.', 'woocommerce-product-reviews-pro' );
			}
		}

		return $success;
	}


	/**
	 * Returns users who have flagged this contribution.
	 *
	 * @since 1.0.0
	 *
	 * @return int[] list with IDs of flagged users
	 */
	public function get_users_flagged() {

		$users_flagged = get_comment_meta( $this->get_id(), 'users_flagged', true );

		return $users_flagged ? explode( ',', $users_flagged ) : array();
	}


	/**
	 * Checks if a user has flagged the contribution.
	 *
	 * If user ID is not provided falls back to checking cookies.
	 *
	 * @since 1.0.0
	 *
	 * @param int|null $user_id optional, defaults to the current user, if logged in
	 * @return bool
	 */
	public function has_user_flagged( $user_id = null ) {

		$user_id     = ! empty( $user_id ) ? (int) $user_id : get_current_user_id();
		$has_flagged = null;

		// Rules:
		// * If user ID is in flagged users list, then user has flagged
		// * If user ID matches current user ID and cookie is set, then user has flagged
		// * If no user ID is provided, and cookie is set, then user has flagged

		if ( $user_id ) {
			$has_flagged = in_array( $user_id, $this->get_users_flagged(), false );
		}

		if ( null === $has_flagged && ( ! $user_id || $user_id !== get_current_user_id() ) ) {

			$flagged_comments = isset( $_COOKIE['wc_product_reviews_pro_flagged_comments'] ) ? explode( ',', $_COOKIE['wc_product_reviews_pro_flagged_comments'] ) : array();

			$has_flagged = in_array( $this->get_id(), $flagged_comments, false );
		}

		return (bool) $has_flagged;
	}


	/**
	 * Returns the contribution flag count.
	 *
	 * @since 1.10.0
	 *
	 * @return int
	 */
	public function get_flag_count() {

		return max( 0, is_numeric( $this->flag_count ) ? (int) $this->flag_count : 0 );
	}


	/**
	 * Checks whether the flag count is above the optionally set threshold level.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function is_flag_count_above_threshold() {

		$handling        = get_option( 'wc_product_reviews_pro_flagged_contribution_handling', 'keep_published' );
		$above_threshold = false;

		// two of the possible settings when threshold is enabled, 'keep_published' would imply no threshold at all
		if ( in_array( $handling, array( 'pending_approval_customer', 'pending_approval_guest' ), true ) ) {

			/**
			 * Filters the required flags threshold.
			 *
			 * TODO in the future this may be moved to an input number setting {FN 2018-02-05}
			 *
			 * @since 1.10.0
			 *
			 * @param int $threshold a number (minimum 1, default)
			 */
			$threshold = max( 1, (int) apply_filters( 'wc_product_reviews_pro_flagged_contribution_threshold', 1 ) );

			// all flags matter
			if ( 'pending_approval_guest' === $handling ) {

				$above_threshold = $this->get_flag_count() >= $threshold;

			// only flags from verified users matter
			} else {

				$flags    = $this->get_flags();
				$verified = 0;

				foreach ( $flags as $flag ) {
					if ( ! $flag->is_anonymous() ) {
						$verified++;
					}
				}

				$above_threshold = $verified >= $threshold;
			}
		}

		return $above_threshold;
	}


	/**
	 * Decreases the flag count by 1 or more.
	 *
	 * @since 1.10.0
	 *
	 * @param int $amount an amount of flags to remove from the total count (default 1)
	 * @return bool
	 */
	public function decrease_flag_count( $amount = 1 ) {

		$success = false;

		if ( $this->flag_count >= 1 && is_numeric( $amount ) && $amount >= 1 ) {

			$this->flag_count -= (int) $amount;

			$success = update_comment_meta( $this->get_id(), 'flag_count', $this->flag_count );
		}

		return (bool) $success;
	}


	/**
	 * Increases the flag count by 1 or more.
	 *
	 * @since 1.10.0
	 *
	 * @param int $amount an amount of flags to add to the total count (default 1)
	 * @return bool
	 */
	public function increase_flag_count( $amount = 1 ) {

		$success = false;

		if ( is_numeric( $amount ) && $amount >= 1 ) {

			$this->flag_count += (int) $amount;

			$success = (bool) update_comment_meta( $this->get_id(), 'flag_count', $this->flag_count );
		}

		return $success;
	}


	/**
	 * Set a flag count.
	 *
	 * @since 1.10.0
	 *
	 * @param int $count a number of 0 or greater value
	 * @return bool
	 */
	public function set_flag_count( $count ) {

		$success = false;

		if ( is_numeric( $count ) && $count >= 0 ) {

			$this->flag_count = (int) $count;

			$success = (bool) update_comment_meta( $this->get_id(), 'flag_count', $this->flag_count );
		}

		return $success;
	}


	/**
	 * Returns the flags set for the current contribution.
	 *
	 * @since 1.10.0
	 *
	 * @param string $output whether to output an associative array of data (ARRAY_A) instead of objects (default OBJECT, output objects)
	 * @return array|\WC_Product_Reviews_Pro_Contribution_Flag[] array of contribution flag objects or associative array if $raw_output is chosen
	 */
	public function get_flags( $output = OBJECT ) {

		$comment_id  = $this->get_id();
		$flags_data  = get_comment_meta( $comment_id, 'flags', true );
		$flags_data  = empty( $flags_data ) || ! is_array( $flags_data ) ? get_comment_meta( $comment_id, 'flag_reason', true ) : $flags_data;
		$legacy_data = false;
		$set_data    = $flag_objects = $raw_data = array();
		$unresolved  = 0;

		// accounts for some possible legacy data (a single reason as a string)
		if ( ! empty( $flags_data ) && ( is_array( $flags_data ) || is_string( $flags_data ) ) ) {

			foreach ( (array) $flags_data as $flag_id => $flag_data ) {

				// standard handling
				if ( is_array( $flag_data ) ) {

					$set_data = array( $flag_id => $flag_data );

				// handle legacy data format for backwards compatibility
				} elseif ( is_string( $flag_data ) ) {

					$legacy_data = true;
					$set_data    = array(
						uniqid( '', false ) => array(
							'user_id'   => 0,
							'timestamp' => strtotime( $this->get_contribution_date_gmt() ),
							'reason'    => $flag_data,
							'resolved'  => false,
						)
					);
				}

				if ( ! empty( $set_data ) ) {

					$flag_object = new \WC_Product_Reviews_Pro_Contribution_Flag( $comment_id, $set_data );

					$raw_data[ key( $flag_object->get_raw_data() ) ] = current( $flag_object->get_raw_data() );
					$flag_objects[ $flag_object->get_id() ]          = $flag_object;

					if ( $flag_object->is_unresolved() ) {
						$unresolved++;
					}
				}
			}
		}

		// upgrade data to the current format and remove the legacy one
		if ( $legacy_data && ! empty( $raw_data ) ) {

			$this->update_flags( $raw_data );

			delete_comment_meta( $comment_id, 'flag_reason' );
		}

		// update the flag count if there is a mismatch
		if ( $unresolved !== $this->flag_count ) {

			$this->flag_count = $unresolved;

			$this->set_flag_count( $unresolved );
		}

		return ARRAY_A === $output ? $raw_data : $flag_objects;
	}


	/**
	 * Sets flags data.
	 *
	 * @since 1.10.0
	 *
	 * @param array $data associative array of flags data
	 * @return bool
	 */
	public function update_flags( array $data ) {

		return (bool) update_comment_meta( $this->get_id(), 'flags', $data );
	}


	/**
	 * Checks whether contribution is editable or not.
	 *
	 * @since 1.8.0
	 *
	 * @return bool
	 */
	public function is_editable() {

		$is_editable = false;

		// check if user is logged in and review update confirmation email is enabled
		if ( is_user_logged_in() && wc_product_reviews_pro_review_update_confirmation_enabled() ) {

			$comment = $this->get_comment_data();
			$user_id = get_current_user_id();

			// check if comment type is review and if user is admin or review author
			if ( 'review' === $comment->comment_type && ( current_user_can( 'manage_options' ) || $user_id === (int) $comment->user_id ) ) {

				$is_editable = true;
			}
		}

		return $is_editable;
	}


	/**
	 * Returns the message with reason why voting/flagging failed.
	 *
	 * @since 1.0.0
	 *
	 * @return string failure message
	 */
	public function get_failure_message() {

		return $this->_failure_message;
	}


}
