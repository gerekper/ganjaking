<?php

namespace ACP\Plugin\Update;

use AC\Plugin\Update;
use AC\Plugin\Version;
use DirectoryIterator;

class V5400 extends Update {

	public function __construct() {
		parent::__construct( new Version( '5.4.0' ) );
	}

	public function apply_update() {
		$upload_dir = wp_upload_dir();
		$export_dir = trailingslashit( $upload_dir['basedir'] ) . 'admin-columns/export/';

		if ( is_dir( $export_dir ) ) {
			foreach ( new DirectoryIterator( $export_dir ) as $file_info ) {
				if ( $file_info->isFile() && 'csv' === $file_info->getExtension() ) {
					unlink( $file_info->getRealPath() );
				}
			}
		}
	}

}