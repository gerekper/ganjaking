<?php
/**
 * My account admin endpoint item
 *
 * @package YITH WooCommerce Customize My Account Page
 * @since 3.0.0
 * @author YITH <plugins@yithemes.com>
 * @var string $item_key
 * @var array $options
 * @var array $fields
 * @var string $type
 * @var boolean $is_default
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

?>

<li class="dd-item item <?php echo esc_attr( $type ); ?>" data-id="<?php echo esc_attr( $item_key ); ?>" data-type="<?php echo esc_attr( $type ); ?>">

	<?php
	/**
	 * DO_ACTION: yith_wcmap_admin_before_single_item_content
	 *
	 * Allows to render some content before the item in the plugin panel.
	 *
	 * @param string $item_key Item key.
	 * @param array  $options  Item options
	 * @param string $type     Item type.
	 */
	do_action( 'yith_wcmap_admin_before_single_item_content', $item_key, $options, $type );
	?>

	<div class="dd-handle item-content">
		<!-- Header -->
		<div class="item-header">
			<div class="toggle-options dd-nodrag">
				<span class="yith-icon yith-icon-arrow_right"></span>
			</div>
			<div class="item-info-container">
				<div class="item-info">
					<span class="item-title">
						<?php echo isset( $options['label'] ) ? esc_html( $options['label'] ) : ''; ?>
						<span class="sub-item-label">
							<i><?php esc_html_e( 'sub item', 'yith-woocommerce-customize-myaccount-page' ); ?></i>
						</span>
					</span>
					<span class="item-type">
						<?php echo esc_html( ucfirst( $type ) ); ?>
					</span>
				</div>
			</div>
			<div class="item-actions dd-nodrag">
				<?php
				$endpoint_status = array(
					'id'      => 'yith_wcmap_endpoint_' . esc_attr( $item_key ) . '_active',
					'name'    => 'yith_wcmap_endpoint_' . esc_attr( $item_key ) . '[active]',
					'type'    => 'onoff',
					'default' => 'on',
					'value'   => $options['active'] ? 'yes' : 'no',
				);

				yith_plugin_fw_get_field( $endpoint_status, true );

				if ( ! $is_default ) {
					yith_plugin_fw_get_component(
						array(
							'title'  => __( 'Delete', 'yith-woocommerce-customize-myaccount-page' ),
							'type'   => 'action-button',
							'action' => 'delete',
							'icon'   => 'trash',
							'class'  => 'remove-item',
						)
					);
				} else {
					echo '<div class="yith-wcamp-endpoint-delete-placeholder"></div>';
					yith_plugin_fw_get_component(
						array(
							'title'  => __( 'Default item, it can\'t be deleted', 'yith-woocommerce-customize-myaccount-page' ),
							'type'   => 'action-button',
							'action' => 'delete',
							'icon'   => 'trash',
							'class'  => 'remove-item disabled',
							'url'    => '#;',
						)
					);
				}
				?>

			</div>
			<?php
				yith_plugin_fw_get_component(
					array(
						'title'  => __( 'Rearrange', 'yith-woocommerce-customize-myaccount-page' ),
						'type'   => 'action-button',
						'icon'   => 'drag',
						'class'  => 'drag-item',
					)
				);
			?>
		</div>
		<!-- Content -->
		<div class="item-options" style="display: none;">
			<table class="options-table form-table">
				<tbody>
				<?php
				foreach ( $fields as $field => $field_options ) :

					if ( isset( $field_options['exclude'] ) && in_array( $item_key, $field_options['exclude'], true ) ) {
						continue;
					}
					// Prepare option fields.
					$value = isset( $options[ $field ] ) ? $options[ $field ] : '';
					yith_wcmap_admin_print_single_field( $field_options, $field, "yith_wcmap_endpoint_{$item_key}", $value );

				endforeach;
				?>
				</tbody>
			</table>
			<div class="yith-toggle-content-buttons">
				<div class="spinner"></div>
				<button class="button-primary save-item">
					<?php echo esc_attr_x( 'Save', 'Admin button label', 'yith-woocommerce-customize-myaccount-page' ); ?>
				</button>
				<button class="button-secondary reset-item">
					<?php echo esc_attr_x( 'Reset', 'Admin button label', 'yith-woocommerce-customize-myaccount-page' ); ?>
				</button>
			</div>
		</div>
	</div>

	<?php
	/**
	 * DO_ACTION: yith_wcmap_admin_after_single_item_content
	 *
	 * Allows to render some content after the item in the plugin panel.
	 *
	 * @param string $item_key Item key.
	 * @param array  $options  Item options
	 * @param string $type     Item type.
	 */
	do_action( 'yith_wcmap_admin_after_single_item_content', $item_key, $options, $type );
	?>
</li>
