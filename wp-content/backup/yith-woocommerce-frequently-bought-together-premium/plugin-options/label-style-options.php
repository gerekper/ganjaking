<?php
/**
 * LABEL STYLE ARRAY OPTIONS
 */

$label_style = array(

	'label-style' => array(

		array(
			'title' => __( 'Label and Style Options', 'yith-woocommerce-frequently-bought-together' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcfbt-label-style-options',
		),

		array(
			'id'        => 'yith-wfbt-total-single-label',
			'title'     => __( 'Total label for single product', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'This is the text shown for total price label when only one product has been checked.', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Price', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-total-double-label',
			'title'     => __( 'Total label for double products', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'This is the text shown for total price label when two products have been checked.', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Price for both', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-total-three-label',
			'title'     => __( 'Total label for three products', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'This is the text shown for total price label when three products have been checked.', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Price for all three', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-total-multi-label',
			'title'     => __( 'Total label for multiple products', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'This is the label shown for total price label when more than three products have been checked.', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Price for all', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-button-single-label',
			'title'     => __( 'Button label for single product', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'This is the label shown for "Add to cart" button when only one product has been checked.', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Add to Cart', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-button-double-label',
			'title'     => __( 'Button label for two products', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'This is the label shown for "Add to cart" button when two products have been checked.', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Add both to Cart', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-button-three-label',
			'title'     => __( 'Button label for three products', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'This is the label shown for "Add to cart" button when three products have been checked.', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Add all three to Cart', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-button-multi-label',
			'title'     => __( 'Button label for multiple products', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'This is the label shown for "Add to cart" button when more than two products have been checked.', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Add all to Cart', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-open-popup-button-label',
			'title'     => __( 'Variation pick button label', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'This is the text of the button that opens the popup to select the product variation', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'View options >', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-popup-title',
			'title'     => __( 'Popup title', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'This is the title of the popup where users can select the product variation', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Choose the product', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-popup-submit-button-label',
			'title'     => __( 'Popup button label', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'This is the text of the "Add to cart" button in the popup', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Add product', 'yith-woocommerce-frequently-bought-together' ),
		),

		array(
			'id'        => 'yith-wfbt-form-background-color',
			'title'     => __( 'Form Background Color', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'Select background color for Frequently Bought form', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#ffffff',
		),

		array(
			'id'        => 'yith-wfbt-button-color',
			'title'     => __( 'Button Color', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'Select button background color', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wfbt_get_proteo_default( 'yith-wfbt-button-color', '#222222', true ),
		),

		array(
			'id'        => 'yith-wfbt-button-color-hover',
			'name'      => __( 'Button Hover Color', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'Select button background hover color', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wfbt_get_proteo_default( 'yith-wfbt-button-color-hover', '#222222', true ),
		),

		array(
			'id'        => 'yith-wfbt-button-text-color',
			'title'     => __( 'Button Text Color', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'Select button text color', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wfbt_get_proteo_default( 'yith-wfbt-button-text-color', '#ffffff', true ),
		),

		array(
			'id'        => 'yith-wfbt-button-text-color-hover',
			'title'     => __( 'Button Text Hover Color', 'yith-woocommerce-frequently-bought-together' ),
			'desc'      => __( 'Select button text hover color', 'yith-woocommerce-frequently-bought-together' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_wfbt_get_proteo_default( 'yith-wfbt-button-text-color-hover', '#ffffff', true ),
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wcfbt-label-style-options',
		),
	),
);

return apply_filters( 'yith_wcfbt_panel_label_style_options', $label_style );