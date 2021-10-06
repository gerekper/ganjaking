<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFunctionForEmailTemplate' ) ) {

	class RSFunctionForEmailTemplate {

		public static function init() {
			add_action( 'woocommerce_before_my_account' , array( __CLASS__ , 'subscribe_option' ) ) ;

			add_action( 'wp_head' , array( __CLASS__ , 'redirect_after_unsubscribe' ) ) ;
		}

		/* For Unsubscribe option in My account Page */

		public static function field_for_subcribe( $echo = false ) {
			
			$banning_type = check_banning_type( get_current_user_id() ) ;
			if ( 'earningonly' == $banning_type  || 'both' == $banning_type ) {
				return ;
			}
			
			ob_start() ;
			?>
			<div class="rs_subscriptionoption">
				<p class="rs_email_subscription">
					<input type="checkbox"
						name="subscribeoption" 
						id="subscribeoption" 
						value="yes" <?php checked( 'yes' , get_user_meta( get_current_user_id() , 'unsub_value' , true ) ) ; ?>/> 
						<?php echo wp_kses_post(get_option( 'rs_unsub_field_caption', 'Unsubscribe Here to Stop Receiving Reward Points Emails' ) ); ?>
				</p>
			</div>
			<?php
			$content = ob_get_contents() ;
			
			if ( ! $echo ) {
				return $content ;
			}
						
			ob_end_flush() ;
		}

		public static function subscribe_option() {
			if ( 'yes'  != get_option( 'rs_reward_content' )) {
				return ;
			}

			if ( 2 == get_option( 'rs_show_hide_your_subscribe_link' )  ) {
				return ;
			}

			self::field_for_subcribe( true ) ;
		}

		public static function redirect_after_unsubscribe() {
			if ( isset( $_GET[ 'userid' ] ) && isset( $_REQUEST[ 'nonce' ] ) ) {
				if ( isset($_GET[ 'unsub' ]) && ( 'yes' == $_GET[ 'unsub' ] ) ) {
					update_user_meta( absint($_GET[ 'userid' ]) , 'unsub_value' , sanitize_title($_GET[ 'unsub' ]) ) ;
					wp_safe_redirect( site_url() ) ;
					exit() ;
				}
			}
		}

	}

	RSFunctionForEmailTemplate::init() ;
}
