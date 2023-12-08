<?php

namespace ACP\Sorting;

/**
 * @deprecated 6.3
 */
abstract class Strategy
{

    protected $model;

    public function __construct(AbstractModel $model)
    {
        $this->model = $model;
    }

    abstract public function manage_sorting(): void;

    /**
     * Add the meta query for sorting to an existing meta query
     */
    protected static function add_meta_query(array $sorting_meta_query, array $meta_query): array
    {
        if (empty($meta_query)) {
            return $sorting_meta_query;
        }

        $meta_query['relation'] = 'AND';
        $meta_query[] = $sorting_meta_query;

        return $meta_query;
    }

    /**
     * Check if a key is a universal id
     */
    protected static function is_universal_id(string $key): bool
    {
        return 'ids' === $key;
    }

}