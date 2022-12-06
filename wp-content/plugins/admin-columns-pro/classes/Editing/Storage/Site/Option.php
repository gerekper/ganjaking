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

	public function update( int $id, $data ): bool {
		switch_to_blog( $id );

		$result = update_option( $this->option_name, $data );

		restore_current_blog();

		return $result;
	}

	public function get( int $id ) {
		return ac_helper()->network->get_site_option( $id, $this->option_name );
	}

}