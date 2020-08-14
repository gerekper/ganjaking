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
	'related' => array(

		array(
			'title' => __( 'Related Options', 'yith-woocommerce-compare' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-woocompare-related-options',
		),

		array(
			'title'     => __( 'Related Products', 'yith-woocommerce-compare' ),
			'desc'      => __( 'Choose if you want to enable related products in the comparison', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-woocompare-show-related',
		),

		array(
			'title'             => __( 'Show related products in', 'yith-woocommerce-compare' ),
			'desc'              => __( 'Popup', 'yith-woocommerce-compare' ),
			'id'                => 'yith-woocompare-related-in-popup',
			'default'           => 'yes',
			'type'              => 'checkbox',
			'checkboxgroup'     => 'start',
			'custom_attributes' => array(
				'data-deps' => 'yith-woocompare-show-related',
			),
		),

		array(
			'id'            => 'yith-woocompare-related-in-page',
			'desc'          => __( 'Page', 'yith-woocommerce-compare' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => 'end',
		),

		array(
			'title'     => __( 'Visible Number', 'yith-woocommerce-compare' ),
			'desc'      => __( 'Choose number of related products you want to see on the screen.', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'default'   => '4',
			'min'       => '1',
			'id'        => 'yith-woocompare-related-visible-num',
			'deps'      => array(
				'id'    => 'yith-woocompare-show-related',
				'value' => 'yes',
				'type'  => 'hide',
			),

		),

		array(
			'title'     => __( 'Autoplay', 'yith-woocommerce-compare' ),
			'desc'      => __( 'Enable slider autoplay', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'yith-woocompare-related-autoplay',
			'deps'      => array(
				'id'    => 'yith-woocompare-show-related',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'title'     => __( 'Slider Navigation', 'yith-woocommerce-compare' ),
			'desc'      => __( 'Show prev/next navigation in the slider', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'id'        => 'yith-woocompare-related-navigation',
			'deps'      => array(
				'id'    => 'yith-woocompare-show-related',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-woocompare-related-options-end',
		),
	),
);

return apply_filters( 'yith_woocompare_related_settings', $options );
