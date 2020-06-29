<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post types
 *
 * Registers post types and taxonomies
 */
class FUE_Post_Types {

	/**
	 * Add hooks that are executed at a later time than usual so WC will have
	 * its post types and taxonomies already set up and available
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 20 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 20 );
		add_action( 'init', array( __CLASS__, 'register_post_status' ), 20 );

		if ( is_admin() ) {
			add_filter( 'post_updated_messages', 'FUE_Post_Types::register_email_update_messages' );
			require_once FUE_INC_DIR .'/class-fue-meta-boxes.php';
		}
	}

	public static function register_taxonomies() {

		if ( !taxonomy_exists( 'follow_up_email_type' ) ) {
			register_taxonomy( 'follow_up_email_type',
				array( Follow_Up_Emails::$post_type ),
				array(
					'hierarchical' 			=> false,
					'show_ui' 				=> false,
					'show_in_nav_menus' 	=> false,
					'query_var' 			=> is_admin(),
					'rewrite'				=> false,
					'public'                => false
				)
			);
		}

		if ( !taxonomy_exists( 'follow_up_email_campaign' ) ) {
			register_taxonomy( 'follow_up_email_campaign',
				array( Follow_Up_Emails::$post_type ),
				array(
					'labels'                => array(
						'name'          => __('Campaigns', 'follow_up_emails'),
						'singular_name' => __('Campaign', 'follow_up_emails'),
						'all_items'     => __('All Campaigns', 'follow_up_emails'),
						'edit_item'     => __('Edit Campaign', 'follow_up_emails'),
						'view_item'     => __('View Campaign', 'follow_up_emails'),
						'update_item'   => __('Update Campaign', 'follow_up_emails'),
						'add_new_item'  => __('Add New Campaign', 'follow_up_emails'),
						'new_item_name' => __('New Campaign Name', 'follow_up_emails'),
						'search_items'  => __('Search Campaigns', 'follow_up_emails'),
						'popular_items' => __('Popular Campaigns', 'follow_up_emails'),
						'separate_items_with_commas' => __('Separate campaigns with commas', 'follow_up_emails'),
						'add_or_remove_items'   => __('Add or remove campaigns', 'follow_up_emails'),
						'choose_from_most_used' => __( 'Choose from the most used campaigns', 'follow_up_emails' ),
						'not_found'     => __('No campaigns found', 'follow_up_emails')
					),
					'hierarchical' 			=> false,
					'show_ui' 				=> true,
					'show_in_nav_menus' 	=> true,
					'query_var' 			=> is_admin(),
					'rewrite'				=> false,
					'public'                => false,
					'update_count_callback' => '_update_generic_term_count'
				)
			);
		}

	}

	public static function register_post_types() {

		if ( post_type_exists( Follow_Up_Emails::$post_type ) ) {
			return;
		}

		register_post_type( Follow_Up_Emails::$post_type,
			array(
				'labels'        => array(
					'name'               => __('Follow-Up Emails', 'follow_up_emails'),
					'singular_name'      => __('Follow-Up Email', 'follow_up_emails'),
					'menu_name'          => _x( 'Follow-Up Emails', 'Admin menu name', 'follow_up_emails' ),
					'add_new'            => __( 'Add Follow-up', 'follow_up_emails' ),
					'add_new_item'       => __( 'Add New Follow-up', 'follow_up_emails' ),
					'edit'               => __( 'Edit', 'follow_up_emails' ),
					'edit_item'          => __( 'Edit Follow-up', 'follow_up_emails' ),
					'new_item'           => __( 'New Follow-Up', 'follow_up_emails' ),
					'view'               => __( 'View Follow-up', 'follow_up_emails' ),
					'view_item'          => __( 'View Follow-up', 'follow_up_emails' ),
					'search_items'       => __( 'Search Follow-Up Emails', 'follow_up_emails' ),
					'not_found'          => __( 'No Follow-ups found', 'follow_up_emails' ),
					'not_found_in_trash' => __( 'No Follow-ups found in trash', 'follow_up_emails' ),
				),
				'public'            => false,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_in_admin_bar' => true,
				'hierarchical'      => false,
				'supports'          => array( 'title', 'editor' ),
			)
		);

	}

	/**
	 * Register custom status for follow-up emails and email queue
	 */
	public static function register_post_status() {

		register_post_status( 'fue-inactive', array(
			'label'                     => _x( 'Inactive', 'Email status', 'follow_up_emails' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', 'follow_up_emails' )
		) );

		register_post_status( 'fue-active', array(
			'label'                     => _x( 'Active', 'Email status', 'follow_up_emails' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'follow_up_emails' )
		) );

		register_post_status( 'fue-archived', array(
			'label'                     => _x( 'Archived', 'Email status', 'follow_up_emails' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Archived <span class="count">(%s)</span>', 'Archived <span class="count">(%s)</span>', 'follow_up_emails' )
		) );

	}

	/**
	 * Change the update messages displayed when updating Emails
	 *
	 * @param array $messages Existing post update messages.
	 * @return array Amended post update messages with new CPT update messages.
	 */
	public static function register_email_update_messages( $messages ) {
		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages['follow_up_email'] = apply_filters( 'fue_update_messages', array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Email updated.', 'follow_up_emails' ),
			2  => __( 'Custom field updated.', 'follow_up_emails' ),
			3  => __( 'Custom field deleted.', 'follow_up_emails' ),
			4  => __( 'Email updated.', 'follow_up_emails' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Email restored to revision from %s', 'follow_up_emails' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			6  => __( 'Email saved.', 'follow_up_emails' ),
			7  => __( 'Email saved.', 'follow_up_emails' ),
			8  => __( 'Email submitted.', 'follow_up_emails' ),
			9  => sprintf(
				__( 'Email scheduled for: <strong>%1$s</strong>.', 'follow_up_emails' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'follow_up_emails' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Email draft updated.', 'follow_up_emails' )
		) );

		return $messages;
	}

}

FUE_Post_types::init();
