<?php

namespace ACP\ThirdParty\YoastSeo\Settings;

use AC;
use AC\View;

class PrimaryTaxonomy extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $primary_taxonomy;

	protected function define_options() {
		return [ 'primary_taxonomy' ];
	}

	public function create_view() {
		$setting = $this->create_element( 'select' )
		                ->set_attribute( 'data-label', 'update' )
		                ->set_options( $this->get_taxonomies() );

		return new View( [
			'label'   => __( 'Taxonomy' ),
			'setting' => $setting,
		] );
	}

	/**
	 * @return array
	 */
	private function get_taxonomies() {
		$taxonomies = get_object_taxonomies( $this->column->get_post_type(), 'objects' );
		$options = [];

		foreach ( $taxonomies as $taxonomy => $tax_object ) {
			if ( ! $tax_object->hierarchical ) {
				continue;
			}

			$options[ $taxonomy ] = $tax_object->label;
		}

		return $options;
	}

	/**
	 * @return string
	 */
	public function get_primary_taxonomy() {
		return $this->primary_taxonomy;
	}

	/**
	 * @param string $primary_taxonomy
	 *
	 * @return bool
	 */
	public function set_primary_taxonomy( $primary_taxonomy ) {
		$this->primary_taxonomy = $primary_taxonomy;

		return true;
	}

}