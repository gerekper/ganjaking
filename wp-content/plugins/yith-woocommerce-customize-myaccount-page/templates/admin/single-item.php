<?php
/**
 * My account admin endpoint item
 *
 * @package YITH WooCommerce Customize My Account Page
 * @since 3.0.0
 * @author YITH
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
	<div class="dd-handle item-content">
		<!-- Header -->
		<div class="item-header">
			<div class="toggle-options dd-nodrag">
				<span class="yith-icon yith-icon-arrow_right"></span>
			</div>
			<div class="item-info-container">
				<div class="item-info">
					<span class="item-title">
						<?php echo esc_html( $options['label'] ); ?>
						<span class="sub-item-label">
							<i><?php esc_html_e( 'sub item', 'yith-woocommerce-customize-myaccount-page' ); ?></i>
						</span>
					</span>
					<span class="item-type">
						<?php echo esc_html( ucfirst( $type ) ); ?>
					</span>
					<span class="yith-icon yith-icon-drag drag-item"></span>
				</div>
			</div>
			<div class="item-actions dd-nodrag">
				<?php if ( ! $is_default ) : ?>
					<span class="yith-icon yith-icon-trash remove-item"></span>
				<?php endif; ?>
				<span class="yith-plugin-ui">
					<span class="yith-plugin-fw-onoff-container">
						<input type="checkbox" id="yith_wcmap_endpoint_<?php echo esc_attr( $item_key ); ?>_active"
								name="yith_wcmap_endpoint_<?php echo esc_attr( $item_key ); ?>[active]"
								value="yes" <?php checked( $options['active'] ); ?> class="on_off">
						<span class="yith-plugin-fw-onoff"
								data-text-on="<?php echo esc_attr_x( 'YES', 'YES/NO button: use MAX 3 characters!', 'yith-woocommerce-customize-myaccount-page' ); ?>"
								data-text-off="<?php echo esc_attr_x( 'NO', 'YES/NO button: use MAX 3 characters!', 'yith-woocommerce-customize-myaccount-page' ); ?>"></span>
					</span>
				</span>
			</div>
		</div>
		<!-- Content -->
		<div class="item-options" style="display: none;">
			<table class="options-table form-table">
				<tbody>
				<?php
				foreach ( $fields as $field => $field_options ) :

					if ( isset( $field_options['exclude'] ) && in_array( $item_key, $field_options['exclude'] ) ) {
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
				<button class="yith-save-button save-item">
					<?php echo esc_attr_x( 'Save', 'Admin button label', 'yith-woocommerce-customize-myaccount-page' ); ?>
				</button>
			</div>
		</div>
	</div>

	<?php if ( ! empty( $options['children'] ) ) : ?>
		<ol class="dd-list items">
			<?php
			foreach ( (array) $options['children'] as $key => $single_options ) {
				$args = array(
					'item_key' => $key,
					'options'  => $single_options,
					'type'     => isset( $single_options['url'] ) ? 'link' : 'endpoint',
				);

				call_user_func( 'yith_wcmap_admin_print_single_item', $args );
			}
			?>
		</ol>
	<?php endif; ?>
</li>
