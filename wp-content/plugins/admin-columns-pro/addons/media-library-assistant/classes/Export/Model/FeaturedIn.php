<?php
declare( strict_types=1 );

namespace ACA\MLA\Export\Model;

use ACA\MLA\Export\ExtendedPostTrait;
use ACP\Export\Service;

class FeaturedIn implements Service {

	use ExtendedPostTrait,
		FormatPostStatusTrait,
		UnshiftArrayTrait;

	public function get_value( $id ) {
		$item = $this->get_extended_post( (int) $id );

		if ( $item === null ) {
			return '';
		}

		$features = $item->mla_references['features'] ?? [];
		$features = $this->shift_element_to_top( $features, $item->post_parent );

		$values = [];
		foreach ( $features as $feature ) {
			$parent = $feature->ID === $item->post_parent
				? sprintf( ", %s", __( 'PARENT', 'media-library-assistant' ) )
				: '';

			$values[] = sprintf(
				"%1\$s (%2\$s %3\$s%4\$s%5\$s)",
				esc_attr( $feature->post_title ),
				esc_attr( $feature->post_type ),
				$feature->ID,
				$this->format_post_status( $feature->post_status ),
				$parent
			);
		}

		return implode( ",\n", $values );
	}

}