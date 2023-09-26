<?php

namespace ACA\ACF\Service;

use AC\Registerable;

class EditingFix implements Registerable {

	public function register(): void
    {
		add_filter( 'acp/editing/post_statuses', [ $this, 'remove_acf_statuses_for_editing' ] );
	}

	public function remove_acf_statuses_for_editing( $statuses ) {
		if ( isset( $statuses['acf-disabled'] ) ) {
			unset( $statuses['acf-disabled'] );
		}

		return $statuses;
	}

}