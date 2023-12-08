<?php

namespace ElementPack\Modules\TagsCloud\Skins;

use Elementor\Controls_Manager;
use ElementPack\Base\Module_Base;
use Elementor\Skin_Base as Elementor_Skin_Base;





if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Skin_Animated extends Elementor_Skin_Base
{

    public function get_id()
    {
        return 'bdt-animated';
    }

    public function get_title()
    {
        return __('Animated', 'bdthemes-element-pack');
    }


    public function render()
    {

        $settings = $this->parent->get_settings_for_display();

        $taxonomy_filter = (isset($settings['custom_post_type_input']) && !empty($settings['custom_post_type_input'])) ? $settings['custom_post_type_input'] : 'post_tag';

        $cloudSkin = 'animaitedTags';
        $tag_cloud = $this->parent->wp_tag_cloud(
            $cloudSkin,
            array(
                'taxonomy' => $taxonomy_filter, //$current_taxonomy,
                'echo' => false,
                // 'show_count' => '20', //$show_count,
            )
        );

        $globe_height = (isset($settings['globe_height']['size']) && !empty($settings['globe_height']['size']))
            ? $settings['globe_height']['size'] : 350;

        $this->parent->add_render_attribute('tag_animated', 'class', 'bdt-tags-cloud skin-tag-animated');
        $this->parent->add_render_attribute('tag_animated', 'id', 'bdt-canvas-' . $this->parent->get_id());
        $this->parent->add_render_attribute(
            [
                'tag_animated' => [
                    'data-settings' => [
                        wp_json_encode([
                            'idCanvas'           => 'bdt-canvas-' . $this->parent->get_id(),
                            'idTags'             => 'bdt-tags-' . $this->parent->get_id(),
                            'idmyCanvas'         => 'bdt-myCanvas-' . $this->parent->get_id(),
                            'textColour'         => !empty($settings['globe_color']) ? $settings['globe_color'] : '#111111',
                            'outlineColour'      => $settings['globe_outline_colour'], // default #ff00ff
                            'reverse'            => true,
                            'depth'              => $settings['globe_depth']['size'] / 100, // default 0.8 
                            'maxSpeed'           => $settings['globe_animation_speed']['size'] / 1000, // default 0.05 
                            'activeCursor'       => $settings['globe_active_cursor'], // pointer,  crosshair,  cursor, text, wait, progress, help 
                            'bgColour'           => $settings['globe_text_bg'], // #fff // text bg
                            'bgOutlineThickness' => $settings['globe_bg_outline_thickness']['size'], // 0
                            'bgRadius'           => $settings['globe_text_bg_radius']['size'], // text bg radius - default 0
                            'dragControl'        => ($settings['globe_drag_control'] == true) ? true : false, // default false 
                            'fadeIn'             => $settings['globe_fade_in']['size'] * 1000, //  circle visible on Start default 0 with milisecnds
                            'freezeActive'       => ($settings['globe_freeze_active'] == true) ? true : false, // defaults false -  it will stop moving when got a tag under pointer
                            'outlineDash'        => $settings['globe_outline_dash']['size'], // default 0
                            'outlineDashSpace'   => $settings['globe_outline_dash_space']['size'],
                            'outlineDashSpeed'   => $settings['globe_outline_dash_speed']['size'],
                            'outlineIncrease'    => $settings['globe_outline_increase']['size'],
                            'outlineMethod'      => $settings['globe_outline_method'], // outline, classic, block, colour, size, none
                            'outlineRadius'      => $settings['globe_outline_border_radius']['size'],
                            'outlineThickness'   => $settings['globe_outline_thickness']['size'],
                            'shadow'             => $settings['globe_shadow_color'],
                            'shadowBlur'         => $settings['globe_shadow_blur']['size'],
                            'wheelZoom'          => ($settings['globe_wheel_zoom'] == true) ? true : false
                        ]),
                    ],
                ],
            ]
        );

?>

        <div <?php echo $this->parent->get_render_attribute_string('tag_animated'); ?>>
            <canvas height="<?php echo $globe_height . 'px'; ?>" width="<?php echo $globe_height . 'px'; ?>" id="bdt-myCanvas-<?php echo $this->parent->get_id(); ?>">
                <p>Anything in here will be replaced on browsers that support the canvas element</p>
            </canvas>
            <div id="bdt-tags-<?php echo $this->parent->get_id(); ?>" style="display:none">
                <ul>
                    <?php
                    echo $tag_cloud;
                    ?>
                </ul>
            </div>
        </div>

<?php
    }
}
