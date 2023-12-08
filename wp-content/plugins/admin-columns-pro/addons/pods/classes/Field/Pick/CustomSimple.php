<?php

namespace ACA\Pods\Field\Pick;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACP;
use PodsField_Pick;
use PodsForm;

class CustomSimple extends Field\Pick
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
        $_field = PodsForm::field_loader($this->get('type'));

        if ( ! $_field instanceof PodsField_Pick) {
            return [];
        }

        return $_field->get_field_data($this->column->get_pod_field());
    }

}