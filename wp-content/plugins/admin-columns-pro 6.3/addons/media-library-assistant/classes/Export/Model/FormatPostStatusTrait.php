<?php

namespace ACA\MLA\Export\Model;

trait FormatPostStatusTrait {

	private function format_post_status( string $post_status ): ?string {
		switch ( $post_status ) {
			case 'draft' :
				return __( 'Draft' );
			case 'future' :
				return __( 'Scheduled' );
			case 'pending' :
				return _x( 'Pending', 'post state' );
			case 'trash' :
				return __( 'Trash' );
			default:
				return null;
		}
	}

}