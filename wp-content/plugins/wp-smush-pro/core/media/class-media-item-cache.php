<?php

namespace Smush\Core\Media;

use WP_Smush;

/**
 * TODO: maybe reset the media item when:
 * - a new size is added
 */
class Media_Item_Cache {
	const CACHE_GROUP = 'wp-smushit';
	/**
	 * Static instance
	 *
	 * @var self
	 */
	private static $instance;
	/**
	 * @var Media_Item[]
	 */
	private $media_items;

	/**
	 * Static instance getter
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function has( $id ) {
		$media_item = $this->get_from_cache( $id );

		return ! empty( $media_item );
	}

	/**
	 * @param $id
	 *
	 * @return Media_Item|null
	 */
	public function get( $id ) {
		$media_item = $this->get_from_cache( $id );
		if ( ! $media_item ) {
			$media_item = new Media_Item( $id );
			$this->save_to_cache( $id, $media_item );
		}

		return $media_item;
	}

	/**
	 * @param $id
	 *
	 * @return Media_Item|null
	 */
	private function get_from_cache( $id ) {
		return $this->get_array_value(
			$this->media_items,
			$this->make_key( $id )
		);
	}

	private function make_key( $id ) {
		$membership_type_postfix = WP_Smush::is_pro() ? 'pro' : 'free';

		return "wp-smush-$membership_type_postfix-media-item-$id";
	}

	private function save_to_cache( $id, $media_item ) {
		$this->media_items[ $this->make_key( $id ) ] = $media_item;
	}

	public function remove( $id ) {
		unset( $this->media_items[ $this->make_key( $id ) ] );
	}

	private function get_array_value( $array, $key ) {
		return $array && isset( $array[ $key ] )
			? $array[ $key ]
			: null;
	}

	public function reset_all() {
		foreach ( $this->media_items as $media_item ) {
			$media_item->reset();
		}
	}
}