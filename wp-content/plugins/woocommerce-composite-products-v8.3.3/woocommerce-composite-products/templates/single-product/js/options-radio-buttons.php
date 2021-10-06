<?php
/**
 * Radio Button Option template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/js/options-radio-buttons.php'.
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

?><script type="text/template" id="tmpl-wc_cp_options_radio_buttons">
	<# if ( data.is_lazy_loading ) { #>
		<p class="results_message lazy_loading_results">
			<?php _e( 'Loading&hellip;', 'woocommerce-composite-products' ); ?>
		</p>
	<# } else { #>
		<# if ( data.length > 0 ) { #>
			<ul class="component_option_radio_buttons_container cp_clearfix" style="list-style:none">
				<# for ( var index = 0; index <= data.length - 1; index++ ) { #>
					<li id="component_option_radio_button_container_{{ data[ index ].option_suffix }}" class="component_option_radio_button_container {{ data[ index ].outer_classes }}">
						<div id="component_option_radio_button_{{ data[ index ].option_suffix }}" class="cp_clearfix component_option_radio_button {{ data[ index ].inner_classes }}" data-val="{{ data[ index ].option_id }}">
							<div class="radio_button_input">
								<input type="radio" id="wccp_component_radio_{{ data[ index ].option_group_id }}_{{ index }}" class="radio_button" name="wccp_component_radio[{{ data[ index ].option_group_id }}]" value="{{ data[ index ].option_id }}" <# if ( data[ index ].is_disabled ) { #>disabled="disabled"<# } #> <# if ( data[ index ].is_selected ) { #>checked="checked"<# } #> />
								<label for="wccp_component_radio_{{ data[ index ].option_group_id }}_{{ index }}" class="component_option_radio_button_select" aria-label="{{ data[ index ].option_display_title }}"></label>
							</div>
							<div class="radio_button_description">
								<h5 class="radio_button_title title">{{{ data[ index ].option_display_title }}}</h5>
								<# if ( data[ index ].option_price_html ) { #>
									<span class="radio_button_price price">{{{ data[ index ].option_price_html }}}</span>
								<# } #>
							</div>
						</div>
					</li>
				<# } #>
			</ul>
		<# } #>
		<# if ( data.length === 0 ) { #>
			<p class="results_message no_query_results">
				<?php _e( 'No results found.', 'woocommerce-composite-products' ); ?>
			</p>
		<# } else if ( _.where( data, { is_hidden: false } ).length === 0 ) { #>
			<p class="results_message no_compat_results">
				<?php _e( 'No compatible options to display.', 'woocommerce-composite-products' ); ?>
			</p>
		<# } #>
	<# } #>
</script>
