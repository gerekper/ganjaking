<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Ajax Search Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$options = array(

	'output' => array(
		'search_output_options'              => array(
			'title' => __( 'Output Options', 'yith-woocommerce-ajax-search' ),
			'type'  => 'title',
			'id'    => 'yith_wcas_output_options',
		),

		'search_show_thumbnail'              => array(
			'title'     => __( 'Show thumbnail', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'Choose if you want show thumbnail and position', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_show_thumbnail',
			'class'     => 'yith-wcas-chosen',
			'default'   => 'left',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'none'  => __( 'Hide thumbnail', 'yith-woocommerce-ajax-search' ),
				'left'  => __( 'Show on the Left', 'yith-woocommerce-ajax-search' ),
				'right' => __( 'Show on the Right', 'yith-woocommerce-ajax-search' ),
			),
		),

		'search_default_template'            => array(
			'title'     => __( 'Search form default template', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'This option can be overridden by shortcode or widget', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_search_default_template',
			'class'     => 'yith-wcas-chosen',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				''     => __( 'Default', 'yith-woocommerce-ajax-search' ),
				'wide' => __( 'Wide', 'yith-woocommerce-ajax-search' ),
			),
		),

		'search_show_thumbnail_dim'          => array(
			'name'              => __( 'Size of thumbnails', 'yith-woocommerce-ajax-search' ),
			'desc'              => __( 'Insert in px the dimension of thumbnails', 'yith-woocommerce-ajax-search' ),
			'id'                => 'yith_wcas_search_show_thumbnail_dim',
			'default'           => '50',
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'custom_attributes' => '"min"=5 "max"=150 style="width:50px"',
		),

		'search_show_price'                  => array(
			'name'      => __( 'Show price', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'Show price of product', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_show_price',
			'default'   => 'yes',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		'search_price_label'                 => array(
			'name'    => __( 'Price Label', 'yith-woocommerce-ajax-search' ),
			'desc'    => __( 'Show a label before the price', 'yith-woocommerce-ajax-search' ),
			'id'      => 'yith_wcas_search_price_label',
			'default' => __( 'Price:', 'yith-woocommerce-ajax-search' ),
			'type'    => 'text',
		),
		'include_variations' => array(
			'name' => __('Include Product Variation', 'yith-woocommerce-ajax-search'),
			'desc' => __( 'Choose if include or not the product variations in the search results','yith-woocommerce-ajax-search'),
			'id' => 'yith_wcas_include_variations',
			'default' => 'no',
			'type' => 'yith-field',
			'yith-type' => 'onoff'
		),
		'loader_image'                       => array(
			'name'      => __( 'Loader', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'Loader gif', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_loader_url',
			'default'   => YITH_WCAS_ASSETS_IMAGES_URL . 'preloader.gif',
			'type'      => 'yith-field',
			'yith-type' => 'upload',
		),

		'search_output_options_end'          => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcas_output_options_end',
		),

		'search_badges_options'              => array(
			'title' => __( 'Sales and Featured badges', 'yith-woocommerce-ajax-search' ),
			'type'  => 'title',
			'id'    => 'yith_wcas_badges_options',
		),

		'search_show_sale_badge'             => array(
			'name'      => __( 'Show sale badge', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'Show sale badge if the product is on sale', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_show_sale_badge',
			'default'   => 'yes',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		'search_show_sale_badge_color'       => array(
			'name'         => __( 'Sale badge background color', 'yith-woocommerce-ajax-search' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => '',
			'id'           => 'yith_wcas_sale_badge',
			'colorpickers' => array(
				array(
					'name'    => __( 'Default color', 'yith-woocommerce-request-a-quote' ),
					'id'      => 'bgcolor',
					'default' => '#7eb742',
				),
				array(
					'name'    => __( 'Hover color', 'yith-woocommerce-request-a-quote' ),
					'id'      => 'color',
					'default' => '#ffffff',
				),
			),
		),

		'search_show_outofstock_badge'       => array(
			'name'      => __( 'Show out of stock badge', 'yith-woocommerce-ajax-search' ),
			'desc'      => '',
			'id'        => 'yith_wcas_show_outofstock_badge',
			'default'   => 'yes',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		'search_show_outofstock_badge_color' => array(
			'name'         => __( 'Out of stock badge background color', 'yith-woocommerce-ajax-search' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => '',
			'id'           => 'yith_wcas_outofstock',
			'colorpickers' => array(
				array(
					'name'    => __( 'Default color', 'yith-woocommerce-request-a-quote' ),
					'id'      => 'bgcolor',
					'default' => '#7a7a7a',
				),
				array(
					'name'    => __( 'Hover color', 'yith-woocommerce-request-a-quote' ),
					'id'      => 'color',
					'default' => '#ffffff',
				),
			),
		),

		'search_show_featured_badge'         => array(
			'name'      => __( 'Show featured badge', 'yith-woocommerce-ajax-search' ),
			'desc'      => '',
			'id'        => 'yith_wcas_show_featured_badge',
			'default'   => 'yes',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		'search_show_featured_badge_color'   => array(
			'name'         => __( 'Featured badge background color', 'yith-woocommerce-ajax-search' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'desc'         => '',
			'id'           => 'yith_wcas_featured_badge',
			'colorpickers' => array(
				array(
					'name'    => __( 'Default color', 'yith-woocommerce-request-a-quote' ),
					'id'      => 'bgcolor',
					'default' => '#c0392b',
				),
				array(
					'name'    => __( 'Hover color', 'yith-woocommerce-request-a-quote' ),
					'id'      => 'color',
					'default' => '#ffffff',
				),
			),
		),

		'hide_feature_if_on_sale'            => array(
			'name'      => __( 'Hide featured bagde if the product is on sale', 'yith-woocommerce-ajax-search' ),
			'desc'      => '',
			'id'        => 'yith_wcas_hide_feature_if_on_sale',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		'search_badges_options_end'          => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcas_badges_options_end',
		),

		'search_excerpt_options'             => array(
			'title' => __( 'Title & Excerpt', 'yith-woocommerce-ajax-search' ),
			'type'  => 'title',
			'id'    => 'yith_wcas_excerpt_options',
		),

		'search_title_color'                 => array(
			'name'      => __( 'Title color', 'yith-woocommerce-ajax-search' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'desc'      => '',
			'id'        => 'yith_wcas_search_title_color',
			'default'   => '#004b91',
		),

		'search_show_excerpt'                => array(
			'name'      => __( 'Show excerpt', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'Show excerpt of product', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_show_excerpt',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		'search_show_excerpt_num_words'      => array(
			'name'              => __( 'Number of words to show in excerpt', 'yith-woocommerce-ajax-search' ),
			'id'                => 'yith_wcas_show_excerpt_num_words',
			'default'           => '10',
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'min'               => 5,
			'max'               => 100,
			'step'              => 1,
			'custom_attributes' => 'style="width:50px"',
			'deps'              => array(
				'id'    => 'yith_wcas_show_excerpt',
				'value' => 'yes',
			),
		),

		'search_categories_options'          => array(
			'name'      => __( 'Show product categories', 'yith-woocommerce-ajax-search' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'no',
			'id'        => 'yith_wcas_categories',
		),

		'search_excerpt_options_end'         => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcas_search_excerpt_options_end',
		),

		'search_view_all_options'            => array(
			'title' => __( '"View All" Link', 'yith-woocommerce-ajax-search' ),
			'type'  => 'title',
			'id'    => 'yith_search_view_all_options',
		),

		'search_show_view_all'               => array(
			'name'      => __( 'Show "view all" link', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'Add a link to the bottom of results', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_search_show_view_all',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		'search_show_view_all_text'          => array(
			'name'      => __( 'Text of "view all" link', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'Add a link at the bottom of results', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_search_show_view_all_text',
			'default'   => __( 'View all', 'yith-woocommerce-ajax-search' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'deps'      => array(
				'id'    => 'yith_wcas_search_show_view_all',
				'value' => 'yes',
			),
		),

		'search_show_no_results_text'        => array(
			'name'      => __( 'Text of "no results"', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'Show this text for no results', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_search_show_no_results_text',
			'default'   => __( 'No results', 'yith-woocommerce-ajax-search' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'deps'      => array(
				'id'    => 'yith_wcas_search_show_view_all',
				'value' => 'yes',
			),
		),

		'search_view_all_options_end'        => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcas_search_view_all_options_end',
		),


	),
);

if ( defined( 'YIT_THEME_PATH' ) ) {
	unset( $options['output']['search_default_template'] );
}

return $options;
