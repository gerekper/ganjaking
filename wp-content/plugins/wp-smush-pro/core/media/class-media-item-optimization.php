<?php

namespace Smush\Core\Media;

use WP_Error;

abstract class Media_Item_Optimization {
	/**
	 * @param $media_item Media_Item
	 */
	abstract public function __construct( $media_item );

	abstract public function get_key();

	/**
	 * @return Media_Item_Stats
	 */
	abstract public function get_stats();

	/**
	 * @return Media_Item_Stats
	 */
	abstract public function get_size_stats( $size_key );

	abstract public function get_optimized_sizes_count();

	abstract public function save();

	abstract public function is_optimized();

	abstract public function should_optimize();

	abstract public function should_reoptimize();

	/**
	 * @param $size Media_Item_Size
	 */
	abstract public function should_optimize_size( $size );

	/**
	 * @return mixed
	 */
	abstract public function delete_data();

	/**
	 * @return boolean
	 */
	abstract public function optimize();

	public function can_restore() {
		return false;
	}

	public function restore() {
		return false;
	}

	public function has_errors() {
		$wp_error = $this->get_errors();

		return $wp_error
		       && is_a( $wp_error, '\WP_Error' )
		       && $wp_error->has_errors();
	}

	/**
	 * @return WP_Error
	 */
	abstract public function get_errors();
}