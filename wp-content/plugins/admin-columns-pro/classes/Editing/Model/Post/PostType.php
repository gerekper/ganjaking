<?php

namespace ACP\Editing\Model\Post;

use AC;
use ACP\Editing\Model;
use ACP\Editing\RemoteOptions;
use ACP\Helper\Select;

class PostType extends Model\Post implements RemoteOptions {

	public function get_view_settings() {
		return [
			'type' => 'select2_remote',
		];
	}

	public function get_remote_options( $id = null ) {
		$options = new Select\Group\PostTypeType(
			new Select\Formatter\PostTypeLabel( new Select\Entities\PostType() )
		);

		return new AC\Helper\Select\Options( $options->get_copy() );
	}

	public function save( $id, $value ) {
		return false !== set_post_type( $id, $value );
	}

}