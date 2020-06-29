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
	'texts' => array(

		/* =================== HOME =================== */
		'home'     => array(
			array(
				'name' => esc_html__( 'Message Settings', 'yith-live-chat' ),
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
				'name'              => esc_html__( 'Chat Title', 'yith-live-chat' ),
				'desc'              => esc_html__( 'This text will appear in the chat button and the chat title', 'yith-live-chat' ),
				'id'                => 'text-chat-title',
				'type'              => 'text',
				'std'               => ylc_get_default( 'text-chat-title' ),
				'custom_attributes' => array(
					'required' => 'required',
					'style'    => 'width: 100%'
				)
			),
			array(
				'name'              => esc_html__( 'Welcome Message', 'yith-live-chat' ),
				'desc'              => esc_html__( 'This text will appear in the login form', 'yith-live-chat' ),
				'id'                => 'text-welcome',
				'type'              => 'textarea',
				'std'               => ylc_get_default( 'text-welcome' ),
				'custom_attributes' => array(
					'required' => 'required',
					'class'    => 'textareas'
				)
			),
			array(
				'name'              => esc_html__( 'Starting Chat Message', 'yith-live-chat' ),
				'desc'              => esc_html__( 'This text will appear when the chat starts', 'yith-live-chat' ),
				'id'                => 'text-start-chat',
				'type'              => 'textarea',
				'std'               => ylc_get_default( 'text-start-chat' ),
				'custom_attributes' => array(
					'required' => 'required',
					'class'    => 'textareas'
				)
			),
			array(
				'name'              => esc_html__( 'Closing Chat Message', 'yith-live-chat' ),
				'desc'              => esc_html__( 'This text will appear at the end of the chat', 'yith-live-chat' ),
				'id'                => 'text-close',
				'type'              => 'textarea',
				'std'               => ylc_get_default( 'text-close' ),
				'custom_attributes' => array(
					'required' => 'required',
					'class'    => 'textareas'
				)
			),
			array(
				'name'              => esc_html__( 'Offline Message', 'yith-live-chat' ),
				'desc'              => esc_html__( 'This text will appear if no operator is online', 'yith-live-chat' ),
				'id'                => 'text-offline',
				'type'              => 'textarea',
				'std'               => ylc_get_default( 'text-offline' ),
				'custom_attributes' => array(
					'required' => 'required',
					'class'    => 'textareas'
				)
			),
			array(
				'name'              => esc_html__( 'Busy Message', 'yith-live-chat' ),
				'desc'              => esc_html__( 'This text will appear if all operators are busy', 'yith-live-chat' ),
				'id'                => 'text-busy',
				'type'              => 'textarea',
				'std'               => ylc_get_default( 'text-busy' ),
				'custom_attributes' => array(
					'required' => 'required',
					'class'    => 'textareas'
				)
			),
		),
	)
);