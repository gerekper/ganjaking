<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Rev_addon_rel_posts
 * @subpackage Rev_addon_rel_posts/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rev_addon_rel_posts
 * @subpackage Rev_addon_rel_posts/public
 * @author     ThemePunch <info@themepunch.com>
 */
class Rev_addon_rel_posts_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rev_addon_rel_posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rev_addon_rel_posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rev_addon_rel_posts-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rev_addon_rel_posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rev_addon_rel_posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rev_addon_rel_posts-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Display Related Posts under Content
	 * Main Function for the display
	 *
	 * @since    1.0.0
	 */
	public function filter_print_related_posts($content) {
		
		if( !is_single() ) return $content;

		global $post;
		$post_type = get_post_type();

		// Get Settings
		$rev_slider_addon_values = array();
		parse_str(get_option('revslider_rel_posts_addon'), $rev_slider_addon_values);
		
		//Filter
		$rev_slider_addon_values = apply_filters( 'rev_addon_rel_posts_settings_filter', $rev_slider_addon_values );
		
		if( empty($rev_slider_addon_values['revslider-rel-posts-addon-'.$post_type.'-slider']) ) return $content;

		// Check for Cached result
		$rev_slider_addon_transient_name = 'rev_addon_rel_posts_post_id_' . $post->ID;
		
		if ($rev_slider_addon_values['revslider-rel-posts-addon-'.$post_type.'-caching'] > 0 && false !== ($data = get_transient( $rev_slider_addon_transient_name)))
			return ($data); //return cached values if stored
		
		// Get Related Posts
		$related_posts = $this->get_related_posts($rev_slider_addon_values);
		// Filter
		$related_posts = apply_filters( 'rev_addon_rel_posts_filter', $related_posts );

		// Check number Related Posts
		if( !is_array($related_posts) || !sizeof($related_posts) ) return $content;
			
		// Build Related Post Shortcode
		$related_posts_shortcode = do_shortcode('[rev_slider alias="'.$rev_slider_addon_values['revslider-rel-posts-addon-'.$post_type.'-slider'].'"][gallery ids=",'.implode(",", $related_posts).'"][/rev_slider]');

		// Filter
		$related_posts_shortcode = apply_filters( 'rev_addon_rel_posts_slider_filter', $related_posts_shortcode );
		
		// Build Return Value from Shortcode and Content
		if( !empty($rev_slider_addon_values['revslider-rel-posts-addon-'.$post_type.'-position']) && $rev_slider_addon_values['revslider-rel-posts-addon-'.$post_type.'-position'] == 'top' ){
			$related_posts_shortcode = $related_posts_shortcode.$content;
		}
		else {
			$related_posts_shortcode = $content.$related_posts_shortcode;
		}
		// Filter
		$related_posts_shortcode = apply_filters( 'rev_addon_rel_posts_output_filter', $related_posts_shortcode );
		
		set_transient( $rev_slider_addon_transient_name, $related_posts_shortcode, $rev_slider_addon_values['revslider-rel-posts-addon-'.$post_type.'-caching'] );

		return $related_posts_shortcode;
	}

	/**
	 * Get the related Posts
	 *
	 * @since    1.0.0
	 */
	public function get_related_posts($rev_slider_addon_values){
		global $post;
		$related_posts = "";

		$post_type = get_post_type();

		// Start Related Posts by selected taxonomy
		$related_posts = $this->get_related_posts_by_tax($post->ID,$rev_slider_addon_values['revslider-rel-posts-addon-'.$post_type.'-number'],'',$post_type,$rev_slider_addon_values['revslider-rel-posts-addon-'.$post_type.'-start-with']);

		// Number of Posts to display
		$needed_posts = $rev_slider_addon_values['revslider-rel-posts-addon-'.$post_type.'-number'] - sizeof($related_posts);

		// Fill Related Posts
		if(!is_array($related_posts) || $needed_posts){
			$related_posts_list = implode(",", $related_posts);
			if( !empty($related_posts_list) ) $related_posts_list = ',' . $related_posts_list;
			switch ($rev_slider_addon_values['revslider-rel-posts-addon-'.$post_type.'-fill-with']) {
				//fill related posts with posts with the following similarities
				case 'recent':
					$related_posts = array_merge($related_posts,$this->get_recent_posts($post->ID,$needed_posts,$related_posts_list,$post_type));
					break;
				case 'random':
					$related_posts = array_merge($related_posts,$this->get_random_posts($post->ID,$needed_posts,$related_posts_list,$post_type));
					break;
				case 'popular':
					$related_posts = array_merge($related_posts,$this->get_popular_posts($post->ID,$needed_posts,$related_posts_list,$post_type));
					break;
				default: // Taxonomy
					$related_posts = array_merge($related_posts,$this->get_related_posts_by_tax($post->ID,$needed_posts,$related_posts_list,$post_type,$rev_slider_addon_values['revslider-rel-posts-addon-'.$post_type.'-fill-with']));
					break;
			}
		}	
		return $related_posts;
	}

	/**
	 * Get Related Posts by Taxonomy
	 *
	 * @since    1.0.0
	 */
	public function get_related_posts_by_tax($post_id,$max_posts,$already_related_posts_ids='',$post_type="post",$taxonomy=""){
		$cat_related_post_id_array = array();
		$terms = get_the_terms( $post_id, $taxonomy );
		$terms_slugs = array();
		if(is_array($terms)){
			foreach($terms as $term){
				$terms_slugs[] = $term->slug;
			}
		}

		$already_related_posts_ids = empty($already_related_posts_ids) ? '' : ','.$already_related_posts_ids;

		$args = array(
	    	'post_type'			=> $post_type,
	    	'post__not_in' 		=> 	explode(',', $post_id . $already_related_posts_ids) ,
	    	'tax_query' => array(
								array(
									'taxonomy' => $taxonomy,
									'field' => 'slug',
									'terms' => $terms_slugs
								)
							),
	    	'numberposts' 		=>	$max_posts ,
	    	'post_status'		=>  'published',
	    	'suppress_filters'	=>	0
	    );

		$cat_related_posts = get_posts($args);

        $cat_related_post_id_array = array();

        if(is_array($cat_related_posts)){
        	 foreach ($cat_related_posts as $cat_related_post) {
                $cat_related_post_id_array[]=$cat_related_post->ID;
            }
        }
        return $cat_related_post_id_array;
	}

	/**
	 * Get Recent Posts
	 *
	 * @since    1.0.0
	 */
	public function get_recent_posts($post_id,$max_posts,$already_related_posts_ids='',$post_type="post"){
		$already_related_posts_ids = empty($already_related_posts_ids) ? '' : ','.$already_related_posts_ids;
		
		$args = array(
			'post_type'			=> $post_type,
	    	'post__not_in' 		=> 	explode(',', $post_id . $already_related_posts_ids) ,
	    	'numberposts' 		=>	$max_posts ,
	    	'suppress_filters'	=>	0
	    );

		$recent_posts = get_posts($args);
		$recent_post_id_array = array();
		
		if(is_array($recent_posts)){
        	 foreach ($recent_posts as $recent_post) {
                $recent_post_id_array[]=$recent_post->ID;
            }
        }

        return $recent_post_id_array;
	}

	/**
	 * Get Popular Posts (most comments)
	 *
	 * @since    1.0.0
	 */
	/**
	 * Get Recent Posts
	 *
	 * @since    1.0.0
	 */
	public function get_popular_posts($post_id,$max_posts,$already_related_posts_ids='',$post_type="post"){
		$already_related_posts_ids = empty($already_related_posts_ids) ? '' : ','.$already_related_posts_ids;
		
		$args = array(
			'post_type'			=> $post_type,
	    	'post__not_in' 		=> 	explode(',', $post_id . $already_related_posts_ids) ,
	    	'numberposts' 		=>	$max_posts ,
	    	'suppress_filters'	=>	0, 
	    	'orderby'			=> 'comment_count'
	    );

		$popular_posts = get_posts($args);
		$popular_post_id_array = array();
		
		if(is_array($popular_posts)){
        	 foreach ($popular_posts as $popular_post) {
                $popular_post_id_array[]=$popular_post->ID;
            }
        }

        return $popular_post_id_array;
	}

	/**
	 * Get Random Posts
	 *
	 * @since    1.0.0
	 */
	public function get_random_posts($post_id,$max_posts,$already_related_posts_ids='',$post_type="post"){
		$already_related_posts_ids = empty($already_related_posts_ids) ? '' : ','.$already_related_posts_ids;
		
		$args = array(
			'post_type'			=> $post_type,
	    	'post__not_in' 		=> 	explode(',', $post_id . $already_related_posts_ids),
	    	'numberposts' 		=>	$max_posts ,
	    	'suppress_filters'	=>	0,
	    	'orderby'			=> 'rand',
	    	'order'    => 'ASC'
	    );

		$random_posts = get_posts($args);
		$random_post_id_array = array();
		
		if(is_array($random_posts)){
        	 foreach ($random_posts as $random_post) {
                $random_post_id_array[]=$random_post->ID;
            }
        }

        return $random_post_id_array;
	}

}
