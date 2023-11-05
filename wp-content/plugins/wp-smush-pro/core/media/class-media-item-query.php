<?php

namespace Smush\Core\Media;

use Smush\Core\Smush\Smush_Optimization;
use Smush\Core\Smush_File;

class Media_Item_Query {
	public function fetch( $offset = 0, $limit = - 1 ) {
		global $wpdb;
		$query = $this->make_query( 'ID', $offset, $limit );

		return $wpdb->get_col( $query );
	}

	public function fetch_slice_post_meta( $slice, $slice_size ) {
		global $wpdb;

		$offset = $this->get_offset( $slice, $slice_size );
		$limit  = (int) $slice_size;

		$ids_query = $this->make_query( 'ID', $offset, $limit );
		$query     = "SELECT CONCAT(post_id, '-', meta_key), post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE post_id IN (SELECT * FROM ($ids_query) AS slice_ids);";

		return $wpdb->get_results( $query, OBJECT_K );
	}

	public function fetch_slice_posts( $slice, $slice_size ) {
		global $wpdb;

		$offset      = $this->get_offset( $slice, $slice_size );
		$limit       = (int) $slice_size;
		$posts_query = $this->make_query( '*', $offset, $limit );

		return $wpdb->get_results( $posts_query, OBJECT_K );
	}

	public function fetch_slice_ids( $slice, $slice_size ) {
		$offset = $this->get_offset( $slice, $slice_size );
		$limit  = (int) $slice_size;

		return $this->fetch( $offset, $limit );
	}

	public function get_slice_count( $slice_size ) {
		if ( empty( $slice_size ) ) {
			return 0;
		}

		$image_attachment_count = $this->get_image_attachment_count();

		return (int) ceil( $image_attachment_count / $slice_size );
	}

	public function get_image_attachment_count() {
		global $wpdb;
		$query = $this->make_query( 'COUNT(*)' );

		return (int) $wpdb->get_var( $query );
	}

	/**
	 * @param $select
	 * @param $offset
	 * @param $limit
	 *
	 * @return string|null
	 */
	private function make_query( $select, $offset = 0, $limit = - 1 ) {
		global $wpdb;
		$mime_types   = ( new Smush_File() )->get_supported_mime_types();
		$placeholders = implode( ',', array_fill( 0, count( $mime_types ), '%s' ) );
		$column       = $select;

		$query = "SELECT %s FROM $wpdb->posts WHERE post_type = 'attachment' AND post_mime_type IN (%s)";
		$query = sprintf( $query, $column, $placeholders );
		$args  = $mime_types;

		if ( $limit > 0 ) {
			$query  = "$query LIMIT %d";
			$args[] = $limit;

			if ( $offset >= 0 ) {
				$query  = "$query OFFSET %d";
				$args[] = $offset;
			}
		}

		return $wpdb->prepare( $query, $args );
	}

	public function get_lossy_count() {
		global $wpdb;

		$query = $wpdb->prepare( "SELECT COUNT(DISTINCT post_id) FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = 1", Smush_Optimization::LOSSY_META_KEY );

		return $wpdb->get_var( $query );
	}

	public function get_smushed_count() {
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT COUNT(DISTINCT post_meta_optimized.post_id) FROM $wpdb->postmeta as post_meta_optimized
			LEFT JOIN $wpdb->postmeta as post_meta_ignored ON post_meta_optimized.post_id = post_meta_ignored.post_id AND post_meta_ignored.meta_key= %s
			WHERE post_meta_optimized.meta_key = %s AND post_meta_ignored.meta_value IS NULL",
			Media_Item::IGNORED_META_KEY,
			Smush_Optimization::SMUSH_META_KEY
		);

		return $wpdb->get_var( $query );
	}

	public function get_ignored_count() {
		global $wpdb;

		$query = $wpdb->prepare( "SELECT COUNT(DISTINCT post_id) FROM $wpdb->postmeta WHERE meta_key = %s", Media_Item::IGNORED_META_KEY );

		return $wpdb->get_var( $query );
	}

	/**
	 * @param $slice
	 * @param $slice_size
	 *
	 * @return float|int
	 */
	private function get_offset( $slice, $slice_size ) {
		$slice      = (int) $slice;
		$slice_size = (int) $slice_size;

		return ( $slice - 1 ) * $slice_size;
	}
}