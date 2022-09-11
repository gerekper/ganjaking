<?php
// phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
namespace Yoast\WP\SEO\Premium\Integrations\Admin;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Premium\Initializers\Inclusive_Language_Analysis_Initializer;

/**
 * Shows a notification telling the user that inclusive language is available and can be enabled.
 */
class Inclusive_Language_Notification_Integration implements Integration_Interface {

	/**
	 * Holds the name of the user meta key.
	 *
	 * The value of this database field holds whether the user has dismissed this notice or not.
	 *
	 * @var string
	 */
	const USER_META_DISMISSED = 'wpseo-remove-inclusive-language-notice';

	/**
	 * The notification center.
	 *
	 * @var \Yoast_Notification_Center
	 */
	private $notification_center;

	/**
	 * The inclusive language analysis integration.
	 *
	 * @var Inclusive_Language_Analysis_Initializer
	 */
	private $inclusive_language_analysis_initializer;

	/**
	 * Constructs a new inclusive language notification integration.
	 *
	 * @param Inclusive_Language_Analysis_Initializer $inclusive_language_analysis_initializer The inclusive language
	 *                                                                                         integration.
	 * @param \Yoast_Notification_Center              $notification_center                     The notification center.
	 */
	public function __construct(
		Inclusive_Language_Analysis_Initializer $inclusive_language_analysis_initializer,
		\Yoast_Notification_Center $notification_center
	) {
		$this->inclusive_language_analysis_initializer = $inclusive_language_analysis_initializer;
		$this->notification_center                     = $notification_center;
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_init', [ $this, 'set_inclusive_language_notice' ] );
		// Remove notification when deactivating plugin.
		\register_deactivation_hook( \WPSEO_PREMIUM_FILE, [ $this, 'remove_notification' ] );
	}

	/**
	 * Checks whether this integration should be active.
	 *
	 * @return string[] Conditionals on which this integration should be active.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Sets the inclusive language notification.
	 *
	 * Notification should pop up if user has Premium activated, the site language is English and the feature toggle is
	 * not switched on.
	 */
	public function set_inclusive_language_notice() {
		if ( $this->should_show_notification() ) {
			$this->add_notification();
			$this->dismiss_notice_listener();
		}
		else {
			$this->remove_notification();
		}
	}

	/**
	 * Checks whether the notification should be shown.
	 *
	 * @return bool Whether the notification should be shown.
	 */
	private function should_show_notification() {
		$not_active = ! ( $this->inclusive_language_analysis_initializer->is_globally_enabled() && $this->inclusive_language_analysis_initializer->is_user_enabled() );

		$has_language_support = $this->inclusive_language_analysis_initializer->has_inclusive_language_support( \WPSEO_Language_Utils::get_language( \get_locale() ) );

		return $not_active && $has_language_support;
	}

	/**
	 * Adds a notification to the notification center.
	 */
	public function add_notification() {
		$this->notification_center->add_notification( $this->get_notification() );
	}

	/**
	 * Removes a notification from the notification center.
	 */
	public function remove_notification() {
		$this->notification_center->remove_notification( $this->get_notification() );
	}

	/**
	 * Generates the inclusive language notification.
	 *
	 * @return \Yoast_Notification The notification to show.
	 */
	private function get_notification() {
		if ( \is_multisite() && \get_site_option( 'wpseo_ms' )['allow_inclusive_language_analysis_active'] === false ) {
			$message = sprintf(
			/* translators: %1$s is a link to the Features tab on the Yoast SEO Dashboard page, %2$s is a link to the blog post about this feature, %3$s is the link closing tag. */
				__(
					'<strong>New in Yoast SEO Premium 19.2:</strong> Did you know that you can now get feedback on the use of inclusive language? This feature is disabled by default. Please contact your Network admin if you want to enable it. %2$sLearn more about this feature%3$s.',
					'wordpress-seo-premium'
				),
				'<a href="' . \admin_url( 'admin.php?page=wpseo_dashboard#top#features' ) . '">',
				'<a href="' . \WPSEO_Shortlinker::get( 'https://yoa.st/inclusive-language-notification' ) . '" target="_blank">',
				'</a>'
			);
		}
		else {
			$message = sprintf(
			/* translators: %1$s is a link to the Features tab on the Yoast SEO Dashboard page, %2$s is a link to the blog post about this feature, %3$s is the link closing tag. */
				__(
					'<strong>New in Yoast SEO Premium 19.2:</strong> Did you know that you can now %1$senable the beta version of our inclusive language feature%3$s to get feedback on the use of inclusive language? This feature is disabled by default. %2$sLearn more about this feature%3$s.',
					'wordpress-seo-premium'
				),
				'<a href="' . \admin_url( 'admin.php?page=wpseo_dashboard#top#features' ) . '">',
				'<a href="' . \WPSEO_Shortlinker::get( 'https://yoa.st/inclusive-language-notification' ) . '" target="_blank">',
				'</a>'
			);
		}

		return new \Yoast_Notification(
			$message,
			[
				'type'         => \Yoast_Notification::WARNING,
				'id'           => 'wpseo-inclusive-language-notice',
				'capabilities' => 'wpseo_manage_options',
				'priority'     => 0.8,
			]
		);
	}

	/**
	 * Listener for the notice.
	 */
	public function dismiss_notice_listener() {
		if ( filter_input( INPUT_GET, 'yoast_dismiss' ) !== 'upsell' ) {
			return;
		}

		$this->dismiss_notice();

		wp_safe_redirect( admin_url( 'admin.php?page=wpseo_dashboard' ) );
		exit;
	}

	/**
	 * Dismisses the notice.
	 */
	protected function dismiss_notice() {
		update_user_meta( get_current_user_id(), self::USER_META_DISMISSED, true );
	}
}
