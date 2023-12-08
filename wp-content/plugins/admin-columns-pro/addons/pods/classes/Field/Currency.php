<?php

namespace ACA\Pods\Field;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACP\Sorting;
use ACP\Sorting\Type\DataType;

class Currency extends Field
{

    use Editing\DefaultServiceTrait;

    public function sorting()
    {
        return (new Sorting\Model\MetaFactory())->create(
            $this->get_meta_type(),
            $this->get_meta_key(),
            new DataType(DataType::NUMERIC)
        );
    }

}