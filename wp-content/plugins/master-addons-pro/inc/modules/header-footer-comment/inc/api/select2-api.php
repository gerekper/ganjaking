<?php 
namespace MasterHeaderFooter;
use MasterHeaderFooter\Handler_Api;

defined( 'ABSPATH' ) || exit;

class JLTMA_Ajax_Select2_Api extends Handler_Api {

    public function config(){
        $this->prefix = 'select2';
    }


    public function get_post_list(){

        if(!current_user_can('edit_posts')){
            return;
        }

        $query_args = [
            'post_type'         => 'post',
            'post_status'       => 'publish',
            'posts_per_page'    => 15,
        ];

        if(isset($this->request['ids'])){
            $ids = explode(',', $this->request['ids']);
            $query_args['post__in'] = $ids;
        }
        if(isset($this->request['s'])){
            $query_args['s'] = $this->request['s'];
        }

        $query = new \WP_Query($query_args);
        $options = [];
        if($query->have_posts()):
            while ($query->have_posts()) {
                $query->the_post();
                $options[] = [ 'id' => get_the_ID(), 'text' => get_the_title() ];
            }
        endif;

        return ['results' => $options];
        wp_reset_postdata();
    }
    
    public function get_page_list(){
        if(!current_user_can('edit_posts')){
            return;   
           }
        $query_args = [
            'post_type'         => 'page',
            'post_status'       => 'publish',
            'posts_per_page'    => 15,
        ];

        if(isset($this->request['ids'])){
            $ids = explode(',', $this->request['ids']);
            $query_args['post__in'] = $ids;
        }
        if(isset($this->request['s'])){
            $query_args['s'] = $this->request['s'];
        }

        $query = new \WP_Query($query_args);
        $options = [];
        if($query->have_posts()):
            while ($query->have_posts()) {
                $query->the_post();
                $options[] = [ 'id' => get_the_ID(), 'text' => get_the_title() ];
            }
        endif;

        return ['results' => $options];
        wp_reset_postdata();
    }

    public function get_singular_list(){
        $query_args = [
            'post_status'       => 'publish',
            'posts_per_page'    => 15,
            'post_type'         => 'any'
        ];

        if(isset($this->request['ids'])){
            $ids = explode(',', $this->request['ids']);
            $query_args['post__in'] = $ids;
        }
        if(isset($this->request['s'])){
            $query_args['s'] = $this->request['s'];
        }

        $query = new \WP_Query($query_args);
        $options = [];
        if($query->have_posts()):
            while ($query->have_posts()) {
                $query->the_post();
                $options[] = [ 'id' => get_the_ID(), 'text' => get_the_title() ];
            }
        endif;

        return ['results' => $options];
        wp_reset_postdata();
    }

    public function get_category(){

        $taxonomy	 = 'category';
        $query_args = [
            'taxonomy'      => ['category'], // taxonomy name
            'orderby'       => 'name', 
            'order'         => 'DESC',
            'hide_empty'    => true,
            'number'        => 6
        ];

        if(isset($this->request['ids'])){
            $ids = explode(',', $this->request['ids']);
            $query_args['include'] = $ids;
        }
        if(isset($this->request['s'])){
            $query_args['name__like'] = $this->request['s'];
        }

        $terms = get_terms( $query_args );


        $options = [];
        $count = count($terms);
        if($count > 0):
            foreach ($terms as $term) {
                $options[] = [ 'id' => $term->term_id, 'text' => $term->name ];
            }
        endif;      
        return ['results' => $options];
    }

    public function get_product_list(){
        $query_args = [
            'post_type'         => 'product',
            'post_status'       => 'publish',
            'posts_per_page'    => 15,
        ];

        if(isset($this->request['ids'])){
            $ids = explode(',', $this->request['ids']);
            $query_args['post__in'] = $ids;
        }
        if(isset($this->request['s'])){
            $query_args['s'] = $this->request['s'];
        }

        $query = new \WP_Query($query_args);
        $options = [];
        if($query->have_posts()):
            while ($query->have_posts()) {
                $query->the_post();
                $options[] = [ 'id' => get_the_ID(), 'text' => get_the_title() ];
            }
        endif;

        return ['results' => $options];
        wp_reset_postdata();
    }

    public function get_product_cat(){
        $query_args = [
            'taxonomy'      => ['product_cat'], // taxonomy name
            'orderby'       => 'name', 
            'order'         => 'DESC',
            'hide_empty'    => false,
            'number'        => 6
        ];

        if(isset($this->request['ids'])){
            $ids = explode(',', $this->request['ids']);
            $query_args['include'] = $ids;
        }
        if(isset($this->request['s'])){
            $query_args['name__like'] = $this->request['s'];
        }

        $terms = get_terms( $query_args );


        $options = [];
        $count = count($terms);
        if($count > 0):
            foreach ($terms as $term) {
                $options[] = [ 'id' => $term->term_id, 'text' => $term->name ];
            }
        endif;      
        return ['results' => $options];
    }
    
}
new JLTMA_Ajax_Select2_Api();