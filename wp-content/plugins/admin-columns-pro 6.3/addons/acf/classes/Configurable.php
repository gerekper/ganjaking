<?php

namespace ACA\ACF;

interface Configurable {

	const FIELD = 'field';
	const META_KEY = 'meta_key';
	const FIELD_HASH = 'field_hash';
	const FIELD_TYPE = 'field_type';

	/**
	 * @param string $column_type
	 *
	 * @return array|null
	 */
	public function create( $column_type );

}