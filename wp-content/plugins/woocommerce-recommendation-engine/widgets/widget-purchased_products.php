<?php

/**
 * Products Purchased Together Widget
 *
 * Gets and displays recommended products in an unordered list
 *
 * @author 	Lucas Stark
 * @category 	Widgets
 * @package 	WooCommerce_Recommender/Widgets
 * @version 	1.0
 * @extends 	WP_Widget
 */
class WooCommerce_Widget_Purcahsed_Products extends WP_Widget {

    /** Variable to store the recommended prodcuts for sorting. */
    private $similar_products;

    /** Variables to setup the widget. */
    var $woo_widget_cssclass;
    var $woo_widget_description;
    var $woo_widget_idbase;
    var $woo_widget_name;

    /**
     * constructor
     *
     * @access public
     * @return void
     */
    function __construct() {

        /* Widget variable settings. */
        $this->woo_widget_cssclass = 'widget_purchased_products';
        $this->woo_widget_description = __('Display a list of products frequently purchased together on your site.', 'wc_recommender');
        $this->woo_widget_idbase = 'woocommerce_purchased_products';
        $this->woo_widget_name = __('WooCommerce Purchased Products', 'wc_recommender');

        /* Widget settings. */
        $widget_ops = array('classname' => $this->woo_widget_cssclass, 'description' => $this->woo_widget_description);

        /* Create the widget. */
        parent::__construct('purchased-products', $this->woo_widget_name, $widget_ops);
    }

    /**
     * widget function.
     *
     * @see WP_Widget
     * @access public
     * @param array $args
     * @param array $instance
     * @return void
     */
    function widget($args, $instance) {
        global $post, $woocommerce;

        if (!is_product()) {
            return;
        }

        ob_start();
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('Purchased Together', 'wc_recommender') : $instance['title'], $instance, $this->id_base);
        if (!$number = (int) $instance['number'])
            $number = 10;
        else if ($number < 1)
            $number = 1;
        else if ($number > 15)
            $number = 15;

        $activity_type = 'completed';
        ?>

        <?php
        $query_args = array(
            'posts_per_page' => -1,
            'no_found_rows' => 1,
            'post_status' => 'publish',
            'post_type' => 'product');

        $query_args['post__in'] = array();

        $this->similar_products = woocommerce_recommender_get_purchased_together(get_the_ID(), $activity_type);
        if ($this->similar_products) {
            foreach ($this->similar_products as $product_id => $score) {
                if ($score > 0) {
                    $query_args['post__in'][] = $product_id;
                }
            }
        }

        if (!count($this->similar_products)) {
            return;
        }

        $query_args['meta_query'] = array();
        $query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
        $query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();

        $posts = get_posts($query_args);



        if ($posts && count($posts)) :

            woocommerce_recommender_sort_posts($posts, $this->similar_products);
            $product_block = array_chunk($posts, $number);
	    $p = array_values( $product_block[0] );
	    $products = array_merge( array(get_post(get_the_ID())), $p );

            ?>

            <?php echo $before_widget; ?>
            <?php if ($title) echo $before_title . $title . $after_title; ?>
            <ul class="product_list_widget">
                <?php
                foreach ($products as $post) :
                    setup_postdata($post);
                    global $post;
                    global $product;
                    ?>
                    <li><a href="<?php echo esc_url(get_permalink($post->ID)); ?>" title="<?php echo esc_attr($post->post_title ? $post->post_title : $post->ID); ?>">
                            <?php echo $product->get_image(); ?>
                            <?php if ($post->post_title) echo get_the_title($post->ID); else echo $post->ID; ?>

                        </a> <?php echo $product->get_price_html(); ?></li>

                <?php endforeach; ?>
            </ul>
            <?php echo $after_widget; ?>

            <?php
        endif;

        $content = ob_get_clean();

        if (isset($args['widget_id']))
            $cache[$args['widget_id']] = $content;

        echo $content;

        wp_reset_postdata();
    }

    private function sort_by_index($a, $b) {
        if ($this->similar_products[$a->ID] < $this->similar_products[$b->ID]) {
            return 1;
        } elseif ($this->similar_products[$a->ID] > $this->similar_products[$b->ID]) {
            return -1;
        } else {
            return 0;
        }
    }

    /**
     * update function.
     *
     * @see WP_Widget->update
     * @access public
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = (int) $new_instance['number'];

        return $instance;
    }

    /**
     * form function.
     *
     * @see WP_Widget->form
     * @access public
     * @param array $instance
     * @return void
     */
    function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        if (!isset($instance['number']) || !$number = (int) $instance['number']) {
            $number = 5;
        }
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'woocommerce'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of products to show:', 'woocommerce'); ?></label>
            <input id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="text" value="<?php echo esc_attr($number); ?>" size="3" /></p>

        <?php
    }

}