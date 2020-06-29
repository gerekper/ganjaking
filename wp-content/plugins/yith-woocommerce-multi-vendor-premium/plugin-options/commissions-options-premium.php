<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

// merge Unpaid with Processing
$views = array( 'all' => __( 'All', 'yith-woocommerce-product-vendors' ) ) + YITH_Commissions()->get_status();
$views['unpaid'] .= '/' . $views['processing'];
unset( $views['processing'] );

return array(

	'commissions' => array(

		'commissions_default_table_view' => array(
			'title'   => __( 'Commission page view', 'yith-woocommerce-product-vendors' ),
			'type'    => 'select',
			'default' => 'unpaid',
			'desc'    => __( 'Select the default view for commission page', 'yith-woocommerce-product-vendors' ),
			'id'      => 'yith_commissions_default_table_view',
			'options' => $views
		),

		'commissions_default_coupon_handling' => array(
			'title'    => __( 'Coupons', 'yith-woocommerce-product-vendors' ),
			'type'     => 'checkbox',
			'default'  => 'yes',
			'desc'     => __( 'Include coupons in commission calculations', 'yith-woocommerce-product-vendors' ),
			'desc_tip' => __( 'Decide whether vendor commissions have to be calculated including coupon value or not.', 'yith-woocommerce-product-vendors' ),
			'id'       => 'yith_wpv_include_coupon',
		),

		'commissions_default_tax_handling' => array(
			'title'    => __( 'Taxes', 'yith-woocommerce-product-vendors' ),
			'type'     => 'select',
			//in previous version website is "no"
			'default'  => 'website',
			'options'  => array(
				'website' => _x( 'Credit taxes to the website admin', '[Admin]: Option description', 'yith-woocommerce-product-vendors' ),
				'split'   => _x( 'Split tax by percentage between website admin and vendor', '[Admin]: Option description', 'yith-woocommerce-product-vendors' ),
				'vendor'  => _x( 'Credit taxes to the vendor', '[Admin]: Option description', 'yith-woocommerce-product-vendors' ),
			),
			'desc'     => __( 'Tax management in commission calculations', 'yith-woocommerce-product-vendors' ),
			'desc_tip' => __( 'Decide whether vendor commissions have to be calculated including/excluding tax value.', 'yith-woocommerce-product-vendors' ),
			'id'       => 'yith_wpv_commissions_tax_management',
		),
	)
);