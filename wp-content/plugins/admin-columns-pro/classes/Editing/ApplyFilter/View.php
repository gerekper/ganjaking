<?php

namespace ACP\Editing\ApplyFilter;

use AC;
use ACP\Editing;
use ACP\Editing\Service;

class View
{

    private $column;

    private $context;

    private $service;

    public function __construct(AC\Column $column, string $context, Service $service)
    {
        $this->column = $column;
        $this->context = $context;
        $this->service = $service;
    }

    public function apply_filters(Editing\View $view = null): ?Editing\View
    {
        $view = apply_filters('acp/editing/view', $view, $this->column, $this->context, $this->service);

        return $view instanceof Editing\View
            ? $view
            : null;
    }

}