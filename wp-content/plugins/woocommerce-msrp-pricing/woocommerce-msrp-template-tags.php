<?php

function woocommerce_msrp_show_msrp_info( $product = null ) {
	global $woocommerce_msrp_frontend;
	$woocommerce_msrp_frontend->show_msrp( $product );
}

