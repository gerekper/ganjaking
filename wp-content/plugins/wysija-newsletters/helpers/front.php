<?php
defined('WYSIJA') or die('Restricted access');
/**
 * class managing the admin vital part to integrate
 */
class WYSIJA_help_front extends WYSIJA_help{

    function __construct(){
        parent::__construct();

        // wysija form shortcode
        add_shortcode('wysija_form', array($this,'scan_form_shortcode'));
        // wysija total of subscribers shortcode
        add_shortcode('wysija_subscribers_count', array($this,'scan_subscribers_count_shortcode'));

        // init shortcode [wysija_archive]
        require_once(WYSIJA_CORE.'controller.php');
        require_once(WYSIJA_CORE.'module'.DS.'module.php');// @todo: move to autoloader
        $archive_std = WYSIJA_module::get_instance_by_name('archive_std');// implement hook "wysija_front_init()
        if (!empty($archive_std) && is_a($archive_std, 'WYSIJA_module'))
            $archive_std->front_init();

        /* We try to process the least possible code */
        if(isset($_REQUEST['wysija-page']) || isset($_REQUEST['wysija-launch'])){

            if(defined('DOING_AJAX')){
                add_action('wp_ajax_nopriv_wysija_ajax', array($this, 'ajax'));
            }else{
                $paramscontroller=$_REQUEST['controller'];
                //this is an exception on one server this params stats was not accepted
                if($paramscontroller=='stat') $paramscontroller='stats';

                $this->controller=WYSIJA::get($paramscontroller,'controller');
                if(isset($_REQUEST['action']) && method_exists($this->controller, $_REQUEST['action'])){
                    add_action('init',array($this->controller,$_REQUEST['action']));
                    //$this->controller->$_REQUEST['action']();
                }else $this->error('Action does not exist.');

                if(isset($_REQUEST['wysija-page'])){
                    /* set the content filter to replace the shortcode */
                    add_filter('wp_title', array($this,'meta_page_title'));
                    add_filter( 'the_title', array($this,'scan_title'));
                    add_filter( 'the_content', array($this,'scan_content'),98);
                    if(isset($_REQUEST['message_success'])){
                        add_filter( 'the_content', array($this,'scan_content_NLform'),99 );
                    }
                }
            }
        }else{
            add_filter('the_content', array($this,'scan_content_NLform'),99 );
           //if the comment form checkbox option is activated we add some hooks to process it
           $model_config = WYSIJA::get('config','model');
           if($model_config->getValue('commentform')){
                add_action('comment_form', array($this,'comment_form_extend'));
                add_action('comment_post',  array($this,'comment_posted'), 60,2);
           }

           // if the register form checkbox option is activated we add some hooks to process it
           if($model_config->getValue('registerform')){
               if(is_multisite()){
                   add_action('signup_extra_fields', array($this,'register_form_extend'));
                   // we need this condition otherwise we will send two confirmation emails when on ms with buddypress
                    if(!WYSIJA::is_plugin_active('buddypress/bp-loader.php')){
                        add_filter('wpmu_validate_user_signup',  array($this,'registerms_posted'), 60,3);
                    }
               }else{
                   add_action('register_form', array($this,'register_form_extend'));
                   add_action('register_post',  array($this,'register_posted'), 60,3);
               }

               // special case when buddypress is activated
               if(WYSIJA::is_plugin_active('buddypress/bp-loader.php')){
                    add_action('bp_after_signup_profile_fields', array($this,'register_form_bp_extend'));
                    add_action('bp_signup_validate', array($this,'register_bp'),60,3);

                    // we can have just one confirmation email for the wp user and the wysija confirmation when bp and multisite are activated
                    if(is_multisite()){
                        add_action('wpmu_activate_user', array($this,'wpmu_activate_user'));
                    }
                }
           }
        }
    }

