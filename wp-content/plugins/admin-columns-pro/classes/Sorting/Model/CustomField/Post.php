<?php

namespace ACP\Sorting\Model\CustomField;

use AC;
use ACP\Sorting\Model;

class Post extends Model\CustomField {

	public function get_sorting_vars() {
		$setting = $this->column->get_setting( 'post' );

		if ( ! $setting instanceof AC\Settings\Column\Post ) {
			return [];
		}

		$ids = [];

		foreach ( $this->strategy->get_results() as $id ) {
			$title = false;

			$post_ids = ac_helper()->array->get_integers_from_mixed( $this->column->get_raw_value( $id ) );

			if ( $post_ids ) {

				// sort by first post
				$post_id = $post_ids[0];

				$title = $setting->format( $post_id, false );
			}

			$ids[ $id ] = $title;
		}

		return [
			'ids' => $this->sort( $ids ),
		];
	}

}