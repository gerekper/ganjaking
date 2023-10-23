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
?>
<div class="<?php echo esc_attr( $page_wrapper_classes ); ?>">

	<div class="<?php echo esc_attr( $wrap_class ); ?>">
		<?php
		$panel->print_tabs_nav( $tabs_nav_args );
		?>
	</div>
<?php if ( $has_sub_tabs ) : ?>
	<div class="yith-plugin-fw-wp-page__sub-tab-wrap">
<?php endif; // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentAfterOpen
