<?php

class userpro_ed_api {

    public $must_be_custom_fields;

	function __construct() {

		$this->must_be_custom_fields = array('emd_photopreference','emd_accountstatus');

	}

	// Get column field
	function print_field($field, $user_id) {
		global $userpro;
		if ($this->field_value($field, $user_id) != '' && userpro_field_is_viewable_noargs($field,$user_id)) { ?>

		<div class="emd-user-column">
		<div class="emd-user-left"><?php echo __($userpro->field_label($field), 'userpro'); ?></div>
		<div class="emd-user-right"><strong><?php echo $this->field_value( $field, $user_id ); ?></strong></div>
		<div class="userpro-clear"></div>
		</div>

		<?php
		}
	}

	// Get value
	function field_value($field, $user_id){
		$value = userpro_profile_data_nicename( $field, userpro_profile_data( $field, $user_id ) );
		return $value;
	}

	// Show filters or not
	function yes_show_filters( $args ){
		if ($args['emd_filters'] == 1 && $this->has_filters($args) != '')
			return true;
		return false;
	}

	// Has filters
	function has_filters( $args ) {

		$big_array = array_merge( userpro_retrieve_metakeys(), $this->must_be_custom_fields );
		foreach($args as $key=>$v) {
			if (in_array($key, $big_array) || in_array( str_replace('emd_','',$key), $big_array) ) {

				$options = explode(',', $args[$key]);
				if (!isset($options[1])){
					$type = 'input';
				} else {
					$type = $options[1];
				}
				if (in_array($key, $this->must_be_custom_fields)) $type = 'custom';
				$label = $options[0];
				if ($label) {
				$array[ $key ] = array('label' => $label, 'type' => $type );
				}

			}
		}

		if (isset($array) && is_array($array)){
			return 1;
		} else {
			return 0;
		}
	}

	// Show filters
	function show_filters( $args ) {

		$big_array = array_merge( userpro_retrieve_metakeys(), $this->must_be_custom_fields );
		foreach($args as $key=>$v) {
			if (in_array($key, $big_array) || in_array( str_replace('emd_','',$key), $big_array) ) {

				$options = explode(',', $args[$key]);
				if (!isset($options[1])){
					$type = 'input';
				} else {
					$type = $options[1];
				}
				if (in_array($key, $this->must_be_custom_fields)) $type = 'custom';
				$label = $options[0];
				if ($label) {
				$array[ $key ] = array('label' => $label, 'type' => $type );
				}

			}
		}

		if (is_array($array)){
			foreach( $array as $custom_field => $data ) {
				if ( strstr($custom_field, 'emd_') ) {
					$custom_field = str_replace('emd_','',$custom_field);
					?>

					<div class="emd-filter">

						<div class="emd-filter-head"><?php echo $data['label']; ?></div>

						<?php if ($data['type'] == 'dropdown') { ?>
						<select name="emd-<?php echo $custom_field; ?>" id="emd-<?php echo $custom_field; ?>" class="chosen-select" data-placeholder="<?php echo $this->placeholder($custom_field); ?>">
							<?php $this->loop_options( $custom_field ); ?>
						</select>
						<?php } ?>

						<?php if ($data['type'] == 'radio') { ?>
						<?php $this->loop_options_radio( $custom_field ); ?>
						<?php } ?>

						<?php if ($data['type'] == 'input') { ?>
						<div class="userpro-input">
							<input type="text" name="emd-<?php echo $custom_field; ?>" id="emd-<?php echo $custom_field; ?>" placeholder="<?php echo $this->placeholder($custom_field); ?>" value="<?php echo $this->try_text_value( $custom_field ); ?>" />
						</div><div class="userpro-clear"></div>
						<?php } ?>

						<?php if ($data['type'] == 'custom') { ?>
						<?php $this->loop_custom_options( $custom_field ); ?>
						<?php } ?>

					</div>

				<?php
				}
			}
		}

	}

