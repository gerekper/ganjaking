<?php
/**
 * The Template for displaying the Panel Tabs nav.
 *
 * @var YIT_Plugin_Panel|YIT_Plugin_Panel_WooCommerce $panel
 * @var array                                         $nav_args
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit;

list ( $wrapper_class ) = yith_plugin_fw_extract( $nav_args, 'wrapper_class' );
?>
<div class="yith-plugin-fw__panel__menu__wrapper <?php echo esc_attr( $wrapper_class ); ?>">
	<div class="yith-plugin-fw__panel__menu">
		<?php
		foreach ( $panel->settings['admin-tabs'] as $tab_key => $tab_data ) {
			$panel->get_template(
				'panel-nav-item.php',
				array(
					'panel'    => $panel,
					'tab_key'  => $tab_key,
					'tab_data' => $tab_data,
					'nav_args' => $nav_args,
				)
			);
		}
		?>
		<div id="yith-plugin-fw__panel__menu-item-collapse" class="yith-plugin-fw__panel__menu-item">
			<a class="yith-plugin-fw__panel__menu-item__content yith-plugin-fw__panel__sidebar__collapse" href="#">
				<span class="yith-plugin-fw__panel__menu-item__icon">
					<svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path clip-rule="evenodd" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.25-7.25a.75.75 0 000-1.5H8.66l2.1-1.95a.75.75 0 10-1.02-1.1l-3.5 3.25a.75.75 0 000 1.1l3.5 3.25a.75.75 0 001.02-1.1l-2.1-1.95h4.59z"></path>
					</svg>
				</span>
				<span class="yith-plugin-fw__panel__menu-item__name"><?php esc_html_e( 'Collapse', 'yith-plugin-fw' ); ?></span>
			</a>
		</div>
	</div>
</div>
