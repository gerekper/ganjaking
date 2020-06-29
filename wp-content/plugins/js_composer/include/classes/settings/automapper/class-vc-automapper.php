<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Vc_Automapper' ) ) {
	/**
	 * Automated shortcode mapping
	 *
	 * Automapper adds settings tab for VC settings tabs with ability to map custom shortcodes to VC editors,
	 * if shortcode is not mapped by default or developers haven't done this yet.
	 * No more shortcode copy/paste. Add any third party shortcode to the list of VC menu elements for reuse.
	 * Edit params, values and description.
	 *
	 * @since 4.1
	 */
	class Vc_Automapper {
		/**
		 * @var bool
		 */
		protected static $disabled = false;
		protected $title;

		/**
		 *
		 */
		public function __construct() {
			$this->title = esc_attr__( 'Shortcode Mapper', 'js_composer' );
		}

		/**
		 *
		 */
		public function addAjaxActions() {
			add_action( 'wp_ajax_vc_automapper_create', array(
				$this,
				'create',
			) );
			add_action( 'wp_ajax_vc_automapper_read', array(
				$this,
				'read',
			) );
			add_action( 'wp_ajax_vc_automapper_update', array(
				$this,
				'update',
			) );
			add_action( 'wp_ajax_vc_automapper_delete', array(
				$this,
				'delete',
			) );

			return $this;
		}

		/**
		 * Builds html for Automapper CRUD like administration block
		 *
		 * @return bool
		 */
		public function renderHtml() {
			if ( $this->disabled() ) {
				return false;
			}
			?>
			<div class="tab_intro">
				<p><?php esc_html_e( 'WPBakery Page Builder Shortcode Mapper adds custom 3rd party vendors shortcodes to the list of WPBakery Page Builder content elements menu (Note: to map shortcode it needs to be installed on site).', 'js_composer' ); ?></p>
			</div>
			<div class="vc_automapper-toolbar">
				<a href=javascript:;" class="button button-primary"
						id="vc_automapper-add-btn"><?php esc_html_e( 'Map Shortcode', 'js_composer' ); ?></a>
			</div>
			<ul class="vc_automapper-list">
			</ul>
			<?php $this->renderTemplates(); ?>
			<?php
			return true;
		}

		/**
		 * @param $shortcode
		 */
		public function renderListItem( $shortcode ) {
			echo sprintf( '<li class="vc_automapper-item" data-item-id=""><label>%s</label><span class="vc_automapper-item-controls"><a href="javascript:;" class="vc_automapper-edit-btn" data-id="%s" data-tag="%s"></a><a href="javascript:;" class="vc_automapper-delete-btn" data-id="%s" data-tag="%s"></a></span></li>', esc_html( $shortcode->name ), esc_attr( $shortcode->id ), esc_attr( $shortcode->tag ), esc_attr( $shortcode->id ), esc_attr( $shortcode->tag ) );
		}

		/**
		 *
		 */
		public function renderMapFormTpl() {
			$custom_tag = 'script'; // Maybe use html shadow dom or ajax response for templates
			?>
			<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_automapper-add-form-tpl">
				<label for="vc_atm-shortcode-string"
						class="vc_info"><?php esc_html_e( 'Shortcode string', 'js_composer' ); ?></label>

				<div class="vc_wrapper">
					<div class="vc_string">
						<input id="vc_atm-shortcode-string"
								placeholder="<?php esc_attr_e( 'Please enter valid shortcode', 'js_composer' ); ?>"
								type="text" class="vc_atm-string">
					</div>
					<div class="vc_buttons">
						<a href="#" id="vc_atm-parse-string"
								class="button button-primary vc_parse-btn"><?php esc_attr_e( 'Parse Shortcode', 'js_composer' ); ?></a>
						<a href="#" class="button vc_atm-cancel"><?php esc_attr_e( 'Cancel', 'js_composer' ); ?></a>
					</div>
				</div>
				<span
						class="description"><?php esc_html_e( 'Enter valid shortcode (Example: [my_shortcode first_param="first_param_value"]My shortcode content[/my_shortcode]).', 'js_composer' ); ?></span>
			</<?php echo esc_attr( $custom_tag ); ?>>
			<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_automapper-item-complex-tpl">
				<div class="widget-top">
					<div class="widget-title-action">
						<button type="button" class="widget-action hide-if-no-js" aria-expanded="true">
							<span class="screen-reader-text"><?php esc_html_e( 'Edit widget: Search', 'js_composer' ); ?></span>
							<span class="toggle-indicator" aria-hidden="true"></span>
						</button>
					</div>
					<div class="widget-title"><h4>{{ name }}<span class="in-widget-title"></span></h4></div>
				</div>
				<div class="widget-inside">
				</div>
			</<?php echo esc_attr( $custom_tag ); ?>>
			<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_automapper-form-tpl">
				<input type="hidden" name="name" id="vc_atm-name" value="{{ name }}">

				<div class="vc_shortcode-preview" id="vc_shortcode-preview">
					{{{ shortcode_preview }}}
				</div>
				<div class="vc_line"></div>
				<div class="vc_wrapper">
					<h4 class="vc_h"><?php esc_html_e( 'General Information', 'js_composer' ); ?></h4>

					<div class="vc_field vc_tag">
						<label for="vc_atm-tag"><?php esc_html_e( 'Tag:', 'js_composer' ); ?></label>
						<input type="text" name="tag" id="vc_atm-tag" value="{{ tag }}">
					</div>
					<div class="vc_field vc_description">
						<label for="vc_atm-description"><?php esc_html_e( 'Description:', 'js_composer' ); ?></label>
						<textarea name="description" id="vc_atm-description">{{ description }}</textarea>
					</div>
					<div class="vc_field vc_category">
						<label for="vc_atm-category"><?php esc_html_e( 'Category:', 'js_composer' ); ?></label>
						<input type="text" name="category" id="vc_atm-category" value="{{ category }}">
						<span
								class="description"><?php esc_html_e( 'Comma separated categories names', 'js_composer' ); ?></span>
					</div>
					<div class="vc_field vc_is-container">
						<label for="vc_atm-is-container"><input type="checkbox" name="is_container"
									id="vc_atm-is-container"
									value=""> <?php esc_html_e( 'Include content param into shortcode', 'js_composer' ); ?>
						</label>
					</div>
				</div>
				<div class="vc_line"></div>
				<div class="vc_wrapper">
					<h4 class="vc_h"><?php esc_html_e( 'Shortcode Parameters', 'js_composer' ); ?></h4>
					<a href="#" id="vc_atm-add-param"
							class="button vc_add-param">+ <?php esc_html_e( 'Add Param', 'js_composer' ); ?></a>

					<div class="vc_params" id="vc_atm-params-list"></div>
				</div>
				<div class="vc_buttons">
					<a href="#" id="vc_atm-save"
							class="button button-primary"><?php esc_html_e( 'Save Changes', 'js_composer' ); ?></a>
					<a href="#" class="button vc_atm-cancel"><?php esc_html_e( 'Cancel', 'js_composer' ); ?></a>
					<a href="#" class="button vc_atm-delete"><?php esc_html_e( 'Delete', 'js_composer' ); ?></a>
				</div>
			</<?php echo esc_attr( $custom_tag ); ?>>
			<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_atm-form-param-tpl">
				<div class="vc_controls vc_controls-row vc_clearfix"><a
							class="vc_control column_move vc_column-move vc_move-param" href="#"
							title="<?php esc_html_e( 'Drag row to reorder', 'js_composer' ); ?>" data-vc-control="move"><i
								class="vc-composer-icon vc-c-icon-dragndrop"></i></a><span class="vc_row_edit_clone_delete"><a
								class="vc_control column_delete vc_delete-param" href="#"
								title="<?php esc_html_e( 'Delete this param', 'js_composer' ); ?>"><i class="vc-composer-icon vc-c-icon-delete_empty"></i></a></span>
				</div>
				<div class="wpb_element_wrapper">
					<div class="vc_row vc_row-fluid wpb_row_container">
						<div class="wpb_vc_column wpb_sortable vc_col-sm-12 wpb_content_holder vc_empty-column">
							<div class="wpb_element_wrapper">
								<div class="vc_fields vc_clearfix">
									<div class="vc_param_name vc_param-field">
										<label><?php esc_html_e( 'Param name', 'js_composer' ); ?></label>
										<# if ( 'content' === param_name) { #>
										<span class="vc_content"><?php esc_html_e( 'Content', 'js_composer' ); ?></span>
										<input type="text" style="display: none;" name="param_name"
												value="{{ param_name }}"
												placeholder="<?php esc_attr_e( 'Required value', 'js_composer' ); ?>"
												class="vc_param-name"
												data-system="true">
										<span class="description"
												style="display: none;"><?php esc_html_e( 'Use only letters, numbers and underscore.', 'js_composer' ); ?></span>
										<# } else { #>
										<input type="text" name="param_name" value="{{ param_name }}"
												placeholder="<?php esc_attr_e( 'Required value', 'js_composer' ); ?>"
												class="vc_param-name">
										<span
												class="description"><?php esc_html_e( 'Please use only letters, numbers and underscore.', 'js_composer' ); ?></span>
										<# } #>
									</div>
									<div class="vc_heading vc_param-field">
										<label><?php esc_html_e( 'Heading', 'js_composer' ); ?></label>
										<input type="text" name="heading" value="{{ heading }}"
												placeholder="<?php esc_attr_e( 'Input heading', 'js_composer' ); ?>"
										<# if ( 'hidden' === type) { #>
										disabled="disabled"
										<# } #>>
										<span
												class="description"><?php esc_html_e( 'Heading for field in shortcode edit form.', 'js_composer' ); ?></span>
									</div>
									<div class="vc_type vc_param-field">
										<label><?php esc_html_e( 'Field type', 'js_composer' ); ?></label>
										<select name="type">
											<option value=""><?php esc_html_e( 'Select field type', 'js_composer' ); ?></option>
											<option
													value="textfield"<?php echo '<# if (type === "textfield") { #> selected<# } #>'; ?>><?php esc_html_e( 'Textfield', 'js_composer' ); ?></option>
											<option
													value="dropdown"<?php echo '<# if (type === "dropdown") { #> selected<# } #>'; ?>><?php esc_html_e( 'Dropdown', 'js_composer' ); ?></option>
											<option
													value="textarea"<?php echo '<# if(type==="textarea") { #> selected="selected"<# } #>'; ?>><?php esc_html_e( 'Textarea', 'js_composer' ); ?></option>
											<# if ( 'content' === param_name ) { #>
											<option
													value="textarea_html"<?php echo '<# if (type === "textarea_html") { #> selected<# } #>'; ?>><?php esc_html_e( 'Textarea HTML', 'js_composer' ); ?></option>
											<# } #>
											<option
													value="hidden"<?php echo '<# if (type === "hidden") { #> selected<# } #>'; ?>><?php esc_html_e( 'Hidden', 'js_composer' ); ?></option>

										</select>
										<span
												class="description"><?php esc_html_e( 'Field type for shortcode edit form.', 'js_composer' ); ?></span>
									</div>
									<div class="vc_value vc_param-field">
										<label><?php esc_html_e( 'Default value', 'js_composer' ); ?></label>
										<input type="text" name="value" value="{{ value }}" class="vc_param-value">
										<span
												class="description"><?php esc_html_e( 'Default value or list of values for dropdown type (Note: separate by comma).', 'js_composer' ); ?></span>
									</div>
									<div class="description vc_param-field">
										<label><?php esc_html_e( 'Description', 'js_composer' ); ?></label>
										<textarea name="description" placeholder=""
										<# if ( 'hidden' === type ) { #>
										disabled="disabled"
										<# } #> >{{ description
										}}</textarea>
										<span
												class="description"><?php esc_html_e( 'Enter description for parameter.', 'js_composer' ); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</<?php echo esc_attr( $custom_tag ); ?>>
			<?php
		}

		/**
		 *
		 */
		public function renderTemplates() {
			$custom_tag = 'script'; // Maybe use ajax resonse for template
			?>
			<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_automapper-item-tpl">
				<label class="vc_automapper-edit-btn">{{ name }}</label>
				<span class="vc_automapper-item-controls">
					<a href="#" class="vc_automapper-delete-btn" title="<?php esc_html_e( 'Delete', 'js_composer' ); ?>"></a>
					<a href="#" class="vc_automapper-edit-btn" title="<?php esc_html_e( 'Edit', 'js_composer' ); ?>"></a>
				</span>
			</<?php echo esc_attr( $custom_tag ); ?>>
			<?php
			$this->renderMapFormTpl();
		}

		public function create() {
			if ( ! vc_request_param( '_vcnonce' ) ) {
				return;
			}
			vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'manage_options' )->validateDie()->part( 'settings' )->can( 'vc-automapper-tab' )->validateDie();

			$data = vc_post_param( 'data' );
			$shortcode = new Vc_Automap_Model( $data );

			$this->result( $shortcode->save() );
		}

		public function update() {
			if ( ! vc_request_param( '_vcnonce' ) ) {
				return;
			}
			vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'manage_options' )->validateDie()->part( 'settings' )->can( 'vc-automapper-tab' )->validateDie();

			$id = (int) vc_post_param( 'id' );
			$data = vc_post_param( 'data' );
			$shortcode = new Vc_Automap_Model( $id );
			if ( ! isset( $data['params'] ) ) {
				$data['params'] = array();
			}
			$shortcode->set( $data );

			$this->result( $shortcode->save() );
		}

		public function delete() {
			if ( ! vc_request_param( '_vcnonce' ) ) {
				return;
			}
			vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'manage_options' )->validateDie()->part( 'settings' )->can( 'vc-automapper-tab' )->validateDie();

			$id = vc_post_param( 'id' );
			$shortcode = new Vc_Automap_Model( $id );

			$this->result( $shortcode->delete() );
		}

		public function read() {
			if ( ! vc_request_param( '_vcnonce' ) ) {
				return;
			}
			vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'manage_options' )->validateDie()->part( 'settings' )->can( 'vc-automapper-tab' )->validateDie();

			$this->result( Vc_Automap_Model::findAll() );
		}

		/**
		 * Ajax result output
		 *
		 * @param $data
		 */
		public function result( $data ) {
			if ( false !== $data ) {
				wp_send_json_success( $data );
			} else {
				wp_send_json_error( $data );
			}
		}

		/**
		 * Setter/Getter for Disabling Automapper
		 * @static
		 *
		 * @param bool $disable
		 */
		public static function setDisabled( $disable = true ) {
			self::$disabled = $disable;
		}

		/**
		 * @return bool
		 */
		public static function disabled() {
			return self::$disabled;
		}

		/**
		 * Setter/Getter for Automapper title
		 *
		 * @static
		 *
		 * @param string $title
		 */
		public function setTitle( $title ) {
			$this->title = $title;
		}

		/**
		 * @return string|void
		 */
		public function title() {
			return $this->title;
		}

		/**
		 *
		 */
		public static function map() {
			$shortcodes = Vc_Automap_Model::findAll();
			foreach ( $shortcodes as $shortcode ) {
				vc_map( array(
					'name' => $shortcode->name,
					'base' => $shortcode->tag,
					'category' => vc_atm_build_categories_array( $shortcode->category ),
					'description' => $shortcode->description,
					'params' => vc_atm_build_params_array( $shortcode->params ),
					'show_settings_on_create' => ! empty( $shortcode->params ),
					'atm' => true,
					'icon' => 'icon-wpb-atm',
				) );
			}
		}
	}
}
