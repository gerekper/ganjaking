<?php

namespace ACP\Sorting\Strategy;

use ACP\Sorting\Strategy;
use WP_Comment_Query;

final class Comment extends Strategy {

	/**
	 * @var WP_Comment_Query
	 */
	protected $query;

	public function manage_sorting() {
		add_action( 'pre_get_comments', [ $this, 'handle_sorting_request' ] );
	}

	private function set_comment_query( WP_Comment_Query $query ) {
		$this->query = $query;
	}

	/**
	 * @return WP_Comment_Query
	 */
	public function get_query() {
		return $this->query;
	}

	public function get_order() {
		return $this->get_query_var( 'order' );
	}

	public function get_query_var( $key ) {
		if ( $this->query instanceof WP_Comment_Query && isset( $this->query->query_vars[ $key ] ) ) {
			return $this->query->query_vars[ $key ];
		}

		return null;
	}

	public function get_results( array $args = [] ) {
		$defaults = [
			'fields' => 'ids',
		];

		$query = new WP_Comment_Query( array_merge( $defaults, $args ) );

		return $query->get_comments();
	}

	/**
	 * @param WP_Comment_Query $query
	 */
	public function handle_sorting_request( WP_Comment_Query $query ) {
		$this->set_comment_query( $query );

		// check query conditions
		if ( ! $this->get_query_var( 'orderby' ) ) {
			return;
		}

		// run only once
		remove_action( 'pre_get_comments', [ $this, __FUNCTION__ ] );

		foreach ( $this->model->get_sorting_vars() as $key => $value ) {
			if ( $this->is_universal_id( $key ) ) {
				$key = 'comment__in';
			}

			$query->query_vars[ $key ] = $value;
		}

		// pre-sorting done with an array
		$comment__in = $this->get_query_var( 'comment__in' );

		if ( ! empty( $comment__in ) ) {
			$query->query_vars['orderby'] = 'comment__in';
		}
	}

}