<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_module_view_stats_subscribers_std_view extends WYSIJA_view_back {

	public function hook_subscriber_bottom($data) {
		if (empty($data['opened_newsletters']['stats']))
			return;
		?>
		<div class="container-stats-subscribers_std container" rel="<?php echo $data['module_name']; ?>">
			<h3 class="title">
				<?php
				if ($data['opened_newsletters']['stats']['emails_count'] > 1)
					echo sprintf(__('Subscriber opened %1$s of %2$s emails', WYSIJA), $data['opened_newsletters']['stats']['opened_emails_count'], $data['opened_newsletters']['stats']['emails_count']);
				else
					echo sprintf(__('Subscriber opened %1$s of %2$s email', WYSIJA), $data['opened_newsletters']['stats']['opened_emails_count'], $data['opened_newsletters']['stats']['emails_count']);
				?>
			</h3>
			<?php if (empty($data['opened_newsletters']['emails'])) { ?>
				<div class="notice-msg updated inline"><ul><li><?php echo $data['messages']['data_not_available']; ?></li></ul></div>
			<?php }
			else { ?>
				<table class="widefat fixed">
					<thead>
					<th class="check-column">&nbsp;</th>
					<th class="newsletter"><?php echo esc_attr__('Newsletter', WYSIJA); ?></th>
					<th class="link"><?php echo esc_attr__('Link', WYSIJA); ?></th>
					<th class="click sortable sort-filter <?php echo $data['order_direction']['clicks']; ?>" rel="click"><a href="javascript:void(0);" class="orderlink"><span><?php echo esc_attr__('Clicks'); ?></span><span class="sorting-indicator"></span></a></th>
					<!--th><?php echo esc_attr__('Device', WYSIJA); ?></th-->
					<!--th class="date"><?php echo esc_attr__('Opened date', WYSIJA); ?></th-->
					<th class="date"><?php echo esc_attr__('Date sent', WYSIJA); ?></th>
					</thead>
					<tbody class="list:user user-list">
						<?php
						$i			= 1;
						$alt		  = false;
						$email_helper = WYSIJA::get('email', 'helper');
						foreach ($data['opened_newsletters']['emails'] as $email) {
							$full_url = $email_helper->getVIB($email);
							if (empty($email['urls'])) {
								?>
								<tr class="<?php
								echo $alt ? 'alternate' : '';
								$alt = !$alt;
								?>">
									<td><?php
								echo $i;
								$i++;
								?></td>
									<td>
										<a href="<?php echo $full_url ?>" target="_blank" class="viewnews" title="<?php _e('Preview in new tab', WYSIJA) ?>">
					<?php echo $email['subject']; ?>
										</a>
									</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<!--td><?php echo esc_attr__('N/A', WYSIJA); ?></td-->
									<!--td><?php echo $this->fieldListHTML_created_at($email['opened_at']); ?></td-->
									<td><?php echo $this->fieldListHTML_created_at($email['sent_at']); ?></td>
								</tr>
								<?php
							}
							else {
								$link_helper = WYSIJA::get('links', 'helper');
								add_filter('wysija_link', array( $link_helper, 'render_link' ), 1, 6);
								foreach ($email['urls'] as $url) {
									if ($i === 1) {
										$wysija_link = apply_filters('wysija_link', '', $url['url'], 50, 15, false);
									}
									else {
										$wysija_link = apply_filters('wysija_link', '', $url['url'], 50, 15, false, '...');
									}
									?>
									<tr class="<?php
						echo $alt ? 'alternate' : '';
						$alt		 = !$alt;
									?>">
										<td><?php
						echo $i;
						$i++;
									?></td>
										<td>
											<a href="<?php echo $full_url ?>" target="_blank" class="viewnews" title="<?php _e('Preview in new tab', WYSIJA) ?>">
						<?php echo $email['subject']; ?>
											</a>
										</td>
										<td><?php echo $wysija_link; ?></td>
										<td><?php echo $url['number_clicked']; ?></td>
										<!--td><?php echo esc_attr__('N/A', WYSIJA); ?></td-->
										<!--td><?php echo $this->fieldListHTML_created_at($email['opened_at']); ?></td-->
										<td><?php echo $this->fieldListHTML_created_at($email['sent_at']); ?></td>
									</tr>
									<?php
								}
							}
						}
						?>
					</tbody>
				</table>
			<?php } ?>
			<?php
			//$this->model->countRows = 103;//$data['top_subscribers']['count'];
//            if (empty($this->viewObj)) $this->viewObj = new stdClass();
//            $this->viewObj->msgPerPage = esc_attr__('Show',WYSIJA).':';
//            $this->viewObj->title = '';
			//$this->limitPerPage(); // not implemented yet
			?>
			<div class="cl"></div>
		</div>
		<?php
	}

}