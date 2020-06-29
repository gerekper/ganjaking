<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
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

	/**
	 * @param WP_User_Query $query
	 */
	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$order = $this->get_order();

		$query->query_fields .= ", COUNT( acsort_usermeta.meta_key ) AS acsort_metacount";
		$query->query_from .= $wpdb->prepare( "
			{$join_type} JOIN {$wpdb->usermeta} AS acsort_usermeta 
				ON {$wpdb->users}.ID = acsort_usermeta.user_id
				AND acsort_usermeta.meta_key = %s
		", $this->meta_key );

		if ( ! $this->show_empty ) {
			$query->query_from .= " AND acsort_usermeta.meta_value <> ''";
		}

		$query->query_orderby = "
			GROUP BY {$wpdb->users}.ID
			ORDER BY acsort_metacount $order, {$wpdb->users}.ID $order
		";

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );
	}

}