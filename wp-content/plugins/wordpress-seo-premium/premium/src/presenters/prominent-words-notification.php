<?php

namespace Yoast\WP\SEO\Presenters;

use WPSEO_Shortlinker;

/**
 * Class Prominent_Words_Notification_Presenter
 *
 * @package Yoast\WP\SEO\Presenters
 */
class Prominent_Words_Notification extends Abstract_Presenter {

	/**
	 * Presents the notification.
	 *
	 * @return string The notification.
	 */
	public function present() {
		return \sprintf(
			/* translators: 1: link to yoast.com post about internal linking suggestion. 2: is anchor closing. 3: two linebreak. 4: link to the recalculation option. */
			\__(
				'You need to analyze your posts and/or pages in order to receive the best %1$slink suggestions%2$s. %3$s %4$sAnalyze the content%2$s to generate the missing link suggestions.',
				'wordpress-seo-premium'
			),
			'<a href="' . \esc_url( WPSEO_Shortlinker::get( 'https://yoa.st/notification-internal-link' ) ) . '">',
			'</a>',
			'<br /><br />',
			'<a href="' . \esc_url( \admin_url( 'admin.php?page=wpseo_tools#start-indexation-yoastProminentWordsIndexationData' ) ) . '" class="button">'
		);
	}
}
