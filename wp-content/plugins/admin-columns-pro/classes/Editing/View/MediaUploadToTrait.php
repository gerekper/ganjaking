<?php

namespace ACP\Editing\View;

trait MediaUploadToTrait {

	public function set_upload_media_only( $upload_only ) {
		$args = (array) $this->get_arg( 'attachment' );

		$args['library']['uploadedTo'] = (bool) $upload_only;

		return $this->set( 'attachment', $args );
	}

}