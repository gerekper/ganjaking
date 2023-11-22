<?php

namespace DynamicContentForElementor\AdminPages\Features;

use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Assets;
class FrontendNavigator
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'frontend-navigator';
    }
    /**
     * Get Label
     *
     * @return string
     */
    public function get_label()
    {
        return __('Frontend Navigator', 'dynamic-content-for-elementor');
    }
    /**
     * Should Display Count
     *
     * @return boolean
     */
    public function should_display_count()
    {
        return \false;
    }
    /**
     * Render
     *
     * @return void
     */
    public function render()
    {
        ?>

		<form action="" method="post">
			<?php 
        wp_nonce_field('dce-settings-page', 'dce-settings-page');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['frontend_navigator'])) {
            update_option(DCE_FRONTEND_NAVIGATOR_OPTION, $_POST['frontend_navigator']);
            \DynamicContentForElementor\Plugin::instance()->admin_pages->notices->success(__('Your preferences have been saved.', 'dynamic-content-for-elementor'));
        }
        ?>
			<?php 
        $option = get_option(DCE_FRONTEND_NAVIGATOR_OPTION);
        ?>

			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row">
						<div>
							<label for="frontend_navigator"><?php 
        _e('Frontend Navigator', 'dynamic-content-for-elementor');
        ?></label>
						</div>
					</th>
					<td>
					<div>
					<select name="frontend_navigator">
						<option value="active" <?php 
        if ($option === 'active') {
            ?>selected="selected"<?php 
        }
        ?>><?php 
        _e('Active only for administrators', 'dynamic-content-for-elementor');
        ?></option>
						<option value="active-visitors" <?php 
        if ($option === 'active-visitors') {
            ?>selected="selected"<?php 
        }
        ?>><?php 
        _e('Active for all roles and visitors', 'dynamic-content-for-elementor');
        ?></option>
						<option value="inactive" <?php 
        if ($option === 'inactive' || !$option) {
            ?>selected="selected"<?php 
        }
        ?>><?php 
        _e('Inactive', 'dynamic-content-for-elementor');
        ?></option>
					</select>
				</div>
				</td>
				</tr>
			</table>

			<?php 
        submit_button('');
        ?>
		</form>
		<?php 
    }
}
