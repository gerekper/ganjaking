<?php

namespace ACP\Filtering;

/**
 * @since 4.0
 */
class Helper {

	/**
	 * @param int[] $post_ids Post ID's
	 *
	 * @return array
	 */
	public function get_post_titles( $post_ids ) {
		$titles = [];

		if ( $post_ids ) {
			foreach ( $post_ids as $id ) {
				$post = get_post( $id );

				if ( ! $post ) {
					continue;
				}

				$title = $post->post_title;

				if ( ! $title ) {
					$title = '#' . $post->ID;
				}

				$titles[ $id ] = $title;
			}
		}

		foreach ( ac_helper()->array->get_duplicates( $titles ) as $id => $title ) {
			$titles[ $id ] .= ' (' . get_post_field( 'post_name', $id ) . ')';
		}

		return $titles;
	}

	/**
	 * @param array  $term_ids
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	public function get_term_names( $term_ids, $taxonomy ) {
		$terms = [];

		if ( $term_ids ) {
			foreach ( $term_ids as $term_id ) {
				$term = get_term_by( 'id', $term_id, $taxonomy );

				if ( ! $term ) {
					continue;
				}

				$label = $term->name;

				if ( ! $label ) {
					$label = '#' . $term->term_id;
				}

				$terms[ $term_id ] = $label;
			}
		}

		foreach ( ac_helper()->array->get_duplicates( $terms ) as $term_id => $label ) {
			$terms[ $term_id ] .= ' (' . get_term_field( 'slug', $term_id, $taxonomy ) . ')';
		}

		return $terms;
	}

	/**
	 * Return options for a date filter based on an array of dates
	 *
	 * @param array       $dates
	 * @param string      $display How to display the date
	 * @param string      $format  Format of the date
	 * @param string|null $key
	 *
	 * @return array
	 */
	public function get_date_options( array $dates, $display, $format = 'Y-m-d', $key = null ) {
		$options = [];

		switch ( $display ) {
			case 'yearly':
				$display = 'Y';
				$key = 'Y';

				break;
			case 'monthly':
				$display = 'F Y';
				$key = 'Ym';

				break;
			case 'daily':
				$display = 'j F Y';
				$key = 'Ymd';

				break;
		}

		if ( ! $key ) {
			$key = $format;
		}

		foreach ( $dates as $date ) {
			$timestamp = ac_helper()->date->get_timestamp_from_format( $date, $format );

			if ( ! $timestamp ) {
				continue;
			}

			$option = date( $key, $timestamp );

			if ( ! isset( $options[ $key ] ) ) {
				$options[ $option ] = ac_format_date( $display, $timestamp );
			}
		}

		ksort( $options, SORT_NUMERIC );

		$options = array_reverse( $options, true );

		return $options;
	}

	/**
	 * @param string $format
	 *
	 * @return array|false
	 */
	public function get_date_options_relative( $format ) {
		$options = [];

		switch ( $format ) {
			case 'future_past':
				$options = [
					'future' => __( 'Future dates', 'codepress-admin-columns' ),
					'past'   => __( 'Past dates', 'codepress-admin-columns' ),
				];

				break;
		}

		if ( empty( $options ) ) {
			return false;
		}

		return $options;
	}

}