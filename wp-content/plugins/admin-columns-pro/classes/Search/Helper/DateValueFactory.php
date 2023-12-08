<?php

declare(strict_types=1);

namespace ACP\Search\Helper;

use ACP\Search\Value;
use DateTime;

class DateValueFactory
{

    protected $type;

    protected $format;

    public function __construct(string $type, string $format = null)
    {
        if (null === $format) {
            $format = $this->get_format_from_type($type);
        }

        $this->type = $type;
        $this->format = $format;
    }

    protected function get_format_from_type(string $type): string
    {
        if ($type === Value::INT) {
            return 'U';
        }

        return 'Y-m-d H:i:s';
    }

    public function create(DateTime $date): Value
    {
        return new Value(
            $date->format($this->format),
            $this->type
        );
    }

    public function create_range(DateTime $start, DateTime $end): Value
    {
        return new Value(
            [
                $start->format($this->format),
                $end->format($this->format),
            ],
            $this->type
        );
    }

    public function create_range_today(): Value
    {
        return $this->create_range_single_day(new DateTime());
    }

    public function create_range_single_day(DateTime $day): Value
    {
        $day->setTime(0, 0);
        $end = clone $day;
        $end->modify('+1 day -1 second');

        return $this->create_range($day, $end);
    }

    public function create_less_than_days_ago(int $days): Value
    {
        $date = new DateTime();
        $date->setTime(0, 0);
        $date->modify(sprintf('-%s day', $days));

        return $this->create_range($date, new DateTime());
    }

    public function create_single_day(DateTime $day): Value
    {
        $day->setTime(0, 0);

        return new Value(
            $day->format($this->format),
            $this->type
        );
    }

    public function create_range_year(int $year): Value
    {
        $start = new DateTime(
            sprintf('%s-01-01 00:00:00', $year)
        );
        $end = clone $start;
        $end->modify('+1 year')
            ->modify('-1 second');

        return $this->create_range($start, $end);
    }

    public function create_range_month(string $year_month): Value
    {
        $year = (int)substr($year_month, 0, 4);
        $month = (int)substr($year_month, 4, 2);

        $start = new DateTime(
            sprintf('%s-%s-01 00:00:00', $year, $month)
        );

        $end = clone $start;
        $end->modify('+1 month')
            ->modify('-1 second');

        return $this->create_range($start, $end);
    }

    public function create_today(): Value
    {
        $date = new DateTime();
        $date->setTime(0, 0);

        return new Value(
            $date->format($this->format),
            $this->type
        );
    }

}