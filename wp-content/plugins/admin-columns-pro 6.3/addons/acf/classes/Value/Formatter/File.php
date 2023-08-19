<?php

namespace ACA\ACF\Value\Formatter;

use ACA\ACF\Value\Formatter;

class File extends Formatter {

	public function format( $attachment_id, $id = null ) {
		$value = null;

		if ( $attachment_id ) {
			$attachment = get_attached_file( $attachment_id );

			if ( $attachment ) {
				$value = ac_helper()->html->link( wp_get_attachment_url( $attachment_id ), esc_html( basename( $attachment ) ), [ 'target' => '_blank' ] );
			} else {
				$value = '<em>' . __( 'Invalid attachment', 'codepress-admin-columns' ) . '</em>';
			}
		}

		return $value ?: $this->column->get_empty_char();
	}

}