<?php
defined( 'WYSIJA' ) or die( 'Restricted access' );
class WYSIJA_view_front_widget_nl extends WYSIJA_view_front {

	function __construct(){
		$this->model=WYSIJA::get('user','model');
	}

	function wrap( $content  ) {
		$attributes = array();

		if ( function_exists( 'is_rtl' ) && is_rtl() ){
			$attributes[] = 'dir="rtl"';
		}

		if ( $lang = get_bloginfo( 'language' ) ) {
			if ( get_option( 'html_type' ) == 'text/html' ){
				$attributes[] = "lang=\"$lang\"";
			}
		}

		/**
		 * Filter the language attributes for display in the html tag.
		 *
		 * @since 2.6.7.1
		 *
		 * @param string $output A space-separated list of language attributes.
		 */
		$language_attributes = apply_filters( 'language_attributes', implode( ' ', $attributes ) );


		// We need to find a way to remove this ob_start...
		ob_start();
		wp_print_scripts('jquery');
		wp_print_styles('validate-engine-css');
		if(isset($_REQUEST['external_site'])){
			$iframeJsUrl=$iframeCssUrl=false;
			//check if an iframe.css file exists in the site uploads/wysija/css/iframe.css or in MS blogs.dir/5/files/wysija/css/iframe.css
			if(file_exists(WYSIJA_UPLOADS_DIR.'css'.DS.'iframe.css')){
				$iframeCssUrl=WYSIJA_UPLOADS_URL.'css/iframe.css';
			}else{
			   //if we are in a multisite check to see if there is a file defined in the main site
				if(is_multisite() && file_exists(WYSIJA_UPLOADS_MS_DIR.'css'.DS.'iframe.css')){
					$iframeCssUrl=WYSIJA_UPLOADS_MS_URL.'css/iframe.css';
				}
			}
			$iframeCssUrl = apply_filters('wysija_iframe_css_url', $iframeCssUrl);

			//check if an iframe.js file exists in the site uploads/wysija/js/iframe.js or in MS blogs.dir/5/files/wysija/js/iframe.js
			if(file_exists(WYSIJA_UPLOADS_DIR.'js'.DS.'iframe.js')){
				$iframeJsUrl=WYSIJA_UPLOADS_URL.'js/iframe.js';
			}else{
			   //if we are in a multisite check to see if there is a file defined in the main site
				if(is_multisite() && file_exists(WYSIJA_UPLOADS_MS_DIR.'js'.DS.'iframe.js')){
					$iframeJsUrl=WYSIJA_UPLOADS_MS_URL.'js/iframe.js';
				}
			}
			$iframeJsUrl = apply_filters('wysija_iframe_js_url', $iframeJsUrl);

			//if an iframe file has been detected then load it
			if($iframeCssUrl){
				wp_register_style('wysija-iframe-css',$iframeCssUrl,array(),WYSIJA::get_version());
				wp_print_styles('wysija-iframe-css');
			}

			//if an iframe js file has been detected then load it
			if($iframeJsUrl){
				wp_register_style('wysija-iframe-js',$iframeJsUrl,array(),WYSIJA::get_version());
				wp_print_styles('wysija-iframe-js');
			}
		}
		wp_print_scripts('wysija-validator-lang');
		wp_print_scripts('wysija-validator');
		wp_print_scripts('wysija-front-subscribers');
		wp_print_scripts('jquery-ui-datepicker');

		$helperUser=WYSIJA::get('user','helper');
		if($helperUser->isCaptchaEnabled()) {
			wp_print_scripts( 'wysija-recaptcha' );
		}

		$scripts_html .= ob_get_contents();
		ob_end_clean();

		$html =
		'<!DOCTYPE html>' .
		'<!--[if lt IE 7]> <html class="no-js ie6 oldie" ' . $language_attributes . '> <![endif]-->' .
		'<!--[if IE 7]>    <html class="no-js ie7 oldie" ' . $language_attributes . '> <![endif]-->' .
		'<!--[if IE 8]>    <html class="no-js ie8 oldie" ' . $language_attributes . '> <![endif]-->' .
		'<!--[if gt IE 8]><!--> <html ' . $language_attributes . '> <!--<![endif]-->' .
		'<head>' .
			'<meta name="robots" content="noindex, nofollow">' .
			'<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">' .
			'<meta charset="' . get_bloginfo( 'charset' ) . '" />' .
			'<title>' . __( 'MailPoet Subscription Form', WYSIJA ) . '</title>' .
			$scripts_html .
		'</head>' .
		'<body>' .
			'<div class="' . esc_attr( implode( ' ', array( 'wysija-frame', ( isset( $_REQUEST['external_site'] ) ? '' : 'iframe-hidden' ) ) ) ) . '" >' .
				$content .
			'</div>' .
		'</body>' .
			'</html>';

		return $html;
	}

