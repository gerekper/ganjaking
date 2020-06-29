<?php

namespace ACP\Sorting\Model\Comment;

use ACP\Sorting\AbstractModel;

class Response extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'comments_clauses', [ $this, 'comments_clauses_callback' ] );

		return [];
	}

	public function comments_clauses_callback( $pieces ) {
		global $wpdb;

		$pieces['join'] .= " INNER JOIN $wpdb->posts acsort_posts ON acsort_posts.ID = $wpdb->comments.comment_post_ID ";
		$pieces['orderby'] = "acsort_posts.post_title " . esc_sql( $this->get_order() );

		remove_filter( 'comments_clauses', [ $this, __FUNCTION__ ] );

		return $pieces;
	}

}