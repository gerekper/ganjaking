<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_Category_Accordion_Shortcode' ) ){

    class YITH_Category_Accordion_Shortcode{

        public static function print_shortcode( $atts, $content=null ){


            $default    =   array(
                'how_show'          =>  false,
                'show_sub_cat'      =>  'on',
                'show_last_post'    =>  'off',
                'post_limit'        =>  '-1',
                'menu_ids'          =>  '' ,
                'exclude_post'      =>  '',
                'exclude_page'      =>  '',
                'exclude_cat'       =>  '',
                'acc_style'         =>  'style_1',
                'title'             =>  '',
                'highlight'         =>  'on',
                'orderby'           =>  'name',
                'order'             =>  'asc',
                'show_count'        =>  'off',
                'tag_wc'            =>  'off',
                'tag_wp'            =>  'off',
                'name_wc_tag'       =>  __("WooCommerce TAGS", "yith-woocommerce-category-accordion" ),
                'name_wp_tag'       =>  __("WordPress TAGS", "yith-woocommerce-category-accordion"),
            );
            $atts   =   shortcode_atts( $default, $atts );


            extract( $atts );

            $args   =   array();

            $args['orderby']        =   $orderby;
            $args['order']          =   strtoupper( $order );
            $args['show_count']     =   $show_count ==  'on' ? 1 : false ;
            $args['depth']          =   get_option( 'ywcca_max_depth_acc' );
            $limit                  =   get_option( 'ywcca_limit_number_cat_acc' );
            $args['style_count']    =   get_option( 'ywcca_'.$acc_style.'_count' );
            $args['hide_empty']     =   get_option( 'ywcca_hide_empty_cat' ) ==  'yes';
            $args['hierarchical']   =   true;
            $args['pad_counts']     =   true;
            $args['title_li']       =   '';


            if( apply_filters( 'ywcca_hide_category_title', false ) ){

	            $args['use_desc_for_title'] = 0;
            }

            if( is_singular( 'product' ) ) {
                global $post;

                $product_categories = wc_get_product_terms( $post->ID, 'product_cat', apply_filters( 'woocommerce_product_categories_widget_product_terms_args', array( 'orderby' => 'parent', 	'order'   => 'DESC', ) ) );
                $product_category = isset( $product_categories[0] ) ? $product_categories[0] : '';
                if( !empty( $product_category ) ) {
                    $current_category = apply_filters('yith_category_accordion_current_category', $product_category->term_id, $product_categories, $post->ID );
                    $args['current_category'] =  $current_category;
                    
                }
            }


            /*Check if current post or page is excluded*/
            $id_pages    =   array();
            $id_posts    =   array();
            $shop_id     =   wc_get_page_id( 'shop' );

            if( !empty( $exclude_page ) )
                $id_pages   =   explode( ',', $exclude_page );

            if( !empty( $exclude_post ) )
                $id_posts   =   explode( ',', $exclude_post );

            if( ( is_shop() && in_array( $shop_id, $id_pages ) ) )
                return;

            if( is_page() ) {
                $page_id    =   get_queried_object_id();

                if( in_array( $page_id, $id_pages )  )
                    return;
            }
            if( is_single() ) {
                $post_id    =     get_queried_object_id();

                if( in_array( $post_id, $id_posts) )
                    return;
            }

            global $wpdb;

	        wp_enqueue_style( 'ywcca_accordion_style' );
	        wp_enqueue_script( 'ywcca_accordion' );
	        wp_enqueue_script( 'hover_intent' );
	        wp_enqueue_style( 'ywcca_dynamics' );
	        ob_start();
            echo '<div class="ywcca_container ywcca_widget_container_'.$acc_style.'">';
            echo '<h3 class="ywcca_widget_title">'.$title.'</h3>';
            $content =  '<ul class="ywcca_category_accordion_widget %s" data-highlight_curr_cat="%s" data-ywcca_style="%s" data-ywcca_orderby="%s" data-ywcca_order="%s">';
            $general_content = sprintf( $content,  'category_accordion', $highlight,$acc_style,$orderby, $order );
            $end_general_content='</ul>';

            switch( $how_show ){

                case 'wc':
                    include_once( YWCCA_INC . '/walkers/class.yith-category-accordion-walker.php' );

                    $args['taxonomy']   =   'product_cat';
                    $args['walker']     =   new YITH_Category_Accordion_Walker;
                    $args['exclude']     =  empty( $exclude_cat ) ? '' : explode( ',', $exclude_cat );

                    if( $show_sub_cat == 'off' ) {
                        $args['parent'] = 0;
                    }

                    if( $orderby !=='menu_order') {
                        $args[ 'orderby' ] = $orderby == 'name' ? 'title' : $orderby;
                    }
                    else{
                        $args['menu_order'] = 'asc';
                        unset( $args[ 'orderby' ] );

                    }


                    /*if a limit is set*/
                    if( $limit!=-1 )
                    {
                        $args_cat = array(
                            'orderby'   =>  $orderby == 'name' ? 'title' : $orderby,
                            'order'     =>  $order,
                            'parent'    =>  0,
                            'number'    => $limit,
                            'taxonomy'  =>  'product_cat',

                        );

                        /*Get category parent */

                        $categories =   get_categories( $args_cat );

                        $include =   array();

                        foreach ( $categories as $category ) {
                            $include[]  =   $category->term_id;
                        }
                            //get the child category
                            $children = $wpdb->get_col( "SELECT `term_id` FROM {$wpdb->term_taxonomy } WHERE `parent` IN (" . implode( ',', $include ) . ")" );
                            $args['include'] = implode( ',', $include ) . ',' . implode( ',', $children );


                    }


                    echo $general_content;
                        wp_list_categories( apply_filters( 'ywcca_wc_product_categories_widget_args', $args ) );
                    echo $end_general_content;

                    break;
                case 'wp' :
                    include_once( YWCCA_INC . '/walkers/class.yith-category-accordion-walker.php' );

                    $args['taxonomy']       =   'category';
                    $args['walker']         =   new YITH_Category_Accordion_Walker;
                    $args['exclude']        =   empty( $exclude_cat ) ? '' : explode( ',', $exclude_cat );
                    $args['show_last_post'] =   $show_last_post=='on';
                    $args['post_limit']     =   $post_limit;
                    $args['pad_counts'] = false;



                    /*if a limit is set*/
                    if( $limit!=-1 )
                    {
                        $args_cat = array(
                            'orderby'   =>  $orderby,
                            'order'     =>  $order,
                            'parent'    =>  0,
                            'number'    => $limit,
                            'taxonomy'  =>  'category'
                        );

                        /*Get category parent */
                        $categories =   get_categories( $args_cat );

                        $include =   array();

                        foreach ( $categories as $category ) {
                            $include[]  =   $category->term_id;
                        }

                        //get the child category
                        $children   =   $wpdb->get_col("SELECT `term_id` FROM {$wpdb->term_taxonomy } WHERE `parent` IN (". implode( ',', $include ). ")");
                        $args['include'] =   implode( ',', $include ).','.implode( ',', $children );

                    }

                    echo $general_content;
                        wp_list_categories( apply_filters( 'ywcca_wc_product_categories_widget_args', $args ) );
                    echo $end_general_content;


                    break;

                case 'tag':
                    include_once( YWCCA_INC . '/walkers/class.yith-category-accordion-walker.php' );

                    $args['walker']             =   new YITH_Category_Accordion_Walker;
                    //get the tag
                    $tags   =   $wpdb->get_col("SELECT `term_id` FROM {$wpdb->term_taxonomy } WHERE `taxonomy` IN ( 'post_tag','product_tag' )");
                    $args['include'] =  implode( ',', $tags );
                    $args['show_option_none']  =   __('No Tags', 'yith-woocommerce-category-accordion');

                    echo $general_content;
                    if( $tag_wc=='on' ){
                        echo '<li class="cat-item"><a href="#">'.$name_wc_tag.'</a>';
                        echo '<ul class="children">';
                        wp_list_categories( array_merge( array( 'taxonomy'=>'product_tag' ),$args ) );
                        echo '</ul>';
                        echo '</li>';
                    }
                    if( $tag_wp=='on' ) {

                        echo '<li class="cat-item"><a href="#">'.$name_wp_tag.'</a>';
                        echo '<ul class="children">';
                        wp_list_categories( array_merge( array( 'taxonomy' => 'post_tag' ), $args ) );
                        echo '</ul>';
                        echo '</li>';
                    }
                  echo $end_general_content;
                    break;

                case 'menu' :
                    $menu_ids   =   explode( ',', $menu_ids );
                    $args['container']     =   false;


                    $general_content = sprintf( $content, 'category_menu_accordion',  $highlight,$acc_style,'','' );

                    echo $general_content;
                  if( !empty( $menu_ids ) ) {

                  	add_filter('nav_menu_submenu_css_class', 'ywcca_change_submenu_css_class', 20, 3 );
                      foreach ( $menu_ids as $menu_id ) {
	                      wp_nav_menu( array_merge( array( 'menu'       => $menu_id,
	                                                       'menu_class' => 'ywcca-menu'
	                      ), $args ) );
                      }
	                  remove_filter('nav_menu_submenu_css_class', 'ywcca_change_submenu_css_class', 20 );

                  }
                  echo  $end_general_content;
                    break;
            }

            echo '</div>';

            $template = ob_get_contents();

            ob_end_clean();
            return $template;

        }
    }

}

add_shortcode( 'yith_wcca_category_accordion', array( 'YITH_Category_Accordion_Shortcode', 'print_shortcode' ) );

/**
 * @param array $classes
 * @param array $args
 * @param $depth
 */
function ywcca_change_submenu_css_class( $classes, $args, $depth ){


	if( !empty( $args->menu_class ) && 'ywcca-menu' === $args->menu_class  ){

		$key =array_search('sub-menu', $classes );


		if( $key!== false ){
			$classes[$key] = 'ywcca-sub-menu';
		}
	}


	return $classes;
}