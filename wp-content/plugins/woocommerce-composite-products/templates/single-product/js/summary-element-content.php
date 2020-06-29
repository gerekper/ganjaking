<?php
/**
 * Composite Summary Element Content template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/js/summary-element-content.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><script type="text/template" id="tmpl-wc_cp_summary_element_content">
	<div class="summary_element_title summary_element_data">
		<h3 class="title summary_element_content"><span class="step_index">{{ data.element_index }}</span> <span class="step_title">{{ data.element_title }}</span></h3>
	</div>
	<# if ( data.element_is_in_widget ) { #>
		<div class="summary_element_tap summary_element_data">
			<span class="summary_element_select_wrapper">
				<a href="{{ data.element_button_link }}" rel="nofollow" class="summary_element_select" aria-label="{{ data.element_label }}">{{{ data.element_action }}}</a>
			</span>
		</div>
	<# } #>
	<# if ( data.element_image_src ) { #>
		<div class="summary_element_image summary_element_data">
			<img class="summary_element_content" alt="{{ data.element_image_title }}" src="{{ data.element_image_src }}" srcset="{{ data.element_image_srcset }}" sizes="{{ data.element_image_sizes }}" />
		</div>
	<# } #>
	<# if ( data.element_selection_title ) { #>
		<div class="summary_element_selection summary_element_data">
			<# if ( data.element_selection_title ) { #>
				<span class="summary_element_content">{{{ data.element_selection_title }}}</span>
			<# } #>
		</div>
	<# } #>
	<# if ( data.element_price ) { #>
		<div class="summary_element_price summary_element_data">{{{ data.element_price }}}</div>
	<# } #>
	<# if ( ! data.element_is_in_widget ) { #>
		<div class="summary_element_button summary_element_data">
			<a href="{{ data.element_button_link }}" rel="nofollow" class="button summary_element_select" aria-label="{{ data.element_label }}">{{{ data.element_action }}}</a>
		</div>
	<# } #>
</script>
