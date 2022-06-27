<?php

namespace ACP\Export\Settings;

use AC;
use AC\View;
use ACP;

class Column extends AC\Settings\Column implements AC\Settings\Header {

	const NAME = 'export';

	/**
	 * @var string
	 */
	private $export;

	/**
	 * @return array
	 */
	protected function define_options() {
		return [
			'export' => 'off',
		];
	}

	public function create_header_view() {
		$view = new View( [
			'title'    => __( 'Enable Export', 'codepress-admin-columns' ),
			'dashicon' => 'cpacicon cpacicon-download',
			'state'    => $this->get_export(),
		] );

		$view->set_template( 'settings/header-icon' );

		return $view;
	}

	private function get_instructions() {
		$view = new View();
		$view->set_template( 'tooltip/export' );

		return $view->render();
	}

	/**
	 * @return View
	 */
	public function create_view() {
		$setting = new AC\Form\Element\Toggle( self::NAME, '', $this->get_value( self::NAME ) === 'on', 'on', 'off' );
		$setting->add_class( 'ac-setting-input_' . self::NAME );

		$view = new View();
		$view->set( 'label', __( 'Export', 'codepress-admin-columns' ) )
		     ->set( 'instructions', $this->get_instructions() )
		     ->set( 'setting', $setting );

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_export() {
		return $this->export;
	}

	/**
	 * @param string $export
	 *
	 * @return $this
	 */
	public function set_export( $export ) {
		$this->export = $export;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return 'on' === $this->get_export();
	}

}