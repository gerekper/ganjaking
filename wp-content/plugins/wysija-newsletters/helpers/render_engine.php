<?php
defined('WYSIJA') or die('Restricted access');
/**
 * @class Wysija Rendering Engine Helper (PHP4 version)
 */
class WYSIJA_help_render_engine extends WYSIJA_object {
    private $_template = null;
    private $_template_path = array();
    private $_data_path = null;
    private $_strip_special_chars = false;
    private $_render_time = false;
    private $_i18n = null;
    private $_lc_time = '';
    private $_inline = false;
    private $_fonts = array(
        'Arial' => "Arial, 'Helvetica Neue', Helvetica, sans-serif",
        'Comic Sans MS' => "'Comic Sans MS', 'Marker Felt-Thin', Arial, sans-serif",
        'Courier New' => "'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace",
        'Georgia' => "Georgia, Times, 'Times New Roman', serif",
        'Tahoma' => "Tahoma, Verdana, Segoe, sans-serif",
        'Times New Roman' => "'Times New Roman', Times, Baskerville, Georgia, serif",
        'Trebuchet MS' => "'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif",
        'Verdana' => "Verdana, Geneva, sans-serif"
    );

    const _OPEN = '{';
    const _CLOSE = '}';
    const _LOOP = 'loop';
    const _FOREACH = 'foreach';
    const _INCLUDE = 'include';
    const _TRANSLATE = 'translate';
    const _IF = 'if';
    const _SET = 'set';
    const _COUNTER = 'i';
    const _PAIR = 'IS_PAIR';
    const _FIRST = 'IS_FIRST';
    const _LAST = 'IS_LAST';
    const _VALUE = '_VALUE';
    const _FIRST_COL = 'IS_FIRST_COL';
    const _LAST_COL = 'IS_LAST_COL';
    const _FIRST_ROW = 'IS_FIRST_ROW';
    const _LAST_ROW = 'IS_LAST_ROW';
    const _VAR = '([\$#])([\w\-_\.]+)';
    const _MODIFIER = "(\|{0,1}[^}]*)";

    function __construct () { }

    public function setLcTime ($lc_time)
    {
        $this->_lc_time = $lc_time;
    }

    public function setTranslator($i18n)
    {
        $this->_i18n = $i18n;
    }

    public function setTemplatePath ($path)
    {
        if (!is_array ($path)) {
            $path = array ($path);
        }
        $this->_template_path = $path;
    }

    public function getTemplatePath ()
    {
        return $this->_template_path;
    }

    public function setDataPath ($path)
    {
        $this->_data_path = $path;
    }

    public function setStripSpecialchars ($value)
    {
        $this->_strip_special_chars = $value;
    }

    public function setInline($bool)
    {
        $this->_inline = $bool;
        $this->_inline = false;
    }

    public function renderCSS($vars) {
        $result = array();

        if (isset($vars['family'])) {
            $font_family = '\''.$vars['family'].'\'';
            // check if we have fallbacks for this font
            if(array_key_exists($vars['family'], $this->_fonts)) {
                $font_family = $this->_fonts[$vars['family']];
            }
            $result[] = 'font-family: '.$font_family.';';
        }
        if (isset($vars['size'])) $result[] = 'font-size: ' . self::_css_format_font_size_value($vars['size']) . ';';
        if (isset($vars['color'])) $result[] = 'color: ' . self::_css_format_color($vars['color']) . ';color: ' . self::_css_format_color($vars['color']) . ' !important;';
        if (isset($vars['background']) && $vars['background'] !== '') $result[] = 'background-color: ' . self::_css_format_color($vars['background']) . ';';
        if (isset($vars['underline'])) {
            if ($vars['underline'] == '1') $result[] = 'text-decoration: underline;';
            elseif ($vars['underline'] == '-1') $result[] = 'text-decoration: none;';
        }
        if (isset($vars['text-decoration'])) $result[] = 'text-decoration: ' . $vars['text-decoration'] . ';';
        if (isset($vars['outline'])) $result[] = 'outline: ' . $vars['outline'] . ';';
        if (isset($vars['height'])) $result[] = 'height: ' . $vars['height'] . ';';
        if (isset($vars['border']) && is_array($vars['border'])) {
            $result[] = 'border: ' . $vars['border']['size'] . ' ' . $vars['border']['style'] . ' ' . $vars['border']['color'] . ';';
        } else {
            $result[] = 'border: 0;';
        }
        if (isset($vars['border-collapse'])) $result[] = 'border-collapse: ' . $vars['border-collapse'] . ';';
        if (isset($vars['mso-table-space'])) $result[] = 'mso-table-lspace: ' . $vars['mso-table-space'] . '; mso-table-rspace: ' . $vars['mso-table-space'] . ';';
        if (isset($vars['text-align'])) $result[] = 'text-align: ' . $vars['text-align'] . ';';
        if (isset($vars['font-weight'])) $result[] = 'font-weight: ' . $vars['font-weight'] . ';';
        if (isset($vars['font-style'])) $result[] = 'font-style: ' . $vars['font-style'] . ';';
        if (isset($vars['letter-spacing'])) $result[] = 'letter-spacing: ' . $vars['letter-spacing'] . ';';
        if (isset($vars['line-height'])) {
            if ($vars['line-height'] == '0') {
                $result[] = 'line-height: 0;';
            } else {
                $result[] = 'mso-line-height-rule: exactly;-mso-line-height-rule: exactly;line-height: ' . self::_css_format_line_height($vars['line-height']) . ';';
            }
        }
        if (isset($vars['list-style-position'])) $result[] = 'list-style-position: ' . $vars['list-style-position'] . ';';
        if (isset($vars['list-style-type'])) $result[] = 'list-style-type: ' . $vars['list-style-type'] . ';';
        if (isset($vars['list-style'])) $result[] = 'list-style: ' . $vars['list-style'] . ';';
        if (isset($vars['display'])) $result[] = 'display: ' . $vars['display'] . ';';
        if (isset($vars['margin'])) $result[] = 'margin: ' . $vars['margin'] . ';';
        if (isset($vars['padding'])) $result[] = 'padding: ' . $vars['padding'] . ';';
        if (isset($vars['margin-top']) && $vars['margin-top'] != '') $result[] = 'margin-top: ' . $vars['margin-top'] . ';';
        if (isset($vars['margin-right']) && $vars['margin-right'] != '') $result[] = 'margin-right: ' . $vars['margin-right'] . ';';
        if (isset($vars['margin-bottom']) && $vars['margin-bottom'] != '') $result[] = 'margin-bottom: ' . $vars['margin-bottom'] . ';';
        if (isset($vars['margin-left']) && $vars['margin-left'] != '') $result[] = 'margin-left: ' . $vars['margin-left'] . ';';
        if (isset($vars['padding-top']) && $vars['padding-top'] != '') $result[] = 'padding-top: ' . $vars['padding-top'] . ';';
        if (isset($vars['padding-right']) && $vars['padding-right'] != '') $result[] = 'padding-right: ' . $vars['padding-right'] . ';';
        if (isset($vars['padding-bottom']) && $vars['padding-bottom'] != '') $result[] = 'padding-bottom: ' . $vars['padding-bottom'] . ';';
        if (isset($vars['padding-left']) && $vars['padding-left'] != '') $result[] = 'padding-left: ' . $vars['padding-left'] . ';';
        if (isset($vars['clear'])) $result[] = 'clear: ' . $vars['clear'] . ';';
        if (isset($vars['float'])) $result[] = 'float: ' . $vars['float'] . ';';
        if (isset($vars['vertical-align'])) $result[] = 'vertical-align: ' . $vars['vertical-align'] . ';';
        if (isset($vars['word-wrap'])) $result[] = 'word-wrap: break-word;';
        if (isset($vars['interpolation'])) $result[] = '-ms-interpolation-mode: ' . $vars['interpolation'] . ';';
        if (isset($vars['min-width'])) $result[] = 'min-width: ' . $vars['min-width'] . ';';
        if (isset($vars['max-width'])) $result[] = 'max-width: ' . $vars['max-width'] . ';';
        if (isset($vars['outlook_fix_tr'])) $result[] = 'font-size: 1px; mso-line-height-alt: 0; mso-margin-top-alt: 1px;';

        return implode('', $result);
    }

