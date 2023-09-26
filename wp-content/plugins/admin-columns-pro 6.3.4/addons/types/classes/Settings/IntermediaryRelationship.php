<?php

namespace ACA\Types\Settings;

use AC;
use AC\View;

class IntermediaryRelationship extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $intermediary_relationship;

	protected function define_options() {
		return [ 'intermediary_relationship' ];
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

	private function get_relationships() {
		$options = [];

		$relationship = toolset_get_relationship( $this->column->get_post_type() );

		if ( empty( $relationship ) ) {
			return $options;
		}

		if ( $relationship['roles']['parent']['types'][0] ) {
			$post_type = get_post_type_object( $relationship['roles']['parent']['types'][0] );
			$options[ $post_type->name ] = $post_type->label;
		}

		if ( $relationship['roles']['child']['types'][0] ) {
			$post_type = get_post_type_object( $relationship['roles']['child']['types'][0] );
			$options[ $post_type->name ] = $post_type->label;
		}

		return $options;
	}

	public function get_dependent_settings() {
		return [
			new AC\Settings\Column\Post( $this->column ),
		];
	}

	/**
	 * @return string
	 */
	public function get_intermediary_relationship() {
		if ( null === $this->intermediary_relationship ) {
			$this->set_intermediary_relationship( $this->get_first_relationship() );
		}

		return $this->intermediary_relationship;
	}

	/**
	 * @param string $relationship
	 *
	 * @return true
	 */
	public function set_intermediary_relationship( $relationship ) {
		$this->intermediary_relationship = $relationship;

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

}