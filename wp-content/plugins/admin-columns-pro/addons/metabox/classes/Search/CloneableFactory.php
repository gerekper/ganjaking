<?php

namespace ACA\MetaBox\Search;

use ACA\MetaBox\Column;

interface CloneableFactory {

	public function create_cloneable( Column $column );

}