<?php
declare( strict_types=1 );

namespace ACA\MLA\Export\Model;

use ACA\MLA\Export\ExtendedPostTrait;
use ACP;
use InvalidArgumentException;

class MlaGalleryIn implements ACP\Export\Service {

	use ExtendedPostTrait,
		FormatPostStatusTrait,
		UnshiftArrayTrait;

	private $reference_key;

	public function __construct( string $reference_key ) {
		$this->reference_key = $reference_key;

		$this->validate();
	}

	private function validate(): void {
		if ( ! in_array( $this->reference_key, [ 'mla_galleries', 'galleries' ], true ) ) {
			throw new InvalidArgumentException( 'Invalid gallery reference key.' );
		}
	}

	public function get_value( $id ) {
		$item = $this->get_extended_post( (int) $id );

		if ( $item === null ) {
			return '';
		}

		$galleries = $item->mla_references[ $this->reference_key ] ?? [];
		$galleries = $this->shift_element_to_top( $galleries, $item->post_parent );

		$values = [];
		foreach ( $galleries as $gallery ) {
			$parent = $gallery['ID'] === $item->post_parent
				? sprintf( ", %s", __( 'PARENT', 'media-library-assistant' ) )
				: '';

			$values[] = sprintf(
				"%1\$s (%2\$s %3\$s%4\$s%5\$s)",
				esc_attr( $gallery['post_title'] ),
				esc_attr( $gallery['post_type'] ),
				$gallery['ID'],
				$this->format_post_status( $gallery['post_status'] ),
				$parent
			);
		}

		return implode( ",\n", $values );
	}

}