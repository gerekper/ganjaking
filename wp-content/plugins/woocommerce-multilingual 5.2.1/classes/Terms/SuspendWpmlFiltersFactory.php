<?php

namespace WCML\Terms;

use WCML\Utilities\Suspend\Filters as SuspendFilters;
use function WCML\functions\getSitePress;

class SuspendWpmlFiltersFactory {

	/**
	 * @return \WCML\Utilities\Suspend\Suspend
	 */
	public static function create() {
		$sitepress = getSitePress();

		return new SuspendWpmlFilters(
			new SuspendFilters( [
				[ 'get_term', [ $sitepress, 'get_term_adjust_id' ], 1, 1 ],
				[ 'get_terms', [ 'WPML_Terms_Translations', 'get_terms_filter' ], 10, 2 ],
				[ 'terms_clauses', [ $sitepress, 'terms_clauses' ], 10, 3 ],
			] )
		);
	}
}
