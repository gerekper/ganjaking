<?php if (!defined('ABSPATH')) {
	die;
} ?>

<?php

use \Premmerce\WooCommercePinterest\Pinterest\Api\ApiState;

?>

<?php

/**
 * Used vars list
 *
 * @var string $field_key
 * @var ApiState $state
 * @var string $userName
 *
 *
 */

?>


<?php $baseAdminUrl = admin_url('admin-post.php'); ?>

<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['title']); ?> <?php echo wc_help_tip($data['desc_tip'], true); ?></label>
	</th>

	<td class="forminp">
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>

			<?php if ($state->isConnected($type)) : ?>
				<?php
				$urlParams['action']      = 'woocommerce_pinterest_disconnect';
				$urlParams['api_version'] = esc_html($type);

				$url = $baseAdminUrl . '?' . build_query($urlParams);
				$url = wp_nonce_url($url, $urlParams['action'], 'woocommerce_pinterest_nonce');
				?>

				<p>
					<a href="<?php echo esc_url($url); ?>"
					   class="button"><?php esc_html_e('Disconnect', 'woocommerce-pinterest'); ?></a>
				</p>

				<p class="description">
					<?php
					/* translators: %s is replaced with Pinterest user first name */
					echo sprintf(esc_html__('Connected as %s', 'woocommerce-pinterest'), esc_html($userName));
					?>
				</p>


			<?php else : ?>

				<?php

				$urlParams['action']      = 'woocommerce_pinterest_connect';
				$urlParams['api_version'] = esc_html($type);

				$url = $baseAdminUrl . '?' . build_query($urlParams);
				$url = wp_nonce_url($url, $urlParams['action'], 'woocommerce_pinterest_nonce'); 
				?>

				<p class="description">
					<?php
					/* translators: %s is replaced with Pinterest user first name */
					esc_html_e('Please provide access to your Pinterest account in the following pop-up window.', 'woocommerce-pinterest');
					?>
				</p>

				<p>
					<a href="<?php echo esc_url($url); ?>"
					   class="button"><?php esc_html_e('Connect', 'woocommerce-pinterest'); ?></a>
				</p>

			<?php endif; ?>
		</fieldset>
	</td>
</tr>



