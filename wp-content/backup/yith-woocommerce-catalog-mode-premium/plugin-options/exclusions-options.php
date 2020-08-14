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

$tabs = array(
	'exclusions' => array(
		'exclusions-options' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => array(
				'exclusions-items' => array(
					'title' => esc_html__( 'List of excluded items', 'yith-woocommerce-catalog-mode' ),
				),
			),
		),
	),
);

if ( ywctm_is_multivendor_active() && '' === ywctm_get_vendor_id( true ) ) {
	$tabs['exclusions']['exclusions-options']['sub-tabs']['exclusions-vendors'] = array(
		'title' => esc_html__( 'List of vendors', 'yith-woocommerce-catalog-mode' ),
	);
}

return $tabs;
