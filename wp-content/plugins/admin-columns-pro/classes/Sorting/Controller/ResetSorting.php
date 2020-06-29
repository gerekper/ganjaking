<?php

namespace ACP\Sorting\Controller;

use AC\Capabilities;
use AC\Message;
use AC\Preferences;
use AC\Registrable;

/**
 * Reset all sorting preferences for all users
 */
class ResetSorting implements Registrable {

	public function register() {
		add_action( 'admin_init', [ $this, 'handle_request' ] );
	}

	public function handle_request() {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_acnonce' ), 'reset-sorting-preference' ) ) {
			return;
		}

		$preference = new Preferences\Site( 'sorted_by' );
		$preference->reset_for_all_users();

		$notice = new Message\Notice( __( 'All sorting preferences have been reset.', 'codepress-admin-columns' ) );
		$notice->register();
	}

}