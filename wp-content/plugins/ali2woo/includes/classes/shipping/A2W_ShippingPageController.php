<?php
/**
 * Description of A2W_ShippingPageController
 *
 * @author MA_GROUP
 * 
 * @autoload: a2w_admin_init
 */
if (!class_exists('A2W_ShippingPageController')):

    class A2W_ShippingPageController extends A2W_AbstractAdminPage {

        public function __construct() {
            parent::__construct(__('Shipping List', 'ali2woo'), __('Shipping List', 'ali2woo'), 'import', 'edit.php?post_type=a2w_shipping', 30, 1);

            add_action('admin_init', array($this, 'admin_init'));

            add_action('manage_a2w_shipping_posts_columns', array($this, 'get_columns'));
            add_action('manage_a2w_shipping_posts_custom_column', array($this, "edit_columns"));
            add_action('save_post', array($this, 'save_details'), 10, 3);
        }

        public function render($params = array()) {
            
        }

        public function get_columns($columns) {
            $columns = array(
                "cb" => '<input type="checkbox">',
                "title" => _x('Shipping name', 'Shipping List page', 'ali2woo'),
                "initial_name" => _x('Initial Name', 'Shipping List page', 'ali2woo'),
                "service_name" => _x('Service name', 'Shipping List page', 'ali2woo'),
                "use_price_rule" => _x('Enable price rule', 'Shipping List page', 'ali2woo'),
                "status" => _x('Status', 'Shipping List page', 'ali2woo'),
            );

            return $columns;
        }

        public function edit_columns($column) {
            global $post;

            switch ($column) {
                case "initial_name":
                    echo get_post_meta($post->ID, 'a2w_text_initial_name', true);
                    break;
                case "service_name":
                    echo get_post_meta($post->ID, 'a2w_service_name', true);
                    break;
                case "use_price_rule":
                    echo A2W_ShippingPriceFormula::allow_post_price_rule($post->ID) ? "yes" : "no";
                    break;
                case "status":
                    echo $post->post_status;
                    break;
            }

            return $column;
        }

        public function admin_init() {
            add_meta_box('a2w_shipping_settings_metabox', 'Shipping settings', array($this, 'settings_metabox'), 'a2w_shipping', 'normal', 'high');
        }

        public function settings_metabox() {
            global $post;
            $custom = get_post_custom($post->ID);

            $initial_name = $custom['a2w_text_initial_name'][0];
            $service_name = $custom['a2w_service_name'][0];
          
            $use_price_rule_checked = A2W_ShippingPriceFormula::allow_post_price_rule($post->ID) ? "checked" : "";
            ?>
            <table class="form-table">
                <tr><th><label><?php _ex('Initial Name', 'Shipping method Edit page', 'ali2woo');?></label></th><td><input type="text" value="<?php echo $initial_name; ?>" readonly><br/><em>readonly</em></td></tr>
                <tr><th><label><?php _ex('Service Name', 'Shipping method Edit page', 'ali2woo');?></label></th><td><input type="text" value="<?php echo $service_name; ?>" readonly><br/><em>readonly</em></td></tr>
                <tr><th><label><?php _ex('Enable price rule', 'Shipping method Edit page', 'ali2woo');?></label></th><td><input name="a2w_use_price_rule" type="checkbox" value="1" <?php echo $use_price_rule_checked; ?>><br/><em></em></td></tr>
            </table>
            <?php
        }

        public function save_details($post_id, $post, $update) {
        
            $post_type = get_post_type($post_id);
            if ( "a2w_shipping" != $post_type ) return;
            
            if ( isset( $_POST['a2w_use_price_rule'] ) ) {
                update_post_meta( $post_id, 'a2w_use_price_rule', 1 );
            } else {
                update_post_meta( $post_id, 'a2w_use_price_rule', 0 );
            }
        }

    }

    

	
endif;
