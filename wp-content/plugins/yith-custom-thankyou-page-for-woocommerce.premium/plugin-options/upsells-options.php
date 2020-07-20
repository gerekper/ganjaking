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

	'upsells' => apply_filters(
		'yith_ctpw_upsells_options',
		array(
			'ctpw_upsells_options_start'                  => array(
				'type' => 'sectionstart',
			),

			'ctpw_upsells_options_title'                  => array(
				'title' => esc_html_x( 'UpSells Settings ', 'Panel: page title', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => '',
			),

			'ctpw_upsells_enable_upsells'                 => array(
				'title'     => esc_html_x( 'Enable UpSelling section', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html_x( 'Check this option to show the up-selling section', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_enable_upsells',
				'default'   => 'yes',
			),

			'ctpw_upsells_section_title'                  => array(
				'title'     => esc_html_x( 'Section Title', 'Admin option - UpSells title', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'text',
				'desc'      => esc_html_x( 'Title to show for this section', 'Admin option - UpSells title description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_upsells_section_title',
				'default'   => esc_html__( 'You may be interested in...', 'yith-custom-thankyou-page-for-woocommerce' ),
			),

			'ctpw_upsells_options_columns'                => array(
				'title'     => esc_html_x( 'Products per row', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'options'   => apply_filters(
					'yith_ctpw_ups_columns_options',
					array(
						2 => '2',
						3 => '3',
						4 => '4',
						5 => '5',
						6 => '6',
					)
				),
				'id'        => 'yith_ctpw_ups_columns',
				'default'   => 4,
				'deps'      => array(
					'id'    => 'yith_ctpw_enable_upsells',
					'value' => 'yes',
					'type'  => 'disable',
				),
			),

			'ctpw_upsells_options_products_per_page'      => array(
				'title'     => esc_html_x( 'Products per page', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'number',
				'id'        => 'yith_ctpw_ups_ppp',
				'default'   => 4,
				'deps'      => array(
					'id'    => 'yith_ctpw_enable_upsells',
					'value' => 'yes',
					'type'  => 'disable',
				),
			),

			'ctpw_upsells_options_orderby'                => array(
				'title'     => esc_html_x( 'Order by', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'options'   => array(
					'title' => esc_html__( 'title', 'yith-custom-thankyou-page-for-woocommerce' ),
					'rand'  => esc_html__( 'random', 'yith-custom-thankyou-page-for-woocommerce' ),
					'date'  => esc_html__( 'date', 'yith-custom-thankyou-page-for-woocommerce' ),
				),
				'id'        => 'yith_ctpw_ups_orderby',
				'default'   => 4,
				'deps'      => array(
					'id'    => 'yith_ctpw_enable_upsells',
					'value' => 'yes',
					'type'  => 'disable',
				),
			),

			'ctpw_upsells_options_order'                  => array(
				'title'     => esc_html_x( 'Order', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'options'   => array(
					'asc'  => esc_html__( 'ASC', 'yith-custom-thankyou-page-for-woocommerce' ),
					'desc' => esc_html__( 'DESC', 'yith-custom-thankyou-page-for-woocommerce' ),
				),
				'id'        => 'yith_ctpw_ups_order',
				'default'   => 4,
				'deps'      => array(
					'id'    => 'yith_ctpw_enable_upsells',
					'value' => 'yes',
					'type'  => 'disable',
				),
			),

			'ctpw_upsells_options_ids'                    => array(
				'title' => esc_html_x( 'Select products', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'  => 'ctpw_product_select',
				'id'    => 'yith_ctpw_upsells_ids',
				'deps'  => array(
					'id'    => 'yith_ctpw_enable_upsells',
					'value' => 'yes',
					'type'  => 'disable',
				),
			),

			'ctpw_upsells_options_end'                    => array(
				'type' => 'sectionend',
			),

			// upsells title style.
			'ctpw_cstyles_options_upsells_start'          => array(
				'type' => 'sectionstart',
			),

			'ctpw_cstyles_options_upsells_title'          => array(
				'title' => esc_html_x( 'Style ', 'Section title', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => '',
			),

			'ctpw_styles_options_upsells_title_color'     => array(
				'title'     => esc_html__( 'Title font color', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'ctpw_upsells_title_color',
				'type'      => 'yith-field',
				'yith-type' => 'colorpicker',
				'default'   => '#000000',

			),

			'ctpw_styles_options_upsells_title_font_size' => array(
				'title'             => esc_html__( 'Title font size', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'              => 'number',
				'default'           => 20,
				'id'                => 'ctpw_upsells_title_fontsize',
				'custom_attributes' => array(
					'min' => 10,
					'max' => 50,
				),
			),

			'ctpw_styles_options_upsells_title_font_weight' => array(
				'title'   => esc_html__( 'Title font weight', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'    => 'select',
				'default' => 'bold',
				'id'      => 'ctpw_upsells_title_fontweight',
				'options' => array(
					'lighter' => 'Lighter',
					'normal'  => 'Normal',
					'bold'    => 'Bold',
					'bolder'  => 'Bolder',
				),
			),

			'ctpw_cstyles_options_upsells_end'            => array(
				'type' => 'sectionend',
			),


		)
	),
);

