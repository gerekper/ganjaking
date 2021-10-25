<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WP_User;
use Yoast\WP\SEO\Actions\Indexation\Indexable_General_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexation\Indexable_Post_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexation\Indexable_Post_Type_Archive_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexation\Indexable_Term_Indexation_Action;
use Yoast\WP\SEO\Actions\Prominent_Words\Content_Action;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Helpers\Capability_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Prominent_Words_Notification as Prominent_Words_Notification_Presenter;
use Yoast_Notification;
use Yoast_Notification_Center;

/**
 * Integration for determining and showing the notification
 * to ask users to calculate prominent words for their site.
 *
 * @package Yoast\WP\SEO\Integrations\Admin
 */
class Prominent_Words_Notification implements Integration_Interface {

	/**
	 * The ID of the notification.
	 */
	const NOTIFICATION_ID = 'wpseo-premium-prominent-words-recalculate';

	/**
	 * How many indexables without prominent words should exist before this notification is shown to the user.
	 */
	const UNINDEXED_THRESHOLD = 25;

	/**
	 * The content action.
	 *
	 * @var Content_Action
	 */
	private $content_action;

	/**
	 * The notification center.
	 *
	 * @var Yoast_Notification_Center
	 */
	private $notification_center;

	/**
	 * The capability helper.
	 *
	 * @var Capability_Helper
	 */
	private $capability;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * The post indexation action.
	 *
	 * @var Indexable_Post_Indexation_Action
	 */
	private $post_indexation;

	/**
	 * The term indexation action.
	 *
	 * @var Indexable_Term_Indexation_Action
	 */
	private $term_indexation;

	/**
	 * The general indexation action.
	 *
	 * @var Indexable_General_Indexation_Action
	 */
	private $general_indexation;

	/**
	 * The post type archive indexation action.
	 *
	 * @var Indexable_Post_Type_Archive_Indexation_Action
	 */
	private $post_type_archive_indexation;

	/**
	 * Prominent_Words_Notification_Integration constructor.
	 *
	 * @param Yoast_Notification_Center                     $notification_center          The notification center.
	 * @param Content_Action                                $content_action               The content action.
	 * @param Indexable_Post_Indexation_Action              $post_indexation              The post indexation action.
	 * @param Indexable_Term_Indexation_Action              $term_indexation              The term indexation action.
	 * @param Indexable_General_Indexation_Action           $general_indexation           The general indexation action.
	 * @param Indexable_Post_Type_Archive_Indexation_Action $post_type_archive_indexation The post type indexation action.
	 * @param Capability_Helper                             $capability                   The capability helper.
	 * @param Options_Helper                                $options                      The options helper.
	 */
	public function __construct(
		Yoast_Notification_Center $notification_center,
		Content_Action $content_action,
		Indexable_Post_Indexation_Action $post_indexation,
		Indexable_Term_Indexation_Action $term_indexation,
		Indexable_General_Indexation_Action $general_indexation,
		Indexable_Post_Type_Archive_Indexation_Action $post_type_archive_indexation,
		Capability_Helper $capability,
		Options_Helper $options
	) {
		$this->notification_center          = $notification_center;
		$this->content_action               = $content_action;
		$this->post_indexation              = $post_indexation;
		$this->term_indexation              = $term_indexation;
		$this->general_indexation           = $general_indexation;
		$this->post_type_archive_indexation = $post_type_archive_indexation;
		$this->capability                   = $capability;
		$this->options                      = $options;
	}

	/**
	 * Initializes the integration by registering the right hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'wpseo_dashboard' && ! $this->requires_notification() ) {
			\add_action( 'admin_init', [ $this, 'cleanup_notification' ] );
		}

		if ( ! \wp_next_scheduled( self::NOTIFICATION_ID ) ) {
			\wp_schedule_event( \time(), 'daily', self::NOTIFICATION_ID );
			\add_action( 'admin_init', [ $this, 'manage_notification' ] );
		}

		\add_action( 'update_option_wpseo', [ $this, 'handle_option_change' ], 10, 2 );
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Handles the option change to make sure the notification will be removed when link suggestions are disabled.
	 *
	 * @param mixed $old_value The old value.
	 * @param mixed $new_value The new value.
	 */
	public function handle_option_change( $old_value, $new_value ) {
		if ( ! empty( $old_value['enable_link_suggestions'] ) && empty( $new_value['enable_link_suggestions'] ) ) {
			$this->cleanup_notification();
		}
	}

	/**
	 * Manages, for each user, if the notification should be shown or removed.
	 */
	public function manage_notification() {
		$users = $this->capability->get_applicable_users( 'wpseo_manage_options' );

		if ( $this->requires_notification() ) {
			$this->add_notification( $users );
		}
		else {
			$this->remove_notification( $users );
		}
	}

	/**
	 * Cleans up the notification for all applicable users.
	 */
	public function cleanup_notification() {
		$users = $this->capability->get_applicable_users( 'wpseo_manage_options' );
		$this->remove_notification( $users );
	}

	/**
	 * Adds the notification for the given array of users,
	 * if they do not have the notification already.
	 *
	 * @param WP_User[] $users The users to add the notification for.
	 */
	private function add_notification( $users ) {
		foreach ( $users as $user ) {
			$notification = $this->notification_center->get_notification_by_id( self::NOTIFICATION_ID, $user->ID );
			if ( ! $notification ) {
				$this->notification_center->add_notification( $this->get_notification( $user ) );
			}
		}
	}

	/**
	 * Removes the notification for the given array of users.
	 *
	 * @param WP_User[] $users The users to remove the notification for.
	 */
	private function remove_notification( $users ) {
		foreach ( $users as $user ) {
			$notification = $this->notification_center->get_notification_by_id( self::NOTIFICATION_ID, $user->ID );
			if ( $notification instanceof Yoast_Notification ) {
				$this->notification_center->remove_notification( $notification );
			}
		}
	}

	/**
	 * Checks if the link suggestions are enabled and the total number of unindexed posts is larger than the threshold.
	 *
	 * @return bool `true` if the notification should be shown.
	 */
	private function requires_notification() {
		if ( ! $this->has_enabled_link_suggestions() ) {
			return false;
		}

		$total_unindexed  = $this->content_action->get_total_unindexed();
		$total_unindexed += $this->post_indexation->get_total_unindexed();
		$total_unindexed += $this->term_indexation->get_total_unindexed();
		$total_unindexed += $this->general_indexation->get_total_unindexed();
		$total_unindexed += $this->post_type_archive_indexation->get_total_unindexed();

		return $total_unindexed >= self::UNINDEXED_THRESHOLD;
	}

	/**
	 * Determines whether the user has enabled the link suggestions or not.
	 *
	 * @return bool True when link suggestions are enabled.
	 */
	private function has_enabled_link_suggestions() {
		return $this->options->get( 'enable_link_suggestions', false );
	}

	/**
	 * Returns the prominent words reindex notification for the specified user.
	 *
	 * @param WP_User $user The user to show the notification to.
	 *
	 * @return Yoast_Notification The notification to show.
	 */
	private function get_notification( $user ) {
		return new Yoast_Notification(
			(string) new Prominent_Words_Notification_Presenter(),
			[
				'type'         => Yoast_Notification::WARNING,
				'id'           => self::NOTIFICATION_ID,
				'capabilities' => [ 'wpseo_manage_options' ],
				'priority'     => 0.8,
				'user'         => $user,
			]
		);
	}
}