    public function render($vars, $template)
    {
        if (is_object ($vars)) {
            $vars = get_object_vars($vars);
        }
        if ($string = $this->_loadTemplate ($template)) {
            if ($this->_render_time) {
                $time_start = microtime();
            }
            $this->_vars = $vars;

            if($this->_inline) {
                // inline mode (removes any tabs, carriage return, line breaks)
                $string = preg_replace("#(\t|\r|\n)#UiS", '', trim($string));
                $string = preg_replace("#> +<#UiS", '><', $string);
            }

            $output = $this->_parse($string);

            if ($this->_render_time) {
                $time_end = microtime();
                $time = $time_end - $time_start;
                $output .= "<div>wysija rendering engine rendering took : $time second(s)</div>";
            }
            return $output;
        } else {
            throw new Exception('wysija rendering engine needs a template');
        }
    }

    // converts HTML template to PrototypeJS Template
    protected function _parseForJS($string, $vars = null) {
        $old_string = $string;
        if (null === $vars) {
            $vars =& $this->_vars;
        }

        # search loop, include or if statement
        while (preg_match ("`".self::_OPEN."(".self::_LOOP."|".self::_FOREACH."|".self::_INCLUDE."|".self::_IF."|".self::_SET."|".self::_TRANSLATE.")([^\{]*?)".self::_CLOSE."`", $string, $preg)) {
            $result_pattern = '';
            $tag_mask = $preg[0];
            $tag_type = $preg[1];
            $tag_properties = $preg[2];

            # LOOP & FOREACH
            if ($tag_type == self::_LOOP || $tag_type == self::_FOREACH ) {
                $loop_name = $this->_getProperty ('name', $tag_properties);
                $loop_as = $this->_getProperty ('value', $tag_properties);
                $loop_key = $this->_getProperty ('key', $tag_properties);
                $loop_row_size = $this->_getProperty ('row_size', $tag_properties);
                $reverse = $this->_getProperty ('reverse', $tag_properties);

                # define loop string and replace pattern
                $loop_replace_pattern = $this->_getEncapsuledPattern ($string, $tag_type, $tag_mask);
                preg_match ("`".$loop_replace_pattern."`s", $string, $loops);
                $loop_string = $loops[1];

                # loop on array
                preg_match ("`^".self::_VAR."`", $loop_name, $preg);
                $loop_array = $this->_getValue ($preg, $vars);

				if ($reverse) {
					$loop_array = array_reverse ($loop_array);
				}

                if (is_array ($loop_array)) {
                    $count = count ($loop_array);
                    $loop = array ();
                    $i = 0;
                    foreach ($loop_array as $line) {
                        # meta values
                        if (is_array($line)) {
                            # is pair ?
                            $line[self::_PAIR] = ($i+1)%2 ;

                            # position in list
                            $i < $count-1 ? $line[self::_LAST] = false : $line[self::_LAST] = true;
                            $i === 0 ? $line[self::_FIRST] = true : $line[self::_FIRST] = false;

                            # rows and columns infos
                            if ($loop_row_size) {
                                # col position
                                ($i+1)%$loop_row_size ? $line[self::_LAST_COL] = false : $line[self::_LAST_COL] = true;
                                ($i+1)%$loop_row_size === 1 ? $line[self::_FIRST_COL] = true : $line[self::_FIRST_COL] = false;

                                # row position
                                $i<$loop_row_size ? $line[self::_FIRST_ROW] = true : $line[self::_FIRST_ROW] = false;
                                $modulo = $count%$loop_row_size;
                                if (($modulo && $i>=$count-$modulo) || (!$modulo && $i>=$count-$loop_row_size)) {
                                    $line[self::_LAST_ROW] = true;
                                } else {
                                    $line[self::_LAST_ROW] = false;
                                }
                            }
                        }

                        # switch case
                        if ($tag_type == self::_LOOP) {
                            $loop_key ? $line[$loop_key] = $i : $line[self::_COUNTER] = $i;
                            $loop[] = $this->_parseForJS($loop_string, $line);
                        } else {
                            $loop_key ? $vars[$loop_key] = $i : $vars[self::_COUNTER] = $i;
                            $vars[$loop_as] = $line;
                            $loop[] = $this->_parseForJS($loop_string, $vars);
                        }
                        $i++;
                    }

                    $result_pattern .= implode ($loop, '');
                }

                # replace
                $string = preg_replace ("`".$loop_replace_pattern."`s", $result_pattern, $string, 1);
            }

            # IF
            if ($tag_type == self::_IF) {
                $replace_pattern = $this->_getEncapsuledPattern ($string, self::_IF, $tag_mask);
                preg_match ("`".$replace_pattern."`s", $string, $result);
                $if_string = $result[1];

                # condition
                if ($this->_getConditionResult ($tag_properties, $vars)) {
                    $result_pattern = $if_string;
                }

                # replace
                $string = preg_replace ("`".$replace_pattern."`s", $result_pattern, $string, 1);
            }

            # INCLUDE
            if ($tag_type == self::_INCLUDE) {
                $replace_pattern = $tag_mask;
                $include_file = $this->_getProperty ('file', $tag_properties);
                $var_type = substr($include_file, 0, 1);
                if ($var_type == '#' || $var_type == '$') {
                    $var_name = substr($include_file, 1, strlen($include_file)-1);
                    $include_file = $this->_getValue (array($include_file, $var_type, $var_name), $vars);
                    $result_pattern = $this->_loadTemplate($include_file);
                } else {
                    $result_pattern = $this->_loadTemplate($include_file);
                }

                # replace
                $string = str_replace ($tag_mask, $result_pattern, $string);
            }

            # SET
            if ($tag_type == self::_SET) {
                $replace_pattern = $tag_mask;
                $var_name = $this->_getProperty ('var', $tag_properties);
                $var_type = substr ($var_name, 0, 1);
                $var_name = substr ($var_name, 1, strlen ($var_name)-1);
                $var_value = $this->_getProperty ('value', $tag_properties);
                $var_concat = $this->_getProperty ('concat', $tag_properties);

                if ($var_value) {
                    if (preg_match ("`".self::_VAR.self::_MODIFIER."`", $var_value, $is_var)) {
                        $var_value = $this->_getValue ($is_var, $vars);
                    }
                } else if ($var_concat) {
                    $split = explode (',', $var_concat);
                    $var_value = '';
                    foreach ($split as $value) {
                        if (preg_match ("`".self::_VAR.self::_MODIFIER."`", $value, $is_var)) {
                            $var_value .= $this->_getValue ($is_var, $vars);
                        } else {
                            $var_value .= $value;
                        }
                    }
                }

                # set value
                if ($var_type == '#') {
                    $this->_setValue($var_name, $var_value, $vars);
                } else if ($var_type == '$') {
                    $this->_setValue($var_name, $var_value, $this->_vars);
                }

                # replace
                $string = str_replace ($tag_mask, '', $string);
            }
        }


        //return $string;

        $string = $this->_replaceForJS($string, $vars);
        return $string;
    }

