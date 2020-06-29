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
	'color' => array(

		/* =================== HOME =================== */
		'home'     => array(
			array(
				'name' => esc_html__( 'Color Settings', 'yith-faq-plugin-for-wordpress' ),
				'type' => 'title'
			),
			array(
				'type' => 'close'
			)
		),
		/* =================== END SKIN =================== */

		/* =================== MESSAGES =================== */
		'settings' => array(
			array(
				'name' => esc_html__( 'Customize Search Button Colors', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'customize-search',
				'type' => 'on-off',
				'std'  => 'off',
			),
			array(
				'name' => esc_html__( 'Search Button Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'search-color',
				'type' => 'colorpicker',
				'std'  => '#B0B0B0',
				'deps' => array(
					'ids'    => 'customize-search',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Search Button Hover Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'search-color-hover',
				'type' => 'colorpicker',
				'std'  => '#FFFFFF',
				'deps' => array(
					'ids'    => 'customize-search',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Search Button Icon Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'search-icon-color',
				'type' => 'colorpicker',
				'std'  => '#FFFFFF',
				'deps' => array(
					'ids'    => 'customize-search',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Search Button Icon Hover Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'search-icon-color-hover',
				'type' => 'colorpicker',
				'std'  => '#B0B0B0',
				'deps' => array(
					'ids'    => 'customize-search',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Customize Category Button Colors', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'customize-category',
				'type' => 'on-off',
				'std'  => 'off',
			),
			array(
				'name' => esc_html__( 'Category Button Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'category-color',
				'type' => 'colorpicker',
				'std'  => '#FFFFFF',
				'deps' => array(
					'ids'    => 'customize-category',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Category Button Hover/Active Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'category-color-hover',
				'type' => 'colorpicker',
				'std'  => '#B0B0B0',
				'deps' => array(
					'ids'    => 'customize-category',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Category Button Text Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'category-text-color',
				'type' => 'colorpicker',
				'std'  => '#B0B0B0',
				'deps' => array(
					'ids'    => 'customize-category',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Category Button Text Hover/Active Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'category-text-color-hover',
				'type' => 'colorpicker',
				'std'  => '#FFFFFF',
				'deps' => array(
					'ids'    => 'customize-category',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Customize Navigation Button Colors', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'customize-navigation',
				'type' => 'on-off',
				'std'  => 'off',
			),
			array(
				'name' => esc_html__( 'Navigation Button Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'navigation-color',
				'type' => 'colorpicker',
				'std'  => '#FFFFFF',
				'deps' => array(
					'ids'    => 'customize-navigation',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Navigation Button Hover/Active Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'navigation-color-hover',
				'type' => 'colorpicker',
				'std'  => '#B0B0B0',
				'deps' => array(
					'ids'    => 'customize-navigation',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Navigation Button Text Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'navigation-text-color',
				'type' => 'colorpicker',
				'std'  => '#B0B0B0',
				'deps' => array(
					'ids'    => 'customize-navigation',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Navigation Button Text Hover/Active Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'navigation-text-color-hover',
				'type' => 'colorpicker',
				'std'  => '#FFFFFF',
				'deps' => array(
					'ids'    => 'customize-navigation',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Customize FAQ Icon Colors ', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'customize-icons',
				'type' => 'on-off',
				'std'  => 'off',
			),
			array(
				'name' => esc_html__( 'FAQ Icon Background Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'icon-background-color',
				'type' => 'colorpicker',
				'std'  => '#B0B0B0',
				'deps' => array(
					'ids'    => 'customize-icons',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'FAQ Icon Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'icon-color',
				'type' => 'colorpicker',
				'std'  => '#FFFFFF',
				'deps' => array(
					'ids'    => 'customize-icons',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'Customize FAQ Link Button Colors', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'customize-link',
				'type' => 'on-off',
				'std'  => 'off',
			),
			array(
				'name' => esc_html__( 'FAQ Link Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'link-color',
				'type' => 'colorpicker',
				'std'  => '#B0B0B0',
				'deps' => array(
					'ids'    => 'customize-link',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'FAQ Link Hover Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'link-color-hover',
				'type' => 'colorpicker',
				'std'  => '#FFFFFF',
				'deps' => array(
					'ids'    => 'customize-link',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'FAQ Link Icon Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'link-icon-color',
				'type' => 'colorpicker',
				'std'  => '#FFFFFF',
				'deps' => array(
					'ids'    => 'customize-link',
					'values' => 'yes'
				),
			),
			array(
				'name' => esc_html__( 'FAQ Link Icon Hover Color', 'yith-faq-plugin-for-wordpress' ),
				'desc' => '',
				'id'   => 'link-icon-color-hover',
				'type' => 'colorpicker',
				'std'  => '#B0B0B0',
				'deps' => array(
					'ids'    => 'customize-link',
					'values' => 'yes'
				),
			),
		),
	)
);