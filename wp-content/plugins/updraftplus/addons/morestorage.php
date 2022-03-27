<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: morestorage:Multiple storage options
Description: Provides the ability to backup to multiple remote storage facilities, not just one
Version: 1.3
Shop: /shop/morestorage/
Latest Change: 1.11.28
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

new UpdraftPlus_Addon_MoreStorage;

class UpdraftPlus_Addon_MoreStorage {

	public function __construct() {
		add_filter('updraftplus_storage_printoptions', array($this, 'storage_printoptions'), 10, 2);
		add_filter('updraftplus_storage_printoptions_multi', array($this, 'storage_printoptions_multi'), 10, 1);
		// add_action('updraftplus_config_print_after_storage', array($this, 'config_print_after_storage'));
		add_action('updraftplus_config_print_before_storage', array($this, 'config_print_before_storage'), 10, 2);
		add_action('updraftplus_config_print_add_multi_storage', array($this, 'config_print_add_multi_storage'), 10, 2);
		add_action('updraftplus_config_print_add_instance_label', array($this, 'config_print_add_instance_label'), 10, 2);
		add_action('updraftplus_config_print_add_conditional_logic', array($this, 'config_print_add_conditional_logic'), 10, 2);
		add_filter('updraftplus_savestorage', array($this, 'savestorage'), 10, 2);
		add_action('updraftplus_after_remote_storage_heading_message', array($this, 'after_remote_storage_heading_message'));
		add_filter('updraft_boot_backup_remote_storage_instance_include', array($this, 'boot_backup_remote_storage_instance_include'), 10, 5);
	}

	public function after_remote_storage_heading_message() {
		return '<em>'.__('(as many as you like)', 'updraftplus').'</em>';
	}

	public function admin_print_footer_scripts() {
		?>
		<script>
		jQuery(function() {
			
			jQuery('.remote-tab').on('click', function(event) {
				//Close other tabs and open the clicked one
				event.preventDefault();
				var the_method = jQuery(this).attr('name');
				updraft_remote_storage_tab_activation(the_method);
			});
			
		});
		
		</script>
		<?php
	}

