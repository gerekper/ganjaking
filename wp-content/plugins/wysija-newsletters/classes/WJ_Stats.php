<?php
defined('WYSIJA') or die('Restricted access');
/**
 * Class Stats.
 *
 * Handling subscribers click and open action of an email
 */
class WJ_Stats extends WYSIJA_object {

    public $email_id = null;
    public $user_id = null;
    public $clicked_url = '';
    public $is_preview = false;

    /**
     * Email object
     * @var Wysija_email
     */
    private $_email;

    /**
     * User object
     * @var Wysija_user
     */
    private $_user;

    private $_url_id = null;
    private $_unique_click = false;
    public $decoded_url = '';


    /**
     * load some global variables into the class
     */
    function __construct() {
        if (!empty($_REQUEST['email_id']))
            $this->email_id = (int) $_REQUEST['email_id'];
        if (!empty($_REQUEST['user_id']))
            $this->user_id = (int) $_REQUEST['user_id'];
        if (!empty($_REQUEST['demo']))
            $this->is_preview = true;
        $this->_get_clicked_url();

	// $this->_user can be empty, it's helpful in case an email was sent as a preview to a non-existing user, then a link is opened from there
        $this->_user = WYSIJA::get('user', 'model')->getOne(false,(int)$this->user_id);

        $this->_email = WYSIJA::get('email', 'model')->getOne(false,(int)$this->email_id);
        if (empty($this->_email))
            exit;

        // consider to get / insert URL record here
        // $this->_url = $this->_get_url($this->clicked_url);
    }

    /**
     * Get user object
     * @param type $user_id
     */
    public function get_user() {
        return $this->_user;
    }

    /**
     * Get email object
     * @param type $user_id
     */
    public function get_email() {
        return $this->_email;
    }

    /**
     * count the action as an open and display the empty picture
     */
    public function subscriber_opened() {
        if (!$this->_is_open_action())
            exit;

        $model_email_user_stat = WYSIJA::get('email_user_stat', 'model');
        $model_email_user_stat->reset();

        // update status to 1 and set opened_at time
        // only if the status = 0
        $model_email_user_stat->update(
            array('status' => 1, 'opened_at' => time()),
            array('email_id' => $this->email_id, 'user_id' => $this->user_id, 'status' => 0)
        );

        $this->_update_user(array('last_opened' => time()));

        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');

        if (empty($picture))
            $picture = WYSIJA_DIR_IMG . 'statpicture.png';
        $handle = fopen($picture, 'r');

        if (!$handle)
            exit;
        header('Content-type: image/png');
        $contents = fread($handle, filesize($picture));
        fclose($handle);
        echo $contents;
        exit;
    }

    public function subscriber_clicked() {
        if (!$this->_is_click_action())
            exit;

        if ($this->email_id && $this->is_preview === false) { //if not email_id that means it is an email preview
            $this->_record_url();

            $this->_record_user_url();

            $this->_record_url_mail();

            $redirect = $this->_record_email_user_stat();
            if (!empty($redirect))
                $this->decoded_url = $redirect;
            $this->_update_user(array('last_clicked' => time()));
        }else {
            $this->_get_url_preview();
        }
        //sometimes this will be a life saver :)
        $this->decoded_url = str_replace('&amp;', '&', $this->decoded_url);
        if ($this->is_browser_link($this->decoded_url)) {
            $this->decoded_url = $this->attach_user($this->decoded_url);
        }
        return $this->decoded_url;
    }

    /**
     * Attach user_id as a param of an url
     * @param string $url
     * @param int $user_id
     * @return string
     */
    protected function attach_user($url) {
	if (!empty($this->_user) && !empty($this->_user['user_id'])) {
            $url_components = parse_url($url);
            $arr_params = array();
            if (!empty($url_components['query']))
                parse_str($url_components['query'],$arr_params);
            if (empty($arr_params['user_id'])) {
                $url .= ((strpos($url, '?') !== false) ? '&' : '?');
                $url .= 'user_id='.$this->_user['user_id'];
            }
        }
	return $url;
    }

