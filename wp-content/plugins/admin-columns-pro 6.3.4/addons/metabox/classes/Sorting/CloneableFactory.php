<?php

namespace ACA\MetaBox\Sorting;

use ACA\MetaBox\Column;

interface CloneableFactory {

	public function create_cloneable( Column $column );

}