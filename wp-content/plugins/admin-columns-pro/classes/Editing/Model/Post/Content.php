<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing;
use ACP\Editing\Model;
use ACP\Editing\Settings\BulkEditing;

class Content extends Model\Post {

	public function get_view_settings() {
		/* @var Editing\Settings\Content $setting */
		$setting = $this->column->get_setting( Editing\Settings\Content::NAME );

		return [
			self::VIEW_BULK_EDITABLE => false,
			self::VIEW_TYPE          => $setting->get_editable_type(),
		];
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_content' => $value ] );
	}

	public function register_settings() {
		parent::register_settings();

		$this->column->add_setting( new Editing\Settings\Content( $this->column ) );
		$this->column->remove_setting( BulkEditing::NAME );
	}

}