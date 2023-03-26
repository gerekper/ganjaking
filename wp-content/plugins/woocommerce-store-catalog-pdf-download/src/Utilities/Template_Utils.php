<?php
/**
 * Template utilities.
 *
 * @since 2.1.0
 */

namespace Themesquad\WC_Store_Catalog_PDF_Download\Utilities;

/**
 * Class Template_Utils.
 */
class Template_Utils {

	/**
	 * Gets templates passing attributes and including the file.
	 *
	 * @since 2.1.0
	 *
	 * @param string $template_name The template name.
	 * @param array  $args          Optional. The template arguments.
	 */
	public static function get_template( $template_name, $args = array() ) {
		wc_get_template( $template_name, $args, '', WC_STORE_CATALOG_PDF_DOWNLOAD_PATH . 'templates/' );
	}

	/**
	 * Locates a template and return the path for inclusion.
	 *
	 * @since 2.1.0
	 *
	 * @param string $template_name The template name.
	 * @return string
	 */
	public static function locate_template( $template_name ) {
		return wc_locate_template( $template_name, '', WC_STORE_CATALOG_PDF_DOWNLOAD_PATH . 'templates/' );
	}
}
