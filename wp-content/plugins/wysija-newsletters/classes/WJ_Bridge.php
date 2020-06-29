<?php
defined('WYSIJA') or die('Restricted access');

class WJ_Bridge {
  public $error;
  private $api_key = '';
  public function __construct($api_key) {
    $this->api_key = $api_key;
  }

  public function send_mail(& $object)
  {
    $replyToKey = key($object->ReplyTo);

    $msg = array(
      'to' => array(
        'name' => '' ,
        'address' => $object->to[0][0] ),
      'reply_to'=> array(
        'name' => '' ,
        'address' => $object->ReplyTo[$replyToKey][0]
      ),
      'from' => array(
        'name' => $object->FromName ,
        'address' => $object->From ),
    );

    // set the subject
    if (!empty ($object->Subject)) $msg['subject']= $object->Subject;

    // set the body
    if (!empty ($object->sendHTML) || !empty($object->AltBody)){
      $msg['html']= $object->Body;
      if (!empty ($object->AltBody)) $msg['text']=$object->AltBody;
    }else{
      $msg['text']=$object->Body;
    }

    if(!empty($object->to[0][1])) $msg['to']['name'] = $object->to[0][1];

    if(!empty($object->ReplyTo[0][1])) $msg['reply_to']['name']= $object->ReplyTo[0][1];

    $url = 'https://bridge.mailpoet.com/api/messages';

    $args = array( $msg );

    return $this->run( $url, $args);

  }

  protected function run($url, $args)
  {
    $return = null;
    $data   = json_encode($args);

    $params = array(
      'headers' => array(
        'Content-Type: application/json',
        'Authorization' => 'Basic ' . base64_encode('api:' . $this->api_key )
      ),
      'body'  => $data
    );

    $result = null;
    $result = wp_remote_post($url, $params);
    try {
      if (!is_wp_error($result) && in_array( (int)$result['response']['code'], array( 201, 400, 401) ) )
      {
        switch( $result['response']['code'] ){
        case 201:
          $return = true;
        break;
        case 400:
          $this->error = 'Bad input';
        break;
        case 401:
          $this->error = 'Not Authorized';
        break;
        }
      }
      else if (is_wp_error($result)) {
        $this->error = $result->get_error_messages();
      }
    } catch(Exception $e) {
      $this->error = 'Unexpected error: '.$e->getMessage() . ' ['.var_export($result, true).']';// do nothing
    }

    return $return;
  }

}
