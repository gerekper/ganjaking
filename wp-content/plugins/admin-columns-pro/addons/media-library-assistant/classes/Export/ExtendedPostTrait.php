<?php

namespace ACA\MLA\Export;

use MLAData;
use WP_Post;

trait ExtendedPostTrait {

	public function get_extended_post( int $id ): ?WP_Post {
		$data = MLAData::mla_get_attachment_by_id( $id );

		return $data
			? new WP_Post( (object) $data )
			: null;
	}

}