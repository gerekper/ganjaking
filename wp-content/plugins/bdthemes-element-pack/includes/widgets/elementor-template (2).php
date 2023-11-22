<?php
// Adds widget: Element Pack Elementor Template
use ElementPack\Element_Pack_Loader;


if (!defined('ABSPATH')) exit; // Exit if accessed directly
class Element_Pack_Elementor_Template_Widget extends WP_Widget {

    private $sidebar_id;

    /**
     * Element_Pack_Elementor_Template_Widget constructor.
     */
    function __construct() {
        parent::__construct(
            'elementortemplate_widget',
            esc_html__('Elementor Template', 'bdthemes-element-pack')
        );
    }

    /**
     * widget area, template will render here based on template id
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if (!empty($instance['template_id']) && 'publish' === get_post_status($instance['template_id'])) {
            $this->sidebar_id = $args['widget_id'];

            add_filter('elementor/frontend/builder_content_data', [$this, 'filter_duplicate_data']);

            echo Element_Pack_Loader::elementor()->frontend->get_builder_content_for_display($instance['template_id']);

            remove_filter('elementor/frontend/builder_content_data', [$this, 'filter_duplicate_data']);

            unset($this->sidebar_id);
        }

        echo $args['after_widget'];
    }

    /**
     * filter duplicate data from template library data for better result
     * @param $data
     * @return array|mixed
     */
    public function filter_duplicate_data($data) {
        if (!empty($data)) {
            $data = Element_Pack_Loader::elementor()->db->iterate_data($data, function ($element) {
                if ('widget' === $element['elType'] && 'sidebar' === $element['widgetType'] && $this->sidebar_id === $element['settings']['sidebar']) {
                    $element['settings']['sidebar'] = null;
                }

                return $element;
            });
        }

        return $data;
    }

    /**
     * widget form for wp admin widget area
     * @param array $instance
     * @return string|void
     */
    public function form($instance) {
        $default = [
            'title'       => '',
            'template_id' => '',
        ];

        $instance = array_merge($default, $instance);

        $template_list = Element_Pack_Loader::elementor()->templates_manager->get_source('local')->get_items();

        if (empty($template_list)) {
            echo __('Template Not Found!', 'bdthemes-element-pack');
            return;
        }

?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e('Title', 'bdthemes-element-pack'); ?>
                :</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('template_id')); ?>">
                <?php esc_attr_e('Select Template', 'bdthemes-element-pack'); ?>
                :</label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('template_id')); ?>" name="<?php echo esc_attr($this->get_field_name('template_id')); ?>">
                <option value="">— <?php _e('Select', 'bdthemes-element-pack'); ?> —</option>
                <?php
                foreach ($template_list as $template) :
                    $selected_template = selected($template['template_id'], $instance['template_id']);
                ?>
                    <option value="<?php echo $template['template_id']; ?>" <?php echo $selected_template; ?> data-type="<?php echo esc_attr($template['type']); ?>">
                        <?php echo $template['title']; ?> (<?php echo $template['type']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>

        </p>
<?php
    }

    /**
     * wordpress widget update procedure
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance) {
        $instance                = [];
        $instance['title']       = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['template_id'] = $new_instance['template_id'];

        return $instance;
    }
}

/**
 * register the widget class to widget action
 */
function register_ep_elementor_template_widget() {
    register_widget('Element_Pack_Elementor_Template_Widget');
}

add_action('widgets_init', 'register_ep_elementor_template_widget');
