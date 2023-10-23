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

$icons = array(
	'dashboard'     => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"></path></svg>',
	'settings'      => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
	'configuration' => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 13.5V3.75m0 9.75a1.5 1.5 0 010 3m0-3a1.5 1.5 0 000 3m0 3.75V16.5m12-3V3.75m0 9.75a1.5 1.5 0 010 3m0-3a1.5 1.5 0 000 3m0 3.75V16.5m-6-9V3.75m0 3.75a1.5 1.5 0 010 3m0-3a1.5 1.5 0 000 3m0 9.75V10.5"></path></svg>',
	'tools'         => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"></path></svg>',
	'add-ons'       => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z"></path></svg>',
	'email'         => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"></path></svg>',
	'boost'         => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path></svg>',
	'help'          => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"></path></svg>',
	'premium'       => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"></path></svg>',
);

$first_sub_tab = $panel->get_first_sub_tab_key( $tab_key );
$sub_tab       = ! ! $first_sub_tab ? $first_sub_tab : '';
$layout        = $panel->get_sub_tabs_nav_layout( $tab_key );
$sub_tabs      = $panel->get_sub_tabs( $tab_key );
$url           = $panel->get_nav_url( $the_page, $tab_key, $sub_tab, $parent_page );

$is_current  = $current_tab === $tab_key;
$is_opened   = $is_current;
$has_submenu = ! ! $sub_tabs && 'vertical' === $layout;
$icon        = false;
if ( isset( $tab_data['icon'] ) ) {
	$icon = $icons[ $tab_data['icon'] ] ?? $tab_data['icon'];
}
$has_icon = isset( $tab_data['icon'] );

$active_class = $current_tab === $tab_key && ( ! $current_sub_tab || ! $has_submenu ) ? 'yith-plugin-fw--active' : '';

if ( 'premium' === $tab_key ) {
	$active_class .= ' ' . $premium_class;
}
$active_class = apply_filters( 'yith_plugin_fw_panel_active_tab_class', $active_class, $current_tab, $tab_key );


$menu_id = 'yith-plugin-fw__panel__menu-item-' . $tab_key;
$classes = array( 'yith-plugin-fw__panel__menu-item' );
if ( $has_submenu ) {
	$classes[] = 'yith-plugin-fw--has-submenu';
	$classes[] = $is_current ? 'yith-plugin-fw__panel__menu-item--current' : '';
	$classes[] = $is_opened ? 'yith-plugin-fw--open' : '';
} else {
	$classes[] = $active_class;
}

$classes = $panel->apply_filters( 'nav_item_classes', $classes, $tab_key, $tab_data, $nav_args );
$classes = implode( ' ', array_filter( $classes ) );

$allowed_icon_tags = array_merge( wp_kses_allowed_html( 'post' ), yith_plugin_fw_kses_allowed_svg_tags() );

?>
<div id="<?php echo esc_attr( $menu_id ); ?>" class="<?php echo esc_attr( $classes ); ?>">
	<a class="yith-plugin-fw__panel__menu-item__content" href="<?php echo esc_url( $url ); ?>">
		<?php if ( $icon ) : ?>
			<span class="yith-plugin-fw__panel__menu-item__icon">
				<?php echo wp_kses( $icon, $allowed_icon_tags ); ?>
			</span>
		<?php endif; ?>

		<span class="yith-plugin-fw__panel__menu-item__name"><?php echo wp_kses_post( $tab_data['title'] ); ?></span>

		<?php if ( $has_submenu ) : ?>
			<svg class="yith-plugin-fw__panel__menu-item__toggle" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path>
			</svg>
		<?php endif; ?>
	</a>

	<?php if ( $has_submenu ) : ?>
		<div class="yith-plugin-fw__panel__submenu" style="<?php echo ! $is_opened ? 'display: none' : ''; ?>">
			<div class="yith-plugin-fw__panel__submenu__wrap">
				<div class="yith-plugin-fw__panel__submenu__head" aria-hidden="true">
					<?php echo wp_kses_post( $tab_data['title'] ); ?>
				</div>
				<?php foreach ( $sub_tabs as $sub_tab_key => $sub_tab_data ) : ?>
					<?php
					$item_active_class = $current_tab === $tab_key && $current_sub_tab === $sub_tab_key ? 'yith-plugin-fw--active' : '';
					$item_classes      = array( 'yith-plugin-fw__panel__submenu-item', $item_active_class );
					$item_classes      = $panel->apply_filters( 'nav_submenu_item_classes', $item_classes, $tab_key, $sub_tab_key, $sub_tab_data, $nav_args );
					$item_classes      = implode( ' ', array_filter( $item_classes ) );

					$url = $panel->get_nav_url( $the_page, $tab_key, $sub_tab_key );
					?>
					<div class="<?php echo esc_attr( $item_classes ); ?>">
						<a class="yith-plugin-fw__panel__submenu-item__content" href="<?php echo esc_url( $url ); ?>">
						<span class="yith-plugin-fw__panel__submenu-item__name">
							<?php echo wp_kses_post( $sub_tab_data['title'] ); ?>
						</span>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
