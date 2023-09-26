<?php

namespace ACP\Editing\Storage\Comment;

use AC\MetaType;
use ACP\Editing\Storage;

class Meta extends Storage\Meta {

	public function __construct( $meta_key ) {
		parent::__construct( $meta_key, new MetaType( MetaType::COMMENT ) );
	}

}