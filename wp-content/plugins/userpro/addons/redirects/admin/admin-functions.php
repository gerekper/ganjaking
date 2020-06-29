<?php

	/* Get roles */
	function userpro_rd_get_roles() {
		if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
		$roles = $wp_roles->get_names();
		foreach($roles as $k=>$v) {
			?>
			<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
			<?php
		}
	}
	
	/* Get users */
	function userpro_rd_get_users() {
		$users = get_users();
		?>
		<option value="all"><?php _e('Map All Users','userpro'); ?></option>
		<?php
		foreach($users as $user) {
			?>
			<option value="<?php echo $user->ID; ?>"><?php echo $user->user_login; ?></option>
			<?php
		}
	}
	
	/* Get fields */
	function userpro_rd_get_fields() {
		$fields = get_option('userpro_fields');
		if(!empty($fields)) {
			foreach ($fields as $field => $arr) {

				?>
                <option value="<?php echo $field; ?>"><?php if(!empty($arr['label']))echo $arr['label']; ?></option>
				<?php
			}
		}
	}
	/* Check page exists */
	function userpro_rd_admin_page_exists($template) {
		$pages = get_option('userpro_rd_pages');
		if ($template=='view') $template = 'profile';
		if (isset($pages[$template]))
			$page_id = $pages[$template];
			$page_data = get_page($page_id);
			if($page_data->post_status == 'publish'){
				return true;
			}
		return false;
	}

	/* Get page link for social */
	function userpro_rd_admin_link($template){
		$pages = get_option('userpro_rd_pages');
		if ($template=='view') $template = 'profile';
		if (isset($pages[$template])){
			return get_page_link( $pages[$template] );
		}
	}
	
	/* list active redirects */
	function userpro_rd_list_redirects($type) {
		global $userpro;
		$res = '';
		//delete_option('userpro_redirects_'.$type);
		$redirects = get_option('userpro_redirects_'.$type);
		$res .= "<thead>
		<tr>
			<th scope='col' class='manage-column'>".__('Username','userpro')."</th>
			<th scope='col' class='manage-column'>".__('Role','userpro')."</th>
			<th scope='col' class='manage-column'>".__('Custom Field','userpro')."</th>
			<th scope='col' class='manage-column'>".__('Field Value','userpro')."</th>
			<th scope='col' class='manage-column'>".__('Redirection URL','userpro')."</th>
			<th scope='col' class='manage-column'>".__('Remove','userpro')."</th>
		</tr>
		</thead>";
		$res .= "<tfoot>
		<tr>
			<th scope='col' class='manage-column'>".__('Username','userpro')."</th>
			<th scope='col' class='manage-column'>".__('Role','userpro')."</th>
			<th scope='col' class='manage-column'>".__('Custom Field','userpro')."</th>
			<th scope='col' class='manage-column'>".__('Field Value','userpro')."</th>
			<th scope='col' class='manage-column'>".__('Redirection URL','userpro')."</th>
			<th scope='col' class='manage-column'>".__('Remove','userpro')."</th>
		</tr>
		</tfoot>";
		if (is_array($redirects)){
			$redirects = array_reverse( $redirects, true);
			foreach($redirects as $k => $info) {
				$user = get_userdata($info['user']);
				if(!empty($user)) {
					if ($user->ID) {
						$info['user'] = '<a href="' . $userpro->permalink($info['user']) . '">' . $user->user_login . '</a>';
					}
				}
				$res .= '<tr valign="top">';
				$res .= '<td>'.$info['user'].'</td>';
				$res .= '<td>'.userpro_user_role($info['role']).'</td>';
				$res .= '<td>'.$userpro->field_label( $info['field'] ).'</td>';
				$res .= '<td>'.$info['field_value'].'</td>';
				$res .= '<td>'.$info['url'].'</td>';
				$res .= '<td><a href="#" class="remove-redirect-rule" data-k="'.$k.'">'.__('Remove Rule','userpro').'</a></td>';
				$res .= '</tr>';
			}
		}
		return $res;
	}
	
	/* add new redirect via backend */
	add_action('wp_ajax_nopriv_userpro_new_redirect', 'userpro_new_redirect');
	add_action('wp_ajax_userpro_new_redirect', 'userpro_new_redirect');
	function userpro_new_redirect(){
		$output = array();
		
		if (!$_POST['rd_url']){
			$output['error'] = __('You did not specify a redirection URL.','userpro');
		} elseif ( !$_POST['rd_role'] && !$_POST['rd_user'] && !$_POST['rd_field'] ) {
			$output['error'] = __('You did not specify any custom condition (e.g. specific user) for this redirection.','userpro');
		} else {
		
			/* add the redirection rle */
			$type = $_POST['type'];
			$redirects = get_option('userpro_redirects_'.$type);
			$time = time();
			$redirects[$time] = array(
				'role' => $_POST['rd_role'],
				'user' => $_POST['rd_user'],
				'field' => $_POST['rd_field'],
				'field_value' => $_POST['rd_field_value'],
				'url' => $_POST['rd_url']
			);
			update_option( 'userpro_redirects_'.$type, $redirects);
			$output['html'] = userpro_rd_list_redirects( $type );
			
		}
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* remove an existing redirect */
	add_action('wp_ajax_nopriv_userpro_remove_redirect', 'userpro_remove_redirect');
	add_action('wp_ajax_userpro_remove_redirect', 'userpro_remove_redirect');
	function userpro_remove_redirect(){
		$output = array();
		
		if (!$_POST['key']) 
		{
			$key = (int)0;
		}
		else 
		{
			$key = $_POST['key'];
		}
		
		$type = $_POST['type'];
		
		$redirects = get_option('userpro_redirects_'.$type);
		unset($redirects[$key]);
		update_option( 'userpro_redirects_'.$type, $redirects);
		$output['html'] = userpro_rd_list_redirects( $type );
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}

