<?php
/**
 * New deposit created email (plain)
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( 'You have received an order with deposits from %s. This is the detail of full amount payments:', 'yith-woocommerce-deposits-and-down-payments' ), $parent_order->get_formatted_billing_full_name() ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo strtoupper( sprintf( __( 'Order number: %s', 'woocommerce' ), '{order_number}' ) ) . "\n";
echo '{order_date}' . "\n\n";

echo strtoupper( __( 'Deposits:', 'yith-woocommerce-deposits-and-down-payments' ) ) . "\n\n";

echo '{deposit_table}';

echo "\n" . sprintf( __( 'View order: %s', 'woocommerce'), admin_url( 'post.php?post=' . $parent_order->get_order_number() . '&action=edit' ) ) . "\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
