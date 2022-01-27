<?php if (!defined('ABSPATH')) {
	die;
} ?>

<?php

/**
 * Used vars list
 *
 * @var bool $hidden
 * @var int $loop
 * @var string $pinTitle
 * @var string $descriptionFieldDescription
 */

?>

<div class="show_if_variation_pin_title" <?php echo $hidden ? 'style="display:none;"' : ''; ?> >
<?php
woocommerce_wp_textarea_input(
	array(
		'id' => "pin_title_template{$loop}",
		'name' => "pin_title_template[{$loop}]",
		'value' => $pinTitle,
		'label' => __('Pin title', 'woocommerce-pinterest'),
		'desc_tip' => true,
		'description' => $descriptionFieldDescription,
		'wrapper_class' => 'form-row form-row-full'
	)
);
?>
</div>
