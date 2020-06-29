<?php

interface WP_Post {}

/**
 * Mocks out necessary properties to look like a term object after construction.
 */
class MockWpPost implements WP_Post {

	/**
	 * The post ID.
	 * @var int
	 */
	public $ID;

	/**
	 * The post type.
	 * @var string
	 */
	public $post_type;

	public function __construct() {
	}
}
