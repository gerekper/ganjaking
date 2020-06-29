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

$section1 = array(
	'email_title'                       => array(
		'name' => __( 'Email before expiration - Settings', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_email_title',
	),
	'send_email_before_expiration_date' => array(
		'name'      => __( 'Send an email before the expiration date', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_send_email_before_expiration_date',
	),

	'send_email_days_before'            => array(
		'name'      => __( 'Days before points expire', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Number of days before point expiration when the email will be sent', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => '',
		'id'        => 'ywpar_send_email_days_before',
		'deps'      => array(
			'id'    => 'ywpar_send_email_before_expiration_date',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'expiration_email_content'          => array(
		'name'      => __( 'Email content', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => _x(
			'You can use the following placeholders,<br>
                {username} = customer\'s username <br>
                {first_name} = customer\'s  first name <br>
                {last_name} = customer\'s last name <br>
                {expiring_points} = expiring points <br>
                {label_points} = label for points <br>
                {expiring_date} = points expiry date<br>
                {total_points} = current balance',
			'do not translate the text inside the brackets',
			'yith-woocommerce-points-and-rewards'
		),
		'yith-type' => 'textarea',
		'type'      => 'yith-field',
		'default'   => _x(
			'Hi {username},
this email is to remind you that you have {expiring_points} {label_points} about to expire.
They expire on {expiring_date}.',
			'do not translate the text inside the brackets',
			'yith-woocommerce-points-and-rewards'
		),
		'id'        => 'ywpar_expiration_email_content',
		'deps'      => array(
			'id'    => 'ywpar_send_email_before_expiration_date',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),


	'email_title_end'                   => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_email_title_end',
	),

	'update_points_email_title'         => array(
		'name' => __( 'Updated Points - Email Settings', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_update_points_email_title',
	),

	'enable_update_point_email'         => array(
		'name'      => __( 'Enable this email notification on updated points', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_enable_update_point_email',
	),

	'update_point_mail_time'            => array(
		'name'      => __( 'Send Email', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'options'   => array(
			'daily'        => __( 'Once a day if points have been updated.', 'yith-woocommerce-points-and-rewards' ),
			'every_update' => __( 'As soon as points are updated.', 'yith-woocommerce-points-and-rewards' ),
		),
		'default'   => 'daily',
		'id'        => 'ywpar_update_point_mail_time',
		'deps'      => array(
			'id'    => 'ywpar_enable_update_point_email',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'update_point_mail_on_admin_action' => array(
		'name'      => __( 'Avoid email sending for manual update.', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Enable to not send email when the admin updates points manually.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_update_point_mail_on_admin_action',
		'deps'      => array(
			'id'    => 'ywpar_update_point_mail_time',
			'value' => 'every_update',
			'type'  => 'hide',
		),

	),


	'update_point_email_content'        => array(
		'name'      => __( 'Email content', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => sprintf(
			'%s {username} = %s {first_name} = %s {last_name} = %s {latest_updates} = %s {total_points} = %s %s ',
			__( 'You can use the following placeholders', 'yith-woocommerce-points-and-rewards' ) . '<br>',
			__( "customer's username", 'yith-woocommerce-points-and-rewards' ) . '<br>',
			__( "customer's first name", 'yith-woocommerce-points-and-rewards' ) . '<br>',
			__( "customer's last name", 'yith-woocommerce-points-and-rewards' ) . '<br>',
			__( 'latest updates of your points', 'yith-woocommerce-points-and-rewards' ) . '<br>',
			__( 'label for points', 'yith-woocommerce-points-and-rewards' ),
			__( 'current balance', 'yith-woocommerce-points-and-rewards' )
		),
		'yith-type' => 'textarea',
		'type'      => 'yith-field',
		'default'   => _x(
			'Hi {username},
below you can find the latest updates about your {label_points}. {latest_updates} Your current balance is {total_points}.', 'do not translate the text inside the brackets',
			'yith-woocommerce-points-and-rewards'
		),
		'id'        => 'ywpar_update_point_email_content',
		'deps'      => array(
			'id'    => 'ywpar_enable_update_point_email',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'update_points_email_title_end'     => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_update_points_email_title_end',
	),



);

return array( 'emails' => $section1 );
