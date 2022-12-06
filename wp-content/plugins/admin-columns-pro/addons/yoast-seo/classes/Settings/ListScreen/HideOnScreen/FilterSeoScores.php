<?php

namespace ACA\YoastSeo\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class FilterSeoScores extends HideOnScreen {

	const NAME = 'hide_filter_yoast_seo_scores';

	public function __construct() {
		parent::__construct( self::NAME, __( 'SEO Scores', 'codepress-admin-columns' ) );
	}

	public function get_dependent_on() {
		return [ HideOnScreen\Filters::NAME ];
	}

}