<?php

namespace ACA\WC\Filtering\Product;

use ACP;

class BackordersAllowed extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		$available_options = [
			'no'     => __( 'Do not allow', 'woocommerce' ),
			'notify' => __( 'Allow, but notify customer', 'woocommerce' ),
			'yes'    => __( 'Allow', 'woocommerce' ),
		];

		$options = [];

		foreach ( $this->get_meta_values() as $value ) {
			if ( isset( $available_options[ $value ] ) ) {
				$options[ $value ] = $available_options[ $value ];
			}
		}

		return [
			'options' => $options,
		];
	}

}
