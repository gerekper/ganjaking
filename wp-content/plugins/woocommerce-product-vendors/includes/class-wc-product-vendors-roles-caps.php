<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Roles and Capability Class.
 *
 * Adds all the default roles and capabilities for store and vendor admins.
 *
 * @category Roles and Capability
 * @package  WooCommerce Product Vendors/Roles and Capability
 * @version  2.0.0
 */
class WC_Product_Vendors_Roles_Caps {
	/**
	 * Init
	 */
	public function __construct() {
		add_filter( 'woocommerce_shop_manager_editable_roles', array( $this, 'shop_manager_vendor_management' ), 10, 1 );
		add_filter( 'user_row_actions', array( $this, 'user_row_actions' ), 10, 2 );
		add_action( 'admin_action_approve_vendor', array( $this, 'approve_vendor_handler' ) );
		add_action( 'admin_notices', array( $this, 'vendor_approved_notice' ) );
		add_filter( 'user_has_cap', array( $this, 'vendor_allow_edit_attachment' ), 10, 4 );
	}

	/**
	 * Allow shop manager to manage vendor profiles.
	 *
	 * @since 2.1.10
	 * @param array $roles Roles that a shop manager can manage.
	 * @return array
	 */
	public function shop_manager_vendor_management( $roles = array() ) {
		$roles[] = 'wc_product_vendors_admin_vendor';
		$roles[] = 'wc_product_vendors_manager_vendor';
		$roles[] = 'wc_product_vendors_pending_vendor';

		return $roles;
	}

	/**
	 * Declares the default admin vendor capabilities
	 *
	 * @access protected
	 * @since 2.0.0
	 * @version 2.0.19
	 * @return array
	 */
	protected function default_admin_vendor_caps() {
		return apply_filters( 'wcpv_default_admin_vendor_role_caps', array(
			'read_product'              => true,
			'manage_product'            => true,
			'edit_products'             => true,
			'edit_product'              => true,
			'edit_published_products'   => true,
			'edit_shop_orders'          => true,
			'assign_product_terms'      => true,
			'upload_files'              => true,
			'read'                      => true,
			'edit_others_products'      => true,
			'view_vendor_sales_widget'  => true,
			'delete_published_products' => true,
			'delete_others_products'    => true,
			'delete_posts'              => true,
			'delete_others_posts'       => true,
			'edit_comment'              => false,
			'edit_comments'             => false,
			'view_woocommerce_reports'  => false,
			'publish_products'          => false,
		) );
	}

	/**
	 * Declares the default manager vendor capabilities
	 *
	 * @access protected
	 * @since 2.0.0
	 * @version 2.0.19
	 * @return array
	 */
	protected function default_manager_vendor_caps() {
		return apply_filters( 'wcpv_default_manager_vendor_role_caps', array(
			'read_product'             => true,
			'manage_product'           => true,
			'edit_products'            => true,
			'edit_product'             => true,
			'edit_published_products'  => true,
			'edit_shop_orders'         => true,
			'assign_product_terms'     => true,
			'upload_files'             => true,
			'read'                     => true,
			'edit_others_products'     => true,
			'delete_posts'             => false,
			'delete_product'           => false,
			'edit_comment'             => false,
			'edit_comments'            => false,
			'view_woocommerce_reports' => false,
			'publish_products'         => false,
		) );
	}

	/**
	 * Declares the default pending vendor capabilities
	 *
	 * @access protected
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return array
	 */
	protected function default_pending_vendor_caps() {
		return apply_filters( 'wcpv_default_pending_vendor_role_caps', array(
			'read' => true,
		) );
	}

