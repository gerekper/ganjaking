<?php

namespace ACP\Editing\Settings;

use AC;
use AC\View;

class CreateTerms extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $enable_term_creation;

	protected function define_options() {
		return [
			'enable_term_creation' => 'off',
		];
	}

	public function create_view() {
		$taxonomy = get_taxonomy( $this->column->get_taxonomy() );
		$view = new View();
		$enable_term = $this
			->create_element( 'radio', 'enable_term_creation' )
			->set_options( [
					'on'  => __( 'Yes' ),
					'off' => __( 'No' ),
				]
			);

		if ( $taxonomy ) {
			$view->set( 'label', sprintf( __( 'Allow new %s?', 'codepress-admin-columns' ), strtolower( $taxonomy->labels->name ) ) )
			     ->set( 'tooltip', sprintf( __( 'Allow new %s to be created whilst editing', 'codepress-admin-columns' ), strtolower( $taxonomy->labels->name ) ) )
			     ->set( 'setting', $enable_term );
		}

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_enable_term_creation() {
		return $this->enable_term_creation;
	}

	/**
	 * @param string $enable_term_creation
	 *
	 * @return $this
	 */
	public function set_enable_term_creation( $enable_term_creation ) {
		$this->enable_term_creation = $enable_term_creation;

		return $this;
	}

}