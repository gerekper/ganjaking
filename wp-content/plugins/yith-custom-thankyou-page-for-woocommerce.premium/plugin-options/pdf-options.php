<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 **/

if ( ! defined( 'YITH_CTPW_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}


return array(

	'pdf' => apply_filters(
		'yith_ctpw_pdf_options',
		array(
			// general.
			'ctpw_pdf_options_start'            => array(
				'type' => 'sectionstart',
			),

			'ctpw_pdf_options_title'            => array(
				'title' => esc_html_x( 'PDF Settings ', 'Panel: page title', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => '',
			),

			'ctpw_enable_pdf'                   => array(
				'title'     => esc_html_x( 'Enable PDF button', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html_x( 'Check this option to show the PDF button on Custom Thank You Page automatically', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_enable_pdf',
				'default'   => 'no',
			),

			'ctpw_pdf_button_type'              => array(
				'title'     => esc_html_x( 'Use as Shortcode', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html_x( 'If this option is checked the button will show only when you add the shortcode to the page [yith_ctpw_pdf_button]', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_enable_pdf_as_shortcode',
				'default'   => 'no',
				'deps'      => array(
					'id'    => 'yith_ctpw_enable_pdf',
					'value' => 'no',
					'type'  => 'disable',
				),
			),

			'ctpw_button_label'                 => array(
				'title'     => esc_html_x( 'PDF Button Label', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'text',
				'desc'      => esc_html_x( 'set the PDF button label', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_pdf_button_label',
				'default'   => esc_html__( 'Save as PDF', 'yith-custom-thankyou-page-for-woocommerce' ),
			),

			'ctpw_button_back_color'            => array(
				'title'        => esc_html_x( 'PDF Button Background Color', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'         => 'yith-field',
				'yith-type'    => 'multi-colorpicker',
				'id'           => 'yith_ctpw_pdf_button_colors',
				'colorpickers' => array(
					array(
						'name'    => esc_html_x( 'Default Color', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
						'id'      => 'normal',
						'default' => '#000000',
					),
					array(
						'name'    => esc_html_x( 'Hover Color', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
						'id'      => 'hover',
						'default' => '#666666',
					),
				),
			),

			'ctpw_button_text_color'            => array(
				'title'        => esc_html_x( 'PDF Button Text Colors', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'         => 'yith-field',
				'yith-type'    => 'multi-colorpicker',
				'id'           => 'yith_ctpw_pdf_button_text_colors',
				'colorpickers' => array(
					array(
						'name'    => esc_html_x( 'Default Color', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
						'id'      => 'normal',
						'default' => '#ffffff',
					),
					array(
						'name'    => esc_html_x( 'Hover Color', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
						'id'      => 'hover',
						'default' => '#ffffff',
					),
				),
			),


			'ctpw_pdf_show_logo'                => array(
				'title'     => esc_html_x( 'Show Logo on PDF', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html_x( 'Check this option to show a custom logo image on PDF', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_pdf_show_logo',
				'default'   => 'no',
			),

			'ctpw_pdf_custom_logo'              => array(
				'title'     => esc_html_x( 'Logo', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'upload',
				'desc'      => esc_html_x( 'Upload or select a logo image (available extensions: jpg, png)', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_pdf_custom_logo',
				'default'   => '',
				'deps'      => array(
					'id'    => 'yith_ctpw_pdf_show_logo',
					'value' => 'yes',
					'type'  => 'hide',
				),
			),

			'ctpw_pdf_custom_logo_max_width'    => array(
				'title'     => esc_html_x( 'Max Logo Width', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'number',
				'desc'      => esc_html_x( 'set the max size for the logo image in px', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_pdf_custom_logo_max_size',
				'default'   => '200',
				'deps'      => array(
					'id'    => 'yith_ctpw_pdf_show_logo',
					'value' => 'yes',
					'type'  => 'hide',
				),
			),

			'ctpw_pdf_show_order_header'        => array(
				'title'     => esc_html_x( 'Show Order Header Table', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html_x( 'Show Order Header Table', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_pdf_show_order_header',
				'default'   => 'yes',
			),

			'ctpw_pdf_show_order_details_table' => array(
				'title'     => esc_html_x( 'Show Order Details Table', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html_x( 'Show Order Details Table', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_pdf_show_order_details_table',
				'default'   => 'yes',
			),

			'ctpw_pdf_show_customer_details'    => array(
				'title'     => esc_html_x( 'Show Customer Details', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html_x( 'Show Customer Informations', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_pdf_show_customer_details',
				'default'   => 'no',
			),

			'ctpw_pdf_footer'                   => array(
				'title'     => esc_html_x( 'Footer Text', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'textarea',
				'desc'      => esc_html_x( 'Add custom message to PDF footer', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_pdf_footer_text',
			),

			'ctpw_pdf_preview_button'           => array(
				'title'     => '',
				'type'      => 'yith-field',
				'yith-type' => 'buttons',
				'buttons'   => array(
					array(
						'name'  => esc_html__( 'Preview PDF', 'yith-custom-thankyou-page-for-woocommerce' ),
						'class' => 'yctpw_pdf_preview_backend',
						'data'  => array(
							'action' => 'yctpw_pdf_preview_backend',
						),
					),
				),
			),

			'ctpw_pdf_options_end'              => array(
				'type' => 'sectionend',
			),

		)
	),
);
