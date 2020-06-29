<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\Settings\Column\TaxonomyPostType;

/**
 * @since 4.5.6
 */
class CountForPostType extends AC\Column {

	public function __construct() {
		$this->set_label( 'Count for Post Type' );
		$this->set_type( 'column-term_count_for_post_type' );
	}

	public function get_value( $id ) {
		$raw_value = $this->get_raw_value( $id );
		$count = $raw_value ? number_format_i18n( $raw_value ) : 0;
		$term = get_term( $id, $this->get_taxonomy() );

		$url = add_query_arg( [ 'post_type' => $this->get_post_type_setting(), $this->get_taxonomy_param( $this->get_taxonomy() ) => $term->slug ], admin_url( 'edit.php' ) );

		return sprintf( '<a href="%s">%s</a>', $url, $count );
	}

	public function get_raw_value( $id ) {
		$posts = get_posts( [
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'post_type'      => $this->get_post_type_setting() ?: 'any',
			'tax_query'      => [
				[
					'taxonomy' => $this->get_taxonomy(),
					'field'    => 'id',
					'terms'    => $id,
				],
			],
		] );

		return count( $posts );
	}

	public function register_settings() {
		$this->add_setting( new TaxonomyPostType( $this ) );
	}

	/**
	 * Get the correct param name based on the taxonomy
	 *
	 * @param string $taxonomy
	 *
	 * @return string
	 */
	public function get_taxonomy_param( $taxonomy ) {
		switch ( $taxonomy ) {
			case 'category':
				$taxonomy = 'category_name';
				break;
			case 'post_tag':
				$taxonomy = 'tag';
				break;
		}

		return $taxonomy;
	}

	/**
	 * @return string
	 */
	private function get_post_type_setting() {
		return $this->get_setting( 'taxonomy_post_type' )->get_value();
	}

}