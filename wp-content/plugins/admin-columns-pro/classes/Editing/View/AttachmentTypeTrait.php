<?php

namespace ACP\Editing\View;

use InvalidArgumentException;

trait AttachmentTypeTrait {

	/**
	 * @param string $type
	 *
	 * @return $this
	 */
	public function set_attachment_type( $type ) {
		if ( ! in_array( $type, [ 'image', 'video', 'audio' ], true ) ) {
			throw new InvalidArgumentException( 'Invalid attachment type.' );
		}

		$this->set( 'attachment', [
			'library' => [
				'type' => $type,
			],
		] );

		return $this;
	}

}