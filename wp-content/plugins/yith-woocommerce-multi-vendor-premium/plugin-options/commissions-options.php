<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

return apply_filters( 'yith_wpv_panel_commissions_options', array(

	'commissions' => array(

		'commissions_options_start'          => array(
			'type'  => 'sectionstart',
		),

		'commissions_options_title'          => array(
			'title' => __( 'General settings', 'yith-woocommerce-product-vendors' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wpv_commissions_options_title'
		),

		'commissions_options_base_commission' => array(
			'title'             => __( 'Commission Base', 'yith-woocommerce-product-vendors' ),
			'type'              => 'number',
			'default'           => 50,
			'desc'              => __( 'Default commission percentage', 'yith-woocommerce-product-vendors' ),
			'id'                => 'yith_vendor_base_commission',
			'css'               => 'width:55px;',
			'custom_attributes' => array(
				'min'  => 0,
				'max'  => 100,
				'step' => apply_filters( 'yith_wcmv_commissions_step', 0.1 ),
			)
		),

		'commissions_options_end'          => array(
			'type'  => 'sectionend',
		),
	)
), 'commissions'
);