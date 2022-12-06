<?php
declare( strict_types=1 );

namespace ACA\WC\Helper\Select\Paginated;

use AC\Helper\Select\Options\Paginated;
use ACA\WC\Helper\Select;

class Products extends Paginated {

	public function __construct( string $search, int $paged, array $post_types = [] ) {
		$args = [ 's' => $search, 'paged' => $paged ];

		if ( $post_types ) {
			$args['post_type'] = $post_types;
		}

		$entities = new Select\Entities\Product( $args );

		parent::__construct(
			$entities,
			new Select\Formatter\ProductTitleAndSKU( $entities )
		);
	}

}