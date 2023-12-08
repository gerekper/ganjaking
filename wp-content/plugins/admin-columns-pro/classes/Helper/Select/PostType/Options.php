<?php

declare(strict_types=1);

namespace ACP\Helper\Select\PostType;

use AC\Helper\Select;
use WP_Post_Type;

class Options extends Select\Options
{

    /**
     * @var WP_Post_Type[]
     */
    private $post_types;

    /**
     * @var array
     */
    private $labels = [];

    private $formatter;

    public function __construct(array $post_types, LabelFormatter $formatter)
    {
        $this->formatter = $formatter;
        array_map([$this, 'set_post_type'], $post_types);
        natcasesort($this->labels);

        parent::__construct($this->get_options());
    }

    private function set_post_type(WP_Post_Type $post_type): void
    {
        $this->post_types[$post_type->name] = $post_type;
        $this->labels[$post_type->name] = $this->formatter->format_label($post_type);
    }

    public function get_post_type(string $name): WP_Post_Type
    {
        return $this->post_types[$name];
    }

    private function get_options(): array
    {
        return self::create_from_array($this->labels)->get_copy();
    }

}