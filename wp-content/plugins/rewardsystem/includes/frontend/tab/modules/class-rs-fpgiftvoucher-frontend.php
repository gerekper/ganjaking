<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSGiftVoucherFrontend' ) ) {

	class RSGiftVoucherFrontend {

		public static function init() {
			if ( '1' == get_option( 'rs_redeem_voucher_position' ) ) {
				add_action( 'woocommerce_before_my_account' , array( __CLASS__ , 'giftvoucherfield_in_myaccount' ) ) ;
			} else {
				add_action( 'woocommerce_after_my_account' , array( __CLASS__ , 'giftvoucherfield_in_myaccount' ) ) ;
			}
		}

		public static function giftvoucherfield_in_myaccount() {
			if ( '2' == get_option( 'rs_show_hide_redeem_voucher' ) ) {
				return ;
			}

			if ( 'yes' != get_option( 'rs_reward_content' ) ) {
				return ;
			}

			self::giftvoucherfield() ;
		}

		public static function giftvoucherfield() {
					
			$BanType = check_banning_type( get_current_user_id() ) ;
			if ( 'redeemingonly' == $BanType || 'both' == $BanType ) {
				return ;
			}

			ob_start() ;
			?>
			<div class="rs_giftvoucher_field">
				<h3><?php echo wp_kses_post(get_option( 'rs_redeem_your_gift_voucher_label' )) ; ?></h3>
				<input type="text" size="50" name="rs_redeem_voucher" id="rs_redeem_voucher_code" placeholder="<?php echo wp_kses_post(get_option( 'rs_redeem_your_gift_voucher_placeholder' ) ); ?>"/>
				<input type="submit" class="button rs_gift_voucher_submit_button <?php echo esc_attr(get_option( 'rs_extra_class_name_redeem_gift_voucher_button' )) ; ?>" name="rs_submit_redeem_voucher" value="<?php echo wp_kses_post(get_option( 'rs_redeem_gift_voucher_button_label' )) ; ?>"/>
				<div class="rs_redeem_voucher_error"></div>
				<div class="rs_redeem_voucher_success"></div>
			</div>
			<?php

		}

	}

	RSGiftVoucherFrontend::init() ;
}
