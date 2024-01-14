<?php
/**
 * Awarding Points for Actions.
 *
 * @package Rewardsystem.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSPointExpiry' ) ) {

	/**
	 * Class RSPointExpiry.
	 */
	class RSPointExpiry {

		/**
		 * Total Points.
		 *
		 * @var float
		 */
		protected static $total_points;

		/**
		 * Redeemed Points.
		 *
		 * @var float
		 */
		protected static $redeemed_points;

		/**
		 * Expired Points.
		 *
		 * @var float
		 */
		protected static $expired_points;

		/**
		 * Available Points.
		 *
		 * @var float
		 */
		protected static $available_points;

		/**
		 * Message for page comment.
		 *
		 * @var bool
		 * @since 28.9.0
		 */
		private static $page_comment_notice_exists = false;

		/**
		 * Message for post creation.
		 *
		 * @var bool
		 * @since 28.9.0
		 */
		private static $post_creation_notice_exists = false;

		/**
		 * Message for post comment.
		 *
		 * @var bool
		 * @since 28.9.0
		 */
		private static $post_comment_notice_exists = false;

		/**
		 * Add Hooks in Init.
		 */
		public static function init() {
			$order_status_list = get_option( 'rs_order_status_control', array( 'processing', 'completed' ) );
			if ( is_array( $order_status_list ) && ! empty( $order_status_list ) ) {
				foreach ( $order_status_list as $value ) {
					add_action( 'woocommerce_order_status_' . $value, array( __CLASS__, 'update_earning_points_for_user' ), 999 );
					// add_action( 'woocommerce_thankyou' , array( __CLASS__ , 'update_earning_points_for_user' ) , 1 ) ; Commented because product and referral purchase points awarded twice.
					add_action( 'woocommerce_shipstation_shipnotify', array( __CLASS__, 'update_earning_points_for_user' ), 1 );

					add_action( 'woocommerce_order_status_' . $value, array( __CLASS__, 'award_reward_points_for_coupon' ), 1 );

					add_action( 'woocommerce_order_status_' . $value, array( __CLASS__, 'signup_points_after_purchase' ) );
				}
			}

			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'checkout_cookies_referral_meta' ), 1, 2 );

			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'delete_cookie_for_user_and_guest' ), 10, 2 );

			add_action( 'woocommerce_process_shop_order_meta', array( __CLASS__, 'award_point_for_manual_order' ), 50, 2 );
			add_action( 'woocommerce_order_status_refunded', array( __CLASS__, 'update_revised_points_for_user' ) );
			add_action( 'woocommerce_order_status_cancelled', array( __CLASS__, 'update_revised_points_for_user' ) );
			add_action( 'woocommerce_order_status_failed', array( __CLASS__, 'update_revised_points_for_user' ) );

			add_action( 'wpo_wcsre_email_sent', array( __CLASS__, 'update_revised_points_for_user' ) );

			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'update_redeem_point_for_user' ), 1 );
			add_action( 'woocommerce_process_shop_order_meta', array( __CLASS__, 'redeem_point_for_manual_order' ), 50, 2 );
			add_action( 'woocommerce_order_status_refunded', array( __CLASS__, 'update_revised_redeem_points_for_user' ) );
			add_action( 'woocommerce_order_status_cancelled', array( __CLASS__, 'update_revised_redeem_points_for_user' ) );
			add_action( 'woocommerce_order_status_failed', array( __CLASS__, 'update_revised_redeem_points_for_user' ) );

			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'redeem_points_for_using_reward_gateway' ), 1 );
			add_action( 'woocommerce_process_shop_order_meta', array( __CLASS__, 'redeem_points_for_manual_order_using_reward_gateway' ), 50, 2 );
			add_action( 'woocommerce_order_status_refunded', array( __CLASS__, 'revise_redeemed_points_through_reward_gateway_for_user' ) );
			add_action( 'woocommerce_order_status_cancelled', array( __CLASS__, 'revise_redeemed_points_through_reward_gateway_for_user' ) );
			add_action( 'woocommerce_order_status_failed', array( __CLASS__, 'revise_redeemed_points_through_reward_gateway_for_user' ) );

			add_action( 'wp_head', array( __CLASS__, 'check_if_expiry' ) );

			add_action( 'wp_head', array( __CLASS__, 'delete_if_used' ) );

			add_action( 'wp_head', array( __CLASS__, 'delete_if_expired' ) );

			// add_action( 'admin_init', array( __CLASS__, 'update_order_status' ), 9999 );

			add_action( 'delete_user', array( __CLASS__, 'delete_referral_points_if_user_deleted' ) );

			// Delete all birthday related data.
			add_action( 'delete_user', array( __CLASS__, 'delete_birthday_data_if_user_deleted' ) );

			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'check_redeeming_in_order' ), 10, 2 );

			add_action( 'rs_perform_action_for_order', array( __CLASS__, 'insert_buying_points_for_user' ) );

			add_filter( 'the_content', array( __CLASS__, 'msg_for_page_and_post_comment' ) );

			add_action( 'comment_post', array( __CLASS__, 'award_points_for_comments' ), 10 );

			add_action( 'transition_comment_status', array( __CLASS__, 'award_points_for_comments_is_approved' ), 10, 3 );

			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'trash_sumo_coupon_if_order_placed' ), 10, 2 );

			add_action( 'woocommerce_process_shop_order_meta', array( __CLASS__, 'trash_sumo_coupon_if_order_placed' ), 10, 2 );

			add_action( 'sumopaymentplans_payment_is_completed', array( __CLASS__, 'final_payment' ), 1, 3 );

			add_action( 'woocommerce_register_form', array( __CLASS__, 'display_checkbox_in_registration_form' ) );

			add_action( 'woocommerce_before_my_account', array( __CLASS__, 'display_checkbox_in_my_account_page' ) );

			if ( '1' == get_option( 'rs_message_before_after_cart_table' ) ) {
				if ( '1' == get_option( 'rs_reward_point_troubleshoot_before_cart' ) ) {
					add_action( 'woocommerce_before_cart', array( __CLASS__, 'available_points_for_user' ) );
				} else {
					add_action( 'woocommerce_before_cart_table', array( __CLASS__, 'available_points_for_user' ) );
				}
			} else {
				add_action( 'woocommerce_after_cart_table', array( __CLASS__, 'available_points_for_user' ) );
			}
			add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'available_points_for_user' ), 11 );

			add_action( 'save_post', array( __CLASS__, 'award_points_for_product_creation' ), 1, 2 );

			// Award Coupon for User.
			add_action( 'srp_birthday_cron', array( __CLASS__, 'award_bday_points' ) );
			// Save Birthday Date in Create User Profile.
			add_action( 'edit_user_created_user', array( __CLASS__, 'update_birthday_date' ) );
			// Save Birthday Date in Edit User Profile.
			add_action( 'profile_update', array( __CLASS__, 'update_birthday_date' ), 10, 1 );
			// Save Birthday Date in Registration Form.
			add_action( 'user_register', array( __CLASS__, 'update_birthday_date' ), 10, 1 );
			// Save Birthday Date in Account Details.
			add_action( 'woocommerce_save_account_details', array( __CLASS__, 'update_birthday_date' ), 10, 1 );
			// May be update birthday field in order.
			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'update_birthday_field_in_order' ) );
			// Award Points for Blog Post Creation.
			add_action( 'wp_insert_post', array( __CLASS__, 'award_points_for_blog_post_creation' ), 10, 3 );

			add_filter( 'woocommerce_get_formatted_order_total', array( __CLASS__, 'order_total_in_order_detail' ), 10, 2 );
		}

		/**
		 * Award Points for Product Creation.
		 *
		 * @param int     $post_id Post Id.
		 * @param WP_Post $post Product Object.
		 */
		public static function award_points_for_product_creation( $post_id, $post ) {
			if ( 'product' !== $post->post_type ) {
				return;
			}

			$ban_type = check_banning_type( get_current_user_id() );
			if ( 'earningonly' === $ban_type || 'both' === $ban_type ) {
				return;
			}

			if ( 'no' == get_option( 'rs_reward_for_enable_product_create' ) ) {
				return;
			}

			if ( empty( get_option( 'rs_reward_Product_create' ) ) ) {
				return;
			}

			if ( '1' === get_post_meta( $post->ID, 'productcreationpoints', true ) ) {
				return;
			}

			$new_obj        = new RewardPointsOrder( 0, 'no' );
			$valuestoinsert = array(
				'pointstoinsert'    => get_option( 'rs_reward_Product_create' ),
				'event_slug'        => 'RPCPRO',
				'user_id'           => $post->post_author,
				'product_id'        => $productid,
				'totalearnedpoints' => get_option( 'rs_reward_Product_create' ),
			);
			$new_obj->total_points_management( $valuestoinsert );
			update_post_meta( $post->ID, 'productcreationpoints', '1' );
		}

		/**
		 * Get Available Points for user.
		 */
		public static function available_points_for_user() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( ! allow_reward_points_for_user( get_current_user_id() ) ) {
				return;
			}

			if ( 'both' === check_banning_type( get_current_user_id() ) ) {
				return;
			}

			$show_msg = is_cart() ? get_option( 'rs_show_hide_message_for_my_rewards' ) : get_option( 'rs_show_hide_message_for_my_rewards_checkout_page' );
			if ( '2' === $show_msg ) {
				return;
			}

			$user_id     = get_current_user_id();
			$points_data = new RS_Points_Data( $user_id );
			$points      = $points_data->total_available_points();
			if ( empty( $points ) ) {
				return;
			}

			$class_names[] = is_cart() ? 'sumo_reward_points_current_points_message rs_cart_message' : 'sumo_available_points rs_checkout_messages';

			if ( 'yes' == get_option( 'rs_available_points_display' ) && self::validate_redeeming_is_applied() ) {
				$class_names[] = 'rs_hide_available_points_info';
			}

			$message = is_cart() ? get_option( 'rs_message_user_points_in_cart' ) : get_option( 'rs_message_user_points_in_checkout' );
			?>
			<div class="woocommerce-info <?php echo esc_attr( implode( ' ', $class_names ) ); ?>">
				<?php echo do_shortcode( $message ); ?>
			</div>
			<?php
		}

		/**
		 * Validate Redeeming is applied in cart/ checkout.
		 */
		public static function validate_redeeming_is_applied() {
			$user = get_user_by( 'id', get_current_user_id() );
			if ( ! is_object( $user ) || ! $user->exists() ) {
				return false;
			}

			$redeeming_coupon      = 'sumo_' . strtolower( "$user->user_login" );
			$auto_redeeming_coupon = 'auto_redeem_' . strtolower( "$user->user_login" );
			$cart_coupons          = WC()->cart->get_applied_coupons();
			if ( ! srp_check_is_array( $cart_coupons ) ) {
				return false;
			}

			if ( in_array( $redeeming_coupon, $cart_coupons ) || in_array( $auto_redeeming_coupon, $cart_coupons ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Display Checkbox in My Account to involve User in Reward Program.
		 */
		public static function display_checkbox_in_my_account_page() {
			if ( 'yes' !== get_option( 'rs_enable_reward_program' ) ) {
				return;
			}

			$banning_type = check_banning_type( get_current_user_id() );
			if ( 'earningonly' === $banning_type || 'both' === $banning_type ) {
				return;
			}

			$checkbox_value = get_user_meta( get_current_user_id(), 'allow_user_to_earn_reward_points', true );
			if ( empty( $checkbox_value ) ) {
				update_user_meta( get_current_user_id(), 'allow_user_to_earn_reward_points', 'yes' );

				/**
				 * This hook is used to do extra action when user involved in Reward Program.
				 *
				 * @param int $userid User ID.
				 * @since 29.4
				 */
				do_action( 'fp_rs_reward_program_enabled', get_current_user_id() );
			}
			?>
			<div class="enable_reward_points">
				<p>
					<input type="checkbox" name="rs_enable_earn_points_for_user" id="rs_enable_earn_points_for_user" class="rs_enable_earn_points_for_user" 
					<?php
					if ( 'yes' === $checkbox_value ) {
						?>
						checked="checked"<?php } ?>/> 
						<?php echo wp_kses_post( 'yes' === $checkbox_value ? get_option( 'rs_msg_in_acc_page_when_checked' ) : get_option( 'rs_msg_in_acc_page_when_unchecked' ) ); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * Display Checkbox in Checkout to involve User in Reward Program
		 */
		public static function display_checkbox_in_registration_form() {
			if ( 'yes' != get_option( 'rs_enable_reward_program' ) ) {
				return;
			}

			$banning_type = check_banning_type( get_current_user_id() );
			if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
				return;
			}
			?>
			<div class="enable_reward_points">
				<p>
					<input type="checkbox" name="rs_enable_earn_points_for_user_in_reg_form" id="rs_enable_earn_points_for_user_in_reg_form" class="rs_enable_earn_points_for_user_in_reg_form" data-enable-reward-program="<?php echo esc_attr( get_option( 'rs_enable_reward_program' ) ); ?>"/> <?php echo wp_kses_post( get_option( 'rs_msg_in_reg_page' ) ); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * Fires when admin change the status
		 *
		 * @param string  $new_status New status.
		 * @param string  $old_status Old status.
		 * @param WP_Post $comment_obj Comment Object.
		 */
		public static function award_points_for_comments_is_approved( $new_status, $old_status, $comment_obj ) {
			if ( 'yes' !== get_option( 'rs_reward_action_activated' ) ) {
				return;
			}

			if ( ! is_object( $comment_obj ) ) {
				return;
			}

			$comment_obj  = get_comment( $comment_obj->comment_ID );
			$comment_type = get_post_type( $comment_obj->comment_post_ID );

			/**
			 * Hook:rs_custom_post_type_for_posts.
			 *
			 * @since 1.0
			 */
			$post_types = apply_filters( 'rs_custom_post_type_for_posts', array( 'post' ) );

			$award_after_approved = '';
			if ( 'page' === $comment_type ) {
				$award_after_approved = get_option( 'rs_page_comment_reward_status' );
			} elseif ( in_array( $comment_type, $post_types ) ) {
				$award_after_approved = get_option( 'rs_post_comment_reward_status' );
			} elseif ( 'product' === $comment_type ) {
				$award_after_approved = get_option( 'rs_review_reward_status' );
			}

			if ( '1' === $award_after_approved ) {
				if ( 'approved' === $new_status && 'unapproved' === $old_status ) {
					self::award_points_for_comments( $comment_obj->comment_ID );
				}
			}
		}

		/**
		 * Fires when Comment in Frontend.
		 *
		 * @param int $comment_id Comment ID.
		 */
		public static function award_points_for_comments( $comment_id ) {
			if ( 'yes' !== get_option( 'rs_reward_action_activated' ) ) {
				return;
			}

			if ( ! is_user_logged_in() ) {
				return;
			}

			$comment_obj  = get_comment( $comment_id );
			$comment_type = get_post_type( $comment_obj->comment_post_ID );

			// Award Points for Page Comment.
			self::award_points_for_page_comment( $comment_obj, $comment_type );

			// Award Points for Post Comment.
			self::award_points_for_post_comment( $comment_obj, $comment_type );

			// Award Points for Product Review.
			self::award_points_for_product_review( $comment_obj, $comment_type );
		}

		/**
		 * Awarding Points for Page Comment.
		 *
		 * @param WP_Post $comment_obj Comment object.
		 * @param string  $comment_type Comment Type.
		 */
		public static function award_points_for_page_comment( $comment_obj, $comment_type ) {
			// RPCPAR Checkpoints is changed to RPFPAC(Reward Points For Page Comment).
			if ( 'yes' !== get_option( 'rs_reward_for_comment_Page' ) ) {
				return;
			}

			if ( 'page' !== $comment_type ) {
				return;
			}

			$points_to_insert = get_option( 'rs_reward_page_review' );
			if ( empty( $points_to_insert ) ) {
				return;
			}

			$status_to_award_points = get_option( 'rs_page_comment_reward_status' );
			$restrict_points        = get_option( 'rs_restrict_reward_page_comment' );
			self::check_whether_award_points_once_or_more( $restrict_points, $comment_obj->user_id, $comment_obj->comment_post_ID, 'usercommentpage', 'RPFPAC', $points_to_insert, $comment_obj->comment_approved, $status_to_award_points );
		}

		/**
		 * Awarding Points for Post Comment.
		 *
		 * @param WP_Post $comment_obj Comment object.
		 * @param string  $comment_type Comment Type.
		 */
		public static function award_points_for_post_comment( $comment_obj, $comment_type ) {
			// RPCPR Checkpoints is changed to RPFPOC(Reward Points For Post Comment).
			if ( 'yes' !== get_option( 'rs_reward_for_comment_Post' ) ) {
				return;
			}

			if ( 'post' !== $comment_type ) {
				return;
			}

			$points_to_insert = get_option( 'rs_reward_post_review' );
			if ( empty( $points_to_insert ) ) {
				return;
			}

			$status_to_award_points = get_option( 'rs_post_comment_reward_status' );
			$restrict_points        = get_option( 'rs_restrict_reward_post_comment' );
			self::check_whether_award_points_once_or_more( $restrict_points, $comment_obj->user_id, $comment_obj->comment_post_ID, 'usercommentpost', 'RPFPOC', $points_to_insert, $comment_obj->comment_approved, $status_to_award_points );
		}

		/**
		 * Awarding Points for Product Review.
		 *
		 * @param WP_Post $comment_obj Comment object.
		 * @param string  $comment_type Comment Type.
		 */
		public static function award_points_for_product_review( $comment_obj, $comment_type ) {
			if ( 'yes' !== get_option( 'rs_enable_product_review_points' ) ) {
				return;
			}

			if ( 'product' !== $comment_type ) {
				return;
			}

			$points_to_insert = rs_get_product_review_reward_points( $comment_obj->comment_post_ID );
			if ( empty( $points_to_insert ) ) {
				return;
			}

			$status_to_award_points = get_option( 'rs_review_reward_status' );
			$restrict_points        = get_option( 'rs_restrict_reward_product_review' );
			$user_info              = get_user_by( 'id', $comment_obj->user_id );

			if ( ! is_object( $user_info ) ) {
				return;
			}

			if ( 'yes' === get_option( 'rs_reward_for_comment_product_review' ) ) {
				$purchased_product = self::check_if_customer_purchased( $comment_obj->user_id, $user_info->user_email, $comment_obj->comment_post_ID, '' );
				if ( $purchased_product <= 0 ) {
					return;
				}

				if ( ! self::validate_product_review_based_on_specific_days_limit( $comment_obj->user_id, $user_info->user_email, $comment_obj->comment_post_ID ) ) {
					return;
				}

				self::check_whether_award_points_once_or_more( $restrict_points, $comment_obj->user_id, $comment_obj->comment_post_ID, 'userreviewed', 'RPPR', $points_to_insert, $comment_obj->comment_approved, $status_to_award_points );
			} else {
				self::check_whether_award_points_once_or_more( $restrict_points, $comment_obj->user_id, $comment_obj->comment_post_ID, 'userreviewed', 'RPPR', $points_to_insert, $comment_obj->comment_approved, $status_to_award_points );
			}

			/**
			 * Hook:fp_reward_point_for_product_review.
			 *
			 * @since 1.0
			 */
			do_action( 'fp_reward_point_for_product_review' );
		}

		/**
		 * Validate Product Review Based On Specific Days Limit.
		 *
		 * @param int    $user_id User ID.
		 * @param string $email_id Email Id.
		 * @param int    $post_id Post ID
		 */
		public static function validate_product_review_based_on_specific_days_limit( $user_id, $email_id, $post_id ) {

			if ( empty( $user_id ) || empty( $email_id ) || empty( $post_id ) ) {
				return false;
			}

			$number_of_days = get_option( 'rs_product_review_limit_in_days' );
			if ( ! $number_of_days ) {
				return true;
			}

			$order_date = self::get_order_date_based_on_purchased_user( $user_id, $email_id, $post_id, '' );
			if ( empty( $order_date ) ) {
				return true;
			}

			$limited_days_in_time = strtotime( $order_date ) + absint( $number_of_days ) * ( 24 * 60 * 60 );
			if ( time() > $limited_days_in_time ) {
				return false;
			}

			return true;
		}

		/**
		 * Get Order Date Based On Purchased User.
		 *
		 * @param int    $user_id User ID.
		 * @param string $emails Email Id.
		 * @param int    $product_id Product ID.
		 * @param int    $variation_id Variation ID
		 */
		public static function get_order_date_based_on_purchased_user( $user_id, $emails, $product_id, $variation_id ) {
			global $wpdb;
			$db         = &$wpdb;
			$order_date = $db->get_var(
				$db->prepare(
					"
			SELECT DISTINCT posts.post_date_gmt
			FROM {$db->prefix}woocommerce_order_items as order_items
			LEFT JOIN {$db->prefix}woocommerce_order_itemmeta AS itemmeta ON order_items.order_item_id = itemmeta.order_item_id
                        LEFT JOIN {$db->postmeta} AS postmeta ON order_items.order_id = postmeta.post_id
			LEFT JOIN {$db->posts} AS posts ON order_items.order_id = posts.ID
			WHERE
				posts.post_status IN ( 'wc-completed', 'wc-processing' ) AND
				itemmeta.meta_value  = %s AND
				itemmeta.meta_key    IN ( '_variation_id', '_product_id' ) AND
				postmeta.meta_key    IN ( '_billing_email', '_customer_user' ) AND
				(
					postmeta.meta_value  IN ( '" . implode( "','", array_map( 'esc_sql', array_unique( (array) $emails ) ) ) . "' ) OR
					(
						postmeta.meta_value = %s
					) 
				) ORDER BY posts.post_date_gmt DESC
			",
					empty( $variation_id ) ? $product_id : $variation_id,
					$user_id
				)
			);
			return $order_date;
		}

		/**
		 * Check Whether to Award Point for Product Review, Page and Post Comment Only Once or More
		 */
		public static function check_whether_award_points_once_or_more( $restrict_points, $user_id, $PostId, $MetaName, $EventSlug, $points_to_insert, $PostStatus, $status_to_award_points ) {
			if ( 'yes' == $restrict_points ) {
				$CheckIfUserAlreadyReviewed = get_user_meta( $user_id, $MetaName . $PostId, true );
				if ( '1' == $CheckIfUserAlreadyReviewed ) {
					return;
				}

				if ( '1' == $status_to_award_points ) {
					if ( '1' == $PostStatus ) {
						self::rs_insert_points_for_comments( $points_to_insert, $EventSlug, $user_id, $PostId, $MetaName );
					}
				} else {
					self::rs_insert_points_for_comments( $points_to_insert, $EventSlug, $user_id, $PostId, $MetaName );
				}
			} elseif ( '1' == $status_to_award_points ) {
				if ( '1' == $PostStatus ) {
					self::rs_insert_points_for_comments( $points_to_insert, $EventSlug, $user_id, $PostId, $MetaName );
				}
			} else {
				self::rs_insert_points_for_comments( $points_to_insert, $EventSlug, $user_id, $PostId, $MetaName );
			}
		}

		/**
		 * Insert Points for Product Review, Page and Post Comment
		 */
		public static function rs_insert_points_for_comments( $points_to_insert, $EventSlug, $user_id, $PostId, $MetaName ) {
			if ( ! allow_reward_points_for_user( $user_id ) ) {
				return;
			}

			$Object = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' === get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$Object->check_point_restriction( $points_to_insert, 0, $EventSlug, $user_id, '', '', $PostId, '', '' );
			} else {
				$ValuesToInsert = array(
					'pointstoinsert'    => $points_to_insert,
					'event_slug'        => $EventSlug,
					'user_id'           => $user_id,
					'product_id'        => $PostId,
					'totalearnedpoints' => $points_to_insert,
				);
				$Object->total_points_management( $ValuesToInsert );
				update_user_meta( $user_id, $MetaName . $PostId, '1' );
			}
		}

		/**
		 * Update whether it was final payment for Subscription.
		 *
		 * @param int    $payment_id Payment ID.
		 * @param int    $order_id Order ID.
		 * @param string $final_status Status.
		 */
		public static function final_payment( $payment_id, $order_id, $final_status ) {
			$order = wc_get_order( $order_id );
			$order->update_meta_data( '_rs_final_payment_plan', 'yes' );
			$order->save();
		}

		/**
		 * Revise Redeemed points through Reward Gateway for user when order status reach failed status.
		 *
		 * @param int    $order_id Order ID.
		 */
		public static function revise_redeemed_points_through_reward_gateway_for_user( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( '1' === $order->get_meta( 'refund_gateway' ) ) {
				return;
			}

			if ( 'reward_gateway' !== $order->get_payment_method() ) {
				return;
			}

			$total_redeem = $order->get_meta( 'total_redeem_points_for_order_point_price' );
			if ( empty( $total_redeem ) ) {
				return;
			}

			$order_obj  = srp_order_obj( $order );
			$table_args = array(
				'user_id'           => $order_obj['order_userid'],
				'pointstoinsert'    => $total_redeem,
				'checkpoints'       => 'RVPFRPG',
				'totalearnedpoints' => $total_redeem,
				'orderid'           => $order_id,
			);
			self::insert_earning_points( $table_args );
			self::record_the_points( $table_args );

			$order->update_meta_data( 'refund_gateway', '1' );
			$order->save();
		}

		/**
		 * Message for Page/Post Comment.
		 *
		 * @param string $content Message Content.
		 */
		public static function msg_for_page_and_post_comment( $content ) {

			global $wp_query;
			/* If Conflict with other plugins . So Check for display inside the loop for Earning Notices  */
			if ( isset( $wp_query->in_the_loop ) && ! $wp_query->in_the_loop ) {
				return $content;
			}

			if ( 'yes' !== get_option( 'rs_reward_action_activated' ) ) {
				return $content;
			}

			if ( ! is_home() && ! is_cart() && ! is_checkout() && ! is_product() && ! is_account_page() ) {
				self::message_for_page_comment( $content );
				self::message_for_post_creation( $content );
				self::message_for_post_comment( $content );
			}
			return $content;
		}

		/**
		 * Message for Page Comment.
		 *
		 * @param string $content Message Content.
		 */
		public static function message_for_page_comment( $content ) {
			if ( self::$page_comment_notice_exists ) {
				return $content;
			}

			if ( ! is_page() ) {
				return $content;
			}

			if ( 'yes' !== get_option( 'rs_reward_for_comment_Page' ) ) {
				return $content;
			}

			if ( '' === get_option( 'rs_reward_page_review' ) ) {
				return $content;
			}

			if ( '2' === get_option( 'rs_show_hide_message_for_page_comment' ) ) {
				return $content;
			}

			$comment_points = round_off_type( get_option( 'rs_reward_page_review' ) );
			$comment_points = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $comment_points );
			if ( empty( $comment_points ) ) {
				return $content;
			}

			$replaced_message = str_replace( '[rspagecommentpoints]', $comment_points, get_option( 'rs_message_user_points_for_page_comment' ) );
			?>
			<div class="woocommerce-info"><?php echo do_shortcode( $replaced_message ); ?></div>
			<?php

			self::$page_comment_notice_exists = true;
		}

		/**
		 * Message for Post Creation.
		 *
		 * @param string $content Message Content.
		 */
		public static function message_for_post_creation( $content ) {
			if ( self::$post_creation_notice_exists ) {
				return $content;
			}

			if ( is_page() ) {
				return $content;
			}

			if ( ! is_single() ) {
				return $content;
			}

			if ( 'yes' !== get_option( 'rs_reward_for_Creating_Post' ) ) {
				return $content;
			}

			if ( '' === get_option( 'rs_reward_post' ) ) {
				return $content;
			}

			if ( '2' === get_option( 'rs_show_hide_message_for_blog_create' ) ) {
				return $content;
			}

			$creation_points = round_off_type( get_option( 'rs_reward_post' ) );
			$creation_points = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $creation_points );
			if ( empty( $creation_points ) ) {
				return $content;
			}

			$replaced_message = str_replace( '[rspostcreationpoints]', $creation_points, get_option( 'rs_message_user_points_for_blog_creation' ) );
			?>
			<div class="woocommerce-info"><?php echo do_shortcode( $replaced_message ); ?></div>
			<?php

			self::$post_creation_notice_exists = true;
		}

		/**
		 * Message for Post Comment.
		 *
		 * @param string $content Message Content.
		 */
		public static function message_for_post_comment( $content ) {
			if ( self::$post_comment_notice_exists ) {
				return $content;
			}

			if ( ! is_home() && ! is_cart() && ! is_checkout() && ! is_product() && ! is_account_page() ) {
				if ( ! is_page() ) {
					if ( ! is_single() ) {
						return $content;
					}

					if ( 'yes' != get_option( 'rs_reward_for_comment_Post' ) ) {
						return $content;
					}

					if ( ! get_option( 'rs_reward_post_review' ) ) {
						return $content;
					}

					if ( '2' == get_option( 'rs_show_hide_message_for_post_comment' ) ) {
						return $content;
					}

					$comment_points = round_off_type( get_option( 'rs_reward_post_review' ) );
					$comment_points = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $comment_points );
					if ( empty( $comment_points ) ) {
						return $content;
					}

					$replaced_message = str_replace( '[rspostpoints]', $comment_points, get_option( 'rs_message_user_points_for_blog_comment' ) );
					?>
					<div class="woocommerce-info"><?php echo do_shortcode( $replaced_message ); ?></div>
					<?php

					self::$post_comment_notice_exists = true;
				}
			}
		}

		/**
		 * Award Points for Manual Order.
		 */
		public static function award_point_for_manual_order( $order, $post ) {
			if ( 'yes' != get_option( 'rs_product_purchase_activated' )) {
				return;
			}

			$order = is_object($order) ? $order : wc_get_order( $order );
			if ( '1' == $order->get_meta( 'frontendorder' ) ) {
				return;
			}

			$order_obj = srp_order_obj( $order );
			$user_id   = isset( $order_obj['order_userid'] ) ? $order_obj['order_userid'] : '';

			if ( ! $user_id ) {
				return;
			}

			$points = ( 'yes' === get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) ) ? self::modified_points_for_manual_order($order) : self::original_points_for_manual_order($order);

			$order->update_meta_data( 'points_for_current_order', $points );
			$order->update_meta_data( 'rs_points_for_current_order_as_value', array_sum( $points ) );

			$order->save();
		}

		/**
		 * Redeem Points for Manual Order.
		 */
		public static function redeem_point_for_manual_order( $order, $post ) {
			if ( 'yes' != get_option( 'rs_redeeming_activated' )) {
				return;
			}

			$order = is_object( $order ) ? $order : wc_get_order( $order );

			if ( '1' == $order->get_meta( 'frontendorder' ) ) {
				return;
			}

			if ( '1' === $order->get_meta( 'redeem_point_once_for_manual_order' ) ) {
				return;
			}

			$OrderObj    = srp_order_obj( $order );
			$order_id = $order->get_id();
			$user_id     = $OrderObj['order_userid'];
			$redeempoints = self::get_redeem_points_and_send_sms_when_redeem( $order_id, $user_id );
			if ( $redeempoints ) {
				self::perform_calculation_with_expiry( $redeempoints, $user_id );
				$points_data = new RS_Points_Data( $user_id );
				$totalpoints = $points_data->total_available_points();
				if ( $totalpoints >= 0 ) {
					$table_args = array(
						'user_id'     => $user_id,
						'usedpoints'  => $redeempoints,
						'date'        => '999999999999',
						'checkpoints' => 'RP',
						'orderid'     => $order_id,
					);
					self::record_the_points( $table_args );

					$order->update_meta_data( 'redeem_point_once_for_manual_order', '1' );
				}
			}

			$order->save();
		}

		/**
		 * Redeem Points for manual order using SUMO Reward Gateway
		 * 
		 * @param WP_Post $order Order Object.
		 * @since 29.8.0
		 * */
		public static function redeem_points_for_manual_order_using_reward_gateway( $order, $post ) {
			if ( 'yes' != get_option( 'rs_gateway_activated' )) {
				return;
			}

			$order = is_object($order) ? $order : wc_get_order( $order );
			if ( '1' == $order->get_meta( 'frontendorder' ) ) {
				return;
			}

			$order_obj = srp_order_obj( $order );
			$user_id   = isset( $order_obj['order_userid'] ) ? $order_obj['order_userid'] : '';

			if ( ! $user_id ) {
				return;
			}

			if ( 'reward_gateway' !== $order->get_payment_method() ) {
				return;
			}

			if ( '1' === $order->get_meta( 'manuall_order' ) ) {
				return;
			}

			if ( $order->get_total() < get_option( 'rs_max_redeem_discount_for_sumo_reward_points' ) ) {
				return;
			}

			$redeemed_points = gateway_points( $order->get_id() );
			$order->update_meta_data( 'total_redeem_points_for_order_point_price', $redeemed_points );

			self::perform_calculation_with_expiry( $redeemed_points, $user_id );
			$points_data = new RS_Points_Data( $user_id );
			$totalpoints = $points_data->total_available_points();
			if ( $totalpoints >= 0 ) {
				$table_args = array(
					'user_id'     => $user_id,
					'usedpoints'  => $redeemed_points,
					'date'        => '999999999999',
					'checkpoints' => 'RPFGW',
					'orderid'     => $order->get_id(),
				);
				self::record_the_points( $table_args );
			}

			$order->update_meta_data( 'manuall_order', '1' );
			$order->update_meta_data( 'refund_gateway', '2' );
			$order->save();
		}

		/**
		 * Modified Points for Products in manual order.
		 * 
		 * @param WP_Post $order Order Object.
		 * */
		public static function modified_points_for_manual_order( $order ) {
			$points          = array();
			$original_points = self::original_points_for_manual_order($order);
			if ( ! srp_check_is_array( $original_points ) ) {
				return $points;
			}

			foreach ( $original_points as $product_id => $point ) {
				$modified_points = self::coupon_points_conversion_for_manual_order( $product_id, $point, $order );
				if ( ! empty( $modified_points ) ) {
					$points[ $product_id ] = $modified_points;
				}
			}

			return $points;
		}

		/**
		 * Original Points for Products
		 * */
		public static function original_points_for_manual_order( $order ) {
			$user_id = $order->get_user_id();
			if ( 'earningonly' === check_banning_type( $user_id ) || 'both' === check_banning_type( $user_id ) ) {
				return array();
			}

			global $totalrewardpoints;
			$points        = array();
			if ( srp_check_is_array( $order->get_items() ) ) {
				foreach ( $order->get_items() as $value ) {
					if ( 'yes' === block_points_for_salepriced_product( $value['product_id'], $value['variation_id'] ) ) {
						continue;
					}

					$args          = array(
						'productid'   => $value['product_id'],
						'variationid' => $value['variation_id'],
						'item'        => $value,
						'order'       => $order,
					);
					$cart_quantity = isset( $value['qty'] ) ? $value['qty'] : 0;
					$product_id    = isset( $value['product_id'] ) ? $value['product_id'] : 0;
					$variation_id  = isset( $value['variation_id'] ) ? $value['variation_id'] : 0;
					$quantity      = rs_get_minimum_quantity_based_on_product_total( $product_id, $variation_id );

					if ( $quantity && $cart_quantity < $quantity ) {
						continue;
					}

					$Points               = check_level_of_enable_reward_point( $args );
					$user_role_percentage = RSMemberFunction::earn_points_percentage( $user_id, (float) $Points );
					if ( empty( $user_role_percentage ) ) {
						continue;
					}

					$totalrewardpoints = $Points;
					$ProductId         = ! empty( $value['variation_id'] ) ? $value['variation_id'] : $value['product_id'];

					if ( ! empty( $totalrewardpoints ) ) {
						if ( isset( $points[ $ProductId ] ) ) {
							$points[ $ProductId ] = $Points + $points[ $ProductId ];
						} else {
							$points[ $ProductId ] = $Points;
						}
					}
				}
			}
			return $points;
		}

		public static function coupon_points_conversion_for_manual_order( $ProductId, $Points, $order ) {

			if ( empty( $Points ) ) {
				return $Points;
			}

			$applied_coupons = $order->get_coupon_codes();
			if ( ! srp_check_is_array( $applied_coupons ) ) {
				return $Points;
			}

			$DiscountedTotal = self::get_coupon_discount_total($order);

			$DiscountedTotal = array_sum( $DiscountedTotal );
			$CouponAmounts   = self::get_product_price_for_individual_product( $ProductId, $Points, $DiscountedTotal, $order );
			if ( ! srp_check_is_array( $CouponAmounts ) ) {
				return $Points;
			}

			$ConversionRate  = array();
			$ConvertedPoints = 0;

			$product_price = self::get_product_price_in_cart( $order );

			foreach ( $order->get_coupon_codes() as $CouponCode ) {
				$CouponObj    = new WC_Coupon( $CouponCode );
				$CouponObj    = srp_coupon_obj( $CouponObj );
				$ProductList  = $CouponObj['product_ids'];
				$CouponAmount = $CouponAmounts[ $CouponCode ][ $ProductId ];
				$LineTotal    = self::get_product_price_for_included_products( $ProductList, $order );

				if ( empty( $ProductList ) && $product_price ) {
					$ConvertedPoints = $DiscountedTotal / $product_price;
				} elseif ( $LineTotal ) {
					$ConvertedPoints = $CouponAmount / $LineTotal;
				}

				$ConvertedAmount = $ConvertedPoints * $Points;
				if ( $Points > $ConvertedAmount ) {
					$ConversionRate[] = $Points - $ConvertedAmount;
				}
			}

			return end( $ConversionRate );
		}

		/**
		 * Get Product Price for individual products.
		 *
		 * @param array $product_id Product ID.
		 */
		public static function get_coupon_discount_total( $order ) {
			$coupon_amount = array();
			foreach ( $order->get_coupon_codes() as $coupon_code ) {
				$coupon_obj   = new WC_Coupon( $coupon_code );
				$coupon_obj   = srp_coupon_obj( $coupon_obj );
				$coupon_amount[] = $coupon_obj['coupon_amount'];
			}
			return $coupon_amount;
		}

		/**
		 * Get Product Price for individual products.
		 *
		 * @param array $product_id Product ID.
		 * @param float $points Points.
		 * @param float $discount_total Discount Total.
		 */
		public static function get_product_price_for_individual_product( $product_id, $points, $discount_total, $order ) {
			$coupon_amount = array();
			foreach ( $order->get_coupon_codes() as $coupon_code ) {
				$coupon_obj   = new WC_Coupon( $coupon_code );
				$coupon_obj   = srp_coupon_obj( $coupon_obj );
				$product_list = $coupon_obj['product_ids'];
				if ( ! empty( $product_list ) ) {
					if ( in_array( $product_id, $product_list ) ) {
						$coupon_amount[ $coupon_code ][ $product_id ] = $discount_total;
					}
				} else {
					$coupon_amount[ $coupon_code ][ $product_id ] = $discount_total;
				}
			}
			return $coupon_amount;
		}

		/**
		 * Get Product Price for included products.
		 *
		 * @param array $product_list Product List.
		 */
		public static function get_product_price_for_included_products( $product_list, $order ) {
			$line_total = array();
			foreach ( $order->get_items() as $item ) {
				$product_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
				if ( in_array( $product_id, $product_list ) ) {
					$line_total[] = $order->get_line_subtotal( $item );
				}
			}
			return array_sum( $line_total );
		}

		/**
		 * Get Product Price in Cart.
		 *
		 * @param array $order Referrer arguments.
		 */
		public static function get_product_price_in_cart( $order ) {
			$price = array();
			foreach ( $order->get_items() as $items ) {
				$args = array(
					'productid'   => $items['product_id'],
					'variationid' => $items['variation_id'],
					'item'        => $items,
					'order'       => $order,
				);

				$points            = check_level_of_enable_reward_point( $args );
				$totalrewardpoints = RSMemberFunction::earn_points_percentage( $order->get_user_id(), (float) $points );

				if ( empty( $totalrewardpoints ) ) {
					continue;
				}

				$price[] = $order->get_line_subtotal($items);
			}
			return array_sum( $price );
		}

		/**
		 * Award Buying Points for User 
		 * 
		 */
		public static function insert_buying_points_for_user( $order_id ) {
			if ( 'yes' !== get_option( 'rs_buyingpoints_activated' ) ) {
				return;
			}

			if ( ! block_points_for_renewal_order_sumo_subscriptions( $order_id, get_option( 'rs_award_buying_point_for_renewal_order' ) ) ) {
					return;
			}

			if ( ! rs_block_points_for_renewal_order_wc_subscriptions( $order_id, get_option( 'rs_award_buying_point_wc_renewal_order' ) ) ) {
					return;
			}

			$order = wc_get_order( $order_id );
			if ( 'yes' === $order->get_meta( 'reward_points_awarded' ) ) {
				return;
			}

			foreach ( $order->get_items() as $item ) {
				$ProductObj = srp_product_object( $item['product_id'] );
				$ProductId  = empty( $item['variation_id'] ) ? $item['product_id'] : $item['variation_id'];
				if ( 'yes' !== get_post_meta( $ProductId, '_rewardsystem_buying_reward_points', true ) && 1 != get_post_meta( $ProductId, '_rewardsystem_buying_reward_points', true ) ) {
					continue;
				}

				$BuyingPoints = get_post_meta( $ProductId, '_rewardsystem_assign_buying_points', true );
				if ( empty( $BuyingPoints ) ) {
					continue;
				}

				$BuyingPoints = (float) $BuyingPoints * $item['qty'];
				$orderobj     = srp_order_obj( $order );
				$user_id      = $orderobj['order_userid'];
				$new_obj      = new RewardPointsOrder( $order_id, 'no' );
				if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
					$new_obj->check_point_restriction( $BuyingPoints, 0, 'RPBSRP', $orderobj['order_userid'], '', '', $item['product_id'], $item['variation_id'], '' );
				} else {
					$valuestoinsert = array(
						'pointstoinsert'    => $BuyingPoints,
						'event_slug'        => 'RPBSRP',
						'user_id'           => $orderobj['order_userid'],
						'product_id'        => $item['product_id'],
						'variation_id'      => $item['variation_id'],
						'totalearnedpoints' => $BuyingPoints,
					);
					$new_obj->total_points_management( $valuestoinsert );
					update_order_meta_if_points_awarded( $order_id, $user_id );
					$order->update_meta_data( 'srp_bp_reward_points_awarded', 'yes' );
				}

				/**
				 * Hook:fp_reward_point_for_buying_sumo_reward_points.
				 *
				 * @since 1.0
				 */
				do_action( 'fp_reward_point_for_buying_sumo_reward_points', $item['product_id'], $BuyingPoints );
			}
			$order->save();
		}

		/**
		 * Redeem Points using Reward Gateway for User.
		 *
		 * @param int $order_id Order ID.
		 * */
		public static function redeem_points_for_using_reward_gateway( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( 'reward_gateway' !== $order->get_payment_method() ) {
				return;
			}

			if ( '2' === $order->get_meta( 'second_time_gateway' ) ) {
				return;
			}

			$redeemed_points = gateway_points( $order_id );
			$total_redeem = $order->update_meta_data( 'total_redeem_points_for_order_point_price' , $redeemed_points);
			$OrderObj    = srp_order_obj( $order );
			$user_id     = $OrderObj['order_userid'];
			self::perform_calculation_with_expiry( $redeemed_points, $user_id );
			$points_data = new RS_Points_Data( $user_id );
			$totalpoints = $points_data->total_available_points();

			if ( $totalpoints >= 0 ) {
				$table_args = array(
					'user_id'     => $user_id,
					'usedpoints'  => $redeemed_points,
					'date'        => '999999999999',
					'checkpoints' => 'RPFGW',
					'orderid'     => $order_id,
				);
				self::record_the_points( $table_args );
			}
			$order->update_meta_data( 'second_time_gateway', '2' );
			$order->save();
		}

		/**
		 * Redeem Points for User.
		 *
		 * @param int $order_id Order ID.
		 * */
		public static function update_redeem_point_for_user( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( '1' === $order->get_meta( 'redeem_point_once' ) ) {
				return;
			}

			$OrderObj    = srp_order_obj( $order );
			$user_id     = $OrderObj['order_userid'];
			$points_data = new RS_Points_Data( $user_id );
			$totalpoints = $points_data->total_available_points();
			$redeempoints = self::get_redeem_points_and_send_sms_when_redeem( $order_id, $user_id );
			if ( $redeempoints ) {
				$pointsredeemed = self::perform_calculation_with_expiry( $redeempoints, $user_id );
				$user_info      = get_user_by( 'id', $user_id );
				$UserName       = $user_info->user_login;
				$AutoRedeem     = 'auto_redeem_' . strtolower( $UserName );
				$Redeem         = 'sumo_' . strtolower( $UserName );
				if ( $totalpoints >= 0 ) {
					$table_args = array(
						'user_id'     => $user_id,
						'usedpoints'  => $redeempoints,
						'date'        => '999999999999',
						'checkpoints' => 'RP',
						'orderid'     => $order_id,
					);
					self::record_the_points( $table_args );
					$used_coupons = (float) WC()->version < (float) ( '3.7' ) ? $order->get_used_coupons() : $order->get_coupon_codes();
					if ( in_array( $Redeem, $used_coupons ) ) {
						/**
						 * Hook:fp_redeem_reward_points_manually.
						 *
						 * @since 1.0
						 */
						do_action( 'fp_redeem_reward_points_manually', $order_id, $pointsredeemed );

						if ( 'yes' == get_option( 'rs_email_activated' ) ) {
							send_mail_for_product_purchase( $user_id, $order_id, 'redeeming' );
						}
					}

					if ( in_array( $AutoRedeem, $used_coupons ) ) {
						/**
						 * Hook:fp_redeem_reward_points_automatically.
						 *
						 * @since 1.0
						 */
						do_action( 'fp_redeem_reward_points_automatically', $order_id, $pointsredeemed );

						if ( 'yes' == get_option( 'rs_email_activated' ) ) {
							send_mail_for_product_purchase( $user_id, $order_id, 'redeeming' );
						}
					}

					$order->update_meta_data( 'redeem_point_once', '1' );
					$order->update_meta_data( 'frontendorder', '1' );
				}
			}

			$order->save();
		}

		public static function get_redeem_points_and_send_sms_when_redeem( $OrderId, $user_id ) {
			if ( empty( $user_id ) ) {
				return;
			}

			$OrderObj       = new WC_Order( $OrderId );
			$AppliedCoupons = $OrderObj->get_items( array( 'coupon' ) );
			if ( ! srp_check_is_array( $AppliedCoupons ) ) {
				return;
			}

			$user_info  = get_user_by( 'id', $user_id );
			$UserName   = $user_info->user_login;
			$Redeem     = 'sumo_' . strtolower( $UserName );
			$AutoRedeem = 'auto_redeem_' . strtolower( $UserName );
			foreach ( $AppliedCoupons as $coupon ) {

				if ( ! is_object( $coupon ) ) {
					continue;
				}

				$coupon_name = $coupon->get_name();

				if ( $coupon_name == $Redeem || $coupon_name == $AutoRedeem ) {
					if ( '1' == get_option( 'rewardsystem_looped_over_coupon' . $OrderId ) ) {
						continue;
					}

					$CouponIds    = ( $coupon_name == $AutoRedeem ) ? get_user_meta( $user_id, 'auto_redeemcoupon_ids', true ) : get_user_meta( $user_id, 'redeemcouponids', true );
					$DiscountAmnt = $coupon['discount_amount'];
					if ( 'incl' == get_option( 'woocommerce_tax_display_cart' ) && 'no' == get_option( 'woocommerce_prices_include_tax' ) ) {
						$DiscountAmnt = $coupon['discount_amount'] + $coupon['discount_amount_tax'];
					} elseif ( 'incl' == get_option( 'woocommerce_tax_display_cart' ) && 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) {
						$DiscountAmnt = $coupon['discount_amount'] + $coupon['discount_amount_tax'];
					} elseif ( 'excl' == get_option( 'woocommerce_tax_display_cart' ) && 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) {
						$DiscountAmnt = $coupon['discount_amount'] + $coupon['discount_amount_tax'];
					}
					$RedeemedPoints = redeem_point_conversion( $DiscountAmnt, $user_id );
					if ( 'yes' == get_option( 'rs_sms_activated' ) && 'yes' == get_option( 'rs_enable_send_sms_to_user' ) ) {
						if ( 'yes' == get_option( 'rs_send_sms_redeeming_points' ) ) {
							$PhoneNumber = ! empty( get_user_meta( $user_id, 'rs_phone_number_value_from_signup', true ) ) ? get_user_meta( $user_id, 'rs_phone_number_value_from_signup', true ) : get_user_meta( $user_id, 'rs_phone_number_value_from_account_details', true );
							$PhoneNumber = ! empty( $PhoneNumber ) ? $PhoneNumber : get_user_meta( $user_id, 'billing_phone', true );
							if ( '1' == get_option( 'rs_sms_sending_api_option' ) ) {
								RSFunctionForSms::send_sms_twilio_api( $OrderId, 'redeeming', $RedeemedPoints, $PhoneNumber );
							} elseif ( '2' == get_option( 'rs_sms_sending_api_option' ) ) {
								RSFunctionForSms::send_sms_nexmo_api( $OrderId, 'redeeming', $RedeemedPoints, $PhoneNumber );
							}
						}
					}

					update_option( 'rewardsystem_looped_over_coupon' . $OrderId, '1' );
					return $RedeemedPoints;
				}
			}
		}

		/**
		 * Update Revised Redeem Point for User.
		 *
		 * @param int $order_id Order ID.
		 * */
		public static function update_revised_redeem_points_for_user( $order_id ) {
			$order = wc_get_order( $order_id );

			if ( '2' === $order->get_meta( 'revise_redeem_point_once' ) ) {
				return;
			}

			$order_obj    = srp_order_obj( $order );
			$user_id      = $order_obj['order_userid'];
			$redeempoints = self::update_revised_reward_points_to_user( $order_id, $user_id );
			if ( empty( $redeempoints ) ) {
				return;
			}

			$table_args = array(
				'user_id'           => $user_id,
				'pointstoinsert'    => $redeempoints,
				'checkpoints'       => 'RVPFRP',
				'totalearnedpoints' => $redeempoints,
				'orderid'           => $order_id,
			);
			self::insert_earning_points( $table_args );
			self::record_the_points( $table_args );

			self::reset_maximum_points_restriction_per_day( $table_args );

			update_option( 'rewardsystem_looped_over_coupon' . $order_id, '' );

			$order->update_meta_data( 'revise_redeem_point_once', '2' );
			$order->save();
		}

		public static function reset_maximum_points_restriction_per_day( $table_args ) {
			$max_pts_restriction_per_day = get_option( 'rs_maximum_redeeming_per_day_restriction' );
			if ( ! $max_pts_restriction_per_day ) {
				return;
			}

			$user_id = isset( $table_args['user_id'] ) ? absint( $table_args['user_id'] ) : 0;
			$user    = get_user_by( 'ID', $user_id );
			if ( ! is_object( $user ) ) {
				return;
			}

			$used_points_in_order = floatval( $table_args['pointstoinsert'] );
			if ( ! $used_points_in_order || $used_points_in_order > $max_pts_restriction_per_day ) {
				return;
			}

			$current_time                = strtotime( gmdate( 'Y-m-d' ) );
			$stored_max_pts_per_day_data = get_user_meta( $user_id, 'rs_maximum_points_restriction_per_day', true );
			if ( empty( $stored_max_pts_per_day_data ) ) {
				return;
			}

			$stored_max_pts_per_day = isset( $stored_max_pts_per_day_data[ $current_time ] ) ? $stored_max_pts_per_day_data[ $current_time ] : 0;
			if ( ! $stored_max_pts_per_day ) {
				return;
			}

			$diff_value = floatval( $stored_max_pts_per_day ) - $used_points_in_order;
			$diff_value = $diff_value > 0 ? $diff_value : 0;

			update_user_meta( $user_id, 'rs_maximum_points_restriction_per_day', array( $current_time => $diff_value ) );
		}

		public static function signup_points_after_purchase( $order_id ) {
			$order    = wc_get_order( $order_id );
			$OrderObj = srp_order_obj( $order );
			$user_id  = $OrderObj['order_userid'];
			if ( ! empty( $user_id ) ) {
				global $wpdb;
				$db          = &$wpdb;
				$order_ids   = $db->get_results(
					$db->prepare(
						"SELECT posts.ID
			FROM $db->posts as posts
			LEFT JOIN {$db->postmeta} AS meta ON posts.ID = meta.post_id
			WHERE   meta.meta_key       = '_customer_user'
			AND     posts.post_type     IN ('" . implode( "','", wc_get_order_types( 'order-count' ) ) . "')
			AND     posts.post_status   IN ('" . implode( "','", array_keys( wc_get_order_statuses() ) ) . "')
			AND     meta_value          = %d
		",
						$user_id
					),
					ARRAY_A
				);
				$order_count = count( $order_ids );
				if ( '1' === get_option( 'rs_select_referral_points_award' ) ) {
					if ( 'yes' === get_option( 'rs_referral_reward_signup_after_first_purchase' ) || 'yes' === get_option( 'rs_reward_signup_after_first_purchase' ) ) {
						self::reward_points_after_first_purchase( $order_id );
					}
				}

				if ( '2' === get_option( 'rs_select_referral_points_award' ) ) {
					if ( '' !== get_option( 'rs_number_of_order_for_referral_points' ) ) {
						if ( get_option( 'rs_number_of_order_for_referral_points' ) <= $order_count ) {
							self::reward_points_after_first_purchase( $order_id );
						}
					}
				}

				if ( '3' === get_option( 'rs_select_referral_points_award' ) ) {
					if ( '' !== get_option( 'rs_amount_of_order_for_referral_points' ) ) {
						foreach ( $order_ids as $values ) {
							$order_obj = wc_get_order( $values['ID'] );
							$total[]   = $order_obj->get_meta( '_order_total' );
						}
						$order_total = array_sum( $total );
						if ( get_option( 'rs_amount_of_order_for_referral_points' ) <= $order_total ) {
							self::reward_points_after_first_purchase( $order_id );
						}
					}
				}
			}
			if ( '1' === get_option( 'rs_referral_reward_signup_getting_refer' ) && 'yes' === get_option( 'rs_referral_reward_getting_refer_after_first_purchase' ) ) {
				self::reward_points_after_first_purchase_get_refer( $order_id );
			}
		}

		public static function reward_points_after_first_purchase( $order_id ) {
			$Order    = wc_get_order( $order_id );
			$OrderObj = srp_order_obj( $Order );
			$user_id  = $OrderObj['order_userid'];
			if ( empty( $user_id ) ) {
				return;
			}

			if ( 'yes' === get_user_meta( $user_id, 'rs_after_first_purchase', true ) ) {
				return;
			}

			$fetchdata = get_user_meta( $user_id, 'srp_data_for_reg_points', true );
			if ( ! srp_check_is_array( $fetchdata ) ) {
				return;
			}

			$curregpoints   = isset( $fetchdata[ $user_id ]['points'] ) ? $fetchdata[ $user_id ]['points'] : 0;
			$refregpoints   = isset( $fetchdata[ $user_id ]['refpoints'] ) ? $fetchdata[ $user_id ]['refpoints'] : 0;
			$userid         = $fetchdata[ $user_id ]['userid'];
			$refuserid      = $fetchdata[ $user_id ]['refuserid'];
			$event_slug     = isset( $fetchdata[ $user_id ]['event_slug'] ) ? $fetchdata[ $user_id ]['event_slug'] : '';
			$reasonindetail = isset( $fetchdata[ $user_id ]['reaseonidetail'] ) ? $fetchdata[ $user_id ]['reaseonidetail'] : '';
			$checkredeeming = self::check_redeeming_in_order( $order_id, $user_id );
			if ( '1' !== get_user_meta( $userid, '_points_awarded', true ) && 'yes' === get_option( 'rs_reward_action_activated' ) ) {
				if ( 'yes' === $Order->get_meta( 'rs_check_enable_option_for_redeeming' ) && false == $checkredeeming ) {
					$table_args = array(
						'user_id'           => $user_id,
						'pointstoinsert'    => $curregpoints,
						'checkpoints'       => $event_slug,
						'totalearnedpoints' => $curregpoints,
						'orderid'           => $order_id,
						'reason'            => $reasonindetail,
					);
					self::insert_earning_points( $table_args );
					self::record_the_points( $table_args );
				} else {
					$table_args = array(
						'user_id'           => $user_id,
						'pointstoinsert'    => $curregpoints,
						'checkpoints'       => $event_slug,
						'totalearnedpoints' => $curregpoints,
						'orderid'           => $order_id,
						'reason'            => $reasonindetail,
					);
					self::insert_earning_points( $table_args );
					self::record_the_points( $table_args );
				}
				add_user_meta( $user_id, '_points_awarded', '1' );
			}

			if ( $refuserid ) {
				if ( '1' !== get_user_meta( $user_id, 'rs_referrer_regpoints_awarded', true ) && 'yes' === get_option( 'rs_referral_activated' ) ) {
					$new_obj = new RewardPointsOrder( $order_id, $apply_previous_order_points = 'no' );
					if ( 'yes' === get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
						$new_obj->check_point_restriction( $refregpoints, $pointsredeemed = 0, $event_slug     = 'RRRP', $refuserid, $nomineeid      = '', $user_id, $productid      = '', $variationid    = '', $reasonindetail = '' );
					} else {
						$valuestoinsert = array(
							'pointstoinsert'    => $refregpoints,
							'event_slug'        => 'RRRP',
							'user_id'           => $refuserid,
							'referred_id'       => $user_id,
							'totalearnedpoints' => $refregpoints,
						);
						$new_obj->total_points_management( $valuestoinsert );
						$previouslog = get_option( 'rs_referral_log' );
						RS_Referral_Log::update_referral_log( $refuserid, $user_id, $refregpoints, array_filter( (array) $previouslog ) );
						update_user_meta( $user_id, '_rs_i_referred_by', $refuserid );

						if ( 'yes' === get_option( 'rs_enable_referral_bonus_reward_signup', 'no' ) ) {
							$referreduser_count  = RS_Referral_Log::corresponding_referral_count( $refuserid );
							$no_of_users         = get_option( 'rs_no_of_users_referral_to_get_reward_signup_bonus', '0' );
							$bonus_reward_points = get_option( 'rs_referral_reward_signup_bonus_points', '' );

							if ( '1' === get_option( 'rs_referral_reward_signup_bonus', '1' ) ) {
								if ( $referreduser_count == $no_of_users ) {
									$valuestoinsert = array(
										'pointstoinsert' => $bonus_reward_points,
										'event_slug'     => 'RRRPB',
										'user_id'        => $refuserid,
										'referred_id'    => $user_id,
										'totalearnedpoints' => $bonus_reward_points,
									);
									$new_obj->total_points_management( $valuestoinsert );
									$previouslog = get_option( 'rs_referral_log' );
									RS_Referral_Log::update_referral_log( $refuserid, $user_id, $bonus_reward_points, array_filter( (array) $previouslog ) );
									update_user_meta( $user_id, '_rs_i_referred_by', $refuserid );
								}
							} elseif ( ( $referreduser_count % $no_of_users ) == 0 ) {
									$valuestoinsert = array(
										'pointstoinsert' => $bonus_reward_points,
										'event_slug'     => 'RRRPB',
										'user_id'        => $refuserid,
										'referred_id'    => $user_id,
										'totalearnedpoints' => $bonus_reward_points,
									);
									$new_obj->total_points_management( $valuestoinsert );
									$previouslog = get_option( 'rs_referral_log' );
									RS_Referral_Log::update_referral_log( $refuserid, $user_id, $bonus_reward_points, array_filter( (array) $previouslog ) );
									update_user_meta( $user_id, '_rs_i_referred_by', $refuserid );
							}
						}
					}
										/**
										 * Hook:fp_signup_points_for_referrer.
										 *
										 * @since 1.0
										 */
					do_action( 'fp_signup_points_for_referrer', $refuserid, $user_id, $refregpoints );

					add_user_meta( $user_id, 'rs_referrer_regpoints_awarded', '1' );
				}
			}
			add_user_meta( $user_id, 'rs_after_first_purchase', 'yes' );
		}

		public static function reward_points_after_first_purchase_get_refer( $order_id ) {
			if ( 'yes' != get_option( 'rs_referral_activated' ) ) {
				return;
			}

			$Order    = new WC_Order( $order_id );
			$OrderObj = srp_order_obj( $Order );
			$user_id  = $OrderObj['order_userid'];
			if ( empty( $user_id ) ) {
				return;
			}

			if ( 'yes' == get_user_meta( $user_id, 'rs_after_first_purchase_get_refer', true ) ) {
				return;
			}

			$fetchdata = get_user_meta( $user_id, 'srp_data_for_get_referred_reg_points', true );
			if ( ! srp_check_is_array( $fetchdata ) ) {
				return;
			}

			if ( '1' == get_user_meta( $user_id, '_points_awarded_get_refer', true ) ) {
				return;
			}

			$refregpoints = $fetchdata[ $user_id ]['refpoints'];
			$refuserid    = $fetchdata[ $user_id ]['userid'];
			$new_obj      = new RewardPointsOrder( $order_id, 'no' );
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$new_obj->check_point_restriction( $refregpoints, 0, 'RRPGR', $user_id, '', $refuserid, '', '', '' );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $refregpoints,
					'event_slug'        => 'RRPGR',
					'user_id'           => $user_id,
					'referred_id'       => $refuserid,
					'totalearnedpoints' => $refregpoints,
				);
				$new_obj->total_points_management( $valuestoinsert );
			}
						/**
						 * Hook:fp_signup_points_for_getting_referred.
						 *
						 * @since 1.0
						 */
			do_action( 'fp_signup_points_for_getting_referred', $refuserid, $user_id, $refregpoints );

			add_user_meta( $user_id, '_points_awarded_get_refer', '1' );
			add_user_meta( $user_id, 'rs_after_first_purchase_get_refer', 'yes' );
		}

		public static function check_if_expiry() {
			global $wpdb;
			$Data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rspointexpiry WHERE expirydate < %d and expirydate NOT IN(999999999999) and expiredpoints IN(0) and userid = %d", time(), get_current_user_id() ), ARRAY_A );
			if ( ! srp_check_is_array( $Data ) ) {
				return;
			}

			foreach ( $Data as $key => $eacharray ) {
				$wpdb->update( "{$wpdb->prefix}rspointexpiry", array( 'expiredpoints' => $eacharray['earnedpoints'] - $eacharray['usedpoints'] ), array( 'id' => $eacharray['id'] ) );
			}
			foreach ( WC()->cart->get_applied_coupons() as $coupon_code ) {
				$coupon        = new WC_Coupon( $coupon_code );
				$coupon_obj    = srp_coupon_obj( $coupon );
				$coupon_amount = $coupon_obj['coupon_amount'];
				if ( strpos( $coupon_code, 'sumo_' ) || strpos( $coupon_code, 'auto_redeem_' ) ) {
					$coupon_remove_check = self::remove_sumo_coupon_after_points_expiry( $coupon_amount );
					if ( $coupon_remove_check ) {
						WC()->cart->remove_coupon( $coupon_code );
					}
				}
			}
			send_mail_for_thershold_points();
		}

		public static function remove_sumo_coupon_after_points_expiry( $coupon_amount ) {
			$points_data     = new RS_Points_Data( get_current_user_id() );
			$points          = $points_data->total_available_points();
			$available_price = redeem_point_conversion( $points, get_current_user_id(), 'price' );
			return ( $available_price > $coupon_amount ) ? false : true;
		}

		public static function delete_if_used() {
			global $wpdb;
			$userid = get_current_user_id();
			$Data   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rspointexpiry WHERE earnedpoints=usedpoints and expiredpoints IN(0) and userid = %d", $userid ), ARRAY_A );

			if ( srp_check_is_array( $Data ) ) {

				$totalearnedpoints = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(earnedpoints) FROM {$wpdb->prefix}rspointexpiry WHERE earnedpoints=usedpoints and expiredpoints IN(0) and userid = %d", $userid ) );
				$totalusedpoints   = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(usedpoints) FROM {$wpdb->prefix}rspointexpiry WHERE earnedpoints=usedpoints and expiredpoints IN(0) and userid = %d", $userid ) );

				$earned_points_before_delete = array_sum( $totalearnedpoints ) + (float) get_user_meta( $userid, 'rs_earned_points_before_delete', true );
				$used_points_before_delete   = array_sum( $totalusedpoints ) + (float) get_user_meta( $userid, 'rs_redeem_points_before_delete', true );

				update_user_meta( $userid, 'rs_earned_points_before_delete', $earned_points_before_delete );
				update_user_meta( $userid, 'rs_redeem_points_before_delete', $used_points_before_delete );

				foreach ( $Data as $eacharray ) {
					$wpdb->delete( "{$wpdb->prefix}rspointexpiry", array( 'id' => $eacharray['id'] ) );
				}
			}
		}

		public static function delete_if_expired() {
			global $wpdb;
			$userid = get_current_user_id();
			$Data   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rspointexpiry WHERE earnedpoints=(usedpoints+expiredpoints) and expiredpoints NOT IN(0) and userid = %d", $userid ), ARRAY_A );

			if ( srp_check_is_array( $Data ) ) {

				$totalearnedpoints  = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(earnedpoints) FROM {$wpdb->prefix}rspointexpiry WHERE earnedpoints=(usedpoints+expiredpoints) and expiredpoints NOT IN(0) and userid = %d", $userid ) );
				$totalusedpoints    = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(usedpoints) FROM {$wpdb->prefix}rspointexpiry WHERE earnedpoints=(usedpoints+expiredpoints) and expiredpoints NOT IN(0) and userid = %d", $userid ) );
				$totalexpiredpoints = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(expiredpoints) FROM {$wpdb->prefix}rspointexpiry WHERE earnedpoints=(usedpoints+expiredpoints) and expiredpoints NOT IN(0) and userid = %d", $userid ) );

				$earned_points_before_delete  = array_sum( $totalearnedpoints ) + (float) get_user_meta( $userid, 'rs_earned_points_before_delete', true );
				$used_points_before_delete    = array_sum( $totalusedpoints ) + (float) get_user_meta( $userid, 'rs_redeem_points_before_delete', true );
				$expired_points_before_delete = array_sum( $totalexpiredpoints ) + (float) get_user_meta( $userid, 'rs_expired_points_before_delete', true );

				update_user_meta( $userid, 'rs_earned_points_before_delete', $earned_points_before_delete );
				update_user_meta( $userid, 'rs_redeem_points_before_delete', $used_points_before_delete );
				update_user_meta( $userid, 'rs_expired_points_before_delete', $expired_points_before_delete );

				foreach ( $Data as $eacharray ) {
					$wpdb->delete( "{$wpdb->prefix}rspointexpiry", array( 'id' => $eacharray['id'] ) );
				}
			}
		}

		/* Get the Paypal ID or Custom Payment Details */

		public static function get_paypal_id_form_cashback_form( $userid ) {
			if ( empty( $userid ) ) {
				return;
			}

			global $wpdb;
			$table_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sumo_reward_encashing_submitted_data WHERE userid=%d", $userid ), ARRAY_A );
			foreach ( $table_data as $data ) {
				$data_to_return = ( 'encash_through_paypal_method' == $data['encashpaymentmethod'] ) ? $data['paypalemailid'] : $data['otherpaymentdetails'];
			}
			return $data_to_return;
		}

		/**
		 * Insert the Data based on Point Expiry.
		 *
		 * @param array $args Aruguments.
		 * */
		public static function insert_earning_points( $args = array() ) {
			$default_args = array(
				'pointstoinsert'    => 0,
				'usedpoints'        => 0,
				'date'              => expiry_date_for_points(),
				'orderid'           => 0,
				'totalearnedpoints' => 0,
				'totalredeempoints' => 0,
				'reason'            => '',
			);
			$table_args   = wp_parse_args( $args, $default_args );
			extract( $table_args );
			if ( empty( $user_id ) ) {
				return;
			}

			global $wpdb;
			$earned_points = 'yes' === get_option( 'rs_enable_round_off_type_for_calculation' ) ? round_off_type( $pointstoinsert, array(), false ) : (float) $pointstoinsert;
			$noofday       = 'yes' === get_option( 'rs_point_expiry_activated' ) ? get_option( 'rs_point_to_be_expire' ) : 0;
			if ( empty( $noofday ) ) {
				$query = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rspointexpiry WHERE userid = %d and expirydate = '999999999999'", $user_id ), ARRAY_A );
				if ( ! empty( $query ) && 999999999999 == $date ) {
					$oldearnedpoints = $query['earnedpoints'] + $earned_points;
					$usedpoints      = $usedpoints + $query['usedpoints'];
					$wpdb->update(
						"{$wpdb->prefix}rspointexpiry",
						array(
							'earnedpoints' => $oldearnedpoints,
							'usedpoints'   => $usedpoints,
						),
						array( 'id' => $query['id'] )
					);
				} else {
					$wpdb->insert(
						"{$wpdb->prefix}rspointexpiry",
						array(
							'earnedpoints'      => $earned_points,
							'usedpoints'        => $usedpoints,
							'expiredpoints'     => 0,
							'userid'            => $user_id,
							'earneddate'        => time(),
							'expirydate'        => $date,
							'checkpoints'       => $checkpoints,
							'orderid'           => $orderid,
							'totalearnedpoints' => $totalearnedpoints,
							'totalredeempoints' => $totalredeempoints,
							'reasonindetail'    => $reason,
						)
					);
				}
			} else {
				$wpdb->insert(
					"{$wpdb->prefix}rspointexpiry",
					array(
						'earnedpoints'      => $earned_points,
						'usedpoints'        => $usedpoints,
						'expiredpoints'     => '0',
						'userid'            => $user_id,
						'earneddate'        => time(),
						'expirydate'        => $date,
						'checkpoints'       => $checkpoints,
						'orderid'           => $orderid,
						'totalearnedpoints' => $totalearnedpoints,
						'totalredeempoints' => $totalredeempoints,
						'reasonindetail'    => $reason,
					)
				);
			}
		}

		public static function record_the_points( $args = array() ) {
			$check_points = isset( $args['checkpoints'] ) ? $args['checkpoints'] : '';
			$default_args = array(
				'pointstoinsert'    => 0,
				'usedpoints'        => 0,
				'date'              => expiry_date_for_points( $check_points ),
				'orderid'           => 0,
				'totalearnedpoints' => 0,
				'totalredeempoints' => 0,
				'reason'            => '',
				'productid'         => '',
				'variationid'       => '',
				'refuserid'         => 0,
				'nomineeid'         => 0,
				'nomineepoints'     => 0,
			);
			$table_args   = wp_parse_args( $args, $default_args );
			extract( $table_args );
			if ( empty( $user_id ) ) {
				return;
			}

			global $wpdb;
			$points_data = new RS_Points_Data( $user_id );
			$points_data->reset( $user_id );
			$points        = $points_data->total_available_points();
			$earned_points = 'yes' == get_option( 'rs_enable_round_off_type_for_calculation' ) ? round_off_type( $pointstoinsert, array(), false ) : (float) $pointstoinsert;
			$earned_time   = time();
						$wpdb->insert(
							"{$wpdb->prefix}rsrecordpoints",
							array(
								'earnedpoints'             => $earned_points,
								'redeempoints'             => $usedpoints,
								'userid'                   => $user_id,
								'earneddate'               => $earned_time,
								'expirydate'               => $date,
								'checkpoints'              => $checkpoints,
								'earnedequauivalentamount' => earn_point_conversion( $earned_points ),
								'redeemequauivalentamount' => redeem_point_conversion( $usedpoints, $user_id, 'price' ),
								'productid'                => $productid,
								'variationid'              => $variationid,
								'orderid'                  => $orderid,
								'refuserid'                => $refuserid,
								'reasonindetail'           => $reason,
								'totalpoints'              => $points,
								'showmasterlog'            => false,
								'showuserlog'              => false,
								'nomineeid'                => $nomineeid,
								'nomineepoints'            => $nomineepoints,
							)
						);

			if ( 'RRP' == $checkpoints || 'RRPGR' == $checkpoints ) {
				$to        = get_user_by( 'id', $user_id )->user_email;
				$user_name = get_user_by( 'id', $user_id )->user_login;
				rs_send_mail_for_actions( $to, $checkpoints, $earned_points, $user_name );
			}

			/**
						 * Hook:fp_reward_points_after_recorded.
						 *
						 * @since 24.8.2
						 */
			do_action( 'fp_reward_points_after_recorded', $user_id, $earned_points, $usedpoints, $earned_time, $table_args );
		}

		public static function perform_calculation_with_expiry( $redeempoints, $user_id ) {
			if ( empty( $user_id ) ) {
				return $redeempoints;
			}

			global $wpdb;
			$Data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rspointexpiry WHERE earnedpoints-usedpoints NOT IN(0) and  expiredpoints IN(0) and userid=%d ORDER BY expirydate ASC", $user_id ), ARRAY_A );
			if ( ! srp_check_is_array( $Data ) ) {
				return $redeempoints;
			}

			foreach ( $Data as $key => $eachrow ) {
				$BalancePoints = $eachrow['earnedpoints'] - $eachrow['usedpoints'];
				if ( $redeempoints >= $BalancePoints ) {
					$usedpoints   = $eachrow['usedpoints'] + $BalancePoints;
					$id           = $eachrow['id'];
					$redeempoints = $redeempoints - $BalancePoints;

					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}rspointexpiry SET usedpoints = %s WHERE id = %d", $usedpoints, $id ) );
					if ( empty( $redeempoints ) ) {
						break;
					}
				} else {
					$usedpoints = (float) $eachrow['usedpoints'] + (float) $redeempoints;
					$id         = $eachrow['id'];
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}rspointexpiry SET usedpoints = %s  WHERE id = %d", $usedpoints, $id ) );
					break;
				}
			}
			return $redeempoints;
		}

		/**
		 * Update Revised Points for User.
		 *
		 * @param int $order_id Order ID.
		 */
		public static function update_revised_points_for_user( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( 'yes' !== $order->get_meta( 'reward_points_awarded' ) ) {
				return;
			}

			$new_obj = new RewardPointsOrder( $order_id, 'no' );

			if ( $new_obj->check_redeeming_in_order() ) {
				return;
			}

			$order_obj             = srp_order_obj( $order );
			$orderuserid           = $order_obj['order_userid'];
			$order_status          = $order_obj['order_status'];
			$order_status          = str_replace( 'wc-', '', $order_status );
			$selected_order_status = get_option( 'rs_order_status_control', array( 'processing', 'completed' ) );
			if ( in_array( $order_status, $selected_order_status ) ) {
				return;
			}

			if ( 'yes' === $order->get_meta( 'srp_gateway_points_awarded' ) ) {
				$getpaymentgatewayused = points_for_payment_gateways( $order_id, $orderuserid, $order_obj['payment_method'] );
				$getpaymentgatewayused = RSMemberFunction::earn_points_percentage( $orderuserid, (float) $getpaymentgatewayused );
				if ( ! empty( $getpaymentgatewayused ) ) {
					$valuestoinsert = array(
						'pointsredeemed'    => $getpaymentgatewayused,
						'event_slug'        => 'RVPFRPG',
						'user_id'           => $orderuserid,
						'totalredeempoints' => $getpaymentgatewayused,
					);
					$new_obj->total_points_management( $valuestoinsert );
				}
			}
			if ( '1' !== $order->get_meta( 'rs_revised_points_once' ) ) {
				if ( 'yes' === get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
					$product_ids = $order->get_meta( 'points_for_current_order' );
					self::insert_revised_points( $orderuserid, $order_id, $product_ids );
				} elseif ( '1' === get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
						$product_ids = $order->get_meta( 'points_for_current_order' );
						self::insert_revised_points( $orderuserid, $order_id, $product_ids );
				} elseif ( '2' === get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					self::insert_revised_points_based_on_carttotal( $orderuserid, $order_id );
				} else {
					self::insert_revised_points_based_on_range( $orderuserid, $order_id );
				}
				$referreduser = $order->get_meta( '_referrer_name' );
				if ( '' !== $referreduser ) {
					if ( '1' === get_option( 'rs_award_points_for_cart_or_product_total_for_refferal_system', 1 ) ) {
						$product_ids = $order->get_meta( 'rsgetreferalpoints' );
						self::insert_revised_get_refer_points( 0, $orderuserid, $order_id, $product_ids );
						self::insert_revised_referral_points( 0, $referreduser, $orderuserid, $order_id, $new_obj, $order );
					} else {
						self::insert_revised_referrer_points_based_on_cart_total( $order_id, $referreduser, $new_obj );
						self::insert_revised_referred_points_based_on_cart_total( $order_id );
					}
				}
				$order->update_meta_data( 'rs_revised_points_once', '1' );
			}
			$order->update_meta_data( 'earning_point_once', '2' );
			$order->save();
		}

		/**
		 * Insert Revised Points for Referrer.
		 *
		 * @param int     $order_id Order ID.
		 * @param int     $referrer_user_id Referrer ID.
		 * @param WP_Post $reward_points_order_obj Order object.
		 */
		public static function insert_revised_referrer_points_based_on_cart_total( $order_id, $referrer_user_id, $reward_points_order_obj ) {
			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			$referrer    = is_object( get_user_by( 'ID', $referrer_user_id ) ) ? get_user_by( 'ID', $referrer_user_id ) : get_user_by( 'login', $referrer_user_id );
			$referrer_id = is_object( $referrer ) ? $referrer->ID : '';

			if ( empty( $referrer_id ) ) {
				return;
			}

			$referrer_points = $order->get_meta( 'rs_referrer_points_based_on_cart_total' );
			if ( empty( $referrer_points ) ) {
				return;
			}

			$valuestoinsert = array(
				'pointsredeemed'    => $referrer_points,
				'event_slug'        => 'RVPFPPRRPCT',
				'user_id'           => $referrer_id,
				'referred_id'       => $order->get_user_id(),
				'totalredeempoints' => $referrer_points,
			);

			$reward_points_order_obj->total_points_management( $valuestoinsert );
		}

		/**
		 * Insert Revised Points for Referrer.
		 *
		 * @param int $order_id Order ID.
		 */
		public static function insert_revised_referred_points_based_on_cart_total( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			$referred_points = $order->get_meta( 'rs_referred_points_based_on_cart_total' );
			if ( empty( $referred_points ) ) {
				return;
			}

			$table_args = array(
				'user_id'           => $order->get_user_id(),
				'pointstoinsert'    => 0,
				'usedpoints'        => $referred_points,
				'checkpoints'       => 'RVPPRRPGCT',
				'totalearnedpoints' => 0,
				'orderid'           => $order_id,
			);

			self::insert_earning_points( $table_args );
			self::record_the_points( $table_args );
		}

		public static function update_revised_reward_points_to_user( $order_id, $orderuserid ) {
			// Inside Loop
			$Order = new WC_Order( $order_id );
			if ( ! is_object( $Order ) ) {
				return 0;
			}

			$AppliedCoupons = $Order->get_items( array( 'coupon' ) );
			if ( ! srp_check_is_array( $AppliedCoupons ) ) {
				return 0;
			}

			$user_info  = get_user_by( 'id', $orderuserid );
			$UserName   = $user_info->user_login;
			$Redeem     = 'sumo_' . strtolower( $UserName );
			$AutoRedeem = 'auto_redeem_' . strtolower( $UserName );
			foreach ( $AppliedCoupons as $couponcode => $value ) {
				if ( $value['name'] == $Redeem || $value['name'] == $AutoRedeem ) {
					$getcouponid   = get_user_meta( $orderuserid, 'redeemcouponids', true );
					$currentamount = get_post_meta( $getcouponid, 'coupon_amount', true );
					$tax_value     = ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) && isset( $value['discount_tax'] ) ) ? $value['discount_tax'] : 0;
					$discount_amnt = $value['discount_amount'] + $tax_value;
					if ( $currentamount && $currentamount < $value['discount_amount'] ) {
						continue;
					}

					$redeemedpoints = redeem_point_conversion( $discount_amnt, $orderuserid );

					return $redeemedpoints;
				}
			}
		}

		public static function insert_revised_get_refer_points( $pointstoearn, $orderuserid, $order_id, $product_ids ) {
			if ( ! empty( $product_ids ) ) {
				foreach ( $product_ids as $key => $value ) {
					$used_points = RSMemberFunction::earn_points_percentage( $orderuserid, (float) $value );
					if ( ! $used_points ) {
						continue;
					}

					$table_args = array(
						'user_id'           => $orderuserid,
						'pointstoinsert'    => $pointstoearn,
						'usedpoints'        => $used_points,
						'productid'         => $key,
						'variationid'       => $key,
						'checkpoints'       => 'RVPPRRPG',
						'totalearnedpoints' => $pointstoearn,
						'orderid'           => $order_id,
					);
					self::insert_earning_points( $table_args );
					self::record_the_points( $table_args );
				}
			}
		}

		public static function insert_revised_points( $orderuserid, $order_id, $points_data ) {
			global $wpdb;

			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) && ! empty( get_option( 'rs_max_earning_points_for_user' ) ) ) {
				$points_to_revise = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(earnedpoints) as earnedpoints FROM {$wpdb->prefix}rsrecordpoints WHERE userid = %d and orderid = %d and expirydate NOT IN(0) and checkpoints IN('MREPFU','PPRP')", $orderuserid, $order_id ), ARRAY_A );
				if ( ! empty( $points_to_revise[0]['earnedpoints'] ) ) {
					self::revise_product_purchase_points( $points_data, $orderuserid, $order_id, $points_to_revise[0]['earnedpoints'] );
				}
			} elseif ( ! empty( $points_data ) ) {
				self::revise_product_purchase_points( $points_data, $orderuserid, $order_id, false );
			}

			self::insert_buying_points( $orderuserid, $order_id );
			self::insert_first_purchase_points( $orderuserid, $order_id );
		}

		/**
		 * Insert Revised Product Purchase Points.
		 *
		 * @param array $points_data Points Data.
		 * @param int   $orderuserid User ID.
		 * @param int   $order_id Order ID.
		 * @param bool  $points_to_revise Whether to revise points or not.
		 */
		public static function revise_product_purchase_points( $points_data, $orderuserid, $order_id, $points_to_revise = false ) {
			$order = wc_get_order( $order_id );
			if ( 'yes' !== $order->get_meta( 'srp_pp_reward_points_awarded' ) ) {
				return;
			}

			$table_args = array(
				'user_id'     => $orderuserid,
				'checkpoints' => 'RVPFPPRP',
				'orderid'     => $order_id,
			);

			if ( $points_to_revise ) {
				$table_args['usedpoints'] = RSMemberFunction::earn_points_percentage( $orderuserid, (float) $points_to_revise );
				self::insert_earning_points( $table_args );
				self::record_the_points( $table_args );
			} else {
				foreach ( $points_data as $product_id => $value ) {
					$usedpoints                = RSMemberFunction::earn_points_percentage( $orderuserid, (float) $value );
					$table_args['usedpoints']  = ( 'yes' === get_option( 'rs_enable_round_off_type_for_calculation' ) ) ? round_off_type( $usedpoints, array(), false ) : (float) $usedpoints;
					$table_args['productid']   = $product_id;
					$table_args['variationid'] = $product_id;

					self::insert_earning_points( $table_args );
					self::record_the_points( $table_args );
				}
			}
		}

		/**
		 * Insert Revised Product Purchase Points.
		 *
		 * @param int $orderuserid User ID.
		 * @param int $order_id Order ID.
		 */
		public static function insert_revised_points_based_on_carttotal( $orderuserid, $order_id ) {
			self::insert_buying_points( $orderuserid, $order_id );
			self::insert_first_purchase_points( $orderuserid, $order_id );

			global $wpdb;

			$used_points = array();
			$order       = wc_get_order( $order_id );
			if ( 'yes' === get_option( 'rs_enable_disable_max_earning_points_for_user' ) && ! empty( get_option( 'rs_max_earning_points_for_user' ) ) ) {
				$used_points = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(earnedpoints) as earnedpoints FROM {$wpdb->prefix}rsrecordpoints WHERE userid = %d and orderid = %d and expirydate NOT IN(0) and checkpoints IN('MREPFU','PPRP')", $orderuserid, $order_id ), ARRAY_A );
				$used_points = ! empty( $used_points[0]['earnedpoints'] ) ? $used_points[0]['earnedpoints'] : 0;
			} else {
				$used_points = $order->get_meta( 'points_for_current_order_based_on_cart_total' );
			}

			if ( empty( $used_points ) ) {
				return;
			}

			$table_args = array(
				'user_id'     => $orderuserid,
				'usedpoints'  => $used_points,
				'checkpoints' => 'RVPFPPRPBCT',
				'orderid'     => $order_id,
			);
			self::insert_earning_points( $table_args );
			self::record_the_points( $table_args );
		}

		/**
		 * Insert Revised Product Purchase Points.
		 *
		 * @param int $orderuserid User ID.
		 * @param int $order_id Order ID.
		 */
		public static function insert_revised_points_based_on_range( $orderuserid, $order_id ) {
			self::insert_buying_points( $orderuserid, $order_id );
			self::insert_first_purchase_points( $orderuserid, $order_id );

			global $wpdb;

			$used_points = array();
			$order       = wc_get_order( $order_id );
			if ( 'yes' === get_option( 'rs_enable_disable_max_earning_points_for_user' ) && ! empty( get_option( 'rs_max_earning_points_for_user' ) ) ) {
				$used_points = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(earnedpoints) as earnedpoints FROM {$wpdb->prefix}rsrecordpoints WHERE userid = %d and orderid = %d and expirydate NOT IN(0) and checkpoints IN('MREPFU','PPRP')", $orderuserid, $order_id ), ARRAY_A );
				$used_points = ! empty( $used_points[0]['earnedpoints'] ) ? $used_points[0]['earnedpoints'] : 0;
			} else {
				$used_points = $order->get_meta( 'rs_points_for_current_order_based_on_range' );
			}

			if ( empty( $used_points ) ) {
				return;
			}

			$table_args = array(
				'user_id'     => $orderuserid,
				'usedpoints'  => $used_points,
				'checkpoints' => 'RVPFPPRPBCT',
				'orderid'     => $order_id,
			);
			self::insert_earning_points( $table_args );
			self::record_the_points( $table_args );
		}

		/**
		 * Insert Buying Points.
		 *
		 * @param int $orderuserid User ID.
		 * @param int $order_id Order ID.
		 */
		public static function insert_buying_points( $orderuserid, $order_id ) {
			$order      = wc_get_order( $order_id );
			$buy_points = $order->get_meta( 'buy_points_for_current_order' );

			if ( ! srp_check_is_array( $buy_points ) ) {
				return;
			}

			if ( 'yes' !== $order->get_meta( 'srp_bp_reward_points_awarded' ) ) {
				return;
			}

			foreach ( $buy_points as $key => $value ) {
				$table_args = array(
					'user_id'     => $orderuserid,
					'usedpoints'  => RSMemberFunction::earn_points_percentage( $orderuserid, (float) $value ),
					'productid'   => $key,
					'variationid' => $key,
					'checkpoints' => 'RVPFBPRP',
					'orderid'     => $order_id,
				);
				self::insert_earning_points( $table_args );
				self::record_the_points( $table_args );
			}
		}

		/**
		 * Insert First Purchase Points.
		 *
		 * @param int $orderuserid User ID.
		 * @param int $order_id Order ID.
		 */
		public static function insert_first_purchase_points( $orderuserid, $order_id ) {
			$order          = wc_get_order( $order_id );
			$first_purchase = $order->get_meta( 'rs_first_purchase_points' );
			if ( ! empty( $first_purchase ) ) {
				$table_args = array(
					'user_id'     => $orderuserid,
					'usedpoints'  => $first_purchase,
					'checkpoints' => 'RPFFP',
					'orderid'     => $order_id,
				);
				self::insert_earning_points( $table_args );
				self::record_the_points( $table_args );
			}
		}

		/**
		 * Insert Revised Referral Points.
		 *
		 * @param float   $pointsredeemed Redeemed Points.
		 * @param int     $referreduser Referred User.
		 * @param int     $orderuserid User ID.
		 * @param int     $order_id Order ID.
		 * @param Object  $new_obj Points Data.
		 * @param Wp_Post $order Order object.
		 */
		public static function insert_revised_referral_points( $pointsredeemed, $referreduser, $orderuserid, $order_id, $new_obj, $order ) {
			$refuser = get_user_by( 'login', $referreduser );
			$myid    = $refuser ? $refuser->ID : $referreduser;
			foreach ( $order->get_items() as $item ) {
				$productid   = $item['product_id'];
				$variationid = empty( $item['variation_id'] ) ? 0 : $item['variation_id'];
				$args        = array(
					'productid'     => $item['product_id'],
					'variationid'   => empty( $item['variation_id'] ) ? 0 : $item['variation_id'],
					'item'          => $item,
					'referred_user' => $myid,
					'order'         => $order,
				);
				if ( 'yes' === get_option( 'rs_referral_points_after_discounts' ) ) {
					$item_product_id        = 'variable' == wc_get_product( $item['product_id'] )->get_type() ? $item['variation_id'] : $item['product_id'];
					$points_after_discounts = $order->get_meta( 'rs_referrer_points_after_discounts' );
					$pointstoinsert         = isset( $points_after_discounts[ $item_product_id ] ) ? $points_after_discounts[ $item_product_id ] : 0;
				} else {
					$pointstoinsert = check_level_of_enable_reward_point( $args );
				}

				if ( $pointstoinsert ) {
					$valuestoinsert = array(
						'pointsredeemed'    => $pointstoinsert,
						'event_slug'        => 'RVPFPPRRP',
						'user_id'           => $myid,
						'referred_id'       => $orderuserid,
						'product_id'        => $productid,
						'variation_id'      => $variationid,
						'totalredeempoints' => $pointstoinsert,
					);
					$new_obj->total_points_management( $valuestoinsert );
				}
			}
		}

		/**
		 * Updates earning points for user in db.
		 *
		 * @param int/WP_Post $order_obj Order ID/Object.
		 */
		public static function update_earning_points_for_user( $order_obj ) {
			$order_id = is_object( $order_obj ) ? $order_obj->get_id() : $order_obj;
			$order    = wc_get_order( $order_id );
			if ( 'no' === get_option( 'rs_restrict_days_for_product_purchase' ) ) {
				award_points_for_product_purchase_based_on_cron( $order_id );
			} else {
				$order->update_meta_data( 'rs_order_status_reached', 'yes' );
				$interval = get_option( 'rs_restrict_product_purchase_time' );
				if ( 'minutes' === get_option( 'rs_restrict_product_purchase_cron_type' ) ) {
					$interval = $interval * 60;
				} elseif ( 'hours' === get_option( 'rs_restrict_product_purchase_cron_type' ) ) {
					$interval = $interval * 3600;
				} elseif ( 'days' === get_option( 'rs_restrict_product_purchase_cron_type' ) ) {
					$interval = $interval * 86400;
				}
				$timestamp = time() + (int) $interval;
				$date      = gmdate( 'Y-m-d h:i:sa', $timestamp );
				$order->update_meta_data( 'rs_date_time_for_awarding_points', $date );
				if ( false === wp_next_scheduled( 'rs_restrict_product_purchase_for_time', array( $order_id ) ) ) {
					wp_schedule_single_event( $timestamp, 'rs_restrict_product_purchase_for_time', array( $order_id ) );
				}
				$order->save();
			}
		}

		public static function check_redeeming_in_order( $order_id, $orderuserid ) {
			$new_obj = new RewardPointsOrder( $order_id, 'no' );
			$new_obj->check_redeeming_in_order();
		}

		public static function delete_referral_points_if_user_deleted( $user_id ) {
			if ( 2 == get_option( '_rs_reward_referal_point_user_deleted' ) ) {
				return;
			}

			$user_info            = new WP_User( $user_id );
			$ModifiedRegDate      = gmdate( 'Y-m-d h:i:sa', strtotime( $user_info->user_registered ) );
			$DelayedDate          = gmdate( 'Y-m-d h:i:sa', strtotime( $ModifiedRegDate . ' + ' . get_option( '_rs_days_for_redeeming_points' ) . ' days ' ) );
			$ModifiedCheckingDate = strtotime( $DelayedDate );
			$ModifiedCurrentDate  = strtotime( gmdate( 'Y-m-d h:i:sa' ) );
			$condition            = ( '1' == get_option( '_rs_time_validity_to_redeem' ) ) ? true : ( $ModifiedCurrentDate < $ModifiedCheckingDate );
			if ( ! $condition ) {
				return;
			}

			global $wpdb;
			$refuserid = get_user_meta( $user_id, '_rs_i_referred_by', true );
			if ( ! empty( $refuserid ) ) {
				$RefRegPoints = $wpdb->get_results( $wpdb->prepare( "SELECT (earnedpoints) FROM {$wpdb->prefix}rsrecordpoints WHERE userid = %d AND checkpoints = %s AND refuserid = %d", $refuserid, 'RRRP', $user_id ), ARRAY_A );
				if ( srp_check_is_array( $RefRegPoints ) ) {
					$Count = (int) get_user_meta( $refuserid, 'rsreferreduserregisteredcount', true ) - 1;
					update_user_meta( $refuserid, 'rsreferreduserregisteredcount', $Count );
					$table_args = array(
						'user_id'     => $refuserid,
						'usedpoints'  => isset( $RefRegPoints[0]['earnedpoints'] ) ? $RefRegPoints[0]['earnedpoints'] : 0,
						'checkpoints' => 'RVPFRRRP',
						'refuserid'   => $user_id,
					);
					self::insert_earning_points( $table_args );
					self::record_the_points( $table_args );
					update_user_meta( $user_id, '_rs_i_referred_by', $refuserid );
				}
			}
			$getlistoforder = get_user_meta( $user_id, '_update_user_order', true );
			if ( ! srp_check_is_array( $getlistoforder ) ) {
				return;
			}

			foreach ( $getlistoforder as $order_id ) {
				$order = wc_get_order( $order_id );
				if ( 'completed' != $order->status ) {
					continue;
				}

				$OrderObj = srp_order_obj( $order );
				$user_id  = $OrderObj['order_userid'];

				foreach ( $order->get_items() as $item ) {
					if ( '1' == get_option( 'rs_set_price_to_calculate_rewardpoints_by_percentage' ) ) {
						$getregularprice = get_post_meta( $item['product_id'], '_regular_price', true );
						$getregularprice = empty( $getregularprice ) ? get_post_meta( $item['product_id'], '_price', true ) : $getregularprice;
					} else {
						$getregularprice = get_post_meta( $item['product_id'], '_price', true );
						$getregularprice = empty( $getregularprice ) ? get_post_meta( $item['product_id'], '_regular_price', true ) : $getregularprice;
					}
										/**
										 * Hook:rs_delete_points_for_referral_simple.
										 *
										 * @since 1.0
										 */
					do_action_ref_array( 'rs_delete_points_for_referral_simple', array( $getregularprice, $item ) );
					$referreduser = $order->get_meta( '_referrer_name' );
					if ( ! empty( $referreduser ) ) {
						$new_obj = new RewardPointsOrder( $order_id, 'no' );
						self::insert_revised_referral_points( 0, $referreduser, $user_id, $order_id, $new_obj, $order );
					}
					self::update_revised_reward_points_to_user( $order_id, $user_id );
				}
			}
		}

		/**
		 * Delete Birthday Date if user deleted.
		 */
		public static function delete_birthday_data_if_user_deleted( $user_id ) {
			$user_data  = get_userdata( $user_id );
			$user_email = $user_data->user_email;

			$args = array(
				'meta_query' => array(
					array(
						'key'     => 'srp_user_email',
						'value'   => $user_email,
						'compare' => '==',
					),
				),
			);

			$birthday = srp_get_birthday_ids( $args );

			if ( srp_check_is_array( $birthday ) ) {
				$birthday_id = reset( $birthday );
				srp_delete_birthday( $birthday_id );
			}
		}

		public static function check_if_customer_purchased( $user_id, $emails, $product_id, $variation_id ) {
			global $wpdb;
			$db      = &$wpdb;
			$results = $db->get_results(
				$db->prepare(
					"
			SELECT DISTINCT order_items.order_item_id
			FROM {$db->prefix}woocommerce_order_items as order_items
			LEFT JOIN {$db->prefix}woocommerce_order_itemmeta AS itemmeta ON order_items.order_item_id = itemmeta.order_item_id
                        LEFT JOIN {$db->postmeta} AS postmeta ON order_items.order_id = postmeta.post_id
			LEFT JOIN {$db->posts} AS posts ON order_items.order_id = posts.ID
			WHERE
				posts.post_status IN ( 'wc-completed', 'wc-processing' ) AND
				itemmeta.meta_value  = %s AND
				itemmeta.meta_key    IN ( '_variation_id', '_product_id' ) AND
				postmeta.meta_key    IN ( '_billing_email', '_customer_user' ) AND
				(
					postmeta.meta_value  IN ( '" . implode( "','", array_map( 'esc_sql', array_unique( (array) $emails ) ) ) . "' ) OR
					(
						postmeta.meta_value = %s
					)
				)
			",
					empty( $variation_id ) ? $product_id : $variation_id,
					$user_id
				)
			);

			if ( ! srp_check_is_array( $results ) ) {
				return 0;
			}

			foreach ( $results as $each_results ) {
				$array_results[] = $each_results->order_item_id;
			}
			$new = $db->get_results( $db->prepare( "SELECT SUM(meta_value) as totalqty FROM {$db->prefix}woocommerce_order_itemmeta WHERE order_item_id IN(%s) and meta_key='_qty'", implode( ',', $array_results ) ) );
			return $new[0]->totalqty;
		}

		public static function msg_for_log( $csvmasterlog, $user_deleted, $order_status_changed, $earnpoints, $checkpoints, $productid, $orderid, $variationid, $userid, $refuserid, $reasonindetail, $redeempoints, $masterlog, $nomineeid, $usernickname, $nominatedpoints, $values = array() ) {
			$myaccountlink = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
			$vieworderlink = esc_url_raw( add_query_arg( 'view-order', $orderid, $myaccountlink ) );

			$order = wc_get_order( $orderid );

			if ( is_admin() && is_object( $order ) ) {
				$vieworderlink = esc_url( get_edit_post_link( $orderid ) );
			}
			/**
			 * Hook:rs_display_reward_log_order_id.
			 *
			 * @since 1.0
			 */
			$display_order_id       = apply_filters( 'rs_display_reward_log_order_id', $orderid );
			$vieworderlinkforfront  = '<a href="' . $vieworderlink . '">#' . $display_order_id . '</a>';
			$view_product           = '<a target="_blank" href="' . get_permalink( $productid ) . '">' . get_the_title( $productid ) . '</a>';
			$vieworderlink1         = esc_url_raw( add_query_arg( 'view-subscription', $orderid, $myaccountlink ) );
			$vieworderlinkforfront1 = '<a href="' . $vieworderlink1 . '">#' . $display_order_id . '</a>';
			$payment_method_title   = ( is_object( $order ) ) ? $order->get_meta( 'payment_method' ) : '';
			$gateway_title          = ! empty( $payment_method_title ) ? get_payment_gateway_title( $payment_method_title ) : '';
			switch ( $checkpoints ) {
				case 'RPFAC':
					return get_option( 'rs_reward_log_for_affiliate' );
				case 'RPFWLS':
					$message = str_replace( '{rs_waitlist_product_name}', $view_product, get_option( '_rs_localize_reward_points_for_waitlist_subscribing' ) );
					return $message;
				case 'RPFWLSC':
					$message = str_replace( '{rs_waitlist_product_name}', $view_product, get_option( '_rs_localize_reward_points_for_waitlist_sale_conversion' ) );
					return $message;
				case 'RPG':
					$message = str_replace( '{payment_title}', $gateway_title, get_option( '_rs_localize_reward_for_payment_gateway_message' ) );
					return $message;
				case 'PPRPBCT':
					if ( 'Replaced' == $reasonindetail ) {
						$message = get_option( 'rs_log_for_product_purchase_when_overidded' );
					} elseif ( false == $masterlog ) {
							$message = get_option( '_rs_localize_points_earned_for_purchase_based_on_cart_total_for_master_log' );
					} else {
						$message = get_option( '_rs_localize_points_earned_for_purchase_based_on_cart_total' );
					}
					$OrderLink = ( false == $csvmasterlog ) ? $vieworderlinkforfront : '#' . $display_order_id;
					$message   = str_replace( '{currentorderid}', $OrderLink, $message );
					return $message;
				case 'MLBP': /* Member Level Bonus Points */
					$rules       = get_option( 'rewards_dynamic_rule' );
					$points_data = new RS_Points_Data( $userid );
					$TotalPoints = '1' == get_option( 'rs_select_earn_points_based_on' ) ? $points_data->total_earned_points() : $points_data->total_available_points();
					$rule_id     = rs_get_earning_and_redeeming_level_id( $TotalPoints, 'earning' );
					$levelname   = isset( $rules[ $rule_id ]['name'] ) ? $rules[ $rule_id ]['name'] : '';
					return str_replace( '{level_name}', $levelname, get_option( 'rs_log_for_bonus_points', 'Bonus Points earned for reaching the <b>“{level_name}”</b> level' ) );
				case 'PFFP':
					return str_replace( '{currentorderid}', $vieworderlinkforfront, get_option( 'rs_log_for_first_purchase_points' ) );
				case 'RPFFP':
					return str_replace( '{currentorderid}', $vieworderlinkforfront, get_option( 'rs_log_for_revised_first_purchase_points', 'Revised Points for First Purchase {currentorderid}' ) );
				case 'PPRP':
					if ( 'Replaced' == $reasonindetail ) {
						$message = get_option( 'rs_log_for_product_purchase_when_overidded' );
					} elseif ( false == $masterlog ) {
							$message = get_option( '_rs_localize_points_earned_for_purchase_main' );
					} else {
						$message = get_option( '_rs_localize_product_purchase_reward_points' );
					}
					$OrderLink = ( false == $csvmasterlog ) ? $vieworderlinkforfront : '#' . $display_order_id;
					$message   = str_replace( '{currentorderid}', $OrderLink, $message );
					$message   = str_replace( '{itemproductid}', $productid, $message );
					$message   = str_replace( '{productname}', $view_product, $message );
					return $message;
				case 'PPRRPG':
					$message = str_replace( '{itemproductid}', get_the_title( $productid ), get_option( '_rs_localize_referral_reward_points_for_purchase_gettin_referred' ) );
					$message = str_replace( '{productname}', $view_product, $message );
					return $message;
				case 'RRPGR':
					return get_option( '_rs_localize_referral_reward_points_gettin_referred' );
				case 'PPRRP':
					$message = str_replace( '{itemproductid}', $productid, get_option( '_rs_localize_referral_reward_points_for_purchase' ) );
					$message = str_replace( '{productname}', $view_product, $message );
					$message = str_replace( '{purchasedusername}', '' != $refuserid ? $refuserid : __( 'Guest', 'rewardsystem' ), $message );
					return $message;
				case 'PPRRPCT':
					$message = str_replace( '{orderid}', $vieworderlinkforfront, get_option( '_rs_localize_referrer_reward_points_based_on_cart_total', 'Referrer Reward Points earned for this order {orderid} by {purchasedusername}' ) );
					$message = str_replace( '{purchasedusername}', '' != $refuserid ? $refuserid : __( 'Guest', 'rewardsystem' ), $message );
					return $message;
				case 'PPRRPGCT':
					$message = str_replace( '{orderid}', $vieworderlinkforfront, get_option( '_rs_localize_referred_reward_points_based_on_cart_total', 'Getting Referred Reward Points earned for this order {orderid}' ) );
					return $message;
				case 'RRP':
					return get_option( '_rs_localize_points_earned_for_registration' );
				case 'SLRRP':
					$message = str_replace( '[network_name]', $reasonindetail, get_option( '_rs_localize_points_earned_for_social_registration' ) );
					return $message;
				case 'RRRP':
					$refuserid = '' != $refuserid ? $refuserid : '(User Deleted)';
					$message   = str_replace( '{registereduser}', $refuserid, get_option( '_rs_localize_points_earned_for_referral_registration' ) );
					return $message;
				case 'LRP':
					return get_option( '_rs_localize_reward_points_for_login' );
				case 'SLRP':
					$message = str_replace( '[network_name]', $reasonindetail, get_option( '_rs_localize_reward_points_for_social_login' ) );
					return $message;
				case 'SLLRP':
					$message = str_replace( '[network_name]', $reasonindetail, get_option( '_rs_localize_reward_points_for_social_linking' ) );
					return $message;
				case 'CRFRP':
					$message = str_replace( '[field_name]', $reasonindetail, get_option( '_rs_localize_reward_points_for_cus_reg_field' ) );
					return $message;
				case 'CRPFDP':
					$message = str_replace( '[field_name]', $reasonindetail, get_option( '_rs_localize_reward_points_for_datepicker_cus_reg_field' ) );
					return $message;
				case 'RPC':
					return get_option( '_rs_localize_coupon_reward_points_log' );
				case 'RPFBP':
					return get_option( '_rs_localize_reward_points_for_create_post' );
				case 'RPFBPG':
					return get_option( '_rs_localize_reward_points_for_create_group' );
				case 'RPFBPC':
					return get_option( '_rs_localize_reward_points_for_post_comment' );
				case 'RPFL':
				case 'RPFLP':
					return get_option( '_rs_localize_reward_for_facebook_like' );
				case 'RPFS':
				case 'RPFSP':
					return get_option( '_rs_localize_reward_for_facebook_share' );
				case 'RPTT':
				case 'RPTTP':
					return get_option( '_rs_localize_reward_for_twitter_tweet' );
				case 'RPIF':
				case 'RPIFP':
					return get_option( '_rs_localize_reward_for_instagram' );
				case 'RPTF':
				case 'RPTFP':
					return get_option( '_rs_localize_reward_for_twitter_follow' );
				case 'RPOK':
				case 'RPOKP':
					return get_option( '_rs_localize_reward_for_ok_follow' );
				case 'RPGPOS':
				case 'RPGPOSP':
					return get_option( '_rs_localize_reward_for_google_plus' );
				case 'RPVL':
				case 'RPVLP':
					return get_option( '_rs_localize_reward_for_vk' );
				case 'BRP':
					return get_option( '_rs_localize_log_for_bday' );
				case 'RPPR':
					$message = str_replace( '{reviewproductid}', $productid, get_option( '_rs_localize_points_earned_for_product_review' ) );
					$message = str_replace( '{productname}', $view_product, $message );
					return $message;
				case 'RP':
					$OrderLink = ( false == $csvmasterlog ) ? $vieworderlinkforfront : '#' . $display_order_id;
					$message   = str_replace( '{currentorderid}', $OrderLink, get_option( '_rs_localize_points_redeemed_towards_purchase' ) );
					$message   = str_replace( '{productname}', $view_product, $message );
					return $message;
				case 'MAP':
				case 'MRP':
				case 'MAURP':
				case 'MRURP':
					return $reasonindetail;
				case 'CBRP':
					return get_option( '_rs_localize_points_to_cash_log' );
				case 'RCBRP':
					return get_option( '_rs_localize_points_to_cash_log_revised' );
				case 'RPGV':
					$message = str_replace( '{rsusedvouchercode}', $reasonindetail, get_option( '_rs_localize_voucher_code_usage_log_message' ) );
					return $message;
				case 'RPBSRP':
					$OrderLink = ( false == $csvmasterlog ) ? $vieworderlinkforfront : '#' . $display_order_id;
					$message   = str_replace( '{currentorderid}', $OrderLink, get_option( '_rs_localize_buying_reward_points_log' ) );
					$message   = str_replace( '{productname}', $view_product, $message );
					return $message;
				case 'RPCPR':
					$message = str_replace( '{postid}', get_the_title( $productid ), get_option( '_rs_localize_points_earned_for_post_review' ) );
					return $message;
				case 'RPFPOC':
					$message = str_replace( '{postid}', get_the_title( $productid ), get_option( '_rs_localize_points_earned_for_post_review' ) );
					return $message;
				case 'RPCPRO':
					$message = str_replace( '{ProductName}', get_the_title( $productid ), get_option( '_rs_localize_points_earned_for_product_creation' ) );
					return $message;
				case 'MREPFU':
					$message = str_replace( '[rsmaxpoints]', get_option( 'rs_max_earning_points_for_user' ), get_option( '_rs_localize_max_earning_points_log' ) );
					return $message;
				case 'RPFGW':
					$OrderLink = ( false == $csvmasterlog ) ? $vieworderlinkforfront : '#' . $display_order_id;
					$message   = str_replace( '{currentorderid}', $OrderLink, get_option( '_rs_reward_points_gateway_log_localizaation' ) );
					return $message;
				case 'RPFGWS':
					$message = str_replace( '{subscription_id}', $vieworderlinkforfront1, get_option( '_rs_localize_reward_for_using_subscription' ) );
					return $message;
				case 'RVPFRPG':
					$message = str_replace( '{payment_title}', $gateway_title, get_option( '_rs_localize_revise_reward_for_payment_gateway_message' ) );
					return $message;
				case 'RVPFPPRP':
					$message   = ( false == $masterlog ) ? get_option( '_rs_log_revise_product_purchase_main' ) : get_option( '_rs_log_revise_product_purchase' );
					$OrderLink = ( false == $csvmasterlog ) ? $vieworderlinkforfront : '#' . $display_order_id;
					$message   = str_replace( '{currentorderid}', $OrderLink, $message );
					$message   = str_replace( '{productid}', $productid, $message );
					$message   = str_replace( '{productname}', $view_product, $message );
					return $message;
				case 'RVPFBPRP':
					$OrderLink = ( false == $csvmasterlog ) ? $vieworderlinkforfront : '#' . $display_order_id;
					$message   = str_replace( array( '{currentorderid}', '{productname}' ), array( $OrderLink, $view_product ), get_option( '_rs_log_revise_buy_points_main' ) );
					return $message;
				case 'RVPFPPRPBCT':
					if ( false == $masterlog ) {
						$message = get_option( '_rs_log_revise_for_product_purchase_based_on_cart_total_in_my_reward' );
					} else {
						$message = get_option( '_rs_log_revise_for_product_purchase_based_on_cart_total' );
					}
					$OrderLink = ( false == $csvmasterlog ) ? $vieworderlinkforfront : '#' . $display_order_id;
					$message   = str_replace( '{orderid}', $OrderLink, $message );
					return $message;
				case 'RVPFPPRRP':
					if ( true == $order_status_changed ) {
						$message = str_replace( '{productid}', $productid, get_option( '_rs_log_revise_referral_product_purchase' ) );
					} elseif ( true == $user_deleted ) {
						$message = str_replace( '{productid}', $productid, get_option( '_rs_localize_revise_points_for_referral_purchase' ) );
					}
					$message = str_replace( '{productname}', $view_product, $message );
					$message = str_replace( '{usernickname}', $refuserid, $message );
					return $message;
				case 'RVPFPPRRPCT':
					if ( true == $order_status_changed ) {
						$message = str_replace( '{orderid}', $display_order_id, get_option( 'rs_log_revise_referrer_points_based_cart_total', 'Revised Referrer Product Purchase Points for this order {orderid}' ) );
					} elseif ( true == $user_deleted ) {
						$message = str_replace( '{orderid}', $display_order_id, get_option( 'rs_localize_revise_referrer_points_for_deletion_based_cart_total', 'Revised Referrer Reward Points earned for Purchase {orderid} by deleted user {usernickname}' ) );
					}
					$message = str_replace( '{usernickname}', $refuserid, $message );
					return $message;
				case 'RVPPRRPGCT':
					if ( true == $order_status_changed ) {
						$message = str_replace( '{orderid}', $display_order_id, get_option( 'rs_log_revise_referred_points_based_cart_total', 'Revised Getting Referred Product Purchase Points for this order {orderid}' ) );
					} elseif ( true == $user_deleted ) {
						$message = str_replace( '{orderid}', $display_order_id, get_option( 'rs_localize_revise_referred_points_for_deletion_based_cart_total', 'Revised Getting Referred Reward Points earned for Purchase {orderid} by deleted user {usernickname}' ) );
					}
					$message = str_replace( '{usernickname}', $refuserid, $message );
					return $message;
				case 'RVPPRRPG':
					if ( true == $order_status_changed ) {
						$message = str_replace( '{productid}', $productid, get_option( '_rs_log_revise_getting_referred_product_purchase' ) );
					} elseif ( true == $user_deleted ) {
						$message = str_replace( '{productid}', $productid, get_option( '_rs_localize_revise_points_for_getting_referred_purchase' ) );
					}
					$message = str_replace( '{productname}', $view_product, $message );
					$message = str_replace( '{usernickname}', $refuserid, $message );
					return $message;
				case 'RVPFRP':
					$message   = get_option( '_rs_log_revise_points_redeemed_towards_purchase' );
					$OrderLink = ( false == $masterlog ) ? $vieworderlinkforfront : '#' . $display_order_id;
					$message   = str_replace( '{currentorderid}', $OrderLink, $message );
					$message   = str_replace( '{productname}', $view_product, $message );
					return $message;
				case 'RVPFRRRP':
					$message = str_replace( '{usernickname}', $refuserid, get_option( '_rs_localize_referral_account_signup_points_revised' ) );
					return $message;
				case 'RVPFRPVL':
					return get_option( '_rs_localize_reward_for_vk_like_revised' );
				case 'RVPFRPGPOS':
					return get_option( '_rs_localize_reward_for_google_plus_revised' );
				case 'RVPFRPFL':
					return get_option( '_rs_localize_reward_for_facebook_like_revised' );
				case 'PPRPFN':
					$Name    = ( true === $masterlog ) ? $usernickname : 'Your';
					$message = str_replace( array( '[points]', '[user]', '[name]' ), array( $earnpoints, $nomineeid, $Name ), get_option( '_rs_localize_log_for_nominee' ) );
					return $message;
				case 'PPRPFNP':
					$Name    = ( true == $masterlog ) ? $usernickname : 'Your';
					$message = str_replace( array( '[points]', '[user]', '[name]' ), array( $nominatedpoints, $nomineeid, $Name ), get_option( '_rs_localize_log_for_nominated_user' ) );
					return $message;
				case 'IMPADD':
					$message = str_replace( '[points]', $earnpoints, get_option( '_rs_localize_log_for_import_add' ) );
					return $message;
				case 'IMPOVR':
					$Name    = ( true == $masterlog ) ? $earnpoints : 'Your';
					$message = str_replace( '[points]', $Name, get_option( '_rs_localize_log_for_import_override' ) );
					return $message;
				case 'RPFP':
					$replacepostid = str_replace( '{postid}', get_the_title( $productid ), get_option( '_rs_localize_points_earned_for_post' ) );
					return $replacepostid;
				case 'SP':
					$Name    = ( true == $masterlog ) ? $usernickname : 'You';
					$message = str_replace( array( '[points]', '[user]', '[name]' ), array( $earnpoints, $nomineeid, $Name ), get_option( '_rs_localize_log_for_reciver' ) );
					return $message;
				case 'RPCPAR':
				case 'RPFPAC':
					$message = str_replace( '{pagename}', get_the_title( $productid ), get_option( '_rs_localize_points_earned_for_page_review' ) );
					return $message;
				case 'SENPM':
					$message = str_replace( '[user]', $nomineeid, get_option( '_rs_localize_log_for_sender' ) );
					$message = str_replace( '[points]', $redeempoints, $message );
					$Name    = ( true == $masterlog ) ? $usernickname : 'Your';
					$message = str_replace( '[name]', $Name, $message );
					return $message;
				case 'SPB':
					return ( false == $masterlog ) ? get_option( '_rs_localize_log_for_sender_after_submit' ) : '';
				case 'SPA':
						$message = str_replace( '[user]', $nomineeid, get_option( '_rs_localize_log_for_sender' ) );
						$message = str_replace( '[points]', $redeempoints, $message );
						$message = str_replace( '[name]', 'Your', $message );
					return ( false == $masterlog ) ? $message : '';
				case 'SEP':
					return get_option( '_rs_localize_points_to_send_log_revised' );
				case 'RPFURL':
					$replacepoints = str_replace( '[points]', $earnpoints, get_option( 'rs_message_for_pointurl', '[points] Points added, from Visited Point URL' ) );
					return $replacepoints;
				case 'OBP':
					$msg = get_option( '_rs_bonus_reward_points_orders_log', 'Bonus Points Earned for placing successful orders on the site' );
					return $msg;
				case 'AAP':
					$msg = str_replace( '{point_value}', $earnpoints, '{point_value} Account Anniversary Point(s) will be earned for the user' );
					return get_option( 'rs_account_anniversary_field_reward_log', 'Points earned for Account Anniversary' );
				case 'CSAP':
					$stored_field_names = get_user_meta( $userid, 'rs_stored_single_anniversary_field_names', true );
					$earned_time        = isset( $values['earneddate'] ) ? $values['earneddate'] : '';
					$anniversary_name   = isset( $stored_field_names[ $earned_time ] ) ? $stored_field_names[ $earned_time ] : __( 'Anniversary', 'rewardsystem' );
					$msg                = str_replace( array( '{anniversary_name}' ), array( $anniversary_name ), get_option( 'rs_custom_anniversary_field_reward_log', 'Points earned for {anniversary_name}' ) );
					return $msg;
				case 'CMAP':
					$rules              = get_option( 'rs_custom_anniversary_rules' );
					$stored_field_names = get_user_meta( $userid, 'rs_stored_multiple_anniversary_field_names', true );
					if ( srp_check_is_array( $rules ) ) {
						foreach ( $rules as $rule_key => $rule_value ) {
							$reward_log = isset( $rule_value['reward_log'] ) ? $rule_value['reward_log'] : '';
							if ( ! $reward_log ) {
								continue;
							}

							$anniversary_point = get_user_meta( $userid, 'rs_stored_multiple_anniversary_point_' . $rule_key, true );
							if ( $anniversary_point ) {
								$earned_time      = isset( $values['earneddate'] ) ? $values['earneddate'] : '';
								$anniversary_name = isset( $stored_field_names[ $earned_time ] ) ? $stored_field_names[ $earned_time ] : __( 'Anniversary', 'rewardsystem' );
								$msg              = str_replace( array( '{anniversary_name}' ), array( $anniversary_name ), $reward_log );
								return $msg;
							}
						}
					}
					return '';
				case 'RRRPB':
					$no_of_users = get_option( 'rs_no_of_users_referral_to_get_reward_signup_bonus' );
					$message     = str_replace( '{noofreferral}', $no_of_users, get_option( '_rs_localize_points_earned_for_no_of_referral_bonus' ) );
					return $message;
			}
		}

		public static function checkout_cookies_referral_meta( $order_id, $order_posted ) {
			$user_id = get_current_user_id();
			if ( isset( $_COOKIE['rsreferredusername'] ) ) {
				$cookie_name = wc_clean( wp_unslash( $_COOKIE['rsreferredusername'] ) );
				$refuser     = ( 1 == get_option( 'rs_generate_referral_link_based_on_user' ) ) ? get_user_by( 'login', $cookie_name ) : get_user_by( 'id', $cookie_name );
				if ( ! $refuser || ! is_object( $refuser ) ) {
					return;
				}

				$myid = $refuser->ID;
			} else {
				$myid    = check_if_referrer_has_manual_link( $user_id );
				$refuser = $myid ? get_user_by( 'ID', $myid ) : '';
				if ( ! is_object( $refuser ) ) {
					return;
				}
			}

			if ( ! $myid ) {
				return;
			}

			if ( $user_id == $myid ) {
				return;
			}

			if ( isset( $refuser->ID ) ) {
				$billing_email        = isset( $_REQUEST['billing_email'] ) ? sanitize_email( $_REQUEST['billing_email'] ) : '';
				$OrderCount           = self::get_order_count( sanitize_email( $billing_email ), $user_id, array_keys( wc_get_order_statuses() ), $refuser->ID );
				$CheckOrderCountLimit = self::check_order_count_limit( $OrderCount, 'yes' );
				if ( $CheckOrderCountLimit ) {
					return;
				}

				// Validate old user not in referral system.
				if ( ! self::validate_old_user_not_in_referral_system( $order_id ) ) {
					return;
				}

				if ( ! rs_restrict_referral_system_purchase_point_for_free_shipping( $order_id ) ) {
					return;
				}
			}

			// Save cart total meta in order.
			self::save_cart_total_referral_system_meta_in_order( $order_id );

			$order = wc_get_order( $order_id );
			$order->update_meta_data( '_referrer_name', $myid );
			$order->update_meta_data( '_referrer_email', $refuser->user_email );
			$referral_data = array(
				'referred_user_name'                => $myid,
				'award_referral_points_for_renewal' => get_option( 'rs_award_referral_point_for_renewal_order' ),
			);

			$order->update_meta_data( 'rs_referral_data_for_renewal_order', $referral_data );
			$getmetafromuser = get_user_meta( $user_id, '_update_user_order', true );
			$getorderlist[]  = $order_id;
			$mainmerge       = srp_check_is_array( $getmetafromuser ) ? array_merge( $getmetafromuser, $getorderlist ) : $getorderlist;
			update_user_meta( $user_id, '_update_user_order', $mainmerge );

			// Update Postmeta for Referrer points after Discounts.
			if ( 'yes' === get_option( 'rs_referral_points_after_discounts' ) ) {
				$points_after_discounts = RSFunctionForReferralSystem::referrel_points_for_product_in_cart( $myid, false );
				if ( srp_check_is_array( $points_after_discounts ) ) {
					$order->update_meta_data( 'rs_referrer_points_after_discounts', $points_after_discounts );
				}
			}
			$order->save();
		}

		/**
		 * Save cart total referral system meta in order \
		 *
		 * @param int $order_id Order ID.
		 * */
		public static function save_cart_total_referral_system_meta_in_order( $order_id ) {
			if ( '1' == get_option( 'rs_award_points_for_cart_or_product_total_for_refferal_system', 1 ) ) {
				return;
			}

			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			$shipping_cost   = $order->get_shipping_total() + $order->get_shipping_tax();
			$shipping_cost   = ! empty( $shipping_cost ) ? $shipping_cost : 0;
			$referrer_points = rs_get_reward_points_based_on_cart_total_for_referrer( $order, $shipping_cost );
			if ( ! empty( $referrer_points ) ) {
				$order->update_meta_data( 'rs_referrer_points_based_on_cart_total', $referrer_points );
			}

			$referred_points = rs_get_reward_points_based_on_cart_total_for_referred( $order, $shipping_cost );
			if ( ! empty( $referred_points ) ) {
				$order->update_meta_data( 'rs_referred_points_based_on_cart_total', $referred_points );
			}
			$order->save();
		}

		public static function delete_cookie_for_user_and_guest() {
			if ( ! isset( $_COOKIE['rsreferredusername'] ) ) {
				return;
			}

			$cookie_name = wc_clean( wp_unslash( $_COOKIE['rsreferredusername'] ) );
			$referrer    = ( 1 == get_option( 'rs_generate_referral_link_based_on_user' ) ) ? get_user_by( 'login', $cookie_name ) : get_user_by( 'id', $cookie_name );

			if ( ! is_object( $referrer ) ) {
				return;
			}

			$billing_email        = isset( $_REQUEST['billing_email'] ) ? sanitize_email( $_REQUEST['billing_email'] ) : '';
			$OrderCount           = self::get_order_count( $billing_email, get_current_user_id(), array_keys( wc_get_order_statuses() ), $referrer->ID );
			$CheckOrderCountLimit = self::check_order_count_limit( $OrderCount, 'yes' );
			if ( $CheckOrderCountLimit ) {
				setcookie( 'rsreferredusername', $cookie_name, time() - 3600, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
				$referrer_ip = isset( $_COOKIE['referrerip'] ) ? wc_clean( wp_unslash( $_COOKIE['referrerip'] ) ) : '';
				setcookie( 'referrerip', $referrer_ip, time() - 3600, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
			}
		}

		public static function get_order_count( $billing_email, $userid, $poststatus, $referrer_id ) {
			$args = array(
				'post_type'      => 'shop_order',
				'post_status'    => $poststatus,
				'fields'         => 'ids',
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => '_billing_email',
						'value'   => $billing_email,
						'compare' => '=',
					),
					array(
						'key'     => '_customer_user',
						'value'   => $userid,
						'compare' => '=',
					),
					array(
						'key'     => '_referrer_name',
						'value'   => $referrer_id,
						'compare' => '=',
					),
				),
				'posts_per_page' => '-1',
				'cache_results'  => false,
			);

			$order_id = get_posts( $args );
			return count( $order_id );
		}

		public static function check_order_count_limit( $OrderCount, $OrderCountLimit ) {
			if ( 'yes' != get_option( 'rs_enable_delete_referral_cookie_after_first_purchase' ) ) {
				return false;
			}

			$NoofPurchase = get_option( 'rs_no_of_purchase' );
			if ( empty( $NoofPurchase ) ) {
				return false;
			}

			$CountLimit = ( 'yes' == $OrderCountLimit ) ? ( $OrderCount >= $NoofPurchase ) : ( $OrderCount > $NoofPurchase );
			if ( $CountLimit ) {
				return true;
			}

			return false;
		}

		public static function check_if_user_has_multiple_referrer( $BillingEmail, $order ) {

			if ( 'yes' != get_option( 'rs_restrict_referral_points_for_multiple_referrer' ) ) {
				return true;
			}

			if ( ! is_object( $order ) ) {
				return true;
			}

			$order_user_id = $order->get_user_id();

			$args = array(
				'post_type'      => 'shop_order',
				'post_status'    => array( 'wc-processing', 'wc-completed', 'wc-on-hold', 'wc-pending' ),
				'meta_query'     => array(
					'relation' => $order_user_id > 0 ? 'OR' : 'AND',
					array(
						'key'     => '_billing_email',
						'value'   => $BillingEmail,
						'compare' => '=',
					),
					array(
						'key'     => '_customer_user',
						'value'   => $order_user_id,
						'compare' => '=',
					),
				),
				'posts_per_page' => -1,
				'fields'         => 'ids',
			);

			$OrderIds = get_posts( $args );
			if ( empty( $OrderIds ) ) {
				return true;
			}

			if ( 1 == count( $OrderIds ) ) {
				return true;
			}

			foreach ( $OrderIds as $OrderId ) {
				$order_obj = wc_get_order( $OrderId );
				if ( $order_obj->get_meta( '_referrer_name' ) !== $order->get_meta( '_referrer_name' ) ) {
					return false;
				}
			}

			return true;
		}

		public static function validate_old_user_not_in_referral_system( $order_id ) {

			if ( 'yes' != get_option( 'rs_restrict_referral_points_old_user_not_in_referral_system' ) ) {
				return true;
			}

			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return true;
			}

			$order_user_id = $order->get_user_id();
			if ( ! $order_user_id ) {
				return true;
			}

			$order_ids = get_posts(
				array(
					'post_type'      => 'shop_order',
					'post_status'    => wc_get_order_statuses(),
					'meta_key'       => '_customer_user',
					'meta_value'     => $order_user_id,
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'order'          => 'ASC',
					'post__not_in'   => array( $order_id ),
				)
			);

			if ( ! srp_check_is_array( $order_ids ) ) {
				return true;
			}

			foreach ( $order_ids as $order_id ) {

				if ( ! $order_id ) {
					continue;
				}

				$order_obj     = wc_get_order( $order_id );
				$referrer_name = $order_obj->get_meta( '_referrer_name' );
				if ( $referrer_name ) {
					return true;
				}
			}

			return false;
		}

		public static function check_if_referrer_and_referral_from_same_ip( $order ) {
			if ( 'yes' != get_option( 'rs_restrict_referral_points_for_same_ip' ) ) {
				return true;
			}

			if ( ! isset( $_COOKIE['referrerip'] ) ) {
				return true;
			}

			if ( ! is_object( $order ) ) {
				return true;
			}

			$RefIPAddrs = base64_decode( wc_clean( wp_unslash( $_COOKIE['referrerip'] ) ) );
			$IPAddrs    = $order->get_customer_ip_address();
			if ( $RefIPAddrs == $IPAddrs ) {
				return false;
			}

			return true;
		}

		public static function award_reward_points_for_coupon( $OrderId ) {
			if ( 'yes' != get_option( 'rs_reward_action_activated' ) ) {
				return;
			}

			$Order          = new WC_Order( $OrderId );
			$CouponsInOrder = $Order->get_items( array( 'coupon' ) );
			if ( ! srp_check_is_array( $CouponsInOrder ) ) {
				return;
			}

			$OrderObj       = srp_order_obj( $Order );
			$user_id        = $OrderObj['order_userid'];
			$AppliedCoupons = array();
			$SortType       = ( '1' == get_option( 'rs_choose_priority_level_selection_coupon_points' ) ) ? 'desc' : 'asc';
			$Rules          = multi_dimensional_sort( get_option( 'rewards_dynamic_rule_couponpoints' ), 'reward_points', $SortType );
			$Codes          = array();
			$Datas          = array();
			if ( ! srp_check_is_array( $Rules ) ) {
				return;
			}

			foreach ( $Rules as $Key => $Rule ) {
				if ( ! isset( $Rule['coupon_codes'] ) ) {
					continue;
				}

				if ( ! srp_check_is_array( $Rule['coupon_codes'] ) ) {
					continue;
				}

				foreach ( $Rule['coupon_codes'] as $Code ) {
					if ( in_array( $Code, $Codes ) ) {
						continue;
					}

					$Codes[ $Key ] = $Code;
				}
			}

			if ( ! srp_check_is_array( $Codes ) ) {
				return;
			}

			foreach ( $Codes as $KeyToFind => $Value ) {
				$Datas[] = $Rules[ $KeyToFind ];
			}

			if ( ! srp_check_is_array( $Datas ) ) {
				return;
			}

			foreach ( $CouponsInOrder as $Code ) {
				$AppliedCoupons[] = $Code['name'];
			}

			foreach ( $Datas as $Data ) {
				$CouponCodes = $Data['coupon_codes'];
				$points      = $Data['reward_points'];
				foreach ( $CouponCodes as $CouponCode ) {
					if ( ! check_if_coupon_exist_in_cart( $CouponCode, $AppliedCoupons ) ) {
						continue;
					}

					$new_obj = new RewardPointsOrder( $OrderId, 'no' );
					if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
						$new_obj->check_point_restriction( $points, $pointsredeemed = 0, $event_slug     = 'RPC', $user_id, $nomineeid      = '', $referrer_id    = '', $product_id     = '', $variationid    = '', $reasonindetail = '' );
					} else {
						$valuestoinsert = array(
							'pointstoinsert'    => $points,
							'event_slug'        => 'RPC',
							'user_id'           => $user_id,
							'totalearnedpoints' => $points,
						);
						$new_obj->total_points_management( $valuestoinsert );
					}
				}
			}
						/**
						 * Hook:fp_reward_point_for_using_coupons.
						 *
						 * @since 1.0
						 */
			do_action( 'fp_reward_point_for_using_coupons' );
		}

		public static function replace_total_points_for_user( $user_id = 0, $points = 0, $date = '', $reason = '', $replace = true ) {
			global $wpdb;
			if ( ! empty( $user_id ) && $replace ) {
				$wpdb->delete( "{$wpdb->prefix}rspointexpiry", array( 'userid' => $user_id ) );
			}

			$table_args = array(
				'user_id'        => $user_id,
				'pointstoinsert' => $points,
				'checkpoints'    => 'MAP',
				'date'           => $date,
				'reason'         => $reason,
			);
			self::insert_earning_points( $table_args );
			self::record_the_points( $table_args );
		}

		/**
		 * Award Points for User.
		 */
		public static function award_bday_points() {
			$args = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => 'srp_birthday_month',
						'value'   => gmdate( 'm-d' ),
						'compare' => '==',
					),
					array(
						'relation' => 'OR',
						array(
							'key'     => 'srp_last_issued_year',
							'value'   => gmdate( 'Y' ),
							'compare' => '>=',
						),
						array(
							'key'     => 'srp_last_issued_year',
							'compare' => 'NOT EXISTS',
						),
					),
				),
			);

			$birthday_ids = srp_get_birthday_ids( $args );

			if ( ! srp_check_is_array( $birthday_ids ) ) {
				return;
			}

			foreach ( $birthday_ids as $birthday_id ) {

				if ( ! self::check_if_already_awarded( $birthday_id ) ) {
					continue;
				}

				$birthday_obj = srp_get_birthday( $birthday_id );

				if ( ! self::birthday_date( $birthday_obj ) ) {
					continue;
				}

				$user_id = $birthday_obj->get_user_id();

				self::insert_bday_points( $user_id, $birthday_obj );
			}
		}

				/**
				 * May be update birthday field in order.
				 */
		public static function update_birthday_field_in_order( $order_id ) {

			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			$user_id = $order->get_user_id();
			if ( ! $user_id ) {
				return;
			}

			self::update_birthday_date( $user_id );
		}

		/**
		 * Save Birthday Date in Account Details
		 */
		public static function update_birthday_date( $user_id ) {
			if ( isset( $_REQUEST['srp_birthday_date'] ) && ! empty( $_REQUEST['srp_birthday_date'] ) ) {
				$user_data     = get_userdata( $user_id );
				$user_name     = $user_data->display_name;
				$user_email    = $user_data->user_email;
				$birthday_date = wc_clean( wp_unslash( $_REQUEST['srp_birthday_date'] ) );
				update_user_meta( $user_id, 'srp_birthday_date', $birthday_date );

				self::create_birthday( $user_id, $user_name, $user_email, $birthday_date );

				if ( self::birthday_date( $birthday_date ) ) {
					self::insert_bday_points_if_update( $user_id, $birthday_date );
				}
			}
		}

		/**
		 * Insert Points for User
		 */
		public static function insert_bday_points( $userid, $birthday_obj ) {
			if ( ! $userid || 'yes' != get_option( 'rs_enable_bday_points' ) ) {
				return;
			}

			$bdaypoints = get_option( 'rs_bday_points' );
			if ( empty( $bdaypoints ) ) {
				return;
			}

			$ban_type = check_banning_type( $userid );
			if ( 'earningonly' == $ban_type || 'both' == $ban_type ) {
				return;
			}

			if ( ! allow_reward_points_for_user( $userid ) ) {
				return;
			}

			$new_obj = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$new_obj->check_point_restriction( $bdaypoints, $pointsredeemed = 0, 'BRP', $userid, $nomineeid      = '', $referrer_id    = '', $productid      = '', $variationid    = '', '' );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $bdaypoints,
					'event_slug'        => 'BRP',
					'user_id'           => $userid,
					'reasonindetail'    => '',
					'totalearnedpoints' => $bdaypoints,
				);
				$new_obj->total_points_management( $valuestoinsert );
			}

			$prev_data = $birthday_obj->get_issued_year();

			if ( srp_check_is_array( $prev_data ) ) {
				$merged_data = array_merge( $prev_data, array( gmdate( 'Y' ) ) );
			} else {
				$merged_data = array( gmdate( 'Y' ) );
			}

			$birthday_args = array(
				'srp_issued_year'      => $merged_data,
				'srp_issued_date'      => gmdate( 'Y-m-d' ),
				'srp_last_issued_year' => gmdate( 'Y' ),
			);

			srp_update_birthday( $birthday_obj->get_id(), $birthday_args );
						/**
						 * Hook:fp_reward_point_for_birthday.
						 *
						 * @since 1.0
						 */
			do_action( 'fp_reward_point_for_birthday' );
		}

		/**
		 * Insert Points for User
		 */
		public static function insert_bday_points_if_update( $userid, $birthday_date ) {

			$bdate_obj = SRP_Date_Time::get_date_time_object( $birthday_date );

			$args = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => 'srp_user_id',
						'value'   => $userid,
						'compare' => '==',
					),
					array(
						'key'     => 'srp_birthday_month',
						'value'   => $bdate_obj->format( 'm-d' ),
						'compare' => '==',
					),
					array(
						'relation' => 'OR',
						array(
							'key'     => 'srp_last_issued_year',
							'value'   => gmdate( 'Y' ),
							'compare' => '>=',
						),
						array(
							'key'     => 'srp_last_issued_year',
							'compare' => 'NOT EXISTS',
						),
					),
				),
			);

			$birthday_ids = srp_get_birthday_ids( $args );

			if ( ! srp_check_is_array( $birthday_ids ) ) {
				return;
			}

			foreach ( $birthday_ids as $birthday_id ) {

				if ( ! self::check_if_already_awarded( $birthday_id ) ) {
					continue;
				}

				$birthday_obj = srp_get_birthday( $birthday_id );

				if ( ! self::birthday_date( $birthday_obj ) ) {
					continue;
				}

				$user_id = $birthday_obj->get_user_id();

				self::insert_bday_points( $user_id, $birthday_obj );
			}
		}

		/**
		 * Create Birthday Post
		 */
		public static function create_birthday( $user_id, $user_name, $user_email, $birthday_date ) {
			$args = array(
				'meta_query' => array(
					array(
						'key'     => 'srp_user_email',
						'value'   => $user_email,
						'compare' => '==',
					),
				),
			);

			$birthday = srp_get_birthday_ids( $args );

			$bdate_obj = SRP_Date_Time::get_date_time_object( $birthday_date );

			if ( ! srp_check_is_array( $birthday ) ) {
				$birthday_args = array(
					'srp_user_id'             => $user_id,
					'srp_user_email'          => $user_email,
					'srp_user_name'           => $user_name,
					'srp_birthday_date'       => $birthday_date,
					'srp_birthday_updated_on' => gmdate( 'Y-m-d' ),
					'srp_birthday_month'      => $bdate_obj->format( 'm-d' ),
				);

				$birthday_id = srp_create_new_birthday( $birthday_args );
			} else {
				$birthday_id = reset( $birthday );

				$birthday_args = array(
					'srp_birthday_date'       => $birthday_date,
					'srp_birthday_updated_on' => gmdate( 'Y-m-d' ),
					'srp_birthday_month'      => $bdate_obj->format( 'm-d' ),
				);

				srp_update_birthday( $birthday_id, $birthday_args );
			}
		}

		/**
		 * Check User's Birthday Date.
		 */
		public static function birthday_date( $birthday_obj ) {

			$birthday_date = is_object( $birthday_obj ) ? $birthday_obj->get_birthday_date() : $birthday_obj;

			$bdate_obj = SRP_Date_Time::get_date_time_object( $birthday_date );

			$date = gmdate( 'm-d' );

			if ( $bdate_obj->format( 'm-d' ) == $date ) {
				return true;
			}

			return false;
		}

		/**
		 * Check If Already awarded.
		 */
		public static function check_if_already_awarded( $birthday_id ) {
			$current_year = gmdate( 'Y' );
			$awarded_year = (array) get_post_meta( $birthday_id, 'srp_issued_year', true );
			if ( in_array( $current_year, $awarded_year ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Award Blog Post Creation Points for User.
		 *
		 * @param int     $post_id Blog Post ID.
		 * @param WP_Post $post_object Post Object.
		 * @param bool    $update Bool Value.
		 * */
		public static function award_points_for_blog_post_creation( $post_id, $post_object, $update ) {
			if ( ! is_object( $post_object ) ) {
				return;
			}

			if ( 'shop_coupon' === $post_object->post_type ) {
				return;
			}

			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( 'yes' === get_post_meta( $post_id, 'rewardpointsforblogpost', true ) ) {
				return;
			}

			$user_id  = $post_object->post_author;
			$ban_type = check_banning_type( $user_id );
			if ( 'earningonly' === $ban_type || 'both' === $ban_type ) {
				return;
			}

			if ( 'yes' !== get_option( 'rs_reward_for_Creating_Post' ) ) {
				return;
			}

			$points = get_option( 'rs_reward_post' );
			if ( empty( $points ) ) {
				return;
			}

			if ( 'publish' === $post_object->post_status && '1' === get_option( 'rs_post_visible_for' ) ) {
				self::insert_blog_post_points( $post_id, $points, $user_id );
			} elseif ( 'private' === $post_object->post_status && '2' === get_option( 'rs_post_visible_for' ) ) {
				self::insert_blog_post_points( $post_id, $points, $user_id );
			}
		}

		/**
		 * Insert Blog Post Creation Points for User.
		 *
		 * @param int   $post_id Blog Post ID.
		 * @param float $points Points to Insert.
		 * @param int   $user_id User ID.
		 * */
		public static function insert_blog_post_points( $post_id, $points, $user_id ) {
			$table_args = array(
				'user_id'           => $user_id,
				'pointstoinsert'    => $points,
				'checkpoints'       => 'RPFP',
				'totalearnedpoints' => $points,
				'productid'         => $post_id,
			);
			self::insert_earning_points( $table_args );
			self::record_the_points( $table_args );
			update_post_meta( $post_id, 'rewardpointsforblogpost', 'yes' );
		}

		/**
		 * Display order total in Order Detail.
		 *
		 * @param float   $total Order Total.
		 * @param WP_Post $order Order Object.
		 * */
		public static function order_total_in_order_detail( $total, $order ) {
			if ( ! is_user_logged_in() ) {
				return $total;
			}

			if ( 'yes' !== get_option( 'rs_point_price_activated' ) ) {
				return $total;
			}

			if ( '2' === get_option( 'rs_enable_disable_point_priceing' ) ) {
				return $total;
			}

			$order_object = srp_order_obj( $order );
			$user_id      = $order_object['order_userid'];
			if ( 'reward_gateway' !== $order->get_payment_method() ) {
				return $total;
			}

			$fee_total      = $order->get_total();
			$tax_total      = $order->get_total_tax();
			$shipping_total = $order->get_shipping_total();
			$order_obj      = $order->get_items();

			if ( ! srp_check_is_array( $order_obj ) ) {
				return $total;
			}

			$point_price = srp_pp_get_point_price_values( $order_obj );

			if ( ! srp_check_is_array( $point_price ) ) {
				return $total;
			}

			$tax_total_points = redeem_point_conversion( $tax_total, get_current_user_id() );
			$shipping_points  = redeem_point_conversion( $shipping_total, get_current_user_id() );

			if ( 'yes' === $point_price['enable_point_price'] ) {
				$TotalPoints   = $point_price['points'] + $tax_total_points + $shipping_points;
				$product_price = display_point_price_value( $TotalPoints );
				$separator     = get_option( 'rs_separator_for_point_price' );
				$order_total   = str_replace( $separator, '', $product_price );
				return $order_total;
			}

			if ( 'yes' === $point_price['regular_product'] ) {
				$total_amount  = redeem_point_conversion( $fee_total, get_current_user_id() );
				$product_price = display_point_price_value( $total_amount );
				$separator     = get_option( 'rs_separator_for_point_price' );
				$order_total   = str_replace( $separator, '', $product_price );
				return $order_total;
			}

			return $total;
		}

		/**
		 * Trash SUMO Coupon when Order Placed.
		 *
		 * @param int     $order_id Order ID.
		 * @param WP_Post $order_post Order Object.
		 */
		public static function trash_sumo_coupon_if_order_placed( $order_id, $order_post ) {
			$order    = wc_get_order( $order_id );
			$OrderObj = srp_order_obj( $order );
			$UserId   = $OrderObj['order_userid'];
			if ( empty( $UserId ) ) {
				return;
			}

			$UserInfo     = get_user_by( 'id', $UserId );
			$UserName     = $UserInfo->user_login;
			$Redeem       = 'sumo_' . strtolower( $UserName );
			$AutoRedeem   = 'auto_redeem_' . strtolower( $UserName );
			$group        = 'coupons';
			$used_coupons = (float) WC()->version < (float) ( '3.7' ) ? $order->get_used_coupons() : $order->get_coupon_codes();
			if ( ! ( in_array( $Redeem, $used_coupons ) || in_array( $AutoRedeem, $used_coupons ) ) ) {
				$order->update_meta_data( 'rs_check_enable_option_for_redeeming', 'no' );
				return;
			}

			foreach ( $used_coupons as $CouponCode ) {
				$CouponId   = ( $Redeem == $CouponCode ) ? get_user_meta( $UserId, 'redeemcouponids', true ) : get_user_meta( $UserId, 'auto_redeemcoupon_ids', true );
				$CouponName = ( $Redeem == $CouponCode ) ? $Redeem : $AutoRedeem;
				if ( empty( $CouponId ) ) {
					continue;
				}

				if ( get_option( '_rs_restrict_coupon' ) == '1' || get_option( '_rs_enable_coupon_restriction' ) == 'no' ) {
					wp_trash_post( $CouponId );
				} else {
					self::schedule_cron_to_trash_sumo_coupon( $CouponId );
				}
				if ( class_exists( 'WC_Cache_Helper' ) ) {
					wp_cache_delete( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_' . $CouponName, 'coupons' );
				}
			}
			$EnableRedeem = ( in_array( $Redeem, $used_coupons ) || in_array( $AutoRedeem, $used_coupons ) ) ? get_option( 'rs_enable_redeem_for_order' ) : ( srp_check_is_array( $used_coupons ) ? get_option( 'rs_disable_point_if_coupon' ) : 'no' );
			$order->update_meta_data( 'rs_check_enable_option_for_redeeming', $EnableRedeem );
			$order->save();
		}

		/**
		 * Schedule Cron  to trash SUMO Coupon.
		 *
		 * @param int $coupon_id Coupon ID.
		 * */
		public static function schedule_cron_to_trash_sumo_coupon( $coupon_id ) {
			$time = get_option( 'rs_delete_coupon_specific_time' );
			if ( empty( $time ) ) {
				return;
			}

			$coupon_id = array( 'rs_coupon_id' => $coupon_id );
			if ( '1' == get_option( 'rs_delete_coupon_by_cron_time' ) ) {
				$next_schedule_time = time() + ( 24 * 60 * 60 * $time );
			} elseif ( '2' == get_option( 'rs_delete_coupon_by_cron_time' ) ) {
				$next_schedule_time = time() + ( 60 * 60 * $time );
			} else {
				$next_schedule_time = time() + ( 60 * $time );
			}
			if ( false == wp_next_scheduled( $next_schedule_time, 'rs_delete_coupon_based_on_cron' ) ) {
				wp_schedule_single_event( $next_schedule_time, 'rs_delete_coupon_based_on_cron', $coupon_id );
			}
		}
	}

	RSPointExpiry::init();
}
