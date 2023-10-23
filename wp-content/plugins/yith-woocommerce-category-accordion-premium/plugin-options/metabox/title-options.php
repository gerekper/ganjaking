<?php
/**
 * The title options style metabox configuration
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
$title_options_tab = array(

	'title_options' => array(
		'label'  => __( 'Title options', 'yith-woocommerce-category-accordion' ),
		'fields' => array(
			'ywcacc_color_title' => array(
				'label' => __( 'Color', 'yith-woocommerce-category-accordion' ),
				'type'  => 'colorpicker',
				'std'   => '#000',
				'desc'  => __( 'Set the border color of the title', 'yith-woocommerce-category-accordion' ),
			),

			'ywcacc_font_weight' => array(
				'label'   => __( 'Font weight', 'yith-woocommerce-category-accordion' ),
				'type'    => 'select',
				'options' => array(
					'regular'     => __( 'Regular', 'yith-woocommerce-category-accordion' ),
					'bold'        => __( 'Bold', 'yith-woocommerce-category-accordion' ),
					'extra-bold'  => __( 'Extra bold', 'yith-woocommerce-category-accordion' ),
					'italic'      => __( 'Italic', 'yith-woocommerce-category-accordion' ),
					'bold-italic' => __( 'Italic bold', 'yith-woocommerce-category-accordion' ),
				),
				'std'     => 'bold',
				'desc'    => __( 'Set the font weight', 'yith-woocommerce-category-accordion' ),
			),

			'ywcacc_font_size' => array(
				'label'  => __( 'Title font size', 'yith-woocommerce-category-accordion' ),
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

			'ywcacc_alignment' => array(
				'label'   => __( 'Alignment', 'yith-woocommerce-category-accordion' ),
				'type'    => 'select',
				'options' => array(
					'center' => __( 'Center', 'yith-woocommerce-category-accordion' ),
					'left'   => __( 'Left', 'yith-woocommerce-category-accordion' ),
					'right'  => __( 'Right', 'yith-woocommerce-category-accordion' ),
				),
				'std'     => 'center',
				'desc'    => __( 'Set the title alignment', 'yith-woocommerce-category-accordion' ),
			),

			'ywcacc_text_transform' => array(
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

			'ywcacc_border_style' => array(
				'label'   => __( 'Separator style', 'yith-woocommerce-category-accordion' ),
				'type'    => 'select',
				'options' => array(
					'no_border'    => __( 'No border', 'yith-woocommerce-category-accordion' ),
					'single_line'  => __( 'Single line', 'yith-woocommerce-category-accordion' ),
					'thick_line'   => __( 'Thick line', 'yith-woocommerce-category-accordion' ),
					'double_lines' => __( 'Double lines', 'yith-woocommerce-category-accordion' ),
				),
				'std'     => 'no_border',
				'desc'    => __( 'Set the border style below the title', 'yith-woocommerce-category-accordion' ),
			),

			'ywcacc_border_color' => array(
				'label' => __( 'Border color', 'yith-woocommerce-category-accordion' ),
				'type'  => 'colorpicker',
				'std'   => '#000',
				'desc'  => __( 'Set the border color', 'yith-woocommerce-category-accordion' ),
			),

			'save-button' => array(
				'type'   => 'custom',
				'action' => 'ywcca_add_save_button',
			),
		),
	),


);

return $title_options_tab;
