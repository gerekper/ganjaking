<?php

namespace ACA\WC\Settings\Address;

use ACA\WC\Settings\Address;

/**
 * @since 3.0
 */
class Billing extends Address {

	public function get_display_options() {
		$options = parent::get_display_options();

		$options['email'] = __( 'Email', 'woocommerce' );
		$options['phone'] = __( 'Phone', 'woocommerce' );

		return $options;
	}

}