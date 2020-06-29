<?php
/**
 * STYLE OPTIONS ARRAY
 *
 * @author  Your Inspiration Themes
 * @package YITH Woocommerce Compare Premium
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WOOCOMPARE' ) ) {
	exit;
} // Exit if accessed directly

$options = array(
	'style' => array(

		array(
			'name' => __( 'Category Filter Options', 'yith-woocommerce-compare' ),
			'type' => 'title',
			'desc' => '',
			'id'   => 'yith-woocompare-filter-style-end',
		),

		array(
			'title'     => __( 'Filter title', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The title for the category filter section', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Category Filter', 'yith-woocommerce-compare' ),
			'id'        => 'yith-woocompare-categories-filter-title',
		),

		array(
			'title'     => __( 'Filter title color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the title of the category filter section', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_woocompare_get_proteo_default( 'yith-woocompare-categories-filter-title-color', '#333333', true ),
			'id'        => 'yith-woocompare-categories-filter-title-color',
		),

		array(
			'title'     => __( 'Filter link color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the link of the category filter', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#777777',
			'id'        => 'yith-woocompare-categories-filter-link-color',
		),

		array(
			'title'     => __( 'Filter link hover color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the link of the category filter on mouse hover', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#333333',
			'id'        => 'yith-woocompare-categories-filter-link-hover-color',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-woocompare-filter-end',
		),

		array(
			'name' => __( 'Table options', 'yith-woocommerce-compare' ),
			'type' => 'title',
			'desc' => '',
			'id'   => 'yith-woocompare-table-style',
		),

		array(
			'title'     => __( 'Remove color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the "Remove Product" link', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#777777',
			'id'        => 'yith-woocompare-table-remove-color',
		),

		array(
			'title'     => __( 'Remove hover color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the "Remove Product" link on mouse hover', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#333333',
			'id'        => 'yith-woocompare-table-remove-color-hover',
		),

		array(
			'title'     => __( 'Button text color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the text of the button', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_woocompare_get_proteo_default( 'yith-woocompare-table-button-text-color', '#ffffff', true ),
			'id'        => 'yith-woocompare-table-button-text-color',
		),

		array(
			'title'     => __( 'Button text hover color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the text of the button on mouse hover', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_woocompare_get_proteo_default( 'yith-woocompare-table-button-text-color-hover', '#ffffff', true ),
			'id'        => 'yith-woocompare-table-button-text-color-hover',
		),

		array(
			'title'     => __( 'Button color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the button background', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_woocompare_get_proteo_default( 'yith-woocompare-table-button-color', '#b2b2b2', true ),
			'id'        => 'yith-woocompare-table-button-color',
		),

		array(
			'title'     => __( 'Button hover color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the button background on mouse hover', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_woocompare_get_proteo_default( 'yith-woocompare-table-button-color-hover', '#303030', true ),
			'id'        => 'yith-woocompare-table-button-color-hover',
		),

		array(
			'title'     => __( 'Star color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color of the star', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#303030',
			'id'        => 'yith-woocompare-table-star',
		),

		array(
			'title'     => __( 'Highlighted row color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The background color of highlighted row', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#e4e4e4',
			'id'        => 'yith-woocompare-highlights-color',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-woocompare-table-style-end',
		),

		array(
			'name' => __( 'Share Options', 'yith-woocommerce-compare' ),
			'type' => 'title',
			'desc' => '',
			'id'   => 'yith-woocompare-share-style',
		),

		array(
			'title'     => __( 'Share Title', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The title for the share section', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Share on', 'yith-woocommerce-compare' ),
			'id'        => 'yith-woocompare-share-title',
		),

		array(
			'title'     => __( 'Share Title Color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the title of the share section', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_woocompare_get_proteo_default( 'yith-woocompare-share-title-color', '#333333', true ),
			'id'        => 'yith-woocompare-share-title-color',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-woocompare-share-style-end',
		),

		array(
			'name' => __( 'Related Options', 'yith-woocommerce-compare' ),
			'type' => 'title',
			'desc' => '',
			'id'   => 'yith-woocompare-related-style',
		),

		array(
			'title'     => __( 'Related Title', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The title for the related section', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'Related Products', 'yith-woocommerce-compare' ),
			'id'        => 'yith-woocompare-related-title',
		),

		array(
			'title'     => __( 'Related Title Color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the title of the related section', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_woocompare_get_proteo_default( 'yith-woocompare-related-title-color', '#333333', true ),
			'id'        => 'yith-woocompare-related-title-color',
		),

		array(
			'title'     => __( 'Button text color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the text of the button', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_woocompare_get_proteo_default( 'yith-woocompare-related-button-text-color', '#ffffff' ),
			'id'        => 'yith-woocompare-related-button-text-color',
		),

		array(
			'title'     => __( 'Button text hover color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the text of the button on mouse hover', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_woocompare_get_proteo_default( 'yith-woocompare-related-button-text-color-hover', '#ffffff', true ),
			'id'        => 'yith-woocompare-related-button-text-color-hover',
		),

		array(
			'title'     => __( 'Button color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the button background', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_woocompare_get_proteo_default( 'yith-woocompare-related-button-color', '#b2b2b2', true ),
			'id'        => 'yith-woocompare-related-button-color',
		),

		array(
			'title'     => __( 'Button hover color', 'yith-woocommerce-compare' ),
			'desc'      => __( 'The color for the button background on mouse hover', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => yith_woocompare_get_proteo_default( 'yith-woocompare-related-button-color-hover', '#303030', true ),
			'id'        => 'yith-woocompare-related-button-color-hover',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-woocompare-related-style-end',
		),
	),
);

return apply_filters( 'yith_woocompare_style_settings', $options );
