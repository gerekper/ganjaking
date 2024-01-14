<?php
/**
 * Advance Tab Functionality.
 *
 * @package Rewardsystem/Frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RSFunctionForAdvanced' ) ) {

	/**
	 * Class Initialization.
	 */
	class RSFunctionForAdvanced {

		/**
		 * Add Hooks.
		 */
		public static function init() {
			if ( 'yes' === get_option( 'rs_reward_content_menu_page' ) ) {
				$title_url = ( '' !== get_option( 'rs_my_reward_url_title' ) ) ? get_option( 'rs_my_reward_url_title' ) : 'sumo-rewardpoints';
				add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'add_reward_menu_in_my_account' ) );
				add_action( 'woocommerce_account_' . sanitize_title( $title_url ) . '_endpoint', array( __CLASS__, 'reward_menu_content' ) );
				add_action( 'woocommerce_before_my_account', array( __CLASS__, 'subscribe_option' ) );
				add_action( 'wp_head', array( __CLASS__, 'redirect_after_unsubscribe' ) );
			}
		}

		/**
		 * Add Reward Menu in My Account Page.
		 *
		 * @param array $items Menu items.
		 */
		public static function add_reward_menu_in_my_account( $items ) {
			if ( 'earningonly' === check_banning_type( get_current_user_id() ) || 'both' === check_banning_type( get_current_user_id() ) ) {
				return $items;
			}

			$title_url   = ( '' !== get_option( 'rs_my_reward_url_title' ) ) ? get_option( 'rs_my_reward_url_title' ) : 'sumo-rewardpoints';
			$reward_menu = array( $title_url => get_option( 'rs_my_reward_content_title' ) );
			$items       = array_slice( $items, 0, 2 ) + $reward_menu + array_slice( $items, 2, count( $items ) - 1 );
			return $items;
		}

		/**
		 * Get Reward Menu content in My Account Page.
		 */
		public static function reward_menu_content() {

			if ( 'earningonly' === check_banning_type( get_current_user_id() ) || 'both' === check_banning_type( get_current_user_id() ) ) {
				wp_safe_redirect( get_permalink() );
				return;
			}

			$column_values = array(
				'rs_myrewards_table',
				'rs_nominee_field',
				'rs_gift_voucher_field',
				'rs_referral_table',
				'rs_generate_referral_link',
				'rs_refer_a_friend_form',
				'rs_my_cashback_form',
				'rs_my_cashback_table',
				'rs_email_subscribe_link',
			);

			$sorted_column = srp_check_is_array( get_option( 'rs_sorted_menu_settings_list' ) ) ? get_option( 'rs_sorted_menu_settings_list' ) : $column_values;
			if ( ! isset( $sorted_column['rs_my_cashback_form'] ) ) {
				$sorted_column = array_slice( $sorted_column, 0, 4, true ) +
						array( 'rs_my_cashback_form' ) +
						array_slice( $sorted_column, 3, count( $sorted_column ) - 4, true );
			}

			foreach ( $sorted_column as $column_key => $column_value ) {
				$function_name = str_replace( 'rs_', '', $column_key );
				if ( ! method_exists( 'RSFunctionForAdvanced', $function_name ) ) {
					continue;
				}

				self::$function_name();
			}
		}

		public static function my_cashback_form() {

			if ( 'yes' != get_option( 'rs_cashback_activated' ) || '1' != get_option( 'rs_my_cashback_form_menu_page', 1 ) ) {
				return;
			}

			if ( '1' != get_option( 'rs_enable_disable_encashing' ) ) {
				return;
			}

			RS_Rewardsystem_Shortcodes::shortcode_rsencashform();
		}

		public static function my_cashback_table() {

			if ( 'yes' != get_option( 'rs_cashback_activated' ) || '1' != get_option( 'rs_my_cashback_table_menu_page' ) ) {
				return '';
			}

			RSCashBackFrontend::cash_back_log();
		}

		public static function myrewards_table() {

			if ( '1' != get_option( 'rs_my_reward_table_menu_page' ) ) {
				return '';
			}

						RSFunctionForMessage::reward_log( true );
		}

		public static function nominee_field() {

			if ( 'yes' != get_option( 'rs_nominee_activated' ) || '1' != get_option( 'rs_show_hide_nominee_field_menu_page' ) ) {
				return '';
			}

						RSFunctionForNominee::display_nominee_field_in_my_account();
		}

		public static function gift_voucher_field() {

			if ( 'yes' != get_option( 'rs_gift_voucher_activated' ) || '1' != get_option( 'rs_show_hide_redeem_voucher_menu_page' ) ) {
				return '';
			}

						RSGiftVoucherFrontend::giftvoucherfield();
		}

		public static function referral_table() {

			if ( 'yes' != get_option( 'rs_referral_activated' ) || '1' != get_option( 'rs_show_hide_referal_table_menu_page' ) ) {
				return '';
			}

			if ( ! check_if_referral_is_restricted_based_on_history() ) {
				return '';
			}

						RSFunctionForReferralSystem::referral_list_table_in_menu();
		}

		public static function refer_a_friend_form() {

			if ( 'yes' != get_option( 'rs_referral_activated' ) || '1' != get_option( 'rs_show_hide_refer_a_friend_menu_page', '1' ) || '1' != get_option( 'rs_enable_message_for_friend_form' ) ) {
				return '';
			}

						echo wp_kses_post( sprintf( '<h3 class="rs_refer_a_friend_title">%s</h3>', esc_html__( 'Refer a Friend Form', 'rewardsystem' ) ) );
						RS_Rewardsystem_Shortcodes::display_refer_a_friend_form();
		}

		public static function email_subscribe_link() {

			if ( 1 != get_option( 'rs_show_hide_your_subscribe_link_menu_page' ) ) {
				return '';
			}

				echo wp_kses_post( sprintf( '<h3 class="rs_email_subscribe_link_title">%s</h3>', esc_html__( 'Email - Subscribe Link', 'rewardsystem' ) ) );
				self::field_for_subcribe( true );
		}

		public static function generate_referral_link() {

			if ( 'yes' != get_option( 'rs_referral_activated' ) || '1' != get_option( 'rs_show_hide_generate_referral_menu_page' ) ) {
				return '';
			}

			if ( '2' == get_option( 'rs_show_hide_generate_referral_link_type' ) ) {
				if ( check_if_referral_is_restricted_based_on_history() ) {
					RSFunctionForReferralSystem::static_referral_link();
				}
			} elseif ( check_if_referral_is_restricted_based_on_history() ) {
					RSFunctionForReferralSystem::list_of_generated_link_and_field();
			}
		}
		public static function subscribe_option() {

			if ( 'yes' != get_option( 'rs_reward_content' ) ) {
				return;
			}

			if ( 2 == get_option( 'rs_show_hide_your_subscribe_link' ) ) {
				return;
			}

			self::field_for_subcribe( true );
		}
		/* For Unsubscribe option in My account Page */

		public static function field_for_subcribe( $echo = false ) {

			$banning_type = check_banning_type( get_current_user_id() );
			if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
				return;
			}

			ob_start();
			?>
			<div class="rs_subscriptionoption">
				<p class="rs_email_subscription">
					<input type="checkbox"
						name="subscribeoption" 
						id="subscribeoption" 
						value="yes" <?php checked( 'yes', get_user_meta( get_current_user_id(), 'unsub_value', true ) ); ?>/> 
						<?php echo esc_html( get_option( 'rs_unsub_field_caption', 'Unsubscribe Here to Stop Receiving Reward Points Emails' ) ); ?>
				</p>
			</div>
			<?php
			$content = ob_get_contents();

			if ( ! $echo ) {
				return $content;
			}

			ob_end_flush();
		}

		public static function redirect_after_unsubscribe() {
			if ( isset( $_GET['userid'] ) && isset( $_REQUEST['nonce'] ) ) {
				if ( isset( $_GET['unsub'] ) && ( 'yes' == $_GET['unsub'] ) && absint( $_GET['userid'] ) == get_current_user_id() ) {
					update_user_meta( absint( $_GET['userid'] ), 'unsub_value', sanitize_title( $_GET['unsub'] ) );
				}
			}
		}
	}

	RSFunctionForAdvanced::init();
}