    // render HTML templates
    protected function _parse($string, $vars=null)
    {
        $old_string = $string;
        if (null === $vars) {
            $vars =& $this->_vars;
        }

        # search loop, include or if statement
        while (preg_match ("`".self::_OPEN."(".self::_LOOP."|".self::_FOREACH."|".self::_INCLUDE."|".self::_IF."|".self::_SET."|".self::_TRANSLATE.")([^\{]*?)".self::_CLOSE."`", $string, $preg)) {
            $result_pattern = '';
            $tag_mask = $preg[0];
            $tag_type = $preg[1];
            $tag_properties = $preg[2];

            # LOOP & FOREACH
            if ($tag_type == self::_LOOP || $tag_type == self::_FOREACH ) {
                $loop_name = $this->_getProperty ('name', $tag_properties);
                $loop_as = $this->_getProperty ('value', $tag_properties);
                $loop_key = $this->_getProperty ('key', $tag_properties);
                $loop_row_size = (int)$this->_getProperty ('row_size', $tag_properties);
                $reverse = $this->_getProperty ('reverse', $tag_properties);

                # define loop string and replace pattern
                $loop_replace_pattern = $this->_getEncapsuledPattern ($string, $tag_type, $tag_mask);
                preg_match ("`".$loop_replace_pattern."`s", $string, $loops);
                $loop_string = $loops[1];

                # loop on array
                preg_match ("`^".self::_VAR."`", $loop_name, $preg);
                $loop_array = $this->_getValue ($preg, $vars);

                if($reverse) {
                    $loop_array = array_reverse ($loop_array);
                }

                if(is_array($loop_array)) {
                    $count = count ($loop_array);
                    $loop = array ();
                    $i = 0;
                    foreach ($loop_array as $key => $line) {
						# meta values
                        if (is_array($line)) {
                            # is pair ?
                            $line[self::_PAIR] = ($i+1)%2 ;

                            # position in list
                            $i < $count-1 ? $line[self::_LAST] = false : $line[self::_LAST] = true;
                            $i === 0 ? $line[self::_FIRST] = true : $line[self::_FIRST] = false;

                            # rows and columns infos
                            if ($loop_row_size) {
                                # col position
                                (($i+1) % $loop_row_size) ? $line[self::_LAST_COL] = false : $line[self::_LAST_COL] = true;
                                (($i+1) % $loop_row_size === 1) ? $line[self::_FIRST_COL] = true : $line[self::_FIRST_COL] = false;

                                # row position
                                ($i < $loop_row_size) ? $line[self::_FIRST_ROW] = true : $line[self::_FIRST_ROW] = false;
                                $modulo = $count % $loop_row_size;
                                if(($modulo && $i>=$count-$modulo) || (!$modulo && $i>=$count-$loop_row_size)) {
                                    $line[self::_LAST_ROW] = true;
                                } else {
                                    $line[self::_LAST_ROW] = false;
                                }
                            }
                        }

                        # switch case
                        if($tag_type == self::_LOOP) {
                            $loop_key ? $line[$loop_key] = $i : $line[self::_COUNTER] = $i;
                            $loop[] = $this->_parse($loop_string, $line);
                        } else {
                            //$loop_key ? $vars[$loop_key] = $i : $vars[self::_COUNTER] = $i;
                            $vars[self::_COUNTER] = $i;
                            $vars[$loop_key] = $key;
                            $vars[$loop_as] = $line;
                            $loop[] = $this->_parse($loop_string, $vars);
                        }
                        $i++;
                    }

                    $result_pattern .= implode ($loop, '');
                }

                # replace
                $string = preg_replace ("`".$loop_replace_pattern."`s", $result_pattern, $string, 1);
            }

            # IF
            if ($tag_type == self::_IF) {
                $replace_pattern = $this->_getEncapsuledPattern ($string, self::_IF, $tag_mask);
                preg_match ("`".$replace_pattern."`s", $string, $result);
                $if_string = $result[1];

                # condition
                if ($this->_getConditionResult ($tag_properties, $vars)) {
                    $result_pattern = $if_string;
                }

                # replace
                $string = preg_replace ("`".$replace_pattern."`s", $result_pattern, $string, 1);
            }

            # INCLUDE
            if ($tag_type == self::_INCLUDE) {
                $replace_pattern = $tag_mask;
                $include_file = $this->_getProperty ('file', $tag_properties);
                $var_type = substr ($include_file, 0, 1);
                if ($var_type == '#' || $var_type == '$') {
                    $var_name = substr ($include_file, 1, strlen($include_file)-1);
                    $include_file = $this->_getValue (array ($include_file, $var_type, $var_name), $vars);
                    $result_pattern = $this->_loadTemplate ($include_file);
                } else {
                    $result_pattern = $this->_loadTemplate ($include_file);
                }

                # replace
                $string = str_replace ($tag_mask, $result_pattern, $string);
            }

            # TRANSLATE
            if ($tag_type == self::_TRANSLATE) {
                $replace_pattern = $tag_mask;
                $key = $this->_getProperty ('key', $tag_properties);
                $into = $this->_getProperty ('into', $tag_properties);

                $default = $this->_getProperty ('default', $tag_properties);
                if (preg_match ("`".self::_VAR.self::_MODIFIER."`", $default, $is_var)) {
                    $default = $this->_getValue ($is_var, $vars);
                }

                $key_list = explode (',', $key);
                foreach ($key_list as $i=>$key) {
                    $var_type = $this->_getVarType($key);
                    if ($var_type == '#' || $var_type == '$') {
                        $key_list[$i] = $this->_getValue (array ($key, $var_type, $this->_getVarName($key)), $vars);
                    }
                }

                if ($this->_i18n !== null) {
                    $key = implode ('', $key_list);
                    $tmp = $this->_i18n->translate ($key);
                    if ($tmp == $key) {
                        $result_pattern = $default;
                    } else {
                        $result_pattern = $tmp;
                    }
                } else {
                    $result_pattern = $default;
                }

                # replace
                if ($into) {
                    $var_type = $this->_getVarType($into);
                    $var_name = $this->_getVarName($into);
                    if ($var_type == '#') {
                        $this->_setValue($var_name, $result_pattern, $vars);
                    } else if ($var_type == '$') {
                        $this->_setValue($var_name, $result_pattern, $this->_vars);
                    }
                    $result_pattern = '';
                }

                $string = str_replace ($tag_mask, $result_pattern, $string);
            }

            # SET
            if ($tag_type == self::_SET) {
                $replace_pattern = $tag_mask;
                $var_name = $this->_getProperty ('var', $tag_properties);
                $var_type = substr ($var_name, 0, 1);
                $var_name = substr ($var_name, 1, strlen ($var_name)-1);
                $var_value = $this->_getProperty ('value', $tag_properties);
                $var_concat = $this->_getProperty ('concat', $tag_properties);

                if ($var_value) {
                    if (preg_match ("`".self::_VAR.self::_MODIFIER."`", $var_value, $is_var)) {
                        $var_value = $this->_getValue ($is_var, $vars);
                    }
                } else if ($var_concat) {
                    $split = explode (',', $var_concat);
                    $var_value = '';
                    foreach ($split as $value) {
                        if (preg_match ("`".self::_VAR.self::_MODIFIER."`", $value, $is_var)) {
                            $var_value .= $this->_getValue ($is_var, $vars);
                        } else {
                            $var_value .= $value;
                        }
                    }
                }

                # set value
                if ($var_type == '#') {
                    $this->_setValue($var_name, $var_value, $vars);
                } else if ($var_type == '$') {
                    $this->_setValue($var_name, $var_value, $this->_vars);
                }

                # replace
                $string = str_replace ($tag_mask, '', $string);
            }
        }

        $string = $this->_replace ($string, $vars);
        return $string;
    }

