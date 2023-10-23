<?php
/**
 * Frontend Manager content
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<small class="act">
	<?php do_action( 'yith_wcfm_inline_actions_start' ); ?>
	<?php if( ! empty( $edit_uri ) ) : ?>
        <a href="<?php echo $edit_uri; ?>" class="yith-wcfm-edit edit">
            <?php echo __('Edit', 'yith-frontend-manager-for-woocommerce'); ?>
        </a>
	<?php endif; ?>
	<?php if( ! empty( $delete_uri ) ) : ?>
        <?php echo ! empty( $edit_uri ) ? '|' : ''; ?>
        <a href="<?php echo $delete_uri ?>" class="yith-wcfm-delete delete">
            <?php echo __('Delete', 'yith-frontend-manager-for-woocommerce'); ?>
        </a>
    <?php endif; ?>
    <?php if( ! empty( $view_uri ) ) : ?>
	    <?php echo ! empty( $edit_uri ) || ! empty( $delete_uri )  ? '|' : ''; ?>
        <a href="<?php echo $view_uri ?>" class="yith-wcfm-view view">
            <?php echo __('View', 'yith-frontend-manager-for-woocommerce'); ?>
        </a>
    <?php endif; ?>
	<?php if( ! empty( $duplicate_uri ) ) : ?>
		<?php echo ! empty( $view_uri ) || ! empty( $delete_uri )  ? '|' : ''; ?>
		<a href="<?php echo $duplicate_uri ?>" class="yith-wcfm-view view">
			<?php echo __('Duplicate', 'yith-frontend-manager-for-woocommerce'); ?>
		</a>	<?php endif; ?>
    <?php do_action( 'yith_wcfm_inline_actions_end' ); ?>
</small>
