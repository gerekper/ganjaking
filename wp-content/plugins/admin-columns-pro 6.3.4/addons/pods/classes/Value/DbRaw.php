<?php

namespace ACA\Pods\Value;

use AC;

class DbRaw {

	/**
	 * @var string
	 */
	private $meta_key;

	/**
	 * @var string
	 */
	private $meta_type;

	public function __construct( $meta_key, $meta_type ) {
		$this->meta_key = $meta_key;
		$this->meta_type = $meta_type;
	}

	/**
	 * Get the raw DB value
	 *
	 * @param int $id
	 *
	 * @return array|false
	 */
	public function get_value( $id ) {
		global $wpdb;

		switch ( $this->meta_type ) {
			case AC\MetaType::POST:
				$sql = $wpdb->prepare(
					"
					SELECT $wpdb->postmeta.meta_value 
					FROM $wpdb->postmeta 
					WHERE $wpdb->postmeta.meta_key = %s 
					AND $wpdb->postmeta.post_id = %d
				", $this->meta_key, $id );

				break;
			case AC\MetaType::USER:
				$sql = $wpdb->prepare(
					"
					SELECT $wpdb->usermeta.meta_value 
					FROM $wpdb->usermeta 
					WHERE $wpdb->usermeta.meta_key = %s 
					AND $wpdb->usermeta.user_id = %d
				", $this->meta_key, $id );

				break;
			case AC\MetaType::COMMENT:
				$sql = $wpdb->prepare(
					"
					SELECT $wpdb->commentmeta.meta_value 
					FROM $wpdb->commentmeta 
					WHERE $wpdb->commentmeta.meta_key = %s 
					AND $wpdb->commentmeta.comment_id = %d
				", $this->meta_key, $id );

				break;
			case AC\MetaType::TERM:
				$sql = $wpdb->prepare(
					"
					SELECT $wpdb->termmeta.meta_value 
					FROM $wpdb->termmeta 
					WHERE $wpdb->termmeta.meta_key = %s 
					AND $wpdb->termmeta.term_id = %d
				", $this->meta_key, $id );

				break;
			default :
				$sql = false;
		}

		if ( ! $sql ) {
			return false;
		}

		return $wpdb->get_col( $sql );
	}

}