    protected function _getVarType ($var_name)
    {
        return substr ($var_name, 0, 1);
    }

    protected function _getVarName ($var_name)
    {
        return substr ($var_name, 1, strlen ($var_name)-1);
    }

    protected function _replace ($string, $vars)
    {
        while(preg_match ("`".self::_OPEN.self::_VAR.self::_MODIFIER.self::_CLOSE."`", $string, $reg)) {
            $value = $this->_getValue ($reg, $vars);
            $string = str_replace ($reg[0], strval ($value), $string);
        }
        return $string;
    }

    protected function _replaceForJS($string, $vars)
    {
        while(preg_match ("`".self::_OPEN.self::_VAR.self::_MODIFIER.self::_CLOSE."`", $string, $reg)) {
            //$value = $this->_getValue ($reg, $vars);
            //$value = var_export($reg, false);
            $value = $this->_convertVarToJS($reg);
            $string = str_replace($reg[0], strval ($value), $string);
        }
        return $string;
    }

    protected function _convertVarToJS($var) {
        if(isset($var[2])) {
            //return '#{element'.ucfirst($var[2]).'}';
            return '#{'.$var[2].'}';
        } else {
            return $var[0];
        }
    }

    protected function _getConditionResult ($condition, $vars)
    {
        preg_match_all ("`".self::_VAR."`", $condition, $preg_list);
        $count = count($preg_list[0]);
        for ($i=0; $i<$count; $i++) {
            $preg = array ($preg_list[0][$i], $preg_list[1][$i], $preg_list[2][$i]);
            $value[$i] = $this->_getValue ($preg, $vars);
            $condition = substr_replace ($condition, '$value['.$i.']', strpos ($condition, $preg[0]), strlen ($preg[0]));
        }

        $eval_string = sprintf ("if (%s){\$result=true;} else {\$result=false;}", $condition);
        @eval ($eval_string);
        return $result;
    }

