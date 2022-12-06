<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\ApplyFilter;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class PostStatus extends BasicStorage {

	/**
	 * @var string
	 */
	private $post_type;

	/**
	 * @var ApplyFilter\PostStatus
	 */
	private $filter_post_status;

	public function __construct( $post_type, ApplyFilter\PostStatus $filter_post_status ) {
		parent::__construct( new Storage\Post\Field( 'post_status' ) );

		$this->post_type = (string) $post_type;
		$this->filter_post_status = $filter_post_status;
	}

	public function get_view( string $context ): ?View {
		$options = $this->get_stati_options();

		return empty( $options )
			? null
			: new View\Select( $options );
	}

	/**
	 * @return array
	 */
	private function get_stati_options() {
		$post_type_object = get_post_type_object( $this->post_type );

		if ( ! $post_type_object || ! current_user_can( $post_type_object->cap->publish_posts ) ) {
			return [];
		}

		$stati = $this->filter_post_status->apply_filters( get_post_stati( [ 'internal' => 0 ], 'objects' ) );

		if ( empty( $stati ) ) {
			return $stati;
		}

		$options = [];

		foreach ( $stati as $name => $status ) {
			if ( ! in_array( $name, [ 'future', 'trash' ] ) ) {
				$options[ $name ] = $status->label;
			}
		}

		return $options;
	}

}