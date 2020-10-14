<?php
/**
 * SearchWP MissingEngineSourceAdminNotice.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin\AdminNotices;

use SearchWP\Admin\AdminNotice;

/**
 * Class MissingEngineSourceAdminNotice displays when an Admin Engine has been defined, but the Source being searched is not added to it.
 *
 * @since 4.0
 */
class MissingEngineSourceAdminNotice extends AdminNotice {

	protected $dismissible = false;
	protected $type        = 'warning';

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 * @param string $engine The Engine name.
	 * @param array  $source The Source name.
	 * @return void
	 */
	function __construct( string $engine, string $source ) {
		$this->slug    = 'missing-engine-source-' . sanitize_title_with_dashes( $engine . ' ' . $source );
		$this->message = sprintf(
			// Translators: 1st placeholder is the name of a Source. 2nd placeholder is the name of an Engine.
			__( 'Note: <strong>%1$s</strong> were not added to the <strong>%2$s</strong> SearchWP Engine, which is used for Admin searches. As a result default WordPress results are shown.', 'searchwp' ),
			$source,
			$engine
		);

		parent::__construct();
	}
}