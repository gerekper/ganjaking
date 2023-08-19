<?php

namespace ACA\MetaBox\Column;

use ACA\MetaBox\Column;

class Map extends Column {

	public function format_single_value( $value, $id = null ) {
		if ( ! is_array( $value ) ) {
			return $this->get_empty_char();
		}

		if ( empty( $value['latitude'] ) || empty( $value['longitude'] ) ) {
			return $this->get_empty_char();
		}

		$parts = [
			sprintf( '%s: %s', __( 'Latitude', 'codepress-admin-columns' ), $value['latitude'] ),
			sprintf( '%s: %s', __( 'Longitude', 'codepress-admin-columns' ), $value['longitude'] ),
			sprintf( '%s: %s', __( 'Zoom', 'codepress-admin-columns' ), $value['zoom'] ),
		];

		return ac_helper()->html->link(
			$this->get_link( $value ),
			ac_helper()->html->tooltip( __( 'View' ), implode( '<br>', $parts ) ),
			[ 'target' => '_blank' ]
		);
	}

	protected function get_link( $value ) {
		return sprintf( 'https://www.google.com/maps/search/?api=1&query=%s,%s&z=%s', $value['latitude'], $value['longitude'], $value['zoom'] );
	}

}