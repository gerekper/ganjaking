<?php
/**
 * List of Charts in widget
 *
 * @author  Yithemes
 * @package YITH Product Size Charts for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCPSC' ) ) {
    exit;
} // Exit if accessed directly

/*
 * $c_id -> the id of the Product Size Chart post
 */

$c = get_post($c_id);
?>
<li class="yith-wcpsc-product-size-chart-list" data-chart-id="<?php echo $c_id ?>"><?php echo $c->post_title ?></li>

