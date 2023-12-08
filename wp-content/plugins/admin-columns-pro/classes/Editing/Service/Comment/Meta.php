<?php

declare(strict_types=1);

namespace ACP\Editing\Service\Comment;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Meta implements Service
{

    private $meta_key;

    private $view;

    public function __construct(string $meta_key, View $view = null)
    {
        $this->meta_key = $meta_key;
        $this->view = $view ?: new View\Text();
    }

    public function get_view(string $context): ?View
    {
        return $this->view;
    }

    public function get_value(int $id)
    {
        return (new Storage\Comment\Meta($this->meta_key))->get($id);
    }

    public function update(int $id, $data): void
    {
        (new Storage\Comment\Meta($this->meta_key))->update($id, $data);
    }

}