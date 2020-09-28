<?php
namespace NinjaTables\Admin;
use NinjaTables\Classes\ArrayHelper;

class DeactivationMessage
{
    private $slug = 'ninja-tables';
    private $apiUrl = 'https://wpmanageninja.com/?wpmn_api=product_users';

    public function broadcastFeedback() {
        if($this->isLocalhost()) {
            return;
        }
        $reason = ArrayHelper::get($_REQUEST, 'reason', 'other');
        $reason_message = ArrayHelper::get($_REQUEST, 'custom_message', '');

        $currentUser = wp_get_current_user();
        $data = array(
            'first_name' => $currentUser->first_name,
            'last_name' => $currentUser->last_name,
            'display_name' => $currentUser->display_name,
            'email' => $currentUser->user_email,
            'site_url' => site_url(),
            'deactivate_category' => $reason,
            'deactivate_message' => $reason_message,
            'product' => $this->slug,
            'request_from' => $this->get_request_from(),
            'ninja_doing_action' => 'deactivate_reason'
        );

        wp_remote_post($this->apiUrl, array(
            'method' => 'POST',
            'sslverify' => false,
            'body' => $data
        ));

        wp_send_json_success(array(
            'message' => 'Deactivating'
        ));
    }


    public function addPluginDeactivationMessage() {
        if($this->isLocalhost()) {
            return;
        }
        $reasons = $this->getReasons();
        $slug = $this->slug;
        ob_start();
        include 'partials/deactive_form.php';
        $message = ob_get_clean();

        echo $message;
    }

    public function getReasons() {
        return array(
            "got_better" => array(
                "label" => "I found a better plugin",
                "custom_placeholder" => "What's the plugin name",
                "custom_label" => '',
                'has_custom' => true
            ),
            "does_not_work" => array(
                "label" => "The plugin didn't work",
                "custom_placeholder" => "",
                "custom_label" => 'Kindly tell us any suggestion so we can improve',
                'has_custom' => true
            ),
            "temporary" => array(
                "label" => "It's a temporary deactivation. I'm just debugging an issue.",
                'has_custom' => false
            ),
            "other" => array(
                "label" => "Other",
                "custom_label" => 'Kindly tell us the reason so we can improve.',
                "custom_placeholder" => "",
                'has_custom' => true
            )
        );
    }

    public function get_request_from() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function isLocalhost() {
        $whitelist = array('127.0.0.1', '::1');
        return in_array($this->get_request_from(), $whitelist);
    }
}