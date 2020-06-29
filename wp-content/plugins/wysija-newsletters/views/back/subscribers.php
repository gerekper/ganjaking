<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_view_back_subscribers extends WYSIJA_view_back
{

    var $icon = "icon-users";
    var $column_action_list = "email";

    function __construct()
    {
	$this->title = __("Lists and Subscribers", WYSIJA);
	parent::__construct();

	$this->search = array("title" => __("Search subscribers", WYSIJA));
	$this->column_actions = array('editlist' => __('Edit', WYSIJA), 'duplicatelist' => __('Duplicate', WYSIJA), 'deletelist' => __('Delete', WYSIJA));
    }


    function main($data)
    {
        echo '<form method="post" action="#currentform" id="posts-filter">';
	$this->filtersLink($data);
	$this->filterDDP($data);
	$this->listing($data);
	$this->limitPerPage();
	echo '</form>';
    }

    function menuTop($case = false) {
		if (!$case)
		    $case = $this->menuTop;

		$arrayTrans = array("backtolist" => __("Back to lists", WYSIJA), "back" => __("Back", WYSIJA), "add" => __('Add Subscriber', WYSIJA), "addlist" => __('Add List', WYSIJA), "lists" => __('Edit Lists', WYSIJA), "import" => __('Import', WYSIJA), "export" => __('Export', WYSIJA));

		switch ($case) {
		    case "add":
		    case "edit":
		    case "export":
		    case "import":
		    case "addlist":
		    case "editlist":
		    default:
			$arrayMenus = array("add", "addlist", "lists", "import", "export");
		}

		$html = "";
		$classes = function_exists('wp_star_rating') ? 'add-new-h2' : 'button-secondary2';
		foreach ($arrayMenus as $action) {
		    if (isset($_GET['action']) && $_GET['action'] == $action)
			continue; // Skip printing the list if we are in this action already

		    $html.= '<a href="admin.php?page=wysija_subscribers&action='.$action.'" class="'.$classes.'">'.$arrayTrans[$action].'</a>';
		}

		return $html;
    }

    function filterDDP($data)
    {
	?>
	<ul class="subsubsub">
	    <?php
	    $total = count($data['counts']);
	    $i = 1;
	    foreach ($data['counts'] as $countType => $count)
	    {
		if (!$count)
		{
		    continue;
		}
		switch ($countType)
		{
		    case 'all':
			$tradText = __('All', WYSIJA);
			break;
		    case 'unconfirmed':
			$tradText = __('Unconfirmed', WYSIJA);
			break;
		    case 'unsubscribed':
			$tradText = __('Unsubscribed', WYSIJA);
			break;
		    case 'subscribed':
			$tradText = __('Subscribed', WYSIJA);
			break;
		    case 'unlisted':
			$tradText = __('Unlisted', WYSIJA);
			break;
		    case 'inactive':
			$tradText = __('Never opened or clicked', WYSIJA);
			break;
		}
		$classcurrent = '';
		$radio_checked = '';
		if ((isset($_REQUEST['link_filter']) && $_REQUEST['link_filter'] == $countType) || ($countType == 'all' && !isset($_REQUEST['link_filter'])))
		{
		    $classcurrent = 'class="current"';
		    $radio_checked = 'checked="checked"';
		}
		if ($i > 1)
		    echo ' | ';
		echo '<li><a '.$classcurrent.' href="admin.php?page=wysija_subscribers&link_filter='.$countType.'">'.$tradText.' <span class="count">('.$count.')</span></a>';
		echo '<input type="radio" name="wysija[filter][link_filter]" id="link_filter_'.$countType.'" value="'.$countType.'" '.$radio_checked.' style="display:none;"/>';
		echo '</li>';
		$i++;
	    }
	    ?>
	</ul>
	<?php $this->searchBox(); ?>
	<?php
	$action_locale = array(
            'delete' => __('Delete this subscriber forever?', WYSIJA),
            'delete_bulk' => __('Delete these subscribers forever?', WYSIJA)
	);
	?>
	<div class="tablenav">
	    <div class="alignleft actions">
		<select name="method" class="global-action" data-locale='<?php echo json_encode($action_locale); ?>'>
		    <option selected="selected" value=""><?php _e('Bulk Actions', WYSIJA); ?></option>
		    <option value=""><?php _e('Move to list', WYSIJA); ?>...</option>
		    <?php
		    foreach ($data['lists'] as $listK => $list)
		    {
			//if(!(isset($_REQUEST['filter-list']) && $_REQUEST['filter-list']== $listK) && $list['is_enabled']){ // Commented by TNT
			if ($list['is_enabled'])
			{
			    ?><option value="actionvar_movetolist-listid_<?php echo $listK ?>" data-nonce="<?php echo $this->secure(array('action' => "actionvar_movetolist-listid_" . $listK), true)?>"><?php
				echo str_repeat('&nbsp;', 5).$list['name'];
				if (isset($list['users']))
				    echo ' ('.$list['users'].')';
				?></option><?php
			}
		    }
		    ?>
		    <option value=""><?php _e('Add to list', WYSIJA); ?>...</option>
		    <?php
		    foreach ($data['lists'] as $listK => $list)
		    {
			//if(!(isset($_REQUEST['filter-list']) && $_REQUEST['filter-list']== $listK) && $list['is_enabled']){ // Commented by TNT
			if ($list['is_enabled'])
			{
			    ?><option value="actionvar_copytolist-listid_<?php echo $listK ?>" data-nonce="<?php echo $this->secure(array('action' => "actionvar_copytolist-listid_" . $listK), true)?>"><?php
				echo str_repeat('&nbsp;', 5).$list['name'];
				if (isset($list['users']))
				    echo ' ('.$list['users'].')';
				?></option><?php
			}
		    }
		    ?>
		    <option value=""><?php _e('Remove from list', WYSIJA); ?>...</option>
		    <?php
		    foreach ($data['lists'] as $listK => $list)
		    {
			//if(!(isset($_REQUEST['filter-list']) && $_REQUEST['filter-list']== $listK) && $list['is_enabled']){ // Commented by TNT
			if ($list['is_enabled'])
			{
			    ?><option value="actionvar_removefromlist-listid_<?php echo $listK ?>" data-nonce="<?php echo $this->secure(array('action' => "actionvar_removefromlist-listid_" . $listK), true)?>"><?php
			    echo str_repeat('&nbsp;', 5).$list['name'];
			    if (isset($list['users']))
				echo ' ('.$list['users'].')';
			    ?></option><?php
			}
		    }
		    ?>
		    <option value="actionvar_removefromalllists" data-nonce="<?php echo $this->secure(array('action' => "actionvar_removefromalllists" ), true)?>"><?php _e('Remove from all lists', WYSIJA); ?></option>
		    <option value="exportlist" data-nonce="<?php echo $this->secure(array('action' => "exportlist" ), true)?>"><?php _e('Export', WYSIJA); ?></option>
		    <option value="deleteusers" data-nonce="<?php echo $this->secure(array('action' => "deleteusers" ), true)?>"><?php _e('Delete subscribers', WYSIJA); ?></option>
		    <?php
		    $config_model = WYSIJA::get('config', 'model');
			$confirm_dbleoptin = $config_model->getValue('confirm_dbleoptin');
		    if ($confirm_dbleoptin)
		    {
			?>
	    	    <option value="actionvar_confirmusers" data-nonce="<?php echo $this->secure(array('action' => "actionvar_confirmusers" ), true)?>"><?php _e('Confirm unconfirmed subscribers', WYSIJA); ?></option>
				<option value="actionvar_resendconfirmationemail" data-nonce="<?php echo $this->secure(array('action' => "actionvar_resendconfirmationemail" ), true)?>"><?php _e('Resend confirmation email', WYSIJA); ?></option>
		    <?php } ?>
		</select>
		<input type="submit" class="bulksubmit button-secondary action" name="doaction" value="<?php echo esc_attr(__('Apply', WYSIJA)); ?>">
		    <?php
		    	$this->secure(array(
			    	'action' => 'bulk_action',
			    	'controller' => 'wysija_subscribers'
			    ));
			   ?>
	    </div>

	    <div class="alignleft actions">
		<select name="wysija[filter][filter_list]" class="global-filter">
	<?php
	$is_list_pre_selected = false;
        $options_list = '';
	foreach ($data['lists'] as $listK => $list)
	{
			$selected = '';
			if (in_array($listK, $data['selected_lists'])) {
				$selected = ' selected="selected" ';
				$is_list_pre_selected = true;
			}
	    if (isset($list['users']))
		$options_list .= '<option '.$selected.' value="'.$list['list_id'].'">'.$list['name'].' ('.$list['users'].')'.'</option>';
	    else
		$options_list .= '<option '.$selected.' value="'.$list['list_id'].'">'.$list['name'].'</option>';
	}

	?>

                    <?php
                        // Now, if there is not any selected list, let's select "View all lists" by default.
                        $selected = in_array('', $data['selected_lists']) ? ' selected="selected" ' : '';
                    ?>
			<option <?php echo $selected; ?> data-sort='0' value=""><?php _e('View all lists', WYSIJA); ?></option>
                    <?php
                        $selected = in_array('orphaned', $data['selected_lists']) ? ' selected="selected" ' : '';
			if (in_array('orphaned', $data['selected_lists'])) {
				$selected = ' selected="selected" ';
				$is_list_pre_selected = true;
			}
                    ?>
                    <option <?php echo $selected; ?> value="orphaned" data-sort='0'><?php _e('Subscribers in no list', WYSIJA); ?></option>


                        <?php echo $options_list; ?>
		</select>
		<input type="submit" class="filtersubmit button-secondary action" name="doaction" value="<?php echo esc_attr(__('Filter', WYSIJA)); ?>">
	    </div>


		    <?php $this->pagination(); ?>

	    <div class="clear"></div>
	</div>
		    <?php
		}

		/*
		 * main view
		 */

		function listing($data, $simple = false)
		{
		    ?>
	<div class="list">
	    <table cellspacing="0" class="widefat fixed">
		<thead>
		    <?php
		    $status_sorting = $last_opened_sorting = $last_clicked_sorting = $username_sorting = $created_at_sorting = ' sortable desc';
		    $hiddenOrder = '';
		    if (isset($_REQUEST['orderby']))
		    {
			switch ($_REQUEST['orderby'])
			{
			    case 'email':
				$username_sorting = ' sorted '.$_REQUEST['ordert'];
				break;
			    case 'created_at':
				$created_at_sorting = ' sorted '.$_REQUEST['ordert'];
				break;
			    case 'last_opened':
				$last_opened_sorting = ' sorted '.$_REQUEST['ordert'];
				break;
			    case 'last_clicked':
				$last_clicked_sorting = ' sorted '.$_REQUEST['ordert'];
				break;
			    case 'status':
				$status_sorting = ' sorted '.$_REQUEST['ordert'];
				break;
			}
			$hiddenOrder = '<input type="hidden" name="orderby" id="wysija-orderby" value="'.esc_attr($_REQUEST["orderby"]).'"/>';
			$hiddenOrder.='<input type="hidden" name="ordert" id="wysija-ordert" value="'.esc_attr($_REQUEST["ordert"]).'"/>';
		    }
		    $header = '<tr class="thead">
                            <th scope="col" id="user-id" class="manage-column column-user-id check-column"><input type="checkbox" /></th>
                            <th class="manage-column column-username'.$username_sorting.'" id="email" scope="col" style="width:140px;"><a href="#" class="orderlink" ><span>'.__('Email', WYSIJA).'</span><span class="sorting-indicator"></span></a></th>';
		    $header .='<th class="manage-column column-list-names" id="list-list" scope="col">'.__('Lists', WYSIJA).'</th>';
		    $header .='<th class="manage-column column-status'.$status_sorting.'" id="status" scope="col" style="width:80px;"><a href="#" class="orderlink" ><span>'.__('Status', WYSIJA).'</span><span class="sorting-indicator"></span></a></th>';
		    $header .= '<th class="manage-column column-date'.$created_at_sorting.'" id="created_at" scope="col"><a href="#" class="orderlink" ><span>'.__('Subscribed on', WYSIJA).'</span><span class="sorting-indicator"></span></a></th>';
		    $header .= '<th class="manage-column column-date' . $last_opened_sorting . '" id="last_opened" scope="col"><a href="#" class="orderlink" ><span>' . __('Last open', WYSIJA) . '</span><span class="sorting-indicator"></span></a></th>';
		    $header .= '<th class="manage-column column-date' . $last_clicked_sorting . '" id="last_clicked" scope="col"><a href="#" class="orderlink" ><span>' . __('Last click', WYSIJA) . '</span><span class="sorting-indicator"></span></a></th>';

		    $header .= '</tr>';
		    echo $header;
		    ?>
		</thead>
		<tfoot>
		       <?php
		       echo $header;
		       ?>
		</tfoot>
		       <?php if ($data['show_batch_select'])
		       { ?>
	    	<tr class="batch-select">
	    	    <td colspan="5">
	    		<input type="radio" name="wysija[user][force_select_all]" id="force_select_all" style="display:none;" />
	    		<input type="hidden" name="wysija[user][timestamp]" value="<?php echo $data['max_create_at']; ?>"/>
	    		<div class="force_to_select_all_link" style="display:none">
			   <?php _e('All subscribers on this page are selected.', WYSIJA); ?>
	    		    <a href="javascript:void(0);"><?php echo sprintf(__('Select all %1$s subscribers.', WYSIJA), $data['current_counts']); ?></a>
	    		</div>
	    		<div class="clear_select_all" style="display:none">
			   <?php echo sprintf(__('All %1$s subscribers are selected.', WYSIJA), $data['current_counts']); ?>
	    		    <a><?php echo __('Clear selection', WYSIJA); ?></a>
	    		</div>
	    	    </td>
	    	</tr>
	<?php
		}
		$class = 'list:'.$this->model->table_name.' '.$this->model->table_name.'-list';
		$id = 'wysija-'.$this->model->table_name;
	?>

		<tbody class="<?php echo $class; ?>" id="<?php echo $id; ?>">
		<?php
			$listingRows = '';
			$alt = true;

			$statuses = array('-1' => __('Unsubscribed', WYSIJA), '0' => __('Unconfirmed', WYSIJA), '1' => __('Subscribed', WYSIJA));

			$config = WYSIJA::get('config', 'model');
			if(!$config->getValue('confirm_dbleoptin')) {
		    	$statuses['0'] = $statuses['1'];
			}

			$links_helper = WYSIJA::get('links', 'helper');
			foreach ($data['subscribers'] as $row) {
		    	$classRow = '';
		    	if($alt) {
		    		$classRow = 'alternate';
		    	}

		    ?>
			<tr class="<?php echo $classRow; ?>">
				<th scope="col" class="check-column" >
	        		<input type="checkbox" name="wysija[user][user_id][]" id="user_id_<?php echo $row['user_id'] ?>" value="<?php echo esc_attr($row['user_id']) ?>" class="checkboxselec" />
	        	</th>
	        	<td class="username column-username">
				<?php
					echo get_avatar($row['email'], 32);
					echo '<strong>'.$row['email'].'</strong>';
					echo '<p style="margin:0;">'.esc_html($row['firstname'].' '.$row['lastname']).'</p>';
				?>
		    	<div class="row-actions">
		    	    <span class="edit">
			    		<a href="<?php echo $links_helper->detailed_subscriber($row['user_id']); ?>" class="submitedit"><?php _e('View stats or edit', WYSIJA) ?></a>
		    	    </span>
		    	</div>
	        </td>
	        <td><?php
	    if (isset($row['lists']))
	    {
		echo $row['lists'];
		if (isset($row['unsub_lists']))
		    echo ' / ';
	    }

	    if (isset($row['unsub_lists']))
		echo '<span class="wysija-unsubscribed-on" title="'.__('Lists to which the subscriber was subscribed.', WYSIJA).'">'.$row['unsub_lists'].'</span>';
	    ?></td>
	        <td><?php echo $statuses[$row['status']]; ?></td>
	        <td><?php echo $this->fieldListHTML_created_at($row['created_at']) ?></td>
	        <td><?php echo $this->fieldListHTML_created_at($row['last_opened']) ?></td>
	        <td><?php echo $this->fieldListHTML_created_at($row['last_clicked']) ?></td>
	        </tr><?php
			$alt = !$alt;
		    }
		    ?>

	    </tbody>
	</table>
	</div>

			    <?php
			    echo $hiddenOrder;
			}

			function export($data)
			{
			    /* make a list of fields to export */
			    ?>
	<form name="submitexport" method="post" id="submitexport" action="" class="form-valid">
	    <table class="form-table">
		<tbody>
	<?php
	if (!isset($data['subscribers']))
	{//select a list and export
	    /* set the filters necessary to export
	     * 1-export a list
	     * 2-export all
	     * 3-export confirmed
	     * 4-export
	     */

	    $formObj = WYSIJA::get('forms', 'helper');
	    $config = WYSIJA::get('config', 'model');
	    ?>
	    	    <tr>
	    		<th><label for="filterlist"><?php _e("Pick a list", WYSIJA); ?></label></th>
	    		<td>
	    <?php
	    $lists_html = '';
	    foreach ($data['lists'] as $listK => $list)
	    {
		$lists_html .= '<label><input type="checkbox" name="wysija[export][filter][list][]" checked="checked" class="validate[minCheckbox[1]] checkbox" value="'.esc_attr($list['list_id']).'" />';
		$lists_html .= '&nbsp;'.$list['name'];
		$lists_html .= ' <span class="count-confirmed-only">('.$list['subscribers'].')</span>';
		$lists_html .= '<span class="count-all">('.((int) $list['belonging']).')</span>';
		$lists_html .= '</label>';
	    }

	    echo $lists_html;
	    ?>
	    		</td>
	    	    </tr>
	    	    <tr>
	    		<th><label for="exportformat"><?php _e("Format", WYSIJA); ?></label></th>
	    		<td>
	    		    <input type="radio" id="export_format1" value="," checked="checked" name="wysija[export][format]">
	    		    <label for="export_format1"><?php _e('CSV file', WYSIJA); ?></label>
	    		    <input type="radio" id="export_format2" value=";" name="wysija[export][format]">
	    		    <label for="export_format2"><?php _e('Excel for Windows', WYSIJA); ?></label>
	    		</td>
	    	    </tr>
				<?php
				if ($config->getValue('confirm_dbleoptin'))
				{
				    ?>
			    <tr>
				<th><label for="confirmedcheck"><?php _e("Export confirmed subscribers only", WYSIJA); ?></label></th>
				<td>
				    <input type="checkbox" name="wysija[export][filter][confirmed]" checked="checked" value="1" id="confirmedcheck" />
				</td>
			    </tr>
		<?php
	    }
	}
	elseif (!empty($data['user']['timestamp']) && !empty($data['user']['force_select_all']) && (bool) $data['user']['force_select_all'])
	{ //batch-select and export
	    if (!empty($data['filter']) && is_array($data['filter']))
		foreach ($data['filter'] as $k => $v)
		    echo '<input type="hidden" value="'.$v.'" name="wysija[filter]['.$k.']" />';
	    foreach ($data['user'] as $k => $v)
		if ($k != 'user_id') // we don't ask for user_id in case of batch-select
		    echo '<input type="hidden" value="'.$v.'" name="wysija[user]['.$k.']" />';
	}
	?>
		    <tr>
			<th scope="row">
			    <label for="listfields"><?php _e('List of fields to export', WYSIJA); ?></label>
			</th>
			<td>
	<?php
	$model_user_field = WYSIJA::get('user_field', 'model');
	$fields = $model_user_field->getFields();

	$helper_forms = WYSIJA::get('forms', 'helper');
	echo $helper_forms->checkboxes(array('class' => 'validate[minCheckbox[1]] checkbox', 'name' => 'wysija[export][fields][]', 'id' => 'wysijafields'), $fields);
	?>
			</td>
		    </tr>
		</tbody>
	    </table>
	    <p class="submit">
		<input type="hidden" name="wysija[export][user_ids]" id="user_ids" value="<?php if (isset($data['subscribers'])) echo base64_encode(json_encode($data['subscribers'])) ?>" />
		<input type="hidden" value="export_get" name="action" />
                <?php $this->secure(array('action' => "export_get")); ?>
		<input type="submit" value="<?php echo esc_attr(__('Export', WYSIJA)) ?>" class="button-primary wysija">
	    </p>
	</form>
	<?php
    }

    function add($data = false)
    {
	$this->buttonsave = __('Save', WYSIJA);
	if (!$data['user'] || isset($this->add))
	{

	    $this->buttonsave = __('Add Subscriber', WYSIJA);
	}

	$formid = 'wysija-'.$_REQUEST['action'];
	?>
	<form name="<?php echo $formid ?>" method="post" id="<?php echo $formid ?>" action="" class="form-valid">

	    <table class="form-table">
		<tbody>
		    <tr>
			<th scope="row">
			    <label for="email"><?php _e('Email', WYSIJA); ?></label>
			</th>
			<td>
			    <input type="text" size="40" class="validate[required,custom[email]]" id="email" value="<?php if ($data['user']) echo esc_attr($data['user']['details']['email']) ?>" name="wysija[user][email]" />
			</td>
		    </tr>

		    <tr>
			<th scope="row">
			    <label for="fname"><?php _e('First name', WYSIJA); ?></label>
			</th>
			<td>
			    <input type="text" size="40" id="fname" value="<?php if ($data['user']) echo esc_attr($data['user']['details']['firstname']) ?>" name="wysija[user][firstname]" />
			</td>
		    </tr>

		    <tr>
			<th scope="row">
			    <label for="lname"><?php _e('Last name', WYSIJA); ?></label>
			</th>
			<td>
			    <input type="text" size="40" id="lname" value="<?php if ($data['user']) echo esc_attr($data['user']['details']['lastname']) ?>" name="wysija[user][lastname]" />
			</td>
		    </tr>
		    <tr>
			<th scope="row">
			    <label for="user-status" ><?php _e('Status', WYSIJA); ?></label>
			</th>
			<td>
			    <?php
			    $form_obj = WYSIJA::get('forms', 'helper');
			    $user_status = 1;
			    $config = WYSIJA::get('config', 'model');
			    if ($config->getValue("confirm_dbleoptin"))
			    {
				$statusddp = array('1' => __('Subscribed', WYSIJA), '0' => __('Unconfirmed', WYSIJA), '-1' => __('Unsubscribed', WYSIJA));
				if ($data['user'])
				    $user_status = $data['user']['details']['status'];
			    }else
			    {
				$statusddp = array('1' => __('Subscribed', WYSIJA), '-1' => __('Unsubscribed', WYSIJA));
				if ($data['user'])
				{
				    if ((int) $data['user']['details']['status'] == 0)
				    {
					$user_status = 1;
				    }
				    else
				    {
					$user_status = $data['user']['details']['status'];
				    }
				}
			    }


			    echo "<p>".$form_obj->radios(
				    array('id' => 'user-status', 'name' => 'wysija[user][status]'), $statusddp, $user_status, ' class="validate[required]" ')."</p>";
			    ?>
			</td>
		    </tr>
		    <tr>
			<th scope="row">
			    <label for="lists" class="title"><?php _e('Lists', WYSIJA); ?></label>

			</th>
			<td>
			    <?php
			    $field_html = '';
			    $field = 'list';
			    $valuefield = array();
			    if ($data['user'] && isset($data['user']['lists']))
			    {

				foreach ($data['user']['lists'] as $list)
				{
				    $valuefield[$list['list_id']] = $list;
				}
			    }

			    $_display_style = 2; // 1 = 1 column, 2 = float left
	            usort( $data['list'], array( $this, 'sort_by_name' ) );

			    foreach ($data['list'] as $list)
			    {

				$checked = false;
				$extra_checkbox = $hidden_field = '';

				if (isset($valuefield[$list['list_id']]))
				{
				    //if the subscriber has this list and is not unsubed then we check the checkbox
				    if ($valuefield[$list['list_id']]['unsub_date'] <= 0)
				    {
					$checked = true;
				    }
				    else
				    {
					//we keep a reference of the list to which we are unsubscribed
					$hidden_field = $form_obj->hidden(array('id' => $field.$list['list_id'], 'name' => 'wysija[user_list][unsub_list][]', 'class' => 'checkboxx'), $list['list_id']);
					$hidden_field.=' / <span class="wysija-unsubscribed-on"> '.sprintf(__('Unsubscribed on %1$s', WYSIJA), date('D, j M Y H:i:s', $valuefield[$list['list_id']]['unsub_date'])).'</span>';
				    }
				}

				$checkout_params = array('id' => $field.$list['list_id'], 'name' => "wysija[user_list][list_id][]", 'class' => '');
				$checkbox = $form_obj->checkbox($checkout_params, $list['list_id'], $checked, $extra_checkbox) . "<label for='list{$list['list_id']}'>{$list['name']}</label>";

				if ($_display_style == 1)
				{
				    $field_html.= '<p><label for="'.$field.$list['list_id'].'">';
				    $field_html.=$checkbox;
				    $field_html.=$hidden_field;
				    $field_html.='</label></p>';
				}
				else
				{
				    $field_html .= '<p class="labelcheck">';
				    $field_html .= $checkbox;
				    $field_html .= $hidden_field;
				    $field_html .= '</p>';
				}
			    }


			    echo $field_html;
			    ?>
			</td>
		    </tr>
			<?php
			/*
			  Custom Fields.
			 */
			echo WJ_FieldRender::render_all(
				$data['user']['details']['user_id']
			);
			?>
		    <tr class='submit_row'>
			<td colspan='2'>
	<?php $this->secure(array('action' => "save", 'id' => $data['user']['details']['user_id'])); ?>
			    <input type="hidden" name="wysija[user][user_id]" id="user_id" value="<?php echo esc_attr($data['user']['details']['user_id']) ?>" />
			    <input type="hidden" value="save" name="action" />
			    <input type="submit" value="<?php echo esc_attr($this->buttonsave) ?>" class="button-primary wysija">
			</td>
		    </tr>
		</tbody>
	    </table>
	</form>
	<?php
    }

    // @todo: move to a helper
    function subscribers_stats($htmlContent, $data)
    {
	$htmlContent = '';

	if (count($data['charts']['stats']) > 0)
	{
	    $htmlContent.= '<p>';
	    $helper_licence = WYSIJA::get('licence', 'helper');
	    $url_checkout = $helper_licence->get_url_checkout('subscriber_stats');
	    $htmlContent.= str_replace(
		    array('[link]', '[/link]'), array('<a title="'.__('Get Premium now', WYSIJA).'" target="_blank" href="'.$url_checkout.'">', '</a>'), __("Note: Find out what this subscriber opened and clicked with our [link]Premium version.[/link]", WYSIJA));
	    $htmlContent.= '</p>';
	}
	return $htmlContent;
    }

    function edit($data)
    {
	// loop to show the core lists to which the user is subscribed to
	// @todo: we should move this block to controller, or at least a separated function
	foreach ($data['list'] as $keyl => $list)
	{
	    if (!$list['is_enabled'])
	    {
		//make sure this lists is in the user lists
		foreach ($data['user']['lists'] as $ulist)
		{
		    if ($list['list_id'] == $ulist['list_id'])
		    {
			continue(2);
		    }
		}
		unset($data['list'][$keyl]);
	    }
	}
	?>
	<div id="hook_subscriber_left" class="hook-column left-column hook">
		<?php $this->add($data); ?>
		<?php if (!empty($data['hooks']['hook_subscriber_left'])) echo $data['hooks']['hook_subscriber_left']; ?>
	</div>
		<?php if (!empty($data['hooks']['hook_subscriber_right']))
		{ ?>
	    <div id="hook_subscriber_right" class="hook-column right-column hook">
			<?php echo $data['hooks']['hook_subscriber_right']; ?>
	    </div>
		    <?php } ?>
	<?php if (!empty($data['hooks']['hook_subscriber_bottom']))
	{ ?>
	    <div id="hook_subscriber_bottom" class="hook"><?php echo $data['hooks']['hook_subscriber_bottom']; ?></div>
	    <?php
	}
    }

    function globalActionsLists($data = false)
    {
	?>
	<div class="tablenav">

		<?php $this->pagination("&action=lists"); ?>
	    <div class="clear"></div>
	</div>
	<?php
    }

    /*
     * main view when editing lists it has one listing and one form
     */

    function lists($data)
    {
	echo '<form method="post" action="" id="posts-filter">';
	$this->globalActionsLists($data);
	?>
	<div class="list">
	    <table cellspacing="0" class="widefat fixed" >
		<thead>
		    <tr class="thead">
			<th class="manage-column column-name" id="name-list" scope="col" style="width:30%;"><?php _e('Name', WYSIJA) ?></th>
			<th class="manage-column column-subscribed" id="subscribed-list" scope="col"><?php _e('Subscribed', WYSIJA) ?></th>
	<?php
	$config = WYSIJA::get("config", "model");
	if ($config->getValue("confirm_dbleoptin"))
	{
	    ?><th class="manage-column column-unsubscribed" id="unconfirmed-list" scope="col"><?php _e('Unconfirmed', WYSIJA) ?></th><?php
	}
	?>

			<th class="manage-column column-unsubscribed" id="unsubscribed-list" scope="col"><?php _e('Unsubscribed', WYSIJA) ?></th>
	    <?php /* <th class="manage-column column-campaigns" id="campaigns-list" scope="col"><?php _e('Newsletters sent',WYSIJA) ?></th> */ ?>
			<th class="manage-column column-date" id="date-list" scope="col" style="width:15%;"><?php _e('Date created', WYSIJA) ?></th>
		    </tr>
		</thead>

		<tbody class="list:<?php echo $this->model->table_name.' '.$this->model->table_name.'-list" id="wysija-'.$this->model->table_name.'"' ?>>
	<?php
	$listingRows = "";
	$alt = true;
	foreach ($data['list'] as $row => $columns)
	{
	    $classRow = "";
	    if ($alt)
		$classRow = ' class="alternate" ';
	    ?>
	    	       <tr <?php echo $classRow ?> >
	    	       <td class="manage-column column-name"  scope="col">
	        <strong><a href="admin.php?page=wysija_subscribers&id=<?php echo $columns['list_id'] ?>&action=editlist" class="submitedit"><?php
	    echo $columns['name'];
	    ?></a></strong>
	        <div class="row-actions">
	    	<span class="edit">
	    	    <a href="admin.php?page=wysija_subscribers&id=<?php echo $columns['list_id'] ?>&action=editlist" class="submitedit"><?php _e('Edit', WYSIJA) ?></a> |
	    	</span>
	    	<span class="duplicate">
	    	    <a href="admin.php?page=wysija_subscribers&id=<?php echo $columns['list_id'] ?>&action=duplicatelist&_wpnonce=<?php echo $this->secure(array("action" => "duplicatelist", "id" => $columns['list_id']), true); ?>" class="submitduplicate"><?php _e('Duplicate', WYSIJA) ?></a>
	    	</span>
	    <?php if ($columns['namekey'] != "users"): ?>
			|
			<span class="delete">
			    <a href="admin.php?page=wysija_subscribers&id=<?php echo $columns['list_id'] ?>&action=deletelist&_wpnonce=<?php echo $this->secure(array("action" => "deletelist", "id" => $columns['list_id']), true); ?>" class="submitdelete"><?php _e('Delete', WYSIJA) ?></a>
			</span>
	    <?php
	    endif;
	    if (!$columns['is_enabled']):
		?>
			|
			<span class="synch">
			    <a href="admin.php?page=wysija_subscribers&id=<?php echo $columns['list_id'] ?>&action=synchlist&_wpnonce=<?php echo $this->secure(array("action" => "synchlist", "id" => $columns['list_id']), true); ?>" class="submitsynch"><?php _e('Update', WYSIJA) ?></a>
			</span>
		<?php
	    endif;
	    global $current_user;

	    if ($columns['namekey'] == 'users' && !$columns['is_enabled'] && is_multisite() && is_super_admin($current_user->ID)):
		?>
			|
			<span class="synchtotal">
			    <a href="admin.php?page=wysija_subscribers&id=<?php echo $columns['list_id'] ?>&action=synchlisttotal&_wpnonce=<?php echo $this->secure(array("action" => "synchlisttotal", "id" => $columns['list_id']), true); ?>" class="submitsynch"><?php _e('Get all MS users', WYSIJA) ?></a>
			</span>
	    <?php endif; ?>
		    <?php if (!$columns['is_enabled'] && $columns['namekey'] != 'users'): ?>
			|
		    <?php endif; ?>
	    	<span class="view_subscribers">
	    	    <a href="admin.php?page=wysija_subscribers&filter-list=<?php echo $columns['list_id'] ?>">| <?php _e('View subscribers', WYSIJA) ?></a>
	    	</span>
	        </div>

	        </td>
	        <td class="manage-column column-subscribed"  scope="col"><?php echo $columns['subscribers'] ?></td>
	    <?php
	    if ($config->getValue("confirm_dbleoptin"))
	    {
		?><td class="manage-column column-unconfirmed"  scope="col"><?php echo $columns['unconfirmed'] ?></td><?php
	    }
	    ?>

	        <td class="manage-column column-unsubscribed"  scope="col"><?php echo $columns['unsubscribers'] ?></td>
	    <?php /* <td class="manage-column column-campaigns"  scope="col"><?php echo $columns['campaigns_sent'] ?></td> */ ?>
	        <td class="manage-column column-date"  scope="col"><?php echo $this->fieldListHTML_created_at($columns['created_at']) ?></td>
	        </tr>
	    <?php
	    $alt = !$alt;
	}
	?>

	    </tbody>
	</table>
	</div>
	<?php
	echo '</form>';
    }

    function addList($data)
    {
	$this->editList($data);
    }

    function editList($data)
    {
	?>
	<div class="form">

	    <form class="form-valid" action="admin.php?page=wysija_subscribers&action=lists<?php if ($data['form']['list_id']) echo "&id=".$data['form']['list_id'] ?>" id="wysija-edit" method="post" name="wysija-edit">

		<input type="hidden" name="wysija[list][list_id]" id="list_id" value="<?php echo esc_attr($data['form']['list_id']) ?>">
		<table class="form-table">
		    <tbody>
			<tr>
			    <th scope="row">
				<label for="list-name"><?php _e('Name', WYSIJA); ?> </label>
			    </th>
			    <td>
				<input type="text" size="40" class="validate[required]" id="list-name" value="<?php echo esc_attr($data['form']['name']) ?>" name="wysija[list][name]" />
			    </td>
			</tr>
			<tr>
			    <th scope="row">
				<label for="list-desc"><?php _e('Description', WYSIJA); ?> </label>
		    <p class="description"><?php _e('For your own use and never shown to your subscribers.', WYSIJA); ?></p>
		    </th>
		    <td>
			<textarea type="text" cols="40" rows="3" id="list-desc" name="wysija[list][description]" /><?php echo $data['form']['description'] ?></textarea>
		    </td>
		    </tr>
		    </tbody>
		</table>
	<?php
	if ($_REQUEST['action'] == "editlist")
	{
	    $buttonName = __('Update list', WYSIJA);
	}
	else
	{
	    $buttonName = __('Add list', WYSIJA);
	}
	?>
		<p class="submit">
	<?php $this->secure(array('action' => "savelist", 'id' => $data['form']['list_id'])); ?>
		    <input type="hidden" value="savelist" name="action" />
		    <input type="submit" value="<?php echo esc_attr($buttonName) ?>" class="button-primary wysija">
		</p>
	    </form>
	</div>
	<?php
    }

    function import($data)
    {
	$helperNumbers = WYSIJA::get('numbers', 'helper');
	$data = $helperNumbers->get_max_file_upload();
	$bytes = $data['maxmegas'];
	?>
	<div class="form">
	    <form class="form-valid" action="admin.php?page=wysija_subscribers&action=lists" id="wysija-edit" enctype="multipart/form-data" method="post" name="wysija-edit">
		<table class="form-table">
		    <tbody>

			<tr>
			    <th scope="row">
				<label for="redirect"><?php _e('How do you want to import?', WYSIJA); ?> </label>
			    </th>
			    <td>
				<p>
				    <label for="copy-paste">
					<input type="radio" class="validate[required]" id="copy-paste" value="copy" name="wysija[import][type]" ><?php _e('Copy paste in a text box', WYSIJA); ?>
				    </label>
				    <label for="upload-file">
					<input type="radio" class="validate[required]" id="upload-file" value="upload" name="wysija[import][type]"><?php _e('Upload a file', WYSIJA); ?>
				    </label>
	<?php
	$config = WYSIJA::get('config', "model");
	$importPossible = $config->getValue("pluginsImportableEgg");
	$importedalready = $config->getValue("pluginsImportedEgg");
	if (is_array($importPossible))
	{
	    foreach ($importPossible as $tableName => $pluginInfos)
	    {
		if (is_array($importedalready) && in_array($tableName, $importedalready))
		    continue;
		?>
					    <label for="import-from-plugin-<?php echo $tableName; ?>">
						<a class="button-secondary2" id="import-from-plugin-<?php echo $tableName; ?>" href="admin.php?page=wysija_subscribers&action=importplugins">
				    <?php echo sprintf(__('Import from %1$s', WYSIJA), '<strong>"'.$pluginInfos['name'].'"</strong>'); ?>
						</a>
					    </label>
				    <?php
				}
			    }
			    ?>
				</p>
			    </td>
			</tr>

			<tr class="csvmode copy">
			    <th scope="row" >
				<label for="csvtext"><?php _e('Then paste your list here', WYSIJA); ?> </label>
		    <p class="description"><?php echo str_replace(array("[link]", "[/link]"), array('<a target="_blank" href="http://support.mailpoet.com/knowledgebase/importing-subscribers-with-a-csv-file/?utm_source=wpadmin&utm_campaign=import">', '</a>'), __('This needs to be in CSV style or a simple paste from Gmail, Hotmail or Yahoo. See [link]examples in our support site[/link].', WYSIJA)) ?></p>
		    </th>
		    <td>
			<textarea type="text" style="width:500px;" cols="130" rows="10" class="validate[required]" id="csvtext" name="wysija[user_list][csv]" /></textarea>
			<p class="fieldsmatch"></p>
		    </td>
		    </tr>

		    <tr class="csvmode upload">
			<th scope="row" >
			    <label for="csvfile"><?php _e('Upload a file', WYSIJA); ?> </label>
		    <p class="description"><?php echo str_replace(array("[link]", "[/link]"), array('<a target="_blank" href="http://support.mailpoet.com/knowledgebase/importing-subscribers-with-a-csv-file/?utm_source=wpadmin&utm_campaign=import file">', '</a>'), __('This needs to be in CSV style. See [link]examples in our support site[/link].', WYSIJA)) ?></p>
		    </th>
		    <td>
			<input type="file" name="importfile" size="50" />( <?php echo sprintf(__('total max upload file size : %1$s', WYSIJA), $bytes) ?> )
			<p class="fieldsmatch"></p>
		    </td>
		    </tr>

		    <tr>
			<th scope="row" colspan="2">
			    <label for="redirect"><?php _e('Did these subscribers ask to be in your list?', WYSIJA); ?> </label>
		    <p class="description">
			    <?php _e('If the answer is "no", consider yourself a spammer.', WYSIJA); ?><br />
			    <?php echo str_replace(array("[link]", "[/link]"), array('<a target="_blank" href="http://support.mailpoet.com/knowledgebase/dont-import-subscribers-who-didnt-sign-up/#utm_source=wpadmin&utm_campaign=importwarning">', '</a>'), __('[link]Read more on support.mailpoet.com[/link].', WYSIJA)) ?>
		    </p>
		    </th>
		    </tr>
		    </tbody>
		</table>

		<p class="submit">
		    <input type="hidden" value="importmatch" name="action" />
                    <?php $this->secure(array('action' => "importmatch")); ?>
		    <input type="submit" value="<?php echo esc_attr(__('Next step', WYSIJA)) ?>" class="button-primary wysija">

		</p>
	    </form>
	</div>

	    <?php
	}

	function importmatch($data)
	{
	    ?>
	<form class="form-valid" action="admin.php?page=wysija_subscribers&action=lists" id="wysija-edit" method="post"  name="wysija-edit">
	    <div class="list" style="overflow:auto">
		<table cellspacing="0" class="widefat fixed" >
		    <thead>
			<tr class="thead">
			    <th id="first-row"><?php _e('Match data', WYSIJA); ?></th>
			    <?php
			    $columns = array(
				'nomatch' => __('Ignore column...', WYSIJA),
				'email' => __('Email', WYSIJA),
				'firstname' => __('First name', WYSIJA),
				'lastname' => __('Last name', WYSIJA),
				'ip' => __('IP address', WYSIJA),
				'status' => __('Status', WYSIJA));

			    $WJ_Field = new WJ_Field();
			    $custom_fields = $WJ_Field->get_all();

			    $extended_columns = array();
			    if (!empty($custom_fields))
			    {
				foreach ($custom_fields as $row)
				{
				    $extended_columns['cf_'.$row->id] = $row->name;
				}
			    }


			    $helper_form = WYSIJA::get('forms', 'helper');
			    $i = 0;

			    $email_column_matched = false;
			    $this->new_column_can_be_imported = array();

			    $data['csv'][0] = array_map('trim', $data['csv'][0]);

			    foreach ($data['csv'][0] as $column_key => $column_name)
			    {
				$selected = '';

				$columns_array = $columns;
				// we make a key out of the column name
				$column_name_key = str_replace(array(' ', '-', '_'), '', strtolower($column_name));

				// we try to automatically match columns with previous matches recorded in the past
				$import_fields = get_option('wysija_import_fields');
				if (isset($import_fields[$column_name_key]) && substr($import_fields[$column_name_key], 0, 10) != 'new_field|')
				{
				    $selected = $import_fields[$column_name_key];
				}
				else
				{

				    // we're making the matches dropdown with an extra value 'Import as "name of the column"'
				    // since we didn't detect it in the previously matched columns
				    $columns_array = array();
				    foreach ($columns as $col_key => $col_val)
				    {
					// we need to put that extra value right after the ignore column value
					if (count($columns_array) === 1) {
					    $column_name = preg_replace('|[^a-z0-9#_.-]|i','',$column_name);

                                            $columns_array['new_field|input|'.$column_name] = sprintf(__('Import as "%1$s"', WYSIJA), $column_name);
					    $columns_array['new_field|date|'.$column_name] = sprintf(__('Import "%1$s" as date field', WYSIJA), $column_name);
					    $this->new_column_can_be_imported[$column_key] = true;
					}
					else
					{
					    $columns_array[$col_key] = $col_val;
					}
				    }
				}

				// if it is an email column we set it by default as email
				if (!$email_column_matched && isset($data['keyemail'][$column_key]))
				{
				    $selected = 'email';
				    $email_column_matched = true;
				    $columns_array = $columns;
				}
				$columns_array = array_map('trim', array_merge($columns_array, $extended_columns));


				// we're building one dropdown per column
				$dropdown = '<div class="match-dropdown">'.$helper_form->dropdown(array('id' => 'column-match-'.$i, 'name' => 'wysija[match]['.$i.']', 'class' => 'create_extra row-'.$column_key), $columns_array, $selected).'</div>';
				/**
				 * We need to improve the import, fields come back to options for columns later on.
				if (isset($this->new_column_can_be_imported[$column_key])) {
					$dropdown .= '<div class="import-new-field" id="column-match-date-wrap-'.$i.'"><input id="column-match-date-'.$i.'"  type="checkbox" name="wysija[ignore_invalid_date]['.$i.']"\><label for="column-match-date-'.$i.'">' . __('Ignore invalid dates', WYSIJA) . '</label></div>';
				}
				*/
				echo '<th>'.$dropdown.'</th>';
				$i++;
			    }
			    ?>
			</tr>
		    </thead>

		    <tbody class="list:<?php echo $this->model->table_name.' '.$this->model->table_name.'-list" id="wysija-'.$this->model->table_name.'"' ?>>

	    <?php
	    $listingRows = '';
	    $alt = true;
	    $i = 0;
	    foreach ($data['csv'] as $columns)
	    {
		$classRow = '';
		if ($alt)
		    $classRow = ' class="alternate" ';

		echo "<tr $classRow>";
		if (isset($data['firstrowisdata']))
		{
		    $j = $i + 1;
		}
		else
		    $j = $i;

		if ($i == 0)
		{
		    $valuefrow = '';

		    if (isset($data['firstrowisdata']))
		    {
			$valuefrow = '1<input value="1" type="hidden" id="firstrowdata" name="firstrowisdata"  />';
		    }
		    echo '<td>'.$valuefrow.'</td>';
		    //echo '<td><label for="firstrowdata" class="title" title="'.__("This line is not a header description, it is data and needs to be inserted!",WYSIJA).'"><input '.$checked.' type="checkbox" id="firstrowdata" name="firstrowisdata"  />'.__("Insert line!",WYSIJA).'</label></td>';
		}
		else
		    echo '<td>'.$j.'</td>';

		foreach ($columns as $key_col => $val)
		{
      $val = esc_html($val);
		    if ($i == 0 && !isset($data['firstrowisdata']))
			echo '<td><strong>'.$val.'</strong></td>';
		    else
		    {
			if (!empty($this->new_column_can_be_imported[$key_col]))
			{
			    $timestamp = strtotime($val);
			    if ($timestamp > 0)
			    {
				$val_converted = '<span class="converted-field-to-date row-'.$key_col.'" title="'.__('Verify that the date in blue matches the original one.', WYSIJA).'">'.date(get_option('date_format').' '.get_option('time_format'), $timestamp).'</span>';
			    }
			    else
			    {
				$val_converted = '<span class="converted-field-error row-'.$key_col.'" title="'.__('Do not match as a \'date field\' if most of the rows for that column return the same error.', WYSIJA).'">'.__('Error matching date.', WYSIJA).'</span>';
			    }
			    $val = ' <span class="imported-field">'.$val.'</span>'.$val_converted;
			}
			echo '<td>'.$val.'</td>';
		    }
		}
		echo '</tr>';

		$alt = !$alt;
		$i++;
	    }

	    if ($data['totalrows'] > 3)
	    {
		?>

	    		   <tr class="alternate" >
		<?php
		echo '<td>...</td>';
		foreach ($data['csv'][0] as $col)
		{
		    echo '<td>...</td>';
		}
		?>
	    		   </tr>
	    		<tr><td><?php echo $data['totalrows'] ?></td>
	    <?php
	    foreach ($data['lastrow'] as $key_col => $val)
	    {
        $val = esc_html($val);
		if (!empty($this->new_column_can_be_imported[$key_col]))
		{
		    $timestamp = strtotime($val);
		    if ($timestamp > 0)
		    {
			$val_converted = '<span class="converted-field-to-date row-'.$key_col.'" title="'.__('Verify that the date in blue matches the original one.', WYSIJA).'">'.date(get_option('date_format').' '.get_option('time_format'), $timestamp).'</span>';
		    }
		    else
		    {
			$val_converted = '<span class="converted-field-error row-'.$key_col.'" title="'.__('Do not match as a \'date field\' if most of the rows for that column return the same error.', WYSIJA).'">'.__('Error matching date.', WYSIJA).'</span>';
		    }
		    $val = ' <span class="imported-field">'.$val.'</span>'.$val_converted;
		}
		echo '<td>'.$val.'</td>';
	    }
	    ?>
	    		</tr>
		<?php
	    }
	    ?>
		    </tbody>
		</table>
	    </div>
	<?php
	if ($data['errormatch'])
	{

	}
	else
	{
	    ?>
	        <table class="form-table">
	    	<tbody>
	    	    <tr>
	    		<th scope="row">
	    		    <label for="name"><?php _e('Pick one or many lists', WYSIJA); ?> </label>
	    	<p class="description"><?php _e('Pick the lists you want to import those subscribers to.', WYSIJA); ?> </p>
	    	</th>
	    	<td>
	    <?php
	    //create an array of existing lists to import within
	    $model_list = WYSIJA::get('list', 'model');
	    $lists = $model_list->get(array('name', 'list_id'), array('is_enabled' => 1));
	    //first value is to create new list
	    $lists[] = array('name' => __('New list', WYSIJA), 'list_id' => 0);

	    //create an array of active(status 99) follow_up emails aossicated to a list_id
	    $helper_email = WYSIJA::get('email', 'helper');
	    $follow_ups_per_list = $helper_email->get_active_follow_ups(array('subject', 'params'));

	    $follow_up_name_per_list = array();
	    foreach ($follow_ups_per_list as $list_id => $follow_ups)
	    {
		if (!isset($follow_up_name_per_list[$list_id]))
		    $follow_up_name_per_list[$list_id] = array();
		foreach ($follow_ups as $follow_up)
		{
		    $follow_up_name_per_list[$list_id][] = $follow_up['subject'];
		}
	    }

	    $helper_form = WYSIJA::get('forms', 'helper');
	    //field name for processing
	    $field = 'list';
	    $fieldHTML = '<div>';
	    foreach ($lists as $list)
	    {
		if ($list['list_id'] == 0)
		{
		    $fieldHTML.= '<p><label for="'.$field.$list['list_id'].'">';
		    $fieldHTML.=$helper_form->checkbox(array('class' => 'validate[minCheckbox[1]] checkbox', 'id' => $field.$list['list_id'], 'name' => "wysija[user_list][$field][]"), $list['list_id']).'<span>'.$list['name'].'</span>';
		    $fieldHTML.='</label> ';
		    $fieldHTML.='<span id="blocknewlist">'.$helper_form->input(array('class' => 'validate[required]', 'id' => 'namenewlist', 'size' => 30, 'name' => 'wysija[list][newlistname]', 'value' => __('Type name of your new list', WYSIJA))).'</span></p>';
		}
		else
		{
		    $fieldHTML.= '<p><label for="'.$field.$list['list_id'].'">'.$helper_form->checkbox(array('class' => 'validate[minCheckbox[1]] checkbox', 'id' => $field.$list['list_id'], 'name' => "wysija[user_list][$field][]"), $list['list_id']).$list['name'];

		    if (isset($follow_up_name_per_list[$list['list_id']]))
		    {
			$fieldHTML.=' <span style="margin-left:10px;"><strong>'.__('Note:', WYSIJA).' </strong>'.sprintf(__('subscribers will receive "%1$s" after import.', WYSIJA), implode(', ', $follow_up_name_per_list[$list['list_id']])).'</span>';
		    }
		    $fieldHTML.='</label></p>';
		}
	    }

	    $fieldHTML .= '</div>';
	    echo $fieldHTML;
	    ?>
	    	</td>
	    	</tr>
	    	</tbody>
	        </table>
	        <p class="submit">
	    <?php $this->secure(array('action' => 'import_save')); ?>
	    	<input type="hidden" value="<?php echo esc_attr($data['dataImport']) ?>" name="wysija[dataImport]" />

	    	<input type="hidden" value="import_save" name="action" />
	    	<input type="submit" value="<?php echo esc_attr(__('Import', WYSIJA)) ?>" class="button-primary wysija">
	        </p>
	    <?php
	}
	?>
	</form>
	<?php
    }

    function import_save($data)
    {
	return false;
    }

    function importplugins($data)
    {
	echo '<form class="form-valid" action="admin.php?page=wysija_subscribers&action=lists" id="wysija-edit" method="post"  name="wysija-edit">';
	echo '<ul>';
	$config = WYSIJA::get('config', 'model');
	$imported_already = $config->getValue('pluginsImportedEgg');
	foreach ($data['plugins'] as $tablename => $pluginInfos)
	{
	    if (is_array($imported_already) && in_array($tablename, $imported_already))
		continue;
	    echo '<li><label for="import-'.$tablename.'1">';
	    echo sprintf(__('Import the %1$s subscribers from the plugin: %2$s ', WYSIJA), "<strong>".$pluginInfos['total']."</strong>", "<strong>".$pluginInfos['name']."</strong>").'</label>';
	    echo '<label for="import-'.$tablename.'1"><input checked="checked" type="radio" id="import-'.$tablename.'1" name="wysija[import]['.$tablename.']" value="1" />'.__('Yes', WYSIJA).'</label>';
	    echo '<label for="import-'.$tablename.'0"><input type="radio" id="import-'.$tablename.'0" name="wysija[import]['.$tablename.']" value="0" />'.__('No', WYSIJA).'</label>';
	    echo '</li>';
	}
	echo '</ul>';
	?>
	<p class="submit">
	<?php $this->secure(array('action' => "importpluginsave")); ?>
	    <input type="hidden" value="importpluginsave" name="action" />
	    <input type="submit" value="<?php echo esc_attr(__('Import', WYSIJA)) ?>" class="button-primary wysija">
	</p>
	<?php
	echo '</form>';
    }
}
