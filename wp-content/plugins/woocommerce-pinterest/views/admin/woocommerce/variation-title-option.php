<?php if (!defined('ABSPATH')) {
	die;
} ?>

<?php
/**
 * Used vars list
 *
 * @var int $loop
 * @var bool $isVariableHasPinTitle
 */
?>

<label class="tips" data-tip="
<?php
esc_html_e('Enable this option if a variation has a Pin title',
	'woocommerce-pinterest');
?>
	">
	<?php esc_html_e('Pin title', 'woocommerce-pinterest'); ?>
	<input type="checkbox" class="checkbox variable_is_pin_title"
		   name="variable_is_pin_title[<?php echo esc_attr($loop); ?>]"
														<?php
														checked($isVariableHasPinTitle,
														true);
														?>
		 />
</label>
