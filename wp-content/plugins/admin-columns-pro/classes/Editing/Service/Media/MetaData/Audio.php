<?php

namespace ACP\Editing\Service\Media\MetaData;

use ACP\Editing\Service;

class Audio extends Service\Media\MetaData {

	public function is_editable( int $id ): bool {
		if ( ! wp_attachment_is( 'audio', $id ) ) {
			return false;
		}

		return parent::is_editable( $id );
	}

	public function get_not_editable_reason( int $id ): string {
		return __( 'Item is not an audio file.', 'codepress-admin-columns' );
	}
}