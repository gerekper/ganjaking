<?php

namespace ACP\Editing\Service\Post;

use AC\Request;
use AC\Storage\Transaction;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service;
use ACP\Editing\View;
use ACP\Helper\Select\Paginated\Terms;
use RuntimeException;
use WP_Error;
use WP_Post;

class Taxonomy implements Service, PaginatedOptions {

	/**
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * @var bool
	 */
	private $enable_tags;

	public function __construct( $taxonomy, $enable_tags ) {
		$this->taxonomy = (string) $taxonomy;
		$this->enable_tags = (bool) $enable_tags;
	}

	public function get_view( $context ) {
		$view = new View\AjaxSelect();

		$view->set_multiple( 'post_format' !== $this->taxonomy )
		     ->set_clear_button( true );

		if ( $this->enable_tags ) {
			$view->set_tags( true );
		}

		if ( $context === self::CONTEXT_BULK ) {
			$view->has_methods( true );
		}

		return $view;
	}

	public function get_value( $id ) {
		$terms = get_the_terms( $id, $this->taxonomy );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return [];
		}

		$values = [];

		foreach ( $terms as $term ) {
			$values[ $term->term_id ] = htmlspecialchars_decode( $term->name );
		}

		return $values;
	}

	public function update( Request $request ) {
		$id = $request->get( 'id' );
		$post = get_post( $id );

		if ( ! $post instanceof WP_Post ) {
			throw new RuntimeException( 'Post not found.' );
		}

		$params = $request->get( 'value' );

		if ( ! isset( $params['method'] ) ) {
			$params = [
				'method' => 'replace',
				'value'  => $params,
			];
		}

		$term_ids = array_map( 'absint', array_unique( array_filter( (array) $params['value'] ) ) );

		switch ( $params['method'] ) {
			case 'add':
				$result = $this->set_terms( $post, $term_ids, true );
				break;
			case 'remove':
				$result = wp_remove_object_terms( $id, $term_ids, $this->taxonomy );
				break;
			case 'replace':
			default:
				$result = $this->set_terms( $post, $term_ids );
		}

		if ( is_wp_error( $result ) ) {
			throw new RuntimeException( $result->get_error_message() );
		}

		wp_update_post( [ 'ID' => $post->ID ] );

		return ! empty( $result );
	}

	public function set_terms( $id, $term_ids, $append = false ) {
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
			$result = wp_set_post_categories( $_post->ID, $term_ids, $append );
		} else if ( $this->taxonomy === 'post_tag' && is_object_in_taxonomy( $_post->post_type, 'post_tag' ) ) {
			$result = wp_set_post_tags( $_post->ID, $term_ids, $append );
		} else {
			$result = wp_set_object_terms( $_post->ID, $term_ids, $this->taxonomy, $append );
		}

		if ( is_wp_error( $result ) ) {
			$transaction->rollback();

			throw new RuntimeException( $result->get_error_message() );
		}

		$result = wp_update_post( [ 'ID' => $_post->ID ] );

		if ( is_wp_error( $result ) ) {
			$transaction->rollback();

			return $result;
		}

		$transaction->commit();

		return $term_ids;
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		return new Terms( $search, $page, [ $this->taxonomy ] );
	}

}