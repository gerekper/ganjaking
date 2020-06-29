<?php
defined('WYSIJA') or die('Restricted access');
global $wysi_location;
class WYSIJA_control_back extends WYSIJA_control{
    var $redirectAfterSave=true;
    var $searchable=array();
    var $data=array();
    var $jsTrans=array();
    var $msgOnSave=true;
    var $pref=array();
    var $statuses=array();
    var $viewShow=null;
    var $_affected_rows = 0; //affected rows by batch select

    function __construct($extension="wysija-newsletters"){
        $this->extension=$extension;
        parent::__construct();
        global $wysija_msg,$wysija_queries,$wysija_queries_errors;
        $wysija_msgTemp=get_option('wysija_msg');

        if(is_array($wysija_msgTemp) && count($wysija_msgTemp)>0){
            $wysija_msg=$wysija_msgTemp;
        }

        $wysija_qryTemp=get_option('wysija_queries');
        $wysija_qryErrors=get_option('wysija_queries_errors');
        if(is_array($wysija_qryTemp) && count($wysija_qryTemp)>0){
            $wysija_queries=$wysija_qryTemp;
        }

        if(is_array($wysija_qryErrors) && count($wysija_qryErrors)>0){
            $wysija_queries_errors=$wysija_qryErrors;
        }

        WYSIJA::update_option('wysija_queries','');
        WYSIJA::update_option('wysija_queries_errors','');
        WYSIJA::update_option('wysija_msg','');
        global $wysija_installing;
        if($wysija_installing===true) return;
        $this->pref=get_user_meta(WYSIJA::wp_get_userdata('ID'),'wysija_pref',true);

        $prefupdate=false;
        if($this->pref) {
            $prefupdate=true;
            $this->pref=unserialize(base64_decode($this->pref));
        }else{
            $this->pref=array();
        }

        if(!isset($_GET['action'])) $action='default';
        else $action=$_GET['action'];

        if(isset($_REQUEST['limit_pp'])){
            $this->pref[$_REQUEST['page']][$action]['limit_pp']=$_REQUEST['limit_pp'];
        }

        if (!empty($_REQUEST['orderby'])) {
            $_REQUEST['orderby'] = preg_replace('|[^a-z0-9#_.-]|i','',$_REQUEST['orderby']);
        }
        if (!empty($_REQUEST['ordert']) && !in_array(strtoupper($_REQUEST['ordert']), array('DESC', 'ASC'))){
            $_REQUEST['ordert'] = 'DESC';
        }

        if(!empty($_REQUEST['id'])){
            $_REQUEST['id'] = (int) $_REQUEST['id'];
        }

        if(!empty($_REQUEST['search'])){
            $_REQUEST['search'] = esc_attr($_REQUEST['search']);
        }

        if($this->pref && isset($_REQUEST['page']) && $_REQUEST['page'] && isset($this->pref[$_REQUEST['page']][$action]['limit_pp'])){
            $this->viewObj->limit_pp=$this->pref[$_REQUEST['page']][$action]['limit_pp'];
            $this->modelObj->limit_pp=$this->pref[$_REQUEST['page']][$action]['limit_pp'];
        }

        if($prefupdate){
            update_user_meta(WYSIJA::wp_get_userdata('ID'),'wysija_pref',base64_encode(serialize($this->pref)));
        }else{
            add_user_meta(WYSIJA::wp_get_userdata('ID'),'wysija_pref',base64_encode(serialize($this->pref)));
        }
        add_action('wysija_various_check',array($this,'variousCheck'));
        do_action('wysija_various_check');

    }

    function variousCheck(){
        $model_config = WYSIJA::get('config','model');

        if(get_option('wysicheck')){
            $helper_licence = WYSIJA::get('licence','helper');
            $result = $helper_licence->check(true);
            if($result['nocontact']){
                // redirect instantly to a page with a javascript file  where we check the domain is ok
                $data = get_option('wysijey');
                // remotely connect to host
                wp_enqueue_script('wysija-verif-licence', 'http://www.mailpoet.com/?wysijap=checkout&wysijashop-page=1&controller=customer&action=checkDomain&js=1&data='.$data, array( 'jquery' ), time());
            }
        }

    }


