<?php

namespace Smush\Core\Media_Library;

use Smush\Core\Array_Utils;
use Smush\Core\Controller;
use Smush\Core\Helper;
use Smush\Core\Media\Media_Item_Query;

class Media_Library_Slice_Data_Fetcher extends Controller {
	private $slice_post_meta = array();

	private $slice_post_ids = array();

	private $query;
	/**
	 * @var \WDEV_Logger|null
	 */
	private $logger;

	private $is_multisite;

	private $current_site_id;
	/**
	 * @var Array_Utils
	 */
	private $array_utils;

	public function __construct( $is_multisite = false, $current_site_id = 0 ) {
		$this->is_multisite    = $is_multisite;
		$this->current_site_id = $current_site_id;
		$this->query           = new Media_Item_Query();
		$this->logger          = Helper::logger();
		$this->array_utils     = new Array_Utils();

		$this->register_filter( 'wp_smush_before_scan_library_slice', array( $this, 'prefetch_slice_data' ), 10, 3 );
		$this->register_filter( 'wp_smush_before_scan_library_slice', array( $this, 'hook_meta_filters' ), 20, 3 );
		$this->register_filter( 'wp_smush_after_scan_library_slice', array( $this, 'unhook_meta_filters' ) );
		$this->register_filter( 'wp_smush_after_scan_library_slice', array( $this, 'reset_slice_data' ) );
	}

	public function hook_meta_filters() {
		add_filter( 'get_post_metadata', array( $this, 'maybe_serve_post_meta' ), 10, 3 );
		add_filter( 'add_post_meta', array( $this, 'update_post_meta_on_add' ), 10, 3 );
		add_filter( 'update_post_meta', array( $this, 'update_post_meta_on_update' ), 10, 4 );
		add_action( 'delete_post_meta', array( $this, 'purge_post_meta_on_delete' ), 10, 3 );
	}

	public function unhook_meta_filters() {
		remove_filter( 'get_post_metadata', array( $this, 'maybe_serve_post_meta' ) );
		remove_filter( 'add_post_meta', array( $this, 'update_post_meta_on_add' ) );
		remove_filter( 'update_post_meta', array( $this, 'update_post_meta_on_update' ) );
		remove_action( 'delete_post_meta', array( $this, 'purge_post_meta_on_delete' ) );
	}

	public function prefetch_slice_data( $slice_data, $slice, $slice_size ) {
		$this->prefetch_slice_post_meta( $slice, $slice_size );

		$this->prefetch_slice_posts( $slice, $slice_size );

		return $slice_data;
	}

	public function maybe_serve_post_meta( $meta_value, $attachment_id, $meta_key ) {
		$slice_post_meta = $this->get_slice_post_meta();
		if ( empty( $slice_post_meta ) ) {
			return $meta_value;
		}

		$cache_key    = $this->get_post_meta_cache_key( $attachment_id, $meta_key );
		$cached_value = '';
		if ( isset( $slice_post_meta[ $cache_key ]->meta_value ) ) {
			$cached_value = maybe_unserialize( $slice_post_meta[ $cache_key ]->meta_value );
		}

		return array( $cached_value );
	}

	public function update_post_meta_on_add( $attachment_id, $meta_key, $meta_value ) {
		$this->update_post_meta( $attachment_id, $meta_key, $meta_value );
	}

	public function update_post_meta_on_update( $meta_id, $attachment_id, $meta_key, $meta_value ) {
		$this->update_post_meta( $attachment_id, $meta_key, $meta_value );
	}

	public function purge_post_meta_on_delete( $meta_ids, $attachment_id, $meta_key ) {
		$cache_key = $this->get_post_meta_cache_key( $attachment_id, $meta_key );

		$slice_post_meta = $this->get_slice_post_meta();
		if ( isset( $slice_post_meta[ $cache_key ] ) ) {
			unset( $slice_post_meta[ $cache_key ] );
			$this->set_slice_post_meta( $slice_post_meta );
		}
	}

	public function reset_slice_data( $slice_data ) {
		$this->set_slice_post_meta( array() );

		$this->reset_slice_posts();

		return $slice_data;
	}

	private function prefetch_slice_post_meta( $slice, $slice_size ) {
		$fetched_post_meta = $this->query->fetch_slice_post_meta( $slice, $slice_size );
		$fetched_post_meta = $this->array_utils->ensure_array( $fetched_post_meta );

		$this->set_slice_post_meta( $fetched_post_meta );
	}

	private function prefetch_slice_posts( $slice, $slice_size ) {
		$slice_posts = $this->query->fetch_slice_posts( $slice, $slice_size );
		if ( ! empty( $slice_posts ) && is_array( $slice_posts ) ) {
			$slice_post_ids = array();
			foreach ( $slice_posts as $slice_post_key => $slice_post ) {
				$slice_post_ids[] = $slice_post_key;

				// Sanitize before adding to cache otherwise the post is going to be sanitized every time it is fetched from the cache
				$sanitized_post = sanitize_post( $slice_post, 'raw' );
				wp_cache_add( $slice_post_key, $sanitized_post, 'posts' );
			}
			$this->set_slice_post_ids( $slice_post_ids );
		}
	}

	private function reset_slice_posts() {
		foreach ( $this->get_slice_post_ids() as $slice_post_id ) {
			wp_cache_delete( $slice_post_id, 'posts' );
		}

		$this->set_slice_post_ids( array() );
	}

	/**
	 * @param $attachment_id
	 * @param $meta_key
	 *
	 * @return string
	 */
	private function get_post_meta_cache_key( $attachment_id, $meta_key ) {
		return "$attachment_id-$meta_key";
	}

	private function get_slice_post_meta() {
		$slice_post_meta = $this->slice_post_meta;
		if ( $this->is_multisite ) {
			$slice_post_meta = $this->array_utils->get_array_value( $slice_post_meta, $this->current_site_id );
		}

		return $this->array_utils->ensure_array( $slice_post_meta );
	}

	private function set_slice_post_meta( $slice_post_meta ) {
		if ( $this->is_multisite ) {
			$this->slice_post_meta[ $this->current_site_id ] = $slice_post_meta;
		} else {
			$this->slice_post_meta = $slice_post_meta;
		}
	}

	private function get_slice_post_ids() {
		$slice_post_ids = $this->slice_post_ids;
		if ( $this->is_multisite ) {
			$slice_post_ids = $this->array_utils->get_array_value( $slice_post_ids, $this->current_site_id );
		}

		return $this->array_utils->ensure_array( $slice_post_ids );
	}

	private function set_slice_post_ids( $slice_post_ids ) {
		if ( $this->is_multisite ) {
			$this->slice_post_ids[ $this->current_site_id ] = $slice_post_ids;
		} else {
			$this->slice_post_ids = $slice_post_ids;
		}
	}

	/**
	 * @param $attachment_id
	 * @param $meta_key
	 * @param $meta_value
	 *
	 * @return void
	 */
	private function update_post_meta( $attachment_id, $meta_key, $meta_value ) {
		$cache_key = $this->get_post_meta_cache_key( $attachment_id, $meta_key );

		$slice_post_meta = $this->get_slice_post_meta();
		if ( empty( $slice_post_meta[ $cache_key ] ) ) {
			$slice_post_meta[ $cache_key ] = new \stdClass();
		}
		$slice_post_meta[ $cache_key ]->meta_value = $meta_value;
		$this->set_slice_post_meta( $slice_post_meta );
	}
}