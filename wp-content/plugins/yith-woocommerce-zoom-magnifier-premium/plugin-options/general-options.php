<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$image_size = array(
	'name'     => esc_html__( 'Image size', 'yith-woocommerce-zoom-magnifier' ),
	'desc'     => esc_html__( 'The size of the images used within the magnifier box', 'yith-woocommerce-zoom-magnifier' ),
	'id'       => 'woocommerce_magnifier_image',
	'css'      => '',
	'type'     => 'yith_ywzm_image_width',
	'default'  => array(
		'width'  => 600,
		'height' => 600,
		'crop'   => true
	),
	'std'      => array(
		'width'  => 600,
		'height' => 600,
		'crop'   => true
	),
	'desc_tip' => true
);

$general_settings = array(
	array(
		'name' => esc_html__( 'General Settings', 'yith-woocommerce-zoom-magnifier' ),
		'type' => 'title',
		'desc' => '',
		'id'   => 'yith_wcmg_general'
	),
	array(
		'name'    => esc_html__( 'Activate YITH WooCommerce Zoom Magnifier', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'Activate the plugin or use the WooCommerce default product image.', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'yith_wcmg_enable_plugin',
		'std'     => 'yes',
		'default' => 'yes',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
	),
	array(
		'name'    => esc_html__( 'Activate on mobile device', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'Set if zoom and slider functionalities should be shown also on mobile devices.', 'yith-woocommerce-zoom-magnifier' ),

		'id'      => 'yith_wcmg_enable_mobile',
		'std'     => 'yes',
		'default' => 'yes',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
	),
	array(
		'name'    => esc_html__( 'Forced Image Size', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'If disabled, you will able to customize the sizes of the zoomed images. Disable it at your own risk; the magnifier could not properly work with images out of proportion.', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'yith_wcmg_force_sizes',
		'std'     => 'yes',
		'default' => 'yes',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
	),
	array(
		'type' => 'sectionend',
		'id'   => 'yith_wcmg_general_end'
	)
);

$magnifier_settings = array(
	array(
		'name' => esc_html__( 'Magnifier Settings', 'yith-woocommerce-zoom-magnifier' ),
		'type' => 'title',
		'desc' => '',
		'id'   => 'yith_wcmg_magnifier'
	),
	'zoom_box_width'    => array(
		'name'    => esc_html__( 'Zoom Box Width', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'The width of the magnifier box (default: auto)', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'yith_wcmg_zoom_width',
		'std'     => 'auto',
		'default' => 'auto',
		'type'    => 'text',
	),
	array(
		'name'    => esc_html__( 'Zoom Box Height', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'The height of the magnifier box (default: auto)', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'yith_wcmg_zoom_height',
		'std'     => 'auto',
		'default' => 'auto',
		'type'    => 'text',
	),
	$image_size,
	'zoom_box_position' => array(
		'name'    => esc_html__( 'Zoom Box Position', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'The magnifier position', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'yith_wcmg_zoom_position',
		'std'     => 'right',
		'default' => 'right',
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'options' => array(
			'right'  => esc_html__( 'Right', 'yith-woocommerce-zoom-magnifier' ),
			'inside' => esc_html__( 'Inside', 'yith-woocommerce-zoom-magnifier' )
		)
	),
	array(
		'name'    => esc_html__( 'Zoom Box Position for mobile devices', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'The magnifier position for the mobile devices (iPhone, Android, etc.)', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'yith_wcmg_zoom_mobile_position',
		'std'     => 'default',
		'default' => 'inside',
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'options' => array(
			'default' => esc_html__( 'Default', 'yith-woocommerce-zoom-magnifier' ),
			'inside'  => esc_html__( 'Inside', 'yith-woocommerce-zoom-magnifier' ),
			'disable' => esc_html__( 'Disable', 'yith-woocommerce-zoom-magnifier' )
		)
	),
	array(
		'name'    => esc_html__( 'Loading label', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => '',
		'id'      => 'yith_wcmg_loading_label',
		'std'     => esc_html__( 'Loading...', 'yith-woocommerce-zoom-magnifier' ),
		'default' => esc_html__( 'Loading...', 'yith-woocommerce-zoom-magnifier' ),
		'type'    => 'text',
	),
	array(
		'name'    => esc_html__( 'Lens Opacity', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => '',
		'id'      => 'yith_wcmg_lens_opacity',
        'type'      => 'yith-field',
        'yith-type' => 'slider',
        'option'    => array( 'min' => 0, 'max' => 1 ),
		'step'    => .1
	),

	array(
		'name'    => esc_html__( 'Blur', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'Add a blur effect to the small image on mouse hover.', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'yith_wcmg_softfocus',
		'std'     => 'no',
		'default' => 'no',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
	),
	array( 'type' => 'sectionend', 'id' => 'yith_wcmg_magnifier_end' )
);

$slider_settings = array(
	array(
		'name' => esc_html__( 'Slider Settings', 'yith-woocommerce-zoom-magnifier' ),
		'type' => 'title',
		'desc' => '',
		'id'   => 'yith_wcmg_slider'
	),
	array(
		'name'    => esc_html__( 'Activate Slider', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'Activate Thumbnail Slider.', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'yith_wcmg_enableslider',
		'std'     => 'yes',
		'default' => 'yes',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
	),
	array(
		'name'    => esc_html__( 'Activate Responsive Slider', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'This option fits the thumbnails within the available space. Disable it if you want to manage freely the thumbnails (eg. add margins, paddings, etc.)', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'yith_wcmg_slider_responsive',
		'std'     => 'yes',
		'default' => 'yes',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
	),
	array(
		'name'    => esc_html__( 'Items', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'Number of items to show', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'yith_wcmg_slider_items',
        'default' => 3,
        'type'      => 'yith-field',
        'yith-type' => 'slider',
        'option'    => array( 'min' => 1, 'max' => 10 ),
        'step'    => 1
	),
	array(
		'name'    => esc_html__( 'Circular carousel', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'It defines whether the carousel should be circular.', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'yith_wcmg_slider_circular',
		'std'     => 'yes',
		'default' => 'yes',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
	),
	array(
		'name'    => esc_html__( 'Infinite carousel', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'It defines whether the carousel should be infinite. Note: It is possible to create a non-circular, infinite carousel, but it is not possible to create a circular, non-infinite carousel.', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'yith_wcmg_slider_infinite',
		'std'     => 'yes',
		'default' => 'yes',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
	),
	array(
		'name'    => esc_html__( 'Auto carousel', 'yith-woocommerce-zoom-magnifier' ),
		'desc'    => esc_html__( 'Set if the items will scroll automatically on the carousel slider.', 'yith-woocommerce-zoom-magnifier' ),
		'id'      => 'ywzm_auto_carousel',
		'std'     => 'no',
		'default' => 'no',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
	),
	array( 'type' => 'sectionend', 'id' => 'yith_wcmg_slider_end' )
);

$general_settings   = apply_filters( 'yith_ywzm_general_settings', $general_settings );
$magnifier_settings = apply_filters( 'yith_ywzm_magnifier_settings', $magnifier_settings );
$slider_settings    = apply_filters( 'yith_ywzm_slider_settings', $slider_settings );

$options['general'] = array();


$options['general'] = array_merge( $options['general'], $general_settings, $magnifier_settings, $slider_settings );

return apply_filters( 'yith_wcmg_tab_options', $options );