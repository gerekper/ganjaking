<?php

namespace WBCR\FactoryClearfy227\Pages;

/**
 * This file is the add-ons page.
 *
 * @author        Alex Kovalev <alex@byonepress.com>
 * @since         1.0.0
 * @copyright (c) 2017, OnePress Ltd
 *
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class Components extends \Wbcr_FactoryClearfy227_PageBase {

	/**
	 * The id of the page in the admin menu.
	 *
	 * Mainly used to navigate between pages.
	 *
	 * @since 1.0.0
	 * @see   FactoryPages435_AdminPage
	 *
	 * @var string
	 */
	public $id = "components";

	public $page_menu_position = 0;

	public $page_menu_dashicon = 'dashicons-admin-plugins';

	public $type = 'page';

	public $show_right_sidebar_in_options = false;

	public $available_for_multisite = true;

	/**
	 * @param \Wbcr_Factory436_Plugin $plugin
	 */
	public function __construct(\Wbcr_Factory436_Plugin $plugin)
	{
		$this->menu_title = __('Components', 'wbcr_factory_clearfy_227');
		$this->page_menu_short_description = __('More features for plugin', 'wbcr_factory_clearfy_227');

		parent::__construct($plugin);

		$this->plugin = $plugin;
	}

	/**
	 * Requests assets (js and css) for the page.
	 *
	 * @return void
	 * @since 1.0.0
	 * @see   FactoryPages435_AdminPage
	 *
	 */
	public function assets($scripts, $styles)
	{
		parent::assets($scripts, $styles);

		$this->styles->add(FACTORY_CLEARFY_227_URL . '/assets/css/components.css');

		/**
		 * @param \Wbcr_Factory436_StyleList $styles
		 * @param \Wbcr_Factory436_ScriptList $scripts
		 * @since 1.4.0
		 *
		 */
		do_action('wbcr/clearfy/components/page_assets', $scripts, $styles);
	}

	/**
	 * We register notifications for some actions
	 *
	 * @param                        $notices
	 * @param \Wbcr_Factory436_Plugin $plugin
	 *
	 * @return array
	 * @see libs\factory\pages\themplates\FactoryPages435_ImpressiveThemplate
	 */
	public function getActionNotices($notices)
	{
		$notices[] = [
			'conditions' => [
				'wbcr-force-update-components-success' => 1
			],
			'type' => 'success',
			'message' => __('Components have been successfully updated to the latest version.', 'wbcr_factory_clearfy_227')
		];

		$notices[] = [
			'conditions' => [
				'wbcr-force-update-components-error' => 'inactive_licence'
			],
			'type' => 'danger',
			'message' => __('To use premium components, you need activate a license!', 'wbcr_factory_clearfy_227') . '<a href="admin.php?page=license-wbcr_clearfy" class="btn btn-gold">' . __('Activate license', 'wbcr_factory_clearfy_227') . '</a>'
		];

		$notices[] = [
			'conditions' => [
				'wbcr-force-update-components-error' => 'unknown_error'
			],
			'type' => 'danger',
			'message' => __('An unknown error occurred while updating plugin components. Please contact the plugin support team to resolve this issue.', 'hide_my_wp')
		];

		return $notices;
	}

	public function get_components()
	{
		return [];
	}

	/**
	 * This method simply sorts the list of components.
	 *
	 * @param $components
	 *
	 * @return array
	 */
	public function order($components)
	{
		$deactivate_components = $this->plugin->getPopulateOption('deactive_preinstall_components', []);

		$ordered_components = [
			'premium_active' => [],
			'premium_deactive' => [],
			'other' => []
		];

		foreach((array)$components as $component) {

			if( ('premium' === $component['build'] || 'freemium' === $component['build']) && 'internal' === $component['type'] ) {
				if( in_array($component['name'], $deactivate_components) ) {
					// free component is deactivated
					$order_key = 'premium_deactive';
				} else {
					// free component activated
					$order_key = 'premium_active';
				}
			} else {
				$order_key = 'other';
			}

			$ordered_components[$order_key][] = $component;
		}

		return array_merge($ordered_components['premium_active'], $ordered_components['premium_deactive'], $ordered_components['other']);
	}

	/**
	 * This method simply show contents of the component page.
	 *
	 * @throws \Exception
	 */
	public function showPageContent()
	{
		$components = $this->order($this->get_components());

		/**
		 * @param array $components
		 * @since 1.4.0
		 *
		 */
		$components = apply_filters('wbcr/clearfy/components/items_list', $components);

		?>
		<div class="wbcr-factory-page-group-header"><?php _e('<strong>Plugin Components</strong>.', 'wbcr_factory_clearfy_227') ?>
			<p>
				<?php _e('These are components of the plugin bundle. When you activate the plugin, all the components turned on by default. If you don’t need some function, you can easily turn it off on this page.', 'wbcr_factory_clearfy_227') ?>
			</p>
		</div>
		<div class="wbc-factory-clearfy-227-components">
			<?php
			/**
			 * @since 1.4.0
			 */
			do_action('wbcr/clearfy/components/custom_plugins_card', $components);
			?>

			<?php foreach((array)$components as $component): ?>
				<?php

				$slug = $component['name'];

				if( $component['type'] == 'wordpress' || $component['type'] == 'creativemotion' ) {
					$slug = $component['base_path'];
				}

				$install_button = $this->plugin->get_install_component_button($component['type'], $slug);

				$status_class = '';
				if( !$install_button->is_plugin_activate() ) {
					$status_class = ' plugin-status-deactive';
				}

				$install_button->add_class('install-now');

				// Delete button
				$delete_button = $this->plugin->get_delete_component_button($component['type'], $slug);
				$delete_button->add_class('delete-now');

				?>
				<div class="plugin-card<?php echo esc_attr($status_class) ?>">
					<?php if( isset($component['build']) ): ?>
						<div class="plugin-card-<?php echo esc_attr($component['build']) ?>-ribbon"><?php echo ucfirst(esc_html($component['build'])) ?></div>
					<?php endif; ?>
					<div class="plugin-card-top">
						<div class="name column-name">
							<h3>
								<a href="<?php echo esc_url($component['url']) ?>" class="thickbox open-plugin-details-modal">
									<?php echo esc_html($component['title']) ?>
									<img src="<?php echo esc_attr($component['icon']) ?>" class="plugin-icon" alt="<?php echo esc_attr($component['title']) ?>">
								</a>
							</h3>
						</div>
						<div class="desc column-description">
							<p><?php echo esc_html($component['description']); ?></p>
						</div>
					</div>
					<div class="plugin-card-bottom">
						<?php if( 'premium' === $component['build'] && !($this->plugin->premium->is_activate() && $this->plugin->premium->is_install_package()) ): ?>
							<a target="_blank" href="<?php echo esc_url($component['url']) ?>" class="button button-default install-now"><?php _e('Read more', 'wbcr_factory_clearfy_227'); ?></a>
						<?php else: ?>
							<?php $delete_button->render_button(); ?><?php $install_button->render_button(); ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
			<div class="clearfix"></div>
		</div>
		<?php
	}
}


