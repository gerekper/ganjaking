<?php

namespace ACP\Column\Taxonomy;

use AC;
use AC\Settings;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Export;

class CustomDescription extends AC\Column
	implements Editing\Editable, Export\Exportable, ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_label( 'Description' )
		     ->set_type( 'column-term_custom_description' );
	}

	protected function register_settings() {
		parent::register_settings();

		$this->add_setting( new Settings\Column\WordLimit( $this ) );
	}

	public function editing() {
		return new Editing\Service\Basic(
			new Editing\View\TextArea(),
			new Editing\Storage\Taxonomy\Field( $this->get_taxonomy(), 'description' )
		);
	}

	public function export() {
		return new Export\Model\Term\Description( $this );
	}

}