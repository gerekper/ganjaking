<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://woocommerce.com/
 * @since      1.0.0
 *
 * @package    class-coupon-referral-program
 * @subpackage class-coupon-referral-program/public/partials
 */

$user_id         = get_current_user_ID();
$mwb_crp_revenue = $this->get_revenue( $user_id );
?>
<?php
if ( $this->is_social_sharing_enabled() || $this->check_share_vai_referal_code() ) {
	include_once COUPON_REFERRAL_PROGRAM_DIR_PATH . 'public/partials/coupon-referral-program-public-referal-sharing-section.php';
}
?>
<style type="text/css"><?php echo wp_kses_post( self::get_custom_style_popup_btn() ); ?></style>
<?php
/*Hide coupon section if points and rewards is enable.*/
if ( self::mwb_crp_points_rewards_hide_referal() ) {
	return;
}
?>
<div class="mwb-crp-referral-wrapper">
	<div class="mwb-crp-referral-column">
		<div class="mwb-crp-referral-column-inner">
			<div class="mwb-crp-referral-icon"><?php echo wp_kses_post( get_woocommerce_currency_symbol() ); ?></div>
			<span><?php esc_html_e( 'Total Utilization', 'coupon-referral-program' ); ?></span>
			<h4><?php echo wp_kses_post( wc_price( $this->get_utilize_coupon_amount( $user_id ) ) ); ?></h4>
		</div>	
	</div>
	<div class="mwb-crp-referral-column">
		<div class="mwb-crp-referral-column-inner">
			<div class="mwb-crp-referral-icon"><i class="fas fa-users"></i></div>
			<span><?php esc_html_e( 'Total Referred Users', 'coupon-referral-program' ); ?></span>
			<h4><?php echo esc_html( $mwb_crp_revenue['referred_users'] ); ?></h4>
		</div>	
	</div>
	<div class="mwb-crp-referral-column">
		<div class="mwb-crp-referral-column-inner">
		<div class="mwb-crp-referral-icon"><i class="fas fa-credit-card"></i></div>
			<span><?php esc_html_e( 'Total Coupons', 'coupon-referral-program' ); ?></span>
			<h4><?php echo esc_html( $mwb_crp_revenue['total_coupon'] ); ?></h4>
		</div>	
	</div>
