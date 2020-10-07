<?php

namespace ACP\Editing;

use AC;
use AC\View;

class Settings extends AC\Settings\Column
	implements AC\Settings\Header {

	const NAME = 'edit';

	private $edit;

	protected function define_options() {
		return [ self::NAME => 'off' ];
	}

	/**
	 * @return string
	 */
	private function get_instruction() {
		$view = new View( [
			'object_type' => $this->column->get_list_screen()->get_singular_label(),
		] );
		$view->set_template( 'tooltip/inline-editing' );

		return $view->render();
	}

	public function create_header_view() {
		$filter = $this->get_edit();

		$view = new View( [
			'title'    => __( 'Enable Editing', 'codepress-admin-columns' ),
			'dashicon' => 'dashicons-edit',
			'state'    => $filter,
		] );

		$view->set_template( 'settings/header-icon' );

		return $view;
	}

	public function create_view() {
		$edit = $this->create_element( 'radio', 'edit' );
		$edit
			->set_options( [
				'on'  => __( 'Yes' ),
				'off' => __( 'No' ),
			] );

		$view = new View();
		$view->set( 'label', __( 'Inline Editing', 'codepress-admin-columns' ) )
		     ->set( 'instructions', $this->get_instruction() )
		     ->set( 'setting', $edit );

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_edit() {
		return $this->edit;
	}

	/**
	 * @param string $edit
	 *
	 * @return $this
	 */
	public function set_edit( $edit ) {
		$this->edit = $edit;

		return $this;
	}

	public function is_active() {
		return 'on' === $this->get_edit();
	}

}