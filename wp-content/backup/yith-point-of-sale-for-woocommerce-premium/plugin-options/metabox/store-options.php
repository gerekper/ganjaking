<?php
// Exit if accessed directly
!defined( 'YITH_POS' ) && exit();

$is_published     = isset( $_GET[ 'post' ] ) && 'publish' === get_post_status( $_GET[ 'post' ] );
$required_message = yith_pos_get_required_field_message();

$save_button = '<div class="yith-pos-edit-actions"><input type="submit" class="yith-pos-edit-save button-primary button-xl" value="' . __( 'Save', 'yith-point-of-sale-for-woocommerce' ) . '"></div>';

$args = array(
    'label'    => __( 'Edit', 'yith-point-of-sale-for-woocommerce' ),
    'pages'    => YITH_POS_Post_Types::$store,
    'context'  => 'normal',
    'priority' => 'high',
    'class'    => yith_set_wrapper_class(),
    'tabs'     => array(
        'edit_store_details' => array(
            'label'  => __( 'Store Info', 'yith-point-of-sale-for-woocommerce' ) . yith_pos_svg( 'store', false ),
            'fields' => array(
                'wizard_current_page'     => array(
                    'type' => 'hidden',
                    'std'  => 1
                ),
                'general_info_title'      => array(
                    'label' => '',
                    'type'  => 'html',
                    'html'  => YITH_POS_Admin()->store_post_type_admin->get_section_title( 1, $is_published ),
                ),
                'store_info_notices'                => array(
                    'type'  => 'html',
                    'html' => '<div class="yith-pos-store-notices"></div>',
                ),
                'info_sep'                => array(
                    'type'  => 'sep',
                    'label' => '',
                ),
                'general_info_title_edit' => array(
                    'desc' => __( 'General Info', 'yith-point-of-sale-for-woocommerce' ),
                    'type' => 'title',
                ),
                'name'                    => array(
                    'label'             => __( 'Store Name', 'yith-point-of-sale-for-woocommerce' ),
                    'type'              => 'text',
                    'required'          => true,
                    'desc'              => __( 'Enter a name to identify this store.', 'yith-point-of-sale-for-woocommerce' ) . $required_message,
                    'custom_attributes' => ' required validate="true" data-message="' . __( 'The store name is required', 'yith-point-of-sale-for-woocommerce' ) . '"',
                    'yith-sanitize-callback' => 'sanitize_text_field'
                ),
                'vat_number'              => array(
                    'label' => __( 'VAT Number', 'yith-point-of-sale-for-woocommerce' ),
                    'type'  => 'text',
                    'desc'  => __( 'Enter a VAT number for this store.', 'yith-point-of-sale-for-woocommerce' ),
                ),
                'address_title'           => array(
                    'desc' => __( 'Address', 'yith-point-of-sale-for-woocommerce' ),
                    'type' => 'title',
                ),
                'address'                 => array(
                    'label' => __( 'Full Address', 'yith-point-of-sale-for-woocommerce' ),
                    'desc'  => __( 'Enter a street address for this store location.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'  => 'textarea',
                ),
                'city'                    => array(
                    'label' => __( 'City', 'yith-point-of-sale-for-woocommerce' ),
                    'desc'  => __( 'Enter the city in which this store is located.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'  => 'text',
                ),
                'country_state'                   => array(
                    'label' => __( 'Country / State', 'yith-point-of-sale-for-woocommerce' ),
                    'desc'  => __( 'Enter the Country / State / Province in which this store is located.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'  => 'country-select',
                    'std'   => get_option( 'woocommerce_default_country' )
                ),
                'postcode'                => array(
                    'label' => __( 'Postcode / ZIP', 'yith-point-of-sale-for-woocommerce' ),
                    'desc'  => __( 'Enter the postal code of this address.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'  => 'text',
                ),
                'contact_title'           => array(
                    'desc' => __( 'Contact Info', 'yith-point-of-sale-for-woocommerce' ),
                    'type' => 'title',
                ),
                'phone'                   => array(
                    'label'             => __( 'Phone', 'yith-point-of-sale-for-woocommerce' ),
                    'desc'              => __( 'Enter the phone number of this store.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'              => 'text',
                    'custom_attributes' => 'placeholder="999.999.999"',
                ),
                'fax'                     => array(
                    'label'             => __( 'Fax', 'yith-point-of-sale-for-woocommerce' ),
                    'desc'              => __( 'Enter the fax number of this store.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'              => 'text',
                    'custom_attributes' => 'placeholder="999.999.999"',
                ),
                'email'                   => array(
                    'label'             => __( 'E-mail', 'yith-point-of-sale-for-woocommerce' ),
                    'desc'              => __( 'Enter the e-mail address of this store.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'              => 'text',
                    'custom_attributes' => 'placeholder="user@example.com" validate="true" data-message="' . __( 'Please provide a valid email address', 'yith-point-of-sale-for-woocommerce' ) . '"',
                ),
                'website'                 => array(
                    'label'             => __( 'Website', 'yith-point-of-sale-for-woocommerce' ),
                    'desc'              => __( 'Enter the website url of this store.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'              => 'text',
                    'custom_attributes' => 'placeholder="yourwebsite.com" validate="true" data-message="' . __( 'Please provide a valid website url', 'yith-point-of-sale-for-woocommerce' ) . '"',
                ),
                'title'                   => array(
                    'desc' => __( 'Social', 'yith-point-of-sale-for-woocommerce' ),
                    'type' => 'title',
                ),
                'facebook'                => array(
                    'label'             => __( 'Facebook', 'yith-point-of-sale-for-woocommerce' ),
                    'desc'              => __( 'Enter the Facebook url of this store.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'              => 'text',
                    'custom_attributes' => 'placeholder="facebook.com/your_page"',
                ),
                'twitter'                 => array(
                    'label'             => __( 'Twitter', 'yith-point-of-sale-for-woocommerce' ),
                    'desc'              => __( 'Enter the Twitter ID or username of this store.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'              => 'text',
                    'custom_attributes' => 'placeholder="@username"',
                ),
                'instagram'               => array(
                    'label'             => __( 'Instagram', 'yith-point-of-sale-for-woocommerce' ),
                    'desc'              => __( 'Enter the Instagram ID or username of this store.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'              => 'text',
                    'custom_attributes' => 'placeholder="instagram.com/username"',
                ),
                'youtube'                 => array(
                    'label'             => __( 'Youtube', 'yith-point-of-sale-for-woocommerce' ),
                    'desc'              => __( 'Enter the Youtube username for this store.', 'yith-point-of-sale-for-woocommerce' ),
                    'type'              => 'text',
                    'custom_attributes' => 'placeholder="youtube.com/channel"',
                ),
                'store_edit_details_save' => array(
                    'label' => '',
                    'type'  => 'html',
                    'html'  => $save_button,
                ),
            ),
        ),

        'edit_store_employees' => array(
            'label'  => __( 'Employees', 'yith-point-of-sale-for-woocommerce' ) . yith_pos_svg( 'employees', false ),
            'fields' => array(
                'general_info_title' => array(
                    'label' => '',
                    'type'  => 'html',
                    'html'  => YITH_POS_Admin()->store_post_type_admin->get_section_title( 2, $is_published ),
                ),
                'store_employees_notices'                => array(
                    'type'  => 'html',
                    'html' => '<div class="yith-pos-store-notices"></div>',
                ),
                'info_sep'           => array(
                    'type'  => 'sep',
                    'label' => '',
                ),
                'manager_title'      => array(
                    'desc' => __( 'Managers ', 'yith-point-of-sale-for-woocommerce' ),
                    'type' => 'title',
                ),
                'manager_title_des'  => array(
                    'type' => 'html',
                    'html' => '<p class="section-description">' . __( 'A <strong>manager</strong> has full access to all registers and functionalities, assigned to this store.<br/>Please note, that <strong>you have to assign at least one manager</strong> before proceeding to the next step.', 'yith-point-of-sale-for-woocommerce' ) . '</p>',
                ),

                'managers' => array(
                    'type'     => 'ajax-customers',
                    'label'    => __( 'Choose managers from your "Users" list', 'yith-point-of-sale-for-woocommerce' ),
                    'required' => true,
                    'class'    => 'yith-customer-search store-manager',
                    'multiple' => true,
                    'desc'     => '<p style="margin-bottom: 10px"><span class="yith-plugin-fw-deselect-all button button-secondary" data-select-id="_managers">' . __( 'Remove All', 'yith-point-of-sale-for-woocommerce' ) . '</span></p>' . __( 'Click and type at least 1 letter to search and add a user as a manager.', 'yith-point-of-sale-for-woocommerce' ) . '<br/>' . __( '<strong>Note:</strong> a Contributor and a Subscriber can not be added as Manager.', 'yith-point-of-sale-for-woocommerce' ),
                    'data'     => array(
                        'action'      => 'woocommerce_json_search_customers',
                        'security'    => wp_create_nonce( 'search-customers' ),
                        'placeholder' => __( 'Search Managers', 'yith-point-of-sale-for-woocommerce' ),
                        'message'     => __( 'You have to set at least one manager to proceed to next step', 'yith-point-of-sale-for-woocommerce' ),
                    ),

                ),

                'add_manager' => array(
                    'label' => ' ',
                    'type'  => 'html',
                    'html'  => '<p>' . __( 'Do you need to setup a new manager profile?', 'yith-point-of-sale-for-woocommerce' ) . '</p>' .
                               yith_pos_register_user_form( array(
                                                                'button_text'         => __( 'Create new manager', 'yith-point-of-sale-for-woocommerce' ),
                                                                'button_close_text'   => __( 'Close new manager creation', 'yith-point-of-sale-for-woocommerce' ),
                                                                'title'               => __( 'Create new store manager', 'yith-point-of-sale-for-woocommerce' ),
                                                                'save_text'           => __( 'Create manager', 'yith-point-of-sale-for-woocommerce' ),
                                                                'user_type'           => 'manager',
                                                                'select2_to_populate' => '#_managers'
                                                            ), false ),
                ),

                'cashier_title'             => array(
                    'desc' => __( 'Cashiers', 'yith-point-of-sale-for-woocommerce' ),
                    'type' => 'title',
                ),
                'cashier_title_desc'        => array(
                    'type' => 'html',
                    'html' => __( '<p class="section-description">A <strong>Cashier</strong> might have limited access to the store Registers (e.g. being allowed to use only Register 1, but not Register 2). You can set these limits when adding a register.</p>', 'yith-point-of-sale-for-woocommerce' ),
                ),
                'cashiers'                  => array(
                    'type'     => 'ajax-customers',
                    'label'    => __( 'Choose Cashiers from your "Users" list', 'yith-point-of-sale-for-woocommerce' ),
                    'class'    => 'yith-customer-search store-cashier',
                    'multiple' => true,
                    'desc'     => '<p style="margin-bottom: 10px"><span class="yith-plugin-fw-deselect-all button button-secondary" data-select-id="_cashiers">' . __( 'Remove All', 'yith-point-of-sale-for-woocommerce' ) . '</span></p>' . __( 'Click and type at least 3 letters to search and add a user as a manager.', 'yith-point-of-sale-for-woocommerce' ) . '<br/>' . __( '<strong>Note:</strong> a Contributor and a Subscriber can not be added as cashiers.', 'yith-point-of-sale-for-woocommerce' ),
                    'data'     => array(
                        'action'      => 'woocommerce_json_search_customers',
                        'security'    => wp_create_nonce( 'search-customers' ),
                        'placeholder' => __( 'Search Cashiers', 'yith-point-of-sale-for-woocommerce' ),
                        'message'     => __( 'You have to set at least one Cashier to proceed to the next step', 'yith-point-of-sale-for-woocommerce' ),
                    ),

                ),
                'add_cashier'               => array(
                    'label' => ' ',
                    'type'  => 'html',
                    'html'  => '<p>' . __( 'Do you need to set up a new Cashier profile?', 'yith-point-of-sale-for-woocommerce' ) . '</p>' .
                               yith_pos_register_user_form( array(
                                                                'button_text'         => __( 'Create new Cashier', 'yith-point-of-sale-for-woocommerce' ),
                                                                'button_close_text'   => __( 'Create new store Cashier', 'yith-point-of-sale-for-woocommerce' ),
                                                                'title'               => __( 'Create new store Cashier', 'yith-point-of-sale-for-woocommerce' ),
                                                                'save_text'           => __( 'Create cashier', 'yith-point-of-sale-for-woocommerce' ),
                                                                'user_type'           => 'cashier',
                                                                'select2_to_populate' => '#_cashiers'
                                                            ), false )
                ),
                'store_edit_employees_save' => array(
                    'label' => '',
                    'type'  => 'html',
                    'html'  => $save_button,
                ),
            ),
        ),
        'edit_store_registers' => array(
            'label'  => __( 'Registers', 'yith-point-of-sale-for-woocommerce' ) . yith_pos_svg( 'register', false ),
            'fields' => array(
                'registers_title_edit' => array(
                    'label' => '',
                    'type'  => 'html',
                    'html'  => YITH_POS_Admin()->store_post_type_admin->get_section_title( 3, $is_published ),
                ),
                'store_registers_notices'                => array(
                    'type'  => 'html',
                    'html' => '<div class="yith-pos-store-notices"></div>',
                ),
                'registers_sep_edit'   => array(
                    'type'  => 'sep',
                    'label' => '',
                ),

                'registers_create_edit' => array(
                    'type'             => 'custom',
                    'yith-display-row' => false,
                    'action'           => 'yith_pos_store_metabox_registers_list'
                )
            ),
        ),

    ),
);

return $args;