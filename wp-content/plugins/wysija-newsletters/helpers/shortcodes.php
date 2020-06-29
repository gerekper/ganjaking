<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_help_shortcodes extends WYSIJA_object {

    // Email object.
    private $email;
    // Receiver object.
    private $receiver;
    // User model.
    private $userM;
    // Shortcodes found.
    private $find;
    // Replacement values for shortcodes found.
    private $replace;

    function __construct(){
        parent::__construct();
    }

    /**
     * This serves for custom: shortcodes only
     * @return type
     */
    function getReceiver() {
        return $this->receiver;
    }

    // Main function. Call it assigning an Email object and a Receiver object.
    private function initialize($email, $receiver) {

        // Set current object properties.
        $this->email = $email;
        $this->receiver = $receiver;
        $this->userM = WYSIJA::get('user','model');
        $this->userM->getFormat = OBJECT;
        $this->find = array();
        $this->replace = array();

    }

    public function replace_body($email, $receiver = NULL) {

        $this->initialize($email, $receiver);

        $body_tags = $this->email->tags;
        $this->loop_tags($body_tags);
        $replaced_body = str_replace($this->find, $this->replace, $this->email->body);
        return $replaced_body;

    }

    public function replace_subject($email, $receiver = NULL) {

        $this->initialize($email, $receiver);

        $subject_tags = $this->email->subject_tags;
        $this->loop_tags($subject_tags);
        $replaced_subject = str_replace($this->find, $this->replace, $this->email->subject);
        return $replaced_subject;

    }


    /**
     * Loop through tags and fetch data (replacement) of each tag
     * @param array $tags
     * [[user:displayname | default:myvalue]] => Array <br/>
        (<br/>
            [0] => Array<br/>
                (<br/>
                    [0] => user<br/>
                    [1] => displayname<br/>
                )<br/>
            [1] => Array<br/>
                (<br/>
                    [0] => default<br/>
                    [1] => myvalue<br/>
                )<br/>
        )<br/>
     */
    private function loop_tags(Array $tags) {
        $this->find = array();
        $this->replace = array();

        // Loop through the shortcodes array and call private group functions.
        foreach($tags as $tag_find => $tag_replace){
            foreach($tag_replace as $couple_value){
                $couple_value[0] = strip_tags($couple_value[0]);// strip html tags
                $couple_value[1] = strip_tags($couple_value[1]);// strip html tags
                switch ($couple_value[0]) {

                    // [user:xxx | default:xxx]
                    case 'user':
                        $replacement = $this->replace_user_shortcodes($couple_value[1]);
                        // "subscriber" or "member" means: we don't find out a right value for the tag.
			// The next loop should be the tag "default".
			// Let's "continue" and get the default value instead.
                        if ($replacement === 'subscriber' || $replacement === 'member') {
                            continue;
                        }
                        break(2);
                    case 'default':
                        $replacement = $couple_value[1];
                        break(2);

                    // [newsletter:xxx]
                    case 'newsletter':
                        $replacement = $this->replace_newsletter_shortcodes($couple_value[1]);
                        break;

                    // [date:xxx]
                    case 'date':
                        $replacement = $this->replace_date_shortcodes($couple_value[1]);
                        break;

                    // [global:xxx]
                    case 'global':
                        $replacement = $this->replace_global_shortcodes($couple_value[1]);
                        break;

                    // [custom:xxx]
                    case 'custom':
                        $replacement = $this->replace_custom_shortcodes($couple_value[1]);
                        break;

                    // [field:xxx]
                    case 'field':
                      $replacement = $this->replace_cfield_shortcodes($couple_value[1]);
                      break;

                    default:
                        break;
                }
            }

            if (isset($replacement)){
                $this->find[] = $tag_find;
                $this->replace[] = $replacement;
            } else {
                $this->find[] = $tag_find;
                $this->replace[] = do_shortcode($tag_find);
            }
            $replacement = '';

        }

    }

    // [user:firstname]
    // [user:lastname]
    // [user:email]
    // [user:displayname]
    // [user:count]
    private function replace_user_shortcodes($tag_value) {
        $replacement = '';
        if (($tag_value === 'firstname') || ($tag_value === 'lastname') || ($tag_value === 'email')) {
            if(isset($this->receiver->$tag_value) && $this->receiver->$tag_value) {
                // uppercase the initials of the first name and last name when replacing it
                if (($tag_value === 'firstname') || ($tag_value === 'lastname')){
		    $use_default_case = apply_filters('mpoet_shortcode_names_default_case', false);
		    if ($use_default_case) {
			$replacement = $this->receiver->$tag_value;
		    } else {
			if (function_exists('mb_convert_case') && function_exists('mb_strtolower')) {
			    // http://stackoverflow.com/questions/9823703/using-ucwords-for-non-english-characters
			    $replacement = mb_convert_case(mb_strtolower($this->receiver->$tag_value),MB_CASE_TITLE, 'UTF-8');
			} else {
			    $replacement = ucwords(strtolower($this->receiver->$tag_value));
			}
		    }
                }else{
                    $replacement = $this->receiver->$tag_value;
                }
             } else {
                $replacement = 'subscriber';
             }
        }

        if ($tag_value === 'displayname') {
            $replacement = 'member';
            if(!empty($this->receiver->wpuser_id))
            {
                $user_info = get_userdata($this->receiver->wpuser_id);
                if(!empty($user_info->display_name) && $user_info->display_name != false) {
                    $replacement = $user_info->display_name;
                 } elseif(!empty($user_info->user_nicename) && $user_info->user_nicename != false) {
                    $replacement = $user_info->user_nicename;
                }
            }
        }
        if ($tag_value === 'count') {
            $model_config = WYSIJA::get('config', 'model');
            $replacement = $model_config->getValue('total_subscribers');
        }

        return $replacement;

    }

    // [global:unsubscribe]
    // [global:manage]
    // [global:browser]
    private function replace_global_shortcodes($tag_value) {
        $replacement = '';
        if (($tag_value === 'unsubscribe')) {
            $replacement = $this->userM->getUnsubLink($this->receiver);
        }

        if ($tag_value === 'manage') {
            $replacement = $this->userM->getEditsubLink($this->receiver);
        }

        if ($tag_value === 'browser') {
            $replacement = '';
        }

        return $replacement;

    }

    // [newsletter:subject]
    // [newsletter:total]
    // [newsletter:post_title]
    // [newsletter:number]
    private function replace_newsletter_shortcodes($tag_value) {
        $replacement = '';
        switch ($tag_value) {
            case 'subject':
                $replacement = $this->email->subject;
                break;

            case 'total':
                if(isset($this->email->params['autonl']['articles']['count'])) {
                    $replacement = $this->email->params['autonl']['articles']['count'];
                }
                break;

            case 'post_title':
                if(isset($this->email->params['autonl']['articles']['first_subject'])) {
                    $replacement = $this->email->params['autonl']['articles']['first_subject'];
                }
                break;

            case 'number':
                // number is the issue number not the number of articles that were sent since the beginning.
                $replacement = (isset($this->email->params['autonl']['total_child']) ? (int)$this->email->params['autonl']['total_child'] : 1);
                break;

            default:
                $replacement = '';
                break;
        }

        return $replacement;

    }

    // [date:d]
    // [date:m]
    // [date:y]
    // [date:dtext]
    // [date:mtext]
    // [date:dordinal]
    private function replace_date_shortcodes($tag_value) {

        $current_time = current_time('timestamp');

        switch ($tag_value) {
            case 'd':
                $replacement = date_i18n( 'j', $current_time);
                break;

            case 'm':
                $replacement = date_i18n( 'n', $current_time);
                break;

            case 'y':
                $replacement = date_i18n( 'Y', $current_time);
                break;

            case 'dtext':
                $replacement = date_i18n( 'l', $current_time);
                break;

            case 'mtext':
                $replacement = date_i18n( 'F', $current_time);
                break;

            case 'dordinal':
                $replacement = date_i18n( 'jS', $current_time);
                break;

            default:
                $replacement = '';
                break;
        }

        return $replacement;

    }

    /**
     * We pass the value of the tag, the string after custom:
     * To the external filter, and we expect the filter to return a string.
     */
    // [custom:xxx]
    private function replace_custom_shortcodes($tag_value) {
        $user_id = 0;

        if (!empty($this->receiver) && isset($this->receiver->user_id)) {
            $user_id = (int) $this->receiver->user_id;
        }

        return apply_filters('wysija_shortcodes', $tag_value, $user_id);
    }

    // [field:1]
    private function replace_cfield_shortcodes($tag_value) {
        $replacement = '';
        if (isset($tag_value)) {
          $user_id = (int) $this->receiver->user_id;
          $field_id = (int) $tag_value;
          $field_user = new WJ_FieldUser();
          $field_user->set(array(
            'user_id' =>  $user_id,
            'field_id' => $field_id
          ));
          $value = $field_user->value();
          // If we don't have a value, we return the empty string.
          if (isset($value)) {
            // Check if the field value needs formatting output.
            switch ($field_user->field->type) {
              case 'checkbox':
                if ($value == 1) {
                  $value = "Yes";
                } else {
                  $value ="No";
                }
                break;
              case 'date':
                $value = date('F j, Y', $value);
                break;
              default:
                break;
            }
            $replacement = $value;
          } else {
            $replacement = '';
          }
        }
        return $replacement;
    }

}
