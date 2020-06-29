<?php
/**
 * Product Loop End
 *
 * @version     2.0.0
 */
global $woocommerce_loop, $porto_woocommerce_loop;

if ( isset( $porto_woocommerce_loop['view'] ) && 'creative' == $porto_woocommerce_loop['view'] && ! isset( $porto_woocommerce_loop['creative_grid'] ) ) {
	echo '<li class="grid-col-sizer"></li>';
}

do_action( 'porto_woocommerce_shop_loop_end' );

$woocommerce_loop['product_loop']  = '';
$woocommerce_loop['cat_loop']      = '';
$woocommerce_loop['category-view'] = '';
$woocommerce_loop['columns']       = '';
$woocommerce_loop['column_width']  = '';
$woocommerce_loop['addlinks_pos']  = '';
unset( $GLOBALS['porto_woocommerce_loop'] );
?>
</ul>
