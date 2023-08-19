<?php

declare(strict_types=1);

namespace ACA\MLA\Service;

use AC;
use AC\Registerable;
use ACA\MLA\Export\Strategy;
use ACP;

class Export implements Registerable
{

    public function register(): void
    {
        add_filter('ac/export/headers', [$this, 'fix_excel_issue'], 10, 2);
        add_filter('ac/export/value', [$this, 'strip_tags_value'], 10, 2);
    }

    public function strip_tags_value($value, AC\Column $column)
    {
        if ($column->get_group() === ColumnGroup::NAME) {
            $value = strip_tags((string)$value);
        }

        return $value;
    }

    /**
     * Error 'SYLK: File format is not valid' in Excel
     * MS Excel 2003 and 2013 does not allow the first label to start with 'ID'
     */
    public function fix_excel_issue(array $headers, ACP\Export\Strategy $strategy): array
    {
        if ( ! $strategy instanceof Strategy) {
            return $headers;
        }

        foreach ($headers as $name => $label) {
            $first = substr($label, 0, 2);
            $end = substr($label, 2);

            // Rename label 'ID' to 'id'
            if ('ID' === $first) {
                $headers[$name] = strtolower($first) . $end;
            }
            break;
        }

        return $headers;
    }

}