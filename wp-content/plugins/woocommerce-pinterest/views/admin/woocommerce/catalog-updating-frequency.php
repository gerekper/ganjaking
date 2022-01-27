<?php if ( ! defined('ABSPATH')) {
	die;
} ?>

<?php
	/**
	 * Used vars list
	 *
	 * @var string $title
	 * @var string $days
	 * @var string $time
	 * @var int|false $nextScheduledTime
	 */
?>

<?php 
$formattedNextScheduledTime = ( new DateTime() )->format('Y-m-d H:i:s');
$formattedNow               = ( new DateTime() )->format('Y-m-d H:i:s');
?>

<tr valign="top" style="display: none">
	<th scope="row" class="titledesc">
		<?php echo esc_html($title); ?>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo esc_html($title); ?></span></legend>

			<label for="woocommerce-pinterest-catalog-updating-frequency-days"><?php esc_html_e('Update catalog once in (days number)', 'woocommerce-pinterest'); ?></label>
			<input
					id="woocommerce-pinterest-catalog-updating-frequency-days"
					name="woocommerce_pinterest_pinterest_catalog_updating_frequency[days]"
					type="number"
					min="1"
					max="365"
					value="<?php echo esc_attr($days); ?>"
			/>
			<label for="woocommerce-pinterest-catalog-updating-frequency-time"><?php esc_html_e(_x('at', 'about time', 'woocommerce_pinterest')); ?></label>
			<input
					id="woocommerce-pinterest-catalog-updating-frequency-time"
					name="woocommerce_pinterest_pinterest_catalog_updating_frequency[time]"
					type="time"
					value="<?php echo esc_attr($time); ?>"
			/>

			<?php /* translators: '%s' is replaced with formatted time */ ?>
			<?php $nextScheduledMessage = sprintf(esc_html(__('Next catalog generation is scheduled on %s', 'woocommerce-pinterest')), $formattedNextScheduledTime); ?>
			<?php $notScheduledMessage = __('Next catalog generation hasn\'t been scheduled yet.', 'woocommerce-pinterest'); ?>
			<p class="description"><?php echo $nextScheduledTime ? esc_html($nextScheduledMessage) : esc_html($notScheduledMessage); ?>

			<p class="description">
			<?php
			/* translators: '%s' is replaced with formatted time*/
			esc_html_e(
					sprintf('Current server time is %s', esc_html($formattedNow)), 'woocommerce-pinterest'
					); 
			?>
					</p>
		</fieldset>
	</td>
</tr>
