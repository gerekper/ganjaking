<?php if (!defined('ABSPATH')) {
	die;
} ?>

<?php if (!$state->isConnected('v1')) : ?>
	<span class="woo-pinterest-orange">
	<?php esc_html_e('Connect your Pinterest account to update boards', 'woocommerce-pinterest'); ?>
	</span>
<?php elseif ($state->isWaiting()) : ?>
	<span class="woo-pinterest-orange">
		<?php esc_html_e('API is temporary unavailable. You\'ve reached the daily limit (100 requests), please, wait 24 hours and try again.', 'woocommerce-pinterest'); ?>
	</span>
<?php else : ?>
	<a class="woocommerce-pinterest-update-link"
	   href="<?php echo esc_url(admin_url('admin-post.php') . '?action=woocommerce_pinterest_update_boards'); ?>">
		<?php esc_html_e('Update boards', 'woocommerce-pinterest'); ?>
	</a>
<?php endif; ?>
