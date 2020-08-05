<?php

namespace Yoast\WP\SEO\Integrations\Admin\Prominent_Words;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Integrations\Admin\Prominent_Words_Notification;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Integration for the prominent words notification event used in the cron job.
 */
class Notification_Event_Integration implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The prominent words notification integration.
	 *
	 * @var Prominent_Words_Notification
	 */
	private $prominent_words_notification;

	/**
	 * Notification_Event_Integration constructor.
	 *
	 * @param Prominent_Words_Notification $prominent_words_notification The prominent words notification integration.
	 */
	public function __construct( Prominent_Words_Notification $prominent_words_notification ) {
		$this->prominent_words_notification = $prominent_words_notification;
	}

	/**
	 * Initializes the integration by registering the right hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( Prominent_Words_Notification::NOTIFICATION_ID, [ $this->prominent_words_notification, 'manage_notification' ] );
	}
}
