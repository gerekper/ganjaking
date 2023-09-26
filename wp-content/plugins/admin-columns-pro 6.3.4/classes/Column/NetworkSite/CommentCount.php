<?php

namespace ACP\Column\NetworkSite;

use AC;
use ACP\ConditionalFormat;
use ACP\Settings;

class CommentCount extends AC\Column implements ConditionalFormat\Formattable {

	use ConditionalFormat\IntegerFormattableTrait;

	public function __construct() {
		$this->set_type( 'column-msite_commentcount' )
		     ->set_label( __( 'Comments', 'codepress-admin-columns' ) );
	}

	public function get_value( $blog_id ) {
		return $this->get_formatted_value( $blog_id );
	}

	public function register_settings() {
		$this->add_setting( new Settings\Column\NetworkSite\CommentCount( $this ) );
	}

}