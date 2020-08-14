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
	'style' => array(

		/* =================== HOME =================== */
		'home'     => array(
			array(
				'name' => esc_html__( 'Appearance Settings', 'yith-live-chat' ),
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
				'name' => esc_html__( 'Main Color', 'yith-live-chat' ),
				'desc' => esc_html__( 'This color will be applied to Chat button, header and form buttons. Default', 'yith-live-chat' ) . ': #009EDB',
				'id'   => 'header-button-color',
				'type' => 'colorpicker',
				'std'  => ylc_get_default( 'header-button-color' ),
			),
			array(
				'name'    => esc_html__( 'Chat Button Type', 'yith-live-chat' ),
				'desc'    => '',
				'id'      => 'chat-button-type',
				'type'    => 'select',
				'std'     => ylc_get_default( 'chat-animation' ),
				'options' => array(
					'classic' => esc_html__( 'Classic', 'yith-live-chat' ),
					'round'   => esc_html__( 'Round', 'yith-live-chat' ),
				)
			),
			array(
				'name'   => esc_html__( 'Chat Button Diameter', 'yith-live-chat' ),
				'desc'   => sprintf( esc_html__( 'Default%s Min.%s Max.%s', 'yith-live-chat' ), ': 60px,', ': 40px,', ': 100px' ),
				'id'     => 'chat-button-diameter',
				'type'   => 'slider',
				'std'    => ylc_get_default( 'chat-button-diameter' ),
				'option' => array(
					'min' => 40,
					'max' => 100,
				),
				'deps'   => array(
					'ids'    => 'chat-button-type',
					'values' => 'round'
				),
			),
			array(
				'name'   => esc_html__( 'Chat Button Width', 'yith-live-chat' ),
				'desc'   => sprintf( esc_html__( 'Default%s Min.%s Max.%s', 'yith-live-chat' ), ': 260px,', ': 220px,', ': 400px' ),
				'id'     => 'chat-button-width',
				'type'   => 'slider',
				'std'    => ylc_get_default( 'chat-button-width' ),
				'option' => array(
					'min' => 220,
					'max' => 400,
				),
				'deps'   => array(
					'ids'    => 'chat-button-type',
					'values' => 'classic'
				),
			),
			array(
				'name'   => esc_html__( 'Chat Conversation Width', 'yith-live-chat' ),
				'desc'   => sprintf( esc_html__( 'Default%s Min.%s Max.%s', 'yith-live-chat' ), ': 370px,', ': 220px,', ': 400px' ),
				'id'     => 'chat-conversation-width',
				'type'   => 'slider',
				'std'    => ylc_get_default( 'chat-conversation-width' ),
				'option' => array(
					'min' => 220,
					'max' => 400,
				),
			),
			array(
				'name'   => esc_html__( 'Form Width', 'yith-live-chat' ),
				'desc'   => sprintf( esc_html__( 'Default%s Min.%s Max.%s', 'yith-live-chat' ), ': 260px,', ': 220px,', ': 400px' ),
				'id'     => 'form-width',
				'type'   => 'slider',
				'std'    => ylc_get_default( 'form-width' ),
				'option' => array(
					'min' => 220,
					'max' => 400,
				),
			),
			array(
				'name'   => esc_html__( 'Border Radius', 'yith-live-chat' ),
				'desc'   => sprintf( esc_html__( 'Default%s Min.%s Max.%s', 'yith-live-chat' ), ': 5px,', ': 0px,', ': 50px' ),
				'id'     => 'border-radius',
				'type'   => 'slider',
				'std'    => ylc_get_default( 'border-radius' ),
				'option' => array(
					'min' => 0,
					'max' => 50,
				),
			),
			array(
				'name'    => esc_html__( 'Chat Position', 'yith-live-chat' ),
				'desc'    => esc_html__( 'Default', 'yith-live-chat' ) . ': Right bottom corner',
				'id'      => 'chat-position',
				'type'    => 'select',
				'std'     => ylc_get_default( 'chat-position' ),
				'options' => array(
					'left-top'     => esc_html__( 'Top left corner', 'yith-live-chat' ),
					'right-top'    => esc_html__( 'Top right corner', 'yith-live-chat' ),
					'left-bottom'  => esc_html__( 'Bottom left corner', 'yith-live-chat' ),
					'right-bottom' => esc_html__( 'Bottom right corner', 'yith-live-chat' ),
				)
			),
			array(
				'name'    => esc_html__( 'Chat Opening Animation', 'yith-live-chat' ),
				'desc'    => esc_html__( 'Default', 'yith-live-chat' ) . ': Bounce',
				'id'      => 'chat-animation',
				'type'    => 'select',
				'std'     => ylc_get_default( 'chat-animation' ),
				'options' => array(
					'none'     => esc_html__( 'None', 'yith-live-chat' ),
					'bounceIn' => esc_html__( 'Bounce', 'yith-live-chat' ),
					'fadeIn'   => esc_html__( 'Fade', 'yith-live-chat' ),
				)
			),
			array(
				'name'   => esc_html__( 'Autoplay Delay', 'yith-live-chat' ),
				'desc'   => esc_html__( 'Seconds that have to pass before chat popup opens automatically. Set zero for no autoplay. Default', 'yith-live-chat' ) . ': 10',
				'id'     => 'autoplay-delay',
				'type'   => 'slider',
				'std'    => ylc_get_default( 'autoplay-delay' ),
				'option' => array(
					'min' => 0,
					'max' => 20,
				)
			),
			array(
				'name' => esc_html__( 'Custom CSS', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'custom-css',
				'type' => 'textarea-codemirror',
				'std'  => ylc_get_default( 'custom-css' ),
			)
		),
	)
);