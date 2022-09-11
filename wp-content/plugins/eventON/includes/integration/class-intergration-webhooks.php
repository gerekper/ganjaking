<?php 
/**
 * EventON webhooks integration	
 * @version 4.1
 */

class EVO_WebHooks{
	function __construct(){
		add_filter('eventon_settings_3rdparty', array($this, 'settings'),10,1);
		add_action( 'wp_ajax_evo_webhook_settings', array( $this, 'ajax_webhook_settings' ) );
		add_action( 'wp_ajax_evo_webhook_delete', array( $this, 'ajax_webhook_delete' ) );
		add_action( 'wp_ajax_evo_webhook_settings_save', array( $this, 'ajax_webhook_settings_save' ) );
	}

	public function send_webhook($webhook_url, $data_array){

		$request = array(
			'body'=> $data_array,			
			'timeout'=> 30,
		);

		
		$result = wp_remote_post($webhook,$request);
		if ( $result['response']['code'] == 200 ) {
	        return array( 1 );
	    } else {
	        return array( 0, $result )  ;
	    }
	}

// admin
	public function settings($A){
		$B = array(
			array('type'=>'sub_section_open','name'=>__('Webhooks [BETA]','eventon')),
			array('id'=>'note',
				'type'=>'note',
				'name'=>'Create webhooks from EventON for these platforms: zapier, IFTTT, Integromat, Automate.io, Built.io, Workato, elastic.io, APIANT, Webhook',				
			),
			array('id'=>'evcal__note','type'=>'customcode','code'=>$this->webhookz_code()),			
			array('type'=>'sub_section_close'),
		);

		return array_merge($A, $B);
	}
	public function ajax_webhook_settings(){
		
		$HELP = new evo_helper();
		$post = $HELP->sanitize_array($_POST);

		if( isset($post['id'])){
			$hook_data = $this->get_hook_data($post['id']);
		}else{
			$hook_data = array('id'=>  rand(10000,99999) );
		}
		
		ob_start();	

		$triggers = $this->get_trigger_events();

		?>
		<div style='padding:20px;'>
			<form class='evo_webhook_settings'>
				<input type="hidden" name="id" value='<?php echo $hook_data['id'];?>'/>
				<input type="hidden" name="action" value='evo_webhook_settings_save'/>
				<?php wp_nonce_field( plugin_basename( __FILE__ ), 'evowh_noncename' );?>
				<p class='evo_elm_row'><?php _e('Webhook ID','eventon');?>: <span><?php echo $hook_data['id'];?></span></p>	
				<?php 

				if( count($triggers)<1):
					echo "<p>". __('You do not have valid trigger points avialable yet.','eventon') ."</p>";
				else:

				echo EVO()->elements->process_multiple_elements(
					array(
						array(
							'type'=>'dropdown',
							'id'=>'trig',
							'value'=>	(isset($hook_data['trig']) ? $hook_data['trig']: ''),
							'name'=> __('Select available EventON trigger points to pass values to webhook','eventon'),
							'options'=> $triggers
						),array(
							'type'=>'text',
							'id'=>'url',
							'value'=>	(isset($hook_data['url']) ? $hook_data['url']: ''),
							'name'=> __('Webhook URL','eventon'),							
						)
					)
				);
			?>	
				<div class='evo_elm_row'>
					<p class='evo_field_label'><?php _e('Fields passed on to webhook','eventon');?></p>
					<p class='evo_field_container' data-d=''>-</p>
				</div>
				<p><span class='evo_btn save_webhook_config ' style='margin-right: 10px'><?php _e('Save Changes','eventon');?></span></p>	

			<?php endif;?>
			</form>
		</div>
		<?php 

		echo json_encode(array(
			'status'=>'good','html'=> ob_get_clean()
		));exit;
	}
 	
