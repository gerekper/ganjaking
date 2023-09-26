<?php

namespace ACA\ACF\Value\Formatter;

use ACA\ACF\Value\Formatter;

class Link extends Formatter {

	public function format( $link, $id = null ) {
		if( empty( $link ) ){
			return $this->column->get_empty_char();
		}

		$label = $link['title'];

		if ( ! $label ) {
			$label = str_replace( [ 'http://', 'https://' ], '', $link['url'] );
		}

		if ( '_blank' === $link['target'] ) {
			$label .= '<span class="dashicons dashicons-external" style="font-size: 1em;"></span>';
		}

		return ac_helper()->html->link( $link['url'], $label );
	}

}