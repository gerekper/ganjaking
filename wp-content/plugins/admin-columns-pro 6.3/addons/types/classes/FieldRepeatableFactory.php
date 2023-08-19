<?php

namespace ACA\Types;

use ACA\Types\Field\Repeatable\Audio;
use ACA\Types\Field\Repeatable\Colorpicker;
use ACA\Types\Field\Repeatable\Date;
use ACA\Types\Field\Repeatable\Email;
use ACA\Types\Field\Repeatable\Embed;
use ACA\Types\Field\Repeatable\File;
use ACA\Types\Field\Repeatable\Image;
use ACA\Types\Field\Repeatable\Number;
use ACA\Types\Field\Repeatable\Phone;
use ACA\Types\Field\Repeatable\Skype;
use ACA\Types\Field\Repeatable\Textarea;
use ACA\Types\Field\Repeatable\Textfield;
use ACA\Types\Field\Repeatable\Url;
use ACA\Types\Field\Repeatable\Video;

class FieldRepeatableFactory {

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
			default :
				return new Field( $column );
		}
	}

}