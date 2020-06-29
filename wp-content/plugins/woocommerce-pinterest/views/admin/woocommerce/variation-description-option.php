<?php if (!defined('ABSPATH')) {
	die;
} ?>

<?php
/**
 * Used vars list
 *
 * @var int $loop
 * @var bool $isVariableHasPinDescription
 */
?>

<label class="tips" data-tip="
<?php 
esc_html_e('Enable this option if a variation has a Pin description',
	'woocommerce-pinterest'); 
?>
	">
	<?php esc_html_e('Pin description', 'woocommerce-pinterest'); ?>
	<input type="checkbox" class="checkbox variable_is_pin_description"
		   name="variable_is_pin_description[<?php echo esc_attr($loop); ?>]" 
														<?php 
														checked($isVariableHasPinDescription,
														true); 
														?>
		 />
</label>
