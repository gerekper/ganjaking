<?php

namespace ACP\Search\Settings;

use AC;
use AC\View;
use ACP;

class Column extends AC\Settings\Column implements AC\Settings\Header {

	/**
	 * @var string
	 */
	private $search;

	/**
	 * @return array
	 */
	protected function define_options() {
		return [
			'search' => 'on',
		];
	}

	public function create_header_view() {
		$view = new View( [
			'title' => __( 'Enable Smart Filtering', 'codepress-admin-columns' ),
			'class' => 'cpacicon-smart-filter',
			'state' => $this->get_search(),
		] );

		$view->set_template( 'settings/header-icon' );

		return $view;
	}

	private function get_instructions() {
		$view = new View();
		$view->set_template( 'tooltip/smart-filtering' );

		return $view->render();
	}

	/**
	 * @return View
	 */
	public function create_view() {
		$setting = new AC\Form\Element\Toggle( 'search', '', $this->get_value( 'search' ) === 'on', 'on', 'off' );
		$setting->add_class( 'ac-setting-input_search' );

		$view = new View();
		$view->set( 'label', __( 'Smart Filtering', 'codepress-admin-columns' ) )
		     ->set( 'instructions', $this->get_instructions() )
		     ->set( 'setting', $setting );

		return $view;
	}

	/**
	 * @return bool True when search is selected
	 */
	public function is_active() {
		return 'on' === $this->get_search();
	}

	/**
	 * @return string
	 */
	public function get_search() {
		return $this->search;
	}

	/**
	 * @param string $search
	 *
	 * @return $this
	 */
	public function set_search( $search ) {
		$this->search = $search;

		return $this;
	}

}