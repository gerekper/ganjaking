<?php
class BetterDocs_Pro_Admin_Screen
{
    public function __construct() {
        add_filter('betterdocs_admin_screen_after_header_button', array($this, 'select_kb'));
        add_action('betterdocs_admin_filter_after_category', array($this, 'filter_by_kb'));
        add_action('betterdocs_admin_filter_before_submit', array($this, 'filter_by_view'));
    }

    public function select_kb()
    {
        $html = '';
        if (BetterDocs_Multiple_Kb::$enable == 1) {
            $html .= '<select class="dashboard-search-field select-kb-top" name="knowledgebase">
                            <option value="all">'.esc_html__('All Knowledge Base', 'betterdocs-pro').'</option>';
            $selected = (isset($_GET['knowledgebase'])) ? $_GET['knowledgebase'] : '';
            $terms_object = array(
                'taxonomy' => 'knowledge_base',
                'hide_empty' => false
            );
            $taxonomy_objects = get_terms($terms_object);
            if ($taxonomy_objects && !is_wp_error($taxonomy_objects)) :
                foreach ($taxonomy_objects as $term) :
                    $sel = ($term->slug === $selected) ? ' selected' : '';
                    $html .= '<option value="' . esc_attr($term->slug) . '"' . $sel . '>' . $term->name . '</option>';
                endforeach;
            endif;
            $html .= '</select>';
        }

        echo $html;
    }

    public function filter_by_kb() {
        if (BetterDocs_Multiple_Kb::$enable == 1) { ?>
            <select class="dashboard-search-field dashboard-select-kb" name="knowledgebase" id="dashboard-select-kb">
                <option value="all"><?php esc_html_e('All KBs', 'betterdocs-pro') ?></option>
                <?php
                $selected = (isset($_GET['knowledgebase'])) ? $_GET['knowledgebase'] : '';
                echo BetterDocs_Helper::term_options('knowledge_base', $selected);
                ?>
            </select>
        <?php }
    }

    public function filter_by_view() {
        $post_status = (isset($_GET['view'])) ? $_GET['view'] : ''; ?>
        <select class="dashboard-search-field dashboard-select-view" name="view" id="dashboard-select-view">
            <option value=""><?php esc_html_e('Views', 'betterdocs-pro') ?></option>
            <option value="most_viewed"<?php echo ('most_viewed' === $post_status) ? ' selected' : '' ?>><?php esc_html_e('Most Viewed', 'betterdocs-pro') ?></option>
            <option value="least_viewed"<?php echo ('least_viewed' === $post_status) ? ' selected' : '' ?>><?php esc_html_e('Least Viewed', 'betterdocs-pro') ?></option>
        </select>
        <?php
    }
}