<?php

declare(strict_types=1);

namespace ACP\Filtering\Asset;

use AC;
use AC\Asset\Location;
use AC\Asset\Script\Localize\Translation;
use AC\Helper\Select\ArrayMapper;
use AC\Request;
use ACP\Filtering;
use ACP\Filtering\OptionsFactory;
use ACP\Filtering\Settings;
use ACP\Search\Comparison;
use ACP\Search\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

final class TableScriptFactory
{

    private $location;

    private $comparison_factory;

    private $options_factory;

    private $request;

    public function __construct(
        Location $location,
        ComparisonFactory $comparison_factory,
        OptionsFactory $options_factory,
        Request $request
    ) {
        $this->location = $location;
        $this->comparison_factory = $comparison_factory;
        $this->options_factory = $options_factory;
        $this->request = $request;
    }

    public function create(AC\ListScreen $list_screen): AC\Asset\Script
    {
        $script = new AC\Asset\Script(
            'acp-filtering-table',
            $this->location->with_suffix('assets/filtering/js/table.js'),
            ['jquery', 'jquery-ui-datepicker', AC\Asset\Script\GlobalTranslationFactory::HANDLE]
        );

        $script->add_inline_variable('acp_filtering', [
            'filters' => $this->get_filters($list_screen),
            'rules'   => $this->get_rules($list_screen),
        ]);

        $script->localize(
            'acp_filtering_i18n',
            Translation::create([
                'fetching_results'    => __('Fetching options', 'codepress-admin-columns'),
                'label_start_date'    => __('Start date', 'codepress-admin-columns'),
                'label_end_date'      => __('End date', 'codepress-admin-columns'),
                'label_start_number'  => __('Min', 'codepress-admin-columns'),
                'label_end_number'    => __('Max', 'codepress-admin-columns'),
                'filter'              => __('Filter', 'codepress-admin-columns'),
                'no_results'          => __('No options found', 'codepress-admin-columns'),
                'more_search_records' => __('Please enter more characters to narrow down the search results'),
            ])
        );

        return $script;
    }

    private function get_rules(AC\ListScreen $list_screen): array
    {
        $request = $this->request;

        $rules = [];

        foreach ($request->get('acp_filter', []) as $column_name => $value) {
            $column = $list_screen->get_column_by_name($column_name);

            if ( ! $column) {
                continue;
            }

            $comparison = $this->comparison_factory->create($column);

            if ( ! $comparison) {
                continue;
            }

            $setting = $column->get_setting('filter');

            if ( ! $setting instanceof Settings || ! $setting->is_active()) {
                continue;
            }

            $rules[] = [
                'column_name' => $column->get_name(),
                'value'       => $value,
                'label'       => $this->get_option_label($comparison, $value),
            ];
        }

        return $rules;
    }

    private function get_option_label(Comparison $comparison, $value): ?string
    {
        if ($value === Filtering\EmptyOptions::IS_EMPTY) {
            return $comparison->get_labels()[Operators::IS_EMPTY];
        }

        if ($value === Filtering\EmptyOptions::NOT_IS_EMPTY) {
            return $comparison->get_labels()[Operators::NOT_IS_EMPTY] ?? $value;
        }

        if ($comparison instanceof Comparison\RemoteValues && is_scalar($value)) {
            return $comparison->format_label($value);
        }

        if ($comparison instanceof Comparison\SearchableValues) {
            return $comparison->format_label($value);
        }

        return null;
    }

    private function get_filters(AC\ListScreen $list_screen): array
    {
        $filters = [];

        foreach ($list_screen->get_columns() as $column) {
            $comparison = $this->comparison_factory->create($column);

            if ( ! $comparison) {
                continue;
            }

            $setting = $column->get_setting('filter');

            if ( ! $setting instanceof Settings || ! $setting->is_active()) {
                continue;
            }

            $filter = [
                'column'        => $column->get_name(),
                'label'         => $this->get_filter_label($column),
                'type'          => $this->get_filter_type($comparison),
                'remote_values' => false,
            ];

            if ($comparison instanceof Comparison\Values) {
                $options = $this->options_factory->create_by_values($comparison);

                if ($options->count() > 0) {
                    $filter['options'] = ArrayMapper::map($options);

                    if ( ! $filter['type']) {
                        $filter['type'] = 'select';
                    }
                }
            }

            if ($this->is_logic_group_only($comparison)) {
                $filter['options'] = ArrayMapper::map($this->options_factory->create_logic_options($comparison));
                $filter['type'] = 'select';
            }

            if ( ! $filter['type']) {
                continue;
            }

            if ($column instanceof Filtering\FilterableDateSetting) {
                $date_type = $column->get_filtering_date_setting();

                if (null !== $date_type) {
                    $filter['type'] = $date_type ? sprintf('date_%s', $date_type) : 'date';
                    $filter['remote_values'] = $comparison instanceof Comparison\RemoteValues;
                }

                if ('future_past' === $date_type) {
                    $filter['type'] = 'select';
                    $filter['options'] = ArrayMapper::map(
                        AC\Helper\Select\Options::create_from_array([
                            'future' => __('Future', 'codepress-admin-columns'),
                            'past'   => __('Past', 'codepress-admin-columns'),
                        ])
                    );
                }
            }

            $filters[] = $filter;
        }

        return $filters;
    }

    private function is_logic_group_only(Comparison $comparison): bool
    {
        if ($this->get_filter_type($comparison)) {
            return false;
        }

        return $comparison->get_operators()->search(Operators::IS_EMPTY) ||
               $comparison->get_operators()->search(Operators::NOT_IS_EMPTY);
    }

    private function get_filter_type(Comparison $comparison): ?string
    {
        if ($comparison->get_operators()->search(Operators::BETWEEN)) {
            switch ($comparison->get_value_type()) {
                case Value::INT:
                case Value::DECIMAL:
                    return 'numeric';
                case Value::DATE:
                    return 'date';
            }
        }

        switch (true) {
            case $comparison instanceof Comparison\Values:
                return 'select';
            case $comparison instanceof Comparison\RemoteValues:
                return 'select_remote';
            case $comparison instanceof Comparison\SearchableValues:
                return 'select_search';
        }

        if ($comparison->get_operators()->search(Operators::CONTAINS)) {
            return 'search';
        }

        return null;
    }

    private function get_filter_label(AC\Column $column): string
    {
        $label = $column->get_label();
        $setting = $column->get_setting('filter');

        if ($setting instanceof Settings) {
            $label = $setting->get_filter_label();

            if ( ! $label) {
                $label = $setting->get_filter_label_default();
            }
        }

        return $label;
    }

}