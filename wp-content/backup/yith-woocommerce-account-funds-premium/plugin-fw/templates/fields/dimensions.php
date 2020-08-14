<?php
/**
 * This file belongs to the YIT Plugin Framework.
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @var array $field
 */

! defined( 'ABSPATH' ) && exit; // Exit if accessed directly

$default_options = array(
	'dimensions'   => array(
		'top'    => _x( 'Top', 'Position in the "Dimensions" field', 'yith-plugin-fw' ),
		'right'  => _x( 'Right', 'Position in the "Dimensions" field', 'yith-plugin-fw' ),
		'bottom' => _x( 'Bottom', 'Position in the "Dimensions" field', 'yith-plugin-fw' ),
		'left'   => _x( 'Left', 'Position in the "Dimensions" field', 'yith-plugin-fw' ),
	),
	'units'        => array(
		'px'         => 'px',
		'percentage' => '%',
	),
	'allow_linked' => true,
	'min'          => false,
	'max'          => false,
);

$field = wp_parse_args( $field, $default_options );

/**
 * @var string   $id
 * @var string   $custom_attributes
 * @var array    $dimensions
 * @var array    $units
 * @var bool     $allow_linked
 * @var bool|int $min
 * @var bool|int $max
 */
extract( $field );

$class = isset( $class ) ? $class : '';
$class = 'yith-plugin-fw-dimensions ' . $class;

$value = ! empty( $value ) ? $value : array();

$unit_value        = isset( $value['unit'] ) ? $value['unit'] : current( array_keys( $units ) );
$dimensions_values = isset( $value['dimensions'] ) ? $value['dimensions'] : array();
$linked            = isset( $value['linked'] ) ? $value['linked'] : 'yes';

if ( $allow_linked && 'yes' === $linked ) {
	$class .= ' yith-plugin-fw-dimensions--linked-active';
}
?>
<div id="<?php echo $id ?>" class="<?php echo $class; ?>"
	<?php echo $custom_attributes ?>
	<?php echo isset( $data ) ? yith_plugin_fw_html_data_to_string( $data ) : ''; ?>
>
	<div class="yith-plugin-fw-dimensions__units">
		<input class='yith-plugin-fw-dimensions__unit__value' type="hidden" name="<?php echo $name ?>[unit]" value="<?php echo isset( $value['unit'] ) ? $value['unit'] : current( array_keys( $units ) ) ?>">
		<?php foreach ( $units as $key => $label ) : ?>
			<?php
			$key     = sanitize_title( $key );
			$classes = array(
				'yith-plugin-fw-dimensions__unit',
				"yith-plugin-fw-dimensions__unit--{$key}-unit",
			);
			if ( $unit_value === $key ) {
				$classes[] = 'yith-plugin-fw-dimensions__unit--selected';
			}
			$classes = implode( ' ', $classes );
			?>
			<span class="<?php echo $classes; ?>" data-value="<?php echo $key; ?>"><?php echo $label; ?></span>
		<?php endforeach ?>
	</div>

	<ul class="yith-plugin-fw-dimensions__dimensions">
		<?php foreach ( $dimensions as $key => $dimension ) : ?>
			<?php
			$d_id         = "{$id}-dimension-" . sanitize_title( $key );
			$d_name       = "{$name}[dimensions][" . sanitize_title( $key ) . "]";
			$d_value      = isset( $dimensions_values[ $key ] ) ? $dimensions_values[ $key ] : 0;
			$d_attributes = '';
			$d_label      = $dimension;
			$d_min        = $min;
			$d_max        = $max;

			if ( is_array( $dimension ) ) {
				$d_label = isset( $dimension['label'] ) ? $dimension['label'] : $key;
				if ( isset( $dimension['custom_attributes'] ) ) {
					$d_attributes .= $dimension['custom_attributes'];
				}
				$d_min = isset( $dimension['min'] ) ? $dimension['min'] : $d_min;
				$d_max = isset( $dimension['max'] ) ? $dimension['max'] : $d_max;
			}

			if ( $d_max !== false ) {
				$d_attributes = " max='{$d_max}' . $d_attributes";
			}

			if ( $d_min !== false ) {
				$d_attributes = " min='{$d_min}' " . $d_attributes;
			}

			?>
			<li class="yith-plugin-fw-dimensions__dimension yith-plugin-fw-dimensions__dimension--<?php echo sanitize_title( $key ); ?>">
				<input id="<?php echo $d_id; ?>" class="yith-plugin-fw-dimensions__dimension__number"
						type="number" name="<?php echo $d_name; ?>" value="<?php echo $d_value; ?>"
					<?php echo $d_attributes; ?>
				>
				<label for="<?php echo $d_id; ?>" class="yith-plugin-fw-dimensions__dimension__label"><?php echo $d_label; ?></label>
			</li>
		<?php endforeach ?>

		<?php if ( $allow_linked ): ?>
			<li class="yith-plugin-fw-dimensions__linked" title="<?php _ex( 'Link values together', 'Tooltip in the "Dimensions" field', 'yith-plugin-fw' ); ?>">
				<input class='yith-plugin-fw-dimensions__linked__value' type="hidden" name="<?php echo $name ?>[linked]" value="<?php echo $linked ?>">
				<span class="dashicons dashicons-admin-links"></span>
			</li>
		<?php endif; ?>
	</ul>
</div>
