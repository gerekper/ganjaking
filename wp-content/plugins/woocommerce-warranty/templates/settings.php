<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<?php
$active_tab = ( ! empty( $_GET['tab'] ) ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
?>
<div class="wrap woocommerce">

	<h2><?php esc_html_e( 'Settings', 'wc_warranty' ); ?></h2>

	<?php if ( ! empty( $_GET['updated'] ) ) : ?>
		<div id="message" class="updated fade">
			<p><?php esc_html_e( 'Settings saved', 'wc_warranty' ); ?></p>
		</div>
	<?php endif; ?>

	<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a href="admin.php?page=warranties-settings" class="nav-tab <?php echo 'general' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'General', 'wc_warranty' ); ?></a>
		<a href="admin.php?page=warranties-settings&tab=default" class="nav-tab <?php echo 'default' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Default Warranty', 'wc_warranty' ); ?></a>
		<a href="admin.php?page=warranties-settings&tab=form" class="nav-tab <?php echo 'form' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Warranty Form Builder', 'wc_warranty' ); ?></a>
		<a href="admin.php?page=warranties-settings&tab=emails" class="nav-tab <?php echo 'emails' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Notification Emails', 'wc_warranty' ); ?></a>
		<a href="admin.php?page=warranties-settings&tab=permissions" class="nav-tab <?php echo 'permissions' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Permissions', 'wc_warranty' ); ?></a>
		<?php do_action( 'wc_warranty_settings_tabs' ); ?>
	</h2>

	<form action="admin-post.php" method="POST">
		<?php
		$settings = Warranty_Settings::get_settings_fields();

		switch ( $active_tab ) {
			case 'general':
				include WooCommerce_Warranty::$base_path . 'templates/settings/settings-general.php';
				break;

			case 'default':
				include WooCommerce_Warranty::$base_path . 'templates/settings/settings-default.php';
				break;

			case 'form':
				include WooCommerce_Warranty::$base_path . 'templates/settings/settings-form.php';
				break;

			case 'emails':
				include WooCommerce_Warranty::$base_path . 'templates/settings/settings-emails.php';
				break;

			case 'permissions':
				include WooCommerce_Warranty::$base_path . 'templates/settings/settings-permissions.php';
				break;
		}

		do_action( 'wc_warranty_settings_panels', $active_tab );
		?>

		<div class="submit">
			<input type="hidden" name="action" value="wc_warranty_settings_update" />
			<input type="hidden" name="tab" value="<?php echo esc_attr( $active_tab ); ?>" />
			<?php
			wp_nonce_field( 'wc_warranty_settings_save' );
			submit_button( __( 'Save changes', 'wc_warranty' ) );
			?>
		</div>
	</form>

</div>
<script>
	jQuery( document ).ready( function( $ ) {
		$( '.woocommerce-help-tip' ).tipTip( { 'attribute': 'data-tip' } );
	} );
</script>
