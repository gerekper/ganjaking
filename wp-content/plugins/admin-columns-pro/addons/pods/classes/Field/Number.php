<?php

namespace ACA\Pods\Field;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACP;
use ACP\Search;
use ACP\Sorting\Type\DataType;

class Number extends Field
{

    use Editing\DefaultServiceTrait;

    public function sorting()
    {
        return (new ACP\Sorting\Model\MetaFactory())->create(
            $this->get_meta_type(),
            $this->get_meta_key(),
            new DataType(DataType::NUMERIC)
        );
    }

}