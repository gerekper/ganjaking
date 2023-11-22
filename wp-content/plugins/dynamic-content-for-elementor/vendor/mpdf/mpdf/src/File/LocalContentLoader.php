<?php

namespace DynamicOOOS\Mpdf\File;

class LocalContentLoader implements \DynamicOOOS\Mpdf\File\LocalContentLoaderInterface
{
    public function load($path)
    {
        return \file_get_contents($path);
    }
}
