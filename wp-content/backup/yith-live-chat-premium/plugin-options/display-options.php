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

$vendor_only = ( ylc_check_premium() && defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM ) ? array(
	'name' => esc_html__( 'Show popup only in vendors\' pages', 'yith-live-chat' ),
	'desc' => esc_html__( 'Show the chat popup only in vendors\' pages and hide it in entire shop pages (You must have enabled the premium version of YITH WooCommerce Multi Vendor)', 'yith-live-chat' ),
	'id'   => 'only-vendor-chat',
	'type' => 'on-off',
	'std'  => ylc_get_default( 'only-vendor-chat' ),
) : '';


return array(
	'display' => array(
		/* =================== HOME =================== */
		'home'     => array(
			array(
				'name' => esc_html__( 'Display Settings', 'yith-live-chat' ),
				'type' => 'title'
			),
			array(
				'type' => 'close'
			)
		),
		/* =================== END SKIN =================== */

		/* =================== GENERAL =================== */
		'settings' => array(
			array(
				'name' => esc_html__( 'Hide on mobile devices', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'hide-mobile',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'hide-mobile' )
			),
			array(
				'name' => esc_html__( 'Hide to non-logged users', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'hide-guest',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'hide-mobile' )
			),
			array(
				'name' => esc_html__( 'Hide when operators are offline', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'hide-chat-offline',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'hide-chat-offline' ),
			),
			array(
				'name' => esc_html__( 'Show popup in all pages', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'showing-pages-all',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'showing-pages-all' ),
			),
			array(
				'name' => esc_html__( 'Show in home page', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'showing-home-page',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'showing-home-page' ),
				'deps' => array(
					'ids'    => 'showing-pages-all',
					'values' => 'no'
				),
			),
			array(
				'name' => esc_html__( 'Show in blog pages', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'showing-blog',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'showing-blog' ),
				'deps' => array(
					'ids'    => 'showing-pages-all',
					'values' => 'no'
				),
			),
			array(
				'name' => esc_html__( 'Show in shop & product pages', 'yith-live-chat' ),
				'desc' => '',
				'id'   => 'showing-shop',
				'type' => 'on-off',
				'std'  => ylc_get_default( 'showing-shop' ),
				'deps' => array(
					'ids'    => 'showing-pages-all',
					'values' => 'no'
				),
			),
			array(
				'name' => esc_html__( 'Pages selection', 'yith-live-chat' ),
				'desc' => esc_html__( 'Select the pages where you want to show the chat popup', 'yith-live-chat' ),
				'id'   => 'showing-pages',
				'type' => 'page-select',
				'std'  => ylc_get_default( 'showing-pages' ),
				'deps' => array(
					'ids'    => 'showing-pages-all',
					'values' => 'no'
				),
			),
			$vendor_only,
		)
	)
);
