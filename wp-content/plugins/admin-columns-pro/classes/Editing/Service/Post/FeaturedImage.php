<?php

namespace ACP\Editing\Service\Post;

use AC\Request;
use ACP\Editing\Service;
use ACP\Editing\View;

class FeaturedImage implements Service {

	public function get_view( $context ) {
		return ( new View\Image() )->set_clear_button( true );
	}

	public function get_value( $id ) {
		return get_post_thumbnail_id( $id );
	}

	public function update( Request $request ) {
		$value = $request->get( 'value' );
		$id = (int) $request->get( 'id' );

		return $value
			? (bool) set_post_thumbnail( $id, (int) $value )
			: delete_post_thumbnail( $id );
	}

}