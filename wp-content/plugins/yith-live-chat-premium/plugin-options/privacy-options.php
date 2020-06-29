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
	'privacy' => array(

		/* =================== HOME =================== */
		'home'     => array(
			array(
				'name' => esc_html__( 'Privacy Settings', 'yith-live-chat' ),
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
				'name' => esc_html__( 'Enable GDPR Compliance for offline messages', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'offline-gdpr-compliance',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'offline-gdpr-compliance' ),
			),
			array(
				'name'              => esc_html__( 'Checkbox Label', 'yith-live-chat' ),
				'id'                => 'offline-gdpr-checkbox-label',
				'desc'              => '',
				'type'              => 'text',
				'std'               => ylc_get_default( 'offline-gdpr-checkbox-label' ),
				'deps'              => array(
					'ids'    => 'offline-gdpr-compliance',
					'values' => 'yes'
				),
				'custom_attributes' => array(
					'required' => 'required',
				)
			),
			array(
				'name'              => esc_html__( 'Checkbox Description', 'yith-live-chat' ),
				'id'                => 'offline-gdpr-checkbox-desc',
				'desc'              => esc_html__( 'Wrap with { } the word(s) that will become the link to your privacy policy', 'yith-live-chat' ),
				'type'              => 'textarea',
				'std'               => ylc_get_default( 'offline-gdpr-checkbox-desc' ),
				'deps'              => array(
					'ids'    => 'offline-gdpr-compliance',
					'values' => 'yes'
				),
				'custom_attributes' => array(
					'required' => 'required',
					'class'    => 'textareas'
				)
			),
			array(
				'name' => esc_html__( 'Enable GDPR Compliance for chat popup', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'chat-gdpr-compliance',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'chat-gdpr-compliance' ),
			),
			array(
				'name'              => esc_html__( 'Checkbox Label', 'yith-live-chat' ),
				'id'                => 'chat-gdpr-checkbox-label',
				'desc'              => '',
				'type'              => 'text',
				'std'               => ylc_get_default( 'chat-gdpr-checkbox-label' ),
				'deps'              => array(
					'ids'    => 'chat-gdpr-compliance',
					'values' => 'yes'
				),
				'custom_attributes' => array(
					'required' => 'required',
				)
			),
			array(
				'name'              => esc_html__( 'Checkbox Description', 'yith-live-chat' ),
				'id'                => 'chat-gdpr-checkbox-desc',
				'desc'              => esc_html__( 'Wrap with { } the word(s) that will become the link to your privacy policy', 'yith-live-chat' ),
				'type'              => 'textarea',
				'std'               => ylc_get_default( 'chat-gdpr-checkbox-desc' ),
				'deps'              => array(
					'ids'    => 'chat-gdpr-compliance',
					'values' => 'yes'
				),
				'custom_attributes' => array(
					'required' => 'required',
					'class'    => 'textareas'
				)
			),
			array(
				'name' => esc_html__( 'Privacy Page', 'yith-live-chat' ),
				'id'   => 'offline-gdpr-privacy-page',
				'desc' => esc_html__( 'If not expressed, the system will use the default WordPress privacy page.', 'yith-live-chat' ),
				'type' => 'text',
				'std'  => ylc_get_default( 'offline-gdpr-privacy-page' ),
			),

		),
	)
);