 	function ajax_webhook_delete(){
		$HELP = new evo_helper();
		$post = $HELP->sanitize_array($_POST);

		if(!isset( $post['id'] )){
			echo json_encode(array('status'=>'bad','msg'=> __('Missing webhook ID')	));exit;
		}

		$webhooks = $this->get_hook_data();
		if( !isset($webhooks[ $post['id'] ])) return;

		unset($webhooks[ $post['id'] ]);

		EVO()->cal->set_cur('evcal_1');
		EVO()->cal->set_prop('evowhs', $webhooks );

		echo json_encode(array('status'=>'good',
			'msg'=> __('Successfully saved webhook data'),
			'html'=> $this->get_webhooks_html()
		));exit;

	}

	function get_trigger_events(){
		return apply_filters('evo_webhook_triggers',
			array()
		);
	}


	// save values
		public function ajax_webhook_settings_save(){
			$HELP = new evo_helper();
			$post = $HELP->sanitize_array($_POST);

			if(!isset( $post['id'] )){
				echo json_encode(array('status'=>'bad','msg'=> __('Missing webhook ID')	));exit;
			}

			$webhooks = $this->get_hook_data();
			if(!$webhooks) $webhooks = array();

			$hook_id = (int)$post['id'];

			foreach(array('trig','url') as $valid_field){
				if(!isset( $post[ $valid_field ] )) continue;
				$webhooks[ $hook_id ][$valid_field] =  $post[ $valid_field ];
			}

			EVO()->cal->set_cur('evcal_1');
			EVO()->cal->set_prop('evowhs', $webhooks );

			echo json_encode(array('status'=>'good',
				'msg'=> __('Successfully saved webhook data'),
				'html'=> $this->get_webhooks_html()
			));exit;
		}

	// return a list of all webhooks incely
		public function get_webhooks_html(){
			$webhooks = $this->get_hook_data();

			$OUT = '';

			if($webhooks){
				$HELP = new evo_helper();
				$available_hooks = $this->get_trigger_events();

				foreach($webhooks as $id=>$data){
					$name = isset($available_hooks[ $data[ 'trig' ]]) ? $available_hooks[ $data[ 'trig' ]]: $data[ 'trig' ];
					$url = isset($data[ 'url' ]) ? $data[ 'url' ] : '-';
					$data = array(
						'popc'=>'print_lightbox',
						'lb_cl_nm'=>'evo_webhook_settings',
						'ajax'=>'yes',
						't'=>'Webhook Configurations',
						'd'=> array(
							'action'=>'evo_webhook_settings',
							'id'=> $id
						)
					);

					$OUT .= "<p data-id='{$id}'><span>{$id}</span><span>{$name}</span><span>{$url}</span><em><i class='fa fa-pencil evowh_edit ajde_popup_trig' ". $HELP->array_to_html_data($data) ."></i><i class='evowh_del fa fa-minus-circle'></i></em></p>";
				}
			}else{
				$OUT .= "<p data-id=''>".__('No webhooks created yet')."</p>";
			}

			return $OUT;
		}

	// codes for settings
	public function webhookz_code(){
		
		ob_start();
		$data = array(
			'popc'=>'print_lightbox',
			'lb_cl_nm'=>'evo_webhook_settings',
			'ajax'=>'yes',
			't'=>'Webhook Configurations',
			'd'=> array(
				'action'=>'evo_webhook_settings'
			)
		);
		$HELP = new evo_helper();
		?>
		<div id='evowhs_container'><?php echo $this->get_webhooks_html();?></div>
		<p><a class='evo_btn ajde_popup_trig' <?php echo $HELP->array_to_html_data($data);?>><?php _e('Create a new webhook connection');?></a></p>
		<?php

		return ob_get_clean();
	}

	public function get_hook_data($id = ''){
		$whs = EVO()->cal->get_prop('evowhs','evcal_1');

		if(!empty($id)){
			if(isset($whs[ $id ])){
				$whs[$id]['id']= $id;
				return $whs[$id];
			} 
		}
		return $whs;
	}
	
}