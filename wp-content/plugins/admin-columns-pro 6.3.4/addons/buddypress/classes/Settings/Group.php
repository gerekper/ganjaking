<?php

namespace ACA\BP\Settings;

use AC;
use AC\View;

class Group extends AC\Settings\Column
	implements AC\Settings\FormatValue {

	/**
	 * @var string
	 */
	private $group_property;

	protected function set_name() {
		$this->name = 'group';
	}

	protected function define_options() {
		return [
			'group_property_display' => 'title',
		];
	}

	public function get_dependent_settings() {
		return [
			new GroupLink( $this->column ),
		];

	}

	public function format( $value, $original_value ) {
		$group_id = $original_value;

		$group = groups_get_group( $group_id );

		switch ( $this->get_group_property_display() ) {
			case 'slug' :
				$value = $group->slug;

				break;
			default:
				$value = $group->name;
		}

		return $value;
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_options( $this->get_display_options() );

		$view = new View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $select,
		] );

		return $view;
	}

	protected function get_display_options() {
		return [
			'title' => __( 'Title' ),
			'slug'  => __( 'Slug' ),
		];
	}

	/**
	 * @return string
	 */
	public function get_group_property_display() {
		return $this->group_property;
	}

	/**
	 * @param string $group_property
	 *
	 * @return bool
	 */
	public function set_group_property_display( $group_property ) {
		$this->group_property = $group_property;

		return true;
	}

}