<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat;

use AC\Type\ListScreenId;

final class RulesRepositoryFactory
{

    public function create(ListScreenId $id): RulesRepository
    {
        return new RulesRepository($id);
    }

}