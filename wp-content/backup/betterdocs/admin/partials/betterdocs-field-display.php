<?php 
$opt_alert_class = '';
if( isset( $field['disable'] ) && $field['disable'] === true ) {
    $opt_alert_class = 'betterdocs-opt-alert';
}
?>
<tr data-id="<?php echo $key; ?>" id="<?php echo esc_attr( $id ); ?>" class="betterdocs-field <?php echo $row_class.' type-'.$field['type']; ?>">
    <?php if( $field['type'] == 'title' && !empty( $field['label'] )) : ?>
        <th colspan="2" class="betterdocs-control betterdocs-title"><h3><?php esc_html_e( $field['label'], 'betterdocs' ); ?></h3></th>
    <?php elseif( $field['type'] == 'card' && !empty( $field['label'] )) : ?>
        <td class="betterdocs-control betterdocs-card"><a href="<?php echo esc_url($field['url']) ?>"><div class="betterdocs-card-content"><img src="<?php echo BETTERDOCS_ADMIN_URL; ?>/assets/img/betterdocs-customize.svg" alt="betterdocs-documentation"><p><?php _e('Design your Documentation and Article page live with Customizer.', 'betterdocs'); ?></p><p class="betterdocs-customize-button"><?php esc_html_e( $field['label'], 'betterdocs' ); ?></p></div></a></td>
    <?php else: ?>
        <?php if( empty( $field['label'] ) ) : ?>
            <td class="betterdocs-control" colspan="2">
        <?php else : ?>
        <th class="betterdocs-label">
            <label for="<?php echo esc_attr( $name ); ?>"><?php esc_html_e( $field['label'], 'betterdocs' ); ?></label>
        </th>
        <td class="betterdocs-control">
        <?php 
            endif; 
            do_action( 'betterdocs_field_before_wrapper', $name, $value, $field, $post_id );
        ?>
            <div class="betterdocs-control-wrapper <?php echo $opt_alert_class; ?>">
            <?php 
                if( $file_name ) {
                    $file = BETTERDOCS_ADMIN_DIR_PATH . 'includes/fields/betterdocs-'. $file_name .'.php';
                    if( file_exists( $file ) ) {
                        include $file;
                    }
                }

                if( isset( $field['view'] ) && ! empty( $field['view'] ) ) {
                    $args = array();
                    if( isset( $field['default'] ) ) {
                        $args['default'] = $field['default'];
                    }
                    if( isset( $field['options'] ) ) {
                        $args['options'] = $field['options'];
                    }
                    if( ! empty( $value ) ) {
                        $args['value'] = $value;
                    }
                    call_user_func_array( $field['view'], $args );
                }

                if( isset( $field['description'] ) && ! empty( $field['description'] ) ) : 
                    ?>
                        <p class="betterdocs-field-description"><?php _e( $field['description'], 'betterdocs' ); ?></p>
                    <?php
                endif;
                if( isset( $field['help'] ) && ! empty( $field['help'] ) ) : 
                    ?>
                        <p class="betterdocs-field-help"><?php _e( $field['help'], 'betterdocs' ); ?></p>
                    <?php
                endif;
            ?>
            </div>
            <?php do_action( 'betterdocs_field_after_wrapper', $name, $value, $field, $post_id ); ?>
        </td>
    <?php endif; ?>
</tr>
