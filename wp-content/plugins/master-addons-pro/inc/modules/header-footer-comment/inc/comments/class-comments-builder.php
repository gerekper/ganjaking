<?php 
namespace MasterHeaderFooter\Inc\Comments;
use  MasterHeaderFooter\Inc\Comments\Addon\Master_Addons_Comments;

defined( 'ABSPATH' ) || exit;


if( !class_exists('JLTMA_Comments_Builder') ){

	class JLTMA_Comments_Builder{

		private static $_instance = null;

        public $jltma_set_var;

        private $settings;

		public function __construct( array $settings=[] ){
			add_action( 'init', [$this, 'jltma_enable_comments_custom_post_type'], 11 );
			add_filter( 'wp_insert_post_data', [$this, 'jltma_comments_on_by_default'] );
			
			// Remove Clickable Comment Links
			remove_filter('comment_text', 'make_clickable', 9);
			add_filter('pre_comment_content', [$this, 'jltma_strip_comment_links' ]);

			add_action('comment_post', array($this, 'jltma_save_comment_meta_data'));

			
            add_action('wp_ajax_jltma_like_dislike', array($this, 'jltma_like_dislike_action')); 
            add_action('wp_ajax_nopriv_jltma_like_dislike', array($this, 'jltma_like_dislike_action'));

			// Comment Pagination Ajax
            add_action('wp_ajax_jltma_comment_pagination', array($this, 'jltma_comment_pagination'));
            add_action('wp_ajax_nopriv_jltma_comment_pagination', array($this, 'jltma_comment_pagination'));

			// Loadmore Comment Pagination Ajax
            // add_action('wp_ajax_jltma_loadmore_comments', array($this, 'jltma_loadmore_comments'));
            // add_action('wp_ajax_nopriv_jltma_loadmore_comments', array($this, 'jltma_loadmore_comments'));

            add_action( 'elementor/frontend/before_register_styles', [$this, 'jltma_comments_frontend_styles'] );
            add_action( 'elementor/frontend/before_register_scripts', [$this, 'jltma_comments_frontend_scripts'] );
            // add_action( 'elementor/preview/enqueue_scripts', [ $this, 'jltma_comments_preview_scripts' ] );

			add_action( 'elementor/widgets/widgets_registered', [ $this, 'jltma_register_comments_widget' ] );

			add_filter( 'plugin_row_meta', array( $this, 'jltma_plugin_row_meta' ), 10, 2 );

            $this->jltma_set_var = $settings;
            
            //Extra Comment Fields
            add_action( 'comment_form_after_fields', [ $this,'jltma_build_input_settings'],10,2 );
            add_action( 'add_meta_boxes_comment', [ $this, 'jltma_comment_add_meta_box'] );
            add_action( 'edit_comment', [$this, 'jltma_comment_edit_comment'] );
            add_action( 'comment_post', [$this, 'jltma_comment_insert_comment'], 10, 1 );
            add_filter( 'comment_text', array($this, 'render_comment_meta_front'),10,2);


            // Remove Autop on Comment Text
            add_filter( 'comment_text', 'wptexturize'            );
            add_filter( 'comment_text', 'convert_chars'          );
            add_filter( 'comment_text', 'make_clickable',      9 );
            add_filter( 'comment_text', 'force_balance_tags', 25 ); 
            add_filter( 'comment_text', 'convert_smilies',    20 );
            add_filter( 'comment_text', 'wpautop',            30 );

            //Check SPAM Protection reCaptcha
            add_action('pre_comment_on_post', [$this,'jltma_verify_google_recaptcha']);
            
            // Unset Default Fields
            // add_action('comment_form_default_fields', [$this,'jltma_default_comment_fields']);
		}

        public function jltma_default_comment_fields($fields){
            $jltma_comment_fields = $this->jltma_set_var;
            if(isset($jltma_comment_fields['jltma_comment_fields_url_display']) && $jltma_comment_fields['jltma_comment_fields_url_display'] =="show"){

            if(isset($fields['url']))
                unset($fields['url']);
            }
            return $fields;
        }


        public function render_comment_meta_front( $jltma_comment_text, $comment ){

            $comment_meta = get_option('jltma_comments');
            $comment_content = $comment->comment_content;

            $comment_extra ="";

            if(isset($comment_meta) && !empty($comment_meta)){

                if( is_admin()){
                    $jltma_extra_field_heading = esc_html__('Extra Fields:', JLTMA_TD);
                    $comment_extra .= '<p><strong>'.sprintf(__('%s'), $jltma_extra_field_heading).'</strong></p>';
                }
                
                $comment_extra .= '<ul>';

                foreach ($comment_meta as $key => $value) {

                    $label_name         = $value['label_name'];
                    $field_type         = $value['field_type'];
                    $required           = $value['required'];

                    $unique_field_id    = strtolower(str_replace(" ", "_",$label_name));
                    $jltma_field_value = 'jltma_' . $unique_field_id;

                    if(is_admin()){
                        $jltma_comment_extra_value = get_comment_meta( get_comment_ID(), $jltma_field_value, true);
                    }else{
                        $jltma_comment_extra_value = get_comment_meta( $comment->comment_ID, $jltma_field_value, true );
                    }

                    if($jltma_comment_extra_value != ''){
                        $comment_extra .= '<li><strong>'.sprintf(__('%s: '), esc_attr($label_name)).'</strong>';
                        $comment_extra .= sprintf(__('%s'), esc_attr($jltma_comment_extra_value)).'</li>';
                    }
                }

                $comment_extra .= '</ul>';

            }
            
            $jltma_comment_text = $comment_content . $comment_extra;

            return $jltma_comment_text;


        }


        public function jltma_comment_insert_comment( $comment_id ){

            $comment_meta = get_option('jltma_comments');

            if(isset($comment_meta )){
                foreach ($comment_meta as $key => $value) {
                    
                    $label_name         = $value['label_name'];
                    $field_type         = $value['field_type'];
                    $required           = $value['required'];

                    $unique_field_id    = strtolower(str_replace(" ", "_",$label_name));
                    
                    $jltma_field_value = 'jltma_' . $unique_field_id;

                    if( isset( $_POST[$jltma_field_value] ) )
                        update_comment_meta( $comment_id, $jltma_field_value, esc_attr( $_POST[$jltma_field_value] ) );
                }
            }



        }


        public function jltma_comment_edit_comment( $comment_id ){
            if( ! isset( $_POST['jltma_comment_update'] ) || ! wp_verify_nonce( $_POST['jltma_comment_update'], 'jltma_comment_update' ) ) return;

            $comment_meta = get_option('jltma_comments');

            if(isset($comment_meta )){
                foreach ($comment_meta as $key => $value) {

                    $label_name         = $value['label_name'];
                    $field_type         = $value['field_type'];
                    $required           = $value['required'];

                    $unique_field_id    = strtolower(str_replace(" ", "_",$label_name));
                    $jltma_field_value = 'jltma_' . $unique_field_id;

                    if( isset( $_POST[$jltma_field_value] ) )
                        update_comment_meta( $comment_id, $jltma_field_value, esc_attr( $_POST[$jltma_field_value] ) );
                }
            }
        }

        
        public function jltma_comment_add_meta_box( $comment ){

            add_meta_box( 'jltma-comment-extra-fields', esc_html__( 'Extra Comment Fields', JLTMA_TD ), [$this, 'jltma_comment_meta_box_cb'], 'comment', 'normal', 'high' );

        }

        public function fixObject (&$object){

            if (!is_object ($object) && gettype ($object) == 'object')
                return ($object = unserialize (serialize ($object)));
            return $object;
            
        }

        public function jltma_comment_meta_box_cb( $comment ){
            
            wp_nonce_field( 'jltma_comment_update', 'jltma_comment_update', false );

            $comment_meta = get_option('jltma_comments');

            if(isset($comment_meta )){
                foreach ($comment_meta as $key => $value) {

                    $label_name         = $value['label_name'];
                    $field_type         = $value['field_type'];
                    $required           = $value['required'];

                    $unique_field_id    = strtolower(str_replace(" ", "_",$label_name));
                    $jltma_field_value = get_comment_meta( $comment->comment_ID, 'jltma_' . $unique_field_id, true );

                    if ($field_type == 'text') {
                        echo '<p>
                                <label for="jltma_'. $unique_field_id .'">' . $label_name . '</label>
                                <input type="text" name="jltma_'. $unique_field_id .'" value="' . $jltma_field_value . '" class="widefat" />
                            </p>';
                    }
                }
            }
        }



        public function jltma_build_input_settings(){
            
            $jltma_comment_fields = $this->jltma_set_var;

            if(isset($jltma_comment_fields['jltma_comment_extra_fields_items'] )){
                foreach ($jltma_comment_fields['jltma_comment_extra_fields_items'] as $key => $value) {

                    $label_name         = $value['label_name'];
                    $field_type         = $value['field_type'];
                    $display_label      = $value['display_label'];
                    $placeholder        = $value['placeholder'];
                    $error_msg          = $value['error_msg'];
                    $required           = $value['required'];
                    // $checkbox_options   = $value['checkbox_options'];

                    $unique_field_id    = strtolower(str_replace(" ", "_",$label_name));
                    

                    if($required == 'yes'){
                        $required = 'true';
                        $required_label = __(' <span class="required">*</span>', JLTMA_TD);
                    }else{
                        $required = 'false';
                        $required_label = '';
                    }
                    

                    // Render Field Types

                    if ($field_type == 'text') {

                        $jltma_cmnt_extra_label_container = "";
                        if ($display_label == "show") {
                            $label_name = ($label_name!= '') ? esc_attr($label_name) :'';
                            $required_labels = isset( $required_label ) ? $required_label :"";

                            $jltma_cmnt_extra_label_container = '<div class="jltma-name-div">  
                                    <label>' . $label_name . $required_labels . '</label>
                                </div>';
                        }

                        $jltma_cmnt_extra_placeholder = ($placeholder!='') ? esc_attr($placeholder) : '';

                        $jltma_cmnt_extra_ft = (isset( $value['jltma_field_type']) && $value['jltma_field_type'] ) ? $value['jltma_field_type'] : "text";

                        echo '<div class="jltma-name-value-div jltma-'. $unique_field_id .'">
                                ' . $jltma_cmnt_extra_label_container . '
                            <div class="jltma-value-div">
                                <input class="form-control" type="text" name="jltma_'. $unique_field_id .'" id="'. $unique_field_id .'" value="" placeholder="' . $jltma_cmnt_extra_placeholder . '"  aria-required="'.$required.'"/>
                            </div>
                        </div>';
                    }

                    
                    if ($field_type == 'textarea') {

                        $jltma_cmnt_extra_label_container = "";
                        if ($display_label == "show") {
                            $label_name = ($label_name!= '') ? esc_attr($label_name) :'';
                            $required_labels = isset( $required_label ) ? $required_label :"";

                            $jltma_cmnt_extra_label_container = '<div class="jltma-name-div">  
                                    <label>' . $label_name . $required_labels . '</label>
                                </div>';
                        }
                        
                        $jltma_cmnt_extra_placeholder = ($placeholder!='') ? esc_attr($placeholder) : '';


                        echo '<div class="jltma-name-value-div jltma-'. $unique_field_id .'">
                                ' . $jltma_cmnt_extra_label_container . '
                            <div class="jltma-value-div">
                                <textarea class="form-control" name="jltma_'. $unique_field_id .'" id="'. $unique_field_id .'" rows="4" cols="50" placeholder="' . $jltma_cmnt_extra_placeholder .  '" aria-required="'.$required.'"></textarea>
                            </div>
                        </div>';
                    }

                    
                    if ($field_type == 'checkbox') {

                        $jltma_cmnt_extra_label_container = "";
                        if ($display_label == "show") {
                            $label_name = ($label_name!= '') ? esc_attr($label_name) :'';
                            $required_labels = isset( $required_label ) ? $required_label :"";

                            $jltma_cmnt_extra_label_container = '<div class="jltma-name-div">  
                                    <label>' . $label_name . $required_labels . '</label>
                                </div>';
                        }

                        $jltma_cmnt_extra_placeholder = ($placeholder!='') ? esc_attr($placeholder) : '';

                        echo '<div class="jltma-name-value-div jltma-'. $unique_field_id .'">
                                ' . $jltma_cmnt_extra_label_container . '
                            <div class="jltma-value-div mb-4 mt-2">
                                
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="jltma_'. $unique_field_id .'" id="'. $unique_field_id .'">
                                    <label class="form-check-label" for="jltma_' . strtolower($label_name) . '"> '.esc_attr($label_name).'  &nbsp;</label>
                                </div>
                            </div>
                        </div>';

                    }
                    
                    update_option('jltma_comments', $jltma_comment_fields['jltma_comment_extra_fields_items'] );
                }
            }

        }



        public static function jltma_get_post_settings( $settings ) {

            $extra_fields_items = $settings['jltma_comment_extra_fields_items'];

            foreach ($extra_fields_items as $key => $value) {
                $post_args['title']                 = $value['title'];
                $post_args['label_name']            = $value['label_name'];
                $post_args['field_type']            = $value['field_type'];
                $post_args['placeholder']           = $value['placeholder'];
                $post_args['error_msg']             = $value['error_msg'];
                $post_args['required']              = $value['required'];
            }

            return $post_args;
        }


		public function jltma_plugin_row_meta( $links, $file ){
            if ( strpos( $file, 'wp-comment-designer-lite.php' ) !== false ) {
                $new_links = array(
                    'demo' => '<a href="' . esc_url('https://master-addons.com') . '" target="_blank"><span class="dashicons dashicons-welcome-view-site"></span>Live Demo</a>',
                    'doc' => '<a href="'. esc_url('https://master-addons.com') .'" target="_blank"><span class="dashicons dashicons-media-document"></span>Documentation</a>',
                    'support' => '<a href="https://master-addons.com/contact-us" target="_blank"><span class="dashicons dashicons-admin-users"></span>Support</a>',
                    'pro' => '<a href="'. esc_url('https://master-addons.com') .'" target="_blank"><span class="dashicons dashicons-cart"></span>Premium version</a>'
                );
                $links = array_merge( $links, $new_links );
            }
            return $links;
		}


		public function jltma_loadmore_comments(){
			global $post, $wpdb;
			$post = get_post( $_POST['post_id'] );
			setup_postdata( $post );
		 
			// actually we must copy the params from wp_list_comments() used in our theme
			$comments_list = wp_list_comments( array(
				'avatar_size' 	=> 100,
				'page' 			=> $_POST['jc_page'], // current comment page
				'per_page' 		=> get_option('comments_per_page'),
				'short_ping' 	=> true
			) );

			$this->jltma_list_comments( $comments_list, $class="", $css="", $template="style_one", $settings="");

			die; // don't forget this thing if you don't want "0" to be displayed
		}


        public function jltma_comments_preview_scripts(){
            wp_enqueue_style('jltma-comments', JLTMA_PLUGIN_URL . 'assets/css/jltma-comments.css', array(), JLTMA_VERSION);
            wp_enqueue_script( 'jltma-comments', JLTMA_PLUGIN_URL . 'assets/js/jltma-comments.js', array( 'jquery' ), JLTMA_VERSION, true );
        }

        // CSS
		public function jltma_comments_frontend_styles(){
            wp_register_style('jltma-comments', JLTMA_PLUGIN_URL . 'assets/css/jltma-comments.css', array(), JLTMA_VERSION);
        }


        // JS
        public function jltma_comments_frontend_scripts(){

			wp_register_script( 'jltma-comments', JLTMA_PLUGIN_URL . 'assets/js/jltma-comments.js', array( 'jquery' ), JLTMA_VERSION, true );
			
            // if ( !empty($jltma_api_settings['recaptcha_site_key']) and !empty($jltma_api_settings['recaptcha_secret_key']) ) {
            wp_register_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js', ['jquery'], null, true );
            // }

			$jc_page = get_query_var('cpage') ? get_query_var('cpage') : 1;

            $localize_comments_data = array(
            	'ajax_url' 				=> admin_url('admin-ajax.php'),
                'ajax_nonce' 			=> wp_create_nonce('jltma_frontend_ajax_nonce'),
                'empty_comment'			=> esc_html__('Comment cannot be empty',JLTMA_TD),
                'page_number_loader' 	=> JLTMA_PLUGIN_URL . '/assets/images/ajax-loader.gif',
                'parent_post_id'		=> get_the_ID(),
                'jc_page' 				=> $jc_page
            );
            wp_localize_script('jltma-comments', 'jltma_localize_comments_data', $localize_comments_data);
		}


        function jltma_like_dislike_action($args) {
            if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'jltma_frontend_ajax_nonce')) {

                $comment_id = intval(sanitize_text_field($_POST['comment_id']));
                if (!empty($comment_id)) {
                    $type = sanitize_text_field($_POST['type']);

                    $jltma_like_cookie = sanitize_text_field($_POST['jltma_like_cookie']);
                    $jltma_dislike_cookie = sanitize_text_field($_POST['jltma_dislike_cookie']);

                    $total_like_count = get_comment_meta($comment_id, 'jltma_like_count', true);
                    $total_dislike_count = get_comment_meta($comment_id, 'jltma_dislike_count', true);

                    $total_like_count=(empty($total_like_count)? 0 : $total_like_count);
                    $total_dislike_count=(empty($total_dislike_count)? 0 : $total_dislike_count);
                    
                    if ($type == 'like') {
                        $total_like_count = $total_like_count + 1;
                        if(!empty($jltma_dislike_cookie)){
                            $total_dislike_count = ($total_dislike_count - 1);
                            if ($total_dislike_count < 0){
                                $total_dislike_count = 0;
                            }
                        }
                        $check = update_comment_meta($comment_id, 'jltma_like_count', $total_like_count);
                        if ($check) {
                            update_comment_meta($comment_id, 'jltma_dislike_count', $total_dislike_count);
                            $total_like_count = self::jltma_number_format($total_like_count);
                            $total_dislike_count = self::jltma_number_format($total_dislike_count);
                            $response_array = array('success' => true, 'latest_like_count' => $total_like_count, 'latest_dislike_count' => $total_dislike_count);
                        }
                        else{
                            $response_array = array('success' => false, 'latest_like_count' => '');
                        }
                    }
                    if ($type == 'dislike') {
                        $total_dislike_count = $total_dislike_count + 1;
                        if(!empty($jltma_like_cookie)){
                            $total_like_count = ($total_like_count -1);
                            if ($total_like_count < 0){
                                $total_like_count= 0;
                            }
                        }
                        $check = update_comment_meta($comment_id, 'jltma_dislike_count', $total_dislike_count);
                        if ($check) {
                            update_comment_meta($comment_id, 'jltma_like_count', $total_like_count);
                            $total_like_count = self::jltma_number_format($total_like_count);
                            $total_dislike_count = self::jltma_number_format($total_dislike_count);
                            $response_array = array('success' => true, 'latest_like_count' => $total_like_count, 'latest_dislike_count' => $total_dislike_count);
                        }
                        else{
                            $response_array = array('success' => false, 'latest_dislike_count' =>'');
                        }
                    }
                }
                echo json_encode($response_array);
                die();
            }
        }


		
		// Save Comments Meta data
        function jltma_save_comment_meta_data($comment_id) {

            add_comment_meta($comment_id, 'jltma_like_count', 0);
            add_comment_meta($comment_id, 'jltma_dislike_count', 0);
        }


		public function jltma_strip_comment_links($content) {

		    global $allowedtags;

		    $tags = $allowedtags;
		    unset($tags['a']);
		    $content = addslashes(wp_kses(stripslashes($content), $tags));

		    return $content;
		}


		// Allow Comments for Master Template by default
		public function jltma_comments_on_by_default( $data ) {
		    if( $data['post_type'] == 'master_template' ) {
		        $data['comment_status'] = 'open';
		    }
		    return $data;
		}

		//Enable Comments for Master Template
		public function jltma_enable_comments_custom_post_type() {
			add_post_type_support( 'master_template', 'comments' );
		}

        public function jltma_comment_pagination( $settings ) {
        	global $post;
            if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'jltma_frontend_ajax_nonce')) {

                $page_number 	= intval(sanitize_text_field($_POST['page_number']));
                $post_id 		= intval(sanitize_text_field($_POST['post_id']));
                $ajax_template 	= sanitize_text_field($_POST['template']);
                $sort_type 		= 'default';

                $this->jltma_comment_pagination_inner( $comment_listing, $page_number, $post_id, $settings);
                die();
            }
        }


        public function jltma_comment_pagination_inner( $comment_listing, $page_number, $post_id, $settings){
			
        	global $wpdb;

			$page_number 	 = empty($page_number) ? 1 : $page_number;
			$template 		 = ($settings['jltma_comment_style_preset']) ? esc_attr($settings['jltma_comment_style_preset']) : 'style_one';
			$pagination 	 = ($settings['jltma_comment_pagination'] == "yes") ? esc_attr($settings['jltma_comment_pagination']): 'yes';
			$items_per_page  = ( $settings['jltma_comment_pagination_items']['size']) ? esc_attr($settings['jltma_comment_pagination_items']['size']) : '2';
			$pagination_type = 'page_number';
			$sort_type 		 = "default";

		?>

			<div class="jltma-comment-list-inner">
			    <?php
				    $db_table_name = $wpdb->prefix . "comments";
				    $comment_listing = self::jltma_recursive_array_builder(
			    		$db_table_name = $wpdb->prefix . "comments", 
			    		$parent = 0, 
			    		$parent_child = true, 
			    		$post_id, 
			    		$sort_type, 
			    		$pagination, 
			    		$items_per_page, 
			    		$pagination_type, 
			    		$page_number
			    	);
			    ?>

			    <div class="jltma-comment-listing-wrapper">
			        <?php
				        $class = 'jltma-comment-list';
				        $css="";
				        $child = 0;
				        $this->jltma_list_comments($comment_listing, $class, $css, $template, $settings);
			        ?>
			    </div>
			</div>        	
		<?php
	}


        public static function jltma_comment_elementor_preview_mode(){
        	return (\Elementor\Plugin::$instance->preview->is_preview_mode() || \Elementor\Plugin::$instance->editor->is_edit_mode());
        }


        public static function jltma_recursive_array_builder($db_table_name, $parent, $parent_child, $post_id, $sort_type, $pagination, $items_per_page, $pagination_type, $page_number) {

            global $wpdb, $post;

            $db_table_name 		= $wpdb->prefix . "comments";
            $jltma_commentmeta 	= $wpdb->prefix . "commentmeta";

            if ($pagination == 'yes') {
                $all_comments_approved = self::parent_comment_counter($post_id);

                /* Comments offset */
                $offset = (($page_number - 1) * $items_per_page);
                $max_num_pages = ceil($all_comments_approved / $items_per_page);
                $page_query = 'LIMIT' . ' ' . $offset . ', ' . $items_per_page;

            } else {
                $page_query = '';
            }

            $jltma_comments = $wpdb->get_results("SELECT * FROM $db_table_name  WHERE comment_parent = $parent AND comment_post_ID = $post_id AND comment_approved =1 $page_query");
            $list = array();

            if(!empty($jltma_comments)){
                foreach ($jltma_comments as $comment) {
                    $list[] = array(
                        'author_name' 	=> $comment->comment_author,
                        'time' 			=> $comment->comment_date,
                        'comment_text' 	=> get_comment_text($comment->comment_ID),
                        'author_email' 	=> $comment->comment_author_email,
                        'gravatar' 		=> get_avatar_url($comment->comment_author_email),
                        'comment_id' 	=> $comment->comment_ID,
                        'post_id' 		=> $comment->comment_post_ID,
                        "child" 		=> ($parent_child) ? self::jltma_recursive_array_builder($db_table_name, $comment->comment_ID, true, $comment->comment_post_ID, $sort_type, $pagination, $items_per_page, $pagination_type, $page_number) : ''
                    );
                }
                return $list;
            }
        }


        public static function jltma_number_format($input) {
            $prev = $input;
            $input = '10M';
            $input = number_format((float) $input);
            $input_count = substr_count($input, ',');
            $arr = array(1 => 'K', 'M', 'B', 'T');
            if (isset($arr[(int) $input_count])) {
                return substr($input, 0, (-1 * $input_count) * 4) . $arr[(int) $input_count];
            } else {
                return $prev;
            }
        }


        public function jltma_list_comments($comment_listing, $class, $css, $template, $settings) {

            if(!empty($comment_listing)){
                foreach ($comment_listing as $listing) {
                    $gravatar 			= $listing['gravatar'];
                    $author_name 		= $listing['author_name'];
                    $time 				= $listing['time'];
                    $comment_content 	= $listing['comment_text'];
                    $comment_id 		= $listing['comment_id'];
                    $post_id 			= $listing['post_id'];

                    $this->jltma_comment_listing_html( $listing, $class, $css, $template, $settings );
                }
            }


            // Demo Contents for Elementor Template Preivew
	        if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) { 

	        	$dummy_comment_array = array(1, 2, 3, 4, 5);
	        	foreach ($dummy_comment_array as $key => $value) {
	        		$this->jltma_comment_listing_html( $listing="", $class, $css, $template, $settings, $value );
	        	}
	        }

        }


        public function jltma_comment_rating($comment_id="", $settings="") {

        	$total_like_count 		= get_comment_meta($comment_id, 'jltma_like_count', true);
			$total_like_count 		= apply_filters('jltma_like_count', $total_like_count, $comment_id);
			$total_like_count 		= self::jltma_number_format($total_like_count);

			$total_dislike_count 	= get_comment_meta($comment_id, 'jltma_dislike_count', true);
			$total_dislike_count 	= apply_filters('jltma_dislike_count', $total_dislike_count, $comment_id);
			$total_dislike_count 	= self::jltma_number_format($total_dislike_count);

			$template = (isset($settings['jltma_comment_style_preset']) && $settings['jltma_comment_style_preset'] != '') ? esc_attr($settings['jltma_comment_style_preset']) : 'style_one';

			if(isset($_COOKIE['jltma_like_'.$comment_id])) {
			    $liked = 'jltma-already-liked';
			    $disliked = '';
			} else if (isset($_COOKIE['jltma_dislike_'.$comment_id])){
			    $disliked = 'jltma-already-disliked';
			    $liked = '';
			} else {
			    $liked = '';
			    $disliked = '';
			}
			?>

			<div class="jltma-message" id= "jltma-message-<?php echo esc_attr($comment_id); ?>"></div>

			<div class="jltma-like-dislike-wrapper clearfix">
			    <div class="jltma-like-wrap  jltma-common-wrap">
			        <a href="javascript:void(0);" class="jltma-like-trigger jltma-like-dislike-trigger <?php echo $liked; ?>" data-comment-id="<?php echo $comment_id; ?>" data-trigger-type="like" title="like">
			            <?php $likeicon = 'fa fa-thumbs-o-up'; ?>
			            <span class = "<?php echo $likeicon; ?> jltma-liked-wrap" > </span>
			        </a> 
			        <div class="jltma-count-wrap  jltma-common-wrap ">
			            <span class="jltma-like-count-wrap jltma-count-wrapper" id="jltma-like-count-<?php echo $comment_id; ?>"><?php echo (empty($total_like_count)) ? 0 : $total_like_count; ?>
			            </span>

			        </div>
			    </div>
			    <div class="jltma-dislike-wrap  jltma-common-wrap">
			        <a href="javascript:void(0);" class="jltma-dislike-trigger jltma-like-dislike-trigger <?php echo $disliked; ?> " data-comment-id="<?php echo $comment_id; ?>" data-trigger-type="dislike" title="dislike">
			            <?php $dislikeicon = 'fa fa-thumbs-o-down'; ?>
			            <span class="<?php echo $dislikeicon ?> jltma-disliked-wrap" ></span>
			        </a>
			        <div class="jltma-count-wrap  jltma-common-wrap ">
			            <span class="jltma-dislike-count-wrap jltma-count-wrapper" id="jltma-dislike-count-<?php echo $comment_id; ?>"><?php echo (empty($total_dislike_count)) ? 0 : $total_dislike_count; ?>
			            </span>
			        </div>
			    </div>
			</div>
        

        <?php }
        

        public function jltma_comment_templates($listing, $class, $css, $template, $settings, $demo_comment_id =""){ 

				$class              = "img-rounded";
                $comment_id         = isset( $listing['comment_id'] ) ? $listing['comment_id'] : '';

				$hide_replies 		= ($settings['jltma_comment_replies']) ? $settings['jltma_comment_replies'] : '';

				$show_reply_label 	= ($settings['jltma_comment_show_reply_label']) ? esc_attr($settings['jltma_comment_show_reply_label']) : esc_html__('Show Replies',JLTMA_TD);

				$hide_reply_label 	= ($settings['jltma_comment_hide_reply_label']) ? esc_attr($settings['jltma_comment_hide_reply_label']) : esc_html__('Hide Replies',JLTMA_TD);

				$reply_button_label = ($settings['jltma_comment_reply_label']) ? esc_attr($settings['jltma_comment_reply_label']) : esc_html__('Reply',JLTMA_TD);

				$show_gravatar = ($settings['jltma_comment_gravatar']) ? $settings['jltma_comment_gravatar'] : "";

        	?>



			<?php if( $show_gravatar =="show"){ ?>

			    <div class="jltma-comment-gravatar">
			    	
			    	<?php if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) { ?>
						<img class="<?php echo esc_attr($class);?>" scr="https://secure.gravatar.com/avatar/d7a973c7dab26985da5f961be7b74480?s=96&amp;d=mm&amp;r=g" srcset="https://secure.gravatar.com/avatar/d7a973c7dab26985da5f961be7b74480?s=96&d=mm&r=g" >
			    	<?php } else{ ?>
						<img class="<?php echo esc_attr($class);?>" scr= "<?php echo esc_url($listing['gravatar']); ?>" srcset="<?php echo $listing['gravatar']; ?>" >
			    	<?php } ?>			        
			    </div>

		    <?php } ?>

		    <div class="jltma-body media-body pl-3">

		        <div class="jltma-title-date clearfix">
		            <div class="jltma-author-name">
						<?php 
						if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) { 
							echo esc_html__('A WordPress Commenter', JLTMA_TD);
						} else{ 
							echo esc_html( $listing['author_name'] ); 
						} ?>
		            </div>
		            <div class="jltma-date-time" data-time="<?php echo get_the_modified_date( 'c' );?>">
		                <?php 
                            if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) { 
                                echo get_the_time('j M Y g:ia');
                            } else{ 

                                $date                       = date_create($listing['time']); 
                                $jltma_comment_date_time    = date_format($date, 'j M Y g:ia');
                                $comments_time_type         = ($settings['jltma_comments_time_type'] === 'custom') ? date_format($date, $settings['jltma_comments_time_format']) : $jltma_comment_date_time;
                                ?>
                                <div class="jltma-date">
                                    <?php echo esc_html( $comments_time_type ); ?>
                                </div>                                
                        <?php }  ?>
		            </div>
		        </div>

		        <div class="jltma-comment jltma-comment-content-<?php echo $comment_id; ?>" id="jltma-comment-<?php echo $comment_id; ?>">

	                <?php 
		                if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) {
		                	echo wp_specialchars_decode('Hi, this is a comment. <br>
								To get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.<br>
								Commenter avatars come from Gravatar.');
		                }else {
			                $comment = get_comment($comment_id);
			                comment_text($comment_id);		                	
		                }
	                ?>


		            <?php 
			            if( $settings['jltma_comment_ratings'] == "show"){
			            	if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) {
			            		$this->jltma_comment_rating();	
			            	}else{
			            		$this->jltma_comment_rating($comment_id, $settings);
			            	}
			            }
		            ?>
		        </div>

		        <div class="jltma-comment-footer clearfix">
		            <?php
		            $args = array('reply_text' => $reply_button_label, 'depth' => 1, 'max_depth' => 10, 'add_below' => "jltma-unique-comment" );
		            
		            if (comments_open()) {
	                    if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) {
	                    	echo '<div class="jltma-reply-button">';
	                    	echo esc_html($settings['jltma_comment_reply_label']);
	                    	echo '</div>';
	                    } else{
	                    	echo '<div class="jltma-reply-button">';
	                    	comment_reply_link($args, $comment_id, get_the_ID()); 
	                    	echo '</div>';
	                    }
		            }

                    if (!empty($listing['child'])) {
                        $children = $listing['child'];
                    } else {
                        $children = null;
                    }

		            if (!empty($children)) {

		                if($hide_replies == 'show') { ?>
		                    <a href="javascript:void(0);" class="jltma-show-replies-trigger jltma-show-reply-trigger-<?php echo $comment_id; ?>" data-comment-id="<?php echo $comment_id; ?>"> 
		                    	<?php echo $show_reply_label; ?> 
		                    </a>

		                    <a href="javascript:void(0);" class="jltma-hide-replies-trigger jltma-hide-reply-trigger-<?php echo $comment_id; ?>" data-comment-id="<?php echo $comment_id; ?>" style="display:none;"> <?php echo $hide_reply_label; ?> </a> <?php
		                }
		            
		            }elseif (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) { 
		            	
						if( $demo_comment_id%2 !=0 && $demo_comment_id !=5 ){ ?>

	                    <a href="javascript:void(0);" class="jltma-show-replies-trigger jltma-show-reply-trigger-<?php echo $comment_id; ?>" data-comment-id="<?php echo $comment_id; ?>"> 
	                    	<?php echo $show_reply_label;  ?> 
	                    </a>

	                    <a href="javascript:void(0);" class="jltma-hide-replies-trigger jltma-hide-reply-trigger-<?php echo $comment_id; ?>" data-comment-id="<?php echo $comment_id; ?>" style="display:none;"> <?php echo $hide_reply_label; ?> </a>
		            <?php } } ?>
		        </div>
		    </div>

        <?php
    	}		


        public static function parent_comment_counter($post_id) {
            global $wpdb;
            $db_table_name = $wpdb->prefix . "comments";
            $query = "SELECT COUNT(comment_post_id) AS count FROM $db_table_name WHERE comment_approved = 1 AND comment_post_ID = $post_id AND comment_parent = 0";
            $parents = $wpdb->get_row($query);
            return $parents->count;
        }


        public function jltma_comment_listing_html( $comment_listing, $class, $css, $template, $settings, $demo_comment_id ="" ){

        	$comment_id 		= isset( $comment_listing['comment_id'] ) ? $comment_listing['comment_id'] : '';
        	$hide_replies 		= ($settings['jltma_comment_replies'] == 'show') ? 'show' : '';
			$css="style='display:block;'";

            // Demo Contents for Elementor Template Preivew
	        if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) { 
	        		
	        	if( $demo_comment_id%2!=0 ){ ?>
	        		<ul class="<?php echo $class; ?> " data-comment-id="<?php echo esc_attr($demo_comment_id);?>" <?php echo $css ?>>
	        	<?php }else{ 

	            if($hide_replies == 'show'){
	                $css= "style='display:none;'";
	            }else{
	                $css="style='display:block;'";
	            }
	            ?>
	        		<ul class="jltma-children <?php echo $class; ?> " data-comment-id="<?php echo esc_attr($demo_comment_id);?>" <?php echo $css ?>>
	        	<?php } ?>
				
			<?php } else{ ?>
				<ul class="<?php echo $class; ?> " data-comment-id="<?php echo $comment_id; ?>" <?php echo $css ?>>
			<?php } ?>


		    <?php if($template == 'style_one' || $template == 'style_two' || $template =='style_four'){
			        $clearfix= 'clearfix';
			    }else{
			        $clearfix= '';
			    } ?>

			<?php 
            // Demo Contents for Elementor Template Preivew
	        if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) { ?>
	        	<div class="jltma-comment-template media jltma-comment-<?php echo $template ?> <?php echo $clearfix ?>" id= "jltma-unique-comment-<?php echo $demo_comment_id; ?>" >
	        <?php } else{?>
	        	<div class="jltma-comment-template media jltma-comment-<?php echo $template ?> <?php echo $clearfix ?>" id= "jltma-unique-comment-<?php echo $comment_id; ?>" >
	        <?php } ?>
			    
			        <?php 
			        	$this->jltma_comment_templates($comment_listing, $c="", $css, $template, $settings, $demo_comment_id); 
			        ?>
			    </div>

		    <?php
                if (!empty($comment_listing['child'])) {
                    $children = $comment_listing['child'];
                } else {
                    $children = null;
                }

		        if (!empty($children)) {

			        $c = 'jltma-children' . ' ' . 'jltma-comment-list';

		            if($hide_replies == 'show'){
		                $css= "style='display:none;'";
		            }else{
		                $css="style='display:block;'";
		            }
		            $this->jltma_list_comments($children, $c, $css, $template, $settings); 
		        }else{
		            $css="style='display:block;'";
		        } 

		        if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) { 
		        	
		        	if( $demo_comment_id%2!=0 ){
		        		return;
		        	}else{
		        		echo "</ul></ul>";
		        	}

		        }else{
		        	echo "</ul>";
		        }

		    
			
        }

		public function jltma_register_comments_widget(){
			//Master Comments for Elementor 
			include JLTMA_PLUGIN_PATH . '/inc/comments/jltma-comments-addon.php';

			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Addon\Master_Addons_Comments() );

		}

        /**
         * Google recaptcha check, validate and catch the spammer
         */
        public function jltma_is_valid_captcha($captcha) {
            $jltma_api_settings = get_option( 'jltma_api_save_settings' );

            $captcha_postdata = http_build_query(array(
                                      'secret' => $jltma_api_settings['recaptcha_secret_key'],
                                      'response' => $captcha,
                                      'remoteip' => $_SERVER['REMOTE_ADDR']));
            $captcha_opts = array('http' => array(
                                'method'  => 'POST',
                                'header'  => 'Content-type: application/x-www-form-urlencoded',
                                'content' => $captcha_postdata));
            $captcha_context  = stream_context_create($captcha_opts);
            $captcha_response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify" , false , $captcha_context), true);
            if ($captcha_response['success'])
              return true;
            else
              return false;
        }


        public function jltma_verify_google_recaptcha() {
            $jltma_comment_recaptha = $this->jltma_set_var;
            if(isset($jltma_comment_recaptha['jltma_comment_spam_protection']) && $jltma_comment_recaptha['jltma_comment_spam_protection'] =="yes"){
                $recaptcha = $_POST['g-recaptcha-response'];
                if (empty($recaptcha))
                  wp_die( __("<b>ERROR:</b> please select <b>I'm not a robot!</b><p><a href='javascript:history.back()'>« Back</a></p>"));
                else if (! $this->jltma_is_valid_captcha($recaptcha))
                  wp_die( __("<b>Go away SPAMMERsss!</b>", MELA_TD));
            }
        }


		// Enable/Disable Comments for Post Types
	    public function jltma_comments_open($open, $post_id = 0){

		    // post types without comments
		    $closed_comments_post_types = array('page', 'attachment');

		    // is the current post type among the ones without comments?
		    if(in_array( get_post_type(), $closed_comments_post_types)) return false;
	        return $open;
	    }

	    public static function get_instance() {
	        if ( is_null( self::$_instance ) ) {
	            self::$_instance = new self();
	        }
	        return self::$_instance;
	    }


	}
    JLTMA_Comments_Builder::get_instance();
}