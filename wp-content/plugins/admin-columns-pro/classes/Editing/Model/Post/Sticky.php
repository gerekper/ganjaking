<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class Sticky extends Model\Post {

	public function get_view_settings() {
		return [
			'type'    => 'togglable',
			'options' => [
				'no'  => __( 'No', 'codepress-admin-columns' ),
				'yes' => __( 'Yes', 'codepress-admin-columns' ),
			],
		];
	}

	public function get_edit_value( $id ) {
		$value = parent::get_edit_value( $id );

		return $value ? 'yes' : 'no';
	}

	public function save( $id, $value ) {
		if ( 'yes' === $value ) {
			stick_post( $id );
		} else {
			unstick_post( $id );
		}

		return $this->update_post( $id );
	}

}