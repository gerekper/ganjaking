<?php
/**
 * Shortcodes options tab
 *
 * @package YITH\FAQPluginForWordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'shortcodes' => array(
		'shortcodes-tab' => array(
			'type'           => 'custom_tab',
			'action'         => 'yith_faq_shortcodes',
			'show_container' => true,
		),
	),
);
