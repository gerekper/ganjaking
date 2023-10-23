<?php
/**
 * My account admin items list
 *
 * @package YITH WooCommerce Customize My Account Page
 * @since 3.0.0
 * @author YITH <plugins@yithemes.com>
 * @var array $items An array of items to list.
 * @var array $actions An array of actions buttons.
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

?>

<div id="yith-wcmap-items-list" class="yith-plugin-fw  yit-admin-panel-container">
	<div class="button-container">
		<?php foreach ( $actions as $target => $button ) : ?>
			<button type="button" class="button yith-add-button create-new-item"
				data-target="<?php echo esc_attr( $target ); ?>" data-label="<?php echo esc_attr( $button['alt-label'] ); ?>">
				<?php echo esc_html( $button['label'] ); ?>
			</button>
		<?php endforeach; ?>
		<form id="yith-wcmap-add-item-form" method="POST" style="display: none;">
			<div class="yith-toggle-content-buttons">
				<button class="yith-save-button add-item">
					<?php echo esc_html_x( 'Add item', 'Admin button label', 'yith-woocommerce-customize-myaccount-page' ); ?>
				</button>
			</div>
			<input type="hidden" name="type" value="">
		</form>
	</div>
	<form id="yith-wcmap-items-form" method="POST">
		<div class="dd items-container">
			<ol class="dd-list items">
				<!-- Endpoints -->
				<?php
				foreach ( $items as $key => $item ) {
					call_user_func(
						'yith_wcmap_admin_print_single_item',
						array(
							'item_key' => $key,
							'type'     => isset( $value[ $key ] ) ? $value[ $key ]['type'] : 'endpoint',
							'options'  => $item,
						)
					);
				}
				?>
			</ol>
		</div>

		<input type="hidden" class="items-order" name="yith_wcmap_endpoint" value=""/>
		<p class="submit">
			<input name="yith_wcmap_items_save" class="button-primary" type="submit" value="<?php esc_html_e( 'Save changes', 'yith-woocommerce-customize-myaccount-page' ); ?>" />
			<?php wp_nonce_field( 'yith_wcmap_items_save' ); ?>
		</p>
	</form>
	<form id="yith-wcmap-items-form-reset" method="POST">
		<p class="submit">
			<input name="yith_wcmap_items_reset" class="button-secondary" type="submit" value="<?php esc_html_e( 'Reset Defaults', 'yith-woocommerce-customize-myaccount-page' ); ?>" />
			<?php wp_nonce_field( 'yith_wcmap_items_reset' ); ?>
		</p>
	</form>
</div>
