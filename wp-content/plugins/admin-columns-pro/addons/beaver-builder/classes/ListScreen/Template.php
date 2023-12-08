<?php

namespace ACA\BeaverBuilder\ListScreen;

use AC\Type\Uri;
use ACP;

class Template extends ACP\ListScreen\Post
{

    public const POST_TYPE = 'fl-builder-template';

    private $template_page;

    private $custom_label;

    public function __construct(string $template_page, string $label)
    {
        parent::__construct('fl-builder-template', self::POST_TYPE . $template_page);

        $this->template_page = $template_page;
        $this->custom_label = $label;
        $this->label = $label;
        $this->group = 'beaver_builder';
    }

    public function get_label(): ?string
    {
        return $this->custom_label;
    }

    public function get_table_url(): Uri
    {
        return parent::get_table_url()->with_arg('fl-builder-template-type', $this->template_page);
    }

}