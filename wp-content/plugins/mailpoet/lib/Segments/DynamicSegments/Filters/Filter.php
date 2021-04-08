<?php

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;

interface Filter {
  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder;
}
