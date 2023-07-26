<?php
/*
	Event Tickets Admin init
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-tickets/Classes
 * @version     1.3.10
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evotx_admin{	
	private $addon_data;
	private $urls;
	private $evotx_opt;

	public $evotx_email_vals;

	function __construct(){
		add_action('admin_init', array($this, 'evotx_admin_init'));
		add_action('admin_head', array($this, 'evotx_admin_head'));
		include_once('class-meta_boxes.php');
		include_once('class-admin-evo-tix.php');

		// HOOKs		
		$this->evotx_opt = get_option('evcal_options_evcal_tx');
		
		// actions when event moved to trash that have wc product
		add_action('wp_trash_post', array($this, 'move_to_trash'));

		// duplicating events
		add_action('evo_after_duplicate_event', array($this, 'after_event_duplicate'), 10, 2);
		add_action('eventon_duplicate_event_exclude_meta', array($this, 'exclude_duplicate_field'), 10, 1);

		add_action( 'transition_post_status', array($this,'update_wc_status'), 10, 3 );

		// add edit event button to wc product ticket edit page
		add_filter( 'post_submitbox_misc_actions', array($this,'event_edit_button'),10,2 );

		add_action( 'admin_menu', array( $this, 'menu' ),9);
		add_action( 'admin_menu', array( $this, 'order_tix' ),99);	
		add_filter( 'pre_get_posts', array($this,'meta_filter_posts' ));

		// shortcode inclusions
		add_filter('eventon_shortcode_popup',array($this, 'add_shortcode_options'), 10, 1);

	}


	// Initiate admin for tickets addon
		function evotx_admin_init(){

			// set ticket language strings
			$lang = new evotx_lang();
			
			// eventCard inclusion
			add_filter( 'eventon_eventcard_boxes',array($this,'evotx_add_toeventcard_order') , 10, 1);

			// icon in eventon settings
			add_filter( 'eventon_custom_icons',array($this,'evotx_custom_icons') , 10, 1);

			global $pagenow, $typenow, $wpdb, $post;	
			
			if ( $typenow == 'post' && ! empty( $_GET['post'] ) && $post ) {
				$typenow = $post->post_type;
			} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
		        $typenow = get_post_type( $_GET['post'] );
		    } elseif (empty( $typenow ) && ! empty( $_GET['post-type'] ) ) {
		    	$typenow = $_GET['post-type'];
		    }

			if ( $typenow == '' || $typenow == "ajde_events" || $typenow =='evo-tix' || $typenow =='shop_order') {

				// Event Post Only
				$print_css_on = array( 'post-new.php', 'post.php', 'edit.php' );

				foreach ( $print_css_on as $page ){
					add_action( 'admin_print_styles-'. $page, array($this,'evotx_event_post_styles') );		
				}				
			}

			// include ticket id in the search
				if($typenow =='' || $typenow == 'evo-tix' && !wp_doing_ajax()){
					// Filter the search page
					add_filter('pre_get_posts', array($this, 'evotx_search_pre_get_posts'));		
				}

				if($pagenow == 'edit.php' && $typenow == 'evo-tix'){
					add_action( 'admin_print_styles-edit.php', array($this, 'evotx_event_post_styles' ));	
				}

			// for only eventon tickets settings page
				if($pagenow == 'admin.php' && !empty($_REQUEST['page']) && $_REQUEST['page']=='eventon' && !empty($_REQUEST['tab']) && $_REQUEST['tab']=='evcal_tx'){
					$this->evotx_admin_styles();
				}

			// settings
			add_filter('eventon_settings_tabs',array($this,'evotx_tab_array') ,10, 1);
			add_action('eventon_settings_tabs_evcal_tx',array($this,'evotx_tab_content') );
		}

		function evotx_admin_head(){
			global $pagenow,$typenow;
			// disable evo-tix post creation button
			if(!empty($typenow) && $typenow =='evo-tix') echo '<style>h1 .page-title-action{display: none !important;}</style>';
		}
	
	// Shortcode
		function add_shortcode_options($shortcode_array){
			global $evo_shortcode_box;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_TX',
					'name'=>'Buy Ticket Button (Beta)',
					'code'=>'evotx_btn',
					'variables'=>array(
						array(
							'name'=>'<i>NOTE: This standalone buy ticket button can be placed anywhere in your website to prompt a lightbox ticket data to quickly add a ticket to cart.</i>',
							'type'=>'note',
						),
						array(
							'name'=>'Button Text',
							'placeholder'=>'eg. Buy Ticket Now',
							'type'=>'text',
							'var'=>'btn_txt','default'=>'0',
						),array(
							'name'=>'Show event date/time',
							'type'=>'YN',
							'var'=>'date_time',
							'default'=>'no'
						),array(
							'name'=>'Show event location (if any)',
							'type'=>'YN',
							'var'=>'location',
							'default'=>'no'
						),
						array(
							'name'=>'Event ID',
							'type'=>'select','var'=>'id',
							'placeholder'=>'eg. 234',	
							'options'=>	$this->get_event_ids(),
							'guide'=> __('These are the events that have tickets enabled','evotx')	
						),array(
							'name'=>'Repeat Interval ID',
							'type'=>'text',
							'var'=>'ri',
							'placeholder'=>'eg. 2',
							'guide'=>__('Enter the repeat interval instance ID of the event you want to show from the repeating events series (the number at the end of the single event URL)  eg. 3. This is only for repeating events','eventon')
						)
					)
				),
				array(
					'id'=>'s_TX2',
					'name'=>'Show All Attendees (Beta)',
					'code'=>'evotx_attendees',
					'variables'=>array(
						array(
							'name'=>'<i>NOTE: This will display all the attendees of the event on frontend.</i>',
							'type'=>'note',
						),						
						array(
							'name'=>'Event ID',
							'type'=>'select','var'=>'id',
							'placeholder'=>'eg. 234',	
							'options'=>	$this->get_event_ids(),
							'guide'=> __('These are the events that have tickets enabled','evotx')	
						),
						array(
							'name'=>'Repeat Interval ID',
							'type'=>'text',
							'var'=>'ri',
							'placeholder'=>'eg. 2',
							'guide'=>__('Enter the repeat interval instance ID of the event you want to show from the repeating events series (the number at the end of the single event URL)  eg. 3. This is only for repeating events','eventon')
						),array(
							'name'=>'Show event details header',
							'type'=>'YN',
							'var'=>'event_details',
							'default'=>'no'
						)

					)
				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
		}

		function get_event_ids(){
			global $post;
			$backup_post = $post;

			$events = new WP_Query(array(
				'orderby'=>'title','order'=>'ASC',
				'post_type'=> 'ajde_events',
				'posts_per_page'=>-1,
				'meta_key'=>'evotx_tix',
				'meta_value'=>'yes'
			));
			$ids = array();
			$ids['na'] = '--';
			if($events->have_posts()){
				while($events->have_posts()): $events->the_post();
					$id = $events->post->ID;
					$ids[$id] = get_the_title($id).' ('.$id.')';

				endwhile;	
				//$events->reset_postdata();
				wp_reset_postdata();			
			}
			
			$post = $backup_post;
			return $ids;
		}

	// duplication of events
		public function after_event_duplicate($EVENT, $post){
			$pmv = get_post_meta($post->ID);

			// if tickets activated for this event
			if(!empty($pmv['tx_woocommerce_product_id']) && !empty($pmv['evotx_tix']) && $pmv['evotx_tix'][0]=='yes' ){
				$wc_post = get_post($pmv['tx_woocommerce_product_id'][0]);

				$new_event_id = $EVENT->ID;

				// create a duplicate of associated wc tix product for new duplicated event
				$new_wc_id = eventon_create_duplicate_from_event($wc_post);
				//update_post_meta( $new_event_id, 'aaa',$new_wc_id.' '.$new_event_id);
				update_post_meta( $new_event_id, 'tx_woocommerce_product_id',$new_wc_id);
				update_post_meta( $new_wc_id, '_eventid',$new_event_id);

				// set a new sku
				$sku = get_post_meta( $new_wc_id, '_sku',true);
				update_post_meta( $new_wc_id, '_sku', $sku .'_'. ( rand(2000,9999)) );

				// pluggable for event with tickets
				do_action('evotx_after_duplicate_ticket_event', $EVENT, $new_wc_id, $post);
			}
		}
		function exclude_duplicate_field($array){
			$array[] = 'tx_woocommerce_product_id';
			return $array;
		}



	// update associated wc product status along side event post status
		public function update_wc_status( $new_status, $old_status, $post ) {
			if($post->post_type=='ajde_events'){
				$tx_wc_id = get_post_meta($post->ID, 'tx_woocommerce_product_id', true);
				// only events with wc tx product association
				$post_exists = $this->post_exist($tx_wc_id);
				if($tx_wc_id && $post_exists){
					$product = get_post($tx_wc_id, 'ARRAY_A');
					$product['post_status']= $new_status;
					$product['ID']= $tx_wc_id;
					wp_update_post($product);
				}				
			}
		}

	// UPDATE woocommerce ticket product for event
		function update_woocommerce_product($woo_post_id, $post_id){
			global $evotx;

			$user_ID = get_current_user_id();
			$woo_post_id = (int)$woo_post_id;

			if(empty($woo_post_id)) return false;
			
			$post = array(
				'ID'=>$woo_post_id,
				//'post_author' => $user_ID,
				'post_status' => "publish",
				'post_type' => "product",				
			);

			// Update WC product title with event title if set
				if(evo_settings_check_yn($this->evotx_opt, 'evotx_wc_prodname_update' )){
					$title = EVOTX()->functions->get_ticket_product_title( $post_id );
					if($title) $post['post_title'] = $title;
				}

			if(!empty($_REQUEST['_tx_desc']))
				$post['post_content'] = $_REQUEST['_tx_desc'];

			// create woocommerce product
			$woo_post_id = wp_update_post( $post );
			
			//update_post_meta( $post_id, 'tx_woocommerce_product_id', $woo_post_id);
			//wp_set_object_terms( $woo_post_id, $product->model, 'product_cat' );

			wp_set_object_terms($woo_post_id, $_POST['tx_product_type'], 'product_type');		

			$evotx->functions->save_product_meta_values($woo_post_id, $post_id);			
		}
	
		
	// SUPPORT
		// check if post exist for a ID
			function post_exist($ID){
				global $wpdb;

				$post_id = $ID;
				$post_exists = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE id = '" . $post_id . "'", 'ARRAY_A');
				return $post_exists? $post_exists['ID']: false;
			}
		
		
	    // move a event to trash
		    function move_to_trash($post_id){
		    	$post_type = get_post_type( $post_id );
		    	$post_status = get_post_status( $post_id );
		    	if($post_type == 'ajde_events' && in_array($post_status, array('publish','draft','future')) ){
		    		$woo_product_id = get_post_meta($post_id, 'tx_woocommerce_product_id', true);

		    		if(!empty($woo_product_id)){
		    			$__product = array(
		    				'ID'=>$woo_product_id,
		    				'post_status'=>'trash'
		    			);
		    			wp_update_post( $__product );
		    		}	
		    	}
		    }

	// TABS SETTINGS
		function evotx_tab_array($evcal_tabs){
			$evcal_tabs['evcal_tx']='Tickets';		
			return $evcal_tabs;
		}

		function evotx_tab_content(){
			include_once('class-settings.php');
			$settings = new evotx_settings();
			$settings->content();
		}

		

	// GET product type by product ID
		public function get_product_type($id){
			if ( $terms = wp_get_object_terms( $id, 'product_type' ) ) {
				$product_type = sanitize_title( current( $terms )->name );
			} else {
				$product_type = apply_filters( 'default_product_type', 'simple' );
			}
			return $product_type;
		}

	// other hooks
		function evotx_search_pre_get_posts($query){
		    // Verify that we are on the search page that that this came from the event search form
		    if($query->query_vars['s'] != '' && is_search())
		    {
		        // If "s" is a positive integer, assume post id search and change the search variables
		        if(absint($query->query_vars['s']))
		        {
		            // Set the post id value
		            $query->set('p', $query->query_vars['s']);

		            // Reset the search value
		            $query->set('s', '');
		        }
		    }
		}	
		function evotx_event_post_styles(){
			global $evotx;

			wp_enqueue_script('evo_handlebars');
			
			// enque trumbo editor
			wp_enqueue_script( 'evo_wyg_editor_j',EVO()->assets_path.'lib/trumbowyg/trumbowyg.min.js','', EVO()->version, true );
			wp_register_style( 'evo_wyg_editor',EVO()->assets_path.'lib/trumbowyg/trumbowyg.css', '', EVO()->version);

			wp_enqueue_style('evo_wyg_editor');

			wp_enqueue_style( 'evotx_admin_post',$evotx->assets_path.'admin_evotx_post.css');
			wp_enqueue_script( 'evotx_draw_attendees',$evotx->assets_path.'tx_draw_attendees.js','',$evotx->version);
			wp_enqueue_script( 'evotx_admin_post_script',$evotx->assets_path.'tx_admin_post_script.js','',$evotx->version);
			wp_localize_script( 
				'evotx_admin_post_script', 
				'evotx_admin_ajax_script', 
				apply_filters('evotx_admin_localize_data', array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'evotx_nonce' ),
					'text'=> array(
						't1'=> __('Search Attendees ticket id, name, email','evotx'),
						't2'=> __('More Filters','evotx'),
					)
				))
			);

		}
		function evotx_admin_styles(){
			global $evotx;
			wp_enqueue_style( 'evotx_admin_css', EVOTX()->assets_path.'tx_admin.css');
			wp_enqueue_script( 'evotx_admin_script', EVOTX()->assets_path.'tx_admin_script.js');
			wp_localize_script( 
				'evotx_admin_script', 
				'evotx_admin_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'evotx_nonce' )
				)
			);
		}
		// add edit event button to WC product page
			function event_edit_button(){
				global $post;

				if ( function_exists( 'event_edit_button' ) ) return;

				if ( ! is_object( $post ) ) return;

				if ( $post->post_type != 'product' ) return;

				
				// if event ticket category set
					if(has_term('ticket','product_cat', $post)){
						$event_id = get_post_meta($post->ID,'_eventid', true );
						
						if(!empty($event_id)){
						?>
						<div class="misc-pub-section" >
							<div id="edit-event-action"><a class="button" href="<?php echo get_edit_post_link($event_id); ?>"><?php _e( 'Edit Event', 'evotx' ); ?></a></div>
							
						</div>
						<?php
						}	
					}
			}

	// event tickets to eventcard
		function evotx_add_toeventcard_order($array){
			$array['evotx']= array('evotx',__('Event Ticket Box','evotx')); 

			//print_r($array);
			return $array;
		}
	// even tticket eventcard icons
		function evotx_custom_icons($array){
			$array[] = array('id'=>'evcal__evotx_001','type'=>'icon','name'=>'Event Ticket Icon','default'=>'fa-tags');
			return $array;
		}	

	// EventON settings menu inclusion
		function menu(){
			add_submenu_page( 'eventon', 'Tickets', __('Tickets','evotx'), 'manage_eventon', 'admin.php?page=eventon&tab=evcal_tx', '' );
		}
	// add submenu for ticket orders only
		function order_tix(){
			// add submenu page
			add_submenu_page('woocommerce','Ticket Orders', 'Ticket Orders', 'manage_eventon','edit.php?s&post_type=shop_order&meta_key=_order_type&meta_value=evotix');
		}
	// add search parameters to get only event ticket orders
		function meta_filter_posts( $query ) {
			if(!is_admin() ) return $query;

			if( !is_search() ) return $query;

			if( isset( $_GET['post_type'] ) && $_GET['post_type']=='shop_order'
				&& !empty($_GET['meta_value']) && $_GET['meta_value']=='evotix'
			){
				$query->set( 'meta_key', '_order_type' );
				$query->set( 'meta_value', 'evotix' );
			}
			return $query;
		}

		function get_format_time($unix){
			$evcal_opt1 = get_option('evcal_options_evcal_1');
			$date_format = eventon_get_timeNdate_format($evcal_opt1);

			$TIME = eventon_get_editevent_kaalaya($unix, $date_format[1], $date_format[2]);

			return $TIME;
		}
	
}

$GLOBALS['evotx_admin'] = new evotx_admin();



	
