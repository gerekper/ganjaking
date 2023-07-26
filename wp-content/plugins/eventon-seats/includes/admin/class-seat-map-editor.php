<?php
/**
 * Seat Map Editor
 * @version 1.2.1
 */

class EVOST_Seat_Map_Editor{
	private $SEATS;
	private $event_id;
	private $wcid;
	public function __construct(){
		// AJAX
		$ajax_events = array(
			'evost_editor_content'=>'editor_content',
			'evost_editor_forms'=>'editor_forms',
			'evost_save_editor_forms'=>'save_editor_forms',
			'evost_delete_item'=>'delete_item',
			'evost_editor_save_changes'=>'editor_save_changes',
			'evost_duplicate_section'=>'duplicate_section',
			'evost_get_upload_form'=>'get_upload_form',
			'evost_save_uploaded_map'=>'save_uploaded_map_data',
			'evost_clear_map'=>'clear_map_data',
			'evost_make_all_av'=>'make_all_seats_available',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}

		$this->help = new evo_helper();
	}

// AJAX RETURNS
	function editor_content(){
		$event_id = $_POST['event_id'];
		$wcid = $_POST['wcid'];
		$SEATS = new EVOST_Seats_Json($event_id, $wcid);

		// get all attendees for this event
		$EA = new EVOTX_Attendees();
		$json = $EA->get_tickets_for_event($event_id);

		echo json_encode(array(
			'j'=> $SEATS->__j_get_all_sections(),
			'content'=> $this->_html_get_main_editor( $event_id, $wcid ),
			'template'=> EVO()->temp->get('evost_seat_map'),
			'attendees'=> array('tickets'=>$json, 'od_gc'=>$EA->_user_can_check() ),
			'temp_attendees'=> EVO()->temp->get('evotx_view_attendees'),
			'status'=>'good',
			'd'=> $SEATS->seats_data
		)); exit;
	}
	

	// save uploaded seat map data
		function get_upload_form(){
			ob_start();
			EVO()->elements->print_import_box_html(array(
				'box_id'=>'evo_seatdata_upload',
				'title'=>__('Upload JSON Seat Map Data Form'),
				'message'=>__('NOTE: You can only upload seatmap data as .json file. If you are uploading a big seat map, max_input_vars value in php ini will need to be increased to allow upload of big file.'),
				'file_type'=>'.json',
				'type'=> 'lightbox',
			));
			echo json_encode(array(
				'content'=> ob_get_clean(), 
				'status'=>'good', 
			));exit;
		}
		public function save_uploaded_map_data(){
			$postdata = $this->help->sanitize_array($_POST);

			$data_back = $postdata['data_back'];
			$data = $postdata['data'];

			$SEATS = new EVOST_Seats_Json($data_back['event_id'], $data_back['wcid']);

			// process seat map data
			$seat_map_data = $SEATS->process_seatmap_data_for_save( $data );

			$SEATS->save_seat_map_data( $seat_map_data );

			echo json_encode(array(
				'j'=> $seat_map_data, 
				'j2'=> $data, 
				'status'=>'good', 
				'msg'=> __('New seat map data processed'), 
			));exit;
		}

	// clear seat map data
		function clear_map_data(){
			$postdata = $this->help->sanitize_array($_POST);

			$data = $postdata['data'];
			$SEATS = new EVOST_Seats_Json($data['event_id'], $data['wcid']);

			$SEATS->seat_data = false;
			$SEATS->save_seat_map_data( array() );

			echo json_encode(array(
				'status'=>'good', 'msg'=> __('Seat map data cleared out','evost'), 
			));exit;

		}

	//makeall seats available for reserve
		public function make_all_seats_available(){
			$postdata = $this->help->sanitize_array($_POST);
			$data = $postdata['data'];
			$SEATS = new EVOST_Seats_Json($data['event_id'], $data['wcid']);

			$SEATS->update_all_seats( 'status','av');

			echo json_encode(array(
				'new_map_data'=> $SEATS->__j_get_all_sections(),
				'status'=>'good', 'msg'=> __('All seats are made available for reserve.'), 
			));exit;
		}

	// get lightbox forms
	function editor_forms(){

		$post_data = $this->help->recursive_sanitize_array_fields( $_POST);

		$method = $post_data['method'];
		$data = $post_data['data'];
		$type = $data['item_type'];
		$event_id = $this->event_id = $data['event_id'];
		$wcid = $this->wcid = $data['wcid'];

		// data for edit form method
			if($method == 'edit'){
				$SEATS = new EVOST_Seats($event_id);
				switch($data['item_type']){
					case 'section':
						$SEATS->set_section( $data['section_id'] );
						$data['rows'] = $SEATS->get_rows();
						$data['seats'] = $SEATS->get_max_seats();
						$data['section_name'] = $SEATS->get_item_prop('section_name');
						foreach( array(
							'def_price',
							'type',
							'bgc','fc','bgcA', 'brd',
							'align',
							'seat_shape',
							'section_index',
							'capacity',
							'sold',
							'inprogress',
							'icon',
							'shape',
							'desc',
						) as $field){
							$data[$field] = $SEATS->get_item_prop($field);
						}
					break;
					case 'row':
						$SEATS->set_row( $data['row_id'], $data['section_id'] );
						$data['seats'] = $SEATS->get_row_seats();
						$data['row_index'] = $SEATS->get_item_prop('row_index');
						$data['row_price'] = $SEATS->get_item_prop('row_price');
					break;
					case 'seat':
						$SEATS->set_seat( $data['seat_id'], $data['row_id'], $data['section_id'] );
						$data['id'] = $SEATS->get_item_prop('id');
						$data['number'] = $SEATS->get_item_prop('number');
						$data['price'] = $SEATS->get_item_prop('price');
						$data['status'] = $SEATS->get_item_prop('status');
						$data['handicap'] = $SEATS->get_item_prop('handicap');
					break;
					case 'settings':
						$data_s = $SEATS->get_seat_settings();
						$data = $data_s? array_merge($data, $data_s): $data;
					break;
				}
			}

			//print_r($SEATS->item_data);

		echo json_encode(array(
			'content'=> $this->get_form( $event_id, $data['item_type'], $method, $data ),
			'status'=>'good'
		)); exit;
	}

