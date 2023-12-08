<?php

namespace ACA\Pods\Field\Pick;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACP;

class UsState extends Field\Pick
{

    use Editing\DefaultServiceTrait;

    public function sorting()
    {
        return (new ACP\Sorting\Model\MetaFactory())->create($this->get_meta_type(), $this->get_meta_key());
    }

    public function get_options()
    {
        return (array)$this->get_pick_field()->data_us_states();
    }

}