<?php
/**
 * PRODUCT DATA OPTION
 */

$product_data = array(
	array(
		'class'             => 'show_if_variable',
		'default_variation' => array(
			'name'  => 'yith_wfbt_default_variation',
			'label' => __( 'Select default variation', 'yith-woocommerce-frequently-bought-together' ),
			'type'  => 'variation_select',
		),
	),

	array(
		'products_type'  => array(
			'name'    => 'yith_wfbt_product_type',
			'label'   => __( 'Products type', 'yith-woocommerce-frequently-bought-together' ),
			'desc'    => __( 'Choose which products you want to use as frequently bought products', 'yith-woocommerce-frequently-bought-together' ),
			'type'    => 'radio',
			'options' => array(
				'related'     => __( 'Use related', 'yith-woocommerce-frequently-bought-together' ),
				'cross-sells' => __( 'Use cross-sells', 'yith-woocommerce-frequently-bought-together' ),
				'up-sells'    => __( 'Use up-sells', 'yith-woocommerce-frequently-bought-together' ),
				'custom'      => __( 'Custom products', 'yith-woocommerce-frequently-bought-together' ),
			),
		),
		'products'       => array(
			'name'  => 'yith_wfbt_ids',
			'label' => __( 'Select products', 'yith-woocommerce-frequently-bought-together' ),
			'desc'  => __( 'Select products for "Frequently bought together" group', 'yith-woocommerce-frequently-bought-together' ),
			'type'  => 'product_select',
			'data'  => array(
				'deps'  => 'yith_wfbt_product_type',
				'value' => 'custom',
			),
		),
		'num_visible'    => array(
			'name'  => 'yith_wfbt_num',
			'label' => __( 'Select number of products', 'yith-woocommerce-frequently-bought-together' ),
			'desc'  => __( 'Select the number of products to show excluding current one.', 'yith-woocommerce-frequently-bought-together' ),
			'type'  => 'number',
			'attr'  => array(
				'min' => 1,
			),
		),
		'show_unchecked' => array(
			'name'  => 'yith_wfbt_show_unchecked',
			'label' => __( 'Show products unchecked', 'yith-woocommerce-frequently-bought-together' ),
			'desc'  => __( 'Show all products in group unchecked.', 'yith-woocommerce-frequently-bought-together' ),
			'type'  => 'checkbox',
		),

		'additional_text' => array(
			'name'  => 'yith_wfbt_additional_text',
			'label' => __( 'Set additional text', 'yith-woocommerce-frequently-bought-together' ),
			'desc'  => __( 'Set additional text to show before products', 'yith-woocommerce-frequently-bought-together' ),
			'type'  => 'textarea',
		),

	),

	array(
		'discount_type'         => array(
			'name'    => 'yith_wfbt_discount_type',
			'label'   => __( 'Discount Type', 'yith-woocommerce-frequently-bought-together' ),
			'type'    => 'select',
			'options' => array(
				'fixed'      => __( 'Fixed amount', 'yith-woocommerce-frequently-bought-together' ),
				'percentage' => __( 'Percentage', 'yith-woocommerce-frequently-bought-together' ),
			),
		),
		'discount_fixed'        => array(
			'name'  => 'yith_wfbt_discount_fixed',
			'label' => __( 'Discount amount', 'yith-woocommerce-frequently-bought-together' ) . ' (' . get_woocommerce_currency_symbol() . ')',
			'desc'  => __( 'Set a fixed discount amount for this group. Leve it blank or set 0 for no discount.', 'yith-woocommerce-frequently-bought-together' ),
			'type'  => 'text',
			'class' => 'wc_input_price',
			'data'  => array(
				'deps'  => 'yith_wfbt_discount_type',
				'value' => 'fixed',
			),
		),
		'discount_percentage'   => array(
			'name'  => 'yith_wfbt_discount_percentage',
			'label' => __( 'Discount percentage (%)', 'yith-woocommerce-frequently-bought-together' ),
			'desc'  => __( 'Add a percentage discount to products group. Leave it blank or set 0 for no discount.', 'yith-woocommerce-frequently-bought-together' ),
			'type'  => 'number',
			'class' => 'wc-product-number',
			'attr'  => array(
				'min' => 0,
				'max' => 100,
			),
			'data'  => array(
				'deps'  => 'yith_wfbt_discount_type',
				'value' => 'percentage',
			),
		),
		'discount_min_spend'    => array(
			'name'  => 'yith_wfbt_discount_min_spend',
			'label' => __( 'Minimum spend', 'yith-woocommerce-frequently-bought-together' ) . ' (' . get_woocommerce_currency_symbol() . ')',
			'desc'  => __( 'Set a minimum spend referred only to this group (subtotal) allowed to use the discount.', 'yith-woocommerce-frequently-bought-together' ),
			'type'  => 'text',
			'class' => 'wc_input_price',
		),
		'discount_min_products' => array(
			'name'  => 'yith_wfbt_discount_min_products',
			'label' => __( 'Minimum product chosen', 'yith-woocommerce-frequently-bought-together' ),
			'desc'  => __( 'Set a minimum chosen products referred only to this group (subtotal) allowed to use the discount. Cannot be less then 2.', 'yith-woocommerce-frequently-bought-together' ),
			'type'  => 'number',
			'class' => 'wc-product-number',
			'attr'  => array(
				'min' => 2,
			),
		),
	),
);

return apply_filters( 'yith_wcfbt_panel_product_data_options', $product_data );