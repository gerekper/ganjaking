<?php

namespace ACP\Helper\Select\Entities;

use AC;
use ACP\Helper\Select\Value;
use WP_Comment_Query;

class Comment extends AC\Helper\Select\Entities
	implements AC\Helper\Select\Paginated {

	/**
	 * @var WP_Comment_Query
	 */
	protected $query;

	/**
	 * @param array                  $args
	 * @param AC\Helper\Select\Value $value
	 */
	public function __construct( array $args = [], AC\Helper\Select\Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\Comment();
		}

		$args = array_merge( [
			'number'        => 30,
			'fields'        => 'ID',
			'orderby'       => 'comment_date_gmt',
			'paged'         => 1,
			'search'        => null,
			'no_found_rows' => false,
		], $args );

		$args['offset'] = ( $args['paged'] - 1 ) * $args['number'];

		$this->query = new WP_Comment_Query( $args );

		parent::__construct( $this->query->get_comments(), $value );
	}

	public function get_total_pages() {
		return $this->query->max_num_pages;
	}

	public function get_page() {
		return $this->query->query_vars['paged'];
	}

	public function is_last_page() {
		return $this->get_total_pages() <= $this->get_page();
	}

}