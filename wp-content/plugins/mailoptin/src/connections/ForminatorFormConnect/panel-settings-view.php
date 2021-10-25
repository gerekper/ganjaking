<table class="sui-table">
    <thead>
    <tr>
        <th><?= esc_html__('Field', 'mailoptin') ?></th>
        <th><?= esc_html__('Forminator Field', 'mailoptin') ?></th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <?= esc_html__('Email Address', 'mailoptin') ?><span class="integrations-required-field">*</span>
            </td>
            <td>
                <div class="sui-form-field {{$error_css_class_EMAIL}}">
                    <select class="sui-select" name="fields_map[moEmail]">
                        <?php if ( empty( $email_fields ) ) { ?>
                            <option value=""><?php esc_html_e( 'None', 'mailoptin' ); ?></option>
                        <?php } else {
                            foreach ($email_fields as $key => $email_field) {
                        ?>
                            <option value="<?php echo esc_attr( $email_field['element_id'] ); ?>"
                                <?= selected( $current_data['fields_map']['moEmail'], $email_field['element_id'], false); ?>>
                                <?= esc_html( $email_field['field_label'] . ' | ' . $email_field['element_id'] ); ?>
                            </option>
                        <?php }
                        } ?>
                    </select>
                    {{$error_message_moEmail}}
                </div>
            </td>
        </tr>
        <?php
        foreach ($custom_fields as $key => $value) {
        ?>
            <tr>
                <td><?= esc_html__($value) ?></td>
                <td>
                    <div class="sui-form-field {{$error_css_class_<?php echo esc_attr( $key ); ?>}}">
                        <select class="sui-select" name="fields_map[<?php echo esc_attr($key); ?>]">
                            <option value=""><?php esc_html_e( 'None', 'mailoptin' ); ?></option>
                            <?php foreach ( $this->form_fields as $form_field ) { ?>
                                <option value="<?php echo esc_attr( $form_field['element_id'] ); ?>"
                                    <?= selected( $current_data['fields_map'][$key], $form_field['element_id'], false ); ?>>
                                    <?php echo esc_html( $form_field['field_label'] . ' | ' . $form_field['element_id'] ); ?>
                                </option>
                            <?php } ?>
                        </select>
                        {{$error_message_<?php echo esc_attr($key); ?>}}
                    </div>
                </td>
            </tr>
        <?php
        }

        ?>

    </tbody>
</table>