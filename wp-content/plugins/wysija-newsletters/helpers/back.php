<?php
defined('WYSIJA') or die('Restricted access');


/**
 * class managing the admin vital part to integrate wordpress
 */
class WYSIJA_help_back extends WYSIJA_help{

    function __construct(){
        parent::__construct();
        //check that the application has been installed properly
        $config=WYSIJA::get('config','model');

        define('WYSIJA_DBG',(int)$config->getValue('debug_new'));
        //by default do not show the errors until we get into the debug file
        if(!defined('WP_DEBUG') || !WP_DEBUG){
            error_reporting(0);
            ini_set('display_errors', '0');
        }

        add_filter('admin_footer_text', array(&$this, 'admin_footer_text'));
        add_filter('update_footer', array(&$this, 'update_footer'), 15);

        //the controller is backend is it from our pages or from wordpress?
        //are we pluging-in to wordpress interfaces or doing entirely our own page?
        if(isset($_GET['page']) && substr($_GET['page'],0,7)=='wysija_'){
            define('WYSIJA_ITF',TRUE);
            $this->controller=WYSIJA::get(str_replace('wysija_','',$_GET['page']),'controller');
        }else{//check if we are pluging in wordpress interface
            define('WYSIJA_ITF',FALSE);
        }

        if( WYSIJA_DBG>0 ) include_once(WYSIJA_INC.'debug.php');

        if(!function_exists('dbg')) {
            function dbg($mixed,$exit=true){}
        }


        //we set up the important hooks for backend: menus js css etc
        if(defined('DOING_AJAX')){
            //difference between frontend and backend

            add_action( 'after_setup_theme', array($this, 'ajax_setup') );

        }else{
            if(WYSIJA_ITF)  {
                add_action('admin_init', array( $this , 'verify_capability'),1);
                add_action('admin_init', array($this->controller, 'main'));
                add_action('after_setup_theme',array($this,'resolveConflicts'));
            }
            //this is a fix for qtranslate as we were loading translatable string quite early


            //somehow if we add caps to one role the user with that role doesnt get its caps updated ...
            add_action('after_setup_theme', array('WYSIJA', 'update_user_caps'),11);
            add_action('admin_menu', array($this, 'define_translated_strings'),98);
            add_action('admin_menu', array($this, 'add_menus'),99);
            add_action('admin_enqueue_scripts',array($this, 'add_js'),10,1);


            //add specific page script
            add_action('admin_head-post-new.php',array($this,'addCodeToPagePost'));
            add_action('admin_head-post.php',array($this,'addCodeToPagePost'));

            //make sure that admin and super admin always have the highest access
             $wptools = WYSIJA::get('wp_tools', 'helper');
             $wptools->set_default_rolecaps();

            // Hook the warning function for premium.
            add_action( 'admin_init', array(&$this, 'warn_action_on_premium') );
        }

        //if the comment form option is activated then we add an approval action
        if($config->getValue('commentform')){
            add_action('wp_set_comment_status', array($this,'comment_approved'), 60,2);
        }
    }

