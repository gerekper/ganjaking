<?php
/**
 * Admin View: Fields General Tab
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! empty( $sections ) ) : ?>

	<div id="ywccp-submenu">
		<ul class="subsubsub">
		<?php foreach( $sections as $key => $value ) : ?>
			<li>
				<a href="<?php echo add_query_arg( 'section', $value, $base_page_url ); ?>" <?php echo ( $value == $current ) ? 'class="current"' : '' ?> ><?php echo ucwords( $value ) . ' ' . __('Fields', 'yith-woocommerce-checkout-manager'); ?></a>
				<?php if( end( $sections ) != $value ) echo ' | '; ?>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>

<?php endif; ?>

<?php if( ! $current ) : ?>
<?php else : ?>
	<?php
	/**
	 * This action print the fields table based on current visible section
	 */
	do_action( 'ywccp_print_admin_fields_section_table', $current );
	?>
<?php endif; ?>

<!-- RESET FORM -->
<form id="plugin-fw-wc-reset" method="post">
	<?php $warning = __( 'If you go on with this action, you will reset all options in this page.', 'yith-woocommerce-checkout-manager' ) ?>
	<input type="hidden" name="ywccp-admin-action" value="fields-reset" />
	<input type="hidden" name="ywccp-admin-section" value="<?php echo $current ?>" />
	<input type="submit" name="yit-reset" class="button-secondary" value="<?php _e( 'Reset defaults', 'yith-woocommerce-checkout-manager' ) ?>" onclick="return confirm('<?php echo $warning . '\n' . __( 'Are you sure?', 'yith-woocommerce-checkout-manager' ) ?>');" />
</form>
