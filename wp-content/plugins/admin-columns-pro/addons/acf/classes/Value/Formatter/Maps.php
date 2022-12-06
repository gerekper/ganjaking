<?php

namespace ACA\ACF\Value\Formatter;

use ACA\ACF\Value\Formatter;

class Maps extends Formatter {

	public function format( $maps_data, $id = null ) {
		if ( ! $maps_data ) {
			return $this->column->get_empty_char();
		}

		$url = $this->get_maps_url( $maps_data );
		$label = $maps_data['address'] ?: 'Google Maps';

		return sprintf( '<a href="%s" target="_blank">%s</a>', $url, $label );
	}

	private function get_maps_url( $data ) {
		$base = 'https://www.google.com/maps/search/?api=1';

		$take_arguments = [ 'address', 'lat', 'lng' ];
		$arguments = [];
		foreach ( $take_arguments as $arg ) {
			if ( isset( $data[ $arg ] ) ) {
				$arguments[] = $data[ $arg ];
			}
		}

		return add_query_arg(
			[
				'query' => implode( ',', $arguments ),
				'zoom'  => $data['zoom'] ?: 15,
			],
			$base
		);
	}

}