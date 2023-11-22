<?php

namespace DynamicOOOS;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
\Elementor\Plugin::$instance->frontend->add_body_class('elementor-template-full-width');
get_header('shop');
$dce_default_options = get_option(\DCE_TEMPLATE_SYSTEM_OPTION);
$dce_elementor_templates = 'dyncontel_field_singleproduct';
$dce_default_template = $dce_default_options[$dce_elementor_templates];
do_action('woocommerce_before_single_product');
echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($dce_default_template);
do_action('woocommerce_after_single_product');
while (have_posts()) {
    the_post();
}
get_footer('shop');
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