    protected function _setValue ($var_string, $value, &$vars)
    {
        # array
        $array = explode('.', $var_string);
        $count_array = count ($array);
        if ($count_array > 1) {
            $string_eval = "\$vars['".$array[0]."']";
            for ($i=1; $i<$count_array; $i++) {
                $string_eval .= "['".$array[$i]."']";
            }
            $string_eval .= " = \$value;";
            eval($string_eval);
        } else {
            $vars[$var_string] = $value;
        }
    }

    protected function _getValue ($reg, $vars=null)
    {
        if ($reg[1] == '$') {
            $vars = $this->_vars;
        } else if (null === $vars) {
            $vars =& $this->_vars;
        }

        $key = $reg[2];
        if (isset ($reg[3])) {
            $modifier = $reg[3];
        }
        $value = '';

        $split = explode (".", $key);
        $split_count = count ($split);
        # array
        if ($split_count > 1 && isset ($vars[$split[0]]) && is_array ($vars[$split[0]])) {
            $value = $vars[$split[0]];
            for ($i=1; $i<$split_count; $i++) {
                if (isset ($value[$split[$i]])) {
                    $value = $value[$split[$i]];
                } else {
                    $value = null;
                }
            }
        }
        # object
        else if ($split_count == 2 && isset ($vars[$split[0]]) && is_object($vars[$split[0]])) {
            $object = $vars[$split[0]];
            $key = $split[1];
            if (isset ($object->$key)) {
                $value = $object->$key;
            }
        }
        # regular var
        if (isset ($vars[$key])) {
            $value = $vars[$key];
        } else
        # constant
        if (defined ($key)) {
            $value = constant ($key);
        }

        # modifier
        if (isset ($modifier) && $modifier) {
            $value = $this->_modifier ($modifier, $value, $vars);
        }

        return $value;
    }

    protected function _getEncapsuledPattern ($string, $tag_name, $mask)
    {
        $start = $end = 0;
        $string = strstr ($string, $mask);

        while (preg_match ("`".self::_OPEN."(/{0,1}".$tag_name.")(.*?)".self::_CLOSE."`", $string, $preg)) {
            $string = strstr ($string, $preg[0]);
            if ($preg[1] == $tag_name) {
                $start++;
            } else {
                $end++;
            }
            if ($start == $end) {
                break;
            }
            $pos = strlen ($preg[0]);
            $string = substr ($string, $pos, strlen ($string) - $pos);
        }

        # make pattern
        $reg = addcslashes($mask, '?.+*()|[]$^').'(';
        for ($j=0; $j<$end; $j++) {
            if ($j < $end-1) {
                $reg .= ".*?".self::_OPEN."/".$tag_name.self::_CLOSE;
            } else {
                $reg .= ".*?)".self::_OPEN."/".$tag_name.self::_CLOSE;
            }
        }

        return $reg;
    }

    protected function _getProperty ($key, $string)
    {
        if (preg_match ("` ".$key."=\"(.*?)\"`", $string, $properties)) {
            return $properties[1];
        } else {
            return false;
        }
    }

