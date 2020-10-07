<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;
use ACP\Editing\Settings\BulkEditing;

class Slug extends Model\Post {

	public function get_edit_value( $id ) {
		return urldecode( get_post_field( 'post_name', $id, 'raw' ) );
	}

	public function get_view_settings() {
		return [
			self::VIEW_TYPE          => 'text',
			self::VIEW_BULK_EDITABLE => false,
			self::VIEW_PLACEHOLDER   => __( 'Enter slug', 'codepress-admin-columns' ),
		];
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_name' => $value ] );
	}

	public function register_settings() {
		parent::register_settings();

		$this->column->remove_setting( BulkEditing::NAME );
	}

}