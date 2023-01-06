<?php

namespace WCML\Utilities;

class WpAdminPages {

	/**
	 * @return bool
	 */
	public static function isDashboard() {
		global $pagenow;

		return is_admin() && 'index.php' === $pagenow;
	}
}
