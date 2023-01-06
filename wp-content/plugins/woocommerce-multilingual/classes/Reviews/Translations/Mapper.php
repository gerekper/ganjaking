<?php

namespace WCML\Reviews\Translations;

class Mapper {

	/**
	 * @var \wpdb
	 */
	private $wpdb;

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function registerMissingReviewStrings() {
		foreach ( $this->getUnregisteredReviews() as $review ) {
			FrontEndHooks::registerReviewString( $review );
		}
	}

	/**
	 * @return int
	 */
	public function countMissingReviewStrings() {
		return count( $this->getUnregisteredReviews() );
	}

	/**
	 * @return array
	 */
	private function getUnregisteredReviews() {
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$q = "SELECT c.comment_ID, c.comment_post_ID, c.comment_content, c.comment_type, tr.language_code, st.name, st.value
				FROM {$this->wpdb->comments} AS c
				LEFT JOIN {$this->wpdb->prefix}icl_translations AS tr
					ON tr.element_id = c.comment_post_ID AND tr.element_type = 'post_product'
				LEFT JOIN {$this->wpdb->prefix}icl_strings AS st
					ON c.comment_content = st.value
						AND st.context = '" . FrontEndHooks::CONTEXT . "'
				WHERE c.comment_type = '" . FrontEndHooks::COMMENT_TYPE . "'
					AND st.name IS null";

		return (array) $this->wpdb->get_results( $q );
	}
}
