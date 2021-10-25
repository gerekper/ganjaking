<?php
/**
 * Extension-Boilerplate
 *
 * @link https://github.com/ReduxFramework/extension-boilerplate
 *
 * GT3 Registration - Modified For ReduxFramework
 *
 * @package     GT3 Registration - Extension for building header
 * @author      gt3themes
 * @version     1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_gt3_registration' ) ) {

    /**
     * Main ReduxFramework_custom_field class
     *
     * @since       1.0.0
     */
    class ReduxFramework_gt3_registration extends ReduxFramework {

        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value ='', $parent ) {


            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;


            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );

                if (trailingslashit( str_replace( '\\', '/', ABSPATH ) ) == '/') {
                    $this->extension_url = site_url( $this->extension_dir );
                }else{
                    /*$this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );*/
                }
                $this->extension_url = plugin_dir_url(__FILE__);
            }


            /*if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                $this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
            }

            // Set default args for this field to avoid bad indexes. Change this to anything you use.
            $defaults = array(
                'options'           => array(),
                'stylesheet'        => '',
                'output'            => true,
                'enqueue'           => true,
                'enqueue_frontend'  => true
            );
            $this->field = wp_parse_args( $this->field, $defaults );   */

        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {

            // HTML output goes here

            $nonce = wp_create_nonce( "redux_{$this->parent->args['opt_name']}_gt3_registrator" );

            if ( ! is_array( $this->value ) && isset( $this->field['options'] ) ) {
                $this->value = $this->field['options'];
            }

            $option_value = $this->value;
            if (!is_array($option_value)) {
                $option_value = array(
                    'puchase_code' => $option_value,
                );
            }
            $purchase_code = $this->value;

            $gt3_registration_status = get_option( 'gt3_registration_status');
            $side_row = '';
            /*$registration_returns = array();
            if ($gt3_registration_status != 'active' && !empty($purchase_code)) {
                $registration_returns = gt3_registration($purchase_code);
                $registration_returns = json_decode($registration_returns,true);
                if ($registration_returns['respond'] == 'Product is activated!') {
                    update_option( 'gt3_registration_status' , 'active');
                    if (!empty($registration_returns['supported_until'])) {
                        update_option( 'gt3_registration_supported_until' , $registration_returns['supported_until']);
                    }
                }
            }else{
                update_option( 'gt3_registration_status' , '');
                update_option( 'gt3_registration_supported_until' , '');
            }*/

            echo '<div class="gt3_register_container'.($gt3_registration_status == 'active' ? ' gt3_register_active' : '').'" data-nonce="' . $nonce . '" data-step="'.($gt3_registration_status == 'active' ? '3' : '1').'">';

	        if($gt3_registration_status == 'active' && empty(trim($option_value['puchase_code']))) {
		        $gt3_registration_msg   = '<strong>Your purchase code is not activated!</strong><br/>
If you see an empty field for the purchase code, please put the correct purchase code and click deactivate button, then activate it again.';
		        echo '<div style="margin: 5px 0 15px;
    background: #fff;
    border: 1px solid #ccd0d4;
    border-left-width: 4px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 1px 12px;
    border-left-color: #dc3232;
    "><p style="margin: 0.5em 0;
    padding: 2px;">'.$gt3_registration_msg.'</p></div>';
	        }


            echo '<input type="text" id="' . esc_attr($this->field['id']) . '_purchase_code" name="'.($this->parent->args['opt_name']).'[' . esc_attr($this->field['id']) . '][puchase_code]" value="'.$option_value['puchase_code'].'" class="regular-text "'
                        .(($gt3_registration_status == 'active' && !empty(trim($option_value['puchase_code']))) ? ' readonly="readonly"' : '')
                        . esc_attr($this->field['class'])
                . '>';
            echo '<div class="gt3_register__buttons">';
                echo '<a href="javascript:void(0);" class="gt3_register__check">'.__( 'Activate', 'wizecore' ).'</a>';
                echo '<a href="javascript:void(0);" class="gt3_register__submit">'.__( 'Activate', 'wizecore' ).'</a>';
                echo '<a href="javascript:void(0);" class="gt3_register__deregister">'.__( 'Deactivate', 'wizecore' ).'</a>';
//                echo '<a href="javascript:void(0);" class="gt3_activation_refresh" title="'.__( 'Refresh Activation', 'wizecore' ).'"><i class="fa fa-refresh" aria-hidden="true"></i></a>';
            echo "</div>";


            if ($gt3_registration_status != 'active') {
                echo '<div class="gt3_info_container">'.__( 'To unlock all theme features and get auto-updates, please activate the theme. Enter your purchase code and click "Activate". Don\'t know where the purchase code is? ', 'wizecore' ).'<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">'.__( 'Click here.', 'wizecore').'</a>'.'</div>';
            }


            if ($gt3_registration_status == 'active') {

                if (get_option( 'gt3_account_attached' ) == 'true') {
                    echo "<div class='gt3_support_container gt3_info_container'>";
                        echo '<div><strong>'.esc_html( 'The purchase code is linked to your GT3themes account', 'gt3_themes_core' ).' </strong></div>';
                        echo "<a class='button button-primary' href='https://gt3accounts.com/app' style='color: #71c341;background: #ffffff;border-color: #ffffff;box-shadow: none;text-shadow: none;color: #d54e21;font-weight: 600;margin-top: 5px;'>". esc_html( "Go to Your Account", "gt3_themes_core" ) ." </a>";
                    echo "</div>";
                }else{
                    echo '<div class="gt3_info_container gt3_info_container--no_icon gt3_account_submit_container">';
                        echo "<div class='gt3_register__popup_sepparator'><span>".__( 'Next Step', 'wizecore' )."</span></div>";
                        echo '<strong style="font-size: 1.4em;color: #121212;">'.__( 'It\'s time to protect your purchase code.', 'wizecore' ).'</strong>';
                        echo '<p class="gt3_register__key"><i class="fa fa-key" aria-hidden="true"></i> <span class="key" style="text-decoration: underline;">'.$option_value['puchase_code'].'</span></p>';
                        echo "<p>".__( 'Enter your email address to bind the purchase code. We will create a customer account for you by using your email address.', 'wizecore' )."</p>";
                        echo "<div>".__( 'After that, you will be able to:', 'wizecore' )."</div>";
                        echo "<ul>
                            <li>".__( 'View where your purchase code is used.', 'wizecore' )."</li>
                            <li>".__( 'Manage all your purchase codes.', 'wizecore' )."</li>
                            <li>".__( 'Check for product and support status.', 'wizecore' )."</li>
                        </ul>";
                        $admin_email = '';// empty($option_value['email_account']) ? get_bloginfo('admin_email') : $option_value['email_account'];
                        echo '<input type="email"  id="' . esc_attr($this->field['id']) . '_account_email"  name="'.($this->parent->args['opt_name']).'[' . esc_attr($this->field['id']) . '][email_account]" value="'.$admin_email.'" class="regular-text gt3_account '.esc_attr($this->field['class']) .'">';
                        $newsletter_value = '';
                        echo '<a href="javascript:void(0);" class="gt3_account_submit">'.__( 'Protect & Create Account', 'wizecore' ).'</a>';
                    echo "</div>";
                }


                if (get_option('sdfgdsfgdfg') != 'Product is activated!') {
                    update_option( 'gt3_registration_status', '');
                }
                if (function_exists('gt3_get_support_time_left')) {
                    $support_time_left = gt3_get_support_time_left();
                    if (!empty($support_time_left['time_to_left'])) {
                        $gt3_tmeme_id = get_option( 'gt3_tmeme_id' );
                        if (!empty($gt3_tmeme_id)) {
                            $theme_link = 'https://themeforest.net/checkout/from_item/'.(int)$gt3_tmeme_id.'?license=regular&size=source&support=renew_6month';
                        }else{
                            $theme_link = 'https://themeforest.net/user/gt3themes/portfolio?ref=gt3themes';
                        }
                        echo "<div class='".(!empty($support_time_left['expired']) && $support_time_left['expired'] == true ? '' : 'gt3_support_container')." gt3_info_container'>";
                        if (!empty($support_time_left['expired']) && $support_time_left['expired'] == true) {
                            printf( 'Your support package for this theme was expired <strong>%1$s</strong> ago', $support_time_left['time_to_left']);
                        }else{
                            printf( 'You have <strong>%1$s</strong> of available support', $support_time_left['time_to_left']);
                        }
                        echo '<div><a class="button button-primary" href="'. $theme_link .'" style="color: #71c341;background: #ffffff;border-color: #ffffff;box-shadow: none;text-shadow: none;color: #d54e21;font-weight: 600;margin-top: 5px;">'. esc_html( "Update Support Package", "gt3_themes_core" ) .' <i class="fa fa-angle-right" aria-hidden="true"></i></a></div>';
                        echo "</div>";
                    }
                }
            }


            echo "<div class='gt3_register__popup'>";
                echo "<div class='gt3_register__popup_container'>";
                    echo "<div class='gt3_register__popup_close'><i class='fa fa-times' aria-hidden='true'></i></div>";
                    echo '<div class="gt3_info_container gt3_info_container--no_icon gt3_account_submit_container">';
                        echo "<div class='gt3_register__popup_sepparator'><span>".__( 'Next Step', 'wizecore' )."</span></div>";
                        echo '<strong style="font-size: 1.4em;color: #121212;">'.__( 'It\'s time to protect your purchase code.', 'wizecore' ).'</strong>';
                        echo '<p class="gt3_register__key"><i class="fa fa-key" aria-hidden="true"></i> <span class="key" style="text-decoration: underline;">'.$option_value['puchase_code'].'</span></p>';
                        echo "<p>".__( 'Enter your email address to bind the purchase code. We will create a customer account for you by using your email address.', 'wizecore' )."</p>";
                        echo "<div>".__( 'After that, you will be able to:', 'wizecore' )."</div>";
                        echo "<ul>
                            <li>".__( 'View where your purchase code is used.', 'wizecore' )."</li>
                            <li>".__( 'Manage all your purchase codes.', 'wizecore' )."</li>
                            <li>".__( 'Check for product and support status.', 'wizecore' )."</li>
                        </ul>";
                        $admin_email = '';//empty($option_value['email_account']) ? get_bloginfo('admin_email') : $option_value['email_account'];
                        echo '<input type="email"  id="' . esc_attr($this->field['id']) . '_account_email"  name="'.($this->parent->args['opt_name']).'[' . esc_attr($this->field['id']) . '][email_account]" value="'.$admin_email.'" class="regular-text gt3_account '.esc_attr($this->field['class']) .'">';
                        $newsletter_value = '';
                        echo '<a href="javascript:void(0);" class="gt3_account_submit">'.__( 'Protect & Create Account', 'wizecore' ).'</a>';
                    echo "</div>";
                echo "</div>";
                echo '<div class="gt3_info_container gt3_info_container--no_icon gt3_account_submit_success_container">';
                    echo '<strong style="font-size: 1.4em;color: #121212;">'.__( 'Hey!', 'wizecore' ).'</strong>';
                    echo "<p>".__( 'Your account has been successfully created and its details were sent your e-mail address ', 'wizecore' )."<strong class='gt3_account_emai_holder'></strong></p>";
                    echo "<p>".__( 'You can log into your account at', 'wizecore' ).' <a href="https://gt3accounts.com/app">https://gt3accounts.com/app</a> '.__( 'to manage your purchase code(s) and get dedicated support.', 'wizecore' )."</p>";
                    echo "<p>".__( "If you haven't received your account details, please contact us at", 'wizecore' ).' <a href="mailto:help@gt3themes.com">help@gt3themes.com</a> '.__( "and our support team will assist you.", 'wizecore' )."</p>";
                    echo "<p><strong>".__( "Thank you for choosing our product.", 'wizecore' )."</strong></p>";
                    echo "<div class='gt3_account_submit_success_button'>".__( "Close", 'wizecore' )."</div>";
                echo "</div>";
            echo "</div>";


            echo "</div>"; //end gt3_register_container




        }

        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {

            wp_enqueue_script(
                'redux-field_gt3_registration-js',
                trailingslashit($this->extension_url) . 'field_gt3_registration.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field_gt3_registration-css',
                trailingslashit($this->extension_url) . 'field_gt3_registration.css',
                time(),
                true
            );

        }

        /*function localize( $field, $value = "" ) {

            $params = array();

            if ( empty( $value ) ) {
                $value = $this->value;
            }
            $params['val'] = $value;

            return $params;
        }*/

        /**
         * Output Function.
         *
         * Used to enqueue to the front-end
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function output() {

            if ( $this->field['enqueue_frontend'] ) {

            }

        }

    }
}
