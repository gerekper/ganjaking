<?php

namespace ACP\Editing\Settings;

use AC;
use AC\View;
use ACP;

class BulkEditing extends AC\Settings\Column implements AC\Settings\Header {

	const NAME = 'bulk_edit';

	/**
	 * @var string
	 */
	private $bulk_edit;

	/**
	 * @return array
	 */
	protected function define_options() {
		return [
			'bulk_edit' => 'on',
		];
	}

	/**
	 * @return string
	 */
	private function get_instructions() {
		return ( new View() )->set_template( 'tooltip/bulk-editing' )->render();
	}

	public function create_header_view() {
		$view = new View( [
			'title' => __( 'Enable Bulk Editing', 'codepress-admin-columns' ),
			'class' => 'cpacicon-bulk-edit',
			'state' => $this->get_bulk_edit(),
		] );

		$view->set_template( 'settings/header-icon' );

		return $view;
	}

	/**
	 * @return View
	 */
	public function create_view() {
		$setting = $this->create_element( 'radio', 'bulk_edit' )
		                ->set_options( [
			                'on'  => __( 'Yes' ),
			                'off' => __( 'No' ),
		                ] );

		$view = new View();
		$view->set( 'label', __( 'Bulk Editing', 'codepress-admin-columns' ) )
		     ->set( 'instructions', $this->get_instructions() )
		     ->set( 'setting', $setting );

		return $view;
	}

	/**
	 * @return bool True when bulk_edit is selected
	 */
	public function is_active() {
		return 'on' === $this->get_bulk_edit();
	}

	/**
	 * @return string
	 */
	public function get_bulk_edit() {
		return $this->bulk_edit;
	}

	/**
	 * @param string $bulk_edit
	 *
	 * @return $this
	 */
	public function set_bulk_edit( $bulk_edit ) {
		$this->bulk_edit = $bulk_edit;

		return $this;
	}

}