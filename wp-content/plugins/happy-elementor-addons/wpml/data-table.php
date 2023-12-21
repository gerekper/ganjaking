<?php
/**
 * Data Table
 */
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class WPML_Data_Table_Column_Data extends \WPML_Elementor_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'columns_data';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'column_name',
		];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'column_name':
				return __( 'Data Table: Column Name', 'happy-elementor-addons' );
			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'column_name':
				return 'LINE';
			default:
				return '';
		}
	}
}

class WPML_Data_Table_Row_Data extends \WPML_Elementor_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'rows_data';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'cell_name',
			'cell_link' => ['url']
		];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'cell_name':
				return __( 'Data Table: Cell Title', 'happy-elementor-addons' );
			case 'url':
				return __( 'Data Table: Cell Link', 'happy-elementor-addons' );
			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'cell_name':
				return 'LINE';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}
}
