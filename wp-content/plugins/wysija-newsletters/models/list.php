<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_list extends WYSIJA_model{

    var $pk='list_id';
    var $table_name='list';
    var $columns=array(
        'list_id'=>array('auto'=>true),
        'name' => array('req'=>true,'type'=>'text'),
        'namekey' => array('req'=>true,'type'=>'text'),
        'description' => array('type'=>'text'),
        'unsub_mail_id' => array('req'=>true,'type'=>'integer'),
        'welcome_mail_id' => array('req'=>true,'type'=>'integer'),
        'is_enabled' => array('req'=>true,'type'=>'boolean'),
        'is_public' => array('req'=>true,'type'=>'boolean'),
        'ordering' => array('req'=>true,'type'=>'integer'),
        'created_at' => array('req'=>true,'type'=>'integer'),
    );
    var $escapeFields=array('name','description');
    var $escapingOn=true;

    function __construct(){
        $this->columns['name']['label']=__('Name',WYSIJA);
        $this->columns['description']['label']=__('Description',WYSIJA);
        $this->columns['is_enabled']['label']=__('Enabled',WYSIJA);
        $this->columns['ordering']['label']=__('Ordering',WYSIJA);
        parent::__construct();
    }

    function beforeInsert() {
        if(!isset($this->values['namekey']) || !$this->values['namekey']){
            if(isset($this->values['name']))    $this->values['namekey']=sanitize_title($this->values['name']);
        }
        return true;
    }

    function get_one_list($list_id){
        $query='SELECT A.name, A.list_id, A.description, A.is_enabled, A.is_public, A.namekey
                FROM '.$this->getPrefix().'list as A
                LEFT JOIN '.$this->getPrefix().'email as B on A.welcome_mail_id=B.email_id
                WHERE A.list_id='.(int)$list_id;
        $result = $this->getResults($query);
        $this->escapeQuotesFromRes($result);
        return $result[0];
    }

    /**
     * get the detail about list(s) total of subscribers, unsubscribers, unconfirmed etc
     * @param int $list_id
     * @return type
     */
    function getLists(){
        $model_user = WYSIJA::get('user','model');

        $query='SELECT A.`name`, A.`list_id`, A.`created_at`, A.`is_enabled`, A.`is_public`, A.`namekey`, 0 as subscribers, 0 as campaigns_sent
        FROM '.$this->getPrefix().'list as A ORDER BY A.`name`';

        $this->countRows=$this->count($query);

        if(isset($this->_limitison) && $this->_limitison)  $query.=$this->setLimit();
        $result_lists=$this->getResults($query);

        $lists_details=array();

        foreach($result_lists as $result){
            $lists_details[$result['list_id']]=$result;
        }

        $list_ids = array_keys($lists_details);


        $model_config = WYSIJA::get('config' , 'model');
        if($model_config->getValue('speed_no_count')){
            // we disable the count in order to speed up the process
            $counts_per_list = array();
        }else{
            // these count requests can take a lot of time on big databases, we need to change the db diagram slightly to improve that
            $counts_per_list = $model_user->count_users_per_list($list_ids);
        }


        // get the count of confirmed user per each and unconfirmed user per list
        foreach($lists_details as $key_list_id => &$result){
            if(isset($counts_per_list[$key_list_id])){
                foreach($counts_per_list[$key_list_id] as $property => $value){
                    $result[$property] = $value;
                }
            }
        }


        // we need to fill in the count value that will be used by the dropdown
        // the value will depend on the status filter
        foreach($lists_details as $key_list_id => &$result){

            if(!isset($result['unsubscribers'])) $result['unsubscribers'] = 0;
            if(!isset($result['subscribers'])) $result['subscribers'] = 0;
            if(!isset($result['unconfirmed'])) $result['unconfirmed'] = 0;
            if(!isset($result['belonging'])) $result['belonging'] = 0;

            if(!empty($_REQUEST['link_filter'])){
                switch($_REQUEST['link_filter']){
                    case 'unsubscribed' :
                        $result['users'] = $result['unsubscribers'];
                        break;
                    case 'subscribed' :
                        $result['users'] = $result['subscribers'];
                        break;
                    case 'unconfirmed' :
                        $result['users'] = $result['unconfirmed'];
                        break;
                    default :

                        $result['users'] = $result['belonging'];
                }
            }else{
                $result['users'] = $result['belonging'];
            }

        }


        $this->escapeQuotesFromRes($lists_details);
        return $lists_details;

    }
}
