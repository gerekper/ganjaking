<?php

namespace ACP\ThirdParty\ACF;

use AC\Registrable;

class Addon implements Registrable {

	public function register() {
		add_filter( 'acp/editing/post_statuses', [ $this, 'remove_acf_statuses_for_editing' ] );
	}

	/**
	 * @param array $statuses
	 *
	 * @return array
	 */
	public function remove_acf_statuses_for_editing( $statuses ) {
		if ( isset( $statuses['acf-disabled'] ) ) {
			unset( $statuses['acf-disabled'] );
		}

		return $statuses;
	}

}