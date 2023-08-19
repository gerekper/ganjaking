<?php

namespace ACA\BP\Settings;

use AC;
use AC\View;
use ACA\BP\Column;
use BP_XProfile_Field;
use BP_XProfile_Group;

/**
 * @property Column\Profile $column
 */
class Profile extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $profile_field;

	protected function define_options() {
		return [ 'profile_field' ];
	}

	public function create_view() {
		$select = $this->create_element( 'select' );

		$select
			->set_no_result( sprintf( __( 'No %s fields available.', 'codepress-admin-columns' ), __( 'BuddyPress', 'codepress-admin-columns' ) ) )
			->set_options( $this->get_field_types() )
			->set_attribute( 'data-refresh', 'column' )
			->set_attribute( 'data-label', 'update' );

		$view = new View( [
			'label'       => __( 'Field', 'codepress-admin-columns' ),
			'description' => sprintf( __( 'Select your %s field.', 'codepress-admin-columns' ), __( 'BuddyPress profile', 'codepress-admin-columns' ) ),
			'setting'     => $select,
		] );

		return $view;
	}

	public function get_dependent_settings() {
		return $this->column->get_field()->get_dependent_settings();
	}

	/**
	 * @return string
	 */
	public function get_profile_field() {
		if ( null === $this->profile_field ) {
			$this->set_profile_field( $this->get_first_profile_field() );
		}

		return $this->profile_field;
	}

	/**
	 * @param string $profile_field
	 *
	 * @return true
	 */
	public function set_profile_field( $profile_field ) {
		$this->profile_field = $profile_field;

		return true;
	}

	/**
	 * @return string
	 */
	private function get_first_profile_field() {
		$fields = $this->get_field_types();

		reset( $fields );

		return key( $fields );
	}

	private function get_field_types() {
		$option_groups = [];
		/** @var BP_XProfile_Group[] $groups */
		$groups = BP_XProfile_Group::get( [ 'fetch_fields' => true ] );

		foreach ( $groups as $group ) {
			$options = [];

			foreach ( $group->fields as $field ) {
				/** @var BP_XProfile_Field $field */
				$options[ $field->id ] = $field->name;
			}

			$option_groups[ $group->id ] = [
				'title'   => $group->name,
				'options' => $options,
			];

		}

		return $option_groups;
	}

}
