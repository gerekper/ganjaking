<?php

namespace ACA\WC\Editing\ShopOrder;

use ACA\WC;
use ACP;
use ACP\Editing\View;

class LastNote implements ACP\Editing\Service {

	/**
	 * @var WC\Column\ShopOrder\Notes
	 */
	private $column;

	public function __construct( WC\Column\ShopOrder\Notes $column ) {
		$this->column = $column;
	}

	public function get_view( string $context ): ?View {
		return $context === self::CONTEXT_SINGLE
			? new ACP\Editing\View\TextArea()
			: null;
	}

	public function get_value( $id ) {
		$note = $this->get_last_note_for_order( $id );

		return $note ? $note->content : null;
	}

	private function get_last_note_for_order( $id ) {
		return $this->column->get_last_order_note( $id );
	}

	public function update( int $id, $data ): void {
		$note = $this->get_last_note_for_order( $id );

		wp_update_comment( [
			'comment_ID'      => $note ? $note->id : 0,
			'comment_content' => $data,
		] );
	}

}
