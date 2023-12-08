<?php

declare(strict_types=1);

namespace ACP\Search\Helper\Select\User;

use AC\Helper\Select\Options;
use DateTime;

class DateOptionsFactory
{

    public function create_label(string $value): string
    {
        $date = DateTime::createFromFormat('Ym', $value);

        return $date ? $date->format('F Y') : $value;
    }

    private function prepend_month_with_leading_zero(string $date): string
    {
        $year = substr($date, 0, 4);
        $month = sprintf("%02d", substr($date, 4, 2));

        return $year . $month;
    }

    public function create_options(string $db_column): Options
    {
        global $wpdb;

        $db_field = esc_sql($db_column);

        $sql = "
			SELECT DISTINCT CONCAT( YEAR( $db_field ), MONTH( $db_field ) )
			FROM $wpdb->users
			ORDER BY $db_field DESC
		";

        $options = [];

        foreach ($wpdb->get_col($sql) as $date) {
            $options[$this->prepend_month_with_leading_zero($date)] = $this->create_label($date);
        }

        return Options::create_from_array($options);
    }

}