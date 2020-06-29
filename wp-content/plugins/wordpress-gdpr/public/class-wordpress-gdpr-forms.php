<?php

class WordPress_GDPR_Forms extends WordPress_GDPR
{
    protected $plugin_name;
    protected $version;
    protected $options;

    /**
     * Store Locator Plugin Construct
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   string                         $plugin_name
     * @param   string                         $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Init the Public
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function init()
    {
        global $wordpress_gdpr_options;

        $this->options = $wordpress_gdpr_options;

        if (!$this->get_option('enable')) {
            return false;
        }

        return true;
    }

    public function get_forget_me_form()
    {
        $this->type = "forget-me";
        $this->option_prefix = "forgetMe";
        $this->type_text = __('Forget Me Request', 'wordpress-gdpr');
        return $this->get_form();
    }

    public function get_contact_dpo_form()
    {
        $this->type = "contact-dpo";
        $this->option_prefix = "contactDPO";
        $this->type_text = __('DPO Contact Request', 'wordpress-gdpr');
        return $this->get_form();
    }

    public function get_data_rectification_form()
    {
        $this->type = "data-rectification";
        $this->option_prefix = "dataRectification";
        $this->type_text = __('Data Rectification', 'wordpress-gdpr');
        return $this->get_form();
    }
    

    public function get_request_data_form()
    {
        $this->type = "request-data";
        $this->option_prefix = "requestData";
        $this->type_text = __('Data Request', 'wordpress-gdpr');
        return $this->get_form();
    }



    /**
     * Get Forms depending on Type
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function get_form()
    {
        ob_start();

        $privacyCenterPage = $this->get_option('privacyCenterPage');

        if(!empty($privacyCenterPage)) {
            $privacyCenterPage = get_permalink($privacyCenterPage);
            echo '<a class="wordpress-gdpr-back-link" href="' . $privacyCenterPage . '">&larr; ' . __('Return to Privacy Center', 'wordpress-gdpr') . '</a>';
        }

        if (isset($_POST['wordpress_gdpr_form_type'])) {

            $status = $this->sanitize_data($_POST, $_POST['wordpress_gdpr_form_type']);

            if ($status) {
                echo '<div class="alert alert-success" role="alert">';
                    if($this->type == "contact-dpo" || $this->type == "data-rectification") {
                        echo __('Request sent successfully. Our DPO will answer you soon.', 'wordpress-gdpr');
                    } 
                    else {
                        echo __('Request received. We have sent you a confirmation email to validate your email address for this type of request.', 'wordpress-gdpr');
                    }
                echo '</div>';
                unset($_POST);
            } else {
                echo '<div class="alert alert-danger" role="alert">';
                    echo __('Could not send your request!', 'wordpress-gdpr') . '<br/>';
                    echo implode('<br/>', $this->errors);
                echo '</div>';
            }
        }

        if($this->type == 'request-data' && $this->get_option('requestDataLoggedInButton')) {
            $this->get_button();
        }

        if($this->type == 'forget-me' && $this->get_option('forgetMeLoggedInButton')) {
            $this->get_button();
        }

        echo '<form action="' . esc_url($_SERVER['REQUEST_URI']) . '" class="wordpress-gdpr-form wordpress-gdpr-form-' . $this->type . '" method="post">';

            echo '<input type="hidden" name="wordpress_gdpr_form_type" value="' . $this->type . '">';

            $this->get_recaptcha();

            $this->get_firstname_field(); 
            $this->get_lastname_field(); 
            $this->get_email_field(); 

            if($this->type == "contact-dpo" || $this->type == "data-rectification") {
                $this->get_message_field();     
            }
            $this->get_accept_conditions_field(); 
            $this->get_submit_button(); 

        echo '</form>';

        $html = ob_get_clean();
        return $html;
    }

    private function sanitize_data($data)
    {
        if(isset($data['gdpr_firstname'])) {
            $data['gdpr_firstname'] = sanitize_text_field($data['gdpr_firstname']);
        }

        if(isset($data['gdpr_lastname'])) {
            $data['gdpr_lastname'] = sanitize_text_field($data['gdpr_lastname']);
        }

        if(isset($data['gdpr_email'])) {
            $data['gdpr_email'] = filter_var($data['gdpr_email'], FILTER_SANITIZE_EMAIL);
        }

        if(isset($data['gdpr_terms'])) {
            $data['gdpr_terms'] = sanitize_text_field($data['gdpr_terms']);
        }

        return $this->validate_data($data);
    }

    private function validate_data($data)
    {
        $this->errors = array();

        if($this->get_option('enableRecaptcha')) {
            $is_valid = apply_filters('google_invre_is_valid_request_filter', true);
            
            if(!$is_valid) {
                $this->errors[] = __('Recaptcha not passed!', 'wordpress-gdpr');
            }
        }

        if(!isset($data['gdpr_firstname']) || empty($data['gdpr_firstname'])) {
            $this->errors[] = __('First Name missing.', 'wordpress-gdpr');
        }

        if(!isset($data['gdpr_lastname']) || empty($data['gdpr_lastname'])) {
            $this->errors[] = __('Last Name missing.', 'wordpress-gdpr');
        }

        if(!isset($data['gdpr_email']) || empty($data['gdpr_email'])) {
            $this->errors[] = __('Email missing.', 'wordpress-gdpr');
        }

        if(!isset($data['gdpr_terms']) || empty($data['gdpr_terms'])) {
            $this->errors[] = __('Terms not accepted.', 'wordpress-gdpr');
        }

        if($this->type == 'contact-dpo' || $this->type == "data-rectification") {
            if(!isset($data['gdpr_message']) || empty($data['gdpr_message'])) {
                $this->errors[] = __('Message missing.', 'wordpress-gdpr');
            }
        }

        $userCheckDisabled = $this->get_option($this->option_prefix . 'DisableUserExistsCheck');
        if(!$userCheckDisabled) {
            $this->user = get_user_by('email', $data['gdpr_email']);
            if(!$this->user) {
               $this->errors[] = __('Could not find a user with this email. Please contact our DPO directly.', 'wordpress-gdpr'); 
            }
        }

        if(!empty($this->errors)){
            return false;
        }

        return $this->process_data($data);
    }

    private function process_data($data)
    {
        global $wp_version;
        
        if($this->get_option('useWPCoreFunctions') && version_compare( $wp_version, '4.9.6', '>=' ) && !in_array($this->type, array("contact-dpo", "data-rectification"))) {
            return $this->use_core_function($data);
        }

        $post_content = '';
        if(isset($data['gdpr_message']) && !empty($data['gdpr_message'])) {
            $post_content = $data['gdpr_message'];
        }

        $post_data = array(
           'post_type' => 'gdpr_request',
           'post_title' => $this->type_text,
           'post_content' => $post_content,
           'post_status' => 'publish',
           'comment_status' => 'closed',
           'ping_status' => 'closed',
        );

        if($this->type == 'forget-me' || $this->type == 'request-data' && !empty($this->user)) {
            $post_data['post_author'] = $this->user->ID;
        }

        $post_id = wp_insert_post($post_data);

        if(!$post_id) {
            $this->errors[] = __('Could not create your GDPR request.', 'wordpress-gdpr'); 
            return false;
        }

        $unique = uniqid();
        add_post_meta($post_id, 'gdpr_firstname', $data['gdpr_firstname']);
        add_post_meta($post_id, 'gdpr_lastname', $data['gdpr_lastname']);
        add_post_meta($post_id, 'gdpr_email', $data['gdpr_email']);
        add_post_meta($post_id, 'gdpr_type', $this->type);
        add_post_meta($post_id, 'gdpr_option_prefix', $this->option_prefix);
        add_post_meta($post_id, 'gdpr_unique', $unique);
        add_post_meta($post_id, 'gdpr_status', __('Open', 'wordpress-gdpr'));
        
        // Contact DPO will be sent directly 
        if($this->type == "contact-dpo" || $this->type == "data-rectification") {
            $subject = $this->get_option($this->option_prefix . 'Subject');
            $recipient = $this->get_option($this->option_prefix . 'Email');
            $message = sprintf( __('First Name: %s', 'wordpress-gdpr'), $data['gdpr_firstname']) . '<br>';
            $message .= sprintf( __('Last Name: %s', 'wordpress-gdpr'), $data['gdpr_lastname']) . '<br>';
            $message .= sprintf( __('Email: %s', 'wordpress-gdpr'), $data['gdpr_email']) . '<br>';
            $message .= sprintf( __('Terms Accepted: %s', 'wordpress-gdpr'), $data['gdpr_terms']) . '<br>';

            if(isset($data['gdpr_message']) && !empty($data['gdpr_message'])) {
                $message .= sprintf( __('Message: %s', 'wordpress-gdpr'), $data['gdpr_message']);
            }
            add_post_meta($post_id, 'gdpr_confirmed', __('Not Needed', 'wordpress-gdpr'));
        // Forget me & Export need an email confirmation
        } else {
            $subject = $this->get_option('confirmationEmailSubject');
            $recipient = $data['gdpr_email'];
            $confirmation = $this->get_option('confirmationEmailText');

            $confirmation = sprintf($confirmation, $data['gdpr_firstname'] . ' ' . $data['gdpr_lastname'], $this->type_text);

            $confirmation_link = get_permalink($this->get_option('privacyCenterPage')) . '?confirm=' . $post_id . '&key=' . $unique;
            $confirmation .= '<br><br><a href="' . $confirmation_link . '" target="_blank">' . __('Confirm Request', 'wordpress-gdpr') . '</a>';

            $message = $confirmation;
            add_post_meta($post_id, 'gdpr_confirmed', __('No', 'wordpress-gdpr'));
            if(!empty($this->user)) {
                add_post_meta($post_id, 'gdpr_user_id', $this->user->ID);
            }
        }

        $headers = array(
            'Content-Type: text/html; charset=UTF-8'
        );

        return wp_mail($recipient, $subject, $message, $headers);
    }

    public function use_core_function($data)
    {
        $action = "";
        if($this->type == 'forget-me') {
            $action = 'remove_personal_data';
        }

        if($this->type == 'request-data') {
            $action = 'export_personal_data';
        }

        $request_id = wp_create_user_request(  $data['gdpr_email'], $action );

        if ( is_wp_error( $request_id ) ) {
            $this->errors[] = $request_id->get_error_message();
            return false;
        } elseif ( ! $request_id ) {
            $this->errors[] = $request_id->get_error_message();
            return false;
        }

        wp_send_user_request( $request_id );

        return true;
    }

    private function get_recaptcha()
    {
        if($this->get_option('enableRecaptcha')) {
            do_action('google_invre_render_widget_action');
        }
    }

    /**
     * Get Firstname Field
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    private function get_firstname_field()
    {
        echo '<div class="form-group">';
            echo '<label for="gdpr_firstname">' . __('Your First Name (*)', 'wordpress-gdpr') . '</label>';
            echo '<input class="form-control" type="text" name="gdpr_firstname" value="' . ( isset($_POST["gdpr_firstname"]) ? esc_attr($_POST["gdpr_firstname"]) : '' ) . '" required />';
        echo '</div>';
    }

    /**
     * Get Lastname Field
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    private function get_lastname_field()
    {
        echo '<div class="form-group">';
            echo '<label for="gdpr_lastname">' . __('Your Last Name (*)', 'wordpress-gdpr') . '</label>';
            echo '<input class="form-control" type="text" name="gdpr_lastname" value="' . ( isset($_POST["gdpr_lastname"]) ? esc_attr($_POST["gdpr_lastname"]) : '' ) . '" required />';
        echo '</div>';
    }

    /**
     * Get Email Field
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    private function get_email_field()
    {
        echo '<div class="form-group">';
            echo '<label for="gdpr_email">' . __('Your Email (*)', 'wordpress-gdpr') . '</label>';
            echo '<input class="form-control" type="email" name="gdpr_email" value="' . ( isset($_POST["gdpr_email"]) ? esc_attr($_POST["gdpr_email"]) : '' ) . '" required />';
        echo '</div>';
    }

    /**
     * Get Message Field
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    private function get_message_field()
    {
        echo '<div class="form-group">';
            echo '<label for="gdpr_message">' . __('Your message (*)', 'wordpress-gdpr') . '</label>';
            echo '<textarea class="form-control" name="gdpr_message" required />';
                echo isset($_POST["gdpr_message"]) ? esc_attr($_POST["gdpr_message"]) : '';
            echo '</textarea>';
        echo '</div>';
    }

    /**
     * Get Message Field
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    private function get_accept_conditions_field()
    {
        echo '<div class="form-group">';
            echo '<label for="gdpr_terms">';
                echo '<input id="gdpr_terms" class="form-control" type="checkbox" name="gdpr_terms" value="' . ( isset($_POST["gdpr_terms"]) ? esc_attr($_POST["gdpr_terms"]) : 1 ) . '" required />';
                echo '<span class="gdpr-accept-conditiones-text">' . __('I agree to the privacy policy and that my data will be stored for this GDPR request .', 'wordpress-gdpr') . '</span>';
            echo '</label>';
        echo '</div>';
    }

    /**
     * Get Message Field
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    private function get_submit_button()
    {
        echo '<input type="submit" name="gdpr_submitted" value="' . __('Submit', 'wordpress-gdpr') . '"/>';
    }

    private function get_button()
    {
        if(!is_user_logged_in()) {
            return false;
        }

        if($this->type == "forget-me") {
            $btn_text = __('Delete my Data', 'wordpress-gdpr');
        } else {
            $btn_text = __('Export my Data', 'wordpress-gdpr');
        }

        echo '<form action="' . esc_url($_SERVER['REQUEST_URI']) . '" class="wordpress-gdpr-btn-form wordpress-gdpr-btn-form-' . $this->type . '" method="post">';
            echo '<input type="hidden" name="wordpress_gdpr_btn_form" value="' . $this->type . '">';
            echo '<input type="submit" name="gdpr_submitted" value="' . $btn_text . '"/>';   
        echo '</form>';
    }
}