    /**
     * record entry into the table email_user_url
     */
    private function _record_user_url() {
        //look for email_user_url entry and insert if not exists
        $model_email_user_url = WYSIJA::get('email_user_url', 'model');
        $data_email_user_url = array('email_id' => $this->email_id, 'user_id' => $this->user_id, 'url_id' => $this->_url_id);
        $email_user_url_array = $model_email_user_url->getOne(false, $data_email_user_url);

        if (!$email_user_url_array && $this->email_id > 0 && $this->user_id > 0 && $this->_url_id > 0) {
            //we need to insert in email_user_url
            $model_email_user_url->reset();
            $query_EmailUserUrl = 'INSERT IGNORE INTO [wysija]email_user_url (`email_id` ,`user_id`,`url_id`) ';
            $query_EmailUserUrl .= 'VALUES (' . $this->email_id . ', ' . $this->user_id . ', ' . $this->_url_id . ')';

            $model_email_user_url->query($query_EmailUserUrl);

            //$modelEmailUserUrl->insert($dataEmailUserUrl);
            $this->_unique_click = true;
        }

        //increment stats counter on email_user_url clicked
        $model_email_user_url = WYSIJA::get('email_user_url', 'model');
        $model_email_user_url->update(array('clicked_at' => time(), 'number_clicked' => '[increment]'), $data_email_user_url);
    }

    /**
     * record entry into the table url_mail
     */
    private function _record_url_mail() {
        //look for url_mail entry and insert if not exists
        $model_url_mail = WYSIJA::get('url_mail', 'model');
        $data_url_mail = array('email_id' => $this->email_id, 'url_id' => $this->_url_id);
        $urlMailObj = $model_url_mail->getOne(false, $data_url_mail);
        if (!$urlMailObj) {
            //we need to insert in url_mail
            $model_url_mail->reset();
            $model_url_mail->insert($data_url_mail);
        }

        $data_update = array('total_clicked' => '[increment]');
        if (!$this->_unique_click)
            $data_update['unique_clicked'] = '[increment]';
        //increment stats counter on url_mail clicked
        $model_url_mail->update($data_update, $data_url_mail);
    }

