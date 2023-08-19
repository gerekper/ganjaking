<?php

namespace ACP\Settings\Column;

use AC;

/**
 * @since 4.5.6
 */
class TaxonomyPostType extends AC\Settings\Column {

	/** @var string */
	private $post_type;

	protected function define_options() {
		return [
			'taxonomy_post_type' => 'any',
		];
	}

	public function create_view() {
		$select = $this->create_element( 'select' );

		$select->set_options( $this->get_post_types_for_taxonomy( $this->column->get_taxonomy() ) );

		$view = new AC\View( [
			'label'   => __( 'Post Type', 'codepress-admin-columns' ),
			'setting' => $select,
		] );

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_taxonomy_post_type() {
		return $this->post_type;
	}

	public function set_taxonomy_post_type( $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 * @param $taxonomy
	 *
	 * @return array
	 */
	private function get_post_types_for_taxonomy( $taxonomy ) {
		$post_types = [];
		$tax_object = get_taxonomy( $taxonomy );

		if ( empty( $tax_object ) ) {
			return $post_types;
		}

		foreach ( $tax_object->object_type as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			if ( ! $post_type_object ) {
				continue;
			}

			$post_types[ $post_type_object->name ] = $post_type_object->label;
		}

		return $post_types;
	}

}