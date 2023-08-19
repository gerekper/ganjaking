<?php

declare(strict_types=1);

namespace ACP\Search;

use AC\Plugin;
use ACP\Search\Storage\Table\Segment;

final class Install implements Plugin\Install
{

    private $table;

    public function __construct(Segment $table)
    {
        $this->table = $table;
    }

    public function install(): void
    {
        if ( ! $this->table->exists()) {
            $this->table->create();
        }
    }

}