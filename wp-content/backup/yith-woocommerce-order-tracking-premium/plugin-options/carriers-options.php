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
	'carriers' => array(
		'section_carriers'     => array(
			'name' => __( 'List of supported carriers', 'yith-woocommerce-order-tracking' ),
			'desc' => __( "Check carriers that will be used for order shipping. <b>If you shouldn't find your favorite carrier in this list, please open a ticket on support.yithemes.com. Our support team will be glad to add it as soon as possible.</b>", 'yith-woocommerce-order-tracking'),
			'type' => 'title',
		),
		'home' => array(
			'id'    => 'ywot_carriers',
			'type'   => 'carriers_list',
		),
		'section_carriers_end' => array(
			'type' => 'sectionend',
		)
	)
);