<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

return apply_filters( 'yith_wpv_panel_accounts_privacy_options', array(

	'accounts-privacy' => array(

		'privacy_options_start'          => array(
			'type'  => 'sectionstart',
		),

		'privacy_options_title'          => array(
			'title' => __( 'Personal Data Exporter', 'yith-woocommerce-product-vendors' ),
			'type'  => 'title',
		),

		'privacy_options_commission' => array(
			'title'             => __( "Export vendor's commission", 'yith-woocommerce-product-vendors' ),
			'type'              => 'checkbox',
			'default'           => 'yes',
			'desc'              => __( 'Export Commissions', 'yith-woocommerce-product-vendors' ),
			'desc_tip'          => __( 'When handling an <a href="' . esc_url( admin_url( 'tools.php?page=export_personal_data' ) ) . '">account personal data export request</a>, should include commissions ?', 'yith-woocommerce-product-vendors' ),
			'id'                => 'yith_vendor_exports_commissions'
		),

		'privacy_options_end'          => array(
			'type'  => 'sectionend',
		),

		'accounts-privacy_options_start'          => array(
			'type'  => 'sectionstart',
		),

		'privacy_eraser_options_title'          => array(
			'title' => __( 'Personal Data Eraser', 'yith-woocommerce-product-vendors' ),
			'type'  => 'title',
		),

		'privacy_options_eraser_vendor_data' => array(
			'title'             => __( "Vendor Profile", 'yith-woocommerce-product-vendors' ),
			'type'              => 'checkbox',
			'default'           => 'no',
			'desc'              => __( "Remove vendor profile information", 'yith-woocommerce-product-vendors' ),
			'desc_tip'          => sprintf( __( 'When handling an <a href="%s">account erasure request</a>, should vendor data be retained or removed?', 'yith-woocommerce-product-vendors' ), esc_url( admin_url( 'tools.php?page=remove_personal_data' ) ) ),
			'id'                => 'yith_vendor_remove_vendor_profile_data'
		),

		'privacy_options_eraser_commissions' => array(
			'title'             => __( "Commissions", 'yith-woocommerce-product-vendors' ),
			'type'              => 'checkbox',
			'default'           => 'no',
			'desc'              => __( "Remove user id in vendor's commissions", 'yith-woocommerce-product-vendors' ),
			'desc_tip'          => sprintf( __( 'When handling an <a href="%s">account erasure request</a>, should user id within commissions be retained or removed?', 'yith-woocommerce-product-vendors' ), esc_url( admin_url( 'tools.php?page=remove_personal_data' ) ) ),
			'id'                => 'yith_vendor_remove_user_id_in_commissions'
		),

		'accounts-privacy_options_end'          => array(
			'type'  => 'sectionend',
		),
	)
), 'accounts-privacy'
);