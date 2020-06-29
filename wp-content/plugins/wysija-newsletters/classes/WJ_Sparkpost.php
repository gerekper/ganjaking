<?php
defined('WYSIJA') or die('Restricted access');

class WJ_Sparkpost {
  public $error;
  private $api_key = '';

  public function __construct($api_key) {
    $this->api_key = $api_key;
  }

  public function send_mail(& $object) {
    $msg = array(
      'recipients' => array(
        array(
          'address' => array(
            'email' => $object->to[0][0]
          )
        )
      ),
      'content' => array(
        'from' => array(
          'name' => $object->FromName,
          'email' => $object->From
        ),
        'reply_to' => $object->ReplyTo[0][0]
      )
    );

    // set the subject
    if(!empty ($object->Subject)) $msg['content']['subject'] = $object->Subject;

    // set the body
    if(!empty ($object->sendHTML) || !empty($object->AltBody)) {
      $msg['content']['html'] = $object->Body;
      if(!empty ($object->AltBody)) $msg['content']['text'] = $object->AltBody;
    } else {
      $msg['content']['text'] = $object->Body;
    }

    $url = 'https://api.sparkpost.com/api/v1/transmissions';

    return $this->run($url, $msg);

  }

  protected function run($url, $msg) {
    $return = null;
    $params = array(
      'headers' => array(
        'Content-Type: application/json',
        'Authorization' => $this->api_key
      ),
      'body' => json_encode($msg)
    );

    $result = null;
    $result = wp_remote_post($url, $params);
    try {
      if(!is_wp_error($result)) {
        switch($result['response']['code']) {
          case 200:
            $return = true;
            break;
          default:
            $body = json_decode($result['body'], true);
            if(isset($body['errors']) && isset($body['errors'][0]) && isset($body['errors'][0]['description'])) {
              $this->error = $body['errors'][0]['description'];
            } else {
              $this->error = $result['response']['message'];
            }
            break;
        }
      } else {
        $this->error = (is_wp_error($result)) ?
          $result->get_error_messages() :
          __('We were unable to contact the API, the site may be down. Please try again later.', WYSIJA);
      }
    } catch(Exception $e) {
      $this->error = 'Unexpected error: ' . $e->getMessage() . ' [' . var_export($result, true) . ']';// do nothing
    }

    return $return;
  }

}
