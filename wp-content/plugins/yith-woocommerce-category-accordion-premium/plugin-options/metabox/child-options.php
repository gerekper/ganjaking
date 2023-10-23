<?php
/**
 * The title parent category options style metabox configuration
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$child_category_options_tab = array(

	'child_options' => array(
		'label'  => __( 'Child category options', 'yith-woocommerce-category-accordion' ),
		'fields' => array(
			'ywcacc_child_color' => array(
				'label'        => __( 'Color', 'yith-woocommerce-category-accordion' ),
				'type'         => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'id'      => 'child_text_color',
						'name'    => __( 'DEFAULT', 'yith-woocommerce-category-accordion' ),
						'default' => '#000',
					),
					array(
						'id'      => 'child_hover_color',
						'name'    => __( 'HOVER', 'yith-woocommerce-category-accordion' ),
						'default' => '#000',
					),
				),
				'desc'         => __( 'Set the text colors', 'yith-woocommerce-category-accordion' ),
			),

			'ywcacc_child_font_weight' => array(
				'label'   => __( 'Font weight', 'yith-woocommerce-category-accordion' ),
				'type'    => 'select',
				'options' => array(
					'regular'     => __( 'Regular', 'yith-woocommerce-category-accordion' ),
					'bold'        => __( 'Bold', 'yith-woocommerce-category-accordion' ),
					'extra-bold'  => __( 'Extra bold', 'yith-woocommerce-category-accordion' ),
					'italic'      => __( 'Italic', 'yith-woocommerce-category-accordion' ),
					'bold-italic' => __( 'Italic bold', 'yith-woocommerce-category-accordion' ),
				),
				'std'     => 'bold_font',
				'desc'    => __( 'Set the font weight', 'yith-woocommerce-category-accordion' ),
			),

			'ywcacc_child_font_size' => array(
				'label'  => __( 'Font size', 'yith-woocommerce-category-accordion' ),
				'type'   => 'inline-fields',
				'fields' => array(
					'font_size'      => array(
						'type' => 'number',
						'min'  => 0,
						'std'  => 20,
					),
					'type_font_size' => array(
						'type'    => 'select',
						'options' => array(
							'px'  => 'px',
							'em'  => 'em',
							'pt'  => 'pt',
							'rem' => 'rem',
						),
					),
				),
				'desc'   => __( 'Set the font size', 'yith-woocommerce-category-accordion' ),
			),

			'ywcacc_child_text_transform' => array(
				'label'   => __( 'Text transform', 'yith-woocommerce-category-accordion' ),
				'type'    => 'select',
				'options' => array(
					'none'       => __( 'None', 'yith-woocommerce-category-accordion' ),
					'lowercase'  => __( 'Lowercase', 'yith-woocommerce-category-accordion' ),
					'uppercase'  => __( 'Uppercase', 'yith-woocommerce-category-accordion' ),
					'capitalize' => __( 'Capitalize', 'yith-woocommerce-category-accordion' ),
				),
				'std'     => 'none',
				'desc'    => __( 'Set the text transform options', 'yith-woocommerce-category-accordion' ),
			),

			'ywcacc_child_border_color' => array(
				'label' => __( 'Border color', 'yith-woocommerce-category-accordion' ),
				'type'  => 'colorpicker',
				'std'   => '#000',
				'desc'  => __( 'Set the border color', 'yith-woocommerce-category-accordion' ),
			),

			'ywcacc_child_bg_color' => array(
				'label'        => __( 'Background color', 'yith-woocommerce-category-accordion' ),
				'type'         => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'id'      => 'child_default_color',
						'name'    => __( 'DEFAULT', 'yith-woocommerce-category-accordion' ),
						'default' => 'rgba(255,255,255,0)',
					),
					array(
						'id'      => 'child_hover_color',
						'name'    => __( 'HOVER', 'yith-woocommerce-category-accordion' ),
						'default' => 'rgba(255,255,255,0)',
					),
				),
				'desc'         => __( 'Set the background colors', 'yith-woocommerce-category-accordion' ),
			),
			'save-button' => array(
				'type'   => 'custom',
				'action' => 'ywcca_add_save_button',
				),
		),
	),



);

return $child_category_options_tab;
