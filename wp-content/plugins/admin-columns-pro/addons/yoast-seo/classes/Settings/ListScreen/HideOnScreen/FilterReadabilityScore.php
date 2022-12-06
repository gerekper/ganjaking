<?php

namespace ACA\YoastSeo\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class FilterReadabilityScore extends HideOnScreen {

	const NAME = 'hide_filter_yoast_readability_score';

	public function __construct() {
		parent::__construct( self::NAME, __( 'Readability Score', 'codepress-admin-columns' ) );
	}

	public function get_dependent_on() {
		return [ HideOnScreen\Filters::NAME ];
	}

}