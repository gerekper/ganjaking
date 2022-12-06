<?php

namespace ACA\WC\Editing\ShopOrder;

use ACA\WC;
use ACP;
use ACP\Editing\View;
use WC_DateTime;

class NotesSystem implements ACP\Editing\Service {

	public function update( int $id, $data ): void {
		$request_note_ids = array_map( 'absint', (array) $data );
		$current_note_ids = $this->get_note_ids( $id );

		// System notes can only be deleted
		$deleted_note_ids = array_diff( $current_note_ids, $request_note_ids );
		array_map( 'wc_delete_order_note', $deleted_note_ids );
	}

	public function get_view( string $context ): ?View {
		return ( new WC\Editing\View\Notes() )->set_mode( 'system' );
	}

	private function get_note_ids( $id ): array {
		global $wpdb;

		$ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT comment_ID 
				FROM $wpdb->comments
				WHERE comment_type = 'order_note' 
					AND comment_author = %s
					AND comment_post_ID = %d
			",
				(string) __( 'WooCommerce', 'woocommerce' ),
				(int) $id
			)
		);

		return array_map( 'absint', $ids );
	}

	private function get_date_formatted( WC_DateTime $date ): string {
		return sprintf( __( '%1$s at %2$s', 'woocommerce' ), $date->date_i18n( wc_date_format() ), $date->date_i18n( wc_time_format() ) );
	}

	public function get_value( int $id ) {
		$value = [];

		$order_notes = array_map( 'wc_get_order_note', $this->get_note_ids( $id ) );

		foreach ( $order_notes as $note ) {
			$value[] = [
				'added_by' => null,
				'content'  => $note->content,
				'id'       => $note->id,
				'date'     => $note->date_created instanceof WC_DateTime ? $this->get_date_formatted( $note->date_created ) : null,
			];
		}

		return $value ?: null;
	}

}
