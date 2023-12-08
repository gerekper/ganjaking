<?php

namespace ACA\ACF;

interface Configurable {

	public const FIELD = 'field';
	public const META_KEY = 'meta_key';
	public const FIELD_HASH = 'field_hash';
	public const FIELD_TYPE = 'field_type';

	public function create( string $column_type ): ?array;

}