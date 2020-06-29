<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class Status extends Model\Post {

	public function get_view_settings() {
		$post_type_object = get_post_type_object( $this->column->get_post_type() );

		if ( ! $post_type_object || ! current_user_can( $post_type_object->cap->publish_posts ) ) {
			return false;
		}

		$stati = $this->get_editable_statuses();

		if ( ! $stati ) {
			return false;
		}

		$options = [];

		foreach ( $stati as $name => $status ) {
			if ( in_array( $name, [ 'future', 'trash' ] ) ) {
				continue;
			}

			$options[ $name ] = $status->label;
		}

		return [
			'type'    => 'select',
			'options' => $options,
		];
	}

	private function get_editable_statuses() {
		return apply_filters( 'acp/editing/post_statuses', get_post_stati( [ 'internal' => 0 ], 'objects' ), $this->column );
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_status' => $value ] );
	}

}