<?php

namespace ACA\Types\Settings;

use AC;
use AC\View;
use IToolset_Relationship_Definition;
use Toolset_Element_Domain;
use Toolset_Relationship_Definition_Repository;
use Toolset_Relationship_Query_V2;

class Relationship extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $relationship;

	protected function define_options() {
		return [ 'relationship' ];
	}

	public function create_view() {
		$select = $this->create_element( 'select' );

		$select
			->set_no_result( __( 'No relations available.', 'codepress-admin-columns' ) )
			->set_attribute( 'data-label', 'update' )
			->set_attribute( 'data-refresh', 'column' )
			->set_options( $this->get_relationships() );

		$view = new View( [
			'label'   => __( 'Relationship', 'codepress-admin-columns' ),
			'setting' => $select,
		] );

		return $view;
	}

	public function get_dependent_settings() {
		return [
			new AC\Settings\Column\Post( $this->column ),
		];
	}

	/**
	 * @return string
	 */
	public function get_relationship() {
		if ( null === $this->relationship ) {

			// Default
			$this->set_relationship( $this->get_first_relationship() );
		}

		return $this->relationship;
	}

	/**
	 * @param string $relationship
	 *
	 * @return true
	 */
	public function set_relationship( $relationship ) {
		$this->relationship = $relationship;

		return true;
	}

	/**
	 * @return string
	 */
	private function get_first_relationship() {
		$relationship = $this->get_relationships();

		reset( $relationship );

		return key( $relationship );
	}

	/**
	 * @return IToolset_Relationship_Definition|null
	 */
	public function get_relationship_object() {
		$relationships = Toolset_Relationship_Definition_Repository::get_instance();

		return $relationships->get_definition( $this->get_relationship() );
	}

	private function get_relationships() {
		$options = [];

		$query = new Toolset_Relationship_Query_V2();
		$query->add(
			$query->has_domain_and_type(
				$this->column->get_post_type(),
				Toolset_Element_Domain::POSTS
			)
		);

		$relationships = $query->get_results();

		foreach ( $relationships as $relationship ) {
			$related_post_type = in_array( $this->column->get_post_type(), $relationship->get_parent_type()->get_types() )
				? $relationship->get_child_type()->get_types()[0]
				: $relationship->get_parent_type()->get_types()[0];

			$post_type = get_post_type_object( $related_post_type );

			$options[ $relationship->get_slug() ] = $post_type ? sprintf( '%s (%s)', $relationship->get_display_name(), $post_type->label ) : $relationship->get_display_name();
		}

		return $options;
	}

}