<?php

namespace ACP\Search\Comparison\Meta;

use AC;
use ACP\Helper\Select;
use ACP\Search\Comparison\Meta;

class Image extends Meta\Media {

	public function get_values( $s, $paged ) {
		$entities = [];

		$ids = AC\Helper\Select\MetaValuesFactory::create( $this->meta_type, $this->meta_key, $this->post_type );

		if ( $ids ) {
			$entities = new Select\Entities\Post( [
				's'              => $s,
				'paged'          => $paged,
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'post__in'       => $ids,
			] );
		}

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Group\Date( new Select\Formatter\PostTitle( $entities ) )
		);
	}

}