    function errorInstall(){
       $this->viewObj->renderErrorInstall();
    }

    function _resetGlobMsg(){
        global $wysija_msg,$wysija_queries,$wysija_queries_errors;

        $wysija_msg=$wysija_queries=$wysija_queries_errors=array();
    }
    function defaultDisplay(){
        $this->viewShow=$this->action='main';

        // if it has not been enqueud in the head we print it here(can happens based on the action after a save or so)
        $this->js[]='wysija-admin-list';

        // get the filters
        if(isset($_REQUEST['search']) && $_REQUEST['search']){
            $this->filters['like']=array();
            foreach($this->searchable as $searchable){
                $this->filters['like'][$searchable]=$_REQUEST['search'];
            }

        }

        if($this->filters){
            $this->modelObj->setConditions($this->filters);
        }

        if($this->joins){
            $this->modelObj->setJoin($this->joins);
        }

        if($this->statuses){
            //we count by statuses
            $query='SELECT count('.$this->modelObj->pk.') as count, status FROM `[wysija]'.$this->modelObj->table_name.'` GROUP BY status';
            $countss=$this->modelObj->query('get_res',$query,ARRAY_A);
            $counts=array();
            $this->modelObj->countRows=0;

            foreach($countss as $count){
                $mystat=(int)$count['status'];

                $this->statuses[$mystat]['count']=$count['count'];
                $this->statuses[$mystat]['uri']=$this->getDefaultUrl(false).'&link_filter='.$this->statuses[$mystat]['key'];

                $this->modelObj->countRows=$this->modelObj->countRows+$count['count'];
                $this->viewObj->statuses=$this->statuses;
            }

        }else{
            $this->modelObj->countRows=$this->modelObj->count();
        }




        if(isset($_REQUEST['orderby'])){
            $this->modelObj->orderBy($_REQUEST['orderby'],strtoupper($_REQUEST['ordert']));
        }else{
            $this->modelObj->orderBy($this->modelObj->getPk(),'DESC');
        }
        $this->modelObj->limitON=true;

        $data=$this->modelObj->getRows($this->list_columns);

        $methodDefaultData='defaultData';
        if(method_exists($this,$methodDefaultData )){
            $this->$methodDefaultData($data);
        }

    }

    function defaultData($data){
        $this->data=$data;
    }


    function render(){

        $this->viewObj->render($this->viewShow,$this->data);
    }

    /**
     * by default this is the first method called from a controller this is from where we route to other methods
     */
    function main(){
        $this->__construct();
        if($this->model){
            if(isset($_REQUEST['action']))  $action=$_REQUEST['action'];
            else  $action='defaultDisplay';
            if(!$action) $action='defaultDisplay';

            if($action){
                $this->_tryAction($action);
            }
        }else{
            $this->error('No Model is linked to this controller : '. get_class($this));
            return false;
        }

        return true;
    }

    function __setMetaTitle(){
        global $title;

        if(isset($this->title))$title=$this->title;
        else $title=$this->viewObj->title;
    }

    function _tryAction($action){
        $action=strip_tags($action);
        $_REQUEST   = stripslashes_deep($_REQUEST);
        $_POST   = stripslashes_deep($_POST);

        $is_batch_select = $this->_batchSelect();
        $this->_affected_rows = $is_batch_select ? $this->_batch_select['count'] : (!empty($_REQUEST['wysija']['user']['user_id']) ? count($_REQUEST['wysija']['user']['user_id']) : 0);
        if(method_exists($this, $action)){
            /* in some bulk actions we need to specify the action name and one or few variables*/
            $this->action=$action;
            $this->viewShow=$this->action;
            if(!$this->viewShow) $this->viewShow='defaultDisplay';

            if(strpos($action, 'bulk_')===false)$this->$action();
            else {
                $this->$action($_REQUEST['wysija'][$this->model][$this->modelObj->pk]);
            }

            $this->__setMetaTitle();
        }else{
            /* in some bulk actions we need to specify the action name and one or few variables*/
            if(strpos($action,'actionvar_')!== false){
                $data=explode('-',$action);
                $datas=array();

                foreach($data as $dt){
                    $res=explode('_',$dt);
                    $datas[$res[0]]=$res[1];
                }

                $action =$datas['actionvar'];
                unset($datas['actionvar']);
                $this->action=$action;

                if(method_exists($this, $this->action)){
                    $this->viewShow=$this->action;
                    $this->$action($datas);
                    $this->__setMetaTitle();

                }else{
                    $this->error("Action '".$action."' does not exist in controller : ". get_class($this));
                    $this->redirect();
                }
            }else{
                $this->error("Action '".$action."' does not exist in controller : ". get_class($this));
                $this->redirect();
                //$this->defaultDisplay();
            }

        }

        if(defined('WYSIJA_REDIRECT'))  $this->redirectProcess();

        if( !empty( $_REQUEST['page'] ) && $_REQUEST['page'] !== 'wysija_premium'){
            $this->checkTotalSubscribers();
        }

    }

