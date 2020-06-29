<?php
/**
 * Component Options Pagination template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/js/options-pagination.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @since    3.7.0
 * @version  4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><script type="text/template" id="tmpl-wc_cp_options_pagination">
	<p class="index woocommerce-result-count" tabindex="-1">{{ data.i18n_page_of_pages }}</p>
	<nav class="woocommerce-pagination">
		<ul class="page-numbers">

			<# if ( data.page > 1 ) { #>
				<li><a class="page-numbers component_pagination_element prev" data-page_num="{{ data.page - 1 }}" href="#" rel="nofollow" aria-label="<?php echo _x( 'Previous page', 'options pagination previous label', 'woocommerce-composite-products' ); ?>"><?php echo _x( '&larr;', 'options pagination previous', 'woocommerce-composite-products' ); ?></a></li>
			<# } #>

			<# for ( var i = 1; i <= data.pages; i++ ) { #>
				<# if ( ( i >= data.page - data.range_mid && i <= data.page + data.range_mid ) || data.pages <= data.pages_in_range || i <= data.range_end || i > data.pages - data.range_end ) { #>
					<li>
						<# if ( data.page === i ) { #>
							<span aria-current="page" class="page-numbers component_pagination_element number current" data-page_num="{{ i }}">{{ i }}</span>
						<# } else { #>
							<a class="page-numbers component_pagination_element number" data-page_num="{{ i }}" href="#" rel="nofollow" aria-label="<?php echo sprintf( _x( 'Page %s', 'options pagination label', 'woocommerce-composite-products' ), '{{ i }}' ); ?>">{{ i }}</a>
						<# } #>
					</li>
				<# } else if ( ( i === data.page - data.range_mid - 1 ) || ( i === data.page + data.range_mid + 1 ) || ( i === data.range_end + 1 && data.page < data.range_end ) || ( i === data.pages - data.range_end - 1 && data.page > data.pages - data.range_end + data.range_mid + 1 ) ) { #>
					<li><span class="page-numbers component_pagination_element dots"><?php echo _x( '&hellip;', 'options pagination dots', 'woocommerce-composite-products' ); ?></span></li>
				<# } #>
			<# } #>

			<# if ( data.page < data.pages ) { #>
				<li><a class="page-numbers component_pagination_element next" data-page_num="{{ data.page + 1 }}" href="#" rel="nofollow" aria-label="<?php echo _x( 'Next page', 'options pagination next label', 'woocommerce-composite-products' ); ?>"><?php echo _x( '&rarr;', 'options pagination next', 'woocommerce-composite-products' ); ?></a></li>
			<# } #>

		</ul>
	</nav>
</script>