	/**
	 * Adds the default roles
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function add_default_roles() {
		// admin.
		remove_role( 'wc_product_vendors_admin_vendor' );
		add_role( 'wc_product_vendors_admin_vendor', __( 'Vendor Admin', 'woocommerce-product-vendors' ), $this->default_admin_vendor_caps() );

		// manager.
		remove_role( 'wc_product_vendors_manager_vendor' );
		add_role( 'wc_product_vendors_manager_vendor', __( 'Vendor Manager', 'woocommerce-product-vendors' ), $this->default_manager_vendor_caps() );

		// pending.
		remove_role( 'wc_product_vendors_pending_vendor' );
		add_role( 'wc_product_vendors_pending_vendor', __( 'Pending Vendor', 'woocommerce-product-vendors' ), $this->default_pending_vendor_caps() );

		return true;
	}

	/**
	 * Adds the necessary caps to shop manager and administrator.
	 *
	 * @since 2.1.15
	 */
	public function add_manager_caps() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) && ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		// Add additional caps to admins and shop managers.
		if ( is_object( $wp_roles ) ) {
			$shop_manager_role_caps = array(
				'manage_vendors',
				'edit_other_vendors_products',
				'delete_other_vendors_products',
				'edit_other_vendors_global_availabilities',
				'delete_other_vendors_global_availabilities',
				'edit_other_vendors_wc_bookings',
				'delete_other_vendors_wc_bookings',
			);
			foreach ( $shop_manager_role_caps as $cap ) {
				$wp_roles->add_cap( 'shop_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Removes the manage_bookings cap since it's deprecated in bookings 1.14.2
	 */
	public function remove_deprecated_caps() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) && ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles(); // phpcs:disable WordPress.WP.GlobalVariablesOverride.OverrideProhibited
		}

		if ( is_object( $wp_roles ) ) {
			if ( defined( 'WC_BOOKINGS_VERSION' ) && version_compare( WC_BOOKINGS_VERSION, '1.14.2', '>=' ) ) {
				if ( 'yes' !== get_option( 'wcpv_removed_manage_bookings_cap' ) ) {

					$wp_roles->get_role( 'wc_product_vendors_manager_vendor' )->remove_cap( 'manage_bookings' );
					$wp_roles->get_role( 'wc_product_vendors_admin_vendor' )->remove_cap( 'manage_bookings' );

					update_option( 'wcpv_removed_manage_bookings_cap', 'yes' );
				}
			}
		}
	}

	/**
	 * Adds publish products capability to a user
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function add_publish_products( $user_id = null ) {
		if ( null === $user_id ) {
			return;
		}

		$user = new WP_User( $user_id );
		$user->add_cap( 'publish_products' );

		return true;
	}

	/**
	 * Remove publish products capability from a user
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function remove_publish_products( $user_id = null ) {
		if ( null === $user_id ) {
			return;
		}

		$user = new WP_User( $user_id );
		$user->remove_cap( 'publish_products' );

		return true;
	}

	/**
	 * Adds manage users capabilities to a user
	 *
	 * @access public
	 * @since 2.1.0
	 * @version 2.1.0
	 * @return bool
	 */
	public function add_manage_users( $user_id = null ) {
		if ( null === $user_id ) {
			return;
		}

		$user = new WP_User( $user_id );
		$user->add_cap( 'list_users' );
		$user->add_cap( 'create_users' );
		$user->add_cap( 'edit_users' );
		$user->add_cap( 'edit_shop_orders' );

		return true;
	}

	/**
	 * Remove manage users capabilities from a user
	 *
	 * @access public
	 * @since 2.1.0
	 * @version 2.1.0
	 * @return bool
	 */
	public function remove_manage_users( $user_id = null ) {
		if ( null === $user_id ) {
			return;
		}

		$user = new WP_User( $user_id );
		$user->remove_cap( 'list_users' );
		$user->remove_cap( 'create_users' );
		$user->remove_cap( 'edit_users' );
		$user->remove_cap( 'edit_shop_orders' );

		return true;
	}

	/**
	 * Add vendor actions.
	 *
	 * @param string[] $actions     An array of action links to be displayed.
	 *                              Default 'Edit', 'Delete' for single site, and
	 *                              'Edit', 'Remove' for Multisite.
	 * @param WP_User  $user_object WP_User object for the currently listed user.
	 */
	public function user_row_actions( $actions, $user_object ) {
		if ( ! current_user_can( 'promote_users' ) ) {
			return $actions;
		}

		if ( in_array( 'wc_product_vendors_pending_vendor', $user_object->roles, true ) ) {
			$actions['approve_vendor_admin'] = sprintf(
				'<a href="%1$s">%2$s</a>',
				add_query_arg(
					array(
						'role'        => 'wc_product_vendors_pending_vendor',
						'action'      => 'approve_vendor',
						'vendor_role' => 'admin',
						'user_id'     => $user_object->ID,
						'_nonce'      => wp_create_nonce( 'wc-product-vendors-approve-user' ),
					),
					admin_url( 'users.php' )
				),
				__( 'Approve as Vendor Admin', 'woocommerce-product-vendors' )
			);

			$actions['approve_vendor_manager'] = sprintf(
				'<a href="%1$s">%2$s</a>',
				add_query_arg(
					array(
						'role'        => 'wc_product_vendors_pending_vendor',
						'action'      => 'approve_vendor',
						'vendor_role' => 'manager',
						'user_id'     => $user_object->ID,
						'_nonce'      => wp_create_nonce( 'wc-product-vendors-approve-user' ),
					),
					admin_url( 'users.php' )
				),
				__( 'Approve as Vendor Manager', 'woocommerce-product-vendors' )
			);
		}

		return $actions;
	}

	/**
	 * Handle vendor actions.
	 */
	public function approve_vendor_handler() {
		if ( ! current_user_can( 'promote_users' ) ) {
			return;
		}

		if ( empty( $_GET['user_id'] ) || empty( $_GET['vendor_role'] ) || empty( $_GET['_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_nonce'] ) ), 'wc-product-vendors-approve-user' ) ) {
			return;
		}

		$user = get_userdata( sanitize_key( wp_unslash( $_GET['user_id'] ) ) );
		$role = sanitize_key( wp_unslash( $_GET['vendor_role'] ) );

		if ( ! in_array( $role, array( 'admin', 'manager' ), true ) ) {
			return;
		}

		$user->set_role( "wc_product_vendors_{$role}_vendor" );

		wp_safe_redirect(
			add_query_arg(
				array(
					'approved_vendor' => $user->ID,
					'vendor_role'     => $role,
					'_nonce'          => wp_create_nonce( 'wc-product-vendors-approved' ),
				),
				admin_url( 'users.php' )
			)
		);
	}

	/**
	 * Vendor approved notice.
	 */
	public function vendor_approved_notice() {
		$current_screen = get_current_screen();
		if ( 'users' !== $current_screen->id ) {
			return;
		}

		if ( empty( $_GET['approved_vendor'] ) || empty( $_GET['vendor_role'] ) || empty( $_GET['_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_nonce'] ) ), 'wc-product-vendors-approved' ) ) {
			return;
		}

		$user = get_userdata( sanitize_key( wp_unslash( $_GET['approved_vendor'] ) ) );
		$role = sanitize_key( wp_unslash( $_GET['vendor_role'] ) );

		if ( ! $user ) {
			return;
		}

		$role_name = 'admin' === $role ? __( 'Vendor Admin', 'woocommerce-product-vendors' ) : __( 'Vendor Manager', 'woocommerce-product-vendors' );

		?>
		<div class="notice notice-success is-dismissible">
			<p>
			<?php
				printf(
					/* translators: %1$s is the vendor display name. $2$s is the vendor role. */
					esc_html__( '%1$s is approved as a %2$s!', 'woocommerce-product-vendors' ),
					esc_html( $user->display_name ),
					esc_html( $role_name )
				);
			?>
			</p>
		</div>
		<?php
	}

	/**
	 * Allow Vendors to edit attachment details.
	 *
	 * @param array   $allcaps All capabilities.
	 * @param array   $caps    Capabilities.
	 * @param array   $args    Arguments.
	 * @param WP_User $user    The user object.
	 *
	 * @return array The filtered array of all capabilities.
	 */
	public function vendor_allow_edit_attachment( $allcaps, $caps, $args, $user ) {
		// Allow edit only to vendor users, edit_post requested capability and users who can't edit others posts.
		if (
			! WC_Product_Vendors_Utils::is_vendor( $user->ID ) ||
			! isset( $caps[0], $args[2] ) ||
			'edit_post' !== $args[0] ||
			( isset( $allcaps['edit_others_posts'] ) && $allcaps['edit_others_posts'] )
		) {
			return $allcaps;
		}

		$post = get_post( $args[2] );
		if ( ! empty( $post ) && 'attachment' === $post->post_type ) {
			$vendor_id   = WC_Product_Vendors_Utils::get_logged_in_vendor();
			$post_vendor = absint( get_post_meta( $post->ID, '_wcpv_vendor', true ) );
			// Only allow if the user is the post author or post is attached to logged-in vendor.
			if ( $vendor_id === $post_vendor || $args[1] === $post->post_author ) {
				$allcaps[ $caps[0] ] = true;
			}
		}

		return $allcaps;
	}
}
