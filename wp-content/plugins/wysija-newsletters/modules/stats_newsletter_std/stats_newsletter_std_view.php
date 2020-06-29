<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_module_view_stats_newsletter_std_view extends WYSIJA_view_back {

	public function hook_newsletter_top($data) {
		echo '<div class="stats_newsletter_std hook-column left" style="height:300px;">';
		if (empty($data['emails_count']))
			return;
		if (!empty($data['dataset']))
			foreach ($data['dataset'] as $index => $dataset) {
				$data['dataset'][$index]['container'] = 'chart'.md5($dataset['title'].rand());
				echo '<div id="'.$data['dataset'][$index]['container'].'" style="height: 300px; width:100%"></div>';
			}
		?>
		<script type="text/javascript">
			// Load the Visualization API and the piechart package.
			google.load('visualization', '1.0', {'packages': ['corechart']});
			// Set a callback to run when the Google Visualization API is loaded.
			google.setOnLoadCallback(function() {
		<?php
		foreach ($data['dataset'] as $dataset) {
			?>
						var dataTable = new google.visualization.DataTable();
			<?php
			foreach ($dataset['columns'] as $column) {
				?>
								dataTable.addColumn('<?php echo $column['type']; ?>', '<?php echo $column['label']; ?>');
				<?php
			}
			?>
						dataTable.addRows([
			<?php
			$count = 0;
			foreach ($dataset['data'] as $index => $value) {
				if ($count++ > 0)
					echo ',';
				echo '["'.$index.'",'.$value.']';
			}
			?>
						]);
						var options = {
							title: '<?php echo $dataset['title']; ?>',
							is3D: false,
							chartArea: {left: 0},
							backgroundColor: 'transparent'
						};
						var chart = new google.visualization.PieChart(document.getElementById('<?php echo $dataset['container']; ?>'));
						chart.draw(dataTable, options);
			<?php
		}
		?>
			});
		</script>
		<?php
		echo '</div>';
	}

	public function hook_newsletter_top_links($data) {
		?>
		<div class="container-top-links container clear" rel="<?php echo $data['module_name']; ?>">
			<h3 class="title"><?php echo __('Top links', WYSIJA); ?></h3>
			<?php if (empty($data['top_links'])) { ?>
				<div class="notice-msg updated inline"><ul><li><?php echo $data['messages']['data_not_available']; ?></li></ul></div>
			<?php }
			else { ?>
				<table class="widefat fixed">
					<thead>
					<th class="check-column" style="width:40px">&nbsp;</th>
					<th class="link_column" style="width:40%"><?php echo __('Link', WYSIJA); ?></th>
					<!--th class="sortable sort-filter <?php // echo $data['order_direction']['clicks'];  ?>" rel="click">
						<a href="" class="orderlink">
							<span><?php // echo __('Clicks');  ?></span><span class="sorting-indicator"></span>
						</a>
						</th-->
					<th class="click_column"><?php echo __('Unique clicks'); ?></th>
					<th class="click_column"><?php echo __('Total clicks'); ?></th>
					<th>&nbsp;</th>
					</thead>
					<tbody class="">
						<?php
						$i		   = 1;
						$alt		 = false;
						$link_helper = WYSIJA::get('links', 'helper');
						add_filter('wysija_link', array( $link_helper, 'render_link' ), 1, 6);
						foreach ($data['top_links'] as $url) {
							if ($i === 1)
								$wysija_link = apply_filters('wysija_link', '', $url['url'], 50, 15, true);
							else
								$wysija_link = apply_filters('wysija_link', '', $url['url'], 50, 15, true, '...');
							?>
							<tr class="<?php echo $alt ? 'alternate' : '';
							$alt = !$alt;
							?>">
								<td><?php echo $i;
							$i++; ?></td>
								<td><?php echo $wysija_link; ?></td>
								<td>
									<?php
									echo $url['unique_clicks'];
									if ($data['is_premium']) {
										echo ' - ';
										if ($url['is_viewing'])
											echo __('Currently viewing', WYSIJA);
										else
											echo '<a href="'.$url['view_subscriber_url'].'">'.__('View subscribers', WYSIJA).'</a>';
									}
									?>
								</td>
								<td><?php echo $url['total_clicks']; ?></td>
								<td>&nbsp;</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			<?php } ?>
			<?php
			//$this->model->countRows = 103;//$data['top_subscribers']['count'];
//            if (empty($this->viewObj)) $this->viewObj = new stdClass();
//            $this->viewObj->msgPerPage = __('Show',WYSIJA).':';
//            $this->viewObj->title = '';
			//$this->limitPerPage(); // not implemented yet
			?>
			<div class="cl"></div>
		</div>
		<?php
	}

	/**
	 * Render actions of a newsletter
	 * @param type $data
	 */
	public function hook_newsletter_action_buttons($data) {
		echo '<div class="actions right">';

		$classes = function_exists('wp_star_rating') ? 'add-new-h2' : 'button-secondary2';

		// view button
		$email_helper = WYSIJA::get('email', 'helper');
		$link_view	= $email_helper->getVIB($data['email_object']);
		echo '<a id="action-view" target="_blank" href="'.$link_view.'" class="action-view '.$classes.'">'.__('View', WYSIJA).'</a>';

		//duplicate button
		$action		   = 'duplicate';
		$params		   = array(
			'page'		  => 'wysija_campaigns',
			'action'		=> $action,
			'id'			=> $data['id'],
                        'email_id'              => $data['email_id'],
                        '_wpnonce'              => WYSIJA_view::secure(array('action' => $action , 'id' => $data['id']), true)
		);
		$link_duplicate = 'admin.php?'.http_build_query($params);
		echo '<a id="action-'.$action.'" href="'.$link_duplicate.'" class="action-'.$action.' '.$classes.'">'.__('Duplicate', WYSIJA).'</a>';

		$alternate = false;
		echo '<table class="newsletter-stats-block widefat fixed">';
		// Google tracking code
		if (!empty($data['email_object']['params']['googletrackingcode'])) {
			// echo '<div class="googletrackingcode"><span>' . __('Google Analytics', WYSIJA) . ':</span> ' . $data['email_object']['params']['googletrackingcode'] . '</div>';
			echo '<tr class="'.($alternate ? 'alternate' : '').'"><td class="label">'.__('Google Analytics', WYSIJA).'</td><td>'.$data['email_object']['params']['googletrackingcode'].'</td></tr>';
			$alternate = !$alternate;
		}

		// Sent on:
		// echo '<div class="googletrackingcode"><span>' . __('Sent on', WYSIJA) . ':</span> ' . $this->fieldListHTML_created_at_time($data['email_object']['sent_at']) . '</div>';
		echo '<tr class="'.($alternate ? 'alternate' : '').'"><td class="label">'.__('Sent on', WYSIJA).'</td><td>'.$this->fieldListHTML_created_at_time($data['email_object']['sent_at']).'</td></tr>';
		$alternate = !$alternate;

		// Lists:
		if (!empty($data['lists'])) {
			// echo '<div class="googletrackingcode"><span>' . __('Lists', WYSIJA) . ':</span> ' . implode(', ',$data['lists']) . '</div>';
			echo '<tr class="'.($alternate ? 'alternate' : '').'"><td class="label">'.__('Lists', WYSIJA).'</td><td>'.implode(', ', $data['lists']).'</td></tr>';
			$alternate = !$alternate;
		}

		// From address:
		$_from_address = array( );
		if (!empty($data['email_object']['from_name'])) {
			$_from_address[] = $data['email_object']['from_name'];
		}
		if (!empty($data['email_object']['from_email'])) {
			$_from_address[] = $data['email_object']['from_email'];
		}
		if (!empty($_from_address)) {
			// echo '<div class="googletrackingcode"><span>' . __('From', WYSIJA) . ':</span> ' . implode(', ',$_from_address) . '</div>';
			echo '<tr class="'.($alternate ? 'alternate' : '').'"><td class="label">'.__('From', WYSIJA).'</td><td>'.implode(' &lt;', $_from_address).(count($_from_address) > 1 ? '&gt;' : '').'</td></tr>';
			$alternate = !$alternate;
		}

		// Reply-to address
		$_reply_to_address = array( );
		if (!empty($data['email_object']['replyto_name'])) {
			$_reply_to_address[] = $data['email_object']['replyto_name'];
		}
		if (!empty($data['email_object']['replyto_email'])) {
			$_reply_to_address[] = $data['email_object']['replyto_email'];
		}

		if (!empty($_reply_to_address)) {
			// echo '<div class="googletrackingcode"><span>' . __('Reply-to', WYSIJA) . ':</span> ' . implode(', ',$_reply_to_address) . '</div>';
			echo '<tr class="'.($alternate ? 'alternate' : '').'"><td class="label">'.__('Reply-to', WYSIJA).'</td><td>'.implode(' &lt;', $_reply_to_address).(count($_reply_to_address) > 1 ? '&gt;' : '').'</td></tr>';
			$alternate = !$alternate;
		}

		if (!empty($data['bounce'])) {
			// echo '<div class="googletrackingcode"><span>' . __('Bounce', WYSIJA) . ':</span> ' . $data['bounce'] . '</div>';
			echo '<tr class="'.($alternate ? 'alternate' : '').'"><td class="label">'.__('Bounce', WYSIJA).'</td><td>'.$data['bounce'].'</td></tr>';
			$alternate = !$alternate;
		}

		echo '</table>';
		echo '</div>';
	}

}