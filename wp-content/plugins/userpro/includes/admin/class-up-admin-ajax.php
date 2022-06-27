<?php

if (!defined('ABSPATH')) {
    exit;
}

class UP_UserAdminAjax extends UP_Ajax{

    /**
     * User ajax events instance.
     *
     * @since 4.9.31
     * @var null
     */
    protected static $instance = null;

    /**
     * Register ajax events. true|false for priv/nopriv ajax methods.
     *
     * @since 4.9.31
     * @var array
     */
    protected $ajax_events = [
        'user_invite' => true,
        'verifyUnverifyAllUsers' => true,
        'reset_option' => true,
    ];

    /**
     * Create instance of UserPro admin ajax class
     *
     * @since 4.9.31
     * @return UP_UserAdminAjax|null
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->registerAjaxEvents();
    }


    public static function reset_option()
    {

        update_option('userpro', userpro_default_options() );

        $response = [
            'success' => true,
            'message' => __('Settings was revert to default.', 'userpro'),
            'messageType' => 'success',
        ];

        wp_send_json_success($response);

    }

    /**
     * Send user invitation email.
     * @since 4.9.31
     */
    public static function user_invite()
    {
        if(empty($_POST['emails'])){
            wp_send_json_error(__('Please enter email address to invite user', 'userpro'), 409);
        }
        $ccEmails = null;
        //Trim white spaces from emails
        $emails = preg_replace('/\s+/', '', $_POST['emails']);
        $emails = explode(',', $emails);

        $warning_response = [];
        // Get
        if(!empty($_POST['cc_emails'])){
            $ccEmails = preg_replace('/\s+/', '', $_POST['cc_emails']);
            $ccEmails = explode(',', $ccEmails);
            foreach ($ccEmails as $email){
                if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
                    wp_send_json_error(__('Not a valid email address', 'userpro'), 409);
                }
            }
        }

        foreach($emails as $email) {
            if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
                wp_send_json_error(__('Not a valid email address', 'userpro'), 409);
            }else{
                $invitedUsers = new UP_UserInvitation();
                $userEmails = $invitedUsers->getAll();

                if(in_array($email, array_column($userEmails, 'email'))){
                    $warning_response[] = ['status' => 'warning', 'message' => __('Emails address already exist','userpro')];
                    continue;
                }

                $isInvited = $invitedUsers -> send($email, $ccEmails);
                if (!$isInvited) {
                    wp_send_json_error(__('User with this email is already registered.', 'userpro'), 409);
                }

            }
        }

        $response = !empty($warning_response) ? $warning_response : __('Invitation mail sent Successfully!', 'userpro');

        wp_send_json_success($response, 200);
    }

    /**     
     * Verify/unverify/approve/deny all users
     * 
     * @since 4.9.33
     * @return JSON
     */
    public static function verifyUnverifyAllUsers(){
    
        if (!current_user_can('manage_options')){
            die(); 
        }// admin priv

        global $userpro,$userpro_request_admin;
        $output = array();
        $user_ids = $_POST['user_id'];
        $user_ids = explode(' ', $user_ids); 
        $action_type = $_POST['action_type'];
        $messageType = 'success';
        
        if($action_type == 'verify'){
            foreach($user_ids as $user_id){
                $userpro->verify($user_id);
    
                $arr = get_option('userpro_verify_requests');
                if (isset($arr) && is_array($arr)){
                    $arr = array_diff($arr, array( $user_id ));
                    update_option('userpro_verify_requests', $arr);
                }   
        
                do_action('userpro_after_account_verified');
            }
            $responseText = __('All users have been verified', 'userpro'); 
        }
        elseif($action_type == 'unverify'){
            foreach($user_ids as $user_id){
                $userpro->unverify($user_id);
    
                do_action('userpro_after_account_unverified'); // cache clear
            }

            $responseText = __('All users have been rejected', 'userpro'); 
        }
        elseif($action_type == 'approve'){
            foreach($user_ids as $user_id){
                $role=userpro_get_option('update_role');
    
                if($role!='no_role')
                {
                    $user_obj = new WP_User( $user_id );
                    $user_obj->set_role($role);
                }
        
                $userpro->activate($user_id);
            }

            $responseText = __('All users have been approved', 'userpro'); 
        }
        else{ // $action_type = 'deny'
            foreach($user_ids as $user_id){
                $reject_email_subject = userpro_get_option('mail_rejectuser_s');
                $reject_email_message = userpro_get_option('mail_rejectuser');
                $ud = get_userdata($user_id);
                $user_email = $ud->user_email;
                wp_mail($user_email, $reject_email_subject, $reject_email_message);
                
                $userpro->delete_user($user_id);
            }

            $responseText = __('All users have been deleted', 'userpro');
        }

        $output['count'] = $userpro_request_admin->get_pending_verify_requests_count_only();
        $output['message'] = $responseText;
        $output['messageType'] = $messageType;

        wp_send_json_success($output, 200);
    }
}