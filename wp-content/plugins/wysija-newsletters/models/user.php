<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_user extends WYSIJA_model{

    var $pk='user_id';
    var $table_name='user';
    var $columns=array(
        'user_id'=>array('auto'=>true),
        'wpuser_id' => array('req'=>true,'type'=>'integer'),
        'email' => array('req'=>true,'type'=>'email'),
        'firstname' => array(),
        'lastname' => array(),
        'ip' => array('req'=>true,'type'=>'ip'),
        'keyuser' => array(),
        'status' => array('req'=>true,'type'=>'boolean'),
        'created_at' => array('auto'=>true,'type'=>'date'),
        'confirmed_ip' => array(),
        'confirmed_at' => array(),
        'last_opened' => array(),
        'last_clicked' => array(),
        'count_confirmations' => array('type'=>'integer'),
    );
    var $searchable = array('email','firstname', 'lastname');

    function __construct(){
        $this->columns['status']['label']=__('Status',WYSIJA);
        $this->columns['created_at']['label']=__('Created on',WYSIJA);
        parent::__construct();
    }

    function refresh_columns(){
        $WJ_Field = new WJ_Field();
        $custom_fields = $WJ_Field->get_all();
        if(!empty($custom_fields)){
            foreach($custom_fields as $row){
                $this->columns['cf_'.$row->id] = array();
            }
        }
    }

    function beforeInsert(){
        // set the activation key
        $model_user = WYSIJA::get( 'user' , 'model' );

        $this->values['keyuser'] = md5( AUTH_KEY . $this->values['email'] . $this->values['created_at'] );
        while( $model_user->exists( array( 'keyuser' => $this->values['keyuser'] ) ) ){
            $this->values['keyuser'] = $this->generateKeyuser( $this->values['email'] );
            $model_user->reset();
        }

        if( !isset( $this->values['status'] ) ){
            $this->values['status'] = 0;
        }

        // automatically add value to the field "domain". This is useful for statistics
        $this->values['domain'] = substr( $this->values['email'] , strpos( $this->values['email'] , '@' ) +1 );
        return true;
    }

    /**
     * Get status number of a subscriber
     * @param int $user_id
     * @return int or null
     */
    function getSubscriptionStatus($user_id){
        $this->getFormat = OBJECT;
        $result = $this->getOne( array( 'status' ) , array( 'user_id' => $user_id ) );
        return ($result->status !== NULL) ? (int)$result->status : $result->status;
    }

    /**
     * count total subscribers per list based on parameters passed
     * TODO we should use get_subscribers() instead
     * @param array $list_ids
     * @param boolean $confirmed_subscribers
     * @return int
     */
    function countSubscribers(Array $list_ids = array(), $confirmed_subscribers = true)
    {
        $model_config = WYSIJA::get('config','model');
        $confirm_dbleoptin = $model_config->getValue('confirm_dbleoptin');
        if($confirm_dbleoptin) $confirmed_subscribers = true;


        $where = array();
        $where[] = 'C.is_enabled = 1';
        $where[] = $confirmed_subscribers ? 'status = 1' : 'status >= 0';
        if(!empty($list_ids)){
            $where[] = 'C.list_id IN ('.implode(',',$list_ids).')';
        }

        $query = '
            SELECT
                COUNT(DISTINCT A.user_id)
            FROM
               [wysija]user A
            JOIN
                [wysija]user_list B ON A.user_id = B.user_id
            JOIN
                [wysija]list C ON C.list_id = B.list_id
            WHERE 1';
        if(!empty($where)) $query .= ' AND '.implode (' AND ', $where);
        return $this->count($query);
    }


    /**
     * get a user object not an array as we do by default with "getOne"
     * @param int $user_id
     * @return object
     */
    function getObject($user_id){
        $this->getFormat=OBJECT;
        return $this->getOne(false,array('user_id'=>$user_id));
    }

    /**
     * Get User object using his email address
     * @param string $email
     * @return object WYSIJA_model_user
     */
    function get_object_by_email($email){
        $this->getFormat=OBJECT;
        return $this->getOne(false,array('email'=>$email));
    }

    /**
     * get the details, lists and stats email counts regarding one user
     * @param array $conditions : MailPoet's condition format
     * @param boolean $stats : do we get the stats data of that user
     * @param boolean $subscribed_list_only :
     * @return boolean
     */
    function getDetails($conditions,$stats=false,$subscribed_list_only=false){
        $data=array();
        $this->getFormat=ARRAY_A;
        $array=$this->getOne(false,$conditions);
        if(!$array) return false;

        $data['details'] = $array;

        //get the list  that the user subscribed to
        $model_user_list = WYSIJA::get('user_list','model');
        $conditions = array('user_id'=>$data['details']['user_id']);
        if($subscribed_list_only){
            $conditions['unsub_date']=0;
        }

        $data['lists'] = $model_user_list->get(false,$conditions);

        //get the user stats if requested
        if($stats){
            $model_email_user_stat = WYSIJA::get('email_user_stat','model');
            $model_email_user_stat->setConditions(array('equal'=>array('user_id'=>$data['details']['user_id'])));
            $data['emails'] = $model_email_user_stat->count(false);
        }

        return $data;
    }

    /**
     * return the subscriber object for the currently logged in WordPress user
     * @return object user
     */
    function getCurrentSubscriber(){
        static $result_user;
        if(!empty($result_user)) return $result_user;
        $this->getFormat = OBJECT;

        $wp_user_id = (int)WYSIJA::wp_get_userdata('ID');
        if( !( $wp_user_id > 0 ) ){
            return $result_user;
        }
        $result_user = $this->getOne(false,array('wpuser_id'=>$wp_user_id));

        if(!$result_user){
            $this->getFormat = OBJECT;
            $result_user = $this->getOne(false,array('email'=>WYSIJA::wp_get_userdata('user_email')));
            $this->update(array('wpuser_id'=>$wp_user_id),array('email'=>WYSIJA::wp_get_userdata('user_email')));
        }

        //the subscriber doesn't seem to exist let's insert it in the DB
        if(!$result_user){
            $data = get_userdata($wp_user_id);
            $firstname = $data->first_name;
            $lastname = $data->last_name;
            if(!$data->first_name && !$data->last_name) $firstname = $data->display_name;
            $this->noCheck=true;
            $this->insert(array(
                'wpuser_id'=>$data->ID,
                'email'=>$data->user_email,
                'firstname'=>$firstname,
                'lastname'=>$lastname));

            $this->getFormat = OBJECT;
            $result_user = $this->getOne(false,array('wpuser_id'=>$wp_user_id));
        }

        return $result_user;
    }

    /**
     * function used to generate the links for subscriber management, confirm, unsubscribe, edit subscription
     * @param boolean,object $user_obj
     * @param string $action what axctin will be performed (subscribe, unsubscribe, subscriptions)
     * @param string $text name of the link
     * @param boolean $url_only returns only the url no html wrapper
     * @param string $target how does the link open
     * @return type
     */
    function getConfirmLink($user_obj = false, $action = 'subscribe', $text = false, $url_only = false, $target = '_blank' , $page_id_known = false){
        if(!$text) {
			switch ($action) {
				case 'unsubscribe':
					$text = __('Click here to unsubscribe',WYSIJA);
					break;

				case 'subscribe':
				default:
					$text = __('Click here to subscribe',WYSIJA);
					break;

			}
		}
        $users_preview = false;
        //if($action=='subscriptions')dbg($userObj);
        if(!$user_obj){
            //preview mode
            $user_obj = $this->getCurrentSubscriber();
            $users_preview = true;
        }
        $params = array(
        'wysija-page'=>1,
        'controller'=>'confirm',
        );
        if($user_obj && isset($user_obj->keyuser)){
            //if the user key doesn exists let's generate it
            if(!$user_obj->keyuser){
                $user_obj->keyuser = $this->generateKeyuser($user_obj->email);
                while($this->exists(array('keyuser'=>$user_obj->keyuser))){
                    $user_obj->keyuser = $this->generateKeyuser($user_obj->email);
                }
               $this->update(array('keyuser' => $user_obj->keyuser),array('user_id' => $user_obj->user_id));
            }

            $this->reset();
            $params['wysija-key'] = $user_obj->keyuser;
        }
        $params['action'] = $action;
        $model_config = WYSIJA::get('config','model');
        if($users_preview) $params['demo']=1;

        $default_page_id = $model_config->getValue('confirm_email_link');

        $action_page_param = array(
            'subscribe' => 'confirmation_page',
            'unsubscribe' => 'unsubscribe_page',
            'subscriptions' => 'subscriptions_page',
        );

        $page_id_for_action = $model_config->getValue($action_page_param[$action]);

        if(!$page_id_for_action || $default_page_id == $page_id_for_action){
            // if we use the default page created on install for this action
            $page_id = $default_page_id;
        }else{
            // if the page for this action has been modified in the settings
            $page_id = $page_id_for_action;
        }

        if($page_id_known!==false) $page_id = $page_id_known;

        $full_url = WYSIJA::get_permalink($page_id,$params);

        if($url_only) return $full_url;
        return '<a href="'.$full_url.'" target="'.$target.'">'.$text.'</a>';
    }

    /**
     * get the edit subscription link
     * @param type $user_obj
     * @param type $url_only
     * @param type $target
     * @return type
     */
    function getEditsubLink($user_obj=false,$url_only=false, $target = '_blank'){
        $model_config = WYSIJA::get('config','model');

        return $this->getConfirmLink($user_obj,'subscriptions',$model_config->getValue('manage_subscriptions_linkname'),$url_only,$target);
    }

    /**
     * get the unsubscribe link
     * @param type $user_obj
     * @param type $url_only
     * @return string
     */
    function getUnsubLink($user_obj=false,$url_only=false){
        $model_config = WYSIJA::get('config','model');

        return $this->getConfirmLink($user_obj,'unsubscribe',$model_config->getValue('unsubscribe_linkname'),$url_only);
    }

    /**
     * used to generate a hash to identify each subscriber, this is used later in confirmation links etc...
     * @param string $email
     * @return string md5
     */
    function generateKeyuser($email){
        return md5( AUTH_KEY . $email . time() );
    }

    /**
     * returns the user_id providing either the wpuser_id value or an email
     * @param string $email
     * @return int
     */
    function user_id($email){
        $this->getFormat=ARRAY_A;
        if(is_numeric($email)){
            $result = $this->getOne(array('user_id'),array('wpuser_id'=>$email));
        }else{
            $result = $this->getOne(array('user_id'),array('email'=>$email));
        }
        return (int)$result['user_id'];
    }


    /**
     * prepare the filters for a user select query based on the PHP global parameters
     * @return array
     */
    function detect_filters(){
        $filters = array();
        // get the filters
        if(!empty($_REQUEST['search'])){
            $filters['search'] = $_REQUEST['search'];
        }

        if(!empty($_REQUEST['wysija']['filter']['search'])){
            $filters['search'] = $_REQUEST['wysija']['filter']['search'];
        }

        // Lists filters
        // Catch list filter from URL
        if ( empty($_REQUEST['wysija']['filter']['filter_list']) && !empty($_REQUEST['filter-list']) ) {
            if ($_REQUEST['filter-list'] == 'orphaned') {
                $filters['lists'] = 0; // force to 0. 0 means: no list!
            } else {
                $filters['lists'] = $_REQUEST['filter-list'];
            }
        }

        if(!empty($_REQUEST['wysija']['filter']['filter_list'])){
            if ($_REQUEST['wysija']['filter']['filter_list'] == 'orphaned') {
                $filters['lists'] = 0; // force to 0. 0 means: no list!
            } else {
                //we only get subscribed or unconfirmed users
                $filters['lists'] = $_REQUEST['wysija']['filter']['filter_list'];
            }
        }


        if(!empty($_REQUEST['link_filter'])){
            $filters['status'] = $_REQUEST['link_filter'];
        }

        if(!empty($_REQUEST['wysija']['user']['timestamp'])){
            //$filters['created_at']= $_REQUEST['wysija']['user']['timestamp'];
        }

		if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'actionvar_resendconfirmationemail') {
			$filters['status'] = 'unconfirmed';
		}


        return $filters;
    }


    /**
     * count the confirmed and unconfirmed users for each list by status
     * @param type $list_ids
     * @return type
     */
    function count_users_per_list($list_ids=array()){
        $select = array( 'COUNT(DISTINCT([wysija]user.user_id)) as total_users', '[wysija]user_list.list_id');
        $count_group_by = 'list_id';

        // filters for unsubscribed
        $filters = array();
        $filters['lists'] = $list_ids;
        $filters['status'] = 'unsubscribed';

        $unsubscribed_users = $this->get_subscribers( $select, $filters, $count_group_by );

        $list_count_per_status=array();
        foreach($unsubscribed_users as $unsubscribed){
            if(!isset($list_count_per_status['list_id']['unsubscribers'])){
                $list_count_per_status[$unsubscribed['list_id']]['unsubscribers']=$unsubscribed['total_users'];
            }
        }

        // count confirmed subscribers
        $filters = array();
        $filters['lists'] = $list_ids;
        $filters['status'] = 'subscribed';

        $subscribed_users = $this->get_subscribers( $select, $filters, $count_group_by );

        foreach($subscribed_users as $subscribed){
            if(!isset($list_count_per_status['list_id']['subscribers'])){
                $list_count_per_status[$subscribed['list_id']]['subscribers']=$subscribed['total_users'];
            }
        }

        // count unconfirmed subscribers
        $filters = array();
        $filters['lists'] = $list_ids;
        $filters['status'] = 'unconfirmed';

        $unconfirmed_users = $this->get_subscribers( $select, $filters, $count_group_by);

        foreach($unconfirmed_users as $unconfirmed){
            if(!isset($list_count_per_status['list_id']['unconfirmed'])){
                $list_count_per_status[$unconfirmed['list_id']]['unconfirmed']=$unconfirmed['total_users'];
            }
        }

        // get the total count of subscribers per list
        $filters = array();
        $filters['lists'] = $list_ids;
        $total_belonging = $this->get_subscribers( $select, $filters , $count_group_by );

        // get the count of confirmed user per each and unconfirmed user per list
        foreach($total_belonging as $belonging){
            if(!isset($list_count_per_status['list_id']['belonging'])){
                $list_count_per_status[$belonging['list_id']]['belonging']=$belonging['total_users'];
            }
        }

        return $list_count_per_status;
    }


    function _convert_filters($filters_in){
        $filters_out = array();
        $model_config = WYSIJA::get('config','model');
        $filter_has_list = false;

        // here we found a search condition
        if(!empty($filters_in['search'])){
            $filters_out['like'] = array();
            $filters_in['search'] = trim($filters_in['search']);
            foreach($this->searchable as $field){
                $filters_out['like'][$field] = trim($filters_in['search']);
            }
        }

        // as soon as we detect lists we set the query that way
        if (isset($filters_in['lists']) && $filters_in['lists'] === 0) // orphan
        {
            $filters_out['equal']['list_id'] = 0;
        }elseif(!empty($filters_in['lists'])){
            $filters_out['equal']['list_id'] = $filters_in['lists'];
            $filter_has_list = true;
        }

        // we detect a status condition
        if(!empty($filters_in['status'])){
            switch($filters_in['status']){
                case 'unconfirmed':
                    $filters_out['equal']['status'] = 0;

                    if($filter_has_list){
                        $filters_out['greater_eq']['sub_date'] =0;
                        $filters_out['equal']['unsub_date'] =0;
                    }
                    break;
                case 'unsubscribed':
                    $filters_out['equal']['status'] = -1;

                    if($filter_has_list){
                        $filters_out['equal']['sub_date'] =0;
                        $filters_out['greater_eq']['unsub_date'] =0;
                    }
                    break;
                case 'subscribed':
                    if($model_config->getValue('confirm_dbleoptin'))  $filters_out['equal']['status'] = 1;
                    else $filters_out['greater_eq']=array('status'=>0);

                    if($filter_has_list){
                        $filters_out['greater_eq']['sub_date'] =0;
                        $filters_out['equal']['unsub_date'] =0;
                    }
                    break;

		case 'inactive':
		    $temporary_table = $this->get_inactive_subscribers_table();
		    // A in "A.user_id" is correct but it's not recommended.
		    $filters_out['is']['A.user_id'] = 'IN (SELECT inactive_users.`user_id` FROM `'.$temporary_table.'` inactive_users)';
		    break;

                case 'all':
                    break;
            }
        }

        if(!empty($filters_in['created_at'])){
            $filters_out['less_eq']['created_at'] = $filters_in['created_at'];
        }


        return $filters_out;
    }
    /**
     *
     * @param type $select
     * @param type $filters
     */
    function get_subscribers($select = array(), $filters = array(), $count_group_by = '', $return_query=false){

        $this->noCheck=true;
        $is_count = false;

        $select = str_replace(array('[wysija]user_list', '[wysija]user', 'count('), array('B', 'A', 'COUNT('), $select);

        if(!empty($filters)){
            $filters = $this->_convert_filters($filters);
            $this->setConditions($filters);
        }



        //1 - prepare select
        if(isset($filters['equal']['list_id'])){
            // orphans are selected with this kind of join
            if($filters['equal']['list_id']=== 0) {
                // reset all prefixes. We are selecting from only 1 table - [wysija]user
                $select_string = implode(', ', $select);

                // make sure we select the user_id from the table that has that information not from user_list which will return NULL
                $select_string = str_replace('B.user_id','A.user_id',$select_string);

                // we need to make the difference between the count query useful for pagination etc and the rest
                $is_count = strpos($select_string, 'COUNT(') !== false;

                // this query left joins on null values of user_list, allows us to display the subscribers not belonging to any list
                $query = 'SELECT '.$select_string.'
                        FROM [wysija]user as A
                        LEFT OUTER JOIN [wysija]user_list as B on A.user_id = B.user_id
                        WHERE B.`user_id` is NULL';

                $this->conditions=array(); // reset all conditions
                $filters = array(); // reset all conditions

            }else{
                // standard select when lists ids are in the filters

                $select_string = implode(', ', $select);
                if(strpos($select_string, 'COUNT(') === false){
                    $select_string = str_replace('A.user_id', 'DISTINCT(B.user_id)', $select_string);
                }else{
                    $is_count = true;
                }

                $query = 'SELECT '.$select_string.' FROM `[wysija]user_list` as B';
                $query .= ' JOIN `[wysija]user` as A on A.user_id=B.user_id';
            }
        } else {
            // when there is no filter list
            $select_string = implode(', ', $select);
            if(strpos($select_string, 'COUNT(') === false){
                $select_string = str_replace('B.user_id', 'A.user_id', $select_string);
            }else{
                $is_count = true;
            }

            $query = 'SELECT '.$select_string.' FROM `[wysija]user` as A';
        }

        $query .= $this->makeWhere();

        if(!$is_count){
            if($return_query) return $query;

            if( empty($_REQUEST['orderby']) || !is_string($_REQUEST['orderby']) || preg_match('|[^a-z0-9#_.-]|i',$_REQUEST['orderby']) !== 0 ){
                if(!empty($_REQUEST['wysija']['filter']['filter_list']) && $_REQUEST['wysija']['filter']['filter_list'] == 'orphaned'){
                    $order_by = '';
                }else{
                    $order_by = ' ORDER BY A.user_id DESC';
                }
            }else{

                if(!in_array(strtoupper($_REQUEST['ordert']),array('DESC','ASC'))){
                    $_REQUEST['ordert'] = 'DESC';
                }

                $order_by = ' ORDER BY `'.$_REQUEST['orderby'].'` '.$_REQUEST['ordert'];
            }




            $query = $query.' '.$order_by.$this->setLimit();
	    return $this->getResults($query);
        }else{
            if(!empty($count_group_by)){
                return $this->getResults($query.' GROUP BY '.$count_group_by);
            }else{
                $result = $this->getResults($query);
                return $result[0];
            }
        }
    }

    /**
     * Add last opens of one or more users
     * @todo: Not in use. Will be removed soon
     *
     * @param array $users
     * @return array
     * Array(
     *	int => array(
     *	    'user_id' => int,
     *	    'firstname' => string,
     *	    'lastname' => string,
     *	    ...
     *	    'last_open' => int
     *	)
     * )
     */
    protected function add_last_opens(Array $users) {
	$user_ids = array();
	foreach ($users as $subscriber) {
	    $user_ids[] = $subscriber['user_id'];
	}
	$query = '
	    SELECT
		eus.`user_id`,
		MAX(eus.`opened_at`) AS `last_open`
	    FROM
		[wysija]email_user_stat eus
	    WHERE
		eus.`user_id` IN ('.implode(', ', $user_ids).')
	    GROUP BY
		eus.`user_id`
	    ';
	$users_last_opens = $this->indexing_dataset_by_field('user_id', $this->getResults($query), true, 'last_open');
	foreach ($users as &$user) {
	    $user['last_open'] = !empty($users_last_opens[$user['user_id']]) ? $users_last_opens[$user['user_id']] : null;
	}
	return $users;
    }

    /**
     * Add last opens of one or more users
     *
     * @todo: Not in use. Will be removed soon
     * @param array $users
     * @return array
     * Array(
     *	int => array(
     *	    'user_id' => int,
     *	    'firstname' => string,
     *	    'lastname' => string,
     *	    ...
     *	    'last_click' => int
     *	)
     * )
     */
    protected function add_last_clicks(Array $users) {
	$user_ids = array();
	foreach ($users as $subscriber) {
	    $user_ids[] = $subscriber['user_id'];
	}
	$query = '
	    SELECT
		euu.`user_id`,
		MAX(euu.`clicked_at`) AS `last_click`
	    FROM
		[wysija]email_user_url euu
	    WHERE
		euu.`user_id` IN ('.implode(', ', $user_ids).')
	    GROUP BY
		euu.`user_id`
	    ';
	$users_last_clicks = $this->indexing_dataset_by_field('user_id', $this->getResults($query), true, 'last_click');
	foreach ($users as &$user) {
	    $user['last_click'] = !empty($users_last_clicks[$user['user_id']]) ? $users_last_clicks[$user['user_id']] : null;
	}
	return $users;
    }


    protected static $_inactive_subscribers_table = null;
    /**
     * Get name of a temporary table of inactive users. It's helpful for all actions related to MASSIVE editing subscribers
     * @return type
     */
    protected function get_inactive_subscribers_table() {
	if (empty(self::$_inactive_subscribers_table))
	    self::$_inactive_subscribers_table = '[wysija]is'.time();
	return self::$_inactive_subscribers_table;
    }

    /**
     * Preprare a temporary table of inactive users. It's helpful for all actions related to MASSIVE editing subscribers
     */
    public function prepare_inactive_users_table() {
	// Use temporary table for better joins
	$temporary_table = $this->get_inactive_subscribers_table();
	$temporary_table_2 = $temporary_table.'_2';
	$queries = array();
	// Main table which stores ID of inactive subscribers
	$queries[] = '
	    CREATE TEMPORARY TABLE `'.$temporary_table.'` (
		`user_id` INT(10) NOT NULL,
		`created_at` INT(10) NULL,
		PRIMARY KEY(`user_id`)
	    )
	    ';
	$queries[] = '
	    CREATE TEMPORARY TABLE `'.$temporary_table_2.'` (
		`user_id` INT(10) NOT NULL,
		PRIMARY KEY(`user_id`)
	    )
	    ';
	$queries[] = '
	    INSERT INTO `'.$temporary_table_2.'`
		SELECT
		    DISTINCT `user_id`
		FROM
		    `[wysija]email_user_stat`
		GROUP BY
		    `user_id`
		HAVING SUM(IF (`status` < 0, 0, `status`)) <= 0
	    ';
	$queries[] = '
	    INSERT INTO `'.$temporary_table.'`
		SELECT
		    DISTINCT u.`user_id`, u.`created_at`
		FROM
		    `'.$temporary_table_2.'` t2
		LEFT JOIN
		    `[wysija]user` u ON t2.`user_id` = u.`user_id`
		WHERE
		    u.`status` > 0
		GROUP BY
		    u.`user_id`
	    ';
	foreach ($queries as $query) {
	    $this->getResults($query);
	}
    }
    /**
     * Count inactive users.
     * Inactive users are people who:
     * - AND were confirmed (status > 0)
     * - AND were received at least 1 newsletter
     * - AND have never opened or clicked
     *
     * @return array
     * Array (
     * 'count' => int,
     * 'max_created_at' => int
     * )
     */
    public function count_inactive_users() {
	$temporary_table = $this->get_inactive_subscribers_table();
	$main_query = 'SELECT COUNT(*) AS `count`, MAX(`created_at`) AS `max_created_at` FROM `'.$temporary_table.'` ';
	$result = $this->getResults($main_query);
	if ($result)
	    return $result[0];
    }
    public function structure_user_status_count_array($count_by_status) {
		$counts = array(
			'unsubscribed' => 0,
			'unconfirmed' => 0,
			'subscribed' => 0,
			'inactive' => 0
		);
        $model_config = WYSIJA::get('config','model');
        $is_dbleoptin	  = (boolean)$model_config->getValue('confirm_dbleoptin');

		foreach ($count_by_status as $status_data) {
			switch ($status_data['status']) {
				case '-1':
					$counts['unsubscribed'] += $status_data['users'];
					break;

				case '0':
					if ($is_dbleoptin) {
						$counts['unconfirmed']  += $status_data['users'];
					} else {
						$counts['subscribed']  += $status_data['users'];
					}
					break;

				case '1':
					$counts['subscribed']  += $status_data['users'];
					break;

				case '-99':
					$counts['inactive']	 += $status_data['users'];
					break;

				default:
					break;
			}
		}

		$counts['all'] = array_sum(array_values($counts)) - $counts['inactive'];
		return $counts;
	}

    public function get_max_create($count_by_status){
        $arr_max_create_at = array();
        foreach($count_by_status as $status_data){
            $arr_max_create_at[] = $status_data['max_create_at'];
        }
        return $arr_max_create_at;
    }


    /**
     * triggered before a user is deleted using the delete function of the user model
     * @return boolean
     */
    function beforeDelete($conditions){
        $model_user = new WYSIJA_model_user();
        $users = $model_user->get(array('user_id'),$this->conditions);
        $user_ids = array();
        foreach($users as $user) $user_ids[]=$user['user_id'];

        //delete all the user stats
        $model_email_user_stat=WYSIJA::get('email_user_stat','model');
        $conditions=array('user_id'=>$user_ids);
        $model_email_user_stat->delete($conditions);
        //delete all the queued emails
        $model_queue=WYSIJA::get('queue','model');
        $model_queue->delete($conditions);
        return true;
    }

    /**
     * triggered after a user is deleted using the delete function of the user model
     * @return boolean
     */
    function afterDelete(){
        $helper_user=WYSIJA::get('user','helper');
        $helper_user->refreshUsers();
        return true;
    }

    /**
     * triggered after a user is inserted using the insert function of the user model
     * @return boolean
     */
    function afterInsert($id){
        $helper_user=WYSIJA::get('user','helper');
        $helper_user->refreshUsers();

        $this->_update_domain_field();

        do_action('wysija_subscriber_added', $id);
        return true;
    }



    function beforeUpdate($id=null){

        do_action('wysija_subscriber_modified', $id);
        return true;
    }

    function _update_domain_field(){
        // automatically add value to the field "domain". This is useful for statistics
        // but only possible if we update the email
        if(isset($this->values['email'])){
            $this->values['domain'] = substr(
                $this->values['email'] ,
                strpos($this->values['email'],
                        '@')+1);
        }
    }

    function afterUpdate($id){
        $helper_user=WYSIJA::get('user','helper');
        $helper_user->refreshUsers();

        $this->_update_domain_field();

        do_action('wysija_subscriber_modified', $id);
        return true;
    }


      /**
     * function used to generate the links if they are not valid anymore,
     * will be needed for old version of the plugin still using old unsafe links
     * @param int $user_id
     * @param int $email_id
     * @return string
     */
    function old_get_new_link_for_expired_links($user_id,$email_id){
        $params=array(
            'wysija-page'=>1,
            'controller'=>'confirm',
            'action'=>'resend',
            'user_id'=>$user_id,
            'email_id'=>$email_id
        );

        $model_config = WYSIJA::get('config','model');
        return WYSIJA::get_permalink($model_config->getValue('confirm_email_link'),$params);
    }
}
