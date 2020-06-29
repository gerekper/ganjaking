<?php
// Exit if accessed directly
!defined( 'ABSPATH' ) && exit;

$per_page      = 5;
$page          = !empty( $_REQUEST[ 'page' ] ) ? $_REQUEST[ 'page' ] : 1;
$offset        = $page > 1 ? ( ( $page - 1 ) * $per_page ) : 0;
$product_types = yith_wcpb_get_allowed_product_types();

$search = !empty( $_REQUEST[ 's' ] ) ? $_REQUEST[ 's' ] : '';

$args = array(
    'limit'            => $per_page,
    'offset'           => $offset,
    'type'             => array_keys( $product_types ),
    'status'           => 'publish',
    'paginate'         => true,
    'suppress_filters' => false,
);

if ( !!$search && 'sku:' === substr( $search, 0, 4 ) ) {
    $args[ 'sku' ] = substr( $search, 4 );
} else {
    $args[ 's' ] = $search;
}

$args = apply_filters( 'yith_wcpb_select_product_box_args', $args );

$products_query = new WC_Product_Query( $args );
$results        = $products_query->get_products();
$products       = $results->products;
$total          = $results->total;
$total_pages    = $results->max_num_pages;
?>
<table class="yith-wcpb-select-product-box__products__table widefat striped">
    <thead>
    <tr>
        <td class="column-image"><?php _e( 'Image', 'yith-woocommerce-product-bundles' ) ?></td>
        <td class="column-name"><?php _e( 'Name', 'yith-woocommerce-product-bundles' ) ?></td>
        <td class="column-price"><?php _e( 'Price', 'yith-woocommerce-product-bundles' ) ?></td>
        <td class="column-type"><?php _e( 'Type', 'yith-woocommerce-product-bundles' ) ?></td>
        <td class="column-action"><?php _e( 'Action', 'yith-woocommerce-product-bundles' ) ?></td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ( $products as $product ):
        /** @var WC_Product $product */
        $product_type_raw = $product->get_type();
        $product_type = isset( $product_types[ $product_type_raw ] ) ? $product_types[ $product_type_raw ] : ucfirst( str_replace( '_', ' ', $product->get_type() ) );
        $edit_link = get_edit_post_link( $product->get_id() );
        ?>
        <tr>
            <td class="column-image"><?php echo $product->get_image( 'thumbnail' ) ?></td>

            <td class="column-name">
                <div class="product-name">
                    <a href="<?php echo $edit_link ?>" target="_blank"><?php echo $product->get_formatted_name(); ?></a>
                </div>
                <div class="product-info">
                    <?php if ( !$product->is_in_stock() ) : ?>
                        <span class="product-single-info out-of-stock"><?php _e( 'Out of stock', 'yith-woocommerce-product-bundles' ) ?></span>
                    <?php endif; ?>
                </div>
            </td>
            <td class="column-price"><?php echo $product->get_price_html() ?></td>
            <td class="column-type"><?php echo $product_type ?></td>
            <td class="column-action">
                <span class="yith-wcpb-add-product" data-id="<?php echo $product->get_id() ?>"><?php _e( 'Add', 'yith-woocommerce-product-bundles' ) ?></span>
                <span class="yith-wcpb-product-added"><?php _e( 'Added', 'yith-woocommerce-product-bundles' ) ?></span>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="yith-wcpb-select-product-box__products__pagination">
    <?php
    $prev_disabled = $page < 2 ? 'disabled' : '';
    $next_disabled = $page >= $total_pages ? 'disabled' : '';
    $prev_page     = max( 1, ( $page - 1 ) );
    $next_page     = min( $total_pages, ( $page + 1 ) );
    ?>
    <span class="first <?php echo $prev_disabled ?>" data-page="1">&laquo;</span>
    <span class="prev <?php echo $prev_disabled ?>" data-page="<?php echo $prev_page ?>"><?php _e( 'prev', 'yith-woocommerce-product-bundles' ) ?></span>
    <span class="current"><?php echo sprintf( "%s/%s", $page, $total_pages ) ?></span>
    <span class="next <?php echo $next_disabled ?>" data-page="<?php echo $next_page ?>"><?php _e( 'next', 'yith-woocommerce-product-bundles' ) ?></span>
    <span class="last <?php echo $next_disabled ?>" data-page="<?php echo $total_pages ?>">&raquo;</span>
</div>
