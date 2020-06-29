<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_control_back_subscribers extends WYSIJA_control_back{
    var $model='user';
    var $view='subscribers';
    var $list_columns=array('user_id','firstname', 'lastname','email','created_at');
    var $searchable=array('email','firstname', 'lastname');
    var $_separators = array(',', ';'); // csv separator; comma is for standard csv, semi-colon is good for Excel
    var $_default_separator = ';';

    /**
     * Inactive users = users who never opened or clicked
     * @todo: disabled on 2.6. Once it's enabled, please double check in term of "inactive users".
     * OR - users who never opened or clicked
     * OR - users who never opened or clicked AND received at least 1 newsletter.
     * @var boolean
     */
    var $_filter_by_inactive_users = true;

    function __construct() {
        global $wpdb;
		WYSIJA_control_back::__construct();
		if ($this->_filter_by_inactive_users) {
                        // load the inactive subscribers on the listing and when a any bulk action is executed
                        if (    empty($_REQUEST['action'])
                                ||
                                (       !empty($_REQUEST['action'])
                                        &&
                                        ( in_array($_REQUEST['action'], array('export_get','exportlist', 'deleteusers'))
                                        ||
                                        substr($_REQUEST['action'], 0,10)=='actionvar_')
                                )
                        ) {
				$this->modelObj->prepare_inactive_users_table();
			}
		}
        $this->wpdb = $wpdb;
	}

    /*
     * common task to all the list actions
     */
    private function _commonlists(){
        $this->js[]='wysija-validator';

        $this->data=array();
        $this->data['list']=$this->_getLists(10);

    }

        /**
     * We are using the same form for different purposes:
     * - Bulk actions
     * - Filter by list
     * We need to remove all un-necessary values from interface
     */
    private function _cleanup_form() {
	if (!empty($_REQUEST['doaction'])) {
	    $action_type = strtolower(trim($_REQUEST['doaction']));
	    switch ($action_type)
	    {
		// Filter by list
		case 'filter':
		    if (!empty($_REQUEST['wysija']['user']))
			unset($_REQUEST['wysija']['user']);
		    if (!empty($_REQUEST['action']))
			unset($_REQUEST['action']);
		    break;
		// Bulk action. Nothing to do, because we will invoke _tryAction() directly right after this step
		case 'apply':
		default:
		    break;
	    }
	}
    }

    private function _getLists($limit=false){

        $model_list = WYSIJA::get('list','model');
        $model_list->escapingOn=true;
        $model_list->_limitison=$limit;
        return $model_list->getLists();
    }

    private function _getForm($id=false){
        if($id){
            $model_list = WYSIJA::get('list','model');

            return $model_list->get_one_list($id);
        }else{
            $array=array('name'=>'','list_id'=>'','description'=>'','is_public'=>true,'is_enabled'=>true);
            return $array;
        }

    }

        /**
     * Get selected lists
     * @return array
     */
    private function _get_selected_lists() {
        $result = array();
        if (isset($_REQUEST['wysija']['filter']['filter_list'])) {
            $result[] = $_REQUEST['wysija']['filter']['filter_list'];
        } elseif (!empty($_REQUEST['filter-list'])) {
            $lists = explode(',', trim($_REQUEST['filter-list']));// currently, only single list is allowed.
            if (!empty($lists)) {
                $result = array_merge ($result, $lists);
            }
        }
        return $result;
    }

    function save(){
        $this->redirectAfterSave=false;
        $this->requireSecurity();
        $helper_user = WYSIJA::get('user','helper');
        if( isset( $_REQUEST['id'] ) ){
            $id = $_REQUEST['id'];
            parent::save();

            //run the unsubscribe process if needed
            if((int)$_REQUEST['wysija']['user']['status']==-1){
                $helper_user->unsubscribe($id);
            }

            /* update subscriptions */
            $model_user_list = WYSIJA::get('user_list','model');
            $model_user_list->backSave=true;
            /* list of core list */
            $model_list = WYSIJA::get('list','model');
            $results = $model_list->get(array('list_id'),array('is_enabled'=>'0'));
            $core_listids = array();
            foreach($results as $res){
                $core_listids[]=$res['list_id'];
            }

            //0 - get current lists of the user
            $userlists = $model_user_list->get(array('list_id','unsub_date'),array('user_id'=>$id));

            $oldlistids=$newlistids=array();
            foreach($userlists as $listdata)    $oldlistids[$listdata['list_id']]=$listdata['unsub_date'];

            $model_config = WYSIJA::get('config','model');
            $dbloptin = $model_config->getValue('confirm_dbleoptin');
            //1 - insert new user_list
            if(isset($_POST['wysija']['user_list']) && $_POST['wysija']['user_list']){
                $model_user_list->reset();
                $model_user_list->update(array('sub_date'=>time()),array('user_id'=>$id));
                if(!empty($_POST['wysija']['user_list']['list_id'])){
                    foreach($_POST['wysija']['user_list']['list_id'] as $list_id){
                        //if the list is not already recorded for the user then we will need to insert it
                        if(!isset($oldlistids[$list_id])){
                            $model_user_list->reset();
                            $newlistids[]=$list_id;
                            $dataul=array('user_id'=>$id,'list_id'=>$list_id,'sub_date'=>time());
                            //if double optin is on and user is unconfirmed or unsubscribed, then we need to set it as unconfirmed subscription
                            if($dbloptin && (int)$_POST['wysija']['user']['status']<1)  unset($dataul['sub_date']);
                            $model_user_list->insert($dataul);
                        //if the list is recorded already then let's check the status, if it is an unsubed one then we update it
                        }else{
                            if($oldlistids[$list_id]>0){
                                $model_user_list->reset();
                                $model_user_list->update(array('unsub_date'=>0,'sub_date'=>time()),array('user_id'=>$id,'list_id'=>$list_id));
                            }
                        }
                    }
                }

            }else{
                // if no list is selected we unsubscribe them all
                $model_user_list->reset();
                $model_user_list->update(array('unsub_date'=>time(),'sub_date'=>0),array('user_id'=>$id));
            }

            //if a confirmation email needs to be sent then we send it
            if($dbloptin && (int)$_POST['wysija']['user']['status']==0 && !empty($newlistids)){
                $helper_user = WYSIJA::get('user','helper');
                $helper_user->sendConfirmationEmail($id,true,$newlistids);
            }

            if((int)$_POST['wysija']['user']['status']==0 || (int)$_POST['wysija']['user']['status']==1){
                $model_user_list->reset();
                $model_user_list->update(array('unsub_date'=>0,'sub_date'=>time()),array('user_id'=>$id,'list_id'=>$core_listids));
            }

            $arrayLists=array();
            if(isset($_POST['wysija']['user_list']['list_id'])) $arrayLists=$_POST['wysija']['user_list']['list_id'];
            $notEqual=array_merge($core_listids, $arrayLists);

            //unsubscribe from lists which exist in the old list but does not exist in the new list
            $unsubsribe_list = array_diff(array_keys($oldlistids), $arrayLists);
            if(!empty($unsubsribe_list))
            {
                $model_user_list->reset();
                $model_user_list->update(array('unsub_date'=>time()),array('user_id'=>$id,'list_id'=>$unsubsribe_list));
            }
            $model_user_list->reset();

            /*
            Custom Fields.
            */
            if (isset($_POST['wysija']['field'])) {
              WJ_FieldHandler::handle_all(
                $_POST['wysija']['field'], $id
              );
            }


        }else{
            //instead of going through a classic save we should save through the helper
            $data=$_REQUEST['wysija'];
            $data['user_list']['list_ids'] = !empty($data['user_list']['list_id']) ? $data['user_list']['list_id'] : array();
            unset($data['user_list']['list_id']);
            $data['message_success']=__('Subscriber has been saved.',WYSIJA);
            $id=$helper_user->addSubscriber($data,true);
            if(!$id) {
                $this->viewShow=$this->action='add';
                $data=array('details'=>$_REQUEST['wysija']['user']);
                return $this->add($data);
            } else {
                if(isset($_POST['wysija']['field'])) {
                    WJ_FieldHandler::handle_all($_POST['wysija']['field'], $id);
                }
            }
        }
        $this->redirect();
        return true;
    }


    function defaultDisplay(){
        $this->viewShow=$this->action='main';
        $this->js[]='wysija-admin-list';
        $this->viewObj->msgPerPage = __('Subscribers per page:',WYSIJA);

        $this->jsTrans['selecmiss'] = __('Select at least 1 subscriber!',WYSIJA);

        // get the total count for subscribed, unsubscribed and unconfirmed users
        $select = array( 'COUNT(`user_id`) AS users' , 'status' , 'MAX(`created_at`) AS `max_create_at`');
        $count_group_by = 'status';
        $count_by_status = $this->modelObj->get_subscribers( $select , array() , $count_group_by );
	if ($this->_filter_by_inactive_users) {
	    $inactive_users = $this->modelObj->count_inactive_users();
	    if ($inactive_users) {
		array_unshift($count_by_status, array(
		    'users' => $inactive_users['count'],
		    'status' => -99,//-99 = inactive
		    'max_create_at' => $inactive_users['max_created_at']
		));
	    }
	}


        $counts = $this->modelObj->structure_user_status_count_array($count_by_status);
        $arr_max_create_at = $this->modelObj->get_max_create($count_by_status);

        // count the rows based on the filters
        $filters = $this->modelObj->detect_filters();

        $select = array( 'COUNT(DISTINCT([wysija]user.user_id)) as total_users', 'MAX([wysija]user.created_at) as max_create_at');
        $count_rows = $this->modelObj->get_subscribers( $select, $filters);

        // without filter we already have the total number of subscribers
        $this->data['max_create_at'] = null; //max value of create_at field of current list of users
        if(!empty($filters)){
            // used for pagination
            $this->modelObj->countRows = $count_rows['total_users'];
            // used for
            $this->data['max_create_at'] = $count_rows['max_create_at'];
        }else{
            $this->data['max_create_at'] = !empty($arr_max_create_at) ? max($arr_max_create_at) : 0;
            $this->modelObj->countRows=$counts['all'];
        }

        $select = array(
	    '[wysija]user.firstname',
	    '[wysija]user.lastname',
	    '[wysija]user.status',
	    '[wysija]user.email',
	    '[wysija]user.created_at',
	    '[wysija]user.last_opened',
	    '[wysija]user.last_clicked',
	    '[wysija]user_list.user_id'
	    );

        $this->data['subscribers'] = $this->modelObj->get_subscribers($select , $filters, '', false, true);

        $this->data['current_counts'] = $this->modelObj->countRows;
        $this->data['show_batch_select'] = ($this->modelObj->limit >= $this->modelObj->countRows) ? false : true;
        $this->data['selected_lists'] = $this->_get_selected_lists();
        $this->modelObj->reset();


        // make the data object for the listing view
        $model_list = WYSIJA::get('list','model');
        $lists_db = $model_list->getLists();

        $lists=array();

        foreach($lists_db as $listobj){
            $lists[$listobj['list_id']]=$listobj;
        }

        $user_ids=array();
        foreach($this->data['subscribers'] as $subscriber){
            $user_ids[]=$subscriber['user_id'];
        }

        // 3 - user_list request
        if($user_ids){
            $model_user_list = WYSIJA::get('user_list','model');
            $userlists=$model_user_list->get(array('list_id','user_id','unsub_date'),array('user_id'=>$user_ids));
        }

        $this->data['lists']=$lists;
        $this->data['counts']=array_reverse($counts);

        // regrouping all the data in the same array
       foreach($this->data['subscribers'] as $keysus=>$subscriber){
            // default key while we don't have the data
            //TODO add data for stats about emails opened clicked etc
            $this->data['subscribers'][$keysus]['emails']=0;
            $this->data['subscribers'][$keysus]['opened']=0;
            $this->data['subscribers'][$keysus]['clicked']=0;

            if($userlists){
                foreach($userlists as $key=>$userlist){
                    if($subscriber['user_id']==$userlist['user_id'] && isset($lists[$userlist['list_id']])){
                        //what kind of list ist it ? unsubscribed ? or not

                        if($userlist['unsub_date']>0){
                            if(!isset($this->data['subscribers'][$keysus]['unsub_lists']) ){
                                $this->data['subscribers'][$keysus]['unsub_lists']=$this->data['lists'][$userlist['list_id']]['name'];
                            }else{
                                $this->data['subscribers'][$keysus]['unsub_lists'].=', '.$this->data['lists'][$userlist['list_id']]['name'];
                            }
                       }else{
                            if(!isset($this->data['subscribers'][$keysus]['lists']) ){
                                $this->data['subscribers'][$keysus]['lists']=$this->data['lists'][$userlist['list_id']]['name'];
                            }else{
                                $this->data['subscribers'][$keysus]['lists'].=', '.$this->data['lists'][$userlist['list_id']]['name'];
                            }
                        }
                    }
                }
            }
        }
        if(!$this->data['subscribers']){
            $this->notice(__('Yikes! Couldn\'t find any subscribers.',WYSIJA));
        }

    }

    function main(){
        $this->messages['insert'][true]=__('Subscriber has been saved.',WYSIJA);
        $this->messages['insert'][false]=__('Subscriber has not been saved.',WYSIJA);
        $this->messages['update'][true]=__('Subscriber has been modified. [link]Edit again[/link].',WYSIJA);
        $this->messages['update'][false]=__('Subscriber has not been modified.',WYSIJA);
    	$this->_cleanup_form();
        parent::__construct();

        //we change the default model of the controller based on the action
        if(isset($_REQUEST['action'])){
            switch($_REQUEST['action']){
                case 'listsedit':
                case 'savelist':
                case 'lists':
                    $this->model='list';
                    break;
                default:
                    $this->model='user';
            }
        }

        WYSIJA_control::__construct();
        if(!isset($_REQUEST['action']) || !$_REQUEST['action']) {
            $this->defaultDisplay();
            $this->checkTotalSubscribers();
        } else {
            $this->_tryAction($_REQUEST['action']);
        }

    }

    /**
     * bulk action copy to list
     * @global type $wpdb
     * @param type $data
     */
    function copytolist($data){
        $this->requireSecurity();
        $helper_user = WYSIJA::get('user','helper');
        if(empty($this->_batch_select))
            $helper_user ->addToList($data['listid'],$_POST['wysija']['user']['user_id']);
        else
            $helper_user ->addToList($data['listid'],$this->_batch_select, true);

        $model_list = WYSIJA::get('list','model');
        $result = $model_list->getOne(array('name'),array('list_id'=>$data['listid']));

        if($this->_affected_rows > 1)
            $this->notice(sprintf(__('%1$s subscribers have been added to "%2$s".',WYSIJA),$this->_affected_rows,$result['name']));
        else
            $this->notice(sprintf(__('%1$s subscriber has been added to "%2$s".',WYSIJA),$this->_affected_rows,$result['name']));
        $this->redirect_after_bulk_action();
    }

    /**
     * Moves subscriber to another list.
     *
     * @param array $data List id to move, $data = array('listid' => 1);
     */
    function movetolist($data) {
        $this->requireSecurity();
        $helper_user = WYSIJA::get('user', 'helper');

        if (!empty($this->_batch_select)) {
            $helper_user->moveToList($data['listid'], $this->_batch_select, true);
        } elseif (isset($_POST['wysija']['user']['user_id'])) {
            $helper_user->moveToList($data['listid'], $_POST['wysija']['user']['user_id']);
        }

        $model_list = WYSIJA::get('list','model');
        $result = $model_list->getOne(array('name'), array('list_id' => $data['listid']));

        if ($this->_affected_rows > 1) {
            $this->notice(sprintf(__('%1$s subscribers have been moved to "%2$s".',WYSIJA), $this->_affected_rows, $result['name']));
        } else {
            $this->notice(sprintf(__('%1$s subscriber has been moved to "%2$s".',WYSIJA), $this->_affected_rows, $result['name']));
        }

        $this->redirect_after_bulk_action();
    }

	/**
	 * After performing a bulk action, let's keep the current list filter
	 */
	protected function redirect_after_bulk_action() {
		$filter_list = !empty($_REQUEST['wysija']['filter']['filter_list']) ? $_REQUEST['wysija']['filter']['filter_list'] : 0;
		if (empty($filter_list)) {// view all lists
			$this->redirect('admin.php?page=wysija_subscribers');
		} elseif (is_numeric($filter_list)) {
			$this->redirect('admin.php?page=wysija_subscribers&filter-list='.$filter_list);
		} else {// subscribers in no list
			$this->redirect('admin.php?page=wysija_subscribers&filter-list=orphaned');
		}
	}

    /**
     * Bulk action remove subscribers from all existing lists
     * @param type $data = array('list_id'=>?)
     */
    function removefromalllists($data){
        $this->requireSecurity();
        $helper_user = WYSIJA::get('user','helper');
        if(!empty($this->_batch_select))
            $helper_user->removeFromLists(array(),$this->_batch_select, true);
        else
            $helper_user->removeFromLists(array(),$_POST['wysija']['user']['user_id']);

        if($this->_affected_rows > 1)
            $this->notice(sprintf(__('%1$s subscribers have been removed from all existing lists.',WYSIJA),$this->_affected_rows));
        else
            $this->notice(sprintf(__('%1$s subscriber has been removed from all existing lists.',WYSIJA),$this->_affected_rows));
        $this->redirect_after_bulk_action();
    }

    /**
     * Bulk action remove subscribers from all existing lists
     * @param type $data = array('list_id'=>?)
     */
    function removefromlist($data = array()){
        $this->requireSecurity();
        $helper_user = WYSIJA::get('user','helper');
        if(!empty($this->_batch_select)){
            $helper_user->removeFromLists(array($data['listid']),$this->_batch_select, true);
        }else{
            $helper_user->removeFromLists(array($data['listid']),$_POST['wysija']['user']['user_id']);
        }

        $model_list = WYSIJA::get('list','model');
        $result = $model_list->getOne(array('name'),array('list_id'=>$data['listid']));

        if($this->_affected_rows > 1){
            $this->notice(sprintf(__('%1$s subscribers have been removed from "%2$s".',WYSIJA),$this->_affected_rows, $result['name']));
        }else{
            $this->notice(sprintf(__('%1$s subscriber has been removed from "%2$s".',WYSIJA),$this->_affected_rows, $result['name']));
        }

        $this->redirect_after_bulk_action();
    }

    /**
     * Bulk confirm users
     */
    function confirmusers(){
        $this->requireSecurity();
        $helper_user = WYSIJA::get('user','helper');
        if(!empty($this->_batch_select)){
            $helper_user->confirmUsers($this->_batch_select, true);
        }else{
            $helper_user->confirmUsers($_POST['wysija']['user']['user_id']);
        }

        if($this->_affected_rows > 1){
            $this->notice(sprintf(__('%1$s subscribers have been confirmed.',WYSIJA),$this->_affected_rows));
        }else{
           $this->notice(sprintf(__('%1$s subscriber has been confirmed.',WYSIJA),$this->_affected_rows));
        }

        $this->redirect_after_bulk_action();
    }

	/*
     * Bulk resend confirmation emails
	 * Maximum emails to be sent is 100, and only send to unconfirmed subscribers, of coruse.
     */
    function resendconfirmationemail() {
        $this->requireSecurity();
        $helper_user = WYSIJA::get('user','helper');
		$user_ids = array();
        if(!empty($this->_batch_select)) {
			$model_user = WYSIJA::get('user','model');
			$users = $model_user->get_results($this->_batch_select['query'] . ' LIMIT 0, 100');
			if (!empty($users)) {
				foreach ($users as $user) {
					$user_ids[] = $user['user_id'];
				}
			}
        } else {
            $user_ids = array_filter($_POST['wysija']['user']['user_id'], 'ctype_digit');
        }

		$sending_statuses = array();// array(user_id => 1/0)
		if (!empty($user_ids)) {
			$model_user_list = WYSIJA::get('user_list','model');
			$user_lists = $model_user_list->get_lists($user_ids);
			if (!empty($user_lists)) {
				foreach ($user_lists as $user_id => $lists) {
					$sending_statuses[$user_id] = $helper_user->sendConfirmationEmail($user_id, true, $lists);
				}
			}
		}

		$success_sending_number = count(array_values($sending_statuses));
		if ($success_sending_number <= 0) {
			$this->notice(__('No email sent.',WYSIJA));
		} else {
                        $this->notice( sprintf(_n( 'One email has been sent.', '%d emails have been sent to unconfirmed subscribers.', (int)$success_sending_number, WYSIJA ), $success_sending_number ) );
        }

        $this->redirect_after_bulk_action();
    }

    function lists(){
        $this->js[]='wysija-admin-list';
        $this->_commonlists();

        $this->modelObj=WYSIJA::get('list','model');
        $this->viewObj->title=__('Edit lists',WYSIJA);
        $this->modelObj->countRows=$this->modelObj->count();

        $this->viewObj->model=$this->modelObj;
        $this->data['form']=$this->_getForm();
    }

    function editlist(){
        $this->_commonlists();
        $this->data['form']=$this->_getForm($_REQUEST['id']);

        $this->viewObj->title=sprintf(__('Editing list %1$s',WYSIJA), '<b><i>'.$this->data['form']['name'].'</i></b>');
    }

    function addlist(){
        $this->_commonlists();
        $this->viewObj->title=__('How about a new list?',WYSIJA);
        $this->data['form']=$this->_getForm();
    }

    function duplicatelist(){
        $this->requireSecurity();
        /* get the list's email id
         * 0 duplicate the list's welcome email
         * 1 duplicate the list
         * 2 duplicate the list's subscribers
         */

        $model_list = WYSIJA::get('list','model');
        $data=$model_list->getOne(array('name','namekey','welcome_mail_id','unsub_mail_id'),array('list_id'=>(int)$_REQUEST['id']));

        $query='INSERT INTO `[wysija]list` (`created_at`,`name`,`namekey`,`description`,`welcome_mail_id`,`unsub_mail_id`,`is_enabled`,`ordering`)
            SELECT '.time().',concat("' . $this->wpdb->_real_escape( __( 'Copy of ', WYSIJA ) ) . '",`name`) ,"copy_'.$data['namekey'].time().'" ,`description`,0,0 ,1,`ordering` FROM [wysija]list
            WHERE list_id='.(int)$_REQUEST['id'];

        $list_id = $model_list->query($query);

        $query='INSERT INTO `[wysija]user_list` (`list_id`,`user_id`,`sub_date`,`unsub_date`)
            SELECT '.$list_id .',`user_id`,`sub_date`,`unsub_date` FROM [wysija]user_list
            WHERE list_id='.(int)$_REQUEST['id'];

        $model_list->query($query);

        $this->notice(sprintf(__('List "%1$s" has been duplicated.',WYSIJA),$data['name']));
        $this->redirect('admin.php?page=wysija_subscribers&action=lists');

    }

    function add($data=false){
        $this->js[]='wysija-validator';
        $this->viewObj->add=true;

        $this->title=$this->viewObj->title=__('Add Subscriber',WYSIJA);

        $this->data=array();
        $this->data['user']=false;
        if($data)$this->data['user']=$data;
        $model_list = WYSIJA::get('list','model');
        $model_list->limitON=false;
        $this->data['list'] = $model_list->get(false,array('greater'=>array('is_enabled'=>'0') ));

    }

    function back(){
        $this->redirect();
    }

    function backtolist(){
        $this->redirect('admin.php?page=wysija_subscribers&action=lists');
    }

    function edit($id=false){

        if (empty($_REQUEST['id']) && empty($id)){
            $this->error('Cannot edit element primary key is missing : '. get_class($this));
            return;
        }

        if(!$id) $id=$_REQUEST['id'];

        // get detail info of current user
        $this->data['user']=$this->modelObj->getDetails(array('user_id'=>$id));
        if(!$this->data['user']){
            $this->notice(__('No subscriber found, most probably because he was deleted.',WYSIJA));
            return $this->redirect();
        }

        // get list info
        $model_list=WYSIJA::get('list','model');
        $model_list->limitON=false;
        $model_list->orderBy('is_enabled','DESC');
        $this->data['list']=$model_list->get(false,array('greater'=>array('is_enabled'=>'-1') ));
        $this->viewObj->title=__('Edit',WYSIJA).' '.$this->data['user']['details']['email'];

        // execute hooks
        $hook_params = array(
            'user_id' => $id
        );
	$this->data['hooks']['hook_subscriber_left'] = apply_filters('hook_subscriber_left',WYSIJA_module::execute_hook('hook_subscriber_left', $hook_params), $hook_params);
	$this->data['hooks']['hook_subscriber_right'] = apply_filters('hook_subscriber_right',WYSIJA_module::execute_hook('hook_subscriber_right', $hook_params), $hook_params);
	$this->data['hooks']['hook_subscriber_bottom'] = apply_filters('hook_subscriber_bottom',WYSIJA_module::execute_hook('hook_subscriber_bottom', $hook_params), $hook_params);


        // prepare js, for rendering
        $this->js[]='wysija-validator';
    }

    function deletelist(){
        $this->requireSecurity();

        /* get the list's email id
         * 0 delete the welcome email corresponding to that list
         * 1 delete the list subscribers reference
         * 2 delete the list campaigns references
         * 4 delete the list
         */
        $model_list=WYSIJA::get('list','model');
        $data=$model_list->getOne(array('name','namekey','welcome_mail_id'),array('list_id'=>(int)$_REQUEST['id']));

        if($data && isset($data['namekey']) && ($data['namekey']!='users')){

            //there is no welcome email per list that's old stuff
            $model_user_list=WYSIJA::get('user_list','model');
            $model_user_list->delete(array('list_id'=>$_REQUEST['id']));

            $model_campaign_list=WYSIJA::get('campaign_list','model');
            $model_campaign_list->delete(array('list_id'=>$_REQUEST['id']));

            $model_list->reset();
            $model_list->delete(array('list_id'=>$_REQUEST['id']));

            $this->notice(sprintf(__('List "%1$s" has been deleted.',WYSIJA),$data['name']));
        }else{
            $this->error(__('The list does not exists or cannot be deleted.',WYSIJA),true);
        }

        $this->redirect('admin.php?page=wysija_subscribers&action=lists');

    }


    function synchlist(){
        $this->requireSecurity();

        $helper_user=WYSIJA::get('user','helper');
        $helper_user->synchList($_REQUEST['id']);

        $this->redirect('admin.php?page=wysija_subscribers&action=lists');
    }

    function synchlisttotal(){
        $this->requireSecurity();

        global $current_user;

        if(is_multisite() && is_super_admin( $current_user->ID )){
            $helper_user=WYSIJA::get('user','helper');
            $helper_user->synchList($_REQUEST['id'],true);
        }

        $this->redirect('admin.php?page=wysija_subscribers&action=lists');
    }


    function savelist(){
        $this->requireSecurity();
        $this->_resetGlobMsg();
        $update=false;

        if($_REQUEST['wysija']['list']['list_id']){
            $update=true;
        }
        /* save the result */
        /* 1-save the welcome email*/
        /* 2-save the list*/
        if(isset($_REQUEST['wysija']['list']['is_public'])){
            if($_REQUEST['wysija']['list']['is_public']=='on'){
                $_REQUEST['wysija']['list']['is_public']=1;
            }else{
                $_REQUEST['wysija']['list']['is_public']=0;
            }
        }

        if($update){
            $this->modelObj->update($_REQUEST['wysija']['list']);
            $this->notice(__('List has been updated.',WYSIJA));
        }else{
            $_REQUEST['wysija']['list']['created_at']=time();
            $_REQUEST['wysija']['list']['is_enabled']=1;

            $this->modelObj->insert($_REQUEST['wysija']['list']);
            $this->notice(__('Your brand-new list awaits its first subscriber.',WYSIJA));
        }


        $this->redirect('admin.php?page=wysija_subscribers&action=lists');
    }



    function importpluginsave($id=false){
        $this->requireSecurity();
        $this->_resetGlobMsg();
        $model_config = WYSIJA::get('config','model');
        $helper_import = WYSIJA::get('plugins_import','helper');
        $plugins_importable=$model_config->getValue('pluginsImportableEgg');
        $plugins_imported=array();
        foreach($_REQUEST['wysija']['import'] as $table_name =>$result){
            $connection_info=$helper_import->getPluginsInfo($table_name);

            if($result){
                $plugins_imported[]=$table_name;
                if(!$connection_info) $connection_info=$plugins_importable[$table_name];
                $helper_import->import($table_name,$connection_info);
                sleep(2);
                $this->notice(sprintf(__('Import from plugin %1$s has been completed.',WYSIJA),"<strong>'".$connection_info['name']."'</strong>"));
            }else{
                $this->notice(sprintf(__('Import from plugin %1$s has been cancelled.',WYSIJA),"<strong>'".$connection_info['name']."'</strong>"));
            }

        }

        $model_config->save(array('pluginsImportedEgg'=>$plugins_imported));

        $this->redirect('admin.php?page=wysija_subscribers&action=lists');
    }

    function importplugins($id=false){
        $this->js[]='wysija-validator';

        $this->viewObj->title=__('Import subscribers from plugins',WYSIJA);

        $model_config=WYSIJA::get('config','model');

        $this->data=array();
        $this->data['plugins']=$model_config->getValue('pluginsImportableEgg');
        $imported_plugins=$model_config->getValue('pluginsImportedEgg');

        if($imported_plugins){
            foreach($imported_plugins as $tablename){
                unset( $this->data['plugins'][$tablename]);
            }
        }


        if(!$this->data['plugins']){
            $this->notice(__('There is no plugin to import from.',WYSIJA));
            return $this->redirect();
        }
        $this->viewShow='importplugins';

    }

    function import($id=false){
        $this->js[]='wysija-validator';
        $this->viewObj->title=__('Import Subscribers',WYSIJA);
        $this->viewShow='import';
    }

    function importmatch(){
	$this->requireSecurity();
        $this->jsTrans['subscribers_import_match_confirmation_1'] = __('The selected value is already matched to another column.', WYSIJA);
	$this->jsTrans['subscribers_import_match_confirmation_2'] = __('Can you confirm that this column is corresponding to that field?', WYSIJA);
        $this->js[] = 'wysija-validator';
        $helper_numbers = WYSIJA::get('numbers','helper');
        $bytes = $helper_numbers->get_max_file_upload();

        if(isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH']>$bytes['maxbytes']){
            if(isset($_FILES['importfile']['name']) && $_FILES['importfile']['name']){
                $file_name = $_FILES['importfile']['name'];
            }else{
                $file_name = __('which you have pasted',WYSIJA);
            }

            $this->error(sprintf(__('Upload error, file %1$s is too large! (MAX:%2$s)',WYSIJA) , $file_name , $bytes['maxmegas']),true);
            $this->redirect('admin.php?page=wysija_subscribers&action=import');
            return false;
        }

        $import = new WJ_Import();
        $this->data = $import->scan_csv_file();

        if($this->data === false){
            $this->redirect('admin.php?page=wysija_subscribers&action=import');
        }

        $model_config = WYSIJA::get('config', 'model');
        $this->jsTrans['userStatuses'] = array(
                -1 => __('Unsubscribed', WYSIJA),
                0 => $model_config->getValue('confirm_dbleoptin') ? __('Unconfirmed',WYSIJA) : __('Subscribed',WYSIJA),
                1 => __('Subscribed',WYSIJA)
        );
        $this->js[] = 'wysija-import-match';
        $this->viewObj->title=__('Import Subscribers',WYSIJA);
        $this->viewShow='importmatch';

    }

    function import_save(){
        @ini_set('max_execution_time',0);

        $this->requireSecurity();
        $this->_resetGlobMsg();

        //we need to save a new list in that situation
        if(!empty($_REQUEST['wysija']['list']['newlistname'])){
            $model_list = WYSIJA::get('list','model');
            $data_list = array();
            $data_list['is_enabled'] = 1;
            $data_list['name'] = $_REQUEST['wysija']['list']['newlistname'];
            $_REQUEST['wysija']['user_list']['list'][] = $model_list->insert($data_list);
        }

        //if there is no list selected, we return to the same form prompting the user to take action
        if(!isset($_REQUEST['wysija']['user_list']['list']) || !$_REQUEST['wysija']['user_list']['list']){
            $this->error(__('You need to select at least one list.',WYSIJA),true);
            return $this->importmatch();
        }

        $import = new WJ_Import();
        $data_numbers = $import->import_subscribers();
        $duplicate_emails_count = $import->get_duplicate_emails_count();

        if($data_numbers === false){
            return $this->redirect('admin.php?page=wysija_subscribers&action=import');
        }

        //get a list of list name
        $model_list = WYSIJA::get('list','model');
        $results = $model_list->get(array('name'),array('list_id'=>$_REQUEST['wysija']['user_list']['list']));

        $list_names=array();
        foreach($results as $k =>$v){
            $list_names[]=$v['name'];
        }

        $this->notice( sprintf(__('%1$s subscribers added to %2$s.', WYSIJA),
                    $data_numbers['list_user_ids'],
                    '"'.implode('", "',$list_names).'"'
                    ) );

        if(count($duplicate_emails_count)>0){
            $list_emails = '';
            $i = 0;
            foreach($duplicate_emails_count as $email_address => $occurences){
                if( $i > 0 )$list_emails.=', ';
                $list_emails.= $email_address.' ('.$occurences.')';
                $i++;
            }
            //$emailsalreadyinserted=array_keys($emailsCount);
            $this->notice(sprintf(__('%1$s emails appear more than once in your file : %2$s.',WYSIJA),count($duplicate_emails_count),$list_emails),0);
        }

        if(count($data_numbers['invalid'])>0){
            $string = sprintf(__('%1$s emails are not valid : %2$s.',WYSIJA),count($data_numbers['invalid']), utf8_encode(implode(', ',$data_numbers['invalid'])));
            $this->notice($string,0);
        }

        $this->redirect();
    }


    function export(){
        $this->js[]='wysija-validator';

        $this->viewObj->title=__('Export Subscribers',WYSIJA);
        $this->data=array();
        //$this->data['lists']=$this->_getLists();
        $this->data['lists'] = $model_list = WYSIJA::get('list','model');
        $lists_results = $model_list->getLists();

        $lists=array();

        foreach($lists_results as $list_row){
            $lists[$list_row['list_id']]=$list_row;
        }
        $this->data['lists']=$lists;

        $this->viewShow='export';
    }

    function exportcampaign(){
        $this->requireSecurity();

        if(isset($_REQUEST['file_name'])){
            $file_name = preg_replace('#[^a-z0-9_\-.]#i','',base64_decode($_REQUEST['file_name']));

            $helper_file = WYSIJA::get('file', 'helper');
            $exported_file_link = $helper_file->url($file_name, 'temp');

            $content = file_get_contents( $exported_file_link );
            $user_ids=explode(",",$content);
        }
        $_REQUEST['wysija']['user']['user_id']=$user_ids;

        $this->exportlist();
    }


    /**
     * bulk delete option
     */
    function deleteusers(){
        $this->requireSecurity();
        $helper_user=WYSIJA::get('user','helper');
        if(!empty($this->_batch_select)){
            $helper_user->delete($this->_batch_select, false, true);
        }else{
            $helper_user->delete($_POST['wysija']['user']['user_id']);
        }

        if($this->_affected_rows > 1){
            $this->notice(sprintf(__(' %1$s subscribers have been deleted.',WYSIJA),$this->_affected_rows));
        }else{
            $this->notice(sprintf(__(' %1$s subscriber has been deleted.',WYSIJA),$this->_affected_rows));
        }

        // make sure the total count of subscribers is updated
        $helper_user->refreshUsers();
        $this->redirect_after_bulk_action();
    }

     /**
     * function generating an export file based on an array of user_ids
     */
    function export_get(){
        @ini_set('max_execution_time',0);
        $this->requireSecurity();
        $export = new WJ_Export();

        if(!empty($this->_batch_select)){
            $export->batch_select = $this->_batch_select;
        }

        $file_path_result = $export->export_subscribers();

        $this->notice(str_replace(
                array('[link]','[/link]'),
                array('<a href="'.$file_path_result['url'].'" target="_blank" class="exported-file" >','</a>'),
                sprintf(__('%1$s subscribers were exported. Get the exported file [link]here[/link].',WYSIJA),$export->get_user_ids_rows())));

        if(isset($_REQUEST['camp_id'])){
            $this->redirect('admin.php?page=wysija_campaigns&action=viewstats&id='.$_REQUEST['camp_id']);
        }else{
            $this->redirect();
        }
    }

    public function exportlist(){
        $this->requireSecurity();
        if(!empty($_REQUEST['wysija']['user']['force_select_all'])){

            $select = array( 'COUNT(DISTINCT([wysija]user.user_id)) as total_users');
            if(!empty($_REQUEST['wysija']['filter']['filter_list'])){
                $select[] =  '[wysija]user_list.list_id';
            }

            // filters for unsubscribed
            $filters = $this->modelObj->detect_filters();

            $count = $this->modelObj->get_subscribers( $select, $filters );
            $number = $count['total_users'];
        } else {
            $number = count($_REQUEST['wysija']['user']['user_id']);
        }

        $this->viewObj->title = sprintf(__('Exporting %1$s subscribers',WYSIJA),$number);
        $this->data=array();

        $this->data['subscribers'] = $_REQUEST['wysija']['user']['user_id'];
        $this->data['user'] = $_REQUEST['wysija']['user'];//for batch-selecting

        if(!empty($_REQUEST['search'])){
            $_REQUEST['wysija']['filter']['search'] = $_REQUEST['search'];
        }

        if(isset($_REQUEST['wysija']['filter'])){
            $this->data['filter'] = $_REQUEST['wysija']['filter'];//for batch-selecting
        }
        $this->viewShow = 'export';
    }

}