	function display($title='',$params,$echo=true,$iframe=false){

		$helperUser=WYSIJA::get('user','helper');

		if ( ! $iframe ){
			wp_enqueue_script( 'wysija-validator-lang' );
			wp_enqueue_script( 'wysija-validator' );
			wp_enqueue_script( 'wysija-front-subscribers' );

			if($helperUser->isCaptchaEnabled()) {
				wp_enqueue_script( 'wysija-recaptcha' );
			}

			wp_enqueue_style( 'validate-engine-css' );
		}
		$data = '';
		$label_email = '';
		$form_id_real = 'form-'.$params['id_form'];

		$data.= $title;
		$list_fields_hidden=$list_fields='';
		$disabled_submit=$msg_success_preview='';

		// set specific class depending on form type (shortcode, iframe, html)
		$extra_class = '';
		if(isset($params['form_type'])) {
			$extra_class = ' '.$params['form_type'].'_wysija';
		}

		$data.='<div class="widget_wysija_cont'.$extra_class.'">';

		//if data has been posted the classique php/HTML way we display the result straight in good old HTML
		if(isset($_POST['wysija']['user']['email']) && isset($_POST['formid']) && $form_id_real==$_POST['formid']){
			$data.= str_replace ('class="wysija-msg', 'id="msg-'.$form_id_real.'" class="wysija-msg', $this->messages());
		}else{
			$data.='<div id="msg-'.$form_id_real.'" class="wysija-msg ajax">'.$msg_success_preview.'</div>';
		}

		// A form built with the form editor has been selected
		if(isset($params['form']) && (int)$params['form'] > 0) {

			// get form data
			$model_forms = WYSIJA::get('forms', 'model');
			$form = $model_forms->getOne(array('form_id' => (int)$params['form']));

			// if the form exists
			if(!empty($form)) {
				// load form data into form engine
				$helper_form_engine = WYSIJA::get('form_engine', 'helper');
				$helper_form_engine->set_data($form['data'], true);

				// get html rendering of form
				$form_html = $helper_form_engine->render_web();

				// replace shortcodes
				if(strpos($form_html, '[total_subscribers]') !== FALSE) {
					$model_config = WYSIJA::get('config', 'model');
					// replace total subscribers shortcode by actual value
					$form_html = str_replace('[total_subscribers]', number_format($model_config->getValue('total_subscribers'), 0, '.', ' '), $form_html);
				}

				// IMPORTANT: we remove the ones that could break our subscription form
				// These resolve the conflict with Ultimate Shortcodes
				remove_shortcode('user');
				remove_shortcode('user_list');
				remove_shortcode('list_ids');
				remove_shortcode('list_id');
				remove_shortcode('firstname');
				remove_shortcode('lastname');
				remove_shortcode('email');
				remove_shortcode('custom');
				remove_shortcode('required');

				// These resolve the conflict with Ultimate TinyMCE
				remove_shortcode('field');

				// interpret shortcodes
				$form_html = do_shortcode($form_html);

				$data .= '<form id="'.$form_id_real.'" method="post" action="#wysija" class="widget_wysija'.$extra_class.'">';
				$data .= $form_html;
				$data .= '</form>';
			}
		} else {

			// What is included in this Else condition is only for retrocompatibility we should move it maybe to another file at some point as deprecated

			$data .= '<form id="'.$form_id_real.'" method="post" action="#wysija" class="widget_wysija form-valid-sub">';

			if(isset($params['instruction']) && $params['instruction'])   {
				if(strpos($params['instruction'], '[total_subscribers') !== false){
					$modelC=WYSIJA::get('config','model');
					$totalsubscribers=  str_replace(',', ' ', number_format($modelC->getValue('total_subscribers')));

					$params['instruction']=str_replace('[total_subscribers]', $totalsubscribers, $params['instruction']);
				}
				$data.='<p class="wysija-instruct">'.$params['instruction'].'</p>';
			}


			if(isset($params['autoregister']) && $params['autoregister']=='auto_register'){
				$list_fields='<div class="wysija_lists">';
				$i=0;
				foreach($params['lists'] as $list_id){
					$list_fields.='<p class="wysija_list_check">
						<label for="'.$form_id_real.'_list_id_'.$list_id.'"><input id="'.$form_id_real.'_list_id_'.$list_id.'" class="validate[minCheckbox[1]] checkbox checklists" type="checkbox" name="wysija[user_list][list_id][]" value="'.$list_id.'" checked="checked" /> '.$params['lists_name'][$list_id].' </label>
							</p>';
					$i++;
				}
				$list_fields.='</div>';

			}else{

				if(isset($params['lists'])) $list_exploded=esc_attr(implode(',',$params['lists']));
				else $list_exploded='';

				$list_fields_hidden='<input type="hidden" name="wysija[user_list][list_ids]" value="'.$list_exploded.'" />';
			}

			$captcha_field = '';
			if($helperUser->isCaptchaEnabled()) {
				$captcha_key = htmlspecialchars($model_config->getValue('recaptcha_key'));
				$captcha_field = '<div class="g-recaptcha" data-sitekey="'.$captcha_key.'" data-size="compact"></div>';
			}

			$submit_value = (!empty($params['submit'])) ? $params['submit'] : __('Submit', WYSIJA);
			$submitbutton=$list_fields.$captcha_field.'<input type="submit" '.$disabled_submit.' class="wysija-submit wysija-submit-field" name="submit" value="'.esc_attr($submit_value).'"/>';
			$dataCf=$this->customFields($params,$form_id_real,$submitbutton);

			if($dataCf){
				$data.=$dataCf;

			}else{
				$user_email=WYSIJA::wp_get_userdata('user_email');
				$value_attribute='';
				if(is_user_logged_in() && !current_user_can('switch_themes') && !is_admin() && $user_email && is_string($user_email)){
					$value_attribute=$user_email;
				}

				$classValidate='wysija-email '.$this->getClassValidate($this->model->columns['email'],true);
				$data.='<p><input type="text" id="'.$form_id_real.'-wysija-to" class="'.$classValidate.'" value="'.$value_attribute.'" name="wysija[user][email]" />';
				$data.=$this->honey($params,$form_id_real);
				$data.=$submitbutton.'</p>';
			}

                $success_value = (!empty($params['success'])) ? $params['success'] : __('Success', WYSIJA);
				// few hiddn field
				$data.='<input type="hidden" name="formid" value="'.esc_attr($form_id_real).'" />
					<input type="hidden" name="action" value="save" />
				'.$list_fields_hidden.'
				<input type="hidden" name="message_success" value="'.esc_attr($success_value).'" />
				<input type="hidden" name="controller" value="subscribers" />';
				$data.='<input type="hidden" value="1" name="wysija-page" />';

				$data.='</form>';

		}
		//hook to let plugins modify our html the way they want
		$data = apply_filters('wysija_subscription_form', $data);
		$data.='</div>';
		if($echo) echo $data;
		else return $data;
	}

