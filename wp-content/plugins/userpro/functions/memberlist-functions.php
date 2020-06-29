<?php

	/* Search user by more criteria */
	function userpro_query_search_displayname( $query ) {
		global $wpdb;
		$search_string = esc_attr( trim( $_GET['searchuser'] ) );
		$query->query_where .= $wpdb->prepare( " OR $wpdb->users.display_name LIKE %s", '%' . like_escape( $search_string ) . '%' );
	}

	/* prepare loop of users list */
	function userpro_memberlist_loop($args){
		global $userpro;
		$userid=get_current_user_id();
		$mycountry=userpro_profile_data('country',$userid);
		
		foreach ($args as &$val)
		{
			
			if($val==='mycountry')
			{   
				$val=$mycountry;
				
			}
			
		
		}
		if(isset($_GET['tags']))		
		$args['tags'] = $_GET['tags'];
		$per_page = (isset($args['per_page'])) ? $args['per_page'] : 0;
		$relation = (isset($args['relation'])) ? $args['relation'] : 'AND';
		$role = (isset($args['role'])) ? $args['role'] : '';
		$memberlist_verified = (isset($args['memberlist_verified'])) ? $args['memberlist_verified'] : 0;
		$sortby = (isset($args['sortby'])) ? $args['sortby'] : '';
		$order = (isset($args['order'])) ? $args['order'] : '';
		$arr=array();
		if ( get_option('userpro_trial') == 1) {
			$per_page = 3;
			do_action('userpro_pre_form_message');
		}
	
		global $wpdb;
		$blog_id = get_current_blog_id();

		$page = (!empty($_GET['userp'])) ? $_GET['userp'] : 1;
		$offset = ( ($page -1) * $per_page);

		/** QUERY ARGS BEGIN **/
		
		/* exclude specific users? */
		if (isset($args['exclude'])){
			$exclude = explode(',',$args['exclude']);
			$query['exclude'] = $exclude;
		}
		
		/* Start Added by Ranjith to hide/show unapproved users */
		
		if (isset($args['userpro_show_unapproved_members']) && $args['userpro_show_unapproved_members']==='0'){
			$user_ids = get_users(array(
					'meta_key'=>'_account_status',
					'meta_value'=>array('pending_admin','pending'),
					'fields'=>"ID"
			));
			if(isset($args['exclude'])){
				$exclude = explode(',',$args['exclude']);
				if(is_array($user_ids) && is_array($exclude)){
					$query['exclude'] = array_merge($user_ids,$exclude);
				}
				else if(is_array($user_ids))
				{
					$query['exclude'] = $user_ids;
				}
				else if(is_array($exclude)){
					$query['exclude'] = $exclude;
				}
				
			}
		}
		
		/* End */
		
		$query['meta_query'] = array('relation' => strtoupper($relation) );
		
	if (isset($role) && $role!=''){
			$roles = explode(',',$role);
			$query['meta_query']['relation'] = 'AND';
			if (count($roles) >= 2){
				$role_query['relation']= 'or';
			}
			foreach($roles as $subrole){
			$role_query[] = array(
				'key' => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
				'value' => $subrole,
				'compare' => 'like'
			);
			}
			$query['meta_query'][] = $role_query;
		}
		
		
		/* limited to userpro fields */
		if (userpro_retrieve_metakeys()){
			foreach(userpro_retrieve_metakeys() as $key){
			
				if ($userpro->field_type($key) == 'multiselect' ||
					$userpro->field_type($key) == 'checkbox' ||
					$userpro->field_type($key) == 'checkbox-full'
					) {
					$like = 'like';
				} else {
					$like = '=';
				}
			
				if ( !empty($args[$key]) && isset($args[$key]) && $key != 'role' ){
				
					if (substr( trim( htmlspecialchars_decode($args[$key])  ) , 0, 1) === '>') {
						$choices = explode('>', trim(  htmlspecialchars_decode($args[$key]) ));
						$target = $choices[1];
						$query['meta_query'][] = array(
							'key' => $key,
							'value' => $target,
							'compare' => '>'
						);
					} elseif (substr( trim(  htmlspecialchars_decode($args[$key]) ) , 0, 1) === '<') {
						$choices = explode('<', trim(  htmlspecialchars_decode($args[$key]) ));
						$target = $choices[1];
						$query['meta_query'][] = array(
							'key' => $key,
							'value' => $target,
							'compare' => '<'
						);
					} elseif (strstr( esc_attr( trim(  $args[$key] ) ) , ':')){
						$choices = explode(':', esc_attr( trim(  $args[$key] ) ));
						$min = $choices[0];
						$max = $choices[1];
						$query['meta_query'][] = array(
							'key' => $key,
							'value' => array($min, $max),
							'compare' => 'between'
						);
					} elseif (strstr( esc_attr( trim( $args[$key] ) ) , ',')){
						$choices = explode(',', esc_attr( trim(  $args[$key] ) ));
						foreach($choices as $choice){
							$query['meta_query'][] = array(
								'key' => $key,
								'value' => $choice,
								'compare' => $like
							);
						}
					} else {
							$query['meta_query'][] = array(
								'key' => $key,
								'value' => esc_attr( trim( $args[$key] ) ),
								'compare' => $like
							);
					}
					
				}
				
			}
		}
		
		if ($memberlist_verified) {
			$query['meta_query'][] = array(
				'key' => 'userpro_verified',
				'value' => 1,
				'compare' => '='
			);
		}
		
		
		
		if (isset($args['memberlist_verified']) && $args['memberlist_verified'] === '0') {
			$query['meta_query'][] = array(
				'key' => 'userpro_verified',
				'compare' => 'NOT EXISTS'
			);
		}
		
		$memberlist_withavatar = $args['memberlist_withavatar'];
		if (isset($memberlist_withavatar) && $memberlist_withavatar == 1){
			$query['meta_query'][] = array(
				'key' => 'profilepicture',
				'value' => '',
				'compare' => '!='
			);
		}
		
		/* meta query for profile privacy*/
		if(!current_user_can('manage_options')){
			$query['meta_query'][]= array(
					'key' => 'profile_privacy',
					'compare' => 'NOT EXISTS'
				);
		}
		
		/**
			CUSTOM SEARCH FILTERS UPDATE
		**
		**
		**/
		
		if (isset($_GET['searchuser'])) {
			global $userpro_emd;
			
			$role = (isset($args['role'])) ? $args['role'] : '';
			if($role != '') {
				$role = str_replace(',', '|', $role);
			}
			$role_query['relation']= 'and';
			$role_query[] = array(
					'key' => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
					'value' => $role,
					'compare' => 'REGEXP'
			);
			/* Searchuser query param */
			if( strpos($_GET['searchuser'], "'") !== false){
				$search_string = stripcslashes( $_GET['searchuser'] );
			}
			else{
				$search_string = esc_attr( trim( $_GET['searchuser'] ) );
			}
			if ($search_string != '') {
			
				if (isset($args['memberlist_filters']) && !empty($args['memberlist_filters']) ){
					$customfilters = explode(',',$args['memberlist_filters']);
					if ($customfilters){
						if (count($customfilters) > 1) {
							$customfiltersquery['relation'] = 'or';
						}
						foreach($customfilters as $customfilter){
							$customfiltersquery[] = array(
								'key' => $customfilter,
								'value' => $search_string,
								'compare' => 'like'
							);
						}
						$query['meta_query'][] = $customfiltersquery;
						$testkeys = new WP_User_Query($query);
					}elseif($role != ''){
						if (count($customfilters) > 1) {
							$customfilter_query['relation'] = 'or';
						}
						foreach($customfilters as $customfilter){
							$customfilter_query[] = array(
									'key' => $customfilter,
									'value' => $search_string,
									'compare' => 'like'
							);
						}
// 						$query['meta_query'][] = array(
// 								'relation'	=> 'and' ,
// 								array(
// 										'key' => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
// 										'value' =>$role,
// 										'compare' => 'REGEXP'
// 								) ,
// 								$customfilter_query
// 						);
						
					}
				}
				
				if ( empty( $testkeys->results ) && $role != ''){
					$query['meta_query'][] = array(
						'relation' => 'and',
						array(
						'key' => 'display_name',
						'value' => $search_string,
						'compare' => 'like'
					),
						array(
						'key' => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
						'value' =>$role,
						'compare' => 'REGEXP'
					)
				);
				}else if(empty( $testkeys->results )){
					$query['meta_query'][] = array(
							'key' => 'display_name',
							'value' => $search_string,
							'compare' => 'like'
					);
				}
				
				
					
				
			
			}
			/* Searchuser query param */

			parse_str($_SERVER['QUERY_STRING'], $params);
			foreach($params as $k => $v){
				$v = trim( strip_tags( esc_attr( $v ) ) );
				$cleankey = str_replace('emd-','',$k);
				
				if (strstr($cleankey, 'from_') ) {
					
					$rangekey = str_replace('from_','',$cleankey);
					if (is_numeric($v)){
						$rangefilter[$rangekey]['compare']['min'] = $v;
					}
				
				} elseif (strstr($cleankey, 'to_') ) {
					
					$rangekey = str_replace('to_','',$cleankey);
					if (is_numeric($v)){
						$rangefilter[$rangekey]['compare']['max'] = $v;
					}
				
				} else {

					if (in_array( 'emd_'.$cleankey, $userpro_emd->must_be_custom_fields )) {
						$cleanparams[$cleankey] = $v;
					} elseif ( $userpro->field_label($cleankey) != '') {
						$cleanparams[$cleankey] = $v;
					}
				
				}
			}
			if (isset($rangefilter)){
			
				foreach($rangefilter as $range_k => $arr ) {
				
					if ( ( isset($arr['compare']['min']) && isset($arr['compare']['max']) ) || isset($arr['compare']['min']) || isset($arr['compare']['max']) ) {
					
					if (!isset($arr['compare']['min'])){
						$split = explode(',',$args[$range_k . '_range']);
						$arr['compare']['min'] = $split[0];
					}
					
					if (!isset($arr['compare']['max'])){
						$split = explode(',',$args[$range_k . '_range']);
						$arr['compare']['max'] = $split[1];
					}
					
					$query['meta_query'][] = array(
						'key' => $range_k,
						'value' => array($arr['compare']['min'], $arr['compare']['max']),
						'compare' => 'between'
					);
					
					}
				}
				
			}
			
			
			
			
			
			
			if (isset($cleanparams)){
			foreach($cleanparams as $k => $v) {
				if ($k == 'photopreference') {
					if ($v === '1') {
					$query['meta_query'][] = array(
						'key' => 'profilepicture',
						'value' => '',
						'compare' => '!='
					);
					}
					if ($v === '2') {
					$query['meta_query'][] = array(
						'key' => 'profilepicture',
						'value' => '',
						'compare' => '=='
					);
					}
				} elseif ($k == 'accountstatus') {
					if ($v > 0) {
					$query['meta_query'][] = array(
						'key' => 'userpro_verified',
						'value' => 1,
						'compare' => '=='
					);
					}
				} elseif ($v != 'all') {
				
					if ($v != '') {
					
						if ($userpro->field_type($k) == 'multiselect' ||
							$userpro->field_type($k) == 'checkbox' ||
							$userpro->field_type($k) == 'checkbox-full'
							) {
							$like = 'like';
						} else {
							$like = '=';
						}
					
					$query['meta_query'][] = array(
						'key' => $k,
						'value' => $v,
						'compare' => $like
					);
					
					}
				}
			}
			}

		}

			$query['meta_query'][] =array(
                                'relation' => 'OR',
                                array(
				'key' => 'userpro_account_status',
				'compare' => 'NOT EXISTS'
                                    ),
                                array(
                                    'key' => 'userpro_account_status',
                                    'value' => '1',
                                    'compare' => '!='
                                )
			);
		
		if( isset($_GET['userpa']) && userpro_get_option('alphabetical_pagination') == 1 ){
			$query['meta_query'][] = array(
					'key' => 'display_name',
					'value' => '^'.$_GET['userpa'],
					'compare' => 'REGEXP'
			);
		}
		
		
		
		/** DO **/

		if ($sortby) $query['orderby'] = $sortby;
		
		if ($order) $query['order'] = strtoupper($order); // asc to ASC
		
		/** QUERY ARGS END **/
		
		$defaultorder= array('ID','display_name','name','user_name','login','user_login','nicename','user_nicename','email','user_email','url','user_url', 'registered','user_registered','post_count');
	/* Start Added By Yogesh for sorting the users*/
				
		if (in_array($sortby, $defaultorder))
		{
		$query['number'] = $per_page;
		$query['offset'] = $offset;
		}
  
    	/* End Added By Yogesh for sorting the users*/

		/* Search mode */
		if ( ( isset($_GET['searchuser']) && !empty($_GET['searchuser']) ) || count($query['meta_query']) > 1 ){
			$count_args = array_merge($query, array('number'=>10000));
			unset($count_args['offset']);
			$user_count_query = $userpro->get_cached_query( $count_args );
		}

		
		$wp_user_query = $userpro->get_cached_query( $query );
		if ($per_page) {
		
			/* Get Total Users */
			if ( ( isset($_GET['searchuser']) && !empty($_GET['searchuser']) ) || count($query['meta_query']) > 1 ){
				$user_count = $user_count_query->get_results();
				$total_users = $user_count ? count($user_count) : 1;
			} else {
				/* Commented by Ranjith to show proper count */
				//$result = count_users();
				//$total_users = $result['total_users'];
				$total_users = $wp_user_query->total_users;
			}
				
			$total_pages = ceil($total_users / $per_page);
		
		}
		
		remove_action( 'pre_user_query', 'userpro_query_search_displayname' );
		
		$url = parse_url(wp_guess_url());
		if (isset($url['query'])){
		$string_query = $url['query'];
		} else {
		$string_query = null;
		}

if (!in_array($sortby, $defaultorder))
		{
		/* Start Added By Yogesh for sorting the users*/
		$userstable = $wpdb->base_prefix."users";
		$usermetatable = $wpdb->base_prefix."usermeta";
		$query="SELECT a.ID
			FROM $userstable AS a
			JOIN $usermetatable ON a.ID= $usermetatable.user_id WHERE $usermetatable.meta_key = '$sortby' AND $usermetatable.user_id = a.ID ORDER BY   $usermetatable.meta_value $order";
		
		$orderArray=$wpdb->get_results($query);
		
		$allusers  = sortArrayByArray($wp_user_query->results ,$orderArray);
		
		$paged_users = array();
		
		$keyedUsers = array();
		foreach($allusers as $key => $user){
			$keyedUsers[] = $user;
		}
		for($i = $offset; $i<$offset+$per_page; $i++)
		{
			
		if(isset($keyedUsers[$i]))
			array_push($paged_users , $keyedUsers[$i]);
		}
		
		$wp_user_query->results = $paged_users;
		}
		/* End Added By Yogesh for sorting the users*/
		
			
		if (! empty( $wp_user_query->results )) {
			$arr['total'] = $total_users;
			$arr['paginate'] = paginate_links( array(
					'base'         => @add_query_arg('userp','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('« Previous','userpro'),
					'next_text'    => __('Next »','userpro'),
					'type'         => 'plain',
					'add_args' => false ,
				));
			$arr['users'] = $wp_user_query->results;
		}

		return $arr;
		
	}
	
	/* prepare loop of users list */
	function userpro_memberlist_listusers($args, $list_users=null){
		global $userpro, $wpdb;
		
		$list_per_page = $args['list_per_page'];
		$list_order = $args['list_order'];
		$list_sortby = $args['list_sortby'];
		$list_relation = $args['list_relation'];
		$list_verified = $args['list_verified'];
        $list_excludes = $args['exclude'];

		$blog_id = get_current_blog_id();
		
		if ( get_option('userpro_trial') == 1) {
			$list_per_page = 3;
			do_action('userpro_pre_form_message');
		}
		
		// Show Verified accounts only
		if ($list_verified) {
			$query['meta_query'] = array('relation' => strtoupper($list_relation) );
			$query['meta_query'][] = array(
				'key' => 'userpro_verified',
				'value' => 1,
				'compare' => '='
			);
		}

        // Exclude users
        if(isset($list_excludes)){
            $exclude = explode(',',$args['exclude']);
            $query['exclude'] = $exclude;
        }

		$query['orderby'] = $list_sortby;
		
		$query['order'] = strtoupper($list_order); // asc to ASC
		
		$query['number'] = $list_per_page;
		
		// Show/promote specific users
		if (isset($list_users) && !empty($list_users)){
			if ($list_users == 'author') {
				$author = get_the_author_meta('ID');
				$include[] = $author;
			} else {
				$usernames = explode(',',$list_users);
				foreach($usernames as $username){
					$user = get_user_by( 'login', $username );
					if ($user){
						$include[] = $user->ID;
					}
				}
			}
			
			if (isset($include) && is_array($include)){
			$query['include'] = $include;
			$query['number'] = 0;
			}
		}
		
		// Done, run query
		
		$wp_user_query = $userpro->get_cached_query( $query );
		
		if (! empty( $wp_user_query->results ))

			$arr['users'] = $wp_user_query->results;
			
		if (isset($arr)) return $arr;
		
	}
function sortArrayByArray($array, $orderArray) {
		$ordered = array();
		foreach($orderArray as $key => $ordarr) {
			foreach ($array as $unorderKey => $value){
					
				if($value->ID == $ordarr->ID){
					$ordered[$key] = $array[$unorderKey];
	
				}
			}
		}
		return $ordered;
	}
