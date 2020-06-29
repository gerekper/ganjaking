<?php
defined('WYSIJA') or die('Restricted access');
/* some Functions are based on Codeigniter's Form Helper Class (http://www.codeigniter.com) */
class WYSIJA_help_forms{
	var $eachValues    = array();
	var $eachValuesSec = array();

	function __construct(){
		// I believe this is for translation purpose, making sure the correct language is loaded
		add_action( 'init', array( $this, 'apply_filters' ), 20 );

		$this->eachValues = array(
			'one_min' => __( 'every minute', WYSIJA ),
			'two_min' => __( 'every 2 minutes', WYSIJA ),
			'five_min' => __( 'every 5 minutes', WYSIJA ),
			'ten_min' => __( 'every 10 minutes', WYSIJA ),
			'fifteen_min' => __( 'every 15 minutes', WYSIJA ),
			'thirty_min' => __( 'every 30 minutes', WYSIJA ),
			'hourly' => __( 'every hour', WYSIJA ),
			'two_hours' => __( 'every 2 hours', WYSIJA )
		);

		$this->eachValuesSec = array(
			'one_min' => '60',
			'two_min' => '120',
			'five_min' => '300',
			'ten_min' => '600',
			'fifteen_min' => '900',
			'thirty_min' => '1800',
			'hourly' => '3600',
			'two_hours' => '7200',
			'twicedaily' => '43200',
			'daily' => '86400',
		);
	}

	function apply_filters() {
		$this->eachValues    = apply_filters( 'mpoet_sending_frequency', $this->eachValues );
		$this->eachValuesSec = apply_filters( 'mpoet_sending_frequency_sec', $this->eachValuesSec );
	}

	function input($data = '', $value = '', $extra = '') {
			$defaults = array('type' => 'text', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);
			$content="<input ".$this->setAttrib($data, $defaults).$extra." />";
			return $content;
	}
	function password($data = '', $value = '', $extra = '') {
			$defaults = array('type' => 'password', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);
			$content="<input ".$this->setAttrib($data, $defaults).$extra." />";
			return $content;
	}

	function hidden($data = '', $value = '', $extra = '') {
			$defaults = array('type' => 'hidden', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);
			$content="<input ".$this->setAttrib($data, $defaults).$extra." />";
			return $content;
	}

	function textarea($data = '', $value = '', $extra = '') {
			$defaults = array('name' => (( ! is_array($data)) ? $data : ''), 'cols' => '90', 'rows' => '12');

			if ( ! is_array($data) OR ! isset($data['value'])) {
					$val = $value;
			} else {
					$val = $data['value'];
					unset($data['value']);
			}

			return '<textarea '.$this->setAttrib($data, $defaults).$extra.'>'.esc_textarea($val).'</textarea>';
	}


	function tinymce($idName = '', $content = '') {
		$this->the_editor(stripslashes($content), $idName,'title',false);
	}

