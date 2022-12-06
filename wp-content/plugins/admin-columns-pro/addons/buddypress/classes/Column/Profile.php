<?php

namespace ACA\BP\Column;

use AC;
use ACA\BP\Field;
use ACA\BP\Settings;
use ACP;
use BP_XProfile_Field;

class Profile extends AC\Column
	implements ACP\Editing\Editable, ACP\Filtering\Filterable, ACP\Sorting\Sortable, ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-buddypress' )
		     ->set_label( __( 'Profile Fields', 'buddypress' ) )
		     ->set_group( 'buddypress' );
	}

	public function get_value( $id ) {
		$value = $this->get_field()->get_value( $id );

		if ( ! $value ) {
			$value = $this->get_empty_char();
		}

		return $value;
	}

	public function is_valid() {
		return bp_is_active( 'xprofile' );
	}

	public function editing() {
		return $this->get_field()->editing();
	}

	public function filtering() {
		return $this->get_field()->filtering();
	}

	public function sorting() {
		return $this->get_field()->sorting();
	}

	public function export() {
		return $this->get_field()->export();
	}

	public function search() {
		return $this->get_field()->search();
	}

	protected function register_settings() {
		$this->add_setting( new Settings\Profile( $this ) );
	}

	public function get_raw_value( $id ) {
		return $this->get_field()->get_raw_value( $id );
	}

	/**
	 * @return false|Field\Profile
	 */
	public function get_field() {
		$class = Field\Profile::class;

		$type = $class . '\\' . ucfirst( $this->get_buddypress_field_option( 'type' ) );

		// Single fields
		if ( class_exists( $type ) ) {
			$class = $type;
		}

		return new $class( $this );
	}

	public function get_buddypress_field() {
		return new BP_XProfile_Field( $this->get_buddypress_field_id() );
	}

	public function get_buddypress_field_id() {
		return (string) $this->get_setting( 'profile_field' )->get_value();
	}

	/**
	 * @param string $property
	 *
	 * @return mixed
	 */
	public function get_buddypress_field_option( $property ) {
		$field = $this->get_buddypress_field();

		if ( ! isset( $field->$property ) ) {
			return false;
		}

		return $field->$property;
	}

}