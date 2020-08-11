<?php
if( ! class_exists( 'BetterDocsSetupWizard' ) ){
    class BetterDocsSetupWizard {
        public static $sections_array = array();
        public static $optionGroupName = 'betterdocs_settings';
        public static function load(){
            // Menu.
            // add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
            
            // tab builder
            add_action( 'admin_enqueue_scripts', array(__CLASS__, 'setup_wizard_scripts'), 30 );
            add_action('betterdocs_nav_tabs', array(__CLASS__, 'add_nav_tabs'));
            add_action('betterdocs_tabs_content', array(__CLASS__, 'add_tab_content'));
            // ajax request
            add_action( 'wp_ajax_better_docs_quick_setup_wizard_data_save', array(__CLASS__, 'better_docs_quick_setup_wizard_data_save') );
            // ajax url change
            add_action( 'wp_ajax_better_docs_setup_generate_live_url', array(__CLASS__, 'better_docs_setup_generate_live_url') );
        }
        
        public static function setup_wizard_scripts(){
            wp_enqueue_style( 'betterdocs-setup-wizard', BETTERDOCS_ADMIN_URL . 'setup-wizard/assets/css/betterdocs-setup-wizard.css' );
            $BetterDocsQswVersionNumber  = date("ymd-Gis", filemtime( BETTERDOCS_ADMIN_DIR_PATH . 'setup-wizard/assets/js/betterdocs-setup-wizard.js' ));
            wp_enqueue_script( 'betterdocs-setup-wizard', BETTERDOCS_ADMIN_URL . 'setup-wizard/assets/js/betterdocs-setup-wizard.js', array('jquery'), $BetterDocsQswVersionNumber, false );
            
            // Localize the script with new data
           
            wp_localize_script( 'betterdocs-setup-wizard', 'bdquicksetup', array(
                'customizerurl' => betterdocs_setup_get_customizer_setting_url(),
                'docspageurl'   => betterdocs_setup_docs_page_url(),
                'currentslug'   => BetterDocs_DB::get_settings('docs_slug'),
            ));
        }

        // add admin page
        public static function admin_menu() {
            add_submenu_page(
                null,
                'Better Docs Setup',
                'Better Docs Setup',
                'manage_options',
                'betterdocs-setup',
                array(  __CLASS__, 'plugin_setting_page' )
            );
        }

        // add admin page
        public static function knowledge_base_plugins() {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            $plugins = array();
            if (is_plugin_active('wedocs/wedocs.php')) {
                $plugins[] = array('wedocs', 'weDocs');
            }
            if (is_plugin_active('bsf-docs/bsf-docs.php')) {
                $plugins[] = array('bsf-docs', 'BSF docs');
            }
            if (is_plugin_active('documentor-lite/documentor-lite.php')) {
                $plugins[] = array('documentor-lite', 'Documentor');
            }
            if (is_plugin_active('echo-knowledge-base/echo-knowledge-base.php')) {
                $plugins[] = array('echo-knowledge-base', 'Echo Knowledge Base');
            }
            if (is_plugin_active('pressapps-knowledge-base/pressapps-knowledge-base.php')) {
                $plugins[] = array('pressapps-knowledge-base', 'PressApps Knowledge Base');
            }
            return $plugins;
        }

        // add admin page
        public static function existing_plugins_data($plugins) {
            $plugins_data = array();
            if ($plugins === 'wedocs') {
                $plugins_data['name'] = 'wedocs';
                $plugins_data['url'] = 'wedocs/wedocs.php';
            }
            if ($plugins === 'bsf-docs') {
                $plugins_data['name'] = 'bsf-docs';
                $plugins_data['url'] = 'bsf-docs/bsf-docs.php';
            }
            if ($plugins === 'documentor-lite') {
                $plugins_data['name'] = 'documentor-lite';
                $plugins_data['url'] = 'documentor-lite/documentor-lite.php';
            }
            if ($plugins === 'echo-knowledge-base') {
                $plugins_data['name'] = 'echo-knowledge-base';
                $plugins_data['url'] = 'echo-knowledge-base/echo-knowledge-base.php';
            }
            if ($plugins === 'pressapps-knowledge-base') {
                $plugins_data['name'] = 'pressapps-knowledge-base';
                $plugins_data['url'] = 'pressapps-knowledge-base/pressapps-knowledge-base.php';
            }
            return $plugins_data;
        }

        public static function insert_terms_hierarchically($existing_term, $new_term, $parentId = 0) {
            $into = array();
            $cats = get_terms($existing_term, array('hide_empty' => false));
            if ($cats) {
                foreach ($cats as $i => $cat) {
                    if ($cat->parent == $parentId) {
                        $into[$cat->term_id] = $cat;
                        unset($cats[$i]);
                        $doc_parent_term = term_exists( $cat->name, $new_term );
                        wp_insert_term (
                            $cat->name,
                            $new_term,
                            array(
                                'alias_of' => $cat->slug,
                                'description' => $cat->description,
                                'slug'        => $cat->slug,
                                'parent'      => $cat->parent,
                            )
                        );
                    }
                }
                if ($cats) {
                    foreach ($cats as $i => $cat) {
                        $get_existing_term = get_term_by('id', $cat->parent, $existing_term);
                        $doc_parent_term = term_exists( $get_existing_term->name, $new_term );
                        wp_insert_term (
                            $cat->name,
                            $new_term,
                            array(
                                'alias_of' => $cat->slug,
                                'description' => $cat->description,
                                'slug'        => $cat->slug,
                                'parent'      => $doc_parent_term['term_id'],
                            )
                        );
                    }
                }
            }
        }

        public static function insert_posts($existing_post, $existing_cat, $existing_tag) {
            $args = array(
                'post_type'  => $existing_post,
                'post_status' => 'any',
                'posts_per_page' => -1
            );
            $postslist = get_posts( $args );
            if($postslist) {
                foreach ($postslist as $post) {
                    // Create post object
                    if (!get_page_by_title($post->post_title, 'OBJECT', 'docs') ){
                        $post_args = array(
                            'post_type'    => 'docs',
                            'post_title'    => $post->post_title,
                            'post_content'  => $post->post_content,
                            'post_status'   => $post->post_status,
                            'post_author'   => $post->post_author,
                            'post_date'   	=> $post->post_date,
                            'post_date_gmt'   => $post->post_date_gmt,
                            'post_excerpt'   => $post->post_excerpt,
                            'comment_status'   => $post->comment_status,
                            'ping_status'   => $post->ping_status,
                            'post_password'   => $post->post_password,
                            'post_name'   => $post->post_name,
                            'to_ping'   => $post->to_ping,
                            'pinged'   => $post->pinged,
                            'post_modified'   => $post->post_modified,
                            'post_modified_gmt'   => $post->post_modified_gmt,
                            'post_content_filtered'   => $post->post_content_filtered,
                            'post_parent'   => $post->post_parent,
                            'post_mime_type'   => $post->post_mime_type,
                            'comment_count'   => $post->comment_count,
                            'filter'   => $post->filter,
                        );
                        // Insert the post into the database
                        $result = wp_insert_post( $post_args );
                        if ( $result && ! is_wp_error( $result ) ) {
                            $cat_list = wp_get_post_terms($post->ID, $existing_cat, array('fields' => 'all'));
                            if($cat_list) {
                                $post_id = $result;
                                wp_set_object_terms( $post_id, array($cat_list['0']->name), 'doc_category', false );
                            }
                            $tag_list = wp_get_post_terms($post->ID, $existing_tag, array('fields' => 'all'));
                            if($tag_list) {
                                $post_id = $result;
                                wp_set_object_terms( $post_id, array($tag_list['0']->name), 'doc_tag', false );
                            }
                        }
                    }
                }
            }
        }
        
        public static function eco_knowledgerbase_migration() {
            self::insert_terms_hierarchically('epkb_post_type_1_category', 'doc_category');
            self::insert_terms_hierarchically('epkb_post_type_1_tag', 'doc_tag');
            self::insert_posts('epkb_post_type_1', 'epkb_post_type_1_category', 'epkb_post_type_1_tag');
        }

        public static function pressapps_migration() {
            self::insert_terms_hierarchically('knowledgebase_category', 'doc_category');
            self::insert_terms_hierarchically('knowledgebase_tags', 'doc_tag');
            self::insert_posts('knowledgebase', 'knowledgebase_category', 'knowledgebase_tags');
        }

        public static function better_docs_quick_setup_wizard_data_save() {
            check_ajax_referer( 'betterdocsqswnonce', 'security');
            $oldValue = get_option( self::$optionGroupName, true );

            // duplicate existing plugin data and deactivate
            if(isset($_POST['activate_plugin'])) {
                $existing_plugins_data = self::existing_plugins_data($_POST['activate_plugin']);
                if ( $existing_plugins_data['name'] === 'echo-knowledge-base' ) {
                    self::eco_knowledgerbase_migration();
                } elseif ( $existing_plugins_data['name'] === 'pressapps-knowledge-base' ) {
                    self::pressapps_migration();
                }
                deactivate_plugins( $existing_plugins_data['url'] );
            }
            $newValue['builtin_doc_page'] = (isset($_POST['builtin_doc_page']) ? $_POST['builtin_doc_page'] : 'off');
            $newValue['docs_slug'] = (isset($_POST['docs_slug']) ? $_POST['docs_slug'] : 'docs');
            $newValue['enable_disable'] = (isset($_POST['enable_disable']) ? $_POST['enable_disable'] : 'off');
            // new update value
            $updatedValue = array_merge($oldValue, $newValue);
            if(!get_option(self::$optionGroupName)) {
                add_option(self::$optionGroupName, $updatedValue);
            } else {
                update_option(self::$optionGroupName, $updatedValue);
            }
            wp_die(); // this is required to terminate immediately and return a proper response
        }

        public static function better_docs_setup_generate_live_url(){
            $query = [];
            $query['autofocus[panel]'] = 'betterdocs_customize_options';
            $query['return'] = admin_url( 'edit.php?post_type=docs' );
            $docs_slug = (isset($_POST['docs_slug']) ? $_POST['docs_slug'] : null);
            if($docs_slug){
                $query['url'] = site_url( '/'.$docs_slug );
            }
            $customizer_link = add_query_arg( $query, admin_url( 'customize.php' ) );


            $result = array(
                'customizerurl' => $customizer_link,
                'siteurl'       => esc_url( site_url( '/'.$docs_slug ) )
            );
            print json_encode( $result);
            wp_die();
        }
        
        public static function plugin_setting_page() {
            ?> 
                <div id="wpwrap">
                    <div class="betterdocs-settings-wrap">
                        <div class="betterdocs-topbar-logo text-center">
                            <img width="150" src="<?php print esc_url( BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-logo.svg'); ?>" alt="logo">
                        </div>
                        <!-- setup wizard -->
                        <div class="betterdocs-setup-wizard">
                            <form method="post" action="#">
                                <input type="hidden" name="betterdocsqswnonce" value="<?php print wp_create_nonce( 'betterdocsqswnonce' ); ?>">
                                <div class="betterdocs-tabnav-wrap">
                                    <ul class="tab-nav">
                                        <?php do_action('betterdocs_nav_tabs'); ?>
                                    </ul>
                                </div>
                                <div class="betterdocs-tab-content-wrap">
                                    <?php 
                                        do_action('betterdocs_tabs_content'); 
                                    ?>
                                    <div class="betterdocs-button-wrap">
                                        <a id="betterdocs-prev-option" href="#" class="btn betterdocs-prev-option"><?php esc_html_e('Previous', 'betterdocs'); ?></a>
                                        <a id="betterdocs-next-option" href="#" class="btn betterdocs-next-option"><?php esc_html_e('Next', 'betterdocs'); ?></a>
                                    </div>
                                    <div class="bottom-notice-left">
                                        <p class="whatwecollecttext">We collect non-sensitive diagnostic data and plugin usage <br> information. Your site URL, WordPress & PHP version, <br> plugins & themes and email address to send you the discount <br> coupon. This data lets us make sure this plugin always stays <br> compatible with the most popular plugins and themes. No spam, we promise.</p>
                                        <button type="button" class="btn-collect"><?php esc_html_e('What We Collect?', 'betterdocs'); ?></button>
                                    </div>
                                    <div class="bottom-notice">
                                        <button type="button" id="betterdocsqswemailskipbutton" class="btn-skip"><?php esc_html_e('Skip This Step', 'betterdocs'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php
        }

        public static function setSection( $section ){
            // Bail if not array.
            if ( ! is_array( $section ) ) {
                return false;
            }
            
            self::$sections_array[$section['id']] = $section;

            // Assign to the sections array
            return  self::$sections_array;
        }

        public static function get_value($args){
            if($args == null || $args == ''){
                return;
            }
            $optionValue = get_option( self::$optionGroupName ) ;
            if(isset($optionValue[$args['id']])) {
               return $optionValue[$args['id']];
            }else if(isset($args['default'])){
                return $args['default'];
            }
            return;
        }

        public static function get_field_description($args){
            if ( ! empty( $args['desc'] ) ) {
                $desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
            } else {
                $desc = '';
            }

            return $desc;
        }

        /**
         * Fields Type: Text
         * @param array
         * @return Markup
         */
        public static function callback_text($args) {
            $value = esc_attr( self::get_value($args) );
            $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
            $type  = isset( $args['type'] ) ? $args['type'] : 'text';
            $field = '';
            $markup = '';
            $field  = sprintf( '<input type="%1$s" class="%2$s-text" name="%3$s" value="%4$s" placeholder="%5$s"/>', $type, $size, $args['id'], $value, $args['placeholder'] );
            $field .= self::get_field_description( $args );
            $markup .= '<tr>
                <th scope="row">
                    <label for="'.$args['id'].'">'.$args['title'].'</label>
                </th>
                <td>
                    '.$field.'
                </td>
            </tr>';
            echo $markup;
        }

        /**
         * Fields Type: textarea
         * @param array
         * @return Markup
         */
        public static function callback_textarea( $args ) {
            $value = esc_textarea( self::get_value($args) );
            $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
            $field = '';
            $markup = '';
            $field  = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s" name="%3$s" placeholder="%4$s">%5$s</textarea>', $size, $args['id'], $args['id'], $args['placeholder'], $value );
            $field .= self::get_field_description( $args );
            $markup .= '<tr>
                <th scope="row">
                    <label for="'.$args['id'].'">'.$args['title'].'</label>
                </th>
                <td>
                    '.$field.'
                </td>
            </tr>';
            echo $markup;
        }
        
        /**
         * Fields Type: checkbox
         * @param array
         * @return Markup
         */
        public static function callback_checkbox_pro_feature( $args ) {
            $value = self::get_value($args);
            $field = '';
            $markup = '';
            $field  = sprintf( '<input class="%1$s-checkbox" id="%1$s" name="%1$s" type="checkbox" %3$s>', $args['id'], 1, checked( 1, $value, false ) );
            $field .= self::get_field_description( $args );
            $markup .= '<tr>
                <th scope="row">
                    <label for="'.$args['id'].'">'.$args['title'].'</label>
                </th>
                <td>';
                    if(!class_exists('Betterdocs_Pro')) {
                        $markup .='<div class="betterdocs-checkbox betterdocs-pro-feature-checkbox" data-id="instant_answer">
                            <input disabled="" type="checkbox" id="instant_answer" name="instant_answer">
                            <label for="instant_answer"></label>
                            <p class="betterdocs-module-title">Instant Answer            
                                <sup class="betterdocs-pro-label has-to-update"></sup><sup class="betterdocs-pro-label">Pro</sup>                
                            </p>
                        </div>';
                    } else {
                        $markup .= $field;
                    }
                $markup .='</td>
            </tr>';
            echo $markup;
        }

        /**
         * Fields Type: link
         * @param array
         * @return Markup
         */
        public static function callback_link( $args ) {
            $value = self::get_value($args);
            $field = '';
            $markup = '';
            $field  = sprintf( '<input class="%1$s-checkbox" id="%1$s" name="%1$s" type="checkbox" %3$s>', $args['id'], 1, checked( 1, $value, false ) );
            $field .= self::get_field_description( $args );
            $markup .= '<tr class="text-center">
                <td colspan="2">
                    <h2 class="bd-sw-title">'.(isset($args['title']) ? $args['title'] : '').'</h2>
                    <p class="bd-sw-sub-title">'.(isset($args['sub_title']) ? $args['sub_title'] : '').'</p>
                </td>
            </tr>
            <tr>
                <td>
                    <ul class="betterdocs-setup-feature-list">';
                        if(is_array($args['options'])){
                            foreach($args['options'] as $item){
                                $markup .='<li>
                                    <div class="bd-media-left"><span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="#37de89" width="40px"><path d="M 21.1875 9.28125 L 19.78125 10.71875 L 24.0625 15 L 4 15 L 4 17 L 24.0625 17 L 19.78125 21.28125 L 21.1875 22.71875 L 27.90625 16 Z"></path></svg></span></div>
                                    <div class="bd-media-body">
                                        <h4><a href="'.esc_url($item['url']).'" target="_blank">'.(isset($item['feature_title']) ? $item['feature_title'] : '').'</a></h4>
                                        <p>'.(isset($item['feature_content']) ? $item['feature_content'] : '').'</p>
                                        <a '.(isset($item['id']) ? 'id="'.$item['id'].'"' : '' ).' href="'.esc_url($item['url']).'" class="btn-setup-wizard" target="_blank">'.$item['title'].'</a>
                                    </div>
                                </li>';
                            }
                        }
                    $markup .='</ul>
                </td>
                <td>
                    <img src="'.(isset($args['image_url']) ? esc_url($args['image_url']) : '').'" />
                </td>
            </tr>';
            echo $markup;
        }
        /**
         * Fields Type: link
         * @param array
         * @return Markup
         */
        public static function callback_final_step( $args ) {
            $value = self::get_value($args);
            $field = '';
            $markup = '';
            $field  = sprintf( '<input class="%1$s-checkbox" id="%1$s" name="%1$s" type="checkbox" %3$s>', $args['id'], 1, checked( 1, $value, false ) );
            $field .= self::get_field_description( $args );
            $markup .= '<tr class="text-center">
                <td colspan="2">';
                    if(isset($args['image_url'])){
                        $markup .= '<img src="'.(isset($args['image_url']) ? $args['image_url'] : '').'" alt="" />';
                    }
                    $markup .='<h2 class="bd-sw-title">'.(isset($args['title']) ? $args['title'] : '').'</h2>
                    <p class="bd-sw-sub-title">'.(isset($args['sub_title']) ? $args['sub_title'] : '').'</p>
                </td>
            </tr>
            <tr class="text-center">
                <td>';
                    foreach($args['options'] as $item){
                        $markup .='<a '.(isset($item['id']) ? 'id="'.$item['id'].'"' : '' ).' href="'.esc_url($item['url']).'" class="btn-setup-wizard" target="_blank">'.$item['title'].'</a>';
                    }
                $markup .='</td>
            </tr>';
            echo $markup;
        }
        
        /**
         * Fields Type: link
         * @param array
         * @return Markup
         */
        public static function callback_migration( $args ) {
            $field = '';
            $markup = '';
            $markup .= '<tr class="text-center">
                <td colspan="2">';
                    $markup .='<p class="bd-sw-sub-title">'.(isset($args['sub_title']) ? $args['sub_title'] : '').'</p>
                </td>
            </tr>';
            
            foreach($args['options'] as $item){
                $field  = sprintf( '<input class="%1$s-checkbox activate_plugin" id="%1$s" name="activate_plugin" type="checkbox" value="%1$s" %2$s>', $item['id'], checked( 1, $item['default'], false ) );
                $markup .= '<tr>
                    <th scope="row">
                        <label for="'.$item['id'].'">'.$item['title'].'</label>
                    </th>
                    <td>
                        '.$field.'
                    </td>
                </tr>';
            }
            echo $markup;
        }

        /**
         * Fields Type: checkbox
         * @param array
         * @return Markup
         */
        public static function callback_checkbox( $args ) {
            $value = self::get_value($args);
            $field = '';
            $markup = '';
            $field  = sprintf( '<input class="%1$s-checkbox" id="%1$s" name="%1$s" type="checkbox" %3$s>', $args['id'], 1, checked( 1, $value, false ) );
            $field .= self::get_field_description( $args );
            $markup .= '<tr>
                <th scope="row">
                    <label for="'.$args['id'].'">'.$args['title'].'</label>
                </th>
                <td>
                    '.$field.'
                </td>
            </tr>';
            echo $markup;
        }

        /**
         * Fields Type: radio
         * @param array
         * @return Markup
         */
        public static function callback_radio( $args ) {
            $value = self::get_value($args);
            $field = '';
            $markup = '';
            if(is_array($args['options'])){
                foreach($args['options'] as $key => $option){
                    $field .= sprintf( '<input class="%1$s-radio" type="radio" name="%1$s" value="%2$s" %4$s>%3$s', $args['id'], $key, $option, checked( $key, $value, false ) );
                }
            }
            $field .= self::get_field_description( $args );
            $markup .= '<tr>
                <th scope="row">
                    <label for="'.$args['id'].'">'.$args['title'].'</label>
                </th>
                <td>
                    '.$field.'
                </td>
            </tr>';
            echo $markup;
        }


        public static function callback_select( $args ){
            $value = self::get_value($args);
            $field = $markup = '';
            $field .= sprintf( '<select id="%1$s" class="%1$s-select" name="%1$s" multiple>', $args['id'] );
                if(is_array($args['options'])){
                    $field .='<option value=""></option>';
                    foreach($args['options'] as $key => $option){
                        $field .= sprintf( '<option value="%1$s" '.(($value != "") ? (in_array($key, $value) ? 'selected' : '') : '').'>%2$s</option>',$key, $option);
                    }
                }
            $field .= '</select>';
            $field .= self::get_field_description( $args );
            $markup .= '<tr>
                <th scope="row">
                    <label for="'.$args['id'].'">'.$args['title'].'</label>
                </th>
                <td>
                    '.$field.'
                </td>
            </tr>';
            echo $markup;
        }

        public static function callback_welcome( $args ){
            $current_user = wp_get_current_user();
            ?>
            <tr>
                <td>
                    <h2 id="<?php print (isset($args['id']) ? $args['id'] : ''); ?>"><?php print (isset($args['title']) ? $args['title'] : ''); ?></h2>
                    <p class="welcome-sub-title"><?php print (isset($args['sub_title']) ? $args['sub_title'] : ''); ?></p>
                    <?php 
                        if(isset($args['video_url'])) :
                    ?>
                    <div class="wpscp-getting-started-video">
                        <iframe width="620" height="350" src="<?php print (isset($args['video_url']) ? esc_url($args['video_url']) : '') ?>" frameborder="0"></iframe>
                    </div>
                    <?php 
                        endif;
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="betterdocs_getting_started_form text-center">
                        <input type="checkbox" id="betterdocs_user_email_address" name="betterdocs_user_email_address" value="<?php print $current_user->user_email; ?>" checked> Share non-sensitive diagnostic data and plugin usage information.
                    </div>
                </td>
            </tr>
            <?php
        }
       
        public static function add_nav_tabs(){
            $tabNavCounter = 0;
            $allSections = apply_filters( 'betterdocs_setup_wizard_fields', self::$sections_array );
            foreach ($allSections as $section) :
                ?>
                    <li class="nav-item<?php print ($tabNavCounter == 0 ? ' tab-active' : ''); ?>">
                        <span class="text"><?php print (isset($section['title']) ? $section['title'] : ''); ?></span>
                        <span class="number"><?php print (isset($section['sub_title']) ? $section['sub_title'] : ''); ?></span>
                    </li>
                <?php
                $tabNavCounter++;
            endforeach;
        }

        public static function add_tab_content(){
            $tabContentCounter = 0;
            $allSections = apply_filters( 'betterdocs_setup_wizard_fields', self::$sections_array );
            foreach ($allSections as $section) :
            ?>
                <div class="tab-content" id="<?php print (isset($section['id']) ? $section['id'] : 'default-nav'); ?>">
                    <table class="form-table" role="presentation">
                        <tbody>
                        <?php 
                            if(isset($section['fields']) && is_array($section['fields'])){
                                foreach ( $section['fields'] as $field ) {
                                    $methodName = 'callback_' . $field['type'];
                                    self::$methodName($field);
                                }
                            }
                        ?>
                         </tbody>
                    </table>
                </div>
            <?php
            $tabContentCounter++;
            endforeach;
        }


    }
    BetterDocsSetupWizard::load();
}