<?php

namespace ACA\Types\Field;

use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACP\Editing;

class Video extends File
{

    public function editing()
    {
        return new Editing\Service\Basic(
            (new Editing\View\Video())->set_clear_button(true),
            new Storage\File($this->get_meta_key(), new MetaType($this->get_meta_type()))
        );
    }

}