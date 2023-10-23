<?php
/**
 * The Template for displaying the Panel Tabs nav.
 *
 * @var YIT_Plugin_Panel|YIT_Plugin_Panel_WooCommerce $panel
 * @var array                                         $nav_args
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit;

list ( $current_tab, $current_sub_tab, $the_page ) = yith_plugin_fw_extract( $nav_args, 'current_tab', 'current_sub_tab', 'page' );

$first_sub_tab = $panel->get_first_sub_tab_key( $current_tab );
$layout        = $panel->get_sub_tabs_nav_layout( $current_tab );
$sub_tabs      = $panel->get_sub_tabs( $current_tab );
?>

<?php if ( $sub_tabs && 'horizontal' === $layout ) : ?>
	<div class="yith-plugin-fw__panel__header-nav__wrapper">
		<div class="yith-plugin-fw__panel__header-nav">
			<?php foreach ( $sub_tabs as $sub_tab_key => $sub_tab_data ) : ?>
				<?php
				$item_active_class = $current_sub_tab === $sub_tab_key ? 'yith-plugin-fw--active' : '';
				$item_classes      = array( 'yith-plugin-fw__panel__header-nav-item', $item_active_class );
				$item_classes      = $panel->apply_filters( 'nav_submenu_item_classes', $item_classes, $current_tab, $sub_tab_key, $sub_tab_data, $nav_args );
				$item_classes      = implode( ' ', array_filter( $item_classes ) );

				$url = $panel->get_nav_url( $the_page, $current_tab, $sub_tab_key );
				?>
				<div class="<?php echo esc_attr( $item_classes ); ?>">
					<a class="yith-plugin-fw__panel__header-nav-item__content" href="<?php echo esc_url( $url ); ?>">
						<span class="yith-plugin-fw__panel__header-nav-item__name">
							<?php echo wp_kses_post( $sub_tab_data['title'] ); ?>
						</span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>