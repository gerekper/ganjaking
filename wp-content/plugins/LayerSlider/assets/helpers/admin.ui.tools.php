<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

function lsGetOptionField( $type, $key, $default, $attrs = array() ) {

	$value = get_option( 'ls_'.$key, $default );
	$input = '<input type="'.$type.'" name="'.$key.'"';

	if( $type === 'checkbox' && $value ) {
		$input .= ' checked="checked"';

	} else {
		$input .= ' value="'.$value.'"';
	}

	// Theme forced settings
	if( isset( LS_Config::$forced[ $key ] ) ) {
		$help = sprintf(__('This setting is enforced by <b><i>%s</i></b> in order to maximize compatibility on your site.', 'LayerSlider'), LS_Config::$forcedBy[ $key ] );

		$input .= ' class="locked yellow" data-help-delay="100" data-help="'.$help.'" disabled';
	}

	foreach ($attrs as $key => $value) {
		$input .= $key.'="'.$value.'"';
	}

	return $input.'>';

}

function lsOptionRow( $type, $default, $current, $attrs = array(), $trClasses = '', $forceOptionVal = false) {

	$wrapperStart = '';
	$wrapperEnd = '';
	$control = '';

	$default['desc'] = ! empty( $default['desc'] ) ? $default['desc'] : '';


	if( ! empty($default['advanced']) ) {
		$trClasses .= ' ls-advanced ls-hidden';
		$wrapperStart = '<div><i class="dashicons dashicons-flag" data-help="'.__('Advanced option', 'LayerSlider').'"></i>';
		$wrapperEnd = '</div>';

	} else if( ! empty($default['premium']) ) {
		if( ! LS_Config::isActivatedSite() ) {
			$trClasses .= ' ls-premium';
			$wrapperStart = '<div><a class="ls-activation-lock dashicons dashicons-lock" target="_blank" href="'.admin_url('admin.php?page=layerslider-addons' ).'" data-help="'.__('This feature requires product activation. Click on the padlock icon to learn more.', 'LayerSlider').'"></a>';
			$wrapperEnd = '</div>';
		}
	}


	switch($type) {
		case 'input':
			$control = lsGetInput($default, $current, $attrs, true);
			break;

		case 'checkbox':
			$control = lsGetCheckbox($default, $current, $attrs, true);
			break;

		case 'select':
			$control = lsGetSelect($default, $current, $attrs, $forceOptionVal, true);
			break;
	}

	$trClasses = ! empty($trClasses) ? ' class="'.$trClasses.'"' : '';

	echo '<tr'.$trClasses.'>
	<td>'.$wrapperStart.''.$default['name'].''.$wrapperEnd.'</td>
	<td>'.$control.'</td>
	<td class="desc">'.$default['desc'].'</td>
</tr>';
}

function lsGetInput($default, $current, $attrs = array(), $return = false) {

	// Markup
	$el 		= LayerSlider\PHPQuery\phpQuery::newDocumentHTML('<input>')->children();
	$attributes = array();

	$attributes['value'] = $default['value'];
	$attributes['type']  = is_string($default['value']) ? 'text' : 'number';
	$attributes['name']  = $name = is_string($default['keys']) ? $default['keys'] : $default['keys'][0];

	$attrs = isset($default['attrs']) ? array_merge($default['attrs'], $attrs) : $attrs;
	if( ! empty($attrs) && is_array( $attrs ) ) {
		$attributes = array_merge($attributes, $attrs);
	}

	if(isset($default['tooltip'])) {
		$attributes['data-help'] = $default['tooltip'];
	}

	// Combo box
	if( ! empty($attributes['data-options']) ) {
		if( empty($attributes['class']) ) { $attributes['class'] = ''; }

		$attributes['class'] .= ' km-combo-input';
		// $attributes['autocomplete'] = 'off';
	}

	// Override the default
	if(isset($current[$name]) && $current[$name] !== '') {
		$attributes['value'] = htmlspecialchars(stripslashes($current[$name]));
	}

	$attributes['data-value'] = $attributes['value'];
	$el->attr($attributes);

	// Product activation check
	if( ! empty( $default['premium'] ) ) {
		if( ! LS_Config::isActivatedSite() ) {
			$el->addClass('locked');
			$el->attr('disabled', 'disabled');
		}
	}

	$ret = (string) $el;
	LayerSlider\PHPQuery\phpQuery::unloadDocuments();

	if( $return ) { return $ret; } else { echo $ret; }
}



