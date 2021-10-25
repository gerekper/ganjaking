<?php

namespace ACP\Sorting\Model\CustomField;

use AC;
use ACP\Sorting\Model;

class User extends Model\CustomField {

	public function get_sorting_vars() {
		$setting = $this->column->get_setting( 'user' );

		if ( ! $setting instanceof AC\Settings\Column\User ) {
			return [];
		}

		$ids = [];

		foreach ( $this->strategy->get_results() as $id ) {
			$name = false;

			$user_ids = ac_helper()->array->get_integers_from_mixed( $this->column->get_raw_value( $id ) );

			if ( $user_ids ) {

				// sort by first user
				$name = $setting->get_user_name( $user_ids[0] );
			}

			$ids[ $id ] = $name;
		}

		return [
			'ids' => $this->sort( $ids ),
		];
	}

}