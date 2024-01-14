<?php
/**
 * Redeem Point Button
 *
 * @since 29.8.0
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>

<button type='button' class='button srp-redeem-point-popup-button' value='<?php echo wp_kses_post($order->get_id()); ?>'><?php esc_html_e('Apply Points', 'rewardsystem'); ?></button>
<div class='srp-hide srp-popup-point-lightcase' data-popup='#srp-redeem-point-popup'>
	<div id='srp-redeem-point-popup'>
	</div>
</div>
<?php
