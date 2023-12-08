<?php

namespace ACA\Pods\Field\Pick;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACP;
use PodsField_Pick;

class PostStatus extends Field\Pick
{

    use Editing\DefaultServiceTrait;

    public function sorting()
    {
        $options = $this->get_options();
        natcasesort($options);

        return (new ACP\Sorting\Model\MetaMappingFactory())->create(
            $this->get_meta_type(),
            $this->get_meta_key(),
            array_keys($options)
        );
    }

    public function get_options()
    {
        if ( ! class_exists('PodsField_Pick')) {
            return [];
        }

        $pod = new PodsField_Pick();

        return $pod->data_post_stati();
    }

}