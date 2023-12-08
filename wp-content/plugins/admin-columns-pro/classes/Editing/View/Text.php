<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class Text extends View implements Placeholder, MaxLength
{

    use MaxlengthTrait;
    use PlaceholderTrait;

    public function __construct()
    {
        parent::__construct('text');
    }

}