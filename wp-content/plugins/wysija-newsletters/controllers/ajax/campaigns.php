<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_control_back_campaigns extends WYSIJA_control{

	function __construct(){
		if(!WYSIJA::current_user_can('wysija_newsletters'))  die('Action is forbidden.');
		parent::__construct();;
	}

	function save_poll(){
		$this->requireSecurity();

                if( in_array($_REQUEST['how'], array('repository' , 'search_engine' , 'friend', 'url' )) ){

                    $data_conf = array( 'poll_origin' => $_REQUEST['how'] );
                    if( !empty( $_REQUEST['where'] ) ){
                        $data_conf['poll_origin_url'] = esc_url($_REQUEST['where']);
                    }
                    $model_config = WYSIJA::get('config','model');
                    $model_config->save( $data_conf );

                    $res['result'] = true;
                    $res['msg'] = '<span><span class="checkmark">---</span>'. __('Thanks!',WYSIJA). '</span>';
                    return $res;
                }

                $res['result'] = false;
                return $res;

	}

	function switch_theme() {
		$this->requireSecurity();
                if(isset($_POST['wysijaData'])) {
			$rawData = $_POST['wysijaData'];
			// avoid using stripslashes as it's not reliable depending on the magic quotes settings
			$rawData = str_replace('\"', '"', $rawData);
			// decode JSON data
			$rawData = json_decode($rawData, true);

			$theme = (isset($rawData['theme'])) ? $rawData['theme'] : 'default';

			$helper_wj_engine = WYSIJA::get('wj_engine', 'helper');
			$res['templates'] = $helper_wj_engine->renderTheme($theme);

			$email_id = (int)$_REQUEST['id'];

			$campaignsHelper = WYSIJA::get('campaigns', 'helper');

			if(isset($res['templates']['divider_options'])) {
				// save divider
				$campaignsHelper->saveParameters($email_id, 'divider', $res['templates']['divider_options']);
			}

			// save theme used
			$campaignsHelper->saveParameters($email_id, 'theme', $theme);

			$res['templates']['theme'] = $theme;
			$res['styles'] = $helper_wj_engine->renderThemeStyles($theme);
		} else {
			$res['msg'] = __("The theme you selected could not be loaded.",WYSIJA);
			$res['result'] = false;
		}
		return $res;
	}

	function save_editor() {
		$this->requireSecurity();
                // decode json data and convert to array
		$rawData = '';
		if(isset($_POST['wysijaData'])) {
			$rawData = $_POST['wysijaData'];
			// avoid using stripslashes as it's not reliable depending on the magic quotes settings
			$rawData = str_replace('\"', '"', $rawData);
			// decode JSON data
			$rawData = json_decode($rawData, true);
		}

		if(!$rawData){
			$this->error('Error saving',false);
			return array('result' => false);
		}

		$helper_wj_engine = WYSIJA::get('wj_engine', 'helper');
		$helper_wj_engine->setData( $rawData );
		$result = false;

		// get email id
		$email_id = (int)$_REQUEST['id'];

		$model_email = WYSIJA::get('email', 'model');
		$emailData = $model_email->getOne(array('wj_styles', 'subject', 'params', 'email_id', 'campaign_id'), array('email_id' => $email_id));

		$helper_wj_engine->setStyles($emailData['wj_styles'], true);

		$values = array('wj_data' => $helper_wj_engine->getEncoded('data'));
		$values['body'] = $helper_wj_engine->renderEmail($emailData);
		$values['email_id'] = $email_id;

		$updated_email = $helper_wj_engine->getEmailData();

		// update modified_at timestamp
		$model_email->columns['modified_at']['autoup']=1;

		// update data in DB
		$result = $model_email->update($values, array('email_id' => $email_id));

		if(!$result) {
			// throw error
			$this->error(__('Your email could not be saved', WYSIJA));
		} else {
			// save successful
			$this->notice(__('Your email has been saved', WYSIJA));
		}

		return array('result' => $result);
	}

	function save_styles() {
		$this->requireSecurity();
                // decode json data and convert to array
		$rawData = '';
		if(isset($_POST['wysijaStyles'])) {
			$rawData = $_POST['wysijaStyles'];
			// avoid using stripslashes as it's not reliable depending on the magic quotes settings
			$rawData = str_replace('\"', '"', $rawData);
			// decode JSON data
			$rawData = json_decode($rawData, true);

		}

		// handle checkboxes
		if(array_key_exists('a-underline', $rawData) === false) {
			$rawData['a-underline'] = -1;
		}

		$helper_wj_engine = WYSIJA::get('wj_engine', 'helper');
		$helper_wj_engine->setStyles($helper_wj_engine->formatStyles($rawData));

		$result = false;

		$values = array(
			'wj_styles' => $helper_wj_engine->getEncoded('styles')
		);

		// get email id
		$email_id = (int)$_REQUEST['id'];

		// update data in DB
		$model_email = WYSIJA::get('email', 'model');
		$result = $model_email->update($values, array('email_id' => $email_id));

		if(!$result) {
			// throw error
			$this->error(__('Styles could not be saved', WYSIJA));
		} else {
			// save successful
			$this->notice(__('Styles have been saved', WYSIJA));
		}

		return array(
			'styles' => $helper_wj_engine->renderStyles(),
			'result' => $result
		);
	}

	function deleteimg(){
                $this->requireSecurity();
		if(isset($_REQUEST['imgid']) && $_REQUEST['imgid']>0){
			/* delete the image with id imgid */
			 $result=wp_delete_attachment($_REQUEST['imgid'],true);
			 if($result){
				 $this->notice(__('Image has been deleted.',WYSIJA));
			 }
		}

		$res=array();
		$res['result'] = $result;
		return $res;
	}

	function deleteTheme(){
		$this->requireSecurity();
                if(isset($_REQUEST['themekey']) && $_REQUEST['themekey']){
			/* delete the image with id imgid */
			$helperTheme=WYSIJA::get('themes','helper');
			$result=$helperTheme->delete($_REQUEST['themekey']);
		}

		$res=array();
		$res['result'] = $result;
		return $res;
	}

	// set newsletter default theme
	function setDefaultTheme() {
		$this->requireSecurity();
                if(isset($_REQUEST['theme']) && $_REQUEST['theme']) {
			// check that the theme exists
			// TODO
			$theme_exists = true;
			if($theme_exists === true) {
				// update config
				$model_config = WYSIJA::get('config', 'model');
				$model_config->save(array('newsletter_default_theme' => $_REQUEST['theme']));

				$result = true;
			} else {
				$result = false;
			}
		}

		return array('result' => $result);
	}

	function save_IQS() {
		$this->requireSecurity();
                // decode json data and convert to array
		$wysijaIMG = '';
		if(isset($_POST['wysijaIMG'])) {
			$wysijaIMG = json_decode(stripslashes($_POST['wysijaIMG']), TRUE);
		}
		$values = array(
			'params' => array('quickselection'=>$wysijaIMG)
		);

		// get email id
		$email_id = (int)$_REQUEST['id'];
		$values['email_id']=$email_id;

		// update data in DB
		$model_email = WYSIJA::get('email', 'model');
		$result = $model_email->update($values, array('email_id' => $email_id));

		if(!$result) {
			// throw error
			$this->error(__('Image selection has not been saved.', WYSIJA));
		} else {
			// save successful
			$this->notice(__('Image selection has been saved.', WYSIJA));
		}

		return array('result' => $result);
	}

	function insert_articles() {
		$this->requireSecurity();
                // get raw params
		$raw_params = $_REQUEST['data'];

		// format params
		$params = array();
		foreach($raw_params as $value) {
			$params[$value['name']] = $value['value'];
		}

		if($params['show_divider'] === 'yes') {
			// get divider
			$divider = $_REQUEST['divider'];
		} else {
			$divider = null;
		}
		$params['divider'] = $divider;

		// get post ids
		$post_ids = array();
		if(isset($_REQUEST['post_ids']) && strlen(trim($_REQUEST['post_ids'])) > 0) {
			$post_ids = explode(',', $_REQUEST['post_ids']);
		}

		if(empty($post_ids)) {
			// return error
			$res['msg'] = __('Please select an article.', WYSIJA);
			$res['result'] = false;
			return $res;
		}

		// specify custom fields to get from posts
		$post_params = array('include' => $post_ids);

		// include sort by parameter into post params
		$post_params['sort_by'] = $params['sort_by'];

		// get posts
		$model_wp_posts = WYSIJA::get('wp_posts', 'model');
		$posts = $model_wp_posts->get_posts($post_params);

		// check if we need to interpret shortcodes
		$model_config = WYSIJA::get('config', 'model');
		$interpret_shortcode = (bool)$model_config->getValue('interp_shortcode');

		// get some model and helpers
		$helper_articles = WYSIJA::get('articles', 'helper');
		$helper_wj_engine = WYSIJA::get('wj_engine', 'helper');

		$output = '';

		// save parameters for next time
		$model_config->save(array('insert_post_parameters' => $helper_wj_engine->encodeParameters($params)));

		foreach($posts as $key => $post) {
			if($interpret_shortcode === true) {
				// interpret shortcodes
				$posts[$key]['post_content'] = apply_filters('the_content', $post['post_content']);
			}
			// get thumbnail
			$posts[$key]['post_image'] = $helper_articles->getImage($post);
		}

		$output .= base64_encode($helper_wj_engine->renderPostsToBlocks($posts, $params));

		if(strlen($output) > 0) {
			$res['result'] = true;
			$res['posts'] = $output;
		}else {
			$res['msg'] = __('There are no posts corresponding to that search.',WYSIJA);
			$res['result'] = false;
		}

		return $res;
	}

	function send_preview($spamtest=false){
		$this->requireSecurity();
                $mailer=WYSIJA::get('mailer','helper');
		$email_id = $_REQUEST['id'];
		$resultarray=array();

		// update data in DB
		$model_email = WYSIJA::get('email', 'model');
		$model_email->getFormat=OBJECT;
		$email_object = $model_email->getOne(false,array('email_id' => $email_id));
		$mailer->testemail=true;


		if(isset($_REQUEST['data'])){
		   $dataTemp=$_REQUEST['data'];
			$_REQUEST['data']=array();
			foreach($dataTemp as $val) $_REQUEST['data'][$val['name']]=$val['value'];
			unset($dataTemp);
			foreach($_REQUEST['data'] as $k =>$v){
				$newkey=str_replace(array('wysija[email][',']'),'',$k);
				$configVal[$newkey]=$v;
			}
			if(isset($configVal['from_name'])){
				$params=array(
					'from_name'=>$configVal['from_name'],
					'from_email'=>$configVal['from_email'],
					'replyto_name'=>$configVal['replyto_name'],
					'replyto_email'=>$configVal['replyto_email']);
				if(isset($configVal['subject']))    $email_object->subject=$configVal['subject'];
			}

		}else{
			$params=array(
				'from_name'=>$email_object->from_name,
				'from_email'=>$email_object->from_email,
				'replyto_name'=>$email_object->replyto_name,
				'replyto_email'=>$email_object->replyto_email
			);
		}
		if(strpos($_REQUEST['receiver'], ',')) {
			$receivers = explode(',',$_REQUEST['receiver']);
		} else if(strpos($_REQUEST['receiver'], ';')) {
			$receivers = explode(';',$_REQUEST['receiver']);
		} else {
			$receivers = array($_REQUEST['receiver']);
		}

		$user_model = WYSIJA::get('user', 'model');
		foreach($receivers as $key => $receiver){
			$receiver = trim($receiver);
			$dummy_receiver = $user_model->get_object_by_email($receiver);
			if(empty($dummy_receiver)){
				$dummy_receiver = new stdClass();
				$dummy_receiver->user_id = 0;
				$dummy_receiver->email = $receiver;
				$dummy_receiver->status = 1;
				$dummy_receiver->lastname = $dummy_receiver->firstname = '';
			}

			if($spamtest){
				$langextra = '';
				$dummy_receiver->firstname ='Mail Tester';

				$wp_lang = get_locale();
				if(!empty($wp_lang)) $langextra ='&lang='.$wp_lang;
				$resultarray['urlredirect']='http://www.mail-tester.com/check.php?id='.urlencode($dummy_receiver->email).$langextra;
			}
			$receivers[$key] = $dummy_receiver;

		}

		$email_clone=array();
		foreach($email_object as $kk=>$vv)  $email_clone[$kk]=$vv;


		$wj_engine = WYSIJA::get('wj_engine', 'helper');
		// set data & styles
		if(isset($email_clone['wj_data'])) { $wj_engine->setData($email_clone['wj_data'], true); } else { $wj_engine->setData(); }
		if(isset($email_clone['wj_styles'])) { $wj_engine->setStyles($email_clone['wj_styles'], true); } else { $wj_engine->setStyles(); }

		// generate email html body
		$body = $wj_engine->renderEmail($email_clone);

		// get back email data as it will be updated during the rendering (articles ids + articles count)
		$email_child = $wj_engine->getEmailData();

		// [total] [number] and [post_title] are only valid for post notifications newsletter
		if((int)$email_child['type'] === 2 && isset($email_child['params']['autonl']['event']) &&
				$email_child['params']['autonl']['event'] === 'new-articles' && isset($email_child['params']['autonl']['articles'])){

			$item_count = 0;
			$total_count = 1;
			$first_subject = '';

			if(isset($email_child['params']['autonl']['articles']['count'])) $item_count = (int)$email_child['params']['autonl']['articles']['count'];
			if(isset($email_child['params']['autonl']['articles']['first_subject'])) $first_subject = $email_child['params']['autonl']['articles']['first_subject'];
			if(isset($email_child['params']['autonl']['total_child'])) $total_count = (int)$email_child['params']['autonl']['total_child'] + 1;

			$email_object->subject = str_replace(
				array('[total]','[number]','[post_title]'),
				array($item_count, $total_count, $first_subject),
				$email_child['subject']
			);
		}
		$successmsg = __('Your email preview has been sent to %1$s', WYSIJA);

		// correction added for post notifications with the tag [newsletter:post_title] failing to send
		if(isset($email_object->params['autonl']) && isset($email_child['params']['autonl'])){
			$email_object->params['autonl']=$email_child['params']['autonl'];
		}

		if(isset($email_object->params)) {
			$params['params']=$email_object->params;

			if(isset($configVal['params[googletrackingcode'])){
				$paramsemail=array();
				if(!is_array($email_object->params)) $paramsemail=unserialize(base64_decode($email_object->params));

				if(trim($configVal['params[googletrackingcode'])) {
					$paramsemail['googletrackingcode']=$configVal['params[googletrackingcode'];
				}
				else {
					unset($paramsemail['googletrackingcode']);
				}
				$params['params'] = base64_encode(serialize($paramsemail));
			}
		}

		$params['email_id'] = $email_object->email_id;
		$receiversList = array();
		$res = false;
		foreach($receivers as $receiver){
			if($mailer->sendSimple($receiver,  stripslashes($email_object->subject),$email_object->body,$params)) {
				$res = true;
				$receiversList[] = $receiver->email;
			}
			WYSIJA::log('preview_sent', $mailer, 'manual');
		}

		if($res === true) {
			$this->notice(sprintf($successmsg, implode(', ', $receiversList)));
		}

		$resultarray['result'] = $res;

		return $resultarray;
	}

	/**
	 * send spam test function step 2 of the newsletter edition process
	 */
	function send_spamtest(){
		$this->requireSecurity();
                return apply_filters('wysija_send_spam_test','',$this);
	}

	function set_divider()
	{
		$this->requireSecurity();
                $src = isset($_POST['wysijaData']['src']) ? $_POST['wysijaData']['src'] : NULL;
		$width = isset($_POST['wysijaData']['width']) ? (int)$_POST['wysijaData']['width'] : NULL;
		$height = isset($_POST['wysijaData']['height']) ? (int)$_POST['wysijaData']['height'] : NULL;

		if($src === NULL OR $width === NULL OR $height === NULL) {
			// there is a least one missing parameter, fallback to default divider
			$dividersHelper = WYSIJA::get('dividers', 'helper');
			$divider = $dividersHelper->getDefault();
		} else {
			// use provided params
			$divider = array(
				'src' => $src,
				'width' => $width,
				'height' => $height
			);
		}

		// update campaign parameters
		$email_id = (int)$_REQUEST['id'];
		$campaignsHelper = WYSIJA::get('campaigns', 'helper');
		$campaignsHelper->saveParameters($email_id, 'divider', $divider);

		// set params
		$block = array_merge(array('no-block' => true, 'type' => 'divider'), $divider);

		$helper_wj_engine=WYSIJA::get('wj_engine','helper');
		return base64_encode($helper_wj_engine->renderEditorBlock($block));
	}

	function generate_social_bookmarks() {
                $this->requireSecurity();
		$size = 'medium';
		$iconset = '01';

		if(isset($_POST['wysijaData']) && !empty($_POST['wysijaData'])) {
			$data = $_POST['wysijaData'];
			$items = array();

			foreach($data as $key => $values) {
				if($values['name'] === 'bookmarks-size') {
					// get size
					$size = $values['value'];
				} else if($values['name'] === 'bookmarks-theme') {
					// get theme name
					$theme = $values['value'];
				} else if($values['name'] === 'bookmarks-iconset') {
					// get iconset
					$iconset = $values['value'];
					if(strlen(trim($iconset)) === 0) {
						$this->error('No iconset specified', false);
						return false;
					}
				} else {
					$keys = explode('-', $values['name']);
					$network = $keys[1];
					$property = $keys[2];
					if(array_key_exists($network, $items)) {
						$items[$network][$property] = $values['value'];
					} else {
						$items[$network] = array($property => $values['value']);
					}
				}
			}
		}

		$urls = array();
		// check data and remove network with an empty url
		foreach($items as $network => $item) {
			if(strlen(trim($item['url'])) === 0) {
				// empty url
				unset($items[$network]);
			} else {
				// url specified
				$urls[$network] = $item['url'];
			}
		}

		// check if there's at least one url left
		if(empty($urls)) {
			$this->error('No url specified', false);
			return false;
		}

		// save url in config
		$config=WYSIJA::get('config','model');
		$config->save(array('social_bookmarks' => $urls));

		// get iconset icons
		$bookmarksHelper = WYSIJA::get('bookmarks', 'helper');

		// if the iconset is 00, then it's the theme's bookmarks
		if($iconset === '00') {
			$icons = $bookmarksHelper->getAllByTheme($theme);
		} else {
			// otherwise it's a basic iconset
			$icons = $bookmarksHelper->getAllByIconset($size, $iconset);
		}


		// format data
		$block = array(
			'position' => 1,
			'type' => 'gallery',
			'items' => array(),
			'alignment' => 'center'
		);

		$width = 0;
		foreach($items as $key => $item) {
			$block['items'][] = array_merge($item, $icons[$key], array('alt' => ucfirst($key)));
			$width += (int)$icons[$key]['width'];
		}
		// add margin between icons
		$width += (count($block['items']) - 1) * 10;
		// set optimal width
		$block['width'] = max(0, min($width, 564));

		$helper_wj_engine=WYSIJA::get('wj_engine','helper');
		return base64_encode($helper_wj_engine->renderEditorBlock($block));
	}

	function install_theme() {
		$this->requireSecurity();
                if( isset($_REQUEST['theme_id'])){
			global $wp_version;
			//check if theme is premium if you have the premium licence
			if(isset($_REQUEST['premium']) && $_REQUEST['premium']){
				$getpremiumtheme=apply_filters('wysija_install_theme_premium', false);

				if(!$getpremiumtheme){
					$helper_wj_engine = WYSIJA::get('wj_engine', 'helper');
					$themes = $helper_wj_engine->renderThemes();
					return array('result'=>false, 'themes' => $themes);
				}
			}

			$helperToolbox = WYSIJA::get('toolbox','helper');
			$domain_name = $helperToolbox->_make_domain_name(admin_url('admin.php'));

			$request = 'http://api.mailpoet.com/download/zip/'.$_REQUEST['theme_id'].'?domain='.$domain_name;

			$args = array(
					'timeout' =>  30,
					'body' => array(  ),
		 'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			);
			$raw_response = wp_remote_post( $request, $args );

			if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ){
				if(method_exists($raw_response, 'get_error_messages')){
					$this->error($raw_response->get_error_messages());
				}
				$ZipfileResult = false;
			}else{
				$ZipfileResult = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );
			}

			if($ZipfileResult === false){
				$result = false;
				$this->error(__('We were unable to contact the API, the site may be down. Please try again later.',WYSIJA),true);
			}else{
				$themesHelp=WYSIJA::get('themes','helper');
				$result = $themesHelp->installTheme($ZipfileResult);

				// refresh themes list
				$helper_wj_engine = WYSIJA::get('wj_engine', 'helper');
				$themes = $helper_wj_engine->renderThemes();
			}
		}else{
			$result = false;
			$themes = '';
			$this->notice('missing info');
		}

		return array('result' => $result, 'themes' => $themes);
	}

        function get_social_bookmarks() {
		$size = isset($_POST['wysijaData']['size']) ? $_POST['wysijaData']['size'] : NULL;
		$theme = isset($_POST['wysijaData']['theme']) ? $_POST['wysijaData']['theme'] : NULL;

		$bookmarksHelper = WYSIJA::get('bookmarks', 'helper');
		$bookmarks = $bookmarksHelper->getAll($size, $theme);
		return json_encode(array('icons' => $bookmarks));
	}

	function refresh_themes() {
		// refresh themes list
		$helper_wj_engine = WYSIJA::get('wj_engine', 'helper');
		return array('result'=>true, 'themes' => $helper_wj_engine->renderThemes());
	}

	function generate_auto_post() {
		// get params and generate html
		$helper_wj_engine = WYSIJA::get('wj_engine', 'helper');
		$helper_articles = WYSIJA::get('articles', 'helper');

		// get parameters
		$block_params = array();
		if(isset($_POST['wysijaData'])) {
			// store category ids (TBR)
			$category_ids = array();

			foreach($_POST['wysijaData'] as $pairs) {
				// special cases
				switch($pairs['name']) {
					case 'author_label':
					case 'category_label':
					case 'readmore':
					case 'nopost_message':
						$block_params[] = array('key' => $pairs['name'], 'value' => base64_encode(stripslashes($pairs['value'])));
						break;
					case 'category_ids':
						$category_ids = array_filter( array_map( 'absint', explode( ',', $pairs['value'] ) ) );
					break;
					default:
						$block_params[] = array('key' => $pairs['name'], 'value' => $pairs['value']);
				}
			}

			// make sure we have only unique ids in categories
			$block_params[] = array('key' => 'category_ids', 'value' => join(',', array_unique($category_ids)));
		}

		if(empty($block_params)) {
			// an error occurred, do something!
			return false;
		} else {
			$data = array(
				'type' => 'auto-post',
				'params' => $block_params
			);
			return base64_encode($helper_wj_engine->renderEditorBlock($data));
		}
	}

	function load_auto_post() {
		$params = array();

		if(isset($_POST['wysijaData'])) {

			$pairs = explode('&', $_POST['wysijaData']);

			foreach($pairs as $pair) {
				list($key, $value) = explode('=', $pair);
				switch($key) {
					case 'autopost_count':
						$params[$key] = (int)$value;
						break;
					case 'readmore':
					case 'author_label':
					case 'category_label':
					case 'nopost_message':
						$params[$key] = base64_decode($value);
						break;
					case 'exclude':
						$params[$key] = explode(',', $value);
						break;
					default:
						$params[$key] = $value;
				}
			}
		}

		if(empty($params)) {
			// an error occurred, do something!
			return false;
		} else {

			// get email params
			$email_id = (int)$_REQUEST['id'];
			$model_email = WYSIJA::get('email', 'model');
			$email = $model_email->getOne(array('params','sent_at','campaign_id'), array('email_id' => $email_id));

			$helper_articles = WYSIJA::get('articles', 'helper');
			$helper_wj_engine = WYSIJA::get('wj_engine', 'helper');

			// see if posts have already been sent
			if(!empty($email['params']['autonl']['articles']['ids'])) {
				if(!isset($params['exclude'])) { $params['exclude'] = array(); }

				$params['exclude'] = array_unique(array_merge($email['params']['autonl']['articles']['ids'], $params['exclude']));
			}

			//we set the post_date to filter articles only older than that one
			if(isset($email['params']['autonl']['firstSend'])){
				$params['post_date'] = $email['params']['autonl']['firstSend'];
			}

			// if immediate let it know to the get post
			if(isset($email['params']['autonl']['articles']['immediatepostid'])){
				$params['include'] = $email['params']['autonl']['articles']['immediatepostid'];
				$params['post_limit'] = 1;
			}else{
				//we set the post_date to filter articles only older than the last time we sent articles
				if(isset($email['params']['autonl']['lastSend'])){
					$params['post_date'] = $email['params']['autonl']['lastSend'];
				}else{
					//get the latest child newsletter sent_at value
					$mEmail=WYSIJA::get('email','model');
					$mEmail->reset();
					$mEmail->orderBy('email_id','DESC');
					$lastEmailSent=$mEmail->getOne(false,array('campaign_id'=>$email['campaign_id'],'type'=>'1'));

					if(!empty($lastEmailSent)) $params['post_date'] = $lastEmailSent['sent_at'];
				}
			}

			// get posts
			$model_wp_posts = WYSIJA::get('wp_posts','model');
			$posts = $model_wp_posts->get_posts($params);

			if(empty($posts)) {
				// nothing to display
				$posts = array();
			} else {
				// used to keep track of post ids present in the auto post
				$post_ids = array();

				// cleanup post and get image
				foreach($posts as $key => $post) {
					if($params['image_alignment'] !== 'none') {
						// attempt to get post image
						$posts[$key]['post_image'] = $helper_articles->getImage($post);
					}

					// store article id
					$post_ids[] = $post['ID'];
				}
				// store article ids
				$params['post_ids'] = join(',', $post_ids);
			}

			// get divider if necessary (for immediate post notification, the "show_divider" parameter is not available)
			if(isset($params['show_divider']) && $params['show_divider'] === 'yes') {
				if(isset($email['params']['divider'])) {
					$params['divider'] = $email['params']['divider'];
				} else {
					$helper_dividers = WYSIJA::get('dividers', 'helper');
					$params['divider'] = $helper_dividers->getDefault();
				}
			}

			return base64_encode($helper_wj_engine->renderEditorAutoPost($posts, $params));
		}
	}

        function search_terms( $request = null ){
		$response = (object) array(
			'status' => false,
			'message' => __( 'Your request has failed', WYSIJA ),
			'results' => array(),
			'more' => true,
		);

		if ( ( ! defined( 'DOING_AJAX' ) && is_null( $request ) ) || ! is_user_logged_in() ){
			return $response;
		}

		$request = (object) wp_parse_args(
			$request,
			array(
				'search' => isset( $_GET['search'] ) ? $_GET['search'] : '',
				'post_type' => isset( $_GET['post_type'] ) ? $_GET['post_type'] : null,
				'page' => absint( isset( $_GET['page'] ) ? $_GET['page'] : 0 ),
				'page_limit' => absint( isset( $_GET['page_limit'] ) ? $_GET['page_limit'] : 10 ),
			)
		);

		if ( is_null( $request->post_type ) ){
			return $response;
		}

		$response->status  = true;
		$response->message = __( 'Request successful', WYSIJA );

		$response->post_type = get_post_types( array( 'name' => $request->post_type ) );
		$response->post_type = reset( $response->post_type );

		preg_match( '/@(\w+)/i', $request->search, $response->regex );

		if ( ! empty( $response->regex ) ){
			$request->search = array_filter( array_map( 'trim', explode( '|', str_replace( $response->regex[0], '|', $request->search ) ) ) );
			$request->search = reset( $request->search );
			$taxonomies      = $response->regex[1];
		} else {
			$taxonomies = get_object_taxonomies( $response->post_type );
		}
		$response->taxonomies = get_object_taxonomies( $response->post_type, 'objects' );

		$response->results = get_terms(
			(array) $taxonomies,
			array(
				'hide_empty' => false,
				'search' => $request->search,
				'number' => $request->page_limit,
				'offset' => $request->page_limit * ( $request->page - 1 ),
			)
		);

		if ( empty( $response->results ) || count( $response->results ) < $request->page_limit ){
			$response->more = false;
		}

		return $response;
	}

        /**
	 * returns a list of articles to the popup in the visual editor
	 * @global type $wpdb
	 * @return boolean
	 */
	function get_articles(){
		// fixes issue with pcre functions
		@ini_set('pcre.backtrack_limit', 1000000);

		// get parameters
		$raw_data = $_REQUEST['data'];
		$params = array();
		foreach ($raw_data as $value) {
			$params[$value['name']] = $value['value'];
		}

		// get options
		$model_config = WYSIJA::get('config', 'model');
		$interpret_shortcode = (bool)$model_config->getValue('interp_shortcode');

		// post statuses
		$helper_wp_tools = WYSIJA::get('wp_tools', 'helper');
		$post_statuses = $helper_wp_tools->get_post_statuses();
		$post_types = $helper_wp_tools->get_post_types();

		// filter by post_type
		if(isset($params['post_type'])) {
			$post_types_filter = array();
			if(strlen(trim($params['post_type'])) === 0) {
				$post_types_filter = array_keys($post_types);
				$post_types_filter[] = 'post';
				$post_types_filter[] = 'page';
			} else {
				$post_types_filter = trim($params['post_type']);
			}
			// set condition on post type
			$params['post_type'] = $post_types_filter;
		}

		// query offset when doing incremental loading
		$query_offset = (isset($_REQUEST['query_offset']) && (int)$_REQUEST['query_offset'] >= 0) ? (int)$_REQUEST['query_offset'] : 0;
		$params['query_offset'] = $query_offset;

		// fetch posts
		$helper_articles = WYSIJA::get('articles', 'helper');

		// set is_search_query (true) to get a count in addition to the results
		$params['is_search_query'] = true;

		$model_wp_posts = WYSIJA::get('wp_posts','model');
		$data = $model_wp_posts->get_posts($params);

		// extract data
		$posts = $data['rows'];
		// contains the total number of rows available
		$count = $data['count'];

		// return results
		$result = array(
			'result' => true,
			'append' => ($query_offset > 0)
		);

		if(empty($posts) === false) {
			foreach($posts as $key => $post) {
				// interpret shortcodes
				if($interpret_shortcode === true) {
					$posts[$key]['post_content'] = apply_filters('the_content', $posts[$key]['post_content']);
				}

				// get thumbnail
				$posts[$key]['post_image'] = $helper_articles->getImage($post);

				// set post status
				$post_status_label = '';
				if(isset($post_statuses[$posts[$key]['post_status']])) {
					$post_status_label = $post_statuses[$posts[$key]['post_status']];
				}
				$posts[$key]['post_status'] = $post_status_label;
			}
			$result['posts'] = $posts;
			$result['total'] = (int)$count['total'];
		}else {
			$result['msg'] = __('There are no posts corresponding to that search.', WYSIJA);
			$result['result'] = false;
		}

		return $result;
	}
}
