<?php
/**
 * WooCommerce Tab Manager
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Tab Manager to newer
 * versions in the future. If you wish to customize WooCommerce Tab Manager for your
 * needs please refer to http://docs.woocommerce.com/document/tab-manager/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_4_0 as Framework;

/**
 * WooCommerce Tab Manager Admin
 *
 * Main admin file which loads all Tab Manager panels and modifications
 */


/**
 * Helper function to determine if the current page is a product tab admin page.
 *
 * @param bool $return_detailed_info If true will return array with detailed info
 * @return bool|array
 */
function wc_tab_manager_is_tab_admin_page( $return_detailed_info = false ) {

	// Get admin screen info.
	$screen = get_current_screen();

	// Check current screen ID against tab page screen IDs.
	$known_screen_ids = array(
		'tab-list' => 'edit-wc_product_tab',
		'tab-edit' => 'wc_product_tab',
		'defaults' => 'admin_page_tab_manager',
	);

	$is_admin_page = $screen && in_array( $screen->id, $known_screen_ids, true );

	if ( ! $return_detailed_info ) {
		return $is_admin_page;
	}

	$data = array(
		'result'  => $is_admin_page,
		'screens' => $known_screen_ids,
		'is_page' => array(
			'tab-list' => $known_screen_ids['tab-list'] === $screen->id,
			'tab-edit' => $known_screen_ids['tab-edit'] === $screen->id,
			'defaults' => $known_screen_ids['defaults'] === $screen->id,
		),
	);

	return $data;
}

add_action( 'admin_init', 'wc_tab_manager_admin_init' );

/**
 * Initialize the admin, adding actions to properly display and handle
 * the Tab Manager admin custom tabs and panels
 * @access public
 */
function wc_tab_manager_admin_init() {
	global $pagenow;

	// on the product new/edit page
	if ( 'post-new.php' === $pagenow || 'post.php' === $pagenow ) {
		include_once( wc_tab_manager()->get_plugin_path() . '/admin/post-types/writepanels/writepanels-init.php' );
	}
}


add_action( 'admin_head', 'wc_tab_manager_admin_menu_highlight' );

/**
 * Highlight the correct top level admin menu item for the product tab post type add screen
 * @access public
 */
function wc_tab_manager_admin_menu_highlight() {

	global $parent_file, $submenu_file;

	if ( wc_tab_manager_is_tab_admin_page() ) {
		$submenu_file = 'edit.php?post_type=wc_product_tab';
		$parent_file  = 'woocommerce';
	}
}

add_action( 'admin_enqueue_scripts', 'wc_tab_manager_admin_enqueue_scripts', 15 );

/**
 * Add necessary admin scripts
 * @access public
 */
function wc_tab_manager_admin_enqueue_scripts() {

	// get screen info
	$screen_id              = get_current_screen()->id;
	// figure out which page we're on
	$is_default_layout_page = 'admin_page_tab_manager' === $screen_id;
	$is_custom_layout_page  = 'product' === $screen_id;

	// need this for the drag-and-drop to work on the Default Tab Layout page
	if ( $is_default_layout_page ) {

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'woocommerce_admin' );
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script(
			'woocommerce_admin_meta_boxes',
			WC()->plugin_url() . '/assets/js/admin/meta-boxes.min.js',
			array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'accounting', 'round' ),
			WC()->version,
			true
		);

		// keep the `woocommerce_admin_meta_boxes` script happy
		wp_localize_script( 'woocommerce_admin_meta_boxes', 'woocommerce_admin_meta_boxes', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		) );
	}

	if ( $is_default_layout_page || $is_custom_layout_page ) {

		wp_enqueue_script(
			'wc_tab_manager_admin',
			wc_tab_manager()->get_plugin_url() . '/assets/js/admin/wc-tab-manager-admin.min.js',
			array( 'jquery' ),
			\WC_Tab_Manager::VERSION,
			true
		);

		wp_localize_script( 'wc_tab_manager_admin', 'wc_tab_manager_admin_params', array(
			'remove_product_tab' => __( 'Remove this product tab?', 'woocommerce-tab-manager' ),
			'remove_label'       => __( 'Remove', 'woocommerce-tab-manager' ),
			'click_to_toggle'    => __( 'Click to toggle', 'woocommerce-tab-manager' ),
			'title_label'        => __( 'Title', 'woocommerce-tab-manager' ),
			'title_description'  => __( 'The tab title, this appears in the tab', 'woocommerce-tab-manager' ),
			'content_label'      => __( 'Content', 'woocommerce-tab-manager' ),
			'ajax_url'           => admin_url( 'admin-ajax.php' ),
			'get_editor_nonce'   => wp_create_nonce( 'get-editor' ),
		) );
	}
}


add_action( 'admin_print_styles', 'wc_tab_manager_admin_css' );

/**
 * Queue required CSS
 * @access public
 */
function wc_tab_manager_admin_css() {

	wp_enqueue_style(
		'wc_tab_manager_admin_styles',
		wc_tab_manager()->get_plugin_url() . '/assets/css/admin/wc-tab-manager-admin.min.css',
		array(),
		\WC_Tab_Manager::VERSION
	);

	if ( wc_tab_manager_is_tab_admin_page() ) {
		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );
	}
}


