<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
        <?php echo $tip; ?>
    </th>
    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
        <select
            name="<?php echo esc_attr( $value['id'] ); ?>[]"
            id="<?php echo esc_attr( $value['id'] ); ?>"
            style="<?php echo esc_attr( $value['css'] ); ?>"
            class="multi-select2 <?php echo esc_attr( $value['class'] ); ?>"
            multiple
            <?php echo implode( ' ', $custom_attributes ); ?>
            >
            <?php
            foreach ( $value['options'] as $key => $val ) {
                ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php

                if ( is_array( $option_value ) )
                    selected( in_array( $key, $option_value ), true );
                else
                    selected( $option_value, $key );

                ?>><?php echo $val ?></option>
            <?php
            }
            ?>
        </select> <?php echo $description; ?>
    </td>
</tr>