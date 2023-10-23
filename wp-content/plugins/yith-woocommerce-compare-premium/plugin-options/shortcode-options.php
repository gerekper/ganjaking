<?php
/**
 * Shortcode tab options
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Compare
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WOOCOMPARE' ) ) {
	exit;
} // Exit if accessed directly

$options = array(
	'shortcode' => array(
		'tab' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_woocompare_shortcode_tab',
		),
	),
);

return $options;