    function checkTotalSubscribers(){
        add_action('wysija_check_total_subscribers',array($this,'_checkTotalSubscribers'));
        do_action('wysija_remove_action_check_total_subscribers');
        do_action('wysija_check_total_subscribers');
    }

    /**
     * Batch select process
     * - Currently, is for subscribers only
     * - Get all matched subscribers and override to $_REQUEST['wysija']['user']['user_id']
     */
    function _batchSelect(){
        if(empty($_REQUEST['wysija']['user']['force_select_all']))
            return FALSE;
        if (!(bool)$_REQUEST['wysija']['user']['force_select_all'])
            return FALSE;
        if(empty($_REQUEST['wysija']['user']['timestamp']))
            return FALSE;
        //$_POST['wysija']['filter'] = array(
        //  link_filter => '', //[subscribed, unsubscribed, unsubscribed, all]
        //  filter_list => int
        //);
        //
        //$_POST['wysija']['user']['timestamp'] = int
        //
        //select all users which match to $_POST['wysija']['filter'] and create_at <= $_POST['wysija']['user']['timestamp']
        // - build query

        $select = array( '[wysija]user.user_id');

        // filters for unsubscribed
        $filters = $this->modelObj->detect_filters();


        $this->_batch_select = array();


        $this->_batch_select['query'] = $this->modelObj->get_subscribers( $select, $filters, '', true );
        $this->_batch_select['query_count'] = $this->modelObj->get_subscribers( array( 'COUNT(DISTINCT([wysija]user.user_id))'), $filters, '', true );


        //Create a temporary table
        $temp_table_name = '[wysija]user'. time();
        $temp_table_create = 'CREATE TEMPORARY TABLE IF NOT EXISTS '.$temp_table_name . ' (user_id int (10) NOT NULL, PRIMARY KEY (user_id))';
        $temp_table_insert = 'INSERT IGNORE INTO '.$temp_table_name.' ' . $this->_batch_select['query'];
        $model_user = WYSIJA::get('user','model');

	$model_user->query($temp_table_create);
	$model_user->query($temp_table_insert);

        //Override the queres with temporary table
        unset($this->_batch_select['where']);
        $row_count = $model_user->query('get_row', 'SELECT COUNT(*) as row_count FROM '.$temp_table_name);
        $this->_batch_select['original_query'] = $this->_batch_select['query']; // useful for export feature; in this case, we don't use temporary table
        $this->_batch_select['select'] = 'SELECT DISTINCT user_id';
        $this->_batch_select['from'] = 'FROM '.$temp_table_name . ' A';
        $this->_batch_select['query'] = 'SELECT user_id FROM '.$temp_table_name;
        $this->_batch_select['count'] = $row_count['row_count'];
        return true;
    }



