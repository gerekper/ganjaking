<?php
/**
 * SearchWP MissingIntegrationAdminNotice.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin\AdminNotices;

use SearchWP\Admin\AdminNotice;

/**
 * Class MissingIntegrationAdminNotice indicates a known missing integration Extension.
 *
 * @since 4.0
 */
class MissingIntegrationAdminNotice extends AdminNotice {

	protected $dismissible = true;
	protected $type        = 'warning';

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 * @param string $slug        The slug for this Integration Extension.
	 * @param array  $integration The details of the Integration Extension.
	 * @return void
	 */
	function __construct( string $slug, array $integration ) {
		$this->slug    = 'missing-integration-' . $slug;
		$this->message = sprintf(
			// Translators: 1st placeholder is a link, 2nd is a plugin name, 3rd closes the link, 4th is a link to an Extension, 5th is the Extension name, 6th closes the link.
			__( '<strong>Missing SearchWP Integration Extension</strong>. For full integration with %1$s%2$s%3$s please install the %4$s%5$s%6$s Extension.', 'searchwp' ),
			'<a href="' . esc_url( $integration['plugin']['url'] ) . '" target="_blank">',
			$integration['plugin']['name'],
			'</a>',
			'<a href="' . esc_url( $integration['integration']['url'] ) . '" target="_blank">',
			$integration['integration']['name'],
			'</a>'
		);

		parent::__construct();
	}
}