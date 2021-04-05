<?php
/**
 * Class to create a CSS Control.
 *
 * @package  Storefront_Powerpack
 * @author   Tiago Noronha
 * @since    1.0.0
 */
class SP_Designer_CSS_Control extends WP_Customize_Control {
	/**
	 * @access public
	 * @var string
	 */
	public $type = 'sp_designer_css';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @uses WP_Customize_Control::__construct()
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_templates' ) );
	}

	/**
	 * Enqueue our media manager resources, scripts, and styles.
	 *
	 * @since 1.0.0
	 * @uses wp_enqueue_media()
	 */
	public function enqueue() {
		// Enqueues all needed media resources.
		wp_enqueue_media();
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 1.0.0
	 */
	public function to_json() {
		parent::to_json();
		$this->json['id']  = $this->id;
		$this->json['css'] = $this->value();
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
	}

	/**
	 * Render a JS template for the content of the CSS control.
	 *
	 * @since 1.0.0
	 */
	public function content_template() {
		?>
			<# if ( data.label ) { #>
				<h3 class="sp-designer-selector-title">{{ data.label }}</h3>
				<ul class="sp-designer-selector-content">
					<li>
						<div class="sp-designer-editing">
							<strong><?php _e( 'Editing:', 'storefront-powerpack' ); ?></strong>
							{{ data.label }}
						</div>
					</li>
					<li>
						<label class="customize-control-title"><?php _e( 'Display', 'storefront-powerpack' ); ?></label>

						<div class="sp-designer-css-property sp-designer-update-display">
							<label class="sp-designer-sub-title"><?php _e( 'Show / Hide', 'storefront-powerpack' ); ?></label>

							<select data-sp-designer-property="updateDisplay">
								<?php foreach ( array( 'inline' => __( 'Show', 'storefront-powerpack' ), 'none' => __( 'Hide', 'storefront-powerpack' ) ) as $k => $v ) : ?>
									<option value="<?php echo esc_attr( $k ); ?>" <# if ( '<?php echo esc_attr( $k ); ?>' == data.css.updateDisplay ) { #> selected<# } #>><?php echo esc_attr( $v ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<hr style="margin: 1em 0; clear: both;">
					</li>
					<li>
						<label class="customize-control-title"><?php _e( 'Text', 'storefront-powerpack' ); ?></label>

						<div class="sp-designer-css-property sp-designer-font-style">
							<label class="sp-designer-sub-title"><?php _e( 'Font Style', 'storefront-powerpack' ); ?></label>

							<label for="italic_{{ data.id }}">
								<input name="italic_{{ data.id }}" id="italic_{{ data.id }}" class="sp-designer-radio-toggle" type="checkbox" value="italic" data-sp-designer-property="fontStyle" <# if ( 'italic' == data.css.fontStyle ) { #> checked<# } #> />
								<span class="sp-toggle dashicons dashicons-editor-italic"></span>
							</label>

							<label for="underline_{{ data.id }}">
								<input name="underline_{{ data.id }}" id="underline_{{ data.id }}" class="sp-designer-radio-toggle" type="checkbox" value="underline" data-sp-designer-property="textUnderline" <# if ( 'underline' == data.css.textUnderline ) { #> checked<# } #> />
								<span class="sp-toggle dashicons dashicons-editor-underline"></span>
							</label>

							<label for="strikethrough_{{ data.id }}">
								<input name="strikethrough_{{ data.id }}" id="strikethrough_{{ data.id }}" class="sp-designer-radio-toggle" type="checkbox" value="line-through" data-sp-designer-property="textLineThrough" <# if ( 'line-through' == data.css.textLineThrough ) { #> checked<# } #> />
								<span class="sp-toggle dashicons dashicons-editor-strikethrough"></span>
							</label>
						</div>

						<div class="sp-designer-css-property sp-designer-font-family">
							<label class="sp-designer-sub-title"><?php _e( 'Font Family', 'storefront-powerpack' ); ?></label>

							<select data-sp-designer-property="fontFamily" class="font-family-test">
								<?php foreach ( SP_Designer::customize_fonts() as $group ) : ?>
									<optgroup label="<?php esc_attr_e( $group['text'], 'storefront-powerpack' ); ?>">
										<?php foreach ( $group['fonts'] as $font ) : ?>
											<option value="<?php esc_attr_e( $font, 'storefront-powerpack' ); ?>" <# if ( '<?php esc_attr_e( $font, 'storefront-powerpack' ); ?>' == data.css.fontFamily ) { #> selected<# } #>><?php esc_attr_e( $font, 'storefront-powerpack' ); ?></option>
										<?php endforeach; ?>
									</optgroup>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-font-weight">
							<label class="sp-designer-sub-title"><?php _e( 'Font Weight', 'storefront-powerpack' ); ?></label>

							<select class="font-weight" data-sp-designer-property="fontWeight">
								<option value=""><?php _e( 'Default', 'storefront-powerpack' ); ?></option>
								<option value="100" <# if ( 100 == data.css.fontWeight ) { #> selected<# } #>>100 - <?php _e( 'Thin', 'storefront-powerpack' ); ?></option>
								<option value="200" <# if ( 200 == data.css.fontWeight ) { #> selected<# } #>>200 - <?php _e( 'Extra Light', 'storefront-powerpack' ); ?></option>
								<option value="300" <# if ( 300 == data.css.fontWeight ) { #> selected<# } #>>300 - <?php _e( 'Light', 'storefront-powerpack' ); ?></option>
								<option value="400" <# if ( 400 == data.css.fontWeight ) { #> selected<# } #>>400 - <?php _e( 'Normal', 'storefront-powerpack' ); ?></option>
								<option value="500" <# if ( 500 == data.css.fontWeight ) { #> selected<# } #>>500 - <?php _e( 'Medium', 'storefront-powerpack' ); ?></option>
								<option value="600" <# if ( 600 == data.css.fontWeight ) { #> selected<# } #>>600 - <?php _e( 'Semi Bold', 'storefront-powerpack' ); ?></option>
								<option value="700" <# if ( 700 == data.css.fontWeight ) { #> selected<# } #>>700 - <?php _e( 'Bold', 'storefront-powerpack' ); ?></option>
								<option value="800" <# if ( 800 == data.css.fontWeight ) { #> selected<# } #>>800 - <?php _e( 'Extra Bold', 'storefront-powerpack' ); ?></option>
								<option value="900" <# if ( 900 == data.css.fontWeight ) { #> selected<# } #>>900 - <?php _e( 'Ultra Bold', 'storefront-powerpack' ); ?></option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-font-variant sp-designer-one-half">
							<label class="sp-designer-sub-title"><?php _e( 'Font Variant', 'storefront-powerpack' ); ?></label>

							<select class="font-variant" data-sp-designer-property="fontVariant">
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-font-size sp-designer-measurement sp-designer-one-half sp-designer-one-half-last">
							<label class="sp-designer-sub-title"><?php _e( 'Font Size', 'storefront-powerpack' ); ?></label>

							<input type="number" class="sp-measurement-value" min="0" step="any" value="{{ parseFloat( data.css.fontSize ) }}" data-sp-designer-property="fontSize" />

							<select class="sp-measurement-unit" data-sp-designer-property="fontSizeUnit">
								<option value="px" <# if ( 'px' == data.css.fontSizeUnit ) { #> selected<# } #>>px</option>
								<option value="em" <# if ( 'em' == data.css.fontSizeUnit ) { #> selected<# } #>>em</option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-line-height sp-designer-one-half">
							<label class="sp-designer-sub-title"><?php _e( 'Line Height', 'storefront-powerpack' ); ?></label>
							<input type="number" min="0" step="any" value="{{ parseFloat( data.css.lineHeight ) }}" data-sp-designer-property="lineHeight" />
						</div>

						<div class="sp-designer-css-property sp-designer-letter-spacing sp-designer-measurement sp-designer-one-half sp-designer-one-half-last">
							<label class="sp-designer-sub-title"><?php _e( 'Letter Spacing', 'storefront-powerpack' ); ?></label>

							<input type="number" class="sp-measurement-value" step="any" value="{{ parseFloat( data.css.letterSpacing ) }}" data-sp-designer-property="letterSpacing" />

							<select class="sp-measurement-unit" data-sp-designer-property="letterSpacingUnit">
								<option value="px" <# if ( 'px' == data.css.letterSpacingUnit ) { #> selected<# } #>>px</option>
								<option value="em" <# if ( 'em' == data.css.letterSpacingUnit ) { #> selected<# } #>>em</option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-text-color sp-designer-color" style="clear: both;">
							<label class="sp-designer-sub-title"><?php _e( 'Text Color', 'storefront-powerpack' ); ?></label>

							<input type="text" value="{{ data.css.color }}" data-sp-designer-property="color" />
						</div>

						<hr style="margin: 1em 0; clear: both;">
					</li>

					<li>
						<label class="customize-control-title"><?php _e( 'Margin', 'storefront-powerpack' ); ?></label>

						<div class="sp-designer-css-property sp-designer-margin sp-designer-measurement sp-designer-one-half">
							<label class="sp-designer-sub-title"><?php _e( 'Top', 'storefront-powerpack' ); ?></label>

							<input type="number" class="sp-measurement-value" step="any" value="{{ parseFloat( data.css.marginTop ) }}" data-sp-designer-property="marginTop" />

							<select class="sp-measurement-unit" data-sp-designer-property="marginTopUnit">
								<option value="px" <# if ( 'px' == data.css.marginTopUnit ) { #> selected<# } #>>px</option>
								<option value="em" <# if ( 'em' == data.css.marginTopUnit ) { #> selected<# } #>>em</option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-margin sp-designer-measurement sp-designer-one-half sp-designer-one-half-last">
							<label class="sp-designer-sub-title"><?php _e( 'Bottom', 'storefront-powerpack' ); ?></label>

							<input type="number" class="sp-measurement-value" step="any" value="{{ parseFloat( data.css.marginBottom ) }}" data-sp-designer-property="marginBottom" />

							<select class="sp-measurement-unit" data-sp-designer-property="marginBottomUnit">
								<option value="px" <# if ( 'px' == data.css.marginBottomUnit ) { #> selected<# } #>>px</option>
								<option value="em" <# if ( 'em' == data.css.marginBottomUnit ) { #> selected<# } #>>em</option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-margin sp-designer-measurement sp-designer-one-half">
							<label class="sp-designer-sub-title"><?php _e( 'Left', 'storefront-powerpack' ); ?></label>

							<input type="number" class="sp-measurement-value" step="any" value="{{ parseFloat( data.css.marginLeft ) }}" data-sp-designer-property="marginLeft" />

							<select class="sp-measurement-unit" data-sp-designer-property="marginLeftUnit">
								<option value="px" <# if ( 'px' == data.css.marginLeftUnit ) { #> selected<# } #>>px</option>
								<option value="em" <# if ( 'em' == data.css.marginLeftUnit ) { #> selected<# } #>>em</option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-margin sp-designer-measurement sp-designer-one-half sp-designer-one-half-last">
							<label class="sp-designer-sub-title"><?php _e( 'Right', 'storefront-powerpack' ); ?></label>

							<input type="number" class="sp-measurement-value" step="any" value="{{ parseFloat( data.css.marginRight ) }}" data-sp-designer-property="marginRight" />

							<select class="sp-measurement-unit" data-sp-designer-property="marginRightUnit">
								<option value="px" <# if ( 'px' == data.css.marginRightUnit ) { #> selected<# } #>>px</option>
								<option value="em" <# if ( 'em' == data.css.marginRightUnit ) { #> selected<# } #>>em</option>
							</select>
						</div>

						<hr style="margin: 1em 0; clear: both;">
					</li>

					<li>
						<label class="customize-control-title"><?php _e( 'Padding', 'storefront-powerpack' ); ?></label>

						<div class="sp-designer-css-property sp-designer-padding sp-designer-measurement sp-designer-one-half">
							<label class="sp-designer-sub-title"><?php _e( 'Top', 'storefront-powerpack' ); ?></label>

							<input type="number" class="sp-measurement-value" min="0" step="any" value="{{ parseFloat( data.css.paddingTop ) }}" data-sp-designer-property="paddingTop" />

							<select class="sp-measurement-unit" data-sp-designer-property="paddingTopUnit">
								<option value="px" <# if ( 'px' == data.css.paddingTopUnit ) { #> selected<# } #>>px</option>
								<option value="em" <# if ( 'em' == data.css.paddingTopUnit ) { #> selected<# } #>>em</option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-padding sp-designer-measurement sp-designer-one-half sp-designer-one-half-last">
							<label class="sp-designer-sub-title"><?php _e( 'Bottom', 'storefront-powerpack' ); ?></label>

							<input type="number" class="sp-measurement-value" min="0" step="any" value="{{ parseFloat( data.css.paddingBottom ) }}" data-sp-designer-property="paddingBottom" />

							<select class="sp-measurement-unit" data-sp-designer-property="paddingBottomUnit">
								<option value="px" <# if ( 'px' == data.css.paddingBottomUnit ) { #> selected<# } #>>px</option>
								<option value="em" <# if ( 'em' == data.css.paddingBottomUnit ) { #> selected<# } #>>em</option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-padding sp-designer-measurement sp-designer-one-half">
							<label class="sp-designer-sub-title"><?php _e( 'Left', 'storefront-powerpack' ); ?></label>

							<input type="number" class="sp-measurement-value" min="0" step="any" value="{{ parseFloat( data.css.paddingLeft ) }}" data-sp-designer-property="paddingLeft" />

							<select class="sp-measurement-unit" data-sp-designer-property="paddingLeftUnit">
								<option value="px" <# if ( 'px' == data.css.paddingLeftUnit ) { #> selected<# } #>>px</option>
								<option value="em" <# if ( 'em' == data.css.paddingLeftUnit ) { #> selected<# } #>>em</option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-padding sp-designer-measurement sp-designer-one-half sp-designer-one-half-last">
							<label class="sp-designer-sub-title"><?php _e( 'Right', 'storefront-powerpack' ); ?></label>

							<input type="number" class="sp-measurement-value" min="0" step="any" value="{{ parseFloat( data.css.paddingRight ) }}" data-sp-designer-property="paddingRight" />

							<select class="sp-measurement-unit" data-sp-designer-property="paddingRightUnit">
								<option value="px" <# if ( 'px' == data.css.paddingRightUnit ) { #> selected<# } #>>px</option>
								<option value="em" <# if ( 'em' == data.css.paddingRightUnit ) { #> selected<# } #>>em</option>
							</select>
						</div>

						<hr style="margin: 1em 0; clear: both;">
					</li>

					<li>
						<label class="customize-control-title"><?php _e( 'Border', 'storefront-powerpack' ); ?></label>

						<div class="sp-designer-css-property sp-designer-border-style sp-designer-one-half">
							<label class="sp-designer-sub-title"><?php _e( 'Border Style', 'storefront-powerpack' ); ?></label>

							<select class="sp-border-style" data-sp-designer-property="borderStyle">
								<option value="none"><?php _e( 'None', 'storefront-powerpack' ); ?></option>
								<option value="dotted"><?php _e( 'Dotted', 'storefront-powerpack' ); ?></option>
								<option value="dashed"><?php _e( 'Dashed', 'storefront-powerpack' ); ?></option>
								<option value="double"><?php _e( 'Double', 'storefront-powerpack' ); ?></option>
								<option value="solid"><?php _e( 'Solid', 'storefront-powerpack' ); ?></option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-border-width sp-designer-measurement sp-designer-one-half sp-designer-one-half-last">
							<label class="sp-designer-sub-title"><?php _e( 'Border Width', 'storefront-powerpack' ); ?></label>

							<input type="number" class="sp-measurement-value" min="0" step="any" value="{{ parseFloat( data.css.borderWidth ) }}" data-sp-designer-property="borderWidth" />

							<select class="sp-measurement-unit" data-sp-designer-property="borderWidthUnit">
								<option value="px" <# if ( 'px' == data.css.BorderWidthUnit ) { #> selected<# } #>>px</option>
								<option value="em" <# if ( 'em' == data.css.BorderWidthUnit ) { #> selected<# } #>>em</option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-border-radius sp-designer-measurement sp-designer-one-half sp-designer-one-half-last" style="float: left; clear: both;">
							<label class="sp-designer-sub-title"><?php _e( 'Border Radius', 'storefront-powerpack' ); ?></label>

							<input type="number" class="sp-measurement-value" min="0" step="any" value="{{ parseFloat( data.css.borderRadius ) }}" data-sp-designer-property="borderRadius" />

							<select class="sp-measurement-unit" data-sp-designer-property="borderRadiusUnit">
								<option value="px" <# if ( 'px' == data.css.borderRadiusUnit ) { #> selected<# } #>>px</option>
								<option value="em" <# if ( 'em' == data.css.borderRadiusUnit ) { #> selected<# } #>>em</option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-border-color sp-designer-color" style="clear: both;">
							<label class="sp-designer-sub-title"><?php _e( 'Border Color', 'storefront-powerpack' ); ?></label>

							<input type="text" value="{{ data.css.borderColor }}" data-sp-designer-property="borderColor" />
						</div>

						<hr style="margin: 1em 0; clear: both;">
					</li>

					<li>
						<label class="customize-control-title"><?php _e( 'Background', 'storefront-powerpack' ); ?></label>

						<div class="sp-designer-css-property sp-designer-background-color sp-designer-color" style="clear: both;">
							<label class="sp-designer-sub-title"><?php _e( 'Background Color', 'storefront-powerpack' ); ?></label>

							<input type="text" value="{{ data.css.backgroundColor }}" data-sp-designer-property="backgroundColor" />
						</div>

						<div class="sp-designer-css-property sp-designer-background-image" style="clear: both;">
							<label class="sp-designer-sub-title"><?php _e( 'Background Image', 'storefront-powerpack' ); ?></label>

							<div class="current">
								<span class="placeholder"><?php _e( 'No background image set', 'storefront-powerpack' ); ?></span>
								<img class="sp-background-image" />
							</div>
							<div class="actions">
								<button type="button" class="button button-secondary new"><?php _e( 'Add Image', 'storefront-powerpack' ); ?></button>
								<button type="button" class="button button-secondary change"><?php _e( 'Change Image', 'storefront-powerpack' ); ?></button>
			 					<button type="button" class="button button-secondary remove"><?php _e( 'Remove Image', 'storefront-powerpack' ); ?></button>
							</div>
						</div>

						<div class="sp-designer-css-property sp-designer-background-repeat" style="clear: both;">
							<label class="sp-designer-sub-title"><?php _e( 'Background Repeat', 'storefront-powerpack' ); ?></label>

							<select data-sp-designer-property="backgroundRepeat">
								<option value="no-repeat" <# if ( 'no-repeat' == data.css.backgroundRepeat ) { #> selected<# } #>><?php _e( 'No repeat', 'storefront-powerpack' ); ?></option>
								<option value="repeat" <# if ( 'repeat' == data.css.backgroundRepeat ) { #> selected<# } #>><?php _e( 'Tile', 'storefront-powerpack' ); ?></option>
								<option value="repeat-x" <# if ( 'repeat-x' == data.css.backgroundRepeat ) { #> selected<# } #>><?php _e( 'Tile Horizontally', 'storefront-powerpack' ); ?></option>
								<option value="repeat-y" <# if ( 'repeat-y' == data.css.backgroundRepeat ) { #> selected<# } #>><?php _e( 'Tile Vertically', 'storefront-powerpack' ); ?></option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-background-position" style="clear: both;">
							<label class="sp-designer-sub-title"><?php _e( 'Background Position', 'storefront-powerpack' ); ?></label>

							<select data-sp-designer-property="backgroundPosition">
								<option value="left" <# if ( 'left' == data.css.backgroundPosition ) { #> selected<# } #>><?php _e( 'Left', 'storefront-powerpack' ); ?></option>
								<option value="center" <# if ( 'center' == data.css.backgroundPosition ) { #> selected<# } #>><?php _e( 'Center', 'storefront-powerpack' ); ?></option>
								<option value="right" <# if ( 'right' == data.css.backgroundPosition ) { #> selected<# } #>><?php _e( 'Right', 'storefront-powerpack' ); ?></option>
							</select>
						</div>

						<div class="sp-designer-css-property sp-designer-background-attachment" style="clear: both;">
							<label class="sp-designer-sub-title"><?php _e( 'Background Attachment', 'storefront-powerpack' ); ?></label>

							<select data-sp-designer-property="backgroundAttachment">
								<option value="scroll" <# if ( 'scroll' == data.css.backgroundAttachment ) { #> selected<# } #>><?php _e( 'Scroll', 'storefront-powerpack' ); ?></option>
								<option value="fixed" <# if ( 'fixed' == data.css.backgroundAttachment ) { #> selected<# } #>><?php _e( 'Fixed', 'storefront-powerpack' ); ?></option>
							</select>
						</div>

						<hr style="margin: 1em 0; clear: both;">

						<div class="sp-designer-css-property-actions submitbox">
							<button type="button" class="button-link item-delete submitdelete deletion"><?php echo sprintf( __( 'Reset %s styles', 'storefront-powerpack' ), '&ldquo;{{ data.label }}&rdquo;' ); ?></button>
						</div>
					</li>
				</ul>
			<# } #>
		<?php
	}

	/**
	 * Print the JavaScript templates used to render Menu Customizer components.
	 *
	 * Templates are imported into the JS use wp.template.
	 *
	 * @since 4.3.0
	 * @access public
	 */
	public function print_templates() {
		?>
		<script type="text/html" id="tmpl-sp-designer-selector-title">
			<li>
				<div class="customize-section-title">
					<button class="customize-section-back" tabindex="0">
						<span class="screen-reader-text"><?php _e( 'Back', 'storefront-powerpack' ); ?></span>
					</button>
					<h3>
						<span class="customize-action">
							{{{ data.action }}}
						</span>
						{{ data.title }}
					</h3>
				</div>
			</li>
		</script>
		<?php
	}
}