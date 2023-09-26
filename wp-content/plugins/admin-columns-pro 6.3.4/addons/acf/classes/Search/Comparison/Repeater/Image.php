<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use AC;
use ACP;

class Image extends Media {

	public function __construct( $meta_type, $parent_key, $sub_key ) {
		parent::__construct( $meta_type, $parent_key, $sub_key, 'image' );
	}

	public function get_values( $s, $paged ) {
		$entities = $this->get_search_entities( $s, $paged );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Group\Date( new ACP\Helper\Select\Formatter\PostTitle( $entities ) )
		);
	}

}