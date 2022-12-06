<?php

namespace ACA\Pods\Export;

use ACP;

class File extends ACP\Export\Model {

	public function get_value( $id ) {
		$urls = [];

		foreach ( (array) $this->get_column()->get_raw_value( $id ) as $attachment_id ) {
			if ( is_numeric( $attachment_id ) ) {
				$urls[] = wp_get_attachment_url( $attachment_id );
			}
		}

		return implode( ',', $urls );
	}

}