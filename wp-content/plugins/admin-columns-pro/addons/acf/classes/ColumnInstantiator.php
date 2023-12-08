<?php

namespace ACA\ACF;

use ACA\ACF\ConditionalFormatting;
use ACA\ACF\Search;
use ACA\ACF\Sorting;

final class ColumnInstantiator
{

    /**
     * @var ConfigFactory
     */
    private $config_factory;

    /**
     * @var Search\ComparisonFactory
     */
    private $search_factory;

    /**
     * @var Sorting\ModelFactory
     */
    private $sorting_factory;

    /**
     * @var Editing\EditingModelFactory
     */
    private $editing_factory;

    /**
     * @var ConditionalFormatting\FormattableFactory
     */
    private $formattable_factory;

    public function __construct(
        ConfigFactory $config_factory,
        Search\ComparisonFactory $search_factory,
        Sorting\ModelFactory $sorting_factory,
        Editing\EditingModelFactory $editing_factory,
        ConditionalFormatting\FormattableFactory $formattable_factory
    ) {
        $this->config_factory = $config_factory;
        $this->search_factory = $search_factory;
        $this->sorting_factory = $sorting_factory;
        $this->editing_factory = $editing_factory;
        $this->formattable_factory = $formattable_factory;
    }

    public function initiate(Column $column): void
    {
        $config = $this->config_factory->create($column->get_type());

        if ( ! $config) {
            return;
        }

        $column->set_config($config);

        $column->set_search_comparison_factory($this->search_factory);
        $column->set_sorting_model_factory($this->sorting_factory);
        $column->set_editing_model_factory($this->editing_factory);
        $column->set_formattable_factory($this->formattable_factory);
    }

}