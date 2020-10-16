<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Checks if the YOAST_SEO_ZAPIER_INTEGRATION constant is set.
 */
class Zapier_Conditional extends Feature_Flag_Conditional {

	/**
	 * Returns the name of the feature flag.
	 * 'YOAST_SEO_' is automatically prepended to it and it will be uppercased.
	 *
	 * @return string the name of the feature flag.
	 */
	protected function get_feature_flag() {
		return 'ZAPIER_INTEGRATION';
	}
}
