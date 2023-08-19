<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat\Service;

use AC;
use AC\Asset\Location;
use AC\Column;
use AC\ColumnRepository;
use AC\ListScreen;
use AC\Registerable;
use AC\Type\ListScreenId;
use AC\Type\UserId;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\ColumnRepository\FilterByConditionalFormat;
use ACP\ConditionalFormat\Operators;
use ACP\ConditionalFormat\RuleCollection;
use ACP\ConditionalFormat\RulesRepositoryFactory;
use ACP\ConditionalFormat\Settings\ListScreen\HideOnScreenFactory;

final class Assets implements Registerable
{

    /**
     * @var Location\Absolute
     */
    private $location;

    /**
     * @var Operators
     */
    private $operators;

    /**
     * @var RulesRepositoryFactory
     */
    private $rules_repository_factory;

    /**
     * @var HideOnScreenFactory
     */
    private $hide_on_screen_factory;

    public function __construct(
        Location\Absolute $location,
        Operators $operators,
        RulesRepositoryFactory $rules_repository_factory,
        HideOnScreenFactory $hide_on_screen_factory
    ) {
        $this->location = $location;
        $this->operators = $operators;
        $this->rules_repository_factory = $rules_repository_factory;
        $this->hide_on_screen_factory = $hide_on_screen_factory;
    }

    private function is_enabled(ListScreen $list_screen): bool
    {
        return ! $this->hide_on_screen_factory->create()->is_hidden($list_screen);
    }

    private function get_column_labels(array $columns): array
    {
        $data = [];

        foreach ($columns as $column) {
            $data[$column->get_name()] = [
                'label'     => $this->get_column_label($column),
                'operators' => [],
            ];
        }

        return $data;
    }

    private function get_column_label(Column $column): string
    {
        $label = $this->sanitize_column_label($column->get_custom_label());

        if ( ! $label) {
            $label = $this->sanitize_column_label($column->get_label());

            if ( ! $label) {
                $label = $column->get_type();
            }
        }

        return $label;
    }

    /**
     * Allows plain text and dashicons
     */
    private function sanitize_column_label(string $label): string
    {
        if (false === strpos($label, 'dashicons')) {
            $label = strip_tags($label);
        }

        return trim($label);
    }

    public function register(): void
    {
        add_action('ac/table_scripts', function (ListScreen $list_screen) {
            if ( ! $this->is_enabled($list_screen) || ! $list_screen->has_id()) {
                return;
            }

            $columns = (new ColumnRepository($list_screen))->find_all([
                'filter' => new FilterByConditionalFormat(),
            ]);

            $assets = [
                new ConditionalFormat\Asset\Table(
                    $this->location,
                    $this->operators,
                    $this->get_rules($list_screen->get_id(), array_keys($columns)),
                    $this->get_column_labels($columns)
                ),
                new AC\Asset\Style(
                    'acp-cf-table',
                    $this->location->with_suffix('assets/conditional-format/css/table.css')
                ),
            ];

            foreach ($assets as $asset) {
                $asset->enqueue();
            }
        });
    }

    private function get_rules(ListScreenId $list_id, array $column_names): RuleCollection
    {
        $filtered = new RuleCollection();

        $rules = $this->rules_repository_factory
            ->create($list_id)
            ->find(new UserId(get_current_user_id()));

        foreach ($rules as $rule) {
            if (in_array($rule->get_column_name(), $column_names, true)) {
                $filtered->add($rule);
            }
        }

        return $filtered;
    }

}