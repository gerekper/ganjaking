<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use WP_User_Query;

class Meta extends AbstractModel {

	/**
	 * @var string
	 */
	protected $meta_key;

	/**
	 * @param string        $meta_key
	 * @param DataType|null $data_type
	 */
	public function __construct( $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$from = $wpdb->prepare( "
			{$join_type} JOIN {$wpdb->usermeta} AS acsort_usermeta 
				ON {$wpdb->users}.ID = acsort_usermeta.user_id
				AND acsort_usermeta.meta_key = %s
		", $this->meta_key );

		if ( ! $this->show_empty ) {
			$from .= " AND acsort_usermeta.meta_value <> ''";
		}

		$query->query_from .= $from;
		$query->query_orderby = $this->get_order_by();

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );
	}

	/**
	 * @return string
	 */
	protected function get_order_by() {
		global $wpdb;

		$order = esc_sql( $this->get_order() );
		$cast_type = CastType::create_from_data_type( $this->data_type )->get_value();

		return "ORDER BY CAST( acsort_usermeta.meta_value AS {$cast_type} ) $order, {$wpdb->users}.ID $order";

	}

}