<?php

namespace ACA\Pods\Filtering;

use ACA\Pods\Field;
use ACA\Pods\Filtering;
use WP_Post;

class Pick extends Filtering {

	public function get_filtering_data() {
		$data = [
			'empty_option' => true,
		];

		$field = $this->column->get_field();

		if ( $field instanceof Field\Pick ) {
			$options = $this->get_meta_values();

			$data['options'] = $this->format_options_by_dataset( $options, $field->get_options() );
		}

		return $data;
	}

	/**
	 * @param WP_Post[] | int[] $post_ids
	 *
	 * @return array
	 */
	protected function get_titles( $post_ids ) {
		$titles = [];

		foreach ( (array) $post_ids as $k => $post_id ) {
			$titles[ $post_id ] = ac_helper()->post->get_raw_post_title( $post_id );
		}

		return array_filter( $titles );
	}

	protected function format_options_by_dataset( $values, $dataset ) {
		$options = [];
		foreach ( $values as $key ) {
			if ( isset( $dataset[ $key ] ) ) {
				$options[ $key ] = $dataset[ $key ];
			}
		}

		return $options;
	}

}