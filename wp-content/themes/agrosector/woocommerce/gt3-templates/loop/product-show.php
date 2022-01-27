<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$products_count = wc_get_default_products_per_row() * wc_get_default_product_rows_per_page();

$gt3_shop_list_options = get_query_var( 'gt3_shop_list_options_'.get_the_ID() );
if ( $gt3_shop_list_options && $gt3_shop_list_options['per_page'] ) $products_count = $gt3_shop_list_options['per_page'];

$products_count = !empty($products_count) ? $products_count : 9;

$orderby = isset( $_GET['show_prod'] ) ? wc_clean( $_GET['show_prod'] ) : 'default';
$show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

$catalog_orderby_options = array(
	$products_count => esc_html__( 'Default', 'agrosector' ),
	$products_count*2 => $products_count*2,
	$products_count*4 => $products_count*4,
);

?>
<form class="woocommerce-ordering products-show" method="get">
	<p class="gt3-products-header-sort_by"><?php esc_html_e('Show:', 'agrosector'); ?></p>
	<div class="gt3-woocommerce-ordering-select">
		<select name="show_prod" class="orderby" title="<?php esc_attr_e('orderby', 'agrosector'); ?>">
			<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
				<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php wc_query_string_form_fields( null, array( 'show_prod', 'submit' ) ); ?>
</form>