    private function _set_ajax_nonces(){
            if( isset( $_GET['page'] ) && substr( $_GET['page'] ,0 ,7 ) == 'wysija_' ){

                        $ajax_nonces = array(
                            'campaigns' => array(
                                'switch_theme' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'switch_theme'
                                    ), true),
                                'save_editor' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'save_editor'
                                    ), true),
                                'save_styles' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'save_styles'
                                    ), true),
                                'deleteimg' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'deleteimg'
                                    ), true),
                                'deleteTheme' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'deleteTheme'
                                    ), true),
                                'save_IQS' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'save_IQS'
                                    ), true),
                                'send_preview' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'send_preview'
                                    ), true),
                                'send_spamtest' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'send_spamtest'
                                    ), true),
                                'insert_articles' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'insert_articles'
                                    ), true),
                                'set_divider' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'set_divider'
                                    ), true),
                                'generate_social_bookmarks' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'generate_social_bookmarks'
                                    ), true),
                                'install_theme' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'install_theme'
                                    ), true),
                                'setDefaultTheme' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'setDefaultTheme'
                                    ), true),
                                'deleteTheme' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'deleteTheme'
                                    ), true),
                                'save_poll' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'save_poll'
                                    ), true),
                                'sub_delete_image' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_campaigns',
                                    'action' => 'sub_delete_image',
                                    ), true),

                            ),
                            'config' => array(
                                'send_test_mail' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_config',
                                    'action' => 'send_test_mail'
                                    ), true),
                                'send_test_mail_ms' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_config',
                                    'action' => 'send_test_mail_ms'
                                    ), true),
                                'bounce_process' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_config',
                                    'action' => 'bounce_process'
                                    ), true),
                                'share_analytics' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_config',
                                    'action' => 'share_analytics'
                                    ), true),
                                'wysija_form_manage_field' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_config',
                                    'action' => 'wysija_form_manage_field'
                                    ), true),
                                'form_field_delete' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_config',
                                    'action' => 'form_field_delete'
                                    ), true),
                                'form_name_save' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_config',
                                    'action' => 'form_name_save'
                                    ), true),
                                'form_save' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_config',
                                    'action' => 'form_save'
                                    ), true),
                                'validate' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_config',
                                    'action' => 'validate'
                                    ), true),
                                'linkignore' => WYSIJA_view::secure(array(
                                    'controller' => 'wysija_config',
                                    'action' => 'linkignore'
                                    ), true),
                            )
                        );

            }else{
                $ajax_nonces = array();
            }

            wp_localize_script('wysija-admin', 'wysijanonces', $ajax_nonces);
    }

    /**
     * On any of the administration pages related to MailPoet, if the user
     * has the key and doesn't have the premium plugin active a warning will
     * be displayed.
     *
     * @return null
     */
    public function warn_action_on_premium(){
        $mdl_config=WYSIJA::get('config','model');

        if($mdl_config->getValue('premium_key') && !WYSIJA::is_plugin_active(WJ_Upgrade::$plugins[1])){
            if( file_exists( WPMU_PLUGIN_DIR . DIRECTORY_SEPARATOR . WJ_Upgrade::$plugins[1] ) ||  file_exists( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . WJ_Upgrade::$plugins[1] ) ){
                //send a message to the user so that he activates the premium plugin or try to fetch it directly.
                $this->notice('<p>'.__('You need to activate the MailPoet Premium plugin.', WYSIJA).' <a data-warn="' . esc_attr__( "Confirm activating the MailPoet Premium Plugin?", WYSIJA ) . '" class="button-primary" title="' . esc_attr__( "Activate MailPoet Premium Version", WYSIJA ) . '" href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . urlencode(WJ_Upgrade::$plugins[1]) . '&amp;plugin_status=all', 'activate-plugin_' . WJ_Upgrade::$plugins[1]) . '">'.__('Activate now',WYSIJA).'</a></p>');
            } else {

                $args = array(
                    'page' => 'wysija_config',
                    'action' => 'packager-switch',
                    '_mp_action' => 'install',
                    '_wpnonce' => wp_create_nonce('packager-switch'),
                );
                if (WYSIJA::is_beta())
                    $args["stable"] = 1;

                $link = esc_attr(add_query_arg($args, admin_url('admin.php')));

                //send a message to the user so that he gets the premium plugin or try to fetch it directly.
                $this->notice('<p>'.__('Congrats, your Premium license is active. One last step...', WYSIJA).' <a data-warn="' . esc_attr__( "Confirm installing the MailPoet Premium Plugin?", WYSIJA ) . '" id="install-wjp" class="button-primary" title="' . esc_attr__( "Installing MailPoet Premium Version", WYSIJA ) . '" href="' . esc_url($link) . '">'.__('Download the Premium plugin.',WYSIJA).'</a></p>');
            }
        }

        return null;
    }

    function comment_approved($cid,$comment_status){
        //if the comment is approved and the meta wysija_comment_subscribe appears, then we have one subscriber more to add
        $metaresult=get_comment_meta($cid, 'wysija_comment_subscribe', true);

        if($comment_status=='approve' && get_comment_meta($cid, 'wysija_comment_subscribe', true)){
            $mConfig=WYSIJA::get('config','model');
            $comment = get_comment($cid);
            $userHelper=WYSIJA::get('user','helper');
            $data=array('user'=>array('email'=>$comment->comment_author_email,'firstname'=>$comment->comment_author),'user_list'=>array('list_ids'=>$mConfig->getValue('commentform_lists')));
            $userHelper->addSubscriber($data);
        }
    }

    function ajax_setup(){
        if(!isset($_REQUEST['adminurl']) && !is_user_logged_in())    add_action('wp_ajax_nopriv_wysija_ajax', array($this, 'ajax'));
        else    add_action('wp_ajax_wysija_ajax', array($this, 'ajax'));
    }


    /**
     * let's fix all the conflicts that we may have
     */
    function resolveConflicts(){

        // check conflicting themes
        $possibleConflictiveThemes = $this->controller->get_conflictive_plugins(true);

        $conflictingTheme = null;
        $currentTheme = strtolower(function_exists( 'wp_get_theme' ) ? wp_get_theme() : get_current_theme());
        foreach($possibleConflictiveThemes as $keyTheme => $conflictTheme) {
            if($keyTheme === $currentTheme) {
                $conflictingTheme = $keyTheme;
            }
        }

        // if the current theme is known to make troubles, let's resolve this
        if($conflictingTheme !== null) {
            $helperConflicts = WYSIJA::get('conflicts', 'helper');
            $helperConflicts->resolve(array($possibleConflictiveThemes[$conflictingTheme]));
        }

        // check conflicting plugins
        $possibleConflictivePlugins=$this->controller->get_conflictive_plugins();

        $conflictingPlugins=array();
        foreach($possibleConflictivePlugins as $keyPlg => $conflictPlug){
            if(WYSIJA::is_plugin_active($conflictPlug['file'])) {
                //plugin is activated
                $conflictingPlugins[$keyPlg]=$conflictPlug;
            }
        }

        if($conflictingPlugins){
            $helperConflicts=WYSIJA::get('conflicts','helper');
            $helperConflicts->resolve($conflictingPlugins);
        }

        // WP 4.9 script conflicts
        global $wp_version;
        if(version_compare( $wp_version, '4.9', '>=' )) {
          $helperConflicts=WYSIJA::get('conflicts','helper');
          $helperConflicts->resolveScriptConflicts();
        }
    }

    /**
     * this function will check the role of the user executing the action, if it's called from another
     * WordPress admin page than page.php for instance admin-post.php
     * @return boolean
     */
    function verify_capability(){
        if( isset( $_GET['page'] ) && substr( $_GET['page'] ,0 ,7 ) == 'wysija_' ){

            switch( $_GET['page'] ){
                case 'wysija_campaigns':
                    $role_needed = 'wysija_newsletters';
                    break;
                case 'wysija_subscribers':
                    $role_needed = 'wysija_subscribers';
                    break;
                case 'wysija_config':
                    $role_needed = 'wysija_config';
                    break;
                case 'wysija_statistics':
                    $role_needed = 'wysija_stats_dashboard';
                    break;
                default:
                    $role_needed = 'switch_themes';
            }

            if( current_user_can( $role_needed ) ){
                return true;
            } else{
                die( 'You are not allowed here.' );
            }

        }else{
            // this is not a wysija interface/action we can let it pass
            return true;
        }
    }

    /**
     * translatable strings need to be not loaded to early, this is why we put them ina separate function
     * @global type $wysija_installing
     */
    function define_translated_strings(){
        $config = WYSIJA::get('config','model');
        $linkcontent = __("It doesn't always work the way we want it to, doesn't it? We have a [link]dedicated support website[/link] with documentation and a ticketing system.",WYSIJA);
        $finds = array('[link]','[/link]');
        $replace = array('<a target="_blank" href="http://support.mailpoet.com" title="support.mailpoet.com">','</a>');
        $truelinkhelp = '<p>'.str_replace($finds,$replace,$linkcontent).'</p>';
        $truelinkhelp .= '<p>'.__('MailPoet Version: ',WYSIJA).'<strong>'.WYSIJA::get_version().'</strong></p>';

        $red_dot = is_plugin_active('mailpoet/mailpoet.php') ? '2' : '<span class="update-plugins"><span class="update-count">1</span></span>';

        $this->menus=array(
            'campaigns'=>array('title'=>'MailPoet '. $red_dot),
            'subscribers'=>array('title'=>__('Subscribers',WYSIJA)), // if the key "subscribers" is changed, please change in the filter "wysija_menus" as well.
            'config'=>array('title'=>__('Settings',WYSIJA)),
            'premium'=>array('title'=>__('Premium',WYSIJA)),
            'mp3'=>array('title'=>__('Try MailPoet 3',WYSIJA))
        );
        $this->menus = apply_filters('wysija_menus', $this->menus);
        $this->menuHelp = $truelinkhelp;
        if($config->getValue('queue_sends_slow')){
            $msg=$config->getValue('ignore_msgs');
            if(!isset($msg['queuesendsslow'])){
                $this->notice(
                        __('Tired of waiting more than 48h to send your emails?',WYSIJA).' '. str_replace(array('[link]','[/link]'), array('<a href="http://docs.mailpoet.com/article/48-wp-cron-batch-emails-sending-frequency" target="_blank">','</a>'), __('[link]Find out[/link] how you can improve this.',WYSIJA)).
                        ' <a class="linkignore queuesendsslow" href="javascript:;">'.__('Hide!',WYSIJA).'</a>');
            }
        }

        if(WYSIJA_ITF){
            global $wysija_installing;
            if( !$config->getValue('sending_emails_ok')){
                $msg=$config->getValue('ignore_msgs');

                $urlsendingmethod='admin.php?page=wysija_config#tab-sendingmethod';
                if($_REQUEST['page'] === 'wysija_config') {
                    $urlsendingmethod='#tab-sendingmethod';
                }

            }
        }
    }


    function add_menus(){
        global $menu,$submenu;// WordPress globals be careful there
        $count=0;

        //anti conflicting menus code to make sure that another plugin is not at the same level as us
        $position=50;
        $positionplus1=$position+1;

        while(isset($menu[$position]) || isset($menu[$positionplus1])){
            $position++;
            $positionplus1=$position+1;
            //check that there is no menu at our level neither at ourlevel+1 because that will make us disappear in some case :/
            if(!isset($menu[$position]) && isset($menu[$positionplus1])){
                $position=$position+2;
            }
        }

        global $wysija_installing;
        foreach($this->menus as $action=> $menutemp){
            $actionFull='wysija_'.$action;
            if (!isset($menutemp['subtitle']))
                $menutemp['subtitle'] = $menutemp['title'];
            if ($action == 'campaigns')
                $roleformenu = 'wysija_newsletters';
            elseif ($action == 'subscribers')
                $roleformenu = 'wysija_subscribers';
            elseif ($action == 'statistics')
                $roleformenu = 'wysija_stats_dashboard';
            else
                $roleformenu = 'wysija_config';

            if($wysija_installing===true){
                if($count==0){
                    $parentmenu=$actionFull;
                    $hookname = add_menu_page(
                        $menutemp['title'],
                        $menutemp['subtitle'],
                        $roleformenu,
                        $actionFull,
                        array($this->controller, 'errorInstall'),
                        WYSIJA_EDITOR_IMG.'menu-icon.png',
                        $position
                    );
                }
            }else{
                if($count==0){
                    $parentmenu = $actionFull;
                    $hookname = add_menu_page(
                        $menutemp['title'],
                        $menutemp['subtitle'],
                        $roleformenu,
                        $actionFull ,
                        array($this->controller, 'render'),
                        WYSIJA_EDITOR_IMG.'menu-icon.png',
                        $position
                    );
                }else{
                    $hookname=add_submenu_page($parentmenu,$menutemp['title'], $menutemp['subtitle'], $roleformenu, $actionFull , array($this->controller, 'render'));
                }

                //manage wp help tab
                if(WYSIJA_ITF){
                    //wp3.3
                    if(version_compare(get_bloginfo('version'), '3.3.0')>= 0){
                        add_action('load-'.$hookname, array($this,'add_help_tab'));
                    }else{
                        //wp3.0
                        add_contextual_help($hookname, $this->menuHelp);
                    }
                }
            }
            $count++;
        }

        // Correct the text of submenu, in case there is only 1 submenu is enabled
        if(isset($submenu[$parentmenu])) {
            switch ($submenu[$parentmenu][0][2]) {
                case 'wysija_subscribers':
                    $textmenu=__('Subscribers',WYSIJA);
                    break;

                case 'wysija_statistics':
                    $textmenu=__('Statistics',WYSIJA);
                    break;

                case 'wysija_config':
                    $textmenu=__('Settings',WYSIJA);
                    break;

                case 'wysija_campaigns':
                default:
                    $textmenu=__('Newsletters',WYSIJA);
                    break;
            }
            $submenu[$parentmenu][0][0]=$submenu[$parentmenu][0][3]=$textmenu;
        }
    }

    function add_help_tab($params){
        $screen = get_current_screen();

        if(method_exists($screen, "add_help_tab")){
            $screen->add_help_tab(array(
            'id'	=> 'wysija_help_tab',
            'title'	=> __('Get Help!',WYSIJA),
            'content'=> $this->menuHelp));
            $tabfunc=true;
        }
    }

    function add_js($hook) {
        //needed in all the wordpress admin pages including wysija's ones

        $jstrans=array();
        wp_register_script('wysija-charts', 'https://www.google.com/jsapi', array( 'jquery' ), true);
        wp_register_script('wysija-admin-list', WYSIJA_URL.'js/admin-listing.js', array( 'jquery' ), true, WYSIJA::get_version());
        wp_register_script('wysija-base-script-64', WYSIJA_URL.'js/base-script-64.js', array( 'jquery' ), true, WYSIJA::get_version());


        wp_enqueue_style('wysija-admin-css-widget', WYSIJA_URL.'css/admin-widget.css',array(),WYSIJA::get_version());

       // If Cron enabled sending, send Mixpanel data and reset flag.
        $model_config = WYSIJA::get('config', 'model');
        if ($model_config->getValue('send_analytics_now') == 1) {
            $analytics = new WJ_Analytics();
            $analytics->generate_data();
            $analytics->send();
            // Reset sending flag.
            $model_config->save(array('send_analytics_now' => 0));
        }


        //we are in wysija's admin interface
        if(WYSIJA_ITF){
            wp_enqueue_style('wysija-admin-css-global', WYSIJA_URL.'css/admin-global.css',array(),WYSIJA::get_version());
            wp_enqueue_script('wysija-admin-js-global', WYSIJA_URL.'js/admin-wysija-global.js',array(),WYSIJA::get_version());
            $pagename=str_replace('wysija_','',$_REQUEST['page']);
            $backloader=WYSIJA::get('backloader','helper');
            $backloader->init( $this->controller );

            //$this->controller->jsTrans["ignoremsg"]=__('Are you sure you want to ignore this message?.',WYSIJA);
            $jstrans=$this->controller->jsTrans;
            //if(!in_array('wysija-admin-ajax-proto',$this->controller->js)) $this->controller->js[]='wysija-admin-ajax';

            $jstrans['gopremium']=__('Go Premium!',WYSIJA);

            //enqueue all the scripts that have been declared in the controller
            $backloader->parse_js( $this->controller, $pagename, WYSIJA_URL );

            //this will load automatically existing scripts and stylesheets based on the page and action parameters
            $backloader->load_assets($pagename,WYSIJA_DIR,WYSIJA_URL,$this->controller);

            //add some translation
            $backloader->localize( $pagename, WYSIJA_DIR, WYSIJA_URL, $this->controller );

            // add rtl support
            if ( is_rtl() ) {
                wp_enqueue_style('wysija-admin-rtl', WYSIJA_URL.'css/rtl.css',array(),WYSIJA::get_version());
            }
            $this->_set_ajax_nonces();
        }
            $jstrans['newsletters']=__('Newsletters',WYSIJA);
            $jstrans['urlpremium']='admin.php?page=wysija_config#tab-premium';
            $jstrans['premium_activating'] = __('Checking license', WYSIJA);
            if(isset($_REQUEST['page']) && $_REQUEST['page']=='wysija_config'){
                $jstrans['urlpremium']='#tab-premium';
            }
            wp_localize_script('wysija-admin', 'wysijatrans', $jstrans);
    }


    /**
     * code only executed in the page or post in admin
     */
    function addCodeToPagePost(){

        //code to add external buttons to the tmce only if the user has the rights to add the forms
        if(get_user_option('rich_editing') == 'true') {
         add_filter("mce_external_plugins", array($this,"addRichPlugin"));
         add_filter('mce_buttons', array($this,'addRichButton1'),999);
         $myStyleUrl = WYSIJA_URL."css/tmce/style.css";
         add_editor_style($myStyleUrl);
         //add_filter('tiny_mce_before_init', array($this,'TMCEinnercss'),12 );
         wp_enqueue_style('custom_TMCE_admin_css', WYSIJA_URL.'css/tmce/panelbtns.css');
         wp_print_styles('custom_TMCE_admin_css');

       }
    }

    function addRichPlugin($plugin_array) {
       global $wp_version;

       if ( version_compare( $wp_version, '3.9', '<' ) ){
            $suffix = '';
       } else {
            $suffix = '_39';
       }

       $plugin_array['wysija_register'] = WYSIJA_URL.'mce/wysija_register/editor_plugin'.$suffix.'.js';

       return $plugin_array;
    }

    function addRichButton1($buttons) {
       $newButtons=array();
       foreach($buttons as $value) $newButtons[]=$value;
       array_push($newButtons, '|', 'wysija_register');
       return $newButtons;
    }

    function admin_footer_text($text) {
        $screen = get_current_screen();
        if (strpos($screen->base, 'wysija')===false)
            return $text;

        return
            "<a target='_blank' href='https://www.mailpoet.com/support/'>" . __( 'Contact Support', WYSIJA ) . "</a>" .
            " | " .
            str_replace(
                array('[link]','[/link]'),
                array('<a href="plugin-install.php?s=mailpoet&tab=search&type=author" >','</a>'),
                __('You’re using an old version of MailPoet. This version does not get improvements any longer. [link]It’s easy to switch to the new MailPoet.[/link]',WYSIJA)
            ) .
            "";
    }

    function update_footer($text){
        $screen = get_current_screen();
        if (strpos($screen->base, 'wysija')===false)
            return $text;

        $version_link = esc_url(add_query_arg(
            array(
                'page' => 'wysija_campaigns',
                'action' => 'whats_new',
            ),
            admin_url('admin.php')
        ));

        $version_string = "</p>" .
            "<p class='alignright'>" .
                __("MailPoet Version", WYSIJA) . ": <a href='{$version_link}'>" . esc_attr(WYSIJA::get_version()) . "</a>";

        $version_string = apply_filters('mailpoet_back_footer', $version_string);
        return $version_string;

    }
}
