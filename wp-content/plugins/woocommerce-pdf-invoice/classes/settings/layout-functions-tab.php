<?php 

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$default_layout_settings = array(
                                "quantity"  => array(
                                                    "use"   => 1,
                                                    "field" => "qty",
                                                    "title" => "Qty",
                                                    "width" => "5",
                                                    "order" => "10"
                                                    ),
                                "sku"       => array(
                                                    "use"   => 0,
                                                    "field" => "sku",
                                                    "title" => "SKU",
                                                    "width" => "10",
                                                    "order" => "20"
                                                    ),
                                "image"     => array(
                                                    "use"   => 0,
                                                    "field" => "qty",
                                                    "title" => "Qty",
                                                    "width" => "10",
                                                    "order" => "30"
                                                    ),
                                "product"   => array(
                                                    "use"   => 1,
                                                    "field" => "qty",
                                                    "title" => "Qty",
                                                    "width" => "50",
                                                    "order" => "40"
                                                    ),
                                "price_ex_use" => array(
                                                    "use"   => 1,
                                                    "field" => "qty",
                                                    "title" => "Qty",
                                                    "width" => "9",
                                                    "order" => "50"
                                                    ),
                                "total_ex_use" => array(
                                                    "use"   => 1,
                                                    "field" => "qty",
                                                    "title" => "Qty",
                                                    "width" => "9",
                                                    "order" => "60"
                                                    ),
                                "tax"     => array(
                                                    "use"   => 1,
                                                    "field" => "qty",
                                                    "title" => "Qty",
                                                    "width" => "7",
                                                    "order" => "70"
                                                    ),
                                "price_inc" => array(
                                                    "use"   => 1,
                                                    "field" => "qty",
                                                    "title" => "Qty",
                                                    "width" => "10",
                                                    "order" => "80"
                                                    ),
                                "total_inc" => array(
                                                    "use"   => 1,
                                                    "field" => "qty",
                                                    "title" => "Qty",
                                                    "width" => "10",
                                                    "order" => "90"
                                                    ),
                                );

?>

<h3 class="dompdf-config"><?php _e("Columns For Order Details Section." , 'woocommerce-pdf-invoice' ); ?></h3>
                    
<form method="post" action="" >
<table class="dompgf-debugging-table">
	<tr>
    	<th><?php _e("Use" , 'woocommerce-pdf-invoice' ); ?></th>
      <th><?php _e("Field" , 'woocommerce-pdf-invoice' ); ?></th>
      <th><?php _e("Title" , 'woocommerce-pdf-invoice' ); ?></th>
      <th><?php _e("Width" , 'woocommerce-pdf-invoice' ); ?></th>
      <th><?php _e("Order" , 'woocommerce-pdf-invoice' ); ?></th>
  </tr>
  <tr>
      <td><input type="checkbox" name="pdflayout_quantity_use"/></td>
      <td><input type="text" name="pdflayout_quantity_field" /></td>
      <td><input type="text" name="pdflayout_quantity_title" /></td>
      <td><input type="text" name="pdflayout_quantity_width" /></td>
      <td><input type="text" name="pdflayout_quantity_order" /></td>
	</tr>
  <tr>
      <td><input type="checkbox" name="pdflayout_sku_use" /></td>
      <td><input type="text" name="pdflayout_sku_field" /></td>
      <td><input type="text" name="pdflayout_sku_title" /></td>
      <td><input type="text" name="pdflayout_sku_width" /></td>
      <td><input type="text" name="pdflayout_sku_order" /></td>
  </tr>
  <tr>
      <td><input type="checkbox" name="pdflayout_image_use" /></td>
      <td><input type="text" name="pdflayout_image_field" /></td>
      <td><input type="text" name="pdflayout_image_title" /></td>
      <td><input type="text" name="pdflayout_image_width" /></td>
      <td><input type="text" name="pdflayout_image_order" /></td>
  </tr>
  <tr>
      <td><input type="checkbox" name="pdflayout_product_use" /></td>
      <td><input type="text" name="pdflayout_product_field" /></td>
      <td><input type="text" name="pdflayout_product_title" /></td>
      <td><input type="text" name="pdflayout_product_width" /></td>
      <td><input type="text" name="pdflayout_product_order" /></td>
  </tr>
  <tr>
      <td><input type="checkbox" name="pdflayout_price_ex_use" /></td>
      <td><input type="text" name="pdflayout_price_ex_field" /></td>
      <td><input type="text" name="pdflayout_price_ex_title" /></td>
      <td><input type="text" name="pdflayout_price_ex_width" /></td>
      <td><input type="text" name="pdflayout_price_ex_order" /></td>
  </tr>
  <tr>
      <td><input type="checkbox" name="pdflayout_total_ex_use" /></td>
      <td><input type="text" name="pdflayout_total_ex_field" /></td>
      <td><input type="text" name="pdflayout_total_ex_title" /></td>
      <td><input type="text" name="pdflayout_total_ex_width" /></td>
      <td><input type="text" name="pdflayout_total_ex_order" /></td>
  </tr>
  <tr>
      <td><input type="checkbox" name="pdflayout_tax_use" /></td>
      <td><input type="text" name="pdflayout_tax_field" /></td>
      <td><input type="text" name="pdflayout_tax_title" /></td>
      <td><input type="text" name="pdflayout_tax_width" /></td>
      <td><input type="text" name="pdflayout_tax_order" /></td>
  </tr>
  <tr>
      <td><input type="checkbox" name="pdflayout_price_inc_use" /></td>
      <td><input type="text" name="pdflayout_price_inc_field" /></td>
      <td><input type="text" name="pdflayout_price_inc_title" /></td>
      <td><input type="text" name="pdflayout_price_inc_width" /></td>
      <td><input type="text" name="pdflayout_price_inc_order" /></td>
  </tr>
  <tr>
      <td><input type="checkbox" name="pdflayout_total_inc_use" /></td>
      <td><input type="text" name="pdflayout_total_inc_field" /></td>
      <td><input type="text" name="pdflayout_total_inc_title" /></td>
      <td><input type="text" name="pdflayout_total_inc_width" /></td>
      <td><input type="text" name="pdflayout_total_inc_order" /></td>
  </tr>
</table>
</form>

