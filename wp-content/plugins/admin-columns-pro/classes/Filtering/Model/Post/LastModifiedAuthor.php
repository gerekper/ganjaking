<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class LastModifiedAuthor extends Model\Meta {

	public function get_filtering_data() {
		$data = [];

		if ( $values = $this->get_meta_values() ) {
			foreach ( $values as $user_id ) {
				$data['options'][ $user_id ] = ac_helper()->user->get_display_name( $user_id );
			}
		}

		return $data;
	}

}