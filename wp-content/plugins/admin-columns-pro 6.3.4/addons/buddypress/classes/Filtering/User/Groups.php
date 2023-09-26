<?php

namespace ACA\BP\Filtering\User;

use ACP;
use WP_User_Query;

class Groups extends ACP\Filtering\Model {

	/**
	 * @param WP_User_Query $query
	 */
	public function filter_by_groups( $query ) {
		global $wpdb, $bp;

		switch ( $this->get_filter_value() ) {
			case 'cpac_empty':
				$query->query_where .= " AND NOT EXISTS( SELECT user_id FROM {$bp->groups->table_name_members} WHERE user_id = {$wpdb->users}.ID )";

				break;
			case 'cpac_nonempty':
				$query->query_from .= " INNER JOIN {$bp->groups->table_name_members} AS bptm ON {$wpdb->users}.ID = bptm.user_id AND is_confirmed = 1";

				break;
			default:
				$query->query_from .= ' ' . $wpdb->prepare( "
					INNER JOIN {$bp->groups->table_name_members} AS bptm ON {$wpdb->users}.ID = bptm.user_id 
					AND bptm.group_id = %d
					", (int) $this->get_filter_value() );
		}
	}

	public function get_filtering_vars( $vars ) {
		add_action( 'pre_user_query', [ $this, 'filter_by_groups' ] );

		return $vars;
	}

	public function get_filtering_data() {
		$groups = groups_get_groups( [
			'show_hidden' => true,
			'per_page'    => -1,
		] );

		$options = [];
		foreach ( $groups['groups'] as $group ) {
			$options[ $group->id ] = $group->name;
		}

		return [
			'empty_option' => $this->get_empty_labels( __( 'Group', 'codepress-admin-columns' ) ),
			'options'      => $options,
		];
	}

}