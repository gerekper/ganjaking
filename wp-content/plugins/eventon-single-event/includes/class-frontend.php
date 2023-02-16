<?php
/**
 * Frontend
 * @version  0.2
 */
class evose_frontend{
	public $is_single_event = false;

	public function __construct(){
		global $eventon_sin_event;

		$this->plugin_url = $eventon_sin_event ->plugin_url;
		$this->opt =	get_option('evcal_options_evcal_1');

		require_once('class-functions.php');
		$this->functions = new evose_functions();

		add_action( 'init', array( $this, 'register_scripts' ) ,16);
		add_action( 'wp_head', array( $this, 'fb_headers' ) );	
		add_action( 'eventon_enqueue_scripts', array( $this, 'enqueue_scripts' ) );	

		add_filter('eventon_event_post_supports', array( $this, 'activate_comments' ) ,10,1 );	
		
		// single event page custom icon
		add_filter('evosin_evodata_vals', array( $this, 'evo_data' ) ,10,1 );	

		// template loading
			add_filter('eventon_template_paths', array( $this, 'add_new_template_load_path' ) ,10,1);

		// eventcard
			//add_filter('eventon_eventcard_additions', array( $this, 'add_social_media_to_eventcard' ) ,10,6 );
			add_filter('eventon_eventCard_evosocial', array($this, 'add_social_media_to_eventcard'), 10, 2);
			add_filter('eventon_eventcard_array', array($this, 'eventcard_array'), 10, 4);
			add_filter('evo_eventcard_adds', array($this, 'eventcard_adds'), 10, 1);
	}
	// update evo data
		function evo_data($array){
			if(!empty($this->opt['evo_gmap_iconurl']) ){
				$array['mapiconurl']= $this->opt['evo_gmap_iconurl'];
			}
			return $array;
		}
	// template path
		public function add_new_template_load_path( $paths ) {	
			global $eventon_sin_event;		
			$paths[] = $eventon_sin_event->plugin_path . '/templates/';			
			return $paths;
		}

	// FRONT end styles and scripts for single event.
		function register_scripts(){
			global $eventon_sin_event; 

			$this->is_single_event= true;
			
			wp_register_style('evcal_single_event',$eventon_sin_event->assets_path.'SE_styles.css');		
			wp_register_style('evcal_single_event_one_style',$eventon_sin_event->assets_path.'style_single.css');		

			wp_register_script('evcal_single_event_one',$eventon_sin_event->assets_path.'single_event_box.js', array('jquery'),'1.0',true );
		}		
		// front end styles for single event ::PAGE
			public function page_frontend_scripts(){
				global $typenow, $post, $wp_scripts, $eventon_sin_event;
				
				wp_enqueue_script('eventon_single_events',$eventon_sin_event->assets_path.'se_page_script.js', array('jquery'), '1.0', true );
				wp_enqueue_style( 'evcal_single_event');				
				$this->is_single_event= true;			
			}

		// enqueue single event scripts to eventon pages
			function enqueue_scripts(){
				global $eventon_sin_event;
				wp_register_script('evoSE_script', $eventon_sin_event->assets_path. 'se_evo_script.js', array('jquery'),'1.0',true );
				wp_enqueue_script('evoSE_script');
			}

	// add eventon single event card field to filter
		function eventcard_array($array, $pmv, $eventid, $__repeatInterval){
			$array['evosocial']= array(
				'event_id' => $eventid,
				'value'=>'tt',
				'__repeatInterval'=>(!empty($__repeatInterval)? $__repeatInterval:0)
			);
			return $array;
		}
		function eventcard_adds($array){
			$array[] = 'evosocial';
			return $array;
		}

	// facebook header to single events pages
		function fb_headers(){
			global $post, $eventon_sin_event;
			//print_r($post);

			if($post && $post->post_type=='ajde_events'):
				//$thumbnail = get_the_post_thumbnail($post->ID, 'medium');
				$img_id =get_post_thumbnail_id($post->ID);
				$pmv = get_post_meta($post->ID);
				
				ob_start();
					$excerpt = eventon_get_normal_excerpt( $post->post_content, 25);
				?>
				<meta name="robots" content="all"/>
				<meta property="description" content="<?php echo $excerpt;?>" />
				<meta property="og:url" content="<?php echo get_permalink($post->ID);?>" /> 
				<meta property="og:title" content="<?php echo $post->post_title;?>" />
				<meta property="og:description" content="<?php echo $excerpt;?>" />
				<?php if($img_id!=''): 
					$img_src = wp_get_attachment_image_src($img_id,'thumbnail');
				?>
					<meta property="og:image" content="<?php echo $img_src[0];?>" /> 
				<?php endif;?>
				<?php
				// organizer as author
					if(!empty($pmv['evcal_organizer']))
						echo '<meta property="article:author" content="'.$pmv['evcal_organizer'][0].'" />';

				echo ob_get_clean();
			endif;
		}

