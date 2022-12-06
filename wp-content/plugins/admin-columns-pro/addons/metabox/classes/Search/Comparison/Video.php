<?php

namespace ACA\MetaBox\Search\Comparison;

use AC;
use ACP;

class Video extends ACP\Search\Comparison\Meta\Image {

	public function get_values( $s, $paged ) {
		$entities = [];

		$ids = AC\Helper\Select\MetaValuesFactory::create( $this->meta_type, $this->meta_key, $this->post_type );

		if ( $ids ) {
			$entities = new ACP\Helper\Select\Entities\Post( [
				's'              => $s,
				'paged'          => $paged,
				'post_type'      => 'attachment',
				'post_mime_type' => 'video',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'post__in'       => $ids,
			] );
		}

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Group\Date( new ACP\Helper\Select\Formatter\PostTitle( $entities ) )
		);
	}

}