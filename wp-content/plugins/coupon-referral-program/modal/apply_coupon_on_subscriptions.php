<?php
/**
 * Exit if accessed directly
 */
/*  Popup Style */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="mwb_crp_loader" style="display: none;">
	<img src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL;?>public/images/loading.gif">
</div>
<div class="mwb-coupon-popup-wrapper" style="display: none;">
	<div class="mwb-coupon-popup-content">
		<div class="mwb-coupon-popup-header">
			<h2><?php _e('Available Coupon','coupon-referral-program');?></h2>
			<span class="mwb-coupon-close-btn">X</span>
		</div>
		<div class="mwb-coupon-popup-body">
			<div class="mwb-coupon-popup-row">
			</div>
		</div>
		<div class="mwb-coupon-popup-footer">
			<a href="javascript:void(0);" class="mwb-coupon-close-btn"><?php _e('Close','coupon-referral-program');?></a>
		</div>
	</div>
</div>