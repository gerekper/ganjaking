<?php

namespace ElementPack;

use Elementor\Plugin;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
final class Context_Menu_Markup {
    public function __construct() {
        add_action('wp_head', [$this, 'styles']);
        add_action('wp_footer', [$this, 'ep_context_menu_should_display']);
    }

    public function styles() {
        wp_register_style('context-menu', BDTEP_URL . 'assets/css/ep-context-menu.css', [], BDTEP_VER);
        wp_enqueue_style('context-menu');
    }

    public function ep_context_menu_settings($setting_id) {
        global $context_menu_settings;
        $return = '';
        if (!isset($context_menu_settings['kit_settings'])) {
            $kit = Plugin::$instance->documents->get(Plugin::$instance->kits_manager->get_active_id(), false);
            $context_menu_settings['kit_settings'] = $kit->get_settings();
        }

        if (isset($context_menu_settings['kit_settings'][$setting_id])) {
            $return = $context_menu_settings['kit_settings'][$setting_id];
        }

        return apply_filters('context_menu_settings' . $setting_id, $return);
    }


    public function ep_context_menu_should_display() {
        if ($this->ep_context_menu_settings('ep_context_menu_enable') === 'yes') {
            if ($this->ep_context_menu_settings('ep_context_menu_only_loggin_in') === 'yes') {
                if (is_user_logged_in()) {
                    if ($this->ep_context_menu_settings('ep_context_menu_specific_page') === 'yes') {
                        $selected_ids = $this->ep_context_menu_settings('ep_context_menu_page_ids');
                        $results = explode(',', $selected_ids);
                        $current_id = get_the_ID();
                        if (in_array($current_id, $results)) {
                            $this->ep_render_data();
                            $this->load_context_menu_scripts();
                        }
                    } else {
                        $this->ep_render_data();
                        $this->load_context_menu_scripts();
                    }
                }
            } else {
                if ($this->ep_context_menu_settings('ep_context_menu_specific_page') === 'yes') {
                    $selected_ids = $this->ep_context_menu_settings('ep_context_menu_page_ids');
                    $results = explode(',', $selected_ids);
                    $current_id = get_the_ID();
                    if (in_array($current_id, $results)) {
                        $this->ep_render_data();
                        $this->load_context_menu_scripts();
                    }
                } else {
                    $this->ep_render_data();
                    $this->load_context_menu_scripts();
                }
            }
        }
    }
    public function ep_render_data() {
        $menus = $this->ep_context_menu_settings('menus');
?>
        <div class="bdt-context-menu">
            <ul class="bdt-context" id="context-menu" style="display: none;">

                <?php foreach ($menus as $item) : ?>

                    <?php
                    $target = (!empty($item['menu_link']['is_external'])) ? 'target="_blank"' : '';
                    $nofollow = (!empty($item['menu_link']['nofollow'])) ? ' rel="nofollow"' : '';

                    if ($item['menu_type'] == 'child_start') {
                        $item_class = 'has-arrow';
                    } else {
                        $item_class = '';
                    }

                    ?>

                    <?php if ($item['menu_type'] !== 'child_end') : ?>
                        <li class="bdt-menu-item">
                            <a class="<?php echo $item_class; ?>" href="<?php echo esc_url($item['menu_link']['url']); ?>" <?php echo wp_kses_post($target);
                                                                                                                            echo wp_kses_post($nofollow); ?>>
                                <span>
                                    <?php if (!empty($item['menu_icon']['value'])) : ?>
                                        <span class="bdt-menu-icon">
                                            <?php Icons_Manager::render_icon($item['menu_icon'], ['aria-hidden' => 'true']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php echo wp_kses($item['menu_title'], element_pack_allow_tags('title')); ?>
                                </span>

                                <?php if ($item['menu_type'] == 'child_start') : ?>
                                    <i class="eicon-caret-right"></i>
                                <?php endif; ?>

                            </a>
                        <?php endif; ?>

                        <?php if ($item['menu_type'] == 'child_start') : ?>
                            <ul class="bdt-context sub">
                            <?php endif; ?>

                            <?php if ($item['menu_type'] == 'child_end') : ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if ($item['menu_type'] == 'item') : ?>
                        </li>
                    <?php endif; ?>

                <?php endforeach; ?>
            </ul>
        </div>
    <?php
    }

    public function load_context_menu_scripts() {
    ?>
        <script>
            (function($) {
                $(document).ready(function() {
                    $(function() {
                        var $doc = $(document),
                            $context = $(".bdt-context:not(.sub)");
                        $doc.on("contextmenu", function(e) {

                            var $window = $("body"),
                                $sub = $context.find(".sub");
                            $sub.removeClass("oppositeX oppositeY");

                            e.preventDefault();

                            var w = $context.width();
                            var h = $context.height();

                            var x = e.pageX;
                            var y = e.pageY;
                            var ww = $window.width();
                            var wh = $window.height();
                            var padx = 30;
                            var pady = 20;
                            var fx = x;
                            var fy = y;
                            var hitsRight = (x + w >= ww - padx);
                            var hitsBottom = (y + h >= wh - pady);


                            if (hitsRight) {
                                fx = fx - $context.width();
                            }

                            if (hitsBottom) {
                                fy = fy - $context.height();
                            }

                            $context
                                .css({
                                    left: fx - 1,
                                    top: fy - 1
                                });

                            var sw = $sub.width();
                            var sh = $sub.height();
                            var sx = $sub.offset().left;
                            var sy = $sub.offset().top;
                            var subHitsRight = (sx + sw - padx >= ww - padx);
                            var subHitsBottom = (sy + sh - pady >= wh - pady);

                            if (subHitsRight) {
                                $sub.addClass("oppositeX");
                            }

                            if (subHitsBottom) {
                                $sub.addClass("oppositeY");
                            }

                            $context.addClass("is-visible");
                            $context.css("display", 'inline-block');


                            $doc.on("mousedown", function(e) {
                                var $tar = $(e.target);

                                if (!$tar.is($context) &&
                                    !$tar.closest(".bdt-context").length) {

                                    $context.removeClass("is-visible");
                                    $doc.off(e);

                                }

                            });

                        });

                        $context.on("mousedown touchstart", "li:not(.nope)", function(e) {

                            if (e.which === 1) {

                                var $item = $(this);

                                $item.removeClass("active");

                                setTimeout(function() {
                                    $item.addClass("active");
                                }, 10);

                            }

                        });
                        $doc.click(function(event) {
                            var relX = event.pageX;
                            var relY = event.pageY;
                        });
                    });
                });
            })(jQuery);
        </script>
<?php }
}
new Context_Menu_Markup();
