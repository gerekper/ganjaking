<?php

namespace ACP\Editing\Model\Post;

use AC\Column;
use ACP\Editing\Service;

/**
 * @deprecated 5.6
 */
class Taxonomy extends Service\Post\Taxonomy {

	public function __construct( Column $column ) {
		$enable_tags = 'on' === $column->get_option( 'enable_term_creation' );

		parent::__construct( $column->get_taxonomy(), $enable_tags );
	}

}