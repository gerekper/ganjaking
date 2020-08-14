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

$blocks = array(
	'yith-woocommerce-ajax-search' => array(
		'title'          => _x( 'Ajax Search', '[gutenberg]: block name', 'yith-woocommerce-ajax-search' ),
		'description'    => _x( 'Show Ajax Search Form', '[gutenberg]: block description', 'yith-woocommerce-ajax-search' ),
		'shortcode_name' => 'yith_woocommerce_ajax_search',
		'do_shortcode'   => false,
		'keywords'       => array(
			_x( 'Ajax Search', '[gutenberg]: keywords', 'yith-woocommerce-ajax-search' ),
			_x( 'Search', '[gutenberg]: keywords', 'yith-woocommerce-ajax-search' ),
		),
		'attributes'     => array(
			'template' => array(
				'type'    => 'select',
				'label'   => _x( 'Template', '[gutenberg]: show or hide the thumbnail', 'yith-woocommerce-ajax-search' ),
				'default' => '',
				'options' => array(
					''     => _x( 'Default', '[gutenberg]: Help to show thumbnail', 'yith-woocommerce-ajax-search' ),
					'wide' => _x( 'Wide', '[gutenberg]: Help to hide thumbnail', 'yith-woocommerce-ajax-search' ),
				),
			),
			'class'    => array(
				'type'    => 'text',
				'label'   => _x( 'Class', '[gutenberg]: class of widget', 'yith-woocommerce-ajax-search' ),
				'default' => __( '', 'yith-woocommerce-ajax-search' ),
			),
		),
	),
);




return apply_filters( 'ywraq_gutenberg_blocks', $blocks );
