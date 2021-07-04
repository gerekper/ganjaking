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
 ?>
<p><?php _e( 'Congratulation! You have received referral link, Below is the Referral link. ', 'coupon-referral-program' ) ?></p>
<p><?php _e( 'Referred By :- ','coupon-referral-program' ) ?><?php echo $user_name;?></p>
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
								<div style="text-align: center;"><span style="display: inline-block;padding: 5px 15px; border: 1px dashed #6d5050; margin-bottom: 10px; background-color: rgba(241, 225, 225, 0.12); font-weight: bold;">'.$refferal_link.'</span></div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>';
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );