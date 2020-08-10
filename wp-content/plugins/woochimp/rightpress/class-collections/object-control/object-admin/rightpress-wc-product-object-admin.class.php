<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wc-object-admin.class.php';
require_once 'interfaces/rightpress-wc-product-object-admin-interface.php';

/**
 * WooCommerce Product Admin
 *
 * @class RightPress_WC_Product_Object_Admin
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Product_Object_Admin extends RightPress_WC_Object_Admin implements RightPress_WC_Product_Object_Admin_Interface
{

    // TODO: maybe add some hidden input with product settings version number so that we reject submits of pages that were opened before update (we changed field names for existing fields)

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Call parent constructor
        parent::__construct();

        // Print checkboxes
        add_filter('product_type_options', array($this, 'add_product_checkbox'));
        add_action('woocommerce_variation_options', array($this, 'print_variation_checkbox'), 10, 3);

        // Print settings
        add_action('woocommerce_product_options_general_product_data', array($this, 'print_product_settings'));
        add_action('woocommerce_product_after_variable_attributes', array($this, 'print_variation_settings'), 10, 3);

        // Process submitted settings
        add_action('woocommerce_admin_process_product_object', array($this, 'process_product_settings'));
        add_action('woocommerce_save_product_variation', array($this, 'process_product_variation_settings'), 10, 2);

        // Add product list shared column value
        RightPress_Loader::load_component('rightpress-product-list-shared-column');
        add_filter('rightpress_product_list_shared_column_values', array($this, 'add_product_list_shared_column_value'), 10, 2);
    }

    /**
     * Add product checkbox
     *
     * @access public
     * @param array $checkboxes
     * @return array
     */
    public function add_product_checkbox($checkboxes)
    {

        global $post;

        // Load product
        if ($product = wc_get_product($post->ID)) {

            // Product does not have children (e.g. variations have their own checkboxes)
            if (!RightPress_Help::wc_product_has_children($product)) {

                // Load subscription product
                if ($subscription_product = subscriptio_get_subscription_product($product)) {

                    // Get value
                    $value = $subscription_product->is_subscription_product() ? 'yes' : 'no';

                    // Add checkbox
                    $checkboxes[$this->get_controller()->get_object_key()] = array(
                        'id' => $this->get_controller()->get_object_key(),
                        'wrapper_class' => 'show_if_simple',
                        'label' => $this->get_checkbox_label(),
                        'description' => $this->get_checkbox_description(),
                        // Note: We are passing actual value here, this will work provided nothing changes in
                        // WooCommerce html-product-data-panel.php and there is no meta set on product
                        // with key equal to $this->get_controller()->get_object_key()
                        'default' => $value,
                    );
                }
            }
        }

        // Return checkboxes array
        return $checkboxes;
    }

    /**
     * Print variation checkbox
     *
     * WC31: Products will no longer be posts ($variation->ID)
     *
     * @access public
     * @param int $loop
     * @param array $variation_data
     * @param object $variation
     * @return void
     */
    public function print_variation_checkbox($loop, $variation_data, $variation)
    {

        // Check if functionality is enabled for this variation
        $is_enabled = $this->get_controller()->is_enabled_for_product($variation->ID);

        // TODO: html validation errors are not visible when variation panel is collapsed

        // Format and print checkbox
        $input = '<input type="checkbox" class="checkbox ' . $this->get_controller()->get_object_key() . '-variable" name="' . $this->get_controller()->get_object_key() . '[' . $loop . ']" ' . checked($is_enabled, true, false) . ' />';
        echo '<label class="tips" data-tip="' . $this->get_checkbox_description() . '"> ' . $this->get_checkbox_label() . ' ' . $input . '</label>';
    }

    /**
     * Process product settings
     *
     * Product object is passed by reference and object data is saved by
     * WooCommerce so we only need to set meta data
     *
     * @access public
     * @param object $product
     * @param bool $enabled
     * @param array $posted
     * @return void
     */
    public function process_product_settings($product, $enabled = null, $posted = null)
    {

        // Do not process products with children here
        if (RightPress_Help::wc_product_has_children($product)) {
            return;
        }

        // Get posted data
        if (!isset($enabled)) {

            // Get checkbox name and settings key
            $checkbox_name      = $this->get_controller()->get_object_key();
            $settings_prefix    = $checkbox_name . '_settings';

            // Functionality was enabled
            if (!empty($_POST[$checkbox_name]) && !empty($_POST[$settings_prefix])) {

                $posted = $_POST[$settings_prefix];
                $enabled = true;
            }
            // Functionality was disabled
            else {

                $posted = null;
                $enabled = false;
            }
        }

        try {

            // Load object
            if ($object = $this->get_controller()->get_object($product)) {

                // Functionality is enabled
                if ($enabled) {

                    // Merge product settings
                    $settings = array_merge(array($this->get_controller()->get_object_name() => true), $posted);

                    // Validate and sanitize settings
                    $settings = $this->sanitize_product_settings($settings, $object);

                    // Set properties
                    $object->set_properties($settings);

                    // Save updated configuration
                    $object->save();
                }
                // Functionality was disabled
                else {

                    // Data cleanup
                    $object->clear();
                }
            }
        }
        catch (RightPress_Exception $e) {

            // Show error to admin
            WC_Admin_Meta_Boxes::add_error($e->getMessage());
        }
    }

    /**
     * Process product variation settings
     *
     * @access public
     * @param int $variation_id
     * @param int $i
     * @return void
     */
    public function process_product_variation_settings($variation_id, $i)
    {

        // Load variation object
        if ($product_variation = wc_get_product($variation_id)) {

            // Get checkbox name and settings key
            $checkbox_name      = $this->get_controller()->get_object_key();
            $settings_prefix    = $checkbox_name . '_settings';

            // Functionality was enabled
            if (!empty($_POST[$checkbox_name][$i]) && !empty($_POST[$settings_prefix][$i])) {

                $posted = $_POST[$settings_prefix][$i];
                $enabled = true;
            }
            // Functionality was disabled
            else {

                $posted = null;
                $enabled = false;
            }

            // Process settings
// TODO: Handle exceptions
            $this->process_product_settings($product_variation, $enabled, $posted);

            // Save variation object
            $product_variation->save();
        }
    }

    /**
     * Add product list shared column value
     *
     * @access public
     * @param array $values
     * @param int $post_id
     * @return array
     */
    public function add_product_list_shared_column_value($values, $post_id)
    {

        // Check if functionality is enabled
        if ($this->get_controller()->is_enabled_for_product($post_id)) {

            // Add column value
            $values[] = $this->get_product_list_shared_column_value($post_id);
        }

        // Return values
        return $values;
    }





}