    function _checkTotalSubscribers(){

        $config=WYSIJA::get('config','model');
        $totalSubscribers=$config->getValue('total_subscribers');
        $helper_licence = WYSIJA::get('licence','helper');

        if((int)$totalSubscribers>1900){
            if((int)$totalSubscribers>2000){

                $url_checkout = $helper_licence->get_url_checkout('over200');
                $this->error(str_replace(array('[link]','[/link]'),
                array('<a title="'.__('Get Premium now',WYSIJA).'" target="_blank" href="'.$url_checkout.'">','</a>'),
                sprintf(__('Yikes. You\'re over the limit of 2000 subscribers for the free version of MailPoet (%1$s in total). Sending is disabled now. Please upgrade your version to [link]premium[/link] to send without limits.',WYSIJA)
                        ,$totalSubscribers)),true);

            }else{
                $url_checkout = $helper_licence->get_url_checkout('near200');
                $this->notice(str_replace(array('[link]','[/link]'),
                    array('<a title="'.__('Get Premium now',WYSIJA).'" target="_blank" href="'.$url_checkout.'">','</a>'),
                    sprintf(__('Yikes! You\'re near the limit of %1$s subscribers for MailPoet\'s free version. Upgrade to [link]Premium[/link] to send without limits, and more.',WYSIJA)
                            ,"2000")));
            }
        }
    }

    function edit($id=false){

        if(isset($_REQUEST['id']) || $id){
            if(!$id) $id=$_REQUEST['id'];
            $this->data[$this->modelObj->table_name]=$this->modelObj->getOne($this->form_columns,array($this->modelObj->pk=>$id));
        }else{
            $this->error('Cannot edit element primary key is missing : '. get_class($this));
        }

    }

    function view($id=false){

        if(isset($_REQUEST['id']) || $id){
            if(!$id) $id=$_REQUEST['id'];
            $this->data[$this->modelObj->table_name]=$this->modelObj->getOne($this->form_columns,array($this->modelObj->pk=>$id));

        }else{
            $this->error('Cannot view element primary key is missing : '. get_class($this));
        }

    }

    function add($dataPost=false){

        if(!$dataPost){
            $data=array();
            foreach($this->form_columns as $key){
                $data[$key]='';
            }
        }else{

            $data=array();
            foreach($this->form_columns as $key){
                if($key != $this->viewObj->model->pk)  $data[$key]=$dataPost[$key];
            }
            $data[$this->viewObj->model->pk]='';
        }

    }

    function save(){
        $this->requireSecurity();
        //see if it's an update or an insert
        //get the pk and its value as a conditions where pk = pkval
        $conditions=$this->getPKVal($this->modelObj);

        if($conditions){
            //this an update

            $result=$this->modelObj->update($_POST['wysija'][$this->model],$conditions);

            if($this->msgOnSave){

                // Create the update success message and add edit again link.
                $update_success = str_replace(array('[link]','[/link]'),array('<a href="admin.php?page=wysija_subscribers&action=edit&id='.$result.'" >',"</a>"), $this->messages['update'][true]);

                if ($result) {
                    $this->notice($update_success);
                } else {
                    if($result==0){

                    }else{
                        $this->error($this->messages['update'][false],true);
                    }

                }
            }


            if($this->redirectAfterSave){
                if(isset($this->modelObj->stay)){
                    $this->action='edit';
                    $this->redirect();
                }else{
                    $this->action='edit';
                    $this->redirect();
                }
            }

        }else{
            //this is an insert
            unset($_POST['wysija'][$this->model][$this->modelObj->pk]);
            $result=$this->modelObj->insert($_POST['wysija'][$this->model]);

            if($this->msgOnSave){
                if($result) $this->notice($this->messages['insert'][true]);
                else{
                    $this->error($this->messages['insert'][false],true);
                }
            }


            if($this->redirectAfterSave){
                if(isset($this->modelObj->stay)){
                    $this->action='add';
                    $this->add($_POST['wysija'][$this->model]);
                }else{
                    $this->action='main';
                    $this->redirect();
                }
            }

        }

        //now we redirect to the edit page with the data in it
        return $result;
    }

    function bulk_delete($ids){
        $this->requireSecurity();
        foreach($ids as $id){

            $conditions=$this->getPKVal($this->modelObj);
            if(!$conditions) $this->error('Cannot obtain PKVal from GET or POST.');

            $result=$this->modelObj->delete($conditions);
            $this->modelObj->reset();
        }
        $this->notice(__('Elements deleted',WYSIJA));
        $this->redirect();
    }

