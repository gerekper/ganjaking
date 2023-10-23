<?php
/**
 * The Template for displaying the Panel Tab nav.
 *
 * @var YIT_Plugin_Panel|YIT_Plugin_Panel_WooCommerce $panel
 * @var string                                        $tab_key
 * @var array                                         $tab_data
 * @var array                                         $nav_args
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit;

list ( $current_tab, $current_sub_tab, $premium_class, $the_page, $parent_page ) = yith_plugin_fw_extract( $nav_args, 'current_tab', 'current_sub_tab', 'premium_class', 'page', 'parent_page' );

$active_class = $current_tab === $tab_key ? 'nav-tab-active' : '';

if ( 'premium' === $tab_key ) {
	$active_class .= ' ' . $premium_class;
}
$active_class = apply_filters( 'yith_plugin_fw_panel_active_tab_class', $active_class, $current_tab, $tab_key );

$first_sub_tab = $panel->get_first_sub_tab_key( $tab_key );
$sub_tab       = ! ! $first_sub_tab ? $first_sub_tab : '';
$sub_tabs      = $panel->get_sub_tabs( $tab_key );
$url           = $panel->get_nav_url( $the_page, $tab_key, $sub_tab, $parent_page );

$is_opened = $current_tab === $tab_key;

$has_submenu = $current_tab !== $tab_key && $sub_tabs;

?>
<li class="yith-plugin-fw-tab-element">
	<a class="nav-tab <?php echo esc_attr( $active_class ); ?>" href="<?php echo esc_url( $url ); ?>">
		<?php echo wp_kses_post( $tab_data['title'] ); ?>
		<?php if ( $has_submenu ) : ?>
			<i class="yith-icon yith-icon-arrow_down"></i>
		<?php endif; ?>
	</a>

	<?php if ( $has_submenu ) : ?>
		<div class="nav-subtab-wrap">
			<ul class="nav-subtab">
				<?php foreach ( $sub_tabs as $sub_tab_key => $sub_tab_data ) : ?>
					<?php
					$url = $panel->get_nav_url( $the_page, $tab_key, $sub_tab_key );
					?>
					<li class="nav-subtab-item">
						<a href="<?php echo esc_url( $url ); ?>">
							<?php echo wp_kses_post( $sub_tab_data['title'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</li>
