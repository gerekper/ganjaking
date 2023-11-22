<?php

namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
trait ExtensionInfo
{
    public $info;
    public function get_info($info)
    {
        if ($this->info === null) {
            $class = \explode('\\', __CLASS__);
            $class = \array_pop($class);
            $this->info = \DynamicContentForElementor\Extensions::$extensions[$class];
        }
        return $this->info[$info];
    }
    public function get_docs()
    {
        return '';
    }
}
