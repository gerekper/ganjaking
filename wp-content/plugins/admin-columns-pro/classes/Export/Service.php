<?php

namespace ACP\Export;

interface Service {

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_value( $id );

}