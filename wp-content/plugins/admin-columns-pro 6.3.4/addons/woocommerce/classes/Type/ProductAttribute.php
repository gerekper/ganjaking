<?php

namespace ACA\WC\Type;

class ProductAttribute
{

    private const TAXONOMY_PREFIX = 'pa_';

    private $name;

    public function __construct(string $name)
    {
        // e.g. 'pa_color', 'pa_material'
        $this->name = $name;
    }

    public function is_taxonomy(): bool
    {
        return 0 === strpos($this->name, self::TAXONOMY_PREFIX) && taxonomy_exists($this->get_taxonomy_name());
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_taxonomy_name(): string
    {
        return wc_attribute_taxonomy_name(
            urldecode(ac_helper()->string->remove_prefix($this->name, self::TAXONOMY_PREFIX))
        );
    }

    public function get_label(): string
    {
        return $this->is_taxonomy()
            ? wc_attribute_label($this->get_taxonomy_name())
            : wc_attribute_label($this->name);
    }

}