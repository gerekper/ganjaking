<?php

namespace ACP\Sorting\Model\User\RelatedMeta;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\DataType;
use WP_User_Query;

class UserMeta extends AbstractModel {

	/**
	 * @var string
	 */
	private $meta_field;

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( $meta_field, $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->meta_field = $meta_field;
		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		$order = $this->get_order();

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$query->query_fields .= ", acsort_usermeta_related.meta_value AS acsort_related_field";
		$query->query_from .= $wpdb->prepare( "
			$join_type JOIN {$wpdb->usermeta} AS acsort_usermeta ON acsort_usermeta.user_id = {$wpdb->users}.ID
				AND acsort_usermeta.meta_key = %s
			$join_type JOIN {$wpdb->users} AS acsort_users ON acsort_users.ID = acsort_usermeta.meta_value
			$join_type JOIN {$wpdb->usermeta} AS acsort_usermeta_related ON acsort_usermeta_related.user_id = acsort_users.ID
				AND acsort_usermeta_related.meta_key = %s
		", $this->meta_key, $this->meta_field );

		if ( ! $this->show_empty ) {
			$query->query_from .= " AND acsort_usermeta_related.meta_value <>''";
		}

		$query->query_orderby = "
			GROUP BY {$wpdb->users}.ID
			ORDER BY acsort_related_field $order, {$wpdb->users}.ID $order
		";

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );
	}

}