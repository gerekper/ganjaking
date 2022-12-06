<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;

class Status extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_orderby', [ $this, 'orderby_status' ] );

		return [];
	}

	private function get_stati(): array {
		$translated_stati = [];

		foreach ( get_post_stati( null, 'objects' ) as $key => $post_status ) {
			$translated_stati[ $key ] = $post_status->label;
		}

		natcasesort( $translated_stati );

		return array_keys( $translated_stati );
	}

	public function orderby_status() {
		global $wpdb;

		remove_filter( 'posts_orderby', [ $this, __FUNCTION__ ] );

		$fields = implode( "','", array_map( 'esc_sql', $this->get_stati() ) );

		return sprintf( "FIELD( $wpdb->posts.post_status, '%s' ) %s", $fields, $this->get_order() );
	}

}