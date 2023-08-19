<?php

namespace ACA\Types\Field;

use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACA\Types\Export;
use ACA\Types\Field;
use ACA\Types\Filtering;
use ACA\Types\Search;
use ACP;

class Checkboxes extends Field {

	public function get_value( $id ) {
		$labels = $this->get_values_as_labels( $id );

		return ac_helper()->html->small_block( $labels );
	}

	public function get_values_as_labels( $id ) {
		$raw = $this->get_raw_value( $id );

		if ( ! $raw ) {
			return false;
		}

		$options = $this->get( 'options' );

		if ( ! $options ) {
			return false;
		}

		// Checkbox keys
		$keys = [];
		foreach ( $raw as $value ) {
			$keys[] = $value[0];
		}

		// Checkbox Labels
		$labels = [];
		foreach ( $options as $option ) {
			if ( in_array( $option['set_value'], $keys ) ) {
				$labels[ $option['set_value'] ] = $option['title'];
			}
		}

		return $labels;
	}

	public function is_serialized() {
		return true;
	}

	public function filtering() {
		return new Filtering\Checkboxes( $this->column );
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\CheckboxList( $this->get_field_options() ) )->set_clear_button( true ),
			new Storage\Checkboxes( $this->get_meta_key(), new MetaType( $this->get_meta_type() ), (array) $this->get( 'options' ) )
		);
	}

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key() );
	}

	public function export() {
		return new Export\Field\Checkboxes( $this->column );
	}

	public function search() {
		return new Search\Checkboxes(
			$this->column->get_meta_key(),
			$this->column->get_meta_type(),
			$this->get_field_options()
		);
	}

	private function get_field_options() {
		$result = [];

		$options = (array) $this->get( 'options' );

		foreach ( $options as $option ) {
			$result[ $option['set_value'] ] = $option['title'];
		}

		return $result;
	}

}