<?php

namespace ACA\YoastSeo\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class FilterSeoScores extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_filter_yoast_seo_scores', __( 'SEO Scores', 'codepress-admin-columns' ), HideOnScreen\Filters::NAME );
	}

}