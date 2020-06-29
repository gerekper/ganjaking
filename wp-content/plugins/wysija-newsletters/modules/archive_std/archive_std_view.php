<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_module_view_archive_std_view extends WYSIJA_view {

	public function hook_settings_super_advanced($data) {
		?>
		<tr class="super-advanced">
			<th>
				<label><?php echo __('Archive page shortcode', WYSIJA); ?></label>
		<p class="description"><?php echo __('Paste this shortcode in a page to display a list of past newsletters.', WYSIJA); ?></p>
		</th>
		<td>
			<?php
			$key		  = 'archive';
			$forms_helper = WYSIJA::get('forms', 'helper');
			?>
			<div id="<?php echo $key; ?>_linkname" class="linknamecboxes">
				<?php echo $forms_helper->input(array( 'name'	 => 'wysija[config]['.$key.'_linkname]', 'size'	 => '75', 'class'	=> 'archive-shortcode', 'readonly' => 'readonly' ), '[wysija_archive]'); ?>
				<div class="clearfix">
					<?php
					foreach ($data['lists'] as $list) {
						?>
						<p class="labelcheck">
							<label for="<?php echo $key; ?>list-<?php echo $list['list_id']; ?>">
								<?php echo $forms_helper->checkbox(array( 'id'	=> $key.'list-'.$list['list_id'], 'name'  => 'wysija[config]['.$key.'_lists][]', 'class' => $key.'-list' ), $list['list_id']); ?>
								<?php echo $list['name']; ?>
							</label>
						</p>
						<?php
					}
					?>
				</div>
			</div>
		</td>
		</tr>
		<script type="text/javascript">
			(function($) {

				/**
				 * class ArchiveShortcode: Shortcode refactor
				 * @param {string} shortcode
				 * @param {string} targetShortcode DOM element selector
				 * @returns {_L2.ArchiveShortcode}
				 */
				var ArchiveShortcode = function(shortcode, targetShortcode) {
					this.shortcode = shortcode;
					this.targetShortcode = targetShortcode;
					this.lists = [];
					this.getLists = function() {

					};
					this.addToLists = function(id) {
						this.lists.push(id);
					};

					this.removeFromList = function(id) {
						var i = this.lists.indexOf(id);
						if (i !== -1) {
							this.lists.splice(i, 1);
						}
					};

					this.renderShortcode = function() {
						shortCode = '[' + +' ' + this.lists.join() + ']';
						shortCode = '[';
						shortCode += this.shortcode;
						if (this.lists.length > 0) {
							shortCode += ' list_id="' + this.lists.join() + '"';
						}
						shortCode += ']';
						$(this.targetShortcode).val(shortCode);
					};

					this.onChangeList = function(isSelected, listId) {
						if (isSelected)
							this.addToLists(listId);
						else
							this.removeFromList(listId);
						this.renderShortcode();
					};

					this.onClick = function() {
						$(this).select();
					};

					$(this.targetShortcode).click(this.onClick);
				};
				$(document).ready(function() {
					var achiveShortcode = new ArchiveShortcode('wysija_archive', '.archive-shortcode');
					$('.archive-list').change(function(e) {
						achiveShortcode.onChangeList($(e.target).is(':checked'), $(e.target).val());
					});
				});
			})(jQuery);
		</script>
		<?php
	}

	/**
	 * Render archieve shortcode at frontend
	 * @param array $data
	 */
	public function render_archive(Array $data) {
		if (empty($data['newsletters'])) {
			echo apply_filters('mpoet_archive_no_newsletters', __('Oops! There are no newsletters to display.', WYSIJA));
			return;
		}
		$filter_mpoet_archive_title = apply_filters('mpoet_archive_title', '' /* __('Newsletter Archives') */);
		if (!empty($filter_mpoet_archive_title)) {
			echo '<h3 class="wysija_archive_title">'.$filter_mpoet_archive_title.'</h3>';
		}
		echo '<ul class="wysija_archive">';
		foreach ($data['newsletters'] as $newsletter) {
			?>
			<li>
				<span class="wysija_archive_date">
					<?php
					add_filter('mpoet_archive_date', array( $this, 'filter_mpoet_archive_date' ), 2);
					echo apply_filters('mpoet_archive_date', $newsletter->sent_at);
					?>
				</span>
				<span class="wysija_archive_subject">
					<?php
					add_filter('mpoet_archive_subject', array( $this, 'filter_mpoet_archive_subject' ), 2);
					echo apply_filters('mpoet_archive_subject', $newsletter);
					?>
				</span>
			</li>
			<?php
		}
		echo '</ul>';
		?>
		<style type="text/css">
			ul.wysija_archive {
				list-style-type: none;
			}
			ul.wysija_archive li {
				padding-top: 5px;
			}
		</style>
		<?php
	}

	/**
	 * Filter Wysija_archive_date
	 * @param type $sent_at
	 * @return type
	 */
	public function filter_mpoet_archive_date($sent_at) {
		return $this->fieldListHTML_created_at($sent_at).' : ';
	}

	/**
	 * Filter Wysija_archive_date
	 * @param type $sent_at
	 * @return type
	 */
	public function filter_mpoet_archive_subject($newsletter) {
		$email_helper = WYSIJA::get('email', 'helper');
		$full_url	 = $email_helper->getVIB($newsletter);
		?>
		<a href="<?php echo $full_url ?>" target="_blank" class="viewnews" title="<?php _e('Preview in new tab', WYSIJA) ?>">
			<?php echo $newsletter->subject; ?>
		</a>
		<?php
	}

}