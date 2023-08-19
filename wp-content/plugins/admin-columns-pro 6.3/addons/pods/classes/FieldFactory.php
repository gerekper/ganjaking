<?php

namespace ACA\Pods;

use ACA\Pods\Field\Boolean;
use ACA\Pods\Field\Code;
use ACA\Pods\Field\Color;
use ACA\Pods\Field\Currency;
use ACA\Pods\Field\Date;
use ACA\Pods\Field\Datetime;
use ACA\Pods\Field\Email;
use ACA\Pods\Field\File;
use ACA\Pods\Field\Number;
use ACA\Pods\Field\Paragraph;
use ACA\Pods\Field\Password;
use ACA\Pods\Field\Phone;
use ACA\Pods\Field\Text;
use ACA\Pods\Field\Time;
use ACA\Pods\Field\Website;
use ACA\Pods\Field\Wysiwyg;

class FieldFactory {

	/**
	 * @param string $type
	 * @param Column $column
	 * @param string $subtype
	 *
	 * @return Field
	 */
	public function create( $type, Column $column, $subtype = null ) {

		switch ( $type ) {
			case 'boolean' :
				return new Boolean( $column );
			case 'code' :
				return new Code( $column );
			case 'color' :
				return new Color( $column );
			case 'currency' :
				return new Currency( $column );
			case 'date' :
				return new Date( $column );
			case 'datetime' :
				return new Datetime( $column );
			case 'email' :
				return new Email( $column );
			case 'file' :
				return new File( $column );
			case 'number' :
				return new Number( $column );
			case 'paragraph' :
				return new Paragraph( $column );
			case 'password' :
				return new Password( $column );
			case 'phone' :
				return new Phone( $column );
			case 'pick' :
				return ( new FieldPickFactory() )->create( $subtype, $column );
			case 'text' :
				return new Text( $column );
			case 'time' :
				return new Time( $column );
			case 'website' :
				return new Website( $column );
			case 'wysiwyg' :
				return new Wysiwyg( $column );
			default :
				return new Field( $column );
		}
	}

}