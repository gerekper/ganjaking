<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit;
}

return array(
	'subscription' => array(
		'subscription-options' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => array(
				'subscription-list-table' => array(
					'title' => esc_html__( 'All Subscriptions', 'yith-woocommerce-subscription' ),
				),
				'subscription-activities' => array(
					'title' => esc_html_x( 'Subscription Activities', 'Admin recap panel with all subscriptions', 'yith-woocommerce-subscription' ),
				),
			),
		),
	),
);
