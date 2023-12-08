<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat\Service;

use AC;
use AC\Registerable;
use AC\Type\UserId;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\Formattable;
use ACP\ConditionalFormat\Operators;
use ACP\ConditionalFormat\RulesRepositoryFactory;
use ACP\Expression\ComparisonOperators;
use ACP\Expression\ComparisonSpecification;
use ACP\Expression\ContainsSpecification;
use ACP\Expression\DateComparisonSpecification;
use ACP\Expression\DateOperators;
use ACP\Expression\DateRelativeDaysSpecification;
use ACP\Expression\DateRelativeDeductedSpecification;
use ACP\Expression\EndsWithSpecification;
use ACP\Expression\Exception\InvalidDateFormatException;
use ACP\Expression\Exception\OperatorNotFoundException;
use ACP\Expression\FloatComparisonSpecification;
use ACP\Expression\IntegerComparisonSpecification;
use ACP\Expression\Specification;
use ACP\Expression\StartsWithSpecification;
use ACP\Expression\StringOperators;
use RuntimeException;

final class Formatter implements Registerable
{

    /**
     * @var Operators
     */
    private $operators;

    /**
     * @var RulesRepositoryFactory
     */
    private $rules_repository_factory;

    public function __construct(Operators $operators, RulesRepositoryFactory $rules_repository_factory)
    {
        $this->operators = $operators;
        $this->rules_repository_factory = $rules_repository_factory;
    }

    public function register(): void
    {
        // Do not format on export
        if (did_action('acp/export/before_batch')) {
            return;
        }

        add_filter('ac/column/value', [$this, 'format_value'], 10, 3);
    }

    /**
     * We use this hook callback to ensure we can use te format method with correct property types
     *
     * @param string    $value
     * @param           $id
     * @param AC\Column $column
     *
     * @return string
     */
    public function format_value($value, $id, AC\Column $column)
    {
        if ( ! is_scalar($value)) {
            return $value;
        }

        return $this->format((string)$value, $id, $column);
    }

    /**
     * Comparisons are done case-insensitive
     */
    public function format(string $value, $id, AC\Column $column): string
    {
        if ( ! $column->get_list_screen()->has_id()) {
            return $value;
        }

        if ( ! $column instanceof Formattable || ! $column->conditional_format()) {
            return $value;
        }

        $initial_value = $value;

        $config = $column->conditional_format();

        $rules_repository = $this->rules_repository_factory->create($column->get_list_screen()->get_id());
        $rules = $rules_repository->find_by_column(
            new UserId(get_current_user_id()),
            $column->get_name()
        );

        foreach ($rules as $rule) {
            if ( ! $this->operators->has_operator($rule->get_operator())) {
                continue;
            }

            try {
                $specification = $this->get_specification(
                    $rule->get_operator(),
                    $config->get_value_formatter()->get_type(),
                    $rule->has_fact() ? $rule->get_fact() : null
                );

                $value = $column->conditional_format()->get_value_formatter()->format(
                    $value,
                    $id,
                    $column,
                    $this->operators->get_group($rule->get_operator())
                );

                if ($specification->is_satisfied_by(strtolower($value))) {
                    return sprintf('<div class="%s">%s</div>', esc_attr($rule->get_format()), $initial_value);
                }
            } catch (RuntimeException $e) {
                continue;
            }
        }

        return $initial_value;
    }

    private function sanitize_fact($fact): string
    {
        return is_string($fact)
            ? strtolower($fact)
            : (string)$fact;
    }

    /**
     * @throws InvalidDateFormatException
     */
    private function get_specification(string $operator, string $type, $fact = null): Specification
    {
        switch ($operator) {
            case StringOperators::STARTS_WITH:
                return new StartsWithSpecification($this->sanitize_fact($fact));
            case StringOperators::ENDS_WITH:
                return new EndsWithSpecification($this->sanitize_fact($fact));
            case StringOperators::CONTAINS:
                return new ContainsSpecification($this->sanitize_fact($fact));
            case StringOperators::NOT_CONTAINS:
                $specification = new ContainsSpecification($this->sanitize_fact($fact));

                return $specification->not();
            case DateOperators::TODAY:
            case DateOperators::FUTURE:
            case DateOperators::PAST:
                return new DateRelativeDeductedSpecification($operator);
            case DateOperators::WITHIN_DAYS:
            case DateOperators::GT_DAYS_AGO:
            case DateOperators::LT_DAYS_AGO:
                return new DateRelativeDaysSpecification((int)$fact, $operator);
            case DateOperators::DATE_IS:
            case DateOperators::DATE_IS_AFTER:
            case DateOperators::DATE_IS_BEFORE:
                return new DateComparisonSpecification($fact, $operator);
            case DateOperators::DATE_BETWEEN:
                $specification = $this->get_specification(DateOperators::DATE_IS_AFTER, $type, $fact[0]);

                return $specification->and_specification(
                    $this->get_specification(DateOperators::DATE_IS_BEFORE, $type, $fact[1])
                );
            case ComparisonOperators::EQUAL:
            case ComparisonOperators::NOT_EQUAL:
            case ComparisonOperators::LESS_THAN:
            case ComparisonOperators::LESS_THAN_EQUAL:
            case ComparisonOperators::GREATER_THAN:
            case ComparisonOperators::GREATER_THAN_EQUAL:
                switch ($type) {
                    case ConditionalFormat\Formatter::INTEGER:
                        return new IntegerComparisonSpecification((int)$fact, $operator);
                    case ConditionalFormat\Formatter::FLOAT:
                        return new FloatComparisonSpecification((float)$fact, $operator);
                    case ConditionalFormat\Formatter::DATE:
                        if (false !== filter_var($fact, FILTER_SANITIZE_NUMBER_INT)) {
                            return new IntegerComparisonSpecification((int)$fact, $operator);
                        }

                        if (false !== filter_var($fact, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)) {
                            return new FloatComparisonSpecification((int)$fact, $operator);
                        }
                }

                return new ComparisonSpecification($this->sanitize_fact($fact), $operator);
            case ComparisonOperators::BETWEEN:
            case ComparisonOperators::NOT_BETWEEN:
                $specification = $this->get_specification(
                    ComparisonOperators::GREATER_THAN_EQUAL,
                    $type,
                    $fact[0]
                );

                $specification = $specification->and_specification(
                    $this->get_specification(
                        ComparisonOperators::LESS_THAN_EQUAL,
                        $type,
                        $fact[1]
                    )
                );

                if ($operator === ComparisonOperators::NOT_BETWEEN) {
                    $specification = $specification->not();
                }

                return $specification;
        }

        throw new OperatorNotFoundException($operator);
    }

}