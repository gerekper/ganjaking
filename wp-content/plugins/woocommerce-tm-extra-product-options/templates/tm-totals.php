<?php
/**
 * The template for displaying the final totals box
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-totals.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="tc-totals-form tm-product-id-<?php echo esc_attr( $product_id ); ?> <?php echo esc_attr( $classtotalform ); ?>"
     data-epo-id="<?php echo esc_attr( $epo_internal_counter ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <input type="hidden" value="<?php echo esc_attr( $price ); ?>" name="cpf_product_price<?php echo esc_attr( $form_prefix ); ?>"
           class="cpf-product-price"/>
    <input type="hidden" value="<?php echo esc_attr( $form_prefix ); ?>" name="<?php echo esc_attr( $tc_form_prefix_name ); ?>" class="<?php echo esc_attr( $tc_form_prefix_class ); ?>"/>
    <?php do_action( "wc_epo_totals_form", $product_id ); ?>
    <div id="tm-epo-totals<?php echo esc_attr( $form_prefix ); ?>"
         class="tc-epo-totals tm-product-id-<?php echo esc_attr( $product_id ); ?> tm-epo-totals tm-custom-prices-total<?php echo esc_attr( $hidden ); ?> <?php echo esc_attr( $classcart ); ?>"
         data-epo-id="<?php echo esc_attr( $epo_internal_counter ); ?>"
         data-tm-epo-final-total-box="<?php echo esc_attr( $tm_epo_final_total_box ); ?>"
         data-theme-name="<?php echo esc_attr( $theme_name ); ?>"
         data-cart-id="<?php echo esc_attr( $forcart ); ?>"
         data-is-sold-individually="<?php echo esc_attr( $is_sold_individually ); ?>"
         data-type="<?php echo esc_attr( $type ); ?>"
         data-price="<?php echo esc_attr( $price ); ?>"
         data-regular-price="<?php echo esc_attr( $regular_price ); ?>"
         data-is-on-sale="<?php echo esc_attr( $is_on_sale ); ?>"
         data-product-price-rules="<?php echo esc_attr( $product_price_rules ); ?>"
         data-fields-price-rules="<?php echo esc_attr( $fields_price_rules ); ?>"
         data-force-quantity="<?php echo esc_attr( $force_quantity ); ?>"
         data-price-override="<?php echo esc_attr( $price_override ); ?>"
         data-is-vat-exempt="<?php echo esc_attr( $is_vat_exempt ); ?>"
         data-non-base-location-prices="<?php echo esc_attr( $non_base_location_prices ); ?>"
         data-taxable="<?php echo esc_attr( $taxable ); ?>"
         data-tax-rate="<?php echo esc_attr( $tax_rate ); ?>"
         data-base-tax-rate="<?php echo esc_attr( $base_tax_rate ); ?>"
         data-taxes-of-one="<?php echo esc_attr( $taxes_of_one ); ?>"
         data-base-taxes-of-one="<?php echo esc_attr( $base_taxes_of_one ); ?>"
         data-modded-taxes-of-one="<?php echo esc_attr( $modded_taxes_of_one ); ?>"
         data-tax-string="<?php echo esc_attr( $tax_string ); ?>"
         data-tax-display-mode="<?php echo esc_attr( $tax_display_mode ); ?>"
         data-prices-include-tax="<?php echo esc_attr( $prices_include_tax ); ?>"
         data-variations="<?php echo esc_attr( $variations ); ?>" <?php do_action( 'wc_epo_template_tm_totals', $args ); ?> ></div>
</div>
