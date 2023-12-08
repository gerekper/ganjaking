<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat;

use AC\Type\ListScreenId;
use AC\Type\UserId;
use ACP\ConditionalFormat\Entity\Rule;

final class RulesRepository
{

    private const FORMAT = 'format';
    private const FACT = 'fact';
    private const OPERATOR = 'operator';
    private const COLUMN_NAME = 'column_name';

    /**
     * @var string
     */
    private $key;

    public function __construct(ListScreenId $list_screen_id)
    {
        $this->key = 'ac_conditional_format_' . $list_screen_id;
    }

    public function find(UserId $id): RuleCollection
    {
        $rules = get_user_meta($id->get_value(), $this->key, true);

        if ( ! is_array($rules)) {
            $rules = [];
        }

        return $this->create_rules($rules);
    }

    public function find_by_column(UserId $id, string $column_name): RuleCollection
    {
        $rule_collection = new RuleCollection();

        foreach ($this->find($id) as $rule) {
            if ($column_name === $rule->get_column_name()) {
                $rule_collection->add($rule);
            }
        }

        return $rule_collection;
    }

    public function save(UserId $id, RuleCollection $rules): void
    {
        $encoded = [];

        foreach ($rules as $rule) {
            $encoded[] = [
                self::COLUMN_NAME => $rule->get_column_name(),
                self::FORMAT      => $rule->get_format(),
                self::OPERATOR    => $rule->get_operator(),
                self::FACT        => $rule->has_fact() ? $rule->get_fact() : null,
            ];
        }

        update_user_meta($id->get_value(), $this->key, $encoded);
    }

    public function remove(UserId $id): void
    {
        delete_user_meta($id->get_value(), $this->key);
    }

    public function remove_for_all_users(): void
    {
        global $wpdb;

        $wpdb->delete(
            $wpdb->usermeta,
            [
                'meta_key' => $this->key,
            ],
            [
                '%s',
            ]
        );
    }

    private function create_rules(array $encoded_rules): RuleCollection
    {
        $rule_collection = new RuleCollection();

        foreach ($encoded_rules as $encoded_rule) {
            $rule = new Rule(
                $encoded_rule[self::COLUMN_NAME],
                $encoded_rule[self::FORMAT],
                $encoded_rule[self::OPERATOR],
                $encoded_rule[self::FACT] ?? null
            );

            $rule_collection->add($rule);
        }

        return $rule_collection;
    }

}