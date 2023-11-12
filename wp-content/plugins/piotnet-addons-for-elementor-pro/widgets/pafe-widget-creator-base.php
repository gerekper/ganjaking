<?php

if(!class_exists('\Twig\Environment')){
	require_once(__DIR__ . '../../inc/twig-vendor/autoload.php');
}

class PAFE_Widget_Creator_Base extends \Elementor\Widget_Base {

	public $pafe_widget_settings;
	public $pafe_widget_scripts;
	public $pafe_widget_styles;

	public function __construct($data, $settings) {
		parent::__construct($data, $settings);

    	$this->pafe_widget_settings = $settings;
    	$this->pafe_widget_scripts = [];
    	$this->pafe_widget_styles = [];
    	$scripts = [];
    	$styles = [];

    	if (!empty($settings['pafe_widget_creator_assets'])) {
    		$assets = explode(',', $settings['pafe_widget_creator_assets']);

    		foreach ($assets as $asset) {
    			if (strpos($asset, '.js') !== false) {
    				$scripts[] = $asset;
    				$this->pafe_widget_scripts[] = str_replace(['/','.js'], ['-',''], $asset);
    			}

    			if (strpos($asset, '.css') !== false) {
    				$styles[] = $asset;
    				$this->pafe_widget_styles[] = str_replace(['/','.css'], ['-',''], $asset);
    			}
    		}
    	}

    	$upload = wp_upload_dir();
		$upload_dir = $upload['baseurl'];
		$dir = $upload_dir . '/piotnet-addons-for-elementor/widget-creator/';

    	if (!empty($this->pafe_widget_scripts)) {
    		foreach ($this->pafe_widget_scripts as $key => $script) {
    			wp_register_script( $script, $dir . $scripts[$key], [ 'jquery' ], PAFE_PRO_VERSION );
    		}
    	}

    	if (!empty($this->pafe_widget_styles)) {
    		foreach ($this->pafe_widget_styles as $key => $style) {
    			wp_register_style( $style, $dir . $styles[$key], [], PAFE_PRO_VERSION );
    		}
    	}

    	if (!empty($settings['pafe_widget_creator_javascript'])) {
    		$GLOBALS['pafe_widget_creator_scripts'][$this->pafe_widget_settings['pafe_widget_creator_name']] = $settings['pafe_widget_creator_javascript'];
    	}

    	if (!empty($settings['pafe_widget_creator_css'])) {
    		$GLOBALS['pafe_widget_creator_styles'][$this->pafe_widget_settings['pafe_widget_creator_name']] = $settings['pafe_widget_creator_css'];
    	}
	}

	public function pafe_set_settings($settings) {
		return $this->pafe_widget_settings = $settings;
	}

	public function get_name() {
		return $this->pafe_widget_settings['pafe_widget_creator_name'];
	}

	public function get_title() {
		return !empty($this->pafe_widget_settings['pafe_widget_creator_title']) ? $this->pafe_widget_settings['pafe_widget_creator_title'] : 'PAFE Widget Creator';
	}

	public function get_icon() {
		return !empty($this->pafe_widget_settings['pafe_widget_creator_icon']) ? $this->pafe_widget_settings['pafe_widget_creator_icon'] : 'eicon-progress-tracker';
	}

	public function get_categories() {
		return !empty($this->pafe_widget_settings['pafe_widget_creator_categories']) ? explode(',', $this->pafe_widget_settings['pafe_widget_creator_categories']) : [ 'pafe-widget-creator' ];
	}

	public function get_keywords() {
		return !empty($this->pafe_widget_settings['pafe_widget_creator_keywords']) ? explode(',', $this->pafe_widget_settings['pafe_widget_creator_keywords']) : [ 'pafe' ];
	}

	public function get_script_depends() {
		return $this->pafe_widget_scripts;
	}

	public function get_style_depends() {
		return $this->pafe_widget_styles;
	}

	public function pafe_conditions($conditions, $relation) {
		$conditions_return = [];
		$conditions_return['relation'] = $relation;
		$conditions_return['terms'] = [];

		$conditions = explode(PHP_EOL, $conditions);

		foreach ($conditions as $condition) {
			if (!empty($condition)) {
				$condition = explode('|', trim($condition));
				$conditions_return['terms'][] = [
					'name' => $condition[0],
					'operator' => $condition[1],
					'value' => $condition[2]
				];
			}
		}

		return $conditions_return;
	}

