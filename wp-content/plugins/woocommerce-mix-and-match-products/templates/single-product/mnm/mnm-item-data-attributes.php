<?php
/**
 * Mix and Match Item Data Attributes
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/mnm-product-data-attributes.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Kathy Darling
 * @package WooCommerce Mix and Match/Templates
 * @since   1.4.0
 * @version 1.9.9
 */
if ( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}
?>
<div class="mnm-item-data" style="display: none;" data-mnm_item_id="<?php echo esc_attr( $mnm_item_id ); ?>" data-regular_price="<?php echo esc_attr( $regular_price ); ?>" data-price="<?php echo esc_attr( $price ); ?>" data-price_incl_tax="<?php echo esc_attr( $price_incl_tax ); ?>" data-price_excl_tax="<?php echo esc_attr( $price_excl_tax ); ?>" data-original_quantity="<?php echo esc_attr( $original_quantity ); ?>" ></div>


