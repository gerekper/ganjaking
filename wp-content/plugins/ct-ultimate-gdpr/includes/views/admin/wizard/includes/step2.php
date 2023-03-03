<?php
/** @var string $cookie_content */
/** @var string $cookie_popup_label_accept */
/** @var string $cookie_popup_label_read_more */
/** @var string $cookie_popup_label_settings */
/** @var string $cookie_content_language */
/** @var string $cookie_group_popup_header_content */

/** @var string $cookie_group_popup_label_will */
/** @var string $cookie_group_popup_label_wont */
/** @var string $cookie_group_popup_label_block_all */
/** @var string $cookie_group_popup_label_essentials */
/** @var string $cookie_group_popup_label_functionality */
/** @var string $cookie_group_popup_label_analytics */
/** @var string $cookie_group_popup_label_advertising */

/** @var string $cookie_group_popup_features_available_group_2 */
/** @var string $cookie_group_popup_features_nonavailable_group_2 */
/** @var string $cookie_group_popup_features_available_group_3 */
/** @var string $cookie_group_popup_features_nonavailable_group_3 */
/** @var string $cookie_group_popup_features_available_group_4 */
/** @var string $cookie_group_popup_features_nonavailable_group_4 */
/** @var string $cookie_group_popup_features_available_group_5 */
/** @var string $cookie_group_popup_features_nonavailable_group_5 */

/** @var string $cookie_box_style */
/** @var string $cookie_box_shape */
/** @var string $cookie_button_settings */
/** @var string $cookie_button_shape */
/** @var string $cookie_button_size */

?>

<h1><?php echo esc_html__( 'Cookie popup display', 'ct-ultimate-gdpr' ); ?></h1>

