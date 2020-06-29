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

	'address-blacklist' => array(

		'ywaf_address_blacklist_title'                 => array(
			'name' => __( 'Addresses blacklist settings', 'yith-woocommerce-anti-fraud' ),
			'type' => 'title',
		),
		'ywaf_address_blacklist_enable'                => array(
			'name'      => __( 'Enable address blacklist', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_address_blacklist_enable',
			'default'   => 'yes',
		),
		'ywaf_address_blacklist_similarity_percentage' => array(
			'name'      => __( 'Percentage of similarity', 'yith-woocommerce-anti-fraud' ),
			'desc'      => __( 'Similarity threshold with stored addresses', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'id'        => 'ywaf_address_blacklist_similarity_percentage',
			'default'   => '65',
		),
		'ywaf_address_blacklist_auto_add'              => array(
			'name'      => __( 'Enable automatic blacklisting', 'yith-woocommerce-anti-fraud' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywaf_address_blacklist_auto_add',
			'default'   => 'yes',
			'desc'      => __( 'Add billing and shipping addresses of orders reported with a high risk of fraud to blacklist automatically', 'yith-woocommerce-anti-fraud' ),
			'deps'      => array(
				'id'    => 'ywaf_address_blacklist_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_address_blacklist_list'                  => array(
			'name'        => __( 'Blacklisted addresses', 'yith-woocommerce-anti-fraud' ),
			'type'        => 'yith-field',
			'yith-type'   => 'ywaf-address-list',
			'id'          => 'ywaf_address_blacklist_list',
			'desc'        => __( 'The following billing and/or shipping addresses are not safe.', 'yith-woocommerce-anti-fraud' ),
			'placeholder' => __( 'Enter an email address&hellip;', 'yith-woocommerce-anti-fraud' ),
			'deps'        => array(
				'id'    => 'ywaf_address_blacklist_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywaf_address_blacklist_end'                   => array(
			'type' => 'sectionend',
		),

	)

);