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
	'offline' => array(

		/* =================== HOME =================== */
		'home'     => array(
			array(
				'name' => esc_html__( 'Offline Messages Settings', 'yith-live-chat' ),
				'type' => 'title'
			),
			array(
				'type' => 'close'
			)
		),
		/* =================== END SKIN =================== */

		/* =================== MESSAGES =================== */
		'settings' => array(


			array(
				'name'              => esc_html__( 'Sender\'s E-mail', 'yith-live-chat' ),
				'desc'              => esc_html__( 'If not expressed, the system will use the default WordPress admin email.', 'yith-live-chat' ),
				'id'                => 'offline-mail-sender',
				'type'              => 'email-field',
				'std'               => ylc_get_default( 'offline-mail-sender' ),
				'custom_attributes' => array(
					'style' => 'width: 100%'
				)
			),
			array(
				'name'              => esc_html__( 'Recipients\' E-mail', 'yith-live-chat' ),
				'desc'              => esc_html__( 'If not expressed, the system will use the default WordPress admin email. Separate email addresses with comma ","', 'yith-live-chat' ),
				'id'                => 'offline-mail-addresses',
				'type'              => 'textarea',
				'std'               => ylc_get_default( 'offline-mail-addresses' ),
				'custom_attributes' => array(
					'style' => 'width: 100%',
					'class' => 'textareas'
				)
			),
			array(
				'name' => esc_html__( 'Send Copy Of Message To Visitor', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'offline-send-visitor',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'offline-send-visitor' ),
			),
			array(
				'name'              => esc_html__( 'Message Body', 'yith-live-chat' ),
				'desc'              => esc_html__( '(HTML tags are not allowed)', 'yith-live-chat' ),
				'id'                => 'offline-message-body',
				'type'              => 'textarea',
				'std'               => ylc_get_default( 'offline-message-body' ),
				'deps'              => array(
					'ids'    => 'offline-send-visitor',
					'values' => 'yes'
				),
				'custom_attributes' => array(
					'required' => 'required',
					'class'    => 'textareas'
				)
			),
			array(
				'name' => esc_html__( 'Show the offline message form even when all the operators are busy', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'offline-busy',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'offline-busy' ),
			),
		),
	)
);