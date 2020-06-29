<?php
/**
 * Composite Pagination template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/js/composite-pagination.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 6.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><script type="text/template" id="tmpl-wc_cp_composite_pagination">
	<nav class="pagination_elements_wrapper">
		<ul class="pagination_elements" style="list-style:none">
			<# for ( var index = 0; index <= data.length - 1; index++ ) { #>
				<li class="pagination_element pagination_element_{{ data[ index ].element_id }} {{ data[ index ].element_class }}" data-item_id="{{ data[ index ].element_id }}">
					<span class="element_inner">
						<span class="element_index">{{ index + 1 }}</span>
						<span class="element_title">
							<a class="element_link {{ data[ index ].element_state_class }}" href="{{ data[ index ].element_link }}" rel="nofollow">{{ data[ index ].element_title }}</a>
						</span>
					</span>
				</li>
			<# } #>
		</ul>
	</nav>
</script>
