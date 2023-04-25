<?php
/**
 * Mix and Match Child Item Wrapper
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/grid/mnm-child-item-wrapper-open.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce Mix and Match/Templates
 * @since   1.3.0
 * @version 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<li <?php wc_product_class( $classes, $child_item->get_product() ); ?> <?php echo wc_implode_html_attributes( $child_item->get_data_attributes() ); ?> >
