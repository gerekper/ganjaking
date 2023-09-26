<?php

namespace ACA\MetaBox\Setting;

use AC;
use AC\View;
use ACA\MetaBox\Column;
use ACA\MetaBox\Column\Group;

class GroupField extends AC\Settings\Column {

	public function __construct( Group $column ) {
		parent::__construct( $column );
	}

	protected $group_field;

	protected function define_options() {
		return [
			'group_field',
		];
	}

	public function create_view(): View {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_attribute( 'data-label', 'update' )
		               ->set_options( $this->get_display_options() );

		return new View( [
			'label'   => __( 'Group Field', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	public function get_dependent_settings() {
		$settings = $this->get_group_field_settings();

		if ( ! $settings ) {
			return [];
		}

		return ( new SubFieldSettingFactory() )->create( $settings, $this->column );
	}

	function get_display_options(): array {
		if ( ! $this->column instanceof Column ) {
			return [];
		}

		$fields = $this->column->get_field_setting( 'fields' );

		if ( ! $fields ) {
			return [];
		}

		$options = [];

		foreach ( $fields as $field ) {
			if ( in_array( $field['type'], [ 'group' ], true ) ) {
				continue;
			}

			$options[ $field['id'] ] = $field['name'];
		}

		return $options;
	}

	public function get_group_field(): ?string {
		return $this->group_field;
	}

	public function set_group_field( string $group_field = null ): void {
		$this->group_field = $group_field;
	}

	public function get_group_field_settings(): ?array {
		if ( ! $this->column instanceof Column ) {
			return null;
		}

		$fields = $this->column->get_field_setting( 'fields' ) ?? [];

		$key = array_search( $this->get_group_field(), array_column( $fields, 'id' ), true );

		return $key === false ? null : $fields[ $key ];
	}

}