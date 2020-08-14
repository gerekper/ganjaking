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

$role_names    = get_editable_roles();
$inherit_roles = array();
$enable_roles  = array();

foreach ( $role_names as $role => $role_info ) {

	if ( $role != 'ylc_chat_op' ) {
		$inherit_roles[ $role ] = $role_info['name'];
	}
	if ( $role != 'ylc_chat_op' && $role != 'administrator' ) {
		$enable_roles[ $role ] = $role_info['name'];
	}

}

return array(
	'user' => array(

		/* =================== HOME =================== */
		'home'     => array(
			array(
				'name' => esc_html__( 'Users Settings', 'yith-live-chat' ),
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
				'name'    => esc_html__( 'Operator Default Role', 'yith-live-chat' ),
				'desc'    => esc_html__( 'In this way, operators will get the same competences of this role, but this won\'t transform the users with this role in operators.', 'yith-live-chat' ),
				'id'      => 'operator-role',
				'type'    => 'select',
				'std'     => ylc_get_default( 'operator-role' ),
				'options' => $inherit_roles
			),
			array(
				'name' => esc_html__( 'Operator Default Avatar', 'yith-live-chat' ),
				'desc' => esc_html__( 'Operators will be able to customize their own avatar from their profile page.', 'yith-live-chat' ),
				'id'   => 'operator-avatar',
				'type' => 'upload-avatar',
				'std'  => ylc_get_default( 'operator-avatar' ),
			),
			array(
				'name'              => esc_html__( 'Maximum Connected Guests', 'yith-live-chat' ),
				'desc'              => esc_html__( 'Default', 'yith-live-chat' ) . ': 2. ' . esc_html__( 'If set to 0, there will be no limits', 'yith-live-chat' ),
				'id'                => 'max-chat-users',
				'type'              => 'number',
				'std'               => ylc_get_default( 'max-chat-users' ),
				'custom_attributes' => array(
					'min'      => 0,
					'required' => 'required'
				)
			),
			array(
				'name'                   => esc_html__( 'Chat Enabled Roles', 'yith-live-chat' ),
				'desc'                   => esc_html__( 'Select user roles that you want to enable as chat operators.', 'yith-live-chat' ),
				'id'                     => 'role-enabled',
				'type'                   => 'checkbox-list',
				'yith-sanitize-callback' => 'ylc_save_capabilities'
			),
		)
	)
);