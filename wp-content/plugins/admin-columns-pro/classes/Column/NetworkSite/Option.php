<?php

namespace ACP\Column\NetworkSite;

use AC;
use ACP\Editing;

abstract class Option extends AC\Column
	implements Editing\Editable {

	/**
	 * @return string Site option name
	 */
	abstract public function get_option_name();

	public function get_value( $blog_id ) {
		return $this->get_site_option( $blog_id );
	}

	public function get_site_option( $blog_id ) {
		return ac_helper()->network->get_site_option( $blog_id, $this->get_option_name() );
	}

	public function get_raw_value( $blog_id ) {
		return $this->get_site_option( $blog_id );
	}

	public function editing() {
		return new Editing\Service\Basic(
			new Editing\View\Text(),
			new Editing\Storage\Site\Option( $this->get_option_name() )
		);
	}

}