<div class="betterdocs-settings-form-wrapper">
    <form method="post" id="betterdocs-settings-form" action="#">
    <?php 
        $i = 1;
        /**
         * Settings Tab Content Rendering
         */
        foreach( $settings_args as $tab_key => $setting ) {
            $active = $i++ === 1 ? 'active ' : '';
            $sections = isset( $setting['sections'] ) ? $setting['sections'] : [];
            $sections = BetterDocs_Helper::sorter( $sections, 'priority', 'ASC' );
            $is_form = isset( $setting['form'] ) ? $setting['form'] : false;
        ?>
        <div 
            id="betterdocs-<?php echo esc_attr( $tab_key ); ?>" 
            class="betterdocs-settings-tab betterdocs-settings-<?php echo esc_attr( $key );?> <?php echo $active; ?>">
            <?php 
                    if( isset( $setting['views'] ) && ! empty( $setting['views'] ) ) {
                        call_user_func_array( $setting['views'], isset( $setting['sections'] ) ? array( 'sections' => $setting['sections'] ) : [] );
                    }
                    if( ! empty( $sections ) && ! isset( $setting['views'] ) ) :
                        /**
                         * Every Section of a tab 
                         * Rendering.
                         */
                        foreach( $sections as $sec_key => $section ) :
                            $fields = isset( $section['fields'] ) ? $section['fields'] : [];
                            $fields = BetterDocs_Helper::sorter( $fields, 'priority', 'ASC' );

                            $sectionTabs = isset( $section['tabs'] ) ? $section['tabs'] : [];
                            $sectionTabs = BetterDocs_Helper::sorter( $sectionTabs, 'priority', 'ASC' );

                            ?>                                 
                            <div 
                                id="betterdocs-settings-<?php echo esc_attr( $sec_key ); ?>" 
                                class="betterdocs-settings-section betterdocs-<?php echo esc_attr( $sec_key ); ?>">
                                <?php 

                                    if( ! empty( $sectionTabs ) ) {
                                        ?>
                                            <div class="betterdocs-section-inner-tab">
                                                <ul class="betterdocs-section-inner-tab-menu">
                                                    <?php
                                                        array_walk( $sectionTabs, function( $value, $key ){
                                                            echo '<li data-target="'. $key .'">' . $value['title'] . '</li>';
                                                        } );
                                                    ?>
                                                </ul>
                                                <div class="betterdocs-section-inner-tab-contents">
                                                    <?php 
                                                        array_walk( $sectionTabs, function( $section, $key ){
                                                            $fields = isset( $section['fields'] ) ? $section['fields'] : [];
                                                            $fields = BetterDocs_Helper::sorter( $fields, 'priority', 'ASC' );
                                                            echo '<div class="betterdocs-section-inner-tab-content" id="'. $key .'"><table><tbody>';
                                                                array_walk( $fields, function( $field, $field_key ){
                                                                    BetterDocs_Settings::render_field( $field_key, $field );
                                                                } );
                                                            echo '</tbody></table></div>';
                                                        } );
                                                    ?>
                                                </div>
                                            </div>
                                        <?php
                                    }

                                if( isset( $section['views'] ) && ! empty( $section['views'] ) ) {
                                    call_user_func_array( $section['views'], 
                                        isset( $section['fields'] ) ? array( 'fields' => $section['fields'] ) : [] );
                                }
                                /**
                                 * Every Section Field Rendering
                                 */
                                if( ! empty( $fields ) && ! isset( $section['views'] ) ) : ?>
                                <table>
                                    <tbody>
                                    <?php 
                                        foreach( $fields as $field_key => $field ) :
                                            BetterDocs_Settings::render_field( $field_key, $field );
                                        endforeach;
                                    ?>
                                    </tbody>
                                </table>
                                <?php endif; // fields rendering end ?>
                            </div>
                            <?php
                        endforeach;
                    endif; // sections rendering end

                    // Submit Button
                    if( isset( $setting['button_text'] ) && ! empty( $setting['button_text'] ) ) :
                ?>
                <button type="submit" class="btn-settings betterdocs-settings-button betterdocs-submit-<?php echo $tab_key; ?>" data-nonce="<?php echo wp_create_nonce('betterdocs_'. $tab_key .'_nonce'); ?>" data-key="<?php echo $tab_key; ?>" id="betterdocs-submit-<?php echo $tab_key; ?>"><?php _e( $setting['button_text'], 'betterdocs' ); ?></button>
            <?php endif; ?>
        </div>
    <?php } ?>
    </form>
</div>