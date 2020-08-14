<?php
// Exit if accessed directly
!defined( 'ABSPATH' ) && exit;

?>
<div class="yith-wcpb-select-product-box">
    <div class="yith-wcpb-select-product-box__filters">
        <?php $minimum_characters = apply_filters( 'yith_wcpb_minimum_characters_ajax_search',3 ); ?>
        <input type="text" class="yith-wcpb-select-product-box__filter__search" placeholder="<?php echo sprintf( __( 'Search for a product (min %s characters)','yith-woocommerce-product-bundles' ),$minimum_characters ); ?>"/>
    </div>
    <div class="yith-wcpb-select-product-box__products">
        <?php include YITH_WCPB_TEMPLATE_PATH . '/admin/select-product-box-products.php'; ?>
    </div>
</div>
