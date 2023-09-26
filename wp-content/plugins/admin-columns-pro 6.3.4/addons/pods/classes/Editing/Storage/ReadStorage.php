<?php

namespace ACA\Pods\Editing\Storage;

interface ReadStorage {

	/**
	 * @return mixed
	 */
	public function get( int $id );
}