    /**
     * record entry into the table email_user_stat
     */
    private function _record_email_user_stat() {
        // clicked status
        $status_email_user_stat = 2;

        // this is the system url case (unsubscribe, view in browser and subscriptions)
        if (in_array($this->clicked_url, array('[unsubscribe_link]', '[subscriptions_link]', '[view_in_browser_link]'))) {
            $this->subscriberClass = WYSIJA::get('user', 'model');
            $this->subscriberClass->getFormat = OBJECT;

            //check if the security hash is passed to insure privacy
            $receiver = $link = false;
            if (isset($_REQUEST['hash'])) {
                if ($_REQUEST['hash'] == md5(AUTH_KEY . $this->clicked_url . $this->user_id)) {
                    $receiver = $this->subscriberClass->getOne(array('user_id' => $this->user_id));
                } else {
                    die('Security check failure.');
                }
            } else {
                //link is not valid anymore
                //propose to resend the newsletter with good links ?
                $link = $this->subscriberClass->old_get_new_link_for_expired_links($this->user_id, $this->email_id);
            }


            switch ($this->clicked_url) {
                case '[unsubscribe_link]':
                    // we need to make sure that this link belongs to that user
                    if ($receiver) {
                        $link = $this->subscriberClass->getUnsubLink($receiver, true);
                        // unsubscribe status
                        $status_email_user_stat = 3;
                    }
                    break;
                case '[subscriptions_link]':
                    if ($receiver) {
                        $link = $this->subscriberClass->getEditsubLink($receiver, true);
                    }
                    break;
                case '[view_in_browser_link]':
                    $model_email = WYSIJA::get('email', 'model');
                    $data_email = $model_email->getOne(false, array('email_id' => $this->email_id));
                    $helper_email = WYSIJA::get('email', 'helper');
                    $link = $helper_email->getVIB($data_email);
                    break;
            }

            //if the subscriber already exists in the DB we will have a link
            if ($link) {
                $this->decoded_url = $link;
            } else {
                //the subscriber doesn't appear in the DB we can redirect to the web version
                $this->decoded_url = $this->_get_browser_link();

                return $this->decoded_url;
            }
        } else {
            // this is the standard non-system url case
            if (strpos($this->decoded_url, 'http://') === false && strpos($this->decoded_url, 'https://') === false) {
                $this->decoded_url = 'http://' . $this->decoded_url;
            }

            // check that there is no broken unsubscribe link such as http://[unsubscribe_link]
            if (strpos($this->decoded_url, '[unsubscribe_link]') !== false) {
                $this->subscriberClass = WYSIJA::get('user', 'model');
                $this->subscriberClass->getFormat = OBJECT;
                $receiver = $this->subscriberClass->getOne($this->user_id);
                $this->decoded_url = $this->subscriberClass->getUnsubLink($receiver, true);
            }

            if (strpos($this->decoded_url, '[view_in_browser_link]') !== false) {
                $link = $this->_get_browser_link();
                $this->decoded_url = $link;
            }
        }

        $data_update = array();

        // check if we already have a record
        $model_email_user_stat = WYSIJA::get('email_user_stat', 'model');
        $exists = $model_email_user_stat->getOne(false, array('equal' => array('email_id' => $this->email_id, 'user_id' => $this->user_id), 'less' => array('status' => $status_email_user_stat)));

        // fix "opened_at" value in case the "opened" status was not properly recorded (blocked images)
        if(is_array($exists) && array_key_exists('opened_at', $exists) && (int)$exists['opened_at'] === 0) {
            // set opened at in case it was not recorded
            $data_update['opened_at'] = time();
        }

        $model_email_user_stat->reset();
        $model_email_user_stat->colCheck = false;

        // set new status
        $data_update['status'] = $status_email_user_stat;

        // update email user stat
        $model_email_user_stat->update($data_update, array('equal' => array('email_id' => $this->email_id, 'user_id' => $this->user_id), 'less' => array('status' => $status_email_user_stat)));
    }

    /**
     * update the user last_clicked or last_opened parameter
     * @param type $data_update
     */
    private function _update_user($data_update) {
        if (!empty($data_update)) {
            $model_user = WYSIJA::get('user', 'model');
            $model_user->update($data_update, array('user_id' => $this->user_id));
        }
    }

    /**
     * when the email sent is a preview email, we just need to emulate actions, no need to count stats or to unsubscribe user etc ...
     * @return boolean
     */
    private function _get_url_preview() {
        // we're in the case of an email preview
        if (in_array($this->clicked_url, array('[unsubscribe_link]', '[subscriptions_link]', '[view_in_browser_link]'))) {
            $model_user = WYSIJA::get('user', 'model');
            $model_user->getFormat = OBJECT;
            $user_object = $model_user->getOne(false, array('wpuser_id' => get_current_user_id()));
            switch ($this->clicked_url) {
                case '[unsubscribe_link]':
                    $link = $model_user->getConfirmLink($user_object, 'unsubscribe', false, true) . '&demo=1';

                    break;
                case '[subscriptions_link]':
                    $link = $model_user->getConfirmLink($user_object, 'subscriptions', false, true) . '&demo=1';
                    break;
                case 'view_in_browser_link':
                case '[view_in_browser_link]':
                    if (!$this->email_id)
                        $this->email_id = $_REQUEST['id'];

                    $link = $this->_get_browser_link();
                    break;
            }

            $this->decoded_url = $link;
        }else {
            if (strpos($this->decoded_url, 'http://') === false && strpos($this->decoded_url, 'https://') === false)
                $this->decoded_url = 'http://' . $this->decoded_url;
        }
        return true;
    }

