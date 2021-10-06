<?php

namespace ACP\Editing\Settings\EditableType;

use AC;
use AC\Helper\Select;
use ACP\Editing\Settings\EditableType;

class Text extends EditableType {

	const TYPE_TEXTAREA = 'textarea';
	const TYPE_TEXT = 'text';

	public function __construct( AC\Column $column, $default = self::TYPE_TEXTAREA ) {
		$options = new Select\Options( [
			new Select\Option( self::TYPE_TEXTAREA, __( 'Textarea', 'codepress-admin-columns' ) ),
			new Select\Option( self::TYPE_TEXT, __( 'Text', 'codepress-admin-columns' ) ),
		] );

		parent::__construct( $column, $options, $default );
	}

}