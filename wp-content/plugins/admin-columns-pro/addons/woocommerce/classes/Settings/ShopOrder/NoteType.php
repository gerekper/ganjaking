<?php

namespace ACA\WC\Settings\ShopOrder;

use AC;

class NoteType extends AC\Settings\Column {

	const NAME = 'note_type';
	const SYSTEM_NOTE = 'system';
	const PRIVATE_NOTE = 'private';
	const CUSTOMER_NOTE = 'customer';

	/**
	 * @var string
	 */
	private $note_type;

	protected function set_name() {
		$this->name = self::NAME;
	}

	protected function define_options() {
		return [
			self::NAME => '',
		];
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_attribute( 'data-label', 'update' )
		               ->set_options( [
			               ''                  => __( 'All Notes', 'codepress-admin-columns' ),
			               self::SYSTEM_NOTE   => __( 'System Notes', 'codepress-admin-columns' ),
			               self::PRIVATE_NOTE  => __( 'Private Notes', 'codepress-admin-columns' ),
			               self::CUSTOMER_NOTE => __( 'Notes to Customer', 'codepress-admin-columns' ),
		               ] );

		return new AC\View( [
			'label'   => __( 'Note Type', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	/**
	 * @return string
	 */
	public function get_note_type() {
		return $this->note_type;
	}

	/**
	 * @param string $note_type
	 */
	public function set_note_type( $note_type ) {
		$this->note_type = $note_type;
	}

}