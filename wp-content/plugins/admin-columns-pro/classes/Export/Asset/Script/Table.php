<?php

namespace ACP\Export\Asset\Script;

use AC\Asset\Location;
use AC\Asset\Script;

final class Table extends Script {

	/**
	 * @param string   $handle
	 * @param Location $location
	 */
	public function __construct( $handle, Location $location ) {
		parent::__construct( $handle, $location, [ 'jquery' ] );
	}

	public function register() {
		global $wp_list_table;

		if ( ! $wp_list_table ) {
			return;
		}

		parent::register();

		wp_localize_script( $this->get_handle(), 'ACP_Export', [
			'total_num_items' => $wp_list_table->get_pagination_arg( 'total_items' ),
			'nonce'           => wp_create_nonce( 'acp_export_listscreen_export' ),
			'i18n'            => [
				'dismiss'          => __( 'Dismiss this notice.' ),
				'export'           => __( 'Export', 'codepress-admin-columns' ),
				'export_error'     => __( 'Something went wrong during exporting. Please try again.', 'codepress-admin-columns' ),
				'processed'        => __( 'Processed {0} of {1} items ({2}%).', 'codepress-admin-columns' ),
				'exporting'        => __( 'Exporting current list of items.', 'codepress-admin-columns' ),
				'export_completed' => __( 'Export completed ({0} items). Your download will start automatically. If this does not happen, you can download the file again: ', 'codepress-admin-columns' ),
				'download_file'    => __( 'Download File', 'codepress-admin-columns' ),
				'leaving'          => __( 'You are currently generating an export file. Leaving the page will cancel this process. Are you sure you want to leave the page?', 'codepress-admin-columns' ),
			],
		] );
	}

}