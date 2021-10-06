<?php
/**
 * Post types class.
 *
 * @package WC_Stamps_Integration
 */

/**
 * Post types handler.
 */
class WC_Stamps_Post_Types {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_types' ), 0 );
	}

	/**
	 * Register custom post types.
	 *
	 * @access public
	 * @return void
	 */
	public function register_post_types() {
		if ( post_type_exists( "wc_stamps_label" ) ) {
			return;
		}

		$admin_capability = 'manage_woocommerce';

		register_post_type( "wc_stamps_label",
			apply_filters( "register_post_type_wc_stamps_label", array(
				'public'          => false,
				'show_ui'         => false,
				'capability_type' => 'post',
				'capabilities'    => array(
					'publish_posts'       => $admin_capability,
					'edit_posts'          => $admin_capability,
					'edit_others_posts'   => $admin_capability,
					'delete_posts'        => $admin_capability,
					'delete_others_posts' => $admin_capability,
					'read_private_posts'  => $admin_capability,
					'edit_post'           => $admin_capability,
					'delete_post'         => $admin_capability,
					'read_post'           => $admin_capability,
				),
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'hierarchical'        => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array( 'title', 'custom-fields' ),
				'has_archive'         => false,
				'show_in_nav_menus'   => false,
			) )
		);
	}
}

new WC_Stamps_Post_Types();
