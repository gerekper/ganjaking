<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class UP_UserInvitation
 *
 * @todo : Pagination for user invitations.
 */

class UP_UserInvitation extends UP_Data
{

    /**
     * User Invitation Object data
     * @since 4.9.31
     * @var array
     */
    protected $data = array(
        'email' => '',
        'status' => '',
        'code' => ''
    );
    /**
     * Default invited users option name
     *
     * @var string
     */
    protected $option_name = 'up_invited_users';

    /**
     * User invitation code
     * @var string
     */
    protected $user_code = '';

    public function __construct()
    {
        add_action('user_invitation_body', [$this, 'template'], 1);
        add_filter('wp_mail_content_type', [$this, 'mailContentType']);
    }

    /**
     * Get all email invited users
     * @since 4.9.31
     * @return array
     */
    public function getAll()
    {
        $this->data = $this->getOption();

        return $this->data;
    }

    public function getOne($value, $column)
    {
        $this->getAll();

        foreach ($this->data as $key => $invitation){

            if($invitation[$column] === $value){
                return $key;
            }
        }

        return false;
    }

    /**
     * Delete User invitation
     *
     * @var $user_email string
     * @since 4.9.31
     */
    public function deleteInvitation($user_email)
    {

        $remove_email = $this->getOne($user_email, 'email');

        unset($this->data[$remove_email]);

        $this->changed_option = $this->data;

        $this->updateOption();
    }

    /**
     * Get User email by invitation code
     *
     * @param $invitation_code
     * @since 4.9.31
     * @return bool
     */
    public function getEmailByInvitationCode($invitation_code)
    {
        $this->getAll();

        $user_exist = $this->getOne($invitation_code, 'code');

        if($user_exist === FALSE)
            return FALSE;

        $this->user_code = $invitation_code;



        return $this->data[$user_exist];
    }

    /**
     * Update user invitation status
     *
     * @param null $code
     * @param $status
     * @since 4.9.31
     */
    public function updateStatus($status, $code = null)
    {
        $this->getAll();

        if(!$code)
            $code = $this->user_code;

        $userStatus = $this->getOne($code, 'code');

        $this->data[$userStatus]['status'] = $status;

        $this->changed_option = $this->data;
        $this->updateOption();

    }

    /**
     * Send invitation email or resend
     *
     * @var $user_email string
     * @var $cc_emails string
     * @since 4.9.31
     * @return boolean
     */
    public function send($user_email, $cc_emails = null)
    {

        if (!email_exists($user_email)) {
            $inviteCode = md5($user_email);
            //generate md5 encoded code for email invitation
            $this->data[] = [
                'email' => $user_email,
                'status' => 'Pending',
                'code' => $inviteCode,
            ];
            // Send email for the user
            $email_message = $this->getEmailMessage($inviteCode, $cc_emails);
            wp_mail($user_email, $email_message['subject'] . get_bloginfo('name'),
                html_entity_decode($email_message['template']),
                $email_message['headers']);

            $this->changed_option = $this->data;
            $this->updateOption();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Resend email for the user.
     *
     * @param string $user_email
     * @since 4.9.31
     * @return boolean
     */
    public function resend($user_email)
    {
        // Get all invited users
        $this->getAll();

        $user_exist = $this->getOne($user_email, 'email');

        if ($user_exist === false) {
            return false;
        }

        $email_message = $this->getEmailMessage($this->data[$user_exist]['code']);
        wp_mail($user_email, $email_message['subject'] . get_bloginfo('name'),
            html_entity_decode($email_message['template']),
            $email_message['headers']);

        return true;
    }

    /**
     * Invitation email message
     *
     * @var $inviteCode string invite code
     * @var $ccEmails array cc emails
     * @since 4.9.31
     * @return array
     */
    public function getEmailMessage($inviteCode, $ccEmails = null)
    {
        $inviteUrl = home_url() . '/' . $this->getOption('slug',
                'userpro') . '/' . $this->getOption('slug_register', 'userpro');

        $mailFromName = !empty($this->getOption('mail_from_name', 'userpro')) ? $this->getOption('mail_from_name', 'userpro') : __('Welcome to the'.get_bloginfo('name'),'userpro');

        $mailFrom = !empty($this->getOption('mail_from', 'userpro')) ?
            $this->getOption('mail_from', 'userpro') : get_option('admin_email');

        $mailInviteSubject = !empty($this->getOption('invite_subject', 'userpro')) ?
            $this->getOption('invite_subject', 'userpro') : __('You are invited to register at ', 'userpro');

        $email_message = array(
            'template' => $this->getEmailTemplate($inviteUrl, $inviteCode),
            'headers' => array('From: ' . $mailFromName . ' <' . $mailFrom . '>'),
            'subject' => $mailInviteSubject,
        );

        if (isset($ccEmails)){

            foreach ($ccEmails as $email)
                $email_message['headers'][] = 'Cc: ' . $email;

        }

        return $email_message;
    }
    /**
     * Get email template for user invitation
     * @var $inviteUrl string
     * @var $code string
     * @since 4.9.31
     * @return string
     */
    public function getEmailTemplate($inviteUrl, $code)
    {
        $inviteEmailTemplate = userpro_get_option('userpro_invite_emails_template');
        $emailTemplate = '<html><body>';
        $emailTemplate .= str_replace('{invitelink}', $inviteUrl . '?code=' . urlencode($code), $inviteEmailTemplate);
        $emailTemplate .= '</body></html>';

        return $emailTemplate;
    }

    /**
     * User listing template
     *
     * @var $users array
     * @since 4.9.31
     * @return string
     */
    public function template($users = null)
    {
        if (is_null($users)) {
            $users = $this->option;
        }

        $form = '<div class="up-invitation">';
        foreach ($users as $user) {
            $status_class = $user['status'] == 'Registered' ? 'up-approve' : 'up-pending';
            $form .= '<div class="up-invitation__user-block">';
            // user email
            $form .= '<span class="up-user-email">' . $user['email'] . '</span>';
            // user status
            $form .= '<span class="'.$status_class.'">' . $user['status'] . '</span>';
            $form .= '<div class="up-invitation__buttons" data-user-email='.$user['email'].'>';
            if($user['status'] !== 'Registered')
            $form .= '<a class="up-admin-btn small approve" data-action="up_resend_invitation" href="#">'.__('Re-send','userpro').'</a>';

            $form .= '<a class="up-admin-btn small remove" data-action="up_delete_invitation"  href="#">'.__('Remove','userpro').'</a>';
            $form .= '</div>';
            $form .= '</div>';
        }
        $form .= '</div>';

        return $form;
    }
    /**
     * Email content type
     *
     * @since 4.9.31
     * @return string
     */
    public function mailContentType()
    {
        return 'text/html';
    }
}