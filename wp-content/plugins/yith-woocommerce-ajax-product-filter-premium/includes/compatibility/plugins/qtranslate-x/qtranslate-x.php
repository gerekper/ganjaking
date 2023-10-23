<?php
/**
 * QTranslateX plugin support
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Compatibility
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

add_filter( 'yith_wcan_use_wp_the_query_object', '__return_true' );
