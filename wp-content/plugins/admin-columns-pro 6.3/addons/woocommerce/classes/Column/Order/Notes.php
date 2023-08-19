<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Settings\ShopOrder\NoteType;
use ACP;
use DateTime;

class Notes extends AC\Column implements ACP\Editing\Editable {

	public function __construct() {
		$this->set_type( 'column-order_note' )
		     ->set_label( __( 'Order Notes', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		switch ( $this->get_display_property() ) {
			case Settings\ShopOrder\Notes::LATEST_VALUE:
				$value = $this->get_latest_value( $id );
				break;
			case Settings\ShopOrder\Notes::COUNT_VALUE :
			default:
				$value = $this->get_count_value( $id );
		}

		return $value ?: $this->get_empty_char();
	}

	public function get_last_order_note( $id ) {
		$notes = $this->get_order_notes( $id );

		return count( $notes ) > 0 ? reset( $notes ) : null;
	}

	private function get_count_value( int $id ): string {
		$notes = $this->get_order_notes( $id );

		if ( ! $notes ) {
			return '';
		}

		$content = [];

		foreach ( $notes as $note ) {
			$content[] = sprintf( '<small>%s</small><br>%s', DateTime::createFromFormat( 'Y-m-d H:i:s', $note->date )->format( 'F j, Y - H:i' ), $note->content );
		}

		array_map( 'strip_tags', $content );

		return ac_helper()->html->tooltip( ac_helper()->html->rounded( count( $content ) ), implode( '<br><br>', $content ) );
	}

	private function get_latest_value( int $id ): string {
		$note = $this->get_last_order_note( (int) $id );

		return $note
			? sprintf( '<small>%s</small><br>%s', DateTime::createFromFormat( 'Y-m-d H:i:s', $note->date )->format( 'F j, Y - H:i' ), $note->content )
			: $this->get_empty_char();
	}

	private function get_order_notes( int $order_id ) {
		global $wpdb;

		$sql = $wpdb->prepare( "
			SELECT cc.comment_content AS content, cc.comment_ID AS id, cc.comment_date AS date, cc.comment_author AS author, cm.meta_value AS is_customer_note 
			FROM $wpdb->comments AS cc
			LEFT JOIN $wpdb->commentmeta AS cm ON cc.comment_ID = cm.comment_id AND cm.meta_key = 'is_customer_note'
			WHERE cc.comment_post_ID = %d
			ORDER BY cc.comment_date DESC
		", $order_id );

		$notes = $wpdb->get_results( $sql );

		switch ( $this->get_note_type() ) {
			case Settings\ShopOrder\NoteType::CUSTOMER_NOTE :
				return array_filter( $notes, [ $this, 'is_customer_note' ] );
			case Settings\ShopOrder\NoteType::PRIVATE_NOTE :
				return array_filter( $notes, [ $this, 'is_private_note' ] );
			case Settings\ShopOrder\NoteType::SYSTEM_NOTE :
				return array_filter( $notes, [ $this, 'is_system_note' ] );
			default :
				return $notes;
		}
	}

	public function register_settings(): void {
		$this->add_setting( new Settings\ShopOrder\NoteType( $this ) );
		$this->add_setting( new Settings\ShopOrder\Notes( $this ) );
	}

	private function is_private_note( $note ): bool {
		return ! $this->is_customer_note( $note ) && ! $this->is_system_note( $note );
	}

	private function is_system_note( $note ): bool {
		return __( 'WooCommerce', 'woocommerce' ) === $note->author;
	}

	private function is_customer_note( $note ): bool {
		return '1' === $note->is_customer_note;
	}

	private function get_note_type(): string {
		return $this->get_setting( Settings\ShopOrder\NoteType::NAME )->get_value();
	}

	private function get_display_property(): string {
		return $this->get_setting( Settings\ShopOrder\Notes::NAME )->get_value();
	}

	public function editing() {
		switch ( $this->get_note_type() ) {
			case NoteType::PRIVATE_NOTE :
				return new Editing\ShopOrder\NotesPrivate();
			case NoteType::CUSTOMER_NOTE :
				return new Editing\ShopOrder\NotesToCustomer();
			case NoteType::SYSTEM_NOTE :
				return new Editing\ShopOrder\NotesSystem();
			default:
				return false;
		}
	}

}