    function delete(){
        // see if it's an update or an insert
        $this->requireSecurity();
        $conditions=$this->getPKVal($this->modelObj);
        if(!$conditions) $this->error('Cannot obtain PKVal from GET or POST.');

        $result=$this->modelObj->delete($conditions);
        if($result){
            $this->notice(__('Element has been deleted.',WYSIJA));
        }


        $this->modelObj->reset();
        //now we redirect to the edit page with the data in it
        $this->action='main';
        $this->redirect();
    }

    function redirect($location=false){
        global $wysi_location;
        define('WYSIJA_REDIRECT',true);
        if($location)
        {
            $url = parse_url($location);
            if(!empty($url['query'])) {
                $location .= '&';
            } else {
                $location .= '?';
            }

            $location .= 'redirect=1';
        }
        $wysi_location=$location;
    }

    function redirectProcess(){
        global $wysi_location;

        if(!$wysi_location)  {
            $wysi_location=$this->getDefaultUrl();
        }
        WYSIJA::redirect($wysi_location);

    }

    function popupReturn($viewFunc) {
        return wp_iframe( array($this->viewObj,'popup_'.$viewFunc), $this->data);
    }

    function _addTab($defaulttab){
        return $this->iframeTabs;
    }

    function popupContent(){
        // remove auth check
        remove_action('admin_enqueue_scripts', 'wp_auth_check_load');

        // add popup css
        wp_enqueue_style('custom_popup_css', WYSIJA_URL.'css/adminPopup.css', array(), WYSIJA::get_version(), 'screen');

        global $viewMedia;
        $viewMedia=$this->viewObj;
        $_GET['type']=$_REQUEST['type']='image';

        $config=WYSIJA::get('config','model');
        $_GET['post_id']=$_REQUEST['post_id']=$config->getValue('confirm_email_link');
        $post_id = isset($_GET['post_id'])? (int) $_GET['post_id'] : 0;
        if(file_exists(ABSPATH.'wp-admin'.DS.'admin.php')) require_once(ABSPATH.'wp-admin'.DS.'admin.php');

        @header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

        add_filter('media_upload_tabs', array($this,'_addTab'));

        if(!isset($this->iframeTabs)) {


            $this->iframeTabs=array(
            'special_new_wordp_upload'=>__('Upload',WYSIJA));

            $this->iframeTabs['special_wysija_browse']=__('Newsletter Images',WYSIJA);
            $this->iframeTabs['special_wordp_browse']=__('WordPress Posts\' Images',WYSIJA);

            foreach($this->iframeTabs as $actionKey =>$actionTitle)
                add_action('media_upload_'.$actionKey, array($this,$actionKey));
        }else   add_action('media_upload_standard', array($this,'popupReturn'));

        // upload type: image, video, file, ..?
        if ( isset($_GET['type']) )
                $type = strval($_GET['type']);
        else
                $type = apply_filters('media_upload_default_type', 'file');

        // tab: gallery, library, or type-specific
        if ( isset($_GET['tab']) )
                $tab = strval($_GET['tab']);
        else
                $tab ='special_wysija_browse';

        $body_id = 'media-upload';
        // let the action code decide how to handle the request
        if ( $tab == 'type' || $tab == 'type_url' )
            //i'm not so sure we need that line
            do_action("media_upload_$type");
        else{
            if(strpos($tab, 'special_')!==false){
                do_action("media_upload_$tab");
            }else{
                do_action('media_upload_standard',$tab);
            }
        }

        exit;

    }

    function getDefaultUrl($filter=true){
        $location="admin.php?page=".$_REQUEST['page'];

        if($filter){
            if(isset($_REQUEST['search']) && $_REQUEST['search']){
                $location.='&search='.$_REQUEST['search'];
            }

            if(isset($_REQUEST['filter-list']) && $_REQUEST['filter-list']){
                $location.='&filter-list='.$_REQUEST['filter-list'];
            }

            if(isset($_REQUEST['link_filter']) && $_REQUEST['link_filter']){
                $location.='&link_filter='.$_REQUEST['link_filter'];
            }

            if(isset($_REQUEST['orderby']) && $_REQUEST['orderby']){
                $location.='&orderby='.$_REQUEST['orderby'];
            }

            if(isset($_REQUEST['ordert']) && $_REQUEST['ordert']){
                $location.='&ordert='.$_REQUEST['ordert'];
            }
        }

        return $location;
    }

