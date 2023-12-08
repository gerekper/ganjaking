<?php

namespace ACA\JetEngine\Field;

use ACA\JetEngine\Mapping;
use ACA\JetEngine\Utils\Api;

trait GlossaryOptionsTrait
{

    public function has_glossary_options()
    {
        return isset($this->settings['options_source']) && $this->settings['options_source'] === 'glossary';
    }

    public function get_glossary_options()
    {
        $options = Api::GlossariesMeta()->get_glossary_for_field((int)$this->settings['glossary_id']);

        return Mapping\Options::from_glossary_options($options);
    }

}