    protected function _loadTemplate ($template)
    {

        foreach($this->_template_path as $path) {
            if ($template && file_exists ("$path/$template")) {
                $file = "$path/$template";
                return $this->_loadFile ($file);
            }
        }
        throw new Exception ("Template '$template' not found !");
        return false;
    }

    protected function _loadFile ($file)
    {
        if ($file && is_file ($file)) {
            $string = file_get_contents ($file);
            return $string;
        }
    }

    protected function _modifier ($string, $value, $vars=null)
    {
        if (null == $vars) {
            $vars = $this->_vars;
        }
        $commands = explode ('|', $string);

        foreach ($commands as $command) {
            $arguments = array ();

            # define list of arguments
            if (strpos ($command, ":")) {
                $strings = array ();

                if (strpos ($command, "'")) {
                    $i = 0;
                    while (preg_match ("`'([^}]*?)'`", $command, $preg)) {
                        $strings[] = $preg[1];
                        $command = str_replace ($preg[0], '{'.$i.'}', $command);
                    }
                }

                $split = explode (':', $command);
                if (count ($split) > 1) {
                    $command = $split[0];
                    $count_split = count ($split);
                    for ($i=1; $i<$count_split; $i++) {
                        if (preg_match ("'".self::_VAR."'", $split[$i], $is_var)) {
                            $arguments[] = $this->_getValue ($is_var, $vars);
                        } else if (preg_match ("`\{([0-9])\}`", $split[$i], $is_string)) {
                            $arguments[] = $strings[$is_string[1]];
                        } else {
                            $arguments[] = $split[$i];
                        }
                    }
                }

            }


            switch ($command) {
                case 'default':
                    if(!$value) {
                        $value = $arguments[0];
                    }
                break;

                case 'max':
                    $value = min($value, $arguments[0]);
                break;

                case 'min':
                    $value = max($value, $arguments[0]);
                break;

                case 'format_text':
                    // trim text
                    $value = trim($value);

                    // ignore non utf8 chars
                    //$value = iconv('UTF-8', 'UTF-8//IGNORE', $value);

                    // convert empty titles into line breaks
                    $value = str_replace('<h1></h1>', '<br />', $value);
                    $value = str_replace('<h2></h2>', '<br />', $value);
                    $value = str_replace('<h3></h3>', '<br />', $value);
                    // convert empty paragraphs to line breaks
                    $value = str_replace('<p></p>', '<br />', $value);
                break;

                case 'sanitize':
                    // prevent XSS
                    $value = preg_replace( '#< *script(?:(?!< */ *script *>).)*< */ *script *>#isU', '', $value );
                    if(strlen($value) > 0) {
                        $value = preg_replace("#<([^><]+?)([^a-z_\-]on\w*|xmlns)(\s*=\s*[^><]*)([><]*)#i", "<\\1\\4", $value);
                    }
                break;

                case 'ratio':
                    $image = $value;
                    $ratio = 1.0;
                    if(isset($image['width']) && isset($image['height'])) {
                        if((int)$image['height'] <= 0) {
                            $image['height'] = 1;
                        }
                        $ratio = round(($image['width'] / $image['height']) * 1000) / 1000;
                    }
                    $value = number_format($ratio, 3, '.', '');
                break;
                case 'format_line_height':
                    $value = $this->_css_format_line_height($value);
                break;

                case 'format_font_size_value':
                    $value = $this->_css_format_font_size_value($value);
                break;

                case 'inc':
                    if (!isset ($arguments[0])) {
                        $value++;
                    } else {
                        $value = $value + $arguments[0];
                    }
                break;

                case 'mult':
                    if ($arguments) {
                        $value = (float)$value * (float)$arguments[0];
                    }
                break;

                case 'fract':
                    if ($arguments) {
                        $value = $value / $arguments[0];
                    }
                break;

                case 'dec':
                    if (!isset ($arguments[0])) {
                        $value --;
                    } else {
                        $value = $value - $arguments[0];
                    }
                break;

                case 'round':
                    $value = round ($value);
                break;

                case 'strlen':
                    if ($arguments[0]) {
                            $value = strlen ($value, $arguments[0]);
                    } else {
                            $value = strlen ($value);
                    }
                break;

                case 'count':
                    $value = count ($value);
                break;

                case 'cut_string':
                    if ($arguments) {
                        $value = substr($value, 0, $arguments[0]);
                    }
                break;

                case 'substr':
                    if (count($arguments) == 2) {
                        $value = substr($value, $arguments[0], $arguments[1]);
                    }
                break;

                case 'width':
                    if (isset ($arguments[0]) && $arguments[0]) {
                            $data_path = $arguments[0];
                    } else {
                            $data_path = $this->_data_path;
                    }

                    $value = $data_path.'/'.$value;
                    if ($value && file_exists ($value)) {
                        $size = @getimagesize ($value);
                        $value = $size[0];
                    } else {
                        $value = '0';
                    }
                break;

                case 'height':
                    if (isset ($arguments[0]) && $arguments[0]) {
                            $data_path = $arguments[0];
                    } else {
                            $data_path = $this->_data_path;
                    }

                    $value = $data_path.'/'.$value;
                    if ($value && file_exists ($value)) {
                        $size = @getimagesize ($value);
                        $value = $size[1];
                    } else {
                        $value = '0';
                    }
                break;

                case 'colorOrNil':
                    if($value === 'transparent') {
                        $value = '';
                    }
                break;

                case 'includes':
                    return (bool)(is_array($value) && in_array($arguments[0], $value));
                break;

                case 'color':
                    $value = $this->_css_format_color($value);
                break;

                case 'size':
                    if (isset ($arguments[0]) && $arguments[0]) {
                            $data_path = $arguments[0];
                    } else {
                            $data_path = $this->_data_path;
                    }

                    $value = $data_path.'/'.$value;
                    if ($value && file_exists ($value)) {
                        $value = round (filesize ($value));
                    } else {
                        $value = '0';
                    }
                break;

                case 'ucfirst':
                    $value = ucfirst ($value);
                break;

                case 'upper':
                    $value = strtoupper($value);
                break;

                case 'lower':
                    $value = strtolower($value);
                break;

                case 'year':
                    if (preg_match ("'([0-9]{4})-[0-9]{2}-[0-9]{2}'", $value, $preg)) {
                        $preg[1] != '0000' ? $value = $preg[1] : $value = '';
                    }
                    if (preg_match ("'^([0-9]{4})[0-9]{4}'", $value, $preg)) {
                        $preg[1] != '0000' ? $value = $preg[1] : $value = '';
                    }
                break;

                case 'month':
                    if (preg_match ("'[0-9]{4}-([0-9]{2})-[0-9]{2}'", $value, $preg)) {
                        $preg[1] != '00' ? $value = $preg[1] : $value = '';
                    }
                    if (preg_match ("'^[0-9]{4}([0-9]{2})[0-9]{2}'", $value, $preg)) {
                        $preg[1] != '00' ? $value = $preg[1] : $value = '';
                    }
                break;

                case 'day':
                    if (preg_match ("'[0-9]{4}-[0-9]{2}-([0-9]{2})'", $value, $preg)) {
                        $preg[1] != '00' ? $value = $preg[1] : $value = '';
                    }
                    if (preg_match ("'^[0-9]{6}([0-9]{2})'", $value, $preg)) {
                        $preg[1] != '00' ? $value = $preg[1] : $value = '';
                    }
                break;

                case 'hour':
                    if (preg_match ("'([0-9]{2}):[0-9]{2}:[0-9]{2}'", $value, $preg)) {
                        $value = $preg[1];
                    }
                    if (preg_match ("'([0-9]{2})[0-9]{4}$'", $value, $preg)) {
                        $value = $preg[1];
                    }
                break;

                case 'minute':
                    if (preg_match ("'[0-9]{2}:([0-9]{2}):[0-9]{2}'", $value, $preg)) {
                        $value = $preg[1];
                    }
                    if (preg_match ("'[0-9]{2}([0-9]{2})[0-9]{2}$'", $value, $preg)) {
                        $value = $preg[1];
                    }
                break;

                case 'second':
                    if (preg_match("'[0-9]{2}:[0-9]{2}:([0-9]{2})'", $value, $preg)) {
                        $value = $preg[1];
                    }
                    if (preg_match("'[0-9]{4}([0-9]{2})$'", $value, $preg)) {
                        $value = $preg[1];
                    }
                break;

                case 'nl2br':
                    $value = nl2br($value);
                break;

                case 'replace_nl_br':
                    $value = str_replace('\n', '<br />', $value);
                break;

                case 'delnl':
                    $value = str_replace ('\n', '', $value);
                    $value = str_replace ('\r', '', $value);
                break;

                case 'addslashes':
                    $value = addslashes ($value);
                break;

                case 'stripslashes':
                    $value = stripslashes ($value);
                break;

                case 'trim':
                    $value = trim ($value);
                break;

                case 'nbsp':
                    $value = str_replace (' ', '&nbsp;', $value);
                break;

                case 'strip_tags':
                    if ($arguments) {
                        $value = strip_tags ($value, $arguments[0]);
                    } else {
                        $value = strip_tags ($value);
                    }
                break;
                case 'htmlentities':
                    $result = '';
                    $length = strlen($value);
                    for ($i = 0; $i < $length; $i++) {
                        $char = $value[$i];
                        $ascii = ord($char);
                        if ($ascii < 128) {
                            // one-byte character
                            $result .= htmlentities($char);
                        } else if ($ascii < 192) {
                            // non-utf8 character or not a start byte
                        } else if ($ascii < 224) {
                            // two-byte character
                            $result .= htmlentities(substr($value, $i, 2), ENT_QUOTES, 'UTF-8');
                            $i++;
                        } else if ($ascii < 240) {
                            // three-byte character
                            $ascii1 = ord($value[$i+1]);
                            $ascii2 = ord($value[$i+2]);
                            $unicode = (15 & $ascii) * 4096 +
                                    (63 & $ascii1) * 64 +
                                    (63 & $ascii2);
                            $result .= "&#$unicode;";
                            $i += 2;
                        } else if ($ascii < 248) {
                            // four-byte character
                            $ascii1 = ord($value[$i+1]);
                            $ascii2 = ord($value[$i+2]);
                            $ascii3 = ord($value[$i+3]);
                            $unicode = (15 & $ascii) * 262144 +
                                    (63 & $ascii1) * 4096 +
                                    (63 & $ascii2) * 64 +
                                    (63 & $ascii3);
                            $result .= "&#$unicode;";
                            $i += 3;
                        }
                    }
                    $value = $result;
                break;

                case 'index':
                    $index = (int)$arguments[0];
                    if(isset($value[$index])) {
                        $value = $value[$index];
                    } else {
                        $value = '';
                    }
                break;

                case 'html_value':
                    $value = str_replace('"', '&#34;', $value);
                    $value = str_replace("'", '&#39;', $value);
                    $value = str_replace('<', '&lt;', $value);
                    $value = str_replace('>', '&gt;', $value);
                break;

                case 'html_entity_decode':
                    $value = html_entity_decode ($value, ENT_QUOTES);
                break;

                case 'json_decode':
                    $value = json_decode($value);
                break;

                case 'json_encode':
                    $value = json_encode($value);
                break;

                case 'maybe_serialize':
                    if(is_array($value) or is_object($value)) {
                        // make sure we clean up the useless parser variables
                        if(array_key_exists(self::_PAIR, $value)) unset($value[self::_PAIR]);
                        if(array_key_exists(self::_FIRST, $value)) unset($value[self::_FIRST]);
                        if(array_key_exists(self::_LAST, $value)) unset($value[self::_LAST]);
                        // serialize value
                        $value = serialize($value);
                    }
                break;

                case 'base64_decode':
                    if($this->is_valid_base64($value) === true) {
                        $value = base64_decode($value);
                    }
                break;

                case 'base64_encode':
                    $value = base64_encode($value);
                break;

                case 'join':
                    if(isset($arguments[0])) {
                        $value = join($value, $arguments[0]);
                    }
                break;

                case 'wordwrap' :
                    if ($arguments) {
                        $value = wordwrap ($value, $arguments[0]);
                    }
                break;

                case 'img_size_limit':
                    if (isset($arguments[2]) && $arguments[2]) {
                            $data_path = $arguments[2];
                    } else {
                            $data_path = $this->_data_path;
                    }

                    if (null !== $data_path && $value != '' && file_exists ($data_path.'/'.$value)) {

                        $size = @getimagesize ($data_path.'/'.$value);
                        $new_width = round($size[0]);
                        $new_height = round($size[1]);

                        # constrain width only
                        if (isset ($arguments[0]) && (!isset ($arguments[1]) || $arguments[1]=='0') && $size[0] > $arguments[0]) {
                            $new_width = $arguments[0];
                            $new_height = round(($new_width * $size[1]) / $size[0]);
                        }

                        # constrain height only
                        if (isset ($arguments[0]) && $arguments[0]=='0' && isset ($arguments[1]) && $arguments[1]!='0' && $size[1] > $arguments[1]) {
                            $new_height = $arguments[1];
                            $new_width = round(($new_height * $size[0]) / $size[1]);
                        }

                        # constrain both
                        if (isset ($arguments[0]) && isset ($arguments[1])) {
                            if ($size[0] > $arguments[0] && $arguments[0]!='0') {
                                $new_width = $arguments[0];
                                $new_height = round(($new_width * $size[1]) / $size[0]);
                            }
                            if ($new_height > $arguments[1] && $arguments[1]!='0') {
                                $new_height = $arguments[1];
                                $new_width = round(($new_height * $size[0]) / $size[1]);
                            }
                        }

                        if (isset ($new_height) && isset ($new_width)) {
                            $value = 'width="'.$new_width.'" height="'.$new_height.'"';
                        }
                    }
                break;

                case 'highlight':
                    if($arguments[0]) {
                        $value = preg_replace ("{(".$arguments[0].")}si", "<".$arguments[1]." class='".$arguments[2]."'>\\1</".$arguments[1].">", $value);
                    }
                break;

                case 'password':
                    $value = preg_replace ("'.'", '*', $value);
                break;

                case 'get_extension':
                    $value = strtolower (pathinfo ($value, PATHINFO_EXTENSION));
                break;

                case 'utf8_encode':
                    $value = utf8_encode($value);
                break;

                case 'url_encode':
                    $value = urlencode ($value);
                break;

                case 'url_decode':
                    $value = urldecode($value);
                break;

                case 'number_format':
                    if ($arguments) {
                        $value = number_format ($value, $arguments[0], $arguments[1], $arguments[2]);
                    }
                break;

                case 'int':
                    $value = (int) $value;
                break;

                case 'format_date':
                    if ($value && $arguments) {
                        $value = $this->_formatDate ($value, $arguments[0]);
                    }
                break;

                case 'mime_type':
                    if ($value && file_exists ($this->_data_path.'/'.$value)) {
                        $value = mime_content_type ($this->_data_path.'/'.$value);
                    }
                break;

                case 'modulo':
                    $value = $value%$arguments['0'];
                break;

                case 'trim_br':
                        $value = str_replace(array ("<br>", "<br/>", "<br />"), "\n", $value);
                        $value = nl2br (trim ($value));
                break;

                case 'implode':
                        $value = implode ($arguments[0], $value);
                break;
            }
        }
        return $value;
    }