add_filter( 'post_updated_messages', 'wc_tab_manager_product_tab_updated_messages' );

/**
 * Set the product updated messages so they're specific to the Product Tabs
 *
 * @access public
 * @param array $messages array of update messages
 * @return array
 */
function wc_tab_manager_product_tab_updated_messages( $messages ) {
	global $post, $post_ID;

	$messages['wc_product_tab'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Tab updated.', 'woocommerce-tab-manager' ),
		2 => __( 'Custom field updated.', 'woocommerce-tab-manager' ),
		3 => __( 'Custom field deleted.', 'woocommerce-tab-manager' ),
		4 => __( 'Tab updated.', 'woocommerce-tab-manager' ),
		5 => isset( $_GET['revision'] ) ? sprintf( __( 'Tab restored to revision from %s', 'woocommerce-tab-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => __( 'Tab updated.', 'woocommerce-tab-manager' ),
		7 => __( 'Tab saved.', 'woocommerce-tab-manager' ),
		8 => __( 'Tab submitted.', 'woocommerce-tab-manager' ),
		9 => sprintf(
			__( 'Tab scheduled for: <strong>%1$s</strong>.', 'woocommerce-tab-manager' ),
			date_i18n( __( 'M j, Y @ G:i', 'woocommerce-tab-manager' ), strtotime( $post->post_date ) )
		),
		10 => __( 'Tab draft updated.', 'woocommerce-tab-manager' ),
	);

	return $messages;
}


add_action( 'all_admin_notices', 'wc_tab_manager_admin_nav', 1 );

/**
 * Use JavaScript to alter the Product Tab post list/add/edit page header so that they become
 * tabs, with a third tab taking us to the Default Tab Layout custom admin
 * page.
 * @access public
 */
function wc_tab_manager_admin_nav() {

	// first show any framework notices
	wc_tab_manager()->get_message_handler()->show_messages();

	$screen          = get_current_screen();
	$is_list_page    = $screen && 'edit-wc_product_tab' === $screen->id;
	$is_edit_page    = $screen && 'wc_product_tab' === $screen->id;
	$is_default_page = $screen && 'admin_page_tab_manager' === $screen->id;
	$is_admin_page   = $is_list_page || $is_edit_page || $is_default_page;

	if ( $is_admin_page ) {
		$tabs_active    = '';
		$edit_tab_label = __( 'Add Global Tab', 'woocommerce-tab-manager' );
		$edit_active    = '';
		$search_query   = '';
		$default_active = '';

		if ( $is_list_page ) {
			$tabs_active    = 'nav-tab-active';
		} else if ( $is_edit_page ) {
			$edit_active    = 'nav-tab-active';
		} else if ( $is_default_page ) {
			$default_active = 'nav-tab-active';
		}
		if ( $is_edit_page && isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) {
			$edit_tab_label = __( 'Edit Tab', 'woocommerce-tab-manager' );
		}

		if ( ! empty( $_REQUEST['s'] ) ) {
			$search_query = get_search_query();
		}
		?>
		<h1 class="nav-tab-wrapper woo-nav-tab-wrapper">
			<a class="nav-tab <?php echo esc_attr( $tabs_active ); ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_product_tab' ) ); ?>">
				<?php esc_html_e( 'Tabs', 'woocommerce-tab-manager' ); ?>
			</a>
			<a class="nav-tab <?php echo esc_attr( $edit_active ); ?>" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=wc_product_tab' ) ); ?>">
				<?php echo esc_attr( $edit_tab_label ); ?>
			</a>
			<a class="nav-tab <?php echo esc_attr( $default_active ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . WC_Tab_Manager::PLUGIN_ID ) ); ?>">
				<?php esc_html_e( 'Default Tab Layout', 'woocommerce-tab-manager' ); ?>
			</a>
			<?php if ( ! empty( $search_query ) ) : ?>
				<span class="subtitle">
					<?php /* translators: Placeholders: %s - keyword search query */
					$search_text = __( 'Search results for &#8220;%s&#8221;', 'woocommerce-tab-manager' );
					$search_text = sprintf( $search_text, $search_query );
					echo esc_html( $search_text );
					?>
				</span>
			<?php endif; ?>
		</h1>
		<?php
	}
}


add_action( 'admin_menu', 'wp_tab_manager_register_layout_page' );

/**
 * Registers the Default Tab Layout page, which I combine with the product tabs
 * list/add/edit page to act as a single Tab Manager submenu
 * @access public
 */
function wp_tab_manager_register_layout_page() {

	add_submenu_page(
		null,                                                       // parent menu
		__( 'WooCommerce Tab Manager', 'woocommerce-tab-manager' ), // page title
		null,                                                       // menu title  (null so it doesn't appear)
		'manage_woocommerce_tab_manager',                           // capability
		\WC_Tab_Manager::PLUGIN_ID,                                 // unique menu slug
		'wc_tab_manager_render_layout_page'                         // callback
	);
}
