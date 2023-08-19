<?php

namespace ACP\Export\Model\Term;

use ACP\Export\Service;

class Slug implements Service {

	public function get_value( $id ) {
		$term = get_term( $id );

		return (string) apply_filters( 'editable_slug', $term->slug, $term );
	}

}