<?php
/**
 * Component Options Pagination template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/component-options-pagination.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @since    2.6.0
 * @version  4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div class="component_pagination cp_clearfix <?php echo esc_attr( $classes ); ?>" data-pagination_data="<?php echo esc_attr( json_encode( $pagination_data ) ); ?>" <?php echo $has_pages ? '' : 'style="display:none"'; ?>><?php
	if ( $append_options ) {
		?><button class="button component_options_load_more"><?php echo __( 'Load more&hellip;', 'woocommerce-composite-products' ); ?></button><?php
	}
?></div>
