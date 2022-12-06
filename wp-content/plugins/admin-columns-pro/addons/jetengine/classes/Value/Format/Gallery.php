<?php

namespace ACA\JetEngine\Value\Format;

use ACA\JetEngine\Field;
use ACA\JetEngine\Mapping\MediaId;
use ACA\JetEngine\Value\Formatter;

class Gallery extends Formatter {

	public function format( $raw_value ): ?string {
		$media_ids = array_filter( $this->get_media_id_by_value( $raw_value ) );

		return empty( $media_ids )
			? $this->column->get_empty_char()
			: $this->column->get_formatted_value( $media_ids, $media_ids );
	}

	private function get_media_id_by_value( $value ) {
		switch ( $this->field->get_value_format() ) {
			case Field\ValueFormat::FORMAT_URL:
				return array_map( 'attachment_url_to_postid', explode( ',', $value ) );

			case Field\ValueFormat::FORMAT_BOTH:
				return array_map( [ MediaId::class, 'from_array' ], $value );

			default:
				return explode( ',', $value );
		}
	}

}