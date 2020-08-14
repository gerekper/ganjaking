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
	'transcript' => array(

		/* =================== HOME =================== */
		'home'     => array(
			array(
				'name' => esc_html__( 'Conversation Settings', 'yith-live-chat' ),
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
				'name' => esc_html__( 'Enable Conversation Evaluation', 'yith-live-chat' ),
				'desc' => esc_html__( 'Allow visitors to evaluate the conversation', 'yith-live-chat' ),
				'id'   => 'chat-evaluation',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'chat-evaluation' ),
			),
			array(
				'name' => esc_html__( 'Enable Conversation Copy Request', 'yith-live-chat' ),
				'desc' => esc_html__( 'Allow visitors to require a copy of chat conversation', 'yith-live-chat' ),
				'id'   => 'transcript-send',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'transcript-send' ),
			),
			array(
				'name'              => esc_html__( 'Sender\'s E-mail', 'yith-live-chat' ),
				'desc'              => esc_html__( 'If not expressed, the system will use the default WordPress email.', 'yith-live-chat' ),
				'id'                => 'transcript-mail-sender',
				'type'              => 'email-field',
				'std'               => ylc_get_default( 'transcript-mail-sender' ),
				'deps'              => array(
					'ids'    => 'transcript-send',
					'values' => 'yes'
				),
				'custom_attributes' => array(
					'style' => 'width: 100%'
				)
			),
			array(
				'name'              => esc_html__( 'Message Body', 'yith-live-chat' ),
				'desc'              => esc_html__( '(HTML tags are not allowed)', 'yith-live-chat' ),
				'id'                => 'transcript-message-body',
				'type'              => 'textarea',
				'std'               => ylc_get_default( 'transcript-message-body' ),
				'deps'              => array(
					'ids'    => 'transcript-send',
					'values' => 'yes'
				),
				'custom_attributes' => array(
					'required' => 'required',
					'class'    => 'textareas'
				)
			),
			array(
				'name' => esc_html__( 'Send A Copy To Administrators', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'transcript-send-admin',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'transcript-send-admin' ),
			),
			array(
				'name'              => esc_html__( 'Administrator\'s Email Addresses', 'yith-live-chat' ),
				'desc'              => esc_html__( 'If not expressed, the system will use the default WordPress admin email. Separate email addresses with comma ","', 'yith-live-chat' ),
				'id'                => 'transcript-send-admin-emails',
				'type'              => 'textarea',
				'std'               => ylc_get_default( 'transcript-send-admin-emails' ),
				'deps'              => array(
					'ids'    => 'transcript-send-admin',
					'values' => 'yes'
				),
				'custom_attributes' => array(
					'class' => 'textareas',
				)
			),
		),
	)
);