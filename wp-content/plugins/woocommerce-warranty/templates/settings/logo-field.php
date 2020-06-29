<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
        <?php echo $tooltip_html; ?>
    </th>
    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
        <input
            name="<?php echo esc_attr( $value['id'] ); ?>"
            id="<?php echo esc_attr( $value['id'] ); ?>"
            type="<?php echo esc_attr( $type ); ?>"
            style="<?php echo esc_attr( $value['css'] ); ?>"
            value="<?php echo esc_attr( $option_value ); ?>"
            class="warranty-logo-field <?php echo esc_attr( $value['class'] ); ?>"
            placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
            <?php echo implode( ' ', $custom_attributes ); ?>
            />
        <input id="<?php echo esc_attr( $value['id'] .'_btn' ); ?>" class="button warranty-logo-upload" name="<?php echo esc_attr( $value['id'] .'_btn' ); ?>" type="button" value="Upload" />
        <?php echo $description; ?>
    </td>
</tr>