	// ADD SOcial media to event card
		function add_social_media_to_eventcard($object, $helpers){
			global $eventon, $eventon_sin_event;

			$__calendar_type = $eventon->evo_generator->__calendar_type;
			$evo_opt = $helpers['evOPT'];

			$event_id = $object->event_id;

			
			// check if social media to show or not
			if( (!empty($evo_opt['evosm_som']) && $evo_opt['evosm_som']=='yes' && $__calendar_type=='single') 
				|| ( empty($evo_opt['evosm_som']) ) || ( !empty($evo_opt['evosm_som']) && $evo_opt['evosm_som']=='no' ) ){
				
				$post_title = get_the_title($event_id);
						
				$permalink 	= urlencode(get_permalink($event_id));
				$permalinkCOUNT 	= get_permalink($event_id);

				// append repeat interval
					//$permalinkCOUNT = esc_url( add_query_arg('ri',$object->__repeatInterval,$permalinkCOUNT) );
					$permalink_connector = (strpos($permalinkCOUNT, '?')!== false)? '&':'?';

					$permalinkCOUNT = (!empty($object->__repeatInterval) && $object->__repeatInterval>0)? 
						$permalinkCOUNT.$permalink_connector.'ri='.$object->__repeatInterval: $permalinkCOUNT;

					//$encodeURL = ($permalinkCOUNT);
					$encodeURL = urlencode($permalinkCOUNT);

				// thumbnail
					$img_id = get_post_thumbnail_id($event_id);
					$img_src = ($img_id)? wp_get_attachment_image_src($img_id,'thumbnail'): false;

				// event details
					$summary = $eventon->frontend->filter_evo_content(get_post_field('post_content',$event_id));

				$title 		= str_replace('+','%20',urlencode($post_title));
				$titleCOUNT = $post_title;
				$summary = (!empty($summary)? urlencode(eventon_get_normal_excerpt($summary, 16)): '--');
				$imgurl = $img_src? urlencode($img_src[0]):'';
				
				//$app_id = '486365788092310';
				// social media array

				$fb_js = "javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;";
				$tw_js = "javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;";
				$gp_js = "javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;";

				$social_sites = apply_filters('evo_se_social_media', array(
					//<div class="fb-like" data-href="PERMALINKCOUNT" data-width="450" data-show-faces="true" data-send="true"></div>
										
					'FacebookShare'    => array(
						'key'=>'eventonsm_fbs',
						'counter' =>1,
						'favicon' => 'likecounter.png',
						'url' => '<a class="fb evo_ss" target="_blank" 
							onclick="'.$fb_js.'"
							href="http://www.facebook.com/sharer.php?s=100&p[url]=PERMALINK&p[title]=TITLE&display=popup" data-url="PERMALINK"><i class="fa fa-facebook"></i></a>',
					),
					'Twitter'    => array(
						'key'=>'eventonsm_tw',
						'counter' =>1,
						'favicon' => 'twitter.png',
						'url' => '<a class="tw evo_ss" onclick="'.$tw_js.'" href="http://twitter.com/share?text=TITLECOUNT" title="Share on Twitter" rel="nofollow" target="_blank" data-url="PERMALINK"><i class="fa fa-twitter"></i></a>',
					),
					'LinkedIn'=> array(
						'key'=>'eventonsm_ln',
						'counter'=>1,'favicon' => 'linkedin.png',
						'url' => '<a class="li evo_ss" href="http://www.linkedin.com/shareArticle?mini=true&url=PERMALINK&title=TITLE&summary=SUMMARY" target="_blank"><i class="fa fa-linkedin"></i></a>',
					),
					'Google' => Array (
						'key'=>'eventonsm_gp',
						'counter' =>1,'favicon' => 'google.png',
						'url' => '<a class="gp evo_ss" href="https://plus.google.com/share?url=PERMALINK" 
							onclick="'.$fb_js.'" target="_blank"><i class="fa fa-google-plus"></i></a>'
					),
					'Pinterest' => Array (
						'key'=>'eventonsm_pn',
						'counter' =>1,'favicon' => 'pinterest.png',
						'url' => '<a class="pn evo_ss" href="http://www.pinterest.com/pin/create/button/?url=PERMALINK&media=IMAGEURL&description=SUMMARY"
					        data-pin-do="buttonPin" data-pin-config="above" target="_blank"><i class="fa fa-pinterest"></i></a>'
					),'EmailShare' => Array (
						'key'=>'eventonsm_email',						
						'url' => '<a class="em evo_ss" href="HREF" target="_blank"><i class="fa fa-envelope"></i></a>'
					)
					
				));
				
				$sm_count = 0;
				$output_sm='';
				
				// foreach sharing option
				foreach($social_sites as $sm_site=>$sm_site_val){
					if(!empty($evo_opt[$sm_site_val['key']]) && $evo_opt[$sm_site_val['key']]=='yes'){
						// for emailing
						if($sm_site=='EmailShare'){
							$url = $sm_site_val['url'];
							$href_ = 'mailto:name@domain.com?subject='.$title.'&body='.$encodeURL;
							$url = str_replace('HREF', $href_, $url);

							$link= "<div class='evo_sm ".$sm_site."'>".$url."</div>";
							
							$output_sm.=$link;
							$sm_count++;
						}else{

							// check interest
							if( $sm_site=='Pinterest' && empty($imgurl)) continue;

							$site = $sm_site;
							$url = $sm_site_val['url'];
							
							$url = str_replace('TITLECOUNT', $titleCOUNT, $url);
							$url = str_replace('TITLE', $title, $url);			
							$url = str_replace('PERMALINKCOUNT', $permalinkCOUNT, $url);
							$url = str_replace('PERMALINK', $encodeURL, $url);
							$url = str_replace('SUMMARY', $summary, $url);
							$url = str_replace('IMAGEURL', $imgurl, $url);
							
							$linkitem = '';
							
							$style='';
							$target='';
							$href = $url;
							
							if($sm_site =='FacebookShare'){}
							
							$link= "<div class='evo_sm ".$sm_site."'>".$href."</div>";
							
							$output_sm.=$link;
							$sm_count++;
						}
					}
				}
				
				if($sm_count>0){
					return "<div class='bordb evo_metarow_socialmedia evcal_evdata_row'>
							".$output_sm."<div class='clear'></div>
						</div>";
				}
			}
		
			$eventon->evo_generator->__calendar_type ='default';		
		}

	// ADD : Comments and event excerpt box
		function activate_comments($array){	
			$array[] = 'comments';
			return $array;	
		}	
}