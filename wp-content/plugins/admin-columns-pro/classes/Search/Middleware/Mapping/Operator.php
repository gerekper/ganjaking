<?php

namespace ACP\Search\Middleware\Mapping;

use ACP\Search\Middleware\Mapping;
use ACP\Search\Operators;

class Operator extends Mapping {

	protected function get_properties() {
		return [
			Operators::EQ           => 'equal',
			Operators::NEQ          => 'not_equal',
			Operators::GT           => 'greater',
			Operators::GTE          => 'greater_or_equal',
			Operators::LT           => 'less',
			Operators::LTE          => 'less_or_equal',
			Operators::CONTAINS     => 'contains',
			Operators::NOT_CONTAINS => 'not_contains',
			Operators::BEGINS_WITH  => 'begins_with',
			Operators::ENDS_WITH    => 'ends_with',
			Operators::IN           => 'in',
			Operators::NOT_IN       => 'not_in',
			Operators::BETWEEN      => 'between',
			Operators::IS_EMPTY     => 'is_empty',
			Operators::NOT_IS_EMPTY => 'is_not_empty',
			Operators::TODAY        => 'date_today',
			Operators::PAST         => 'date_past',
			Operators::FUTURE       => 'date_future',
			Operators::LT_DAYS_AGO  => 'lt_days_ago',
			Operators::GT_DAYS_AGO  => 'gt_days_ago',
			Operators::WITHIN_DAYS  => 'within_days',
			Operators::CURRENT_USER => 'current_user',
		];
	}

}