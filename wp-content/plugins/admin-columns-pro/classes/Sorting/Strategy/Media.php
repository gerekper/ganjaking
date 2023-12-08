<?php

namespace ACP\Sorting\Strategy;

use ACP\Sorting\AbstractModel;

/**
 * @depecated 6.3
 */
final class Media extends Post
{

    public function __construct(AbstractModel $model)
    {
        parent::__construct($model, 'attachment');
    }

    protected function get_pagination_per_page(): int
    {
        return (int)get_user_option('upload_per_page');
    }

}