<?php

declare(strict_types=1);

namespace ACP\ListScreenRepository;

use AC\ListScreen;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Filter;
use AC\Type\ListScreenId;

trait FilteredListScreenRepositoryTrait
{

    protected function find_from_source(ListScreenId $id): ?ListScreen
    {
        $list_screens = (new Filter\ListScreenId($id))->filter(
            $this->find_all_from_source()
        );

        return $list_screens->count()
            ? $list_screens->get_first()
            : null;
    }

    protected function find_all_by_key_from_source(string $key): ListScreenCollection
    {
        return (new Filter\ListScreenKey($key))->filter(
            $this->find_all_from_source()
        );
    }

    abstract protected function find_all_from_source(): ListScreenCollection;

}