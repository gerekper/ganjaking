<?php
/**
 * Show options for ordering
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$shop_page_uri = yit_get_woocommerce_layered_nav_link();

$filter_value_args = array(
    'queried_object' => get_queried_object()
);

$filter_value = yit_get_filter_args( $filter_value_args );
$rel_nofollow = yith_wcan_add_rel_nofollow_to_url( true );


?>
<ul class="orderby">
    <?php foreach ( $catalog_orderby_options as $id => $name ) : ?>

        <?php if( $orderby == $id ) : ?>
            <?php $a_class = 'orderby-item active'; ?>
            <?php unset($filter_value['orderby'] ); ?>
        <?php else: ?>
            <?php $a_class = 'orderby-item'; ?>
            <?php $filter_value['orderby'] = $id; ?>
        <?php endif; ?>

        <li class="orderby-wrapper">
            <a <?php echo $rel_nofollow; ?> data-id="<?php echo esc_attr( $id ); ?>" class="<?php echo $a_class ?>" href="<?php echo esc_url( add_query_arg( $filter_value, $shop_page_uri ) ) ?>">
                <?php echo esc_html( $name ); ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
