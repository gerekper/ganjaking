<?php

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
function plugins_to_deactivate() {
	
	return array(
		'/woo-redsys-gateway-light/woocommerce-redsys.php',
		'/redsysoficial/class-wc-redsys.php',
		'/redsys/class-wc-redsys.php',
		'/bizum/class-wc-bizum.php',
		'/woocommerce-sermepa-payment-gateway/wc_redsys_payment_gateway.php'
	);
}
