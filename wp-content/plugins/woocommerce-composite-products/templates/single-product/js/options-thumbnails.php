<?php
/**
 * Thumbnail Option template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/js/options-thumbnails.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 7.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><script type="text/template" id="tmpl-wc_cp_options_thumbnails">
	<# if ( data.is_lazy_loading ) { #>
		<p class="results_message lazy_loading_results">
			<?php _e( 'Loading&hellip;', 'woocommerce-composite-products' ); ?>
		</p>
	<# } else { #>
		<# if ( data.length > 0 ) { #>
			<ul class="component_option_thumbnails_container cp_clearfix" style="list-style:none">
				<# for ( var index = 0; index <= data.length - 1; index++ ) { #>
					<li id="component_option_thumbnail_container_{{ data[ index ].option_id }}" class="component_option_thumbnail_container {{ data[ index ].outer_classes }}">
						<div id="component_option_thumbnail_{{ data[ index ].option_id }}" class="cp_clearfix component_option_thumbnail {{ data[ index ].inner_classes }}" data-val="{{ data[ index ].option_id }}">
							<div class="image thumbnail_image">
								<a class="component_option_thumbnail_link" href="#">
									{{{ data[ index ].option_thumbnail_html }}}
								</a>
							</div>
							<div class="thumbnail_description">
								<h5 class="thumbnail_title title">{{{ data[ index ].option_display_title }}}</h5>
								<# if ( data[ index ].option_price_html ) { #>
									<span class="thumbnail_price price">{{{ data[ index ].option_price_html }}}</span>
								<# } #>
							</div>
							<div class="thumbnail_buttons">
								<button class="button component_option_thumbnail_select" aria-label="{{ data[ index ].option_button_label }}">{{ data[ index ].option_button_text }}</button>
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
