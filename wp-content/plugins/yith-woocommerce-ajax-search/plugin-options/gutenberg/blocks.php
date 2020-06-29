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

$blocks = array(
	'yith-woocommerce-ajax-search' => array(
		'title'          => _x( 'Ajax Search', '[gutenberg]: block name', 'yith-woocommerce-ajax-search' ),
		'description'    => _x( 'Show Ajax Search Form', '[gutenberg]: block description', 'yith-woocommerce-ajax-search' ),
		'shortcode_name' => 'yith_woocommerce_ajax_search',
		'do_shortcode'   => true,
		'keywords'       => array(
			_x( 'Ajax Search', '[gutenberg]: keywords', 'yith-woocommerce-ajax-search' ),
			_x( 'Search', '[gutenberg]: keywords', 'yith-woocommerce-ajax-search' ),
		),
	),
);




return apply_filters( 'ywraq_gutenberg_blocks', $blocks );
