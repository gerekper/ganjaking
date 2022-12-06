<?php

namespace ACP\Sorting;

use AC;
use AC\View;

class Settings extends AC\Settings\Column
	implements AC\Settings\Header {

	/**
	 * @var string
	 */
	private $sort;

	/**
	 * @var bool
	 */
	private $has_warning;

	public function __construct( AC\Column $column, $has_warning = false ) {
		parent::__construct( $column );

		$this->has_warning = (bool) $has_warning;
	}

	protected function define_options() {
		return [ 'sort' => 'on' ];
	}

	private function get_instructions() {
		$view = new View();
		$view->set_template( 'tooltip/sorting' );

		return $view->render();
	}

	public function create_header_view() {
		$view = new View( [
			'title'    => __( 'Enable Sorting', 'codepress-admin-columns' ),
			'dashicon' => 'dashicons-sort',
			'state'    => $this->get_sort(),
		] );

		$view->set_template( 'settings/header-icon' );

		return $view;
	}

	private function tooltip_warning() {
		return new AC\Admin\Tooltip( 'sort_warning', [
			'content'    => sprintf( '<p>%s</p>', __( 'This sorting query can be slow on very large datasets.', 'codepress-admin-columns' ) ),
			'link_label' => '<span class="dashicons dashicons-warning"></span>',
			'title'      => __( 'Notice', 'codepress-admin-columns' ),
			'position'   => 'right_bottom',
		] );
	}

	public function create_view() {
		$setting = new AC\Form\Element\Toggle( 'sort', '', $this->get_value( 'sort' ) === 'on', 'on', 'off' );
		$setting->add_class( 'ac-setting-input_sort' );

		$tooltip_render = '';

		if ( $this->has_warning ) {
			$setting->add_class( 'ac-setting-input_sort__warning' );
			$tooltip = $this->tooltip_warning();
			$tooltip_render = $tooltip->get_label() . $tooltip->get_instructions();
		}

		$view = new View();
		$view->set( 'label', __( 'Sorting', 'codepress-admin-columns' ) )
		     ->set( 'instructions', $this->get_instructions() )
		     ->set( 'setting', $setting->render() . $tooltip_render );

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_sort() {
		return $this->sort;
	}

	/**
	 * @param string $sort
	 *
	 * @return $this
	 */
	public function set_sort( $sort ) {
		$this->sort = $sort;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return 'on' === $this->get_sort();
	}

}