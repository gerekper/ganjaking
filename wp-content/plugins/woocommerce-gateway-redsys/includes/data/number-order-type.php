<?php
/**
 * Number Order Type
 *
 * List of number order type.
 *
 * @package WooCommerce Redsys Gateway
 * @since 2.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get number order type.
 *
 * @return array
 */
function redsys_return_number_order_type() {

	return array(
		'threepluszeros'        => esc_html__( '3 random numbers followed by zeros (Standard and default). Ex: 734000008934', 'woocommerce-redsys' ),
		'endoneletter'          => esc_html__( 'One random lowercase letter at the end, with zeros. Ex: 00000008934i', 'woocommerce-redsys' ),
		'endtwoletters'         => esc_html__( 'Two random lowercase letter at the end, with zeros. Ex: 000008934iz', 'woocommerce-redsys' ),
		'endthreeletters'       => esc_html__( 'Three random lowercase letter at the end, with zeros. Ex: 000008934izq', 'woocommerce-redsys' ),
		'endoneletterup'        => esc_html__( 'One random capital letter at the end, with zeros. Ex: 00000008934Z', 'woocommerce-redsys' ),
		'endtwolettersup'       => esc_html__( 'Two random lowercase letter at the end, with zeros. Ex: 000008934IZ', 'woocommerce-redsys' ),
		'endthreelettersup'     => esc_html__( 'Three random capital letter at the end, with zeros. Ex: 000008934ZYA', 'woocommerce-redsys' ),
		'endoneletterdash'      => esc_html__( 'Dash One random lowercase letter at the end, with zeros. Ex: 00000008934-i', 'woocommerce-redsys' ),
		'endtwolettersdash'     => esc_html__( 'Dash two random lowercase letter at the end, with zeros. Ex: 000008934-iz', 'woocommerce-redsys' ),
		'endthreelettersdash'   => esc_html__( 'DashThree random lowercase letter at the end, with zeros. Ex: 000008934-izq', 'woocommerce-redsys' ),
		'endoneletterupdash'    => esc_html__( 'Dash One random capital letter at the end, with zeros. Ex: 00000008934-Z', 'woocommerce-redsys' ),
		'endtwolettersupdash'   => esc_html__( 'Dash two random lowercase letter at the end, with zeros. Ex: 000008934-IZ', 'woocommerce-redsys' ),
		'endthreelettersupdash' => esc_html__( 'Dash Three random capital letter at the end, with zeros. Ex: 000008934-ZYA', 'woocommerce-redsys' ),
		'simpleorder'           => esc_html__( 'Number created by WooCommerce only with zeros (it gives problems, not recommended) Ex: 000000008934', 'woocommerce-redsys' ),
	);
}