	// duplicate
		function duplicate_section(){
			$post_data = $this->help->recursive_sanitize_array_fields( $_POST);

			$section_id = $post_data['data']['section_id'];

			$SEATS = new EVOST_Seats_Json($post_data['data']['event_id'], $post_data['data']['wcid']);
			$SEATS->set_section( $section_id );

			$item_data = $SEATS->item_data;

			$duplicate_section_id = $SEATS->get_new_item_index();
			$SEATS->section = $duplicate_section_id;

			// update location
			$item_data['top'] = (int)$item_data['top']+100;
			$item_data['left'] = (int)$item_data['left']+100;

			// update name and id
			$item_data['section_name'] = $item_data['section_name'].' (copy)';
			$item_data['section_id'] = $duplicate_section_id;

			// update section index
			if(isset($item_data['section_index'])){
				$item_data['section_index'] = $item_data['section_index'].'A';
			}

			// make all the seats available
			if(isset($item_data['rows'])){
				foreach($item_data['rows'] as $row_id=>$RD ){
					$s = 1;
					foreach($RD as $seat_id=>$SD){
						if(in_array($seat_id, array('row_id','row_index', 'row_price'))) continue;

						// make all tuav and uva seats available in duplicated section
						if($item_data['rows'][$row_id][$seat_id]['status'] == 'tuav' 
							|| $item_data['rows'][$row_id][$seat_id]['status'] == 'uav'){
							$item_data['rows'][$row_id][$seat_id]['status'] = 'av';
						}

						// new seat number
						$item_data['rows'][$row_id][$seat_id]['number'] = $item_data['section_index'].$RD['row_index'].$s;
						$s++;				
					}
				}
			}

			// for una seating - unassigned seating
			if( $item_data['type'] == 'una'){
				$item_data['sold'] = 0;
			}
			
			// plug
			$item_data = apply_filters('evost_duplicate_section', $item_data, $SEATS);

			$SEATS->save_item_data($item_data);

			do_action('evost_duplicate_section_after_save', $item_data, $section_id, $duplicate_section_id, $SEATS);

			echo json_encode(array(
				'status'=>'good',
				'msg'=>'Successfully duplicated!',
				'j'=> $SEATS->__j_get_all_sections(),
			));exit;
		}

// Save
	// save section, seat, settings edit form
	function save_editor_forms(){
		$postdata = $this->help->sanitize_array($_POST);
		$formdata = $postdata['formdata'];
		
		// saving settings
			if( $formdata['item_type'] == 'settings'){
				$SEATS = new EVOST_Seats($formdata['event_id']);

				$new_settings_vals = array();
				foreach($formdata as $key=>$val){
					if( in_array($key,	array('event_id','item_type'))) continue;
					$new_settings_vals[$key] = $val;
				}

				$SEATS->set_settings( $new_settings_vals);
				$new_settings = $SEATS->event->get_prop('_evost_settings');

				echo json_encode(array(
					'status'=>'good',
					'msg'=>'Settings successfully saved!',
					'settings_data'=> $new_settings
				));exit;
			}

		// saving seat map data
			if( $formdata['item_type'] != 'settings'){
				$SEATS = new EVOST_Seats($formdata['event_id'], isset($formdata['wcid'])? $formdata['wcid']:'');

				$this->SEATS = $this->set_up_seats_obj($formdata);

				do_action('evost_save_editor_form_beforesave', $SEATS, $formdata);

				switch($formdata['item_type']){
					case 'section':
						// adding new section
						if($formdata['method'] == 'new'){
							$this->save_item($formdata);
						}else{
							$this->SEATS->set_section( $formdata['section_id'] );
							$this->save_item($formdata);
						}				
					break;
					case 'row':
						$this->SEATS->set_row( $formdata['row_id'], $formdata['section_id'] );
						$this->save_item($formdata);
					break;
					case 'seat':
						$this->SEATS->set_seat( $formdata['seat_id'], $formdata['row_id'], $formdata['section_id'] );
						$this->save_item($formdata);
					break;
				}

				// update woocommerce product stock
				$SEATS->update_wc_block_stock();

				do_action('evost_save_editor_form_aftersave', $SEATS, $formdata);

				echo json_encode(array(
					'db'=>$formdata,
					'status'=>'good',
					'msg'=>'Successfully Updated!',
					'j'=> $this->SEATS->__j_get_all_sections(),
				));exit;
			}
	}

	// general save changes -- after full seat map is saved
		function editor_save_changes(){
			if(!isset($_POST['s'])){ echo json_encode(array(
					'status'=>'bad',
					'msg'=>'Missing Data!',
				));exit;
			}


			$s = $_POST['s'];
			//print_r($s);

			$wcid = isset($_POST['data']['wcid'])? (int)$_POST['data']['wcid']: '';
			$event_id = isset($_POST['data']['event_id'])? (int)$_POST['data']['event_id']: '';

			$SEATS = new EVOST_Seats($event_id, $wcid);

			foreach($s as $section_id=>$section){
				$SEATS->set_section( $section_id );
				$item_data = $SEATS->item_data;

				$def = $section['type'] =='def'? true: false;

				foreach($section as $key=>$val){
					// not save width height for default section
					if($def && in_array($key, array('w','h'))) continue;
					$item_data[$key] = $val;
				}

				//print_r($item_data);
				$SEATS->save_item_data($item_data);
			}

			// update the total seats to wc product
			$SEATS->update_wc_block_stock();

			do_action('evost_save_map_editor_aftersave', $SEATS);

			echo json_encode(array(
				'status'=>'good',
				'msg'=>'Successfully Saved!',
			));exit;

		}

	function delete_item(){
		$formdata = $_POST['formdata'];

		$SEATS = $this->set_up_seats_obj($formdata);

		do_action('evost_delete_item', $SEATS, $formdata);

		$SEATS->delete_item();

		echo json_encode(array(
			'status'=>'good',
			'msg'=>'Successfully Deleted!',
			'j'=> $SEATS->__j_get_all_sections(),
		)); exit;
	}

