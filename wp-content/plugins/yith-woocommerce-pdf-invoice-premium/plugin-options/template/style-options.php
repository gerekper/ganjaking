<?php
/**
 * Template style options.
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPI_PREMIUM' ) ) {
	exit;
} // Exit if accessed directly.

$style_template_options = array(
	'template-style' => array(
		'template_selection_start'            => array(
			'name' => __( 'Document template', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
			'id'   => 'ywpi_document_template_section_start',
		),
		'template_selection_for_documents'    => array(
			'id'        => 'ywpi_document_template_selected',
			'name'      => __( 'Choose documents template', 'yith-woocommerce-pdf-invoice' ),
			'desc'      => __( 'Choose which template to use for your documents. You can customize the colors of each template.', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'select-images',
			'options'   => array(
				'default'     => array(
					'label' => __( 'Default', 'yith-woocommerce-pdf-invoice' ),
					'image' => YITH_YWPI_URL . 'assets/images/templates/default.png',
				),
				'black_white' => array(
					'label' => __( 'Black & White', 'yith-woocommerce-pdf-invoice' ),
					'image' => YITH_YWPI_URL . 'assets/images/templates/bw.png',
				),
				'modern'      => array(
					'label' => __( 'Modern Stripes', 'yith-woocommerce-pdf-invoice' ),
					'image' => YITH_YWPI_URL . 'assets/images/templates/lines.png',
				),
			),
			'default'   => 'default',
		),
		'template_selection_end'              => array(
			'type' => 'sectionend',
		),
		// Default template options.
		'template_colors_section_start'       => array(
			'name' => __( 'Document colors', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
			'id'   => 'ywpi_template_colors_section_start',
		),
		'body_color'                          => array(
			'name'      => __( 'Background color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#ffffff',
			'id'        => 'ywpi_body_color',
			'desc'      => __( 'Select the color of the template.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'default',
				'type'  => 'show',
			),
		),
		'table_header_color'                  => array(
			'name'      => __( 'Table header color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#619dda',
			'id'        => 'ywpi_table_header_color',
			'desc'      => __( 'Select the color of the table header section.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'default',
				'type'  => 'show',
			),
		),
		'table_header_font_color'             => array(
			'name'      => __( 'Table header font color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#ffffff',
			'id'        => 'ywpi_table_header_font_color',
			'desc'      => __( 'Select the color of the table header text.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'default',
				'type'  => 'show',
			),
		),
		'data_section_color'                  => array(
			'name'      => __( 'Data section color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#eef6ff',
			'id'        => 'ywpi_data_section_color',
			'desc'      => __( 'Select the color of the data section.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'default',
				'type'  => 'show',
			),
		),
		'total_section_color'                 => array(
			'name'      => __( 'Total section color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#eef6ff',
			'id'        => 'ywpi_total_section_color',
			'desc'      => __( 'Select the color of the total section.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'default',
				'type'  => 'show',
			),
		),
		// Black & White template options.
		'body_color_black_white'              => array(
			'name'      => __( 'Background color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#ffffff',
			'id'        => 'ywpi_body_color_black_white',
			'desc'      => __( 'Select the color of the template.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'black_white',
				'type'  => 'show',
			),
		),
		'table_border_color_black_white'      => array(
			'name'      => __( 'Borders color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#000000',
			'id'        => 'ywpi_borders_color_black_white',
			'desc'      => __( 'Select the borders color of the document.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'black_white',
				'type'  => 'show',
			),
		),
		'table_header_color_black_white'      => array(
			'name'      => __( 'Table header color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#000000',
			'id'        => 'ywpi_table_header_color_black_white',
			'desc'      => __( 'Select the color of the table header section.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'black_white',
				'type'  => 'show',
			),
		),
		'table_header_font_color_black_white' => array(
			'name'      => __( 'Table header font color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#ffffff',
			'id'        => 'ywpi_table_header_font_color_black_white',
			'desc'      => __( 'Select the color of the table header text.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'black_white',
				'type'  => 'show',
			),
		),
		'data_section_color_black_white'      => array(
			'name'      => __( 'Data section color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#ffffff',
			'id'        => 'ywpi_data_section_color_black_white',
			'desc'      => __( 'Select the color of the data section.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'black_white',
				'type'  => 'show',
			),
		),
		'total_section_color_black_white'     => array(
			'name'      => __( 'Total section color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#ffffff',
			'id'        => 'ywpi_total_section_color_black_white',
			'desc'      => __( 'Select the color of the total section.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'black_white',
				'type'  => 'show',
			),
		),
		// Modern Stripes template options.
		'body_color_modern'                   => array(
			'id'           => 'ywpi_body_color_modern',
			'name'         => __( 'Background colors', 'yith-woocommerce-pdf-invoice' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'id'      => 'first',
					'name'    => __( 'First color', 'yith-woocommerce-pdf-invoice' ),
					'default' => '#4D5571',
				),
				array(
					'id'      => 'second',
					'name'    => __( 'Second color', 'yith-woocommerce-pdf-invoice' ),
					'default' => '#D67B64',
				),
			),
			'desc'         => __( 'Select the color of the template.', 'yith-woocommerce-pdf-invoice' ),
			'deps'         => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'modern',
				'type'  => 'show',
			),
		),
		'table_border_color_modern'           => array(
			'name'      => __( 'Borders color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#D67B64',
			'id'        => 'ywpi_borders_color_modern',
			'desc'      => __( 'Select the borders color of the template.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'modern',
				'type'  => 'show',
			),
		),
		'invoice_number_color_modern'         => array(
			'name'      => __( 'Invoice number color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#D67B64',
			'id'        => 'ywpi_invoice_number_color_modern',
			'desc'      => __( 'Select the color of the invoice number.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'modern',
				'type'  => 'show',
			),
		),
		'table_header_color_modern'           => array(
			'name'      => __( 'Table header color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#ffffff',
			'id'        => 'ywpi_table_header_color_modern',
			'desc'      => __( 'Select the color of the table header section.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'modern',
				'type'  => 'show',
			),
		),
		'table_header_font_color_modern'      => array(
			'name'      => __( 'Table header font color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#000000',
			'id'        => 'ywpi_table_header_font_color_modern',
			'desc'      => __( 'Select the color of the table header text.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'modern',
				'type'  => 'show',
			),
		),
		'data_section_color_modern'           => array(
			'name'      => __( 'Data section color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#ffffff',
			'id'        => 'ywpi_data_section_color_modern',
			'desc'      => __( 'Select the color of the data section.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'modern',
				'type'  => 'show',
			),
		),
		'total_section_color_modern'          => array(
			'name'      => __( 'Total section color', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#ffffff',
			'id'        => 'ywpi_total_section_color_modern',
			'desc'      => __( 'Select the color of the total section.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_document_template_selected',
				'value' => 'modern',
				'type'  => 'show',
			),
		),
		'template_colors_section_end'         => array(
			'type' => 'sectionend',
		),
	),
);

return apply_filters( 'ywpi_template_style_options', $style_template_options );
