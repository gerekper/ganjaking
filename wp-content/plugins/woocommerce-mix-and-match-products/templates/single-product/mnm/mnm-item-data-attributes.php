<?php
/**
 * Mix and Match Item Data Attributes
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/mnm-product-data-attributes.php.
 * Note: As of 2.0 this is left for back-compatibility, but the add to cart script will look on the opening div/tr element.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce Mix and Match/Templates
 * @since   1.4.0
 * @version 2.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="mnm-item-data" style="display: none;" <?php echo wc_implode_html_attributes( $child_item->get_data_attributes() ); // phpcs:ignore WordPress.Security.EscapeOutput ?>></div>


