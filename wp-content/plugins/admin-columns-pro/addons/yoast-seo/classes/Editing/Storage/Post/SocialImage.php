<?php

namespace ACA\YoastSeo\Editing\Storage\Post;

use ACP;

class SocialImage implements ACP\Editing\Storage {

	/**
	 * @var string
	 */
	private $meta_key_id;

	/**
	 * @var string
	 */
	private $meta_key_url;

	public function __construct( $meta_key_id, $meta_key_url ) {
		$this->meta_key_id = $meta_key_id;
		$this->meta_key_url = $meta_key_url;
	}

	public function get( int $id ) {
		return get_post_meta( $id, $this->meta_key_id, true );
	}

	public function update( int $id, $data ): bool {
		update_post_meta( $id, $this->meta_key_id, $data );

		return update_post_meta( $id, $this->meta_key_url, $data ? wp_get_attachment_url( $data ) : '' );
	}

}