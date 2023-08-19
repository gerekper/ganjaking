<?php

namespace ACA\YoastSeo\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class FilterReadabilityScore extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_filter_yoast_readability_score', __( 'Readability Score', 'codepress-admin-columns' ), HideOnScreen\Filters::NAME );
	}

}