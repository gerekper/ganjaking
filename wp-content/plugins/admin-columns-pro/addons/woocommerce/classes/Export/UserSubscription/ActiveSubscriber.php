<?php

namespace ACA\WC\Export\UserSubscription;

use ACP;

class ActiveSubscriber extends ACP\Export\Model {

	public function get_value( $user_id ) {
		if ( wcs_user_has_subscription( $user_id, '', 'active' ) ) {
			return '1';
		}

		return '';
	}

}