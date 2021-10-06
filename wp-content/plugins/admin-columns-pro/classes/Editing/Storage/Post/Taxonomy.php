<?php

namespace ACP\Editing\Storage\Post;

use AC\Storage\Transaction;
use ACP\Editing\Storage;
use RuntimeException;
use WP_Error;

class Taxonomy implements Storage {

	/**
	 * @var string
	 */
	private $taxonomy;

	/**
	 * @var bool
	 */
	private $append;

	public function __construct( $taxonomy, $append = false ) {
		$this->taxonomy = $taxonomy;
		$this->append = $append;
	}

	public function get( $id ) {
		$terms = get_the_terms( $id, $this->taxonomy );

		if ( ! $terms && is_wp_error( $terms ) ) {
			return [];
		}

		$values = [];

		foreach ( $terms as $term ) {
			$values[ $term->term_id ] = htmlspecialchars_decode( $term->name );
		}

		return $values;
	}

	public function update( $id, $term_ids ) {
		$_post = get_post( $id );

		if ( ! $_post || ! taxonomy_exists( $this->taxonomy ) ) {
			return [];
		}

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

			$term = get_term_by( 'name', $term_id, $this->taxonomy );

			if ( $term ) {
				$term_ids[ $index ] = $term->term_id;
			} else {
				$created_term = wp_insert_term( $term_id, $this->taxonomy );

				if ( $created_term instanceof WP_Error ) {
					$transaction->rollback();

					throw new RuntimeException( $created_term->get_error_message() );
				}

				$created_term_ids[] = $created_term['term_id'];
			}
		}

		// merge
		$term_ids = array_merge( $created_term_ids, $term_ids );

		//to make sure the terms IDs is integers:
		$term_ids = array_map( 'intval', (array) $term_ids );
		$term_ids = array_unique( $term_ids );

		if ( $this->taxonomy === 'category' && is_object_in_taxonomy( $_post->post_type, 'category' ) ) {
			$result = wp_set_post_categories( $_post->ID, $term_ids, $this->append );
		} else if ( $this->taxonomy === 'post_tag' && is_object_in_taxonomy( $_post->post_type, 'post_tag' ) ) {
			$result = wp_set_post_tags( $_post->ID, $term_ids, $this->append );
		} else {
			$result = wp_set_object_terms( $_post->ID, $term_ids, $this->taxonomy, $this->append );
		}

		if ( is_wp_error( $result ) ) {
			$transaction->rollback();

			throw new RuntimeException( $result->get_error_message() );
		}

		$result = wp_update_post( [ 'ID' => $_post->ID ] );

		if ( is_wp_error( $result ) ) {
			$transaction->rollback();

			throw new RuntimeException( $result->get_error_message() );
		}

		$transaction->commit();

		return $term_ids;
	}

}