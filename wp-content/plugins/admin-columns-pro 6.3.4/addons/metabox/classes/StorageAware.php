<?php

namespace ACA\MetaBox;

interface StorageAware {

	public const META_BOX = 'meta_box';
	public const CUSTOM_TABLE = 'custom_table';

	/**
	 * @return string
	 */
	public function get_storage();

}