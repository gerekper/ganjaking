<?php
/**
 * SearchWP DeprecatedIntegrationAdminNotice.
 *
 * @package SearchWP
 */

namespace SearchWP\Admin\AdminNotices;

use SearchWP\Admin\AdminNotice;

/**
 * Class DeprecatedIntegrationAdminNotice indicates a deprecated integration Extension.
 *
 * @since 4.3.10
 */
class DeprecatedIntegrationAdminNotice extends AdminNotice {

	/**
	 * Whether this Admin Notice is dismissible.
	 *
	 * @since 4.3.10
	 *
	 * @var boolean
	 */
	protected $dismissible = true;

	/**
	 * The type of this Admin Notice. Supported types are: 'error', 'warning', 'success', or 'info'.
	 *
	 * @since 4.3.10
	 *
	 * @var string
	 */
	protected $type = 'warning';

	/**
	 * Constructor.
	 *
	 * @since 4.3.10
	 *
	 * @param string $slug The slug for this Integration Extension.
	 */
	public function __construct( string $slug ) {

		$this->slug    = 'deprecated-integration-' . $slug;
		$this->message = $this->get_deprecated_notice( $slug );

		parent::__construct();
	}

	/**
	 * Get the notice message for a deprecated Extension.
	 *
	 * @since 4.3.10
	 *
	 * @param string $extension_slug The slug for this Integration Extension.
	 *
	 * @return string
	 */
	private function get_deprecated_notice( $extension_slug ) {
		switch ( $extension_slug ) {
			case 'searchwp-term-priority':
				$notice = sprintf(
					__( 'The <strong>SearchWP Term Archive Priority extension</strong> has been deprecated. Please consider using the <a href="%s" target="_blank">Taxonomy Source instead</a>.', 'searchwp' ),
					'https://searchwp.com/documentation/setup/engines/#source-settings'
				);
				break;
		}

		return $notice ?? '';
	}
}
