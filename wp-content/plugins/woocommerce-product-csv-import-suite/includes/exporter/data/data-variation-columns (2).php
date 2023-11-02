<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters( 'woocommerce_csv_product_variation_post_columns', array(
	'post_parent'            => 'post_parent',
	'ID'                     => 'ID',
	'post_status'            => 'post_status',
	'menu_order'             => 'menu_order',

	// Core product data
	'_sku'                   => 'sku',
	'_downloadable'          => 'downloadable',
	'_virtual'               => 'virtual',
	'_stock'                 => 'stock',
	'_stock_status'          => 'stock_status',
	'_regular_price'         => 'regular_price',
	'_sale_price'            => 'sale_price',
	'_weight'                => 'weight',
	'_length'                => 'length',
	'_width'                 => 'width',
	'_height'                => 'height',
	'_tax_class'             => 'tax_class',
	'_variation_description' => 'variation_description',

	// Downloadable products
	'_file_path'             => 'file_path',
	'_file_paths'            => 'file_paths',
	'_download_limit'        => 'download_limit',
) );
