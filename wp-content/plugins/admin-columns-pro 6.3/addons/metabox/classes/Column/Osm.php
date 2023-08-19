<?php

namespace ACA\MetaBox\Column;

use ACA;

class Osm extends Map {

	protected function get_link( $value ) {
		return sprintf( 'https://www.openstreetmap.org/#map=%s/%s/%s', $value['zoom'], $value['latitude'], $value['longitude'] );
	}

}