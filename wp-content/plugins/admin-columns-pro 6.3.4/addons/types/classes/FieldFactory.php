<?php

namespace ACA\Types;

use ACA\Types\Field\Audio;
use ACA\Types\Field\Checkbox;
use ACA\Types\Field\Checkboxes;
use ACA\Types\Field\Colorpicker;
use ACA\Types\Field\Date;
use ACA\Types\Field\Email;
use ACA\Types\Field\Embed;
use ACA\Types\Field\File;
use ACA\Types\Field\Image;
use ACA\Types\Field\Number;
use ACA\Types\Field\Phone;
use ACA\Types\Field\Radio;
use ACA\Types\Field\Select;
use ACA\Types\Field\Skype;
use ACA\Types\Field\Textarea;
use ACA\Types\Field\Textfield;
use ACA\Types\Field\Url;
use ACA\Types\Field\Video;
use ACA\Types\Field\Wysiwyg;

class FieldFactory {

	/**
	 * @param string $type
	 * @param Column $column
	 *
	 * @return Field
	 */
	public function create( $type, Column $column ) {

		switch ( $type ) {
			case 'audio' :
				return new Audio( $column );
			case 'checkbox' :
				return new Checkbox( $column );
			case 'checkboxes' :
				return new Checkboxes( $column );
			case 'colorpicker' :
				return new Colorpicker( $column );
			case 'date' :
				return new Date( $column );
			case 'email' :
				return new Email( $column );
			case 'embed' :
				return new Embed( $column );
			case 'file' :
				return new File( $column );
			case 'image' :
				return new Image( $column );
			case 'numeric' :
				return new Number( $column );
			case 'phone' :
				return new Phone( $column );
			case 'radio' :
				return new Radio( $column );
			case 'select' :
				return new Select( $column );
			case 'skype' :
				return new Skype( $column );
			case 'textarea' :
				return new Textarea( $column );
			case 'textfield' :
				return new Textfield( $column );
			case 'url' :
				return new Url( $column );
			case 'video' :
				return new Video( $column );
			case 'wysiwyg' :
				return new Wysiwyg( $column );
			default :
				return new Field( $column );
		}
	}

}