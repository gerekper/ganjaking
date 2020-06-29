<?php
/**
 * Selection title template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/composited-product/js/selection.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 7.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<script type="text/template" id="tmpl-wc_cp_component_selection_title">

	<# if ( data.show_title ) { #>
		<# if ( data.show_selection_ui ) { #>
			<p class="component_section_title selected_option_label_wrapper">
				<label class="selected_option_label"><?php echo __( 'Your selection:', 'woocommerce-composite-products' ); ?></label>
			</p>
		<# } #>
		<{{ data.tag }} class="composited_product_title component_section_title product_title" aria-label="{{ data.selection_title_aria }}" tabindex="-1">{{{ data.selection_title }}}</{{ data.tag }}>
	<# } #>

	<# if ( data.show_selection_ui && data.show_reset_ui ) { #>
		<p class="component_section_title clear_component_options_wrapper">
			<a class="clear_component_options" href="#"><?php echo __( 'Clear selection', 'woocommerce-composite-products' ); ?></a>
		</p>
	<# } #>

</script>
