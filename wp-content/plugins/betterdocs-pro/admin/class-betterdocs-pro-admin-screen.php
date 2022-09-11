<?php
class BetterDocs_Pro_Admin_Screen
{
    public function __construct() {
        add_filter('betterdocs_admin_screen_header_nav', array($this, 'header_template'));
        add_action('betterdocs_sorting_grid_view', array($this, 'betterdocs_sorting_grid_view'));
        add_action('betterdocs_admin_filter_after_category', array($this, 'filter_by_kb'));
        add_action('betterdocs_admin_filter_before_submit', array($this, 'filter_by_view'));
    }

    public function header_template()
    {
        $html = '<div class="betterdocs-settings-header">
            <div class="betterdocs-header-full">
                <img src="'.BETTERDOCS_ADMIN_URL.'assets/img/betterdocs-icon.svg" alt="">
                <h2 class="title">'.esc_html__('BetterDocs', 'betterdocs-pro').'</h2>
                <div class="betterdocs-header-button">
                    <a href="edit.php?post_type=docs&bdocs_view=classic" class="betterdocs-button betterdocs-button-secondary">'.esc_html__('Switch to Classic UI', 'betterdocs-pro').'</a>
                    <a href="post-new.php?post_type=docs" class="betterdocs-button betterdocs-button-primary">'.esc_html__('Add New Doc', 'betterdocs-pro').'</a>';
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
                $html .= '</div>
                <div class="betterdocs-list-grid-icon tabs-nav">
                    <a class="icon-wrap icon-wrap-1" href="#" data-toggle-target=".tab-content-1">
                        <svg width="20" height="18" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.449558 4.82253C0.200258 4.82253 0 4.62228 0 4.37298V0.449558C0 0.200258 0.200258 0 0.449558 0H21.0679C21.3418 0 21.5665 0.224779 21.5665 0.498601V4.32393C21.5665 4.59775 21.3418 4.82253 21.0679 4.82253H0.449558Z"/>
                            <path d="M0.449558 12.6694C0.200258 12.6694 0 12.4691 0 12.2198V8.29639C0 8.04709 0.200258 7.84683 0.449558 7.84683H21.0679C21.3418 7.84683 21.5665 8.07161 21.5665 8.34543V12.1708C21.5665 12.4446 21.3418 12.6694 21.0679 12.6694H0.449558Z"/>
                            <path d="M0.449558 20.5162C0.200258 20.5162 0 20.3159 0 20.0666V16.1432C0 15.8939 0.200258 15.6937 0.449558 15.6937H21.0679C21.3418 15.6937 21.5665 15.9184 21.5665 16.1923V20.0176C21.5665 20.2914 21.3418 20.5162 21.0679 20.5162H0.449558Z"/>
                        </svg>
                    </a>
                    <a class="icon-wrap icon-wrap-2" href="#" data-toggle-target=".tab-content-2">
                        <svg width="19" height="19" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.375 0H0.625C0.28125 0 0 0.28125 0 0.625V4.375C0 4.71875 0.28125 5 0.625 5H4.375C4.71875 5 5 4.71875 5 4.375V0.625C5 0.28125 4.71875 0 4.375 0Z"/>
                            <path d="M4.375 7.5H0.625C0.28125 7.5 0 7.78125 0 8.125V11.875C0 12.2188 0.28125 12.5 0.625 12.5H4.375C4.71875 12.5 5 12.2188 5 11.875V8.125C5 7.78125 4.71875 7.5 4.375 7.5Z"/>
                            <path d="M4.375 15H0.625C0.28125 15 0 15.2812 0 15.625V19.375C0 19.7188 0.28125 20 0.625 20H4.375C4.71875 20 5 19.7188 5 19.375V15.625C5 15.2812 4.71875 15 4.375 15Z"/>
                            <path d="M11.875 0H8.125C7.78125 0 7.5 0.28125 7.5 0.625V4.375C7.5 4.71875 7.78125 5 8.125 5H11.875C12.2188 5 12.5 4.71875 12.5 4.375V0.625C12.5 0.28125 12.2188 0 11.875 0Z"/>
                            <path d="M11.875 7.5H8.125C7.78125 7.5 7.5 7.78125 7.5 8.125V11.875C7.5 12.2188 7.78125 12.5 8.125 12.5H11.875C12.2188 12.5 12.5 12.2188 12.5 11.875V8.125C12.5 7.78125 12.2188 7.5 11.875 7.5Z"/>
                            <path d="M11.875 15H8.125C7.78125 15 7.5 15.2812 7.5 15.625V19.375C7.5 19.7188 7.78125 20 8.125 20H11.875C12.2188 20 12.5 19.7188 12.5 19.375V15.625C12.5 15.2812 12.2188 15 11.875 15Z"/>
                            <path d="M19.375 0H15.625C15.2812 0 15 0.28125 15 0.625V4.375C15 4.71875 15.2812 5 15.625 5H19.375C19.7188 5 20 4.71875 20 4.375V0.625C20 0.28125 19.7188 0 19.375 0Z"/>
                            <path d="M19.375 7.5H15.625C15.2812 7.5 15 7.78125 15 8.125V11.875C15 12.2188 15.2812 12.5 15.625 12.5H19.375C19.7188 12.5 20 12.2188 20 11.875V8.125C20 7.78125 19.7188 7.5 19.375 7.5Z"/>
                            <path d="M19.375 15H15.625C15.2812 15 15 15.2812 15 15.625V19.375C15 19.7188 15.2812 20 15.625 20H19.375C19.7188 20 20 19.7188 20 19.375V15.625C20 15.2812 19.7188 15 19.375 15Z"/>
                        </svg>
                    </a>
                </div>
                <div class="betterdocs-switch-mode">
                    <label for="betterdocs-mode-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="dayIcon" x="0px" y="0px" viewBox="0 0 35 35" style="enable-background:new 0 0 35 35;" xml:space="preserve">
							<g id="Sun">
                                <g>
                                    <path style="fill-rule:evenodd;clip-rule:evenodd;" d="M6,17.5C6,16.672,5.328,16,4.5,16h-3C0.672,16,0,16.672,0,17.5    S0.672,19,1.5,19h3C5.328,19,6,18.328,6,17.5z M7.5,26c-0.414,0-0.789,0.168-1.061,0.439l-2,2C4.168,28.711,4,29.086,4,29.5    C4,30.328,4.671,31,5.5,31c0.414,0,0.789-0.168,1.06-0.44l2-2C8.832,28.289,9,27.914,9,27.5C9,26.672,8.329,26,7.5,26z M17.5,6    C18.329,6,19,5.328,19,4.5v-3C19,0.672,18.329,0,17.5,0S16,0.672,16,1.5v3C16,5.328,16.671,6,17.5,6z M27.5,9    c0.414,0,0.789-0.168,1.06-0.439l2-2C30.832,6.289,31,5.914,31,5.5C31,4.672,30.329,4,29.5,4c-0.414,0-0.789,0.168-1.061,0.44    l-2,2C26.168,6.711,26,7.086,26,7.5C26,8.328,26.671,9,27.5,9z M6.439,8.561C6.711,8.832,7.086,9,7.5,9C8.328,9,9,8.328,9,7.5    c0-0.414-0.168-0.789-0.439-1.061l-2-2C6.289,4.168,5.914,4,5.5,4C4.672,4,4,4.672,4,5.5c0,0.414,0.168,0.789,0.439,1.06    L6.439,8.561z M33.5,16h-3c-0.828,0-1.5,0.672-1.5,1.5s0.672,1.5,1.5,1.5h3c0.828,0,1.5-0.672,1.5-1.5S34.328,16,33.5,16z     M28.561,26.439C28.289,26.168,27.914,26,27.5,26c-0.828,0-1.5,0.672-1.5,1.5c0,0.414,0.168,0.789,0.439,1.06l2,2    C28.711,30.832,29.086,31,29.5,31c0.828,0,1.5-0.672,1.5-1.5c0-0.414-0.168-0.789-0.439-1.061L28.561,26.439z M17.5,29    c-0.829,0-1.5,0.672-1.5,1.5v3c0,0.828,0.671,1.5,1.5,1.5s1.5-0.672,1.5-1.5v-3C19,29.672,18.329,29,17.5,29z M17.5,7    C11.71,7,7,11.71,7,17.5S11.71,28,17.5,28S28,23.29,28,17.5S23.29,7,17.5,7z M17.5,25c-4.136,0-7.5-3.364-7.5-7.5    c0-4.136,3.364-7.5,7.5-7.5c4.136,0,7.5,3.364,7.5,7.5C25,21.636,21.636,25,17.5,25z" />
                                </g>
                            </g>
						</svg>
                    </label>
                    <input class="betterdocs-mode-toggle" id="betterdocs-mode-toggle" type="checkbox">
                    <label class="betterdocs-mode-toggle-button" for="betterdocs-mode-toggle"></label>
                    <label for="betterdocs-mode-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="nightIcon" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
							<path d="M96.76,66.458c-0.853-0.852-2.15-1.064-3.23-0.534c-6.063,2.991-12.858,4.571-19.655,4.571  C62.022,70.495,50.88,65.88,42.5,57.5C29.043,44.043,25.658,23.536,34.076,6.47c0.532-1.08,0.318-2.379-0.534-3.23  c-0.851-0.852-2.15-1.064-3.23-0.534c-4.918,2.427-9.375,5.619-13.246,9.491c-9.447,9.447-14.65,22.008-14.65,35.369  c0,13.36,5.203,25.921,14.65,35.368s22.008,14.65,35.368,14.65c13.361,0,25.921-5.203,35.369-14.65  c3.872-3.871,7.064-8.328,9.491-13.246C97.826,68.608,97.611,67.309,96.76,66.458z" />
						</svg>
                    </label>
                </div>
            </div>
        </div>';

        return $html;
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

    public function betterdocs_sorting_grid_view()
    {
        global $wpdb;

        $terms_object = array(
            'taxonomy' => 'doc_category',
            'orderby' => 'meta_value_num',
            'meta_key' => 'doc_category_order',
            'order' => 'ASC',
            'hide_empty' => false,
        );

        if (BetterDocs_Multiple_Kb::$enable == 1 && isset($_GET['knowledgebase']) && $_GET['knowledgebase'] !== 'all') {
            $terms_object['meta_query'] = array(
                array(
                    'key'       => 'doc_category_knowledge_base',
                    'value'     => $_GET['knowledgebase'],
                    'compare'   => 'LIKE'
                )
            );
        }

        $terms = get_terms($terms_object);
        $kb = '';
        if (isset($_GET['knowledgebase']) && !empty($_GET['knowledgebase']) && $_GET['knowledgebase'] !== 'all') {
            $kb = $_GET['knowledgebase'];
        }
        ?>
        <div class="betterdocs-tab-content tab-content-2">
            <div class="betterdocs-listing-content">
                <?php
                $output = '';
                if (is_array($terms) && !empty($terms)) {
                    foreach ($terms as $term) {
                        $xTra_QL = $xTra_Term_QL = '';
                        $docs_order = rtrim(get_term_meta($term->term_id, '_docs_order', true), ',');
                        if (!empty($docs_order)) {
                            $xTra_QL = " ORDER BY FIELD(ID, $docs_order)";
                        }

                        /**
                         * for a single knowledge base article.
                         */
                        if (isset($_GET['knowledgebase']) && !empty($_GET['knowledgebase']) && $_GET['knowledgebase'] !== 'all') {
                            $kb_term = get_term_by('slug', trim($_GET['knowledgebase']), 'knowledge_base');
                            $kb_term_id = $kb_term->term_id;
                            $xTra_Term_QL = " AND object_id IN ( SELECT object_id AS post_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = $kb_term_id )";
                        }

                        $query = $wpdb->prepare(
                            "SELECT post_title AS title, ID, post_status  FROM $wpdb->posts
                                WHERE post_type = %s
                                AND ( ( post_status = %s ) OR ( post_status = %s ) OR ( post_status = %s ) OR ( post_status = %s ) )
                                AND ID IN ( SELECT object_id AS post_id FROM $wpdb->term_relationships
                                WHERE term_taxonomy_id = %s $xTra_Term_QL )$xTra_QL",
                            array(
                                'docs',
                                'publish',
                                'draft',
                                'pending',
                                'private',
                                $term->term_taxonomy_id
                            )
                        );

                        $results = $wpdb->get_results($query);

                        if (is_array($results)) {
                            $output .= '<div class="betterdocs-single-listing">';
                            $output .= '<div class="betterdocs-single-listing-inner">';
                            $output .= '<h4 class="betterdocs-single-listing-title">' . $term->name . '</h4>';
                            if (!empty($results)) {
                                $output .= '<ul class="docs-droppable" data-category_id="' . $term->term_id . '">';
                                foreach ($results as $doc) {
                                    $edit_post_link = get_edit_post_link($doc->ID, '');
                                    $delete_post_link = get_delete_post_link($doc->ID, '');
                                    $output .= '<li data-id="' . $doc->ID . '">';
                                    $output .= '<div class="betterdocs-single-list-content"><span><svg width="8px" viewBox="0 0 23 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="drag-dots" fill="#C5C5C5" fill-rule="nonzero"><path d="M4,0 C1.79947933,0 0,1.79947933 0,4 C0,6.20052067 1.79947933,8 4,8 C6.20052067,8 8,6.20052067 8,4 C8,1.79947933 6.20052067,0 4,0 Z M4,17 C1.79947933,17 0,18.7994793 0,21 C0,23.2005207 1.79947933,25 4,25 C6.20052067,25 8,23.2005207 8,21 C8,18.7994793 6.20052067,17 4,17 Z M4,34 C1.79947933,34 0,35.7994793 0,38 C0,40.2005207 1.79947933,42 4,42 C6.20052067,42 8,40.2005207 8,38 C8,35.7994793 6.20052067,34 4,34 Z M19,0 C16.7994793,0 15,1.79947933 15,4 C15,6.20052067 16.7994793,8 19,8 C21.2005207,8 23,6.20052067 23,4 C23,1.79947933 21.2005207,0 19,0 Z M19,17 C16.7994793,17 15,18.7994793 15,21 C15,23.2005207 16.7994793,25 19,25 C21.2005207,25 23,23.2005207 23,21 C23,18.7994793 21.2005207,17 19,17 Z M19,34 C16.7994793,34 15,35.7994793 15,38 C15,40.2005207 16.7994793,42 19,42 C21.2005207,42 23,40.2005207 23,38 C23,35.7994793 21.2005207,34 19,34 Z" id="Shape"></path></g></g></svg></span>';
                                    $output .= $edit_post_link ? '<a href="post.php?action=edit&post=' . $doc->ID . '">' : '<span class="betterdocs-article-title">';
                                    $output .= esc_html($doc->title);
                                    if ($doc->post_status === 'draft') {
                                        $output .= ' <span class="betterdocs-draft">( ' . __('Draft', 'betterdocs-pro') . ' )</span>';
                                    }
                                    $output .= $edit_post_link ? '</a>' : '</span>';
                                    $output .= '<span>';
                                    $output .= '<a href="' . get_permalink($doc->ID, '') . '" target="_blank" title="View Docs"><span class="dashicons dashicons-external"></span></a>';
                                    if ($edit_post_link) {
                                        $output .= '<a href="post.php?action=edit&post=' . $doc->ID . '" title="Edit Docs"><span class="dashicons dashicons-edit"></span></a>';
                                    }
                                    if ($delete_post_link) {
                                        $output .= '<a href="' . get_delete_post_link($doc->ID, '') . '" title="Delete Docs"><span class="dashicons dashicons-trash"></span></a>';
                                    }
                                    $output .= '</span></div>';
                                    $output .= '</li>';
                                }
                                $output .= '</ul>';
                            } else {
                                $output .= '<ul class="docs-droppable" data-category_id="' . $term->term_id . '">';
                                $output .= '<li class="betterdocs-no-docs"><svg id="f20e0c25-d928-42cc-98d1-13cc230663ea" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%" viewBox="0 0 820.16 780.81"><defs><linearGradient id="07332201-7176-49c2-9908-6dc4a39c4716" x1="539.63" y1="734.6" x2="539.63" y2="151.19" gradientTransform="translate(-3.62 1.57)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="gray" stop-opacity="0.25"/><stop offset="0.54" stop-color="gray" stop-opacity="0.12"/><stop offset="1" stop-color="gray" stop-opacity="0.1"/></linearGradient><linearGradient id="0ee1ab3f-7ba2-4205-9d4a-9606ad702253" x1="540.17" y1="180.2" x2="540.17" y2="130.75" gradientTransform="translate(-63.92 7.85)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"/><linearGradient id="abca9755-bed1-4a97-b027-7f02ee3ffa09" x1="540.17" y1="140.86" x2="540.17" y2="82.43" gradientTransform="translate(-84.51 124.6) rotate(-12.11)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"/><linearGradient id="2632d424-e666-4ee4-9508-a494957e14ab" x1="476.4" y1="710.53" x2="476.4" y2="127.12" gradientTransform="matrix(1, 0, 0, 1, 0, 0)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"/><linearGradient id="97571ef7-1c83-4e06-b701-c2e47e77dca3" x1="476.94" y1="156.13" x2="476.94" y2="106.68" gradientTransform="matrix(1, 0, 0, 1, 0, 0)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"/><linearGradient id="7d32e13e-a0c7-49c4-af0e-066a2f8cb76e" x1="666.86" y1="176.39" x2="666.86" y2="117.95" gradientTransform="matrix(1, 0, 0, 1, 0, 0)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"/></defs><title>No Docs</title><rect x="317.5" y="142.55" width="437.02" height="603.82" transform="translate(-271.22 62.72) rotate(-12.11)" fill="#e0e0e0"/><g opacity="0.5"><rect x="324.89" y="152.76" width="422.25" height="583.41" transform="translate(-271.22 62.72) rotate(-12.11)" fill="url(#07332201-7176-49c2-9908-6dc4a39c4716)"/></g><rect x="329.81" y="157.1" width="411.5" height="570.52" transform="translate(-270.79 62.58) rotate(-12.11)" fill="#fafafa"/><rect x="374.18" y="138.6" width="204.14" height="49.45" transform="translate(-213.58 43.93) rotate(-12.11)" fill="url(#0ee1ab3f-7ba2-4205-9d4a-9606ad702253)"/><path d="M460.93,91.9c-15.41,3.31-25.16,18.78-21.77,34.55s18.62,25.89,34,22.58,25.16-18.78,21.77-34.55S476.34,88.59,460.93,91.9ZM470.6,137A16.86,16.86,0,1,1,483.16,117,16.66,16.66,0,0,1,470.6,137Z" transform="translate(-189.92 -59.59)" fill="url(#abca9755-bed1-4a97-b027-7f02ee3ffa09)"/><rect x="375.66" y="136.55" width="199.84" height="47.27" transform="translate(-212.94 43.72) rotate(-12.11)" fill="#1fce9c"/><path d="M460.93,91.9a27.93,27.93,0,1,0,33.17,21.45A27.93,27.93,0,0,0,460.93,91.9ZM470.17,135a16.12,16.12,0,1,1,12.38-19.14A16.12,16.12,0,0,1,470.17,135Z" transform="translate(-189.92 -59.59)" fill="#1fce9c"/><rect x="257.89" y="116.91" width="437.02" height="603.82" fill="#e0e0e0"/><g opacity="0.5"><rect x="265.28" y="127.12" width="422.25" height="583.41" fill="url(#2632d424-e666-4ee4-9508-a494957e14ab)"/></g><rect x="270.65" y="131.42" width="411.5" height="570.52" fill="#fff"/><rect x="374.87" y="106.68" width="204.14" height="49.45" fill="url(#97571ef7-1c83-4e06-b701-c2e47e77dca3)"/><path d="M666.86,118c-15.76,0-28.54,13.08-28.54,29.22s12.78,29.22,28.54,29.22,28.54-13.08,28.54-29.22S682.62,118,666.86,118Zm0,46.08a16.86,16.86,0,1,1,16.46-16.86A16.66,16.66,0,0,1,666.86,164Z" transform="translate(-189.92 -59.59)" fill="url(#7d32e13e-a0c7-49c4-af0e-066a2f8cb76e)"/><rect x="377.02" y="104.56" width="199.84" height="47.27" fill="#1fce9c"/><path d="M666.86,118a27.93,27.93,0,1,0,27.93,27.93A27.93,27.93,0,0,0,666.86,118Zm0,44.05A16.12,16.12,0,1,1,683,145.89,16.12,16.12,0,0,1,666.86,162Z" transform="translate(-189.92 -59.59)" fill="#1fce9c"/><g opacity="0.5"><rect x="15.27" y="737.05" width="3.76" height="21.33" fill="#47e6b1"/><rect x="205.19" y="796.65" width="3.76" height="21.33" transform="translate(824.47 540.65) rotate(90)" fill="#47e6b1"/></g><g opacity="0.5"><rect x="451.49" width="3.76" height="21.33" fill="#47e6b1"/><rect x="641.4" y="59.59" width="3.76" height="21.33" transform="translate(523.63 -632.62) rotate(90)" fill="#47e6b1"/></g><path d="M961,832.15a4.61,4.61,0,0,1-2.57-5.57,2.22,2.22,0,0,0,.1-.51h0a2.31,2.31,0,0,0-4.15-1.53h0a2.22,2.22,0,0,0-.26.45,4.61,4.61,0,0,1-5.57,2.57,2.22,2.22,0,0,0-.51-.1h0a2.31,2.31,0,0,0-1.53,4.15h0a2.22,2.22,0,0,0,.45.26,4.61,4.61,0,0,1,2.57,5.57,2.22,2.22,0,0,0-.1.51h0a2.31,2.31,0,0,0,4.15,1.53h0a2.22,2.22,0,0,0,.26-.45,4.61,4.61,0,0,1,5.57-2.57,2.22,2.22,0,0,0,.51.1h0a2.31,2.31,0,0,0,1.53-4.15h0A2.22,2.22,0,0,0,961,832.15Z" transform="translate(-189.92 -59.59)" fill="#4d8af0" opacity="0.5"/><path d="M326.59,627.09a4.61,4.61,0,0,1-2.57-5.57,2.22,2.22,0,0,0,.1-.51h0a2.31,2.31,0,0,0-4.15-1.53h0a2.22,2.22,0,0,0-.26.45,4.61,4.61,0,0,1-5.57,2.57,2.22,2.22,0,0,0-.51-.1h0a2.31,2.31,0,0,0-1.53,4.15h0a2.22,2.22,0,0,0,.45.26,4.61,4.61,0,0,1,2.57,5.57,2.22,2.22,0,0,0-.1.51h0a2.31,2.31,0,0,0,4.15,1.53h0a2.22,2.22,0,0,0,.26-.45A4.61,4.61,0,0,1,325,631.4a2.22,2.22,0,0,0,.51.1h0a2.31,2.31,0,0,0,1.53-4.15h0A2.22,2.22,0,0,0,326.59,627.09Z" transform="translate(-189.92 -59.59)" fill="#fdd835" opacity="0.5"/><path d="M855,127.77a4.61,4.61,0,0,1-2.57-5.57,2.22,2.22,0,0,0,.1-.51h0a2.31,2.31,0,0,0-4.15-1.53h0a2.22,2.22,0,0,0-.26.45,4.61,4.61,0,0,1-5.57,2.57,2.22,2.22,0,0,0-.51-.1h0a2.31,2.31,0,0,0-1.53,4.15h0a2.22,2.22,0,0,0,.45.26,4.61,4.61,0,0,1,2.57,5.57,2.22,2.22,0,0,0-.1.51h0a2.31,2.31,0,0,0,4.15,1.53h0a2.22,2.22,0,0,0,.26-.45,4.61,4.61,0,0,1,5.57-2.57,2.22,2.22,0,0,0,.51.1h0a2.31,2.31,0,0,0,1.53-4.15h0A2.22,2.22,0,0,0,855,127.77Z" transform="translate(-189.92 -59.59)" fill="#fdd835" opacity="0.5"/><circle cx="812.64" cy="314.47" r="7.53" fill="#f55f44" opacity="0.5"/><circle cx="230.73" cy="746.65" r="7.53" fill="#f55f44" opacity="0.5"/><circle cx="735.31" cy="477.23" r="7.53" fill="#f55f44" opacity="0.5"/><circle cx="87.14" cy="96.35" r="7.53" fill="#4d8af0" opacity="0.5"/><circle cx="7.53" cy="301.76" r="7.53" fill="#47e6b1" opacity="0.5"/></svg></li>';
                                $output .= '</ul>';
                            }
                            $output .= '</div>';
                            $output .= '<a href="post-new.php?post_type=docs&cat=' . $term->term_id . '" class="betterdocs-add-new-link"><span class="add-new-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="20px" fill="#b0b2ba"><path d="M 14.970703 2.9726562 A 2.0002 2.0002 0 0 0 13 5 L 13 13 L 5 13 A 2.0002 2.0002 0 1 0 5 17 L 13 17 L 13 25 A 2.0002 2.0002 0 1 0 17 25 L 17 17 L 25 17 A 2.0002 2.0002 0 1 0 25 13 L 17 13 L 17 5 A 2.0002 2.0002 0 0 0 14.970703 2.9726562 z"></path></svg></span><span class="add-new-text">Add New Docs</span></a>';
                            $output .= '</div>';
                        }
                    }
                }
                $query = $wpdb->prepare(
                    "SELECT post_title AS title, ID, post_status FROM $wpdb->posts 
                    WHERE post_type = %s 
                    AND ( ( post_status = %s ) OR ( post_status = %s ) OR ( post_status = %s ) OR ( post_status = %s ) ) 
                    AND ID NOT IN  ( SELECT object_id as post_id FROM $wpdb->term_relationships 
                    WHERE  term_taxonomy_id IN ( SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = %s ) )",
                    array(
                        'docs',
                        'publish',
                        'draft',
                        'pending',
                        'private',
                        'doc_category'
                    )
                );
                $uncategorized_docs = $wpdb->get_results($query);
                if (!empty($uncategorized_docs) && is_array($uncategorized_docs)) {
                    $output .= '<div class="betterdocs-single-listing">';
                    $output .= '<div class="betterdocs-single-listing-inner">';
                    $output .= '<h4 class="betterdocs-single-listing-title">' . __('Uncategorized', 'betterdocs-pro') . '</h4>';
                    $output .= '<ul>';
                    foreach ($uncategorized_docs as $doc) {
                        $edit_post_link = get_edit_post_link($doc->ID, '');
                        $delete_post_link = get_delete_post_link($doc->ID, '');
                        $output .= '<li data-id="' . $doc->ID . '">';
                        $output .= '<div class="betterdocs-single-list-content"><span><svg width="8px" viewBox="0 0 23 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="drag-dots" fill="#C5C5C5" fill-rule="nonzero"><path d="M4,0 C1.79947933,0 0,1.79947933 0,4 C0,6.20052067 1.79947933,8 4,8 C6.20052067,8 8,6.20052067 8,4 C8,1.79947933 6.20052067,0 4,0 Z M4,17 C1.79947933,17 0,18.7994793 0,21 C0,23.2005207 1.79947933,25 4,25 C6.20052067,25 8,23.2005207 8,21 C8,18.7994793 6.20052067,17 4,17 Z M4,34 C1.79947933,34 0,35.7994793 0,38 C0,40.2005207 1.79947933,42 4,42 C6.20052067,42 8,40.2005207 8,38 C8,35.7994793 6.20052067,34 4,34 Z M19,0 C16.7994793,0 15,1.79947933 15,4 C15,6.20052067 16.7994793,8 19,8 C21.2005207,8 23,6.20052067 23,4 C23,1.79947933 21.2005207,0 19,0 Z M19,17 C16.7994793,17 15,18.7994793 15,21 C15,23.2005207 16.7994793,25 19,25 C21.2005207,25 23,23.2005207 23,21 C23,18.7994793 21.2005207,17 19,17 Z M19,34 C16.7994793,34 15,35.7994793 15,38 C15,40.2005207 16.7994793,42 19,42 C21.2005207,42 23,40.2005207 23,38 C23,35.7994793 21.2005207,34 19,34 Z" id="Shape"></path></g></g></svg></span>';
                        $output .= '<a href="post.php?action=edit&post=' . $doc->ID . '">';
                        $output .= esc_html($doc->title);
                        if ($doc->post_status === 'draft') {
                            $output .= ' <span class="betterdocs-draft">( ' . __('Draft', 'betterdocs-pro') . ' )</span>';
                        }
                        $output .= '</a>';
                        $output .= '<span>';
                        if ($edit_post_link) {
                            $output .= '<a href="' . $edit_post_link . '"><span class="dashicons dashicons-edit"></span></a>';
                        }
                        $output .= '<a href="post-new.php?post_type=docs"><span class="dashicons dashicons-plus"></span></a>';
                        if ($delete_post_link) {
                            $output .= '<a href="' . $delete_post_link . '"><span class="dashicons dashicons-trash"></span></a>';
                        }
                        $output .= '</span></div>';
                        $output .= '</li>';
                    }
                    $output .= '</ul>';
                    $output .= '</div>';
                    $output .= '<a href="post-new.php?post_type=docs" class="betterdocs-add-new-link"><span class="add-new-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="20px" fill="#b0b2ba"><path d="M 14.970703 2.9726562 A 2.0002 2.0002 0 0 0 13 5 L 13 13 L 5 13 A 2.0002 2.0002 0 1 0 5 17 L 13 17 L 13 25 A 2.0002 2.0002 0 1 0 17 25 L 17 17 L 25 17 A 2.0002 2.0002 0 1 0 25 13 L 17 13 L 17 5 A 2.0002 2.0002 0 0 0 14.970703 2.9726562 z"></path></svg></span><span class="add-new-text">Add New Docs</span></a>';
                    $output .= '</div>';
                }
                if (empty($terms) && empty($uncategorized_docs)) {
                    $output .= '<div class="betterdocs-single-listing">';
                    $output .= '<div class="betterdocs-single-listing-inner">';
                    $output .= '<h4 class="betterdocs-single-listing-title">No Categories Found</h4>';
                    $output .= '<p class="betterdocs-single-listing-sub-title"> Please create a new Category to get started</p>';
                    $output .= '<ul>';
                    $output .= '<li><svg id="f20e0c25-d928-42cc-98d1-13cc230663ea" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%" viewBox="0 0 820.16 780.81"><defs><linearGradient id="07332201-7176-49c2-9908-6dc4a39c4716" x1="539.63" y1="734.6" x2="539.63" y2="151.19" gradientTransform="translate(-3.62 1.57)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="gray" stop-opacity="0.25"></stop><stop offset="0.54" stop-color="gray" stop-opacity="0.12"></stop><stop offset="1" stop-color="gray" stop-opacity="0.1"></stop></linearGradient><linearGradient id="0ee1ab3f-7ba2-4205-9d4a-9606ad702253" x1="540.17" y1="180.2" x2="540.17" y2="130.75" gradientTransform="translate(-63.92 7.85)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"></linearGradient><linearGradient id="abca9755-bed1-4a97-b027-7f02ee3ffa09" x1="540.17" y1="140.86" x2="540.17" y2="82.43" gradientTransform="translate(-84.51 124.6) rotate(-12.11)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"></linearGradient><linearGradient id="2632d424-e666-4ee4-9508-a494957e14ab" x1="476.4" y1="710.53" x2="476.4" y2="127.12" gradientTransform="matrix(1, 0, 0, 1, 0, 0)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"></linearGradient><linearGradient id="97571ef7-1c83-4e06-b701-c2e47e77dca3" x1="476.94" y1="156.13" x2="476.94" y2="106.68" gradientTransform="matrix(1, 0, 0, 1, 0, 0)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"></linearGradient><linearGradient id="7d32e13e-a0c7-49c4-af0e-066a2f8cb76e" x1="666.86" y1="176.39" x2="666.86" y2="117.95" gradientTransform="matrix(1, 0, 0, 1, 0, 0)" xlink:href="#07332201-7176-49c2-9908-6dc4a39c4716"></linearGradient></defs><title>No Docs</title><rect x="317.5" y="142.55" width="437.02" height="603.82" transform="translate(-271.22 62.72) rotate(-12.11)" fill="#e0e0e0"></rect><g opacity="0.5"><rect x="324.89" y="152.76" width="422.25" height="583.41" transform="translate(-271.22 62.72) rotate(-12.11)" fill="url(#07332201-7176-49c2-9908-6dc4a39c4716)"></rect></g><rect x="329.81" y="157.1" width="411.5" height="570.52" transform="translate(-270.79 62.58) rotate(-12.11)" fill="#fafafa"></rect><rect x="374.18" y="138.6" width="204.14" height="49.45" transform="translate(-213.58 43.93) rotate(-12.11)" fill="url(#0ee1ab3f-7ba2-4205-9d4a-9606ad702253)"></rect><path d="M460.93,91.9c-15.41,3.31-25.16,18.78-21.77,34.55s18.62,25.89,34,22.58,25.16-18.78,21.77-34.55S476.34,88.59,460.93,91.9ZM470.6,137A16.86,16.86,0,1,1,483.16,117,16.66,16.66,0,0,1,470.6,137Z" transform="translate(-189.92 -59.59)" fill="url(#abca9755-bed1-4a97-b027-7f02ee3ffa09)"></path><rect x="375.66" y="136.55" width="199.84" height="47.27" transform="translate(-212.94 43.72) rotate(-12.11)" fill="#1fce9c"></rect><path d="M460.93,91.9a27.93,27.93,0,1,0,33.17,21.45A27.93,27.93,0,0,0,460.93,91.9ZM470.17,135a16.12,16.12,0,1,1,12.38-19.14A16.12,16.12,0,0,1,470.17,135Z" transform="translate(-189.92 -59.59)" fill="#1fce9c"></path><rect x="257.89" y="116.91" width="437.02" height="603.82" fill="#e0e0e0"></rect><g opacity="0.5"><rect x="265.28" y="127.12" width="422.25" height="583.41" fill="url(#2632d424-e666-4ee4-9508-a494957e14ab)"></rect></g><rect x="270.65" y="131.42" width="411.5" height="570.52" fill="#fff"></rect><rect x="374.87" y="106.68" width="204.14" height="49.45" fill="url(#97571ef7-1c83-4e06-b701-c2e47e77dca3)"></rect><path d="M666.86,118c-15.76,0-28.54,13.08-28.54,29.22s12.78,29.22,28.54,29.22,28.54-13.08,28.54-29.22S682.62,118,666.86,118Zm0,46.08a16.86,16.86,0,1,1,16.46-16.86A16.66,16.66,0,0,1,666.86,164Z" transform="translate(-189.92 -59.59)" fill="url(#7d32e13e-a0c7-49c4-af0e-066a2f8cb76e)"></path><rect x="377.02" y="104.56" width="199.84" height="47.27" fill="#1fce9c"></rect><path d="M666.86,118a27.93,27.93,0,1,0,27.93,27.93A27.93,27.93,0,0,0,666.86,118Zm0,44.05A16.12,16.12,0,1,1,683,145.89,16.12,16.12,0,0,1,666.86,162Z" transform="translate(-189.92 -59.59)" fill="#1fce9c"></path><g opacity="0.5"><rect x="15.27" y="737.05" width="3.76" height="21.33" fill="#47e6b1"></rect><rect x="205.19" y="796.65" width="3.76" height="21.33" transform="translate(824.47 540.65) rotate(90)" fill="#47e6b1"></rect></g><g opacity="0.5"><rect x="451.49" width="3.76" height="21.33" fill="#47e6b1"></rect><rect x="641.4" y="59.59" width="3.76" height="21.33" transform="translate(523.63 -632.62) rotate(90)" fill="#47e6b1"></rect></g><path d="M961,832.15a4.61,4.61,0,0,1-2.57-5.57,2.22,2.22,0,0,0,.1-.51h0a2.31,2.31,0,0,0-4.15-1.53h0a2.22,2.22,0,0,0-.26.45,4.61,4.61,0,0,1-5.57,2.57,2.22,2.22,0,0,0-.51-.1h0a2.31,2.31,0,0,0-1.53,4.15h0a2.22,2.22,0,0,0,.45.26,4.61,4.61,0,0,1,2.57,5.57,2.22,2.22,0,0,0-.1.51h0a2.31,2.31,0,0,0,4.15,1.53h0a2.22,2.22,0,0,0,.26-.45,4.61,4.61,0,0,1,5.57-2.57,2.22,2.22,0,0,0,.51.1h0a2.31,2.31,0,0,0,1.53-4.15h0A2.22,2.22,0,0,0,961,832.15Z" transform="translate(-189.92 -59.59)" fill="#4d8af0" opacity="0.5"></path><path d="M326.59,627.09a4.61,4.61,0,0,1-2.57-5.57,2.22,2.22,0,0,0,.1-.51h0a2.31,2.31,0,0,0-4.15-1.53h0a2.22,2.22,0,0,0-.26.45,4.61,4.61,0,0,1-5.57,2.57,2.22,2.22,0,0,0-.51-.1h0a2.31,2.31,0,0,0-1.53,4.15h0a2.22,2.22,0,0,0,.45.26,4.61,4.61,0,0,1,2.57,5.57,2.22,2.22,0,0,0-.1.51h0a2.31,2.31,0,0,0,4.15,1.53h0a2.22,2.22,0,0,0,.26-.45A4.61,4.61,0,0,1,325,631.4a2.22,2.22,0,0,0,.51.1h0a2.31,2.31,0,0,0,1.53-4.15h0A2.22,2.22,0,0,0,326.59,627.09Z" transform="translate(-189.92 -59.59)" fill="#fdd835" opacity="0.5"></path><path d="M855,127.77a4.61,4.61,0,0,1-2.57-5.57,2.22,2.22,0,0,0,.1-.51h0a2.31,2.31,0,0,0-4.15-1.53h0a2.22,2.22,0,0,0-.26.45,4.61,4.61,0,0,1-5.57,2.57,2.22,2.22,0,0,0-.51-.1h0a2.31,2.31,0,0,0-1.53,4.15h0a2.22,2.22,0,0,0,.45.26,4.61,4.61,0,0,1,2.57,5.57,2.22,2.22,0,0,0-.1.51h0a2.31,2.31,0,0,0,4.15,1.53h0a2.22,2.22,0,0,0,.26-.45,4.61,4.61,0,0,1,5.57-2.57,2.22,2.22,0,0,0,.51.1h0a2.31,2.31,0,0,0,1.53-4.15h0A2.22,2.22,0,0,0,855,127.77Z" transform="translate(-189.92 -59.59)" fill="#fdd835" opacity="0.5"></path><circle cx="812.64" cy="314.47" r="7.53" fill="#f55f44" opacity="0.5"></circle><circle cx="230.73" cy="746.65" r="7.53" fill="#f55f44" opacity="0.5"></circle><circle cx="735.31" cy="477.23" r="7.53" fill="#f55f44" opacity="0.5"></circle><circle cx="87.14" cy="96.35" r="7.53" fill="#4d8af0" opacity="0.5"></circle><circle cx="7.53" cy="301.76" r="7.53" fill="#47e6b1" opacity="0.5"></circle></svg></li>';
                    $output .= '</ui>';

                    $output .= '</div>';
                    $output .= '<a href="edit-tags.php?taxonomy=doc_category&post_type=docs" class="betterdocs-add-new-link"><span class="add-new-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="20px" fill="#b0b2ba"><path d="M 14.970703 2.9726562 A 2.0002 2.0002 0 0 0 13 5 L 13 13 L 5 13 A 2.0002 2.0002 0 1 0 5 17 L 13 17 L 13 25 A 2.0002 2.0002 0 1 0 17 25 L 17 17 L 25 17 A 2.0002 2.0002 0 1 0 25 13 L 17 13 L 17 5 A 2.0002 2.0002 0 0 0 14.970703 2.9726562 z"></path></svg></span><span class="add-new-text">Add New Docs</span></a>';
                    $output .= '</div>';
                }
                echo $output;
                ?>

            </div>
        </div>
    <?php
    }
}