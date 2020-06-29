<?php
/**
 * Component Options Title template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/component-options-title.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version  3.12.0
 * @since    3.12.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><p class="component_section_title">
	<label class="select_label">
		<?php echo __( 'Available options:', 'woocommerce-composite-products' ); ?>
	</label>
</p><?php
