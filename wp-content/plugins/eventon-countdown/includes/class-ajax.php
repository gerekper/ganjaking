<?php
/**
 * EventON Subscriber Ajax Handlers
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @version     0.1
 */

class evosub_ajax{
	// construct
		public function __construct(){
			$ajax_events = array(
				'evosub_new_subscriber'=>'evosub_new_subscriber',
				'evosb_generate_csv'=>'evosb_generate_csv',
			);
			foreach ( $ajax_events as $ajax_event => $class ) {
				
				add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
				add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
			}
		}

	// Download CSV of attendance
		function evosb_generate_csv(){

			global $eventon_sb;

			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=Event_Subscribers_".date("d-m-y").".csv");
			header("Pragma: no-cache");
			header("Expires: 0");

			//$fp = fopen('file.csv', 'w');

			echo "Email Address, Name\n";

			$entries = new WP_Query(array(
				'posts_per_page'=>-1,
				'post_type' => 'evo-subscriber',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => 'verified',
						'value'   => 'yes',
						'compare' => '=',
					),
					array(
						'key'     => 'status',
						'value'   => 'yes',
						'type'    => 'numeric',
						'compare' => '=',
					)
				)
			));
			if($entries->have_posts()):
				while($entries->have_posts()): $entries->the_post();
					$email = get_the_title();
					$name = get_post_meta($entries->post->ID, 'name',true);
					$name = (!empty($name))? $name: '-';
					echo $email.",".$name."\n";

				endwhile;

			endif;

			wp_reset_postdata();

		}

	// NEW Subscription - adding new subscriber
		function evosub_new_subscriber(){
			global $eventon_sb;

			foreach($_POST as $ff=>$post){
				$__post[$ff]= urldecode($post);
			}

			// check for email field
			if(!empty($__post['email'])){
				$helper = new evo_helper();


				// check if email exist
				if(! $eventon_sb->frontend->email_exist($__post['email'])){
				
					if($subscriber_id = $helper->create_posts(array(
						'post_type'=>'evo-subscriber',
						'post_title'=>$__post['email'],
						'post_status'=>'publish',
						'author_id'=>1,
						'post_content'=>'',
					))){

						// create meta fields if set
						foreach($eventon_sb->frontend->get_form_fields() as $field=>$fieldname){
							
							if($field == 'name' || $field =='email'){
								if(empty($__post[$field])) continue;
								update_post_meta($subscriber_id, $field, urldecode($_POST[$field]));

							}elseif(!in_array($field, array('verified','status','subtitle'))){
								// for other fields, tax - set value to all if none
								$field_value = (!empty($__post[$field]))? $__post[$field]:
									($eventon_sb->frontend->has_category_fields_selected()?'':'all');
								update_post_meta($subscriber_id, $field, $field_value);
							}				
						}

						// language of the form submitted
							$lang = !empty($__post['lang'])? $__post['lang']: 'L1';
							update_post_meta($subscriber_id, 'lang', $lang);

						// receive updates status = yes at first
							update_post_meta($subscriber_id, 'status', 'yes');					

						// if verification email set save verification key
							$key=''; $verification_required = 'no';
							if(!empty($eventon_sb->frontend->evoOpt_sb['evosb_1_001']) && $eventon_sb->frontend->evoOpt_sb['evosb_1_001']=='yes'){
								$key =md5(time());
								update_post_meta($subscriber_id, 'verification_key', $key);

								$verification_required= 'yes';
							}
						// verification of subscription
							$verified = $verification_required=='yes'?'no':'yes';

							update_post_meta($subscriber_id, 'verified', $verified);
							update_post_meta($subscriber_id, 'verification_required', $verification_required);
							

						// settings values are checked before sending these emails
							$eventon_sb->frontend->email_them(array(
								'to'=>$__post['email'], 
								'key'=>$key,
								'subscriber_id'=>$subscriber_id,
								'lang'=>$_POST['lang']
							));	
					
						echo json_encode(array(
							'status'=>'good','msg'=>''
						));
						exit;

					}else{
						echo json_encode(array(
							'status'=>'bad','msg'=>'no_cpt'
						));
						exit;
					}

				}else{
					echo json_encode(array(
						'status'=>'bad','msg'=>'email_exist'
					));
					exit;
				}
			}else{
				echo json_encode(array(
					'status'=>'bad','msg'=>'empty_email'
				));
				exit;
			}

		}

}
new evosub_ajax();