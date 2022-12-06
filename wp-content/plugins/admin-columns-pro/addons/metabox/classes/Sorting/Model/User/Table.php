<?php

namespace ACA\MetaBox\Sorting\Model\User;

use ACA\MetaBox\Sorting\TableOrderByFactory;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\DataType;
use WP_User_Query;

class Table extends AbstractModel {

	/**
	 * @var string
	 */
	private $table_name;

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( $table_name, $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->table_name = (string) $table_name;
		$this->meta_key = (string) $meta_key;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$query->query_from .= sprintf( "
			LEFT JOIN %s AS acsort_ct 
				ON acsort_ct.ID = $wpdb->users.ID
			",
			esc_sql( $this->table_name )
		);
		$query->query_orderby = sprintf( "
			ORDER BY %s, 
			$wpdb->users.ID %s",
			TableOrderByFactory::create( $this->meta_key, $this->data_type, $this->get_order() ),
			esc_sql( $this->get_order() )
		);
	}

}