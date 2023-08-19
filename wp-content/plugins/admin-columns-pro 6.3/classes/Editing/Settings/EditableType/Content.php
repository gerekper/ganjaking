<?php

namespace ACP\Editing\Settings\EditableType;

use AC;
use AC\Helper\Select;
use ACP\Editing\Settings\EditableType;

class Content extends EditableType {

	const TYPE_TEXTAREA = 'textarea';
	const TYPE_WYSIWYG = 'wysiwyg';

	public function __construct( AC\Column $column, $default = null ) {
		$options = new Select\Options( [
			new Select\Option( self::TYPE_TEXTAREA, __( 'Textarea', 'codepress-admin-columns' ) ),
			new Select\Option( self::TYPE_WYSIWYG, __( 'WYSIWYG', 'codepress-admin-columns' ) ),
		] );

		parent::__construct( $column, $options, $default );
	}

}