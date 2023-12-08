<?php
declare(strict_types=1);

namespace ACP\Expression;

use DateTime;
use DateTimeZone;

final class DateComparisonSpecification extends DateSpecification
{

    use SpecificationTrait;
    use ComparisonTrait;

    /**
     * @var DateTime
     */
    protected $fact;

    /**
     * @throws Exception\InvalidDateFormatException
     */
    public function __construct(
        string $fact,
        string $operator,
        string $format = null,
        DateTimeZone $time_zone = null
    ) {
        parent::__construct($format, $time_zone);

        $this->fact = $this->create_date_from_value($fact);
        $this->operator = $this->map_operator($operator);

        $this->validate_operator();
    }

    private function map_operator($operator)
    {
        $map = [
            DateOperators::DATE_IS        => ComparisonOperators::EQUAL,
            DateOperators::DATE_IS_AFTER  => ComparisonOperators::LESS_THAN,
            DateOperators::DATE_IS_BEFORE => ComparisonOperators::GREATER_THAN,
        ];

        return $map[$operator] ?? $operator;
    }

    /**
     * @throws Exception\InvalidDateFormatException
     */
    public function is_satisfied_by(string $value): bool
    {
        return $this->compare(
            $this->operator,
            (int)$this->create_date_from_value($value)->format('U'),
            (int)$this->fact->format('U')
        );
    }

}