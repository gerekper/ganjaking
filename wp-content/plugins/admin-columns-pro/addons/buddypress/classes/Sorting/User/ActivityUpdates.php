<?php

namespace ACA\BP\Sorting\User;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use WP_User_Query;

class ActivityUpdates extends AbstractModel {

	/**
	 * @var string
	 */
	private $activity_type;

	public function __construct( $activity_type ) {
		parent::__construct();

		$this->activity_type = (string) $activity_type;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb, $bp;

		$where = '';
		if ( $this->activity_type ) {
			$where = $wpdb->prepare( 'AND acsort_activity.type = %s', $this->activity_type );
		}

		$query->query_from .= "
			LEFT JOIN {$bp->activity->table_name} as acsort_activity 
				ON {$wpdb->users}.ID = acsort_activity.user_id {$where}
		";
		$query->query_orderby = sprintf( "GROUP BY {$wpdb->users}.ID ORDER BY %s",
			SqlOrderByFactory::create_with_computation( new ComputationType( ComputationType::COUNT ), "acsort_activity.user_id", $this->get_order() )
		);

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );
	}

}