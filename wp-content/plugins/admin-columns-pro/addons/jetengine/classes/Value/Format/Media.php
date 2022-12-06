<?php

namespace ACA\JetEngine\Value\Format;

use ACA\JetEngine\Field;
use ACA\JetEngine\Value\Formatter;

class Media extends Formatter {

	public function format( $raw_value ): ?string {
		$url = $this->get_media_url_by_value( $raw_value );

		return $url
			? ac_helper()->html->link( $url, esc_html( basename( $url ) ), [ 'target' => '_blank' ] )
			: '<em>' . __( 'Invalid attachment', 'codepress-admin-columns' ) . '</em>';
	}

	private function get_media_url_by_value( $value ) {

		switch ( $this->field->get_value_format() ) {
			case Field\ValueFormat::FORMAT_ID:
				return wp_get_attachment_url( $value );
			case Field\ValueFormat::FORMAT_BOTH:
				return is_array( $value ) && isset( $value['url'] ) ? $value['url'] : false;
			default:
				return $value;
		}
	}

}