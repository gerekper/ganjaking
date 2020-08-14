<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

return array(

	'accounts-privacy' => array(

		'privacy_options_eraser_vendor_media_data' => array(
			'title'             => __( "Vendor Profile Media", 'yith-woocommerce-product-vendors' ),
			'type'              => 'checkbox',
			'default'           => 'no',
			'desc'              => __( "Delete vendor avatar and vendor header image file", 'yith-woocommerce-product-vendors' ),
			'desc_tip'          => sprintf( __( 'When handling an <a href="%s">account erasure request</a>, should vendor media data be retained or removed?', 'yith-woocommerce-product-vendors' ), esc_url( admin_url( 'tools.php?page=remove_personal_data' ) ) ),
			'id'                => 'yith_vendor_delete_vendor_media_profile_data'
		),
	)
);
