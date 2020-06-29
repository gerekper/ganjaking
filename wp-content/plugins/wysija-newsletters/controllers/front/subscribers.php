<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_control_front_subscribers extends WYSIJA_control_front{
    var $model='user';
    var $view='widget_nl';

    function __construct(){
        parent::__construct();
        if(isset($_REQUEST['message_success'])){
            $this->messages['insert'][true]=$_REQUEST['message_success'];
        }else{
            $this->messages['insert'][true]=__('User has been inserted.',WYSIJA);
        }

        $this->messages['insert'][false]=__('User has not been inserted.',WYSIJA);
        $this->messages['update'][true]=__('User has been updated.',WYSIJA);
        $this->messages['update'][false]=__('User has not been updated.',WYSIJA);
    }

   function save(){
        $config=WYSIJA::get('config','model');

        if(!$config->getValue('allow_no_js')){
            $this->notice(__('Subscription without JavaScript is disabled.',WYSIJA));
            return false;
        }

        if(isset($_REQUEST['wysija']['user_list']['list_id'])){
            $_REQUEST['wysija']['user_list']['list_ids']=$_REQUEST['wysija']['user_list']['list_id'];
            unset($_REQUEST['wysija']['user_list']['list_id']);
        }elseif(isset($_REQUEST['wysija']['user_list']['list_ids'])){
            $_REQUEST['wysija']['user_list']['list_ids']=explode(',',$_REQUEST['wysija']['user_list']['list_ids']);
        }
        $_REQUEST['wysija']['user_field'] = $_REQUEST['wysija']['field'];
        $data=$_REQUEST['wysija'];
        unset($_REQUEST['wysija']);

        foreach($_REQUEST as $key => $val){
            if(!isset($data[$key]))  $data[$key]=$val;
        }

        $helperUser=WYSIJA::get('user','helper');
        if(!$helperUser->checkData($data))return false;
        if(!$helperUser->verifyCaptcha($data))return false;
        if(!$helperUser->throttleRepeatedSubscriptions()) return false;

        if($helperUser->addSubscriber($data)) {
          $helperUser->storeSubscriberIP();
        }

        return true;
    }

    /**
     * handles the form generation in iframe mode, basically wysija's iframes call that action to generate the html of the body
     */
    function wysija_outter() {

        //params used to generate the html in the widget class
        $widget_data=array();

        if(isset($_REQUEST['wysija_form']) && (int)$_REQUEST['wysija_form'] > 0) {
            // this a wysija form made with the form editor
            // if it's a preview, we need to dynamically render the form
            // get form data

            $widget_data['form']=(int)$_REQUEST['wysija_form'];
            $widget_data['form_type']='iframe';

        } else {

            //this is the old way, we need to keep it for backward compatibility
            if(isset($_REQUEST['encodedForm'])){
                $encoded_form=json_decode(base64_decode(urldecode($_REQUEST['encodedForm'])));
            } else {
                if(isset($_REQUEST['fullWysijaForm'])){
                    $encoded_form=json_decode(base64_decode(urldecode($_REQUEST['fullWysijaForm'])));
                } else {
                    if(isset($_REQUEST['widgetnumber'])){

                        $widgets=get_option('widget_wysija');
                        if(isset($widgets[$_REQUEST['widgetnumber']])){
                            $encoded_form=$widgets[$_REQUEST['widgetnumber']];
                        }

                    }else{
                        $encoded_form=$_REQUEST['formArray'];
                        $encoded_form=stripslashes_deep($encoded_form);
                    }

                }
            }


            //fill the widget data array based on the parameters found earlier
            if($encoded_form){
                foreach($encoded_form as $key =>$val) {
                    if (in_array($key, array('before_widget', 'after_widget', 'before_title', 'title', 'after_title'))) {
                      $val = sanitize_text_field($val);
                    }
                    $widget_data[$key]=$val;

                    //if the value is an object we need to loop through and make an array of it
                    //I think we could simply cast the object as an array not sure if that works on objects within objects...
                    if(is_object($val)){
                        $object_to_array=array();
                        foreach($val as $key_in =>$val_in){
                            $object_to_array[$key_in]=$val_in;
                            if(is_object($val_in)){
                                $object_to_array_second_level=array();
                                foreach($val_in as $k_in => $v_in){
                                    $object_to_array_second_level[$k_in]=$v_in;
                                }
                                $object_to_array[$key_in]=$object_to_array_second_level;
                            }
                        }
                        $widget_data[$key]=$object_to_array;
                    }
                }
            }else{
                if(current_user_can('switch_themes'))    echo '<b>'.str_replace(array('[link]','[/link]'),array('<a target="_blank" href="'.  admin_url('widgets.php').'">','</a>'),__('It seems your widget has been deleted from the WordPress\' [link]widgets area[/link].',WYSIJA)).'</b>';
                exit;
            }

            //create a unique identifier for the form (the old way)
            if(isset($_REQUEST['widgetnumber']))  $form_identifier=$_REQUEST['widgetnumber'];
            else $form_identifier=rand(5, 1500);
            $widget_data['widget_id']='wysija-nl-iframe-'.$form_identifier;
        }


        require_once(WYSIJA_WIDGETS.'wysija_nl.php');
        $widget_NL=new WYSIJA_NL_Widget(true);
        $widget_NL->iFrame=true;
        $subscription_form = $widget_NL->widget($widget_data,$widget_data);
        $subscription_form = str_replace("</head>", '<script type="text/javascript">
            /* <![CDATA[ */
            var wysijaAJAX = {"action":"wysija_ajax","controller":"subscribers","ajaxurl":"'.admin_url('admin-ajax.php','absolute').'","loadingTrans":"'.__('Loading...',WYSIJA).'"};
            /* ]]> */
            </script></head>', $subscription_form);
        echo $subscription_form;
        exit;
    }
}