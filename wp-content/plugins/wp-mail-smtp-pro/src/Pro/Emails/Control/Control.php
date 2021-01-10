<?php

namespace WPMailSMTP\Pro\Emails\Control;

/**
 * Class Control.
 *
 * @since 1.5.0
 */
class Control {

	/**
	 * Control constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Initialize the Control functionality.
	 *
	 * @since 1.5.0
	 */
	public function init() {

		// Add a new Email Controls tab under General.
		add_filter( 'wp_mail_smtp_admin_get_pages', function ( $pages ) {

			$misc = $pages['misc'];
			unset( $pages['misc'] );

			$pages['control'] = new Admin\SettingsTab();
			$pages['misc']    = $misc;

			return $pages;
		}, 1 );

		// Filter admin area options save process.
		add_filter( 'wp_mail_smtp_options_set', array( $this, 'filter_options_set' ) );

		new Switcher();
	}

	/**
	 * Sanitize admin area options.
	 *
	 * @since 1.5.0
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function filter_options_set( $options ) {

		if ( isset( $options['control'] ) ) {
			foreach ( $options['control'] as $key => $value ) {
				$options['control'][ $key ] = (bool) $value;
			}
		} else {
			$controls = wp_mail_smtp()->pro->get_control()->get_controls( true );

			// All emails are on by default (not disabled).
			foreach ( $controls as $control ) {
				$options['control'][ $control ] = false;
			}
		}

		return $options;
	}

	/**
	 * Get the list of all available emails that we can manage.
	 *
	 * @see   https://github.com/johnbillion/wp_mail Apr 12th 2019.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $keys_only Whether to return the list of emails keys only (no sections/descriptions).
	 *
	 * @return array
	 */
	public function get_controls( $keys_only = false ) {

		$data = array(
			'comments'         => array(
				'title'  => esc_html__( 'Comments', 'wp-mail-smtp-pro' ),
				'emails' => array(
					'dis_comments_awaiting_moderation' => array(
						'label' => esc_html__( 'Awaiting Moderation', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'Comment is awaiting moderation. Sent to the site admin and post author if they can edit comments.', 'wp-mail-smtp-pro' ),
					),
					'dis_comments_published'           => array(
						'label' => esc_html__( 'Published', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'Comment has been published. Sent to the post author.', 'wp-mail-smtp-pro' ),
					),
				),
			),
			'admin_email'      => array(
				'title'  => esc_html__( 'Change of Admin Email', 'wp-mail-smtp-pro' ),
				'emails' => array(
					'dis_admin_email_attempt'         => array(
						'label' => esc_html__( 'Site Admin Email Change Attempt', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'Change of site admin email address was attempted. Sent to the proposed new email address.', 'wp-mail-smtp-pro' ),
					),
					'dis_admin_email_changed'         => array(
						'label' => esc_html__( 'Site Admin Email Changed', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'Site admin email address was changed. Sent to the old site admin email address.', 'wp-mail-smtp-pro' ),
					),
					'dis_admin_email_network_attempt' => array(
						'label' => esc_html__( 'Network Admin Email Change Attempt', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'Change of network admin email address was attempted. Sent to the proposed new email address.', 'wp-mail-smtp-pro' ),
					),
					'dis_admin_email_network_changed' => array(
						'label' => esc_html__( 'Network Admin Email Changed', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'Network admin email address was changed. Sent to the old network admin email address.', 'wp-mail-smtp-pro' ),
					),
				),
			),
			'user_details'     => array(
				'title'  => esc_html__( 'Change of User Email or Password', 'wp-mail-smtp-pro' ),
				'emails' => array(
					'dis_user_details_password_reset_request' => array(
						'label' => esc_html__( 'Reset Password Request', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'User requested a password reset via "Lost your password?". Sent to the user.', 'wp-mail-smtp-pro' ),
					),
					'dis_user_details_password_reset'         => array(
						'label' => esc_html__( 'Password Reset Successfully', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'User reset their password from the password reset link. Sent to the site admin.', 'wp-mail-smtp-pro' ),
					),
					'dis_user_details_password_changed'       => array(
						'label' => esc_html__( 'Password Changed', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'User changed their password. Sent to the user.', 'wp-mail-smtp-pro' ),
					),
					'dis_user_details_email_change_attempt'   => array(
						'label' => esc_html__( 'Email Change Attempt', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'User attempted to change their email address. Sent to the proposed new email address.', 'wp-mail-smtp-pro' ),
					),
					'dis_user_details_email_changed'          => array(
						'label' => esc_html__( 'Email Changed', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'User changed their email address. Sent to the user.', 'wp-mail-smtp-pro' ),
					),
				),
			),
			'personal_data'    => array(
				'title'  => esc_html__( 'Personal Data Requests', 'wp-mail-smtp-pro' ),
				'emails' => array(
					'dis_personal_data_user_confirmed'   => array(
						'label' => esc_html__( 'User Confirmed Export / Erasure Request', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'User clicked a confirmation link in personal data export or erasure request email. Sent to the site or network admin.', 'wp-mail-smtp-pro' ),
					),
					'dis_personal_data_erased_data'      => array(
						'label' => esc_html__( 'Admin Erased Data', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'Site admin clicked "Erase Personal Data" button next to a confirmed data erasure request. Sent to the requester email address.', 'wp-mail-smtp-pro' ),
					),
					'dis_personal_data_sent_export_link' => array(
						'label' => esc_html__( 'Admin Sent Link to Export Data', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'Site admin clicked "Email Data" button next to a confirmed data export request. Sent to the requester email address.', 'wp-mail-smtp-pro' ) . '<br>' .
							'<strong>' . esc_html__( 'Disabling this option will block users from being able to export their personal data, as they will not receive an email with a link.', 'wp-mail-smtp-pro' ) . '</strong>',
					),
				),
			),
			'auto_updates'     => array(
				'title'  => esc_html__( 'Automatic Updates', 'wp-mail-smtp-pro' ),
				'emails' => array(
					'dis_auto_updates_plugin_status' => array(
						'label' => esc_html__( 'Plugin Status', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'Completion or failure of a background automatic plugin update. Sent to the site or network admin.', 'wp-mail-smtp-pro' ),
					),
					'dis_auto_updates_theme_status'  => array(
						'label' => esc_html__( 'Theme Status', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'Completion or failure of a background automatic theme update. Sent to the site or network admin.', 'wp-mail-smtp-pro' ),
					),
					'dis_auto_updates_status'        => array(
						'label' => esc_html__( 'WP Core Status', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'Completion or failure of a background automatic core update. Sent to the site or network admin.', 'wp-mail-smtp-pro' ),
					),
					'dis_auto_updates_full_log'      => array(
						'label' => esc_html__( 'Full Log', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'Full log of background update results. Only sent when you are using a development version of WordPress. Sent to the site or network admin.', 'wp-mail-smtp-pro' ),
					),
				),
			),
			'new_user'         => array(
				'title'  => esc_html__( 'New User', 'wp-mail-smtp-pro' ),
				'emails' => array(
					'dis_new_user_created_to_admin'        => array(
						'label' => esc_html__( 'Created (Admin)', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'A new user was created. Sent to the site admin.', 'wp-mail-smtp-pro' ),
					),
					'dis_new_user_created_to_user'         => array(
						'label' => esc_html__( 'Created (User)', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'A new user was created. Sent to the new user.', 'wp-mail-smtp-pro' ),
					),
					'dis_new_user_invited_to_site_network' => array(
						'label' => esc_html__( 'Invited To Site', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'A new user was invited to a site from Users -> Add New -> Add New User. Sent to the invited user.', 'wp-mail-smtp-pro' ),
					),
					'dis_new_user_created_network'         => array(
						'label' => esc_html__( 'Created On Site', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'A new user account was created. Sent to Network Admin.', 'wp-mail-smtp-pro' ),
					),
					'dis_new_user_added_activated_network' => array(
						'label' => esc_html__( 'Added / Activated on Site', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'A user has been added, or their account activation has been successful. Sent to the user, that has been added/activated.', 'wp-mail-smtp-pro' ),
					),
				),
			),
			'network_new_site' => array(
				'title'  => esc_html__( 'New Site', 'wp-mail-smtp-pro' ),
				'emails' => array(
					'dis_new_site_user_registered_site_network'                  => array(
						'label' => esc_html__( 'User Created Site', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'User registered for a new site. Sent to the site admin.', 'wp-mail-smtp-pro' ),
					),
					'dis_new_site_user_added_activated_site_in_network_to_admin' => array(
						'label' => esc_html__( 'Network Admin: User Activated / Added Site', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'User activated their new site, or site was added from Network Admin -> Sites -> Add New. Sent to Network Admin.', 'wp-mail-smtp-pro' ),
					),
					'dis_new_site_user_added_activated_site_in_network_to_site'  => array(
						'label' => esc_html__( 'Site Admin: Activated / Added Site', 'wp-mail-smtp-pro' ),
						'desc'  => esc_html__( 'User activated their new site, or site was added from Network Admin -> Sites -> Add New. Sent to Site Admin.', 'wp-mail-smtp-pro' ),
					),
				),
			),
		);

		if ( $keys_only === true ) {
			// Create an array of arrays per each section of all the keys.
			$update_data = array_map(
				function( $leaf ) {
					return array_keys( $leaf );
				},
				array_column( $data, 'emails' )
			);

			// Unpack to flatten it the array.
			$data = array_merge( ...$update_data );
		}

		return apply_filters( 'wp_mail_smtp_pro_emails_control_get_controls', $data, $keys_only );
	}
}
