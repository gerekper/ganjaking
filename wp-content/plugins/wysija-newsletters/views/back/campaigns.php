<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_view_back_campaigns extends WYSIJA_view_back {

	var $icon = 'icon-edit-news';
	var $column_action_list = 'name';
	var $queuedemails = false;

	function __construct() {
		$this->title = __('All Newsletters');
		parent::__construct();
		$this->jsTrans['selecmiss'] = __('Select at least 1 subscriber!', WYSIJA);
		$this->search = array('title' => __('Search newsletters', WYSIJA));
		$this->column_actions = array('editlist' => __('Edit', WYSIJA), 'duplicatelist' => __('Duplicate', WYSIJA), 'deletelist' => __('Delete', WYSIJA));
	}

	function installation() {
		return '';
	}

	/**
	 * @see parent::header()
	 */
	function header($data = '')
	{
	if (!empty($_REQUEST['action']))
	{
		switch (trim(strtolower($_REQUEST['action'])))
		{
		case 'viewstats':
			$this->icon = 'icon-stats';
			break;
		default:
			break;
		}
	}
	parent::header($data);
	}

	function main($data) {
		$this->menuTop($this->action);

		echo '<form method="post" action="" id="posts-filter">';
		$this->filtersLink($data);
		$this->filterDDP($data);
		$this->listing($data);
		echo '</form>';
	}

	function menuTop($actionmenu = false, $data = false) {

		$array_translation = array(
			'back' => __('Back', WYSIJA),
			'add' => __('Create a new email', WYSIJA)
		);

		$arrayMenus = array();
		switch ($actionmenu) {
			case 'main':
				$arrayMenus[] = 'add';
				break;

			case 'viewstats':
			case 'add':
			case 'edit':
			default:
				break;
		}
		$menu = '';
		$classes = function_exists('wp_star_rating') ? 'add-new-h2' : 'button-secondary2';
		if ($arrayMenus) {
			$menu .= '<span class="action_buttons">';
			foreach ($arrayMenus as $action) {
				$action_params = $action;
				$extra_params = $link = '';
				if (empty($link))
					$link = 'admin.php?page=wysija_campaigns&action=' . $action_params;
				$menu.= '<a id="action-' . str_replace("_", "-", $action) . '" ' . $extra_params . ' href="' . $link . '" class="action-' . str_replace("_", "-", $action) . ' ' . $classes . '">' . $array_translation[$action] . '</a>';
				if ($actionmenu == 'main' && $action == 'add') {
					$menu.='<span class="description" > ' . __('... or duplicate one below to copy its design.', WYSIJA) . '</span>';
				}
			}
			$menu .= '</span>';
			$menu .= '</h2>';
			$menu .= '<h2 class="hidden">';
		}
		return $menu;
	}

	function filterDDP($data) {
		if (empty($data['campaigns']))
			return;
		?>
		<ul class="subsubsub">
			<?php
			$total = count($data['counts']);
			$i = 1;
			foreach ($data['counts'] as $countType => $count) {
				if (!$count) {
					$i++;
					continue;
				}
				switch ($countType) {
					case 'all':
						$tradText = __('All', WYSIJA);
						break;
					case 'status-sent':
						$tradText = __('Sent', WYSIJA);
						break;
					case 'status-sending':
						$tradText = __('Sending', WYSIJA);
						break;
					case 'status-draft':
						$tradText = __('Draft', WYSIJA);
						break;
					case 'status-paused':
						$tradText = __('Paused', WYSIJA);
						break;
					case 'status-scheduled':
						$tradText = __('Scheduled', WYSIJA);
						break;
					case 'type-regular':
						$tradText = __('Standard Newsletters', WYSIJA);
						break;
					case 'type-autonl':
						$tradText = __('Auto Newsletters', WYSIJA);
						break;
				}
				$classcurrent = '';
				if ((isset($_REQUEST['link_filter']) && $_REQUEST['link_filter'] == $countType) || ($countType == 'all' && !isset($_REQUEST['link_filter'])))
					$classcurrent = 'class="current"';
				echo '<li><a ' . $classcurrent . ' href="admin.php?page=wysija_campaigns&link_filter=' . $countType . '">' . $tradText . ' <span class="count">(' . $count . ')</span></a>';

				if ($total != $i)
					echo ' | ';
				echo '</li>';
				$i++;
			}
			?>
		</ul>

		<?php $this->searchBox(); ?>

		<div class="tablenav">
			<div class="alignleft actions">
				<span class="alignleft actions" id="bulksubmit-area" style="display:none;">
					<?php
						$action_locale = array(
								'delete' => __('Delete this newsletter for ever?', WYSIJA),
								'delete_bulk' => __('Delete these newsletters for ever?', WYSIJA)
						);
					?>
					<?php
					/*
					<input type="submit" class="bulksubmit-button button-secondary action" name="doaction" data-action="delete"
						   data-locale='<?php echo json_encode($action_locale); ?>'
						   value="<?php echo esc_attr(__('Delete selected', WYSIJA)); ?>">
					*/
					?>
			<?php $this->secure('delete'); ?>
				</span>

				<select name="filter-date" class="global-filter">
					<option selected="selected" value=""><?php echo esc_attr(__('Show all months', WYSIJA)); ?></option>
					<?php
					//echo $this->fieldListHTML_created_at($row["created_at"])

					foreach ($data['dates'] as $listK => $list) {
						$selected = "";
						if (isset($_REQUEST['filter-date']) && $_REQUEST['filter-date'] == $listK)
							$selected = ' selected="selected" ';
						echo '<option ' . $selected . ' value="' . esc_attr($listK) . '">' . $list . '</option>';
					}
					?>
				</select>
			</div>

			<div class="alignleft actions">
				<select name="filter-list" class="global-filter">
					<option selected="selected" value=""><?php _e('View by lists', WYSIJA); ?></option>
					<?php
					foreach ($data['lists'] as $listK => $list) {
						$selected = "";
						if (isset($_REQUEST['filter-list']) && $_REQUEST['filter-list'] == $listK)
							$selected = ' selected="selected" ';
						if ($list['users'] > 0)
							echo '<option ' . $selected . ' value="' . $list['list_id'] . '">' . $list['name'] . ' (' . $list['users'] . ')' . '</option>';
					}
					?>
				</select>
				<input type="submit" class="filtersubmit button-secondary action" name="doaction" value="<?php echo esc_attr(__('Filter', WYSIJA)); ?>">
			</div>
		<?php $this->pagination(); ?>

			<div class="clear"></div>
		</div>
		<?php
	}

	function getTransStatusEmail($status) {
		switch ($status) {
			case 'all':
				$tradText = __('All', WYSIJA);
				break;
			case 'allsent':
				$tradText = __('All Sent', WYSIJA);
				break;
			case 'inqueue':
				$tradText = __('In Queue', WYSIJA);
				break;
			case 'notsent':
				$tradText = __('Failed Send', WYSIJA);
				break;
			case 'sent':
				$tradText = __('Unopened', WYSIJA);
				break;
			case 'opened':
				$tradText = __('Opened', WYSIJA);
				break;
			case 'bounced':
				$tradText = __('Bounced', WYSIJA);
				break;
			case 'clicked':
				$tradText = __('Clicked', WYSIJA);
				break;
			case 'unsubscribe':
				$tradText = __('Unsubscribe', WYSIJA);
				break;
			default:
				$tradText = 'status : ' . $status;
		}
		return $tradText;
	}

	function filterDDPVIEW($data) {
		?>
		<ul class="subsubsub">
			<?php
			$total = count($data['counts']);
			$i = 1;
			foreach ($data['counts'] as $countType => $count) {
				if (!$count || $countType == 'all') {
					$i++;
					continue;
				}
				$tradText = $this->getTransStatusEmail($countType);
				$classcurrent = '';
				if ((isset($_REQUEST['link_filter']) && $_REQUEST['link_filter'] == $countType) || ($countType == 'allsent' && !isset($_REQUEST['link_filter'])))
					$classcurrent = 'class="current"';

				echo '<li><a ' . $classcurrent . ' href="admin.php?page=wysija_campaigns&action=viewstats&id=' . $_REQUEST['id'] . '&link_filter=' . $countType . '">' . $tradText . ' <span class="count">(' . $count . ')</span></a>';

				if ($total != $i)
					echo ' | ';
				echo '</li>';
				$i++;
			}
			?>
		</ul>

					<?php $this->searchBox(); ?>

		<div class="tablenav">

			<div class="alignleft actions">
				<select name="action2" class="global-action" id="viewstats_ddp">
					<option value="" data-sort="0"><?php _e('With this segment', WYSIJA); ?></option>
					<?php
					if (isset($_REQUEST['link_filter']) && $_REQUEST['link_filter'] == 'notsent') {

                                            ?>
                                            <option value="removequeue" data-nonce="<?php echo $this->secure(array('action' => "removequeue", 'id' => $_REQUEST['id']), true) ?>"><?php _e('Remove from the queue', WYSIJA); ?></option>
                                            <?php
					}
					?>
					<option value="createnewlist" data-nonce="<?php echo $this->secure(array('action' => "createnewlist", 'id' => $_REQUEST['id']), true) ?>"><?php _e('Create a new list', WYSIJA); ?></option>
					<option value="unsubscribeall" data-nonce="<?php echo $this->secure(array('action' => "unsubscribeall", 'id' => $_REQUEST['id']), true) ?>"><?php _e('Unsubscribe from all lists', WYSIJA); ?></option>
                                        <?php
                                        foreach ($data['lists'] as $listK => $list) {
                                            if ($list['is_enabled']){
                                                echo '<option value="actionvar_unsubscribelist-listid_' . $list['list_id'] . '" data-nonce="'. $this->secure(array('action' => "actionvar_unsubscribelist-listid_" . $list['list_id'], 'id' => $_REQUEST['id']), true).'">' . sprintf(__('Unsubscribe from list: %1$s', WYSIJA), $list['name']) . ' (' . $list['users'] . ')' . '</option>';
                                            }
                                        }
                                        ?>
					<option value="export" data-nonce="<?php echo $this->secure(array('action' => "export", 'id' => $_REQUEST['id']), true) ?>"><?php _e('Export to CSV', WYSIJA); ?></option>

				</select>
                                <?php $this->secure(array('action' => "bulkoptions", 'id' => $_REQUEST['id'])); ?>
				<input type="submit" class="bulksubmitcamp button-secondary action" name="doaction" value="<?php echo esc_attr(__('Apply', WYSIJA)); ?>">
			</div>
		<?php $this->pagination(); ?>

			<div class="clear"></div>
		</div>
					<?php
				}

	/*
	 * main view
	 */

	function listing($data, $simple = false) {
	if (empty($data['campaigns']))
		return;
	?>
		<div class="list">
			<table cellspacing="0" class="widefat fixed">
				<thead>
					<?php
					$openedsorting = $statussorting = $namesorting = $datesorting = $datesorting2 = " sortable desc";
					$hiddenOrder = "";
					if (isset($_REQUEST["orderby"])) {
						switch ($_REQUEST["orderby"]) {
							case "name":
								$namesorting = " sorted " . $_REQUEST["ordert"];
								break;
							case "modified_at":
								$datesorting = " sorted " . $_REQUEST["ordert"];
								break;
							case "sent_at":
								$datesorting2 = " sorted " . $_REQUEST["ordert"];
								break;
							case "status":
								$statussorting = " sorted " . $_REQUEST["ordert"];
								break;
							case "number_opened":
								$openedsorting = " sorted " . $_REQUEST["ordert"];
								break;
						}
						$hiddenOrder = '<input type="hidden" name="orderby" id="wysija-orderby" value="' . esc_attr($_REQUEST["orderby"]) . '"/>';
						$hiddenOrder.='<input type="hidden" name="ordert" id="wysija-ordert" value="' . esc_attr($_REQUEST["ordert"]) . '"/>';
					}
					$header = '<tr class="thead">
							<th scope="col" id="campaign-id" class="manage-column column-campaign-id check-column"><input type="checkbox" /></th>
							<th class="manage-column column-name' . $namesorting . '" id="name" scope="col" style="width:25%"><a href="#" class="orderlink" ><span>' . __('Name', WYSIJA) . '</span><span class="sorting-indicator"></span></a></th>';
					/* $header.='<th class="manage-column column-fname'.$fnamesorting.'" id="firstname" scope="col" style="width:80px;">'.__('First name',WYSIJA).'</th>
					  <th class="manage-column column-lname'.$lnamesorting.'" id="lastname" scope="col" style="width:80px;">'.__('Last name',WYSIJA).'</th>'; */
					$header.='<th class="manage-column column-status' . $statussorting . '" id="status" scope="col" style="width:15%;"><a href="#" class="orderlink" ><span>' . __('Status', WYSIJA) . '</span><span class="sorting-indicator"></span></a></th>';
					$header.='<th class="manage-column column-list-names" id="list-list" scope="col">' . __('Lists', WYSIJA) . '</th>';
					$header.='<th class="manage-column column-opened' . $openedsorting . '" id="number_opened" scope="col" style="width:15%;"><a href="#" class="orderlink" ><span>' . __('Open, clicks, unsubscribed', WYSIJA) . '</span><span class="sorting-indicator"></span></a></th>';


					/* $header.='<th class="manage-column column-emails" id="emails-list" scope="col">'.__('Emails',WYSIJA).'</th>
					  <th class="manage-column column-opened" id="opened-list" scope="col">'.__('Opened',WYSIJA).'</th>
					  <th class="manage-column column-clic" id="clic-list" scope="col">'.__('Clicked',WYSIJA).'</th>'; */
					$header.='<th class="manage-column column-date' . $datesorting . '" id="modified_at" scope="col"><a href="#" class="orderlink" ><span>' . __('Modified On', WYSIJA) . '</span><span class="sorting-indicator"></span></a></th>';
					$header.='<th class="manage-column column-date' . $datesorting2 . '" id="sent_at" scope="col"><a href="#" class="orderlink" ><span>' . __('Sent On', WYSIJA) . '</span><span class="sorting-indicator"></span></a></th>
						</tr>';
					echo $header;
					?>
				</thead>
				<tfoot>
					   <?php
					   echo $header;
					   ?>
				</tfoot>

				<tbody class="list:<?php echo $this->model->table_name . ' ' . $this->model->table_name . '-list" id="wysija-' . $this->model->table_name . '"' ?>>

					   <?php
					   $listingRows = '';
					   $alt = true;

					   $statuses = array('-1' => __('Sent to %1$s out of %2$s', WYSIJA), '0' => __('Draft', WYSIJA), '1' => __('%1$s out of %2$s sent.', WYSIJA), '3' => __('%1$s out of %2$s sent.', WYSIJA), '2' => __('Sent to %1$s out of %2$s', WYSIJA), '99' => __('%1$s out of %2$s sent.', WYSIJA));

					   foreach ($data['campaigns'] as $row) {
						   $classRow = $messageListEdit = '';
						   //check if lists have been removed in case of scheduled newsletter or  auto post notif
						   if (empty($row['name'])) {
							   $row['name'] = $row['campaign_name'];
						   }

						   if (isset($row['classRow'])) {
							   $classRow.=$row['classRow'];
						   }
						   if (isset($row['msgListEdit']))
							   $messageListEdit = $row['msgListEdit'];


						   if ($alt)
							   $classRow.='alternate';
						   $editStep = 'editTemplate';
						   if ($row["type"] == 2) {
							   $classRow.=" autonl";
							   $editStep = 'edit';
						   }

						   if ((int) $row['status'] == 4 && isset($row['params']['schedule']['isscheduled'])) {
							   $classRow.=' scheduled';
						   }
						   if (in_array($row['status'], array(1, 3, 99)))
							   $classRow.=' sending';
						   if ($row['status'] == 2)
							   $classRow.=' sent';


						   //$row["params"]=unserialize(base64_decode($row["params"]));
						   ?>
						   <tr class="<?php echo $classRow ?>" >

					<th scope="col" class="check-column" >
						<input type="checkbox" name="wysija[campaign][campaign_id][]" id="campaign_id_<?php echo $row["campaign_id"] ?>" value="<?php echo esc_attr($row["campaign_id"]) ?>" class="checkboxselec" />
					</th>
					<td class="name column-name">
						<strong>
								<?php
;
								if (in_array($row['status'], array(0, 4, -1))) {
									$durationsent = $statusshared = '';
									?><a href="admin.php?page=wysija_campaigns&id=<?php echo $row['email_id'] ?>&action=edit" class="row-title"><?php echo $row['name']; ?></a> - <span class="post-state"><?php
									if (isset($row['params']['schedule']['isscheduled']) && $row['status'] == 4) {
										$helper_toolbox = WYSIJA::get('toolbox', 'helper');

										//no recording just conversion
										$scheduletimenoffset = strtotime($row['params']['schedule']['day'] . ' ' . $row['params']['schedule']['time']);
										$timeleft = $helper_toolbox->localtime_to_servertime($scheduletimenoffset) - time();
										if ($timeleft <= 0) {
											$autoNL = WYSIJA::get('autonews', 'helper');
											$autoNL->checkScheduled();
										} else {

											$scheduled_on = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $scheduletimenoffset);
											if ($timeleft <= (60 * 60 * 24)) { // 1 day
												$timeleft = $helper_toolbox->duration_string($timeleft, true, 4);
												$durationsent = '<span title="' . $scheduled_on . '">' . sprintf(__('Scheduled to be sent in %1$s', WYSIJA), $timeleft) . '</span>';
											} else {

												$durationsent = sprintf(__('Scheduled to be sent on %1$s', WYSIJA), $scheduled_on);
											}

										}

										$statusshared = $durationsent;
										echo __('Scheduled', WYSIJA);
									} else {
										if ($row['type'] == 2)
											if ($row['status'] == -1)
												echo __('Paused', WYSIJA);
											else
												echo __('Draft', WYSIJA);
										else {
											if ((int) $row['status'] == -1)
												$resulttext = sprintf($statuses[(int) $row['status']], $data['sent'][$row["email_id"]]['to'], $data['sent'][$row["email_id"]]['total']);
											else
												$resulttext = $statuses[(int) $row['status']];

											echo $resulttext;
										}
									}
									?></span>

								<?php
							}else {

								if (isset($data['sent'][$row['email_id']]['to']) && $data['sent'][$row['email_id']]['to'] > 0) {
									?><a href="admin.php?page=wysija_campaigns&id=<?php echo $row["email_id"] ?>&action=viewstats" class="row-title"><?php echo $row['name']; ?></a><?php
								} else {
									if ($row["type"] == 2) {
										?>
										<a href="admin.php?page=wysija_campaigns&id=<?php echo $row["email_id"] ?>&action=pause&_wpnonce=<?php echo $this->secure(array('action' => 'pause' , 'id' => $row["email_id"]), true); ?>" class="row-title pause-edit">
										<?php echo $row['name']; ?>
										</a><?php
									} else {
										echo $row['name'];
									}
								}
							}
							?></strong>
						<div class="row-actions">
							<?php
							$emailH = WYSIJA::get('email', 'helper');
							$fullurl = $emailH->getVIB($row);
							?><span class="viewnl">
								<a href="<?php echo $fullurl ?>" target="_blank" class="viewnews" title="<?php _e('Preview in new tab', WYSIJA) ?>"><?php _e('Preview', WYSIJA) ?></a>
							</span><?php
							$deleteAction = '';
							$dupid = $deleteId = $row['campaign_id'];
							if (isset($row['params']['autonl']['parent']) || ((int) $row['type'] === 2 && $row['params']['autonl']['event'] == 'new-articles')) {
								$deleteAction = 'Email';
								$deleteId = $row['email_id'];
							}

							if ($row['status'] == 0 || $row['status'] == 4) {
								?>
								| <span class="edit">
									<a href="admin.php?page=wysija_campaigns&id=<?php echo $row['email_id'] ?>&action=<?php echo $editStep ?>" class="submitedit"><?php _e('Edit', WYSIJA) ?></a>
								</span>
				<?php
				if (isset($data['sent'][$row["email_id"]]['to']) && $data['sent'][$row["email_id"]]['to'] > 0) {
					?>

									| <span class="viewstats">
										<a href="admin.php?page=wysija_campaigns&id=<?php echo $row["email_id"] ?>&action=viewstats" class="stats"><?php _e('Stats', WYSIJA) ?></a>
									</span>

									<?php
								}
								?>
								| <span class="duplicate">
									<a href="admin.php?page=wysija_campaigns&id=<?php echo $dupid ?>&email_id=<?php echo $row['email_id'] ?>&action=duplicate&_wpnonce=<?php echo $this->secure(array("action" => "duplicate", "id" => $dupid), true); ?>" class="submitedit"><?php _e('Duplicate', WYSIJA) ?></a>
								</span>
								| <span class="delete">
									<a href="<?php echo $data['base_url'] ?>&id=<?php echo $deleteId ?>&action=delete<?php echo $deleteAction ?>&_wpnonce=<?php echo $this->secure(array('action' => 'delete' . $deleteAction, 'id' => $deleteId), true); ?>" class="submitdelete"><?php _e('Delete', WYSIJA) ?></a>
								</span>
								<?php
							} else {

								if ($row["status"] == -1) {
									?>
									| <span class="edit"><a href="admin.php?page=wysija_campaigns&id=<?php echo $row['email_id'] ?>&action=<?php echo $editStep ?>" class="submitedit"><?php _e('Edit', WYSIJA) ?></a></span>
									<?php
									if (isset($data['sent'][$row["email_id"]]['to']) && $data['sent'][$row["email_id"]]['to'] > 0) {
										?>

										| <span class="viewstats">
											<a href="admin.php?page=wysija_campaigns&id=<?php echo $row["email_id"] ?>&action=viewstats" class="stats"><?php _e('Stats', WYSIJA) ?></a>
										</span>

										<?php }
									?>
									| <span class="duplicate">
										<a href="admin.php?page=wysija_campaigns&id=<?php echo $dupid ?>&email_id=<?php echo $row['email_id'] ?>&action=duplicate&_wpnonce=<?php echo $this->secure(array("action" => "duplicate", "id" => $dupid), true); ?>" class="submitedit"><?php _e('Duplicate', WYSIJA) ?></a>
									</span>
									| <span class="delete">
										<a href="<?php echo $data['base_url'] ?>&id=<?php echo $deleteId ?>&action=delete<?php echo $deleteAction ?>&_wpnonce=<?php echo $this->secure(array("action" => "delete" . $deleteAction, "id" => $deleteId), true); ?>" class="submitdelete"><?php _e('Delete', WYSIJA) ?></a>
									</span>
					<?php
				} else {
					if ($row['type'] == 2) {
						?>
										| <span class="edit">
											<a href="admin.php?page=wysija_campaigns&id=<?php echo $row["email_id"] ?>&action=pause&_wpnonce=<?php echo $this->secure(array('action' => 'pause' , 'id' => $row["email_id"]), true); ?>" class="submitedit pause-edit"><?php _e('Edit', WYSIJA) ?></a>
										</span>
						<?php
					}
					if (isset($data['sent'][$row["email_id"]]['to']) && $data['sent'][$row["email_id"]]['to'] > 0) {
						?>

										| <span class="viewstats">
											<a href="admin.php?page=wysija_campaigns&id=<?php echo $row["email_id"] ?>&action=viewstats" class="stats"><?php _e('Stats', WYSIJA) ?></a>
										</span>

						<?php
					}
					?>
									| <span class="duplicate">
										<a href="admin.php?page=wysija_campaigns&id=<?php echo $dupid ?>&email_id=<?php echo $row["email_id"] ?>&action=duplicate&_wpnonce=<?php echo $this->secure(array("action" => "duplicate", "id" => $dupid), true); ?>" class="submitedit"><?php _e('Duplicate', WYSIJA) ?></a>
									</span>
									| <span class="delete">
										<a href="<?php echo $data['base_url'] ?>&id=<?php echo $deleteId ?>&action=delete<?php echo $deleteAction ?>&_wpnonce=<?php echo $this->secure(array("action" => "delete" . $deleteAction, "id" => $deleteId), true); ?>" class="submitdelete"><?php _e('Delete', WYSIJA) ?></a>
									</span>
								<?php
							}
						}
						?>
						</div>
					</td>
					<td><?php
						switch ((int) $row['status']) {
							case 99:
							case 3:
							case 2:
							case 1:
								// automatic newsletters
								if ($row['type'] == 2) {
									$pause = '';
									// non immediate post notifications
									if (isset($row['params']['autonl']['event']) && $row['params']['autonl']['event'] == 'new-articles' && $row['params']['autonl']['when-article'] != 'immediate') {


										//if the next send value of the post notification newsletter is not set or
										if (!isset($row['params']['autonl']['nextSend'])) {
											$nextSend = false;
											//find a way to update the missing next send without triggerring a give_birth
										} else {
											$nextSend = $row['params']['autonl']['nextSend'];
										}

										$helper_toolbox = WYSIJA::get('toolbox', 'helper');
										$time = $helper_toolbox->localtime($row['params']['autonl']['time'], true);
										$dayname = $helper_toolbox->getday($row['params']['autonl']['dayname']);
										$daynumber = $helper_toolbox->getdaynumber($row['params']['autonl']['daynumber']);
										$weeknumber = $helper_toolbox->getweeksnumber($row['params']['autonl']['dayevery']);
										$durationsent = '';
										if ($nextSend) {
											$timeleft = $helper_toolbox->localtime_to_servertime($nextSend) - time();

											$scheduled_on = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $nextSend);
											if ($timeleft < (3600 * 24)) {
												$timeleft = $helper_toolbox->duration_string($timeleft, true, 2);
												$durationsent = '<span title="' . $scheduled_on . '">' . sprintf(__('Next send out in %1$s', WYSIJA), $timeleft) . '</span>';
											} else {
												$timeleft = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $nextSend);
												$durationsent = sprintf(__('Next send out on %1$s', WYSIJA), $timeleft);
											}
										}


										switch ($row['params']['autonl']['when-article']) {
											case 'daily':
												$statussent = sprintf(__('Sent daily at %1$s.', WYSIJA), $time);
												break;
											case 'weekly':
												$statussent = sprintf(__('Sent weekly on %1$s at %2$s', WYSIJA), $dayname, $time);
												break;
											case 'monthly':
												$statussent = sprintf(__('Sent monthly on the %1$s at %2$s', WYSIJA), $daynumber, $time);
												break;
											case 'monthlyevery':
												$statussent = sprintf(__('Sent monthly on the %1$s %2$s at %3$s', WYSIJA), $weeknumber, $dayname, $time);
												break;
										}

										echo '<p>' . $statussent . '</p>';

										echo '<p>' . $durationsent . ' (' . __('if there\'s new content', WYSIJA) . ')</p>';
										if (isset($row['params']['autonl']['late_send']) && WYSIJA_DBG > 1) {
											$last_send = $late_send = 0;
											if(!empty($row['params']['autonl']['late_send'])) $late_send = $row['params']['autonl']['late_send'];
											$late_send = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $late_send);

											if(!empty($row['params']['autonl']['lastSend'])) $last_send = $row['params']['autonl']['lastSend'];
											$last_send = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_send);
											echo '<p>' . sprintf('The last scheduled send due on %1$s was late and postponed.', $late_send) . '</p>';
											echo '<p>' . sprintf('The last executed send was on : %1$s ', $last_send) . '</p>';
										}

										echo $pause;
									} else {
										// autoresponders and immediate post notifications
										$delay = '';
										if (!isset($row['params']['autonl']['numberafter']))
											$numberafter = 0;
										else {
											$numberafter = (int) $row['params']['autonl']['numberafter'];
											$delay = $numberafter . ' ' . $data['autonl']['fields']['numberofwhat']['valuesunit'][$row['params']['autonl']['numberofwhat']];
										}


										$statustext = $this->getSendingStatus($row, $data, $numberafter, $delay);
										echo $statustext . $pause . $this->dataBatches($data, $row, $pause, $statuses, true);
									}
								} else {
									// standard emails
									$pause = ' <a href="admin.php?page=wysija_campaigns&id=' . $row['email_id'] . '&action=pause&_wpnonce='.$this->secure(array('action' => 'pause' , 'id' => $row["email_id"]), true).'" class="submitedit button">' . __("Pause", WYSIJA) . '</a>';
									echo $this->dataBatches($data, $row, $pause, $statuses);
								}

								break;
							case -1:
								if ($row['type'] == 2) {
									$resumelink = __('Not active.', WYSIJA) . ' | <a href="admin.php?page=wysija_campaigns&id=' . $row['email_id'] . '&action=resume&_wpnonce='.$this->secure(array('action' => 'resume' , 'id' => $row["email_id"]), true).'" class="submitedit">' . __('Activate', WYSIJA) . '</a>';
									echo $resumelink;
								} else {
									$resumelink = '<a href="admin.php?page=wysija_campaigns&id=' . $row['email_id'] . '&action=resume&_wpnonce='.$this->secure(array('action' => 'resume' , 'id' => $row["email_id"]), true).'" class="submitedit">' . __('Resume', WYSIJA) . '</a>';
									echo sprintf($statuses[$row['status']], $data['sent'][$row['email_id']]['to'], $data['sent'][$row['email_id']]['total']);
									echo ' | ' . $resumelink;
								}

								break;
							case 4:
							case 0:
								if ($statusshared)
									echo $statusshared;
								else {
									if ($row["type"] == 2)
										echo __('Not active.', WYSIJA);
									else
										echo __('Not sent yet.', WYSIJA); //$statuses[$row["status"]];
								}
								break;
						}
						?></td>
					<td><?php
						if (($row['type'] == 2 && isset($row['params']['autonl']['event']) && $row['params']['autonl']['event'] == 'subs-2-nl')) {
							$row['lists'] = $data['lists'][$row['params']['autonl']['subscribetolist']]['name'];
						}

						if (isset($row['lists']))
							echo $row['lists'];
						else
							echo $messageListEdit;
						?></td>

					<td>
					<?php if (isset($row['stats'])) echo $row['stats']; elseif ($row['status'] != 0) { ?>
							<a href="admin.php?page=wysija_campaigns&id=<?php echo $row["email_id"] ?>&action=viewstats" class="stats" title="<?php echo $row['number_opened'] . ' - ' . $row['number_clicked'] . ' - ' . $row['number_unsub']; ?>">
						<?php echo $row['rate_opened'] . '% - ' . $row['rate_clicked'] . '% - ' . $row['rate_unsub'] . '%'; ?>
							</a>
					<?php } ?>
					</td>
					<td title='<?php echo $this->fieldListHTML_created_at_time($row['modified_at'], get_option('date_format') . ' ' . get_option('time_format')); ?>'><?php echo $this->fieldListHTML_created_at($row['modified_at']); ?></td>
					<td title='<?php echo $this->fieldListHTML_created_at_time($row['sent_at'], get_option('date_format') . ' ' . get_option('time_format')); ?>'><?php echo $this->fieldListHTML_created_at($row['sent_at']); ?>
			<?php
			if (WYSIJA_DBG > 1) {
				echo '<p>' . $row['sent_at'] . '</p>';
			}
			?></td>
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

	function getSendingStatus($row, $data, $numberafter, $delay) {
		$statustext = false;
		if (isset($row['msgSendSuspended'])) {
			$statustext = $row['msgSendSuspended'];
		} else {
			switch ($row["params"]['autonl']['event']) {
				case 'new-articles':

					$statustext = __('Send immediately.', WYSIJA);
					break;
				case 'subs-2-nl':
					$list = '';
					if (isset($data['autonl']['fields']['subscribetolist']['values'][$row["params"]['autonl']['subscribetolist']]))
						$list = '<em>"' . $data['autonl']['fields']['subscribetolist']['values'][$row["params"]['autonl']['subscribetolist']] . '"</em>';

					if ($numberafter < 1 || $row["params"]['autonl']['numberofwhat'] == 'immediate')
						$statustext = sprintf(__('Sending immediately after someone subscribes to the mailing list %1$s', WYSIJA), $list);
					else
						$statustext = sprintf(__('Sent %2$s after someone subscribes to the mailing list %1$s', WYSIJA), $list, '<strong>' . $delay . '</strong>');
					break;
				// Auto newsletter when new user is added to WordPress.
				case 'new-user':
					// Make the "any" word translatable.
					$roles = $row["params"]['autonl']['roles'];
					if ($roles === 'any') {
						$roles = __('any role', WYSIJA);
					}
					if ($numberafter < 1 || $row["params"]['autonl']['numberofwhat'] == 'immediate') {
						// Send immediately on subscription.
						$statustext = sprintf(__('Sent immediately after a new user is added to your site as %1$s.', WYSIJA), '<b>' . $roles . '</b>');
					} else {
						// Send with delay.
						$statustext = sprintf(__('Sent %2$s after a new user is added to your site as %1$s.', WYSIJA), '<b>' . $roles . '</b>', '<strong>' . $delay . '</strong>');
					}
					break;
				default:
					//try to see if the plugin returns something
					$functioname = str_replace('-', '_', $row["params"]['autonl']['event']) . '_sendingStatus';
					if (function_exists($functioname))
						$statustext = call_user_func($functioname, $row["params"]['autonl'], $numberafter, $delay);
					if (!$statustext)
						$statustext = __('Sending per event', WYSIJA);
			}
		}

		return $statustext;
	}

	function sending_process() {
		$config = WYSIJA::get("config", "model");
		if ((int) $config->getValue('total_subscribers') < 2000)
			return true;
		return false;
	}

	function dataBatches($data, $row, $pause, $statuses, $pending = false) {
		$sentto = $senttotal = $sentleft = 0;
		$return = '<div>';
		if (isset($data['sent'][$row["email_id"]]['to']))
			$sentto = $data['sent'][$row["email_id"]]['to'];
		if (isset($data['sent'][$row["email_id"]]['total']))
			$senttotal = $data['sent'][$row["email_id"]]['total'];
		if (isset($data['sent'][$row["email_id"]]['left']))
			$sentleft = $data['sent'][$row["email_id"]]['left'];

		$statusdata = $senttohowmany = '';
		if ($row['type'] != 2)
			$statusdata = sprintf($statuses[$row["status"]], $sentto, $senttotal);
		elseif ($row['params']['autonl']['event'] != 'new-articles')
			$return.=sprintf(__('Sent to %1$s subscribers.', WYSIJA), $sentto) . ' ';

		if ($sentleft > 0) {

			$config = WYSIJA::get('config', 'model');
			add_filter('wysija_send_ok', array($this, 'sending_process'));
			$letsgo = apply_filters('wysija_send_ok', false);

			if ($letsgo) {

				$helper_toolbox = WYSIJA::get('toolbox', 'helper');

				// Standard newsletter. Let's show the progress bar.
				if ($row['type'] != 2) {

					$percent_status = round(($sentto * 100) / $senttotal);

					$return .= '<p><strong>';

					if($data['sent'][$row['email_id']]['remaining_time'] < 1){
						$return .= __('The last batch of emails should start sending automatically in the next few minutes.',WYSIJA);
					}else{
						$return .= sprintf(__('Time remaining: %1$s', WYSIJA), $helper_toolbox->duration_string($data['sent'][$row['email_id']]['remaining_time'], true, 4, 4));
					}

					$return .= '</strong></p>';
					$return .= '<div class="progress_bar">';
					$return .= '<div class="bar">';
					$return .= '<div class="progress" style="width: ' . $percent_status . '%">';
					$return .= '</div>';
					$return .= '<div class="percent">';
					$return .= $sentto . ' / ' . $senttotal;
					$return .= '</div>';
					$return .= '</div>';
					$return .= $pause;
					$return .= '</div>';
					$return .= '<div class="info-stats">';
				}

				$is_multisite = is_multisite();

				//$is_multisite=true;//PROD comment that line
				if ($is_multisite && $config->getValue('sending_method') == 'network') {
					$sending_emails_number = (int) $config->getValue('ms_sending_emails_number');
				} else {
					$sending_emails_number = (int) $config->getValue('sending_emails_number');
				}

				if ($sentleft > $sending_emails_number)
					$nextBatchnumber = $sending_emails_number;
				else
					$nextBatchnumber = (int) $sentleft;

				//Next batch of xx emails will be sent in xx minutes. Don't wait & send right now.
				if ($pending) {
					$return.= '<span style="color:#555"><a href="admin.php?page=wysija_campaigns&action=manual_send&emailid=' . $row['email_id'] . '&pending=1&_wpnonce='.$this->secure(array('action' => 'manual_send'), true).'" title="view pending" class="action-send-test-editor" >' . sprintf(__(' %1$s email(s) scheduled.', WYSIJA) . '</a>', $sentleft);
					$return.= '</span>';
				} else {
					if ($data['sent'][$row['email_id']]['running_for']) {
						$return.= sprintf(__('Latest batch was sent %1$s ago.', WYSIJA), $data['sent'][$row['email_id']]['running_for']);
					} else {
						$time_remaining = trim($helper_toolbox->duration_string($data['sent'][$row['email_id']]['next_batch'], true, 4));
						$return.= '<a href="admin.php?page=wysija_campaigns&action=manual_send&emailid=' . $row['email_id'] . '&_wpnonce='.$this->secure(array('action' => 'manual_send'), true).'" class="action-send-test-editor" >' . __('Don\'t wait & send right now.', WYSIJA) . '</a>';
					}
				}
			} else {
				$return.= $statusdata;
				$helper_licence = WYSIJA::get('licence', 'helper');
				$url_checkout = $helper_licence->get_url_checkout('resume_send');

				$link = str_replace(
						array('[link]', '[/link]'), array('<a title="' . __('Get Premium now', WYSIJA) . '" target="_blank" href="' . $url_checkout . '">', '</a>'), __('To resume send [link]Go premium now![/link]', WYSIJA));
				$return.= '<p>' . $link . '</p>';
			}
		}
		else
			$return.= $statusdata;
		$return.='</div>';
		return $return;
	}

	function linkStats($result, $data) {
		$result = '<ol>';
		$countloop = 0;
		$helper_licence = WYSIJA::get('licence', 'helper');
		$url_checkout = $helper_licence->get_url_checkout('count_click_stats');
		foreach ($data['clicks'] as $click) {
			if ($countloop == 0)
				$label = str_replace(array('[link]', '[/link]'), array('<a class="premium-tab" target="_blank" href="' . $url_checkout . '">', '</a>'), __('see links with a [link]Premium licence[/link].', WYSIJA));
			else
				$label = '...';

			$css_class = 'stats-url-link';
			if (!empty($_REQUEST['url_id']) && $_REQUEST['url_id'] == $click['url_id'])
				$css_class .= ' select';

			$link = 'admin.php?page=wysija_campaigns&action=viewstats&id=' . $_REQUEST['id'] . '&url_id=' . $click['url_id'];
			$result.='<li><a href="' . $link . '" class="' . $css_class . '">' . $click['name'] . '</a> : ' . $label . '</li>';
			$countloop++;
		}
		$result.='</ol>';
		return $result;
	}

	/*
	 * main view
	 */

	function viewstats($data) {
		$this->icon = 'icon-stats';
		$this->search['title'] = __('Search recipients', WYSIJA);
		?>
					<?php if (!empty($data['hooks']['hook_newsletter_top'])) { ?>
			<div id="hook_newsletter_top" class="hook clear"><?php echo $data['hooks']['hook_newsletter_top']; ?></div>
					<?php } ?>
					<?php
					echo '<div style="clear:both;"></div>';
					echo '<form method="post" action="" id="posts-filter">';
					$this->filtersLink($data);
					$this->filterDDPVIEW($data);
					?>
		<div class="list">
			<table cellspacing="0" class="widefat fixed">
				<thead>
					<?php
					$umstatussorting = $statussorting = $fnamesorting = $lnamesorting = $usrsorting = $datesorting = " sortable desc";
					$hiddenOrder = "";
					if (isset($_REQUEST["orderby"])) {
						switch ($_REQUEST["orderby"]) {
							case "email":
								$usrsorting = " sorted " . $_REQUEST["ordert"];
								break;
							case "opened_at"://default stat view
							case "created_at"://queue stat view
							case "clicked_at"://filter by url view
								$datesorting = " sorted " . $_REQUEST["ordert"];
								break;
							case "ustatus":
								$statussorting = " sorted " . $_REQUEST["ordert"];
								break;
							case "umstatus":
								$umstatussorting = " sorted " . $_REQUEST["ordert"];
								break;
						}
						$hiddenOrder = '<input type="hidden" name="orderby" id="wysija-orderby" value="' . esc_attr($_REQUEST["orderby"]) . '"/>';
						$hiddenOrder.='<input type="hidden" name="ordert" id="wysija-ordert" value="' . esc_attr($_REQUEST["ordert"]) . '"/>';
					}
					$header = '<tr class="thead">
							<th class="manage-column column-username' . $usrsorting . '" id="email" scope="col" style="width:140px;"><a href="#" class="orderlink" ><span>' . __('Email', WYSIJA) . '</span><span class="sorting-indicator"></span></a></th>';
					/* $header.='<th class="manage-column column-fname'.$fnamesorting.'" id="firstname" scope="col" style="width:80px;">'.__('First name',WYSIJA).'</th>
					  <th class="manage-column column-lname'.$lnamesorting.'" id="lastname" scope="col" style="width:80px;">'.__('Last name',WYSIJA).'</th>'; */
					$header.='<th class="manage-column column-umstatus' . $umstatussorting . '" id="umstatus" scope="col" style="width:80px;"><a href="#" class="orderlink" ><span>' . __('Email Status', WYSIJA) . '</span><span class="sorting-indicator"></span></a></th>';
					$header.='<th class="manage-column column-list-names" id="list-list" scope="col">' . __('Lists', WYSIJA) . '</th>';
					$header.='<th class="manage-column column-ustatus' . $statussorting . '" id="ustatus" scope="col" style="width:80px;"><a href="#" class="orderlink" ><span>' . __('Subscriber Status', WYSIJA) . '</span><span class="sorting-indicator"></span></a></th>';
					/* $header.='<th class="manage-column column-emails" id="emails-list" scope="col">'.__('Emails',WYSIJA).'</th>
					  <th class="manage-column column-opened" id="opened-list" scope="col">'.__('Opened',WYSIJA).'</th>
					  <th class="manage-column column-clic" id="clic-list" scope="col">'.__('Clicked',WYSIJA).'</th>'; */
					if (empty($data['tableQuery']))
						$data['tableQuery'] = '';
					switch ($data['tableQuery']) {
						case 'email_user_url':
							$header.='<th class="manage-column column-date' . $datesorting . '" id="clicked_at" scope="col"><a href="#" class="orderlink" ><span>' . __('Clicked on', WYSIJA) . '</span><span class="sorting-indicator"></span></a></th>';
							break;
						case 'email_user_stat':
							$label = __('Opened date', WYSIJA);
							if (!empty($_REQUEST['link_filter']) && $_REQUEST['link_filter'] == 'clicked')
								$label = __('Clicked on', WYSIJA);
							$header.='<th class="manage-column column-date' . $datesorting . '" id="opened_at" scope="col"><a href="#" class="orderlink" ><span>' . $label . '</span><span class="sorting-indicator"></span></a></th>';
							break;
						case 'queue':
						default:
							$header.='<th class="manage-column column-date' . $datesorting . '" id="created_at" scope="col"><a href="#" class="orderlink" ><span>' . __('Subscribed on', WYSIJA) . '</span><span class="sorting-indicator"></span></a></th>';
							break;
					}
					$header .= '</tr>';
					echo $header;
					?>
				</thead>
				<tfoot>
					   <?php
					   echo $header;
					   ?>
				</tfoot>

				<tbody class="list:<?php echo $this->model->table_name . ' ' . $this->model->table_name . '-list'; ?>" id="wysija-<?php echo $this->model->table_name; ?>">
					   <?php
					   $listingRows = '';
					   $alt = true;

					   $statuses = array("-1" => __("Unsubscribed", WYSIJA), "0" => __("Unconfirmed", WYSIJA), "1" => __("Subscribed", WYSIJA));
					   $config = WYSIJA::get("config", "model");
					   if (!$config->getValue("confirm_dbleoptin"))
						   $statuses["0"] = $statuses["1"];


					   $mstatuses = array('-2' => $this->getTransStatusEmail('notsent'), '-1' => $this->getTransStatusEmail('bounced'), '0' => $this->getTransStatusEmail('sent')
						   , '1' => $this->getTransStatusEmail('opened'), '2' => $this->getTransStatusEmail('clicked'), '3' => $this->getTransStatusEmail('unsubscribe'));
					   //dbg($data,false);
					   foreach ($data['subscribers'] as $row) {
						   $classRow = '';
						   if ($alt)
							   $classRow = ' class="alternate" ';

						   echo '<tr ' . $classRow . ' >';
						   echo '<td class="username column-username">';
						   echo get_avatar($row['email'], 32);
						   echo '<strong>' . $row['email'] . '</strong>';
						   echo '<p style="margin:0;">' . $row['lastname'] . ' ' . $row['firstname'] . '</p>';


						   echo '<div class="row-actions">
											<span class="edit">
												<a href="admin.php?page=wysija_subscribers&id=' . $row['user_id'] . '&action=edit" class="submitedit">' . __('View stats or edit', WYSIJA) . '</a>
											</span>
										</div>';

						   echo '</td>';
						   /* <td><?php echo $row["firstname"] ?></td>
							 <td><?php  echo $row["lastname"] ?></td> */
						   ?>
						   <td><?php echo $mstatuses[$row["umstatus"]]; ?></td>
						   <td><?php if (isset($row["lists"])) echo $row["lists"] ?></td>
						   <td><?php echo $statuses[$row["ustatus"]]; ?></td>
						   <?php /* <td><?php echo $row["emails"] ?></td>
							 <td><?php echo $row["opened"] ?></td>
							 <td><?php echo $row["clicked"] ?></td> */ ?>
						   <td>
						   <?php
						   if (empty($data['tableQuery']))
							   $data['tableQuery'] = '';
						   switch ($data['tableQuery']) {
							   case 'email_user_url':
								   echo $this->fieldListHTML_created_at_time($row["clicked_at"]);
								   break;
							   case 'email_user_stat':
								   echo $this->fieldListHTML_created_at_time($row["opened_at"]);
								   break;
							   case 'queue':
							   default:
								   if (isset($row['created_at']))
									   echo $this->fieldListHTML_created_at_time($row['created_at']);
								   break;
						   }
						   ?>
						   </td>
							   <?php
							   echo '</tr>';
							   $alt = !$alt;
						   }
						   ?>
					   </tbody>
					   </table>
					   </div>

						   <?php
						   echo $hiddenOrder;
						   $this->limitPerPage();
						   echo '</form>';
						   ?>
						   <?php if (!empty($data['hooks']['hook_newsletter_bottom'])) { ?>
						   <div id="hook_newsletter_bottom" class="hook">
							   <?php echo $data['hooks']['hook_newsletter_bottom']; ?>
						   </div>
						   <?php
						   }
					   }

					   /* when creating a newsletter or when editing as a draft */

					   function add($data = false) {

						   $this->data = $data;
						   $step = array();

						   $step['type'] = array(
							   'type' => 'type_nl',
							   'class' => 'validate[required]',
							   'label' => __('What type of newsletter is this?', WYSIJA),
							   'labeloff' => 1,
							   'desc' => '');

						   $step['params'] = array(
							   'type' => 'frequencies',
							   'label' => __('Automatically sent...', WYSIJA), 'class' => 'validate[required]',
							   'desc' => '',
							   'labeloff' => 1,
							   'rowclass' => 'automatic-nl');


						   $step['subject'] = array(
							   'type' => 'subject',
							   'label' => __('Subject line', WYSIJA),
							   'class' => 'validate[required]',
							   'desc' => __("This is the subject of the email. Be creative since it's the first thing your subscribers will see.", WYSIJA));

						   if ($this->data['lists']) {
							   $step['lists'] = array(
								   'type' => 'lists',
								   'class' => 'validate[minCheckbox[1]] checkbox',
								   'rowclass' => 'listcheckboxes',
								   'label' => __('Lists', WYSIJA),
								   'labeloff' => 1);
						   }





						   if (!isset($msg['browsermsg'])) {
							   ?>
					<div id="browsernotsupported" class="updated" style="display:none;">
					<?php
					echo str_replace(
							array("[/linkchrome]", "[/linkff]", "[/linkie]", "[/linksafari]", "[/link_ignore]",
						"[linkchrome]", "[linkff]", "[linkie]", "[linksafari]", "[link_ignore]"), array("</a>", "</a>", "</a>", "</a>", "</a>",
						'<a href="http://www.google.com/chrome/" target="_blank">', '<a href="http://www.getfirefox.com" target="_blank">', '<a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home" target="_blank">', '<a href="http://www.apple.com/safari/download/" target="_blank">', '<a class="linkignore browsermsg" href="javascript:;">'), __("Yikes! Your browser might not be supported. Get the latest [linkchrome]Chrome[/linkchrome], [linkff]Firefox[/linkff], [linkie]Internet Explorer[/linkie] or [linksafari]Safari[/linksafari]. It seems to work?[link_ignore]Dismiss[/link_ignore].", WYSIJA));
					?>
					</div>
					<?php
				}
				?>
				<form name="step1" method="post" id="campaignstep3" action="" class="form-valid">

					<table class="form-table">
						<tbody>
				<?php
				//dbg($data);
				echo $this->buildMyForm($step, $data, "email", true);
				?>
						</tbody>
					</table>

		<?php
		$this->model->table_name = 'email';
		$this->model->pk = 'email_id';

		if (isset($data['email']['type']) && $data['email']['type'] == 2)
			$this->immediatewarning = '<input type="submit" id="save-reactivate" value="' . __("Save and reactivate", WYSIJA) . '" name="save-reactivate" class="button-primary wysija"/>' . $this->immediatewarning;

		$this->_savebuttonsecure($data, "savecamp", __("Next step", WYSIJA), $this->immediatewarning);
		?>

				</form>
						<?php
					}

	function editTemplate($data=false){
		wp_print_styles('editor-buttons');

		$wjEngine = WYSIJA::get('wj_engine', 'helper');

		if (isset($data['email']['wj_data'])) {
			$wjEngine->setData($data['email']['wj_data'], true);
		} else {
			$wjEngine->setData();
		}
		if (isset($data['email']['wj_styles'])) {
			$wjEngine->setStyles($data['email']['wj_styles'], true);
		} else {
			$wjEngine->setStyles();
		}

		?>
		<style type="text/css" id="wj_css">
		<?php echo $wjEngine->renderStyles(); ?>
		</style>

		<!-- BEGIN: Wysija Editor -->
		<?php echo $wjEngine->renderEditor(); ?>
		<!-- END: Wysija Editor -->

		<?php $defaultData = $wjEngine->getDefaultData(); ?>
		<div id="wysija_default_header" style="display:none;"><?php echo $wjEngine->renderEditorHeader($defaultData['header']); ?></div>
		<div id="wysija_default_footer" style="display:none;"><?php echo $wjEngine->renderEditorFooter($defaultData['footer']); ?></div>
		<div id="wysija_widgets_settings" style="display:none;">
			<div class="autopost"><?php
			// if it's a post notification that should be sent immediately after an article is published, constrain to only 1 autopost with 1 post_limit
			if ((int) $data['email']['type'] === 2 && $data['email']['params']['autonl']['event'] === 'new-articles' && $data['email']['params']['autonl']['when-article'] === 'immediate') {
				print 'single';
			} else {
				print 'multiple';
			}
			?></div>
			<div class="divider">
				<?php
				$params = $data['email']['params'];

				if(is_array($params) and isset($params['divider'])) {
					$divider = $params['divider'];
				} else {
					$divider = $defaultData['widgets']['divider'];
				}
				echo $wjEngine->renderEditorBlock(array_merge(array('type' => 'divider', 'no-block' => true), $divider));
				?>
			</div>
			<div class="image"><?php print WYSIJA_EDITOR_IMG . "transparent.png"; ?></div>
			<div class="theme"><?php if (isset($data['email']['params']['theme'])) {
				print $data['email']['params']['theme'];
			} else {
				print 'default';
			} ?>
			</div>
		</div>

		<!-- BEGIN: Wysija Toolbar -->
		<div id="wysija_toolbar">
			<ul class="wysija_toolbar_tabs">
				<li class="wjt-content">
					<a class="selected" href="javascript:;" rel="content"><?php _e("Content", WYSIJA) ?></a>
				</li>
				<li class="wjt-images"><a href="javascript:;" rel="images"><?php _e("Images", WYSIJA) ?></a></li>
					<?php if (WYSIJA::current_user_can('wysija_style_tab')): ?>
					<li class="wjt-styles"><a href="javascript:;" rel="styles"><?php _e("Styles", WYSIJA) ?></a></li>
					<?php endif; ?>
					<?php if (WYSIJA::current_user_can('wysija_theme_tab')): ?>
					<li class="last wjt-themes"><a href="javascript:;" rel="themes"><?php _e("Themes", WYSIJA) ?></a></li>
					<?php endif; ?>
			</ul>

			<!-- CONTENT BAR -->
			<ul class="wj_content" style="display:block;">
				<li class="notice"><?php _e('Drag the widgets below into your newsletter.', WYSIJA) ?></li>
				<li><a class="wysija_item" wysija_type="text"><?php _e('Titles & text', WYSIJA) ?></a></li>
					<?php if ((int) $data['email']['type'] === 1 || ((int) $data['email']['type'] === 2 && (empty($data['email']['params']['autonl']['event']) || $data['email']['params']['autonl']['event'] !== 'new-articles'))) { ?><li><a class="wysija_item" wysija_type="post"><?php _e('WordPress post', WYSIJA) ?></a></li><?php } ?>
					<?php if ((int) $data['email']['type'] === 2) { ?><li><a class="wysija_item" id="wysija-widget-autopost" wysija_type="popup-auto-post"><?php _e('Automatic latest content', WYSIJA) ?></a></li><?php } ?>
				<li>
					<a class="wysija_item" wysija_type="divider" wysija_src="<?php echo $divider['src'] ?>" wysija_width="<?php echo $divider['width'] ?>" wysija_height="<?php echo $divider['height'] ?>"><?php _e('Divider', WYSIJA) ?></a>
					<a id="wysija_divider_settings" class="wysija_item_settings settings" title="<?php _e('Edit', WYSIJA); ?>" href="javascript:;" href2="admin.php?page=wysija_campaigns&action=dividers&tab=dividers&emailId=<?php echo $_REQUEST['id'] ?>"><span class="dashicons dashicons-admin-generic"></span></a>
				</li>
				<li><a class="wysija_item" wysija_type="popup-bookmark"><?php _e('Social bookmarks', WYSIJA) ?></a></li>
			</ul>

			<!-- IMAGES BAR -->
			<div class="wj_images" style="display:none;">
				<div class="wj_button">
					<?php
					$action = 'special_new_wordp_upload';
					?>
					<a id="wysija-upload-browse" class="button" href="javascript:;" href2="admin.php?page=wysija_campaigns&action=medias&tab=<?php echo $action; ?>&emailId=<?php echo $_REQUEST['id'] ?>"><?php _e('Add Images', WYSIJA) ?></a>
				</div>

				<ul id="wj-images-quick" class="clearfix">
					<?php
					//get list images from template
					$helper_image = WYSIJA::get('image','helper');
					$result = $helper_image->get_list_directory();

					$quick_select = $data['email']['params'];
					if(!isset($quick_select['quickselection'])){
						$quick_select['quickselection'] = array();
					}else{
						foreach($quick_select['quickselection'] as &$image){
							$image = $helper_image->valid_image($image);
						}
					}

					if($result && empty($quick_select['quickselection'])) {
						echo $wjEngine->renderImages($result);
					} else {
						echo $wjEngine->renderImages($quick_select['quickselection']);
					}
					?>
				</ul>
				<div id="wj_images_preview" style="display:none;"></div>
			</div>

			<!-- STYLES BAR -->
			<?php if (WYSIJA::current_user_can('wysija_style_tab')): ?>
					<div class="wj_styles" style="display:none;">
						<form id="wj_styles_form" action="" method="post" accept-charset="utf-8">
				<?php
				echo $wjEngine->renderStylesBar();
				?>
						</form>
					</div>
			<?php endif; ?>

				<!-- THEMES BAR -->
			<?php if (WYSIJA::current_user_can('wysija_theme_tab')): ?>
					<div class="wj_themes" style="display:none;">
						<div class="wj_button" style="display:none;">
							<a id="wysija-themes-browse" class="button" href="javascript:;" href2="admin.php?page=wysija_campaigns&action=themes"><?php _e('Add more themes', WYSIJA) ?></a>
						</div>
						<ul id="wj_themes_list" class="clearfix">
			<?php
			//get themes
			echo $wjEngine->renderThemes();
			?>
						</ul>
						<div id="wj_themes_preview" style="display:none;"></div>
					</div>
				<?php endif; ?>

				<div id="wysija_notices">
					<span id="wysija_notice_msg">
					<?php echo __('Our toolbar doesn\'t load?', WYSIJA) ?>
						<br />
						<?php
						echo str_replace(array('[link]', '[/link]'), array('<a title="' . __('Conflict', WYSIJA) . '" target="_blank" href="http://support.mailpoet.com/knowledgebase/list-of-plugins-that-may-cause-conflict/">', '</a>'), __('There must be an active 3rd party plugin or theme breaking our interface. [link]Read more.[/link]', WYSIJA));
						?>
					</span>
					<img alt="loader" id="ajax-loading" src="<?php echo WYSIJA_URL ?>img/wpspin_light.gif" />
				</div>
			</div>
			<!-- END: Wysija Toolbar -->
		<?php
		global $current_user;

		$emailuser = $current_user->data->user_email;
		?>
				<p><input type="text" name="receiver-preview" id="preview-receiver" value="<?php echo $emailuser ?>" /> <a href="javascript:;" id="wj-send-preview" class="button wysija"><?php _e("Send preview", WYSIJA) ?></a></p>
		<?php
		echo apply_filters('wysija_howspammy', '');
		?>

		<p class="submit">
			<?php $this->secure(array('action' => "saveemail", 'id' => $data['email']['email_id'])); ?>
			<input data-type="<?php echo (int) $data['email']['type'] ?>" type="hidden" name="wysija[email][email_id]" id="email_id" value="<?php echo esc_attr($data['email']['email_id']) ?>" />
			<input type="hidden" value="saveemail" name="action" />

			<a id="wysija-do-save" class="button-primary wysija" href="javascript:;"><?php _e("Save changes", WYSIJA) ?></a>
			<a id="wysija-next-step" class="button-primary wysija" href="admin.php?page=wysija_campaigns&action=editDetails&id=<?php echo $data['email']['email_id'] ?>"><?php _e("Next step",WYSIJA) ?></a>
			<?php
			// we cannot have it everywhere
			if (false && $data && (int) $data['email']['type'] === 2) {
				echo '<a id="save-reactivate" class="button-primary wysija" href="admin.php?page=wysija_campaigns&action=resume&id='.$data['email']['email_id'].'&_wpnonce='.$this->secure(array('action' => 'resume' , 'id' => $data['email']["email_id"]), true).'">'.__("Save and reactivate",WYSIJA).'</a>';
			}
			?>
			<?php echo '<a href="admin.php?page=wysija_campaigns&action=edit&id=' . $data['email']['email_id'] . '">' . __('go back to Step 1', WYSIJA) . '</a>' ?>
		</p>

		<!-- BEGIN: Wysija Toolbar -->
		<script type="text/javascript" charset="utf-8">
			wysijaAJAX.id = <?php echo (int) $_REQUEST['id'] ?>;

			function saveWYSIJA(callback) {
				wysijaAJAX.task = 'save_editor';
				wysijaAJAX._wpnonce = wysijanonces.campaigns.save_editor;
				wysijaAJAX.wysijaData = Wysija.save();
				WYSIJA_SYNC_AJAX({success: callback});
			}

			// trigger the save on these links/buttons (save, next step, view in browser, unsubscribe)
			$$('#wysija-do-save, #wysija-next-step, #wysija_viewbrowser a, #wysija_unsubscribe a').invoke('observe', 'click', function(e) {
				if (this.id === 'wysija-next-step') {
					e.preventDefault();
					var id = this.id,
					    href = this.href;
					var callback = function () {
						if (id === 'wysija-next-step') window.location.href = href
					};
				}
				else var callback = function() {};
				saveWYSIJA(callback);
				return false;
			});

			function switchThemeWYSIJA(event) {
				// get event target
				var target = (event.currentTarget) ? event.currentTarget : event.srcElement.parentElement;

				if(window.confirm("<?php _e('If you confirm the theme switch, it will override your header, footer, dividers and styles', WYSIJA) ?>")) {
					wysijaAJAX.task = 'switch_theme';
                                        wysijaAJAX._wpnonce = wysijanonces.campaigns.switch_theme;
					wysijaAJAX.wysijaData = Object.toJSON(new Hash({theme: $(target).readAttribute('rel')}));
					wysijaAJAX.popTitle = "Switch theme";
					WYSIJA_AJAX_POST({
						'success': function(response) {
							// set theme name
							$('wysija_widgets_settings').down('.theme').update(response.responseJSON.result.templates.theme);

							// set css
							if(response.responseJSON.result.styles.css != null) {
								// updateStyles(response.responseJSON.result.styles.css);
								Wysija.updateCSS(response.responseJSON.result.styles.css.strip());
							}

														// update styles form
							if(response.responseJSON.result.styles.form != null) {
								// refresh styles form
								$('wj_styles_form').innerHTML = response.responseJSON.result.styles.form;
								// setup color pickers
								setupColorPickers();

								// setup apply styles on value changed
								setupStylesForm();

								// apply styles
								applyStyles();
							}

							// set header
							if (response.responseJSON.result.templates.header != undefined) {
								$$('.' + Wysija.options.header)[0].replace(response.responseJSON.result.templates.header);
							}
							// set footer
							if (response.responseJSON.result.templates.footer != undefined) {
								$$('.' + Wysija.options.footer)[0].replace(response.responseJSON.result.templates.footer);
							}
							// set divider
							if (response.responseJSON.result.templates.divider != undefined) {
								Wysija.setDivider(response.responseJSON.result.templates.divider, response.responseJSON.result.templates.divider_options);
								Wysija.replaceDividers();
							}

							Wysija.init();
							saveWYSIJA();
						}
					});
					return false;
				}
			}

					function applyStyles() {
						wysijaAJAX.task = 'save_styles';
                                                wysijaAJAX._wpnonce = wysijanonces.campaigns.save_styles;
						wysijaAJAX.wysijaStyles = Object.toJSON($('wj_styles_form').serialize(true));
						wysijaAJAX.popTitle = "Save styles";
						WYSIJA_AJAX_POST({
							'success': function(response) {
								// remove fixed height for each text block
								$$('.wysija_text').invoke('setStyle', {height: 'auto'});

								// apply new styles
								Wysija.updateCSS(response.responseJSON.result.styles.strip());
							}
						});

						return false;
					}

					function setupStylesForm() {
						$$('#wj_styles_form select, #wj_styles_form input').invoke('observe', 'change', applyStyles);
					}

					function setupColorPickers() {
						jQuery(function($) {
							$('.color').modcoder_excolor({
								hue_bar: 1,
								border_color: '#969696',
								anim_speed: 'fast',
								round_corners: false,
								shadow_size: 2,
								shadow_color: '#f0f0f0',
								background_color: '#ececec',
								backlight: false,
								label_color: '#333333',
								effect: 'fade',
								show_input: false,
								z_index: 20000,
								hide_on_scroll: true,
								callback_on_init: function() {
									Wysija.locks.selectingColor = true;
								},
								callback_on_select: function(color, input) {
									Wysija.updateCSSColor(input, color);
								},
								callback_on_ok: function(color, color_has_changed) {
									if (color_has_changed === true) {
										// apply styles only if the color has changed
										applyStyles();
									}
									// unlock editor
									Wysija.locks.selectingColor = false;
								}
							});
						});
					}

					function saveIQS() {
						wysijaAJAX.task = 'save_IQS';
						wysijaAJAX._wpnonce = wysijanonces.campaigns.save_IQS;
						wysijaAJAX.wysijaIMG = Object.toJSON(wysijaIMG);
						WYSIJA_AJAX_POST();
					}

					// prototype on load
					document.observe('dom:loaded', function() {
						setupStylesForm();

						var konami = new Konami();
						konami.code = function() {
							Wysija.flyToTheMoon();
						}
						konami.load();
					});

					// jquery on load
					jQuery(function($) {
						$(function() {
							setupColorPickers();
						});
					});
				</script>
				<!-- END: Wysija Toolbar -->
				<div id="wysija-konami" >
					<div id="wysija-konami-overlay" style="display:none;width:100%; height:100%; position:fixed;top:0;left:0;background-color:#fff;z-index:99998;overflow:hidden;">
						<img id="wysija-konami-bird" src="<?php echo WYSIJA_URL ?>img/wysija_bird.jpg" style="display:none;z-index:99999;position:absolute;top:100px;left:100px;" width="597" height="483" />
					</div>
				</div>

				<div id="wysija-divider">

				</div>
				<?php
			}

			/* when newsletter has been sent let's see the feedback */

			function editDetails($data = false) {

				$this->data = $data;
				$step = array();
				$step['subject'] = array(
					'type' => 'subject',
					'label' => __('Subject line', WYSIJA),
					'class' => 'validate[required]',
					'desc' => __("Be creative! It's the first thing your subscribers see. Tempt them to open your email.", WYSIJA));

				if ((int) $data['email']['type'] === 2) {
					$step['params'] = array(
						'type' => 'frequencies',
						'label' => __('When...', WYSIJA), 'class' => 'validate[required]',
						'desc' => '',
						'labeloff' => 1,
						'rowclass' => 'automatic-nl');

					$step['type'] = array(
						'type' => 'type_nl',
						'class' => 'validate[required]',
						'labeloff' => 1,
						'label' => __('What type of newsletter is this?', WYSIJA),
						'rowclass' => 'hidden');

					if (isset($data['email']["params"]['autonl']['event']) && $data['email']["params"]['autonl']['event'] == 'new-articles') {
						$step['subject']['desc'] = str_replace(array('[newsletter:number]', '[newsletter:total]', '[newsletter:post_title]'), array('<b>[newsletter:number]</b>', '<b>[newsletter:total]</b>', '<b>[newsletter:post_title]</b>'), __('Insert [newsletter:total] to show number of posts, [newsletter:post_title] to show the latest post\'s title & [newsletter:number] to display the issue number.', WYSIJA));
					}
				}

				if ($this->data['lists']) {
					$step['lists'] = array(
						'type' => 'lists',
						'class' => 'validate[minCheckbox[1]] checkbox',
						'label' => __('Lists', WYSIJA),
						'labeloff' => 1,
						'rowclass' => 'listcheckboxes',
						'desc' => __('The subscriber list that will be used for this campaign.', WYSIJA));
				}

				$step['from_name'] = array(
					'type' => 'fromname',
					'class' => 'validate[required]',
					'label' => __('Sender', WYSIJA),
					'desc' => __('Name & email of yourself or your company.', WYSIJA));



				$step['replyto_name'] = array(
					'type' => 'fromname',
					'class' => 'validate[required]',
					'label' => __('Reply-to name & email', WYSIJA),
					'desc' => __('When the subscribers hit "reply" this is who will receive their email.', WYSIJA));


				$step = apply_filters('wysija_extend_step3', $step);

				//we schedule only the type 1 newsletter
				if ($data['email']['type'] == 1) {
					$step['scheduleit'] = array(
						'type' => 'scheduleit',
						'class' => '',
						'label' => __('Schedule it', WYSIJA),
						'desc' => '');
				}

				if ((int) $data['email']['sent_at'] === 0 && isset($data['autoresponder'])) {
					$step['ignore_subscribers'] = array(
						'type' => 'checkbox',
						'class' => '',
						'label' => __('Ignore current subscribers', WYSIJA),
						'desc' => __('Don\'t send to existing subscribers, only to future ones.', WYSIJA));
				}
				?>
				<form name="step3" method="post" id="campaignstep3" action="" class="form-valid">

					<table class="form-table">
						<tbody>
						<?php
						echo $this->buildMyForm($step, $data, "email");
						?>

						</tbody>
					</table>
						<?php
						global $current_user;
						$emailuser = $current_user->data->user_email;
						?>

					<p><input type="text" name="receiver-preview" id="preview-receiver" value="<?php echo $emailuser ?>" /> <a href="javascript:;" id="wj-send-preview" class="button wysija"><?php _e("Send preview", WYSIJA) ?></a></p>

					<p class="submit">
		<?php $this->secure(array('action' => "savelast", 'id' => $_REQUEST['id'])); ?>
						<input type="hidden" name="wysija[email][email_id]" id="email_id" value="<?php echo esc_attr($data['email']['email_id']) ?>" />
						<input type="hidden" name="wysija[campaign][campaign_id]" id="campaign_id" value="<?php echo esc_attr($data['email']['campaign_id']) ?>" />
						<input type="hidden" value="savelast" name="action"  />
						<input type="hidden" value="" name="wj_redir" id="hid-redir" />
						<?php
						if ((int) $this->data['email']['type'] == 2) {
							$sendNow = esc_attr(__('Activate now', WYSIJA));
							$saveresumesend = esc_attr(__('Activate now', WYSIJA));
							$buttonsave = esc_attr(__('Save as draft and close', WYSIJA));
							$buttonsendlater = $buttonsave;
						} else {

							$sendNow = esc_attr(__('Send', WYSIJA));
							$saveresumesend = esc_attr(__('Send', WYSIJA));
							$buttonsave = esc_attr(__('Save & close', WYSIJA));
							$buttonsendlater = esc_attr(__('Save as draft and close', WYSIJA));
						}

						if (in_array((int) $this->data['email']['status'], array(0, 4))) {

							if ($this->data['lists']) {
								?>
								<input type="submit" value="<?php echo $sendNow ?>" id="submit-send" name="submit-send" class="button-primary wysija"/>
						<?php }
					?>
							<input type="submit" value="<?php echo $buttonsendlater ?>" id="submit-draft" name="submit-draft" class="button wysija"/>
					<?php
				} else {
					?>

							<input type="submit" value="<?php echo $saveresumesend ?>" id="submit-send" name="submit-resume" class="button-primary wysija"/>
							<input type="submit" value="<?php echo $buttonsave ?>" id="submit-draft" name="submit-pause" class="button wysija"/>
					<?php
				}
				?>

				<?php
				echo str_replace(
						array('[link]', '[/link]'), array('<a href="admin.php?page=wysija_campaigns&action=editTemplate&id=' . $data['email']['email_id'] . '" id="link-back-step2">', '</a>'), __("or simply [link]go back to design[/link].", WYSIJA)
				);
				echo $this->immediatewarning;
				?>
					</p>
				</form>
				<?php
			}

			function fieldFormHTML_subject($key, $val, $model, $params) {
				$fieldHTML = '';
				$field = $key;


				$formObj = WYSIJA::get("forms", "helper");
				$fieldHTML = '<div id="titlediv">
			<div id="titlewrap" style="width:70%;">
					<input class="titlebox ' . $params['class'] . '" id="' . $key . '" name="wysija[email][subject]" size="30" type="text" autocomplete="off" value="' . esc_attr($val) . '" />
			</div>
		</div>';


				return $fieldHTML;
			}

			function fieldFormHTML_frequencies($key, $val, $model, $params) {
				$fieldHTML = '<div class="frequencies">';
				$field = $key;
				$id = $key;
				if (!$val) {
					$val = array(
						'autonl' =>
						array(
							'event' => 'new-articles',
							'day' => 'monday',
							'time' => '00:00:00',
							'when-article' => 'daily',
							'when-subscribe' => 'daily',
						)
					);
				} elseif (is_string($val)) {
					$val = unserialize(base64_decode($val));
				}


				if (!isset($val['autonl'])) {
					$val = array(
						'autonl' =>
						array(
							'event' => 'new-articles',
							'day' => 'monday',
							'time' => '00:00:00',
							'when-article' => 'daily',
							'when-subscribe' => 'daily',
						)
					);
				}

				$formsHelp = WYSIJA::get('forms', 'helper');
				foreach ($this->data['autonl']['fields'] as $fieldK => $field) {
					$myval = '';
					$singleFieldHtml = '';

					//dbg($field,0);
					if (isset($field['extend'])) {
						$field = $this->data['autonl']['fields'][$field['extend']];
					}
					if (isset($val['autonl'][$fieldK]))
						$myval = $val['autonl'][$fieldK];

					$classDDP = '';
					if (isset($field['class']))
						$classDDP = $field['class'];

					$dataArray = array('name' => 'wysija[email][params][autonl][' . $fieldK . ']', 'id' => $id . '-' . $fieldK, 'class' => $classDDP);
					if (isset($field['style'])) {
						$dataArray['style'] = $field['style'];
					}

					$arrayFields = array('event');
					if (!in_array($fieldK, $arrayFields))
						$classDDP.='sub-event';
					$arrayFields[] = 'when-article';
					if (!in_array($fieldK, $arrayFields))
						$classDDP.=' sub-when-article';
					$dataArray['class'] = $classDDP;

					//by default we return a dropdown
					if (!isset($field['type'])) {
						$singleFieldHtml.=$formsHelp->dropdown(
								$dataArray, $field['values'], $myval);
					} else {
						$typee = $field['type'];

						if ($typee == 'checkbox') {
							$singleFieldHtml.=$formsHelp->$typee($dataArray, '', $myval);
						} else {
							$singleFieldHtml.=$formsHelp->$typee($dataArray, $myval);
						}
					}

					if (isset($field['label_before']) || isset($field['label_after'])) {
						$before = $after = '';
						if (isset($field['label_before']))
							$before = $field['label_before'];
						if (isset($field['label_after']))
							$after = $field['label_after'];
						$singleFieldHtml = '<label id="' . $id . '-label-' . $fieldK . '" for="' . $id . '-' . $fieldK . '" class="' . $classDDP . '">' . $before . $singleFieldHtml . $after . '</label>';
					}

					$fieldHTML.=$singleFieldHtml;
				}



				$fieldHTML .= '</div>';
				return $fieldHTML;
			}

			function local_time_is() {
				$helper_toolbox = WYSIJA::get('toolbox', 'helper');

				return '<span class="local_time">' . sprintf(__('Local time is <code>%1$s</code>'), $helper_toolbox->site_current_time()) . '</span>';
			}

			function fieldFormHTML_type_nl($key, $val, $model, $params) {
				$fieldHTML = '<div class="list-radios">';
				$field = $key;
				$valuefield = array();

				$typesnl = array(
					'1' => array(
						'type' => 'standard',
						'label' => __('Standard newsletter', WYSIJA),
						'default' => 1
					),
					'2' => array(
						'type' => 'automatic',
						'label' => __('Automatic newsletter', WYSIJA),
					)
				);

				foreach ($typesnl as $typenl => $paramstnl) {

					$checked = '';
					if (($val && (int) $val == (int) $typenl) || (!$val && isset($paramstnl['default']))) {
						$checked = ' checked="checked" ';
					}

					$fieldHTML.='<label for="nl_type_' . $paramstnl['type'] . '">' .
							'<input class="radiotype-nl" id="nl_type_' . $paramstnl['type'] . '" type="radio" name="wysija[email][type]" value="' . $typenl . '" ' . $checked . ' />'
							. $paramstnl['label'] . '</label>';
				}

				$fieldHTML.='</div>';
				return $fieldHTML;
			}

			function fieldFormHTML_lists($key, $val, $model, $params) {
				$fieldHTML = '<div class="list-checkbox">';
				$field = $key;
				$valuefield = array();

				if (isset($this->data['campaign_list']) && $this->data['campaign_list']) {
					foreach ($this->data['campaign_list'] as $list) {
						$valuefield[$list['list_id']] = $list;
					}
				}


				$formObj = WYSIJA::get("forms", "helper");

				usort( $this->data['lists'], array('WYSIJA_view_back_campaigns', 'sort_by_name' ) );

				foreach ($this->data['lists'] as $list) {

					$checked = false;
					if (isset($valuefield[$list['list_id']]))
						$checked = true;

					$fieldHTML.= '<p><label for="' . $field . $list['list_id'] . '">';
					$fieldHTML.=$formObj->checkbox(array('class' => $params['class'] . ' checklists', 'alt' => $list['name'], 'id' => $field . $list['list_id'], 'name' => "wysija[campaign_list][list_id][]"), $list['list_id'], $checked) . $list['name'] . ' (' . $list['count'] . ')';
					$fieldHTML.='<input type="hidden" id="' . $field . $list['list_id'] . 'count" value="' . $list['count'] . '" />';
					$fieldHTML.='</label></p>';
				}

				$fieldHTML.="</div>";
				return $fieldHTML;
			}

			function fieldFormHTML_scheduleit($key, $val, $model, $params) {
				$formObj = WYSIJA::get('forms', 'helper');

				$valuescheduled = '';

				if (isset($this->data['email']['params']['schedule']['isscheduled']))
					$valuescheduled = $this->data['email']['params']['schedule']['isscheduled'];
				$data = $formObj->checkbox(array('class' => $params['class'], 'id' => $key, 'name' => 'wysija[email][params][schedule][isscheduled]'), true, $valuescheduled);
				$data .= $this->fieldFormHTML_datepicker('datepicker', $val, $model, $params);

				return $data;
			}

			/**
			 * @todo: move to top super class or heler
			 */
			function fieldFormHTML_datepicker($key, $val, $model, $params) {
				if ((int) $this->data['email']['type'] == 2)
					return;

				$fieldHTML = '<span id="schedule-area" class="schedule-row" >';
				$field = $key;
				$valuefield = array();

				$formObj = WYSIJA::get("forms", "helper");

				$valuescheduled = $valuetime = '';
				$valueday = date("Y/m/d");
				if (isset($this->data['email']['params']['schedule']['day'])) {
					$valueday = $this->data['email']['params']['schedule']['day'];
				}
				if (isset($this->data['email']['params']['schedule']['time'])) {
					$valuetime = $this->data['email']['params']['schedule']['time'];
				}
				if (isset($this->data['email']['params']['schedule']['isscheduled'])) {
					$valuescheduled = $this->data['email']['params']['schedule']['isscheduled'];
				}

				$fieldHTML.=$formObj->input(array('class' => $params['class'], 'id' => $field . '-day', 'name' => "wysija[email][params][schedule][day]", 'size' => 8), $valueday);
				$fieldHTML.=' @ ';
				$fieldHTML.=$formObj->dropdown(
					array(
						'name' => 'wysija[email][params][schedule][time]',
						'id' => $field.'-time'
					),
					$this->data['autonl']['fields']['time']['values'],
					$valuetime
				);

				$fieldHTML .= $this->local_time_is();
				$fieldHTML.='</span>';
				return $fieldHTML;
			}

			function edit($data) {
				//$this->menuTop("edit");
				$formid = 'wysija-' . $_REQUEST['action'];
				?>
				<div id="wysistats">
					<div id="wysistats1" class="left">
						<div id="statscontainer"></div>
						<h3><?php _e(sprintf('%1$s emails received.', $data['user']['emails']), WYSIJA) ?></h3>
					</div>
					<div id="wysistats2" class="left">
						<ul>
				<?php
				foreach ($data['charts']['stats'] as $stats) {
					echo "<li>" . $stats['name'] . ": " . $stats['number'] . "</li>";
				}
				echo "<li>" . __('Added', WYSIJA) . ":" . $this->fieldListHTML_created_at($data['user']['details']["created_at"]) . "</li>";
				?>

						</ul>
					</div>
					<div id="wysistats3" class="left">
						<p class="title"><?php echo __(sprintf('Total of %1$d clicks:', count($data['clicks'])), WYSIJA); ?></p>
						<ol>
		<?php
		foreach ($data['clicks'] as $click) {
			echo "<li>" . $click['name'] . " : " . $click['url'] . "</li>";
		}
		?>

						</ol>
					</div>
					<div class="clear"></div>
				</div>

		<?php
		$this->buttonsave = __('Save', WYSIJA);
		$this->add($data);
	}

	function popup_image_data($data) {
		echo $this->messages(true);
		?>
				<div class="popup_content addlink">
					<form method="post" action="" class="image-data-form" id="image-data-form">
						<p>
							<label for="url"><?php _e('Address:', WYSIJA) ?></label><br/>
							<input type="text" name="url" value="<?php echo (!empty($data['url'])) ? esc_attr($data['url']) : 'http://' ?>" id="url" />
						</p>
						<p>
							<label for="alt"><?php _e('Alternative text:', WYSIJA) ?></label><br/>
							<input type="text" name="alt" value="<?php echo (!empty($data['alt'])) ? esc_attr($data['alt']) : '' ?>" id="alt" />
						</p>
						<p class="notice"><?php _e('This text is displayed when email clients block images, which is most of the time.', WYSIJA) ?></p>
						<p class="submit_button"><input id="image-data-submit" class="button-primary" type="submit" name="submit" value="<?php _e('Save', WYSIJA) ?>" /></p>
					</form>
				</div>
		<?php
	}

	function popup_themes($errors) {
		echo $this->messages(true);
		?>
		<div id="overlay"><img id="loader" src="<?php echo WYSIJA_URL ?>img/wpspin_light.gif" /></div>
		<div class="popup_content themes">
			<form enctype="multipart/form-data" method="post" action="" class="validate">
				<div id="search-view" class="panel">
					<?php
					if (isset($_REQUEST['reload']) && (int) $_REQUEST['reload'] === 1) {
						echo '<input type="hidden" id="themes-reload" name="themes-reload" value="1" />';
					}
					?>
					<div class="clearfix">
						<input type="button" id="sub-theme-box" name="submit" value="<?php echo esc_attr(__('Upload Theme (.zip)', WYSIJA)); ?>" class="button-secondary"/>
						<span id="filter-selection"></span>
						&nbsp;&nbsp;
						<span><?php echo str_replace(array('[link]', '[/link]'), array('<a href="http://support.mailpoet.com/knowledgebase/guide-to-creating-your-own-mailpoet-theme?utm_source=wpadmin&utm_campaign=theme%20guide" target="_blank">', '</a>'), __('[link]Guide[/link] to create your own theme.', WYSIJA)); ?></span>
						<div id="wj_paginator">
							<a class="selected" href="javascript:;" data-type="free"><?php _e('Free', WYSIJA); ?></a>
							<a href="javascript:;" data-type="premium"><?php _e('Premium', WYSIJA); ?></a>
						</div>
					</div>
					<ul id="themes-list"></ul>
				</div>
				<div id="theme-view" class="panel" style="display:none;"></div>
			</form>
			<div id="theme-upload" class="panel">
				<form enctype="multipart/form-data" method="post" action="" class="validate">
					<div class="wrap actions">
						<a class="button-secondary2 theme-view-back" href="javascript:;"><?php echo __("<< Back", WYSIJA) ?></a>
					</div>
					<div class="form">
					<?php
					$secure = array('action' => "themeupload");
					$this->secure($secure);
					?>
					<p><input type="file" name="my-theme"/>( <?php
					$helperNumbers = WYSIJA::get('numbers', 'helper');
					$data = $helperNumbers->get_max_file_upload();
					$bytes = $data['maxmegas'];

			echo sprintf(__('total max upload file size : %1$s', WYSIJA), $bytes); ?> )</p>
				<p><label for="overwrite"><input type="checkbox" id="overwrite" name="overwriteexistingtheme" /><?php echo __("If a theme with the same name exists, overwrite it.", WYSIJA); ?></label></p>
				<p><input type="hidden" name="action" value="themeupload" />
					<input type="submit" class="button-primary" name="submitter" value="<?php _e("Upload", WYSIJA) ?>" /></p>
				</div>
			</div>
			</form>
		</div>
		<?php
	}

	function test_bounce() {
		exit;
	}

	function themeupload() {
		$this->popup_themes(false);
	}

	function popup_articles($data = array(), $errors = array()) {
		// get articles helper
		$helper_articles = WYSIJA::get('articles', 'helper');

		echo $this->messages(true);
		?>
		<div class="popup_content inline_form articles">
			<form enctype="multipart/form-data" method="post" action="" id="articles-form">
				<div id="basic">
					<div class="clearfix">
						<div class="filters-box">
							<?php
							echo $helper_articles->field_select_post_type(array('value' => $data['params']['post_type'], 'label' => __('Filter by type', WYSIJA)));
							echo $helper_articles->field_select_terms();
							echo $helper_articles->field_select_status();
							?>
						</div>
						<div class="search-box">
							<input type="text" id="search" name="search" autocomplete="off" />
							<input type="submit" id="search-submit" name="submit" value="<?php _e('Search', WYSIJA); ?>" />
						</div>
					</div>

					<div id="results"></div>
				</div>

				<div id="advanced">
					<?php echo $this->_post_display_options($data); ?>
				</div>

				<div class="submit-box">
					<div id="loading-icon"></div>
					<div id="loading-message"></div>
					<a id="toggle-advanced" href="javascript:;"><?php _e('Display and insert options', WYSIJA); ?></a>
					<input id="insert-selection" class="button-primary" type="submit" name="insert" value="<?php _e('Insert selected', WYSIJA); ?>" />
					<input id="back-selection" class="button-secondary" type="button" value="<?php _e('Back to selection', WYSIJA); ?>" />
				</div>
			</form>
		</div>
		<?php
	}

	private function _post_display_options($data) {

		$output = '';

		// display option: excerpt / full post / title only
		$knowledgebase_url = str_replace(
			array('[link]', '[/link]'),
			array('<a href="http://support.mailpoet.com/knowledgebase/excerpts-in-wysija/?utm_source=wpadmin&utm_campaign=editor" target="_blank">', '</a>'),
			__('Which excerpt does it use? [link]Read more[/link]', WYSIJA)
		);

		$output .= '<div class="block clearfix">';
		$output .= '    <label>'.__('Display...', WYSIJA);
		$output .= '        <span class="label">'.$knowledgebase_url.'</span>';
		$output .= '    </label>';
		$output .= '    <label class="radio"><input type="radio" name="post_content" value="excerpt" '.(($data['params']['post_content'] === 'excerpt') ? 'checked="checked"' : '').' />'.__('excerpt', WYSIJA).'</label>';
		$output .= '    <label class="radio"><input type="radio" name="post_content" value="full" '.(($data['params']['post_content'] === 'full') ? 'checked="checked"' : '').' />'.__('full post', WYSIJA).'</label>';
		$output .= '    <label class="radio"><input type="radio" name="post_content" value="title" '.(($data['params']['post_content'] === 'title') ? 'checked="checked"' : '').' />'.__('title only', WYSIJA).'</label>';
		$output .= '</div>';

		// title format
		$output .= '<div class="block clearfix alternate">';
		$output .= '    <label>'.__('Title format', WYSIJA).'</label>';
		$output .= '    <label class="radio"><input type="radio" name="title_tag" value="h1" '.(($data['params']['title_tag'] === 'h1') ? 'checked="checked"' : '').' />'.__('heading 1', WYSIJA).'</label>';
		$output .= '    <label class="radio"><input type="radio" name="title_tag" value="h2" '.(($data['params']['title_tag'] === 'h2') ? 'checked="checked"' : '').' />'.__('heading 2', WYSIJA).'</label>';
		$output .= '    <label class="radio"><input type="radio" name="title_tag" value="h3" '.(($data['params']['title_tag'] === 'h3') ? 'checked="checked"' : '').' />'.__('heading 3', WYSIJA).'</label>';
		$output .= '    <label id="title_tag_list" class="radio"><input type="radio" name="title_tag" value="list" '.(($data['params']['title_tag'] === 'list') ? 'checked="checked"' : '').' />'.__('show as list', WYSIJA).'</label>';
		$output .= '</div>';

		// title position
		$output .= '<div id="title_position_block" class="block clearfix">';
		$output .= '    <label>'.__('Title position', WYSIJA).'</label>';
		$output .= '    <label class="radio"><input type="radio" name="title_position" value="inside" '.(($data['params']['title_position'] === 'inside') ? 'checked="checked"' : '').' />'.__('in text block', WYSIJA).'</label>';
		$output .= '    <label class="radio"><input type="radio" name="title_position" value="outside" '.(($data['params']['title_position'] === 'outside') ? 'checked="checked"' : '').' />'.__('above text block and image', WYSIJA).'</label>';
		$output .= '</div>';

		// title alignment
		$output .= '<div class="block clearfix alternate">';
		$output .= '    <label>'.__('Title alignment', WYSIJA).'</label>';
		$output .= '    <label class="radio"><input type="radio" name="title_alignment" value="left" '.(($data['params']['title_alignment'] === 'left') ? 'checked="checked"' : '').' />'.__('left', WYSIJA).'</label>';
		$output .= '    <label class="radio"><input type="radio" name="title_alignment" value="center" '.(($data['params']['title_alignment'] === 'center') ? 'checked="checked"' : '').' />'.__('center', WYSIJA).'</label>';
		$output .= '    <label class="radio"><input type="radio" name="title_alignment" value="right" '.(($data['params']['title_alignment'] === 'right') ? 'checked="checked"' : '').' />'.__('right', WYSIJA).'</label>';
		$output .= '</div>';

		// image alignment
		$output .= '<div id="image_block">';
		$output .= '    <div class="block clearfix">';
		$output .= '        <label>'.__('Image alignment', WYSIJA).'</label>';

		$output .= '        <div class="group clearfix">';

		// display alternate only when multiple posts are allowed
		if($data['autopost_type'] !== 'single') {
			$output .= '    <label class="radio"><input type="radio" name="image_alignment" value="alternate" '.(($data['params']['image_alignment'] === 'alternate') ? 'checked="checked"' : '').' />'.__('alternate left & right', WYSIJA).'</label>';
		}
		$output .= '        <label class="radio"><input type="radio" name="image_alignment" value="left" '.(($data['params']['image_alignment'] === 'left') ? 'checked="checked"' : '').' />'.__('left', WYSIJA).'</label>';
		$output .= '        <label class="radio"><input type="radio" name="image_alignment" value="center" '.(($data['params']['image_alignment'] === 'center') ? 'checked="checked"' : '').' />'.__('center', WYSIJA).'</label>';
		$output .= '        <label class="radio"><input type="radio" name="image_alignment" value="right" '.(($data['params']['image_alignment'] === 'right') ? 'checked="checked"' : '').' />'.__('right', WYSIJA).'</label>';
		$output .= '        <label class="radio"><input type="radio" name="image_alignment" value="none" '.(($data['params']['image_alignment'] === 'none') ? 'checked="checked"' : '').' />'.__('no image', WYSIJA).'</label>';
		$output .= '    </div>';

		$output .= '    </div>';

		// image width
		$output .= '    <div id="image_width_block" class="block clearfix">';
		$output .= '        <input id="image_width" type="hidden" name="image_width" value="'.(int)$data['params']['image_width'].'" />';
		$output .= '        <label>'.__('Image width', WYSIJA).'</label>';
		$output .= '        <span id="image_width_slider">';
		$output .= '            <span id="slider_handle"></span>';
		$output .= '        </span>';
		$output .= '        <span id="slider_info"><span>'.(int)$data['params']['image_width'].'</span> px</span>';
		$output .= '    </div>';
		$output .= '</div>';

		// author options
		$output .= '<div id="author-block" class="block clearfix alternate">';
		$output .= '    <label>'.__('Include author', WYSIJA).'</label>';
		$output .= '    <div class="group">';
		$output .= '        <p class="clearfix">';
		$output .= '            <label class="radio"><input type="radio" name="author_show" value="no" '.(($data['params']['author_show'] === 'no') ? 'checked="checked"' : '').' />'.__('no', WYSIJA).'</label>';
		$output .= '            <label class="radio"><input type="radio" name="author_show" value="above" '.(($data['params']['author_show'] === 'above') ? 'checked="checked"' : '').' />'.__('above content', WYSIJA).'</label>';
		$output .= '            <label class="radio"><input type="radio" name="author_show" value="below" '.(($data['params']['author_show'] === 'below') ? 'checked="checked"' : '').' />'.__('below content', WYSIJA).'</label>';
		$output .= '        </p>';
		$output .= '        <p class="clearfix">';
		$output .= '            <label>'.__('Preceded by:', WYSIJA).'&nbsp;<input type="text" name="author_label" value="'.stripslashes($data['params']['author_label']).'" /></label>';
		$output .= '        </p>';
		$output .= '    </div>';
		$output .= '</div>';

		// categories options
		$output .= '<div id="category-block" class="block clearfix">';
		$output .= '    <label>'.__('Include categories', WYSIJA).'</label>';
		$output .= '    <div class="group">';
		$output .= '        <p class="clearfix">';
		$output .= '            <label class="radio"><input type="radio" name="category_show" value="no" '.(($data['params']['category_show'] === 'no') ? 'checked="checked"' : '').' />'.__('no', WYSIJA).'</label>';
		$output .= '            <label class="radio"><input type="radio" name="category_show" value="above" '.(($data['params']['category_show'] === 'above') ? 'checked="checked"' : '').' />'.__('above content', WYSIJA).'</label>';
		$output .= '            <label class="radio"><input type="radio" name="category_show" value="below" '.(($data['params']['category_show'] === 'below') ? 'checked="checked"' : '').' />'.__('below content', WYSIJA).'</label>';
		$output .= '        </p>';
		$output .= '        <p class="clearfix">';
		$output .= '            <label>'.__('Preceded by:', WYSIJA).'&nbsp;<input type="text" name="category_label" value="'.stripslashes($data['params']['category_label']).'" /></label>';
		$output .= '        </p>';
		$output .= '    </div>';
		$output .= '</div>';

		// read more
		$output .= '<div id="readmore-block" class="block clearfix">';
		$output .= '    <label for="readmore">'.__('"Read more" text', WYSIJA).'</label>';
		$output .= '    <input type="text" name="readmore" value="'.stripslashes($data['params']['readmore']).'" id="readmore" />';
		$output .= '</div>';

		// check if we allow mutiple posts within the ALC
		if($data['autopost_type'] === 'single') {
			// background color
			$output .= '<div id="bgcolor-block" class="block clearfix">';
			$output .= '    <label>'.__('Background color', WYSIJA).'</label>';
			$output .= '    <input class="color" type="text" name="bgcolor1" value="'.(isset($data['params']['bgcolor1']) ? $data['params']['bgcolor1'] : '').'" />';
			$output .= '</div>';
		} else {
			// batch insert options
			$output .= '<div class="block clearfix">';
			$output .= '    <h2>'.__('Batch insert options', WYSIJA).'</h2>';
			$output .= '</div>';

			// sort by
			$output .= '<div id="sort-block" class="block clearfix">';
			$output .= '    <label>'.__('Sort by', WYSIJA).'</label>';
			$output .= '    <label class="radio"><input type="radio" name="sort_by" value="newest" '.(($data['params']['sort_by'] === 'newest') ? 'checked="checked"' : '').' />'.__('newest', WYSIJA).'</label>';
			$output .= '    <label class="radio"><input type="radio" name="sort_by" value="oldest" '.(($data['params']['sort_by'] === 'oldest') ? 'checked="checked"' : '').' />'.__('oldest', WYSIJA).'</label>';
			$output .= '</div>';

			// show dividers
			$output .= '<div id="divider-block" class="block clearfix">';
			$output .= '    <label>'.__('Show divider between posts', WYSIJA).'</label>';
			$output .= '    <label class="radio"><input type="radio" name="show_divider" value="yes" '.(($data['params']['show_divider'] === 'yes') ? 'checked="checked"' : '').' />'.__('yes', WYSIJA).'</label>';
			$output .= '    <label class="radio"><input type="radio" name="show_divider" value="no" '.(($data['params']['show_divider'] === 'no') ? 'checked="checked"' : '').' />'.__('no', WYSIJA).'</label>';
			$output .= '</div>';

			// background colors
			$output .= '<div id="bgcolor-block" class="block clearfix">';
			$output .= '    <label>'.__('Background color with alternate', WYSIJA).'</label>';
			$output .= '    <input class="color" type="text" name="bgcolor1" value="'.(isset($data['params']['bgcolor1']) ? $data['params']['bgcolor1'] : '').'" />';
			$output .= '    <input class="color" type="text" name="bgcolor2" value="'.(isset($data['params']['bgcolor2']) ? $data['params']['bgcolor2'] : '').'" />';
			$output .= '</div>';
		}

		return $output;
	}

	function popup_dividers($data = array()) {
		echo $this->messages(true);
		?>

		<div class="popup_content dividers">
			<form enctype="multipart/form-data" method="post" action="" class="" id="dividers-form">
				<ul class="dividers">
				<?php
				foreach($data['dividers'] as $divider) {
					$selected = ($divider['src'] === $data['selected']['src']) ? ' class="selected"' : '';
				?>
					<li class="clearfix"><a href="javascript:;"<?php echo $selected ?>><img src="<?php echo $divider['src'] ?>" alt="" width="<?php echo $divider['width'] ?>" height="<?php echo $divider['height'] ?>" /></a></li>
				<?php
				}
				?>
				</ul>
				<input type="hidden" name="email_id" value="<?php echo $data['email']['email_id'] ?>" id="email_id" />
				<input type="hidden" name="divider_src" value="<?php echo $data['selected']['src'] ?>" id="divider_src" />
				<input type="hidden" name="divider_width" value="<?php echo $data['selected']['width'] ?>" id="divider_width" />
				<input type="hidden" name="divider_height" value="<?php echo $data['selected']['height'] ?>" id="divider_height" />
				<p class="submit_button">
					<input type="submit" id="dividers-submit" class="button-primary" name="submit" value="<?php echo esc_attr(__('Done', WYSIJA)); ?>" />
				</p>
			</form>
		</div>


	<?php
	}

	function popup_autopost($data = array()) {
		echo $this->messages(true);

		$output = '';

		// container
		$output .= '<div class="popup_content inline_form autopost">';

		// form
		$output .= '<form enctype="multipart/form-data" method="post" action="" class="" id="autopost-form">';

		// basic options
		$output .= '<div id="basic">';

		$helper_articles = WYSIJA::get('articles', 'helper');
		$output .= '<div class="block clearfix">';
		$output .= '    <label>'.__('Post type', WYSIJA).'</label>';
		$output .= $helper_articles->field_select_post_type( array( 'value' => $data['params']['post_type'] ) );
		$output .= '</div>';

		// post limit
		if($data['autopost_type'] === 'single') {
			$output .= '<input type="hidden" name="post_limit" value="1" />';
		} else {
			$output .= '<div class="block clearfix">';
			$output .= '    <label>'.__('Maximum of posts to show', WYSIJA).'</label>';
			$output .= '    <select name="post_limit" id="post_limit">';
			foreach($data['post_limits'] as $limit) {
				$output .= '    <option value="'.$limit.'" '.(($limit === (int)$data['params']['post_limit']) ? 'selected="selected"' : '').' >'.$limit.'</option>';
			}
			$output .= '    </select>';
			$output .= '</div>';
		}

		// Get selected terms IDs
		$terms_selected = array_filter( ( isset( $data['params']['category_ids'] ) ? explode( ',', trim( $data['params']['category_ids'] ) ) : array() ) );

		// Create the init selection in to be in the Select2 format of results
		$_attr_init_selection = array();

		// Only build if there are any selected terms
		if ( ! empty( $terms_selected ) ) {
			$taxonomies = get_taxonomies( array(), 'objects' );
			$terms = get_terms( array_keys( $taxonomies ), array( 'include' => $terms_selected, 'hide_empty' => false ) );

			foreach ( $terms as $term ) {
				$_attr_init_selection[] = array(
					'id' => $term->term_id,
					'text' => wp_kses( $taxonomies[$term->taxonomy]->labels->singular_name . ': ' . $term->name, array() ),
				);
			}
		}

		// categories
		$output .=
		'<div class="block clearfix" id="categories_filters">' .
			'<label title="' . esc_attr__( 'And taxonomies as well...', WYSIJA ) . '">' . __( 'Categories and tags', WYSIJA ) . '</label>' .
			'<div class="group">' .
				'<p class="category_select clearfix">' .
					'<input data-placeholder="' . __( 'Select...', WYSIJA ) . '" name="category_ids" style="width: 300px" class="category_ids mailpoet-field-select2-terms" data-multiple="true" data-value=\'' . WJ_Utils::esc_json_attr( $_attr_init_selection ) . '\' value="' . esc_attr( implode( ',', $terms_selected ) ) . '" type="hidden">' .
				'</p>' .
			'</div>' .
		'</div>';

		// end - basic options
		$output .= '</div>';

		// display options
		$output .= '<p><a id="toggle-advanced" href="javascript:;">'.__('Show display options', WYSIJA).'</a></p>';
		$output .= '<div id="advanced">';
		$output .= $this->_post_display_options($data);
		$output .= '</div>';

		$output .= '    <p class="submit_button"><input type="submit" id="autopost-submit" class="button-primary" name="submit" value="'.__('Done', WYSIJA).'" /></p>';
		$output .= '</form>';
		$output .= '</div>';

		echo $output;
	}

	function popup_bookmarks($data = array()) {
		echo $this->messages(true);
		?>
				<div class="popup_content inline_form bookmarks">
					<form enctype="multipart/form-data" method="post" action="" class="" id="bookmarks-form">
						<ul class="networks">
				<?php
				$i = 0;
				foreach ($data['networks'] as $key => $network) {
					?>
								<li class="clearfix">
									<input type="hidden" name="bookmarks-<?php echo($key) ?>-position" value="<?php echo($i++) ?>" />
									<label for="bookmarks-url-<?php echo($key) ?>"><?php echo($network['label']) ?></label><input type="text" name="bookmarks-<?php echo($key) ?>-url" placeholder="<?php echo $network['placeholder']; ?>" value="<?php echo htmlentities($network['url']) ?>" id="bookmarks-url-<?php echo($key) ?>" />
								</li>
						<?php
					}
					?>
						</ul>

						<div class="sizes">
							<span><?php _e('Size:', WYSIJA) ?></span>
							<a href="javascript:;" class="small<?php if ($data['size'] === 'small') echo ' selected' ?>" rel="small"><?php _e('small', WYSIJA) ?></a>
							<a href="javascript:;" class="medium<?php if ($data['size'] === 'medium') echo ' selected' ?>" rel="medium"><?php _e('medium', WYSIJA) ?></a>
							<input type="hidden" name="bookmarks-size" value="<?php echo $data['size'] ?>" id="bookmarks-size" />
						</div>

						<ul class="icons"><!-- this will be loaded via ajax --></ul>
						<input type="hidden" name="bookmarks-iconset" value="" id="bookmarks-iconset" />
						<input type="hidden" name="bookmarks-theme" value="<?php echo $data['theme'] ?>" id="bookmarks-theme" />

						<p class="submit_button">
							<input type="submit" id="bookmarks-submit" name="submit" value="<?php echo esc_attr(__("Done", WYSIJA)) ?>" class="button-primary"/>
						</p>
					</form>

				</div>
				<?php
	}

	function popup_wysija_browse($errors) {
				echo $this->messages(true);
				?><div id="overlay"><img id="loader" src="<?php echo WYSIJA_URL ?>img/wpspin_light.gif" /></div>
				<div class="popup_content media-browse">
					<?php
					global $redir_tab, $type;

					$redir_tab = 'wysija_browse';
					media_upload_header();
					$post_id = intval($_REQUEST['post_id']);
					?>

					<form enctype="multipart/form-data" method="post" action="" class="media-upload-form validate" id="wysija-browse-form">
					<?php
					$secure = array('action' => "medias");
					$this->secure($secure);
					?>

						<div id="media-items" class="clearfix"><?php echo $this->_get_media_items($post_id, $errors); ?></div>
					</form>
					<?php $this->_alt_close(); ?>
				</div>
					<?php
				}

	function _alt_close() {
	?>
		<p class="submit_button"><input type="submit" id="close-pop-alt" value="<?php echo esc_attr(__("Done", WYSIJA)) ?>" name="submit-draft" class="button-primary wysija"/></p>
	<?php
	}

	function __filterPostParent($query) {
		global $wp_query;

		return $query . ' AND post_parent!=' . (int) $_REQUEST['post_id'] . ' ';
	}

	function popup_wp_browse($errors) {
		echo $this->messages(true);
		?><div id="overlay"><img id="loader" src="<?php echo WYSIJA_URL ?>img/wpspin_light.gif" /></div>
				<div class="popup_content media-wp-browse">
							<?php
							global $redir_tab, $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types;

							$redir_tab = 'wp_browse';

							media_upload_header();

							$limit = 20;

							$_GET['paged'] = isset($_GET['paged']) ? intval($_GET['paged']) : 0;
							if ($_GET['paged'] < 1)
								$_GET['paged'] = 1;
							$start = ( $_GET['paged'] - 1 ) * $limit;
							if ($start < 1)
								$start = 0;
							add_filter('post_limits', create_function('$a', "return 'LIMIT $start, $limit';"));
							add_filter('posts_where_paged', array($this, '__filterPostParent'));
							//add_filter( 'posts_where_paged', create_function( '$a', "return ' AND post_parent!=1' " ) );

							list($post_mime_types, $avail_post_mime_types) = wp_edit_attachments_query(array('post_mime_type' => array('image')));
							?>

					<form enctype="multipart/form-data" method="post" action="" class="media-upload-form validate" id="library-form">

						<div class="tablenav">

					<?php
					$page_links = paginate_links(array(
						'base' => add_query_arg('paged', '%#%'),
						'format' => '',
						'prev_text' => __('&laquo;'),
						'next_text' => __('&raquo;'),
						'total' => ceil($wp_query->found_posts / $limit),
						'current' => $_GET['paged']
					));

					if ($page_links)
						echo "<div class='tablenav-pages'>$page_links</div>";
					?>
						</div>


					<?php
					$secure = array('action' => "medias");
					$this->secure($secure);
					?>

						<div id="media-items" class="clearfix"><?php echo $this->_get_media_items(null, $errors, true); ?></div>
					</form>

					<?php $this->_alt_close(); ?>
				</div>
					<?php
				}

				function popup_new_wp_upload($errors) {
					echo $this->messages(true);
					?>
				<div id="overlay"><img id="loader" src="<?php echo WYSIJA_URL ?>img/wpspin_light.gif" /></div>
				<div class="popup_content media-wp-upload">
						<?php
						global $redir_tab, $type, $tab;

						$redir_tab = 'new_wp_upload';

						media_upload_header();

						global $type, $tab, $pagenow, $is_IE, $is_opera;

						if (function_exists('_device_can_upload') && !_device_can_upload()) {
							echo '<p>' . __('The web browser on your device cannot be used to upload files. You may be able to use the <a href="http://wordpress.org/extend/mobile/">native app for your device</a> instead.') . '</p>';
							return;
						}

						$upload_action_url = admin_url('async-upload.php');
						$post_id = isset($_REQUEST['post_id']) ? intval($_REQUEST['post_id']) : 0;
						$_type = isset($type) ? $type : '';
						$_tab = isset($tab) ? $tab : '';

						$upload_size_unit = $max_upload_size = wp_max_upload_size();
						$sizes = array('KB', 'MB', 'GB');

						for ($u = -1; $upload_size_unit > 1024 && $u < count($sizes) - 1; $u++) {
							$upload_size_unit /= 1024;
						}

						if ($u < 0) {
							$upload_size_unit = 0;
							$u = 0;
						} else {
							$upload_size_unit = (int) $upload_size_unit;
						}
						?>
					<script type="text/javascript">var post_id = <?php echo $post_id; ?>;</script>
					<div id="media-upload-notice"><?php
					if (isset($errors['upload_notice']))
						echo $errors['upload_notice'];
					?></div>
					<div id="media-upload-error"><?php
					if (isset($errors['upload_error']) && is_wp_error($errors['upload_error']))
						echo $errors['upload_error']->get_error_message();
					?></div>
					<?php
					// Check quota for this blog if multisite
					if (is_multisite() && !is_upload_space_available()) {
						echo '<p>' . sprintf(__('Sorry, you have filled your storage quota (%s MB).'), get_space_allowed()) . '</p>';
						return;
					}

					do_action('pre-upload-ui');

					$post_params = array(
						"post_id" => $post_id,
						"_wpnonce" => wp_create_nonce('media-form'),
						"type" => $_type,
						"tab" => $_tab,
						"short" => "1",
					);

					$post_params = apply_filters('upload_post_params', $post_params); // hook change! old name: 'swfupload_post_params'

					$plupload_init = array(
						'runtimes' => 'html5,silverlight,flash,html4',
						'browse_button' => 'plupload-browse-button',
						'container' => 'plupload-upload-ui',
						'drop_element' => 'drag-drop-area',
						'file_data_name' => 'async-upload',
						'multiple_queues' => true,
						'max_file_size' => $max_upload_size . 'b',
						'url' => $upload_action_url,
						'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
						'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
						'filters' => array(array('title' => __('Allowed Files'), 'extensions' => '*')),
						'multipart' => true,
						'urlstream_upload' => true,
						'multipart_params' => $post_params
					);

					$plupload_init = apply_filters('plupload_init', $plupload_init);
					?>

					<script type="text/javascript">
							<?php
							// Verify size is an int. If not return default value.
							$large_size_h = absint(get_option('large_size_h'));
							if (!$large_size_h)
								$large_size_h = 1024;
							$large_size_w = absint(get_option('large_size_w'));
							if (!$large_size_w)
								$large_size_w = 1024;
							?>
						var resize_height = <?php echo $large_size_h; ?>, resize_width = <?php echo $large_size_w; ?>,
								wpUploaderInit = <?php echo json_encode($plupload_init); ?>;
					</script>

					<div id="plupload-upload-ui" class="hide-if-no-js">
					<?php do_action('pre-plupload-upload-ui'); // hook change, old name: 'pre-flash-upload-ui'  ?>
						<div id="drag-drop-area">
							<div class="drag-drop-inside">
								<p class="drag-drop-info"><?php _e('Drop files here', WYSIJA); ?></p>
								<p><?php _ex('or', 'Uploader: Drop files here - or - Select Files', WYSIJA); ?></p>
								<p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files', WYSIJA); ?>" class="button" /></p>
							</div>
						</div>
				<?php do_action('post-plupload-upload-ui'); // hook change, old name: 'post-flash-upload-ui' ?>
					</div>

					<div id="html-upload-ui" class="hide-if-js">
				<?php do_action('pre-html-upload-ui'); ?>
						<p id="async-upload-wrap" class="clearfix">
							<label class="screen-reader-text" for="async-upload"><?php _e('Upload', WYSIJA); ?></label>
							<input type="file" name="async-upload" id="async-upload" />
				<?php submit_button(__('Upload'), 'button', 'html-upload', false); ?>
							<a href="#" onclick="try {
								top.tb_remove();
							} catch (e) {
							}
							;
							return false;"><?php _e('Cancel', WYSIJA); ?></a>
						</p>
				<?php do_action('post-html-upload-ui'); ?>
					</div>

					<p class="max-upload-size"><?php printf(__('Maximum upload file size: %d%s.', WYSIJA), esc_html($upload_size_unit), esc_html($sizes[$u])); ?></p>
				<?php if (($is_IE || $is_opera) && $max_upload_size > 100 * 1024 * 1024) { ?>
						<p class="big-file-warning"><?php _e('Your browser has some limitations uploading large files with the multi-file uploader. Please use the browser uploader for files over 100MB.', WYSIJA); ?></p>
				<?php }
				?>
					<div id="media-items" class="hide-if-no-js"></div>
				<?php do_action('post-upload-ui'); ?>
				</div>
				<?php
			}

			function _get_media_items($post_id, $errors, $wpimage = false) {
				$attachments = array();

				if ($post_id) {
					$post = get_post($post_id);
					if ($post && $post->post_type == 'attachment')
						$attachments = array($post->ID => $post);
					else
						$attachments = get_children(array('post_parent' => $post_id, 'post_type' => 'attachment', 'orderby' => 'ID', 'order' => 'DESC', 'post_mime_type' => 'image'));
				} else {

					// old weird code reverted as the pagination broke
					if (is_array($GLOBALS['wp_the_query']->posts)) {
						foreach ($GLOBALS['wp_the_query']->posts as $attachment) {
							$attachments[$attachment->ID] = $attachment;
						}
					}
					//TODO update the code so that we take care of the query ourselves without passing through WP get_posts() or get_children()
					//$attachments = get_children( array( 'post_type' => 'attachment', 'orderby' => 'ID', 'order' => 'DESC', 'post_mime_type'=>'image') );
				}

			$selectedImages=$this->_getSelectedImages();
			$output = '';
			$helper_image = WYSIJA::get('image','helper');
			foreach ( (array) $attachments as $id => $attachment ) {

					if (!$post_id && $attachment->post_parent == $_REQUEST['post_id']) {
						continue;
					}
					if ($attachment->post_status == 'trash') {
						continue;
					}
					if ($attachment->post_mime_type == 'image/bmp') {
						continue;
					}
					if (($id = intval($id)) && ($thumb_details = wp_get_attachment_image_src($id, 'thumbnail', true))) {
						$thumb_url = $thumb_details[0];
					} else {
						$thumb_url = false;
					}

					// Check if we have our image size, otherwise, use full image.
					if (($id = intval($id)) && ($wysija_sized_image = wp_get_attachment_image_src($id, 'wysija-newsletters-max', true))) {
						$full_url = $wysija_sized_image[0];
					} else {
						$full_url = $attachment->guid;
					}

					if ( ( $id = intval( $id ) )) $img_details = wp_get_attachment_image_src( $id, 'full', true );

					$image_template = array(
						   'width'=> $img_details[1],
						   'height'=> $img_details[2],
						   'url'=> $full_url,
						   );

					if(empty($image_template['width']) || empty($image_template['height']) || (empty($image_template['width']) && empty($image_template['height']))){
						$image_template = $helper_image->valid_image($image_template);
					}


					 $classname="";

					 if(isset($selectedImages["wp-".$attachment->ID])) $classname=" selected ";

					$output.='<div class="wysija-thumb image-'.  esc_attr($attachment->ID.$classname).'">';
					$output .= '<img title="'.  esc_attr($attachment->post_title).'" alt="'.  esc_attr($attachment->post_title).'" src="'.esc_url($thumb_url).'" class="thumbnail" />';
					if(!$wpimage)    $output.='<span class="delete-wrap"><span class="delete del-attachment">'.esc_html($attachment->ID).'</span></span>';
					$output.='<span class="identifier">'.  esc_html($attachment->ID).'</span>
						<span class="width">'.$image_template['width'].'</span>
						<span class="height">'.$image_template['height'].'</span>
						<span class="url">'.esc_url($full_url).'</span>
						<span class="thumb_url">'.esc_url($thumb_url).'</span></div>';
				}
				if (!$output) {
					if($wpimage === false) {
						$output = "<em>" . __('This tab will be filled with images from your current and previous newsletters.', WYSIJA) . "</em>";
					} else {
						$output = "<em>" . __('This tab will be filled with images from your WordPress Posts.', WYSIJA) . "</em>";
					}
				}
				return $output;
			}

			function _getSelectedImages() {
				$modelEmail = WYSIJA::get("email", "model");
				$email = $modelEmail->getOne(false, array("email_id" => $_REQUEST['emailId']));

				if (!isset($email['params']['quickselection']) or empty($email['params']['quickselection']))
					return array();
				return $email['params']['quickselection'];
			}

			function welcome_new($data) {
				?>
					<div id="update-page" class="about-wrap mpoet-page">

						<h1><?php echo __('Try the new (and much better) MailPoet now', WYSIJA); ?></h1>

						<div class="about-text" style="visibility:hidden"><?php echo $data['abouttext'] ?></div>
						<?php
						foreach ($data['sections'] as $section) {

							$link_hide = $class_added = '';
							if (isset($section['hidelink'])) {
								$link_hide = ' <span class="ctaupdate">-</span> ' . $section['hidelink'];
								$class_added = ' removeme';
							}


										?>
								<div class="changelog <?php echo $class_added ?>">
									<h2><?php echo $section['title'] . $link_hide ?></h2>

									<div class="feature-sec tion <?php echo $section['format'] ?>">
										<?php
										switch ($section['format']) {
											case 'three-col':
												if(true){
													foreach ($section['cols'] as $col) {
														?>
														<div>
															<h4><?php echo $col['title'] ?></h4>
															<p><?php echo $col['content'] ?></p>
														</div>
														<?php
													}
												}else{
													$quick_html_helper = WYSIJA::get('quick_html','helper');
													echo $quick_html_helper->three_arguments($section['cols']);
												}

												break;
											case 'bullets':
												echo '<ul>';
												foreach ($section['paragraphs'] as $line) {
													?>
													<li><?php echo $line ?></li>
													<?php
												}
												echo '</ul>';
												break;

											default :
												foreach ($section['paragraphs'] as $line) {
													echo '<p>'.$line.'</p>';
												}
										}
										?>
									</div>
								</div>
					<?php
			}
			?>

						<a class="button-primary" href="admin.php?page=wysija_campaigns"><?php _e("No thanks! I'll use MailPoet version 2 for now", WYSIJA); ?></a>

					</div>


			<?php
			}

      public function replace_link_shortcode($text, $url) {
        $count = 1;
        return preg_replace(
          '/\[\/link\]/',
          '</a>',
          preg_replace(
            '/\[link\]/',
            sprintf('<a href="%s">', $url),
            $text,
            $count
          ),
          $count
        );
      }
			function whats_new($data) {

				$helper_readme = WYSIJA::get('readme', 'helper');
				$helper_readme->scan();
				$helper_licence = WYSIJA::get('licence', 'helper');
                                $model_config = WYSIJA::get('config', 'model');
				$data = array();
				//

                                $installed_time = (int)$model_config->getValue('installed_time');
                                $usage =  time() - $installed_time;

                                $helper_toolbox = WYSIJA::get('toolbox', 'helper');
                                $usage_string = $helper_toolbox->duration_string($usage, true, 1);

                                $onemonth = 3600*24*31;
                                $twomonths = 3600*24*62;
                                $year = 3600*24*365;
                                if( $usage > $twomonths){
                                    $data['abouttext'] = sprintf(__('You have been a MailPoet user for %s.', WYSIJA), '<strong>'.trim($usage_string).'</strong>');
                                     if( $usage > $twomonths){
                                         $data['abouttext'] .= '<br/>'.__( 'Wow! Thanks for being part of our community for so long.' , WYSIJA ) ;
                                     }

                                }else{
                                    $data['abouttext'] = __('You updated! It\'s like having the next gadget, but better.', WYSIJA);
                                }
                                $data['abouttext'] = '';


				// this is a flag to have a pretty clean update page where teh only call to action is our survey
				$show_survey = false;

				$is_multisite = is_multisite();
				$is_network_admin = WYSIJA::current_user_can('manage_network');

        $data['sections'][] = array(
          'title' => __("Try the new version 3 today.", WYSIJA),
          'format' => 'title-content',
          'content' => '
<ul style="list-style: disc inside none">
  <li><a href="http://beta.docs.mailpoet.com/article/234-video-overview?utm_source=mp2&utm_campaign=whatsnew">'.__("View the 2-minute video", WYSIJA).'</a></li>
  <li><a href="https://www.mailpoet.com/faq-mailpoet-version-2/?utm_source=mp2&utm_campaign=whatsnew">'.__("Read the FAQ", WYSIJA).'</a></li>
  <li><a href="http://beta.docs.mailpoet.com/article/189-comparison-of-mailpoet-2-and-3?utm_source=mp2&utm_campaign=whatsnew">'.__('Comparison table of both versions', WYSIJA).'</a></li>
  <li><a href="http://demo.mailpoet.com?utm_source=mp2&utm_campaign=whatsnew">'.__('Try the online demo', WYSIJA).'</li>
</ul>
<br/>
<a class="button-primary" href="plugin-install.php?s=mailpoet&tab=search&type=author">'.__('Download MailPoet 3 now', WYSIJA).'</a>

<!-- poll -->
<div><br/><br/></div>
<style type="text/css">.pds-box { margin: 0 !important; }</style>
<script type="text/javascript" charset="utf-8" src="https://secure.polldaddy.com/p/9882029.js"></script>
<noscript><a href="https://polldaddy.com/poll/9882029/">I\'m not switching to the new MailPoet 3 because...</a></noscript>
          '
        );

				if ($is_multisite) {
					if ($is_network_admin) {
						$model_config->save(array('ms_wysija_whats_new' => WYSIJA::get_version()));
					}
				} else {
					$model_config->save(array('wysija_whats_new' => WYSIJA::get_version()));
				}

                                $sharing_data = $model_config->getValue('analytics');
/*                                if( empty( $sharing_data ) ){
                                    $data['sections'][] = array(
                                            'title' => __('One quick question...',WYSIJA),

                                            'content' => '<div class="feature-section"><iframe frameborder="0" width="100%" height="370" scrolling="auto" allowtransparency="true" src="//mailpoet.polldaddy.com/s/what-s-new-sept-2015?iframe=1"><a href="//mailpoet.polldaddy.com/s/what-s-new-sept-2015">View Survey</a></iframe></div>'.
											 '<div class="mpoet-update-subscribe" ><h4>'.__( 'Subscribe to our newsletters', WYSIJA ).'</h4><div class="mpoet-update-subscribe-left"> <p>'.__('We send a monthly newsletter with the following:',WYSIJA).'</p>' .
                                                                                                    '<ul>' .
                                                                                                            '<li>'.__('Important plugin updates',WYSIJA).'</li>' .
                                                                                                            '<li>'.__('Coupons',WYSIJA).'</li>' .
                                                                                                            '<li>'.__('Tips for you, or your customers',WYSIJA).'</li>' .
                                                                                                            '<li>'.__('What we’re working on',WYSIJA).'</li>' .
                                                                                                            '<li>'.__('News from us, the team',WYSIJA).'</li>' .
                                                                                                    '</ul>
                                                                                                     <p>View <a target="_blank" href="http://www.mailpoet.com/?wysija-page=1&controller=email&action=view&email_id=1181&wysijap=subscriptions-3">an example blog post email</a> and <a target="_blank" href="http://www.mailpoet.com/?wysija-page=1&controller=email&action=view&email_id=64&wysijap=subscriptions-2">an example newsletter</a>.</p>
                                                                                                        </div>' .
                                                                                            '<div class="mpoet-update-subscribe-right">' .

                                                                                            '<iframe width="380" scrolling="no" frameborder="0" src="http://www.mailpoet.com/?wysija-page=1&controller=subscribers&action=wysija_outter&wysija_form=5&external_site=1&wysijap=subscriptions-3" class="iframe-wysija" vspace="0" tabindex="0" style="position: static; top: 0pt; margin: 0px; border-style: none; height: 180px; left: 0pt; visibility: visible; background-color: #f1f1f1!important;" marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="Subscription Wysija"></iframe>
                                                                                                </div>
                                                                                                <div style="clear:both;"></div>',
                                            'format' => 'title-content',
                                    );
                                }*/



				$msg = $model_config->getValue('ignore_msgs');
        /*
				if ( !isset($msg['ctaupdate']) && $show_survey === false ) {
					$data['sections'][] = array(
						'title' => __('Keep this plugin essentially free', WYSIJA),
						'review' => array(
							'title' => '1. ' . __('Love kittens?', WYSIJA) . ' ' . __('We love stars...', WYSIJA),
							'content' => str_replace(
									array('[link]', '[/link]'), array('<a href="http://goo.gl/D52CBL" target="_blank" title="On wordpress.org">', '</a>'), __('Each time one of our users forgets to write a review, a kitten dies. It\'s sad and breaks our hearts. [link]Add your own review[/link] and save a kitten today.', WYSIJA))
						),
						'follow' => array(
							'title' => '2. ' . __('Follow us and don\'t miss anything!', WYSIJA),
							'content' => $this->_get_social_buttons(false)
						),
						'hidelink' => '<a class="linkignore ctaupdate" href="javascript:;">' . __('Hide!', WYSIJA) . '</a>',
						'format' => 'review-follow',
					);
				}
         */

/*                                if( $show_survey ){
                                    $data['sections'][] = array(
						'title' => 'Answer our survey and make your plugin better',

						'content' => '<iframe frameborder="0" width="100%" height="600" scrolling="auto" allowtransparency="true" src="//mailpoet.polldaddy.com/s/what-s-new-sept-2015?iframe=1"><a href="//mailpoet.polldaddy.com/s/what-s-new-sept-2015">View Survey</a></iframe><hr/>',
						'format' => 'title-content',
					);
                                }*/

				if (isset($helper_readme->changelog[WYSIJA::get_version()])) {
					$data['sections'][] = array(
						'title' => __('Change log', WYSIJA),
						'format' => 'bullets',
						'paragraphs' => $helper_readme->changelog[WYSIJA::get_version()]
					);
				}

				?>
				<div id="update-page" class="about-wrap mpoet-page">

					<h1><?php echo sprintf(__('You\'ve updated to %1$s', WYSIJA), '<span class="version">MailPoet '.WYSIJA::get_version())."</span>"; ?></h1>

					<div class="about-text"><?php echo $data['abouttext'] ?></div>
					<?php
					foreach ($data['sections'] as $section) {

						$link_hide = $class_added = '';
						if (isset($section['hidelink'])) {
							$link_hide = ' <span class="ctaupdate">-</span> ' . $section['hidelink'];
							$class_added = ' removeme';
						}


									?>
							<div class="changelog <?php echo $class_added ?>">
								<h2><?php echo $section['title'] . $link_hide ?></h2>

								<div class="feature-section <?php echo $section['format'] ?>">
									<?php
									switch ($section['format']) {
										case 'title-content':
											?>
											<div>
												<?php echo $section['content'] ?>
											</div>
											<?php
											break;
										case 'three-col':
											if(isset($section['content'])){
												foreach ($section['cols'] as $col) {
													?>
													<div>
														<h4><?php echo $col['title'] ?></h4>
														<p><?php echo $col['content'] ?></p>
													</div>
													<?php
												}
											}else{
												$quick_html_helper = WYSIJA::get('quick_html','helper');
												echo $quick_html_helper->three_arguments($section['cols']);
											}

											break;
										case 'bullets':
											echo '<ul>';
											foreach ($section['paragraphs'] as $line) {
												?>
												<li><?php echo $line ?></li>
												<?php
											}
											echo '</ul>';
											break;
									case 'review-follow':
											$class_review_kitten = ' small';
											$count_title = count(str_split($section['review']['title']));
											$count_content = count(str_split($section['review']['content']));
											if ($count_title > 40 || $count_content > 340)
												$class_review_kitten = ' medium';
											if ($count_title > 50 || $count_content > 400)
												$class_review_kitten = ' large';

											echo '<div id="review-follow">';

											echo '<div class="review-left' . $class_review_kitten . '">';
											echo '<div class="description"><h4>' . $section['review']['title'] . '</h4>';
											echo '<p>' . $section['review']['content'] . '</p></div>';
											echo '<a title="On wordpress.org" target="_blank" class="link-cat-review" href="http://goo.gl/P0r5Fc"> </a></div>';

											echo '<div class="review-right">';
											echo '</div>';

											echo '<div class="subscribe-middle' . $class_review_kitten . '">';
											echo '<div class="description" ><h4>' . $section['follow']['title'] . '</h4>';
											echo '<div class="socials">' . $section['follow']['content'] . '</div></div>';
											echo '</div>';


											$class_name = 'follow-right';
											if(version_compare(get_bloginfo('version'), '3.8')>= 0){
												$class_name .= '38';
											}
											echo '<div class="'.$class_name.'">';
											echo '</div>';
											echo '</div>';
											break;

										default :
											foreach ($section['paragraphs'] as $line) {
												?>
												<p><?php echo $line ?></p>
							<?php
						}
				}
				?>
								</div>
							</div>
				<?php
		}
                $link_class = 'button-primary';
                if($show_survey){
                    $link_class = 'button-secondary';
                }
		?>

					<a class="<?php echo $link_class ?>" href="admin.php?page=wysija_campaigns"><?php _e('Thanks! Now take me to MailPoet.', WYSIJA); ?></a>

				</div>


		<?php
	}

	/**
	 * poll section, we inject one section in the $data object if a poll is available
	 * @param array $data
	 * @return array
	 */
	private function _inject_poll( $data ){
		$polls_available = array( '7970424' ); // all polls' ids from polldaddy
		$display_poll = 0; // poll id to display

		$model_config = WYSIJA::get( 'config' , 'model' );
		$polls_already_viewed = $model_config->getValue('viewed_polls');

		// we go through all of the viewed polls in order to find one that has not been viewed yet
		if( !empty($polls_already_viewed) ){
			foreach( $polls_available as $poll_id ){
				if( !in_array( $poll_id , $polls_already_viewed )){
					$display_poll = $poll_id;
				}
			}
		}else{
			// no poll has been viewed yet, let's display the first one
			$display_poll = $polls_available[0];
		}

		// only if we found a poll which has not been viewed yet, will we display it
		if( $display_poll > 0 ){
			$data['sections'][] = array(
				'title' => __('Hey! We have a quick question for you:', WYSIJA),
				'paragraphs' => array(
					'line1' => '<script type="text/javascript" charset="utf-8" src="https://secure.polldaddy.com/p/'.$display_poll.'.js"></script>
						<noscript>
							<a href="https://polldaddy.com/poll/'.$display_poll.'/">'.__('Hey! We have a quick question for you:', WYSIJA).'</a>
						</noscript>'
				),
				'format' => 'paragraphs'
			);

			$polls_already_viewed[] = $display_poll;

			// save the new array of viewed polls
			$model_config->save( array( 'viewed_polls' => $polls_already_viewed ) );
		}

		// returning the new data array
		return $data;
	}

	private function _get_social_buttons($inline=true){

		 if($inline){
			 $class=' class="socials removeme"';
		 }else{
			 $class=' id="socials-block"';
		 }
		 $wysijaversion='<div '.$class.'>
		<div class="fb" >
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, \'script\', \'facebook-jssdk\'));</script>
		<div class="fb-like" data-href="http://www.facebook.com/mailpoetplugin" data-send="false" data-layout="button_count" data-width="90" data-show-faces="false"></div></div>
		<div class="twitter">
		<a href="https://twitter.com/mail_poet" class="twitter-follow-button" data-show-count="true" data-show-screen-name="false">Follow us</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
		<div class="gplus">
		<!-- Place this tag in your head or just before your close body tag -->
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
		<!-- Place this tag where you want the +1 button to render -->
		<g:plusone href="https://plus.google.com/104749849451537343615" size="medium"></g:plusone></div>
		';
		 if($inline) $wysijaversion.='<div id="hidesocials">
		<a class="linkignore socialfoot" href="javascript:;">'.__('Hide!',WYSIJA).'</a>
			</div>';
			$wysijaversion.= "<div style='clear:both;'></div></div><div style='clear:both;'></div>";
			return $wysijaversion;
	}
}
