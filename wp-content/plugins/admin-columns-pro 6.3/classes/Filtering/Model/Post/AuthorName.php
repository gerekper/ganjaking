<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class AuthorName extends Model {

	public function filter_by_author_name( $where ) {
		global $wpdb;

		return $where . $wpdb->prepare( "AND {$wpdb->posts}.post_author = %s", $this->get_filter_value() );
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_author_name' ] );

		return $vars;
	}

	public function get_filtering_data() {
		$options = [];

		$values = $this->strategy->get_values_by_db_field( 'post_author' );

		if ( $values ) {
			foreach ( $values as $value ) {
				$options[ $value ] = ac_helper()->user->get_display_name( $value );
			}
		}

		return [
			'options' => array_filter( $options ),
		];
	}

}