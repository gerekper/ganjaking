<?php

namespace ACP\Editing\Storage\Site;

use ACP\Editing\Storage;

class Option implements Storage {

	/**
	 * @var string
	 */
	private $option_name;

	public function __construct( $option_name ) {
		$this->option_name = $option_name;
	}

	public function update( $id, $value ) {
		switch_to_blog( $id );

		$result = update_option( $this->option_name, $value );

		restore_current_blog();

		return $result;
	}

	public function get( $blog_id ) {
		return ac_helper()->network->get_site_option( $blog_id, $this->option_name );
	}

}