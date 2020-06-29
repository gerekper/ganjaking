<?php

namespace ACP\Editing\Model\Post;

use AC;
use AC\Storage\Transaction;
use ACP\Editing\Model;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Settings;
use ACP\Helper\Select;
use WP_Error;

class Taxonomy extends Model implements PaginatedOptions {

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public function get_edit_value( $id ) {
		$values = [];

		$terms = get_the_terms( $id, $this->column->get_taxonomy() );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$values[ $term->term_id ] = htmlspecialchars_decode( $term->name );
			}
		}

		return $values;
	}

	private function get_taxonomy_object() {
		return get_taxonomy( $this->column->get_taxonomy() );
	}

	public function get_view_settings() {
		$taxonomy = $this->get_taxonomy_object();

		if ( ! $taxonomy ) {
			return false;
		}

		$data = [
			'multiple'      => true,
			'ajax_populate' => true,
		];

		if ( 'on' === $this->column->get_option( 'enable_term_creation' ) ) {
			$data['tags'] = true;
		}

		if ( 'post_format' === $taxonomy->name ) {
			$data = [
				'multiple' => false,
			];
		}

		$data['type'] = 'taxonomy';
		$data['clear_button'] = true;

		return $data;
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		$entities = new Select\Entities\Taxonomy( [
			'search'   => $search,
			'page'     => $page,
			'taxonomy' => $this->column->get_taxonomy(),
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\TermName( $entities )
		);

	}

	/**
	 * @return array
	 */
	protected function get_term_options() {
		$entities = new Select\Entities\Taxonomy( [
			'number'   => 200,
			'taxonomy' => $this->column->get_taxonomy(),
		] );

		$results = new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Formatter\TermName( $entities )
		);

		$options = [];

		foreach ( $results as $result ) {
			$options[ $result->get_value() ] = $result->get_label();
		}

		return $options;
	}

	public function save( $id, $value ) {
		if ( ! isset( $value['save_strategy'] ) ) {
			$value = [
				'save_strategy' => false,
				'terms'         => $value,
			];
		}

		switch ( $value['save_strategy'] ) {
			case 'add':
				return $this->set_terms( $id, $value['terms'], $this->column->get_taxonomy(), true );
			case 'remove':
				return wp_remove_object_terms( $id, array_map( 'intval', $value['terms'] ), $this->column->get_taxonomy() );
			case 'replace':
			default:
				return $this->set_terms( $id, $value['terms'], $this->column->get_taxonomy() );
		}
	}

	/**
	 * Register editing settings
	 */
	public function register_settings() {
		parent::register_settings();

		$this->column->add_setting( new Settings\Taxonomy( $this->column ) );
	}

	/**
	 * @param int       $post
	 * @param int[]|int $term_ids
	 * @param string    $taxonomy
	 * @param bool      $append
	 *
	 * @return array|false
	 */
	protected function set_terms( $post, $term_ids, $taxonomy, $append = false ) {
		$post = get_post( $post );

		if ( ! $post || ! taxonomy_exists( $taxonomy ) ) {
			return [];
		}

		// Filter list of terms
		if ( empty( $term_ids ) ) {
			$term_ids = [];
		}

		$transaction = new Transaction();

		$term_ids = array_unique( (array) $term_ids );

		// maybe create terms?
		$created_term_ids = [];

		foreach ( (array) $term_ids as $index => $term_id ) {
			if ( is_numeric( $term_id ) ) {
				continue;
			}

			$term = get_term_by( 'name', $term_id, $taxonomy );

			if ( $term ) {
				$term_ids[ $index ] = $term->term_id;
			} else {
				$created_term = wp_insert_term( $term_id, $taxonomy );

				if ( $created_term instanceof WP_Error ) {
					$transaction->rollback();

					return false;
				}

				$created_term_ids[] = $created_term['term_id'];
			}
		}

		// merge
		$term_ids = array_merge( $created_term_ids, $term_ids );

		//to make sure the terms IDs is integers:
		$term_ids = array_map( 'intval', (array) $term_ids );
		$term_ids = array_unique( $term_ids );

		if ( $taxonomy === 'category' && is_object_in_taxonomy( $post->post_type, 'category' ) ) {
			$result = wp_set_post_categories( $post->ID, $term_ids, $append );
		} else if ( $taxonomy === 'post_tag' && is_object_in_taxonomy( $post->post_type, 'post_tag' ) ) {
			$result = wp_set_post_tags( $post->ID, $term_ids, $append );
		} else {
			$result = wp_set_object_terms( $post->ID, $term_ids, $taxonomy, $append );
		}

		if ( is_wp_error( $result ) ) {
			$this->set_error( $result );

			$transaction->rollback();

			return false;
		}

		$result = wp_update_post( [ 'ID' => $post->ID ] );

		if ( is_wp_error( $result ) ) {
			$this->set_error( $result );

			$transaction->rollback();

			return false;
		}

		$transaction->commit();

		return $term_ids;
	}

}