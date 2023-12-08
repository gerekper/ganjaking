<?php

namespace ACA\Types\Field;

use ACA\Types\Field;
use ACA\Types\Search;
use ACP;

class Skype extends Field
{

    public function is_serialized()
    {
        return true;
    }

    public function search()
    {
        return new Search\Skype($this->column->get_meta_key());
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this->column);
    }

    /**
     * @param array $skype
     *
     * @return string
     */
    protected function format($skype)
    {
        if (empty($skype['skypename'])) {
            return false;
        }

        return ac_helper()->html->link('skype:' . $skype['skypename'] . '?' . $skype['action'], $skype['skypename']);
    }

}