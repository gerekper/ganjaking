<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class ID extends Model {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
		$this->set_ranged( true );
	}

	public function filter_by_id( $where ) {
		global $wpdb;

		$value = $this->get_filter_value();

		if ( $value['min'] ) {
			$where .= $wpdb->prepare( "AND {$wpdb->posts}.ID >= %s", $value['min'] );
		}

		if ( $value['max'] ) {
			$where .= $wpdb->prepare( "AND {$wpdb->posts}.ID <= %s", $value['max'] );
		}

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_id' ] );

		return $vars;
	}

	public function get_filtering_data() {
		return false;
	}

}