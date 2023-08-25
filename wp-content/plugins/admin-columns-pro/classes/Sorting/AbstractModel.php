<?php

namespace ACP\Sorting;

use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Type\DataType;

/**
 * Backwards compatible model for version older then NEWVERSION
 * @deprecated NEWVERSION
 */
class AbstractModel
{

    public function __construct()
    {
    }

    /**
     * @var DataType
     */
    protected $data_type;

    /**
     * @var Strategy
     */
    protected $strategy;

    /**
     * @var string
     */
    protected $order;

    /**
     * @depecated NEWVERSION
     */
    public function set_strategy(Strategy $strategy): void
    {
        _deprecated_function(__FUNCTION__, 'NEWVERSION');

        $this->strategy = $strategy;
    }

    public function get_sorting_vars()
    {
        _deprecated_function(__FUNCTION__, 'NEWVERSION', QueryBindings::class);

        return [];
    }

    public function get_order(): string
    {
        _deprecated_function(__FUNCTION__, 'NEWVERSION');

        return $this->order;
    }

    /**
     * @depecated NEWVERSION
     */
    public function set_order(string $order): void
    {
        _deprecated_function(__FUNCTION__, 'NEWVERSION');

        $this->order = $order;
    }

    /**
     * Sorts an array ascending, maintains index association and returns keys
     */
    public function sort(array $array): array
    {
        _deprecated_function(__METHOD__, '5.2');

        return (new Sorter())->sort($array, new DataType(DataType::STRING));
    }

}