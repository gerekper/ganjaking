<?php

namespace ACP\Editing\Model\Post;

use AC\Column;
use ACP\Editing;
use ACP\Editing\Settings\EditableType;
use ACP\Editing\View;

/**
 * @deprecated 5.6
 */
class Content extends Editing\Service\Post\Content {

	public function __construct( Column $column ) {

		/* @var EditableType\Content $setting */
		$setting = $column->get_setting( EditableType\Content::NAME );

		$view = new View\TextArea();

		if ( $setting && EditableType\Content::TYPE_WYSIWYG === $setting->get_editable_type() ) {
			$view = new View\Wysiwyg();
		}

		parent::__construct( $view );
	}

}