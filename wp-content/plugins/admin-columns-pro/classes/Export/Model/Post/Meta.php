<?php

namespace ACP\Export\Model\Post;

use AC;
use ACP\Export\Model;

/**
 * @since 5.7
 */
class Meta extends Model\Meta {

	public function __construct( AC\Column $column, $meta_key ) {
		parent::__construct( $column, new AC\MetaType( AC\MetaType::POST ), $meta_key );
	}

}