	function set_up_seats_obj($formdata){
		$SEATS = new EVOST_Seats_Json($formdata['event_id'], $formdata['wcid']);
		$SEATS->item_type = $formdata['item_type'];
		if(isset($formdata['section_id'])) $SEATS->section = $formdata['section_id'];
		if(empty($formdata['section_id'])) $SEATS->section = '0';
		if(isset($formdata['row_id'])) $SEATS->row = $formdata['row_id'];
		if(isset($formdata['seat_id'])) $SEATS->seat = $formdata['seat_id'];

		return $SEATS;
	}

// VIEWS
	function _html_get_main_editor($event_id, $wcid){

		$EVO_Seats = new EVOST_Seats($event_id, $wcid);

		$seat_settings = $EVO_Seats->get_seat_settings();

		ob_start();

		$j = json_encode(array(
			'event_id'=>$event_id,
			'wcid'=>$wcid
		));

		do_action('evost_mapeditor_before', $EVO_Seats);

		$settings_d = array('d'=> 
			array('type'=>'settings','method'=>'edit','t'=>__('General Seat Map Settings','evost')) 
		);

		?>
		<div class="evosteditor_header" data-j='<?php echo $j;?>' >
			
			<a class='evost_new_section' tip='<?php _e('Add New','evost');?>' data-t="<?php _e('Add New Section','evost');?>"><b><i class="fa fa-plus"></i></b></a>
			<div class='secondary' style='display:none'>
				<span class="stages primary_stage"><i><?php _e('Section','evost');?></i><b></b></span>
				<span class="stages secondary_stage" style='display:none'><i><?php _e('Row','evost');?></i><b>C</b></span>
				
				<a class='evost_edit_section select evost_focus_item' tip='<?php _e('Select Section','evost');?>'><b></b></a>
				<a class='evost_edit_row evost_focus_item' tip='<?php _e('Select Rows','evost');?>'><i class="fa fa-ellipsis-h"></i></a>
				<a class='evost_edit_seat evost_focus_item' tip='<?php _e('Select Seats','evost');?>'><i class="fa fa-square"></i></a>

				<div class='evost_section_only_actions'>
					<a class='evost_edit_selected_section evost_section_only' tip='<?php _e('Edit');?>' data-t="<?php _e('Edit Section','evost');?>"><b><i class="fa fa-pencil"></i></b></a>
					<a class='evost_rotate_l evost_section_only' tip='<?php _e('Rotate Counter Clockwise','evost');?>'><i class="fa fa-undo"></i></a>
					<a class='evost_rotate_r evost_section_only' tip='<?php _e('Rotate Clockwise','evost');?>'><i class="fa fa-rotate-right"></i></a>
					<a class='evost_dup evost_section_only' tip='<?php _e('Duplicate','evost');?>'><i class="fa fa-window-restore"></i></a>
					<a class='evost_attendees evost_section_only' tip='<?php _e('View Attendees','evost');?>'><i class="fa fa-user"></i></a>
					<a class='evost_delete_section evost_section_only' tip='<?php _e('Delete Section','evost');?>'><i class="fa fa-trash"></i></a>
				</div>
			</div>
			<a class='evost_settings_btn evost_trigger_lb2' tip='<?php _e('Seating Settings','evost');?>' data-j='<?php echo json_encode($seat_settings);?>' <?php echo $this->help->array_to_html_data($settings_d);?>><i class="fa fa-cog"></i></a>
		</div>
		<div class='evosteditor_sub_header'>
			<span><i><?php _e('Stats','evost');?></i></span>
			<span class='seat_count'><em><?php _e('Total Seats','evost');?></em> <b>0</b></span>
			<span class='seat_sold'><em><?php _e('Seats Sold','evost');?></em> <b>0</b></span>
			<span class='seat_inprogress'><em><?php _e('Seats In Progress','evost');?></em> <b>0</b></span>
			<span class='section_id hidden'><em><?php _e('Section ID','evost');?></em> <b>0</b></span>		
		</div>
		<p class="evost_msg" style='display:none'></p>
		<?php

			// image
			$img = !empty($seat_settings['seat_bg_img_id'])? wp_get_attachment_image_src($seat_settings['seat_bg_img_id'], 'full'): false;

			// background color
				$bgc = $EVO_Seats->get_seatmap_settings_prop('bg_color');
		?>
		<div class="evosteditor_content" style="<?php echo $img? 'background-image:url('.$img[0].')':'';?> <?php echo "background-color:#{$bgc}";?>">
			<style type="text/css" class='evost_seat_map_styles'></style>
			<div class="evost_in evost_sections_container" ></div>
			<div id='evost_map_j' data-j=''></div>
			
		</div>
		<div class="evosteditor_footer">
			<a class='evo_admin_btn btn_prime evost_save_seating_changes' data-product_id='<?php echo $wcid;?>' data-eid='<?php echo $event_id;?>'><?php _e('Save Changes','eventon');?></a>
			<span class='evosteditor_btn_msg' style='padding-left: 10px;'><?php _e('Need Saved','evost');?>!</span>
			<div class='evost_gen_data' style='display:none' <?php echo $this->help->array_to_html_data(array(

			));?>></div>
		</div>
		<div id='evost_tip' style='display:none'>---</div>
	
		<?php

		return ob_get_clean();
	}

// FORM
	function get_form($event_id, $item_type, $method='add', $data=array()){

		$EVO_Seats = $this->SEATS = new EVOST_Seats($this->event_id, $this->wcid);
		$EVO_Seats->load_seatmap_settings();
		
		if(!empty($data)) extract($data);
		//$EVO_Seats->event->del_prop('_evost_sections');
		
		ob_start();

		?>
		<div class='evost_editor_form' style='padding:20px'>
			<form class='evost_editor_form'>
		<?php

		$fields = array();
		
		switch($item_type){
			case 'section':

				$section_id = (!empty($data['section_id']))? $data['section_id']:'0';
				$fields = array(					
					'ri'=>array('type'=>'hidden','val'=> 'ri0'),
					'event_id'=>array('type'=>'hidden','val'=> $event_id),
					'wcid'=>array('type'=>'hidden','val'=> $wcid),
					'section_id'=>array('type'=>'hidden','val'=> $section_id),
					'item_type'=>array('type'=>'hidden','val'=> $item_type),
					'method'=>array('type'=>'hidden','val'=> $method),
					'top'=>array('type'=>'hidden','val'=> ( !empty($top) ?$top:'')),
					'left'=>array('type'=>'hidden','val'=> ( !empty($left) ?$left:'')),
					'ang'=>array('type'=>'hidden','val'=> ( !empty($ang) ?$ang:'')),
					
					'type'=>array(
						'type'	=>'select',
						'label'=>__('Section Type','evost'),
						'req'=> true,
						'options'=>array(
							'def'=>__('Assigned Seating','evost'),
							'una'=>__('Unassigned Seating','evost'),
							'boo'=>__('Single Space Booth (BETA)','evost'),
							'aoi'=>__('Areas of Interest','evost'),
						),
						'val'=>	(!empty($data['type']))? $data['type']:''
					),
					'subheader'=>array(
						'type'=>'subheader',
						'text'=> ($section_id ? __('Section ID','evost') : null) , 
						'data'=>$section_id
					),
					'if0'=>array('type'=>'if','name'=>'type','val'=>array('def','boo')	),
						'section_index'=>array(
							'type'	=>'text',
							'label'=>__('Section Index','evost'),
							'req'=> false,
							'desc'=> __('Readable number or letter unique to this section','evost'),
							'val'=>	(!empty($data['section_index']))? $data['section_index']:''
						),
					'endif0'=>array(	'type'=>'endif'	),
					'section_name'=>array(
						'type'	=>'text',
						'label'=>__('Section Name','evost'),
						'req'=> true,
						'val'=>	(!empty($data['section_name']))? $data['section_name']:''
					),					

					/*
					'if_a'=>array(	'type'=>'if','name'=>'type','val'=>array('boo')	),
						'booth_size'=>array(
							'type'	=>'select',
							'label'=>__('Set booth design size','evost'),
							'req'=> true,
							'options'=>array(
								'def'=>__('20 x 20','evost'),
							),
							'val'=>	(!empty($data['booth_size']))? $data['booth_size']:'0'
						),						
					'endif_a'=>array(	'type'=>'endif'	),
					*/

					'if1'=>array(	'type'=>'if','name'=>'type','val'=>array('una')	),
						'sec2'=>array('type'=>'startsection','name'=>'s1'),
							'capacity'=>array(
								'type'	=>'number',
								'label'=>__('Total capacity for unassigned seating','evost'),
								'req'=> true,
								'val'=>	(!empty($data['capacity']))? $data['capacity']:'0'
							),
							'sec2e'=>array(	'type'=>'endsection'	),
						'sold'=>array(
							'type'	=>'readable',
							'label'=>__('Seats sold so far','evost'),
							'req'=> false,
							'val'=>	(!empty($data['sold']))? $data['sold']: '0'
						),
					'endif1'=>array(	'type'=>'endif'	),

					'if2'=>array(	'type'=>'if','name'=>'type','val'=>array('aoi')	),
						'icon'=>array(
							'type'	=>'icon',
							'label'=>__('Icon','evost'),
							'req'=> false,
							'val'=>	(!empty($data['icon']))? $data['icon']:''
						),
						'shape'=>array(
							'type'	=>'select',
							'label'=>__('Section area shape style','evost'),
							'req'=> true,
							'options'=>array(
								'def'=>__('5px rounded edge rectangle (Default)','evost'),
								'50per'=>__('50% rounded edge circular shape','evost'),
								'none'=>__('No rounded edge rectangle','evost')
							),
							'val'=>	(!empty($data['shape']))? $data['shape']:''
						),
					'endif2'=>array(	'type'=>'endif'	),

					'if3'=>array('type'=>'if','name'=>'type','val'=>array('def')	),
						'sec1'=>array('type'=>'startsection','name'=>'s1'),
							'align'=>array(
								'type'	=>'select',
								'label'=>__('Seat Alignment','evost'),
								'req'=> true,
								'options'=>array(
									'def'=>__('Center','evost'),
									'l'=>__('Left Align','evost'),
									'r'=>__('Right Align','evost')
								),
								'val'=>	(!empty($data['align']))? $data['align']:''
							),
							'seat_shape'=>array(
								'type'	=>'select',
								'label'=>__('Seat Shape Style','evost'),
								'req'=> true,
								'options'=>array(
									'def'=>__('Border radius 5px box','evost'),
									'circ'=>__('Circle','evost'),									
									'box'=> __('Box','evost')
								),
								'val'=>	(!empty($data['seat_shape']))? $data['seat_shape']:''
							),						
						
							'rows'=>array(
								'type'	=>'number',
								'label'=>__('Rows','evost'),
								'req'=> true,
								'val'=>	(!empty($data['rows']))? $data['rows']:''
							),'seats'=>array(
								'type'	=>'number',
								'label'=>__('Seats','evost'),
								'req'=> true,
								'val'=>	(!empty($data['seats']))? $data['seats']:''
							),
						'sec1e'=>array(	'type'=>'endsection'	),
						
						'note'=>array(
							'type'	=>'note',
							'note'=> ($method == 'edit'? __('NOTE: In order to increase seats for existing rows, please edit each row.','evost'):''),
						),

					'endif3'=>array(	'type'=>'endif'	),

					'if4'=>array('type'=>'if','name'=>'type','val'=> array('def','una','boo')	),
						'def_price'=>array(
							'type'	=>'text',
							'label'=>__('Default Seat Price','evost'),
							'req'=> true,
							'val'=>	(!empty($data['def_price']))? $data['def_price']:''
						),
					'endif4'=>array(	'type'=>'endif'	),
					'bgc'=>array(
						'type'	=>'color_select',
						'label'=>__('Background Color','evost'),
						'req'=> false, 'val'=>	((!empty($data['bgc']))? $data['bgc']:'f7f7f7')
					),
					'fc'=>array(
						'type'	=>'color_select',
						'label'=>__('Text Caption Color','evost'),						
						'req'=> false, 'val'=>	((!empty($data['fc']))? $data['fc']:'868686')
					),'bgcA'=>array(
						'type'	=>'yesno',
						'label'=>__('Transparent section background','evost'),
						'val'=>	((!empty($data['bgcA']))? $data['bgcA']:'no')
					),
					'brd'=>array(
						'type'	=>'yesno',
						'label'=>__('No section border','evost'),
						'val'=>	((!empty($data['brd']))? $data['brd']:'no')
					),
					'desc'=>array(
						'type'	=>'textarea',
						'label'=>__('Details','evost'),
						'val'=>	((!empty($data['desc']))? $data['desc']:'')
					),
					'section_plug'=>array(
						'type'	=>'plugabble',
						'form_data'=>	$data
					),
					'submit'=>array(
						'type'=>'submit_button','text'=>__('Save changes','evost'),
						'show_delete'=> true, 'del_text'=> __('Delete Section','evost')
					),
				);

			break;
			case 'row':
				$fields = array(
					'subheader'=>array('type'=>'subheader','text'=>__('Edit Row','evost')),
					'event_id'=>array('type'=>'hidden','val'=> $event_id),
					'wcid'=>array('type'=>'hidden','val'=> $wcid),
					'item_type'=>array('type'=>'hidden','val'=> $item_type),
					'section_id'=>array('type'=>'hidden','val'=> (!empty($data['section_id']))? $data['section_id']:'' ),					
					'row_id'=>array('type'=>'hidden','val'=> (!empty($data['row_id']))? $data['row_id']:'' ),					
					'row_index'=>array(
						'type'	=>'text',
						'label'=>__('Row Index Letter','evost'),
						'req'=> true,
						'val'=>	((!empty($data['row_index']))? $data['row_index']:''),
						'desc'=> __('Changing this index will effect each seat number on this row' ,'evost')
					),'seats'=>array(
						'type'	=>'number',
						'label'=>__('Number of Seats','evost'),
						'req'=> true,
						'val'=>	(!empty($data['seats']))? $data['seats']:''
					),'row_price'=>array(
						'type'	=>'text',
						'label'=>__('Default Seat Price','evost'),
						'req'=> true,
						'val'=>	(!empty($data['row_price']))? $data['row_price']:'',
						'desc'=> ($method == 'edit'? __('Changing the price here will effect price of all seats in this row','evost') :'')
					),
					'submit_button'=>array(
						'type'=>'submit_button','text'=>__('Save changes','evost'),
						'show_delete'=> true, 'del_text'=> __('Delete Row','evost')
					),
				);
			break;
			case 'seat':
				$SYM = get_woocommerce_currency_symbol();

				$fields = array(
					'subheader'=>array('type'=>'subheader','text'=>__('Edit Seat','evost'), 'data'=>$seat_slug),
					'attendee'=>array('type'=>'attendee','data'=> (!empty($attendee)?$attendee:'') ),
					'event_id'=>array('type'=>'hidden','val'=> $event_id),
					'wcid'=>array('type'=>'hidden','val'=> $wcid),
					'item_type'=>array('type'=>'hidden','val'=> $item_type),
					'section_id'=>array('type'=>'hidden','val'=> (!empty($data['section_id']))? $data['section_id']:'' ),
					'row_id'=>array('type'=>'hidden','val'=> (!empty($data['row_id']))? $data['row_id']:'' ),
					'seat_id'=>array('type'=>'hidden','val'=> (!empty($data['seat_id']))? $data['seat_id']:'' ),	
					'id'=>array('type'=>'hidden','val'=>	(!empty($data['id']))? $data['id']:''),
					'number'=>array(
						'type'	=>'text',
						'label'=>__('Unique Seat Number','evost'),
						'req'=> true,
						'val'=>	(!empty($data['number']))? $data['number']:''
					),
					'price'=>array(
						'type'	=>'text',
						'label'=>__('Seat Price','evost'),
						'req'=> true,
						'val'=>	(!empty($data['price']))? $data['price']:''
					),'status'=>array(
						'type'	=>'select',
						'label'=>__('Seat Status','evost'),
						'req'=> true,
						'options'=>array(
							'av'=>	__('Available','evost'),
							'uav'=>	__('Unavailable','evost'),
							'res'=>	__('Reserved','evost'),
							'tuav'=>	__('Temporarily Unavailable','evost')
						),
						'val'=>	(!empty($data['status']))? $data['status']:''
					),'handicap'=>array(
						'type'	=>'select',
						'label'=>__('Seat Accessibility','evost'),
						'req'=> true,
						'options'=>array(
							'no'=>__('None','evost'),
							'yes'=>__('Handicap Accessible','evost'),
						),
						'val'=>	(!empty($data['handicap']))? $data['handicap']:''
					),
					'submit'=>array('type'=>'submit_button','text'=>__('Save changes','evost')),
				);
			break;
			case 'settings':
				
				$fields = array(
					'event_id'=>array('type'=>'hidden','val'=> $event_id),
					'item_type'=>array('type'=>'hidden','val'=> $item_type),
					'_evost_seat_bg_img_id'=>array(
						'type'	=>'image',
						'label'=>__('Select background image','evost'),
						'req'=> false, 'val'=>	(!empty($data['_evost_seat_bg_img_id']))? $data['_evost_seat_bg_img_id']:'',
						'note'=> __('NOTE: You MUST upload an image with matching resolution to the seat map area selected below','evost')
					),
					'bg_color'=>array(
						'type'	=>'color_select',
						'label'=>__('Background Color','evost'),
						'req'=> false, 'val'=>	$EVO_Seats->get_seatmap_settings_prop('bg_color'),	
					),
					'seat_color'=>array(
						'type'	=>'color_select',
						'label'=>__('Seat Color','evost'),
						'req'=> false, 'val'=>	$EVO_Seats->get_seatmap_settings_prop('seat_color'),	
					),					
					'map_area'=>array(
						'type'	=>'select',
						'label'=>__('Seat Map Area Size (px)','evost'),
						'req'=> true,
						'options'=> apply_filters('evost_settings_map_area',array(
							'650-350'=>'650 x 350',
							'650-600'=>'650 x 600',
							'800-600'=>'800 x 600',
							'900-700'=>'900 x 700',
							'1000-800'=>'1000 x 800',
						)),
						'val'=>	$EVO_Seats->get_seatmap_settings_prop('map_area')
					),
					'seat_size'=>array(
						'type'	=>'select',
						'label'=>__('Seat Size (px)','evost'),
						'options'=>array(
							'15-15'=>'15 x 15',
								'12-12'=>'12 x 12',
								'10-10'=>'10 x 10',
						),
						'req'=> true,
						'val'=>$EVO_Seats->get_seatmap_settings_prop('seat_size')
					),
					
					'tooltip'=>array(
						'type'	=>'yesno',
						'label'=>__('Show static seat details under map (on seat hover)','evost'),
						'guide'=>__('This will show seat details in a static fixed box under seat map, instead of the moving dynamic tooltip.','evost'),
						'val'=>	$EVO_Seats->get_seatmap_settings_prop('tooltip'),	
					),
					'lightbox_map'=>array(
						'type'	=>'yesno',
						'label'=>__('Show seat map as a in-page lightbox window','evost'),
						'guide'=>__('This will load the seat map on a lightbox instead of within the eventcard.','evost'),
						'val'=>	$EVO_Seats->get_seatmap_settings_prop('lightbox_map'),	
					),
					'custom_code_1'=>array(
						'type'	=>'custom_code_1',
						'event_id'=>$event_id,
						'label'=>__('Download and Upload Seat Map','evost'),
						'req'=> false, 'val'=>	'',	
					),
					'submit'=>array('type'=>'submit_button','text'=>__('Save changes','evost')),
				);
			break;
		}

		echo $this->__process_form_fields($fields);

		?>
		</form>
		</div>
		<?php
		return ob_get_clean();
	}

