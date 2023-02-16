<?php
/**
 * Front End class for this addon
 *
 * @version 	0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evosr_front{

	function __construct(){
		add_filter('evo_cal_above_header_btn', array($this, 'header_search_button'), 10, 2);
		add_filter('evo_cal_above_header_content', array($this, 'header_search_bar'), 10, 2);
		add_action('evo_cal_footer', array($this, 'remove_search_bar'), 10);

		// scripts and styles 
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	
		add_action('eventon_enqueue_scripts', array($this,'enque_script'));
		
		//shortcodes
		add_filter('eventon_shortcode_defaults', array($this,'add_shortcode_defaults'), 10, 1);	

		add_filter( 'posts_search', array($this, 'advanced_custom_search'), 500, 2 );	
	}

	function list_searcheable_acf(){
	  $list_searcheable_acf = array("title", "evcal_subtitle", "excerpt_short", "excerpt_long");
	  return $list_searcheable_acf;
	}
	function advanced_custom_search( $where, &$wp_query ) {
	    global $wpdb, $eventon;

	    if( empty($eventon->frontend->evo_options['EVOSR_advance_search']) || 
	    	( !empty($eventon->frontend->evo_options['EVOSR_advance_search']) && $eventon->frontend->evo_options['EVOSR_advance_search'] =='no') ) return $where;
	 
	    if ( empty( $where ))  return $where;

	    // restrict this only to event post search
	    if( $wp_query->query['post_type']!= 'ajde_events') return $where;
	 
	    // get search expression
	    $terms = $wp_query->query_vars[ 's' ];
	    
	    // explode search expression to get search terms
	    $exploded = explode( ' ', $terms );
	    if( $exploded === FALSE || count( $exploded ) == 0 )
	        $exploded = array( 0 => $terms );
	         
	    // reset search in order to rebuilt it as we whish
	    $where = '';
	    $tableprefix = $wpdb->prefix;
	    
	    // get searcheable_acf, a list of advanced custom fields you want to search content in
	    $list_searcheable_acf = $this->list_searcheable_acf();
	    foreach( $exploded as $tag ) :
	        $where .= " 
	          AND (
	            (".$tableprefix."posts.post_title LIKE '%$tag%')
	            OR (".$tableprefix."posts.post_content LIKE '%$tag%')
	            OR EXISTS (
	              SELECT * FROM ".$tableprefix."postmeta
		              WHERE post_id = ".$tableprefix."posts.ID
		                AND (";
	        foreach ($list_searcheable_acf as $searcheable_acf) :
	          if ($searcheable_acf == $list_searcheable_acf[0]):
	            $where .= " (meta_key LIKE '%" . $searcheable_acf . "%' AND meta_value LIKE '%$tag%') ";
	          else :
	            $where .= " OR (meta_key LIKE '%" . $searcheable_acf . "%' AND meta_value LIKE '%$tag%') ";
	          endif;
	        endforeach;
		        $where .= ")
	            )
	            OR EXISTS (
	              SELECT * FROM ".$tableprefix."comments
	              WHERE comment_post_ID = ".$tableprefix."posts.ID
	                AND comment_content LIKE '%$tag%'
	            )
	            OR EXISTS (
	              SELECT * FROM ".$tableprefix."terms
	              INNER JOIN ".$tableprefix."term_taxonomy
	                ON ".$tableprefix."term_taxonomy.term_id = ".$tableprefix."terms.term_id
	              INNER JOIN wp_term_relationships
	                ON ".$tableprefix."term_relationships.term_taxonomy_id = ".$tableprefix."term_taxonomy.term_taxonomy_id
	              WHERE (
	          		taxonomy = 'event_location'
	            		OR taxonomy = 'event_organizer'          		
	            		OR taxonomy = 'event_type'
	          		)
	              	AND object_id = ".$tableprefix."posts.ID
	              	AND ".$tableprefix."terms.name LIKE '%$tag%'
	            )
	        )";
	    endforeach;
	    return $where;
	}

	// include search in header section
		function header_search_button($array, $args){
			if(!empty($args['search']) && $args['search']=='yes'){
				global $eventon;
				$opt = $eventon->frontend->evo_options;
				
				if(!empty($opt['EVOSR_showfield']) && $opt['EVOSR_showfield']=='yes'){
					return $array;
				}else{
					$new['evo-search']='';
					$array = array_merge($new, $array);
				}
			}
			return $array;
		}

	// header search bar
		function header_search_bar($array, $args){
			if(!empty($args['search']) && $args['search']=='yes'){
				global $eventon;
				$opt = $eventon->frontend->evo_options;

				$hidden = (!empty($opt['EVOSR_showfield']) && $opt['EVOSR_showfield']=='yes')? '':'hidden';
				ob_start();
				?>					
					<div class='evo_search_bar <?php echo $hidden;?>'>
						<div class='evo_search_bar_in' >
							<input type="text" placeholder='<?php echo eventon_get_custom_language('', 'evoSR_001', 'Search Events');?>' data-role="none"/>
							<a class="evosr_search_btn"><i class="fa fa-search"></i></a>
						</div>
					</div>

				<?php
				$content = ob_get_clean();
				$array['evo-search']= $content;
			}
			return $array;
		}
	// remove search bar
		function remove_search_bar(){
			//remove_filter('evo_cal_above_header_btn',array($this, 'header_search_button'));
			//remove_filter('evo_cal_above_header_content',array($this, 'header_search_bar'));
		}

	//shortcode defaults
		function add_shortcode_defaults($arr){
			return array_merge($arr, array(
				'search'=>'yes',
			));		
		}

	// styles and scripts
		function register_styles_scripts(){
			global $eventon_sr;
			wp_register_style( 'evo_sr_styles',$eventon_sr->assets_path.'styles.css');
			wp_enqueue_style( 'evo_sr_styles');	

			wp_register_script('evo_sr_script',$eventon_sr->assets_path.'script.js', array('jquery'), $eventon_sr->version, true );
			wp_localize_script( 
				'evo_sr_script', 
				'EVOSR_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'evosr_nonce' )
				)
			);			
		}
		function enque_script(){
			wp_enqueue_script('evcal_easing');
			wp_enqueue_script('evo_sr_script');
		}

}
