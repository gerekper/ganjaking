<?php

namespace ACA\MetaBox;

abstract class Factory {

	abstract public function create( Column $column );

	abstract public function create_disabled( Column $column );

	abstract public function create_default( Column $column );

}