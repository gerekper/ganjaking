<?php
/**
 * Admin
 * @version 0.1
 */
class evoaup_admin{
	private $assigned_user_roles;
	public function __construct(){
		add_action('evoau_poptable', array($this, 'lightbox_content'), 10, 1);
		add_action('evoau_assigninfo_display', array($this, 'event_post_html'), 10, 1);
		add_action('evoau_save_assigned_user_data', array($this, 'save_user_roles'), 10, 1);		
		add_filter('evoau_saved_assigned_user_results', array($this, 'json_results'), 10, 2);		
		add_action( 'admin_init', array( $this, 'admin_scripts' ) ,15);

		add_action('save_post', array($this, 'save_meta_box'), 15,2);
		add_filter('evoau_settings', array($this,'settings'), 10, 1);

		add_filter('evoau_save_settings_optionvals', array($this,'save_settings'), 10, 2);

		add_action( 'add_meta_boxes', array($this, 'meta_boxes') );
		add_action('evoau_assigninfo_display', array($this, 'assigninfo_display'),10,2);

		include_once('class-lang.php');

		// User profile
		add_action('evoau_user_profile_info', array($this,'user_info'),10,1);

	}


	// user profile
		function user_info($user){

			$FNC = new evoaup_fnc();

			$data = $FNC->have_valid_event_submissions();
			//print_r($data );

			if(!$data) return;
			extract($data);


			if($submission_format =='level_based'){
				?>
				<tr>
					<th><label><?php _e('Paid Event Submissions Available');?></label></th>
					<td>
						<span style='display:block'><b style='padding: 3px 10px;background-color: #a9a9a9;border-radius: 12px;color: #fff;'><?php echo $allcount;?>x</b> <?php _e('Total Event Submission');?></span></br>
						<?php
						$submission_levels = $FNC->get_submission_levels();
						
						foreach($submission_data as $index=>$c){
							echo "<span style='margin-right:10px;' ><b style='padding: 3px 10px;background-color: #cccccc;border-radius: 12px;color: #fff;'>".$c."x</b> ". $submission_levels[ $index ]['name'].  "</span>";
						}
						?>
					</td>
				</tr><?php
			}else{

			}
		}

