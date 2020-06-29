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

	'blacklist' => array(

		'ywaf_blacklist_title'          => array(
			'name' => __( 'Email blacklist settings', 'yith-woocommerce-anti-fraud' ),
			'type' => 'title',
		),
		'ywaf_email_blacklist_enable'   => array(
			'name'      => __( 'Enable email blacklist', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_email_blacklist_enable',
			'default'   => 'yes',
		),
		'ywaf_email_blacklist_auto_add' => array(
			'name'      => __( 'Enable automatic blacklisting', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_email_blacklist_auto_add',
			'default'   => 'yes',
			'desc'      => __( 'Add email addresses of orders reported with a high risk of fraud to blacklist automatically', 'yith-woocommerce-anti-fraud' ),
			'deps'      => array(
				'id'    => 'ywaf_email_blacklist_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_email_blacklist_list'     => array(
			'name'        => __( 'Block these email addresses', 'yith-woocommerce-anti-fraud' ),
			'type'        => 'yith-field',
			'yith-type'   => 'ywaf-custom-checklist',
			'id'          => 'ywaf_email_blacklist_list',
			'default'     => '',
			'desc'        => __( 'The following email addresses are not safe.', 'yith-woocommerce-anti-fraud' ),
			'placeholder' => __( 'Enter an email address&hellip;', 'yith-woocommerce-anti-fraud' ),
			'deps'        => array(
				'id'    => 'ywaf_email_blacklist_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_blacklist_end'            => array(
			'type' => 'sectionend',
		),

	)

);