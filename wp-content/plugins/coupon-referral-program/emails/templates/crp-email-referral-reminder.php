<?php
/**
 * Customer Signup coupon email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/crp_email_template.php.
 *
 * @package    Coupon_Referral_Program
 * @subpackage Coupon_Referral_Program/emails/template/plain
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

if ( isset( $additional_content ) && '' !== $additional_content ) {
		echo wp_kses_post( $additional_content );
} else {
	?>
<style>
@media screen and (max-width: 600px) {
	.mwb_wuc_price_code_wrapper {
		width: 100% !important;
		display: block;
		padding: 15px 10px !important;
		}
	}
</style>
<table class="mwb_wuc_email_template" style="width: 100%!important; max-width: 600px; text-align: center; font-size: 20px;" role="presentation" border="0" width="600" cellspacing="0" cellpadding="0" align="center">
	<tbody>
		<tr>
			<td style="background: #fff;">
				<table style="border: 2px solid #b9aca1;" border="0" width="100%" cellspacing="0" cellpadding="0">
					<tbody>
						<tr>
							<td class="mwb_wuc_price_code_wrapper" colspan="2">
								<div style="text-align: center;"><span><?php esc_html_e( 'Refer a Friend using your referral code or referral link. Both will get the coupons', 'coupon-referral-program' ); ?></span></div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
	<?php
}
/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
