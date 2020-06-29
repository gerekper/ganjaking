<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_view_back_statistics extends WYSIJA_view_back{
    public $icon = 'icon-stats';
    function __construct(){
        $this->title = __('Stats dashboard');
        if (empty($this->viewObj))
            $this->viewObj = new stdClass ();
        $this->viewObj->msgPerPage = __('Show');
    }

    function main($data){
        $this->assign_js($data);
        $this->render_date_filter($data);
        echo '<div id="hook_stats">';
        if (!$data['lazy_load']){
            echo $data['hooks']['hook_stats'];
        }
        else {
            if (!empty($data['first_module']))
                echo $data['first_module'];
        }
        echo '</div>';
        $this->render_loading_indicator();
	$this->render_feedback_form();
    }

    protected function render_date_filter($data){

        ?>
        <div class="stats_date_filter">
            <form action="#" name="stats-filter" id="stats-filter">
                <select class="custom_date" name="custom_date" id="custom-date">
                <?php
                foreach($data['custom_dates'] as $custom_date) {
                    $selected = $data['default_duration']->last_days == $custom_date['value'] ? 'selected' : '';
                    ?>
                    <option value="<?php echo $custom_date['value']; ?>" <?php echo $selected; ?> from="<?php echo $custom_date['from']; ?>" to="<?php echo $custom_date['to']; ?>">
                            <?php echo $custom_date['label']; ?>
                    </option>
                    <?php
                    ?>
                <?php
                }?>
                </select>
                <?php echo __('From', WYSIJA); ?>: <input type="text" class="datepicker" name="from" id="stats-filter-from" value="<?php echo esc_attr($data['default_duration']->from); ?>" size="8" />
                <?php echo __('To', WYSIJA); ?>: <input type="text" class="datepicker" name="to" id="stats-filter-to" value="<?php echo esc_attr($data['default_duration']->to); ?>" size="8" />
                <input type="submit" class="button-secondary" value="<?php echo __('Filter', WYSIJA); ?>" />
                <!--(<input type="reset" class="reset"></input>)--><?php // this function does not work correctly. Need to implement: on reset => onchange (from/date) => notify object StatsFilter. ?>
            </form>
        </div>
        <?php
    }

    protected function render_loading_indicator() {
        ?>
        <div class="spinner" >&nbsp;</div>
        <?php
    }

    protected function render_feedback_form() {
	$url = 'http://support.mailpoet.com/feedback/?utm_source=wpadmin&utm_campaign=contact_stats';
	echo "<div class='stats_feedback'>".
		str_replace(
                    array('[link]', '[/link]'),
			array('<a target="_blank" href="' . $url . '">', '</a>'),
			__('You have feedback on these stats? [link]Contact us[/link] directly to let us know.', WYSIJA)
			)
		. "</div>";
    }


    protected function assign_js($data){
        ?>
        <script type="text/javascript">
            var wysijaStatisticVars = {}; // holds all global variables in this page.
            wysijaStatisticVars.lazyLoad = {
                lazyLoadBlocks : <?php echo '["'.implode('", "', $data['lazy_load_modules']).'"]'; ?>,
                blocks : <?php echo '["'.implode('", "', $data['modules']).'"]'; ?>,
                targetContainer: '#hook_stats',
                task:'get_block',
                active: <?php echo !empty($data['lazy_load_modules']) ? 'true' : 'false'; ?>
            };
            wysijaStatisticVars.filter = {
                datePickerElements: '.datepicker',
                dateFormat: '<?php echo $data['js_date_format']; ?>'
            };
        </script>
        <?php
    }
}
