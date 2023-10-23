<?php
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\CategoryAccordion
 */

$settings = array(

	'accordions' => array(
		'accordions_list_table' => array(
			'type'          => 'post_type',
			'post_type'     => 'yith_cacc',
			'wp-list-style' => 'boxed',
			'show_container' => true,
		),
	),
);

return $settings;
