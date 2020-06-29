<?php
/**
 * Customer Signup coupon email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/crp_email_template.php.
 *
 *
 * @author 	MakeWebBetter
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php echo $email_heading . "\n\n";
// $coupon_amount = wc_price($coupon_amount); ?>
<p><?php _e( 'Thanks for creating an account, Below is your Coupon Details. ', 'coupon-referral-program' ) ?></p>
<?php
echo $template = '<style>@media screen and (max-width: 600px) {
		.mwb_wuc_price_code_wrapper {
			width: 100% !important;
			display: block;
			padding: 15px 10px !important;}}</style>
<table class="mwb_wuc_email_template" style="width: 100%!important; max-width: 600px; text-align: center; font-size: 20px;" role="presentation" border="0" width="600" cellspacing="0" cellpadding="0" align="center">
	<tbody>
		<tr>
			<td style="background: #fff;">
				<table style="border: 2px dashed #b9aca1;" border="0" width="100%" cellspacing="0" cellpadding="0">
					<tbody>
						<tr>
							<td colspan="2">
								<div style="text-align: center;"><span style="display: inline-block;padding: 5px 15px; border: 1px dashed #6d5050; margin-bottom: 10px; background-color: rgba(241, 225, 225, 0.12); font-weight: bold;">'.$coupon_code.'</span></div>
							</td>
						</tr>
					<tr>
						<td class="mwb_wuc_price_code_wrapper" style="width: 50%;">
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tbody>
									<tr>
										<td>
											<div style="text-align: center;">'.$coupon_amount.'</div>
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
												<p style="margin: 0;">'.__('Exp Date:','coupon-referral-program')
													.$coupon_expiry.'
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
</table>';

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );