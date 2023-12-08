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
 * @author  ThemeComplete
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $classtotalform, $epo_internal_counter, $product_id, $form_prefix, $price, $tc_form_prefix_class, $tc_form_prefix_name, $classcart, $hidden, $tm_epo_final_total_box, $tm_epo_show_final_total, $tm_epo_show_options_total, $forcart, $is_sold_individually, $product_type, $regular_price, $is_on_sale, $product_price_rules, $fields_price_rules, $force_quantity, $price_override, $is_vat_exempt, $non_base_location_prices, $taxable, $tax_rate, $base_tax_rate, $taxes_of_one, $base_taxes_of_one, $modded_taxes_of_one, $tax_string, $tax_display_mode, $prices_include_tax, $args, $variations ) ) :
	$classtotalform            = (string) $classtotalform;
	$epo_internal_counter      = (string) $epo_internal_counter;
	$product_id                = (string) $product_id;
	$form_prefix               = (string) $form_prefix;
	$price                     = (string) $price;
	$tc_form_prefix_class      = (string) $tc_form_prefix_class;
	$tc_form_prefix_name       = (string) $tc_form_prefix_name;
	$classcart                 = (string) $classcart;
	$hidden                    = (string) $hidden;
	$tm_epo_final_total_box    = (string) $tm_epo_final_total_box;
	$tm_epo_show_final_total   = (string) $tm_epo_show_final_total;
	$tm_epo_show_options_total = (string) $tm_epo_show_options_total;
	$forcart                   = (string) $forcart;
	$is_sold_individually      = (string) $is_sold_individually;
	$product_type              = (string) $product_type;
	$regular_price             = (string) $regular_price;
	$is_on_sale                = (string) $is_on_sale;
	$product_price_rules       = (string) $product_price_rules;
	$fields_price_rules        = (string) $fields_price_rules;
	$force_quantity            = (string) $force_quantity;
	$price_override            = (string) $price_override;
	$is_vat_exempt             = (string) $is_vat_exempt;
	$non_base_location_prices  = (string) $non_base_location_prices;
	$taxable                   = (string) $taxable;
	$tax_rate                  = (string) $tax_rate;
	$base_tax_rate             = (string) $base_tax_rate;
	$taxes_of_one              = (string) $taxes_of_one;
	$base_taxes_of_one         = (string) $base_taxes_of_one;
	$modded_taxes_of_one       = (string) $modded_taxes_of_one;
	$tax_string                = (string) $tax_string;
	$tax_display_mode          = (string) $tax_display_mode;
	$variations                = (string) $variations;
	$prices_include_tax        = (string) $prices_include_tax;
	$args                      = (array) $args;
	?>
<div class="tc-totals-form tm-product-id-<?php echo esc_attr( $product_id ); ?> <?php echo esc_attr( $classtotalform ); ?>" data-epo-id="<?php echo esc_attr( $epo_internal_counter ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>">
	<input type="hidden" value="<?php echo esc_attr( $price ); ?>" name="cpf_product_price<?php echo esc_attr( $form_prefix ); ?>" class="cpf-product-price">
	<input type="hidden" value="<?php echo esc_attr( $form_prefix ); ?>" name="<?php echo esc_attr( $tc_form_prefix_name ); ?>" class="<?php echo esc_attr( $tc_form_prefix_class ); ?>">
	<?php do_action( 'wc_epo_totals_form', $product_id ); ?>
	<div id="tm-epo-totals<?php echo esc_attr( $form_prefix ); ?>"
		class="tc-epo-totals tm-product-id-<?php echo esc_attr( $product_id ); ?> tm-epo-totals tm-custom-prices-total<?php echo esc_attr( $hidden ); ?> <?php echo esc_attr( $classcart ); ?>"
		data-epo-id="<?php echo esc_attr( $epo_internal_counter ); ?>"
		data-tm-epo-final-total-box="<?php echo esc_attr( $tm_epo_final_total_box ); ?>"
		data-tm-epo-show-final-total="<?php echo esc_attr( $tm_epo_show_final_total ); ?>"
		data-tm-epo-show-options-total="<?php echo esc_attr( $tm_epo_show_options_total ); ?>"
		data-cart-id="<?php echo esc_attr( $forcart ); ?>"
		data-is-sold-individually="<?php echo esc_attr( $is_sold_individually ); ?>"
		data-type="<?php echo esc_attr( $product_type ); ?>"
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
	<?php
endif;