</div>
<div class="mwb-crp-referral-table-wrapper">
	<table id="mwb-crp-referral-table" class="mwb-crp-referral-table">
		<thead >
			<tr >
				<th class="mwb_crp_reporting_heading"><?php esc_html_e( 'Coupon', 'coupon-referral-program' ); ?></th>
				<th class="mwb_crp_reporting_heading"><?php esc_html_e( 'Coupon Created', 'coupon-referral-program' ); ?></th>
				<th class="mwb_crp_reporting_heading"><?php esc_html_e( 'Expiry Date', 'coupon-referral-program' ); ?></th>
				<th class="mwb_crp_reporting_heading"><?php esc_html_e( 'Event', 'coupon-referral-program' ); ?></th>
				<th class="mwb_crp_reporting_heading"><?php esc_html_e( 'Referred Users', 'coupon-referral-program' ); ?></th>
				<th class="mwb_crp_reporting_heading"><?php esc_html_e( 'Usage Count', 'coupon-referral-program' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ( ! empty( $this->get_signup_coupon( $user_id ) ) && is_array( $this->get_signup_coupon( $user_id ) ) ) :
				$signup_coupon = $this->get_signup_coupon( $user_id );
				$coupon        = new WC_Coupon( $signup_coupon['singup'] );
				if ( 'publish' === get_post_status( $signup_coupon['singup'] ) && $this->mwb_crp_validate_coupon( $coupon ) ) :
					?>
			<tr>
				<td data-th="<?php esc_html_e( 'Coupon ', 'coupon-referral-program' ); ?>">
					<div class="mwb-crp-coupon-code">
						<p id="<?php echo 'mwb' . esc_html( $signup_coupon['singup'] ); ?>">
							<?php echo esc_html( $coupon->get_code() ); ?>
						</p>
						<span class="mwb-crp-coupon-amount">
						<?php
						echo ( 'fixed_cart' === $coupon->get_discount_type() ) ?
							wp_kses_post( wc_price( $coupon->get_amount() ) ) : esc_html( $coupon->get_amount() ) . '%';
						?>
						</span> 
						<img class="mwb-crp-coupon-scissors" src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'public/images/scissors.png'; ?>" alt="scissor png"> 
						<span class="mwb-crp-coupon-wrap">
							<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb<?php echo esc_html( $signup_coupon['singup'] ); ?>" aria-label="copied">
								<span class="mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copy', 'coupon-referral-program' ); ?></span>
								<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copied', 'coupon-referral-program' ); ?></span>
								<img src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png'; ?>" alt="copy icon">
							</button>
						</span>
					</div>
				</td>
				<td data-th="<?php esc_html_e( 'Coupon Created', 'coupon-referral-program' ); ?>"><?php echo esc_html( $this->mwb_crp_get_transalted_coupon_created_date( $coupon ) ); ?></td>
				<td data-th="<?php esc_html_e( 'Expiry Date', 'coupon-referral-program' ); ?>"><?php echo esc_html( $this->mwb_crp_get_transalted_coupon_exp_date( $coupon ) ); ?></td>
				<td data-th="<?php esc_html_e( 'Event', 'coupon-referral-program' ); ?>"><?php esc_html_e( 'Signup Coupon', 'coupon-referral-program' ); ?></td>
				<td data-th="<?php esc_html_e( 'Referred Users', 'coupon-referral-program' ); ?>">----</td>
				<td data-th="<?php esc_html_e( 'Usage Count', 'coupon-referral-program' ); ?>"><?php echo esc_html( $coupon->get_usage_count() ); ?></td>	
			</tr>
			<?php endif; ?>
			<?php endif; ?>
			<!-- Refferal sigup -->
			<?php
			if ( ! empty( $this->mwb_crp_get_referal_signup_coupon( $user_id ) ) ) :
				foreach ( $this->mwb_crp_get_referal_signup_coupon( $user_id ) as $coupon_code => $crp_user_id ) :
					$coupon = new WC_Coupon( $coupon_code );
					if ( 'publish' === get_post_status( $coupon_code ) && $this->mwb_crp_validate_coupon( $coupon ) ) :
						?>
			<tr>
				<td data-th="<?php esc_html_e( 'Coupon ', 'coupon-referral-program' ); ?>">
					<div class="mwb-crp-coupon-code"><p id="mwb<?php echo esc_html( $coupon_code ); ?>"><?php echo esc_html( $coupon->get_code() ); ?></p>
						<span class="mwb-crp-coupon-amount">
						<?php
						echo ( 'fixed_cart' === $coupon->get_discount_type() ) ?
							wp_kses_post( wc_price( $coupon->get_amount() ) ) : esc_html( $coupon->get_amount() ) . '%';
						?>
							</span> <img class="mwb-crp-coupon-scissors" src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'public/images/scissors.png'; ?>" alt="scissor icon"> <span class="mwb-crp-coupon-wrap">
							<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb<?php echo esc_html( $coupon_code ); ?>" aria-label="copied">
								<span class="mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copy', 'coupon-referral-program' ); ?></span>
								<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copied', 'coupon-referral-program' ); ?></span>
								<img src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png'; ?>" alt="copy icon">
							</button>
						</span>
					</div>
				</td>
				<td data-th="<?php esc_html_e( 'Coupon Created', 'coupon-referral-program' ); ?>"><?php echo esc_html( $this->mwb_crp_get_transalted_coupon_created_date( $coupon ) ); ?></td>
				<td data-th="<?php esc_html_e( 'Expiry Date', 'coupon-referral-program' ); ?>"><?php echo esc_html( $this->mwb_crp_get_transalted_coupon_exp_date( $coupon ) ); ?></td>
				<td data-th="<?php esc_html_e( 'Event', 'coupon-referral-program' ); ?>"><?php esc_html_e( 'Referral Signup', 'coupon-referral-program' ); ?></td>
				<td data-th="<?php esc_html_e( 'Referred Users', 'coupon-referral-program' ); ?>"><?php echo get_userdata( $crp_user_id ) ? esc_html( get_userdata( $crp_user_id )->data->display_name ) : esc_html__( 'User has been deleted', 'coupon-referral-program' ); ?></td>
				<td data-th="<?php esc_html_e( 'Usage Count', 'coupon-referral-program' ); ?>"><?php echo esc_html( $coupon->get_usage_count() ); ?></td>
			</tr>
				<?php endif; ?>
					<?php
			endforeach;
			endif;
			?>
			<!-- End Refferal sigup -->
			<!-- start referal purchase coupon -->
			<?php
			if ( ! empty( $this->get_referral_purchase_coupons( $user_id ) ) ) :
				foreach ( $this->get_referral_purchase_coupons( $user_id ) as $coupon_code => $crp_user_id ) :
					$coupon   = new WC_Coupon( $coupon_code );
					$order_id = get_post_meta( $coupon->get_id(), 'coupon_created_to', true );
					if ( 'publish' === get_post_status( $coupon_code ) && $this->mwb_crp_validate_coupon( $coupon ) ) :
						?>
			<tr>
				<td data-th="<?php esc_html_e( 'Coupon ', 'coupon-referral-program' ); ?>">
					<div class="mwb-crp-coupon-code"><p id="mwb<?php echo esc_html( $coupon_code ); ?>"><?php echo esc_html( $coupon->get_code() ); ?></p>
						<span class="mwb-crp-coupon-amount">
						<?php
						echo ( 'fixed_cart' === $coupon->get_discount_type() ) ?
							wp_kses_post( wc_price( $coupon->get_amount() ) ) : esc_html( $coupon->get_amount() ) . '%';
						?>
							</span> <img class="mwb-crp-coupon-scissors" src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'public/images/scissors.png'; ?>" alt="scissor icon"> <span class="mwb-crp-coupon-wrap">
							<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb<?php echo esc_html( $coupon_code ); ?>" aria-label="copied">
								<span class="mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copy', 'coupon-referral-program' ); ?></span>
								<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copied', 'coupon-referral-program' ); ?></span>
								<img src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png'; ?>" alt="copy icon">
							</button>
						</span>
					</div>
				</td>
				<td data-th="<?php esc_html_e( 'Coupon Created', 'coupon-referral-program' ); ?>"><?php echo esc_html( $this->mwb_crp_get_transalted_coupon_created_date( $coupon ) ); ?></td>
				<td data-th="<?php esc_html_e( 'Expiry Date', 'coupon-referral-program' ); ?>"><?php echo esc_html( $this->mwb_crp_get_transalted_coupon_exp_date( $coupon ) ); ?></td>
				<td data-th="<?php esc_html_e( 'Event', 'coupon-referral-program' ); ?>"><?php echo esc_html__( 'Referral Purchase For', 'coupon-referral-program' ) . ' #' . esc_html( $order_id ); ?></td>
				<td data-th="<?php esc_html_e( 'Referred Users', 'coupon-referral-program' ); ?>"><?php echo get_userdata( $crp_user_id ) ? esc_html( get_userdata( $crp_user_id )->data->display_name ) : esc_html__( 'User has been deleted', 'coupon-referral-program' ); ?></td>
				<td data-th="<?php esc_html_e( 'Usage Count', 'coupon-referral-program' ); ?>"><?php echo esc_html( $coupon->get_usage_count() ); ?></td>
			</tr>
				<?php endif; ?>
					<?php
			endforeach;
			endif;
			?>
			<!-- end referal purchase coupon -->
			<!-- start referal purchase coupon on guest user via referal code -->
			<?php
			if ( ! empty( $this->get_referral_purchase_coupons_on_guest( $user_id ) ) ) :
				foreach ( $this->get_referral_purchase_coupons_on_guest( $user_id ) as $coupon_code => $email ) :
					$coupon   = new WC_Coupon( $coupon_code );
					$order_id = get_post_meta( $coupon->get_id(), 'coupon_created_to', true );
					if ( 'publish' === get_post_status( $coupon_code ) && $this->mwb_crp_validate_coupon( $coupon ) ) :
						?>
			<tr>
				<td data-th="<?php esc_html_e( 'Coupon ', 'coupon-referral-program' ); ?>">
					<div class="mwb-crp-coupon-code"><p id="mwb<?php echo esc_html( $coupon_code ); ?>"><?php echo esc_html( $coupon->get_code() ); ?></p>
						<span class="mwb-crp-coupon-amount">
						<?php
						echo ( 'fixed_cart' === $coupon->get_discount_type() ) ?
							wp_kses_post( wc_price( $coupon->get_amount() ) ) : esc_html( $coupon->get_amount() ) . '%';
						?>
							</span> <img class="mwb-crp-coupon-scissors" src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'public/images/scissors.png'; ?>" alt="scissor icon"> <span class="mwb-crp-coupon-wrap">
							<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb<?php echo esc_html( $coupon_code ); ?>" aria-label="copied">
								<span class="mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copy', 'coupon-referral-program' ); ?></span>
								<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php esc_html_e( 'Copied', 'coupon-referral-program' ); ?></span>
								<img src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png'; ?>" alt="copy icon">
							</button>
						</span>
					</div>
				</td>
				<td data-th="<?php esc_html_e( 'Coupon Created', 'coupon-referral-program' ); ?>"><?php echo esc_html( $this->mwb_crp_get_transalted_coupon_created_date( $coupon ) ); ?></td>
				<td data-th="<?php esc_html_e( 'Expiry Date', 'coupon-referral-program' ); ?>"><?php echo esc_html( $this->mwb_crp_get_transalted_coupon_exp_date( $coupon ) ); ?></td>
				<td data-th="<?php esc_html_e( 'Event', 'coupon-referral-program' ); ?>"><?php echo esc_html__( 'Referral Purchase Via Guest User For', 'coupon-referral-program' ) . ' #' . esc_html( $order_id ); ?></td>
				<td data-th="<?php esc_html_e( 'Referred Users', 'coupon-referral-program' ); ?>"><?php echo $email ? esc_html( $email ) : esc_html__( 'Email not found', 'coupon-referral-program' ); ?></td>
				<td data-th="<?php esc_html_e( 'Usage Status', 'coupon-referral-program' ); ?>"><?php echo esc_html( $coupon->get_usage_count() ); ?></td>
			</tr>
				<?php endif; ?>
					<?php
			endforeach;
			endif;
			?>
			<!-- End referal purchase coupon on guest user via referal code -->
		</tbody>	
	</table>
</div>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
