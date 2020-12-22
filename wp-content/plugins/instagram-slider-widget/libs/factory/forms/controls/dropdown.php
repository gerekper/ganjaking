<?php
	
	/**
	 * Dropdown List Control
	 *
	 * Main options:
	 *  name            => a name of the control
	 *  value           => a value to show in the control
	 *  default         => a default value of the control if the "value" option is not specified
	 *  items           => a callback to return items or an array of items to select
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package factory-forms
	 * @since 1.0.0
	 */
	
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	if ( ! class_exists( 'Wbcr_FactoryForms436_DropdownControl' ) ) {
		
		class Wbcr_FactoryForms436_DropdownControl extends Wbcr_FactoryForms436_Control {
			
			public $type = 'dropdown';
			
			/**
			 * Returns a set of available items for the list.
			 *
			 * @since 1.0.0
			 * @return mixed[]
			 */
			private function getItems() {
				$data = $this->getOption( 'data', array() );
				
				// if the data options is a valid callback for an object method
				if ( ( is_array( $data ) && count( $data ) == 2 && is_object( $data[0] ) ) || is_string( $data ) ) {
					
					return call_user_func( $data );
				}
				
				// if the data options is an array of values
				return $data;
			}
			
			/**
			 * Returns true, if the data should be loaded via ajax.
			 *
			 * @since 1.0.0
			 * @return bool
			 */
			protected function isAjax() {
				
				$data = $this->getOption( 'data', array() );
				
				return is_array( $data ) && isset( $data['ajax'] );
			}
			
			/**
			 * Shows the html markup of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function html() {
				
				$way = $this->getOption( 'way', 'default' );
				$this->addHtmlData( 'way', $way );
				
				$events_data = $this->getOption( 'events', array() );
				
				if ( ! empty( $events_data ) ) {
					$events_string_data = json_encode( $events_data );
					$name_on_form       = $this->getNameOnForm();
					
					$value = $this->getValue();
					
					if ( empty( $value ) || ( is_array( $value ) && empty( $value[0] ) ) ) {
						$value = null;
					}
					
					if ( ! empty( $value ) && isset( $events_data[ $value ] ) && is_array( $events_data[ $value ] ) ) {
						$print_styles = '';
						foreach ( $events_data[ $value ] as $eventName => $selectors ) {
							if ( $eventName == 'hide' ) {
								$print_styles .= $selectors . '{display:none;}';
							} else if ( $eventName == 'show' ) {
								$print_styles .= $selectors . '{display:block;}';
							}
						}
						
						echo '<style>' . $print_styles . '</style>';
					}
					?>
                    <script>
						// Onepress factory dropdown control events
						if( void 0 === window.factory_dropdown_control_events_data ) {
							window.factory_dropdown_control_events_data = {};
						}
						window.factory_dropdown_control_events_data['<?php echo $name_on_form ?>'] = <?= $events_string_data ?>;
                    </script>
					<?php
				}
				if ( $this->isAjax() ) {
					
					$data    = $this->getOption( 'data', array() );
					$ajax_id = 'factory-dropdown-' . rand( 1000000, 9999999 );
					
					$value = $this->getValue();
					
					if ( empty( $value ) || ( is_array( $value ) && empty( $value[0] ) ) ) {
						$value = null;
					}
					
					?>
                    <div class="factory-ajax-loader <?php echo $ajax_id . '-loader'; ?>"></div>
                    <script>
						window['<?php echo $ajax_id ?>'] = {
							'loader': '.<?php echo $ajax_id . '-loader' ?>',
							'url': '<?php echo $data['url'] ?>',
							'data': <?php echo json_encode( $data['data'] ) ?>,
							'selected': '<?php echo $value ?>',
							'empty_list': '<?php echo $this->getOption( 'empty', __( 'The list is empty.', 'wbcr_factory_forms_436' ) ) ?>'
						};
                    </script>
					<?php
					
					$this->addHtmlData( 'ajax', true );
					$this->addHtmlData( 'ajax-data-id', $ajax_id );
					$this->addCssClass( 'factory-hidden' );
				}
				
				if ( 'buttons' == $way ) {
					$this->buttonsHtml();
				} elseif ( 'ddslick' == $way ) {
					$this->ddslickHtml();
				} else {
					$this->defaultHtml();
				}
			}
			
			/**
			 * Shows the Buttons Dropdown.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function buttonsHtml() {
				$items = $this->getItems();
				$value = $this->getValue();
				
				$name_on_form = $this->getNameOnForm();
				
				$this->addCssClass( 'factory-buttons-way' );
				
				?>
                <div <?php $this->attrs() ?>>
                    <div class="btn-group factory-buttons-group">
						<?php foreach ( $items as $item ) { ?>
                            <button type="button" class="btn btn-default btn-small factory-<?php echo $item[0] ?> <?php if ( $value == $item[0] ) {
								echo 'active';
							} ?>" data-value="<?php echo $item[0] ?>"><?php echo $item[1] ?></button>
						<?php } ?>
                        <input type="hidden" id="<?php echo $name_on_form ?>" class="factory-result" name="<?php echo $name_on_form ?>" value="<?php echo $value ?>"/>
                    </div>
                    <div class="factory-hints">
						<?php foreach ( $items as $item ) { ?>
							<?php if ( isset( $item[2] ) ) { ?>
                                <div class="factory-hint factory-hint-<?php echo $item[0] ?>" <?php if ( $value !== $item[0] ) {
									echo 'style="display: none;"';
								} ?>><?php echo $item[2] ?></div>
							<?php } ?>
						<?php } ?>
                    </div>
                </div>
				<?php
			}
			
			/**
			 * Shows the ddSlick dropbox.
			 *
			 * @since 3.2.8
			 * @return void
			 */
			protected function ddslickHtml() {
				$items = $this->getItems();
				$value = $this->getValue();
				
				$name_on_form = $this->getNameOnForm();
				
				$this->addCssClass( 'factory-ddslick-way' );
				$this->addHtmlData( 'name', $name_on_form );
				
				$this->addHtmlData( 'width', $this->getOption( 'width', 300 ) );
				$this->addHtmlData( 'align', $this->getOption( 'imagePosition', 'right' ) );
				
				?>
                <div <?php $this->attrs() ?>>
                    <script>
						//Dropdown plugin data
						var factory_<?php echo $name_on_form ?>_data = [
							<?php foreach ( $items as $item ) { ?>
							{
								text: "<?php echo $item['title'] ?>",
								value: "<?php echo $item['value'] ?>",
								selected: <?php if ( $value == $item['value'] ) {
									echo 'true';
								} else {
									echo 'false';
								} ?>,
								description: "<?php echo( isset( $item['hint'] ) ? $item['hint'] : '' ); ?>",
								imageSrc: "<?php echo( isset( $item['image'] ) ? $item['image'] : '' ); ?>",
								imageHoverSrc: "<?php echo( isset( $item['hover'] ) ? $item['hover'] : '' ); ?>"
							},
							<?php } ?>
						];
                    </script>
                    <div class="factory-ddslick"></div>
                    <input type="hidden" class="factory-result" id="<?php echo $name_on_form ?>" name="<?php echo $name_on_form ?>" value="<?php echo $value ?>"/>
                </div>
				<?php
			}
			
			/**
			 * Shows the standart dropdown.
			 *
			 * @since 1.3.1
			 * @return void
			 */
			protected function defaultHtml() {
				
				$items = $this->getItems();
				$value = esc_attr( $this->getValue() );
				
				$name_on_form = $this->getNameOnForm();
				
				$this->addHtmlAttr( 'id', $name_on_form );
				$this->addHtmlAttr( 'name', $name_on_form );
				$this->addCssClass( 'form-control' );
				
				$hasGroups = $this->getOption( 'hasGroups', true );
				$has_hints = $this->getOption( 'hasHints', false );
				
				foreach ( $items as $item ) {
					if ( isset( $item['type'] ) && $item['type'] == 'group' && ! empty( $item['items'] ) ) {
						foreach ( (array) $item['items'] as $group_item ) {
							$is_hint = ( isset( $group_item['hint'] ) && ! empty( $group_item['hint'] ) ) || ( isset( $group_item[2] ) && ! empty( $group_item[2] ) );
							if ( ! $is_hint ) {
								continue;
							}
							$has_hints = true;
							break;
						}
						if ( $has_hints ) {
							break;
						}
					} else {
						$is_hint = ( isset( $item['hint'] ) && ! empty( $item['hint'] ) ) || ( isset( $item[2] ) && ! $item[2] );
						if ( ! $is_hint ) {
							continue;
						}
						$has_hints = true;
						break;
					}
				}
				
				$is_empty   = $this->isAjax() || empty( $items );
				$empty_list = $this->getOption( 'empty', __( '- empty -', 'wbcr_factory_forms_436' ) );
				
				?>
                <select <?php $this->attrs() ?>>
					<?php if ( $is_empty ) { ?>
                        <option value='' class="factory-empty-option">
							<?php echo $empty_list ?>
                        </option>
					<?php } else { ?>
						<?php $this->printItems( $items, $value ) ?>
					<?php } ?>
                </select>
				<?php if ( $has_hints ) { ?>
                    <div class="factory-hints">
						<?php foreach ( $items as $item ) {
							if ( isset( $item['type'] ) && $item['type'] == 'group' && ! empty( $item['items'] ) ) {
								foreach ( (array) $item['items'] as $group_item ) {
									
									$hint = isset( $group_item[2] ) ? esc_attr( $group_item[2] ) : null;
									$hint = isset( $group_item['hint'] ) ? esc_attr( $group_item['hint'] ) : $hint;
									
									$value = isset( $group_item[0] ) ? esc_attr( $group_item[0] ) : null;
									$value = isset( $group_item['value'] ) ? esc_attr( $group_item['value'] ) : $value;
									
									$this->printHint( $hint, $value, $value !== $value );
								}
							} else {
								$hint = isset( $item[2] ) ? esc_attr( $item[2] ) : null;
								$hint = isset( $item['hint'] ) ? esc_attr( $item['hint'] ) : $hint;
								
								$value = isset( $item[0] ) ? esc_attr( $item[0] ) : null;
								$value = isset( $item['value'] ) ? esc_attr( $item['value'] ) : $value;
								
								$this->printHint( $hint, $value, $value !== $value );
							}
						} ?>
                    </div>
				<?php } ?>
				<?php
			}
			
			/**
			 * Print single hint markup
			 * @since 4.1.0
			 *
			 * @param string $hint
			 *
			 * @return void
			 */
			protected function printHint( $hint, $name, $is_visible = false ) {
				
				if ( ! empty( $hint ) ) {
					$styles = ( $is_visible ) ? 'style="display: none;"' : '';
					
					?>
                    <div style="display: none;" class="factory-hint factory-hint-<?= esc_attr( $name ) ?>"<?= $styles ?>><?php echo $hint ?></div>
					<?php
				}
			}
			
			/**
			 * @param array $items
			 * @param null $selected
			 */
			protected function printItems( $items, $selected = null ) {
				
				foreach ( (array) $items as $item ) {
					
					$subitems = array();
					$data     = null;
					
					// this item is an associative array
					if ( isset( $item['type'] ) || isset( $item['value'] ) ) {
						
						$type = isset( $item['type'] ) ? $item['type'] : 'option';
						
						if ( 'group' === $type ) {
							$subitems = isset( $item['items'] ) ? $item['items'] : array();
						}
						
						$value = isset( $item['value'] ) ? $item['value'] : '';
						$title = isset( $item['title'] ) ? $item['title'] : __( '- empty -', 'wbcr_factory_forms_436' );
						
						$data = isset( $item['data'] ) ? $item['data'] : null;
					} else {
						
						$type = ( count( $item ) == 3 && $item[0] === 'group' ) ? 'group' : 'option';
						if ( 'group' === $type ) {
							$subitems = $item[2];
						}
						
						$title = $item[1];
						$value = esc_attr( $item[0] );
					}
					
					if ( 'group' === $type ) {
						?>
                        <optgroup label="<?php echo $title ?>">
							<?php $this->printItems( $subitems, $selected ); ?>
                        </optgroup>
						<?php
					} else {
						
						$attr = ( $selected == $value ) ? 'selected="selected"' : '';
						
						$strData = '';
						if ( ! empty( $data ) ) {
							
							foreach ( $data as $key => $values ) {
								$strData = $strData . ' data-' . $key . '="' . ( is_array( $values ) ? implode( ',', $values ) : $values ) . '"';
							}
						}
						
						?>
                        <option value='<?php echo $value ?>' <?php echo $attr ?> <?php echo $strData ?>>
							<?php echo $title ?>
                        </option>
						<?php
					}
				}
			}
		}
	}