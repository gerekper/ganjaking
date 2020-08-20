<?php
/**
 * The template for displaying the product element variation id
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;
?><div class="tc-epo-element-product-container-variation-id tm-hidden"><input type="hidden" class="product-variation-id" name="<?php echo esc_attr($name); ?>_variation_id" value="<?php echo esc_attr($variation_id); ?>"></div>