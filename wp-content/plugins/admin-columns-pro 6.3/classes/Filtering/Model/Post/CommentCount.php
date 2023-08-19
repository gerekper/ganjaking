<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class CommentCount extends Model {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
		$this->set_ranged( true );
	}

	public function filter_by_comment_count( $where ) {
		global $wpdb;

		$value = $this->get_filter_value();

		if ( $value['min'] ) {
			$where .= $wpdb->prepare( " AND {$wpdb->posts}.comment_count >= %d", $value['min'] );
		}

		if ( $value['max'] ) {
			$where .= $wpdb->prepare( " AND {$wpdb->posts}.comment_count <= %d", $value['max'] );
		}

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_comment_count' ] );

		return $vars;
	}

	public function get_filtering_data() {
		$options = [];

		if ( $values = $this->strategy->get_values_by_db_field( 'comment_count' ) ) {
			foreach ( $values as $value ) {
				$options[ $value ] = $value;
			}
		}

		return [
			'options'      => $options,
			'empty_option' => $this->get_empty_labels( __( 'Comments' ) ),
		];
	}

}