	// API user Query
	function users( $args ){
		global $userpro, $wpdb;
		$blog_id = get_current_blog_id();

		$page = (!empty($_GET['emd-page'])) ? $_GET['emd-page'] : 1;
		$offset = ( ($page -1) * $args['emd_per_page'] );

		/* setup query params */
		$query = $this->setup_query( $args );

		if (isset($args['sortby'])) {
			$defaultorder= array('ID','display_name','name','user_name','login','user_login','nicename','user_nicename','email','user_email','url','user_url', 'registered','user_registered','post_count');
			if(!in_array($args['sortby'], $defaultorder)){
				$query['orderby'] = "meta_value";
				$query['meta_key'] = $args['sortby'];
			}else{
				$query['orderby'] = $args['sortby'];
			}
			$query['order'] = $args['order'];
		}

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

		/* meta query for profile privacy*/
		$query['meta_query'][]= array(
				'key' => 'profile_privacy',
				'compare' => 'NOT EXISTS'
		);
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
		/* End */

		/* pagi stuff */
		$query['number'] = $args['emd_per_page'];
		$query['offset'] = $offset;

		$count_args = array_merge($query, array('number'=>99999999999));
		unset($count_args['offset']);

		$user_count_query = $userpro->get_cached_query( $count_args );
		//$user_count_query = new WP_User_Query($count_args);

		if ($args['emd_per_page']) {
		$user_count = $user_count_query->get_results();
		$total_users = $user_count ? count($user_count) : 1;
		$total_pages = ceil($total_users / $args['emd_per_page']);
		}

		$wp_user_query = $userpro->get_cached_query( $query );
		//$wp_user_query = new WP_User_Query($query);

		if (! empty( $wp_user_query->results ))
			$big = 999999999; // need an unlikely integer
			$arr['paginate'] = paginate_links( array(
					'base'         => @add_query_arg('emd-page','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('Previous','userpro'),
					'next_text'    => __('Next','userpro'),
					'type'         => 'plain',
					'add_args' => false ,
				));
			$arr['users'] = $wp_user_query->results;

		return $arr;
	}
function collageusers( $args ){
		global $userpro, $wpdb;
		$blog_id = get_current_blog_id();

		$page = (!empty($_GET['emd-page'])) ? $_GET['emd-page'] : 1;
		$offset = ( ($page -1) * $args['collage_per_page'] );

		/* setup query params */
		$query = $this->setup_query( $args );

		if (isset($args['sortby'])) {
			$query['orderby'] = $args['sortby'];
			$query['order'] = $args['order'];
		}

		/* exclude specific users? */
		if (isset($args['exclude'])){
			$exclude = explode(',',$args['exclude']);
			$query['exclude'] = $exclude;
		}
		/* Start Added by Ranjith to hide/show unapproved users - Niranjan */

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

		/* pagi stuff */
		$query['number'] = $args['collage_per_page'];
		$query['offset'] = $offset;

		$count_args = array_merge($query, array('number'=>99999999999));
		unset($count_args['offset']);

		$user_count_query = $userpro->get_cached_query( $count_args );
		//$user_count_query = new WP_User_Query($count_args);

		if ($args['collage_per_page']) {
			$user_count = $user_count_query->get_results();
			$total_users = $user_count ? count($user_count) : 1;
			$total_pages = ceil($total_users / $args['collage_per_page']);
		}

		$wp_user_query = $userpro->get_cached_query( $query );
		//$wp_user_query = new WP_User_Query($query);

		if (! empty( $wp_user_query->results ))
			$big = 999999999; // need an unlikely integer
		$arr['paginate'] = paginate_links( array(
				'base'         => @add_query_arg('emd-page','%#%'),
				'total'        => $total_pages,
				'current'      => $page,
				'show_all'     => false,
				'end_size'     => 1,


				'mid_size'     => 2,
				'prev_next'    => true,
				'prev_text'    => __('Previous','userpro'),
				'next_text'    => __('Next','userpro'),
				'type'         => 'plain',
				'add_args' => false ,
		));
		$arr['users'] = $wp_user_query->results;
		return $arr;
	}



	// Setup query
	function setup_query( $args ) {
		global $wpdb, $userpro;
		$blog_id = get_current_blog_id();
		$query['meta_query'] = array('relation' => 'AND');
		$query['orderby'] = 'registered';
		$query['order'] = 'desc';

		/* Query Parameters */

		/* Role */
		if (isset($args['role'])){
			$role = str_replace(',', '|', $args['role']);
			$query['meta_query'][] = array(
						'key' => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
						'value' =>$role,
						'compare' => 'REGEXP'
					);
		}

		/* meta keys */
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

		/* Search Parameters */

		if (isset($_GET['emd-search'])) {

			parse_str($_SERVER['QUERY_STRING'], $params);
			foreach($params as $k => $v){
				$v = trim( strip_tags( esc_attr( $v ) ) );
				$cleankey = str_replace('emd-','',$k);
				if (in_array( 'emd_'.$cleankey, $this->must_be_custom_fields )) {
					$cleanparams[$cleankey] = $v;
				} elseif ( $userpro->field_label($cleankey) != '') {
					$cleanparams[$cleankey] = $v;
				}
			}

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
		return $query;
	}

	// Loop custom options
	function loop_custom_options( $key ) {
		switch($key) {
			case 'photopreference':
				$array = array(
					0 => __('No preference','userpro'),
					1 => __('Only members with photo','userpro'),
					2 => __('Only members without photo','userpro'),
				);
				break;
			case 'accountstatus':
				$array = array(
					0 => __('All accounts','userpro'),
					1 => __('Only verified accounts','userpro'),
				);
				break;
		}

		if (isset($array)){
		foreach($array as $k=>$v) {
			echo '<label class="userpro-radio full"><span ';
			if (isset($_GET['emd-'.$key]) && $_GET['emd-'.$key] == $k || !isset($_GET['emd-'.$key]) && $k == 0 ) { echo 'class="checked"'; }
			echo '></span><input type="radio" value="'.$k.'" name="emd-'.$key.'" ';
			if (isset($_GET['emd-'.$key]) && $_GET['emd-'.$key] == $k || !isset($_GET['emd-'.$key]) && $k == 0 ) { echo 'checked="checked"'; }
			echo '/>'.$v.'</label>';
		}
		}
	}

	// Loop thru options
	function loop_options( $key, $num_range=null, $placeholder = null ){
		$fields = get_option('userpro_fields');
		if(isset($fields[$key]))
		{
			$array = $fields[$key]['options'];
		}
        echo '<option value="" disabled selected>'.$placeholder.'</option>';


		if ($num_range) {

			$str = explode(',',$num_range);
			$low = $str[0];
			$max = $str[1];

			for($v = $low; $v <= $max; $v++) {

				echo '<option value="'.$v.'" ';
				if (isset($_GET['emd-'.$key]) && $_GET['emd-'.$key] == $v) { echo 'selected="selected"'; }
				echo '>'.$v.'</option>';

			}

		} else {

			echo '<option value="all" ';
			if (isset($_GET['emd-'.$key]) && $_GET['emd-'.$key] == 'all' ) { echo 'selected="selected"'; }
			echo '>'.__('All','userpro').'</option>';

			foreach($array as $k=>$v) {

				echo '<option value="'.$v.'" ';
				if (isset($_GET['emd-'.$key]) && $_GET['emd-'.$key] == $v) { echo 'selected="selected"'; }
				echo '>'.$v.'</option>';

			}

		}
	}

	// Loop thru options
	function try_text_value( $key ){
		if (isset($_GET['emd-'.$key])){
			$v = $_GET['emd-'.$key];
			$v = trim( strip_tags( esc_attr( $v ) ) );
			return $v;
		}
	}

	// Loop radio choices
	function loop_options_radio( $key ){
		$fields = get_option('userpro_fields');
		$array = $fields[$key]['options'];
			echo '<label class="userpro-radio full"><span ';
			if (isset($_GET['emd-'.$key]) && $_GET['emd-'.$key] == 'all' || !isset($_GET['emd-'.$key]) ) { echo 'class="checked"'; }
			echo '></span><input type="radio" value="all" name="emd-'.$key.'" ';
			if (isset($_GET['emd-'.$key]) && $_GET['emd-'.$key] == 'all' || !isset($_GET['emd-'.$key]) ) { echo 'checked="checked"'; }
			echo '/>'.__('All','userpro').'</label>';
		foreach($array as $k=>$v) {
			if ($v != '') {
			echo '<label class="userpro-radio full"><span ';
			if (isset($_GET['emd-'.$key]) && $_GET['emd-'.$key] == $v ) { echo 'class="checked"'; }
			echo '></span><input type="radio" value="'.$v.'" name="emd-'.$key.'" ';
			if (isset($_GET['emd-'.$key]) && $_GET['emd-'.$key] == $v ) { echo 'checked="checked"'; }
			echo '/>'.$v.'</label>';
			}
		}
	}

	// get placeholder
	function placeholder( $key ){
		global $userpro;
		$fields = $userpro->fields;
		if (isset(  $fields[$key]['placeholder'] ) ) {
		$data = $fields[$key]['placeholder'];
		} else {
		$data = sprintf(__('Select %s','userpro'), $userpro->field_label($key));
		}
		return $data;
	}

}

$GLOBALS['userpro_emd'] = new userpro_ed_api();
