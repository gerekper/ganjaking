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
<h2 class="<?php echo esc_attr( $wrapper_class ); ?>">
	<ul class="yith-plugin-fw-tabs">
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
	</ul>
</h2>

<?php
$panel->print_sub_tabs_nav( $nav_args );
?>
