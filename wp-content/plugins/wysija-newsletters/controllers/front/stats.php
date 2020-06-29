<?php
defined('WYSIJA') or die('Restricted access');


class WYSIJA_control_front_stats extends WYSIJA_control_front{
    var $model=''   ;
    var $view='';

    /**
     * Possible characters to be url encoded
     * @var array
     */
    protected $characters_to_encode = array(
        '@'
    );

    function __construct(){
        parent::__construct();
    }

    /**
     * count the click statistic and redirect to the right url
     * @return boolean
     */
    function analyse(){
        if(isset($_REQUEST['email_id']) && isset($_REQUEST['user_id'])){
            $WJ_Stats = new WJ_Stats();
            if(!empty($WJ_Stats->clicked_url)){
                // clicked stats
                $url = $this->encode_url($WJ_Stats->subscriber_clicked());
                $external_url_unescaped_and_without_utm = preg_replace('!/?\?utm.*!i', '', $url);
                $external_url_escaped_and_without_utm = preg_replace('!/?\?utm.*!i', '', htmlentities($WJ_Stats->subscriber_clicked())); // remove anything that starts with ?utm or /?utm
                $internal_site_url = preg_replace('!https?://!i', '', htmlentities(get_site_url()));
                $internal_home_url = preg_replace('!https?://!i', '', htmlentities(get_home_url()));
                $model_email = WYSIJA::get('email', 'model');
                $email_object = $model_email->getOne(false,array('email_id' => $_REQUEST['email_id']));
                if (preg_match('!'. preg_quote($external_url_unescaped_and_without_utm, '!') .'!i', $email_object['body']) ||
                    preg_match('!'. preg_quote($external_url_escaped_and_without_utm, '!') .'!i', $email_object['body']) ||
                    preg_match('!^https?://'. preg_quote($internal_site_url, '!') .'!i', $url) ||
                    preg_match('!^https?://'. preg_quote($internal_home_url, '!') .'!i', $url)

                ) {
                    do_action('mpoet_click_stats', $WJ_Stats);
                    $this->redirect($url);
                }
                header('HTTP/1.0 404 Not Found');
                echo '<h1>404 Not Found</h1>';
                echo 'The page that you have requested could not be found.';
                exit();
            }else{
                // opened stat
                $WJ_Stats->subscriber_opened();
            }
        }

        return true;
    }

    /**
     * Encode some special characters in url
     * @param string $url
     * @return string
     */
    protected function encode_url($url) {
        return str_replace(
                $this->characters_to_encode,
                array_map('urlencode', $this->characters_to_encode),
                $url
                );
    }

}
