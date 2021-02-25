<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Rev_addon_prevnext_posts
 * @subpackage Rev_addon_prevnext_posts/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rev_addon_prevnext_posts
 * @subpackage Rev_addon_prevnext_posts/public
 * @author     ThemePunch <info@themepunch.com>
 */
class Rev_addon_prevnext_posts_Public {

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
		 * defined in Rev_addon_prevnext_posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rev_addon_prevnext_posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rev_addon_prevnext_posts-public.css', array(), $this->version, 'all' );

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
		 * defined in Rev_addon_prevnext_posts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rev_addon_prevnext_posts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rev_addon_prevnext_posts-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Display Related Posts under Content
	 * Main Function for the display
	 *
	 * @since    1.0.0
	 */
	public function filter_print_posts($content) {
		
		if( !is_single() ) return $content;

		global $post;
		$post_type = get_post_type();
		

		// Get Settings
		$rev_slider_addon_values = array();
		parse_str(get_option('revslider_prevnext_posts_addon'), $rev_slider_addon_values);
		
		//Filter
		$rev_slider_addon_values = apply_filters( 'rev_addon_prevnext_posts_settings_filter', $rev_slider_addon_values );
		if( empty($rev_slider_addon_values['revslider-prevnext-posts-addon-'.$post_type.'-slider']) ) return $content;


		// Build Related Post Shortcode
		$prevnext_posts_shortcode = do_shortcode('[rev_slider alias="'.$rev_slider_addon_values['revslider-prevnext-posts-addon-'.$post_type.'-slider'].'"]');
		
		// Filter
		$prevnext_posts_shortcode = apply_filters( 'rev_addon_prevnext_posts_slider_filter', $prevnext_posts_shortcode );



		// Build Return Value from Shortcode and Content
		if( !empty($rev_slider_addon_values['revslider-prevnext-posts-addon-'.$post_type.'-position']) && $rev_slider_addon_values['revslider-prevnext-posts-addon-'.$post_type.'-position'] == 'top' ){
			$prevnext_posts_shortcode = $prevnext_posts_shortcode.$content;
		}
		else {
			$prevnext_posts_shortcode = $content.$prevnext_posts_shortcode;
		}
		// Filter
		$prevnext_posts_shortcode = apply_filters( 'rev_addon_prevnext_posts_output_filter', $prevnext_posts_shortcode );
		
		return $prevnext_posts_shortcode;
	}

	/**
	 * Get the prevNext Posts
	 *
	 * @since    1.0.0
	 */
	public function get_prevnext_posts($rev_slider_addon_values,$post_type){
		global $post;
		if(!isset($post->ID)) return "";

		//values for search in same tax
		$in_same_term = !empty($rev_slider_addon_values['revslider-prevnext-posts-addon-'.$post_type.'-taxonomy-only']) && !empty($rev_slider_addon_values['revslider-prevnext-posts-addon-'.$post_type.'-taxonomy']) ? true : false;
		$taxonomy = $in_same_term ? $rev_slider_addon_values['revslider-prevnext-posts-addon-'.$post_type.'-taxonomy'] : '';

		if($in_same_term){
			// Get terms for post
			$terms = get_the_terms( $post->ID , $taxonomy );
			// Loop over each item since it's an array
			if ( $terms != null ){
				foreach( $terms as $term ) {
					// Print the name method from $term which is an OBJECT
					$termSlug[] = $term->slug;
					// Get rid of the other data stored in the object, since it's not needed
					unset($term);
				} 
			}

			if($post_type == "post") $taxonomy = "category_name";

			if(is_array($termSlug)) $termSlug = implode(",", $termSlug);
			// get_posts in same custom taxonomy
			$postlist_args = array(
			'posts_per_page' => -1,
			'orderby' => 'menu_order date',
			'order' => 'ASC',
			'post_type' => "$post_type",
			"$taxonomy" => "$termSlug", 
			'post_status' => 'publish'
			);
		}
		else{
			$postlist_args = array(
			'posts_per_page' => -1,
			'orderby' => 'menu_order date',
			'order' => 'ASC',
			'post_type' => "$post_type", // this can be your post type
			'post_status' => 'publish'
			);
		}
			
		$postlist = get_posts( $postlist_args );

		// get ids of posts retrieved from get_posts
		$ids = array();
		foreach ($postlist as $thepost) {
			$ids[] = $thepost->ID;
		}

		// get and echo previous and next post in the same taxonomy
		$thisindex = array_search($post->ID, $ids);
		$previd = isset($ids[$thisindex-1]) ? $ids[$thisindex-1] : '';
		$nextid = isset($ids[$thisindex+1]) ? $ids[$thisindex+1] : '';

		if(!empty($previd)) $nextPrevPosts['prev'] = $previd;
    	if(!empty($nextid)) $nextPrevPosts['next'] = $nextid;
		if(!empty($nextPrevPosts)) {
	        return $nextPrevPosts;
	    }
	    else {
	    	return "";
	    }		
	}

	/**
	 * Grabs the excerpt, no matter what!
	 *
	 * @since    1.0.0
	 */
	function prevnext_get_the_excerpt($post_id){
		if(empty($current_post)) $current_post = get_post($post_id);	
		$excerpt = get_the_excerpt();
		return $excerpt;
	}


