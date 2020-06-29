<?php

namespace ACP\Sorting\Admin;

use AC\Form\Element\Checkbox;
use AC\Renderable;
use AC\Settings\General;
use ACP\Sorting\Settings\AllResults;

class ShowAllResults implements Renderable {

	/**
	 * @var AllResults
	 */
	private $option;

	public function __construct() {
		$this->option = new AllResults();
	}

	private function get_label() {
		return sprintf( '%s %s',
			__( "Show all results when sorting.", 'codepress-admin-columns' ),
			sprintf( __( "Default is %s.", 'codepress-admin-columns' ), '<code>' . __( 'off', 'codepress-admin-columns' ) . '</code>' )
		);
	}

	public function render() {
		$name = sprintf( '%s[%s]', General::NAME, $this->option->get_name() );

		$checkbox = new Checkbox( $name );

		$checkbox->set_options( [ '1' => $this->get_label() ] )
		         ->set_value( $this->option->is_enabled() ? 1 : 0 );

		return $checkbox->render();
	}

}