	function customFields($params,$formidreal,$submitbutton){
		$html='';
		$validationsCF=array(
			'email' => array('req'=>true,'type'=>'email','defaultLabel'=>__('Email',WYSIJA)),
			'firstname' => array('req'=>true,'defaultLabel'=>__('First name',WYSIJA)),
			'lastname' => array('req'=>true,'defaultLabel'=>__('Last name',WYSIJA)),
		);

		$wp_user_values=array();
		if(is_user_logged_in() && !is_admin() && !current_user_can('switch_themes')){
			$data_user_wp=WYSIJA::wp_get_userdata();
			if(isset($data_user_wp->user_email))$wp_user_values['email']=$data_user_wp->user_email;
			if(isset($data_user_wp->user_firstname))$wp_user_values['firstname']=$data_user_wp->user_firstname;
			if(isset($data_user_wp->user_lastname))$wp_user_values['lastname']=$data_user_wp->user_lastname;
		}


		if(isset($params['customfields']) && $params['customfields']){
			foreach($params['customfields'] as $fieldKey=> $field){

				//autofill logged in user data
				$value_attribute='';
				if(isset($wp_user_values[$fieldKey])){
					$value_attribute=$wp_user_values[$fieldKey];
				}

				$classField='wysija-'.$fieldKey;
				$classValidate=$classField." ".$this->getClassValidate($validationsCF[$fieldKey],true);
				if(!isset($field['label']) || !$field['label']) $field['label']=$validationsCF[$fieldKey]['defaultLabel'];
				if($fieldKey=='email') $fieldid=$formidreal.'-wysija-to';
				else $fieldid=$formidreal.'-'.$fieldKey;
				if(isset($params['form_type']) && $params['form_type']=='html'){
					$titleplaceholder='placeholder="'.$field['label'].'" title="'.$field['label'].'"';
				}else{
					$titleplaceholder='title="'.$field['label'].'"';
				}

				$value_attribute=' value="'.$value_attribute.'" ';
				if(count($params['customfields'])>1){
					if(isset($params['labelswithin'])){
						 if($params['labelswithin']=='labels_within'){
							$fieldstring='<input type="text" id="'.$fieldid.'" '.$titleplaceholder.' class="defaultlabels '.$classValidate.'" name="wysija[user]['.$fieldKey.']" '.$value_attribute.'/>';
						}else{
							$fieldstring='<label for="'.$fieldid.'">'.$field['label'].'</label><input type="text" id="'.$fieldid.'" class="'.$classValidate.'" name="wysija[user]['.$fieldKey.']" />';
						}
					}else{
						$fieldstring='<label for="'.$fieldid.'">'.$field['label'].'</label><input type="text" id="'.$fieldid.'" class="'.$classValidate.'" name="wysija[user]['.$fieldKey.']" />';
					}
				}else{
					if(isset($params['labelswithin'])){
						 if($params['labelswithin']=='labels_within'){
							$fieldstring='<input type="text" id="'.$fieldid.'" '.$titleplaceholder.' class="defaultlabels '.$classValidate.'" name="wysija[user]['.$fieldKey.']" '.$value_attribute.'/>';
						}else{
							$fieldstring='<input type="text" id="'.$fieldid.'" class="'.$classValidate.'" name="wysija[user]['.$fieldKey.']" '.$value_attribute.'/>';
						}
					}else{
						$fieldstring='<input type="text" id="'.$fieldid.'" class="'.$classValidate.'" name="wysija[user]['.$fieldKey.']" '.$value_attribute.'/>';
					}
				}


				$html.='<p class="wysija-p-'.$fieldKey.'">'.$fieldstring.'</p>';
			}

			$html.=$this->honey($params,$formidreal);

			if($html) $html.=$submitbutton;
		}

		return $html;
	}

	function honey($params,$formidreal){
		$arrayhoney=array(
			'firstname'=>array('label'=>__('First name',WYSIJA),'type'=>'req'),
			'lastname'=>array('label'=>__('Last name',WYSIJA),'type'=>'req'),
			'email'=>array('label'=>__('Email',WYSIJA),'type'=>'email')

			);
		$html='';
		foreach($arrayhoney as $fieldKey=> $field){
			$fieldid=$formidreal.'-abs-'.$fieldKey;

			if(isset($params['labelswithin'])){
				$fieldstring='<input type="text" id="'.$fieldid.'" value="" class="defaultlabels validated[abs]['.$field['type'].']" name="wysija[user][abs]['.$fieldKey.']" />';
			}else{
				$fieldstring='<label for="'.$fieldid.'">'.$field['label'].'</label><input type="text" id="'.$fieldid.'" class="validated[abs]['.$field['type'].']" name="wysija[user][abs]['.$fieldKey.']" />';
			}
			$html.='<span class="wysija-p-'.$fieldKey.' abs-req">'.$fieldstring.'</span>';
		}
		return $html;
	}

}
