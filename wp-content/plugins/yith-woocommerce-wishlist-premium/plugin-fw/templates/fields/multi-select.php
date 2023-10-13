<?php
/**
 * Template for displaying the multi-select field
 *
 * @var array $field The field.
 * @package YITH\PluginFramework\Templates\Fields
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

list ( $field_id, $class, $name, $selects, $size, $columns, $value ) = yith_plugin_fw_extract( $field, 'id', 'class', 'name', 'selects', 'size', 'columns', 'value' );

if ( empty( $selects ) ) {
	return;
}

$selects_count = count( $selects );
$gap           = 16;
$columns       = max( 1, absint( $columns ?? 2 ) );
$default_size  = absint( ( 400 - ( $gap * ( $columns - 1 ) ) ) / $columns );
$size          = max( 122, absint( $size ?? $default_size ) );
$max_width     = $size * $columns + ( $gap * ( $columns - 1 ) );
?>
<div
		class="yith-plugin-fw-multi-select"
		id="<?php echo esc_attr( $field_id ); ?>"
		style="max-width: <?php echo absint( $max_width ); ?>px; grid-template-columns : repeat( auto-fit, <?php echo absint( $size ); ?>px ); gap: <?php echo absint( $gap ); ?>px"
>
	<?php for ( $i = 0; $i < $selects_count; $i ++ ) : ?>
		<div class="yith-single-select">
			<?php
			$select          = $selects[ $i ];
			$select['type']  = 'select';
			$select['title'] = isset( $select['title'] ) ? $select['title'] : $select['name'];
			$select['name']  = $name . "[{$select['id']}]";
			$select['value'] = isset( $value[ $select['id'] ] ) ? $value[ $select['id'] ] : $select['default'];
			$select['id']    = $name . '_' . $select['id'];
			$select['class'] = $class
			?>
			<label for="<?php echo esc_attr( $select['id'] ); ?>"><?php echo esc_html( $select['title'] ); ?></label>
			<?php yith_plugin_fw_get_field( $select, true, false ); ?>
		</div>
	<?php endfor; ?>
</div>