<form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="js-form-wizard">
    <input type="hidden" name="action" value="ct_ultimate_gdpr_wizard_save">
    <?php wp_nonce_field('ct_ultimate_gdpr_wizard_save', 'ct_ultimate_gdpr_wizard'); ?>
    <input type="hidden" name="redirectToUrl" value="<?php echo ct_ultimate_gdpr_wizard_step_url('step3'); ?>">

    <div class="row g-5">
        <div class="col-md-6">
        <legend><?php echo esc_html__( 'Cookies', 'ct-ultimate-gdpr' ); ?></legend>
        <p><?php echo esc_html__('You can import general cookie information notice that will be displayed inside your cookie popup. You can import the text in one of the following languages: Cestina, Deutsch, English, Espanol, Francais, Hrvatskim Magyar, Norwegian, Italiano, Nederlands, Polski, Portugues, Romana, Pyccknn, Slovencia, Danish, Bulgaria, Swedish. 
You can also type your custom message in the text area below, or edit imported content. ','ct-ultimate-gdpr'); ?></p>
<p><?php echo esc_html__('Provide default content for the selected language (remember to save changes)', 'ct-ultimate-gdpr' ); ?></p>
            <div class="mb-3 row">
                <div class="col-md-8">
                    <?php echo $cookie_content_language; ?>
                </div>
                <div class="col-md-4">
                    <label for="" class="sr-only"><?php echo esc_html__( 'Load', 'ct-ultimate-gdpr' ); ?></label>
                    <input type="text" readonly="" class="btn btn-load" name="ct-ultimate-gdpr-cookie-content-language" value="<?php echo esc_html__( 'Load', 'ct-ultimate-gdpr' ); ?>">
                </div>
            
                
            </div>
      
            <div class="mb-3">
                <label for="cookie_content"
                       class="form-label"><?php echo esc_html__( 'Cookie popup content', 'ct-ultimate-gdpr' ); ?></label>
                <?php echo $cookie_content; ?>
            </div>
            <div class="mb-3">
                <label for="cookie_group_popup_header_content"
                       class="form-label"><?php echo esc_html__( 'Advanced cookie groups popup header content', 'ct-ultimate-gdpr' ); ?></label>
                <?php echo $cookie_group_popup_header_content; ?>
            </div>
            


            <fieldset class="mt-3">
                <legend><?php echo esc_html__( 'Labels', 'ct-ultimate-gdpr' ); ?></legend>
                <div class="row">
                    <div class="col-md-6">
                    <div class="mb-3">
                    <label for="cookie_popup_label_accept"
                        class="form-label"><?php echo esc_html__( "Cookie popup 'accept' button", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $cookie_popup_label_accept; ?>
                    </div>
                    </div>
                    <div class="col-md-6">
                    <div class="mb-3">
                    <label for="cookie_popup_label_read_more"
                        class="form-label"><?php echo esc_html__( "Cookie popup 'read more' button", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $cookie_popup_label_read_more; ?>
                </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                    <div class="mb-3">
                    <label for="cookie_popup_label_settings"
                        class="form-label"><?php echo esc_html__( "Cookie popup 'change settings' button", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $cookie_popup_label_settings; ?>
                </div>
                    </div>
                    <div class="col-md-6">
                    <div class="mb-3">
                    <label for="cookie_group_popup_label_will"
                           class="form-label"><?php echo wp_kses_post( __( "This website will label", 'ct-ultimate-gdpr' ) ); ?></label>
                    <?php echo $cookie_group_popup_label_will; ?>
                </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                    <div class="mb-3">
                    <label for="cookie_group_popup_label_wont"
                           class="form-label"><?php echo wp_kses_post( __( "This website won't label", 'ct-ultimate-gdpr' ) ); ?></label>
                    <?php echo $cookie_group_popup_label_wont; ?>
                </div>
                    </div>
                    <div class="col-md-6">
                    <div class="mb-3">
                    <label for="cookie_group_popup_label_block_all"
                           class="form-label"><?php echo wp_kses_post( __( "Block all label", 'ct-ultimate-gdpr' ) ); ?></label>
                    <?php echo $cookie_group_popup_label_block_all; ?>
                </div>
                    </div>
                </div>
                

                <div class="row">
                    <div class="col-md-6">
                    <div class="mb-3">
                    <label for="cookie_group_popup_label_essentials"
                           class="form-label"><?php echo wp_kses_post( __( "Essentials label", 'ct-ultimate-gdpr' ) ); ?></label>
                    <?php echo $cookie_group_popup_label_essentials; ?>
                </div>
                    </div>
                    <div class="col-md-6">
                    <div class="mb-3">
                    <label for="cookie_group_popup_label_functionality"
                           class="form-label"><?php echo wp_kses_post( __( "Functionality label", 'ct-ultimate-gdpr' ) ); ?></label>
                    <?php echo $cookie_group_popup_label_functionality; ?>
                </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                    <div class="mb-3">
                    <label for="cookie_group_popup_label_analytics"
                           class="form-label"><?php echo wp_kses_post( __( "Analytics label", 'ct-ultimate-gdpr' ) ); ?></label>
                    <?php echo $cookie_group_popup_label_analytics; ?>
                </div>
                    </div>
                    <div class="col-md-6">
                    <div class="mb-3">
                    <label for="cookie_group_popup_label_advertising"
                           class="form-label"><?php echo wp_kses_post( __( "Advertising label", 'ct-ultimate-gdpr' ) ); ?></label>
                    <?php echo $cookie_group_popup_label_advertising; ?>
                </div>
                    </div>
                </div>  

            </fieldset>

            


        </div>
        <div class="col-md-6">
            <fieldset class="mb-3">
                <legend><?php echo esc_html__( 'Cookie notice box', 'ct-ultimate-gdpr' ); ?></legend>

                <div class="mb-3 row">
                    <div class="col-md-8">
                    <label for="cookie_box_style"
                           class="form-label col-form-label"><?php echo esc_html__( 'Box style', 'ct-ultimate-gdpr' ); ?></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $cookie_box_style; ?>
                    </div>
                    
                   
                </div>
                <div class="mb-3 row">
                    <div class="col-md-8">
                    <label for="cookie_box_shape"
                           class="form-label col-form-label"><?php echo esc_html__( 'Box shape', 'ct-ultimate-gdpr' ); ?></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $cookie_box_shape; ?>
                    </div>
                    
                    
                </div>
            </fieldset>

            <fieldset class="mb-3">
                <legend><?php echo esc_html__('Buttons styles', 'ct-ultimate-gdpr'); ?></legend>

                <div class="mb-3 row">
                    <div class="col-md-8">
                    <label for="cookie_button_settings"
                           class="form-label col-form-label"><?php echo esc_html__( 'Button settings by', 'ct-ultimate-gdpr' ); ?></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $cookie_button_settings; ?>
                    </div>
                    
                    
                </div>
                <div class="mb-3 row">
                    <div class="col-md-8">
                    <label for="cookie_button_shape"
                           class="form-label col-form-label"><?php echo esc_html__( 'Button shape', 'ct-ultimate-gdpr' ); ?></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $cookie_button_shape; ?>
                    </div>
                    
                    
                </div>
                <div class="mb-3 row">
                    <div class="col-md-8">
                    <label for="cookie_button_size"
                           class="form-label col-form-label"><?php echo esc_html__( 'Button size', 'ct-ultimate-gdpr' ); ?></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $cookie_button_size; ?>
                    </div>
                    
                    
                </div>

            </fieldset>
            <fieldset class="mt-3">
                <legend><?php echo esc_html__( 'Lists of features', 'ct-ultimate-gdpr' ); ?></legend>

                <div class="mb-3">
                    <label for="cookie_group_popup_features_available_group_2"
                           class="form-label"><?php echo esc_html__( 'List of features available for Essential level (semicolon separated)', 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $cookie_group_popup_features_available_group_2; ?>
                </div>
                <div class="mb-3">
                    <label for="cookie_group_popup_features_nonavailable_group_2"
                           class="form-label"><?php esc_html__( 'List of features not available for Essential level (semicolon separated)', 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $cookie_group_popup_features_nonavailable_group_2; ?>
                </div>
                <div class="mb-3">
                    <label for="cookie_group_popup_features_available_group_3"
                           class="form-label"><?php esc_html__( 'List of features available for Functionality level (semicolon separated)', 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $cookie_group_popup_features_available_group_3; ?>
                </div>
                <div class="mb-3">
                    <label for="cookie_group_popup_features_nonavailable_group_3"
                           class="form-label"><?php echo esc_html__( 'List of features not available for Functionality level (semicolon separated)', 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $cookie_group_popup_features_nonavailable_group_3; ?>
                </div>
                <div class="mb-3">
                    <label for="cookie_group_popup_features_available_group_4"
                           class="form-label"><?php echo esc_html__( 'List of features available for Analytics level (semicolon separated)', 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $cookie_group_popup_features_available_group_4; ?>
                </div>
                <div class="mb-3">
                    <label for="cookie_group_popup_features_nonavailable_group_4"
                           class="form-label"><?php echo esc_html__( 'List of features not available for Analytics level (semicolon separated)', 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $cookie_group_popup_features_nonavailable_group_4; ?>
                </div>
                <div class="mb-3">
                    <label for="cookie_group_popup_features_available_group_5"
                           class="form-label"><?php echo esc_html__( 'List of features available for Advertising level (semicolon separated)', 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $cookie_group_popup_features_available_group_5; ?>
                </div>
                <div class="mb-3">
                    <label for="cookie_group_popup_features_nonavailable_group_5"
                           class="form-label"><?php echo esc_html__( 'List of features not available for Advertising level (semicolon separated)', 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $cookie_group_popup_features_nonavailable_group_5; ?>
                </div>

            </fieldset>
            <fieldset class="mb-3">
                <legend><?php echo esc_html__('Preview', 'ct-ultimate-gdpr'); ?></legend>

                <div class="mb-3">
                    <button href="<?php echo ct_ultimate_gdpr_wizard_step_url('step2b'); ?>" class="btn btn-primary js-save-and-go"><?php _e( "Show result", 'ct-ultimate-gdpr'); ?></button>
                </div>

            </fieldset>

        </div>
    </div>
    <!-- / row -->

    <?php ct_ultimate_gdpr_wizard_submit(esc_html__('Submit', 'ct-ultimate-gdpr')); ?>

</form>