function lsGetCheckbox($default, $current, $attrs = array(), $return = false) {

	// Markup
	$el 		= LayerSlider\PHPQuery\phpQuery::newDocumentHTML('<input>')->children();
	$attributes = array();

	$attributes['value'] = $default['value'];
	$attributes['type']  = 'checkbox';
	$attributes['name']  = $name = is_string($default['keys']) ? $default['keys'] : $default['keys'][0];

	$attrs = isset($default['attrs']) ? array_merge($default['attrs'], $attrs) : $attrs;
	if( ! empty($attrs) && is_array( $attrs ) ) {
		$attributes = array_merge($attributes, $attrs);
	}

	if(isset($default['tooltip'])) {
		$attributes['data-help'] = $default['tooltip'];
	}

	// Checked?
	$attributes['data-value'] = false;
	if($default['value'] === true && ( ! isset($current[$name]) || count($current) < 3 ) ) {
		$attributes['checked'] = 'checked';
		$attributes['data-value'] = 'true';
	} elseif(isset($current[$name]) && $current[$name] != false && $current[$name] !== 'false') {
		$attributes['checked'] = 'checked';
		$attributes['data-value'] = 'true';
	}

	$attributes['value'] = $attributes['data-value'];
	$el->attr($attributes);

	// Product activation check
	if( ! empty( $default['premium'] ) ) {
		if( ! LS_Config::isActivatedSite() ) {
			$el->addClass('locked');
			$el->attr('disabled', 'disabled');
		}
	}

	$ret = (string) $el;
	LayerSlider\PHPQuery\phpQuery::unloadDocuments();

	if( $return ) { return $ret; } else { echo $ret; }
}



function lsGetSelect($default, $current, $attrs = array(), $forceOptionVal = false, $return = false ) {

	// Var to hold data to print
	$el 		= LayerSlider\PHPQuery\phpQuery::newDocumentHTML('<select>')->children();
	$attributes = array();
	$options 	= array();
	$listItems  = array();

	$attributes['value'] = $value = $default['value'];
	$attributes['name']  = $name  = is_string($default['keys']) ? $default['keys'] : $default['keys'][0];

	// Attributes
	$attrs = isset($default['attrs']) ? array_merge($default['attrs'], $attrs) : $attrs;
	if( ! empty($attrs) && is_array( $attrs ) ) {
		$attributes = array_merge($attributes, $attrs);
	}

	// Get options
	if(isset($default['options']) && is_array($default['options'])) {
		$options = $default['options'];
	} elseif(isset($attrs['options']) && is_array($attrs['options'])) {
		$options = $attrs['options'];
	}

	// Override the default
	if(isset($current[$name]) && $current[$name] !== '') {
		$attributes['value'] = $value = $current[$name];
	}

	// Tooltip
	if(isset($default['tooltip'])) {
		$attributes['data-help'] = $default['tooltip'];
	}

	// Add options
	foreach($options as $name => $val) {

		$name = (is_string($name) || $forceOptionVal) ? $name : $val;
		$name = ($name === 'zero') ? 0 : $name;


		$checked = ($name == $value) ? ' selected="selected"' : '';
		$listItems[] = "<option value=\"$name\" $checked>$val</option>";
	}

	$attributes['data-value'] = $attributes['value'];
	$el->append( implode('', $listItems) )->attr($attributes);

	// Product activation check
	if( ! empty( $default['premium'] ) ) {
		if( ! LS_Config::isActivatedSite() ) {
			$el->addClass('locked');
			$el->attr('disabled', 'disabled');
		}
	}

	$ret = (string) $el;
	LayerSlider\PHPQuery\phpQuery::unloadDocuments();

	if( $return ) { return $ret; } else { echo $ret; }
}

?>