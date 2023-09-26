<?php

namespace ACA\WC\Filtering\ProductVariation;

use ACP;

class Downloadable extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		$available_options = [
			'yes' => __( 'Is Downloadable', 'codepress-admin-columns' ),
			'no'  => sprintf( __( 'Exclude %s', 'codepress-admin-columns' ), __( 'Downloadable', 'woocommerce' ) ),
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
