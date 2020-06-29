<?php if (!defined('ABSPATH')) {
	die;
} ?>

<?php

/**
 * Used vars list
 *
 * @var bool $hidden
 * @var int $loop
 * @var string $pinDescription
 * @var string $descriptionFieldDescription
 */

?>

<div class="show_if_variation_pin_description" <?php echo $hidden ? 'style="display:none;"' : ''; ?> >
<?php
woocommerce_wp_textarea_input(
	array(
		'id' => "pin_description_template{$loop}",
		'name' => "pin_description_template[{$loop}]",
		'value' => $pinDescription,
		'label' => __('Pin description', 'woocommerce-pinterest'),
		'desc_tip' => true,
		'description' => $descriptionFieldDescription,
		'wrapper_class' => 'form-row form-row-full'
	)
);
?>
</div>