	// Process form fields
		function __process_form_fields($fields){
			ob_start();

			global $ajde;

			foreach($fields as $key=>$data){
				extract($data);
				if(empty($type)) continue;
				$req = !empty($req)? $req:false;
				$value = !empty($val)? $val:false;

				$desc = isset($data['desc'])? "<span class='desc'>". $desc ."</span>":'';

				switch($type){
					case 'custom_code_1':
						?>
						<div class='row evost_download_upload_row evo_data_upload_holder' style='margin-bottom: 10px;position: relative;'>
							<p>
								<label><?php _e('Seat map data download/upload settings','evost');?></label>
								<a class='evo_admin_btn btn_grey evost_download_data' data-eid='<?php echo $data['event_id'];?>'><?php _e('Download Seat Map Data');?></a>	
								<?php
								EVO()->elements->print_trigger_element(array(
									'class_attr'=>'evo_admin_btn evolb_trigger btn_grey',
									'title'=>__('Upload JSON Seat Map Data'),
									'dom_element'=> 'span',
									'lb_class' =>'evost_upload_data',
									'lb_title'=> __('Upload JSON Seat Map Data'),	
									'ajax_data'=>array(
										'action'=>'evost_get_upload_form',
										'eid'=>$data['event_id']
									),
								),'trig_lb');

								?>
							</p>

							<p>
								<label><?php _e('Seat map data alterations','evost');?></label>
								<a class='evo_admin_btn btn_grey evo_data_clear_trigger' data-eid='<?php echo $data['event_id'];?>'><?php _e('Clear All Seat Map Data');?></a>
								<a class='evo_admin_btn btn_grey evo_data_av_trigger' data-eid='<?php echo $data['event_id'];?>'><?php _e('Make all seats available');?></a>
							</p>
						</div>
						<input name='evost_global_price' type="hidden" value=''>
						<?php
					break;
					case 'if':
						?><div class='evost_form_if_start' name='<?php echo $name;?>' data-val='<?php echo json_encode($val);?>'><?php
					break;
					case 'endif':	?></div><?php break;
					case 'startsection': ?><div class='evost_form_sec_start <?php echo $name;?>'><?php break;
					case 'endsection':	?></div><?php break;
					case 'attendee':
						if(!empty($data)):
						?>
						<div style='background-color: #f3f3f3; margin: 10px 0 40px;padding: 20px;'>
							<p><b><?php _e('Attendee for the seat','evost');?></b></p>	
							<p><?php _e('Seat Check-in Status','evost');?>: <?php echo $data['s'];?></p>		
							<p><?php _e('Name','evost');?>: <?php echo $data['n'];?> (<?php echo $data['e'];?>)</p>		
							<p><?php _e('Order ID','evost');?>: <a href='<?php echo get_edit_post_link($data['o']);?>' class='evo_admin_btn' target='_blank'><?php echo $data['o'];?></a> on <?php echo $data['d'];?></p>		
							<p><?php _e('Order Status','evost');?>: <?php echo $data['oS'];?></p>		
							<p><?php _e('Payment Method','evost');?>: <?php echo $data['payment_method'];?></p>		
						</div>
						<?php //print_r($data); ?>		
						<?php
						endif;
					break;
					case 'subheader':
						if( empty($text)) break;
						?>
						<h3 style='font-size: 18px;' class='evopadb20'><?php echo $text.( !empty($data) && !is_array($data)? ' <em style="opacity:0.3;font-style:normal" title="">'.$data.'</em>':'');?></h3>					
						<?php
					break;
					case 'hidden':
						?>
						<input name='<?php echo $key;?>' type="hidden" value='<?php echo !empty($value)? $value:''?>'>
						<?php
					break;
					case 'plugabble':
						do_action('evost_admin_formfields', $key, $form_data, $this->SEATS);
					break;
					case 'note':
						?><p class='evost_sm_note'><em><?php echo $note;?></em></p><?php
					break;
					case 'readable':
						?>
						<p>
							<label><?php echo $label;?> <?php echo $req?'*':'';?></label>
							<input class='<?php echo $req?'req':'';?>' name='<?php echo $key;?>' type="text" readonly value='<?php echo !empty($value)? $value:''?>'>
							<?php echo $desc;?>
						</p>
						<?php
					break;
					case 'text':
						?>
						<p>
							<label><?php echo $label;?> <?php echo $req?'*':'';?></label>
							<input class='<?php echo $req?'req':'';?>' name='<?php echo $key;?>' type="text" value='<?php echo !empty($value)? $value:''?>'>
							<?php echo $desc;?>
						</p>
						<?php
					break;
					case 'textarea':
						?>
						<p>
							<label><?php echo $label;?> <?php echo $req?'*':'';?></label>
							<textarea class='<?php echo $req?'req':'';?>' name='<?php echo $key;?>' style='width:100%'><?php echo !empty($value)? $value:''?></textarea>
							<?php echo $desc;?>
						</p>
						<?php
					break;
					case 'yesno':
						?>
						<p class='yesno_row evo'>
						<?php
							echo $ajde->wp_admin->html_yesnobtn(
								array('label'=>$label,
								'input'=>true,
								'id'=>$key,
								'var'=>$value,
								'guide'=>!empty($guide)?$guide:''
							));
						?>
						</p>
						<?php
					break;
					case 'number':

						echo EVO()->elements->get_element(
							array(
								'type'=>	'plusminus',
								'name'=> 	$label. ($req?'*':''),
								'id'=>		$key,
								'class_2'=> ($req?'req':''),
								'value'=> 	!empty($value)? $value:'1',								
								'row_class'=>'number_change',	
								'field_after_content'=> $desc	
							)
						);

					break;
					case 'icon':

						$icons = $ajde->wp_admin->get_font_icons_data();
						?>
						<p class='icon_select'>
							<label><?php echo $label;?> <?php echo $req?'*':'';?></label>
							<input name='<?php echo $key;?>' type="hidden" value='<?php echo !empty($value)? $value:''?>'>
							
							<span class="selected_icons" style='display:<?php echo empty($value)?'none':'block';?>'>
								<i class='fa <?php echo !empty($value)? $value:''?>'></i>
								<a class='evo_admin_btn btn_triad evost_form_remove_icon'><?php _e('Change Icon','evost');?></a>
							</span>
							<span class='icon_area' style='display:<?php echo empty($value)?'block':'none';?>'>
							<?php
								foreach($icons as $icon){
									echo "<span class='evost_icon'><i data-val='".$icon."' class='fa ".$icon."'></i></span>";
								}
								?>
							</span>
							<?php echo $desc;?>
						</p>
						<?php
					break;
					case 'image':
						?>
						<p>
							<label><?php echo $label;?> <?php echo $req?'*':'';?></label>
							<input type="hidden" class='evost_seat_img' name='<?php echo $key;?>' value='<?php echo !empty($value)? $value:''?>'/>
							<span class='evost_img_holder' style='display: block;'><?php
							if( !empty($value)){
								$ii = wp_get_attachment_image_url($value, 'thumbnail');
								if( $ii) echo "<img src='{$ii}'/>";
							}
							?></span>
							<input type="button" class='evost_select_image evo_admin_btn <?php echo !empty($value)? 'removeimg':'chooseimg';?>' data-txt='<?php echo !empty($value)?'Select Image':'Remove Image';?>' value='<?php echo !empty($value)?'Remove Image':'Select Image';?>'/>
							<i class='note' style='display:block'><?php echo $note;?></i>
						</p>
						<?php
					break;
					case 'select':
						?>
						<p>
							<label><?php echo $label;?> <?php echo $req?'*':'';?></label>
							<select class='<?php echo $req?'req':'';?>'  name="<?php echo $key;?>">
								<?php
								foreach($options as $o=>$v){
									$select = ($value && $o==$value)? 'selected="selected"':'';
									echo "<option value='{$o}' {$select}>{$v}</option>";
								}
								?>
							</select>
							<?php echo $desc;?>
						</p>
						<?php
					break;
					case 'color_select':
						$color = !empty($value)? str_replace('#', '', $value): '';
						?>
						<p class='color_select'>
							<em class='evost_color_pickerx colorselector' style='background-color:#<?php echo $color;?>' hex='#<?php echo $color;?>'></em>
							<label><?php echo $label;?> <?php echo $req?'*':'';?></label>						
							<input class='evocolorp_val' type="hidden" name='<?php echo $key;?>' value='<?php echo $color?>'>
						</p>
						<?php
					break;
					case 'submit_button':
						?>
						<p style='' class='evost_actions'>
							<a class='evo_admin_btn btn_prime evost_save_form'><?php echo $text;?></a>
							<?php if( !empty($show_delete) && $show_delete):?>
								<a class='evo_admin_btn btn_triad evost_delete_item'><?php echo $del_text;?></a>
							<?php endif;?>
						</p>				
						<?php
					break;
					
				}
			}

			return ob_get_clean();
			
		}

// download upload seat data
	public function download_data(){
		$EVENT = new EVO_Seats( (int)$_POST['event_id']);
	}

// SAVE FNC
	function save_item($formdata){

		if(!is_array($formdata)) return false;

		$ST = $this->SEATS;

		$sDATA = $ST->seats_data? $ST->seats_data: array();
	
		// add new section
		if(!$ST->section){
			$seciton_id = $this->get_new_item_index();
			$section_data = array();							
			$section_index = isset($formdata['section_index'])? $formdata['section_index']:$seciton_id; 
			$section_data['section_index'] = $section_index;
			$section_data['section_id'] = $seciton_id;

			// assigned seating
			if( $formdata['type'] == 'def'){
				$row_letter = 'A';
				// build rows
				for($r=1; $r<= $formdata['rows']; $r++){

					$row_id = 'r'.$this->get_new_item_index();

					// each seat
					for($s=1; $s<= $formdata['seats']; $s++){

						$seat_id = 's'.$this->get_new_item_index();
						$section_data['rows'][$row_id][$seat_id]= array(
							'id'=> $seat_id,
							'number'=> $section_index.$row_letter.$s,
							'price' => $formdata['def_price'],
							'status'=>'av',
							'handicap'=>'no'
						);
					}
					$section_data['rows'][$row_id]['row_index'] = $row_letter;
					$section_data['rows'][$row_id]['row_price'] = $formdata['def_price'];

					$row_letter = ++$row_letter;
				}			

				$section_data['row_count'] = $r;

			// booth seating
			}elseif( $formdata['type'] == 'boo'){
				$section_data['sold'] = '0';
				$section_data['h'] = '50';
				$section_data['w'] = '50';

				// update section ID to be unique to booth with B infront
				$seciton_id = 'B' . $seciton_id;
				$section_data['section_id'] = $seciton_id;
			// create seats for unassigned area
			}elseif($formdata['type'] == 'una'){
				$section_data['sold'] = '0';
				$section_data['h'] = '50';
				$section_data['w'] = '50';
			}else{// for area of interest
				$section_data['h'] = '50';
				$section_data['w'] = '50';
			}

			// UPDATE other values
				$section_data = $this->__process_other_formdata($section_data, $formdata);

			//print_r($section_data);
								
			// set top and left for new section
			$section_data['top'] = '50';
			$section_data['left'] = '50';
			$section_data['ang'] = '0';	

			$sDATA[$seciton_id] = $section_data;
			
			// save and load new section
			$ST->save_seat_map_data($sDATA);
			$ST->set_section( $seciton_id );
		}else{	
			
			// for section
				if($ST->item_type=='section'){
					$item_data = $ST->item_data;

					$current_rows = $ST->get_rows();						

					// if changing rows
						if($current_rows && $formdata['rows'] != $current_rows){

							// ADDING more rows
							if($formdata['rows'] > $current_rows){

								$row_letter = $ST->get_section_last_row_letter();
								++$row_letter;
								
								// for each row
								for($r = $current_rows; $r < $formdata['rows']; $r++){
									$section_letter = $ST->get_section_letter_by_id();
									//echo $section_letter.' '.$r.'/';

									$row_id = 'r'.$this->get_new_item_index();

									// for each seat
									for($s=1; $s<= $formdata['seats']; $s++){

										$seat_id = 's'.$this->get_new_item_index();

										$ID = $section_letter.$row_letter.$s;
										$item_data['rows'][$row_id][$seat_id]= array(
											'id'=> $seat_id,
											'number'=> $ID,
											'price' => $formdata['def_price'],
											'status'=>'av',
											'handicap'=>'no'
										);
									}

									$item_data['rows'][$row_id]['row_price'] = $formdata['def_price'];
									$item_data['rows'][$row_id]['row_index'] = $row_letter;
									$row_letter = ++$row_letter;
								}
							}elseif($formdata['rows'] < $current_rows){// removing rows 
								$row_count =1;
								foreach($item_data['rows'] as $index=>$row){
									if( empty($index)){ unset($item_data['rows'][$index]); continue;}
									if($row_count > $formdata['rows'] ){
										unset($item_data['rows'][$index]);
									}
									$row_count++;
								}
							}
						}else{// rows count stay same
							if(isset($item_data['rows'] )){
								foreach($item_data['rows'] as $row_id=>$row_data){
									foreach($row_data as $seat_id=>$seat_data){
										if(in_array($seat_id, array('row_id','row_index', 'row_price'))) continue;
										
										if(!empty($seat_data['price'])) continue; // if seat price is set skip

										// seats with no price set = section default price
										$item_data['rows'][$row_id][$seat_id]['price'] = $formdata['def_price'];

									}
								}
							}							
						}

					// UPDATE other values
					$item_data = $this->__process_other_formdata($item_data, $formdata);
					
				}

			// rows
				if($ST->item_type=='row'){
					$item_data = $ST->item_data;
					$current_seats = $ST->get_max_seats();

					$current_row_index = $item_data['row_index'];

					// if changing row index letter
						if( $formdata['row_index'] != $current_row_index){
							$section_index = $ST->get_section_letter_by_id();
							
							// EACH SEAT
							$seat_count = 1;
							foreach($item_data as $seat_id=>$seat){
								if(in_array($seat_id, array('row_id','row_index', 'row_price'))) continue;

								// before changing seat number
								//if( $item_data[$index]['number'] != $section_index.$current_row_index.$index) continue;

								// include the new row index in seat number
								$item_data[$seat_id]['number'] = $section_index.$formdata['row_index'].$seat_count;
								$seat_count++;
							}
						}

					// if changing seats number
						if($formdata['seats'] != $current_seats){
							// adding seats
							if($formdata['seats']>$current_seats){
								$section_index = $ST->get_section_letter_by_id();
								
								for($s = ($current_seats+1); $s <= $formdata['seats']; $s++ ){

									$seat_id = 's'.$this->get_new_item_index();

									$ID = $section_index.$item_data['row_index'].$s;
									$item_data[$seat_id] = array(
										'id'=> $seat_id,
										'number'=> $ID,
										'price' =>$formdata['row_price'],
										'status'=>'av',
										'handicap'=>'no'
									);
								}
							}else{// removing seats
								$seat_count  = 1;
								foreach($item_data as $index=>$seat){
									if(in_array($index, array('row_id','row_index', 'row_price'))) continue;
									if($seat_count > $formdata['seats'] ){
										unset($item_data[$index]);
									}
									$seat_count ++;
								}
							}
						}

					// if changing the row price
						if( $formdata['row_price'] != $item_data['row_price']){
							foreach($item_data as $seat_id=>$seat){
								if(in_array($seat_id,array('row_id','row_index', 'row_price'))) continue;
								$item_data[$seat_id]['price'] = $formdata['row_price'];
							}
						}

					// UPDATE other values
					$item_data = $this->__process_other_formdata($item_data, $formdata);						
				}

			// Seat
				if($ST->item_type=='seat'){
					$item_data = $this->__process_other_formdata($ST->item_data, $formdata);	
				}

			//print_r($formdata['def_price']);
			//print_r($item_data);
			$ST->save_item_data($item_data);

			// reload item data
			switch($ST->item_type){
				case 'section':
					$ST->set_section($ST->section); 
				break;
				case 'row':
					$ST->set_row($ST->row, $ST->section); 
				break;
				case 'seat':
					$ST->set_seat($ST->seat, $ST->row, $ST->section);
				break;
			}
		}			
	}

	function __process_other_formdata($array, $formdata){
		foreach( $formdata as $key=>$val){
			if(in_array($key, array('wcid','event_id','section','row','seat','item_type','method','rows','seats', 'section_id','row_id','seat_id','sold'))) continue;
			if(empty($val)) $array[$key] = '';

			$array[$key] = $val;
		}

		return $array;
	}

// SUPPORTIVE
	function get_new_item_index(){
		return  rand(1000, 9000);
	}


}



if(is_admin()){
	new EVOST_Seat_Map_Editor();
}