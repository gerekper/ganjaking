<?php

namespace ACP\Editing\Service\Post;

use AC;
use AC\Request;
use ACP\Editing\RemoteOptions;
use ACP\Editing\Service;
use ACP\Editing\View\RemoteSelect;
use ACP\Helper\Select;

class PostType implements Service, RemoteOptions {

	public function get_value( $id ) {
		return get_post_type( $id );
	}

	public function get_view( $context ) {
		return new RemoteSelect();
	}

	public function update( Request $request ) {
		return false !== set_post_type( (int) $request->get( 'id' ), $request->get( 'value' ) );
	}

	public function get_remote_options( $id = null ) {
		$options = new Select\Group\PostTypeType(
			new Select\Formatter\PostTypeLabel( new Select\Entities\PostType() )
		);

		return new AC\Helper\Select\Options( $options->get_copy() );
	}

}