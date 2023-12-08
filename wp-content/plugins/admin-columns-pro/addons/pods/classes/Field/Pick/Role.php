<?php

namespace ACA\Pods\Field\Pick;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACP;
use PodsField_Pick;

class Role extends Field\Pick
{

    use Editing\DefaultServiceTrait;

    public function sorting()
    {
        return (new ACP\Sorting\Model\MetaFactory())->create($this->get_meta_type(), $this->get_meta_key());
    }

    public function get_options()
    {
        if ( ! class_exists('PodsField_Pick')) {
            return [];
        }

        $pod = new PodsField_Pick();

        return $pod->data_roles();
    }

}