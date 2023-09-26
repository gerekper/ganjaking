<?php

namespace ACP\Sorting\UserPreference;

use AC;
use ACP\Sorting\Type;

class SortType
{

    private const OPTION_ORDER = 'order';
    private const OPTION_ORDERBY = 'orderby';

    private $key;

    private $storage;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->storage = new AC\Preferences\Site('sorted_by');
    }

    public function get(): ?Type\SortType
    {
        $data = $this->storage->get($this->key);

        if (empty($data[self::OPTION_ORDERBY])) {
            return null;
        }

        return new Type\SortType(
            (string)$data[self::OPTION_ORDERBY],
            (string)$data[self::OPTION_ORDER]
        );
    }

    public function delete(): bool
    {
        return $this->storage->delete($this->key);
    }

    public function save(Type\SortType $sort_type): void
    {
        $this->storage->set($this->key, [
            self::OPTION_ORDERBY => $sort_type->get_order_by(),
            self::OPTION_ORDER   => $sort_type->get_order(),
        ]);
    }

}