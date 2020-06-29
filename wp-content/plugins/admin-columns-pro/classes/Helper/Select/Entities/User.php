<?php

namespace ACP\Helper\Select\Entities;

use AC;
use ACP\Helper\Select\Value;
use WP_User_Query;

class User extends AC\Helper\Select\Entities
	implements AC\Helper\Select\Paginated {

	/**
	 * @var WP_User_Query
	 */
	protected $query;

	/** @var string */
	private $searchterm;

	/**
	 * @param array                  $args
	 * @param AC\Helper\Select\Value $value
	 */
	public function __construct( array $args = [], AC\Helper\Select\Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\User();
		}

		$args = array_merge( [
			'orderby'        => 'display_name',
			'search_columns' => [ 'ID', 'user_login', 'user_nicename', 'user_email' ],
			'number'         => 30,
			'paged'          => 1,
			'search'         => null,
		], $args );

		$this->searchterm = $args['search'];

		if ( $args['search'] ) {
			$args['search'] = sprintf( '*%s*', trim( $args['search'], '*' ) );
		}

		add_action( 'pre_user_query', [ $this, 'callback_meta_query' ], 1 );

		$this->query = new WP_User_Query( $args );

		parent::__construct( $this->query->get_results(), $value );
	}

	/**
	 * Add meta query for user's first and last name
	 *
	 * @param WP_User_Query $query
	 */
	public function callback_meta_query( WP_User_Query $query ) {
		remove_action( 'pre_user_query', __FUNCTION__, 1 );

		if ( ! $this->searchterm ) {
			return;
		}

		global $wpdb;

		$query->query_from .= "\n INNER JOIN {$wpdb->usermeta} AS um ON um.user_id = {$wpdb->users}.ID";
		$query->query_where .= $wpdb->prepare( "\n OR ( um.meta_key = 'first_name' && um.meta_value LIKE %s )", '%' . $wpdb->esc_like( $this->searchterm ) . '%' );
		$query->query_where .= $wpdb->prepare( "\n OR ( um.meta_key = 'last_name' && um.meta_value LIKE %s )", '%' . $wpdb->esc_like( $this->searchterm ) . '%' );
		$query->query_where .= " GROUP BY {$wpdb->users}.ID";
	}

	/**
	 * @inheritDoc
	 */
	public function get_total_pages() {
		$per_page = $this->query->query_vars['number'];

		return ceil( $this->query->get_total() / $per_page );
	}

	/**
	 * @inheritDoc
	 */
	public function get_page() {
		return $this->query->query_vars['paged'];
	}

	/**
	 * @inheritDoc
	 */
	public function is_last_page() {
		return $this->get_total_pages() <= $this->get_page();
	}

}