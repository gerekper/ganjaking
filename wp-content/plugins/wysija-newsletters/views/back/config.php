<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_view_back_config extends WYSIJA_view_back {

	var $title = 'Settings';

	var $icon = 'icon-options-general';

	var $skip_header = true;

	function reinstall() {
		?>
		<form name="wysija-settings" method="post" id="wysija-settings" action="" class="form-valid" autocomplete="off">
			<input type="hidden" value="doreinstall" name="action" />
			<input type="hidden" value="reinstall" name="postedfrom" />
			<h3><?php _e('If you confirm this, all your current MailPoet data will be erased (newsletters, themes, statistics, lists, subscribers, etc.)', WYSIJA); ?></h3>
			<p class="submit">
				<input type="submit" value="<?php _e('Confirm Reinstallation', WYSIJA) ?>" class="button-secondary" id="submit" name="submit" />
				<?php $this->secure(array( 'action' => 'doreinstall' )); ?>
			</p>
		</form>
		<?php
	}

	function fieldFormHTML_commentform($key, $value, $model, $paramsex) {
		// second part concerning the checkbox
		$formsHelp	   = WYSIJA::get('forms', 'helper');
		$checked		 = false;
		if ($this->model->getValue($key))
			$checked		 = true;
		$checkboxDetails = array( 'id'		  => $key, 'name'		=> 'wysija['.$model.']['.$key.']', 'class'	   => 'activateInput' );
		$contentAfter = '';

		// if it's the commentform field and jetpacks is activated with its comment module then we disable the box
		if ($key == 'commentform' && WYSIJA::is_plugin_active('jetpack/jetpack.php') && in_array('comments', Jetpack::get_active_modules())) {
			$checkboxDetails['disabled'] = 'disabled';
			$contentAfter				= '<p>'.__('This feature cannot work because the "Comments" feature of the plugin JetPack is enabled.', WYSIJA).'</p>';
		}

		// if it's the register form field and registration is not allowed on the site then we just disable it
		$active_signup = false;
		if (is_multisite()) {
			$active_signup = get_site_option('registration');
			if (!$active_signup)
				$active_signup = 'all';

			$active_signup = apply_filters('wpmu_active_signup', $active_signup);
			if (in_array($active_signup, array( 'none', 'blog' )))
				$active_signup = false;
			else
				$active_signup = true;
		}
		else
			$active_signup = get_option('users_can_register');

		if ($key == 'registerform' && !$active_signup) {
			$checkboxDetails['disabled'] = 'disabled';

			$contentAfter = '<p>'.__('Registration is disabled on this site.', WYSIJA).'</p>';
		}

		$fieldHTML = '<label class="checkbox_optin_label" for="'.$key.'">';
		$fieldHTML.=$formsHelp->checkbox($checkboxDetails, 1, $checked);
		$fieldHTML.='</label>';

		$value = $this->model->getValue($key.'_linkname');

		$fieldHTML.='<div id="'.$key.'_linkname'.'" class="checkbox_optin_value">';

		if ($contentAfter) {
			$fieldHTML.='</div>';
			$fieldHTML.=$contentAfter;
		}
		else {
			$fieldHTML.=$formsHelp->input(array( 'name'	  => 'wysija['.$model.']['.$key.'_linkname]', 'size'	  => '75' ), $value);
			$model_list = WYSIJA::get('list', 'model');
			$lists	  = $model_list->get(array( 'name', 'list_id' ), array( 'is_enabled' => 1 ));
			$valuefield  = $this->model->getValue($key.'_lists');
			if (!$valuefield)
				$valuefield  = array( );
			foreach ($lists as $list) {
				if (in_array($list['list_id'], $valuefield))
					$checked = true;
				else
					$checked = false;

				$fieldHTML.= '<p class="labelcheck"><label for="list-'.$list['list_id'].'">'.$formsHelp->checkbox(array( 'id'	=> 'list-'.$list['list_id'],
							'name'  => 'wysija[config]['.$key.'_lists][]', 'class' => 'validate[minCheckbox[1]]' ), $list['list_id'], $checked).$list['name'].'</label></p>';
			}
			$fieldHTML.='</div>';
		}


		return $fieldHTML;
	}

	function fieldFormHTML_subscribers_count($key, $value, $model, $paramsex) {
		// second part concerning the checkbox
		$formsHelp = WYSIJA::get('forms', 'helper');
		$checked   = false;
		if ($this->model->getValue($key)) {
			$checked   = true;
		}
		$fieldHTML = '';
		$fieldHTML .= '<div id="'.$key.'_linkname'.'" class="checkbox_optin_value">';
		$fieldHTML .= $formsHelp->input(array( 'name'	  => 'wysija['.$model.']['.$key.'_linkname]', 'size'	  => '75', 'class'	 => 'subscribers-count-shortcode', 'readonly'  => 'readonly' ), '[wysija_subscribers_count]').'</p>';
		$model_list = WYSIJA::get('list', 'model');
		$lists	  = $model_list->get(array( 'name', 'list_id', 'is_public' ), array( 'is_enabled' => 1 ));


		foreach ($lists as $list) {
			$fieldHTML.= '<p class="labelcheck"><label for="'.$key.'list-'.$list['list_id'].'">'.$formsHelp->checkbox(array( 'id'	=> $key.'list-'.$list['list_id'],
						'name'  => 'wysija[config]['.$key.'_lists][]', 'class' => 'subscribers-count-list' ), $list['list_id']).$list['name'].'</label></p>';
		}
		$fieldHTML.='</div>';


		return $fieldHTML;
	}

	function fieldFormHTML_managesubscribe($key, $value, $model, $paramsex) {
		// second part concerning the checkbox
		$formsHelp = WYSIJA::get('forms', 'helper');
		$checked   = false;
		if ($this->model->getValue($key))
			$checked   = true;
		$fieldHTML = '<label class="checkbox_optin_label" for="'.$key.'">';
		$fieldHTML .= $formsHelp->checkbox(array( 'id'	=> $key, 'name'  => 'wysija['.$model.']['.$key.']', 'class' => 'activateInput' ), 1, $checked);
		$fieldHTML .= '</label>';
		$value  = $this->model->getValue($key.'_linkname');

		$fieldHTML .= '<div id="'.$key.'_linkname'.'" class="checkbox_optin_value">';
		$fieldHTML .= $formsHelp->input(array( 'name'	  => 'wysija['.$model.']['.$key.'_linkname]', 'size'	  => '75' ), $value);
		$fieldHTML .= '<p style="margin-bottom:0px;">'.__('Subscribers can choose from these lists :', WYSIJA).'</p>';
		$model_list = WYSIJA::get('list', 'model');
		$lists	  = $model_list->get(array( 'name', 'list_id', 'is_public' ), array( 'is_enabled' => 1 ));

		usort($lists, array( $this, 'sort_by_name' ));
		foreach ($lists as $list) {
			if ($list['is_public'])
				$checked = true;
			else
				$checked = false;

			$fieldHTML.= '<p class="labelcheck"><label for="'.$key.'list-'.$list['list_id'].'">'.$formsHelp->checkbox(array( 'id'   => $key.'list-'.$list['list_id'],
						'name' => 'wysija[config]['.$key.'_lists][]' ), $list['list_id'], $checked).$list['name'].'</label></p>';
		}
		$fieldHTML.='</div>';


		return $fieldHTML;
	}

	function fieldFormHTML_viewinbrowser($key, $value, $model, $paramsex) {
		/* second part concerning the checkbox */
		$formsHelp = WYSIJA::get('forms', 'helper');
		$checked   = false;
		if ($this->model->getValue($key))
			$checked   = true;
		$field	 = '<p><label for="'.$key.'">';
		$field.=$formsHelp->checkbox(array( 'id'	=> $key, 'name'  => 'wysija['.$model.']['.$key.']', 'class' => 'activateInput' ), 1, $checked);
		$field.='</label>';
		$value  = $this->model->getValue($key.'_linkname');

		$field.=$formsHelp->input(array( "id"   => $key.'_linkname', 'name' => 'wysija['.$model.']['.$key.'_linkname]', 'size' => '75' ), $value).'</p>';

		return $field;
	}

	function fieldFormHTML_page_selection($key, $value, $model, $paramsex) {
		$model_user	= WYSIJA::get('user', 'model');
		$action_takens = array( 'confirmation_page'  => 'subscribe', 'unsubscribe_page'   => 'unsubscribe', 'subscriptions_page' => 'subscriptions' );
		$pages			   = array( $this->model->getValue('confirm_email_link') => __('MailPoet page', WYSIJA) );
		$pages_objects							   = get_pages();


		foreach ($pages_objects as $page_object) {
			$pages[$page_object->ID] = $page_object->post_title;
		}

		$preview_edit_links = '<span id="'.$key.'-links">';
		foreach ($pages as $page_id => $page_title) {

			$preview_link = $model_user->getConfirmLink(false, $action_takens[$key], false, true, '_blank', $page_id);
			$edit_link	= get_edit_post_link($page_id);

			$preview_edit_links .= '<span id="'.$key.'-links-'.$page_id.'" class="links-page">';
			$preview_edit_links .= '<a href="'.$preview_link.'" target="_blank" title="'.__('Preview', WYSIJA).'">'.__('Preview', WYSIJA).'</a>';
			$preview_edit_links .= ' | <a href="'.$edit_link.'" target="_blank" title="'.__('Edit', WYSIJA).'">'.__('Edit', WYSIJA).'</a>';
			$preview_edit_links .= '</span>';
		}
		$preview_edit_links .= '</span>';

		$helper_forms = WYSIJA::get('forms', 'helper');
		$field		= $helper_forms->dropdown(array( 'id'	=> $key, 'class' => 'page_select', 'name'  => 'wysija['.$model.']['.$key.']' ), $pages, $this->model->getValue($key));
		$field .= $preview_edit_links;

		return $field;
	}

	function fieldFormHTML_cron($key, $value, $model) {
		//second part concerning the checkbox
		$helper_forms = WYSIJA::get('forms', 'helper');
		$checked	  = false;
		if ($this->model->getValue($key))
			$checked	  = true;

		$field = '<div><div class="cronleft"><label for="'.$key.'">';
		$field .= $helper_forms->checkbox(array( 'id'	=> $key, 'name'  => 'wysija['.$model.']['.$key.']', 'class' => 'activateInput' ), 1, $checked);
		$field .= '</label></div>';

		$url_cron = site_url('wp-cron.php').'?'.WYSIJA_CRON.'&action=wysija_cron&process=all';
		$field .= '<div class="cronright" id="'.$key.'_linkname">';

		$text_cron_manual_trigger = __('I\'ll setup a cron job on my server to execute at the frequency I want. Read about [link]setting up a cron job yourself[/link].', WYSIJA).'<br/><span>'.__('Use this URL in your cron job: [cron_url]').'</span>';
		$text_cron_manual_trigger = str_replace(array( '[link]', '[/link]', '[cron_url]' ), array( '<a href="http://support.mailpoet.com/knowledgebase/configure-cron-job/?utm_source=wpadmin&utm_campaign=advanced_settings" title="Seting up cron job" target="_blank">', '</a>', '<a href="'.$url_cron.'" target="_blank">'.$url_cron.'</a>' ), $text_cron_manual_trigger);

		$text_cron_page_view	  = __('No thanks! I have enough visitors on my site. Their visits will trigger MailPoet\'s cron automatically.', WYSIJA);
		$values_page_view_trigger = array( 2 => $text_cron_manual_trigger, 1 => $text_cron_page_view );

		$value = 2;
		if ((int)$this->model->getValue('cron_page_hit_trigger') === 1)
			$value = 1;

		$key = 'cron_page_hit_trigger';

		$content_radios = $helper_forms->radios(array( 'id'   => $key, 'name' => 'wysija['.$model.']['.$key.']' ), $values_page_view_trigger, $value);
		add_filter('wysija_extend_cron_config', array( $this, 'add_text_cron_premium' ));
		$field .= apply_filters('wysija_extend_cron_config', $content_radios);

		$field .= '</div></div>';

		// replace the variable in the text
		$field = str_replace(array( '[link]', '[/link]', '[cron_url]' ), array( '<a href="http://support.mailpoet.com/knowledgebase/configure-cron-job/?utm_source=wpadmin&utm_campaign=advanced_settings" title="Seting up cron job" target="_blank">', '</a>', '<a href="'.$url_cron.'" target="_blank">'.$url_cron.'</a>' ), $field);

		return $field;
	}

	// TODO remove that function which is in the premium plugin
	function add_text_cron_premium($content) {
		if (WYSIJA::is_plugin_active('wysija-newsletters-premium/index.php') && $this->model->getValue('premium_key')) {

			$content = __('I\'m a premium user, MailPoet.com will make sure my emails get sent on time.', WYSIJA).'<br/>';
			$content .= __('If I want I can [link]create an additional cron job[/link] on my end to increase the sending frequency.', WYSIJA).'<br/><span>'.__('Use this URL in your cron job: [cron_url]').'</span>';
		}
		return $content;
	}

	function fieldFormHTML_cron_prem($key, $value, $model) {

		$url_cron = site_url('wp-cron.php').'?'.WYSIJA_CRON.'&action=wysija_cron&process=all';
		$field	= '<p>';

		$text_cron_manual_trigger = __('If I want I can [link]create an additional cron job[/link] on my end to increase the frequency.', WYSIJA).'<br/><span>'.__('Use this URL in your cron job: [cron_url]').'</span>';
		$field .= str_replace(array( '[link]', '[/link]', '[cron_url]' ), array( '<a href="http://support.mailpoet.com/knowledgebase/configure-cron-job/?utm_source=wpadmin&utm_campaign=advanced_settings" title="Seting up cron job" target="_blank">', '</a>', '<a href="'.$url_cron.'" target="_blank">'.$url_cron.'</a>' ), $text_cron_manual_trigger);

		$field .= '</p>';

//        $model_config = WYSIJA::get('config', 'model');
//        if ($model_config->getValue('cron_manual') !== true) {
//            $model_config->save(array('cron_manual' => true));
//
//            $helper_licence = WYSIJA::get('licence', 'helper');
//            $helper_licence->check(true);
//        }

		return $field;
	}

	function fieldFormHTML_debugnew($key, $value, $model, $paramsex) {
		/* second part concerning the checkbox */
		$formsHelp = WYSIJA::get('forms', 'helper');
		$selected  = $this->model->getValue($key);
		if (!$selected)
			$selected  = 0;
		$field	 = '<p><label for="'.$key.'">';
		$options   = array( 0 => 'off', 1 => 'SQL queries', 2 => '&nbsp;+extra data', 3 => '&nbsp;&nbsp;+safe PHP errors' );
		$field.=$formsHelp->dropdown(array( 'id'   => $key, 'name' => 'wysija['.$model.']['.$key.']' ), $options, $selected);
		$field.='</label></p>';

		return $field;
	}

	function fieldFormHTML_debuglog($key, $value, $model, $paramsex) {
		/* second part concerning the checkbox */
		$formsHelp = WYSIJA::get('forms', 'helper');

		$lists = array( 'cron', 'post_notif', 'query_errors', 'queue_process', 'manual' );

		$fieldHTML = '<div id="'.$key.'_linkname'.'" class="checkbox_optin_value">';
		foreach ($lists as $list) {
			$checked = false;
			if ($this->model->getValue($key.'_'.$list))
				$checked = true;

			$fieldHTML.= '<p class="labelcheck"><label for="'.$key.'list-'.$list.'">'.$formsHelp->checkbox(array( 'id'   => $key.'list-'.$list,
						'name' => 'wysija[config]['.$key.'_'.$list.'][]' ), 1, $checked).$list.'</label></p>';
		}
		$fieldHTML.='</div>';

		return $fieldHTML;
	}

	function fieldFormHTML_dkim($key, $value, $model, $paramsex) {

		$field		= '';
		$keypublickey = $key.'_pubk';

		//there is no public key, this is the first time we load that function so we need to generate the dkim
		if (!$this->model->getValue($keypublickey)) {
			//refresh the public key private key generation
			$helpersLi = WYSIJA::get('licence', 'helper');
			$helpersLi->dkim_config();
		}

		WYSIJA::update_option('dkim_autosetup', false);
		$formsHelp = WYSIJA::get('forms', 'helper');

		$realkey = $key.'_active';
		$checked = false;
		if ($this->model->getValue($realkey))
			$checked = true;

		$field.='<p>';
		$field.=$formsHelp->checkbox(array( 'id'	=> $realkey, 'name'  => 'wysija['.$model.']['.$realkey.']', 'style' => 'margin-left:0px;', 'class' => 'activateInput' ), 1, $checked);
		$field.='</p>';

		$field.='<div id="'.$realkey.'_linkname" >';
		//$titlelink=str_replace(array('[link]','[\link]'), array('<a href="">','</a>'),'');
		$titlelink = __('Configure your DNS by adding a key/value record in TXT as shown below.', WYSIJA).' <a href="http://support.mailpoet.com/knowledgebase/guide-to-dkim-in-wysija/?utm_source=wpadmin&utm_campaign=settings" target="_blank">'.__('Read more', WYSIJA).'</a>';
		$field.='<fieldset style=" border: 1px solid #ccc;margin: 0;padding: 10px;"><legend>'.$titlelink.'</legend>';

		$field.='<label id="drlab" for="domainrecord">'.__('Key', WYSIJA).' <input readonly="readonly" id="domainrecord" style="margin-right:10px;" type="text" value="wys._domainkey"/></label><label id="drpub" for="dkimpub">'.__('Value', WYSIJA).' <input readonly="readonly" id="dkimpub" type="text" size="70" value="v=DKIM1;s=email;t=s;p='.$this->model->getValue($keypublickey).'"/></label>';

		//the DKIM key is not a 1024 bits it is therefore obsolete
		if (!$this->model->getValue('dkim_1024')) {
			$stringRegenerate = __('You\'re using an older DKIM key which is unsupported by Gmail.', WYSIJA).' '.__('You\'ll need to update your DNS if you upgrade.', WYSIJA);
			$field.='<p><strong>'.$stringRegenerate.'</strong></p>';
			$field.='<p><input type="hidden" id="dkim_regenerate" value="0" name="wysija[config][dkim_regenerate]"><a id="button-regenerate-dkim" class="button-secondary" href="javascript:;">'.__('Upgrade DKIM key', WYSIJA).'</a></p>';
		}

		$field.='</fieldset>';
		$realkey = $key.'_domain';
		$field.='<p><label class="dkim" for="'.$realkey.'">'.__('Domain', WYSIJA).'</label>';

		$field.=$formsHelp->input(array( 'id'   => $realkey, 'name' => 'wysija['.$model.']['.$realkey.']' ), $this->model->getValue($realkey));
		$field.='</p>';

		$field.='</div>';

		return $field;
	}

	function fieldFormHTML_debug($key, $value, $model, $paramsex) {
		/* second part concerning the checkbox */
		$formsHelp = WYSIJA::get('forms', 'helper');
		$checked   = false;
		if ($this->model->getValue($key))
			$checked   = true;
		$field	 = '<p><label for="'.$key.'">';
		$field.=$formsHelp->checkbox(array( 'id'   => $key, 'name' => 'wysija['.$model.']['.$key.']' ), 1, $checked);
		$field.='</label></p>';

		return $field;
	}

	function fieldFormHTML_capabilities($key, $value, $model, $paramsex) {
		/* second part concerning the checkbox */
		$formsHelp = WYSIJA::get('forms', 'helper');

		$field = '<table cellspacing="0" cellpadding="3" class="wp-list-table widefat fixed capabilities_form">
	<thead>
		<tr>
<th class="rolestitle" style="width:200px">'.__('Roles and permissions', WYSIJA).'</th>';

		$wptools		= WYSIJA::get('wp_tools', 'helper');
		$editable_roles = $wptools->wp_get_roles();


		foreach ($editable_roles as $role) {
			$field.='<th class="rolestable" >'.translate_user_role($role['name']).'</th>';
		}

		$field.='</tr></thead><tbody>';

		$alternate = true;
		$count	 = 1;
		$is_hidden = false;
		foreach ($this->model->capabilities as $keycap => $capability) {
			$classAlternate = array( );
			if ($alternate)
				$classAlternate[] = 'alternate';
			if ($count > 2) {
				$classAlternate[] = 'hidden';
				$is_hidden		= true;
			}
			$classAlternate   = implode(' ', $classAlternate);
			$classAlternate   = ' class="'.$classAlternate.'" ';
			$field.='<tr'.$classAlternate.'><td class="title"><p class="description">'.$capability['label'].'</p></td>';

			foreach ($editable_roles as $role) {
				$checked  = false;
				$keycheck = 'rolescap---'.$role['key'].'---'.$keycap;

				//if($this->model->getValue($keycheck))   $checked=true;
				$checkboxparams = array( 'id'   => $keycheck, 'name' => 'wysija['.$model.']['.$keycheck.']' );
				if (in_array($role['key'], array( 'administrator', 'super_admin' ))) {
					$checkboxparams['disabled'] = 'disabled';
				}

				$roling = get_role($role['key']);

				// add "organize_gallery" to this role object
				if ($roling->has_cap('wysija_'.$keycap)) {
					$checked = true;
				}

				$field.='<td class="rolestable" >'.$formsHelp->checkbox($checkboxparams, 1, $checked).'</td>';
			}

			$field.='</tr>';
			$alternate = !$alternate;
			$count++;
		}
		if ($is_hidden) {
			$classAlternate = '';
			if ($alternate)
				$classAlternate = ' class="alternate" ';
			$field.= '<tr'.$classAlternate.'><td colspan="'.(count($editable_roles) + 1).'"><a class="view_all" href="javascript:void(0);">'.__('View all', WYSIJA).'</a></td></tr>';
		}

		$field.='</tbody></table>';
		return $field;
	}

	function fieldFormHTML_email_notifications($key, $value, $model, $paramsex) {
		/* first part concerning the field itself */
		$params = array( );
		$params['type'] = 'default';
		$params['size'] = 38;
		$field		  = $this->fieldHTML($key, $value, $model, $params);

		/* second part concerning the checkbox */
		$threecheck = array(
			'_when_sub'		  => __('When someone subscribes', WYSIJA)
			, '_when_unsub'		=> __('When someone unsubscribes', WYSIJA),
			'_when_dailysummary' => __('Daily summary of emails sent', WYSIJA)
				//,"_when_bounce"=>__('When an email bounces',WYSIJA)
		);
		$formsHelp		   = WYSIJA::get('forms', 'helper');
		foreach ($threecheck as $keycheck => $checkobj) {
			$checked = false;
			if ($this->model->getValue($key.$keycheck))
				$checked = true;
			$field.='<p><label for="'.$key.$keycheck.'">';
			$field.=$formsHelp->checkbox(array( "id"   => $key.$keycheck, 'name' => 'wysija['.$model.']['.$key.$keycheck.']' ), 1, $checked);
			$field.=$checkobj.'</label></p>';
		}

		return $field;
	}

	function fieldFormHTML_selfsigned($key, $value, $model, $params) {

		$formsHelp = WYSIJA::get('forms', 'helper');

		$realvalue = $this->model->getValue($key);

		$value   = 0;
		$checked = false;
		if ($value == $realvalue)
			$checked = true;
		$id	  = str_replace('_', '-', $key).'-'.$value;
		$field   = '<label for="'.$id.'">';
		$field.=$formsHelp->radio(array( "id"   => $id, 'name' => 'wysija['.$model.']['.$key.']' ), $value, $checked);
		$field.=__('No', WYSIJA).'</label>';

		$value   = 1;
		$checked = false;
		if ($value == $realvalue)
			$checked = true;
		$id	  = str_replace('_', '-', $key).'-'.$value;
		$field.='<label for="'.$id.'">';
		$field.=$formsHelp->radio(array( "id"   => $id, 'name' => 'wysija['.$model.']['.$key.']' ), $value, $checked);
		$field.=__('Yes', WYSIJA).'</label>';

		return $field;
	}

	function tabs($current = null) {
		$tabs = array(
			'basics'			 => _x('Basics', 'settings tab title', WYSIJA),
			'forms'			  => _x('Forms', 'settings tab title', WYSIJA),
			'signupconfirmation' => _x('Signup Confirmation', 'settings tab title', WYSIJA),
			'sendingmethod'	  => _x('Send With...', 'settings tab title', WYSIJA),
			'advanced'		   => _x('Advanced', 'settings tab title', WYSIJA),
			'add-ons'			=> _x('Add-ons', 'settings tab title', WYSIJA),
		);

		if (!$this->_user_can('change_sending_method'))
			unset($tabs['sendingmethod']);

		$tabs = apply_filters('wysija_extend_settings', $tabs);
		echo '<div id="icon-options-general" class="icon32"><br /></div>';
		echo '<h2 id="wysija-tabs" class="nav-tab-wrapper">';

		foreach ($tabs as $tab => $name) {
			$class = ($tab === $current) ? ' nav-tab-active' : '';
			$extra = ($tab === 'premium') ? ' premium' : '';
			echo "<a class='nav-tab$class$extra' href='#$tab'>$name</a>";
		}
		echo '</h2>';
	}

	function save($data) {
		$this->main($data);
	}

	/**
	 *
	 * @param string $action
	 * @return boolean
	 */
	function _user_can($action) {
		if (empty($action))
			return false;
		$is_network_admin = WYSIJA::current_user_can('manage_network');

		//$is_network_admin=true;//PROD comment that line
		if ($is_network_admin)
			return true;

		$is_multisite = is_multisite();

		//$is_multisite=true;//PROD comment that line
		switch ($action) {
			case 'change_sending_method':
				if ((!$is_multisite || ($is_multisite && $this->model->getValue('ms_allow_admin_sending_method'))) && WYSIJA::current_user_can('switch_themes')
				) {
					return true;
				}
				return false;
				break;
			case 'toggle_signup_confirmation':
				if ((!$is_multisite || ($is_multisite && $this->model->getValue('ms_allow_admin_toggle_signup_confirmation'))) && WYSIJA::current_user_can('switch_themes')) {
					return true;
				}
				return false;
				break;
		}
	}

	function main($data) {
		$is_multisite	 = is_multisite();
		$is_network_admin = WYSIJA::current_user_can('manage_network');
		//$is_network_admin=$is_multisite=true;//PROD comment that line

		if ($is_multisite && $is_network_admin) {
			add_filter('wysija_extend_settings', array( $this, 'ms_tab_name' ), 12);
			add_filter('wysija_extend_settings_content', array( $this, 'ms_tab_content' ), 12, 2);
		}

		// check for debug
		if (isset($_REQUEST['wysija_debug'])) {
			switch ((int)$_REQUEST['wysija_debug']) {
				// turn off debug
				case 0:
					WYSIJA::update_option('debug_on', false);
					WYSIJA::update_option('debug_new', false);
					break;

				// turn on debug (with debug level as value)
				case 1:
				case 2:
				case 3:
				case 4:
				case 99:
					WYSIJA::update_option('debug_on', true);
					WYSIJA::update_option('debug_new', (int)$_REQUEST['wysija_debug']);
					break;
			}
		}

		echo $this->messages();
		?>
		<div id="wysija-config">
			<?php $this->tabs(); ?>
			<form name="wysija-settings" method="post" id="wysija-settings" action="" class="form-valid" autocomplete="off">
				<div id="basics" class="wysija-panel hidden">
					<?php $this->basics(); ?>
					<p class="submit">
						<input type="submit" value="<?php echo esc_attr(__('Save settings', WYSIJA)); ?>" class="button-primary wysija" />
					</p>
				</div>
				<div id="forms" class="wysija-panel hidden">
					<?php /* if(WYSIJA::is_wysija_admin()) */ $this->form_list(); ?>
				</div>

				<div id="signupconfirmation" class="wysija-panel hidden">
					<?php $this->signupconfirmation(); ?>
					<p class="submit">
						<input type="submit" value="<?php echo esc_attr(__('Save settings', WYSIJA)); ?>" class="button-primary wysija" />
					</p>
				</div>
				<?php
				if ($this->_user_can('change_sending_method')) {
					?>
					<div id="sendingmethod" class="wysija-panel hidden">
						<?php $this->sendingmethod(); ?>
						<p class="submit">
							<input type="submit" value="<?php echo esc_attr(__('Save settings', WYSIJA)); ?>" class="button-primary wysija" />
						</p>
					</div>
					<?php
				}
				?>


				<div id="advanced" class="wysija-panel hidden">
					<?php $this->advanced($data); ?>
					<p class="submit">
						<input type="submit" value="<?php echo esc_attr(__('Save settings', WYSIJA)); ?>" class="button-primary wysija" />
					</p>
				</div>

				<div id="add-ons" class="wysija-panel hidden">
					<?php $this->add_ons(); ?>
				</div>

				<?php
				echo apply_filters('wysija_extend_settings_content', '', array( 'viewObj' => &$this ));
				?>

				<?php $this->secure(array( 'action' => "save" )); ?>
				<input type="hidden" value="save" name="action" />
				<input type="hidden" value="" name="redirecttab" id="redirecttab" />
			</form>
		</div>
		<?php
	}

	function basics() {
		$step = array( );

		$step['company_address'] = array(
			'type'  => 'textarea',
			'label' => __("Your company's address", WYSIJA),
			'desc'  => __("The address will be added to your newsletter's footer. This helps avoid spam filters.", WYSIJA),
			'rows'  => "3",
			'cols'  => "40", );

		$step['emails_notified'] = array(
			'type'  => 'email_notifications',
			'label' => __('Email notifications', WYSIJA),
			'desc'  => __('Enter the email addresses that should receive notifications (separate by comma).', WYSIJA) );

		$step['from_name'] = array(
			'type'  => 'fromname',
			'class' => 'validate[required]',
			'label' => __('Sender of notifications', WYSIJA),
			'desc'  => __('Choose a FROM name and email address for notifications emails.', WYSIJA) );

		$step['commentform'] = array(
			'type'  => 'commentform',
			'label' => __('Subscribe in comments', WYSIJA),
			'desc'  => __('Visitors who submit a comment on a post can click on a checkbox to subscribe.', WYSIJA),
		);

		$showregisteroption = true;
		//this option is only available for the main site
		if (is_multisite()) {
                    $mpoet_allow_register_option_on_all_sites = false;
                    $mpoet_allow_register_option_on_all_sites = apply_filters( 'mpoet_allow_register_option_on_all_sites', $mpoet_allow_register_option_on_all_sites );
                    if( $mpoet_allow_register_option_on_all_sites === false && !is_main_site() ){
                        $showregisteroption = false;
                    }
		}

		if ($showregisteroption) {
			$step ['registerform'] = array(
				'type'  => 'commentform',
				'label' => __('Subscribe in registration form', WYSIJA),
				'desc'  => __('Allow users who register to your site to subscribe on a list of your choice.', WYSIJA)
			);
		}

		$modelU				= WYSIJA::get('user', 'model');
		$objUser			   = $modelU->getCurrentSubscriber();
		$step['viewinbrowser'] = array(
			'type'  => 'viewinbrowser',
			'label' => __('Link to browser version', WYSIJA),
			'desc'  => __('Displays at the top of your newsletters. Don\'t forget to include the link tag, ie: [link]The link[/link]', WYSIJA),
		);

		$step['unsubscribe_linkname'] = array(
			'type'  => 'input',
			'label' => __('Text of "Unsubscribe" link', WYSIJA),
			'desc'  => __('This changes the label for the unsubscribe link in the footer of your newsletters.', WYSIJA) );

		$step['unsubscribe_page'] = array(
			'type'  => 'page_selection',
			'label' => __('Unsubscribe page', WYSIJA),
			'desc'  => __('A subscriber is directed to a page of your choice after clicking on the unsubscribe link, at the bottom of a newsletter.', WYSIJA),
		);

		$model_config = WYSIJA::get('config', 'model');
		?>
		<table class="form-table">
			<tbody>
				<?php
				do_action('mailpoet_pre_config_screen');
				echo $this->buildMyForm($step, $model_config->values, 'config');
				?>
			</tbody>
		</table>
		<?php
	}

	function signupconfirmation() {
		$step = array( );

		$step['confirm_dbleoptin'] = array(
			'type'   => 'radio',
			'values' => array( true	=> __('Yes', WYSIJA), false   => __('No', WYSIJA) ),
			'label' => __('Enable signup confirmation', WYSIJA),
			'desc'  => __('Prevent people from being subscribed to your list unwillingly, this option ensures you to keep a clean list.', WYSIJA).' <a href="http://support.mailpoet.com/knowledgebase/why-you-should-enforce-email-activation/?utm_source=wpadmin&utm_campaign=activation email" target="_blank">'.__('Learn more.', WYSIJA).'</a>' );

		if (!$this->_user_can('toggle_signup_confirmation')) {
			$step['confirm_dbleoptin']['type'] = 'disabled_radio';
		}

		$step['confirm_email_title'] = array(
			'type'	 => 'input',
			'label'	=> __('Email subject', WYSIJA),
			'rowclass' => 'confirmemail' );

		$step['confirm_email_body'] = array(
			'type'	 => 'textarea',
			'label'	=> __('Email content', WYSIJA),
			'desc'	 => __('Don\'t forget to include: <br><br>[activation_link]Confirm your subscription.[/activation_link]. <br><br>Optional: [lists_to_confirm].', WYSIJA),
			'rowclass' => 'confirmemail' );

		$step['confirmation_page'] = array(
			'type'	 => 'page_selection',
			'label'	=> __('Confirmation page', WYSIJA),
			'desc'	 => __('When subscribers click on the activation link, they are redirected to a page of your choice.', WYSIJA),
			'rowclass' => 'confirmemail' );
		?>

		<table class="form-table">
			<tbody>
				<?php
				echo $this->buildMyForm($step, '', 'config');
				?>
			</tbody>
		</table>
		<?php
	}

	function sendingmethod() {
		$key	   = 'sending_method';
		$realvalue = $this->model->getValue($key);
		// Since 2.6.12, remove gmail and use stmp instead
		if ($realvalue == 'gmail')
			$realvalue = 'smtp';

		$helper_forms = WYSIJA::get('forms', 'helper');
		$current_user = WYSIJA::wp_get_userdata();
		?>
		<table class="form-table" id="ms-sendingmethod">
			<tbody>
				<tr class="methods">
					<?php
					$is_multisite = is_multisite();

					if (is_multisite()) {
						$field   = '<th scope="row">';
						$checked = false;
						$value   = 'network';
						$id	  = str_replace('_', '-', $key).'-'.$value;
						if ($value == $realvalue) {
							$checked = true;
						}
						$field .= '<label for="'.$id.'" class="clearfix">';
						$field .= $helper_forms->radio(
								array(
							'id'	=> $id,
							'class' => 'mailpoet-delivery-method',
							'name'  => 'wysija[config]['.$key.']',
								), $value, $checked
						);
						$field .= '<h3>'.__('Network\'s Method', WYSIJA).'</h3></label>';
						$field .= '<p>'.__('Method set by the network admin.', WYSIJA).'</p>';
						if (!$this->model->getValue('ms_sending_emails_ok')) {
							$field .= '<strong'.__('Not Configured!', WYSIJA).'</strong>';
						}
						$field .= '</th>';
						echo $field;
					}
					?>

					<th scope="row">
						<?php
						$checked = false;
						$value   = 'site';
						$id	  = str_replace('_', '-', $key).'-'.$value;
						if ($value == $realvalue)
							$checked = true;
						$field   = '<label for="'.$id.'" class="clearfix">';
						$field.=$helper_forms->radio(array( "id"	 => $id, 'class'  => 'mailpoet-delivery-method', 'name'   => 'wysija[config]['.$key.']' ), $value, $checked);
						$field.='<h3>'.__('Your own website', WYSIJA).'</h3></label>';
						$field.='<p>'.__('The simplest solution for small lists. Your web host sets a daily email limit.', WYSIJA).'</p>';
						echo $field;
						?>
					</th>
					<th scope="row">
						<?php
						$checked = false;
						$value   = 'smtp';
						if ($value === $realvalue)
							$checked = true;

						$id	= str_replace('_', '-', $key).'-'.$value;
						$field = '<label for="'.$id.'" class="clearfix">';
						$field.= $helper_forms->radio(array( 'id'	   => $id, 'class'	=> 'mailpoet-delivery-method', 'name'	 => 'wysija[config]['.$key.']' ), $value, $checked);
						$field.= '<h3>'.__('Third party', WYSIJA).'</h3></label>';
						$field.='<p>'.__('Send with a professional SMTP provider, a great choice for big and small lists. We\'ve negotiated promotional offers with a few providers for you.', WYSIJA).' <a href="http://support.mailpoet.com/knowledgebase/send-with-smtp-when-using-a-professional-sending-provider/?utm_source=wpadmin&utm_campaign=sending method" target="_blank">'.__('Read more', WYSIJA).'</a>.</p>';
						echo $field;
						?>
					</th>
				</tr>

				<tr class="hidechoice choice-sending-method-site">
					<th scope="row">
						<?php
						$field	 = __('Delivery method', WYSIJA);
						$field.='<p class="description">'.__('Send yourself some test emails to confirm which method works with your server.', WYSIJA).'</p>';
						echo $field;
						?>
					</th>
					<td colspan="2">
						<?php
						$key	   = "sending_emails_site_method";
						$checked   = false;
						$realvalue = $this->model->getValue($key);
						$value	 = "phpmail";
						if ($value == $realvalue)
							$checked   = true;

						$id	= str_replace("_", '-', $key).'-'.$value;
						$field = '<p class="title"><label for="'.$id.'">';
						$field.=$helper_forms->radio(array( "id"   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
						$field.='PHP Mail</label></p>';
						$field.='<p class="description">'.__('This email engine works on 95&#37; of servers', WYSIJA).'</p>';


						$value   = "sendmail";
						$checked = false;
						if ($value == $realvalue)
							$checked = true;

						$id = str_replace("_", '-', $key).'-'.$value;
						$field.='<p class="title"><label for="'.$id.'">';
						$field.=$helper_forms->radio(array( "id"   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
						$field.='Sendmail</label>';
						$field.='<p class="description">'.__('This method works on 5&#37; of servers', WYSIJA).'</p>';

						$id = str_replace("_", '-', $key).'-'.$value."-path";
						$field.='<p class="title" id="p-'.$id.'"><label for="'.$id.'">';
						$field.=__("Sendmail path", WYSIJA).'</label>'.$helper_forms->input(array( "id"   => $id, 'name' => 'wysija[config][sendmail_path]' ), $this->model->getValue("sendmail_path")).'</p>';


						if ($this->model->getValue('allow_wpmail')) {
							$checked = false;
							$value   = 'wpmail';
							if ($value == $realvalue)
								$checked = true;

							$id = str_replace('_', '-', $key).'-'.$value;
							$field.='<p class="title"><label for="'.$id.'">';
							$field.=$helper_forms->radio(array( "id"   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
							$field.='WP Mail</label></p>';
							$field.='<p class="description">'.__('Use the same method as the one used for your WP site.', WYSIJA).'</p>';
						}


						echo $field;
						?>
					</td>
				</tr>

				<tr class="hidechoice choice-sending-method-smtp">
					<th scope="row">
						<?php
						$key   = "smtp_host";
						$id	= str_replace("_", '-', $key);
						$field = '<label for="'.$id.'">'.__('SMTP Hostname', WYSIJA)."</label>";
						$field.='<p class="description">'.__('e.g.:smtp.mydomain.com', WYSIJA).'</p>';
						echo $field;
						?>
					</th>
					<td>
						<?php
						$value = $this->model->getValue($key);
						$field = $helper_forms->input(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']', 'size' => '40' ), $value, $checked);
						echo $field;
						?>
					</td>
				</tr>
				<tr class="hidechoice choice-sending-method-smtp choice-sending-method-gmail">
					<th scope="row">
						<?php
						$key   = "smtp_login";
						$id	= str_replace("_", '-', $key);
						$field = '<label for="'.$id.'">'.__('Login', WYSIJA)."</label>";
						echo $field;
						?>
					</th>
					<td colspan="2">
						<?php
						$value = $this->model->getValue($key);
						$field = $helper_forms->input(array( "id"   => $id, 'name' => 'wysija[config]['.$key.']', 'size' => '40' ), $value, $checked);
						echo $field;
						?>
					</td>
				</tr>
				<tr class="hidechoice choice-sending-method-smtp choice-sending-method-gmail">
					<th scope="row">
						<?php
						$key   = "smtp_password";
						$id	= str_replace("_", '-', $key);
						$field = '<label for="'.$id.'">'.__('Password', WYSIJA)."</label>";
						echo $field;
						?>
					</th>
					<td colspan="2">
						<?php
						$value = $this->model->getValue($key);
						$field = $helper_forms->input(array( "type"   => "password", "id"	 => $id, 'name'   => 'wysija[config]['.$key.']', 'size'   => '40' ), $value, $checked);
						echo $field;
						?>
					</td>
				</tr>
				<tr id="restapipossible" class="hidechoice">
					<th scope="row">
						<?php
						$key	 = 'smtp_rest';
						$id	  = str_replace('_', '-', $key);
						$field   = '<label for="'.$id.'">web API</label>';
						$field.='<p class="description">'.__('Activate if your SMTP ports are blocked.', WYSIJA).'</p>';
						echo $field;
						?>
					</th>
					<td>
						<?php
						$value   = $this->model->getValue($key);
						$checked = false;
						if ($this->model->getValue('smtp_rest'))
							$checked = true;
						$field   = $helper_forms->checkbox(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']', 'size' => '3' ), 1, $checked);

						echo $field;
						?>
					</td>
				</tr>

				<tr class="hidechoice choice-sending-method-smtp choice-no-restapi">
					<th scope="row">
						<?php
						$key   = 'smtp_port';
						$id	= str_replace('_', '-', $key);
						$field = '<label for="'.$id.'">'.__('SMTP port', WYSIJA)."</label>";

						echo $field;
						?>
					</th>
					<td>
						<?php
						$value = $this->model->getValue($key);
						$field = $helper_forms->input(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']', 'size' => '3' ), $value, $checked);

						echo $field;
						?>
					</td>
				</tr>

				<tr class="hidechoice choice-sending-method-smtp choice-no-restapi">
					<th scope="row">
						<?php
						$key   = "smtp_secure";
						$id	= str_replace("_", '-', $key);
						$field = '<label for="'.$id.'">'.__('Secure connection', WYSIJA)."</label>";
						echo $field;
						?>
					</th>
					<td colspan="2">
						<?php
						$value = $this->model->getValue($key);

						$field = $helper_forms->dropdown(array( 'name' => 'wysija[config]['.$key.']', "id"   => $id ), array( false	  => __("No"), "ssl"	  => "SSL", "tls"	  => "TLS" ), $value);
						echo $field;
						?>
					</td>
				</tr>

				<tr class="hidechoice choice-sending-method-smtp choice-no-restapi">
					<th scope="row">
						<?php
						$field	 = __('Authentication', WYSIJA);
						echo $field.'<p class="description">'.__("Leave this option to Yes. Only a tiny portion of SMTP services ask Authentication to be turned off.", WYSIJA).'</p>';
						?>
					</th>
					<td colspan="2">
						<?php
						$key	   = 'smtp_auth';
						$realvalue = $this->model->getValue($key);

						$value   = false;
						$checked = false;
						if ($value == $realvalue)
							$checked = true;
						$id	  = str_replace('_', '-', $key).'-'.$value;
						$field   = '<label for="'.$id.'">';
						$field.=$helper_forms->radio(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
						$field.=__('No', WYSIJA).'</label>';

						$value   = true;
						$checked = false;
						if ($value == $realvalue)
							$checked = true;
						$id	  = str_replace('_', '-', $key).'-'.$value;
						$field.='<label for="'.$id.'">';
						$field.=$helper_forms->radio(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
						$field.=__('Yes', WYSIJA).'</label>';


						$value   = false;
						$checked = false;
						if ($value == $realvalue)
							$checked = true;
						$id	  = str_replace('_', '-', $key).'-'.$value;
						$field   = '<label for="'.$id.'">';
						$field.=$helper_forms->radio(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
						$field.=__('No', WYSIJA).'</label>';

						$value   = true;
						$checked = false;
						if ($value == $realvalue)
							$checked = true;
						$id	  = str_replace('_', '-', $key).'-'.$value;
						$field.='<label for="'.$id.'">';
						$field.=$helper_forms->radio(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
						$field.=__('Yes', WYSIJA).'</label>';


						echo $field;
						?>
					</td>
				</tr>

				<tr class="hidechoice choice-sending-method-smtp choice-sending-method-site choice-sending-method-gmail choice-sending-method-network">
					<th scope="row">
						<?php _e('Test method'); ?>
					</th>
					<td colspan="<?php echo ( is_multisite() ? 3 : 2 ); ?>">
						<input type="text" value="<?php echo esc_attr($current_user->data->user_email); ?>" class="mailpoet-test-emails" name="wysija[config][test_mails]" />
						<a class="button-secondary mailpoet-test-delivery"><?php _e('Send a test mail', WYSIJA); ?></a>
					</td>
				</tr>

				<tr class="hidechoice choice-sending-method-smtp choice-sending-method-site choice-sending-method-gmail">
					<th scope="row">
						<?php
						$field = __('Send...', WYSIJA);

						echo $field.'<p class="description">'.str_replace(array( '[link]', '[/link]' ), array( '<a href="http://support.mailpoet.com/knowledgebase/wp-cron-batch-emails-sending-frequency/?utm_source=wpadmin&utm_campaign=choosing%20frequency" target="_blank">', '</a>' ), __('Your web host has limits. We suggest 70 emails per hour to be safe. [link]Find out more[/link].', WYSIJA)).'</p>';
						?>
					</th>
					<td colspan="<?php echo ( is_multisite() ? 3 : 2 ); ?>">

						<?php
						$name   = 'sending_emails_number';
						$id	 = str_replace('_', '-', $name);
						$value  = $this->model->getValue($name);
						$params = array( "id"   => $id, 'name' => 'wysija[config]['.$name.']', 'size' => '6' );
						//if($this->model->getValue("smtp_host")=="smtp.gmail.com") $params["readonly"]="readonly";
						$field = $helper_forms->input($params, $value);
						$field.= '<span class="mailpoet-frequency_inner_texting">'.__('emails', WYSIJA).'</span>';


						$name  = 'sending_emails_each';
						$id	= str_replace('_', '-', $name);
						$value = $this->model->getValue($name);
						$field .=$helper_forms->dropdown(array( 'name' => 'wysija[config]['.$name.']', 'id'   => $id ), $helper_forms->eachValues, $value);
						echo $field;
						echo '<div class="mailpoet-frequency_warning hidden"><b>'.__('This is fast!', WYSIJA).'</b> '.str_replace(array( '[link]', '[/link]' ), array( '<a href="http://support.mailpoet.com/knowledgebase/wp-cron-batch-emails-sending-frequency/?utm_source=wpadmin&utm_campaign=cron" target="_blank">', '</a>' ), __('We suggest you setup a cron job. [link]Read more[/link] on support.mailpoet.com', WYSIJA)).'</span>';
						?>
					</td>
				</tr>

			</tbody>
		</table>
		<script>
		  wysija_translations = {};
		  wysija_translations.api = "<?php _e('API Key', WYSIJA); ?>";
		  wysija_translations.password = "<?php _e('Password', WYSIJA); ?>";
		</script>
		<?php
	}

	function clearlog() {

		echo '<h3>Logs have been cleared</h3>';
	}

	function log() {
		$option_log = get_option('wysija_log');

		foreach ($option_log as $key => $data) {
			echo '<h3>'.$key.'</h3>';
			dbg($data, 0);
		}
	}

	function advanced($data) {

		$advanced_fields	   = $super_advanced_fields = array( );
		$advanced_fields ['role_campaign'] = array(
			'type' => 'capabilities',
			'1col' => 1 );

		$advanced_fields['replyto_name'] = array(
			'type'  => 'fromname',
			'class' => 'validate[required]',
			'label' => __('Reply-to name & email', WYSIJA),
			'desc'  => __('You can change the default reply-to name and email for your newsletters. This option is also used for the activation emails and Admin notifications (in Basics).', WYSIJA) );

		$helper_licence				   = WYSIJA::get('licence', 'helper');
		$url_checkout					 = $helper_licence->get_url_checkout('bounce_address_automated');
		$advanced_fields ['bounce_email'] = array(
			'type'  => 'input',
			'label' => __('Bounce Email', WYSIJA),
			'desc'  => __('To which address should all the bounced emails go? Get the [link]Premium version[/link] to automatically handle these.', WYSIJA),
			'link'  => '<a href="'.$url_checkout.'" target="_blank" title="'.__('Purchase the premium version.', WYSIJA).'">' );

		$advanced_fields = apply_filters('wysija_settings_advanced', $advanced_fields);

		$modelU = WYSIJA::get('user', 'model');

		$advanced_fields ['manage_subscriptions'] = array(
			'type'  => 'managesubscribe',
			'label' => __('Subscribers can edit their profile', WYSIJA),
			'desc'  => __('Add a link in the footer of all your newsletters so subscribers can edit their profile and lists. [link]See your own subscriber profile page.[/link]', WYSIJA),
			'link'  => '<a href="'.$modelU->getConfirmLink(false, 'subscriptions', false, true).'" target="_blank" title="'.__('Preview page', WYSIJA).'">', );

		$advanced_fields['subscriptions_page'] = array(
			'type'	 => 'page_selection',
			'label'	=> __('Subscriber profile page', WYSIJA),
			'desc'	 => __('Select the page to display the subscriber\'s profile.', WYSIJA),
			'rowclass' => 'manage_subscriptions',
		);

		$advanced_fields ['html_source'] = array(
			'label'  => __('Allow HTML edits', WYSIJA),
			'type'   => 'radio',
			'values' => array( true   => __('Yes', WYSIJA), false  => __('No', WYSIJA) ),
			'desc' => __('This allows you to modify the HTML of text blocks in the visual editor.', WYSIJA)
		);

		$advanced_fields ['analytics'] = array(
			'rowclass' => 'analytics',
			'type'	 => 'radio',
			'values'   => array( true	=> __('Yes', WYSIJA), false   => __('No', WYSIJA) ),
			'label' => __('Share anonymous data', WYSIJA),
			'desc'  => __('Share anonymous data and help us improve the plugin. [link]Read more[/link].', WYSIJA),
			'link'  => '<a target="_blank" href="http://support.mailpoet.com/knowledgebase/share-your-data/?utm_source=wpadmin&utm_campaign=advanced_settings">'
		);

		$advanced_fields ['industry'] = array(
			'rowclass' => 'industry',
			'type'	 => 'dropdown_keyval',
			'values'   => array(
				'other'			   => __('other', WYSIJA),
				'art'				 => __('art', WYSIJA),
				'business'			=> __('business', WYSIJA),
				'education'		   => __('education', WYSIJA),
				'e-commerce'		  => __('e-commerce', WYSIJA),
				'food'				=> __('food', WYSIJA),
				'insurance'		   => __('insurance', WYSIJA),
				'government'		  => __('government', WYSIJA),
				'health and sports'   => __('health and sports', WYSIJA),
				'manufacturing'	   => __('manufacturing', WYSIJA),
				'marketing and media' => __('marketing and media', WYSIJA),
				'non profit'		  => __('non profit', WYSIJA),
				'photography'		 => __('photography', WYSIJA),
				'travel'			  => __('travel', WYSIJA),
				'real estate'		 => __('real estate', WYSIJA),
				'religious'		   => __('religious', WYSIJA),
				'technology'		  => __('technology', WYSIJA)
			),
			'label'			   => __('Industry', WYSIJA),
			'desc'				=> __('Select your industry.', WYSIJA) );

		$advanced_fields ['recaptcha'] = array(
			'type'	 => 'radio',
			'values'   => array( true	=> __('Yes', WYSIJA), false   => __('No', WYSIJA) ),
			'label' => __('Enable reCAPTCHA', WYSIJA),
			'desc'  => __('Use reCAPTCHA to protect MailPoet subscription forms. [link]Sign up for an API key pair here[/link].', WYSIJA),
      'link' => '<a target="_blank" href="https://www.google.com/recaptcha/admin">'
		);

		$advanced_fields ['recaptcha_key'] = array(
			'rowclass' => 'recaptcha',
			'type'  => 'input',
			'label' => __('reCAPTCHA site key', WYSIJA),
			'desc' => __('Used in the HTML code your site serves to users.', WYSIJA) );

		$advanced_fields ['recaptcha_secret'] = array(
			'rowclass' => 'recaptcha',
			'type'  => 'input',
			'label' => __('reCAPTCHA secret key', WYSIJA),
			'desc' => __('Used for communication between your site and Google. Be sure to keep it a secret.', WYSIJA) );

		$super_advanced_fields ['subscribers_count'] = array(
			'type'  => 'subscribers_count',
			'label' => __('Shortcode to display total number of subscribers', WYSIJA),
			'desc'  => __('Paste this shortcode to display the number of confirmed subscribers in post or page.', WYSIJA)
		);



		$super_advanced_fields ['advanced_charset'] = array(
			'type'   => 'dropdown_keyval',
			'values' => array( 'UTF-8', 'UTF-7', 'BIG5', 'ISO-2022-JP',
				'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3',
				'ISO-8859-4', 'ISO-8859-5', 'ISO-8859-6',
				'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9',
				'ISO-8859-10', 'ISO-8859-13', 'ISO-8859-14',
				'ISO-8859-15', 'Windows-1251', 'Windows-1252' ),
			'label' => __('Charset', WYSIJA),
			'desc'  => __('Squares or weird characters are displayed in your emails? Select the encoding for your language.', WYSIJA) );

		$super_advanced_fields = apply_filters('wysija_settings_advancednext', $super_advanced_fields);

		$super_advanced_fields ['cron_manual'] = array(
			'type'  => 'cron',
			'label' => __('Enable MailPoet\'s Cron', WYSIJA),
			'desc'  => __('None of your queued emails have been sent? Then activate this option.', WYSIJA) );


		$super_advanced_fields ['debug_new'] = array(
			'type'  => 'debugnew',
			'label' => __('Debug mode', WYSIJA),
			'desc'  => __('Enable this to show MailPoet\'s errors. Our support might ask you to enable this if you seek their help.', WYSIJA) );

		if (WYSIJA_DBG > 1) {
			$advanced_fields ['debug_log'] = array(
				'type'  => 'debuglog',
				'label' => 'Logs',
				'desc'  => str_replace(array( '[link]', '[linkclear]', '[/link]', '[/linkclear]' ), array( '<a href="admin.php?page=wysija_config&action=log" target="_blank">', '<a target="_blank" href="admin.php?page=wysija_config&action=clearlog&_wpnonce='.$this->secure(array( 'action' => 'clearlog' ), true).'">', '</a>', '</a>' ), 'View them [link]here[/link]. Clear them [linkclear]here[/linkclear]') );
		}

		//attach 'super-advanced' class to super_advanced_fields
		$super_advanced_field_class = 'super-advanced';
		foreach ($super_advanced_fields as $key => $field) {
			if (!empty($super_advanced_fields[$key]['rowclass']))
				$super_advanced_fields[$key]['rowclass'] = $super_advanced_fields[$key]['rowclass'].' '.$super_advanced_field_class;
			else
				$super_advanced_fields[$key]['rowclass'] = $super_advanced_field_class;
		}
		?>
		<table class="form-table">
			<tbody>
				<?php echo $this->buildMyForm($advanced_fields, '', 'config'); ?>
				<tr class='title_row'>
					<td colspan="2">
						<h3 class='title' alt='<?php esc_attr_e('Do or do not. There is no try.', WYSIJA); ?>'>
							<?php _e("Geeky Options", WYSIJA); ?>
							<a href='#geeky_options' alt='<?php esc_attr_e("Toggle Geeky Options", WYSIJA); ?>' data-hide='<?php esc_attr_e("Hide", WYSIJA); ?>' class='add-new-h2 mailpoet-geeky-toggle is_toggled'><?php esc_attr_e("Show", WYSIJA); ?></a>
						</h3>
					</td>
				</tr>
				<?php if (!empty($data['hooks']['hook_settings_super_advanced'])) echo $data['hooks']['hook_settings_super_advanced']; ?>
				<?php echo $this->buildMyForm($super_advanced_fields, '', 'config'); ?>
				<?php if (current_user_can('delete_plugins')): ?>
					<tr class="<?php echo $super_advanced_field_class ?>">
						<th scope="row">
				<div class="label"><?php _e('Reinstall from scratch', WYSIJA) ?>
					<p class="description"><?php _e('Want to start all over again? This will wipe out MailPoet and reinstall anew.', WYSIJA) ?></p>
				</div>
			</th>
			<td>
				<p>
					<a class="button" href="admin.php?page=wysija_config&action=reinstall"><?php _e('Reinstall now...', WYSIJA); ?></a>
				</p>
			</td>
			</tr>
		<?php endif ?>
		</tbody>
		</table>
		<?php
	}

	/**
	 * Add-ons Manager developed by Sebs Studio
	 */
	function add_ons() {
		include_once(WYSIJA_DIR.'/add-ons/add-ons.php');
	}

	/**
	 * filter adding its own tab to wysija's config(this deals with the name of the tab)
	 * @param string $tabs
	 * @return string
	 */
	function ms_tab_name($tabs) {
		$tabs['multisite'] = 'MS';
		return $tabs;
	}

	/**
	 * filter adding its own tab to wysija's config (this deals with the content of the tab)
	 * @param type $htmlContent
	 * @param type $arg
	 * @return string
	 */
	function ms_tab_content($htmlContent, $arg) {
		$this->viewObj = $arg['viewObj'];
		$mConfig   = WYSIJA::get('config', 'model');
		$formsHelp = WYSIJA::get('forms', 'helper');

		$htmlContent .='<div id="multisite" class="wysija-panel hidden">'; //start multisite div
		$htmlContent.= '<div class="intro"><h3>'.__('Pick your prefered configuration?', WYSIJA).'</h3></div>';

		$htmlContent.= '<table class="form-table" id="form-ms-config">
			<tbody>
				<tr class="methods">
					<th scope="row">';

		$checked   = false;
		$key	   = 'ms_sending_config';
		$realvalue = $mConfig->getValue($key);
		$value	 = 'one-for-all';
		$id		= str_replace('_', '-', $key).'-'.$value;
		if ($value == $realvalue)
			$checked   = true;
		$field	 = '<label for="'.$id.'" class="clearfix">';
		$field.=$formsHelp->radio(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
		$field.='<p class="title"><strong>'.__('One configuration for all sites', WYSIJA).'</strong></p></label>';
		$field.='<p>'.__('Enforce all sites to send with a unique FROM email address. You only need to configure the Automated Bounce Handling (Premium), SPF & DKIM only once.', WYSIJA).'</p>';
		$field.='<p>'.__('Users can still change their reply-to address for their newsletter. Network admins can still edit sending method for each site.', WYSIJA).'</p>';
		$htmlContent.= $field;

		$htmlContent.= '</th><th scope="row">';

		$checked = false;
		$value   = 'one-each';
		$id	  = str_replace('_', '-', $key).'-'.$value;
		if ($value == $realvalue)
			$checked = true;
		$field   = '<label for="'.$id.'" class="clearfix">';
		$field.=$formsHelp->radio(array( "id"   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
		$field.='<p class="title"><strong>'.__('Configure each site manually', WYSIJA).'</strong></p></label>';
		$field.='<p>'.__('Configure SPF and DKIM independently for each site.', WYSIJA).'</p>';
		$htmlContent.= $field;

		$htmlContent.= '</th><td>
					</td>
				</tr>';

		$htmlContent.='</tbody></table>';

		$htmlContent.='<div class="intro"><h3>'.__('Configuration and Permissions', WYSIJA).'</h3></div>';

		$fields = array( );


		$fields['ms_allow_admin_sending_method'] = array(
			'type'											   => 'debug',
			'label'											  => __('Allow site admins to change the sending method', WYSIJA) );
		$fields['ms_allow_admin_toggle_signup_confirmation'] = array(
			'type'  => 'debug',
			'label' => __('Allow site admins to deactivate Signup Confirmation', WYSIJA) );

		$htmlContent.='<table class="form-table"><tbody>';
		$htmlContent.=$this->viewObj->buildMyForm($fields, '', 'config');
		$htmlContent.='</tbody></table>';

		$htmlContent.='<div class="intro"><h3>'.__('Network\'s Default Sending Method', WYSIJA).'</h3></div>';
		$htmlContent.=$this->ms_sending_method();
		if (false) {
			$htmlContent.= '<div class="intro"><h3>'.__('SPF and DKIM', WYSIJA).'</h3></div>';

			$htmlContent.= '<table class="form-table">
				<tbody>
					<tr class="methods">
						<th scope="row">';

			$htmlContent.='<p>'.__('Your SPF record', WYSIJA).'</p>';

			$htmlContent.= '</th>';
			$htmlContent.= '<th scope="row"></th><td></td></tr>';

			$htmlContent.='</tbody></table>';
		}


		$htmlContent .='<p class="submit"><input type="submit" value="'.esc_attr(__('Save settings', WYSIJA)).'" class="button-primary wysija" /></p>';
		$htmlContent.='</div>'; //end multisite div

		return $htmlContent;
	}

	function ms_sending_method() {
		$prefix	   = 'ms_';
		$model_config = WYSIJA::get('config', 'model');
		$helper_forms = WYSIJA::get('forms', 'helper');
		$html_content = '<table class="form-table" id="ms-sendingmethod">
			<tbody>';



		$key = $prefix.'sending_method';

		$realvalue = $model_config->getValue($key);
		$html_content .= '<tr class="methods">
					<th scope="row">';

		$checked = false;
		$value   = 'site';
		$id	  = str_replace("_", '-', $key).'-'.$value;
		if ($value == $realvalue)
			$checked = true;
		$field   = '<label for="'.$id.'" class="clearfix">';
		$field.=$helper_forms->radio(array( "id"   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
		$field.='<h3>'.__('Your own website', WYSIJA).'</h3></label>';
		$html_content.=$field;
		$html_content.='</th>
					<th scope="row">';

		$checked = false;
		$value   = 'smtp';
		if ($value === $realvalue)
			$checked = true;

		$id	= str_replace('_', '-', $key).'-'.$value;
		$field = '<label for="'.$id.'" class="clearfix">';
		$field.= $helper_forms->radio(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
		$field.= '<h3>'.__('Third party', WYSIJA).'</h3></label>';
		$html_content.=$field;
		$html_content.='</th>

					<td>
					</td>
				</tr>';


		$html_content.='<tr>
					<th scope="row">';

		$key   = $prefix.'from_email';
		$id	= str_replace('_', '-', $key);
		$field = '<label for="'.$id.'">'.__('FROM email address for sites using this method', WYSIJA)."</label>";
		$html_content.=$field;
		$html_content.='
					</th>
					<td colspan="2">';
		$html_content.=$helper_forms->input(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']', 'size' => '40' ), $model_config->getValue($key));
		$html_content.='</td>
				</tr>';

		$html_content.='<tr class="ms-hidechoice ms-choice-sending-method-site">
					<th scope="row">';
		$field = __('Delivery method', WYSIJA);
		$field.='<p class="description">'.__('Send yourself some test emails to confirm which method works with your server.', WYSIJA).'</p>';
		$html_content.=$field;

		$html_content.='</th>
					<td colspan="2">';

		$key	   = $prefix.'sending_emails_site_method';
		$checked   = false;
		$realvalue = $model_config->getValue($key);
		$value	 = 'phpmail';
		if ($value == $realvalue)
			$checked   = true;

		$id	= str_replace('_', '-', $key).'-'.$value;
		$field = '<p class="title"><label for="'.$id.'">';
		$field.=$helper_forms->radio(array( "id"   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
		$field.='PHP Mail</label></p>';
		$field.='<p class="description">'.__('This email engine works on 95&#37; of servers', WYSIJA).'</p>';


		if ($model_config->getValue('allow_wpmail')) {
			$checked = false;
			$value   = 'wpmail';
			if ($value == $realvalue)
				$checked = true;

			$id = str_replace('_', '-', $key).'-'.$value;
			$field.='<p class="title"><label for="'.$id.'">';
			$field.=$helper_forms->radio(array( "id"   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
			$field.='WP Mail</label></p>';
			$field.='<p class="description">'.__('Use the same method as the one used for your WP site.', WYSIJA).'</p>';
		}


		$html_content.=$field;
		$html_content.='</td>
				</tr>';

		$html_content.='<tr class="ms-hidechoice ms-choice-sending-method-smtp">
					<th scope="row">';

		$key   = $prefix.'smtp_host';
		$id	= str_replace('_', '-', $key);
		$field = '<label for="'.$id.'">'.__('SMTP Hostname', WYSIJA)."</label>";
		$field.='<p class="description">'.__('e.g.:smtp.mydomain.com', WYSIJA).'</p>';
		$html_content.=$field;
		$html_content.='
					</th>
					<td colspan="2">';

		$value = $model_config->getValue($key);
		$field = $helper_forms->input(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']', 'size' => '40' ), $value, $checked);
		$html_content.=$field;
		$html_content.='</td>
				</tr>';

		$html_content.='<tr class="ms-hidechoice ms-choice-sending-method-smtp">
					<th scope="row">';

		$key   = $prefix.'smtp_login';
		$id	= str_replace('_', '-', $key);
		$field = '<label for="'.$id.'">'.__('Login', WYSIJA)."</label>";

		$html_content.=$field;

		$html_content.='</th>
					<td colspan="2">';

		$value = $model_config->getValue($key);
		$field = $helper_forms->input(array( "id"   => $id, 'name' => 'wysija[config]['.$key.']', 'size' => '40' ), $value, $checked);
		$html_content.=$field;
		$html_content.='</td>
				</tr>';

		$html_content.='<tr class="ms-hidechoice ms-choice-sending-method-smtp">
					<th scope="row">';

		$key   = $prefix.'smtp_password';
		$id	= str_replace('_', '-', $key);
		$field = '<label for="'.$id.'">'.__('Password', WYSIJA)."</label>";
		$html_content.=$field;

		$html_content.='</th>
					<td colspan="2">';

		$value = $model_config->getValue($key);
		$field = $helper_forms->input(array( "type" => "password", "id"   => $id, 'name' => 'wysija[config]['.$key.']', 'size' => '40' ), $value, $checked);
		$html_content.=$field;

		$html_content.='</td>
				</tr>';

		$html_content.='<tr id="ms-restapipossible" class="hidechoice">
					<th scope="row">';

		$key   = $prefix.'smtp_rest';
		$id	= str_replace('_', '-', $key);
		$field = '<label for="'.$id.'">web API</label>';
		$field.='<p class="description">'.__('Activate if your SMTP ports are blocked.', WYSIJA).'</p>';
		$html_content.=$field;

		$html_content.='</th>
					<td colspan="2">';

		$value   = $model_config->getValue($key);
		$checked = false;
		if ($value)
			$checked = true;
		$field   = $helper_forms->checkbox(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']', 'size' => '3' ), 1, $checked);

		$html_content.=$field;

		$html_content.='</td>
				</tr>';

		$html_content.='<tr class="ms-hidechoice ms-choice-sending-method-smtp ms-choice-no-restapi">
					<th scope="row">';

		$key   = $prefix.'smtp_port';
		$id	= str_replace('_', '-', $key);
		$field = '<label for="'.$id.'">'.__('SMTP port', WYSIJA)."</label>";

		$html_content.=$field;

		$html_content.='</th>
					<td colspan="2">';

		$value = $model_config->getValue($key);
		$field = $helper_forms->input(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']', 'size' => '3' ), $value, $checked);

		$html_content.=$field;
		$html_content.='</td>
				</tr>';

		$html_content.='<tr class="ms-hidechoice ms-choice-sending-method-smtp ms-choice-no-restapi">
					<th scope="row">';

		$key   = $prefix.'smtp_secure';
		$id	= str_replace('_', '-', $key);
		$field = '<label for="'.$id.'">'.__('Secure connection', WYSIJA)."</label>";
		$html_content.=$field;
		$html_content.='</th>
					<td colspan="2">';

		$value = $model_config->getValue($key);

		$field = $helper_forms->dropdown(array( 'name' => 'wysija[config]['.$key.']', "id"   => $id ), array( false => __("No"), "ssl" => "SSL", "tls" => "TLS" ), $value);
		$html_content.=$field;

		$html_content.='</td>
				</tr>';

		$html_content.='<tr class="ms-hidechoice ms-choice-sending-method-smtp ms-choice-no-restapi">
					<th scope="row">';

		$field = __('Authentication', WYSIJA);
		$html_content.=$field.'<p class="description">'.__("Leave this option to Yes. Only a tiny portion of SMTP services ask Authentication to be turned off.", WYSIJA).'</p>';
		$html_content.='</th>
					<td colspan="2">';

		$key	   = $prefix.'smtp_auth';
		$realvalue = $model_config->getValue($key);

		$value   = false;
		$checked = false;
		if ($value == $realvalue)
			$checked = true;
		$id	  = str_replace('_', '-', $key).'-'.$value;
		$field   = '<label for="'.$id.'">';
		$field.=$helper_forms->radio(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
		$field.=__('No', WYSIJA).'</label>';

		$value   = true;
		$checked = false;
		if ($value == $realvalue)
			$checked = true;
		$id	  = str_replace('_', '-', $key).'-'.$value;
		$field.='<label for="'.$id.'">';
		$field.=$helper_forms->radio(array( 'id'   => $id, 'name' => 'wysija[config]['.$key.']' ), $value, $checked);
		$field.=__('Yes', WYSIJA).'</label>';


		$html_content.=$field;
		$html_content.='</td>
				</tr>';

		$current_user = wp_get_current_user();
		$html_content .=
				'<tr class="ms-hidechoice ms-choice-sending-method-smtp ms-choice-sending-method-site">'.
				'<th scope="row">'.
				__('Test method').
				'</th>'.
				'<td colspan="'.( is_multisite() ? 3 : 2 ).'">'.
				'<input type="text" value="'.esc_attr($current_user->data->user_email).'" class="mailpoet-test-emails" name="wysija[config][test_mails]" />'.
				'<a class="button-secondary mailpoet-test-delivery" data-multisite="'.( is_multisite() ? 'true' : 'false' ).'">'.esc_attr__('Send a test mail', WYSIJA).'</a>'.
				'</td>'.
				'</tr>';


		$html_content.='<tr class="ms-hidechoice ms-choice-sending-method-smtp ms-choice-sending-method-site">
					<th scope="row">';

		$field = __('Send...', WYSIJA);

		$html_content.=$field.'<p class="description">'.str_replace(array( '[link]', '[/link]' ), array( '<a href="http://support.mailpoet.com/knowledgebase/wp-cron-batch-emails-sending-frequency/?utm_source=wpadmin&utm_campaign=choosing%20frequency" target="_blank">', '</a>' ), __('Your web host has limits. We suggest 70 emails per hour to be safe. [link]Find out more[/link].', WYSIJA)).'</p>';
		$html_content.='</th>
					<td colspan="2">';

		$name   = $prefix.'sending_emails_number';
		$id	 = str_replace('_', '-', $name);
		$value  = $model_config->getValue($name);
		$params = array( 'id'   => $id, 'name' => 'wysija[config]['.$name.']', 'size' => '6' );
		$field = $helper_forms->input($params, $value);
		$field.= '&nbsp;'.__('emails', WYSIJA).'&nbsp;';


		$name  = $prefix.'sending_emails_each';
		$id	= str_replace('_', '-', $name);
		$value = $model_config->getValue($name);
		$field.=$helper_forms->dropdown(array( 'name' => 'wysija[config]['.$name.']', 'id'   => $id ), $helper_forms->eachValues, $value);
		$field.='<span class="ms-choice-under15"><b>'.__('This is fast!', WYSIJA).'</b> '.str_replace(array( '[link]', '[/link]' ), array( '<a href="http://support.mailpoet.com/knowledgebase/wp-cron-batch-emails-sending-frequency/?utm_source=wpadmin&utm_campaign=cron" target="_blank">', '</a>' ), __('We suggest you setup a cron job. [link]Read more[/link] on support.mailpoet.com', WYSIJA)).'</span>';
		$html_content.=$field;


		$html_content.='</td>
				</tr>
			</tbody>
		</table>';
		return $html_content;
	}

	// WYSIJA Form Editor
	function form_list() {
		$model_forms = WYSIJA::get('forms', 'model');

		$forms = $model_forms->getRows();

		// get available lists which users can subscribe to
		$model_list = WYSIJA::get('list', 'model');

		// get lists users can subscribe to (aka "enabled list")
		$lists = $model_list->get(array( 'name', 'list_id', 'is_public' ), array( 'is_enabled' => 1 ));

		// generate table header/footer
		$table_headers = '
			<th class="manage-column" scope="col"><span>'.__('Name', WYSIJA).'</span></th>
			<th class="manage-column" scope="col"><span>'.__('Lists', WYSIJA).'</span></th>
		';

		$classes = function_exists('wp_star_rating') ? 'add-new-h2' : 'button-secondary2';
		?>

		<!-- Create a new form -->
		<p class="new_form">
			<a class="<?php echo $classes; ?>" href="admin.php?page=wysija_config&action=form_add&_wpnonce=<?php echo $this->secure(array( "action" => "form_add" ), true); ?>"><?php _e('Create a new form', WYSIJA); ?></a>
		</p>

		<?php
		if (count($forms) > 0) {
			?>
			<!-- List of forms -->
			<div class="list">
				<table cellspacing="0" class="widefat fixed">
					<thead>
						<tr>
							<?php echo $table_headers; ?>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<?php echo $table_headers; ?>
						</tr>
					</tfoot>

					<tbody>
						<?php
						for ($i	 = 0, $count = count($forms); $i < $count; $i++) {
							// set current row
							$row = $forms[$i];

							// get lists in settings and build list of lists (separated by "," for display only)
							$form_data = unserialize(base64_decode($row['data']));

							$form_lists = array( );
							if (isset($form_data['settings']['lists']) && !empty($form_data['settings']['lists'])) {
								for ($j		  = 0, $list_count = count($lists); $j < $list_count; $j++) {
									if (in_array($lists[$j]['list_id'], $form_data['settings']['lists'])) {
										$form_lists[] = $lists[$j]['name'];
									}
								}
							}

							// format list of lists depending on who's choosing the list to subscribe to (admin OR user)
							if (empty($form_lists)) {
								$form_lists_display = '<strong>'.__('No list specified', WYSIJA).'</strong>';
							}
							else {
								if ($form_data['settings']['lists_selected_by'] === 'user') {
									// user can select his own lists
									$form_lists_display = sprintf(__('User choice: %s', WYSIJA), join(', ', $form_lists));
								}
								else {
									// admin has selected which lists the user subscribes to
									$form_lists_display = join(', ', $form_lists);
								}
							}
							?>
							<tr class="<?php echo (($i % 2) ? 'alternate' : ''); ?>">
								<td>
									<?php echo $row['name']; ?>
									<div class="row-actions">
										<span class="edit">
											<a href="admin.php?page=wysija_config&action=form_edit&id=<?php echo $row['form_id'] ?>"><?php _e('Edit', WYSIJA); ?></a>
										</span> |
										<span class="duplicate">
											<a href="admin.php?page=wysija_config&action=form_duplicate&id=<?php echo $row['form_id'] ?>&_wpnonce=<?php echo $this->secure(array( 'action' => 'form_duplicate', 'id'	 => $row['form_id'] ), true); ?>"><?php _e('Duplicate', WYSIJA) ?></a>
										</span> |
										<span class="delete">
											<a href="admin.php?page=wysija_config&action=form_delete&id=<?php echo $row['form_id'] ?>&_wpnonce=<?php echo $this->secure(array( 'action' => 'form_delete', 'id'	 => $row['form_id'] ), true); ?>" class="submitdelete"><?php _e('Delete', WYSIJA) ?></a>
										</span>
									</div>
								</td>
								<td>
									<?php echo $form_lists_display ?>
								</td>
							</tr><?php
				}//endfor
								?>
					</tbody>
				</table>
			</div>
			<?php
		} //endif
	}

	function form_edit($data) {
		// get form editor rendering engine
		$helper_form_engine = WYSIJA::get('form_engine', 'helper');
		$helper_form_engine->set_lists($data['lists']);
		$helper_form_engine->set_data($data['form']['data'], true);
		$classes			= function_exists('wp_star_rating') ? 'add-new-h2' : 'button-secondary2';
		?>
		<div class="icon32"><img src="<?php echo WYSIJA_URL ?>img/form-icon.png" alt="" /></div>

		<?php
		if ($data['can_edit'] === false) {
			echo '<h2 class="title clearfix">';
			echo '<span>'.__('Edit form', WYSIJA).'</span>';
			echo '</h2>';
			echo __('Yikes! To edit a form, you need to have at least 1 list.', WYSIJA);
			echo '<a href="admin.php?page=wysija_subscribers&action=addlist" class="button-secondary2">'.__('Add List', WYSIJA).'</a>';
		}
		else {
			echo '<h2 class="title clearfix">';
			echo '<span>'.__('Edit', WYSIJA).'</span>';
			echo '<span id="form-name">'.$data['form']['name'].'</span>';
			echo '<span id="form-name-default">'.$data['form']['name'].'</span>';
			?>
			<span>
				<a id="edit-form-name" class="button" href="javascript:;"><?php echo __('Edit name', WYSIJA); ?></a>
				<a class="<?php echo $classes; ?>" href="admin.php?page=wysija_config#tab-forms"><?php echo __('List of forms', WYSIJA); ?></a>
			</span>
			</h2>

			<div class="clearfix">
				<!-- Form Editor Container -->
				<div id="wysija_form_container">
					<!-- Form Editor -->
					<div id="wysija_form_editor">
						<?php
						// render form editor
						echo $helper_form_engine->render_editor();
						?>
					</div>

					<!-- Form settings -->
					<form id="wysija-form-settings" action="" method="POST">
						<input type="hidden" name="form_id" value="<?php echo (int)$data['form_id']; ?>" />

						<!-- Form settings: list selection -->
						<div id="list-selection" class="clearfix">
							<p>
								<strong><?php _e('This form adds subscribers to these lists:', WYSIJA) ?></strong>
							</p>
							<?php
							if (!empty($data['lists'])) {
								$form_lists = $helper_form_engine->get_setting('lists');
								// make sure that form_lists is an array
								if ($form_lists === null or is_array($form_lists) === false)
									$form_lists = array( );

								usort($data['lists'], array( $this, 'sort_by_name' ));

								print '<select id="lists-selection" name="lists" data-placeholder="'.__('Choose a list', WYSIJA).'" multiple class="chosen_select">';

								for ($i	 = 0, $count = count($data['lists']); $i < $count; $i++) {
									$list	   = $data['lists'][$i];
									$is_checked = (in_array($list['list_id'], $form_lists)) ? 'selected="selected"' : '';
									//print '<label><input type="checkbox" class="checkbox" name="lists" value="' . $list['list_id'] . '" ' . $is_checked . ' />' . $list['name'] . '</label>';
									print '<option value="'.$list['list_id'].'" '.$is_checked.' />'.$list['name'].'</option>';
								}

								print '</select>';
							}
							?>
						</div>

						<!-- Form settings: after submit -->
						<div id="after-submit">
							<p>
								<strong><?php _e('After submit...', WYSIJA) ?></strong>
								<input type="hidden" name="on_success" value="message" />
								<!--<label><input type="radio" name="on_success" value="message" checked="checked"  /><?php _e('show message', WYSIJA); ?></label>
								<label><input type="radio" name="on_success" value="page" /><?php _e('go to page', WYSIJA); ?></label>-->
							</p>
							<textarea name="success_message"><?php echo $helper_form_engine->get_setting('success_message'); ?></textarea>
						</div>

						<p id="form-error" style="display:none;"></p>

						<p class="submit">
							<a href="javascript:;" id="form-save" class="button-primary wysija" ><?php echo esc_attr(__('Save', WYSIJA)); ?></a>
						</p>

						<p id="form-notice" style="display:none;"></p>
					</form>

					<!-- Form export links -->
					<div id="form-export-links"><p><?php echo str_replace(array( '[link]', '[/link]' ), array( '<a href="widgets.php" target="_blank">', '</a>' ), __('You can easily add this form to your theme\'s [link]Widgets areas[/link].', WYSIJA)); ?></p>
						<p><?php
				$text = __('[link_html]HTML[/link_html], [link_php]PHP[/link_php], [link_iframe]iframe[/link_iframe] and [link_shortcode]shortcode[/link_shortcode] versions are also available.', WYSIJA);
				echo str_replace(
						array( '[link_html]', '[link_php]', '[link_iframe]', '[link_shortcode]', '[/link_html]', '[/link_php]', '[/link_iframe]', '[/link_shortcode]' ), array( '<a target="javascript:;" class="expand-code" title="html">', '<a target="javascript:;" class="expand-code" title="php">', '<a target="javascript:;" class="expand-code" title="iframe">', '<a target="javascript:;" class="expand-code" title="shortcode">', '</a>', '</a>', '</a>', '</a>' ), $text);
							?></p></div>

					<!-- Form export -->
					<div id="form-export">
						<?php echo $helper_form_engine->render_editor_export($data['form_id']); ?>
					</div>
				</div>
				<!-- Form Editor: Toolbar -->
				<div id="wysija_form_toolbar">
					<!-- <ul class="wysija_form_toolbar_tabs">
						<li class="wjt-content">
							<a class="selected" href="javascript:;" rel="wj_content"><?php _e('Content', WYSIJA) ?></a>
						</li>
					</ul> -->

					<!-- CONTENT BAR -->
					<ul class="wj_content">
						<?php
						// render editor toolbar
						echo $helper_form_engine->render_editor_toolbar();
						?>
					</ul>

					<!-- WIDGET TEMPLATES -->
					<div id="wysija_widget_templates">
						<?php
						// render editor widget templates
						echo $helper_form_engine->render_editor_templates();
						?>
					</div>

					<div id="wysija_notices" style="display:none;"><span id="wysija_notice_msg"></span><img alt="loader" style="display:none;" id="ajax-loading" src="<?php echo WYSIJA_URL ?>img/wpspin_light.gif" /></div>
				</div>
			</div>
			<script type="text/javascript" charset="utf-8">
				wysijaAJAX.form_id = <?php echo (int)$_REQUEST['id'] ?>;

				function formEditorSave(callback) {
					wysijaAJAX.task = 'form_save';
					wysijaAJAX._wpnonce = wysijanonces.config.form_save;
					wysijaAJAX.wysijaData = WysijaForm.save();
					WYSIJA_SYNC_AJAX({
						success: callback
					});
				}

				function refreshToolbar(data) {
					// update toolbar
					$('wysija_form_toolbar').down('.wj_content').innerHTML = Base64.decode(data.toolbar);

					// update templates
					$('wysija_widget_templates').innerHTML = Base64.decode(data.templates);

					// setup events on widgets
					setupToolbar();
				}

				function refreshBlocks(field_name, block) {
					var elements = $$('#wysija_form_editor .wysija_form_block[wysija_field="'+ field_name +'"]');

					if(elements.length > 0) {
						$(elements).each(function(element) {
							// replace each block by new block
							refreshBlock(element, block);
						});

						// init wysija form
						setTimeout(function() {
							formEditorSave(function() { WysijaForm.init() });
						}, 1);
					}
				}

				function refreshBlock(element, block) {
					element.replace(Base64.decode(block));
				}

				function setupToolbar() {
					// add custom field button
					$('wysija-add-field').observe('click', function() {
						WysijaPopup.open(wysijatrans.add_field, $(this).readAttribute('href2'), function(data) {
							refreshToolbar(data);

							// make sure disabled widgets stay disabled after editing
							WysijaForm.toggleWidgets();
						});
						return false;
					});

					// edit custom field settings
					$$('.wysija_form_item_settings').invoke('stopObserving', 'click');
					$$('.wysija_form_item_settings').invoke('observe', 'click', function() {
						var self = this;
						// get field type
						var field_type = $(this).previous('.wysija_form_item').readAttribute('wysija_field');
						toggleBlocksHighlight('on', field_type);
						WysijaPopup.open(wysijatrans.edit_field, $(this).readAttribute('href2'), function(data) {
							refreshToolbar(data);

							// get field type
							var field_name = $(self).previous('.wysija_form_item').readAttribute('wysija_field');
							refreshBlocks(field_name, data.block);

							// make sure disabled widgets stay disabled after editing
							WysijaForm.toggleWidgets();

							toggleBlocksHighlight('off', field_type);
						}, function() {
							toggleBlocksHighlight('off', field_type);
						});
						return false;
					});

					// remove custom field
					$$('.wysija_form_item_delete').invoke('stopObserving', 'click');
					$$('.wysija_form_item_delete').invoke('observe', 'click', function(e) {
						// get field type
						var field_type = $(this).previous('.wysija_form_item').readAttribute('wysija_field');

						// highlight blocks using this field type
						toggleBlocksHighlight('on', field_type);

						if(window.confirm(wysijatrans.delete_field_confirmation)) {
							// make ajax request
							wysijaAJAX.task = 'form_field_delete';
							wysijaAJAX._wpnonce = wysijanonces.config.form_field_delete;
							// build data with field id
							var data = { field_id: parseInt($(this).readAttribute('data-field-id'), 10) },
							self = this;
							wysijaAJAX.wysijaData = Base64.encode(Object.toJSON(data).gsub('\\"', '"').gsub('"[{', '[{').gsub('}]"', '}]'));

							new Ajax.Request(wysijaAJAX.ajaxurl, {
								method: 'post',
								parameters: wysijaAJAX,
								onSuccess:function(response) {
									// remove widget from list
									$(self).up('li').remove();

									// remove all blocks from the same type
									$$('.wysija_form_block[wysija_field="'+ field_type +'"]').each(function(element) {
										// get block id
										var block = WysijaForm.get($(element));
										block.removeBlock();
									});

									// save form
									formEditorSave();
								},
								onFailure:function(response) {
									// nothing we can do about it.
								}
							});
						}

						// highlight blocks using this field type
						toggleBlocksHighlight('off', field_type);
						return false;
					});
				}

				function toggleBlocksHighlight(toggle, field_type) {
					if(field_type === undefined) return false;

					if(toggle === 'on') {
						// turn highlight on
						$$('.wysija_form_block[wysija_field="'+ field_type +'"]').invoke('addClassName', 'highlighted');
					} else {
						// turn highlight off
						$$('.wysija_form_block[wysija_field="'+ field_type +'"]').invoke('removeClassName', 'highlighted');
					}

				}

				$('form-save').observe('click', function() {
					// hide any previous error
					$('form-error').hide();
					// hide any previous notice
					$('form-notice').hide();
					$('form-export-links').hide();

					// make sure a list has been selected or that we have a list selection widget inserted
					if ($F('lists-selection').length === 0 && $$('#wysija_form_body div[wysija_field="list"]').length === 0) {
						$('form-error').update(wysijatrans.list_cannot_be_empty).show();
					} else {
						// hide export options while saving
						WysijaForm.hideExport();

						formEditorSave(function(response) {
							// regenerate export methods and show
							$('form-export').innerHTML = Base64.decode(response.responseJSON.result.exports);

							// display save message
							$('form-notice').update(Base64.decode(response.responseJSON.result.save_message));
							$('form-export-links').show();
							$('form-notice').show();

							WysijaForm.showExport();
						});
					}
					return false;
				});

				$(document).observe('dom:loaded', function() {
					// setup widget events
					setupToolbar();

					// Setups the jQuery Select2
					jQuery('#lists-selection').select2({
						'width': 640
					});

					// in place editor for form name
					new Ajax.InPlaceEditor('form-name', wysijaAJAX.ajaxurl, {
						okControl: false,
						cancelControl: false,
						clickToEditText: '',
						submitOnBlur: true,
						highlightColor: '#f9f9f9',
						externalControl: 'edit-form-name',
						callback: function(form, value) {
							wysijaAJAX.task = 'form_name_save';
							wysijaAJAX._wpnonce = wysijanonces.config.form_name_save;

							return Object.toQueryString(wysijaAJAX) + '&id=' + wysijaAJAX.form_id + '&name=' + encodeURIComponent(WysijaForm.stripHtmlValue(value));
						},
						onComplete: function(response, element) {
							if(response.responseJSON.result.name.length === 0) {
								$(element).innerHTML = $('form-name-default').innerHTML;
							} else {
								var sanitized_name = WysijaForm.stripHtmlValue(response.responseJSON.result.name);
								$(element).innerHTML = sanitized_name;
								$('form-name-default').innerHTML = sanitized_name;
							}

							// remove observe on input field
							$('form-name-inplaceeditor').down('.editor_field').stopObserving('keypress');
						},
						onEnterEditMode: function(form, value) {
							setTimeout(function() {
								$('form-name-inplaceeditor').down('.editor_field').observe('keypress', function(e) {
									// prevent form from submitting on enter key press
									if (e.keyCode == 13) {
										e.preventDefault();
									}
								});
							}, 1);
						}
					});
				});
			</script>
			<?php
		}
	}

	// Wysija Form Editor: Widget Settings
	function popup_form_widget_settings($data = array( )) {
		$type			 = (isset($data['type']) && strlen(trim($data['type'])) > 0) ? $data['type'] : 'new';
		?>
		<div class="popup_content inline_form form_widget_settings widget_<?php echo $type; ?>">
			<?php
			// it means it's a new field if we have a field_id parameter with a 0 value
			$is_editing_field = (bool)(isset($data['field_id']));

			if (isset($data['field_id'])) {
				$valid_types = array(
					'input'	=> __('Text Input', WYSIJA),
					'textarea' => __('Text Area', WYSIJA),
					'radio'	=> __('Radio buttons', WYSIJA),
					'checkbox' => __('Checkbox', WYSIJA),
					'select'   => __('Select', WYSIJA),
					'date'	 => __('Date', WYSIJA)
						//'country' => __('Country', WYSIJA)
				);
				?>
				<form id="field-settings-form" method="get" action="">
					<input type="hidden" name="action" value="<?php echo esc_attr($_REQUEST['action']); ?>" />
					<input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
					<input type="hidden" name="field_id" value="<?php echo (int)$data['field_id']; ?>" />

					<p class="clearfix">
						<label for="field-type-select"><?php _e('Select a field type:', WYSIJA) ?></label>
						<select id="field-type-select" name="type">
							<option value=""></option>
							<?php
							foreach ($valid_types as $type => $label) {
								$is_selected = ($data['type'] === $type) ? ' selected="selected"' : '';

								echo '<option value="'.$type.'"'.$is_selected.'>'.$label.'</option>';
							}
							?>
						</select>
					</p>
					<hr />
				</form>

				<?php
			}

			if (method_exists($this, 'form_widget_'.$data['type']) === false) {
				// display warning because the widget has no form built in
				// print 'missing method "' . 'form_widget_' . $data['type'] . '"';
			}
			else {
				// get widget form
				$data['is_editing_field'] = $is_editing_field;
				$widget_form			  = $this->{'form_widget_'.$data['type']}($data);
				// check whether the field is a custom field
				$form_engine			  = WYSIJA::get('form_engine', 'helper');
				$is_custom_field		  = $form_engine->is_custom_field($data);
				?>

				<!-- this is to display error messages -->
				<p id="widget-settings-error" style="display:none;"></p>

				<!-- common form for every wysija form widget -->
				<form enctype="multipart/form-data" method="post" action="" class="" id="widget-settings-form">
					<input type="hidden" name="type" value="<?php echo $data['type']; ?>" />
					<input type="hidden" name="field" value="<?php echo $data['field']; ?>" />
					<?php
					if ($is_editing_field === true) {
						?>
						<p class="clearfix">
							<label for="name"><?php _e("Field's name:", WYSIJA); ?></label>
							<input type="text" id="name" name="name" value="<?php echo stripslashes(esc_attr($data['name'])); ?>" />
						</p>
						<?php
					}
					else {
						?>
						<input type="hidden" name="name" value="<?php echo esc_attr($data['name']); ?>" />
						<?php
					}
					?>
					<?php echo $widget_form; ?>

					<p class="submit_button">
						<input id="widget-settings-submit" type="submit" name="submit" value="<?php echo esc_attr(__('Done', WYSIJA)) ?>" class="button-primary" />
						<?php
						if ($is_editing_field === false && $is_custom_field === true) {
							$field_id = (int)substr($data['field'], 3);
							?>
							<a id="widget-field-settings" wysija_field="<?php echo $field_id; ?>" href="javascript:;"><?php echo __('Edit field', WYSIJA); ?></a>
							<?php
						}
						?>
					</p>
				</form>
			</div>
			<?php
		}
	}

	function form_widget_html($data = array( )) {
		$output = '';

		// get widget params from data, this will contain all user editable parameters
		$params = $data['params'];

		// text
		$text = isset($params['text']) ? $params['text'] : '';
		$output .= '<textarea name="text">'.$text.'</textarea>';

		// nl2br?
		$is_checked = (isset($params['nl2br']) && (bool)$params['nl2br'] === true) ? 'checked="checked"' : '';
		$output .= '<label for="field-nl2br"><input id="field-nl2br" type="checkbox" class="checkbox" name="nl2br" '.$is_checked.' value="1" />'.__('Automatically add paragraphs', WYSIJA).'</label>';

		return $output;
	}

	function form_widget_submit($data = array( )) {
		$output = '';

		// label
		$output .= $this->_widget_label($data);

		return $output;
	}

	function form_widget_input($data = array( )) {
		$output = '';

		// label
		$output .= $this->_widget_label($data);

		// inline label
		$output .= $this->_widget_label_inline($data);

		// extra validation
		$output .= $this->_widget_validation($data);

		return $output;
	}

	function form_widget_textarea($data = array( )) {
		$output = '';

		// label
		$output .= $this->_widget_label($data);

		// inline label
		$output .= $this->_widget_label_inline($data);

		// extra validation
		$output .= $this->_widget_validation($data);

		if ($data['is_editing_field'] === false) {
			// number of lines (defaults to 1)
			$number_of_lines = (isset($data['params']['lines']) && (int)$data['params']['lines'] > 0) ? (int)$data['params']['lines'] : 1;

			$output .= '<p class="clearfix"><label for="number-lines-select">'.__('Number of lines:', WYSIJA).'</label>';
			$output .= '    <select id="number-lines-select" name="lines">';
			for ($i = 1; $i < 6; $i++) {
				$is_selected = ($number_of_lines === $i) ? ' selected="selected"' : '';
				$output .= '    <option value="'.$i.'" '.$is_selected.'>'.sprintf(_n('1 line', '%d lines', $i, WYSIJA), $i).'</option>';
			}
			$output .= '    </select>';
			$output .= '</p>';
		}

		return $output;
	}

	function form_widget_list($data = array( )) {
		$output = '';

		// get widget params from data, this will contain all user editable parameters
		$params = $data['params'];

		// get widget extra data, this will contain all the available lists
		$extra = $data['extra'];

		// label
		$output .= $this->_widget_label($data);

		$helper_form_engine = WYSIJA::get('form_engine', 'helper');
		// get list names
		$list_names		 = $helper_form_engine->get_formatted_lists();

		// display select lists
		$output .= '<ul id="lists-selection" class="selection sortable">';

		if (!empty($params['values'])) {
			for ($i	 = 0, $count = count($params['values']); $i < $count; $i++) {
				$list	   = $params['values'][$i];
				$is_checked = ((int)$list['is_checked'] > 0) ? 'checked="checked"' : '';
				if (isset($list_names[$list['list_id']])) {
					$output .= '<li class="clearfix">';
					$output .= '    <input class="is_selected" id="list-'.$list['list_id'].'" type="checkbox" data-list="'.$list['list_id'].'" value="1" '.$is_checked.' />';
					$output .= '    <label for="list-'.$list['list_id'].'">'.$list_names[$list['list_id']].'</label>';
					$output .= '    <a class="icon remove" href="javascript:;"><span></span></a>';
					$output .= '    <a class="icon handle" href="javascript:;"><span></span></a>';
					$output .= '</li>';
				}
			}
		}

		$output .= '</ul>';

		// available lists container
		$output .= '<div id="lists-add-container" class="clearfix">';
		$output .= '<h3>'.__('Select the list you want to add:', WYSIJA).'</h3>';

		// available lists select
		$output .= '<select id="lists-available" class="mp-select-sort">';
		for ($j	 = 0, $count = count($extra['lists']); $j < $count; $j++) {
			// set current list
			$list = $extra['lists'][$j];
			// generate select option
			$output .= '<option value="'.$list['list_id'].'">'.$list['name'].'</option>';
		}
		$output .= '</select>';

		// add button to add currently selected list to selected lists
		$output .= '<a href="javascript:;" class="icon add"><span></span></a>';
		$output .= '</div>';

		// generate prototypeJS template for list selection so we can dynamically add/remove elements
		$output .= '<div id="selection-template">';
		$output .= '<li class="clearfix">';
		$output .= '<input class="is_selected" id="list-#{list_id}" type="checkbox" data-list="#{list_id}" value="1" /><label for="list-#{list_id}">#{name}</label>';
		$output .= '<a class="icon remove" href="javascript:;"><span></span></a>';
		$output .= '<a class="icon handle" href="javascript:;"><span></span></a>';
		$output .= '</li>';
		$output .= '</div>';

		return $output;
	}

	function form_widget_radio($data = array( )) {
		return $this->form_widget_multiple($data, 'radio');
	}

	function form_widget_checkbox($data = array( )) {
		return $this->form_widget_multiple($data, 'checkbox');
	}

	function form_widget_select($data = array( )) {
		return $this->form_widget_multiple($data, 'select');
	}

	// Country selection widget
	function form_widget_country($data = array( )) {
		$output = '';

		// TODO: JSON data coming from API
		//$country_data = json_decode(file_get_contents('http://localhost/countries.json'), true);
		$country_data = array( );

		// format countries for select widget
		$countries = array( );
		foreach ($country_data as $country) {
			$countries[] = array(
				'value'				   => $country['name'],
				'is_checked'			  => false
			);
		}
		$data['params']['values'] = $countries;

		// render select widget form
		$output .= $this->form_widget_multiple($data, 'select');

		return $output;
	}

	public function form_widget_date($data = array( )) {
		$output = '';

		// label
		$output .= $this->_widget_label($data);

		// date types
		$date_types = array(
			'year_month_day' => __('Year, month, day', WYSIJA),
			'year_month'	 => __('Year, month', WYSIJA),
			'month'		  => __('Month (January, February,...)', WYSIJA),
			'year'		   => __('Year', WYSIJA)
		);

		if ($data['is_editing_field'] === false) {
			$date_type_value = (isset($data['params']['date_type']) && strlen(trim($data['params']['date_type'])) > 0) ? trim($data['params']['date_type']) : $date_types[0];
			$output .= '<input type="hidden" name="date_type" value="'.$date_type_value.'">';
		}
		else {

			// date type selection
			$output .= '<p class="clearfix"><label for="date_type">'.__('Type of date:', WYSIJA).'</label>';
			$output .= '    <select id="date_type" name="date_type">';
			foreach ($date_types as $key => $label) {
				$is_selected = (isset($data['params']['date_type']) && $data['params']['date_type'] === $key) ? ' selected="selected"' : '';
				$output .= '    <option value="'.$key.'" '.$is_selected.'>'.$label.'</option>';
			}
			$output .= '    </select>';
			$output .= '</p>';
		}

		// date set default as today
		$output .= $this->_widget_date_default_today($data);

		// date display order
		$output .= $this->_widget_date_order($data);

		return $output;
	}

	// generic widget for handling multiple choices (radio, checkbox, select)
	private function form_widget_multiple($data = array( ), $type = false) {
		$output = '';

		if ($type === false)
			return $output;

		// get widget params from data, this will contain all user editable parameters
		$params = $data['params'];

		// label
		$output .= $this->_widget_label($data);

		// label inline
		$output .= $this->_widget_label_inline($data);

		// flag to know whether to allow multiple values or not
		$is_multiple = true;

		// checkbox only allow one value (for now...)
		if ($type === 'checkbox') {
			$is_multiple = false;

			// make sure we have one value
			if (empty($params['values'])) {
				$params['values'][] = array( 'value'	  => '', 'is_checked' => false );
			}
			else {
				// restrict to 1 value
				if (count($params['values']) > 1) {
					$value			= $params['values'][0];
					$params['values'] = array( $value );
				}
			}
		}

		// set CSS classes for list
		$list_classes = array( 'selection' );
		if ($is_multiple === true) {
			$list_classes[] = 'sortable';
		}

		// hide values when no editing CF global settings
		if ($data['is_editing_field'] === false) {
			$output .= '<div class="hidden">';
		}

		// display values
		$output .= '<ul id="items-selection" class="'.join(' ', $list_classes).'">';

		if (!empty($params['values'])) {
			for ($i	 = 0, $count = count($params['values']); $i < $count; $i++) {
				$item	   = $params['values'][$i];
				$is_checked = ((int)$item['is_checked'] > 0) ? 'checked="checked"' : '';

				$output .= '<li class="clearfix">';
				if ($type === 'checkbox') {
					$output .= '<input type="checkbox" class="is_checked" value="1" '.$is_checked.' />';
				}
				else {
					$output .= '<input type="radio" name="is_checked" class="is_checked" value="1" '.$is_checked.' />';
				}

				$output .= '    <input type="text" class="value" value="'.$item['value'].'" tabindex="'.($i + 1).'" />';

				if ($is_multiple === true) {
					$output .= '    <a class="icon remove" href="javascript:;"><span></span></a>';
					$output .= '    <a class="icon handle" href="javascript:;"><span></span></a>';
				}
				$output .= '</li>';
			}
		}

		$output .= '</ul>';

		if ($is_multiple === true) {
			// add a new option
			$output .= '<a id="items-selection-add" href="javascript:;" class="icon add"><span></span>'.__('Add item', WYSIJA).'</a>';

			// generate prototypeJS template for list selection so we can dynamically add/remove elements
			$output .= '<div id="selection-template">';
			$output .= '<li class="clearfix">';
			if ($type === 'checkbox') {
				$output .= '<input type="checkbox" class="is_checked" value="1" />';
			}
			else {
				$output .= '<input type="radio" name="is_checked" class="is_checked" value="1" />';
			}
			$output .= '    <input type="text" class="value" value="" tabindex="#{item_index}" />';
			$output .= '    <a class="icon remove" href="javascript:;"><span></span></a>';
			$output .= '    <a class="icon handle" href="javascript:;"><span></span></a>';
			$output .= '</li>';
			$output .= '</div>';
		}

		// hide values when no editing CF global settings
		if ($data['is_editing_field'] === false) {
			$output .= '</div>';
		}

		$output .= $this->_widget_validation($data);

		return $output;
	}

	// widget feature: ability to specify a label
	private function _widget_label($data = array( )) {
		$output = '';

		if ($data['is_editing_field'] === false) {
			// label
			$label = (isset($data['params']['label'])) ? $data['params']['label'] : '';
			$output .= '<p class="clearfix"><label for="label">'.__('Label:', WYSIJA).'</label>';
			$output .= '<input id="label" type="text" name="label" value="'.$label.'" /></p>';
		}

		return $output;
	}

	// widget feature: ability to choose whether the label should be inline (a placeholder)
	private function _widget_label_inline($data = array( )) {
		$output = '';

		// only show extra validation for these types
		if (in_array($data['type'], array( 'input', 'textarea', 'select' )) && $data['is_editing_field'] === false) {
			// inline
			$is_label_within = (bool)(isset($data['params']['label_within']) && (int)$data['params']['label_within'] > 0);
			$output .= '<p class="clearfix">';
			$output .= '<label>'.__('Display label within input:', WYSIJA).'</label>';
			$output .= '	<span class="group">';
			$output .= '		<label class="radio"><input type="radio" name="label_within" value="1" '.(($is_label_within === true) ? 'checked="checked"' : '').' />'.__('Yes', WYSIJA).'</label>';
			$output .= '		<label class="radio"><input type="radio" name="label_within" value="0" '.(($is_label_within === false) ? 'checked="checked"' : '').' />'.__('No', WYSIJA).'</label>';
			$output .= '	</span>';
			$output .= '</p>';
		}

		return $output;
	}

	private function _widget_date_order($data = array( )) {
		// date order
		$date_orders = array(
			'year_month_day' => array( 'mm/dd/yyyy', 'dd/mm/yyyy', 'yyyy/mm/dd' ),
			'year_month' => array( 'mm/yyyy', 'yyyy/mm' ),
			'year' => array( 'yyyy' ),
			'month' => array( 'mm' )
		);

		$output = '';

		// get date orders from date type
		$orders = array( );
		if (isset($date_orders[$data['params']['date_type']])) {
			$orders = $date_orders[$data['params']['date_type']];
		}
		else {
			$orders = $date_orders['year_month_day'];
		}

		if ($data['is_editing_field'] === false) {
			if (count($orders) === 1) {
				// show as hidden input
				$output .= '<input type="hidden" name="date_order" value="'.$orders[0].'" />';
			}
			else {
				$output .= '<p class="clearfix">';
				$output .= '<label for="date_order">'.__('Order:', WYSIJA).'</label>';
				$output .= '<select id="date_order" class="date_order" name="date_order">';

				foreach ($orders as $date_order) {
					$is_selected = (isset($data['params']['date_order']) && $data['params']['date_order'] === $date_order) ? ' selected="selected"' : '';
					$output .= '<option value="'.$date_order.'" '.$is_selected.'>'.$date_order.'</option>';
				}
				$output .= '</select>';
				$output .= '</p>';
			}
		}
		else {
			if (isset($data['params']['date_order'])) {
				$output .= '<input type="hidden" name="date_order" value="'.$data['params']['date_order'].'" />';
			}
			else {
				$output .= '<input type="hidden" name="date_order" value="'.$orders[0].'" />';
			}
		}

		return $output;
	}

	private function _widget_date_default_today($data = array( )) {
		$output = '';

		$display_default_today = true;
		$is_default_today	  = (bool)(isset($data['params']['is_default_today']) && (int)$data['params']['is_default_today'] > 0);


		// in the case of a "year", we don't offer the possibility to select today's date as the default
		if ($data['params']['date_type'] === 'year') {
			$display_default_today = false;
			$is_default_today	  = false;
		}

		if ($data['is_editing_field'] === false && $display_default_today === true) {
			$output .= '<p class="clearfix">';
			$output .= '<label>'.__("Preselect today's date:", WYSIJA).'</label>';
			$output .= '    <span class="group">';
			$output .= '        <label class="radio"><input type="radio" name="is_default_today" value="1" '.(($is_default_today === true) ? 'checked="checked"' : '').' />'.__('Yes', WYSIJA).'</label>';
			$output .= '        <label class="radio"><input type="radio" name="is_default_today" value="0" '.(($is_default_today === false) ? 'checked="checked"' : '').' />'.__('No', WYSIJA).'</label>';
			$output .= '    </span>';
			$output .= '</p>';
		}
		else {
			$output .= '<input type="hidden" name="is_default_today" value="'.(int)$is_default_today.'" />';
		}

		return $output;
	}

	// widget feature: data validation - is_required checkbox
	private function _widget_validation_is_required($data = array( )) {
		// is the field mandatory?
		$is_required = (bool)(isset($data['params']['required']) && (int)$data['params']['required'] > 0);
		$output	  = '<p class="clearfix">';
		$output .= '    <label>'.__('Is this field mandatory?', WYSIJA).'</label>';
		$output .= '    <span class="group">';
		$output .= '        <label class="radio"><input type="radio" name="required" value="1" '.(($is_required === true) ? 'checked="checked"' : '').' />'.__('Yes', WYSIJA).'</label>';
		$output .= '        <label class="radio"><input type="radio" name="required" value="0" '.(($is_required === false) ? 'checked="checked"' : '').' />'.__('No', WYSIJA).'</label>';
		$output .= '    </span>';
		$output .= '</p>';
		return $output;
	}

	// widget feature: data validation
	private function _widget_validation($data = array( )) {

		$output = '';

		// display hidden parameters in case we're editing display settings
		if ($data['is_editing_field'] === false) {

			// special case for firstname & lastname
			// we need to display the "is_required" in the form since they're not actual custom fields, they don't have "field settings"
			if (in_array($data['field'], array( 'firstname', 'lastname' ))) {
				$output .= $this->_widget_validation_is_required($data);
			}
			else {
				// display hidden input since these settings are set on the custom field itself
				if (isset($data['params']['required'])) {
					$required_value = (isset($data['params']['required']) && (int)$data['params']['required'] > 0) ? 1 : 0;
					$output .= '<input type="hidden" name="required" value="'.$required_value.'" />';
				}

				if (isset($data['params']['validate'])) {
					$validate_value = (isset($data['params']['validate']) && strlen(trim($data['params']['validate'])) > 0) ? trim($data['params']['validate']) : '';
					$output .= '<input type="hidden" name="validate" value="'.$validate_value.'" />';
				}
			}
		}
		else {
			// validation
			if ($data['field'] === 'email') {
				// the email field is always mandatory, no questions asked
				$output .= '<input type="hidden" name="required" value="1" />';
			}
			else {
				$output .= $this->_widget_validation_is_required($data);
			}

			// extra validation rules
			$rules = array(
				'onlyNumberSp'	 => __('Numbers only', WYSIJA),
				'onlyLetterSp'	 => __('Letters only', WYSIJA),
				'onlyLetterNumber' => __('Alphanumerical ', WYSIJA),
				'phone'			=> __('Phone number, (+,-,#,(,) and spaces allowed)', WYSIJA)
			);

			// only show extra validation for these types
			if (in_array($data['type'], array( 'input', 'textarea' ))) {
				$output .= '<p class="clearfix">';
				$output .= '    <label>'.__('Validate for:', WYSIJA).'</label>';
				$output .= '    <select name="validate">';
				$output .= '        <option value="">'.__('Nothing', WYSIJA).'</option>';
				foreach ($rules as $rule => $label) {
					$is_selected = (isset($data['params']['validate']) && ($rule === $data['params']['validate'])) ? ' selected="selected"' : '';
					$output .= '    <option value="'.$rule.'"'.$is_selected.'>'.$label.'</option>';
				}
				$output .= '    </select>';
				$output .= '</p>';
			}
		}

		return $output;
	}

}
