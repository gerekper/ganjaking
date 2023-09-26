<?php

namespace ACA\MetaBox\Column;

use ACA;

class Background extends ACA\MetaBox\Column {

	public function format_single_value( $value, $id = null ) {
		if ( empty( $value ) ) {
			return $this->get_empty_char();
		}

		$parts = [
			ac_helper()->string->get_color_block( $value['color'] ),
		];

		if ( $value['image'] ) {
			$parts[] = ac_helper()->image->get_image_by_url( $value['image'], [ 60, 60 ] );
		}

		$parts[] = implode( ' | ', array_filter( [ $value['repeat'], $value['attachment'], $value['position'], $value['size'] ] ) );

		return sprintf( '<div class="ac-mb-column-color">%s</div>', implode( $parts ) );
	}

}