<?php

namespace ACP\Editing\Settings;

use AC;
use AC\Column;
use AC\View;

class EditableType extends AC\Settings\Column {

	const NAME = 'editable_type';

	/**
	 * @var string
	 */
	private $editable_type;

	/**
	 * @var AC\Helper\Select\Options
	 */
	private $editable_type_options;

	/**
	 * @var string|null
	 */
	private $default_option;

	public function __construct( Column $column, AC\Helper\Select\Options $options, $default = null ) {
		$this->editable_type_options = $options;
		$this->default_option = $default;

		parent::__construct( $column );
	}

	protected function define_options() {
		return [ self::NAME => $this->default_option ];
	}

	protected function get_formatted_options() {
		$options = [];

		foreach ( $this->editable_type_options->get_copy() as $option ) {
			if ( $option instanceof AC\Helper\Select\Option ) {
				$options[ $option->get_value() ] = $option->get_label();
			}
		}

		return $options;
	}

	public function create_view() {
		$select = $this
			->create_element( 'select', 'editable_type' )
			->set_options( $this->get_formatted_options() );

		$view = new View();
		$view->set( 'label', __( 'Input Type', 'codepress-admin-columns' ) )
		     ->set( 'setting', $select );

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_editable_type() {
		return $this->editable_type;
	}

	/**
	 * @param string $editable_type
	 *
	 * @return $this
	 */
	public function set_editable_type( $editable_type ) {
		$this->editable_type = $editable_type;

		return $this;
	}

}