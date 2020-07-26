<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @var array $field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

extract( $field );
$value = maybe_unserialize( $value );
if ( ! empty( $fields ) && is_array( $fields ) ) { ?>
	<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo isset( $class ) ? $class : ''; ?> yith-inline-fields">
		<?php
		foreach ( $fields as $key => $field ) {
			$allowed_types  = array( 'select', 'select-buttons', 'number', 'text', 'slider', 'hidden', 'html' );
			$default_args   = array( 'type' => 'select' );

			// set default
			if ( ! isset( $field[ 'default' ] ) && isset( $field[ 'std' ] ) ) {
				$field['default'] = $field['std'];
			}
			$std = isset( $field['default'] ) ? $field['default'] : '';

			$field['value'] = isset( $value[ $key ] ) ? maybe_unserialize( $value[ $key ] ) : $std;
			$field['class'] = isset( $field['class'] ) ? $field['class'] : '';
			$field['id']    = $id . '_' . $key;
			$field['name']  = $name . '[' . $key . ']';

			if ( ! in_array( $field['type'], $allowed_types, true ) ) {
				continue;
			}

			if ( in_array( $field['type'], array( 'select', 'select-buttons' ), true ) ) {
				$field['class'] = 'wc-enhanced-select';
			}
			?>
			<?php if ( isset( $field['inline-label'] ) && '' !== $field['inline-label'] ) : ?>
				<div class="option-element">
					<span><?php echo $field['inline-label']; ?></span>
				</div>
			<?php endif; ?>
			<div class="option-element <?php echo $field['type']; ?> <?php echo $field['class']; ?>">
				<?php if ( isset( $field['label'] ) && '' !== $field['label'] ) : ?>
					<label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>
				<?php endif; ?>
				<?php yith_plugin_fw_get_field( $field, true ); ?>
			</div>
		<?php } ?>
	</div>
	<?php

}
