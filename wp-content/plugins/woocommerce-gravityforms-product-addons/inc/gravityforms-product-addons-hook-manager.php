<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_GFPA_Hook_Manager {

	private static $_wp_filters = [];

	/**
	 * Disable gravity forms notifications and confirmations for the form.
	 *
	 * This is necessary because the form is being submitted to capture the entry information for the cart item.
	 * Notifications and confirmations should not be sent until the order is placed.
	 *
	 * @param int|string $form_id
	 */
	public static function disable_notifications_and_confirmations( $form_id ) {

		//MUST disable notifications manually.
		add_filter( 'gform_disable_notification', [ static::class, 'disable_notifications' ], 999,  3);
		add_filter( 'gform_disable_notification_' . $form_id, [ static::class, 'disable_notifications' ], 999, 3 );

		// Disable user notifications for the form.
		add_filter( 'gform_disable_user_notification', [ static::class, 'disable_notifications' ], 999, 3 );
		add_filter( 'gform_disable_user_notification_' . $form_id, [ static::class, 'disable_notifications' ], 999, 3 );

		// Disable admin notifications for the form.
		add_filter( 'gform_disable_admin_notification' . $form_id, [ static::class, 'disable_notifications' ], 10, 3 );
		add_filter( 'gform_disable_admin_notification_' . $form_id, [ static::class, 'disable_notifications' ], 10, 3 );


		add_filter( "gform_confirmation_" . $form_id, [ static::class, 'disable_confirmation' ], 998, 4 );
	}

	/**
	 * Disable gravity forms notifications for the form.
	 *
	 * @param boolean $disabled
	 * @param array $form
	 * @param array $lead
	 *
	 * @return boolean
	 */
	public static function disable_notifications( bool $disabled, array $form, array $lead ): bool {
		return true;
	}

	/**
	 * Disable gravity forms confirmation for the form.
	 *
	 * @param string|array $confirmation
	 * @param array $form
	 * @param array $lead
	 * @param bool $ajax
	 *
	 * @return string
	 */
	public static function disable_confirmation( $confirmation, array $form, array $lead, bool $ajax ): string {
		return '';
	}


	public static function disable_gform_after_submission_hooks( $form_id ) {
		global $wp_filter, $wp_actions;
		$tag = 'gform_after_submission';
		if ( ! isset( self::$_wp_filters[ $tag ] ) ) {
			if ( isset( $wp_filter[ $tag ] ) ) {
				self::$_wp_filters[ $tag ] = $wp_filter[ $tag ];
				unset( $wp_filter[ $tag ] );
			}
		}
		$tag = "gform_after_submission_{$form_id}";
		if ( ! isset( self::$_wp_filters[ $tag ] ) ) {
			if ( isset( $wp_filter[ $tag ] ) ) {
				self::$_wp_filters[ $tag ] = $wp_filter[ $tag ];
				unset( $wp_filter[ $tag ] );
			}
		}
		$tag = 'gform_entry_post_save';
		if ( ! isset( self::$_wp_filters[ $tag ] ) ) {
			if ( isset( $wp_filter[ $tag ] ) ) {
				self::$_wp_filters[ $tag ] = $wp_filter[ $tag ];
				unset( $wp_filter[ $tag ] );
			}
		}
		$tag = "gform_entry_post_save_{$form_id}";
		if ( ! isset( self::$_wp_filters[ $tag ] ) ) {
			if ( isset( $wp_filter[ $tag ] ) ) {
				self::$_wp_filters[ $tag ] = $wp_filter[ $tag ];
				unset( $wp_filter[ $tag ] );
			}
		}

	}

	public static function enable_gform_after_submission_hooks( $form_id ) {
		global $wp_filter;
		$tag = 'gform_after_submission';
		if ( isset( self::$_wp_filters[ $tag ] ) ) {
			$wp_filter[ $tag ] = self::$_wp_filters[ $tag ];
		}
		$tag = "gform_after_submission_{$form_id}";
		if ( isset( self::$_wp_filters[ $tag ] ) ) {
			$wp_filter[ $tag ] = self::$_wp_filters[ $tag ];
		}
	}
}
