<?php
/**
 * Multi Data Types Class
 * @version 2.5.3
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class evo_mdt{
	public $opt;
	public function __construct(){
		$this->opt = get_option('evcal_options_evcal_1');

		add_action('admin_init', array($this, 'admin_init'));
		// /add_action('init', array($this, 'init'));

		// register MDT
		add_action('eventon_register_taxonomy', array($this, 'register'), 10);

		// frontend boxes
		add_filter('eventon_eventcard_array', array($this, 'eventcard_array'), 10, 4);
		add_filter('evo_eventcard_adds', array($this, 'eventcard_adds'), 10, 1);
		
		for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){
			add_filter('eventon_eventCard_evomdt_'.$x, array($this, 'frontend_box'), 10, 2);
		}
	}

	function admin_init(){
		add_filter('eventon_event_metaboxs',array($this, 'event_metabox'), 10, 1);
		add_action('eventon_save_meta', array($this, 'save_event_post'), 10, 2);

		add_action( 'wp_ajax_evo_mdt', array( $this, 'evomdt_ajax' ) );
		//add_action( 'wp_ajax_nopriv_evo_mdt', array( $this, 'evomdt_ajax' ) );
		
		add_action( 'eventon_eventcard_boxes', array( $this, 'eventCard_inclusion' ), 10,1 );
		add_filter( 'eventon_custom_icons',array($this, 'custom_icons') , 10, 1);

		// each multi data types
			//add_action( 'event_speaker_add_form_fields', array($this,'add_meta_fields'), 10, 2 );
	 		//add_action( 'event_speaker_edit_form_fields', array($this,'edit_meta_fields'), 10, 2 );
	 		//add_action( 'edited_event_speaker', array($this,'save_tax_meta'), 10, 2 );
	}

	// Register
		function register(){
			// Each activated multi data types
			$evo_get_mdt_names = $this->evo_get_mdt_names($this->opt);

			$__capabilities = array(
				'manage_terms' 		=> 'manage_eventon_terms',
				'edit_terms' 		=> 'edit_eventon_terms',
				'delete_terms' 		=> 'delete_eventon_terms',
				'assign_terms' 		=> 'assign_eventon_terms',
			);

			for($x=1; $x<= $this->evo_get_mdt_count($this->opt); $x++){
				$mdt_name = $evo_get_mdt_names[$x];

				register_taxonomy( 'multi_data_type_'.$x, 
					apply_filters( 'eventon_taxonomy_objects_mdt'.$x, array('ajde_events') ),
					apply_filters( 'eventon_taxonomy_args_mdt'.$x, array(
						'hierarchical' 			=> false, 
						'label'	 				=> $mdt_name,
						'show_ui' => true,
						'show_in_menu'=>true,
						'show_in_nav_menu'=>true,
						'show_tagcloud'=>false,
						'show_admin_column'=>false,
						'show_in_quick_edit'         => false,
	    				'meta_box_cb'                => false,
						'query_var' => true,
						'capabilities'			=> $__capabilities,
						'rewrite' => array( 'slug' => 'multi-data-type-'.$x ) 
					)) 
				);
			}
		}

	// Frontend
		function eventCard_inclusion($array){
			$mdt_name = $this->evo_get_mdt_names();
			for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){
				$array['evomdt_'.$x]= array( 'evomdt_'.$x, $mdt_name[$x]);
			}
			return $array;
		}
		function custom_icons($array){
			$mdt_name = $this->evo_get_mdt_names();
			for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){
				$array[] = array('id'=>'evcal__evomdt_'.$x,'type'=>'icon','name'=> $mdt_name[$x].' Icon','default'=>'fa-list');
			}
			return $array;
		}
		function frontend_box($object, $helpers){

			$x = $object->x;
			$mdt_name = $this->evo_get_mdt_names();
			$terms = wp_get_post_terms($object->event_id, $object->tax);

			if ( $terms && ! is_wp_error( $terms ) ):
			ob_start();
			echo  "<div class='evo_metarow_mdt_{$x} evo_metarow_mdt evorow evcal_evdata_row bordb evcal_evrow_sm".$helpers['end_row_class']."' data-event_id='".$object->event_id."'>
					<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__evomdt_'.$x, 'fa-list',$helpers['evOPT'] )."'></i></span>
					<div class='evcal_evdata_cell'>";
				echo "<h3 class='evo_h3'>".evo_lang($mdt_name[$x])."</h3>";

				if(!empty($object->pmv['_evomdt_subheader_'.$x]))
					echo "<p class='evomdt_subtitle'>".$object->pmv['_evomdt_subheader_'.$x][0]."</p>";

				echo "<div class='evomdt_data grid'>";
				// each term
				$tax_data = $this->get_mdt_term_data( $object->tax );
				foreach($terms as $term){
					echo "<div class='evomdt_item'>";
					$img_attr = '';
					if(!empty($tax_data[$term->term_id]['image']))
						$img_attr = wp_get_attachment_image_src( $tax_data[$term->term_id]['image'], 'full' );

					$img = !empty($img_attr)? "<img src='".$img_attr[0]."'/>":'';

					echo $img;
					echo "<h4 class='evo_h4'>".$tax_data[$term->term_id]['name'].'</h4>';
					echo apply_filters('the_content',$tax_data[$term->term_id]['description']);

					// additional data fields
					$this->additional_field_values($object->tax, $tax_data , $term->term_id);

					echo "</div>";
				}
				echo "<div class='clear'></div>";
				echo "</div>";

			echo "</div>";
			echo "</div>";

			return ob_get_clean();
			endif;
		}
		function eventcard_array($array, $pmv, $eventid, $__repeatInterval){
			for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){
				$array['evomdt_'.$x]= array(
					'event_id' => $eventid,
					'pmv'=>$pmv,
					'x'=>$x,
					'tax'=>'multi_data_type_'.$x,
					'__repeatInterval'=>(!empty($__repeatInterval)? $__repeatInterval:0)
				);
			}
			return $array;
		}
		function eventcard_adds($array){
			for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){
				$array[] = 'evomdt_'.$x;
			}
			return $array;
		}

		function additional_field_values($tax, $tax_data, $termid){

			$mdt_index = $this->tax_index($tax);

			for( $z=1; $z <= $this->evo_max_mdt_addfield_count(); $z++){
				$postfix = $mdt_index. '_' .$z;
				if( evo_settings_check_yn($this->opt , 'evcal_mdta_'.$postfix) &&
					!empty( $this->opt[ 'evcal_mdta_name_'.$postfix ]) &&
					!empty( $tax_data[$termid]['evcal_mdta_'.$postfix])
				){	
					echo "<p><span>" . $this->opt[ 'evcal_mdta_name_'.$postfix] . "</span>";

					// link filtering
					if( strtolower(substr($tax_data[$termid]['evcal_mdta_'.$postfix],0,4)) == 'http' ){
                        echo '<a href="' . stripslashes($tax_data[$termid]['evcal_mdta_'.$postfix]) . '" target=_blank>' . stripslashes($tax_data[$termid]['evcal_mdta_'.$postfix]) . "</a>";
					}else{
						echo stripslashes($tax_data[$termid]['evcal_mdta_'.$postfix]);
					}
                   

					echo "</p>";
				}
			}
		}	

	// Event Post meta box
		function evomdt_ajax(){
			if(empty($_POST['type'])) return;

			$type = $_POST['type'];
			$output = '';

			switch($type){
			case 'newform':
				$event_id = (int)$_POST['eventid'];
				echo json_encode(array(
					'content' =>$this->mdt_form($event_id, $_POST['tax']),
					'status'=>'good'
				)); exit;
			break;
			case 'editform':
				$event_id = (int)$_POST['eventid'];
				$term_id = (int)$_POST['termid'];
				echo json_encode(array(
					'content' =>$this->mdt_form($event_id, $_POST['tax'],$term_id),
					'status'=>'good'
				)); exit;
			break;
			case 'save':
				echo json_encode($this->save_mdt()); exit;
			break;
			case 'list':
				$eventid = (int)$_POST['eventid'];
				$tax = $_POST['tax'];
				if(empty($eventid) && empty($tax)){
					echo json_encode(array('status'=>'Missing required information')); exit;
				}
				echo json_encode(array(
					'content'=>$this->get_mdt_selectable_list($eventid, $tax),
					'status'=>'good'
				)); exit;
			break;
			case 'savelist':
				$event_id = (int)$_POST['eventid'];
				if(!empty($_POST['mdt'])){
					$mdts = array();
					foreach($_POST['mdt'] as $mdt){
						$mdts[] = (int)$mdt;
					}

					$result = wp_set_object_terms($event_id, $mdts, $_POST['tax'] , false);
				}else{
					$result = wp_set_object_terms($event_id, '', $_POST['tax'] , false);
				}
				echo json_encode(array(
					'result'=>$result,
					'content'=>$this->get_mdt_display_list($event_id, $_POST['tax']),
					'msg'=>__('Successfully Processed!','eventon'),
					'status'=>'good'
				)); exit;
			break;
			case 'removeterm':
				$eventid = (int)$_POST['eventid'];
				$tax = $_POST['tax'];
				$termid = (int)$_POST['termid'];
				if(empty($eventid) && empty($tax) && !empty($termid)){					
					echo json_encode(array('status'=>'Missing required information')); exit;
				}
				
				$result = wp_remove_object_terms($eventid, $termid, $tax);
				echo json_encode(array(
					'content'=>$this->get_mdt_display_list($eventid, $tax),
					'status'=>'good'
				)); exit;
			break;
			}
		}
		function event_metabox($array){
			$mdt_name = $this->evo_get_mdt_names();
			for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){	
				$icon = get_eventON_icon('evcal__evomdt_'.$x, 'fa-list',$this->opt );			
				$array[] = array(
					'id'=>'ev_mdt_'.$x,
					'name'=> $mdt_name[$x],
					'variation'=>'customfield',	
					'hiddenVal'=>'',	
					'iconURL'=>$icon,
					'iconPOS'=>'',
					'type'=>'code',
					'content'=>$this->content($mdt_name[$x], 'multi_data_type_'.$x, $x),
					'slug'=>'ev_mdt_1'
				);
			}

			return $array;			
		}
		function content($name, $tax, $x){
			global $post;

			$p_id = isset($_REQUEST['post'])? $_REQUEST['post']: $post->ID;
			$ev_vals = get_post_custom($p_id);

			global $ajde;

			ob_start();

			$text_select = sprintf( __('Select %s from list','eventon'), $name);
			$text_new = sprintf( __('Create a new %s','eventon'), $name);

			?>
			<div class='evcal_data_block_style1'>
				<div class='evcal_db_data'>
					<p>
						<input type="text" id="evcal_subheader_<?php echo $x;?>" name="_evomdt_subheader_<?php echo $x;?>" value="<?php echo !empty($ev_vals['_evomdt_subheader_'.$x])? $ev_vals['_evomdt_subheader_'.$x][0]:'';?>" style="width:100%"/>
						<label for="evcal_lmlink_target"><?php _e('Section subtitle text','eventon');?></label>	
					</p>
					<div class="evomdt_actions">
						<p>
							<a class='evo_btn evomdt_get_list ajde_popup_trig' data-popc='print_lightbox' data-lb_cl_nm='evo_mdt_lb' data-t="<?php echo $text_select;?>" data-eventid='<?php echo $p_id;?>' data-tax='<?php echo $tax;?>'><?php echo $text_select;?></a>
							<a class='evo_btn evomdt_add_new_btn ajde_popup_trig' data-popc='print_lightbox' data-lb_cl_nm='evo_mdt_lb' data-t="<?php echo $text_new;?>" data-tax='<?php echo $tax;?>' data-eventid='<?php echo $p_id;?>'><?php echo $text_new;?></a> 
						</p>
					</div>
					<ul class="evomdt_selection <?php echo $tax;?>_display_list" data-tax='<?php echo $tax;?>' data-eventid='<?php echo $p_id;?>'>
						<?php 
						echo $this->get_mdt_display_list($p_id, $tax);						
						?>						
					</ul>
				</div>
			</div>
			<?php 

			return ob_get_clean();
		}
		function save_event_post($fields, $post_id){
			for($x=1; $x <= $this->evo_get_mdt_count() ; $x++){	
				if(isset($_POST['_evomdt_subheader_'.$x])){
					update_post_meta($post_id, '_evomdt_subheader_'.$x, $_POST['_evomdt_subheader_'.$x]);	
				}else{
					delete_post_meta($post_id, '_evomdt_subheader_'.$x);
				}
			}
		}

		function save_mdt(){
			$tax = $_POST['tax'];
			$post_id = $_POST['eventid'];
			$term_name = esc_attr(stripslashes($_POST['name']));
			$term = term_exists( $term_name, $tax);

			// Term Exist
			if($term !== 0 && $term !== null){
				wp_set_object_terms( $post_id, $term_name, $tax, true);
				$termid = isset($_POST['termid'])?$_POST['termid']: $term->term_id;

				wp_update_term($termid, $tax, array(
					'name'=> $term_name,
					'description'=> (!empty($_POST['description'])? 
						stripslashes($_POST['description']):'')
				));		
			}else{
				// create slug from name
					$trans = array(" "=>'-', ","=>'');
					$term_slug= strtr($term_name, $trans);

				// create wp term
				$new_term_ = wp_insert_term( $term_name, $tax, array(
					'slug'=>$term_slug,
					'description'=> (!empty($_POST['description'])? $_POST['description']: '')
				) );

				// if term created correctly
				if(!is_wp_error($new_term_)){
					$termid = (int)$new_term_['term_id'];
					wp_set_object_terms( $post_id, array($termid), $tax, true);
				}
			}

			// if term good, save term meta values
			if($termid){
				$newtermmeta = array();
				foreach($this->fields_of_mdt($tax) as $field=>$var){
					if(in_array($field, array('name','description'))) continue;
					if(!empty($_POST[$field])){
						$newtermmeta[$field]= $_POST[$field];
					}else{
						$newtermmeta[$field]= '';
					}
				}
				evo_save_term_metas($tax,$termid, $newtermmeta);	

				// get new list
					$content = $this->get_mdt_display_list($post_id, $tax);
				
				return array(
					'content'=>$content,
					'status'=>'good',
					'msg'=>__('Successfully Processed!','eventon')
				);
			}else{
				return array(
					'status'=>__('Could not perform operation, try again later!','eventon')
				);
			}	
		}

		// List to select items from
			function mdt_list($eventid, $tax){
				$output = array();
				$terms = wp_get_post_terms($eventid, $tax);
				if ( $terms && ! is_wp_error( $terms ) ){
					//$termMeta = get_option( "evo_tax_meta");
					foreach($terms as $term){
						//$termmeta = evo_get_term_meta($tax,$term->term_id, $termMetas);
						$output[$term->term_id]['name'] = $term->name;
						$output[$term->term_id]['description'] = $term->description;
					}
				}
				return $output;
			}
			function get_mdt_term_data($tax){
				$output = array();
				$terms = get_terms($tax, array('hide_empty'=>false));
				if ( $terms && ! is_wp_error( $terms ) ){
					
					$fields = $this->fields_of_mdt($tax);
					
					foreach($terms as $term){

						$termmeta = evo_get_term_meta($tax,$term->term_id);

						$output[$term->term_id]['name'] = $term->name;
						$output[$term->term_id]['description'] = $term->description;

						// each additional data field
						foreach($fields as $field=>$val){
							if(in_array($field, array('name','description'))) continue;
							if(empty($termmeta[$field])) continue;
							$output[$term->term_id][$field] = $termmeta[$field];
						}
					}
				}
				return $output;
			}
			function get_mdt_display_list($eventid, $tax){
				$content = '';
				$list = $this->mdt_list($eventid, $tax);
				if( count($list)>0){
					foreach($list as $termid=>$item){
						$content .= "<li data-termid='{$termid}'>".$item['name']."<i class='fa fa-pencil ajde_popup_trig' data-popc='evomdt_new'></i><i class='fa fa-close'></i></li>";
					}
				}
				return $content;
			}
			// got lightbox get selectable list
			function get_mdt_selectable_list($eventid, $tax){
				$list = $this->get_mdt_term_data( $tax);
				$output = '';
				if(count($list)>0){
					
					$event_terms = $this->get_event_terms($eventid, $tax);

					$output .= "<div class='pad20'>";
					foreach($list as $termid=>$term){
						$checked = in_array($termid, $event_terms)?'checked="checked"':'';
						$output .= "<p><input type='checkbox' name='mdt' value='{$termid}' {$checked}>" . $term['name'] . "</p>";
					}
					$output .= "<p><span class='evo_btn evomdt_save_list_submit' data-eventid='{$eventid}' data-tax='{$tax}'>".__('Save','eventon')."</span></p>";
					$output .= "</div>";
					return $output;
				}else{
					return "<p class='tac pad20'>No items found, please add new!</p>";
				}
			}
			function get_event_terms($eventid, $tax){
				$event_terms = wp_get_post_terms($eventid, $tax);
				$event_term_ids = array();
				if ( $event_terms && ! is_wp_error( $event_terms ) ){
					foreach($event_terms as $term){
						$event_term_ids[] = $term->term_id;
					}
				}
				return $event_term_ids;
			}

		// Fields for a given MDT
			function fields_of_mdt($tax='multi_data_type_1'){

				$mdt_index = $this->tax_index($tax);

				$base = array(
					'name'=>array('Name','text'),
					'description'=> array('Description','textarea'),
				);

				// image field
					if( evo_settings_check_yn($this->opt , 'evcal_mdt_img'.$mdt_index) ){
						$base['image'] = array('Image','image');
					}

				// foreach additional fields
					for( $z=1; $z <= $this->evo_max_mdt_addfield_count(); $z++){
						$postfix = $mdt_index. '_' .$z;
						if( evo_settings_check_yn($this->opt , 'evcal_mdta_'.$postfix) &&
							!empty( $this->opt[ 'evcal_mdta_name_'.$postfix ])
						){
							$base['evcal_mdta_'.$postfix] = array(
								$this->opt[ 'evcal_mdta_name_'.$postfix],
								'text',
								'norequired'
							);
						}
					}
				return $base;
			}
		// add new, edit form
		function mdt_form($eventid, $tax, $termid = ''){
			
			ob_start();			
			?>
			<div class='ev_admin_form'>
				<div class='evo_tax_entry evoselectfield_saved_data  sections'>
				<input type="hidden" class='field' name='eventid' value='<?php echo $eventid;?>'/>
				<input type="hidden" class='field' name='termid' value='<?php echo $termid;?>'/>
				<input type="hidden" class='field' name='tax' value='<?php echo $tax;?>'/>
				<?php

					$termdata = '';
					if(!empty($termid)){
						$data = $this->get_mdt_term_data($tax);		
						$termdata = $data[$termid];				
					}

					// each data type field
					foreach($this->fields_of_mdt($tax) as $key=>$val){
						$label = $val[0];
						$saved_val = !empty($termdata[$key])? stripslashes($termdata[$key]): '';
						$req = !empty($val[2]) && $val[2]=='norequired'? '':'req';

						switch($val[1]){
						case 'text':
							?>
							<p><label for='<?php echo $key;?>'><?php echo $label;?></label><input type='text' class='field <?php echo $req;?>' name='<?php echo $key;?>' value="<?php echo $saved_val;?>" style='width:100%' /></p>
							<?php
						break;
						case 'textarea':
							?>
							<p><label for='<?php echo $key;?>'><?php echo $label;?></label><textarea class='field ' name="<?php echo $key;?>" rows="4" style='width:100%'><?php echo $saved_val;?></textarea>
							</p>
							<?php
						break;
						case 'image':

							$img_src = '';
							$btntxt_attr = __('Remove Image','eventon');
							$btntxt = __('Select Image','eventon');
							$btnclass = 'chooseimg';

							if(!empty($saved_val)){
								$img_attr = wp_get_attachment_image_src( $saved_val, 'full' );
								$img_src = $img_attr[0];
								$btntxt = __('Remove Image','eventon');
								$btntxt_attr = __('Select Image','eventon');
								$btnclass = 'removeimg';
							}
							?>
							<div class='evo_metafield_image' style='padding-top:10px'>				
								<p >
									<label><?php _e('Image','eventon');?></label>
									<input class='field evomdt_img custom_upload_image evo_meta_img' name="<?php echo $key;?>" type="hidden" value="<?php echo $saved_val;?>" /> 
			                		<span class="custom_upload_image_button evo_btn <?php echo $btnclass;?>" data-txt='<?php echo $btntxt_attr;?>'><?php echo $btntxt;?></span>
			                		<span class='evo_img_src image_src' style='clear:both;display:block'>
			                			<img class='evomdt_image' src='<?php echo (!empty($img_src)?$img_src:'');?>' style='display:<?php echo (!empty($img_src)?'block':'none');?>'/>
			                		</span>			                		
			                	</p>
			                </div>
							<?php
						break;
						}
					}
				?>				
                <p><span class="evo_btn evomdt_new_mdt_submit"><?php _e('Save','eventon');?></span></p>
				</div>
			</div><!-- endform-->
			<?php
			return ob_get_clean();
		}
		function save_mdt_item(){}

	// Supportive
		function tax_index($tax){
			$mdt_index = explode('_', $tax);
			return $mdt_index[3];
		}
		function evo_max_mdt_count(){
			return apply_filters('evo_multi_data_type_count',3);
		}
		// this return the count for each multi data type that are activated in accordance
		function evo_get_mdt_count($evopt=''){
			$evopt = (!empty($evopt))? $evopt: $this->opt;

			$maxnum = $this->evo_max_mdt_count();
			$count=0;
			for($x=1; $x<= $maxnum; $x++ ){
				if(!empty($evopt['evcal_mdt_'.$x]) && $evopt['evcal_mdt_'.$x]=='yes'){
					$count = $x;
				}else{	break;	}
			}
			return $count;
		}
		function evo_get_mdt_names($options=''){
			$output = array();

			$options = (!empty($options))? $options: $this->opt;
			for( $x=1; $x <= $this->evo_max_mdt_count($options); $x++){

				$pretext = (!empty($options['evcal_mdt_name_'.$x ]))? 
					$options['evcal_mdt_name_'.$x ]:'Multi Data Type '.$x;

				$output[$x] = evo_lang_get('multi-data-type-'.$x, $pretext);
			}
			return $output;
		}
		function evo_max_mdt_addfield_count(){
			return apply_filters('evo_multi_data_type_fields_count',2);
		}
}