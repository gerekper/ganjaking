<?php

namespace ACA\ACF\Sorting;

use ACA\ACF\Column;
use ACA\ACF\Field;

interface SortingModelFactory
{
    
    public function create(Field $field, string $meta_key, Column $column);

}