<?php
/**
 * The fronend-specific functionality of the plugin.
 *
 * @link       https://woocommerce.com/
 * @since      1.0.0
 *
 * @package    Coupon_Referral_Program
 * @subpackage Coupon_Referral_Program/modal
 */

/**
 * Exit if accessed directly
 */
/*  Popup Style */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="mwb_crp_loader" class="mwb_crp_hide_element">
	<img src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ); ?>public/images/loading.gif">
</div>
<div class="mwb-coupon-popup-wrapper mwb_crp_hide_element">
	<div class="mwb-coupon-popup-content">
		<div class="mwb-coupon-popup-header">
			<h2><?php esc_html_e( 'Available Coupon', 'coupon-referral-program' ); ?></h2>
			<span class="mwb-coupon-close-btn">X</span>
		</div>
		<div class="mwb-coupon-popup-body">
			<div class="mwb-coupon-popup-row">
			</div>
		</div>
		<div class="mwb-coupon-popup-footer">
			<a href="javascript:void(0);" class="mwb-coupon-close-btn"><?php esc_html_e( 'Close', 'coupon-referral-program' ); ?></a>
		</div>
	</div>
</div>
