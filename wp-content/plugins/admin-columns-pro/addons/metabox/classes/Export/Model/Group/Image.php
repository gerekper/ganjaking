<?php

namespace ACA\MetaBox\Export\Model\Group;

class Image extends Raw {

	public function get_single_value( $value ): string {
		$files = [];
		foreach ( $value as $item_id ) {
			if ( is_numeric( $item_id ) ) {
				$image = wp_get_attachment_image_src( $item_id, 'original' );
				if ( $image ) {
					$files[] = $image[0];
				}
			}
		}

		return implode( ', ', $files );

	}

}