    /**
     * construct the view in your browser link
     * @return type
     */
    private function _get_browser_link() {
        $params_url = array(
            'wysija-page' => 1,
            'controller' => 'email',
            'action' => 'view',
            'email_id' => $this->email_id,
            'user_id' => 0
        );
        $config = WYSIJA::get('config', 'model');
        return WYSIJA::get_permalink($config->getValue('confirm_email_link'), $params_url);
    }

    /**
     * Detect if the current link is a browser link
     * @param string $url
     * @return boolean
     */
    protected function is_browser_link($url) {
        $flag = false;
        $helper_toolbox = WYSIJA::get('toolbox', 'helper');
        if ($helper_toolbox->is_internal_link($url)) {
            $url_components = parse_url($url);
            if (!empty($url_components['query'])) {
                $params = array();
                parse_str($url_components['query'], $params);
                if (!empty($params['controller']) && strtolower($params['controller']) == 'email'
                        && !empty($params['action']) && strtolower($params['action']) == 'view'
                        ) {
                    $flag = true;
                }
            }
        }
        return $flag;
    }

    /**
     * check that this is a valid open action
     * @return boolean
     */
    private function _is_open_action() {
        if (empty($this->email_id) || empty($this->user_id))
            return false;

        return true;
    }

    /**
     * check that this is a valid click action
     * @return boolean
     */
    private function _is_click_action() {
        if ((empty($this->email_id) || empty($this->user_id) || empty($this->clicked_url)) && $this->is_preview === false)
            return false;

        return true;
    }

    /**
     * record the url into the db if not recorded already
     * @return boolean
     */
    private function _record_url() {
        //look for url entry and insert if not exists
        $model_url = WYSIJA::get('url', 'model');
        $url_found = $model_url->getOne(false, array('url' => $this->clicked_url));

        if ($url_found) {
            // we need to keep it
            $this->_url_id = $url_found['url_id'];
        } else {
            // we need to record in database
            $this->_url_id = $model_url->insert(array('url' => $this->clicked_url));
        }
    }

    /**
     * get/alter clicked url based on the global parameters
     */
    private function _get_clicked_url() {

        if (isset($_REQUEST['urlencoded'])) {
            $this->clicked_url = $_REQUEST['urlencoded'];
        } elseif (isset($_REQUEST['urlpassed'])) {
            $this->clicked_url = $_REQUEST['urlpassed'];
        }

        // make sure the url is or is not base64 encoded, some server cannot handle long url or url with encoded parameter which is the default behaviour
        if (isset($_REQUEST['no64'])) {
            $this->decoded_url = $this->clicked_url;
        } else {
            $this->clicked_url = $this->decoded_url = base64_decode($this->clicked_url);
        }

        if (strpos($this->clicked_url, 'utm_source') !== false) {
            $this->clicked_url = $this->_clean_params_url(array('utm_source', 'utm_campaign', 'utm_medium'), $this->clicked_url);
        }
        return true;
    }

    /**
     * remove any params from a url
     * @param array $params_to_remove
     * @param string $url
     * @return string
     */
    private function _clean_params_url($params_to_remove = array(), $url = '') {
        if (!$url){
            return $url;
        }

        $url_splitted = explode('?', $url);
        $params_in_url = array();

        // lets parse the parameters of the url
        parse_str($url_splitted[1], $params_in_url);

        foreach ($params_to_remove as $param_key){
            unset($params_in_url[$param_key]);
        }

        // let's use the base of that url to rebuild it without the cleaned out parameters.
        $new_url = $url_splitted[0];

        // when there are params left other than the ones we cleaned out of the url, we stick them back together
        if($params_in_url) {
            $new_url .= '?';
            $i = 0;
            foreach ($params_in_url as $k => $v) {
                if ($i > 0){
                    $new_url .= '&';
                }

                // parse_str keeps the ampersand as a html number, let's clean it out so that the url look good when recorded
                $new_url .= str_replace($k,'&#38;','') . '=' . $v;
                $i++;
            }
        }

        return $new_url;
    }

}