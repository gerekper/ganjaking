<?php

namespace ACA\JetEngine\Field;

interface ValueFormat {

	const FORMAT_ID = 'id';
	const FORMAT_URL = 'url';
	const FORMAT_BOTH = 'both';

	/**
	 * @return string
	 */
	public function get_value_format();

}