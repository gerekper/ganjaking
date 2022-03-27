<?php

namespace ACP\Export\Model;

use ACP\Export\Model;

/**
 * Exportability model for outputting an attachment's URL based on its ID
 * @since 4.1
 */
class AttachmentURLFromAttachmentId extends Model {

	public function get_value( $id ) {
		return wp_get_attachment_url( $this->column->get_raw_value( $id ) );
	}

}