<?php

namespace SearchWP\Dependencies\Smalot\PdfParser\Encoding;

abstract class AbstractEncoding
{
    public abstract function getTranslations() : array;
}
