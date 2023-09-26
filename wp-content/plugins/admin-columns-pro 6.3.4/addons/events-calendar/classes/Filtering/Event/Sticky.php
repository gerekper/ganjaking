<?php

namespace ACA\EC\Filtering\Event;

use ACP;

class Sticky extends ACP\Filtering\Model {

	public function get_filtering_data() {
		return [
			'options' => [
				-1 => __( 'Sticky Events', 'codepress-admin-columns' ),
				0  => __( 'Not Sticky Events', 'codepress-admin-columns' ),
			],
		];
	}

	public function filter_by_menu_order( $where ) {
		global $wpdb;

		$where .= ' ' . $wpdb->prepare( "AND {$wpdb->posts}.menu_order = %s", $this->get_filter_value() );

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_menu_order' ] );

		return $vars;
	}

}