    /**
     * to remove the conflicts in wysija's interfaces
     * @param boolean $themes
     */
    function get_conflictive_plugins($themes=false){

        /**
         * List of all the conflictive extensions which invite themselves on our interfaces and break some of our js:
         * tribulant newsletter
         */
        $conflictivePlugins = array(
            'tribulant-wp-mailinglist' => array(
                'file' => 'wp-mailinglist/wp-mailinglist.php',
                'version' => '3.8.7',
                'clean' => array(
                    'admin_head' => array(
                        '10' => array(
                            'objects' => array('wpMail')
                        )
                    )
                )
            ),
            'wp-events' => array(
                'file' => 'wp-events/wp-events.php',
                'version' => '',
                'clean' => array(
                    'admin_head' => array(
                        '10' => array(
                            'function' => 'events_editor_admin_head'
                        )
                    )
                )
            ),
            'email-users' => array(
                'file' => 'email-users/email-users.php',
                'version' => '',
                'clean' => array(
                    'admin_head' => array(
                        '10' => array(
                            'function' => 'editor_admin_head'
                        )
                    )
                )
            ),
            'acf' => array(
                'file' => 'advanced-custom-fields/acf.php',
                'version' => '3.1.7',
                'clean' => array(
                    'init' => array(
                        '10' => array(
                            'objects' => array('Acf')
                        )
                    )
                )
            ),
            'wptofacebook' => array(
                'file' => 'wptofacebook/index.php',
                'version' => '1.2.3',
                'clean' => array(
                    'admin_head' => array(
                        '10' => array(
                            'function' => 'WpToFb::wptofb_editor_admin_head'
                        )
                    )
                )
            ),
            'mindvalley-pagemash' => array(
                'file' => 'mindvalley-pagemash/pagemash.php',
                'version' => '1.1',
                'clean' => array(
                    'admin_print_scripts' => array(
                        '10' => array(
                            'function' => 'pageMash_head'
                        )
                    )
                )
            ),
            'wp-polls' => array(
                'file' => 'wp-polls/wp-polls.php',
                'version' => '2.63',
                'clean' => array(
                    'wp_enqueue_scripts' => array(
                        '10' => array(
                            'function' => 'poll_scripts'
                        )
                    )
                )
            ),
            'wp_rokajaxsearch' => array(
                'file' => 'wp_rokajaxsearch/rokajaxsearch.php',
                'version' => '',
                'clean' => array(
                    'init' => array(
                        '-50' => array(
                            'function' => 'rokajaxsearch_mootools_init'
                        )
                    )
                )
            ),
            'wp_rokstories' => array(
                'file' => 'wp_rokstories/rokstories.php',
                'version' => '',
                'clean' => array(
                    'init' => array(
                        '-50' => array(
                            'function' => 'rokstories_mootools_init'
                        )
                    )
                )
            ),
            'simple-links' => array(
                'file' => 'simple-links/simple-links.php',
                'version' => '1.5',
                'clean' => array(
                    'admin_print_scripts' => array(
                        '10' => array(
                            'objects' => array('simple_links_admin')
                        )
                    )
                )
            )
        );

        $conflictiveThemes = array(
            'smallbiz' => array(
                'clean' => array(
                    'admin_head' => array(
                        '10' => array(
                            'function' => 'smallbiz_on_admin_head'
                        )
                    )
                )
            ),
            'balance' => array(
                'clean' => array(
                    'admin_enqueue_scripts' => array(
                        '10' => array(
                            'functions' => array('al_admin_scripts', 'al_adminpanel_scripts', 'al_pricing_tables_scripts')
                        )
                    ),
                    'admin_head' => array(
                        '10' => array(
                            'function' => 'al_admin_head'
                        )
                    )
                )
            )
        );

        if($themes) return $conflictiveThemes;
        return $conflictivePlugins;
    }
}
