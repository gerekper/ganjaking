<?php

namespace ACA\WC\Filtering\ProductVariation;

use ACP;

class Virtual extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		$available_options = [
			'yes' => __( 'Is Virtual', 'codepress-admin-columns' ),
			'no'  => sprintf( __( 'Exclude %s', 'codepress-admin-columns' ), __( 'Virtual', 'woocommerce' ) ),
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