	/**
	 * This method will setup the HTML template that is added before each remote storage template.
	 *
	 * @param  String $storage        - the name of the remote storage method
	 * @param  Object $storage_object - the remote storage object
	 * @return String                 - the HTML template
	 */
	public function config_print_before_storage($storage, $storage_object = null) {
		global $updraftplus;
		?>
		<tr class="<?php echo is_object($storage_object) ? $storage_object->get_css_classes() . ' ' . $storage . '_updraft_remote_storage_border' : "updraftplusmethod $storage";?>">
			<th>
				<?php
					if (is_object($storage_object) && $storage_object->supports_feature('multi_storage')) {
					?>
						<h3 class="updraft_edit_label_instance" data-instance_id="{{instance_id}}" data-method="<?php echo $storage; ?>">{{instance_label}}<span class="dashicons dashicons-edit"></span></h3>
					<?php
					} else {
					?>
						<h3><?php echo $updraftplus->backup_methods[$storage]; ?></h3>
					<?php
					}
					?>
			</th>
			<td>
				<?php
					if (is_object($storage_object) && $storage_object->supports_feature('multi_storage')) {
						?>
						<div class="updraft_multi_storage_options">
							<input type="checkbox" class="updraft_instance_toggle" id="<?php echo 'updraft_' . $storage . '_instance_enabled' . '_{{instance_id}}';?>" name="<?php echo 'updraft_' . $storage . '[settings][{{instance_id}}][instance_enabled]';?>" value="1" {{#ifeq "1" instance_enabled}} checked="checked"{{/ifeq}}>
							<label for="<?php echo 'updraft_' . $storage . '_instance_enabled' . '_{{instance_id}}';?>" class="updraft_toggle_instance_label">{{#ifeq "1" instance_enabled}}<?php echo __('Currently enabled', 'updraftplus'); ?>{{else}} <?php echo __('Currently disabled', 'updraftplus'); ?>{{/ifeq}}</label>
						</div>
						<a href="<?php echo esc_url(UpdraftPlus::get_current_clean_url());?>" class="updraft_multi_storage_options updraft_delete_instance" data-instance_id="{{instance_id}}" data-method="<?php echo $storage; ?>"><?php echo __('Delete these settings', 'updraftplus'); ?></a>
						<?php
					}
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Setup conditional logic HTML template
	 *
	 * @param  String $storage        - the name of the remote storage method
	 * @param  Object $storage_object - the remote storage object
	 * @return Void|Null void on success, null if the given storage object doesn't support `conditional_logic` feature
	 */
	public function config_print_add_conditional_logic($storage, $storage_object = null) {
		if (!$storage_object->supports_feature('conditional_logic')) return;
	?>
		<tr class="<?php echo is_object($storage_object) ? $storage_object->get_css_classes() : "updraftplusmethod $storage";?> conditional_logic_row">
			<th><?php _e('Send scheduled backups to this destination:', 'updraftplus'); ?></th>
			<td>
			{{#with instance_conditional_logic as | logic |}}
				<div class="conditional_remote_backup">
					<select class="logic_type" name="<?php echo 'updraft_' . $storage . '[settings][{{@root.instance_id}}][instance_conditional_logic][type]';?>">
						{{#each logic.logic_options}}
						<option value="{{this.value}}"{{#ifCond this.value "==" logic.type}} selected{{/ifCond}}>{{this.label}}</option>
						{{/each}}
					</select>
					<div class="logic"{{#ifCond "undefined" "typeof" logic.rules}} style="display: none"{{else}}{{#ifeq "0" (get_length logic.rules)}} style="display: none"{{/ifeq}}{{/ifCond}}>
						<ul class="rules" data-storage="<?php echo $storage; ?>" data-instance_id="{{@root.instance_id}}" data-rules="{{#ifCond "0" "<" (get_length logic.rules)}}{{get_length logic.rules}}{{else}}1{{/ifCond}}">
							{{#ifCond "0" "<" (get_length logic.rules)}}
								{{#each logic.rules as | rule |}}
									<li>
										<select class="conditional_logic_operand" name="<?php echo 'updraft_' . $storage . '[settings][{{@root.instance_id}}][instance_conditional_logic][rules][{{@index}}][operand]';?>">
										{{#each logic.operand_options}}
											<option value="{{this.value}}"{{#ifCond this.value "==" rule.operand}} selected{{/ifCond}}>{{this.label}}</option>
										{{/each}}
										</select>
										<select name="<?php echo 'updraft_' . $storage . '[settings][{{@root.instance_id}}][instance_conditional_logic][rules][{{@index}}][operator]';?>">
										{{#each logic.operator_options}}
											<option value="{{this.value}}"{{#ifCond this.value "==" rule.operator}} selected{{/ifCond}}>{{this.label}}</option>
										{{/each}}
										</select>
										<select name="<?php echo 'updraft_' . $storage . '[settings][{{@root.instance_id}}][instance_conditional_logic][rules][{{@index}}][value]';?>">
										{{#ifCond "day_of_the_month" "==" rule.operand}}
											{{#for 1 31 1}}<option value="{{this}}"{{#ifCond this "==" rule.value}} selected{{/ifCond}}>{{this}}</option>{{/for}}
										{{/ifCond}}
										{{#ifCond "day_of_the_week" "==" rule.operand}}
											{{#each logic.day_of_the_week_options}}
												<option value="{{this.index}}"{{#ifCond this.index "==" rule.value}} selected{{/ifCond}}>{{this.value}}</option>
											{{/each}}
										{{/ifCond}}
										</select>
										</span>
										{{#ifCond "1" "<" (get_length logic.rules)}}
										<span class="remove-rule">
										<svg viewbox="0 0 25 25">
											<line x1="6.5" y1="18.5" x2="18.5" y2="6.5" fill="none" stroke="#FF6347" stroke-width="3" vector-effect="non-scaling-stroke" ></line>
											<line y1="6.5" x1="6.5" y2="18.5" x2="18.5" fill="none" stroke="#FF6347" stroke-width="3" vector-effect="non-scaling-stroke" ></line>
										</svg>
										</span>
										{{/ifCond}}
									</li>
								{{/each}}
							{{else}}
							<li>
								<select class="conditional_logic_operand" name="<?php echo 'updraft_' . $storage . '[settings][{{@root.instance_id}}][instance_conditional_logic][rules][0][operand]';?>" disabled>
								{{#each logic.operand_options}}
									{{#ifeq @index 0}}{{#set_var 'selected_rule_operand' this.value}}{{/set_var}}{{/ifeq}}
									<option value="{{this.value}}">{{this.label}}</option>
								{{/each}}
								</select>
								<select name="<?php echo 'updraft_' . $storage . '[settings][{{@root.instance_id}}][instance_conditional_logic][rules][0][operator]';?>" disabled>
								{{#each logic.operator_options}}
									<option value="{{this.value}}">{{this.label}}</option>
								{{/each}}
								</select>
								<select name="<?php echo 'updraft_' . $storage . '[settings][{{@root.instance_id}}][instance_conditional_logic][rules][0][value]';?>" disabled>
								{{#ifCond @root.selected_rule_operand "===" "day_of_the_week"}}
									{{#each logic.day_of_the_week_options}}
										<option value="{{this.index}}"{{#ifCond this.index "==" rule.value}} selected{{/ifCond}}>{{this.value}}</option>
									{{/each}}
								{{/ifCond}}
								{{#ifCond @root.selected_rule_operand "===" "day_of_the_month"}}
									{{#for 1 31 1}}<option value="{{this}}"{{#ifCond this "==" rule.value}} selected{{/ifCond}}>{{this}}</option>{{/for}}
								{{/ifCond}}
								</select>
							</li>
							{{/ifCond}}
						</ul>
						<input type="button" class="button-primary add-new-rule" value="Add new rule">
					</div>
				</div>
				{{/with}}
			</td>
		</tr>
	<?php
	}

	/**
	 * This method will setup the HTML template for the add instance button
	 *
	 * @param  String $storage        - the name of the remote storage method
	 * @param  Object $storage_object - the remote storage object
	 * @return String                 - the HTML template
	 */
	public function config_print_add_multi_storage($storage, $storage_object = null) {
		global $updraftplus;
		?><tr class="<?php echo is_object($storage_object) ? $storage_object->get_css_classes(false) . " " . "$storage" . "_add_instance_container" : "updraftplusmethod $storage";?>">
			
			<td colspan="2">
				<a href="<?php echo esc_url(UpdraftPlus::get_current_clean_url()); ?>" class="updraft_add_instance" data-method="<?php echo $storage; ?>"><?php echo sprintf(__('Add another %s account...', 'updraftplus'), $updraftplus->backup_methods[$storage]); ?></a>
			</td>
		</tr>
		<?php
	}

	/**
	 * This method will setup the HTML template for the instance label setting
	 *
	 * @param  String $storage        - the name of the remote storage method
	 * @param  Object $storage_object - the remote storage object
	 * @return String                 - the HTML template
	 */
	public function config_print_add_instance_label($storage, $storage_object) {
		?>
			<input type="hidden" class="<?php echo is_object($storage_object) ? $storage_object->get_css_classes() : "updraftplusmethod $storage";?>" <?php is_object($storage_object) ? $storage_object->output_settings_field_name_and_id('instance_label') . ' ' . $storage . '_updraft_instance_label' : ''; ?> value="{{instance_label}}" />
		<?php
	}

	public function savestorage($rinput, $input) {
		return $input;
	}

	public function storage_printoptions_multi() {
		return 'multi';
	}
	
	public function storage_printoptions($ret, $active_service) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		global $updraftplus;
		add_action('admin_print_footer_scripts', array($this, 'admin_print_footer_scripts'));

		?>
		</div></td></tr>
		<tr>
			<th colspan="2"><h2 class="updraft_settings_sectionheading"><?php _e('Remote Storage Options', 'updraftplus');?></h2>
		</tr>
		<tr id="remote_storage_tabs" style="border-bottom: 1px solid #ccc">
			<td colspan="2" style="padding:0px">
				<?php
					foreach ($updraftplus->backup_methods as $method => $description) {
					echo "<a class=\"nav-tab remote-tab updraft-hidden remote-tab-$method\" id=\"remote-tab-$method\" name=\"$method\" href=\"#\" ";
					// if ((!is_array($active_service) && $active_service !== $method) || !(is_array($active_service) && in_array($method, $active_service))) echo 'style="display:none;"';
					echo 'style="display:none;"';
					echo ">".htmlspecialchars($description)."</a>\n";
					}
				?>
		
		<?php
		return true;

	}

	/**
	 * Perform storages' conditional logic and screens out the storage instance if it is not to be included.
	 *
	 * @param Boolean $include_it		   - pre-filter value
	 * @param Array   $instance_settings   - settings for the instance being looked at.
	 * @param String  $method_id		   - method identifier
	 * @param String  $instance_id		   - settings instance identifier
	 * @param Boolean $is_scheduled_backup - whether the backup started is a scheduled one or not
	 *
	 * @return Boolean - filtered value
	 */
	public function boot_backup_remote_storage_instance_include($include_it, $instance_settings, $method_id, $instance_id, $is_scheduled_backup) {
	
		// Don't further process anything if it is already excluded, not a scheduled backup, or if there are no (valid) rules
		if (!$include_it || !$is_scheduled_backup || empty($instance_settings['instance_conditional_logic']) || empty($instance_settings['instance_conditional_logic']['type']) || empty($instance_settings['instance_conditional_logic']['rules'])) return $include_it;

		global $updraftplus;
	
		$instance_settings = $instance_settings['instance_conditional_logic'];
		
		// check the logic rules, and proceed when things match the rules
		
		$current_day_of_the_month = get_date_from_gmt(gmdate('Y-m-d H:i:s'), 'j');
		$current_day_of_the_week = get_date_from_gmt(gmdate('Y-m-d H:i:s'), 'w');
		$current_day_of_the_week = "" !== $current_day_of_the_week ? $current_day_of_the_week : '';
		$result = 'any' === strtolower($instance_settings['type']) ? false : true;
		
		foreach ((array) $instance_settings['rules'] as $rule) {

			$operand = isset($rule['operand']) ? $rule['operand'] : '';
			if ('day_of_the_week' === $operand) $value1 = $current_day_of_the_week;
			if ('day_of_the_month' === $operand) $value1 = $current_day_of_the_month;
			$operator = isset($rule['operator']) ? $rule['operator'] : '';
			$value2 = isset($rule['value']) ? $rule['value'] : '';
			switch (strtolower($instance_settings['type'])) {
				case 'any':
					$result = $result || $updraftplus->if_cond((string) $value1, $operator, (string) $value2);
					if ($result) break 2;
					break;
				case 'all':
					$result = $result && $updraftplus->if_cond((string) $value1, $operator, (string) $value2);
					if (!$result) break 2;
					break;
				case 'default':
					break;
			}
		}
		
		if (!$result) {
			$updraftplus->log("This instance id ($method_id, $instance_id) has backup rules set up, but one or more conditions didn't match.");
		}

		return $result;
	}
}
