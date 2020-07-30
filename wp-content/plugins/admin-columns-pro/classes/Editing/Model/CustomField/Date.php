<?php

namespace ACP\Editing\Model\CustomField;

use ACP\Editing\Model;

class Date extends Model\CustomField {

	public function get_edit_value( $id ) {
		$timestamp = ac_helper()->date->strtotime( parent::get_edit_value( $id ) );

		if ( ! $timestamp ) {
			return false;
		}

		return date( 'Y-m-d', $timestamp );
	}

	public function get_view_settings() {
		return [
			'type' => 'date',
		];
	}

}