	public function pafe_create_control($control, $key, $inside_repeater = false, $repeater = null) {

		$control_name = !empty($control['name']) ? $control['name'] : $this->pafe_widget_settings['pafe_widget_creator_name'] . '_' . $key;
		$control_type = $control['type'];

		if (!empty($control_type)) {

			$control_args = [
				'label' => !empty($control['label']) ? __( $control['label'], 'pafe' ) : __( 'Label', 'pafe' ),
			];

			if (!empty($control['label_block'])) {
				$control_args['label_block'] = true;
			}

			if (!empty($control['conditions_simple_enable']) && !empty($control['conditions_simple'])) {
				$control_args['conditions'] = $this->pafe_conditions($control['conditions_simple'], !empty($control['conditions_simple_relation']) ? $control['conditions_simple_relation'] : 'and');
			}

			if (!empty($control['conditions_advanced_enable']) && !empty($control['conditions_advanced'])) {
				$conditions_advanced_raw = ltrim(rtrim($control['conditions_advanced'], ","), "'conditions' => ");
				$conditions_advanced = [];
				@eval("\$conditions_advanced = $conditions_advanced_raw;");
				if (!empty($conditions_advanced)) {
					$control_args['conditions'] = $conditions_advanced;
				}
			}

			switch ($control_type) {

				case 'section':

					if ($key !== 0) {
						$this->end_controls_section();
					}

					if (!empty($control['tab'])) {
						$control_args['tab'] = $control['tab'];
					}

					$this->start_controls_section(
						$control_name,
						$control_args
					);

					break;

				case 'text':
				case 'textarea':
				case 'number':
				case 'url':
				case 'wysiwyg':
				case 'select':
				case 'select2':
				case 'choose':
				case 'slider':
				case 'color':
				case 'hidden':
				case 'date_time':
				case 'code':
				case 'dimensions':
				case 'font':
				case 'gallery':
				case 'raw_html':
				case 'switcher':
				case 'image_dimensions':
				case 'media':
				case 'icon':
				case 'icons':

					$control_args['type'] = $control_type;
					$control_args['default'] = !empty($control['default']) ? $control['default'] : '';
					$control_args['placeholder'] = !empty($control['placeholder']) ? $control['placeholder'] : '';
					$control_args['description'] = !empty($control['description']) ? $control['description'] : '';
					$control_args['separator'] = !empty($control['separator']) ? $control['separator'] : 'default';
					$control_args['classes'] = !empty($control['classes']) ? $control['classes'] : '';
					
					if ($control_type == 'textarea' || $control_type == 'code') {
						$control_args['rows'] = !empty($control['rows']) ? $control['rows'] : 5;
					}

					if ($control_type == 'number') {
						if (isset($control['min']) && $control['min'] !== '') {
							$control_args['min'] = $control['min'];
						}
						if (isset($control['max']) && $control['max'] !== '') {
							$control_args['max'] = $control['max'];
						}
						if (isset($control['step']) && $control['step'] !== '') {
							$control_args['step'] = $control['step'];
						}
					}

					if ($control_type == 'select' || $control_type == 'select2' || $control_type == 'choose') {
						$control_args['options'] = [];
						if (!empty($control['options'])) {
							$options = explode(PHP_EOL, $control['options']);

							foreach ($options as $option) {
								if (!empty($option)) {
									if (strpos($option, '|') !== false) {
										$option = explode('|', trim($option));
										$control_args['options'][$option[0]] = $option[1];
									} else {
										$control_args['options'][$option] = $option;
									}
								}
							}

							if (strpos($control['options'], '[') !== false && strpos($control['options'], ']') !== false) {
								$options_raw = preg_replace('!\s+!', ' ', trim($control['options']));
								@eval("\$options_array = $options_raw;");

								if (is_array($options_array)) {
									$control_args['options'] = $options_array;
								}
							}
						}
					}

					if ($control_type == 'select2') {
						if (!empty($control['multiple'])) {
							$control_args['multiple'] = true;

							if (!empty($control['default']) && strpos($control['default'], '|') !== false) {
								$control_args['default'] = explode('|', $control['default']);
							}
						}
					}

					if ($control_type == 'dimensions' || $control_type == 'slider') {
						if (!empty($control['size_units'])) {
							$control_args['size_units'] = explode(',', $control['size_units']);
						}
					}

					if ($control_type == 'raw_html') {
						if (!empty($control['raw'])) {
							$control_args['raw'] = $control['raw'];
						}
					}

					if ($control_type == 'switcher') {
						$control_args['label_on'] = !empty($control['label_on']) ? $control['label_on'] : 'Yes';
						$control_args['label_off'] = !empty($control['label_off']) ? $control['label_off'] : 'No';
						$control_args['return_value'] = !empty($control['return_value']) ? $control['return_value'] : 'yes';
					}

					if ($control_type == 'media') {
						$control_args['media_types'] = !empty($control['media_types']) ? $control['media_types'] : ['image'];
					}

					if ($control_type == 'slider') {
						if (!empty($control['range'])) {
							$range_raw = preg_replace('!\s+!', ' ', trim($control['range']));
							@eval("\$range_array = $range_raw;");
							if (is_array($range_array)) {
								$control_args['range'] = $range_array;
							}
						}
						$control_args['default'] = !empty($control['default']) ? $control['default'] : [];
					}

					if (!empty($control['selectors_enable']) && !empty($control['selectors'])) {
						$selectors = explode(PHP_EOL, $control['selectors']);
						$control_args['selectors'] = [];
						foreach ($selectors as $selector) {
							$selector = explode('|', $selector);
							$control_args['selectors'][trim($selector[0])] = trim($selector[1]);
						}
					}

					if (!empty( $control['default'] )) {
						$default_raw = preg_replace('!\s+!', ' ', trim($control['default']));
						@eval("\$default_array = $default_raw;");

						if (is_array($default_array)) {
							$control_args['default'] = $default_array;
						}
					}

					if (empty($control['responsive'])) {
						if (!$inside_repeater) {
							$this->add_control(
								$control_name,
								$control_args
							);
						} else {
							$repeater->add_control(
								$control_name,
								$control_args
							);
						}
					} else {
						if (!empty($control['devices'])) {
							$control_args['devices'] = $control['devices'];
						}

						if (!$inside_repeater) {
							$this->add_responsive_control(
								$control_name,
								$control_args
							);
						} else {
							$repeater->add_responsive_control(
								$control_name,
								$control_args
							);
						}
					}

					// echo '<pre>';
					// print_r($control_args);
					// echo '</pre>';

					break;

				case 'background':
				case 'border':
				case 'box-shadow':
				case 'css-filter':
				case 'image-size':
				case 'text-shadow':
				case 'typography':

					$control_args['name'] = $control_name;

					if (!empty($control['selectors_enable']) && !empty($control['selectors'])) {
						$control_args['selector'] = $control['selectors'];
					}

					if (!$inside_repeater) {
						$this->add_group_control(
							$control_type,
							$control_args
						);
					} else {
						$repeater->add_group_control(
							$control_type,
							$control_args
						);
					}

					break;

				case 'repeater_end':
					
					if ($control['title_field']) {
						$control_args['title_field'] = $control['title_field'];
					}

					if (!empty( $control['default'] )) {
						$default_raw = preg_replace('!\s+!', ' ', trim($control['default']));
						@eval("\$default_array = $default_raw;");

						if (is_array($default_array)) {
							$control_args['default'] = $default_array;
						}
					}

					$control_args['fields'] = $repeater->get_controls();
					$control_args['type'] = 'repeater';
					$control_args['prevent_empty'] = false;

					$this->add_control(
						$control_name,
						$control_args
					);

					break;

				case 'tabs_start':

					$this->start_controls_tabs(
						$control_name,
						$control_args
					);

					break;

				case 'tabs_end':

					$this->end_controls_tabs();

					break;

				case 'tab_start':

					$this->start_controls_tab(
						$control_name,
						$control_args
					);

					break;

				case 'tab_end':

					$this->end_controls_tab();

					break;
			}
		}

	}

