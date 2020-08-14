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

! defined( 'ABSPATH' ) && exit; // Exit if accessed directly

extract( $field );

$option_value = maybe_unserialize( get_option( $field['id'] ) );

?>
    <table id="<?php echo $field['id'] ?>" class="widefat wc_input_table sortable" cellspacing="0">
        <thead>
        <tr>
            <th class="sort">&nbsp;</th>
            <th><?php _e( 'Address 1', 'yith-woocommerce-anti-fraud' ); ?></th>
            <th><?php _e( 'Address 2', 'yith-woocommerce-anti-fraud' ); ?></th>
            <th><?php _e( 'City', 'yith-woocommerce-anti-fraud' ); ?></th>
            <th><?php _e( 'Postcode', 'yith-woocommerce-anti-fraud' ); ?></th>
            <th><?php _e( 'Country code', 'yith-woocommerce-anti-fraud' ); ?></th>
            <th><?php _e( 'State/Province code', 'yith-woocommerce-anti-fraud' ); ?></th>
        </tr>
        </thead>
        <tbody class="ui-sortable">
		<?php
		if ( is_array( $option_value ) ) {

			$i = - 1;
			foreach ( $option_value as $row ) :
				$i ++; ?>
                <tr class="ui-sortable-handle">
                    <td class="sort">
                    </td>
                    <td><input type="text" value="<?php echo $row['address1'] ?>" name="<?php echo $field['id'] ?>[<?php echo $i; ?>][address1]" id="<?php echo $field['id'] ?>[<?php echo $i; ?>][address1]" /></td>
                    <td><input type="text" value="<?php echo $row['address2'] ?>" name="<?php echo $field['id'] ?>[<?php echo $i; ?>][address2]" id="<?php echo $field['id'] ?>[<?php echo $i; ?>][address2]" /></td>
                    <td><input type="text" value="<?php echo $row['city'] ?>" name="<?php echo $field['id'] ?>[<?php echo $i; ?>][city]" id="<?php echo $field['id'] ?>[<?php echo $i; ?>][city]" /></td>
                    <td><input type="text" value="<?php echo $row['postcode'] ?>" name="<?php echo $field['id'] ?>[<?php echo $i; ?>][postcode]" id="<?php echo $field['id'] ?>[<?php echo $i; ?>][postcode]" /></td>
                    <td><input type="text" value="<?php echo $row['country'] ?>" name="<?php echo $field['id'] ?>[<?php echo $i; ?>][country]" id="<?php echo $field['id'] ?>[<?php echo $i; ?>][country]" maxlength="2" /></td>
                    <td><input type="text" value="<?php echo $row['state'] ?>" name="<?php echo $field['id'] ?>[<?php echo $i; ?>][state]" id="<?php echo $field['id'] ?>[<?php echo $i; ?>][state]" maxlength="2" /></td>
                </tr>
			<?php endforeach;

		}
		?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="7">
                <a href="#" class="add button yith-add-button">
					<?php _e( 'Add new address', 'yith-woocommerce-anti-fraud' ); ?></a>
                <a href="#" class="remove_rows button button-secondary yith-remove-button">
					<?php _e( 'Remove selected address(es)', 'yith-woocommerce-anti-fraud' ); ?>
                </a>
            </th>
        </tr>
        </tfoot>
    </table>
    <script type="text/javascript">
        jQuery(function () {
            jQuery('#<?php echo $field['id'] ?>').on('click', 'a.add', function () {

                var size = jQuery('#<?php echo $field['id'] ?> tbody tr').size();

                jQuery('<tr class="">\
						    <td class="sort"></td>\
							<td><input type="text" name="<?php echo $field['id'] ?>[' + size + '][address1]" id="<?php echo $field['id'] ?>[' + size + '][address1]" /></td>\
							<td><input type="text" name="<?php echo $field['id'] ?>[' + size + '][address2]" id="<?php echo $field['id'] ?>[' + size + '][address2]" /></td>\
							<td><input type="text" name="<?php echo $field['id'] ?>[' + size + '][city]" id="<?php echo $field['id'] ?>[' + size + '][city]"/></td>\
							<td><input type="text" name="<?php echo $field['id'] ?>[' + size + '][postcode]" id="<?php echo $field['id'] ?>[' + size + '][postcode]"/></td>\
							<td><input type="text" name="<?php echo $field['id'] ?>[' + size + '][country]" id="<?php echo $field['id'] ?>[' + size + '][country]" maxlength="2"/></td>\
							<td><input type="text" name="<?php echo $field['id'] ?>[' + size + '][state]" id="<?php echo $field['id'] ?>[' + size + '][state]" maxlength="2" /></td>\
						</tr>')
                    .appendTo('#<?php echo $field['id'] ?> tbody');

                return false;
            });
        });
    </script>
<?php

if ( isset( $field['desc-inline'] ) ) {
	echo "<span class='description inline'>" . $field['desc-inline'] . "</span>";
}