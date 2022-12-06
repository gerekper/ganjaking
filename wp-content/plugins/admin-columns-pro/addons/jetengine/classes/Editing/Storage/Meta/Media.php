<?php

namespace ACA\JetEngine\Editing\Storage\Meta;

use AC\MetaType;
use ACA\JetEngine\Field\ValueFormat;
use ACA\JetEngine\Mapping\MediaId;
use ACP;

class Media extends ACP\Editing\Storage\Meta {

	/**
	 * @var string
	 */
	private $value_format;

	public function __construct( $meta_key, MetaType $meta_type, $value_format ) {
		parent::__construct( $meta_key, $meta_type );

		$this->value_format = (string) $value_format;
	}

	public function get( int $id ) {
		$value = parent::get( $id );

		if ( empty( $value ) ) {
			return false;
		}

		switch ( $this->value_format ) {
			case ValueFormat::FORMAT_URL:
			case ValueFormat::FORMAT_BOTH:
				$value = MediaId::from_array( $value );

				break;
		}

		return $value ?: false;
	}

	public function update( int $id, $data ): bool {
		if ( empty( $data ) ) {
			return parent::update( $id, $data );
		}

		switch ( $this->value_format ) {
			case ValueFormat::FORMAT_URL:
				$data = MediaId::to_url( $data );

				break;
			case ValueFormat::FORMAT_BOTH:
				$data = MediaId::to_array( $data );

				break;
		}

		return parent::update( $id, $data );
	}

}