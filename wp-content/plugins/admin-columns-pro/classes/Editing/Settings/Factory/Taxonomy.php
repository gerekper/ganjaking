<?php

namespace ACP\Editing\Settings\Factory;

use AC;
use ACP;
use ACP\Editing;

class Taxonomy implements Editing\Settings\SettingFactoryInterface {

	/**
	 * @var AC\Column
	 */
	private $column;

	public function __construct( AC\Column $column ) {
		$this->column = $column;
	}

	public function create() {
		$section = new Editing\Settings\CreateTerms( $this->column );
		$section->set_values( $this->column->get_options() );

		$setting = new Editing\Settings( $this->column, true );
		$setting->add_section( $section );

		return $setting;
	}

}