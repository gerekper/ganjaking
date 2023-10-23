<?php
/**
 * Items array list
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 3.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

return array(
	'items' => array(
		'yith_wcmap_items' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_wcmap_admin_items_list',
		),
	),
);