    /**
     * In MS when user account is activated we auto confirm the subscriber
     * @param type $wpuser_id
     * @return boolean
     */
    function wpmu_activate_user($wpuser_id){
        if((int)$wpuser_id>0){
            $model_user = WYSIJA::get('user','model');
            $result_subscriber = $model_user->getOne(false , array('wpuser_id'=>$wpuser_id));

            if(!empty($result_subscriber)){
                $helper_user = WYSIJA::get('user','helper');
                $helper_user->confirm_user($result_subscriber['user_id']);
            }
        }
        return true;
    }

    function meta_page_title($title){
        //Here I can echo the result and see that it's actually triggered
        if($this->is_default_mailpoet_page()){
            // when user have kept the default page we modify the title based on the controller
            if(!empty($this->controller->title)) return $this->controller->title;
        }

        return $title;
    }



    function register_form_bp_extend(){
        if ( !is_user_logged_in()){
            $this->register_form_extend();
        }
    }

    function register_form_extend(){
        $checkbox= '<p class="wysija-after-register">';
        $checkbox.='<label for="wysija-box-after-register">';
        $checkbox.='<input type="checkbox" id="wysija-box-after-register" value="1" name="wysija[register_subscribe]">';
        $mConfig=WYSIJA::get('config','model');
        $checkbox.=$mConfig->getValue('registerform_linkname').'</label></p>';

        echo '<div class="register-section" id="profile-details-section-wysija"><div class="editfield">'.$checkbox.'</div></div>';
    }


    function register_bp(){
        global $bp;

        if ( !isset($bp->signup->errors) && isset($_POST['wysija']['register_subscribe']) && $_POST['wysija']['register_subscribe'] ) {
            $model_config=WYSIJA::get('config','model');
            $helper_user=WYSIJA::get('user','helper');
            $data=array('user'=>array('email'=>$bp->signup->email),'user_list'=>array('list_ids'=>$model_config->getValue('registerform_lists')));

            if(is_multisite()){
                $helper_user->no_confirmation_email=true;
            }

            $helper_user->addSubscriber($data);
        }
    }

    function registerms_posted($result){
        if ( empty($result['errors']->errors) && isset($_POST['wysija']['register_subscribe']) && $_POST['wysija']['register_subscribe']) {
            $mConfig=WYSIJA::get('config','model');
            $userHelper=WYSIJA::get('user','helper');
            $data=array('user'=>array('email'=>$result['user_email']),'user_list'=>array('list_ids'=>$mConfig->getValue('registerform_lists')));
            $userHelper->addSubscriber($data);
        }

        return $result;
    }

    function register_posted($login,$email,$errors){

        if ( empty($errors->errors) && isset($_POST['wysija']['register_subscribe']) && $_POST['wysija']['register_subscribe']) {
            $mConfig=WYSIJA::get('config','model');
            $userHelper=WYSIJA::get('user','helper');
            $data=array('user'=>array('email'=>$email),'user_list'=>array('list_ids'=>$mConfig->getValue('registerform_lists')));
            $userHelper->addSubscriber($data);
        }
    }


    function comment_form_extend(){
        echo '<p class="wysija-after-comment">';
        echo '<label for="wysija-box-after-comment">';
        echo '<input type="checkbox" id="wysija-box-after-comment" value="1" name="wysija[comment_subscribe]">';
        $mConfig=WYSIJA::get('config','model');
        echo $mConfig->getValue('commentform_linkname').'</label></p>';
    }

    function comment_posted($cid,$comment){
        $cid = (int) $cid;
        if ( !is_object($comment) )
            $comment = get_comment($cid);

        //before recording the subscriber, make sure that it is not a spam comment or it needs to be approved first
        if($comment->comment_approved=='spam') return;

        if(isset($_POST['wysija']['comment_subscribe']) && $_POST['wysija']['comment_subscribe']) {
            if($comment->comment_approved=='0')  add_comment_meta($cid, 'wysija_comment_subscribe', 1);
            else{
                $mConfig=WYSIJA::get('config','model');
                $userHelper=WYSIJA::get('user','helper');
                $data=array('user'=>array('email'=>$comment->comment_author_email,'firstname'=>$comment->comment_author),'user_list'=>array('list_ids'=>$mConfig->getValue('commentform_lists')));
                $userHelper->addSubscriber($data);
            }
        }
    }

