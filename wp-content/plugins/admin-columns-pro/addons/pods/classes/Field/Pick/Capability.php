<?php

namespace ACA\Pods\Field\Pick;

use ACA\Pods\Editing;
use ACA\Pods\Field;

class Capability extends Field\Pick
{

    use Editing\DefaultServiceTrait;

    public function get_options()
    {
        return (array)$this->get_pick_field()->data_capabilities();
    }

}