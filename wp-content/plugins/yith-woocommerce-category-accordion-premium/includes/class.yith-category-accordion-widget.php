<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_Category_Accordion_Widget' ) )
{
    class YITH_Category_Accordion_Widget extends  WP_Widget{

        public function __construct()
        {
            parent::__construct(
                'yith_wc_category_accordion',
                __('YITH WooCommerce Category Accordion', 'yith-woocommerce-category-accordion'),
                array(   'description'   =>  __('Show your categories in an accordion!', 'yith-woocommerce-category-accordion' ) )
            );
        }

        public function widget( $args, $instance ){

            $how_show       =   $instance['how_show'];

            $string_build_shortcode =   'how_show="'.$how_show.'" ';


            switch( $how_show )
            {
                case 'wc' :
                    $show_sub_cat   =   $instance['show_wc_subcat'];
                    $exclude_cat    =   $instance['exclude_wc_cat'];
                    $show_count     =   $instance['show_count'];

                    if( is_array( $exclude_cat ) ){
                        $exclude_cat = implode( ',', $exclude_cat );
                    }

                    $string_build_shortcode .=   'show_sub_cat="'.$show_sub_cat.'" exclude_cat="'.$exclude_cat.'" show_count="'.$show_count.'" ';
                    break;
                case 'wp':
                    $show_sub_cat   =   $instance['show_wp_subcat'];
                    $exclude_cat    =   $instance['exclude_wp_cat'];
                    $show_last_post =   $instance['show_post'];
                    $post_limit     =   $instance['post_limit'];
                    $show_count     =   $instance['show_count'];
                    if( is_array( $exclude_cat ) ){
                        $exclude_cat = implode( ',', $exclude_cat );
                    }
                    $string_build_shortcode .=   'show_sub_cat="'.$show_sub_cat.'" exclude_cat="'.$exclude_cat.'" show_last_post="'.$show_last_post.'" post_limit="'.$post_limit.'" show_count="'.$show_count.'" ';
                    break;
                case 'menu':
                    $menu_ids       =   implode( ',', $instance['include_menu'] );

                    $string_build_shortcode .=   'menu_ids="'.$menu_ids.'" ';

                    break;

                case 'tag'  :
                    $menu_wc_name = $instance['name_wc_tag'];
                    $menu_wp_name = $instance['name_wp_tag'];
                    $string_build_shortcode .=  'tag_wc="'.$instance['tag_wc'].'" tag_wp="'.$instance['tag_wp'].'"  name_wc_tag="'.$menu_wc_name.'" name_wp_tag="'.$menu_wp_name.'" ';
                    break;

            }


            /*General params*/
            $title                  =   $instance['title'];
	        $title = apply_filters( 'widget_title',$title, $instance, $this->id_base );
            $exclude_page           =   $instance['exclude_page']   ;
            $exclude_post           =   $instance['exclude_post']   ;
            $highlight              =   $instance['highlight_curr_cat'];
            $style                  =   $instance['acc_style'];
            $orderby                =   $instance['orderby'];
            $order                  =   $instance['order'];


            $exclude_page = is_array( $exclude_page ) ? implode(',', $exclude_page ) : $exclude_page;
            $exclude_post = is_array( $exclude_post ) ? implode(',', $exclude_post ) : $exclude_post;


            $string_build_shortcode .=  'exclude_page="'.$exclude_page.'" exclude_post="'.$exclude_post.'" highlight="'.$highlight.'" orderby="'.$orderby.'" order="'.$order.'" acc_style="'.$style.'" ';

            echo $args['before_widget'];
            echo do_shortcode('[yith_wcca_category_accordion title="'.$title.'" '.$string_build_shortcode.']');
            echo $args['after_widget'];

        }

        public function form( $instance ){

            $is_default = empty( $instance );

            $default    =   array(
                'title'                 =>   isset( $instance['title'] )                                   ? $instance['title'] : '',
                'show_wc_subcat'        =>   isset( $instance['show_wc_subcat'] )                          ? $instance['show_wc_subcat'] : 'off',
                'show_wp_subcat'        =>   isset( $instance['show_wp_subcat'] )                          ? $instance['show_wp_subcat'] : 'off',
                'show_post'             =>   isset( $instance['show_post'] )                               ? $instance['show_post'] : 'off',
                'highlight_curr_cat'    =>   isset( $instance['highlight_curr_cat'] )                      ? $instance['highlight_curr_cat'] : 'on',
                'acc_style'             =>   isset( $instance['acc_style'] )                               ? $instance['acc_style'] :'style_1',
                'exclude_wc_cat'        =>   isset( $instance['exclude_wc_cat'] )                          ? $instance['exclude_wc_cat'] : '',
                'exclude_wp_cat'        =>   isset( $instance['exclude_wp_cat'] )                          ? $instance['exclude_wp_cat'] : '',
                'exclude_page'          =>   isset( $instance['exclude_page'] )                            ? $instance['exclude_page'] : '',
                'exclude_post'          =>   isset( $instance['exclude_post'] )                            ? $instance['exclude_post'] : 'off',
                'how_show'              =>   isset( $instance['how_show'] )                                ? $instance['how_show']     :    '',
                'include_menu'          =>   isset( $instance['include_menu'] )                            ? $instance['include_menu'] :    array(),
                'show_count'            =>   isset( $instance['show_count'] )                              ? $instance['show_count'] :    'off',
                'orderby'               =>   isset( $instance['orderby'] )                                 ? $instance['orderby']    :  'id',
                'order'                 =>   isset( $instance['order'] )                                   ? $instance['order']      : 'asc',
                'tag_wc'                =>   isset( $instance['tag_wc'] )                                   ? $instance['tag_wc']      : 'off',
                'tag_wp'                =>   isset( $instance['tag_wp'] )                                   ? $instance['tag_wp']      : 'off',
                'post_limit'            =>   isset( $instance['post_limit'] )                                ? $instance['post_limit']      : '-1',
                'name_wc_tag'           =>  isset( $instance['name_wc_tag'] )                                ? $instance['name_wc_tag']      : __('WooCommerce TAGS','yith-woocommerce-category-accordion'),
                'name_wp_tag'           =>  isset( $instance['name_wp_tag'] )                                ? $instance['name_wp_tag']      : __('WordPress TAGS','yith-woocommerce-category-accordion'),


            );

            $instance    =   wp_parse_args( $instance, $default );
            ?>

            <style>

                #ywcca_widget_content{
                    background: #fff;
                }

                #ywcca_widget_content p{
                    margin: 0;
                    padding: 10px;
                }

                #ywcca_widget_content .title_shortcode{
                    height: 39px;
                }
                #ywcca_widget_content label{
                    font-weight: bold;
                }

                #ywcca_widget_content input[type="checkbox"] {

                    width: 20px;
                    height: 20px;
                    float: right;
                }
                
                .select2-container {
                    display: block!important;
                    width:100%!important;
                }

            </style>

            <div id="ywcca_widget_content">
                <p class="title_shortcode">
                    <label for="<?php echo $this->get_field_id("title");?>"><?php _e('Title', 'yith-woocommerce-category-accordion');?></label>
                    <input  class="widefat" type="text" id="<?php echo $this->get_field_id("title");?>" name="<?php echo $this->get_field_name("title");?>" placeholder="<?php _e('Insert a title', 'yith-woocommerce-category-accordion');?>" value="<?php echo $instance['title'];?>">
                </p>
                <p class="ywcca_select_field">
                    <label for="<?php echo esc_attr( $this->get_field_id( "how_show" ) );?>"><?php _e( 'Show in Accordion', 'yith-woocommerce-category-accordion');?></label>
                    <select id="<?php echo esc_attr( $this->get_field_id( "how_show" ) );?>" name="<?php echo esc_attr( $this->get_field_name( "how_show" ) );?>" class="ywcca_select_howshow widefat">
                        <option value="" <?php selected( "", $instance['how_show'] );?>><?php _e( 'Select an option', 'yith-woocommerce-category-accordion' );?></option>
                        <option value="wc" <?php selected( 'wc', $instance['how_show'] );?> ><?php _e( 'WooCommerce Category', 'yith-woocommerce-category-accordion' );?></option>
                        <option value="wp" <?php selected( 'wp', $instance['how_show'] );?> ><?php _e( 'Wordpress Category', 'yith-woocommerce-category-accordion' );?></option>
                        <option value="tag" <?php selected( 'tag', $instance['how_show'] );?> ><?php _e( 'Tags', 'yith-woocommerce-category-accordion' );?></option>
                        <option value="menu" <?php selected( 'menu', $instance['how_show'] );?> ><?php _e( 'Menu', 'yith-woocommerce-category-accordion' );?></option>
                    </select>
                </p>
                <div class="ywcca_wc_field" style="display:<?php echo $instance['how_show']=='wc'? 'block' : 'none';?>;">
                    <p class="ywcca_wc_sub_cat">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'show_wc_subcat' ) );?>"><?php _e( 'Show WooCommerce Subcategories','yith-woocommerce-category-accordion' );?></label>
                        <input type="checkbox" <?php checked( 'on', $instance['show_wc_subcat'] );?> id="<?php echo esc_attr( $this->get_field_id( 'show_wc_subcat' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'show_wc_subcat' ) );?>">
                    </p>
                    <p class="ywcca_wc_exclude">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'exclude_wc_cat' ) );?>"><?php _e( 'Exclude WooCommerce Categories','yith-woocommerce-category-accordion' );?></label>
                        <?php

                        $category_ids =   $instance['exclude_wc_cat']   ;

                        if( !is_array( $category_ids )  ){
                            $category_ids = explode( ',', $category_ids );
                        }

                        $json_ids   =   array();

                        foreach( $category_ids as $category_id ){

                            $cat_name   =   get_term_by( 'id', $category_id, 'product_cat' );
                            if( !empty( $cat_name ) )
                                $json_ids[ $category_id ] = '#'.$cat_name->term_id.'-'.$cat_name->name;
                        }

                            $args = array(
                                'id' => $this->get_field_id( 'exclude_wc_cat' ),
                                'class' => 'wc-product-search' . ( $is_default ? ' enhanced' : '' ),
                                'name' => $this->get_field_name( 'exclude_wc_cat' ),
                                'data-multiple' => true,
                                'data-action' => 'yith_category_accordion_json_search_wc_categories' ,
                                'data-placeholder' => __( 'Select categories', 'yith-woocommerce-category-accordion' ),
                                'data-selected' => $json_ids,
                                'value' =>  implode( ',',array_keys( $json_ids ) )
                             );

                        yit_add_select2_fields( $args );

                        ?>

                    </p>

                </div>
                <div class="ywcca_wp_field" style="display:<?php echo $instance['how_show']=='wp'? 'block' : 'none';?>;">
                    <p class="ywcca_wp_sub_field">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'show_wp_subcat' ) );?>"><?php _e( 'Show WordPress Subcategories','yith-woocommerce-category-accordion' );?></label>
                        <input type="checkbox" <?php checked( 'on', $instance['show_wp_subcat'] );?> id="<?php echo esc_attr( $this->get_field_id( 'show_wp_subcat' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'show_wp_subcat' ) );?>">
                    </p>
                    <p class="ywcca_wp_post_field">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'show_post' ) );?>"><?php _e( 'Show Last Post','yith-woocommerce-category-accordion' );?></label>
                        <input type="checkbox" <?php checked( 'on', $instance['show_post'] );?> id="<?php echo esc_attr( $this->get_field_id( 'show_post' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'show_post' ) );?>">
                    </p>
                    <p class="ywcca_wp_post_limit">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'post_limit' ) );?>"><?php _e( 'Number Post (-1 for all post )','yith-woocommerce-category-accordion' );?></label>
                        <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'post_limit' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'post_limit' ) );?>" value="<?php echo $instance['post_limit'];?>">
                    </p>
                    <p class="ywcca_wp_exclude">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'exclude_wp_cat' ) );?>"><?php _e( 'Exclude Wordpress Categories','yith-woocommerce-category-accordion' );?></label>
                        <?php
                        $category_ids =  $instance['exclude_wp_cat'] ;

                        if( !is_array( $category_ids ) ){
                            $category_ids = explode( ',', $category_ids );
                        }
                        $json_ids   =   array();

                        foreach( $category_ids as $category_id ){

                            $cat_name   =   get_term_by( 'id', $category_id, 'category' );
                            if( !empty( $cat_name ) )
                                $json_ids[ $category_id ] = '#'.$cat_name->term_id.'-'.$cat_name->name;
                        }

                        $args = array(
                            'id' => $this->get_field_id( 'exclude_wp_cat' ),
                            'class' => 'wc-product-search' . ( $is_default ? ' enhanced' : '' ),
                            'name' => $this->get_field_name( 'exclude_wp_cat' ),
                            'data-multiple' => true,
                            'data-action' => 'yith_json_search_wp_categories' ,
                            'data-placeholder' => __( 'Select categories', 'yith-woocommerce-category-accordion' ),
                            'data-selected' => $json_ids,
                            'value' =>  implode( ',',array_keys( $json_ids ) )
                        );

                        yit_add_select2_fields( $args );

                        ?>

                    </p>
                </div>
                <div class="ywcca_menu_field" style="display:<?php echo $instance['how_show']=='menu'? 'block' : 'none';?>;">
                    <?php
                    $menu_option    =   yith_get_navmenu();
                    ?>
                    <p class="ywcca_menu_multiselect" >
                        <label for="<?php echo esc_attr( $this->get_field_id( 'include_menu' ) );?>"><?php _e( 'Add menu in accordion', 'yith-woocommerce-category-accordion' );?></label>
                        <select id="<?php echo esc_attr( $this->get_field_id( 'include_menu' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'include_menu' ) );?>[]" multiple="multiple">
                            <?php
                                foreach( $menu_option as $key=>$val ){?>

                                  <option value="<?php echo esc_attr( $key );?>" <?php selected( in_array( $key, $instance['include_menu'] ) );?>><?php echo $val;?></option>
                            <?php    }
                            ?>
                        </select>
                    </p>

                </div>
                <div class="ywcca_tags_field" style="display:<?php echo $instance['how_show']=='tag'? 'block' : 'none';?>;">
                    <p class="ywcca_choose_tag_wc">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'tag_wc' ) );?>"><?php _e('WooCommerce Tag','yith-woocommerce-category-accordion');?></label>
                        <input type="checkbox" <?php checked( 'on', $instance['tag_wc'] );?> id="<?php echo esc_attr( $this->get_field_id( 'tag_wc' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'tag_wc' ) );?>">
                    </p>
                    <p class="ywcca_name_tag_wc">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'name_wc_tag' ) );?>"><?php _e('WooCommerce Tag Label','yith-woocommerce-category-accordion');?></label>
                       <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'name_wc_tag' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'name_wc_tag' ) );?>" value="<?php echo $instance['name_wc_tag'];?>">
                    </p>
                    <p class="ywcca_choose_tag_wp">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'tag_wp' ) );?>"><?php _e('Wordpress Tag','yith-woocommerce-category-accordion');?></label>
                        <input type="checkbox" <?php checked( 'on', $instance['tag_wp'] );?> id="<?php echo esc_attr( $this->get_field_id( 'tag_wp' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'tag_wp' ) );?>">
                    </p>
                    <p class="ywcca_name_tag_wp">
                        <label for="<?php echo esc_attr( $this->get_field_id( 'name_wp_tag' ) );?>"><?php _e('WordPress Tag Label','yith-woocommerce-category-accordion');?></label>
                        <input type="text" id="<?php echo esc_attr( $this->get_field_id( 'name_wp_tag' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'name_wp_tag' ) );?>" value="<?php echo $instance['name_wp_tag'];?>">
                    </p>
                </div>
                <p class="ywcca_highlight">
                    <label for="<?php echo esc_attr( $this->get_field_id( 'highlight_curr_cat' ) );?>"><?php _e( 'Highlight the current category','yith-woocommerce-category-accordion' );?></label>
                    <input type="checkbox" <?php checked( 'on', $instance['highlight_curr_cat'] );?> id="<?php echo esc_attr( $this->get_field_id( 'highlight_curr_cat' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'highlight_curr_cat' ) );?>">
                </p>
                <div class="ywcc_show_count_field" style="display:<?php echo $instance['how_show']=='wc' || $instance['how_show']=='wp' ? 'block' : 'none';?>;">
                <p class="ywcca_show_count">
                    <label for="<?php echo esc_attr( $this->get_field_id( 'show_count' ) );?>"><?php _e( 'Show Count','yith-woocommerce-category-accordion' );?></label>
                    <input type="checkbox" <?php checked( 'on', $instance['show_count'] );?> id="<?php echo esc_attr( $this->get_field_id( 'show_count' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'show_count' ) );?>">
                </p>
                </div>
                <p class="ywcca_select_style">
                    <label for="<?php echo esc_attr( $this->get_field_id( 'acc_style' ) );?>"><?php _e('Style','yith-woocommerce-category-accordion');?></label>
                    <select id="<?php echo esc_attr( $this->get_field_id( 'acc_style' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'acc_style' ) );?>">
                        <option value="style_1" <?php selected( 'style_1', $instance['acc_style'] );?>><?php _e('Style 1', 'yith-woocommerce-category-accordion');?></option>
                        <option value="style_2" <?php selected( 'style_2', $instance['acc_style'] );?>><?php _e('Style 2', 'yith-woocommerce-category-accordion');?></option>
                        <option value="style_3" <?php selected( 'style_3', $instance['acc_style'] );?>><?php _e('Style 3', 'yith-woocommerce-category-accordion');?></option>
                        <option value="style_4" <?php selected( 'style_4', $instance['acc_style'] );?>><?php _e('Style 4', 'yith-woocommerce-category-accordion');?></option>
                    </select>
                </p>
                <p class="ywcca_exclude_page">
                    <label for="<?php echo esc_attr( $this->get_field_id( 'exclude_page' ) );?>"><?php _e( 'Hide Accordion in  pages','yith-woocommerce-category-accordion' );?></label>
                    <?php
                    $post_ids = $instance['exclude_page'] ;

                    if( !is_array( $post_ids ) ){
                        $post_ids = explode( ',', $post_ids );
                    }
                    $json_ids   =   array();

                    foreach( $post_ids as $post_id ){

                        $post_name   =   get_post( $post_id );
                        if( !empty( $post_name ) )
                            $json_ids[ $post_id ] = $post_name->post_title;
                    }
                    $args = array(
                        'id' => $this->get_field_id( 'exclude_page' ),
                        'class' => 'wc-product-search' . ( $is_default ? ' enhanced' : '' ),
                        'name' => $this->get_field_name( 'exclude_page' ),
                        'data-multiple' => true,
                        'data-action' => 'yith_json_search_wp_pages' ,
                        'data-placeholder' => __( 'Select page', 'yith-woocommerce-category-accordion' ),
                        'data-selected' => $json_ids,
                        'value' =>  implode( ',',array_keys( $json_ids ) )
                    );

                    yit_add_select2_fields( $args );
                    ?>

                </p>
                <p class="ywcca_exclude_post">
                    <label for="<?php echo esc_attr( $this->get_field_id( 'exclude_post' ) );?>"><?php _e( 'Hide Accordion in  posts','yith-woocommerce-category-accordion' );?></label>
                    <?php
                    $post_ids =  $instance['exclude_post'] ;
                    if( !is_array( $post_ids ) ){
                        $post_ids = explode( ',', $post_ids );
                    }
                    $json_ids   =   array();

                    foreach( $post_ids as $post_id ){

                        $post_name   =   get_post( $post_id );
                        if( !empty( $post_name ) )
                            $json_ids[ $post_id ] = $post_name->post_title;
                    }
                    $args = array(
                        'id' => $this->get_field_id( 'exclude_post' ),
                        'class' => 'wc-product-search' . ( $is_default ? ' enhanced' : '' ),
                        'name' => $this->get_field_name( 'exclude_post' ),
                        'data-multiple' => true,
                        'data-action' => 'yith_json_search_wp_posts' ,
                        'data-placeholder' => __( 'Select post', 'yith-woocommerce-category-accordion' ),
                        'data-selected' => $json_ids,
                        'value' =>  implode( ',',array_keys( $json_ids ) )
                    );

                    yit_add_select2_fields( $args );
                    ?>

                </p>
                <p class="ywcca_orderby" style="display:<?php echo $instance['how_show']=='menu'? 'none' : 'block';?>;">
                    <label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) );?>"><?php _e('Order By','yith-woocommerce-category-accordion');?></label>
                    <select class="ywcca_type_order" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) );?>">
                        <option value="name" <?php selected( 'name', $instance['orderby'] );?>><?php _e('Name', 'yith-woocommerce-category-accordion');?></option>
                        <option value="count" <?php selected( 'count', $instance['orderby'] );?>><?php _e('Count', 'yith-woocommerce-category-accordion');?></option>
                        <option value="id" <?php selected( 'id', $instance['orderby'] );?>><?php _e('ID', 'yith-woocommerce-category-accordion');?></option>
                        <?php if( $instance['how_show'] == 'wc' ):;?>
                        <option value="menu_order" <?php selected( 'menu_order', $instance['orderby'] );?>><?php _e('WooCommerce Order','yith-woocommerce-category-accordion' );?></option>
                        <?php endif;?>
                    </select>
                    <select class="ywcca_order" id="<?php echo esc_attr( $this->get_field_id( 'order' ) );?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) );?>">
                        <option value="asc" <?php selected( 'asc', $instance['order'] );?>><?php _e('ASC', 'yith-woocommerce-category-accordion');?></option>
                        <option value="desc" <?php selected( 'desc', $instance['order'] );?>><?php _e('DESC', 'yith-woocommerce-category-accordion');?></option>

                    </select>
                </p>
            </div>

        <?php
        }


        public function update( $new_instance, $old_instance ){


            $instance   =   array();

            $instance['title']              =   isset( $new_instance['title'] )                 ?   $new_instance['title']    :   '';
            $instance['show_wc_subcat']     =   isset( $new_instance['show_wc_subcat'] )        ?   $new_instance['show_wc_subcat']    :   'off';
            $instance['show_wp_subcat']     =   isset( $new_instance['show_wp_subcat'] )        ?   $new_instance['show_wp_subcat']    :   'off';
            $instance['show_post']          =   isset( $new_instance['show_post'] )             ?   $new_instance['show_post'] : 'off';
            $instance['highlight_curr_cat'] =   isset( $new_instance['highlight_curr_cat'] )    ?   $new_instance['highlight_curr_cat']    :   'off';
            $instance['acc_style']          =   isset( $new_instance['acc_style'] )             ?   $new_instance['acc_style']      : 'style_1';
            $instance['exclude_wc_cat']     =   isset( $new_instance['exclude_wc_cat'] )        ?   esc_sql( $new_instance['exclude_wc_cat'] ) : '';
            $instance['exclude_wp_cat']     =   isset( $new_instance['exclude_wp_cat'] )        ?   esc_sql( $new_instance['exclude_wp_cat']  ): '';
            $instance['exclude_page']       =   isset( $new_instance['exclude_page'] )          ?   $new_instance['exclude_page']   :   '';
            $instance['exclude_post']       =   isset( $new_instance['exclude_post'] )          ?   $new_instance['exclude_post']   :   'off';
            $instance['how_show']           =   isset( $new_instance['how_show'] )              ?   $new_instance['how_show']       :   '';
            $instance['include_menu']       =   isset( $new_instance['include_menu'] )          ?   $new_instance['include_menu']       :   array();
            $instance['show_count']         =   isset( $new_instance['show_count'] )            ?   $new_instance['show_count']    :   'off';
            $instance['orderby']            =   isset( $new_instance['orderby'] )               ?   $new_instance['orderby']    :  'id';
            $instance['order']              =   isset( $new_instance['order'] )                 ?   $new_instance['order']      : 'asc';
            $instance['tag_wc']             =   isset( $new_instance['tag_wc'] )                ?   $new_instance['tag_wc'] : 'off';
            $instance['tag_wp']             =   isset( $new_instance['tag_wp'] )                ?   $new_instance['tag_wp'] : 'off';
            $instance['post_limit']         =   isset( $new_instance['post_limit'] )            ?   $new_instance['post_limit']      : '-1';
            $instance['name_wc_tag']        =   isset( $new_instance['name_wc_tag'] )           ? $new_instance['name_wc_tag']      : __('WooCommerce TAGS','yith-woocommerce-category-accordion');
            $instance['name_wp_tag']        =  isset( $new_instance['name_wp_tag'] )             ? $new_instance['name_wp_tag']      : __('WordPress TAGS','yith-woocommerce-category-accordion');



            return $instance;
        }
    }
}