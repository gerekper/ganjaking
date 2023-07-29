<?php
/** @var array $choose_plugin */
/** @var string $services_addthis_block_cookies */
/** @var string $services_arforms_consent_field */
/** @var string $services_bbpress_consent_field */
/** @var string $services_buddypress_consent_field */
/** @var string $services_caldera_forms_consent_field */
/** @var string $services_cf7db_hide_from_forgetme_form */
/** @var string $services_wpforms_lite_consent_field */
/** @var string $services_contact_form_7_consent_field */
/** @var string $services_Yikes_Inc_Easy_Mailchimp_Extender_consent_field */
/** @var string $services_ct_waitlist_consent_field */
/** @var string $services_eform_consent_field */
/** @var string $services_events_manager_consent_field */
/** @var string $services_flamingo_hide_from_forgetme_form */
/** @var string $services_formcraft_form_premium_consent_field */
/** @var string $services_formcraft_form_builder_consent_field */
/** @var string $services_formidable_forms_consent_field */
/** @var string $services_gravity_forms_consent_field */
/** @var string $services_klaviyo_consent_field */
/** @var string $services_mailchimp_consent_field */
/** @var string $services_mailerlite_consent_field */
/** @var string $services_mailster_consent_field */
/** @var string $services_metform */
/** @var string $services_metorik_helper_consent_field */
/** @var string $services_newsletter_consent_field */
/** @var string $services_ninja_forms_consent_field */
/** @var string $services_quform_consent_field */
/** @var string $services_ultimate_member_consent_field */
/** @var string $services_woocommerce_consent_field */
/** @var string $services_woocommerce_edit_account_consent_field */
/** @var string $services_woocommerce_checkout_consent_field */
/** @var string $services_wordfence_block_cookies */
/** @var string $services_wp_comments_consent_field */
/** @var string $services_wp_foro_consent_field */
/** @var string $services_wp_job_manager_hide_from_forgetme_form */
/** @var string $services_wp_posts_hide_from_forgetme_form */
/** @var string $services_wp_comments_network_signup_consent_field */
/** @var string $services_wp_comments_register_consent_field */
/** @var string $services_wp_comments_lost_password_consent_field */
/** @var string $services_yith_woocommerce_wishlist_hide_from_forgetme_form */
/** @var string $services_youtube_remove_iframe */

