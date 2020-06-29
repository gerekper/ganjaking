<?php

namespace ACP\Column\NetworkSite;

use AC;

class Status extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-msite_status' );
		$this->set_label( __( 'Status', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		$values = [];

		$site = get_site( $id );

		foreach ( $this->get_statuses() as $status => $label ) {
			if ( ! empty( $site->{$status} ) ) {
				$values[] = $label;
			}
		}

		return ac_helper()->html->implode( $values );
	}

	private function get_statuses() {
		return [
			'public'   => __( 'Public' ),
			'archived' => __( 'Archived' ),
			'spam'     => _x( 'Spam', 'site' ),
			'deleted'  => __( 'Deleted' ),
			'mature'   => __( 'Mature' ),
		];
	}

}