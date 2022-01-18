<?php declare(strict_types = 1);

namespace MailPoet\Premium\Segments\DynamicSegments;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;

class SegmentCombinations {
  /**
   * @param array<string, array<mixed>> $data
   * @param callable $processFilter
   * @return DynamicSegmentFilterData[]
   */
  public function mapMultipleFilters(array $data, callable $processFilter): array {
    $filters = [];
    foreach ($data['filters'] as $filter) {
      $filters[] = $processFilter($filter, $data);
    }
    return $filters;
  }

  /**
   * @param callable $createOrUpdateFilter
   * @param DynamicSegmentFilterData[] $filtersData
   * @return void
   */
  public function saveMultipleFilters(callable $createOrUpdateFilter, array $filtersData): void {
    foreach ($filtersData as $key => $filterData) {
      $createOrUpdateFilter($filterData, $key);
    }
  }
}
