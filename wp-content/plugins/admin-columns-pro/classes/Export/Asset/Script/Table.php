<?php

namespace ACP\Export\Asset\Script;

use AC\Asset\Location;
use AC\Asset\Script;
use ACP\Export;

final class Table extends Script {

	const NONCE_ACTION = 'acp_export_listscreen_export';

	/**
	 * @var Export\Strategy
	 */
	private $strategy;

	/**
	 * @var array [ $column_name => $column_label, ... ]
	 */
	private $columns;

	public function __construct( $handle, Location $location, Export\Strategy $strategy, array $columns ) {
		parent::__construct( $handle, $location, [ 'jquery' ] );

		$this->strategy = $strategy;
		$this->columns = $columns;
	}

	public function register() {
		parent::register();

		$this->add_inline_variable( 'ACP_Export', [
			'total_num_items' => $this->strategy->get_total_items() ?? 0,
			'num_iterations'  => $this->strategy->get_num_items_per_iteration(),
			'nonce'           => wp_create_nonce( self::NONCE_ACTION ),
			'columns'         => $this->columns,
		] );

		wp_localize_script( $this->get_handle(), 'ACP_Export_i18n', [
			'dismiss'          => __( 'Dismiss this notice.' ),
			'export'           => __( 'Export', 'codepress-admin-columns' ),
			'export_error'     => __( 'Something went wrong during exporting. Please try again.', 'codepress-admin-columns' ),
			'processed'        => __( 'Processed {0} of {1} items ({2}%).', 'codepress-admin-columns' ),
			'exporting'        => __( 'Exporting current list of items.', 'codepress-admin-columns' ),
			'export_completed' => __( 'Exported {0} items', 'codepress-admin-columns' ),
			'download_file'    => __( 'Download File', 'codepress-admin-columns' ),
			'failed'           => __( 'Failed', 'codepress-admin-columns' ),
			'done'             => __( 'Done', 'codepress-admin-columns' ),
			'cancel'           => __( 'Cancel', 'codepress-admin-columns' ),
			'leaving'          => __( 'You are currently generating an export file. Leaving the page will cancel this process. Are you sure you want to leave the page?', 'codepress-admin-columns' ),
			'affected_items'   => _x( 'This will affect {0}', 'export', 'codepress-admin-columns' ),
			'items'            => __( '{0} items', 'codepress-admin-columns' ),
			'item'             => __( '1 item', 'codepress-admin-columns' ),
		] );
	}

}