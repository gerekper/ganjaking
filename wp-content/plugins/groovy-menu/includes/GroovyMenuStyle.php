<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( ! class_exists( 'GroovyMenuStyle' ) ) {

	/**
	 * Class GroovyMenuStyle
	 */
	class GroovyMenuStyle {
		/**
		 * All preset options
		 *
		 * @var array
		 */
		protected $options = array();

		/**
		 * All global Groovy Menu options
		 *
		 * @var array
		 */
		protected $optionsGlobal = array();

		/**
		 * Preset settings
		 *
		 * @var array
		 */
		protected $settings;

		/**
		 * Global Groovy Menu settings
		 *
		 * @var array
		 */
		protected $settingsGlobal;

		/**
		 * Preset data
		 *
		 * @var array
		 */
		protected $preset;

		/**
		 * Preset screenshot
		 *
		 * @var string
		 */
		protected $presetScreenshot;

		const OPTION_NAME = 'groovy_menu_settings';

		/**
		 * Return option name
		 *
		 * @param GroovyMenuPreset $preset for id of preset.
		 *
		 * @return string
		 */
		public static function getOptionName( GroovyMenuPreset $preset ) {
			return self::OPTION_NAME . '_preset_' . $preset->getId();
		}

		/**
		 * GroovyMenuStyle constructor.
		 *
		 * @param null $presetId if not null construct for specific preset.
		 */
		public function __construct( $presetId = null ) {
			$this->optionsGlobal = include GROOVY_MENU_DIR . 'includes/config/ConfigGlobal.php';
			$this->options       = include GROOVY_MENU_DIR . 'includes/config/Config.php';
			$preset              = GroovyMenuPreset::getById( $presetId );

			if ( is_null( $presetId ) || empty( $presetId ) || ! $preset ) {
				$preset = GroovyMenuPreset::getCurrentPreset();
			} else {
				$preset = new GroovyMenuPreset( $presetId );
			}

			$this->preset           = $preset;
			$this->presetScreenshot = $preset::getPreviewById( $preset->getId() );
			$this->loadPresetSettings();
			$this->loadGlobalSettings();

		}

		/**
		 * Set preset
		 *
		 * @param GroovyMenuPreset $preset
		 */
		public function setPreset( GroovyMenuPreset $preset ) {
			$this->preset = $preset;
		}

		/**
		 * Get preset
		 *
		 * @return GroovyMenuPreset
		 */
		public function getPreset() {
			return $this->preset;
		}

		/**
		 * Get Screenshot of preset
		 *
		 * @return bool|mixed
		 */
		public function getScreenshot() {
			return $this->presetScreenshot;
		}

		/**
		 * Get groups
		 *
		 * @param string $name name of group.
		 *
		 * @return array
		 */
		public function getGroups( $name ) {
			$groups = array();
			foreach ( $this->options[ $name ]['fields'] as $group => $field ) {
				if ( 'group' === $field['type'] ) {
					$groups[ $group ] = $field;
				}
			}
			if ( 0 === count( $groups ) ) {
				$groups[ $name ]['title'] = $this->options[ $name ]['title'];
			}

			return $groups;
		}

		/**
		 * Serialize config with values for front-end
		 *
		 * @param bool $get_all  if need ignore serialize sub-param.
		 *
		 * @param bool $camelize if need camelize keys name.
		 *
		 * @return array
		 */
		public function serialize( $get_all = false, $camelize = true, $get_global = true, $get_storage = true ) {
			$settings = array();

			if ( isset( $_POST ) && isset( $_POST['wp_customize'] ) ) {
				$customized = json_decode( stripslashes( $_POST['customized'] ), true );
				foreach ( $customized as $field => $value ) {
					$position = stripos( $field, 'groovy-' );
					if ( false !== $position ) {
						$field = explode( '--', str_replace( 'groovy-', '', $field ) );
						$this->set( $field[1], $value );
					}
				}
			}

			// If the parameter is compiled earlier, it will be restored.
			if ( $get_storage && GroovyMenuStyleStorage::getInstance()->get_preset_settings_serialized( $this->preset->getId() ) ) {
				return GroovyMenuStyleStorage::getInstance()->get_preset_settings_serialized( $this->preset->getId() );
			}

			if ( $get_global ) {
				// merge preset setting and global settings.
				$all_settings = array_merge( $this->getSettings(), $this->getSettingsGlobal() );
			} else {
				$all_settings = $this->getSettings();
			}

			foreach ( $all_settings as $categoryName => $group ) {
				foreach ( $group['fields'] as $name => $field ) {
					if ( ! $get_all && isset( $field['serialize'] ) && ! $field['serialize'] ) {
						continue;
					}

					if ( $get_all && isset( $field['type'] ) && 'group' === $field['type'] ) {
						continue;
					}


					$value = $this->getField( $categoryName, $name )->getValue();


					if ( $get_all && is_array( $value ) && isset( $field['type'] ) && 'header' !== $field['type'] ) {
						$value = addslashes( wp_json_encode( $value ) );
					}

					if ( $get_all && isset( $field['type'] ) && ( 'textarea' === $field['type'] || 'text' === $field['type'] ) ) {
						$value = $this->escapeJsonString( trim( $value ) );
					}

					if ( $camelize && 'media' === $field['type'] && ! empty( $field['image_size_field'] ) ) {
						if ( ! empty( $field['value'] ) ) {
							$image_size = 'full';
							if ( isset( $all_settings[ $categoryName ]['fields'][ $field['image_size_field'] ] ) ) {
								$size_field = $all_settings[ $categoryName ]['fields'][ $field['image_size_field'] ];
								if ( isset( $size_field['value'] ) ) {
									$image_size = $size_field['value'];
								} elseif ( isset( $size_field['default'] ) ) {
									$image_size = $size_field['default'];
								}
							}
							$thumbnail = wp_get_attachment_image_src( $field['value'], $image_size );
							$value     = empty( $thumbnail[0] ) ? $value : $thumbnail[0];
						}
					}

					$keyName = $name;
					if ( $camelize ) {
						$keyName = lcfirst( str_replace( ' ', '', ucwords( str_replace( '_', ' ', $name ) ) ) );
					}
					$settings[ $keyName ] = $value;

				}
			}

			GroovyMenuStyleStorage::getInstance()->set_preset_settings_serialized( $this->preset->getId(), $settings );

			return $settings;
		}

		/**
		 * @param $value
		 *
		 * @return mixed
		 */
		public function escapeJsonString( $value ) { # list from www.json.org: (\b backspace, \f formfeed)
			$escapers     = array( "\'", "\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c", '\r\n' );
			$replacements = array( "'", "\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b", '\n' );
			$result       = str_replace( $escapers, $replacements, $value );

			return $result;
		}

		/**
		 * Get and return custom css class from preset.
		 *
		 * @return string
		 */
		public function getCustomHtmlClass() {

			$return_string = '';

			// Add custom user CSS class stored in preset.
			$custom_css_class         = $this->get( 'general', 'custom_css_class' );
			$custom_css_class_escaped = esc_attr( trim( $custom_css_class ) );
			if ( ! empty( $custom_css_class_escaped ) ) {
				$return_string = $custom_css_class_escaped;
			}

			return $return_string;
		}

		/**
		 * Return array of html classes for GM wrapper
		 *
		 * @return array
		 */
		public function getHtmlClasses() {
			$classes_navbar = array();

			$settings = $this->serialize( true );

			$compiled_css = $this->get( 'general', 'compiled_css' );

			if ( empty( $compiled_css ) ) {
				$classes_navbar[] = 'gm-no-compiledCss';
			}

			$custom_css_class = $this->getCustomHtmlClass();
			if ( ! empty( $custom_css_class ) ) {
				$classes_navbar[] = $custom_css_class;
			}

			// Header types.
			if ( ! empty( $settings['header'] ) ) {

				if ( ! empty( $settings['header']['align'] ) ) {
					$classes_navbar[] = 'gm-navbar--align-' . $settings['header']['align'];
				}

				if ( ! empty( $settings['header']['style'] ) && ! empty( $settings['header']['align'] ) ) {
					$classes_navbar[] = 'gm-navbar--style-' . $settings['header']['style'];

					if ( $settings['header']['style'] === 1 && $settings['header']['align'] !== 'center' ) {
						$classes_navbar[] = 'gm-top-links-align-' . $settings['topLvlLinkAlign'];
					}

				}

				if ( $settings['header']['toolbar'] ) {
					$classes_navbar[] = 'gm-navbar--toolbar-' . $settings['header']['toolbar'];
				}

				if ( $settings['header']['style'] === 1 ) {
					if ( isset( $settings['showDividerBetweenMenuLinks'] ) && $settings['showDividerBetweenMenuLinks'] ) {
						$classes_navbar[] = 'gm-navbar--has-divider';
					}
				}

			}

			if ( isset( $settings['shadow'] ) && $settings['shadow'] && $settings['header']['style'] !== 3 ) {
				$classes_navbar[] = 'gm-navbar--has-shadow';
			}

			if ( isset( $settings['shadowSticky'] ) && $settings['shadowSticky'] && $settings['header']['style'] !== 3 ) {
				$classes_navbar[] = 'gm-navbar--has-shadow-sticky';
			}

			if ( isset( $settings['shadowDropdown'] ) && $settings['shadowDropdown'] && $settings['header']['style'] !== 3 ) {
				$classes_navbar[] = 'gm-navbar--has-shadow-dropdown';
			}

			if ( isset( $settings['caret'] ) && ! $settings['caret'] ) {
				$classes_navbar[] = 'gm-navbar--hide-gm-caret';
			}

			// Top level hover Style
			if ( isset( $settings['dropdownHoverStyle'] ) && $settings['dropdownHoverStyle'] ) {
				$classes_navbar[] = 'gm-dropdown-hover-style-' . $settings['dropdownHoverStyle'];
			}

			// Dropdown appearance style
			if ( isset( $settings['dropdownAppearanceStyle'] ) && $settings['dropdownAppearanceStyle'] ) {
				$classes_navbar[] = 'gm-dropdown-appearance-' . $settings['dropdownAppearanceStyle'];
			}

			// Use dark style logo in sidebar menu
			if ( isset( $settings['sidebarMenuUseDarkStyleLogo'] ) && $settings['sidebarMenuUseDarkStyleLogo'] ) {
				$classes_navbar[] = 'gm-navbar--style-3__dark';
			}

			return $classes_navbar;

		}


		/**
		 * @param bool $withFiles
		 *
		 * @return array
		 */
		public function getSettingsArray( $withFiles = false ) {
			$settings = array();
			foreach ( $this->getSettings() as $categoryName => $group ) {
				foreach ( $group['fields'] as $name => $field ) {
					$settings[ $name ] = $this->getField( $categoryName, $name )->getValue();
					if ( $withFiles && $field['type'] === 'media' ) {
						$attachmentId = $settings[ $name ];

						if ( get_attached_file( $attachmentId ) ) {
							$data = '';
							global $wp_filesystem;
							if ( empty( $wp_filesystem ) ) {
								if ( file_exists( ABSPATH . '/wp-admin/includes/file.php' ) ) {
									require_once ABSPATH . '/wp-admin/includes/file.php';
									WP_Filesystem();
								}
							}
							if ( empty( $wp_filesystem ) ) {
								if ( function_exists( 'file_get_contents' ) ) {
									$data = base64_encode( file_get_contents( get_attached_file( $attachmentId ) ) );
								}
							} else {
								$data = base64_encode( $wp_filesystem->get_contents( get_attached_file( $attachmentId ) ) );
							}

							$settings[ $name ] = array(
								'type'           => 'media',
								'data'           => $data,
								'post_mime_type' => get_post_mime_type( $attachmentId )
							);
						}

					}
				}
			}

			return $settings;
		}

		public function getSettings() {
			return $this->settings;
		}

		public function getSettingsGlobal() {
			return $this->settingsGlobal;
		}

		/**
		 * @param $name
		 * @param $value
		 */
		public function set( $name, $value ) {
			foreach ( $this->settings as $categoryName => $category ) {
				foreach ( $category['fields'] as $fieldName => $field ) {
					if ( $name === $fieldName ) {

						if ( is_array( $value ) ) {
							$value = addslashes( wp_json_encode( $value ) );
						}

						$this->settings[ $categoryName ]['fields'][ $fieldName ]['value'] = $value;

					}
				}
			}

		}

		/**
		 * @param      $category
		 * @param null $name
		 *
		 * @return null|string
		 */
		public function get( $category, $name = null ) {
			if ( is_null( $name ) ) {
				return null;
			}
			if ( isset( $this->settings[ $category ]['fields'][ $name ] ) ) {
				return $this->getField( $category, $name )->getValue();
			}
		}

		/**
		 * @param      $category
		 * @param null $name
		 *
		 * @return null|string
		 */
		public function getGlobal( $category, $name = null ) {
			if ( is_null( $name ) ) {
				return null;
			}
			if ( isset( $this->settingsGlobal[ $category ]['fields'][ $name ] ) ) {

				$field = $this->getField( $category, $name );

				if ( 'logo' === $category && 'media' === $field->getFieldType() ) {
					return $field->getValueId();
				} else {
					return $field->getValue();
				}

			}
		}

		/**
		 * @param $category
		 * @param $name
		 *
		 * @return GroovyMenuFieldField
		 */
		public function getField( $category, $name ) {
			if ( isset( $this->settings[ $category ]['fields'][ $name ] ) ) {
				$field = $this->settings[ $category ]['fields'][ $name ];
				$class = 'GroovyMenuField' . ucfirst( $field['type'] );

				return new $class( $category, $name, $field );
			}
			if ( isset( $this->settingsGlobal[ $category ]['fields'][ $name ] ) ) {
				$field = $this->settingsGlobal[ $category ]['fields'][ $name ];
				$class = 'GroovyMenuField' . ucfirst( $field['type'] );

				return new $class( $category, $name, $field );
			}

			return null;
		}

		/**
		 * @param null $data
		 */
		public function update( $data = null ) {
			if ( ! is_null( $data ) ) {
				$this->settings = $data;
			}
			update_option( self::getOptionName( $this->preset ), $this->settings );
		}

		/**
		 * @param null $data
		 */
		public function updateGlobal( $data = null ) {
			if ( ! is_null( $data ) ) {
				$this->settingsGlobal = $data;
			}

			if ( isset( $this->settingsGlobal['taxonomies'] ) && isset( $this->settingsGlobal['taxonomies']['default_master_preset'] ) ) {
				$master_preset = $this->settingsGlobal['taxonomies']['default_master_preset'];

				if ( 'default' === $master_preset ) {
					delete_option( GroovyMenuPreset::DEFAULT_PRESET_OPTION );
				} else {
					update_option( GroovyMenuPreset::DEFAULT_PRESET_OPTION, $master_preset );
				}
			}

			if ( isset( $this->settingsGlobal['taxonomies'] ) && isset( $this->settingsGlobal['taxonomies']['default_master_menu'] ) ) {
				$master_menu = intval( $this->settingsGlobal['taxonomies']['default_master_menu'] );
				$locations   = get_theme_mod( 'nav_menu_locations' );

				if ( $master_menu ) {
					if ( ! isset( $locations['gm_primary'] ) || $locations['gm_primary'] !== $master_menu ) {
						$locations['gm_primary'] = $master_menu;
						set_theme_mod( 'nav_menu_locations', $locations );
					}
				}
			}

			update_option( self::OPTION_NAME, $this->settingsGlobal );
		}

		protected function loadPresetSettings() {

			// If the parameter is compiled earlier, it will be restored.
			if ( GroovyMenuStyleStorage::getInstance()->get_preset_settings( $this->preset->getId() ) ) {
				$this->settings = GroovyMenuStyleStorage::getInstance()->get_preset_settings( $this->preset->getId() );

				return;
			}

			$this->settings = $this->options;
			$settings       = get_option( self::getOptionName( $this->preset ) );

			if ( is_array( $settings ) ) {

				$exclude_types = array( 'inlineStart', 'inlineEnd' );

				foreach ( $settings as $categoryName => $category ) {
					if ( isset( $category['fields'] ) ) {
						foreach ( $category['fields'] as $field => $value ) {
							if ( isset( $this->options[ $categoryName ]['fields'][ $field ] ) ) {

								$field_opt = $this->options[ $categoryName ]['fields'][ $field ];

								if ( isset( $value['type'] ) && in_array( $value['type'], $exclude_types ) ) {
									$value = '';
								} elseif ( is_array( $value ) && isset( $value['value']['type'] ) ) {
									$value = '';
								} elseif ( is_array( $value ) && isset( $value['value'] ) ) {
									$value = $value['value'];
								} elseif ( ! isset( $value['value'] ) && isset( $value['default'] ) ) {
									$value = $value['default'];
								} else {
									$value = '';
								}

								if ( ! empty( $value ) && isset( $field_opt['type'] ) && 'header' !== $field_opt['type'] ) {
									$_test_val = json_decode( stripslashes( $value ), true );
									if ( is_array( $_test_val ) && isset( $_test_val['value'] ) ) {
										$value = is_null( $_test_val['value'] ) ? '' : $_test_val['value'];
									} elseif ( is_array( $_test_val ) && isset( $_test_val['type'] ) ) {
										$value = '';
									} elseif ( is_array( $_test_val ) ) {
										$value = addslashes( $value );
									}
								}

								$this->settings[ $categoryName ]['fields'][ $field ]['value'] = $value;
							}
						}
					} else {
						foreach ( $category as $field => $value ) {
							if ( isset( $this->options[ $categoryName ]['fields'][ $field ] ) ) {

								$field_opt = $this->options[ $categoryName ]['fields'][ $field ];

								if ( is_array( $value ) && isset( $value['value']['type'] ) ) {
									$value = '';
								} elseif ( is_array( $value ) && isset( $value['value'] ) ) {
									$value = $value['value'];
								} elseif ( ! isset( $value['value'] ) && isset( $value['default'] ) ) {
									$value = $value['default'];
								}

								if ( ! empty( $value ) && isset( $field_opt['type'] ) && 'header' !== $field_opt['type'] ) {
									$_test_val = json_decode( stripslashes( $value ), true );
									if ( is_array( $_test_val ) && isset( $_test_val['value'] ) ) {
										$value = is_null( $_test_val['value'] ) ? '' : $_test_val['value'];
									} elseif ( is_array( $_test_val ) && isset( $_test_val['type'] ) ) {
										$value = '';
									} elseif ( is_array( $_test_val ) ) {
										$value = addslashes( $value );
									}
								}

								$this->settings[ $categoryName ]['fields'][ $field ]['value'] = $value;
							}
						}
					}

				}
			} else {
				// set all values from default sub field.
				foreach ( $this->options as $categoryName => $category ) {
					foreach ( $category['fields'] as $field => $parameters ) {
						if ( isset( $parameters['default'] ) ) {
							$this->settings[ $categoryName ]['fields'][ $field ]['value'] = $parameters['default'];
						}
					}
				}
			}

			// Store compiled settings for cache.
			GroovyMenuStyleStorage::getInstance()->set_preset_settings( $this->preset->getId(), $this->settings );

		}

		public function loadGlobalSettings() {

			// If the parameter is compiled earlier, it will be restored.
			if ( GroovyMenuStyleStorage::getInstance()->get_global_settings() ) {
				$this->settingsGlobal = GroovyMenuStyleStorage::getInstance()->get_global_settings();

				return;
			}

			$this->settingsGlobal = $this->optionsGlobal;
			$settings             = get_option( self::OPTION_NAME );

			if ( is_array( $settings ) ) {
				foreach ( $settings as $categoryName => $category ) {
					if ( isset( $category['fields'] ) ) {
						foreach ( $category['fields'] as $field => $value ) {
							if ( isset( $this->optionsGlobal[ $categoryName ]['fields'][ $field ] ) ) {
								$this->settingsGlobal[ $categoryName ]['fields'][ $field ]['value'] = $value;
							}
						}
					} else {
						foreach ( $category as $field => $value ) {
							if ( isset( $this->optionsGlobal[ $categoryName ]['fields'][ $field ] ) ) {
								$this->settingsGlobal[ $categoryName ]['fields'][ $field ]['value'] = $value;
							}
						}
					}

				}
			} else {
				foreach ( $this->optionsGlobal as $categoryName => $category ) {
					foreach ( $category['fields'] as $field => $parameters ) {
						if ( isset( $parameters['default'] ) ) {
							$this->settingsGlobal[ $categoryName ]['fields'][ $field ]['value'] = $parameters['default'];
						}
					}
				}
			}

			// Store compiled settings for cache.
			GroovyMenuStyleStorage::getInstance()->set_global_settings( $this->settingsGlobal );

		}

	}

}
