<?php

namespace ACP\Editing\Service\Post;

use AC\ApplyFilter;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View\Select;

class PostStatus extends Service\BasicStorage {

	/**
	 * @var string
	 */
	private $post_type;

	/**
	 * @var ApplyFilter
	 */
	private $filter_post_status;

	public function __construct( $post_type, ApplyFilter $filter_post_status ) {
		parent::__construct( new Storage\Post\Field( 'post_status' ) );

		$this->post_type = (string) $post_type;
		$this->filter_post_status = $filter_post_status;
	}

	public function get_view( $context ) {
		$options = $this->get_stati_options();

		return empty( $options )
			? false
			: new Select( $options );
	}

	/**
	 * @return []
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