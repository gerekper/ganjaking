<?php

namespace ACP\Column\NetworkSite;

use AC;
use ACP\ConditionalFormat;
use ACP\Settings;

class Theme extends AC\Column implements ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-msite_theme' );
		$this->set_label( __( 'Theme', 'codepress-admin-columns' ) );
	}

	public function get_raw_value( $blog_id ) {
		return $blog_id;
	}

	public function register_settings() {
		$this->add_setting( new Settings\Column\NetworkSite\Theme( $this ) );
	}

}