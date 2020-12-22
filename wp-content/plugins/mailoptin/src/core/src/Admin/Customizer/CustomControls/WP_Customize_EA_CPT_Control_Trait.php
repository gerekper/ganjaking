<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

trait WP_Customize_EA_CPT_Control_Trait
{
    public function get_terms($taxonomy, $search = '', $limt = 200)
    {
        return get_terms([
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
                'fields'     => 'id=>name',
                'number'     => $limt,
                'search'     => $search
            ]
        );
    }

    public function render_fields($custom_post_type, $saved_value = [])
    {
        if ( ! empty($custom_post_type)) {
            $taxonomies = get_object_taxonomies($custom_post_type, 'objects');
            if (class_exists('WooCommerce')) {
                unset($taxonomies['product_type']);
                unset($taxonomies['product_visibility']);
                unset($taxonomies['product_shipping_class']);
                unset($taxonomies['pa_color']);
                unset($taxonomies['pa_size']);
            }

            foreach ($taxonomies as $key => $value) {
                $this->select_markup(
                    $saved_value,
                    $key,
                    $this->get_terms($key),
                    sprintf(__('Restrict to %s', 'mailoptin'), $value->label)
                );
            }
        }
    }

    public function select_markup($saved_value, $name_attr, $choices, $label)
    {
        // ensures saved terms are always available as choices so that they are preselected.
        if (isset($saved_value[$name_attr])) {
            $choices += array_reduce($saved_value[$name_attr], function ($carry, $term_id) {
                $carry[$term_id] = get_term($term_id)->name;

                return $carry;
            }, []);
        }

        ?>
        <div class="mo-ea-cpt-setting" style="margin-bottom: 10px">
            <label>
                <?php if ( ! empty($label)) : ?>
                    <span class="customize-control-title"><?php echo esc_html($label); ?></span>
                <?php endif; ?>
                <select name="<?= $name_attr ?>" class="mailoptin-chosen" data-search-type="ch_get_terms|<?= $name_attr ?>" multiple>
                    <?php
                    if (is_array($choices)) {
                        foreach ($choices as $key => $value) {
                            if (is_array($value)) {
                                echo "<optgroup label='$key'>";
                                foreach ($value as $key2 => $value2) {
                                    echo '<option value="' . esc_attr($key2) . '"' . $this->_selected($name_attr, $key2, $saved_value) . '>' . $value2 . '</option>';
                                }
                                echo "</optgroup>";
                            } else {
                                echo '<option value="' . esc_attr($key) . '"' . $this->_selected($name_attr, $key, $saved_value) . '>' . $value . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
            </label>
        </div>
        <?php
    }

    protected function _selected($name_attr, $key, $saved_value)
    {
        return is_array($saved_value) && array_key_exists($name_attr, $saved_value) && in_array($key, $saved_value[$name_attr]) ? 'selected=selected' : null;
    }
}