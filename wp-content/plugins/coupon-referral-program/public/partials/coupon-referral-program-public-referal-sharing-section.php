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

$crp_public_obj = new Coupon_Referral_Program_Public( 'coupon-referral-program', '1.6.7' );
$user_id        = get_current_user_ID();
?>
<div class="mwb_crp_referal_section_wrap">
	<fieldset class="mwb_crp_referal_section">
		<p class="mwb_cpr_heading"><?php echo get_option( 'referral_tab_text' ) ? esc_html( get_option( 'referral_tab_text' ) ) : esc_html__( 'Refer your friends and you’ll earn discounts on their purchases', 'coupon-referral-program' ); ?></p>
		<?php $crp_public_obj->mwb_crp_get_referrl_code( $user_id ); ?>
		<?php if ( $crp_public_obj->is_social_sharing_enabled() ) { ?>
		<span class="mwb_crp_referral_link"><?php esc_html_e( 'Referral Link: ', 'coupon-referral-program' ); ?></span>
		<div class="mwb_cpr_logged_wrapper">
			<div class="mwb_cpr_refrral_code_copy">
				<p id="mwb_cpr_copy_link">
					<code id="mwb_cpr_copyy_link"><?php echo esc_html( $crp_public_obj->get_referral_link( $user_id ) ); ?></code>
					<span class="mwb_cpr_copy_btn_wrap">
						<button class="mwb_cpr_btn_copy mwb_tooltip" data-clipboard-target="#mwb_cpr_copyy_link" aria-label="copied">
						<span class="mwb_tooltiptext"><?php esc_html_e( 'Copy', 'coupon-referral-program' ); ?></span>
						<span class="mwb_tooltiptext_copied mwb_tooltiptext"><?php esc_html_e( 'Copied', 'coupon-referral-program' ); ?></span>
						<img src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png'; ?>" alt="copy icon">
						</button>
					</span>
				</p>
			</div>
			<div class="clear">
			</div>
		</div>
	<?php } ?>
		<?php
		if ( $crp_public_obj->is_social_sharing_enabled() ) {
			$html = $crp_public_obj->get_social_sharing_html( $user_id );
			// phpcs:ignore WordPress.Security.EscapeOutput
			?>
			<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.9";
			fjs.parentNode.insertBefore(js, fjs);
			}(document, "script", "facebook-jssdk"));</script>
			<?php
				echo wp_kses_post( $html );
			?>
			<div class="mwb_crp_email_wrap">
				<p id="mwb_crp_notice"></p>
				<input type="email" name="mwb_crp_email_id[]" class="mwb_crp_email_id" placeholder="Enter Email Id.." />
			</div>
			<div class="wps_crp_email_buttons">
				<button id="mwb_crp_add_more" class="button alt wps_crp_email_button"><?php esc_html_e( 'Add More', 'coupon-referral-program' ); ?></button>
				<button id="mwb_crp_email_send" class="button alt wps_crp_email_button"><?php esc_html_e( 'Send', 'coupon-referral-program' ); ?></button>
			</div>
			<?php

		}
		?>
	</fieldset>
</div>
