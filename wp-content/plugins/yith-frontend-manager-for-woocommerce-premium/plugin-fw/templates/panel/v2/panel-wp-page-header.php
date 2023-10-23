<?php
/**
 * The Template for displaying the Panel Header.
 *
 * @var YIT_Plugin_Panel|YIT_Plugin_Panel_WooCommerce $panel
 * @var string                                        $page_wrapper_classes
 * @var string                                        $wrap_class
 * @var array                                         $tabs_nav_args
 * @var bool                                          $has_sub_tabs
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit;

$collapsed_class = get_user_setting( 'yithFwSidebarFold', 'o' ) === 'f' ? 'yith-plugin-fw__panel__sidebar--collapsed' : '';
?>
<div class="yith-plugin-fw__panel">
	<div class="yith-plugin-fw__panel__sidebar <?php echo esc_attr( $collapsed_class ); ?>">
		<?php
		$panel->print_sidebar_header();
		$panel->print_tabs_nav( $tabs_nav_args );
		?>
	</div><!-- yith-plugin-fw__panel__sidebar -->
	<div class="<?php echo esc_attr( $panel->get_panel_content_classes() ); ?>">
		<?php $panel->render_panel_header_nav(); ?>
		<div class="<?php echo esc_attr( $page_wrapper_classes ); ?>">
