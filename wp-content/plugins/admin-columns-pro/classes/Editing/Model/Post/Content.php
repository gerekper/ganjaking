<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing;
use ACP\Editing\Model;

class Content extends Model\Post {

	public function get_view_settings() {
		/* @var Editing\Settings\Content $setting */
		$setting = $this->column->get_setting( 'edit' );

		return [
			'bulk_editable' => false,
			'type'          => $setting->get_editable_type(),
		];
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, [ 'post_content' => $value ] );
	}

	public function register_settings() {
		parent::register_settings();

		$this->column->add_setting( new Editing\Settings\Content( $this->column ) );
	}

}