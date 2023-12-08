<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\User;

use WP_User;

interface GroupFormatter {

	public function format( WP_User $user ): string;

}