<?php 
/**
 * Templates for Invitees
 */

class EVORSI_Temp{
	public function __construct(){
		add_action('evo_temp_evorsi_stats', array($this, 'stats'), 10);
		add_action('evo_temp_evorsi_invitee_rows', array($this, 'invitee_rows'), 10);
		add_action('evo_temp_evorsi_invitee_form', array($this, 'form'), 10);
		add_action('evo_temp_evorsi_invitee_msgs', array($this, 'messages'), 10);
		add_action('evo_temp_evorsi_wall', array($this, 'wall'), 10);
		add_action('evo_temp_evorsi_notice', array($this, 'notice'), 10);
	}

	function wall(){
		?>
		{{#each msgs}}
		<p class='{{c}} {{v}}'>
			<span class="t">{{t}}</span>
			<span class='o'>{{n}} {{#if tm}}<em> - {{tm}} <?php evo_lang_e('ago');?></em>{{/if}} - <i>{{v}}</i> </span>
		</p>
		{{else}}
		<p><?php evo_lang_e('No messages','evorsi');?></p>
		{{/each}}
		<?php
	}
	function notice(){
		?>
		<span class="evorsi_send_msg_notice{{#if hide}} evo_hide{{/if}}{{#if error}} error{{/if}}">{{text}}</span>
		<?php
	}
	
	function stats(){
		?>
		{{#each stats}}
		<span class="{{@key}}">
			<em>{{@key}}</em>
			<b>{{this}}</b>
		</span>
		{{/each}}		
		<?php
	}

	// messages on admin
	function messages(){
		global $ajde;
		?>
		{{#each msgs}}
			<p data-i='{{@key}}' class='{{#ifCond n "==" "admin"}}admin{{/ifCond}}{{#ifCond n "!=" "admin"}}invitee{{/ifCond}}'><span class='t'>{{t}}</span> <span class='o'>{{n}} {{#if time}}<em>- {{time}} <?php _e('ago','evorsi');?></em> <em class='v'>{{v}}</em>{{/if}} <i class='evorsi_msg_d' ><?php _e('Delete','evorsi');?></i></span></p>
		{{else}}
		<p><?php _e('No Messages','evorsi');?></p>
		{{/each}}

		<p class='evorsi_custom_new_msg'><textarea cols="30" rows="5" class='evorsi_msgs_msg'></textarea>
			<span style='display:block; opacity: 0.7'><i>HTML code or plain text accepted</i></span>
			<span class='evprsi_admin_message_notice'></span>
			<span class="evo_admin_btn btn_triad evorsi_send_msg"><?php _e('Send Message','evorsi');?></span>
		</p>
		
		<p class='yesno_leg_line '><?php
			echo $ajde->wp_admin->html_yesnobtn(
				array(
					'id'=>'visibility','input'=>true,
					'label'=> evo_lang('Post on message wall as well'),
				)
			);
		?>
		</p>
		<?php
	}

	function invitee_rows(){
		?>
		{{#each rows}}
		<div class="evorsi_list_row">
			<p class='n'><a class='evorsadmin_rsvp' href='{{{edit_link}}}'>#{{@key}}</a>{{name}} {{#if extra}}<em>+{{extra}}</em>{{/if}}</p>
			<p class='e'>{{email}}</p>
			<p class='s'>{{rsvp_status}}</p>
			<p class='a'>
				<a class="link" href='{{link}}'><?php echo __('Link','evorsi');?></a>
				<span class="checkin_status {{status}}">{{status}}</span>
				<i class="fa fa-eye evorsi_view ajde_popup_trig" data-popc='evorsi_lightbox_two' data-iid='{{@key}}' data-eid='{{eid}}'></i>
			</p>
		</div>
		
		{{else}}
		<div class="evorsi_list_row"><p class='none'><?php _e('No invitees','evorsi');?></p></div>
		{{/each}}
		<?php
	}

	// form fields
		function form(){
			$fields = array(
				'e_id'=>array('type'=>'hidden'),
				'invitee_id'=>array('type'=>'hidden'	),
				'repeat_interval'=>array('type'=>'hidden'),
				'rsvp_type'=>array('type'=>'hidden'	),
				'status'=>array('type'=>'hidden'),
				'lang'=>array('type'=>'hidden'	),
				'rsvp'=>array('type'=>'hidden'	),
				'header'=>array(
					'type'=>'header',		
				),
				'first_name'=>array(
					'type'=>'text',
					'req'=> true,
					'label'=> __('First Name','evorsi'),
					'val'=> ''				
				),
				'last_name'=>array(
					'type'=>'text',
					'req'=> false,
					'label'=> __('Last Name','evorsi'),	
					'val'=> ''			
				),'email'=>array(
					'type'=>'text',
					'req'=> true,
					'label'=> __('Email Address','evorsi'),
					'val'=> ''				
				),
				'count'=>array(
					'type'=>'number',
					'label'=> __('Number of people in the party','evorsi'),	
					'val'=> '',
					'desc'=> __('NOTE: If you are setting a count more than 1 make sure to enable RSVP Count field for the form via RSVP Settings > RSVP Form','evorsi')		
				),
				'custom'=>array(
					'type'=>'custom',		
				),
				'button'=>array('type'=>'button',		),
			);

			?>
			
			<div class="evorsi_invitee_form">
			<?php
				echo $this->_form_fields($fields);
			?>			
			</div>
			<?php
			
		}
		function _form_fields($fields){
			ob_start();
			//print_r($fields);

			foreach($fields as $key=>$data){
				extract($data);
				if(empty($type)) continue;

				$req = !empty($req)? $req:false;
				$value = '';
					if(!empty($val)) $value = $val;

				$desc = isset($data['desc'])? "<span class='desc'>". $desc ."</span>":'';

				switch ($type){
					case 'header':?>
						{{#ifCond type "==" "edit"}}
						<p><a href='{{{edit_link}}}' class='evo_admin_btn btn_triad'>#{{invitee_id}}</a></p>
						{{/ifCond}}
					<?php break;
					case 'status':?>
						{{#ifCond type "==" "edit"}}
						<input class='field' name='<?php echo $key;?>' type="hidden" value='<?php echo '{{'.$key.'}}'?>'>
						{{/ifCond}}
					<?php
					break;
					case 'hidden':?>
						<input class='field' name='<?php echo $key;?>' type="hidden" value='<?php echo '{{'.$key.'}}'?>'><?php
					break;
					case 'text':
						?>
						<p>
							<label><?php echo $label;?> <?php echo $req?'*':'';?></label>
							<input class='field <?php echo $req?'req':'';?>' name='<?php echo $key;?>' type="text" value='<?php echo '{{'.$key.'}}'?>'>
							<?php echo $desc;?>
						</p>
						<?php
					break;
					case 'number':
						?>
						<p class='number_change'>
							<label><?php echo $label;?> <?php echo $req?'*':'';?></label>
							<input class='field <?php echo $req?'req':'';?>' name='<?php echo $key;?>' type="hidden" value='<?php echo '{{'.$key.'}}'?>'>
							<?php echo $desc;?>
							<span class='number_field'>
								<em class='minus evorsi_form_number_change'>-</em>
								<i><?php echo '{{'.$key.'}}'?></i>
								<em class='plus evorsi_form_number_change'>+</em>
							</span>
						</p>
						<?php
					break;
					case 'custom':
						?>
						{{#ifCond type "==" "edit"}}
						<div class='evorsi_form_custom_data'>
							<p class='evorsi_form_custom'>
								<a class='f'><?php _e('Additional Information','evorsi');?></a>
								<a class=''><?php _e('Messages','evorsi');?></a>
							</p>
							<div class='evorsi_form_custom_boxes'>
								<div class="evorsi_form_custom_a evorsi_box f">
									{{#each other_data}}
									<p><b>{{@key}}</b> {{this}}</p>
									{{else}}
									<p><?php _e('No Data','evorsi');?></p>
									{{/each}}
								</div>
								<div class="evorsi_form_custom_m evorsi_box"></div>
							</div>
						</div>	
						{{/ifCond}}	
						<?php
					break;
					case 'button':
						?>
						{{#ifCond type "==" "new"}}
						<p style='text-align:center' class='evost_actions'>
							<a data-t='new' class='evo_admin_btn btn_prime evorsi_form_submit'><?php _e('Send Invite','evorsi');?></a>
						</p>
						{{/ifCond}}
		
						{{#ifCond type "==" "edit"}}
						<p style='text-align:center' class='evost_actions'>
							<a data-t='edit' class='evo_admin_btn btn_triad evorsi_resend_invitation'><?php _e('Resend Invitation','evorsi');?></a>
							<a data-t='edit' class='evo_admin_btn btn_prime evorsi_form_submit '><?php _e('Save Changes','evorsi');?></a>
						</p>	
						{{/ifCond}}			
						<?php
					break;
				}

			}

			return ob_get_clean();
		}
}
new EVORSI_Temp();