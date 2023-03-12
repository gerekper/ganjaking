<?php

namespace ACP\Export\Model\CustomField;

use AC\Column;
use ACP\Export\Service;

class Image implements Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$urls = [];

		foreach ( (array) $this->column->get_raw_value( $id ) as $url ) {
			if ( is_numeric( $url ) ) {
				$url = wp_get_attachment_url( $url );
			}

			$urls[] = strip_tags( $url );
		}

		return implode( ', ', $urls );
	}

}