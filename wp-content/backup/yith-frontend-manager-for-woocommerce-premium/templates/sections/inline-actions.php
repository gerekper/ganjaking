<?php
/**
 * Frontend Manager content
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<small class="act">
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
</small>