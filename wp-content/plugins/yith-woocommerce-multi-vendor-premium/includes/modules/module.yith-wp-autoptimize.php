<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 * @support    Add support for Autoptimize. Show Google maps in vendor's page
 * @author     Andrea Grillo <andrea.grillo@yithemes.com>
 *
 */
if( ! function_exists( 'yith_autoptimize_filter_js_exclude' ) ){
	function yith_autoptimize_filter_js_exclude( $js_exclude, $content ){
		if( function_exists( 'YITH_Vendors' ) && ! empty( YITH_Vendors()->frontend ) ){
			if( YITH_Vendors()->frontend->is_vendor_page() ){
				$js_exclude .= ' gmaps-api, gmap3.min.js, jquery';
			}
		}
		return $js_exclude;
	}
}

add_filter( 'autoptimize_filter_js_exclude', 'yith_autoptimize_filter_js_exclude', 10, 2 );