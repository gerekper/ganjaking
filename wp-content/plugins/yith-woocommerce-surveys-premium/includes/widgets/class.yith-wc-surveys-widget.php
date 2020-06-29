<?php
if( !defined( 'ABSPATH' ) )
    exit;
if( !class_exists( 'YITH_WC_Surveys_Widget' ) ) {

    class YITH_WC_Surveys_Widget extends WP_Widget
    {
        public function __construct()
        {
            parent::__construct(
                'yith-wc-surveys',
                __('YITH WooCommerce Surveys', 'yith-woocommerce-surveys'),
                array('description' => __('Show your Surveys in sidebar!', 'yith-woocommerce-surveys'))
            );
        }

        /**
         * @author YIThemes
         * @since 1.0.0
         * @param array $instance
         */
        public function form($instance)
        {

            $default = array(

                'survey_id' => '',
                'title'      => ''

            );

            $instance = wp_parse_args($instance, $default);

            $surveys = YITH_Surveys_Type()->get_other_surveys();

            ?>
            <p class="title_shortcode">
                <label for="<?php echo $this->get_field_id("title");?>"><?php _e('Title', 'yith-woocommerce-surveys');?></label>
                <input  class="widefat" type="text" id="<?php echo $this->get_field_id("title");?>" name="<?php echo $this->get_field_name("title")
                ;?>" placeholder="<?php _e('Enter a title', 'yith-woocommerce-surveys');?>" value="<?php echo $instance['title'];?>">
            </p>
            <p>
                <label
                    for="<?php echo esc_attr($this->get_field_id('survey_id')); ?>"><?php _e('Select a Survey', 'yith-woocommerce-surveys'); ?></label>
                <select id="<?php esc_attr($this->get_field_id('survey_id')); ?>"
                        name="<?php echo esc_attr($this->get_field_name('survey_id')); ?>">
                    <option
                        value="" <?php selected('', $instance['survey_id']); ?>><?php _e('Select a Survey', 'yith-woocommerce-surveys'); ?></option>
                    <?php foreach ($surveys as $survey):
                        $title = get_the_title( $survey );?>
                        <option
                            value="<?php echo esc_attr($survey); ?>" <?php selected($survey, $instance['survey_id']); ?>><?php echo $title; ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
        <?php
        }

        /** update widget args
         * @author YITHEMES
         * @since 1.0.0
         * @param array $new_instance
         * @param array $old_instance
         * @return array
         */
        public function update($new_instance, $old_instance)
        {

            $instance = array();
            $instance['survey_id'] = isset($new_instance['survey_id']) ? $new_instance['survey_id'] : '';
            $instance['title'] = isset($new_instance['title']) ? $new_instance['title'] : '';

            return $instance;

        }

        /**print widget in front-end
         * @author YITHEMES
         * @since 1.0.0
         * @param array $args
         * @param array $instance
         */
        public function widget( $args, $instance )
        {
            extract($args);

            $title = $instance['title'];
            $survey_id = $instance['survey_id'];

            $survey_visible_in = get_post_meta( $survey_id, '_yith_survey_visible_in', true );
            if( 'other_page' === $survey_visible_in ) {

                $survey_shortcode = '[yith_wc_surveys survey_id="' . $survey_id . '"]';

                echo $before_widget;

                echo $before_title . $title . $after_title;

                echo do_shortcode($survey_shortcode);

                echo $after_widget;
            }
        }
    }
}

