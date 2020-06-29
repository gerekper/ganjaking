<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_install extends WYSIJA_object{

    function __construct(){
        if(file_exists(ABSPATH . 'wp-admin'.DS.'includes'.DS.'upgrade.php'))    require_once(ABSPATH . 'wp-admin'.DS.'includes'.DS.'upgrade.php');
    }

    function install(){
        $values=array();
        $model_config=WYSIJA::get('config','model');
        // test server against few things to make sure the installation can be done
        $helper_server=WYSIJA::get('server','helper');
        $missing_capabilities=$helper_server->unhealthy();

        // if it returns false it means we're all good
        if($missing_capabilities!==false){
            // it will fail only if we have more than one missing capabilities or if we have just one and that's a required function
            if(count($missing_capabilities) > 1 ||
                    (count($missing_capabilities)==1 && (!isset($missing_capabilities['functions']) || isset($missing_capabilities['functions']['required']))) ){
                // here we need to return unfortunately
                $this->error(__('Your server cannot run MailPoet.',WYSIJA),1);

                if(isset($missing_capabilities['functions']['required'])){
                    $this->error(sprintf(__('Your server is missing one or many important PHP functions to run properly :  %1$s',WYSIJA),'<strong>'.implode(', ',  array_keys($missing_capabilities['functions']['required'])).'</strong>').' '.__('Please contact your host or server administrator to fix this.',WYSIJA));

                }
                return false;
            }else{
                // here we're ok
            }
        }

        // create the tables there shouldn't be any issue since we've tested it before
        if((int)get_option('installation_step')<1){
             if(!$this->createTables(WYSIJA_DIR.'sql'.DS.'install.sql')) return false;

            WYSIJA::update_option('installation_step', '1');
        }


        // move data to uploads folder: this needs to be done prior to default campaign creation for dependency reasons
        if((int)get_option('installation_step')<4){
            $this->moveData('themes');
            $this->moveData('dividers');
            $this->moveData('bookmarks');
            WYSIJA::update_option('installation_step', '4');
        }


        // record custom fields lastname firstname in the user_field table
        if((int)get_option('installation_step')<5){
            $this->recordDefaultUserField();
            WYSIJA::update_option('installation_step', '5');
        }

        // save default values for the fields : from_name, from_email replyto_name, replyto_email
        if((int)get_option('installation_step')<6){
            $this->defaultSettings($values);
            $model_config->save($values);
            WYSIJA::update_option('installation_step', '6');
        }


        //create a default list
        if((int)get_option('installation_step')<7){
            $this->defaultList($values);
            $model_config->save($values);
            WYSIJA::update_option('installation_step', '7');
        }


        // create a default campaign
        if((int)get_option('installation_step')<8){
            $this->defaultCampaign($values);
            $model_config->save($values);
            WYSIJA::update_option('installation_step', '8');
        }


        // synchronize our user table with wordpress users
        if((int)get_option('installation_step')<9){
            $helper_import=WYSIJA::get('plugins_import','helper');
            $values['importwp_list_id']=$helper_import->importWP();
            $model_config->save($values);
            WYSIJA::update_option('installation_step', '9');
        }

        // create subscription redirection page
        if((int)get_option('installation_step')<10){
            $this->createPage($values);
            $model_config->save($values);
            WYSIJA::update_option('installation_step', '10');
        }


        // create the default dir
        if((int)get_option('installation_step')<11){
            $this->createWYSIJAdir($values);
            $model_config->save($values);
            WYSIJA::update_option('installation_step', '11');
        }


        // create default subscription form
        if((int)get_option('installation_step')<12){
            $this->create_default_subscription_form();
            WYSIJA::update_option('installation_step', '12');
        }


        // save the confirmation email in the table


        if((int)get_option('installation_step')<13){
            // make sure that the activation email is translated
            $model_config->add_translated_default();
            WYSIJA::update_option('installation_step', '13');
        }

        if((int)get_option('installation_step')<14){
            $model_email=WYSIJA::get('email','model');
            $model_email->blockMe=true;
            $values['confirm_email_id']=$model_email->insert(
                    array('type'=>'0',
                        'from_email'=>$values['from_email'],
                        'from_name'=>$values['from_name'],
                        'replyto_email'=>$values['from_email'],
                        'replyto_name'=>$values['from_name'],
                        'subject'=>$model_config->getValue('confirm_email_title'),
                        'body'=>$model_config->getValue('confirm_email_body'),
                        'status'=>99));
            $model_config->save($values);
            WYSIJA::update_option('installation_step', '14');
        }


        // look for existing newsletter plugins to import from
        if((int)get_option('installation_step')<15){
            $this->testNLplugins();

            // administrator caps
            $helper_wp_tools = WYSIJA::get('wp_tools', 'helper');
            $helper_wp_tools->set_default_rolecaps();

            WYSIJA::update_option('installation_step', '15');
        }



        // save the config into the db

        if( (int) get_option('installation_step') < 16){

            $model_config = WYSIJA::get('config','model');

            $values['installed'] = true;
            $values['manage_subscriptions'] = true;
            $values['installed_time'] = time();

            $values['wysija_db_version'] = WYSIJA::get_version();

            $helper_toolbox = WYSIJA::get('toolbox', 'helper');
            $values['dkim_domain'] = $helper_toolbox->_make_domain_name();

            if( get_option('wysija_reinstall',0) ) $values['wysija_whats_new'] = WYSIJA::get_version();
            $model_config->save($values);

            WYSIJA::update_option('installation_step', '16');
        }




        global $wysija_installing;
        $wysija_installing=false;
        WYSIJA::update_option('wysija_reinstall',0);
        return true;
    }


    // Description: Creates the default list.
    function defaultList(&$values){
        $model_list=WYSIJA::get('list','model');
        $listname=__('My first list',WYSIJA);
        $defaultListId=$model_list->insert(array(
            'name'=>$listname,
            'description'=>__('The list created automatically on install of the MailPoet.',WYSIJA),
            'is_public'=>1,
            'is_enabled'=>1));
        $values['default_list_id']=$defaultListId;
        // Add Wordpress user as first subscriber of the default list.
        $helper_user=WYSIJA::get('user','helper');
        $current_user=WYSIJA::wp_get_userdata();

        $user_ids = array($current_user->ID);

        $id=$helper_user->addToList($values['default_list_id'],$user_ids);
    }

    function defaultCampaign($valuesconfig){
        $modelCampaign=WYSIJA::get('campaign','model');
        $campaign_id=$modelCampaign->insert(
                array(
                    'name'=>__('5 Minute User Guide',WYSIJA),
                    'description'=>'',

                    ));

        $modelEmail=WYSIJA::get('email','model');
        $modelEmail->fieldValid=false;

        $dataEmail=array(
            'campaign_id'=>$campaign_id,
            'subject'=>__('5 Minute User Guide',WYSIJA)
        );

        // get default styles
        $wjEngine = WYSIJA::get('wj_engine', 'helper');

        // get solid divider
        $hDividers = WYSIJA::get('dividers', 'helper');
        $defaultDivider = $hDividers->getDefault();

        // get bookmarks from iconset 2
        $hBookmarks = WYSIJA::get('bookmarks', 'helper');
        $bookmarks = $hBookmarks->getAllByIconset('medium', '02');

        //--------------
        $dataEmail['wj_data'] = array(
            'version' => WYSIJA::get_version(),
            'header' => array(
                'text' => null,
                'image' => array(
                    'src' => WYSIJA_EDITOR_IMG.'default-newsletter/newsletter/header.png',
                    'width' => 600,
                    'height' => 72,
                    'alignment' => 'center',
                    'static' => false
                ),
                'alignment' => 'center',
                'static' => false,
                'type' => 'header'
            ),
            'body' => array(
                'block-1' => array(
                    'text' => array(
                        'value' => '<h2><strong>'.__('Step 1:', WYSIJA).'</strong> '.__('hey, click on this text!', WYSIJA).'</h2>'.'<p>'.__('To edit, simply click on this block of text.', WYSIJA).'</p>'
                    ),
                    'image' => null,
                    'alignment' => 'left',
                    'static' => false,
                    'position' => 1,
                    'type' => 'content'
                ),
                'block-2' => array_merge(array(
                        'position' => 2,
                        'type' => 'divider'
                    ), $defaultDivider
                ),
                'block-3' => array(
                    'text' => array(
                        'value' => '<h2><strong>'.__('Step 2:', WYSIJA).'</strong> '.__('play with this image', WYSIJA).'</h2>'
                    ),
                    'image' => null,
                    'alignment' => 'left',
                    'static' => false,
                    'position' => 3,
                    'type' => 'content'
                ),
                'block-4' => array(
                    'text' => array(
                        'value' => '<p>'.__('Position your mouse over the image to the left.', WYSIJA).'</p>'
                    ),
                    'image' => array(
                        'src' => WYSIJA_EDITOR_IMG.'default-newsletter/newsletter/pigeon.png',
                        'width' => 281,
                        'height' => 190,
                        'alignment' => 'left',
                        'static' => false
                    ),
                    'alignment' => 'left',
                    'static' => false,
                    'position' => 4,
                    'type' => 'content'
                ),
                'block-5' => array_merge(array(
                        'position' => 5,
                        'type' => 'divider'
                    ), $defaultDivider
                ),
                'block-6' => array(
                    'text' => array(
                        'value' => '<h2><strong>'.__('Step 3:', WYSIJA).'</strong> '.__('drop content here', WYSIJA).'</h2>'.
                                    '<p>'.sprintf(__('Drag and drop %1$stext, posts, dividers.%2$s Look on the right!', WYSIJA), '<strong>', '</strong>').'</p>'.
                                    '<p>'.sprintf(__('You can even %1$ssocial bookmarks%2$s like these:', WYSIJA), '<strong>', '</strong>').'</p>'
                    ),
                    'image' => null,
                    'alignment' => 'left',
                    'static' => false,
                    'position' => 6,
                    'type' => 'content'
                ),
                'block-7' => array(
                    'width' => 184,
                    'alignment' => 'center',
                    'items' => array(
                        array_merge(array(
                            'url' => 'http://www.facebook.com/mailpoetplugin',
                            'alt' => 'Facebook',
                            'cellWidth' => 61,
                            'cellHeight' => 32
                        ), $bookmarks['facebook']),
                        array_merge(array(
                            'url' => 'http://www.twitter.com/mail_poet',
                            'alt' => 'Twitter',
                            'cellWidth' => 61,
                            'cellHeight' => 32
                        ), $bookmarks['twitter']),
                        array_merge(array(
                            'url' => 'https://plus.google.com/+Mailpoet',
                            'alt' => 'Google',
                            'cellWidth' => 61,
                            'cellHeight' => 32
                        ), $bookmarks['google'])
                    ),
                    'position' => 7,
                    'type' => 'gallery'
                ),
                'block-8' => array_merge(array(
                        'position' => 8,
                        'type' => 'divider'
                    ), $defaultDivider
                ),
                'block-9' => array(
                    'text' => array(
                        'value' => '<h2><strong>'.__('Step 4:', WYSIJA).'</strong> '.__('and the footer?', WYSIJA).'</h2>'.
                                    '<p>'.sprintf(__('Change the footer\'s content in MailPoet\'s %1$sSettings%2$s page.', WYSIJA), '<strong>', '</strong>').'</p>'
                    ),
                    'image' => null,
                    'alignment' => 'left',
                    'static' => false,
                    'position' => 9,
                    'type' => 'content'
                )
            ),
            'footer' => array(
                'text' => NULL,
                'image' => array(
                    'src' => WYSIJA_EDITOR_IMG.'default-newsletter/newsletter/footer.png',
                    'width' => 600,
                    'height' => 46,
                    'alignment' => 'center',
                    'static' => false,
                ),
                'alignment' => 'center',
                'static' => false,
                'type' => 'footer'
            )
        );

        $dataEmail['wj_styles'] = array(
            'html' => array(
                'background' => 'e8e8e8'
            ),
            'header' => array(
                'background' => 'e8e8e8'
            ),
            'body' => array(
                'color' => '000000',
                'family' => 'Arial',
                'size' => 16,
                'background' => 'ffffff'
            ),
            'footer' => array(
                'background' => 'e8e8e8'
            ),
            'h1' => array(
                'color' => '000000',
                'family' => 'Trebuchet MS',
                'size' => 40
            ),
            'h2' => array(
                'color' => '424242',
                'family' => 'Trebuchet MS',
                'size' => 30
            ),
            'h3' => array(
                'color' => '424242',
                'family' => 'Trebuchet MS',
                'size' => 24
            ),
            'a' => array(
                'color' => '4a91b0',
                'underline' => false
            ),
            'unsubscribe' => array(
                'color' => '000000'
            ),
            'viewbrowser' => array(
                'color' => '000000',
                'family' => 'Arial',
                'size' => 12
            )
        );
        //---- END DEFAULT EMAIL ---------
        foreach( $dataEmail['wj_data'] as $key =>&$eachval){
            if($key=="body") {
                foreach($eachval as &$realeachval){
                    if(isset($realeachval['text']['value']))    $realeachval['text']['value']=base64_encode($realeachval['text']['value']);
                }
            }
        }

        $dataEmail['params'] = array(
            'quickselection' => array(
                'wp-301' => array(
                    'identifier' => 'wp-301',
                    'width' => 281,
                    'height' => 190,
                    'url' => WYSIJA_EDITOR_IMG.'default-newsletter/newsletter/pigeon.png',
                    'thumb_url' => WYSIJA_EDITOR_IMG.'default-newsletter/newsletter/pigeon-150x150.png'
                )
            )
        );
        $wjEngine = WYSIJA::get('wj_engine', 'helper');
        $wjEngine->setData($dataEmail['wj_data']);
        $result = false;
        $dataEmail['params'] = base64_encode(serialize($dataEmail['params']));
        $dataEmail['wj_styles'] = base64_encode(serialize($dataEmail['wj_styles']));
        $dataEmail['wj_data'] = base64_encode(serialize($dataEmail['wj_data']));


        $dataEmail['replyto_name']=$dataEmail['from_name']=$valuesconfig['from_name'];
        $dataEmail['replyto_email']=$dataEmail['from_email']=$valuesconfig['from_email'];
        $data['email']['email_id']=$modelEmail->insert($dataEmail);



        $modelEmail = WYSIJA::get('email', 'model');
        $emailData = $modelEmail->getOne(array('wj_styles', 'subject', 'params', 'email_id'), array('email_id' => $data['email']['email_id']));

        $wjEngine->setStyles($emailData['wj_styles'], true);

        $values = array('wj_data' => $wjEngine->getEncoded('data'));
        $values['body'] = $wjEngine->renderEmail($emailData);
        $result = $modelEmail->update($values, array('email_id' => $data['email']['email_id']));

    }

    /**
     * this function creates the table from a specified sql file and test that the tables have been properly created.
     * TODO it should just need  a SQL file and no other parameter
     * @global type $wpdb
     * @param type $sql_file
     * @param type $main_table_ms
     * @return boolean
     */
    function createTables($sql_file, $main_table_ms=false){
        // prepare some parameters
        $model_user=WYSIJA::get('user','model');
        $prefix = $model_user->getPrefix();
        $array_tables_to_test = array('user_list','user','list','campaign','campaign_list','email','user_field','queue','user_history','email_user_stat','url','email_user_url','url_mail');

        // if we are running a ms file we use a different prefix and we verify different tables
        if($main_table_ms){
            $prefix = $model_user->get_site_prefix();
            $array_tables_to_test = array('bounce');
        }


        // load the sql file, read it and separate the queries
        $handle = fopen($sql_file, 'r');
        $query = fread($handle, filesize($sql_file));
        fclose($handle);

        $query=str_replace('CREATE TABLE IF NOT EXISTS `','CREATE TABLE IF NOT EXISTS `'.$prefix,$query);
        $queries=explode('-- QUERY ---',$query);

        // execute the queries one by one
        global $wpdb;
        $has_errors = false;

        foreach($queries as $qry){
            $last_error = $wpdb->last_error;
            $wpdb->query($qry);

            if( !empty($wpdb->last_error) && $last_error != $wpdb->last_error ){
                $this->notice($wpdb->last_error);
                $has_errors = true;
            }

        }

        // list the tables that haven't been created
        $missingtables=array();
        foreach($array_tables_to_test as $table_name){
            if(!$model_user->query("SHOW TABLES like '".$prefix.$table_name."';")) {
                $missingtables[]=$prefix.$table_name;
            }
        }

        // return the result
        if($missingtables) {
            $this->error(sprintf(__('These tables could not be created on installation: %1$s',WYSIJA),implode(', ',$missingtables)),1);
            $has_errors=true;
        }
        if($has_errors) return false;
        return true;
    }

    /**
     *
     * @param type $values
     * @return boolean
     */
    function createWYSIJAdir(&$values){
        $upload_dir = wp_upload_dir();

        $dirname=$upload_dir['basedir'].DS.'wysija'.DS;
        $url=$upload_dir['baseurl'].'/wysija/';
        if(!file_exists($dirname)){
            if(!mkdir($dirname, 0755,true)){
                return false;
            }
        }

        // Create index.html to protect main wysija data directory.
        $filename = 'index.html';
        fclose(fopen($dirname.$filename, "w"));

        $values['uploadfolder']=$dirname;
        $values['uploadurl']=$url;
    }

    function moveData($folder) {
        $fileHelper = WYSIJA::get('file', 'helper');

        // get target directory
        $targetDir = $fileHelper->makeDir($folder);
        if($targetDir === FALSE) {
            // directory does not exist and could not be created
            return FALSE;
        } else {
            // define source directory
            $sourceDir = WYSIJA_DATA_DIR.$folder.DS;

            // don't do anything if source directory does not exist
            if(is_dir($sourceDir) === FALSE) return FALSE;

            // scan for files
            $files = scandir($sourceDir);

            // recursively copy files
            foreach($files as $filename) {
                if(!in_array($filename, array('.', '..', '.DS_Store', 'Thumbs.db'))) {
                    $this->rcopy($sourceDir.$filename, $targetDir.$filename);
                }
            }
        }
    }

    function rrmdir($dir) {
      if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file)
        if ($file != '.' && $file != '..') $this->rrmdir("$dir".DS."$file");
        rmdir($dir);
      }
      else if (file_exists($dir)) {
          $dir=str_replace('/',DS,$dir);
          unlink($dir);
      }
    }

    function rcopy($src, $dst) {
      if (file_exists($dst)) $this->rrmdir($dst);
      if (is_dir($src)) {
        mkdir($dst);
        $files = scandir($src);
        foreach ($files as $file)
        if ($file != '.' && $file != '..') $this->rcopy("$src/$file", "$dst/$file");
      }
      else if (file_exists($src)) {
          copy(str_replace('/',DS,$src), str_replace('/',DS,$dst));
      }
    }
    function recordDefaultUserField(){

        $modelUF=WYSIJA::get("user_field","model");
        $arrayInsert=array(
            array('name'=>__('First name',WYSIJA),'column_name'=>'firstname','error_message'=>__('Please enter first name',WYSIJA)),
            array('name'=>__('Last name',WYSIJA),'column_name'=>'lastname','error_message'=>__('Please enter last name',WYSIJA)));
        foreach($arrayInsert as $insert){
            $modelUF->insert($insert);
            $modelUF->reset();
        }


    }

    function defaultSettings(&$values){

        /* get the user data for the admin */
        //$datauser=wp_get_current_user();
        $current_user=WYSIJA::wp_get_userdata();

        $values['replyto_name'] = $values['from_name'] = $current_user->user_login;
        $values['emails_notified'] = $current_user->user_email;
        $values['replyto_email'] = $values['from_email'] = 'info@' . WJ_Utils::get_domain();
    }


    function createPage(&$values){

        /* get the user data for the admin */
        $my_post = array(
        'post_status' => 'publish',
        'post_type' => 'wysijap',
        'post_author' => 1,
        'post_content' => '[wysija_page]',
        'post_title' => __('Subscription confirmation',WYSIJA),
        'post_name' => 'subscriptions');

        //check if the post already exists?
        $helpersWPPOSTS=WYSIJA::get('wp_posts','model');
        $postss=$helpersWPPOSTS->get_posts(array('post_type'=>'wysijap'));
        $postid=false;
        if($postss){
            if(isset($postss[0]['post_content']) && strpos($postss[0]['post_content'], '[wysija_page]')!==false){
                $postid=$postss[0]['ID'];
            }
        }

        if(!$postid){
            remove_all_actions('pre_post_update');
            remove_all_actions('save_post');
            remove_all_actions('wp_insert_post');
            $values['confirm_email_link']=wp_insert_post( $my_post );
            flush_rewrite_rules();
        }else $values['confirm_email_link']=$postid;


    }


    function testNLplugins(){
        $importHelp=WYSIJA::get('plugins_import','helper');
        $importHelp->testPlugins();
    }

    function create_default_subscription_form() {
        // form engine
        $helper_form_engine = WYSIJA::get('form_engine', 'helper');

        // set default data
        $helper_form_engine->set_data();

        // create form in database
        $model_forms = WYSIJA::get('forms', 'model');
        $model_forms->reset();

        // get form id back because it's required to generate the html form
        $form_id = $model_forms->insert(array('name' => __('Subscribe to our Newsletter', WYSIJA)));

        if((int)$form_id > 0) {
            // set form engine data
            $helper_form_engine->set_data(array_merge($helper_form_engine->get_data(), array('form_id' => (int)$form_id)));

            // update form in database
            $model_forms->reset();
            $model_forms->update(array('data' => $helper_form_engine->get_encoded('data')), array('form_id' => $form_id));
        }
    }
}
