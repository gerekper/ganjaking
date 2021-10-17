<?php
/**
 * Subscriber meta boxes for event page
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin/evo-subscriber
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evosb_subscriber_metaboxes{	
	// Constructor
		function __construct(){
			add_action('add_meta_boxes', array($this,'add_meta_boxes') );
			add_action('save_post',  array($this,'save_post_meta'), 10, 2);

			add_action( 'post_submitbox_misc_actions', array($this,'publish_box_inclusions') );
		}
		function add_meta_boxes(){
			add_meta_box('evo_mb1',__('Event Subscriber','eventon'), array($this,'evosub_metabox_002'),'evo-subscriber', 'normal', 'high');
		}

	// META BOX for subscriber cpt
		function evosub_metabox_002(){
			global $post, $eventon, $eventon_sb;
			$pmv = get_post_custom($post->ID);

			// testing
			//$eventon_sb->admin->send_new_event_email('452');
			?>
			<style type="text/css">#edit-slug-box{display:none!important;}</style>	

			<div class='eventon_mb' style='margin:-6px -12px -12px'>
			<div style='background-color:#ECECEC; padding:15px;'>
				<div style='background-color:#fff; border-radius:8px;'>
				<table width='100%' class='evo_metatable' cellspacing="" style='vertical-align:top' valign='top'>
					<?php
						foreach($eventon_sb->frontend->get_form_fields() as $field=>$value){

							if($field == 'subtitle'){
								echo "<tr><td colspan='2' style='font-weight:bold'>{$value}</td></tr>";
								continue;
							}

							$value_ = (!empty($pmv[$field])? $pmv[$field][0]:null);

							if($field!= 'email' && $field != 'name'&& $field != 'verified'&& $field != 'status')
								$value_ = (!empty($value_))? $value_:'-';

							$guide = '';
							// guide 
							if($field == 'verified' || $field == 'status'){	

								if($field=='verified')
									$guide = $eventon->throw_guide(__('Whether email needed to be verified and if it was verified.','eventon'),'',false);

								if($field=='status')
									$guide = $eventon->throw_guide(__('Subscription status, whether they are still subscribed or unsubscribed.','eventon'),'',false);

								echo "<tr><td>".$value.$guide."</td>
									<td>". eventon_html_yesnobtn(array(
										'var'=>$value_,
										'input'=>true,
										'id'=>$field
									))."</td></tr>";
							}else{
								echo "<tr><td>".$value.$guide."</td><td><input type='text' name='{$field}' value='". ($value_) ."'/></td></tr>";
							}
							


							
						}
					?>
				</table>
				</div>
			</div>
			</div>
			<?php
		}

	// Save subscriber custom post data
		function save_post_meta($post_id, $post){

			if($post->post_type != 'evo-subscriber')
				return;		

			global $eventon_sb;

			foreach($eventon_sb->frontend->get_form_fields() as $field=>$name){

				if($field=='subtitle') continue;

				if(!empty($_POST[$field])){
					update_post_meta( $post_id, $field,$_POST[$field]);
				}elseif(empty($_POST[$field])){
					delete_post_meta($post_id, $field);
				}
			}
		}

	// show if the subscriber is added in mailchimp list as well
		function publish_box_inclusions(){
			global $post;

			if ( ! is_object( $post ) ) return false;
			if( empty($post->post_type) ) return false;
			if( $post->post_type!='evo-subscriber') return false;
			if(!isset($post->ID)) return;

			$mailchimp = get_post_meta($post->ID, '_mailchimp', true);

			if($mailchimp!='added') return;

			echo "<p style=\"background-color: #D9F3FA;padding: 5px 12px;display: block;margin: 0!important;line-height: 1.8em!important;font-weight: 400!important; color: #383838;\" class=\"evosb_data\">Also subscribed in mailchimp list!</p>";
		}

}
new evosb_subscriber_metaboxes();

