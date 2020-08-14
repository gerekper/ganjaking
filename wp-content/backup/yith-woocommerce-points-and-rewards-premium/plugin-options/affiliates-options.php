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
	'affiliates_title'                     => array(
		'name' => __( 'YITH WooCommerce Affiliates Integration', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_affiliates_title',
	),

	'affiliates_enabled'                   => array(
		'name'      => __( 'Enable Integration', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Enable the integration with YITH WooCommerce Affiliates plugin.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_affiliates_enabled',
	),

	'affiliates_earning_conversion_points' => array(
		'name'      => __( 'Points for affiliates', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Select the method to calculate points for your affiliates.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'default'   => 'fixed',
		'options'   => array(
			'fixed'      => __( 'Fixed amount of points for each order', 'yith-woocommerce-points-and-rewards' ),
			'percentage' => __( 'Percent of points earned by customer', 'yith-woocommerce-points-and-rewards' ),
			'conversion' => __( 'Conversion based on order subtotal', 'yith-woocommerce-points-and-rewards' ),
		),
		'id'        => 'ywpar_affiliates_earning_conversion_points',
	),

	'affiliates_earning_fixed'             => array(
		'name'              => __( 'Amount of points earned for each commission', 'yith-woocommerce-points-and-rewards' ),
		'desc'              => __( '', 'yith-woocommerce-points-and-rewards' ),
		'type'              => 'yith-field',
		'yith-type'         => 'number',
		'default'           => 0,
		'custom_attributes' => 'style="width:70px"',
		'id'                => 'ywpar_affiliates_earning_fixed',
		'deps'              => array(
			'id'    => 'ywpar_affiliates_earning_conversion_points',
			'value' => 'fixed',
			'type'  => 'hide',
		),
	),

	'affiliates_earning_percentage'        => array(
		'name'              => __( 'Percent of points', 'yith-woocommerce-points-and-rewards' ),
		'desc'              => __( '(%) Percent of points earned by customer.', 'yith-woocommerce-points-and-rewards' ),
		'type'              => 'yith-field',
		'yith-type'         => 'number',
		'default'           => 0,
		'step'              => 1,
		'min'               => 0,
		'max'               => 100,
		'custom_attributes' => 'style="width:70px"',
		'id'                => 'ywpar_affiliates_earning_percentage',
		'deps'              => array(
			'id'    => 'ywpar_affiliates_earning_conversion_points',
			'value' => 'percentage',
			'type'  => 'hide',
		),
	),

	'affiliates_earning_conversion'        => array(
		'name'      => __( 'Assign points based on the order subtotal.', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Decide how many points will be assigned for each order based on the currency.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'options-conversion',
		'type'      => 'yith-field',
		'default'   => array(
			$currency => array(
				'points' => 1,
				'money'  => 10,
			),
		),
		'id'        => 'ywpar_affiliates_earning_conversion',
		'deps'      => array(
			'id'    => 'ywpar_affiliates_earning_conversion_points',
			'value' => 'conversion',
			'type'  => 'hide',
		),
	),

	'label_affiliates'                     => array(
		'name'      => __( 'Affiliate commission', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Affiliate commission', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_affiliates',
	),

	'affiliates_title_end'                 => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_affiliates_title_end',
	),

);

return array( 'affiliates' => $section1 );
