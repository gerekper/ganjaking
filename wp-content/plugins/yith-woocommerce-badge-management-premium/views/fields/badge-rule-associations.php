<?php
/**
 * Associations badge rule Field
 *
 * @var string $id                 Field ID.
 * @var string $name               Field Name.
 * @var string $text               Field text.
 * @var array  $value              Field Values.
 * @var array  $associations_field Association field args.
 * @var array  $badge_field        Badge field args.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Views
 */

$value = ! $value ? array( array() ) : $value;

?>

<div id="<?php echo esc_attr( $id ); ?>" class="yith-wcbm-associations-badge-rules-container">
	<div class="yith-wcbm-associations-badge-rules">
		<?php foreach ( array_values( $value ) as $index => $rule ) : ?>
			<?php
			$badge_values       = array(
				'name'  => str_replace( '{{data.ruleID}}', $index, $badge_field['name'] ),
				'value' => $rule['badge'] ?? '',
			);
			$association_values = array(
				'name'  => str_replace( '{{data.ruleID}}', $index, $associations_field['name'] ),
				'value' => $rule['association'] ?? '',
			);
			?>
			<div class="yith-wcbm-association-badge-rule-wrapper">
				<?php echo sprintf( $text, yith_plugin_fw_get_field( array_merge( $associations_field, $association_values ) ), yith_plugin_fw_get_field( array_merge( $badge_field, $badge_values ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="yith-wcbm-remove-association-badge-rule yith-icon yith-icon-trash"></span>
			</div>
		<?php endforeach; ?>
	</div>
	<span class="yith-wcbm-add-association-rule">
	<?php esc_html_e( '+ Add rule', 'yith-woocommerce-badges-management' ); ?>
</span>
</div>

<script type="text/html" id="tmpl-<?php echo esc_attr( $id ); ?>">
	<div class="yith-wcbm-association-badge-rule-wrapper">
		<?php echo sprintf( $text, yith_plugin_fw_get_field( $associations_field ), yith_plugin_fw_get_field( $badge_field ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span class="yith-wcbm-remove-association-badge-rule yith-icon yith-icon-trash"></span>
	</div>
</script>
