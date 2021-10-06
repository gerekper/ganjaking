<?php

namespace ACP\Editing\Storage\Post;

use AC\Storage\Transaction;

class Attachments extends Field {

	public function __construct() {
		parent::__construct( 'post_parent' );
	}

	public function get( $id ) {
		$attachment_ids = get_posts( [
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_status'    => null,
			'post_parent'    => $id,
			'fields'         => 'ids',
		] );

		if ( ! $attachment_ids ) {
			return [];
		}

		return $attachment_ids;
	}

	public function update( $id, $attachment_ids ) {
		$current_attachment_ids = get_posts( [
			'post_type'      => 'attachment',
			'post_parent'    => $id,
			'posts_per_page' => -1,
			'fields'         => 'ids',
		] );

		$transaction = new Transaction();

		$results = [];

		// Detach
		if ( $current_attachment_ids ) {
			foreach ( $current_attachment_ids as $attachment_id ) {
				$results[] = parent::update( $attachment_id, '' );
			}
		}

		// Attach
		if ( ! empty( $attachment_ids ) ) {
			foreach ( $attachment_ids as $attachment_id ) {
				$results[] = parent::update( $attachment_id, $id );
			}
		}

		if ( in_array( false, $results, true ) ) {
			$transaction->rollback();

			return false;
		}

		$transaction->commit();

		return true;
	}

}