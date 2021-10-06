<?php
/**
 * Component Options - Dropdown template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/component-options-dropdown.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version  3.7.0
 * @since    3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div class="component_options_select_wrapper" <?php echo $hide_dropdown ? 'style="display:none;"' : ''; ?>>
	<select id="component_options_<?php echo $component_id; ?>" class="component_options_select" name="wccp_component_selection[<?php echo $component_id; ?>]"></select>
</div>
