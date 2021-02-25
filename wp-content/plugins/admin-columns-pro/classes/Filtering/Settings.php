<?php

namespace ACP\Filtering;

use AC;
use AC\View;

class Settings extends AC\Settings\Column
	implements AC\Settings\Header {

	/**
	 * @var string 'On' or 'Off'
	 */
	private $filter;

	/**
	 * @var string Top Label
	 */
	private $filter_label;

	protected function set_name() {
		$this->name = 'filter';
	}

	protected function define_options() {
		return [
			'filter' => 'off', // default Off
			'filter_label',
		];
	}

	/**
	 * @return string
	 */
	private function get_instruction() {
		$view = new View();
		$view->set_template( 'tooltip/filtering' );

		return $view->render();
	}

	public function create_header_view() {
		$filter = $this->get_filter();

		$view = new View( [
			'title'    => __( 'Enable Filtering', 'codepress-admin-columns' ),
			'dashicon' => 'dashicons-filter',
			'state'    => $filter,
		] );

		$view->set_template( 'settings/header-icon' );

		return $view;
	}

	public function create_view() {
		$filter = $this->create_element( 'radio', 'filter' )
		               ->set_options( [
			               'on'  => __( 'Yes' ),
			               'off' => __( 'No' ),
		               ] );

		// Main settings
		$view = new View();
		$view->set( 'label', __( 'Filtering', 'codepress-admin-columns' ) )
		     ->set( 'instructions', $this->get_instruction() )
		     ->set( 'setting', $filter );

		$filter_label = $this->create_element( 'text', 'filter_label' )
		                     ->set_attribute( 'data-default-translation', $this->get_default_translation_string() )
		                     ->set_attribute( 'placeholder', $this->get_filter_label_default() );

		// Sub settings
		$label_view = new View();
		$label_view->set( 'label', __( 'Top label', 'codepress-admin-columns' ) )
		           ->set( 'tooltip', __( "Set the name of the label in the filter menu", 'codepress-admin-columns' ) )
		           ->set( 'setting', $filter_label )
		           ->set( 'for', $filter_label->get_id() );

		$view->set( 'sections', [ $label_view ] );

		return $view;
	}

	/**
	 * @return string
	 */
	protected function get_label_from_column() {
		$label = $this->sanitize_label( $this->column->get_setting( 'label' )->get_value() );

		if ( ! $label ) {
			$label = $this->sanitize_label( $this->column->get_label() );
		}

		return $label;
	}

	/**
	 * @return string
	 */
	public function get_filter_label_default() {
		$label = $this->sanitize_label( $this->column->get_setting( 'label' )->get_value() );

		if ( ! $label ) {
			$label = $this->sanitize_label( $this->column->get_label() );
		}

		if ( $this->column instanceof Filterable && ! $this->column->filtering()->is_ranged() ) {
			$label = sprintf( $this->get_default_translation_string(), $label );
		}

		return $label;
	}

	private function get_default_translation_string() {
		return __( "Any %s", 'codepress-admin-columns' );
	}

	/**
	 * @return string
	 */
	public function get_filter() {
		return $this->filter;
	}

	/**
	 * @param string $filter
	 *
	 * @return $this
	 */
	public function set_filter( $filter ) {
		$this->filter = $filter;

		return $this;
	}

	/**
	 * @return bool True when filter is selected
	 */
	public function is_active() {
		return 'on' === $this->filter;
	}

	protected function sanitize_label( $label ) {
		return trim( strip_tags( $label ) );
	}

	/**
	 * @return string
	 */
	public function get_filter_label() {
		return $this->sanitize_label( $this->filter_label );
	}

	/**
	 * @param string $filter_label
	 *
	 * @return $this
	 */
	public function set_filter_label( $filter_label ) {
		$this->filter_label = $filter_label;

		return $this;
	}

}