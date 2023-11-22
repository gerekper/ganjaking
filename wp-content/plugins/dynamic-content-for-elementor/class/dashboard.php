<?php

namespace DynamicContentForElementor;

class Dashboard
{
    public function __construct()
    {
        // Dashboard box
        //add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard_widget' ] );
    }
    public function add_dashboard_widget()
    {
        wp_add_dashboard_widget('dce-dashboard-overview', DCE_PRODUCT_NAME_LONG, [$this, 'dashboard_overview_widget']);
        global $wp_meta_boxes;
        $dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
        $ours = ['dce-dashboard-overview' => $dashboard['dce-dashboard-overview']];
        // phpcs:ignore WordPress.WP.GlobalVariablesOverride
        $wp_meta_boxes['dashboard']['normal']['core'] = \array_merge($ours, $dashboard);
    }
    public function dashboard_overview_widget()
    {
        ?>
		<div class="e-dashboard-widget">
			<div class="dce-overview__header">
			<div class="dce-overview__logo"><div class="dce-logo-wrapper"><img src="<?php 
        echo DCE_URL . 'assets/media/dce.png';
        ?>" width="36" /></div></div>
				<div class="dce-overview__versions">
					<span class="dce-overview__version"><?php 
        echo DCE_PRODUCT_NAME_LONG;
        ?> v<?php 
        echo DCE_VERSION;
        ?></span>
				</div>
			</div>
			<div class="dce-overview__links">
				<ul>
					<li class="dce-overview__link"><a href="<?php 
        echo admin_url('admin.php?page=dce-features&tab=widgets');
        ?>"><span aria-hidden="true" class="dashicons dashicons-admin-generic"></span><?php 
        _e('Features', 'dynamic-content-for-elementor');
        ?></a></li>
					<li class="dce-overview__link"><a href="<?php 
        echo admin_url('admin.php?page=dce-templatesystem');
        ?>"><span aria-hidden="true" class="dashicons dashicons-welcome-widgets-menus"></span><?php 
        _e('Template System', 'dynamic-content-for-elementor');
        ?></a></li>
					<li class="dce-overview__link"><a href="<?php 
        echo admin_url('admin.php?page=dce-license');
        ?>"><span aria-hidden="true" class="dashicons dashicons-admin-network"></span><?php 
        _e('License', 'dynamic-content-for-elementor');
        ?></a></li>
				</ul>
			</div>
			<div class="dce-overview__footer">
				<ul>
					<li class="dce-overview__help"><a href="https://www.dynamic.ooo/" target="_blank"><?php 
        echo DCE_PRODUCT_NAME;
        ?><span class="screen-reader-text"><?php 
        echo DCE_PRODUCT_NAME;
        ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
					<li class="dce-overview__help"><a href="http://facebook.com/groups/dynamic.ooo" target="_blank"><?php 
        _e('Facebook Community', 'dynamic-content-for-elementor');
        ?><span class="screen-reader-text"><?php 
        _e('Facebook Community', 'dynamic-content-for-elementor');
        ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
					<li class="dce-overview__docs"><a href="https://help.dynamic.ooo" target="_blank"><?php 
        _e('Docs', 'dynamic-content-for-elementor');
        ?><span class="screen-reader-text"><?php 
        _e('Docs', 'dynamic-content-for-elementor');
        ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
				</ul>
			</div>
		</div>
		<?php 
    }
}
