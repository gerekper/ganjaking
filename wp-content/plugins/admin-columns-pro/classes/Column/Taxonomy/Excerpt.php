<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\Editing;

/**
 * @since 2.0.0
 */
class Excerpt extends AC\Column
	implements Editing\Editable {

	public function __construct() {
		$this->set_type( 'column-excerpt' );
		$this->set_label( __( 'Excerpt', 'codepress-admin-columns' ) );
	}

	public function get_raw_value( $term_id ) {
		return ac_helper()->taxonomy->get_term_field( 'description', $term_id, $this->get_taxonomy() );
	}

	public function editing() {
		return new Editing\Service\Basic(
			new Editing\View\TextArea(),
			new Editing\Storage\Taxonomy\Field( $this->get_taxonomy(), 'description' )
		);
	}

	public function register_settings() {
		$this->add_setting( new AC\Settings\Column\WordLimit( $this ) );
	}

}