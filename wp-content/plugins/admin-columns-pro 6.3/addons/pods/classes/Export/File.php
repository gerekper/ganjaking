<?php

namespace ACA\Pods\Export;

use AC\Column;
use ACP;

class File implements ACP\Export\Service {

	/**
	 * @var Column
	 */
	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ): string {
		$urls = [];

		foreach ( (array) $this->column->get_raw_value( $id ) as $attachment_id ) {
			if ( is_numeric( $attachment_id ) ) {
				$urls[] = wp_get_attachment_url( $attachment_id );
			}
		}

		return implode( ',', $urls );
	}

}