<?php

declare(strict_types=1);

namespace ACP\Filtering;

use AC\Helper\Select\Option;
use AC\Helper\Select\OptionGroup;
use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Comparison\RemoteValues;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;
use InvalidArgumentException;

class OptionsFactory
{

    public function create_logic_options(Comparison $comparison): Options
    {
        if ( ! $this->needs_logic_group($comparison)) {
            throw new InvalidArgumentException('Invalid comparison.');
        }

        return new Options([$this->create_logic_group($comparison)]);
    }

    public function create_by_values(Comparison $comparison): Options
    {
        if ( ! $comparison instanceof Values) {
            throw new InvalidArgumentException('Invalid comparison.');
        }

        return $this->needs_logic_group($comparison)
            ? $this->add_logic_group($comparison->get_values(), $comparison)
            : $comparison->get_values();
    }

    public function create_by_remote(Comparison $comparison): Options
    {
        if ( ! $comparison instanceof RemoteValues) {
            throw new InvalidArgumentException('Invalid comparison.');
        }

        return $this->needs_logic_group($comparison)
            ? $this->add_logic_group($comparison->get_values(), $comparison)
            : $comparison->get_values();
    }

    public function create_by_searchable(Comparison $comparison, Options $options): Options
    {
        if ( ! $comparison instanceof SearchableValues) {
            throw new InvalidArgumentException('Invalid comparison.');
        }

        if ($this->needs_logic_group($comparison)) {
            $options = $this->add_logic_group($options, $comparison);
        }

        return $options;
    }

    private function needs_logic_group(Comparison $comparison): bool
    {
        return $comparison->get_operators()->search(Operators::IS_EMPTY) ||
               $comparison->get_operators()->search(Operators::NOT_IS_EMPTY);
    }

    private function add_logic_group(Options $options, Comparison $comparison): Options
    {
        $logic_options = $this->create_logic_group($comparison);

        if ($options->count() < 1) {
            return new Options([$logic_options]);
        }

        $values = $options->get_copy();

        if ( ! $this->contains_group($values)) {
            $values = [$this->wrap_in_group($values)];
        }

        array_unshift(
            $values,
            $logic_options
        );

        return new Options($values);
    }

    private function wrap_in_group(array $options): OptionGroup
    {
        return new OptionGroup(
            __('Values', 'codepress-admin-columns'),
            $options
        );
    }

    private function contains_group(array $options): bool
    {
        return (bool)array_filter($options, [$this, 'is_group']);
    }

    private function is_group($option): bool
    {
        return $option instanceof OptionGroup;
    }

    private function create_logic_group(Comparison $comparison): OptionGroup
    {
        $options = [];

        if ($comparison->get_operators()->search(Operators::IS_EMPTY)) {
            $options[] = new Option(
                EmptyOptions::IS_EMPTY,
                $comparison->get_labels()[Operators::IS_EMPTY]
            );
        }

        if ($comparison->get_operators()->search(Operators::NOT_IS_EMPTY)) {
            $options[] = new Option(
                EmptyOptions::NOT_IS_EMPTY,
                $comparison->get_labels()[Operators::NOT_IS_EMPTY]
            );
        }

        return new OptionGroup(
            __('Logic', 'codepress-admin-columns'),
            $options
        );
    }

}