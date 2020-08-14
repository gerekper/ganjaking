<?php
/**
 * MY ACCOUNT ENDPOINTS GROUP TEMPLATE
 *
 * @package YITH WooCommerce Customize My Account Page
 */
if ( ! defined( 'YITH_WCMAP' ) ) {
    exit;
} // Exit if accessed directly

?>

<li class="dd-item endpoint group" data-id="<?php echo esc_attr( $endpoint ); ?>" data-type="group">

    <label class="on-off-endpoint" for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_active">
        <input type="checkbox" class="hide-show-check" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[active]" id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_active" value="<?php echo esc_attr( $endpoint ); ?>" <?php checked( $options['active'] ) ?>/>
        <i class="fa fa-power-off"></i>
    </label>

    <div class="open-options field-type">
        <span><?php esc_html_e( 'Group', 'yith-woocommerce-customize-myaccount-page' ); ?></span>
        <i class="fa fa-chevron-down"></i>
    </div>

    <div class="dd-handle endpoint-content">

        <!-- Header -->
        <div class="endpoint-header">
            <?php echo esc_html( $options['label'] ); ?>
        </div>

        <div class="endpoint-options" style="display: none;">

            <div class="options-row">
                <span class="hide-show-trigger"><?php echo $options['active'] ? esc_html__( 'Hide', 'yith-woocommerce-customize-myaccount-page') : esc_html__( 'Show', 'yith-woocommerce-customize-myaccount-page' ); ?></span>
                <span class="sep">|</span>
                <span class="remove-trigger" data-endpoint="<?php echo esc_attr( $endpoint ); ?>"><?php esc_html_e( 'Remove', 'yith-woocommerce-customize-myaccount-page'); ?></span>
            </div>

            <table class="options-table form-table">
                <tbody>

                <tr>
                    <th>
                        <label for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_label"><?php esc_html_e( 'Group label', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
                        <img class="help_tip" data-tip="<?php esc_attr_e( 'Menu item for this endpoint in "My Account".',
                            'yith-woocommerce-customize-myaccount-page' ) ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16" width="16" />
                    </th>
                    <td>
                        <input type="text" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[label]" id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_label" value="<?php echo esc_attr( $options['label'] ); ?>">
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_icon"><?php esc_html_e( 'Group icon', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
                        <img class="help_tip" data-tip="<?php esc_attr_e( 'Group icon for "My Account" menu option', 'yith-woocommerce-customize-myaccount-page' ) ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16" width="16" />
                    </th>
                    <td>
                        <select name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[icon]" id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_icon" class="icon-select">
                            <option value=""><?php esc_html_e( 'No icon', 'yith-woocommerce-customize-myaccount-page' ) ?></option>
                            <?php foreach( $icon_list as $icon => $label ) : ?>
                                <option value="<?php echo esc_attr( $label ); ?>" <?php isset( $options['icon'] ) && selected( $options['icon'], $label ); ?>><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_class"><?php esc_html_e( 'Group class', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
                        <img class="help_tip" data-tip="<?php esc_attr_e( 'Add additional classes to group container.', 'yith-woocommerce-customize-myaccount-page' ) ?>" src="<?php echo esc_html( WC()->plugin_url() ); ?>/assets/images/help.png" height="16" width="16" />
                    </th>
                    <td>
                        <input type="text" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[class]" id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_class" value="<?php echo esc_attr( $options['class'] ); ?>">
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_usr_roles"><?php esc_html_e( 'User roles',
                                'yith-woocommerce-customize-myaccount-page' ); ?></label>
                        <img class="help_tip" data-tip="<?php esc_attr_e( 'Restrict endpoint visibility to the following user role(s).',
                            'yith-woocommerce-customize-myaccount-page' ) ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16" width="16" />
                    </th>
                    <td>
                        <select name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[usr_roles][]" id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_usr_roles" multiple="multiple">
                            <?php foreach( $usr_roles as $role => $role_name ) :
                                ! isset( $options['usr_roles'] ) && $options['usr_roles'] = array();
                                ?>
                                <option value="<?php echo esc_attr( $role ); ?>" <?php selected( in_array( $role, (array) $options['usr_roles'] ), true ); ?>><?php echo esc_html( $role_name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_open"><?php esc_html_e( 'Show open', 'yith-woocommerce-customize-myaccount-page' ); ?></label>
                        <img class="help_tip" data-tip="<?php esc_attr_e( 'Show the group open by default. (Please note: this option is valid only for "Sidebar" style)', 'yith-woocommerce-customize-myaccount-page' ) ?>"
                             src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" height="16" width="16" />
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo esc_attr( $id . '_' . $endpoint ); ?>[open]" id="<?php echo esc_attr( $id . '_' . $endpoint ); ?>_open" value="yes" <?php checked( $options['open'] ) ?>>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>

    </div>



    <?php if( ! empty( $options['children'] ) ) : ?>
        <ol class="dd-list endpoints">
        <?php foreach ( (array) $options['children'] as $key => $single_options ) {
            $args = array(
                'endpoint'      => $key,
                'options'       => $single_options,
                'id'            => $id,
                'icon_list'     => $icon_list,
                'usr_roles'     => $usr_roles
            );

            // get type
            $type = isset( $value['children'][ $key ] ) ? $value['children'][ $key ]['type'] : 'endpoint';
            call_user_func( "yith_wcmap_admin_print_{$type}_field", $args );
        } ?>
        </ol>
    <?php endif; ?>
</li>