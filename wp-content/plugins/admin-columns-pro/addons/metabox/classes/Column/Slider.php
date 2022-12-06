<?php

namespace ACA\MetaBox\Column;

use ACA\MetaBox\Editing;

class Slider extends Number {

	public function editing() {
		return ( new Editing\ServiceFactory\Slider )->create( $this );
	}
}