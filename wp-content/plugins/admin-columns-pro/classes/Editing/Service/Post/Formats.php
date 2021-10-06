<?php

namespace ACP\Editing\Service\Post;

use AC\Request;
use ACP\Editing\Service;
use ACP\Editing\View;
use RuntimeException;

class Formats implements Service {

	public function get_view( $context ) {
		return new View\Select( get_post_format_strings() );
	}

	public function get_value( $id ) {
		return get_post_format( $id );
	}

	public function update( Request $request ) {
		$result = set_post_format( (int) $request->get( 'id' ), $request->get( 'value' ) );

		if ( ! $result ) {
			return false;
		}

		if ( is_wp_error( $result ) ) {
			throw new RuntimeException( $result->get_error_message() );
		}

		return true;
	}

}