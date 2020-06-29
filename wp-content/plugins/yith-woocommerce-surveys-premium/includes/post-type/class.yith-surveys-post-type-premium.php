<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_Surveys_Post_Type_Premium' ) ){

    class YITH_Surveys_Post_Type_Premium extends YITH_Surveys_Post_Type{


        protected static $instance;

        public function __construct(){

            parent::__construct();

            add_action( 'pre_get_posts', array( $this,'get_all_parent_posts' ) );
            add_filter( 'wp_count_posts', array( $this, 'survey_count_posts' ), 10, 3 );
            add_filter( "views_edit-{$this->post_type_name}", array( $this, 'set_views' ) );
            add_action( 'admin_init', array( $this, 'add_tab_metabox' ) );
            add_action( 'admin_init', array( $this, 'add_capabilities' ) );
            add_filter( 'yit_fw_metaboxes_type_args', array($this, 'add_custom_survey_metaboxes' ) );

            //save survey metabox data
            add_action( 'save_post', array( $this, 'save_survey_post' ), 20, 1 );
            //delete survey answer
            add_action( 'before_delete_post', array( $this, 'delete_survey_answers' ),20,1 );

            add_filter( 'manage_edit-' . $this->post_type_name . '_columns', array( $this, 'edit_columns' ) );
            add_action( 'manage_' . $this->post_type_name . '_posts_custom_column' , array( $this, 'custom_surveys_column' ), 10, 2 );

            //Custom Surveys Message
            add_filter( 'post_updated_messages', array($this, 'custom_survey_messages' ) );

        }

        /**
         * @return YITH_Surveys_Post_Type_Premium
         */
        public static function  get_instance(){

            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }


        /**
         * Customize the messages for Sliders
         * @param $messages
         * @author Yithemes
         *
         * @return array
         * @fire post_updated_messages filter
         */
        public function custom_survey_messages ( $messages ) {

            $singular_name  =   $this->get_survey_taxonomy_label('singular_name');
            $messages[$this->post_type_name] =   array (

                0    =>  '',
                1    =>  sprintf(__('%s updated','yith-woocommerce-surveys') , $singular_name ) ,
                2    =>  __('Custom field updated', 'yith-woocommerce-surveys'),
                3    =>  __('Custom field deleted', 'yith-woocommerce-surveys'),
                4    =>  sprintf(__('%s updated','yith-woocommerce-surveys') , $singular_name ) ,
                5    =>  isset( $_GET['revision'] ) ? sprintf( __( 'Survey restored to version %s', 'yith-woocommerce-surveys' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
                6    =>  sprintf( __('%s published', 'yith-woocommerce-surveys' ), $singular_name ),
                7    => sprintf( __('%s saved', 'yith-woocommerce-surveys' ), $singular_name ),
                8    => sprintf( __('%s submitted', 'yith-woocommerce-surveys' ), $singular_name ),
                9    => sprintf( __('%s', 'yith-woocommerce-surveys'), $singular_name ),
                10   =>  sprintf( __('%s draft updated', 'yith-woocommerce-surveys'), $singular_name )
            );


            return $messages;
        }

        /**
         * add surveys capabilities for admin and for shop_manager
         * @author YIThemes
         * @since 1.0.0
         */
        public function add_capabilities(){

            $capability_type = 'survey';
            $caps            = array(
                'edit_post'              => "edit_{$capability_type}",
                'delete_post'            => "delete_{$capability_type}",
                'edit_posts'             => "edit_{$capability_type}s",
                'edit_others_posts'      => "edit_others_{$capability_type}s",
                'publish_posts'          => "publish_{$capability_type}s",
                'read_private_posts'     => "read_private_{$capability_type}s",
                'delete_posts'           => "delete_{$capability_type}s",
                'delete_private_posts'   => "delete_private_{$capability_type}s",
                'delete_published_posts' => "delete_published_{$capability_type}s",
                'delete_others_posts'    => "delete_others_{$capability_type}s",
                'edit_private_posts'     => "edit_private_{$capability_type}s",
                'edit_published_posts'   => "edit_published_{$capability_type}s",
                'create_posts'           => "edit_{$capability_type}s",
            );

            // gets the admin and shop_mamager roles
            $admin        = get_role( 'administrator' );
            $shop_manager = get_role( 'shop_manager' );

            foreach ( $caps as $key => $cap ) {

                $admin->add_cap( $cap );
                $shop_manager->add_cap( $cap );
            }

        }

    /* get all others surveys
    * @author YIThemes
    * @since 1.0.0
    * @param array $extra
    * @return array
    */
        public function get_other_surveys(){

            $args = array(
                'meta_query' => array(
                    array(
                        'key' => '_yith_survey_visible_in',
                        'value' => 'other_page',
                        'compare' => '='
                    ),
                  )
            );

            return $this->get_surveys( $args );
        }

        /* get product surveys
            * @author YIThemes
            * @since 1.0.0
            * @param array $extra
            * @return array
            */
        public function get_product_surveys( $handle_position='all', $product_id  = '' ){

            $args = array(
                'meta_query' => array(
                    array(
                        'key' => '_yith_survey_visible_in',
                        'value' => 'product',
                        'compare' => '='
                    ),
                )
            );

            if( $handle_position!= 'all' ){

                $args['meta_query'][] =  array(
                    'key' => '_yith_survey_product_wc_handle',
                    'value' => $handle_position,
                    'compare' => '='
                );
                $args['meta_query']['relation'] = 'AND';
            }

            $results = $this->get_surveys( $args );

            if( !empty( $product_id ) ){

                foreach( $results as $key => $result ){

                    $product_ids = get_post_meta( $result, '_yith_survey_products', true );

                    if( is_array( $product_ids ) && !in_array( $product_id, $product_ids ) ){
                        unset( $results[$key] );
                    }

                }
            }
            return $results;
        }

        /** Edit Columns Table
         * @param $columns
         * @return mixed
         */
        function edit_columns( $columns ){

            $date = $columns['date'];
            unset( $columns['date'] );
            $columns['n_items'] = __( 'Items', 'yith-woocommerce-surveys' );
            $columns['survey_type'] = __( 'Type', 'yith-woocommerce-surveys' );
            $columns['date'] = $date;

            return $columns;
        }

        /**
         * @param $column
         * @param $post_id
         */
        public function custom_surveys_column( $column, $post_id ){

            switch( $column ){

                case 'n_items' :

                    $n_item = count( $this->get_survey_children( array( 'post_parent' => $post_id ) )  );
                    echo $n_item;
                    break;
                case 'survey_type':
                    $type = get_post_meta( $post_id, '_yith_survey_visible_in', true );
                    echo $type;
                    break;
            }
        }

        /**
         * add_tab_metabox
         * Register metabox for product sldier
         * @author YIThemes
         * @since 1.0.0
         */
        public function  add_tab_metabox() {

            $args	=	require_once( YITH_WC_SURVEYS_TEMPLATE_PATH.'metaboxes/survey-meta-box-options.php');

            if (!function_exists( 'YIT_Metabox' ) ) {
                require_once( YITH_WC_SURVEYS_DIR.'plugin-fw/yit-plugin.php' );
            }


            $metabox    =   YIT_Metabox('yit-surveys-setting');
            $metabox->init($args);

        }

        /**
         * add custom metabox
         * @author YIThemes
         * @since 1.0.0
         * @param $args
         * @return mixed
         */
        public function  add_custom_survey_metaboxes( $args ){

        	$custom_field_type = array(
        		'survey_answers', 'survey_text'
	        );

        	if( in_array( $args['type'], $custom_field_type ) ){
		        $args['basename']   = YITH_WC_SURVEYS_DIR;
		        $args['path']       = 'metaboxes/types/';
	        }

            return $args;
        }

        /**
         * save all survey meta and create survey answer
         * @uthor YIThemes
         * @since 1.0.0
         * @param $post_id
         */
        public function save_survey_post( $post_id ){

            $post_id = yith_wpml_get_translated_id( $post_id , 'yith_wc_surveys' );
            $post_type = get_post_type( $post_id );
            $post = get_post( $post_id );

            if( $this->post_type_name === $post_type && $post->post_parent == 0   ) {

                remove_action('save_post', array($this, 'save_survey_post'), 20);
                $this->save_survey_answers( $post_id );
                add_action( 'save_post', array( $this, 'save_survey_post' ), 20 );
            }

        }

	    /**
	     * save all answers sent
	     * @author YITH
	     * @param  int $post_id
	     * @since 1.1.6
	     */
        public function save_survey_answers( $post_id ){
	        if ( isset( $_REQUEST['yith_survey_answers'] ) ) {

		        $answers = $_REQUEST['yith_survey_answers'];
		        $children_ids = $_REQUEST['yith_survey_answer_post_ids'];

		        //update post meta for surveys answer
		        for ($i = 0; $i < count($children_ids); $i++) {

			        $child_id = $children_ids[$i];
			        $answer = $answers[$i];

			        if (!empty( $answer ) ) {

				        if ($child_id == -1) {

					        //check if this answer already exsist
					        $child_id = $this->is_survey_child_exist($answer, $post_id);


					        //if not exsit, create it
					        if ($child_id == 0)
						        $child_id = $this->add_survey_child($post_id, $answer);
				        }
				        else
					        $this->update_survey_child( $child_id,$post_id, $answer );

				        $position = $i;
				        update_post_meta( $child_id, '_yith_answer_visible_in_survey', 'yes');
				        update_post_meta( $child_id, '_yith_survey_position', $position);

			        }
		        }
	        }
        }

        /**
         * @param $post_id
         * @param $post_parent
         * @param $post_title
         */
        public function update_survey_child( $post_id, $post_parent, $post_title ){
            $post_id = yith_wpml_get_translated_id( $post_id , 'yith_wc_surveys' );
            $post_parent = yith_wpml_get_translated_id( $post_parent , 'yith_wc_surveys' );
            $my_post = array(
                'ID'    =>$post_id,
                'post_title' => $post_title,
                'post_name' => sanitize_title( $post_title),
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'yith_wc_surveys',
                'comment_status' => 'closed',
                'post_parent' => $post_parent
            );

            wp_update_post( $my_post );
        }

        /**
         * remove survey post meta and all answers meta
         * @author YIThemes
         * @since 1.0.0
         * @param $post_id
         */
        public function delete_survey_answers( $post_id ){

            $post = get_post( $post_id );

            if( $this->post_type_name === $post->post_type && $post->post_parent == 0   ) {

                global $wpdb;
                remove_action( 'before_delete_post', array( $this, 'delete_survey_answers' ), 20 );


                delete_post_meta( $post_id, '_yith_survey_required' );
                delete_post_meta( $post_id, '_yith_survey_visible_in' );
                delete_post_meta( $post_id, '_yith_survey_wc_handle' );

                $all_child = $this->get_survey_children( array( 'post_parent' => $post_id ) );

                foreach( $all_child as $child_id ){

                    delete_post_meta( $child_id, '_yith_answer_visible_in_survey' );
                    delete_post_meta( $child_id, '_yith_survey_position' );
                    delete_post_meta( $child_id, '_yith_order_survey_voting' );

                    $wpdb->delete( $wpdb->posts, array( 'ID' => $child_id ) );

                }

                add_action( 'before_delete_post', array( $this, 'delete_survey_answers' ), 20 );
            }
        }

        /**
         * get only parent surverys in admin
         * @YIThemes
         * @since 1.0.1
         * @param $query
         */
      public function get_all_parent_posts( $query ) {

            if ( is_admin() && $query->is_main_query() && isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == $this->post_type_name ) {
                $query->set( 'post_parent', '0' );

            }
        }

        /**
         * filters views in custom post type
         * @author YIThemes
         * @since 1.0.3
         * @param $views
         * @return mixed
         */
      public function set_views( $views ){

          if( isset( $views['mine'] ) ){

              $current_user_id = get_current_user_id();

              $mine_args = array(
                  'post_type' => $this->post_type_name,
                  'author' => $current_user_id
              );

              $mine_inner_html = sprintf(
                  _nx(
                      'Mine <span class="count">(%s)</span>',
                      'Mine <span class="count">(%s)</span>',
                      count( $this->get_surveys()),
                      'posts'
                  ),
                  number_format_i18n( count( $this->get_surveys() ) )
                );

              $url = add_query_arg( $mine_args, 'edit.php' );
              $mine = sprintf( '<a href="%s"%s>%s</a>',
                                esc_url( $url ),
                                '',
                                $mine_inner_html
                    );
              $views['mine'] = $mine;
          }

          return $views;
      }

        /**
         * adjust count post for only parent post
         * @author YIThemes
         * @since 1.0.1
         * @param $counts
         * @param $type
         * @param $perm
         * @return mixed
         */
       public function survey_count_posts( $counts, $type, $perm )
        {

            if( !( $type === $this->post_type_name ) )
                return $counts;

            /**
             * Get a list of post statuses.
             */
            $stati = get_post_stati();

            // Update count object
            foreach ($stati as $status) {
                $posts = $this->get_surveys( array( "post_status" =>$status ) );
                $counts->$status = count($posts);
            }
            return $counts;
        }

    }
}