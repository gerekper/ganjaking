<?php

namespace ElementPack\Modules\TagsCloud\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Utils;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Skin_Cloud extends Elementor_Skin_Base
{

    public function get_id()
    {
        return 'bdt-cloud';
    }

    public function get_title()
    {
        return __('Typography', 'bdthemes-element-pack');
    }

    public function render()
    {
        $settings = $this->parent->get_settings_for_display();
        $cloudSkin = 'cloudSkin';

        $taxonomy_filter = (isset($settings['custom_post_type_input']) && !empty($settings['custom_post_type_input'])) ? $settings['custom_post_type_input'] : 'post_tag';

        $tag_cloud = $this->parent->wp_tag_cloud(
            $cloudSkin,
            array(
                'taxonomy' => $taxonomy_filter, //$current_taxonomy,
                'echo' => false,
                // 'show_count' => '20', //$show_count,
            )
        );

        $cloud_color = $settings['cloud_color'];

        if ($settings['cloud_color'] == 'custom') {
            $cloud_color = (!empty($settings['cloud_custom_color'])) ? $settings['cloud_custom_color'] : '#08AEEC';
        }

        $this->parent->add_render_attribute('skin_typography', 'class', 'bdt-tags-cloud skin-typography');
        $this->parent->add_render_attribute(
            [
                'skin_typography' => [
                    'data-settings' => [
                        wp_json_encode(array_filter([
                            'idCloud'     => 'bdt-cloud-' . $this->parent->get_id(),
                            'cloudColor' => $cloud_color,
                            'cloudStyle' => $settings['cloud_style'],

                        ])),
                    ],
                ],
            ]
        );


?>
        <div <?php echo $this->parent->get_render_attribute_string('skin_typography'); ?>>
            <div id="bdt-cloud-<?php echo $this->parent->get_id(); ?>" class="bdt-wordcloud">
                <?php echo $tag_cloud; ?>
            </div>
        </div>



<?php

    }
}
