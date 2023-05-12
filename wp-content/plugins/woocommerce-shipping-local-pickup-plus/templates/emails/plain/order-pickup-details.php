<?php
/**
 * WooCommerce Local Pickup Plus
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce Local Pickup Plus plain text emails order pickup details template file.
 *
 * @type \WC_Order $order Order being displayed
 * @type array $pickup_data Pickup data for given order
 * @type \WC_Shipping_Local_Pickup_Plus $shipping_method Local Pickup Plus Shipping Method instance
 * @type bool $sent_to_admin Whether the email is being sent to an admin
 *
 * @version 2.0.0
 * @since 2.0.0
 */

$packages_count = count( $pickup_data );
$package_number = 1;

echo "\n\n";
echo $packages_count === 1 ? wp_strip_all_tags( $shipping_method->get_method_title() ) . "\n\n" : '';

foreach ( $pickup_data as $pickup_meta ) {

	if ( $packages_count > 1 ) {
		echo sprintf( is_rtl() ? '#%2$s %1$s' . "\n\n" : '%1$s #%2$s' . "\n\n", wp_strip_all_tags( $shipping_method->get_method_title() ), $package_number );
	}

	foreach ( $pickup_meta as $label => $value ) {
		$value = str_replace( '&times;', 'x', $value );
		echo wp_strip_all_tags( is_rtl() ? $value . ' :' . $label . ' -' : '- ' . $label . ': ' .  $value ) . "\n";
	}

	$package_number++;

	if ( $packages_count > 1 && $package_number <= $packages_count ) {
		echo "\n\n";
	}
}