	protected function _register_controls() {

		$controls = $this->pafe_widget_settings['pafe_widget_creator_controls'];

		if (!empty($controls)) {
			$inside_repeater = false;
			$repeater = null;

			if ($controls[0]['type'] !== 'section') {
				return;
			}

			foreach ($controls as $key => $control) {
				if ($control['type'] == 'repeater_start') {
					$repeater = new \Elementor\Repeater();
					$inside_repeater = true;
				}
				if ($control['type'] == 'repeater_end') {
					$inside_repeater = false;
				}

				$this->pafe_create_control($control, $key, $inside_repeater, $repeater);
			}

			$this->end_controls_section();

		}
	}

	public function pafe_render($render) {
		ob_start();

		$settings = $this->get_settings_for_display();

		foreach ($settings as $key => $setting) {
			${$key} = $setting;
		}

		try {
		    eval("?> $render <?php ");;
		} catch (ParseError $e) {
		    echo 'PAFE Widget Creator Debug: ' . $e->getMessage();
		}

		return ob_get_clean();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if (!empty($this->pafe_widget_settings['pafe_widget_creator_render'])) :
			
			$loader = new \Twig\Loader\ArrayLoader([]);
			$twig = new \Twig\Environment($loader, [
			    'debug' => true,
			]);
			$twig->addExtension(new \Twig\Extension\DebugExtension());
			$render_template = $this->pafe_render($this->pafe_widget_settings['pafe_widget_creator_render']);

			if (stripos($render_template, 'PAFE Widget Creator Debug') === false) {
				try {
	            	$twig->tokenize($render_template);
		            $template = $twig->createTemplate($render_template);
					echo $template->render($settings);
		        } catch (\Twig_Error_Syntax $e) {
		        	$error_message = $e->getMessage();
		        	$error_message = explode('"', $error_message);

		        	echo '<pre>';
		        	echo 'PAFE Widget Creator Debug: ' . $error_message[0] . 'Render Template' . $error_message[2];
		        	echo '</pre>';
		        }
			} else {
				echo '<pre>';
				echo $render_template;
				echo '</pre>';
			}
			
    	endif;
	}

}