    function scan_title($title){
        /*careful WordPress global*/
        global $post;

        if($this->is_default_mailpoet_page()){
            // when user have kept the default page we modify the title based on the controller
            if(trim($title)==trim(single_post_title( '', false )) && !empty($this->controller->title)){
                $post->comment_status='close';
                $post->post_password='';
                return $this->controller->title;
            }
        }

        return $title;

    }

    function scan_content($content){
        $wysija_content = $wysija_undo = '';
        if(!empty($this->controller->subtitle))  $wysija_content = $this->controller->subtitle;
        if(!empty($this->controller->optional_subtitle))    $wysija_content = $this->controller->optional_subtitle;
        if(!empty($this->controller->undo_unsubscribe)){
            $wysija_undo = $this->controller->undo_unsubscribe;
        }

        // only force our edit subscription screen at the bottom of the content of the page
        // if it's the right action and there is no wysija_page shortcode in teh page
        if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'subscriptions' && strpos($content, '[wysija_page]') === false){
            // we append the subscription form at the bottom of the page if we can't detect it
            return $content.'<div class="mpoet_profile_edit">'.$wysija_content.'</div>';
        }else{
            // we replace the shortcode by our automatic content, other wise if there is no shortcode the page stays the same
            return str_replace('[wysija_page]', $wysija_content.$wysija_undo, $content);
        }

    }

    function is_default_mailpoet_page(){
        global $post;
        $model_config = WYSIJA::get('config','model');
        if($post->ID == $model_config->getValue('confirm_email_link')) return true;
        else return false;
    }

    /**
     * this is for the new kind of shortcodes [wysija_form form="1"]
     * @param array $attributes
     * @return string html
     */
    function scan_form_shortcode($attributes) {
        // IMPORTANT: this is to make sure MagicMember won't scan our form and find [user_list] as a code they should replace.
        remove_shortcode('user_list');

        if(isset($attributes['id']) && (int)$attributes['id']>0){
            $widget_data=array();
            $widget_data['form']=(int)$attributes['id'];
            $widget_data['form_type'] = 'shortcode';

            $widget_NL=new WYSIJA_NL_Widget(true);
            return $widget_NL->widget($widget_data);

        }
        return '';
    }


    /**
     * this is for the new kind of shortcodes [wysija_form form="1"]
     * @param array $attributes
     * @return string html
     */
    function scan_subscribers_count_shortcode($attributes) {
        $user = WYSIJA::get('user','model');
        $list_ids = !empty($attributes['list_id']) ? explode(',', $attributes['list_id']) : array();

        // if double optin is on we count only the confirmed subscribers, otherwise we count both confirmed and unconfirmed
        $confirmed_subscribers = false;
        $model_config = WYSIJA::get('config', 'model');
        if ($model_config->getValue('confirm_dbleoptin')){
            $confirmed_subscribers = true;
        }
        
        return $user->countSubscribers($list_ids, $confirmed_subscribers);
    }

    function scan_content_NLform($content){
        preg_match_all('/\<div class="wysija-register">(.*?)\<\/div>/i',$content,$matches);
        if(!empty($matches[1]) && count($matches[1])>0)   require_once(WYSIJA_WIDGETS.'wysija_nl.php');
        foreach($matches[1] as $key => $mymatch){
            if($mymatch){
                $widgetdata=unserialize(base64_decode($mymatch));
                $widgetNL=new WYSIJA_NL_Widget(true);
                $contentTABLE= $widgetNL->widget($widgetdata,$widgetdata);
                $content=str_replace($matches[0][$key],$contentTABLE,$content);
            }//endif
        }//endforeach
        return $content;
    }

}
