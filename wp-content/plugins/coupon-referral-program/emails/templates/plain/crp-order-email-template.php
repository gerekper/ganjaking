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
echo esc_html( $email_heading ) . "\n\n"; ?>

<p><?php esc_html_e( 'Congratulations! You have unlocked a discount.', 'coupon-referral-program' ); ?></p>
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
				<table style="border: 2px dashed #b9aca1;" border="0" width="100%" cellspacing="0" cellpadding="0">
					<tbody>
						<tr>
							<td colspan="2">
								<div style="text-align: center;"><span style="display: inline-block;padding: 5px 15px; border: 1px dashed #6d5050; margin-bottom: 10px; background-color: rgba(241, 225, 225, 0.12); font-weight: bold;">' . $coupon_code . '</span></div>
							</td>
						</tr>
					<tr>
						<td class="mwb_wuc_price_code_wrapper" style="width: 50%;">
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tbody>
									<tr>
										<td>
											<div style="text-align: center;"><?php echo wp_kses_post( $coupon_amount ); ?></div>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td class="mwb_wuc_price_code_wrapper mwb_wuc_price_code_date_wrapper" style="width: 50%;">
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tbody>
									<tr>
										<td>
											<div style="padding-top: 4px;text-align: center;">
												<p style="margin: 0;">
												<?php
												$exp_text = esc_html__( 'Exp Date:', 'coupon-referral-program' );
												echo esc_html( $exp_text ) . ' ' . esc_html( $coupon_expiry );
												?>
												</p>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<?php
/**
 * Filter woocommerce email.
 *
 * @since 1.6.4
 * @param string $email_footer
 */
echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
