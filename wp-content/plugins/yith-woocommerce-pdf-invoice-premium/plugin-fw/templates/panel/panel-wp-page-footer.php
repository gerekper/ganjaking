<?php
/**
 * The Template for displaying the Panel Footer in WP Pages.
 *
 * @var YIT_Plugin_Panel|YIT_Plugin_Panel_WooCommerce $panel
 * @var bool                                          $has_sub_tabs
 * @var array                                         $page_args
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit;
?>

<?php if ( $has_sub_tabs ) : ?>
	</div><!-- /yith-plugin-fw-wp-page__sub-tab-wrap -->
<?php endif; ?>
</div><!-- /yith-plugin-fw-wp-page-wrapper -->
