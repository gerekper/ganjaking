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
	'debug' => array(
		'ywsn_debug_title' => array(
			'name' => esc_html__( 'SMS Debug', 'yith-woocommerce-sms-notifications' ),
			'type' => 'title',
		),
		'ywsn_debug_log'   => array(
			'name'              => esc_html__( 'Debug Log', 'yith-woocommerce-sms-notifications' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'id'                => 'ywsn_debug_log',
			'css'               => '',
			'default'           => '',
			'custom_attributes' => implode(
				' ',
				array(
					'readonly',
					'style="resize: vertical; width: 100%; min-height: 200px;"',
				)
			),
		),
		'ywsn_debug_end'   => array(
			'type' => 'sectionend',
		),
	),
);
