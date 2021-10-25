<?php
/**
 * PAPRO Helper Functions.
 */

namespace PremiumAddonsPro\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Helper_Functions.
 */
class PAPRO_Helper {

	/**
	 * A list of safe tage for `validate_html_tag` method.
	 */
	const ALLOWED_HTML_WRAPPER_TAGS = array(
		'article',
		'aside',
		'div',
		'footer',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'header',
		'main',
		'nav',
		'p',
		'section',
		'span',
	);

	/**
	 * Valide HTML Tag
	 *
	 * Validates an HTML tag against a safe allowed list.
	 *
	 * @param string $tag HTML tag.
	 *
	 * @return string
	 */
	public static function validate_html_tag( $tag ) {
		return in_array( strtolower( $tag ), self::ALLOWED_HTML_WRAPPER_TAGS, true ) ? $tag : 'div';
	}
}
