<?php

namespace SearchWP\Dependencies\RtfHtmlPhp;

class ControlWord extends \SearchWP\Dependencies\RtfHtmlPhp\Element
{
    public $word;
    public $parameter;
    public function toString(int $level)
    {
        return \str_repeat("  ", $level) . "WORD {$this->word} ({$this->parameter})\n";
    }
}
