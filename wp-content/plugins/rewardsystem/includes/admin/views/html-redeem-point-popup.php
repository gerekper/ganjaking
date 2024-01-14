<?php
/**
 * Redeem Point Popup.
 *
 * @since 29.8.0
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>

<div class='srp-redeem-point-wrapper'>
	<h1>
		<?php
		/* translators: %d - ID */
		echo wp_kses_post(sprintf(__('Order ID - #%d', 'rewardsystem'), $order_id));
		?>
	</h1>
	<div class="srp-user-details">
		<h2>
			<?php
			/* translators: %s - User name */
			echo wp_kses_post(sprintf(__('User Name - %s', 'rewardsystem'), $user_info->user_login));
			?>
		</h2>
	</div>
	<div class="srp-points-details">
		<h3>
			<?php
			/* translators: %s - Available Points */
			echo wp_kses_post(sprintf(__('Available Points - %s', 'rewardsystem'), $available_points));
			?>
		</h3>
	</div>
	<div class='srp-redeem-point-content'>
		<input type='text' id='srp-point-value' name='srp_redeem_point_value' placeholder='<?php esc_html_e('Enter the points', 'rewardsystem'); ?>'/>
		<input type='submit' class='srp-redeem-point-btn' value='<?php esc_html_e('Apply Points', 'rewardsystem'); ?>' />
	</div>
</div>
<?php
