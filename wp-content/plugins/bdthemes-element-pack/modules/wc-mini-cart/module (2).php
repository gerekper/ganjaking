<?php

namespace ElementPack\Modules\WcMiniCart;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {


    const TEMPLATE_MINI_CART = 'cart/mini-cart.php';
    const OPTION_NAME_USE_MINI_CART = 'use_mini_cart_template';


    public function __construct() {

        parent::__construct();

        //        if ( ! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && is_admin() ) {
        //            add_action( 'init', [ $this, 'register_wc_hooks' ], 5 );
        //        }

        wp_enqueue_script('wc-cart-fragments');

        add_filter('woocommerce_add_to_cart_fragments', [$this, 'element_pack_mini_cart_fragment']);
        add_filter('woocommerce_locate_template', [$this, 'woocommerce_locate_template'], 12, 3);

    }


    public function get_name() {
        return 'wc-mini-cart';
    }

    public function get_widgets() {

        $widgets = ['WC_Mini_Cart'];

        return $widgets;
    }

    public function woocommerce_locate_template($template, $template_name, $template_path) {

        if (self::TEMPLATE_MINI_CART !== $template_name) {
            return $template;
        }

        $plugin_path = BDTEP_MODULES_PATH . 'wc-mini-cart/wc-templates/';

        if (file_exists($plugin_path . $template_name)) {
            $template = $plugin_path . $template_name;
        }

        return $template;
    }

    public function element_pack_mini_cart_fragment($fragments) {
        global $woocommerce;

        ob_start();

?>
        <span class="bdt-mini-cart-inner">
            <span class="bdt-cart-button-text">
                <span class="bdt-mini-cart-price-amount">
                    <?php echo WC()->cart->get_cart_subtotal(); ?>
                </span>
            </span>
            <span class="bdt-mini-cart-button-icon">
                <span class="bdt-cart-badge">
                    <?php echo WC()->cart->get_cart_contents_count(); ?>
                </span>
                <span class="bdt-cart-icon">
                    <i class="ep-icon-cart" aria-hidden="true"></i>
                </span>
            </span>
        </span>

<?php
        $fragments['a.bdt-mini-cart-button .bdt-mini-cart-inner'] = ob_get_clean();

        return $fragments;
    }
}
