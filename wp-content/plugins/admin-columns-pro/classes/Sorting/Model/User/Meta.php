<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use WP_User_Query;

class Meta extends AbstractModel {

	/**
	 * @var string
	 */
	protected $meta_key;

	public function __construct( string $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$query->query_from .= $wpdb->prepare( "
			LEFT JOIN $wpdb->usermeta AS acsort_usermeta 
				ON $wpdb->users.ID = acsort_usermeta.user_id
				AND acsort_usermeta.meta_key = %s
			",
			$this->meta_key
		);

		$query->query_orderby = sprintf( "
			GROUP BY $wpdb->users.ID 
			ORDER BY %s, $wpdb->users.ID %s",
			$this->get_order_by(),
			esc_sql( $this->get_order() )
		);
	}

	protected function get_order_by(): string {
		return SqlOrderByFactory::create( "acsort_usermeta.`meta_value`", $this->get_order(), [ 'cast_type' => (string) CastType::create_from_data_type( $this->data_type ) ] );
	}

}