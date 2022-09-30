<?php
/**
 * Membership advanced administration metabox template
 *
 * @var array $fields
 */
?>

<div id='yith-wcmbs-advanced-admin' class='yith-plugin-ui'>
	<div class='yith-wcmbs-form-field'>
		<label class="yith-wcmbs-form-field__label"><?php esc_html_e( 'Enable Editing', 'yith-woocommerce-membership' ); ?></label>
		<div class="yith-wcmbs-form-field__content">
			<?php yith_plugin_fw_get_field(
				array(
					'type'  => 'onoff',
					'id'    => 'yith-wcmbs-advanced-admin__enabled-toggle',
					'value' => false,
				),
				true,
				false );
			?>
		</div>
	</div>

	<div id='yith-wcmbs-advanced-admin__content' class='yith-wcmbs-advanced-admin--show-if-enabled' style="display: none">

		<div class='yith-wcmbs-advanced-admin__notice'>
			<?php esc_html_e( 'Please pay attention when editing these fields, as any modification will not be reversible!', 'yith-woocommerce-membership' ); ?>
		</div>

		<?php foreach ( $fields as $field_key => $field ): ?>
			<div class='yith-wcmbs-form-field'>
				<label class="yith-wcmbs-form-field__label"><?php echo esc_html( $field['label'] ); ?></label>
				<div class="yith-wcmbs-form-field__content">
					<?php yith_plugin_fw_get_field( $field, true ); ?>
					<?php
					if ( ! empty( $field['extra_html'] ) ) {
						echo $field['extra_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>