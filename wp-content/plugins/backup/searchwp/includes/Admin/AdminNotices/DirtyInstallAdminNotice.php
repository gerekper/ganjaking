<?php
/**
 * SearchWP DirtyInstallAdminNotice.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin\AdminNotices;

use SearchWP\Admin\AdminNotice;

/**
 * Class DirtyInstallAdminNotice indicates that SearchWP 4 was installed
 * on top of SearchWP 3 instead of following the Migration Guide.
 *
 * @since 4.0
 */
class DirtyInstallAdminNotice extends AdminNotice {

	protected $dismissible = false;
	protected $type        = 'error';

	/**
	 * Constructor.
	 *
	 * @since 4.0.1
	 * @return void
	 */
	function __construct() {
		$this->slug    = 'dirty-installation';
		$this->message = sprintf(
			// Translators: Placeholder is a link to the Migration Guide.
			__( 'Error: Duplicate SearchWP version detected! When upgrading SearchWP from version 3.x to 4 you must follow the %1$s%2$s%3$s which will resove this error.', 'searchwp' ),
			'<a href="https://searchwp.com/?p=218795" target="_blank">',
			__( 'Migration Guide', 'searchwp' ),
			'</a>'
		);

		parent::__construct();
	}
}