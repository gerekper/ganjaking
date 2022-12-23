<?php

namespace ACP\Sorting\Model\User\RelatedMeta;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
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
		$this->meta_key = (string) $meta_key;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$query->query_from .= $wpdb->prepare( "
			LEFT JOIN $wpdb->usermeta AS acsort_usermeta ON acsort_usermeta.user_id = $wpdb->users.ID
				AND acsort_usermeta.meta_key = %s
			LEFT JOIN $wpdb->users AS acsort_users ON acsort_users.ID = acsort_usermeta.meta_value
			LEFT JOIN $wpdb->usermeta AS acsort_usermeta_related ON acsort_usermeta_related.user_id = acsort_users.ID
				AND acsort_usermeta_related.meta_key = %s
		", $this->meta_key, $this->meta_field );

		$query->query_orderby = sprintf( "
			GROUP BY $wpdb->users.ID
			ORDER BY %s,$wpdb->users.ID %s
		",
			SqlOrderByFactory::create( "acsort_usermeta_related.meta_value", $this->get_order() ),
			esc_sql( $this->get_order() )
		);
	}

}