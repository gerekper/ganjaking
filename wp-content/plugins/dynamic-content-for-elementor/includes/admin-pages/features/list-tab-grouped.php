<?php

namespace DynamicContentForElementor\AdminPages\Features;

abstract class GroupedListTab extends \DynamicContentForElementor\AdminPages\Features\ListTab
{
    public abstract function get_groups();
    public abstract function get_groups_key();
    public function render_list()
    {
        $features = $this->features;
        $groups_items = \count($this->get_groups());
        $i = 0;
        foreach ($this->get_groups() as $group_name => $group_label) {
            $id_label = 'dce-' . \strtolower($group_label);
            $activate_label = __('Activate all', 'dynamic-content-for-elementor') . ' ' . $this->get_label() . ' in this category';
            $deactivate_label = __('Deactivate all', 'dynamic-content-for-elementor') . ' ' . $this->get_label() . ' in this category';
            echo "<div class='dce-feature-group'>";
            echo '<h3>' . $group_label . '</h3>';
            echo <<<END
\t\t\t\t<p class="dce-group-all">
\t\t\t\t\t<a href="#" class="dce-group-activate-all"><span class='dot green'></span>{$activate_label}</a>
\t\t\t\t\t&nbsp;/&nbsp;
\t\t\t\t\t<a href="#" class="dce-group-deactivate-all"><span class='dot red'></span>{$deactivate_label}</a>
\t\t\t\t</p>
END;
            echo '<div class="dce-modules">';
            foreach (wp_list_filter($features, [$this->get_groups_key() => $group_name]) as $fname => $finfo) {
                $this->show_feature($fname, $finfo);
            }
            echo '</div>';
            if (++$i !== $groups_items) {
                submit_button(__('Save', 'dynamic-content-for-elementor') . ' ' . $this->get_label());
            }
            echo '</div>';
        }
    }
}