	/**
	 * Filters the custom meta placeholders and calls function to replace
	 * @since    1.0.0
	 */
	public function rev_addon_insert_meta($text,$post_id){
		
		//global $post;
		$post_type = get_post_type($post_id);

		//return $post_id;

		// Get Settings
		$rev_slider_addon_values = array();
		parse_str(get_option('revslider_prevnext_posts_addon'), $rev_slider_addon_values);
		//Filter
		$rev_slider_addon_values = apply_filters( 'rev_addon_prevnext_posts_settings_filter', $rev_slider_addon_values );
		//if( empty($rev_slider_addon_values['revslider-prevnext-posts-addon-'.$post_type.'-slider']) ) return $content;

		// Get Related Posts
		$prevnext_posts = $this->get_prevnext_posts($rev_slider_addon_values,$post_type);
		// Filter
		$prevnext_posts = apply_filters( 'rev_addon_prevnext_posts_filter', $prevnext_posts );

		$text = $this->replace_placeholders($text,$prevnext_posts,'next');
		$text = $this->replace_placeholders($text,$prevnext_posts,'prev');
	
		return $text;
	}

	/**
	 * Replace the custom meta placeholders
	 *
	 * @since    1.0.0
	 */
	public function replace_placeholders($text,$post_id,$prevnext_text){
		
		if(empty($post_id[$prevnext_text])){
			$text = str_replace(
					array(
						'{{'.$prevnext_text.'_title}}',
						'{{'.$prevnext_text.'_excerpt}}',
						'{{'.$prevnext_text.'_link}}',
						'{{'.$prevnext_text.'_author_name}}',
						'{{'.$prevnext_text.'_date}}',
						'{{'.$prevnext_text.'_alias}}',
						'{{'.$prevnext_text.'_date_modified}}',
						'{{'.$prevnext_text.'_catlist}}',
						'{{'.$prevnext_text.'_catlist_raw}}' 
					), 
					'',
					$text
			);
			$matches = null;
			$contents = preg_match_all('/\\{\\{'.$prevnext_text.'\\_image\\_.*?\\}\\}/', $text, $matches);
			if($contents){
				foreach ($matches as $content) {
					$text = str_replace($content,'',$text);
				}
			}
			return $text;
		}
		else{
			$post_id = $post_id[$prevnext_text];
		}

		$author_display_name = "";
		if(strpos($text, "author_name") !== false){
			$current_post = get_post($post_id);	
			$author_id= $current_post->post_author;
			$author_display_name = get_the_author_meta("display_name",$author_id);
		}

		$excerpt = "";
		if(strpos($text, "excerpt") !== false){
			if(empty($current_post)) $current_post = get_post($post_id);	
			$excerpt = get_the_excerpt();
		}

		$contents = preg_match_all('/\\{\\{content.*?\\}\\}/', $text, $matches);
		if($contents){
			foreach ($matches as $content) {
				$content_replace = $content[0];
				$content_split = explode(":", $content[0]);
				if(isset($content_split[1]) && $content_split[1]=="words"){
					if(empty($current_post)) $current_post = get_post($post_id);	
					$mycontent = wp_strip_all_tags($current_post->post_content,true);
					$mycontent = wp_trim_words( $mycontent , str_replace("}}", "", $content_split[2]));
					$text = str_replace($content_replace,str_replace("}}", "", $mycontent),$text);	
				}
				else{
					if(empty($current_post)) $current_post = get_post($post_id);
					$mycontent = wp_strip_all_tags($current_post->post_content,true);
					$mycontent = substr( $mycontent , 0, str_replace("}}", "", $content_split[2]));
					$text = str_replace($content_replace,str_replace("}}", "", $mycontent),$text);		
				}
			}
		}

		$text = str_replace(
					array(
						'{{'.$prevnext_text.'_title}}',
						'{{'.$prevnext_text.'_excerpt}}',
						'{{'.$prevnext_text.'_link}}',
						'{{'.$prevnext_text.'_author_name}}',
						'{{'.$prevnext_text.'_date}}',
						'{{'.$prevnext_text.'_alias}}',
						'{{'.$prevnext_text.'_date_modified}}',
						'{{'.$prevnext_text.'_catlist}}',
						'{{'.$prevnext_text.'_catlist_raw}}' 
					), 
					array(
						get_the_title($post_id), 
						$excerpt,
						get_permalink($post_id),
						$author_display_name,
						get_the_date(get_option('date_format'), $post_id  ) ,
						get_post_field( 'post_name', get_post($post_id) ) ,
						get_the_time( get_option('date_format'), $post_id ) ,
						wp_strip_all_tags(get_the_category_list( ',', '', $post_id )),
						wp_strip_all_tags(get_the_category_list( ',', '', $post_id ))
					),
					$text
		);

		$matches = null;
		$contents = preg_match_all('/\\{\\{'.$prevnext_text.'\\_image\\_.*?\\}\\}/', $text, $matches);
		
		if($contents){
			foreach ($matches as $content) {
				$size = str_replace(array($prevnext_text."_image_","_url","{{","}}"), array("","","",""), $content);
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size[0] );
				$text = str_replace($content,$image[0],$text);
			}
		}

		return $text;
	}


}
