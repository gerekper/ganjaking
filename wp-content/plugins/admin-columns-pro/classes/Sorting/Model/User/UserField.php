<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\DataType;
use WP_User_Query;

class UserField extends AbstractModel {

	/**
	 * @var string
	 */
	protected $field;

	public function __construct( $field, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->field = (string) $field;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$query->query_orderby = sprintf( "
				ORDER BY %s, user_login %s
			",
			SqlOrderByFactory::create( "$wpdb->users.`$this->field`", $this->get_order() ),
			esc_sql( $this->get_order() )
		);
	}

}