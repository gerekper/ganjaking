<?php

namespace ACA\MetaBox\Export;

use ACA\MetaBox;
use ACA\MetaBox\Column;
use ACP\Export\Model\StrippedValue;
use ACP\Export\Service;

class Factory {

	public function create( Column $column ): Service {
		switch ( true ) {
			case $column instanceof Column\TextList:
				return new StrippedValue( $column );
			case $column instanceof Column\File:
			case $column instanceof Column\Image:
			case $column instanceof Column\Video:
				return new MetaBox\Export\Model\File( $column );
			case $column instanceof Column\FieldsetText:
				return new MetaBox\Export\Model\FieldsetText( $column );
			case $column instanceof Column\Date:
			case $column instanceof Column\Post:
			case $column instanceof Column\Number:
			case $column instanceof Column\CheckboxList:
			case $column instanceof Column\Select:
			case $column instanceof Column\Taxonomy:
			case $column instanceof Column\User:
				return new MetaBox\Export\Model\Formatted( $column );
			default:
				return new MetaBox\Export\Model\Raw( $column );
		}
	}

}