<?php

namespace ACP\Editing\View;

trait MultipleTrait
{

    public function set_multiple(bool $multiple): self
    {
        $this->set('multiple', $multiple);

        return $this;
    }

}