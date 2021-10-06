<?php

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
/*
* Copyright: (C) 2013 - 2021 José Conti
*/
function redsys_return_number_order_type() {
	
	return array(
		'threepluszeros'        => __( '3 random numbers followed by zeros (Standard and default). Ex: 734000008934', 'woocommerce-redsys' ),
		'endoneletter'	        => __( 'One random lowercase letter at the end, with zeros. Ex: 00000008934i', 'woocommerce-redsys' ),
		'endtwoletters'         => __( 'Two random lowercase letter at the end, with zeros. Ex: 000008934iz', 'woocommerce-redsys' ),
		'endthreeletters'       => __( 'Three random lowercase letter at the end, with zeros. Ex: 000008934izq', 'woocommerce-redsys' ),
		'endoneletterup'	    => __( 'One random capital letter at the end, with zeros. Ex: 00000008934Z', 'woocommerce-redsys' ),
		'endtwolettersup'       => __( 'Two random lowercase letter at the end, with zeros. Ex: 000008934IZ', 'woocommerce-redsys' ),
		'endthreelettersup'     => __( 'Three random capital letter at the end, with zeros. Ex: 000008934ZYA', 'woocommerce-redsys' ),
		'endoneletterdash'	    => __( 'Dash One random lowercase letter at the end, with zeros. Ex: 00000008934-i', 'woocommerce-redsys' ),
		'endtwolettersdash'     => __( 'Dash two random lowercase letter at the end, with zeros. Ex: 000008934-iz', 'woocommerce-redsys' ),
		'endthreelettersdash'   => __( 'DashThree random lowercase letter at the end, with zeros. Ex: 000008934-izq', 'woocommerce-redsys' ),
		'endoneletterupdash'	=> __( 'Dash One random capital letter at the end, with zeros. Ex: 00000008934-Z', 'woocommerce-redsys' ),
		'endtwolettersupdash'   => __( 'Dash two random lowercase letter at the end, with zeros. Ex: 000008934-IZ', 'woocommerce-redsys' ),
		'endthreelettersupdash' => __( 'Dash Three random capital letter at the end, with zeros. Ex: 000008934-ZYA', 'woocommerce-redsys' ),
		'simpleorder'	        => __( 'Number created by WooCommerce only with zeros (it gives problems, not recommended) Ex: 000000008934', 'woocommerce-redsys' ),
	);
}
