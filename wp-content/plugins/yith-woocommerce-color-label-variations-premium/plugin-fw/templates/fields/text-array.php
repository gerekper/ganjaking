<?php
/**
 * Template for displaying the text-array field
 *
 * @var array $field The field.
 * @package YITH\PluginFramework\Templates\Fields
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

list ( $field_id, $name, $value, $fields, $size ) = yith_plugin_fw_extract( $field, 'id', 'name', 'value', 'fields', 'size' );

$value = isset( $value ) && is_array( $value ) ? $value : array();
?>
<table class="yith-plugin-fw-text-array-table">
	<?php foreach ( $fields as $field_name => $field_label ) : ?>
		<?php
		$current_name  = "{$name}[{$field_name}]";
		$current_id    = "{$field_id}_{$field_name}";
		$current_value = isset( $value[ $field_name ] ) ? $value[ $field_name ] : '';
		?>
		<tr>
			<td><?php echo esc_html( $field_label ); ?></td>
			<td>
				<input type="text" id="<?php echo esc_attr( $current_id ); ?>"
						name="<?php echo esc_attr( $current_name ); ?>"
						value="<?php echo esc_attr( $current_value ); ?>"
					<?php if ( isset( $size ) ) : ?>
						style="width: <?php echo absint( $size ); ?>px"
					<?php endif; ?>
				/>
			</td>
		</tr>
	<?php endforeach ?>
</table>
