<?php
/**
 * The general style metabox configuration
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$fields = array(

	'ywcacc_container_colors' => array(
		'label'        => __( 'Container colors', 'yith-woocommerce-category-accordion' ),
		'type'         => 'multi-colorpicker',
		'colorpickers' => array(
			array(
				'id'      => 'container_bg',
				'name'    => __( 'BACKGROUND', 'yith-woocommerce-category-accordion' ),
				'default' => 'rgb(245, 245, 245)',
			),
			array(
				'id'      => 'container_border',
				'name'    => __( 'BORDER', 'yith-woocommerce-category-accordion' ),
				'default' => 'rgb(207, 207, 207)',
			),
		),
	),

	'ywcacc_border_radius' => array(
		'label' => __( 'Border radius', 'yith-woocommerce-category-accordion' ),
		'type'  => 'dimensions',
		'units' => array(
			'px' => 'px',
		),
		'desc'  => __( 'Set the container border radius', 'yith-woocommerce-category-accordion' ),
	),

	'ywcacc_count_style' => array(
		'label'   => __( 'Count style', 'yith-woocommerce-category-accordion' ),
		'type'    => 'select',
		'options' => array(
			'simple_style' => __('Simple', 'yith-woocommerce-category-accordion'),
			'circle_style' => __('Circle', 'yith-woocommerce-category-accordion'),
			'square_style' => __('Square', 'yith-woocommerce-category-accordion'),
		),
		'std'     => 'circle_style',
		'desc'    => __( 'Set the count style', 'yith-woocommerce-category-accordion' ),
	),

	'ywcacc_count_colors' => array(
		'label'        => __( 'Count colors', 'yith-woocommerce-category-accordion' ),
		'type'         => 'multi-colorpicker',
		'colorpickers' => array(
			array(
				'id'      => 'text_color',
				'name'    => __( 'TEXT', 'yith-woocommerce-category-accordion' ),
				'default' => 'rgb(204, 204, 204)',
			),
			array(
				'id'      => 'border_color',
				'name'    => __( 'BORDER', 'yith-woocommerce-category-accordion' ),
				'default' => 'rgb(204, 204, 204)',
			),
			array(
				'id'      => 'background_color',
				'name'    => __( 'BACKGROUND', 'yith-woocommerce-category-accordion' ),
				'default' => 'rgb(245, 245, 245)',
			),
		),
	),

	'ywcacc_toggle_icon_style' => array(
		'label'   => __( 'Toggle icon style', 'yith-woocommerce-category-accordion' ),
		'type'    => 'select',
		'options' => array(
			'simple_style' => __('Simple', 'yith-woocommerce-category-accordion'),
			'circle_style' => __('Circle', 'yith-woocommerce-category-accordion'),
			'square_style' => __('Square', 'yith-woocommerce-category-accordion'),
		),
		'std'     => 'circle_style',
		'desc'    => __( 'Set the toggle icon style', 'yith-woocommerce-category-accordion' ),
	),

	'ywcacc_toggle_icon' => array(
		'label'   => __( 'Toggle icon', 'yith-woocommerce-category-accordion' ),
		'type'    => 'select',
		'options' => array(
			'plus_icon'  => __('Plus icon','yith-woocommerce-category-accordion' ),
			'arrow_icon' => __('Arrow icon','yith-woocommerce-category-accordion' ),
		),
		'std'     => 'plus_icon',
		'desc'    => __( 'Set the toggle icon type', 'yith-woocommerce-category-accordion' ),
	),

	'ywcacc_toggle_colors'        => array(
		'label'        => __( 'Toggle icon colors', 'yith-woocommerce-category-accordion' ),
		'type'         => 'multi-colorpicker',
		'colorpickers' => array(

			array(
				'id'      => 'icon_color',
				'name'    => __( 'ICON', 'yith-woocommerce-category-accordion' ),
				'default' => 'rgb(144, 144, 144)',
			),
			array(
				'id'      => 'border_color',
				'class'   => 'yith-plugin-fw-colorpicker color-picker count_style_deps',
				'name'    => __( 'BORDER', 'yith-woocommerce-category-accordion' ),
				'default' => 'rgb(230, 230, 230)',
			),
			array(
				'id'      => 'background_color',
				'class'   => 'yith-plugin-fw-colorpicker color-picker count_style_deps',
				'name'    => __( 'BACKGROUND', 'yith-woocommerce-category-accordion' ),
				'default' => 'rgb(255, 255, 255)',
			),
			array(
				'id'      => 'icon_hover_color',
				'name'    => __( 'ICON HOVER', 'yith-woocommerce-category-accordion' ),
				'default' => 'rgb(144, 144, 144)',
			),
			array(
				'id'      => 'border_hover_color',
				'class'   => 'yith-plugin-fw-colorpicker color-picker count_style_deps',
				'name'    => __( 'BORDER HOVER', 'yith-woocommerce-category-accordion' ),
				'default' => 'rgb(230, 230, 230)',
			),
			array(
				'id'      => 'background_hover_color',
				'class'   => 'yith-plugin-fw-colorpicker color-picker count_style_deps',
				'name'    => __( 'BACKGROUND HOVER', 'yith-woocommerce-category-accordion' ),
				'default' => 'rgb(255, 255, 255)',
			),
		),
	),
	'ywcacc_toggle_icon_position' => array(
		'label'   => __( 'Toggle icon position', 'yith-woocommerce-category-accordion' ),
		'type'    => 'select',
		'options' => array(
			'left'  => __('Left','yith-woocommerce-category-accordion' ),
			'right' => __('Right','yith-woocommerce-category-accordion' ),
		),
		'std'     => 'left',
		'desc'    => __( 'Set the toggle icon position', 'yith-woocommerce-category-accordion' ),
	),

	'save-button' => array(
		'type'   => 'custom',
		'action' => 'ywcca_add_save_button',
	),
);

$metabox = array(

	'label'    => 'Accordion Settings',
	'pages'    => 'yith_cacc',
	'context'  => 'normal',
	'priority' => 'default',
	'class'    => yith_set_wrapper_class(),
	'tabs'     => array(
		'settings' => array(
			'label'  => __('General style', 'yith-woocommerce-category-accordion' ),
			'fields' => $fields,
		),
	),
);

return $metabox;
