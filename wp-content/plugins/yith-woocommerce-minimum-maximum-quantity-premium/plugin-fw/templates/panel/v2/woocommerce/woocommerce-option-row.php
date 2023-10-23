<?php
/**
 * The Template for displaying the WooCommerce option row.
 *
 * @var YIT_Plugin_Panel_WooCommerce $panel
 * @var array                        $field The field.
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$default_field   = array(
	'id'    => '',
	'title' => $field['name'] ?? '',
	'desc'  => '',
);
$field           = wp_parse_args( $field, $default_field );
$extra_row_class = $field['extra_row_class'] ?? '';

$display_row    = ! in_array( $field['type'], array( 'hidden', 'html', 'sep', 'simple-text', 'title', 'list-table' ), true );
$display_row    = isset( $field['yith-display-row'] ) ? ! ! $field['yith-display-row'] : $display_row;
$show_container = $show_container ?? true;
$is_required    = ! empty( $field['required'] );

$is_disabled = $field['is_option_disabled'] ?? false;

$extra_row_classes = $is_required ? array( 'yith-plugin-fw--required' ) : array();
$extra_row_classes = (array) apply_filters( 'yith_plugin_fw_panel_wc_extra_row_classes', $extra_row_classes, $field );

$row_classes = array(
	'yith-plugin-fw__panel__option',
	'yith-plugin-fw__panel__option--' . $field['type'],
	$is_disabled ? 'yith-plugin-fw__panel__option--is-disabled' : '',
);
$row_classes = array_filter( array_merge( $row_classes, $extra_row_classes, array( $extra_row_class ) ) );
$row_classes = implode( ' ', $row_classes );

$label_id = ! ! $field['id'] ? ( $field['id'] . '__label' ) : '';

?>
<div class="<?php echo esc_attr( $row_classes ); ?>" <?php echo yith_field_deps_data( $field ); ?>>
	<?php if ( $display_row ) : ?>
		<div class="yith-plugin-fw__panel__option__label">
			<label id="<?php echo esc_attr( $label_id ); ?>" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo wp_kses_post( $field['title'] ); ?></label>
			<?php $panel->get_template( 'panel-option-label-tags.php', array( 'field' => $field ) ); ?>
		</div>
		<div class="yith-plugin-fw__panel__option__content">
			<?php yith_plugin_fw_get_field( $field, true, $show_container ); ?>
		</div>
		<?php if ( ! ! $field['desc'] ) : ?>
			<div class="yith-plugin-fw__panel__option__description">
				<?php echo wp_kses_post( $field['desc'] ); ?>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<?php yith_plugin_fw_get_field( $field, true ); ?>
	<?php endif; ?>
</div>
