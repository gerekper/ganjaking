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

global $pagenow;

$description = $page_args['description'] ?? '';
$is_list     = in_array( $pagenow, array( 'edit.php', 'edit-tags.php' ), true )
?>

<?php if ( $is_list && ! ! $description ) : ?>
	<div class="yith-plugin-fw-wp-page__description">
		<?php echo wp_kses_post( $description ); ?>
	</div>
	<script type="text/javascript">
		jQuery( '.yith-plugin-fw-wp-page__description' ).insertBefore( 'hr.wp-header-end' );
	</script>
<?php endif; ?>

</div><!-- /yith-plugin-fw-wp-page-wrapper -->
</div><!-- yith-plugin-fw__panel__content-->
</div><!-- yith-plugin-fw__panel -->
