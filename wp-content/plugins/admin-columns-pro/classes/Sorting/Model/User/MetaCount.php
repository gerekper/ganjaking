<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use WP_User_Query;

/**
 * Sort a user list table on the number of times the meta_key is used by a user.
 * @since 5.2
 */
class MetaCount extends AbstractModel {

	/**
	 * @var string
	 */
	protected $meta_key;

	public function __construct( $meta_key ) {
		parent::__construct();

		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$order = $this->get_order();

		$query->query_from .= $wpdb->prepare( "
			LEFT JOIN $wpdb->usermeta AS acsort_usermeta 
				ON $wpdb->users.ID = acsort_usermeta.user_id
				AND acsort_usermeta.meta_key = %s
		", $this->meta_key );

		$query->query_orderby = sprintf( "
			GROUP BY $wpdb->users.ID
			ORDER BY %s, $wpdb->users.ID %s",
			SqlOrderByFactory::create_with_computation( new ComputationType( ComputationType::COUNT ), 'acsort_usermeta.meta_key', $order, true ),
			esc_sql( $this->get_order() )
		);
	}

}