// echo "<pre>";
// var_dump($choose_plugin);
// echo "</pre>";
?>
<h1><?php echo esc_html__('3rd party services and plugins', 'ct-ultimate-gdpr'); ?></h1>

        <form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="js-form-wizard">
            <input type="hidden" name="action" value="ct_ultimate_gdpr_wizard_save">
            <?php wp_nonce_field('ct_ultimate_gdpr_wizard_save', 'ct_ultimate_gdpr_wizard'); ?>
            <input type="hidden" name="redirectToUrl" value="<?php echo ct_ultimate_gdpr_wizard_step_url('step8'); ?>">

            <div class="row g-4">
                <div class="col-lg-6">
                    <p class="mb-5"><?php echo esc_html__("If you use 3rd party software to create forms on your website, please note that you should include a dedicated checkbox to collect visitor's consent to process his data, you can select the forms to which we should add this consent checkbox automatically.", 'ct-ultimate-gdpr'); ?></p>
                    <div class="mb-4">
                        <label for="cookie_box_style"
                            class="form-label"><?php echo esc_html__( 'Choose plugin', 'ct-ultimate-gdpr' ); ?></label>
                        <select class="form-control js-select-service">
                        <?php
                        foreach($choose_plugin as $option){
                        echo '<option value="'. sanitize_html_class($option) .'">'. $option .'</option>';
                        }
                        ?>
                        </select>
                    </div>

                    <div id="<?php echo sanitize_html_class($choose_plugin[1]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_addthis_block_cookies" class="form-check-label"><?php echo esc_html__( "[Addthis] xxx Block Addthis cookies when a user doesn't accept Functionality cookies", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_addthis_block_cookies; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[2]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_arforms_consent_field" class="form-check-label"><?php echo esc_html__( "[ARForms] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_arforms_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[3]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_bbpress_consent_field" class="form-check-label"><?php echo esc_html__( "[bbPress] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_bbpress_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[4]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_buddypress_consent_field" class="form-check-label"><?php echo esc_html__( "[BuddyPress] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_buddypress_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="<?php echo sanitize_html_class($choose_plugin[5]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_caldera_forms_consent_field" class="form-check-label"><?php echo esc_html__( "[Caldera Forms] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_caldera_forms_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[6]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_cf7db_hide_from_forgetme_form" class="form-check-label"><?php echo esc_html__( "[Contact Form CFDB7] Hide from Forget Me Form", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_cf7db_hide_from_forgetme_form; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[7]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_wpforms_lite_consent_field" class="form-check-label"><?php echo esc_html__( "[WPForms Lite] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_wpforms_lite_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[8]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_contact_form_7_consent_field" class="form-check-label"><?php echo esc_html__( "[Contact Form 7] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_contact_form_7_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="<?php echo sanitize_html_class($choose_plugin[9]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_Yikes_Inc_Easy_Mailchimp_Extender_consent_field" class="form-check-label"><?php echo esc_html__( "[Easy Forms for Mailchimp] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_Yikes_Inc_Easy_Mailchimp_Extender_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[10]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_ct_waitlist_consent_field" class="form-check-label"><?php echo esc_html__( "[Waitlist for WooCommerce - Back In Stock Notifier by CreateIT] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_ct_waitlist_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[11]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_eform_consent_field" class="form-check-label"><?php echo esc_html__( "[eForm - WordPress Form Builder] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_eform_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[12]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_events_manager_consent_field" class="form-check-label"><?php echo esc_html__( "[Events Manager] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_events_manager_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="<?php echo sanitize_html_class($choose_plugin[13]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_flamingo_hide_from_forgetme_form" class="form-check-label"><?php echo esc_html__( "[Flamingo] Hide from Forget Me Form", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_flamingo_hide_from_forgetme_form; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[14]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_formcraft_form_premium_consent_field" class="form-check-label"><?php echo esc_html__( "[Formcraft] Inject consent checkbox to all forms (Premium)", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_formcraft_form_premium_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            
                            <label for="services_formcraft_form_builder_consent_field" class="form-check-label"><?php echo esc_html__( "[Formcraft] Inject consent checkbox to all forms (Basic)", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_formcraft_form_builder_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[15]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_formidable_forms_consent_field" class="form-check-label"><?php echo esc_html__( "[Formidable Forms] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_formidable_forms_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="<?php echo sanitize_html_class($choose_plugin[16]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_gravity_forms_consent_field" class="form-check-label"><?php echo esc_html__( "[Gravity Forms] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_gravity_forms_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[17]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_klaviyo_consent_field" class="form-check-label"><?php echo esc_html__( "[Klaviyo] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_klaviyo_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[18]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_mailchimp_consent_field" class="form-check-label"><?php echo esc_html__( "[Mailchimp] Inject consent checkbox to order fields", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_mailchimp_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[19]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_mailerlite_consent_field" class="form-check-label"><?php echo esc_html__( "[Mailerlite] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_mailerlite_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="<?php echo sanitize_html_class($choose_plugin[20]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_mailster_consent_field" class="form-check-label"><?php echo esc_html__( "[Mailster] Inject consent checkbox to subscribe forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_mailster_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[21]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_metorik_helper_consent_field" class="form-check-label"><?php echo esc_html__( "[Metorik Helper] Inject consent checkbox to subscribe forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_metorik_helper_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[22]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_newsletter_consent_field" class="form-check-label"><?php echo esc_html__( "[Newsletter] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_newsletter_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[23]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_ninja_forms_consent_field" class="form-check-label"><?php echo esc_html__( "[Ninja-Forms] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_ninja_forms_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="<?php echo sanitize_html_class($choose_plugin[24]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_quform_consent_field" class="form-check-label"><?php echo esc_html__( "[Quform] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_quform_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[25]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_ultimate_member_consent_field" class="form-check-label"><?php echo esc_html__( "[Ultimate Member] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_ultimate_member_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[26]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_woocommerce_consent_field" class="form-check-label"><?php echo esc_html__( "[WooCommerce] Inject consent checkbox to order fields", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_woocommerce_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            
                            <label for="services_woocommerce_edit_account_consent_field" class="form-check-label"><?php echo esc_html__( "[WooCommerce] Inject consent checkbox to account forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_woocommerce_edit_account_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            
                            <label for="services_woocommerce_checkout_consent_field" class="form-check-label"><?php echo esc_html__( "[WooCommerce] Inject consent checkbox to checkout", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_woocommerce_checkout_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="<?php echo sanitize_html_class($choose_plugin[27]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_wordfence_block_cookies" class="form-check-label"><?php echo esc_html__( "[Wordfence] Block Wordfence cookies when a user doesn't accept Functionality cookies", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_wordfence_block_cookies; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[28]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_wp_comments_consent_field" class="form-check-label"><?php echo esc_html__( "[WP Comments] Inject consent checkbox to comments fields", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_wp_comments_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[29]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_wp_foro_consent_field" class="form-check-label"><?php echo esc_html__( "[wpForo] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_wp_foro_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[30]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_wp_job_manager_hide_from_forgetme_form" class="form-check-label"><?php echo esc_html__( "[WP Job Manager] Hide from Forget Me Form", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_wp_job_manager_hide_from_forgetme_form; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="<?php echo sanitize_html_class($choose_plugin[31]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_wp_posts_hide_from_forgetme_form" class="form-check-label"><?php echo esc_html__( "[WordPress Posts] Hide from Forget Me Form", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_wp_posts_hide_from_forgetme_form; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[32]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_wp_comments_network_signup_consent_field" class="form-check-label"><?php echo esc_html__( "[WP User] Inject consent checkbox to User network signup form fields", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_wp_comments_network_signup_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            
                            <label for="services_wp_comments_register_consent_field" class="form-check-label"><?php echo esc_html__( "[WP User] Inject consent checkbox to User register form fields", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_wp_comments_register_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            
                            <label for="services_wp_comments_lost_password_consent_field" class="form-check-label"><?php echo esc_html__( "[WP User] Inject consent checkbox to lost password form fields", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_wp_comments_lost_password_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[33]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_yith_woocommerce_wishlist_hide_from_forgetme_form" class="form-check-label"><?php echo esc_html__( "[YITH Woocommerce Wishlist] Hide from Forget Me Form", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_yith_woocommerce_wishlist_hide_from_forgetme_form; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="<?php echo sanitize_html_class($choose_plugin[34]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            
                            <label for="services_youtube_remove_iframe" class="form-check-label"><?php echo esc_html__( "[Youtube] Remove youtube iframes until Necessary cookies accepted", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_youtube_remove_iframe; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $is_active = function_exists( 'akismet_http_post' );
                    $settingLink = $is_active ? '<a href="' . get_admin_url() . '/options-general.php?page=akismet-key-config">Settings</a>' : 'Settings (Not Active)';
                    ?>
                    <div id="<?php echo sanitize_html_class($choose_plugin[35]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            <?php echo esc_html__( "[Akismet Anti-Spam] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?>
                            <p class="form-check-label"><?php printf('<i>Set this on Akismet Plugin ' . $settingLink . '</i>'); ?></p>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    if (in_array( 'metform/metform.php', apply_filters('active_plugins', get_option('active_plugins')) )) :
                    ?>
                    <div id="<?php echo sanitize_html_class($choose_plugin[36]); ?>" class="service-elem sr-only">
                        <div class="mb-3 form-check">
                            <label for="services_metform_consent_field" class="form-check-label"><?php echo esc_html__( "[Metform] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ); ?></label>
                            <?php echo $services_metform_consent_field; ?>
                            <div class="ct-ultimate-gdpr-form-check">
                                <div class="ct-ultimate-gdpr-checkbox-switch">
                                    <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
                <div class="col-lg-6 d-flex justify-content-center">
                    <div class="service__image">
                        <span><?php echo esc_html__( 'Preview', 'ct-ultimate-gdpr' ); ?></span>
                        <div class="service__image-inner"></div>
                    </div>
                </div>
            </div>
            <!-- / row -->

            <?php ct_ultimate_gdpr_wizard_submit(esc_html__('Submit', 'ct-ultimate-gdpr')); ?>

        </form>