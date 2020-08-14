<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

! defined( 'ABSPATH' ) && exit; // Exit if accessed directly

extract( $field );

$op_value = maybe_unserialize( $value );

?>

<table class="widefat wp-list-table yith-check-matrix-table" cellspacing="0" id="<?php echo esc_attr( $id ); ?>">
	<thead>
	<tr>
		<th><?php echo $field['main_column']['label']; ?></th>
		<?php foreach ( $field['columns'] as $column ) : ?>
			<th class="checkbox-column">

				<a id="<?php echo $column['id']; ?>-lnk" href="#" class="tips" data-tip="<?php echo $column['tip']; ?>">
					<?php echo $column['label']; ?>
				</a>

			</th>
		<?php endforeach; ?>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $field['main_column']['rows'] as $key => $label ) : ?>
		<tr>
			<td class="main-column">
				<?php echo $label; ?>
			</td>
			<?php foreach ( $field['columns'] as $column ) : ?>
				<td class="checkbox-column">
					<input
						name="<?php echo $field['id']; ?>[<?php echo $key; ?>][<?php echo $column['id']; ?>]"
						id="<?php echo $field['id']; ?>[<?php echo $key; ?>][<?php echo $column['id']; ?>]"
						type="checkbox"
						class="<?php echo $column['id']; ?>-cb"
						value="1"
						<?php checked( isset( $op_value[ $key ][ $column['id'] ] ) ? $op_value[ $key ][ $column['id'] ] : '0', '1' ); ?>
					/>
				</td>
			<?php endforeach; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>

</table>


