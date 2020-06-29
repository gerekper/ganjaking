<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
use WP_User_Query;

class FullName extends AbstractModel {

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		$order = esc_sql( $this->get_order() );

		$query->query_fields .= ", CONCAT( acsort_usermeta1.meta_value, acsort_usermeta2.meta_value ) AS acsort_fullname";
		$query->query_from .= "
			INNER JOIN {$wpdb->usermeta} AS acsort_usermeta1 ON {$wpdb->users}.ID = acsort_usermeta1.user_id
				AND acsort_usermeta1.meta_key = 'first_name'
			INNER JOIN {$wpdb->usermeta} AS acsort_usermeta2 ON {$wpdb->users}.ID = acsort_usermeta2.user_id
				AND acsort_usermeta2.meta_key = 'last_name'
		";

		$query->query_orderby = "
			GROUP BY {$wpdb->users}.ID
			ORDER BY acsort_fullname {$order}, {$wpdb->users}.display_name {$order}
		";

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );
	}

}