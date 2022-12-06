<?php

namespace ACA\BP\Sorting;

use ACA\BP\Column;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use WP_User_Query;

class Profile extends AbstractModel {

	/**
	 * @var Column\Profile
	 */
	protected $column;

	protected $datatype;

	public function __construct( Column\Profile $column, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->column = $column;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb, $bp;

		$from = $wpdb->prepare( "
			LEFT JOIN {$bp->profile->table_name_data} as acsort_pd 
				ON $wpdb->users.ID = acsort_pd.user_id AND acsort_pd.field_id = %d
		", $this->column->get_buddypress_field_id() );

		$query->query_from .= $from;
		$query->query_orderby = sprintf( "GROUP BY $wpdb->users.ID ORDER BY %s",
			SqlOrderByFactory::create( 'acsort_pd.value', $this->get_order(), [ 'cast_type' => (string) CastType::create_from_data_type( $this->data_type ) ] )
		);

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );
	}

}