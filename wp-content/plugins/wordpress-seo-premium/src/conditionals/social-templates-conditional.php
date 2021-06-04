<?php

namespace Yoast\WP\SEO\Premium\Conditionals;

use WPSEO_Options;
use Yoast\WP\SEO\Conditionals\Feature_Flag_Conditional;

/**
 * Checks if the YOAST_SEO_SOCIAL_TEMPLATES constant is set.
 */
class Social_Templates_Conditional extends Feature_Flag_Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		return parent::is_met() && WPSEO_Options::get( 'opengraph', true ) === true;
	}

	/**
	 * Returns the name of the feature flag.
	 * 'YOAST_SEO_' is automatically prepended to it and it will be uppercased.
	 *
	 * @return string the name of the feature flag.
	 */
	protected function get_feature_flag() {
		return 'SOCIAL_TEMPLATES';
	}
}
