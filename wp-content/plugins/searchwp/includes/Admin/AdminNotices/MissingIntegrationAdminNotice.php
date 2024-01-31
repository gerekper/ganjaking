<?php
/**
 * SearchWP MissingIntegrationAdminNotice.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin\AdminNotices;

use SearchWP\Admin\AdminNotice;
use SearchWP\License;

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

		$this->slug = 'missing-integration-' . $slug;

		$license_requirement = '';

		if ( ! License::is_active() ) {
			$license_requirement = sprintf(
				// Translators: 1st placeholder is a link, 2nd closes the link.
				__( '%1$senter your license key%2$s and', 'searchwp' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=searchwp-settings' ) ) . '">',
				'</a>'
			);
		} elseif ( License::get_type() === 'standard' ) {
			$license_requirement = sprintf(
				// Translators: 1st placeholder is a link, 2nd closes the link.
				__( '%1$supgrade your license to Pro/Agency%2$s and', 'searchwp' ),
				'<a href="https://searchwp.com/account/downloads/?utm_source=WordPress&utm_medium=Missing+Integration+Admin+Notice&utm_campaign=SearchWP&utm_content=Upgrade+Your+License+To+Pro+Agency" target="_blank">',
				'</a>'
			);
		}

		$this->message = sprintf(
			// Translators: 1st placeholder is a plugin name, 2nd is an action requirement like "enter your license and", 3rd is a link to an Extension, 4th is the Extension name, 5th closes the link.
			__( '<strong>Missing SearchWP Integration Extension</strong>. For full integration with %1$s please %2$s install the %3$s%4$s%5$s Extension.', 'searchwp' ),
			$integration['plugin']['name'],
			$license_requirement,
			'<a href="' . esc_url( $integration['integration']['url'] ) . '" target="_blank">',
			$integration['integration']['name'],
			'</a>'
		);

		parent::__construct();
	}
}
