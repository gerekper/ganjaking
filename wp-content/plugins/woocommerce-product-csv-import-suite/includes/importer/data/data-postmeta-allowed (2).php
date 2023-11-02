<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// New postmeta allowed
return array(
	'downloadable' 	=> array( 'yes', 'no' ),
	'virtual' 		=> array( 'yes', 'no' ),
	'visibility'	=> function_exists( 'wc_get_product_visibility_options' ) ? array_keys( wc_get_product_visibility_options() ) : array( 'visible', 'catalog', 'search', 'hidden' ),
	'stock_status'	=> function_exists( 'wc_get_product_stock_status_options' ) ? array_keys( wc_get_product_stock_status_options() ) : array( 'instock', 'outofstock', 'onbackorder' ),
	'backorders'	=> function_exists( 'wc_get_product_backorder_options' ) ? array_keys( wc_get_product_backorder_options() ) : array( 'yes', 'no', 'notify' ),
	'manage_stock'	=> array( 'yes', 'no' ),
	'tax_status'	=> array( 'taxable', 'shipping', 'none' ),
	'featured'		=> array( 'yes', 'no' ),
);