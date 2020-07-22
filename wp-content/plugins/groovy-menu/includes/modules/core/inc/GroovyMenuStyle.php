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

		protected $lver = false;

		const OPTION_NAME = 'groovy_menu_settings';

		/**
		 * Return option name
		 *
		 * @param GroovyMenuPreset $preset for id of preset.
		 *
		 * @return string
		 */
		public static function getPresetPostId( GroovyMenuPreset $preset ) {
			return $preset->getId();
		}

		/**
		 * GroovyMenuStyle constructor.
		 *
		 * @param null $presetId if not null construct for specific preset.
		 */
		public function __construct( $presetId = null ) {

			if ( empty( $this->optionsGlobal ) ) {
				// Try to restore loaded config from cache.
				$cache_config = \GroovyMenu\StyleStorage::getInstance()->get_global_config();
				if ( ! empty( $cache_config ) ) {
					$this->optionsGlobal = $cache_config;
				} else {
					$this->optionsGlobal = include GROOVY_MENU_DIR . 'includes/config/ConfigGlobal.php';
					\GroovyMenu\StyleStorage::getInstance()->set_global_config( $this->optionsGlobal );
				}
			}

			$this->options = include GROOVY_MENU_DIR . 'includes/config/Config.php';
			\GroovyMenu\StyleStorage::getInstance()->set_preset_config( $this->options );

			$preset = GroovyMenuPreset::getById( $presetId );

			if ( is_null( $presetId ) || empty( $presetId ) || ! $preset ) {
				$preset = GroovyMenuPreset::getCurrentPreset();
			} else {
				$preset = new GroovyMenuPreset( $presetId );
			}

			$this->setPreset( $preset );
			$this->presetScreenshot = $preset::getPreviewById( $preset->getId() );
			$this->loadPresetSettings();
			$this->loadGlobalSettings();

			if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
				$this->lver = true;
			}
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
		 * @return array
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


		protected function loadPresetSettings() {

			// If the parameter is compiled earlier, it will be restored.
			if ( \GroovyMenu\StyleStorage::getInstance()->get_preset_settings( self::getPresetPostId( $this->preset ) ) ) {
				$this->settings = \GroovyMenu\StyleStorage::getInstance()->get_preset_settings( self::getPresetPostId( $this->preset ) );

				return;
			}

			$this->settings   = $this->options;
			$settings         = $this->loadPresetOptionsFromPost( self::getPresetPostId( $this->preset ) );
			$compiled_css_opt = $this->loadPresetCssFromPost( self::getPresetPostId( $this->preset ) );

			if ( ! empty( $settings ) ) {

				$exclude_types   = array( 'inlineStart', 'inlineEnd' );
				$can_empty_types = array( 'number', 'slider', 'colorpicker', 'text', 'textarea' );

				foreach ( $this->settings as $categoryName => $category ) {
					if ( isset( $category['fields'] ) ) {
						foreach ( $category['fields'] as $field => $value ) {
							if ( isset( $this->options[ $categoryName ]['fields'][ $field ] ) ) {

								$saved_value = isset( $settings[ $field ] ) ? $settings[ $field ] : null;
								if ( isset( $value['type'] ) && 'checkbox' === $value['type'] ) {
									if ( 'false' === $saved_value || '0' === $saved_value ) {
										$saved_value = '';
									}
									$saved_value = empty( $saved_value ) ? false : true;
								} elseif ( isset( $value['type'] ) && 'number' === $value['type'] ) {
									$saved_value = intval( $saved_value );
								}

								if ( isset( $value['type'] ) && 'header' === $value['type'] ) {
									if ( is_string( $saved_value ) ) {
										$_val = json_decode( stripslashes( $saved_value ), true );
										if ( is_array( $_val ) ) {
											$saved_value = $_val;
										}
									}

									// bugfix.
									if ( ! empty( $saved_value['style'] ) && in_array( $saved_value['style'], [ 3, 4, 5 ], true ) ) {
										if ( ! empty( $saved_value['align'] ) && 'center' === $saved_value['align'] ) {
											$saved_value['align'] = 'left';
										}
									}
								}

								if ( isset( $value['type'] ) && in_array( $value['type'], $exclude_types, true ) ) {
									$value = '';
								} elseif ( is_array( $saved_value ) ) {
									$value = $saved_value;
								} elseif ( empty( $saved_value ) && in_array( $value['type'], $can_empty_types, true ) ) {
									$value = $saved_value;
								} elseif ( ! is_bool( $saved_value ) && empty( $saved_value ) && isset( $value['default'] ) ) {
									$value = $value['default'];
								} elseif ( ! empty( $saved_value ) || is_bool( $saved_value ) ) {
									$value = $saved_value;
								} else {
									$value = '';
								}

								$this->set( $field, $value );

							}
						}
					} else {
						foreach ( $category as $field => $value ) {
							if ( isset( $this->options[ $categoryName ]['fields'][ $field ] ) ) {

								$saved_value = isset( $settings[ $field ] ) ? $settings[ $field ] : null;
								if ( isset( $value['type'] ) && 'checkbox' === $value['type'] ) {
									if ( 'false' === $saved_value || '0' === $saved_value ) {
										$saved_value = '';
									}
									$saved_value = empty( $saved_value ) ? false : true;
								} elseif ( isset( $value['type'] ) && 'number' === $value['type'] ) {
									$saved_value = intval( $saved_value );
								}

								if ( isset( $value['type'] ) && 'header' === $value['type'] ) {
									if ( is_string( $saved_value ) ) {
										$_val = json_decode( stripslashes( $saved_value ), true );
										if ( is_array( $_val ) ) {
											$saved_value = $_val;
										}
									}
								}

								if ( is_array( $saved_value ) ) {
									$value = $saved_value;
								} elseif ( empty( $saved_value ) && in_array( $value['type'], $can_empty_types, true ) ) {
									$value = $saved_value;
								} elseif ( ! is_bool( $saved_value ) && empty( $saved_value ) && isset( $value['default'] ) ) {
									$value = $value['default'];
								} elseif ( ! empty( $saved_value ) || is_bool( $saved_value ) ) {
									$value = $saved_value;
								} else {
									$value = '';
								}

								$this->set( $field, $value );
							}
						}
					}
				}
			} else {
				// set all values from default sub field.
				foreach ( $this->options as $categoryName => $category ) {
					foreach ( $category['fields'] as $field => $parameters ) {
						if ( empty( $parameters['value'] ) && isset( $parameters['default'] ) ) {
							$this->set( $field, $parameters['default'] );
						}
					}
				}
			}

			// update meta compiled_css.
			foreach ( $compiled_css_opt as $index => $item ) {
				$this->settings['general']['fields'][ $index ]['value'] = $item;
			}

			// Store compiled settings for cache.
			\GroovyMenu\StyleStorage::getInstance()->set_preset_settings( $this->preset->getId(), $this->settings );

		}

		public function loadGlobalSettings() {

			// If the parameter is compiled earlier, it will be restored.
			if ( \GroovyMenu\StyleStorage::getInstance()->get_global_settings() ) {
				$this->settingsGlobal = \GroovyMenu\StyleStorage::getInstance()->get_global_settings();

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
			\GroovyMenu\StyleStorage::getInstance()->set_global_settings( $this->settingsGlobal );

		}


		/**
		 * Serialize config with values for front-end
		 *
		 * @param bool $get_all     if need ignore serialize sub-param.
		 * @param bool $camelize    if need camelize keys name.
		 * @param bool $get_global  if need append global setting.
		 * @param bool $get_storage if need use cache storage.
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
			if ( $get_storage && \GroovyMenu\StyleStorage::getInstance()->get_preset_settings_serialized( $this->preset->getId(), $get_all, $camelize, $get_global ) ) {
				return \GroovyMenu\StyleStorage::getInstance()->get_preset_settings_serialized( $this->preset->getId(), $get_all, $camelize, $get_global );
			}

			if ( $get_global ) {
				// merge preset setting and global settings.
				$all_settings = array_merge( $this->getSettings(), $this->getSettingsGlobal() );
			} else {
				$all_settings = $this->getSettings();
			}

			$exclude_types = array( 'group', 'inlineStart', 'inlineEnd' );

			foreach ( $all_settings as $categoryName => $group ) {
				foreach ( $group['fields'] as $name => $field ) {
					if ( ! $get_all && isset( $field['serialize'] ) && ! $field['serialize'] ) {
						continue;
					}
					if ( $get_all && isset( $field['type'] ) && 'group' === $field['type'] ) {
						continue;
					}
					if ( $camelize && isset( $field['type'] ) && in_array( $field['type'], $exclude_types, true ) ) {
						continue;
					}


					$value = $this->getField( $categoryName, $name )->getValue();


					if ( $get_all && isset( $field['type'] ) && ( 'textarea' === $field['type'] || 'text' === $field['type'] ) ) {
						$value = $this->escapeJsonString( $value );
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

			\GroovyMenu\StyleStorage::getInstance()->set_preset_settings_serialized( $this->preset->getId(), $settings, $get_all, $camelize, $get_global );

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

			$compiled_css = $this->get( 'general', 'compiled_css' . ( is_rtl() ? '_rtl' : '' ) );

			if ( empty( $compiled_css ) ) {
				$classes_navbar[] = 'gm-no-compiled-css';
			}

			$custom_css_class = $this->getCustomHtmlClass();
			if ( ! empty( $custom_css_class ) ) {
				$classes_navbar[] = $custom_css_class;
			}

			$header_style = 1;

			// Header types.
			if ( ! empty( $settings['header'] ) ) {

				$header_style = empty( $settings['header']['style'] ) ? 1 : intval( $settings['header']['style'] );

				if ( ! empty( $settings['header']['align'] ) ) {
					$classes_navbar[] = 'gm-navbar--align-' . $settings['header']['align'];
				}

				if ( ! empty( $settings['header']['style'] ) && ! empty( $settings['header']['align'] ) ) {
					$classes_navbar[] = 'gm-navbar--style-' . $header_style;

					if ( $header_style === 1 && $settings['header']['align'] !== 'center' ) {
						$classes_navbar[] = 'gm-top-links-align-' . $settings['topLvlLinkAlign'];
					}

				}

				if ( $settings['header']['toolbar'] ) {
					$classes_navbar[] = 'gm-navbar--toolbar-' . $settings['header']['toolbar'];
				}

				if ( 1 === $header_style ) {
					if ( isset( $settings['showDividerBetweenMenuLinks'] ) && $settings['showDividerBetweenMenuLinks'] ) {
						$classes_navbar[] = 'gm-navbar--has-divider';
					}
				}

			}

			if ( isset( $settings['shadow'] ) && $settings['shadow'] && ! in_array( $header_style, array( 3, 5 ), true ) ) {
				$classes_navbar[] = 'gm-navbar--has-shadow';
			}

			if ( isset( $settings['shadowSticky'] ) && $settings['shadowSticky'] && ! in_array( $header_style, array( 3, 5 ), true ) ) {
				$classes_navbar[] = 'gm-navbar--has-shadow-sticky';
			}

			if ( isset( $settings['shadowDropdown'] ) && $settings['shadowDropdown'] && ! in_array( $header_style, array( 3, 5 ), true ) ) {
				$classes_navbar[] = 'gm-navbar--has-shadow-dropdown';
			}

			if ( isset( $settings['caret'] ) && ! $settings['caret'] ) {
				$classes_navbar[] = 'gm-navbar--hide-gm-caret';
			}

			// Top level hover Style.
			if ( isset( $settings['dropdownHoverStyle'] ) && $settings['dropdownHoverStyle'] ) {
				$classes_navbar[] = 'gm-dropdown-hover-style-' . $settings['dropdownHoverStyle'];
			}

			// Dropdown appearance style.
			if ( isset( $settings['dropdownAppearanceStyle'] ) && $settings['dropdownAppearanceStyle'] ) {
				$classes_navbar[] = 'gm-dropdown-appearance-' . $settings['dropdownAppearanceStyle'];
			}

			// Use dark style logo in sidebar menu.
			if ( isset( $settings['sidebarMenuUseDarkStyleLogo'] ) && $settings['sidebarMenuUseDarkStyleLogo'] ) {
				$classes_navbar[] = 'gm-navbar--style-3__dark';
			}

			// Hide for mobile view.
			if ( isset( $settings['mobileNavMenu'] ) && 'none' === $settings['mobileNavMenu'] ) {
				$classes_navbar[] = 'gm-hide-on-mobile';
			}

			// Scrollbar.
			if ( isset( $settings['scrollbarEnable'] ) && $settings['scrollbarEnable'] ) {
				$classes_navbar[] = 'gm-dropdown-with-scrollbar';
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

					$exclude_types = array( 'group', 'inlineStart', 'inlineEnd' );

					if ( isset( $field['type'] ) && in_array( $field['type'], $exclude_types, true ) ) {
						continue;
					}

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


		/**
		 * @param bool $withFiles
		 *
		 * @return array
		 */
		public function getSettingsArrayCategorized( $withFiles = false ) {
			$settings = array();
			foreach ( $this->getSettings() as $categoryName => $group ) {
				$settings[ $categoryName ] = array();
				foreach ( $group['fields'] as $name => $field ) {
					$settings[ $categoryName ][ $name ] = $this->getField( $categoryName, $name )->getValue();
					if ( $withFiles && $field['type'] === 'media' ) {
						$attachmentId = $settings[ $categoryName ][ $name ];

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

							$settings[ $categoryName ][ $name ] = array(
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

			return null;
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

			return null;
		}

		/**
		 * @param $category
		 * @param $name
		 *
		 * @return \GroovyMenu\FieldField
		 */
		public function getField( $category, $name ) {
			if ( isset( $this->settings[ $category ]['fields'][ $name ] ) ) {
				$field = $this->settings[ $category ]['fields'][ $name ];
				$subClass = isset( $field['type'] ) ? ucfirst( $field['type'] ) : '';
				$class = '\GroovyMenu\Field' . $subClass;

				if ( class_exists( $class ) ) {
					return new $class( $category, $name, $field );
				}
			}
			if ( isset( $this->settingsGlobal[ $category ]['fields'][ $name ] ) ) {
				$field = $this->settingsGlobal[ $category ]['fields'][ $name ];
				$subClass = isset( $field['type'] ) ? ucfirst( $field['type'] ) : '';
				$class = '\GroovyMenu\Field' . $subClass;

				if ( class_exists( $class ) ) {
					return new $class( $category, $name, $field );
				}
			}

			return null;
		}

		/**
		 * @param null|array $data
		 */
		public function update( $data = null ) {

			if ( empty( $data ) ) {
				$data = $this->getSettingsArrayCategorized( true );
			}

			$this->settings = $this->options;

			if ( ! empty( $data ) && is_array( $data ) ) {

				$exclude_types   = array( 'inlineStart', 'inlineEnd' );
				$can_empty_types = array( 'number', 'slider', 'colorpicker', 'text', 'textarea', 'media' );

				foreach ( $data as $categoryName => $category ) {
					if ( isset( $category['fields'] ) ) {
						foreach ( $category['fields'] as $field => $value ) {
							if ( isset( $this->options[ $categoryName ]['fields'][ $field ] ) ) {

								$field_opt = $this->options[ $categoryName ]['fields'][ $field ];
								$new_value = isset( $data[ $categoryName ]['fields'][ $field ]['value'] ) ? $data[ $categoryName ]['fields'][ $field ]['value'] : null;
								if ( isset( $field_opt['type'] ) && 'checkbox' === $field_opt['type'] ) {
									if ( 'false' === $new_value || '0' === $new_value ) {
										$new_value = '';
									}
									$new_value = empty( $new_value ) ? false : true;
								}

								if ( isset( $field_opt['type'] ) && 'header' === $field_opt['type'] ) {
									if ( is_string( $new_value ) ) {
										$_val = json_decode( stripslashes( $new_value ), true );
										if ( is_array( $_val ) ) {
											$new_value = $_val;
										}
									}
								}

								if ( isset( $field_opt['type'] ) && in_array( $field_opt['type'], $exclude_types, true ) ) {
									$value = '';
								} elseif ( is_array( $new_value ) ) {
									$value = $new_value;
								} elseif ( empty( $new_value ) && in_array( $field_opt['type'], $can_empty_types, true ) ) {
									$value = $new_value;
								} elseif ( ! is_bool( $new_value ) && empty( $new_value ) && isset( $field_opt['default'] ) ) {
									$value = $field_opt['default'];
								} elseif ( ! empty( $new_value ) || is_bool( $new_value ) ) {
									$value = $new_value;
								} else {
									$value = '';
								}

								$this->set( $field, $value );
							}
						}
					} else {
						foreach ( $category as $field => $value ) {
							if ( isset( $this->options[ $categoryName ]['fields'][ $field ] ) ) {

								$field_opt = $this->options[ $categoryName ]['fields'][ $field ];
								$new_value = isset( $data[ $categoryName ][ $field ] ) ? $data[ $categoryName ][ $field ] : null;

								if ( isset( $field_opt['type'] ) && 'checkbox' === $field_opt['type'] ) {
									if ( 'false' === $new_value || '0' === $new_value ) {
										$new_value = '';
									}
									$new_value = empty( $new_value ) ? false : true;
								}

								if ( isset( $field_opt['type'] ) && 'header' === $field_opt['type'] ) {
									if ( is_string( $new_value ) ) {
										$_val = json_decode( stripslashes( $new_value ), true );
										if ( is_array( $_val ) ) {
											$new_value = $_val;
										}
									}
								}

								if ( is_array( $new_value ) ) {
									$value = $new_value;
								} elseif ( empty( $new_value ) && in_array( $field_opt['type'], $can_empty_types, true ) ) {
									$value = $new_value;
								} elseif ( ! is_bool( $new_value ) && empty( $new_value ) && isset( $field_opt['default'] ) ) {
									$value = $field_opt['default'];
								} elseif ( ! empty( $new_value ) || is_bool( $new_value ) ) {
									$value = $new_value;
								} else {
									$value = '';
								}

								$this->set( $field, $value );
							}
						}
					}
				}
			} else {
				// set all values from default sub field.
				foreach ( $this->options as $categoryName => $category ) {
					foreach ( $category['fields'] as $field => $parameters ) {
						if ( empty( $parameters['value'] ) && isset( $parameters['default'] ) ) {
							$this->set( $field, $parameters['default'] );
						}
					}
				}
			}

			$preset_settings  = $this->serialize( true, false, false, false );
			$compiled_css     = '';
			$compiled_css_rtl = '';

			unset( $preset_settings['compiled_css'] );
			unset( $preset_settings['compiled_css_rtl'] );

			$preset_settings = wp_json_encode( $preset_settings );
			$preset_key      = md5( rand() . uniqid() . time() );

			update_post_meta( self::getPresetPostId( $this->preset ), 'gm_compiled_css', $compiled_css );
			update_post_meta( self::getPresetPostId( $this->preset ), 'gm_compiled_css_rtl', $compiled_css_rtl );
			update_post_meta( self::getPresetPostId( $this->preset ), 'gm_preset_settings', $preset_settings );
			update_post_meta( self::getPresetPostId( $this->preset ), 'gm_preset_key', $preset_key );
			//update_post_meta( self::getPresetPostId( $this->preset ), 'gm_version', GROOVY_MENU_VERSION );
			//update_post_meta( self::getPresetPostId( $this->preset ), 'gm_version_rtl', GROOVY_MENU_VERSION );
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

		protected function loadPresetOptionsFromPost( $post_id ) {

			$options = array();

			$post_id = intval( $post_id );

			if ( empty( $post_id ) ) {
				return $options;
			}

			$options = get_post_meta( $post_id, 'gm_preset_settings', true );
			$options = json_decode( $options, true );

			if ( empty( $options ) || ! is_array( $options ) ) {
				$options = array();
			}

			// fill missing fields with default values.
			$options = $this->setEmptyOptionsAsDefault( $options );

			if ( empty( $options['menu_z_index'] ) ) {
				$options['menu_z_index'] = '9999';
			}

			if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
				$this->lver = true;
			}

			if ( $this->lver ) {
				if ( isset( $options['header']['style'] ) &&
				     in_array( $options['header']['style'], [ 3, 4 ], true )
				) {
					$options['header']['style'] = 1;
				}

				if ( isset( $options['hover_style'] ) &&
				     in_array( $options['hover_style'], [ '3', '4', '5', '6', '7' ], true )
				) {
					$options['hover_style'] = '1';
				}

			}

			return $options;
		}

		/**
		 * @param array $options
		 *
		 * @return array
		 */
		protected function setEmptyOptionsAsDefault( $options ) {

			if ( empty( $options ) || ! is_array( $options ) ) {
				$options = array();
			}

			$settings         = array();
			$settings_default = array();
			$ignore_fields    = array(
				'group',
				'inlineStart',
				'inlineEnd',
			);

			foreach ( $this->getSettings() as $categoryName => $group ) {
				foreach ( $group['fields'] as $name => $fields ) {
					if ( ! empty( $fields['type'] ) && in_array( $fields['type'], $ignore_fields, true ) ) {
						continue;
					}

					if ( ! isset( $options[ $name ] ) && isset( $fields['default'] ) ) {
						$options[ $name ] = $fields['default'];
					}
				}
			}

			return $options;
		}

		protected function loadPresetCssFromPost( $post_id ) {

			$options = array(
				'version'          => '',
				'version_rtl'      => '',
				'preset_key'       => '',
				'compiled_css'     => '',
				'compiled_css_rtl' => '',
			);

			$post_id = intval( $post_id );

			if ( empty( $post_id ) ) {
				return $options;
			}

			$options = array(
				'version'          => get_post_meta( $post_id, 'gm_version', true ),
				'version_rtl'      => get_post_meta( $post_id, 'gm_version_rtl', true ),
				'preset_key'       => get_post_meta( $post_id, 'gm_preset_key', true ),
				'compiled_css'     => get_post_meta( $post_id, 'gm_compiled_css', true ),
				'compiled_css_rtl' => get_post_meta( $post_id, 'gm_compiled_css_rtl', true ),
			);

			if ( empty( $options['compiled_css'] ) || ! is_string( $options['compiled_css'] ) ) {
				$options['compiled_css'] = '';
			}
			if ( empty( $options['compiled_css_rtl'] ) || ! is_string( $options['compiled_css_rtl'] ) ) {
				$options['compiled_css_rtl'] = '';
			}

			return $options;
		}

	}

}
