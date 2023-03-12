<?php

namespace ACA\WC\Asset\Script;

use AC;

class Table extends AC\Asset\Script {

	public function __construct( string $handle, AC\Asset\Location\Absolute $location ) {
		parent::__construct( $handle, $location->with_suffix( 'assets/js/table.js' ), [ 'jquery' ] );
	}

	public function register(): void {
		parent::register();

		wp_localize_script( $this->handle, 'acp_wc_table', [
			'edit_post_link' => add_query_arg( [ 'action' => 'edit' ], admin_url() . 'post.php' ),
		] );

	}

}