<?php

namespace WPML\TM\ATE\ClonedSites;

use WPML\UIPage;

class ApiCommunication {

	const SITE_CLONED_ERROR = 426;

	const SITE_MOVED_OR_COPIED_MESSAGE = "WPML has detected a change in your site's URL. To continue translating your site, go to your <a href='%s'>WordPress Dashboard</a> and tell WPML if your site has been <a href='%s'>moved or copied</a>.";
	const SITE_MOVED_OR_COPIED_DOCS_URL = 'https://wpml.org/documentation/translating-your-contents/advanced-translation-editor/using-advanced-translation-editor-when-you-move-or-use-a-copy-of-your-site/?utm_source=plugin&utm_medium=gui&utm_campaign=wpmltm';

	/**
	 * @var Lock
	 */
	private $lock;

	/**
	 * @param Lock $lock
	 */
	public function __construct( Lock $lock ) {
		$this->lock = $lock;
	}

	public function handleClonedSiteError( $response ) {
		if ( self::SITE_CLONED_ERROR === $response['response']['code'] ) {
			$parsedResponse = json_decode( $response['body'], true );
			if ( isset( $parsedResponse['errors'] ) ) {
				$this->handleClonedDetection( $parsedResponse['errors'] );
			}
			return new \WP_Error( self::SITE_CLONED_ERROR, 'Site Moved or Copied - Action Required' );
		}

		return $response;
	}

	public function checkCloneSiteLock() {
		if ( Lock::isLocked() ) {
			$errorMessage = sprintf( __( self::SITE_MOVED_OR_COPIED_MESSAGE, 'sitepress-multilingual-cms' ),
				UIPage::getTMDashboard(),
				self::SITE_MOVED_OR_COPIED_DOCS_URL
			);

			return new \WP_Error( self::SITE_CLONED_ERROR, $errorMessage );
		}

		return null;
	}

	public function unlockClonedSite() {
		return $this->lock->unlock();
	}

	private function handleClonedDetection( $error_data ) {
		$error = array_pop( $error_data );
		$this->lock->lock( $error );
	}
}