	// SCRIPT
		function admin_scripts(){
			global $eventon_aup;

			wp_enqueue_style( 'evoaup_admin_styles',EVOAUP()->assets_path.'styles_admin.css', '', 
				EVOAUP()->version);
			wp_enqueue_script( 'evo_aup_backend',EVOAUP()->assets_path.'admin-script.js',array('jquery'),$eventon_aup->version,true);
			wp_localize_script( 
				'evo_aup_backend', 
				'evoaup_admin_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'evoaup_nonce' )
				)
			);
		}

	// meta boxes
		// actionUser meta box additions
		function assigninfo_display($eid, $EVENT){
			$SL = $EVENT->get_prop('_evoaup_submission_level');

			if(!$SL) return;

			$fnc = new evoaup_fnc();
			$submission_level_data = $fnc->get_submission_level_data($SL);
			if(!isset($submission_level_data['name'])) return;

			$color = isset($submission_level_data['color'])? '#'.$submission_level_data['color']: false;

			echo "<div class='evoau_assign_users' style='margin-top:10px'><p>". __('Event submitted using submission level')." <span style='background-color:{$color};color:#fff;font-size:11px;border-radius: 5px;padding: 1px 5px;'>". $submission_level_data['name'] ."</span></p></div>";
		}
		function meta_boxes(){
			global $post;

			$_order_type = get_post_meta($post->ID, '_order_type',true);

			if($_order_type == 'evo_submission' || $_order_type == 'evotix'){

				if($post->post_status != 'wc-completed') return;
				$submission_data = get_post_meta($post->ID, '_submission_data',true);
				$submission_count = get_post_meta($post->ID, '_submission_count',true);

				if(empty($submission_data) && empty($submission_count)) return; 
				add_meta_box('evoaup_box',__('Order Details','eventon'), array($this,'metabox_content'),'shop_order', 'side', 'default');
			}
		}

		// order post
		function metabox_content(){
			global $post;

			echo "<style type='text/css'>
				.evoaup_event_submission_data{margin:-7px -12px -12px -12px; padding:0px;}
				.evoaup_order_metabox{background-color:#fed583;padding:15px; }
				.evoaup_order_metabox p{margin:0; position: relative}
				.evoaup_order_metabox p.evosup_submissions_left{height:45px; display:flex}
				.evoaup_order_metabox input{ width:80px; border:none; background-color:#ffffff94; border-radius: 25px; font-weight:900; font-size:24px;text-align:center; padding:5px 10px; font-family:'open sans'; }
				.evoaup_order_metabox em{font-size:18px; padding-left:10px; display:block; padding-top:8px}
				.evoaup_submitted_events{margin: 0; background-color:#f4d6a9; padding:15px;border-top:1px solid #eac388}
				.evoaup_submitted_events p{margin:0; padding-bottom:5px;}
			</style>";

			//$pmv = get_post_meta($post->ID);
			
			$order_id = $post->ID;
			$submission_data = get_post_meta($order_id, '_submission_data',true);
			$submissions_left = get_post_meta($order_id, '_submission_count',true);
			
			echo "<div class='evoaup_event_submission_data'>";

			if(!empty($submission_data)){
				$fnc = new evoaup_fnc();
				$submission_levels = $fnc->get_submission_levels();
			?>
				<div class='evoaup_order_metabox'>
					<p style='padding-bottom: 10px'><?php _e('Event Submissions Remaining','evoaup');?></p>
				<?php
				foreach($submission_data as $level=>$count){
					$level_data = isset($submission_levels[$level])? $submission_levels[$level]: false;

					if(!$level_data) continue;

				?>
					<p class="evosup_submissions_left">
						<input type="text" name='_submission_data[<?php echo $level;?>]' value='<?php echo $count;?>'/>
						<em><?php echo $level_data['name']?></em>
						<i class="clear"></i>
					</p>
				<?php
				}
				?>					
				</div>
			<?php 
			}else{// old method
			?>
				<div class='evoaup_order_metabox'>
					<p style='padding-bottom: 10px'><?php _e('Event Submissions Remaining','evoaup');?></p>
					<p class="evosup_submissions_left">
						<input type="text" name='_submission_count' value='<?php echo $submissions_left;?>'/>
						<em><?php _e('General','eventon')?></em>
						<i class="clear"></i>
					</p>
				</div>
			<?php 
			}
			
			// show submitted event IDs
			$_submitted_events = get_post_meta($order_id, '_submitted_events', true);
			if(!empty($_submitted_events) && is_array($_submitted_events)):
			?>
				<div class="evoaup_submitted_events">
					<p><?php _e('Submitted Events','eventon');?></p>
					<?php
					foreach($_submitted_events as $eventid){
						echo "<a class='button' href='". get_edit_post_link($eventid) ."'>".$eventid . "</a>";
					}
					?>
				</div>
			<?php
			endif;

			echo "</div>";
			
		}		
		function save_meta_box($post_id, $post){			
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
			if (defined('DOING_AJAX') && DOING_AJAX) return;
			if($post->post_type!='shop_order')	return;

			// update submission count on the order
			if(isset($_POST['_submission_count'])){
				$count = empty($_POST['_submission_count'])? 0: $_POST['_submission_count'];
				update_post_meta($post_id, '_submission_count', $count);
			}
			
			if(isset($_POST['_submission_data'])) update_post_meta($post_id, '_submission_data', $_POST['_submission_data']);
		}

	// User roles
		function lightbox_content($p_id){
			// user roles
				$saved_user_roles = wp_get_object_terms($p_id, 'event_user_roles', array('fields'=>'slugs'));
				$saved_user_roles = (!empty($saved_user_roles))? $saved_user_roles:null;
			?>
			<div id='evoau_role_list' class='evoau_assign_selection'>
				<p><i><?php _e('Select userroles that are assigned to this event. You can use this to create calendars so only those user role types can see this associated events. eg. [add_eventon userroles=\'editor\']','eventon');?></i></p>
				<?php
					$assigned_user_roles = array();

					//print_r($saved_user_roles);

					// ALL
						$checkbox_state = ''; $all = false;
						if(is_array($saved_user_roles) && !empty($saved_user_roles) && in_array('all', $saved_user_roles) ){
							$checkbox_state = 'checked="checked"';
							$assigned_user_roles[] = 'All Users';
							$all = true;
						}

						echo "<p><input name='event_user_roles[]' class='evoau_user_role_list_item allusers' type='checkbox' value='all' ".$checkbox_state."> ".__('All Users','eventon')."</p>";

					foreach( get_editable_roles() as $role_name=>$role_info){
						$checkbox_state='';
						if(!empty($saved_user_roles) && is_array($saved_user_roles) &&  in_array($role_name, $saved_user_roles)
						){									
							$checkbox_state = 'checked="checked"';
							$assigned_user_roles[] = $role_info['name'];
						}

						// if no user roles are selected
						if($all) $checkbox_state = 'checked="checked"';
						echo "<p><input name='event_user_roles[]' id='evoau_role_".$role_name."' class='evoau_user_role_list_item' type='checkbox' value='".$role_name."' uname='".$role_info['name']."' ".$checkbox_state."> ".$role_info['name']."</p>";
					}
				?>
			</div>
			<?php
		}

		// HTML for user roles selected information for event edit page
		function event_post_html($event_id){

			$saved_user_roles = wp_get_object_terms($event_id, 'event_user_roles', array('fields'=>'slugs'));
			$saved_user_roles = (!empty($saved_user_roles))? $saved_user_roles:null;

			$assigned_user_roles = array();
			if(is_array($saved_user_roles)  && !empty($saved_user_roles)){
				if( in_array('all', $saved_user_roles) ){
					$assigned_user_roles[] = 'All Users';
				}else{
					foreach(get_editable_roles() as $role_name=>$role_info){
						if( in_array($role_name, $saved_user_roles)){
							$assigned_user_roles[] = $role_info['name'];
						}
					}
				}
			}	
			?>
			<div class="evoau_assign_users user_roles evoau_assigned_usersroles_in">
				<?php
					if(!empty($assigned_user_roles)){
						echo "<h4>".__('User Roles Assigned to this Event','eventon')."</h4>";
						foreach($assigned_user_roles as $role){
							echo "<p><i>{$role}</i></p>";
						}
					}else{
						echo "<p>".__('You can assign user roles to this event and build calendars with events from only those user roles.','eventon')."</p>";
					}
				?>				
			</div>
			<?php
		}
		function save_user_roles($eventid){
			$roles = !empty($_POST['event_user_roles'])? $_POST['event_user_roles']: false;
			$results = wp_set_object_terms( $eventid, $roles, 'event_user_roles' );
		}
		// JSON results
		function json_results($array, $event_id){
			$saved_user_roles = wp_get_object_terms($event_id, 'event_user_roles', array('fields'=>'slugs'));
			$saved_user_roles = (!empty($saved_user_roles))? $saved_user_roles:null;

			$all_roles = get_editable_roles();			
			$assigned_user_roles = array();	

			// Get Assigned users information
				if(is_array($saved_user_roles)  && !empty($saved_user_roles)){
					if( in_array('all', $saved_user_roles) ){
						$assigned_user_roles[] = 'All Roles';
					}else{
						foreach($all_roles as $role_name=>$role_info){
							if( in_array($role_name, $saved_user_roles)){
								$assigned_user_roles[] = $role_info['name'];
							}
						}
					}
				}

			ob_start();
			if(count($assigned_user_roles)>0 && !empty($assigned_user_roles)){
				echo "<h4>".__('User Roles Assigned to this Event','eventon')."</h4>";
				
				foreach($assigned_user_roles as $role){
					echo "<p><i>{$role}</i></p>";
				}
			}else{
				echo "<p>".__('You can assign user roles to this event and build calendars with events from only those user roles.','eventon')."</p>";
			}

			$array['content_aup'] = ob_get_clean();
			return $array;
		}

	// Settings
		function settings($array){

			$array[] = array(
				'id'=>'evoAU6',
				'name'=>'Paid Submissions Settings',
				'tab_name'=>'Paid Submissions','icon'=>'dollar-sign',
				'fields'=>array(
					array(
						'id'=>'evoaup_create_product',
						'type'=>'yesno',
						'name'=>__('Activate paid submissions','evoaup') 
					),
					array('id'=>'evoaup_product_id',
						'type'=>'customcode',
						'code'=> $this->customCode() 
					),

					array('id'=>'evoaup_submission_page',
						'type'=>'text',
						'name'=>__('URL for event submission form page','evoaup'), 
						'legend'=> __('Type the direct link where you have included the event submission form where customers can submit their paid events submissions. If provided this link will be used to redirect customers on order complete details page.','evoaup'),
						'default'=>'eg. http://www.google.com' 
					),
					array(
						'id'=>'evoaup_note',
						'type'=>'note',
						'name'=>__('Note: If guest checkout is allowed for woocommerce, checking out with event submissions in cart will automatically require customers to create accounts, as an account is required to track their purchased event submissions','evoaup') 
					),
			));
			return $array;
		}
		function customCode(){
			$opt = get_option('evcal_options_evoau_1');
			$product_id = (!empty($opt['evoaup_product_id'])? $opt['evoaup_product_id']: false);

			$wc_currency_sim = get_woocommerce_currency_symbol();

			$ppmv = false;
			if($product_id){
				// check if wc product is published
				$HELP = new evo_helper();

				if($HELP->post_exist( $product_id )){
					$ppmv = get_post_meta($product_id);
				}else{
					$product_id = false;
				}
			}



			ob_start();

			$fnc = new evoaup_fnc();


			$sub_levels = !empty($opt['evoaup_levels'])? $opt['evoaup_levels']: false;

			?>

			<ul class='evoaup_submission_levels'> 
			<?php
			if(!empty($opt['evoaup_levels'])){
				//print_r($opt['evoaup_levels']);
				$sub_levels = $opt['evoaup_levels'];

				if(sizeof($sub_levels)>0){
					foreach($sub_levels as $index=>$level){
						echo $fnc->get_admin_submission_level_html($level, $index);
					}
				}
			}else{
				echo "<p class='none'>".__('You do not have any submission levels!','eventon')."</p>";
			}
			?>
				
			</ul>
			
			<?php
				$attrs = '';
				foreach(array(
					'data-popc'=>'evoaup_lightbox',
					'data-type'=>'new',
					'title'=>__('Add a New Submission Level','eventon')
				) as $key=>$val){
					$attrs .= $key .'="'. $val .'" ';
				}
			?>
			<p><a class='evo_admin_btn btn_secondary ajde_popup_trig evoaup_sl_form'  <?php echo $attrs;?>><?php _e('Add Event Submission Level','eventon');?></a></p>

			<?php


			global $ajde;
			echo $ajde->wp_admin->lightbox_content(array(
				'class'=>'evoaup_lightbox', 
				'content'=>"<p class='evo_lightbox_loading'></p>",
				'title'=>__('Submission Level','eventon'),
				'width'=>'500',
				'outside_click'=>false
				)
			);
			
			echo "<p>".__('Default Price per one event submission (This will be used if submission levels are not created)','eventon'). ' ('.$wc_currency_sim.')</p>';
			echo "<p><span class='nfe_f_width'><input type='text' name='evoaup_price' value='".($ppmv && !empty($ppmv['_regular_price']) ? $ppmv['_regular_price'][0]:'')."' placeholder='eg. 10.00'/></span></p>";
			

			echo '<input type="hidden" name="evoaup_product_id" value="'.$product_id.'"/>';
			return ob_get_clean();
		}

	// Save actionUser plus settigns
		function save_settings($options, $focusTab){
			if($focusTab!= 'evoau_1') return $options;
			$debug = '';
			// enabled
			if(!empty($options['evoaup_create_product']) && $options['evoaup_create_product']=='yes'){

				// there is a woocommerce product
				if(empty( $options['evoaup_product_id']) ){
					$wooid = $this->add_new_woocommerce_product();
					$options['evoaup_product_id'] = $wooid;
					$debug .= $wooid;

				}else{
					$this->save_product_meta_values($options['evoaup_product_id']);
					$debug .= 't';
				}
			}

			//update_post_meta(1900,'aaaa',$debug);

			return $options;
		}

	// Woocommerce
		function add_new_woocommerce_product(){
			$user_ID = get_current_user_id();
			//$sku = 'sku_'.rand(2000,4000);
			$event_title =  'Paid Event Submission';
			
			$post = array(
				'post_author' => $user_ID,
				'post_content' =>  "Event Submission",
				'post_status' => "publish",
				'post_title' => $event_title,
				'post_type' => "product"
			);

			// create woocommerce product
			$woo_post_id = wp_insert_post( $post );
			if($woo_post_id){				
				wp_set_object_terms($woo_post_id, 'simple', 'product_type');				

				$this->save_product_meta_values($woo_post_id);

				// add category 
				$this->assign_woo_cat($woo_post_id);
				return $woo_post_id;
			}
			return false;
		}
		// Save woocommerce product meta values
			function save_product_meta_values($woo_post_id){

				$sku = 'sku_'.rand(2000,4000);
				update_post_meta($woo_post_id, '_sku',  $sku);

				$_product_type = 'evo-submission';
				$price = !empty($_POST['evoaup_price'])? str_replace('$','',$_POST['evoaup_price']):'0.00';
				
				// price					
					if($price){
						update_post_meta($woo_post_id, '_price', $price );
						update_post_meta($woo_post_id, '_regular_price', $price );
					}

				update_post_meta($woo_post_id, '_producttype', $_product_type );
				update_post_meta($woo_post_id, '_visibility', 'hidden');

				$WC_product = wc_get_product($woo_post_id);
				if($WC_product){
					$WC_product->set_catalog_visibility('hidden');
					$WC_product->save();
				}
				
				
				update_post_meta($woo_post_id, '_virtual', 'yes');
				update_post_meta($woo_post_id, '_stock_status', 'instock');
				if(!empty($_POST['_stock']) ){
					update_post_meta($woo_post_id, '_stock', $_POST['_stock']);
					update_post_meta($woo_post_id, '_manage_stock', 'yes');
				}else{
					delete_post_meta($woo_post_id, '_stock');
					update_post_meta($woo_post_id, '_manage_stock', 'no');
				}
				//update_post_meta($woo_post_id, '_sold_individually', 'instock');

			}

		// create and assign woocommerce product category for foodpress items
			function assign_woo_cat($post_id){
				//defaults
				$term_name = 'Evo_submission';
				$term_slug = 'evo-submission';

				// check if term exist
				$terms = term_exists( $term_name, 'product_cat');
				if(!empty($terms) && $terms !== 0 && $terms !== null){
					wp_set_post_terms( $post_id, $terms, 'product_cat' );
				}else{
					// create term
					$new_termid = wp_insert_term(
					  	$term_name, 'product_cat',	array(	'slug'=>$term_slug)
					);

					// assign term to woo product
					wp_set_post_terms( $post_id, $new_termid, 'product_cat' );
				}				
			}

	
}