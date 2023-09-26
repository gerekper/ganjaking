<?php

namespace ACA\MetaBox\Column\PostType;

use AC;
use WP_Taxonomy;

class Taxonomies extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-mb-pt_taxonomies' )
		     ->set_label( __( 'Taxonomies', 'codepress-admin-columns' ) )
		     ->set_group( 'metabox_custom' );
	}

	public function get_value( $id ) {
		$taxonomies = [];
		foreach ( $this->get_raw_value( $id ) as $taxonomy ) {
			$taxonomy = get_taxonomy( $taxonomy );

			if ( $taxonomy instanceof WP_Taxonomy ) {
				$taxonomies[] = sprintf( '<a href="%s">%s</a>', $this->get_taxonomy_link( $taxonomy->name ), $taxonomy->label );
			}
		}

		return implode( ', ', $taxonomies );
	}

	private function get_taxonomy_link( $taxonomy ) {
		return add_query_arg( [ 'taxonomy' => $taxonomy ], admin_url( 'edit-tags.php' ) );
	}

	public function get_raw_value( $id ) {
		$data = json_decode( get_post_field( 'post_content', $id ), true );

		return (array) $data['taxonomies'];
	}
}