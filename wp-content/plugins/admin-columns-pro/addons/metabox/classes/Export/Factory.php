<?php

namespace ACA\MetaBox\Export;

use ACA\MetaBox;
use ACA\MetaBox\Column;

class Factory extends MetaBox\Factory {

	public function create( Column $column ) {
		return $this->create_default( $column );
	}

	public function create_default( Column $column ) {
		switch ( true ) {
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
		}

		return new MetaBox\Export\Model\Raw( $column );
	}

	public function create_disabled( Column $column ) {
		return false;
	}

}