<?php if (!defined('ABSPATH')) {
	die;
} ?>

<?php
/**
 * Used vars list
 *
 * @var string $stateMessage

 */

?>

<?php if ($stateMessage) : ?>
	<span class="dashicons dashicons-warning woo-pinterest-orange woo-pinterest-state"
		  title="<?php echo esc_attr($stateMessage); ?>">
	</span>
<?php endif; ?>
