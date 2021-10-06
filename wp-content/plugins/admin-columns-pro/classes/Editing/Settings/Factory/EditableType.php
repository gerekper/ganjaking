<?php

namespace ACP\Editing\Settings\Factory;

use AC;
use ACP;
use ACP\Editing;
use LogicException;

class EditableType implements Editing\Settings\SettingFactoryInterface {

	const TYPE_CONTENT = 'content';
	const TYPE_TEXT = 'text';

	const VALID_TYPES = [
		self::TYPE_CONTENT, self::TYPE_TEXT,
	];

	/**
	 * @var AC\Column
	 */
	private $column;

	/**
	 * @var string
	 */
	private $type;

	public function __construct( AC\Column $column, $type ) {
		$this->column = $column;

		if ( ! in_array( $type, self::VALID_TYPES ) ) {
			throw new LogicException( sprintf( 'Editable Type %s not supported', $type ) );
		}

		$this->type = $type;
	}

	public function create() {
		$setting = new Editing\Settings( $this->column, true );
		$section = $this->create_section();

		if ( $section ) {
			$section->set_values( $this->column->get_options() );
			$setting->add_section( $section );
		}

		return $setting;
	}

	/**
	 * @return Editing\Settings\EditableType
	 */
	private function create_section() {
		switch ( $this->type ) {
			case self::TYPE_TEXT:
				return new Editing\Settings\EditableType\Text( $this->column );
			case self::TYPE_CONTENT:
				return new Editing\Settings\EditableType\Content( $this->column );
			default:
				return null;
		}

	}

}