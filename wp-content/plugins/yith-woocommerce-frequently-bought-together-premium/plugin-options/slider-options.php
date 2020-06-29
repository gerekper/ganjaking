<?php
/**
 * SLIDER ARRAY OPTIONS
 */

$slider = array(

	'slider' => array(

		array(
			'title' => __( 'Slider Options', 'yith-woocommerce-frequently-bought-together' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wfbt-slider-options',
		),

		array(
			'id'        => 'yith-wfbt-slider-title',
			'title'     => __( 'Slider Title', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'Give a title to the slider with products', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Customers who bought the items in your wishlist also purchased', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-slider-elems',
			'title'     => __( 'Number of products', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'Choose the maximum number of products displayed at a time when the browser is used with its widest width.', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'default'   => 4,
			'min'       => '1',
		),

		array(
			'id'        => 'yith-wfbt-slider-buy-button',
			'title'     => __( '"Add to cart" button label', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'Choose the label for "Add to cart" button.', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Add to cart', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-slider-wishlist-button',
			'title'     => __( '"Wishlist" button', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'Choose the label for "Wishlist" button.', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Add to Wishlist', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'            => 'yith-wfbt-slider-product-image',
			'title'         => __( 'Select product content', 'yith-woocommerce-frequently-bought-together' ),
			'desc'          => __( 'Product Image', 'yith-woocommerce-frequently-bought-together' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => 'start',
		),

		array(
			'id'            => 'yith-wfbt-slider-product-title',
			'desc'          => __( 'Product Name', 'yith-woocommerce-frequently-bought-together' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => '',
		),

		array(
			'id'            => 'yith-wfbt-slider-product-price',
			'desc'          => __( 'Product Price', 'yith-woocommerce-frequently-bought-together' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => '',
		),

		array(
			'id'            => 'yith-wfbt-slider-product-variation',
			'desc'          => __( 'Product Variation', 'yith-woocommerce-frequently-bought-together' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => '',
		),

		array(
			'id'            => 'yith-wfbt-slider-product-rating',
			'desc'          => __( 'Product Rating', 'yith-woocommerce-frequently-bought-together' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => 'end',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wfbt-slider-options',
		),
	),
);

return apply_filters( 'yith_wcfbt_panel_slider_options', $slider );