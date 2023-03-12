<?php
declare( strict_types=1 );

namespace ACA\MLA\Export\Model;

use ACA\MLA\Export\ExtendedPostTrait;
use ACP;

class Inserted implements ACP\Export\Service {

	use ExtendedPostTrait,
		FormatPostStatusTrait,
		UnshiftArrayTrait;

	public function get_value( $id ) {
		$item = $this->get_extended_post( (int) $id );

		if ( $item === null ) {
			return '';
		}

		$_inserts = $item->mla_references['inserts'] ?? [];
		$_inserted_option = $item->mla_references['inserted_option'] ?? '';

		$values = [];

		foreach ( $_inserts as $file => $inserts ) {
			$value = '';

			if ( 'base' !== $_inserted_option ) {
				$value .= $file . "\n";
			}

			$inserts = $this->shift_element_to_top( $inserts, $item->post_parent );

			foreach ( $inserts as $insert ) {
				$parent = $insert->ID === $item->post_parent
					? sprintf( ", %s", __( 'PARENT', 'media-library-assistant' ) )
					: '';

				$value .= sprintf(
					"%1\$s (%2\$s %3\$s%4\$s%5\$s)",
					esc_attr( $insert->post_title ),
					esc_attr( $insert->post_type ),
					$insert->ID,
					$this->format_post_status( $insert->post_status ),
					$parent
				);
			}

			$values[] = $value;
		}

		return implode( ",\n", $values );
	}

}