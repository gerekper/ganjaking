<?php
/**
 * Template of table in Product Page
 *
 * @author  Yithemes
 * @package YITH Product Size Charts for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCPSC' ) ) {
    exit;
} // Exit if accessed directly

/**
 * @var int $c_id -> the id of the Product Size Chart post
 */

$c = get_post( $c_id );

$button_text = get_post_meta( $c_id, 'button_text', true );
$button_text = !!($button_text) ? $button_text : $c->post_title;
?>

<span class="yith-wcpsc-product-size-chart-button" data-chart-id="<?php echo $c_id ?>"><?php echo $button_text ?></span>



