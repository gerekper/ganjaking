<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\DataType;
use WP_User_Query;

class UserField extends AbstractModel {

	/**
	 * @var string
	 */
	protected $field;

	public function __construct( $field, DataType $data_type = null, $show_empty = null ) {
		parent::__construct( $data_type, $show_empty );

		$this->field = (string) $field;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );

		$query->query_orderby = sprintf( "ORDER BY %s.`%s` %s", $wpdb->users, esc_sql( $this->field ), $query->query_vars['order'] );

		if ( ! $this->show_empty ) {
			$query->query_where .= sprintf( " AND %s.`%s` <> ''", $wpdb->users, esc_sql( $this->field ) );
		}
	}

}