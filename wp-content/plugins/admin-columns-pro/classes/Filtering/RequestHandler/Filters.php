<?php

declare(strict_types=1);

namespace ACP\Filtering\RequestHandler;

use AC\ListScreen;
use AC\Message;
use AC\Message\Notice;
use AC\Request;
use ACP\Filtering\EmptyOptions;
use ACP\Filtering\FilterableDateSetting;
use ACP\Query\Bindings;
use ACP\QueryFactory;
use ACP\Search\Comparison;
use ACP\Search\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;
use Exception;

class Filters
{

    public const KEY = 'acp_filter';

    private $list_screen;

    private $comparison_factory;

    public function __construct(ListScreen $list_screen, ComparisonFactory $comparison_factory)
    {
        $this->list_screen = $list_screen;
        $this->comparison_factory = $comparison_factory;
    }

    private function is_request(Request $request): bool
    {
        return (bool)$request->get(self::KEY);
    }

    public function handle(Request $request): void
    {
        if ( ! $this->is_request($request)) {
            return;
        }

        $filters = $request->filter(self::KEY, [], FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if ( ! $filters) {
            return;
        }

        $bindings = $this->create_bindings($filters);

        if ( ! $bindings) {
            return;
        }

        QueryFactory::create(
            $this->list_screen->get_query_type(),
            $bindings
        )->register();
    }

    private function use_operator_contains(Comparison $comparison): bool
    {
        return ! $comparison instanceof Comparison\RemoteValues &&
               ! $comparison instanceof Comparison\SearchableValues &&
               ! $comparison instanceof Comparison\Values &&
               $comparison->get_operators()->search(Operators::CONTAINS);
    }

    private function create_bindings_by_column(string $column_name, $request_value): ?Bindings
    {
        if ('' === $request_value) {
            return null;
        }

        $column = $this->list_screen->get_column_by_name($column_name);

        if ( ! $column) {
            return null;
        }

        $comparison = $this->comparison_factory->create($column);

        if ( ! $comparison) {
            return null;
        }

        $value = new Value($request_value, $comparison->get_value_type());

        $operator = $this->use_operator_contains($comparison)
            ? Operators::CONTAINS
            : Operators::EQ;

        if ($column instanceof FilterableDateSetting && null !== $column->get_filtering_date_setting()) {
            switch ($column->get_filtering_date_setting()) {
                case '':
                case 'daily':
                    return $comparison->get_query_bindings(Operators::EQ, $value);
                case 'monthly':
                    return $comparison->get_query_bindings(Operators::EQ_MONTH, $value);
                case 'yearly':
                    return $comparison->get_query_bindings(Operators::EQ_YEAR, $value);
                case 'future_past':
                    $operator = 'future' === $value->get_value()
                        ? Operators::FUTURE
                        : Operators::PAST;

                    return $comparison->get_query_bindings($operator, $value);
                case 'range':
                    return $this->get_query_bindings_range($value, $comparison);
                default:
                    return null;
            }
        }

        // TODO David Is this the place to fetch all Numeric Range comparisons?
        if (is_array($value->get_value())) {
            return $this->get_query_bindings_range($value, $comparison);
        }

        switch ($value->get_value()) {
            case EmptyOptions::IS_EMPTY:
                return $comparison->get_query_bindings(Operators::IS_EMPTY, new Value(null, $value->get_type()));
            case EmptyOptions::NOT_IS_EMPTY:
                return $comparison->get_query_bindings(Operators::NOT_IS_EMPTY, new Value(null, $value->get_type()));
        }

        return $comparison->get_query_bindings($operator, $value);
    }

    private function get_query_bindings_range(Value $value, Comparison $comparison): ?Bindings
    {
        $min = $value->get_value()[0] ?? null;
        $max = $value->get_value()[1] ?? null;

        switch (true) {
            case $min && $max:
                return $comparison->get_query_bindings(Operators::BETWEEN, $value);
            case $min:
                return $comparison->get_query_bindings(Operators::GTE, new Value($min, $value->get_type()));
            case $max:
                return $comparison->get_query_bindings(Operators::LTE, new Value($max, $value->get_type()));
            default:
                return null;
        }
    }

    private function create_bindings(array $filters): array
    {
        $bindings = [];

        foreach ($filters as $column_name => $value) {
            try {
                $binding = $this->create_bindings_by_column($column_name, $value);
            } catch (Exception $e) {
                $this->display_notice($column_name, $e->getMessage());
                continue;
            }

            if ( ! $binding) {
                continue;
            }

            $bindings[] = $binding;
        }

        return $bindings;
    }

    private function display_notice(string $column_name, string $message): void
    {
        $column = $this->list_screen->get_column_by_name($column_name);

        $label = $column
            ? $column->get_custom_label()
            : $column_name;

        $message = sprintf('Filter %s: %s', sprintf('<strong>%s</strong>', $label), $message);

        (new Notice($message, Message::WARNING))->register();
    }

}