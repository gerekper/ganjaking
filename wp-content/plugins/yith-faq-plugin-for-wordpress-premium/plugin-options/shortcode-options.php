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

return array(
	'shortcode' => array(

		/* =================== HOME =================== */
		'home'     => array(
			array(
				'name' => esc_html__( 'Shortcode Creation', 'yith-faq-plugin-for-wordpress' ),
				'type' => 'title',
			),
			array(
				'type' => 'close',
			),
		),
		/* =================== END SKIN =================== */

		/* =================== MESSAGES =================== */
		'settings' => array(
			'enable_search_box'      => array(
				'id'   => 'enable_search_box',
				'name' => esc_html__( 'Show search box', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'type' => 'on-off',
				'std'  => 'off',
			),
			'enable_category_filter' => array(
				'id'   => 'enable_category_filter',
				'name' => esc_html__( 'Show category filters', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'type' => 'on-off',
				'std'  => 'off',
			),
			'style'                  => array(
				'id'      => 'style',
				'name'    => esc_html__( 'Choose the style', 'yith-faq-plugin-for-wordpress' ),
				'type'    => 'radio',
				'options' => array(
					'list'      => esc_html__( 'List', 'yith-faq-plugin-for-wordpress' ),
					'accordion' => esc_html__( 'Accordion', 'yith-faq-plugin-for-wordpress' ),
					'toggle'    => esc_html__( 'Toggle', 'yith-faq-plugin-for-wordpress' ),
				),
				'std'     => 'list',
			),
			'page_size'              => array(
				'id'     => 'page_size',
				'name'   => esc_html__( 'FAQs per page', 'yith-faq-plugin-for-wordpress' ),
				'type'   => 'slider',
				'option' => array(
					//APPLY_FILTER: yith_faq_minimum_page : set minimum number of items in a page
					'min' => apply_filters( 'yith_faq_minimum_page', 5 ),
					//APPLY_FILTER: yith_faq_maximum_page : set maximum number of items in a page
					'max' => apply_filters( 'yith_faq_maximum_page', 20 ),
				),
				'std'    => '10',
			),
			'categories'             => array(
				'id'       => 'categories',
				'name'     => esc_html__( 'Categories to display', 'yith-faq-plugin-for-wordpress' ),
				'type'     => 'ajax-terms',
				'multiple' => true,
				'data'     => array(
					'placeholder' => esc_html__( 'Search FAQs Categories', 'yith-faq-plugin-for-wordpress' ),
					'taxonomy'    => YITH_FWP()->taxonomy,
				),
			),
			'show_icon'              => array(
				'id'      => 'show_icon',
				'name'    => esc_html__( 'Show icon', 'yith-faq-plugin-for-wordpress' ),
				'type'    => 'radio',
				'options' => array(
					'off'   => esc_html__( 'Off', 'yith-faq-plugin-for-wordpress' ),
					'left'  => esc_html__( 'Left', 'yith-faq-plugin-for-wordpress' ),
					'right' => esc_html__( 'Right', 'yith-faq-plugin-for-wordpress' ),
				),
				'std'     => 'right',
				'deps'    => array(
					'ids'    => 'style',
					'values' => 'accordion,toggle',
				),
			),
			'icon_size'              => array(
				'id'     => 'icon_size',
				'name'   => esc_html__( 'Icon size (px)', 'yith-faq-plugin-for-wordpress' ),
				'type'   => 'slider',
				'option' => array(
					'min' => '8',
					'max' => '40',
				),
				'std'    => '14',
				'deps'   => array(
					'ids'    => 'style',
					'values' => 'accordion,toggle',
				),
			),
			'icon'                   => array(
				'id'           => 'icon',
				'name'         => esc_html__( 'Choose the icon', 'yith-faq-plugin-for-wordpress' ),
				'type'         => 'icons',
				'std'          => 'yfwp:plus',
				'filter_icons' => YITH_FWP_SLUG,
				'deps'         => array(
					'ids'    => 'style',
					'values' => 'accordion,toggle',
				),
			),
			'shortcode'              => array(
				'id'                => 'shortcode',
				'name'              => '',
				'type'              => 'textarea',
				'std'               => '[yith_faq]',
				'custom_attributes' => 'readonly',
			),
		),
	),
);
