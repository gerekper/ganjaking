<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\User;

use WP_User;

interface LabelFormatter {

	public function format_label( WP_User $user ): string;

	public function format_label_unique( WP_User $user ): string;

}