	function checkbox($data = '', $value = '', $checked = FALSE, $extra = '') {
	$defaults = array('type' => 'checkbox', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

	if (is_array($data) AND array_key_exists('checked', $data)) {
		$checked = $data['checked'];

		if ($checked == FALSE) unset($data['checked']);
		else $data['checked'] = 'checked';
	}

	if ($checked == TRUE) $defaults['checked'] = 'checked';
	else unset($defaults['checked']);

	return "<input ".$this->setAttrib($data, $defaults).$extra." />";
	}

	function checkboxes($data = '', $values = array(), $value = '', $extra = '') {
		$html='';
		foreach($values as $val => $valtitle){
			$checked=false;
			$data2=$data;
			$data2['id'].="-".$val;
			if($val==$value)$checked=true;
			$html.='<label for="'.$data2['id'].'">'.$this->checkbox($data2, $val, $checked, $extra).$valtitle."</label>";
		}

		return $html;
	}

	function radios($data = '', $values = array(), $value = '', $extra = '') {
		$html='';
		foreach($values as $val => $valtitle){
			$checked=false;
			$data2=$data;
			$data2['id'].="-".$val;
			if($val==$value)$checked=true;
			$html.='<label for="'.$data2['id'].'">'.$this->radio($data2, $val, $checked, $extra).$valtitle."</label>";
		}

		return $html;
	}

	function radio($data = '', $value = '', $checked = FALSE, $extra = '') {
			if ( ! is_array($data)) {
					$data = array('name' => $data);
			}

			$data['type'] = 'radio';
			return $this->checkbox($data, $value, $checked, $extra);
	}


	function the_editor($content, $id = 'content', $prev_id = 'title', $media_buttons = true, $tab_index = 2){
	  $rows = get_option('default_post_edit_rows');
			if (($rows < 3) || ($rows > 100))
					$rows = 12;

			if ( !current_user_can( 'upload_files' ) )
					$media_buttons = false;

			$richedit =  user_can_richedit();
			$class = '';

			if ( $richedit || $media_buttons ) { ?>
			<div id="editor-toolbar">
	<?php
			if ( $richedit ) {
					$wp_default_editor=tap_get_option("visual_ed_disable");
					?>
					<div class="zerosize"><input accesskey="e" type="button" onclick="switchEditors.go('<?php echo $id; ?>')" /></div>
		<?php	if ( $wp_default_editor ) {
								add_filter('the_editor_content', 'wp_htmledit_pre'); ?>
								<a id="edButtonHTML" class="edButtonHTML active hide-if-no-js" onclick="switchEditors.go('<?php echo $id; ?>', 'html');"><?php _e('HTML'); ?></a>
								<a id="edButtonPreview" class="edButtonPreview hide-if-no-js" onclick="switchEditors.go('<?php echo $id; ?>', 'tinymce');"><?php _e('Visual'); ?></a>
		<?php	} else {
								$class = " class='theEditor'";
								add_filter('the_editor_content', 'wp_richedit_pre'); ?>
								<a id="edButtonHTML" class="edButtonHTML hide-if-no-js" onclick="switchEditors.go('<?php echo $id; ?>', 'html');"><?php _e('HTML'); ?></a>
								<a id="edButtonPreview" class="edButtonPreview active hide-if-no-js" onclick="switchEditors.go('<?php echo $id; ?>', 'tinymce');"><?php _e('Visual'); ?></a>
		<?php	}
			}
			?><div id="media-buttons" class="hide-if-no-js"><?php
			if ( $media_buttons ) { ?>

					<?php do_action( 'media_buttons' ); ?>

	<?php
			} ?>
			</div>
		</div>
	<?php
			}
	?>
			<div id="quicktags"><?php
			wp_print_scripts( 'quicktags' ); ?>
			</div>

	<?php
			$the_editor = apply_filters('the_editor', "<div id='editorcontainer'><textarea rows='$rows'$class cols='40' name='$id' tabindex='$tab_index' id='$id'>%s</textarea></div>\n");
			$the_editor_content = apply_filters('the_editor_content', $content);

			printf($the_editor, $the_editor_content);

	}

	function titleh($idName = '', $selected = '') {
		$options=array("default"=>"default","h2"=>"h2","h3"=>"h3","h4"=>"h4");
		echo $this->dropdown($idName,$options,$selected);
	}

	function enabled($idName = '', $selected = '') {
		$options=array(0=>"disabled",1=>"enabled");
		echo $this->dropdown($idName,$options,$selected);
	}

	function dropdown($data = '', $options = array(), $selected = array(), $extra = '') {
			if ( ! is_array($selected)) {
				$selected = array($selected);
			}

			if ( empty($options) ) {
				return false;
			}

			$defaults = array('name' => (( ! is_array($data)) ? $data : ''));

			/* buggy lines
			 * if (count($selected) === 0) {
				if (isset($_POST[$name])) $selected = array($_POST[$name]);
			}*/

			if ($extra != '') $extra = ' '.$extra;

			$multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';
			$form = '<select '.$this->setAttrib($data, $defaults).$extra.$multiple.">\n";

			foreach ($options as $key => $val) {
				$key = (string) $key;
				if (is_array($val)) {
					$form .= '<optgroup label="'.$key.'">'."\n";
					foreach ($val as $optgroup_key => $optgroup_val) {
							$sel = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';
							$form .= '<option value="'.esc_attr($optgroup_key).'"'.$sel.'>'.(string) $optgroup_val."</option>\n";
					}
					$form .= '</optgroup>'."\n";
				} else {
					$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';
					$form .= '<option value="'.esc_attr($key).'"'.$sel.'>'.(string) $val."</option>\n";
				}
			}
			$form .= '</select>';
			return $form;
	}


	function setAttrib($attributes, $default) {
		if (is_array($attributes)) {
			foreach ($default as $key => $val) {
				if (isset($attributes[$key])) {
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}
			if(isset($attributes["default"])){

				$attributes["onBlur"]="if(this.value=='') {this.value='".$attributes["default"]."';this.style.color='#ccc';this.style.fontStyle='italic';}";
				$attributes["onFocus"]="if(this.value=='".$attributes["default"]."') {this.value='';this.style.color='#000';this.style.fontStyle='normal';}";
				if((!isset($default['value']) || !$default['value'])){
					$default['value']=$attributes["default"];
					$attributes["style"]="color:#ccc;font-style:italic;";
				}
			}

			if (count($attributes) > 0) {
				$default = array_merge($default, $attributes);
			}
		}

		$att = '';

		foreach ($default as $key => $val) {
			if($key=='value') $val=esc_attr($val);
			$att .= $key . '="' . $val . '" ';
		}

		return $att;
	}

}

