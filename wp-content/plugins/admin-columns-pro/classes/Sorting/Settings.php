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

	public function create_view() {
		$setting = new AC\Form\Element\Toggle( 'sort', '', $this->get_value( 'sort' ) === 'on', 'on', 'off' );
		$setting->add_class( 'ac-setting-input_sort' );

		$view = new View();
		$view->set( 'label', __( 'Sorting', 'codepress-admin-columns' ) )
		     ->set( 'instructions', $this->get_instructions() )
		     ->set( 'setting', $setting );

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