<?php
class PAFE_Form_Builder_Data extends \Elementor\Widget_Base {

    public function get_name(){
        return 'pafe-display-form-submissions';
    }

    public function get_title(){
        return __('Form Entries', 'pafe');
    }

    public function get_icon() {
        return 'icon-w-form-entries';
    }

    public function get_categories(){
        return ['pafe-form-builder'];
    }

    public function get_keywords(){
        return ['form', 'data'];
    }

    public function get_script_depends(){
        return [
            'pafe-form-builder',
        ];
    }

    public function get_style_depends(){
        return [
            'pafe-form-builder-style'
        ];
    }

    protected function _register_controls(){
        $submissions = get_posts(
            array(
                'post_type' => 'pafe-form-database',
                'posts_per_page' => -1,
                'fields' => 'ids',
            )
        );

        $field_id = array();
        $forms_id = array();
        $th = array();
        
        foreach ($submissions as $submission_id) {
            $fields_database = get_post_meta($submission_id, '_pafe_form_builder_fields_database', true);
            if ($fields_database) {
                $fields_database = json_decode($fields_database, true);

            }
            $metas = get_post_meta($submission_id);

            foreach($metas as $key => $value) {
                if ( $key === 'form_id' ) {
                    foreach ( $value as $formid ) {
                        $forms_id[$formid] = $formid;
                    }
                }
                if (!in_array($key,$field_id)) {
                    if (is_array($fields_database)) {
                        if (isset($fields_database[$key])) {
                            $field_id[] = $key;
                        }
                    } else {
                        $field_id[] = $key;
                    }
                }
            }
            foreach($field_id as $id) {
                if ( $id != 'form_id' && $id != '_elementor_controls_usage' && $id != '_edit_lock' && $id != 'form_id_elementor' && $id != 'post_id' && $id != '_pafe_form_builder_fields_database') {
                    if (is_array($fields_database)) {
                        if (isset($fields_database[$id])) {
                            if (!empty($fields_database[$id]['label'])) {
                                $th[$fields_database[$id]['label']] = $fields_database[$id]['label'];
                            }
                        }
                    } else {
                        $th[] = $id;
                    }
                }
            }

            $th = array_unique($th);
        }

        $this->start_controls_section(
            'pafe_form_data_section',
            [
                'label' => __( 'Settings', 'pafe' ),
            ]
        );
        $this->add_control(
            'pafe_form_data_id',
            [
                'label' => __( 'Form ID* (Required)', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $forms_id,
            ]
        );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'pafe_form_data_field_label',
            [
                'label' => __( 'Field Label', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $th,
            ]
        );

        $this->add_control(
            'pafe_form_data_repeater',
            [
                'type' => Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ pafe_form_data_field_label }}}',
            ]

        );
        $this->end_controls_section();
        $this->start_controls_section(
            'pafe_form_data_field_name_style',
            [
                'label' => __( 'Field Name', 'pafe' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'pafe_form_data_field_name_color',
            [
                'label' => __( 'Background Color', 'Background Control', 'pafe' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pafe-form-data__field--name' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'pafe_form_data_field_name_typography',
                'selector' => '{{WRAPPER}} .pafe-form-data__field--name',
                'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
            ]
        );

        $this->add_responsive_control(
            'pafe_form_data_field_name_padding',
            [
                'label' => __( 'Padding', 'pafe' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .pafe-form-data__field--name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pafe_form_data_field_name_align',
            [
                'label' => __( 'Alignment', 'elementor' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'pafe' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'pafe' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'pafe' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pafe-form-data__field--name' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pafe_form_data_field_name_background_color',
            [
                'label' => __( 'Background Color', 'Background Control', 'pafe' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pafe-form-data__field--name' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'pafe_form_data_field_value_style',
            [
                'label' => __( 'Field Value', 'pafe' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'pafe_form_data_field_value_color',
            [
                'label' => __( 'Color', 'Background Control', 'pafe' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pafe-form-data__field--value' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'pafe_form_data_field_value_typography',
                'selector' => '{{WRAPPER}} .pafe-form-data__field--value',
                'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
            ]
        );

        $this->add_responsive_control(
            'pafe_form_data_field_value_padding',
            [
                'label' => __( 'Padding', 'pafe' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .pafe-form-data__field--value' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pafe_form_data_field_value_align',
            [
                'label' => __( 'Alignment', 'pafe' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'pafe' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'pafe' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'pafe' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pafe-form-data__field--value' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'pafe_form_data_field_value_background_color',
            [
                'label' => __( 'Background Color', 'Background Control', 'pafe' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pafe-form-data__field--value' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'pafe_form_data_table_style',
            [
                'label' => __( 'Table', 'pafe' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'pafe_form_data_table_width',
            [
                'label' => __( 'Table Width', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 500,
                        'max' => 2000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .pafe-form-database-table' => 'width: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .pafe-form-database-table table' => 'width: 100%',
                    '{{WRAPPER}} .pafe-form-database-table table' => 'margin: 0',
                ],

            ]
        );

        $this->add_responsive_control(
            'pafe_form_data_column_width',
            [
                'label' => __( 'Column Width', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pafe-form-database-table .pafe-form-data__field--name' => 'width: {{SIZE}}{{UNIT}}',
                ],

            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'pafe_form_data_table_border',
                'label' => __( 'Border', 'pafe' ),
                'selector' => '{{WRAPPER}} .pafe-form-database-table table',
            ]
        );

        $this->add_control(
            'pafe_form_data_table_background_color',
            [
                'label' => __( 'Background Color', 'Background Control', 'pafe' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pafe-form-database-table table' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pafe_form_data_table_padding',
            [
                'label' => __( 'Padding', 'pafe' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .pafe-form-database-table' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    private function getColumnsNeedToShow($all_columns, $show_columns) {
        $columnIndexes = array();
        foreach ($show_columns as $item ) {
            $columnIndexes[] =  array_search($item['pafe_form_data_field_label'], $all_columns);
        }
        return $columnIndexes;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $fields = $this->get_fields_data($settings);
        $columnIndexes = $this->getColumnsNeedToShow($fields[0], $settings['pafe_form_data_repeater']);
       ?>
    <div class="pafe-form-database-table">
        <table>
            <tr>
            <?php foreach ( $fields[0] as $key => $fields_th ) : if (in_array( $key, $columnIndexes )) : ?>
                <th class="pafe-form-data__field--name"><?php echo $fields_th; ?></th>
            <?php endif; endforeach; ?>
            </tr>

            <?php
            $field = array_shift($fields);
            foreach ( $fields as $fields_td ) :
            ?>
                <tr>
                <?php foreach ( $fields_td as $key => $fields_tds ) : if (in_array( $key, $columnIndexes ) && isset($fields_tds)) : ?>
                    <td class="pafe-form-data__field--value"><?php echo $fields_tds; ?></td>
                <?php endif; endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php
        wp_reset_postdata();
    }
    private function get_fields_data($settings=array()) {
        $submissions = get_posts(
            array(
                'post_type' => 'pafe-form-database',
                'posts_per_page' => -1,
                'fields' => 'ids',
            )
        );

        if ( !empty($settings['pafe_form_data_id']) ) {
            $form_id = $settings['pafe_form_data_id'];
            $args['meta_value'] = str_replace('+', ' ', $form_id);
            $args['meta_key'] = 'form_id';
        }

        $field_id = array();
        $fields = array();
        $th = array();
        $index = 0;

        foreach ($submissions as $submission_id) {
            $index++;
            if ($index == 1) {
                $fields_database = get_post_meta($submission_id, '_pafe_form_builder_fields_database', true);
                if ($fields_database) {
                    $fields_database = json_decode($fields_database, true);
                }
            }
            $metas = get_post_meta($submission_id);
            foreach( $metas as $key => $value ) {
                if (!in_array($key,$field_id)) {
                    if (is_array($fields_database)) {
                        if (isset($fields_database[$key])) {
                            $field_id[] = $key;
                        }
                    } else {
                        $field_id[] = $key;
                    }
                }
            }
        }

        foreach($field_id as $id) {
            if ( $id != 'form_id' && $id != '_elementor_controls_usage' && $id != '_edit_lock' && $id != 'form_id_elementor' && $id != 'post_id' && $id != '_pafe_form_builder_fields_database') {
                if (is_array($fields_database)) {
                    if (isset($fields_database[$id])) {
                        $th[] = !empty($fields_database[$id]['label']) ? $fields_database[$id]['label'] : $id;
                    }
                } else {
                    $th[] = $id;
                }
            }
        }

        $fields[] = $th;

        foreach ($submissions as $submission_id) {
            $tr = array();
            foreach($field_id as $id) {
                if ( $id != 'form_id' && $id != '_elementor_controls_usage' && $id != '_edit_lock' && $id != 'form_id_elementor' && $id != 'post_id' && $id != '_pafe_form_builder_fields_database') {
                    $meta_value = get_post_meta($submission_id,$id,true);
                    $tr[] = $meta_value;
                }
            }

            $fields[] = $tr;
        }
        
        return $fields;
    }
}
