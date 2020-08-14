<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 **/

if ( ! defined( 'YITH_CTPW_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}
$o = yith_ctpw_get_available_order_to_preview();

if ( $o ) {
	/* we have an order, so we can use it */

	if ( get_option( 'yith_ctpw_dummy_order_id', '' ) > 0 && get_option( 'yith_ctpw_dummy_order_id', '' ) !== '' && get_post_type( get_option( 'yith_ctpw_dummy_order_id', '' ) ) === 'shop_order' ) { //phpcs:ignore
		$preview_message = esc_html__( 'The Preview will be done using the dummy order ', 'yith-custom-thankyou-page-for-woocommerce' ) . get_option( 'yith_ctpw_dummy_order_id' );
	} else {
		$preview_message = esc_html__( 'The Preview will be done using a random completed order.', 'yith-custom-thankyou-page-for-woocommerce' );
	}

	if ( isset ( $_GET['post'] ) ) { //phpcs:ignore
		$page_id = $_GET['post']; //phpcs:ignore
	} else {
		$page_id = 0;
	}

	$preview_link  = 'order-received=' . $o->get_id();
	$preview_link .= '&key=' . $o->get_order_key();
	$preview_link .= '&ctpw=' . $page_id;

	$html  = '<div class="yctpw_preview_button"><p>' . $preview_message . '</p>';
	$html .= '<br /><a target="_blank" class="button" href="#" url-part="' . $preview_link . '">' . esc_html__( 'Preview', 'yith-custom-thankyou-page-for-woocommerce' ) . '</a></div>';
} else {
	/* we have no orders - ask to create a dummy one */
	$html  = '<div class="yctpw_preview_dummy_order"><p>' . esc_html__( 'In order to test this page as a YITH Custom Thank You Page, you need a completed WooCommerce order, but it seems that there is no completed order available at the moment. You can click on the button below to create a dummy order.', 'yith-custom-thankyou-page-for-woocommerce' ) . '</p>';
	$html .= '<br /><a class="button yctpw_create_dummy_order" href="">' . esc_html__( 'Create Dummy Order', 'yith-custom-thankyou-page-for-woocommerce' ) . '</a></div>';
	$html .= '<div style="text-align: center;display: none;" class="yctpw_preview_button" ><p>' . esc_html__( 'The Preview will be done using a random completed order.', 'yith-custom-thankyou-page-for-woocommerce' ) . '</p>';
	$html .= '<br /><a target="_blank" class="button-primary" href="#">' . esc_html__( 'Preview', 'yith-custom-thankyou-page-for-woocommerce' ) . '</a></div>';

}


return array(
	'label'    => esc_html__( 'Preview as Yith Custom Thank You Page', 'yith-custom-thankyou-page-for-woocommerce' ),
	'pages'    => 'page',
	'context'  => 'side',
	'priority' => 'default',
	'tabs'     => array(
		'yctpw-settings' => array(
			'label'  => esc_html__( 'Settings', 'yith-custom-thankyou-page-for-woocommerce' ),
			'fields' => apply_filters(
				'yctpw_page_preview_metabox_fields',
				array(
					'yctpw_preview_link' => array(
						'type' => 'html',
						'html' => $html,
					),
				)
			),
		),
	),
);
