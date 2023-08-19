<?php

namespace ACP\Helper\Select;

use AC;

/**
 * @deprecated 4.7
 */
interface Paginated extends AC\Helper\Select\Paginated {

	/**
	 * @return int
	 */
	public function get_total_pages();

	/**
	 * @return int
	 */
	public function get_page();

	/**
	 * @return bool
	 */
	public function is_last_page();

}