<?php

namespace ACP\Filtering\Settings;

use AC\View;
use ACP\Filtering\Settings;

abstract class Ranged extends Settings {

	/**
	 * @var string
	 * Options: range, daily, monthly, yearly, exact_match, future, past
	 */
	private $filter_format;

	abstract protected function get_options();

	protected function define_options() {
		$options = parent::define_options();

		$options['filter_format'] = ''; // default empty

		return $options;
	}

	public function create_view() {
		$view = parent::create_view();

		$options = $this->get_options();
		$options['range'] = __( 'Range', 'codepress-admin-columns' );

		$filter_format = $this->create_element( 'select', 'filter_format' )->set_options( $options );

		$format_view = new View();
		$format_view->set( 'label', __( 'Filter by', 'codepress-admin-columns' ) )
		            ->set( 'tooltip', __( 'This will allow you to set the filter format.', 'codepress-admin-columns' ) )
		            ->set( 'setting', $filter_format )
		            ->set( 'for', $filter_format->get_id() );

		$sections = $view->get( 'sections' );
		$sections[] = $format_view;

		$view->set( 'sections', $sections );

		return $view;
	}

	/**
	 * @return bool True when ranged selected
	 */
	public function is_ranged() {
		return 'range' === $this->get_filter_format();
	}

	/**
	 * @return string
	 */
	public function get_filter_format() {
		return $this->filter_format;
	}

	/**
	 * @param string $filter_format
	 *
	 * @return $this
	 */
	public function set_filter_format( $filter_format ) {
		$this->filter_format = $filter_format;

		return $this;
	}

}