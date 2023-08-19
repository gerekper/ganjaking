<?php

namespace ACA\WC\Settings\ShopOrder;

use AC;

class Notes extends AC\Settings\Column {

	const NAME = 'note_property';
	const COUNT_VALUE = 'count';
	const LATEST_VALUE = 'latest';

	/**
	 * @var string
	 */
	private $note_property;

	protected function set_name() {
		$this->name = self::NAME;
	}

	protected function define_options() {
		return [
			self::NAME => self::COUNT_VALUE,
		];
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_options( [
			               self::COUNT_VALUE  => __( 'Count', 'codepress-admin-columns' ),
			               self::LATEST_VALUE => __( 'Last Order Note', 'codepress-admin-columns' ),
		               ] );

		return new AC\View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	/**
	 * @return string
	 */
	public function get_note_property() {
		return $this->note_property;
	}

	/**
	 * @param string $note_property
	 */
	public function set_note_property( $note_property ) {
		$this->note_property = $note_property;
	}

}