    // This function checks if the $data passed is a valid base64 string,
    // BEWARE that it does not ensure that the string is actually base64 encoded
    function is_valid_base64($data) {
        // if the string length is not a multiple of 4, it's not a base64 encoded string
        if(strlen($data) % 4 !== 0) return false;

        // check the characters contained within the string
        if(preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
            return true;
        } else {
            return false;
        }
    }

    /* misc */
    protected function _formatDate ($value, $format)
    {
        $date = $this->_getDate ($value);
        return strftime ($format, $date->format('U'));
    }

    protected function _getDate ($value)
    {
        setlocale (LC_TIME, "$this->_lc_time.utf8");
        return new DateTime ($value);
    }

    protected static function _css_format_font_size_value($value) {
        if(strpos($value, 'em') !== FALSE) {
            // if number value, convert to percent
            $percentage = ((float)$value * 100);
            $value = $percentage.'%';
        } else if(strpos($value, '%') === FALSE) {
            $value = $value.'px';
        }
        return $value;
    }

    protected static function _css_format_color($value) {
        if($value !== 'transparent' && $value !== '') {
            $value = '#'.$value;
        }
        return $value;
    }

    protected static function _css_format_line_height($value) {
        // if value in px, don't modify
        if($value !== '0' && strpos($value, 'px') === FALSE && strpos($value, '%') === FALSE) {
            // if number value, convert to percent
            $percentage = ((float)$value * 100);
            $value = $percentage.'%';
        }
        return $value;
    }
}
