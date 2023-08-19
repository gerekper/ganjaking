<?php

namespace ACA\BeaverBuilder\Service;

use AC\Registerable;

class PostTypes implements Registerable {

	public const POST_TYPE_TEMPLATE = 'fl-builder-template';

	public function register(): void
    {
		add_filter( 'ac/post_types', [ $this, 'deregister_global_post_type' ] );
	}

	public function deregister_global_post_type( $post_types ) {
		unset( $post_types[ self::POST_TYPE_TEMPLATE ] );

		return $post_types;
	}

}