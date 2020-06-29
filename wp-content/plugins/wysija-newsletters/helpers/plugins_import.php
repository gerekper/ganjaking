<?php

defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_plugins_import extends WYSIJA_object{

    function __construct(){
        parent::__construct();
    }

    //all the basic information concerning each plugin to make the match works
    function getPluginsInfo($table=false){
        $pluginsTest=array(
            'newsletter'=>array(
                "name"=>__("Newsletter Pro (by Satollo)",WYSIJA),
                "pk"=>"id",
                "matches"=>array("name"=>"firstname","email"=>"email","surname"=>"lastname","ip"=>"ip"),
                "where"=>array("status"=>"C")
            ),
            'easymail_subscribers'=>array(
                "name"=>"ALO EasyMail",
                "pk"=>"id",
                "matches"=>array("name"=>"firstname","email"=>"email"),
                "where"=>array("active"=>1),
                "whereunconfirmed"=>array("active"=>0)
            ),
            'meenewsusers'=>array(
                "name"=>"Meenews",
                "pk"=>"id",
                "matches"=>array("name"=>"firstname","email"=>"email"),
                "where"=>array("state"=>2),
                "whereunconfirmed"=>array("state"=>1),
                'list'=>array(
                    'list'=> array(
                        'table'=>'meenewscategories',
                        'matches'=>array(
                            'categoria'=>'name'
                            ),
                        'pk'=>'id',
                        ),
                    'user_list'=>array(
                        'table'=>'meenewsusers',
                        'matches'=>array(
                            'id_categoria'=>'list_id',
                            'subscriber_id'=>'id'
                            )
                        )
                    )
            ),

            'mailpress_users'=>array(
                "name"=>"MailPress",
                "pk"=>"id",
                "matches"=>array("name"=>"firstname","email"=>"email","laststatus_IP"=>"ip"),
                "where"=>array("status"=>"active"),
                "whereunconfirmed"=>array("status"=>"waiting")
            ),

            'subscribe2'=>array(
                "name"=>"Subscribe2",
                "pk"=>"id",
                "matches"=>array("email"=>"email","ip"=>"ip"),
                "where"=>array("active"=>1),
                "whereunconfirmed"=>array("active"=>0)
            ),

            'eemail_newsletter_sub'=>array(
                "name"=>"Email newsletter",
                "pk"=>"eemail_id_sub",
                "matches"=>array("eemail_email_sub"=>"email","eemail_name_sub"=>"firstname"),
                "where"=>array("eemail_status_sub"=>"YES")
            ),
            'gsom_subscribers'=>array(
                "name"=>"G-Lock Double Opt-in Manager",
                "pk"=>"intId",
                "matches"=>array("varEmail"=>"email","gsom_fname_field"=>"firstname","Last_Name1"=>"lastname","varIP"=>"ip"),
                "where"=>array("intStatus"=>1),
                "whereunconfirmed"=>array("intStatus"=>0)
            ),

            'wpr_subscribers'=>array(
                "name"=>"WP Autoresponder",
                "pk"=>"id",
                "matches"=>array("name"=>"firstname","email"=>"email"),
                "where"=>array("active"=>1,"confirmed"=>1),
                "whereunconfirmed"=>array("active"=>1,"confirmed"=>0),
                'list'=>array(
                    'list'=> array(
                        'table'=>'wpr_newsletters',
                        'matches'=>array(
                            'name'=>'name'
                            ),
                        'pk'=>'id',
                        ),
                    'user_list'=>array(
                        'table'=>'wpr_subscribers',
                        'matches'=>array(
                            'nid'=>'list_id',
                            'id'=>'user_id'
                            )
                        )
                    )
            ),

            'wpmlsubscribers'=>array(
                "name"=>__("Newsletters (by Tribulant)"),
                "pk"=>"id",
                "matches"=>array("email"=>"email","ip_address"=>"ip"),
                "where"=>array("wpmlsubscriberslists.active"=>"Y"),
                'list'=>array(
                    'list'=> array(
                        'table'=>'wpmlmailinglists',
                        'matches'=>array(
                            'title'=>'name'
                            ),
                        'pk'=>'id',
                        ),
                    'user_list'=>array(
                        'table'=>'wpmlsubscriberslists',
                        'matches'=>array(
                            'list_id'=>'list_id',
                            'subscriber_id'=>'user_id'
                            )
                        )
                    )
            ),
            'nl_email'=>array(
                "name"=>"Sendit",
                "pk"=>"id_email",
                "matches"=>array("email"=>"email","contactname"=>"firstname"),
                "where"=>array("accepted"=>"y"),
                "whereunconfirmed"=>array("accepted"=>"n"),
                'list'=>array(
                    'list'=> array(
                        'table'=>'nl_liste',
                        'matches'=>array(
                            'nomelista'=>'name'
                            ),
                        'pk'=>'id_lista',
                        ),
                    'user_list'=>array(
                        'table'=>'nl_email',
                        'matches'=>array(
                            'id_lista'=>'list_id',
                            'id_email'=>'user_id'
                            )
                        )
                    )
            )
        );

        if($table){
            if(!isset($pluginsTest[$table])) return false;
            return $pluginsTest[$table];
        }
        return $pluginsTest;
    }

    function reverseMatches($matches){
        $matchesrev=array();
        foreach($matches as $key => $val){
            $matchesrev[$val]=$key;
        }
        return $matchesrev;
    }

    /**
     * this function is run only once at install to test which plugins are possible to import
     * @global type $wpdb
     */
    function testPlugins(){
        $modelWysija=new WYSIJA_model();
        $possibleImport=array();
        foreach($this->getPluginsInfo() as $tableName =>$pluginInfos){
            /*if the plugin's subscribers table exists*/
            $result=$modelWysija->query("SHOW TABLES like '".$modelWysija->wpprefix.$tableName."';");
            if($result){
                /*select total of users*/
                $where=$this->generateWhere($pluginInfos['where']);
                $result=$modelWysija->query("get_row", "SELECT COUNT(`".$pluginInfos['pk']."`) as total FROM `".$modelWysija->wpprefix.$tableName."` ".$where." ;", ARRAY_A);
                $pluginInfosSave=array();
                if((int)$result['total']>0){
                    /* there is a possible import to do */
                    $pluginInfosSave['total']=(int)$result['total'];

                    /*if the plugin's lists table exists*/
                    if(isset($pluginInfos['list'])){
                        $resultlist=$modelWysija->query("SHOW TABLES like '".$modelWysija->wpprefix.$pluginInfos['list']['list']['table']."';");
                        if($resultlist){
                            /*select total of users*/
                            /*$where=$this->generateWhere($pluginInfos);*/
                            $where="";
                            $queryLists="SELECT COUNT(`".$pluginInfos['list']['list']['pk']."`) as total FROM `".$modelWysija->wpprefix.$pluginInfos['list']['list']['table']."` ".$where." ;";
                            $resultlist=$modelWysija->query("get_row", $queryLists, ARRAY_A);

                            if((int)$resultlist['total']>0){
                                /* there is a possible import to do */
                                $pluginInfosSave['total_lists']=(int)$resultlist['total'];

                            }

                        }
                    }

                    if(!isset($pluginInfosSave['total_lists']))$pluginInfosSave['total_lists']=1;
                    $possibleImport[$tableName]=$pluginInfosSave;

                }
            }
        }

        /* if we found some plugins to import from we just save their details in the config */
        if($possibleImport){
            $modelConfig=WYSIJA::get("config","model");
            $modelConfig->save(array("pluginsImportableEgg"=>$possibleImport));
        }

    }


    function import($table_name,$connection_info,$is_synch_wp=false,$is_main_site=true,$is_synch=false){
        // careful WordPress global
        global $wpdb;
        // insert the list corresponding to that import
        $model=WYSIJA::get('list','model');

        // only we insert the default list the first time we pass here for real import
        if(!$is_synch){
            if($is_synch_wp)   $list_name=__('WordPress Users',WYSIJA);
            else $list_name=sprintf(__('%1$s\'s import list',WYSIJA),$connection_info['name']);
            $descriptionList=sprintf(__('The list created automatically on import of the plugin\'s subscribers : "%1$s',WYSIJA),$connection_info['name']);
            // the list for all the users
            $defaultListId=$model->insert(array(
                'name'=>$list_name,
                'description'=>$descriptionList,
                'is_enabled'=>0,
                'namekey'=>$table_name));
        }else $defaultListId=$is_synch['wysija_list_main_id'];


        $mktime=time();
        $mktimeConfirmed=$mktime+1;

        if(strpos($table_name, 'query-')!==false){
            $lowertbname=str_replace('-', '_', $table_name);

            //TODO maybe we don't need to create a list that we sync but simply need a way to make a filter
            $matches=apply_filters('wysija_fields_'.$lowertbname);
            $query_select=apply_filters('wysija_select_'.$lowertbname);

            $query_select=str_replace('[created_at]', $mktime, $query_select);
            $fields='(`'.implode('`,`',$matches).'`,`created_at` )';
            $query="INSERT IGNORE INTO `[wysija]user` $fields $query_select";
        }else{
            // automated part using the import array with the DB structure
            // prepare the table transfer query
            $colsPlugin=array_keys($connection_info['matches']);

            $extracols=$extravals='';

            if(isset($connection_info['matchesvar'])){
                $extracols=',`'.implode('`,`',array_keys($connection_info['matchesvar'])).'`';
                $extravals=','.implode(',',$connection_info['matchesvar']);
            }

            // import/synch the unconfirmed users from an extension
            if(isset($connection_info['whereunconfirmed'])){
                $fields='(`'.implode('`,`',$connection_info['matches']).'`,`created_at` '.$extracols.' )';
                $values='`'.implode('`,`',$colsPlugin).'`,'.$mktime.$extravals;

                // query to save the external plugins unconfirmed subscribers into wysija subsribers
                $where=$this->generateWhere($connection_info['whereunconfirmed']);

                $query="INSERT IGNORE INTO `[wysija]user` $fields SELECT $values FROM ".$model->wpprefix.$table_name.$where;
                $model->query($query);
            }


            $fields='(`'.implode('`,`',$connection_info['matches']).'`,`created_at` '.$extracols.' )';
            $values='`'.implode('`,`',$colsPlugin).'`,'.$mktimeConfirmed.$extravals;

            // import/synch the confirmed users from wp or an extension
            if($table_name == 'users') {
                // query to save the wordpress users into wysija subsribers

                $query_select = "SELECT u1.ID, u1.user_email, m1.meta_value as first_name, m2.meta_value as last_name, ".$mktimeConfirmed.$extravals."
                        FROM ".$wpdb->base_prefix.$table_name." AS u1
                        JOIN ".$wpdb->base_prefix."usermeta AS m1 ON ( m1.user_id = u1.ID
                        AND m1.meta_key = 'first_name' )
                        JOIN ".$wpdb->base_prefix."usermeta AS m2 ON ( m2.user_id = u1.ID
                        AND m2.meta_key = 'last_name' ) ";

                // in case of a multisite by default we want to import the wp users from the site on which we are running that script
                if(!$is_main_site){
                    $query_select.=' JOIN '.$wpdb->base_prefix.'usermeta as m3 ON ( u1.ID = m3.user_id AND m3.meta_key = \''.$model->wpprefix.'capabilities\' )';
                }



                $query="INSERT IGNORE INTO `[wysija]user` $fields ".$query_select;

            }else    {
                /* query to save the external plugins subscribers into wysija subsribers*/
                $where=$this->generateWhere($connection_info['where']);

                $query="INSERT IGNORE INTO `[wysija]user` $fields SELECT $values FROM ".$model->wpprefix.$table_name.$where;

            }
        }


        $model->query($query);

        //update status to subscribed for the confirmed users
        $modelU=WYSIJA::get('user','model');
        $modelU->update(array('status'=>1),array('created_at'=>$mktimeConfirmed));


        /* query to save the fresshly inserted subscribers into wysija new imported list*/
        $this->insertUserList($defaultListId,$mktime,$mktimeConfirmed);

        $query="SELECT COUNT(user_id) as total FROM ".$model->getPrefix()."user WHERE created_at IN ('".$mktime."','".$mktimeConfirmed."')";
        $result=$wpdb->get_row($query, ARRAY_A);

        /* insert all the lists from plugin if there are any table defined */
        if(isset($connection_info['list'])){
            $listmatchesrev=$this->reverseMatches($connection_info['list']['list']['matches']);
            /*selec the lists*/
            $selectListsKeep="SELECT `".$listmatchesrev['name']."`,`".$connection_info['list']['list']['pk']."` FROM ".$model->wpprefix.$connection_info['list']['list']['table'];
            $resultslists=$model->query("get_res",$selectListsKeep);

            if($resultslists){
                $userlistmatchesrev=$this->reverseMatches($connection_info['list']['user_list']['matches']);
                foreach($resultslists as $listresult){
                    /*insert the lists*/
                    if(!$is_synch){
                       $list_name=sprintf(__('"%2$s" imported from %1$s',WYSIJA),$connection_info["name"],$listresult[$listmatchesrev['name']]);
                        $descriptionList=sprintf(__('The list existed in "%1$s" and has been imported automatically.',WYSIJA),$connection_info["name"]);
                        $listidimported=$model->insert(array(
                        "name"=>$list_name,
                        "description"=>$descriptionList,
                        "is_enabled"=>0,
                        "namekey"=>$table_name."-listimported-".$listresult[$connection_info['list']['list']['pk']]));
                    }else {
                        $model->reset();
                        $datalist=$model->getOne(false,array("namekey"=>$table_name."-listimported-".$listresult[$connection_info['list']['list']['pk']]));
                        $listidimported=$datalist['list_id'];
                    }

                    /* insert the user lists */
                    $query_select_join=' INNER JOIN '.$model->wpprefix.$table_name.' ON ( [wysija]user.email = '.$model->wpprefix.$table_name.'.'.$connection_info['matches']['email'].' )';
                    if($connection_info['list']['user_list']['table']!=$table_name) $query_select_join.=' INNER JOIN '.$model->wpprefix.$connection_info['list']['user_list']['table'].' ON ( '.$model->wpprefix.$connection_info['list']['user_list']['table'].'.'.$userlistmatchesrev['user_id'].' = '.$model->wpprefix.$table_name.'.'.$connection_info['pk'].' )';
                    $query_select_join.=" WHERE ".$model->wpprefix.$connection_info['list']['user_list']['table'].".".$userlistmatchesrev['list_id']."='".$listresult[$connection_info['list']['list']['pk']]."' ";

                    $selectuserCreated="SELECT `[wysija]user`.`user_id`, ".$listidimported.", ".time()." FROM [wysija]user ".$query_select_join;
                    $query="INSERT IGNORE INTO `[wysija]user_list` (`user_id`,`list_id`,`sub_date`) ".$selectuserCreated;
                    $model->query($query);
                }
            }
        }
        
        // since 2.6, we add a new column (`domain`), based on user email's address
        $this->_generate_user_domain();
        
        $helperU=WYSIJA::get('user','helper');
        $helperU->refreshUsers();
        if(!$is_synch){
            //$this->wp_notice(sprintf(_ds_fs('%1$s users from %2$s have been imported into the new list %3$s',WYSIJA),"<strong>".$result['total']."</strong>","<strong>".$plugInfo['name']."</strong>","<strong>".$listname."</strong>"));
        }else{
            //$this->notice(__("List has been synched successfully.",WYSIJA));
        }

        return $defaultListId;
    }
    
    /**
     * since 2.6
     * Generate domain of users based on their email address
     */
    protected function _generate_user_domain() {
        $query = "UPDATE [wysija]user SET `domain` = SUBSTRING(`email`,LOCATE('@',`email`)+1);";
        $user_model = WYSIJA::get('user', 'model');
        $user_model->query($query);
    }

    function insertUserList($defaultListId,$mktime,$mktimeConfirmed,$querySelect=false){
        $model=WYSIJA::get('list','model');
        if(!$querySelect)   $querySelect='SELECT `user_id`, '.$defaultListId.', '.time()." FROM [wysija]user WHERE created_at IN ('".$mktime."','".$mktimeConfirmed."')";
        $query='INSERT IGNORE INTO `[wysija]user_list` (`user_id`,`list_id`,`sub_date`) '.$querySelect;
        return $model->query($query);
    }

    function generateWhere($plugInfoWhere){
        $where=' as B WHERE';
        $i=0;
        foreach($plugInfoWhere as $keyy => $vale){
            if($i>0)$where.=' AND ';
            if($keyy=='wpmlsubscriberslists.active'){
                $model=WYSIJA::get('list', 'model');
                $innerjoin=' INNER JOIN '.$model->wpprefix.'wpmlsubscriberslists as B ON ( '.$model->wpprefix.'wpmlsubscribers.id = B.subscriber_id )';
                $where=$innerjoin." WHERE B.active='".$vale."' ";
            }else{
                $where.=' B.'.$keyy."='".$vale."' ";
            }
            $i++;
        }
        return $where;
    }

    function importWP() {

        // this parameter is needed on import to make sure that we don't import all of the network users in one random
        $is_main_site = true;
        if (is_multisite()) {
            // Carefull WordPress global
            global $wpdb;
            if ($wpdb->prefix != $wpdb->base_prefix) {
                $is_main_site = false;
            }
        }

        $connection_info = array(
            'name' => 'WordPress',
            'pk' => 'ID',
            'matches' => array(
                'ID' => 'wpuser_id', 
                'user_email' => 'email', 
                'first_name' => 'firstname', 
                'last_name' => 'lastname'),
            'matchesvar' => array(
                'status' => 1)
            );

        $table_name = 'users';

        return $this->import($table_name, $connection_info, true, $is_main_site);
    }


}

